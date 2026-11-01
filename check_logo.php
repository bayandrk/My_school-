<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schools = App\Models\School::all();
foreach ($schools as $s) {
    $path = 'public/storage/' . $s->logo;
    $exists = $s->logo ? (file_exists($path) ? 'OK' : 'FILE MISSING') : 'no logo';
    $url = $s->logo ? asset('storage/' . $s->logo) : '-';
    echo 'School#' . $s->id . ' | ' . $s->name . ' | logo=' . ($s->logo ?? 'NULL') . ' | ' . $exists . ' | ' . $url . PHP_EOL;
}
