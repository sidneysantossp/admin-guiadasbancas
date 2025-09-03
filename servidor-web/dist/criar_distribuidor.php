<?php
require 'vendor/autoload.php';

// Carregar o framework Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Iniciando criação de distribuidor de teste...\n";

try {
    // Verificar se já existe um distribuidor com o email especificado
    $distribuidor = DB::table('vendors')
        ->where('email', 'distribuidor@teste.com')
        ->first();

    if ($distribuidor) {
        echo "Um distribuidor com o email 'distribuidor@teste.com' já existe!\n";
        echo "Redefinindo senha e garantindo que está ativo e marcado como distribuidor...\n";
        
        // Atualizar o distribuidor existente
        DB::table('vendors')
            ->where('email', 'distribuidor@teste.com')
            ->update([
                'password' => Hash::make('123456'),
                'status' => 1,
                'distributor' => 1
            ]);
            
        echo "Distribuidor atualizado com sucesso!\n";
    } else {
        // Criar um novo distribuidor
        DB::table('vendors')->insert([
            'f_name' => 'Distribuidor',
            'l_name' => 'Teste',
            'email' => 'distribuidor@teste.com',
            'phone' => '999999999',
            'password' => Hash::make('123456'),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 1,
            'distributor' => 1
        ]);
        
        echo "Distribuidor criado com sucesso!\n";
    }
    
    echo "\nCredenciais do distribuidor:\n";
    echo "Email: distribuidor@teste.com\n";
    echo "Senha: 123456\n";
    
    echo "\nAgora você pode fazer login em http://127.0.0.1:8000/auth/login\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
