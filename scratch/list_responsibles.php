<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Responsable;
use App\Models\User;

echo "--- LIST OF RESPONSABLES ---\n";
foreach (Responsable::all() as $r) {
    echo "ID: {$r->id} | Nombre: {$r->nombre} | User ID: {$r->user_id}\n";
}

echo "--- LIST OF USERS AND THEIR RESPONSABLES ---\n";
foreach (User::all() as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Responsable ID: " . ($u->responsable_id ?? 'NULL') . "\n";
}
