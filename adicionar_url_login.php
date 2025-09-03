<?php
require 'vendor/autoload.php';

// Carregar o framework Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adicionando configuração de URL para login do distribuidor...\n";

try {
    // Verificar se já existe uma configuração
    $distributor_url = DB::table('business_settings')
        ->where('key', 'distributor_login_url')
        ->first();

    if ($distributor_url) {
        echo "URL de login do distribuidor já configurada: " . $distributor_url->value . "\n";
        echo "Atualizando para 'distribuidor'...\n";
        
        // Atualizar configuração
        DB::table('business_settings')
            ->where('key', 'distributor_login_url')
            ->update(['value' => 'distribuidor']);
    } else {
        // Criar nova configuração
        DB::table('business_settings')->insert([
            'key' => 'distributor_login_url',
            'value' => 'distribuidor'
        ]);
        
        echo "URL de login do distribuidor configurada como 'distribuidor'\n";
    }
    
    echo "\nAgora você pode acessar o painel do distribuidor em:\n";
    echo "http://localhost/admin-bancas-do-bairro/login/distribuidor\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
