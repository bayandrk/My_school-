<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $req = \App\Models\SchoolJoinRequest::find(3);
    if ($req) {
        $req->update([
            'status' => 'approved',
            'processed_by' => 1,
            'processed_at' => now(),
        ]);
        echo "UPDATED OK\n";
    } else {
        echo "REQ 3 NOT FOUND\n";
    }
} catch (\Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
