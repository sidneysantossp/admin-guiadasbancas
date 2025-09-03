<?php

namespace App\Console\Commands;

use App\Models\ForbiddenWord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ForbiddenWordsImport extends Command
{
    protected $signature = 'forbidden-words:import
        {source=ldnoobw : Source type: ldnoobw|file}
        {--langs=pt,en : Comma-separated ISO language codes when using ldnoobw}
        {--path= : Local path to a TXT or CSV file (when source=file)}
        {--format=auto : file format when source=file (auto|txt|csv)}
        {--category=default : Category to assign}
        {--severity=high : Severity to assign}
        {--inactive : Import as inactive instead of active}
    ';

    protected $description = 'Importa palavras proibidas a partir de fontes públicas (LDNOOBW) ou arquivos locais TXT/CSV.';

    public function handle(): int
    {
        $source = strtolower($this->argument('source'));
        $category = (string)$this->option('category');
        $severity = (string)$this->option('severity');
        $active = !$this->option('inactive');

        try {
            if ($source === 'ldnoobw') {
                $langs = array_filter(array_map('trim', explode(',', (string)$this->option('langs'))));
                $total = 0;
                foreach ($langs as $lang) {
                    $count = $this->importFromLdnoobw($lang, $category, $severity, $active);
                    $this->info("[ldnoobw] {$lang}: importadas {$count} palavras.");
                    $total += $count;
                }
                $this->info("Total importado: {$total} palavras.");
                return Command::SUCCESS;
            }

            if ($source === 'file') {
                $path = (string)$this->option('path');
                if (!$path) {
                    $this->error('Informe --path=/caminho/para/arquivo.txt|csv quando source=file');
                    return Command::FAILURE;
                }
                $format = strtolower((string)$this->option('format'));
                if ($format === 'auto') {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $format = in_array($ext, ['csv','txt']) ? $ext : 'txt';
                }
                if ($format === 'txt') {
                    $count = $this->importFromTxt($path, $category, $severity, $active);
                } else {
                    $count = $this->importFromCsv($path, $category, $severity, $active);
                }
                $this->info("[file:{$format}] importadas {$count} palavras a partir de {$path}.");
                return Command::SUCCESS;
            }

            $this->error('Fonte inválida. Use ldnoobw ou file.');
            return Command::INVALID;
        } catch (\Throwable $e) {
            $this->error('Falha ao importar: '.$e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function importFromLdnoobw(string $lang, string $category, string $severity, bool $active): int
    {
        // LDNOOBW mantém arquivos por idioma com uma palavra por linha
        // Tentar variantes de código de idioma comuns
        $candidates = [
            "https://raw.githubusercontent.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/master/{$lang}",
            "https://raw.githubusercontent.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/master/{$lang}.txt",
            // variantes pt-br/pt_BR
            $lang === 'pt' ? 'https://raw.githubusercontent.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/master/pt' : null,
            $lang === 'pt-br' ? 'https://raw.githubusercontent.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/master/pt-br' : null,
            $lang === 'pt_br' ? 'https://raw.githubusercontent.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/master/pt_br' : null,
        ];
        $content = null;
        foreach (array_filter($candidates) as $url) {
            $resp = Http::timeout(20)->get($url);
            if ($resp->ok() && strlen($resp->body()) > 0) {
                $content = $resp->body();
                break;
            }
        }
        if ($content === null) {
            $this->warn("Não foi possível obter lista remota para '{$lang}'. Pulando...");
            return 0;
        }
        $lines = preg_split('/\r\n|\r|\n/', $content);
        return $this->persistWords($lines, $lang, $category, $severity, $active);
    }

    protected function importFromTxt(string $path, string $category, string $severity, bool $active): int
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Arquivo não encontrado: {$path}");
        }
        $content = file_get_contents($path);
        $lines = preg_split('/\r\n|\r|\n/', (string)$content);
        // inferir language pelo nome do arquivo, padrão pt
        $lang = strtolower(pathinfo($path, PATHINFO_FILENAME));
        if (!preg_match('/^[a-z]{2}(-[a-z]{2})?$/i', $lang)) {
            $lang = 'pt';
        }
        return $this->persistWords($lines, $lang, $category, $severity, $active);
    }

    protected function importFromCsv(string $path, string $category, string $severity, bool $active): int
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Arquivo não encontrado: {$path}");
        }
        $fh = fopen($path, 'r');
        if (!$fh) throw new \RuntimeException('Falha ao abrir CSV');

        $header = null;
        $rows = [];
        while (($data = fgetcsv($fh)) !== false) {
            if ($header === null) {
                $header = array_map(fn($v) => strtolower(trim($v)), $data);
                continue;
            }
            $row = [];
            foreach ($data as $i => $v) {
                $key = $header[$i] ?? (string)$i;
                $row[$key] = $v;
            }
            $rows[] = $row;
        }
        fclose($fh);

        $words = [];
        foreach ($rows as $r) {
            $word = isset($r['word']) ? $r['word'] : ($r['palavra'] ?? null);
            $lang = isset($r['language']) ? $r['language'] : ($r['lang'] ?? ($r['idioma'] ?? 'pt'));
            $sev = $r['severity'] ?? $severity;
            $cat = $r['category'] ?? $category;
            if (!$word) continue;
            $w = mb_strtolower(trim((string)$word));
            if ($w === '' || str_starts_with($w, '#')) continue;
            $words[] = [
                'word' => $w,
                'language' => strtolower(trim((string)$lang)),
                'severity' => (string)$sev,
                'category' => (string)$cat,
                'replacement' => null,
                'is_active' => $active,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $this->bulkUpsert($words);
    }

    protected function persistWords(array $lines, string $lang, string $category, string $severity, bool $active): int
    {
        $now = now();
        $words = [];
        foreach ($lines as $line) {
            $val = mb_strtolower(trim((string)$line));
            if ($val === '' || str_starts_with($val, '#')) continue;
            $words[] = [
                'word' => $val,
                'language' => $lang,
                'severity' => $severity,
                'category' => $category,
                'replacement' => null,
                'is_active' => $active,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        return $this->bulkUpsert($words);
    }

    protected function bulkUpsert(array $words): int
    {
        $words = array_values(array_filter($words, fn($w) => isset($w['word']) && $w['word'] !== ''));
        $total = count($words);
        if ($total === 0) return 0;

        DB::transaction(function () use ($words) {
            foreach (array_chunk($words, 1000) as $chunk) {
                DB::table('forbidden_words')->upsert(
                    $chunk,
                    ['word','language'],
                    ['severity','category','replacement','is_active','updated_at']
                );
            }
        });
        // limpar cache (se houver)
        cache()->forget('forbidden_words_all');
        return $total;
    }
}