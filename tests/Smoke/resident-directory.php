<?php

// Run against an isolated in-memory database; never uses the website's database.
// php tests/Smoke/resident-directory.php [path-to-installed-Laravel-runtime]
$source = dirname(__DIR__, 2);
$runtime = $argv[1] ?? $source;
require $runtime . '/vendor/autoload.php';
$app = require $runtime . '/bootstrap/app.php';
$temporary = sys_get_temp_dir() . '/resident-directory-test-' . bin2hex(random_bytes(6));
mkdir($temporary . '/app/public', 0700, true);
mkdir($temporary . '/views', 0700, true);
mkdir($temporary . '/framework/views', 0700, true);
$app->useStoragePath($temporary);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array', 'view.compiled' => $temporary . '/views']);
Illuminate\Support\Facades\DB::purge('sqlite');
if (Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite' || Illuminate\Support\Facades\DB::connection()->getDatabaseName() !== ':memory:') {
    throw new RuntimeException('Refusing to test against a persistent database.');
}
require_once $source . '/app/Services/ResidentDirectory.php';
require_once $source . '/app/Http/Controllers/ResidentDirectoryController.php';
require $source . '/routes/web.php';
$app->make('router')->getRoutes()->refreshNameLookups();
$app->make('router')->getRoutes()->refreshActionLookups();
$app->make('url')->setRoutes($app->make('router')->getRoutes());
$app->make('view')->getFinder()->prependLocation($source . '/resources/views');
Illuminate\Support\Facades\View::share('errors', new Illuminate\Support\ViewErrorBag());
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Services\ResidentDirectory;
use App\Http\Controllers\ResidentDirectoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

