<?php

// php tests/Smoke/room-occupancy.php [path-to-installed-Laravel-runtime]
// All fixtures are stored in SQLite :memory:, never in the website database.
$source = dirname(__DIR__, 2);
$runtime = $argv[1] ?? $source;
require $runtime . '/vendor/autoload.php';
foreach (['app/Models/Room.php', 'app/Policies/RoomPolicy.php', 'app/Livewire/Room/RoomForm.php', 'app/Observers/LeaseObserver.php'] as $file) require_once $source . '/' . $file;
$app = require $runtime . '/bootstrap/app.php';
$temp = sys_get_temp_dir() . '/room-occupancy-test-' . bin2hex(random_bytes(6));
mkdir($temp . '/framework/views', 0700, true);
$app->useStoragePath($temp);
// Isolate before providers resolve PermissionRegistrar / cache connections.
$app->beforeBootstrapping(Illuminate\Foundation\Bootstrap\RegisterProviders::class, function ($app) {
    $app['config']->set('database.default', 'sqlite');
    $app['config']->set('database.connections.sqlite.database', ':memory:');
    $app['config']->set('cache.default', 'array');
    $app['config']->set('permission.cache.store', 'array');
    $app['config']->set('permission.cache.key', 'smoke.permission.' . bin2hex(random_bytes(12)));
    $app['config']->set('session.driver', 'array');
});
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (! app(Spatie\Permission\PermissionRegistrar::class)->getCacheStore() instanceof Illuminate\Cache\ArrayStore) {
    throw new RuntimeException('Refusing shared permission cache in smoke tests');
}

config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Room;
use App\Models\Lease;
use App\Livewire\Room\RoomForm;
use App\Livewire\Room\RoomIndex;
DB::purge('sqlite');
if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') throw new RuntimeException('Refusing persistent database');
Schema::create('room_types', function ($t) { $t->id(); $t->unsignedBigInteger('owner_id'); $t->unsignedBigInteger('property_id'); $t->string('name'); });
Schema::create('rooms', function ($t) { $t->id(); $t->unsignedBigInteger('owner_id'); $t->unsignedBigInteger('room_type_id'); $t->string('room_number'); $t->integer('floor'); $t->string('status'); $t->timestamps(); });
Schema::create('leases', function ($t) { $t->id(); $t->unsignedBigInteger('owner_id')->nullable(); $t->unsignedBigInteger('room_id'); $t->unsignedBigInteger('tenant_id')->nullable(); $t->string('status'); $t->date('start_date'); $t->date('end_date')->nullable(); $t->string('created_by')->nullable(); $t->string('updated_by')->nullable(); $t->softDeletes(); $t->timestamps(); });
function check($condition, $label) { if (!$condition) throw new RuntimeException('FAIL: '.$label); echo 'PASS: '.$label.PHP_EOL; }
$user = new class extends App\Models\User {
    public bool $allow = true;
    public function hasPermissionTo($permission, $guardName = null): bool { return $this->allow && in_array($permission, ['edit-room', 'delete-room', 'create-room', 'view-rooms'], true); }
    public function isSuperAdmin(): bool { return false; }
    public function ownerId(): ?int { return 1; }
};
$user->id = 1;
Auth::setUser($user);
Gate::policy(Room::class, App\Policies\RoomPolicy::class);
DB::table('room_types')->insert(['id'=>1,'owner_id'=>1,'property_id'=>1,'name'=>'Test type']);
$room = Room::create(['room_type_id'=>1,'room_number'=>'18','floor'=>3,'status'=>'occupied']);
check($room->computed_status === 'available', 'stale occupied flag without leases is available');
check(Gate::allows('update', $room) && Gate::allows('delete', $room), 'empty room can be edited by permitted user');
$form = new RoomForm(); $form->mount($room->id);
check($form->status === 'available', 'edit form converts stale flag to valid selectable status');
$index = new RoomIndex(); $index->openEditModal($room);
check($index->showModal && $index->editingId === $room->id, 'edit modal opens for empty room');
$user->allow = false;
check(Gate::denies('update', $room) && Gate::denies('delete', $room), 'empty room still requires user permissions');
$user->allow = true;
$room->update(['status'=>'maintenance']);
check($room->fresh()->computed_status === 'maintenance', 'maintenance flag preserved without lease');
$room->update(['status'=>'available']);
$pending = Lease::create(['room_id'=>$room->id,'status'=>'pending','start_date'=>today()]);
check($room->fresh()->status === 'available' && $room->fresh()->computed_status === 'available', 'pending contract does not occupy room');
Lease::create(['room_id'=>$room->id,'status'=>'completed','start_date'=>today()]);
check($room->fresh()->status === 'available', 'completed contract creation does not occupy room');
$active = Lease::create(['room_id'=>$room->id,'status'=>'active','start_date'=>today()]);
check($room->fresh()->status === 'occupied' && $room->fresh()->computed_status === 'occupied', 'active lease occupies room');
// Even an outdated raw available flag must not bypass the update policy.
DB::table('rooms')->where('id',$room->id)->update(['status'=>'available']);
check(Gate::denies('update', $room->fresh()) && Gate::denies('delete', $room->fresh()), 'active lease blocks editing despite raw available flag');
$blocked = new RoomIndex(); $blocked->openEditModal($room->fresh());
check(!$blocked->showModal && $blocked->errorMessage !== '', 'occupied room edit modal remains blocked');
$active->update(['status'=>'completed']);
check($room->fresh()->status === 'available' && Gate::allows('update', $room->fresh()), 'completed lease releases room');
$active2 = Lease::create(['room_id'=>$room->id,'status'=>'active','start_date'=>today()]);
DB::table('leases')->where('id',$active2->id)->update(['deleted_at'=>now()]);
check($room->fresh()->computed_status === 'available' && Gate::allows('update', $room->fresh()), 'deleted active lease cannot keep room locked');
echo 'All occupancy checks passed on SQLite :memory:; production records unchanged.'.PHP_EOL;
