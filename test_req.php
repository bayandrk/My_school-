<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $req = \App\Models\SchoolJoinRequest::first();
    if ($req) {
        $req->status = 'approved';
        $req->processed_by = 1;
        $req->processed_at = now();
        $req->save();
        echo "SUCCESS SAVING\n";
    } else {
        echo "NO REQUEST FOUND\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
