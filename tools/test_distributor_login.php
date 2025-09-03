<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

$emails = ['distribuidor@admin.com','distribuidor@teste.com'];
$results = [];
foreach ($emails as $email) {
    $ok = Auth::guard('distributor')->attempt([
        'email' => $email,
        'password' => '12345678',
        'status' => 1,
        'distributor' => 1,
    ], false);
    $results[] = [
        'email' => $email,
        'login_ok' => (bool)$ok,
    ];
}

$out = [
    'db' => config('database.connections.mysql.database'),
    'results' => $results,
    'login_url' => url('/login/distributor'),
];

$dir = __DIR__ . '/../storage/tmp';
if (!is_dir($dir)) { mkdir($dir, 0777, true); }
file_put_contents($dir . '/test_distributor_login.json', json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), "\n";