foreach (['settings', 'properties', 'room_types', 'rooms', 'tenants', 'leases'] as $table) {
    Schema::create($table, function ($t) use ($table) {
        $t->id(); $t->unsignedBigInteger('owner_id')->nullable(); $t->timestamps();
        if ($table === 'settings') { $t->string('key'); $t->text('value')->nullable(); $t->string('group')->default('general'); return; }
        if ($table === 'properties') { $t->string('name'); return; }
        if ($table === 'room_types') { $t->unsignedBigInteger('property_id'); return; }
        if ($table === 'rooms') { $t->unsignedBigInteger('room_type_id'); $t->string('room_number'); return; }
        $t->string('status'); $t->softDeletes();
        if ($table === 'tenants') { $t->string('name'); $t->string('phone')->nullable(); $t->string('nik')->nullable(); $t->string('ktp_photo')->nullable(); return; }
        $t->unsignedBigInteger('tenant_id'); $t->unsignedBigInteger('room_id'); $t->date('start_date');
    });
}
function check($ok, $label) { if (! $ok) { throw new RuntimeException('FAIL: ' . $label); } echo 'PASS: ' . $label . PHP_EOL; }
function rejects(callable $call, int $status, string $label) {
    try { $call(); } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) { check($e->getStatusCode() === $status, $label); return; }
    throw new RuntimeException('FAIL: ' . $label);
}
function input(array $data = []): Request {
    global $app, $session;
    $r = Request::create('https://livingkost.com/', 'POST', $data); $r->setLaravelSession($session);
    $app->instance('request', $r); $app->instance('session.store', $session); $app->make('url')->setRequest($r);
    return $r;
}
$session = new Illuminate\Session\Store('directory-test', new Illuminate\Session\ArraySessionHandler(120));
$session->start();
$controller = new ResidentDirectoryController();
$token = str_repeat('A', 48); $otherToken = str_repeat('B', 48);
foreach ([1 => $token, 2 => $otherToken] as $owner => $ownerToken) {
    Setting::create(['owner_id' => $owner, 'key' => 'resident_directory_token', 'value' => $ownerToken]);
    Setting::create(['owner_id' => $owner, 'key' => 'resident_directory_phones', 'value' => '["6281234567890"]']);
    Setting::create(['owner_id' => $owner, 'key' => 'resident_directory_version', 'value' => 'version-one']);
    DB::table('properties')->insert(['id' => $owner, 'owner_id' => $owner, 'name' => 'Kos test ' . $owner]);
    DB::table('room_types')->insert(['id' => $owner, 'owner_id' => $owner, 'property_id' => $owner]);
}
for ($i = 1; $i <= 7; $i++) {
    $owner = $i === 2 ? 2 : 1;
    DB::table('rooms')->insert(['id' => $i, 'owner_id' => $owner, 'room_type_id' => $owner, 'room_number' => (string) $i]);
    DB::table('tenants')->insert(['id' => $i, 'owner_id' => $owner, 'name' => 'Test resident ' . $i, 'phone' => '081234567890', 'nik' => str_repeat((string) $i, 16), 'status' => $i === 5 ? 'inactive' : 'active', 'deleted_at' => $i === 6 ? now() : null, 'ktp_photo' => $i === 1 ? 'fixture.png' : null]);
    DB::table('leases')->insert(['id' => $i, 'owner_id' => $owner, 'tenant_id' => $i, 'room_id' => $i, 'status' => $i === 3 ? 'completed' : 'active', 'start_date' => $i === 4 ? today()->addDay() : today()->subMonth(), 'deleted_at' => $i === 7 ? now() : null]);
}
check(ResidentDirectory::residents(1)->pluck('tenant_id')->all() === [1], 'only current active residents of the document owner');
check(ResidentDirectory::normalizePhone('+62 812-3456-7890') === '6281234567890', 'phone formatting normalized');
$gate = $controller->show(input(), $token);
check(!str_contains($gate->getContent(), 'Test resident 1') && str_contains($gate->getContent(), 'Buka dokumen'), 'locked page contains no resident data');
check(str_contains($gate->headers->get('Cache-Control'), 'no-store'), 'private document cannot be cached');
rejects(fn () => $controller->show(input(), str_repeat('C', 48)), 404, 'unknown document rejected');
rejects(fn () => $controller->photo(input(), $token, 1), 403, 'KTP photo locked before access');
try { $controller->unlock(input(['phone' => '081299999999']), $token); throw new RuntimeException('Unregistered phone accepted'); }
catch (Illuminate\Validation\ValidationException $e) { check(!$session->has('resident_directory.1'), 'unregistered phone cannot unlock'); }
$controller->unlock(input(['phone' => '081234567890']), $token);
$opened = $controller->show(input(), $token)->getContent();
check(str_contains($opened, 'Test resident 1') && !str_contains($opened, 'Test resident 2'), 'allowed phone opens only this owner document');
check(!str_contains($opened, '/storage/fixture.png'), 'photo public storage path not exposed');
check(!str_contains($controller->show(input(), $otherToken)->getContent(), 'Test resident 2'), 'document sessions cannot cross owners');
rejects(fn () => $controller->photo(input(), $token, 2), 404, 'another owner photo cannot be opened');
file_put_contents($temporary . '/app/public/fixture.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
check($controller->photo(input(), $token, 1)->getStatusCode() === 200, 'permitted KTP photo streamed');
$controller->close(input(), $token);
check(!$session->has('resident_directory.1'), 'close button clears access');
$controller->unlock(input(['phone' => '081234567890']), $token);
Carbon::setTestNow(now()->addMinutes(31));
check(!str_contains($controller->show(input(), $token)->getContent(), 'Test resident 1'), 'access expires after 30 minutes');
Carbon::setTestNow();
$controller->unlock(input(['phone' => '081234567890']), $token);
Setting::where('owner_id', 1)->where('key', 'resident_directory_phones')->update(['value' => '[]']);
rejects(fn () => $controller->photo(input(), $token, 1), 403, 'removing phone revokes photo access immediately');
$owner = new class extends App\Models\User {
    public function isOwner(): bool { return true; }
    public function isCoOwnerViewer(): bool { return false; }
};
$owner->id = 1;
$r = input(['phones' => "081234567890\n+62 812-3456-7890"]); $r->setUserResolver(fn () => $owner);
$controller->save($r);
check(ResidentDirectory::phones(1) === ['6281234567890'] && ResidentDirectory::phones(2) === ['6281234567890'], 'owner saves normalized distinct phones without changing another owner');
$controller->unlock(input(['phone' => '081234567890']), $token);
$r = input(['phones' => '081234567890']); $r->setUserResolver(fn () => $owner); $controller->save($r);
check(!str_contains($controller->show(input(), $token)->getContent(), 'Test resident 1'), 'updating access revokes existing sessions');
rejects(fn () => $controller->save(input(['phones' => '081234567890'])), 403, 'guest cannot manage access');
$r = input(['phones' => 'not-a-phone']); $r->setUserResolver(fn () => $owner);
try { $controller->save($r); throw new RuntimeException('Invalid phone accepted'); }
catch (Illuminate\Validation\ValidationException $e) { check(true, 'invalid phone rejected when owner saves'); }
$route = app('router')->getRoutes()->getByName('resident-directory.unlock');
check(in_array('throttle:5,1', $route->gatherMiddleware(), true), 'opening attempts throttled');
foreach (['manage', 'show', 'styles'] as $view) {
    $compiled = $app->make('blade.compiler')->compileString(file_get_contents($source . '/resources/views/resident-directory/' . $view . '.blade.php'));
    token_get_all($compiled, TOKEN_PARSE);
    check(true, 'Blade syntax: ' . $view);
}
echo 'All checks passed on SQLite :memory:; production data unchanged.' . PHP_EOL;
