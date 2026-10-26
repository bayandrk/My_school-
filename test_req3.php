<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $req = \App\Models\SchoolJoinRequest::find(3);
    if ($req) {
        $req->status = 'approved';
        $req->processed_by = 1;
        $req->processed_at = now();
        $req->save();
        echo "OK\n";
    } else {
        echo "NOT FOUND\n";
    }
} catch (\Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
