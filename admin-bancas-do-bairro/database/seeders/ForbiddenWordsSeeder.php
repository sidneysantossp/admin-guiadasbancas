<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForbiddenWordsSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $words = [];

        // Português (PT/BR)
        $pt = [
            // insultos comuns
            'idiota','imbecil','estúpido','burro','otário','palhaço','vagabundo','canalha','parasita','cretino','energúmeno','moleque','tapado','porco','imbecis','merda','bosta',
            // ofensas/obscenidades
            'caralho','porra','puta','puto','pqp','fdp','cu','viado','bicha','buceta','piroca','pau no cu','arrombado','safado','cansei de ser
',
            // racismo/xenofobia (exemplos a serem bloqueados)
            'macaco','preto imundo','preta imunda','branquelo','chinês imundo','nordestino de m','nordestina de m','cigano imundo',
            // violência
            'matar você','vou te matar','vou te quebrar','espancar','estupro','estuprar','se matar','suicidar',
            // spam/scam
            'ganhe dinheiro fácil','renda garantida','clique aqui e ganhe','pirâmide financeira','aposta garantida','lucro garantido',
        ];

        // Inglês
        $en = [
            // insults
            'idiot','moron','stupid','dumb','loser','jerk','trash','scum','retard','retarded','bastard','asshole','dickhead','cunt','bitch','fuck','shit','bullshit','piss off','motherfucker','son of a bitch',
            // slurs (to block)
            'chink','spic','nigger','faggot','tranny','kyke','kike','camel jockey','raghead',
            // violence
            'kill you','i will kill you','rape you','i will rape you','beat you up','hang yourself','go kill yourself','suicide',
            // scam/spam
            'make money fast','get rich quick','click here to win','financial pyramid','guaranteed profit','risk-free betting'
        ];

        foreach ($pt as $w) {
            $words[] = [
                'word' => mb_strtolower(trim($w)),
                'language' => 'pt',
                'severity' => 'high',
                'category' => 'default',
                'replacement' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($en as $w) {
            $words[] = [
                'word' => mb_strtolower(trim($w)),
                'language' => 'en',
                'severity' => 'high',
                'category' => 'default',
                'replacement' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Inserir ignorando duplicadas
        foreach (array_chunk($words, 500) as $chunk) {
            DB::table('forbidden_words')->upsert($chunk, ['word','language'], ['severity','category','replacement','is_active','updated_at']);
        }
    }
}