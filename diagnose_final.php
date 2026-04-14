<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\System\Notification;
use App\Models\System\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "--- DIAGNOSTIC START ---\n";

// 1. Current User (Simulated as Admin)
$user = User::where('Username', 'admin')->first();
if (!$user) {
    echo "ERROR: Admin user not found!\n";
} else {
    echo "Admin User Found: ID={$user->UserID}, Role='{$user->Role}'\n";
}

// 2. Check existing notifications
$all = Notification::all();
echo "Total Notifications: " . $all->count() . "\n";
foreach($all as $n) {
    echo "ID: {$n->id}, user_id: " . ($n->user_id ?? 'NULL') . ", role: '{$n->target_role}', title: '{$n->title}'\n";
}

// 3. Update roles to match strictly
$mapping = [
    'Admin' => 'Administrator',
    'Warehouse' => 'Warehouse Staff',
    'Accounting' => 'Accounting Office User',
];

foreach ($mapping as $old => $new) {
    $c = Notification::where('target_role', $old)->update(['target_role' => $new]);
    if ($c > 0) echo "Updated {$c} notifications from '{$old}' to '{$new}'\n";
}

// 4. Test Query
try {
    $got = Notification::unread()
        ->where(function($query) use ($user) {
            $query->where('user_id', '=', $user->UserID)
                  ->orWhere('target_role', '=', (string)$user->Role)
                  ->orWhere(function($q) {
                      $q->whereNull('user_id')->whereNull('target_role');
                  });
        })
        ->get();
    echo "Query test: Found " . $got->count() . " notifications for admin.\n";
} catch (\Exception $e) {
    echo "Query FAILED: " . $e->getMessage() . "\n";
}

echo "--- DIAGNOSTIC END ---\n";
