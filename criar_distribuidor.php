<?php
require 'vendor/autoload.php';

// Carregar o framework Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Iniciando padronização de credenciais dos distribuidores...\n";

$alvoSenha = '12345678';
$agora = now();

$alvos = [
    [
        'email' => 'distribuidor@admin.com',
        'f_name' => 'Distribuidor',
        'l_name' => 'Master',
        'phone'  => '11999999999',
    ],
    [
        'email' => 'distribuidor@teste.com',
        'f_name' => 'Distribuidor',
        'l_name' => 'Teste',
        'phone'  => '999999999',
    ],
];

try {
    foreach ($alvos as $alvo) {
        $existe = DB::table('vendors')->where('email', $alvo['email'])->first();

        if ($existe) {
            echo "Atualizando distribuidor '{$alvo['email']}'...\n";
            DB::table('vendors')
                ->where('email', $alvo['email'])
                ->update([
                    'password'    => Hash::make($alvoSenha),
                    'status'      => 1,
                    'distributor' => 1,
                    'updated_at'  => $agora,
                ]);
            echo "✔ Senha redefinida e flags atualizadas.\n";
        } else {
            echo "Criando distribuidor '{$alvo['email']}'...\n";
            DB::table('vendors')->insert([
                'f_name'      => $alvo['f_name'],
                'l_name'      => $alvo['l_name'],
                'email'       => $alvo['email'],
                'phone'       => $alvo['phone'],
                'password'    => Hash::make($alvoSenha),
                'created_at'  => $agora,
                'updated_at'  => $agora,
                'status'      => 1,
                'distributor' => 1,
            ]);
            echo "✔ Distribuidor criado.\n";
        }
        echo "Credenciais: {$alvo['email']} / {$alvoSenha}\n\n";
    }

    echo "Concluído. Agora você pode fazer login em http://127.0.0.1:8000/auth/login\n";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
