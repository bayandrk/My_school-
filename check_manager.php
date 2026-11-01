<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$managers = App\Models\User::where('role', 'manager')->get();
foreach ($managers as $m) {
    echo $m->id . ' | ' . $m->name . ' | school_id=' . $m->school_id . PHP_EOL;
}
echo PHP_EOL;
$schools = App\Models\School::all();
foreach ($schools as $s) {
    echo 'School#' . $s->id . ' | ' . $s->name . ' | logo=' . ($s->logo ?? 'NULL') . ' | color=' . $s->color . PHP_EOL;
}
