<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$emails = ['distribuidor@admin.com','distribuidor@teste.com'];

$rows = DB::table('vendors')->whereIn('email', $emails)->get();
$data = [];
foreach ($rows as $r) {
    $data[] = [
        'id' => $r->id,
        'email' => $r->email,
        'status' => (int)$r->status,
        'distributor' => (int)$r->distributor,
        'password_matches_12345678' => Hash::check('12345678', $r->password),
        'updated_at' => (string)$r->updated_at,
    ];
}

$out = [
    'db' => config('database.connections.mysql.database'),
    'count' => count($data),
    'result' => $data,
];

$file = __DIR__ . '/_vendors_check.json';
file_put_contents($file, json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), "\n";