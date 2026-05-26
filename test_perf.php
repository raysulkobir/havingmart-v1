<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

$start = microtime(true);
app()->call('App\Http\Controllers\HomeController@index')->render();
echo "Total time: " . (microtime(true) - $start) . "\n";
