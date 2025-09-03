<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Test the addon_published_status function
    echo "Testing addon_published_status('TaxModule'): ";
    $status = addon_published_status('TaxModule');
    echo $status ? 'true' : 'false';
    echo "\n";
    
    // Test the getTaxSystemType method
    echo "Testing Helpers::getTaxSystemType(): ";
    $taxData = \App\CentralLogics\Helpers::getTaxSystemType();
    echo "Success\n";
    print_r($taxData);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}