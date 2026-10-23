<?php
// php tests/Smoke/referrals.php [installed-Laravel-runtime]
// Uses only SQLite :memory:, temporary uploads and mocked outbound HTTP.
$source = dirname(__DIR__, 2); $runtime = $argv[1] ?? $source;
require $runtime . '/vendor/autoload.php';
foreach (['app/Services/ReferralRewards.php','app/Models/TenantRegistration.php','app/Observers/LeaseObserver.php','app/Livewire/Public/TenantRegistrationForm.php','app/Http/Controllers/ReferralController.php','app/Http/Middleware/RestrictSalesAccess.php'] as $file) require_once (is_file($source . '/' . $file) ? $source . '/' . $file : $runtime . '/' . $file);
$app = require $runtime . '/bootstrap/app.php';
$temp = sys_get_temp_dir() . '/referral-test-' . bin2hex(random_bytes(6));
mkdir($temp . '/framework/views',0700,true); mkdir($temp . '/app/public',0700,true);
$app->useStoragePath($temp); // Isolate before providers resolve PermissionRegistrar / cache connections.
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

config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','session.driver'=>'array','filesystems.disks.public.root'=>$temp.'/app/public','hashing.bcrypt.rounds'=>4,'services.fonnte.token'=>'test-only']);
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Services\ReferralRewards;
use App\Models\Setting;
use App\Models\User;
use App\Models\Lease;
use App\Models\TenantRegistration;
use App\Http\Controllers\ReferralController;
use App\Livewire\Public\TenantRegistrationForm;
use Illuminate\Http\Request;
DB::purge('sqlite');
if(DB::connection()->getDriverName()!=='sqlite'||DB::connection()->getDatabaseName()!==':memory:') throw new RuntimeException('Persistent DB refused');
function check($ok,$label){if(!$ok)throw new RuntimeException('FAIL: '.$label);echo 'PASS: '.$label.PHP_EOL;}
function rejected(callable $call,int $status,string $label){try{$call();}catch(Symfony\Component\HttpKernel\Exception\HttpException $e){check($e->getStatusCode()===$status,$label);return;}throw new RuntimeException('FAIL: '.$label);}
// One legacy migration uses MySQL SHOW INDEX; reproduce its index removal on SQLite.
mkdir($temp.'/migrations',0700,true);
foreach(glob($runtime.'/database/migrations/*.php') as $migrationFile){
    $text=file_get_contents($migrationFile);
    if(basename($migrationFile)==='2026_06_13_000006_drop_lease_tenant_unique.php'){
        $text="<?php return new class extends \\Illuminate\\Database\\Migrations\\Migration { public function up(): void { \\Illuminate\\Support\\Facades\\Schema::table('leases', function (\\Illuminate\\Database\\Schema\\Blueprint \$t) { \$t->dropUnique('leases_tenant_id_unique'); }); } };";
    }
    file_put_contents($temp.'/migrations/'.basename($migrationFile),$text);
}
Artisan::call('migrate',['--path'=>$temp.'/migrations','--realpath'=>true,'--force'=>true]);
if(!Illuminate\Support\Facades\Schema::hasTable('sales_agents')){
    (require $source.'/database/migrations/2026_10_23_000000_create_sales_referral_tracking.php')->up();
}
if(!Illuminate\Support\Facades\Schema::hasColumn('tenant_registrations','referral_reward_rates')) (require $source.'/database/migrations/2026_10_23_000001_add_room_type_referral_rewards.php')->up();
check(Illuminate\Support\Facades\Schema::hasColumn('tenant_registrations','sales_agent_id'),'migration creates referral schema');
(new Database\Seeders\RoleAndPermissionSeeder())->run();
require (is_file($source.'/routes/web.php') ? $source.'/routes/web.php' : $runtime.'/routes/web.php'); $app->make('router')->getRoutes()->refreshNameLookups(); $app->make('url')->setRoutes($app->make('router')->getRoutes());
$app->make('view')->getFinder()->prependLocation($source.'/resources/views');
Illuminate\Support\Facades\View::share('errors',new Illuminate\Support\ViewErrorBag());
$session = new Illuminate\Session\Store('ref-test',new Illuminate\Session\ArraySessionHandler(120));$session->start();
function req(array $data=[],?User $user=null,string $routeName='referrals.manage'): Request{
    global $app,$session; $r=Request::create('https://livingkost.com/referrals','POST',$data);$r->setLaravelSession($session);$r->setUserResolver(fn()=>$user);
    $route=$app->make('router')->getRoutes()->getByName($routeName);$route?->bind($r);$r->setRouteResolver(fn()=>$route);
    $app->instance('request',$r);$app->instance('session.store',$session);$app->make('url')->setRequest($r); if($user)Auth::setUser($user);else Auth::forgetUser();return $r;
}
Http::preventStrayRequests();Http::fake(['*'=>Http::response(['status'=>true])]);
$owner=User::create(['name'=>'Owner test','email'=>'owner@example.test','password'=>'Testing-only-password']);$owner->assignRole('owner');
$otherOwner=User::create(['name'=>'Other owner','email'=>'otherowner@example.test','password'=>'Testing-only-password']);$otherOwner->assignRole('owner');
$controller=new ReferralController();
Setting::create(['owner_id'=>$owner->id,'key'=>'referral_reward_amount','value'=>'100000','group'=>'referral']);
$controller->createSales(req(['name'=>'Sales Satu','email'=>'sales1@example.test','phone'=>'081234567890','password'=>'Testing-sales-pass','password_confirmation'=>'Testing-sales-pass'],$owner));
$controller->createSales(req(['name'=>'Sales Dua','email'=>'sales2@example.test','phone'=>'081234567891','password'=>'Testing-sales-pass','password_confirmation'=>'Testing-sales-pass'],$owner));
$controller->createSales(req(['name'=>'Sales Lain','email'=>'sales3@example.test','phone'=>'081234567892','password'=>'Testing-sales-pass','password_confirmation'=>'Testing-sales-pass'],$otherOwner));
$agent=DB::table('sales_agents')->where('owner_id',$owner->id)->orderBy('id')->first();
$sales=User::findOrFail($agent->user_id);$agent2=DB::table('sales_agents')->where('owner_id',$owner->id)->where('id','!=',$agent->id)->first();
$foreign=DB::table('sales_agents')->where('owner_id',$otherOwner->id)->first();
check($sales->hasRole('sales')&&!$sales->can('view-tenants'),'sales account has no tenant permissions');
$propertyId=DB::table('properties')->insertGetId(['owner_id'=>$owner->id,'name'=>'Kos Referral','address'=>'Test address','status'=>'active','created_at'=>now(),'updated_at'=>now()]);
$typeId=DB::table('room_types')->insertGetId(['owner_id'=>$owner->id,'property_id'=>$propertyId,'name'=>'Superior','price'=>2000000,'created_at'=>now(),'updated_at'=>now()]);
$roomId=DB::table('rooms')->insertGetId(['owner_id'=>$owner->id,'room_type_id'=>$typeId,'room_number'=>'18','floor'=>3,'status'=>'available','created_at'=>now(),'updated_at'=>now()]);
$freeId=DB::table('rooms')->insertGetId(['owner_id'=>$owner->id,'room_type_id'=>$typeId,'room_number'=>'19','floor'=>3,'status'=>'available','created_at'=>now(),'updated_at'=>now()]);
$deluxeId=DB::table('room_types')->insertGetId(['owner_id'=>$owner->id,'property_id'=>$propertyId,'name'=>'Deluxe','price'=>2200000,'created_at'=>now(),'updated_at'=>now()]);
$deluxeRoom=DB::table('rooms')->insertGetId(['owner_id'=>$owner->id,'room_type_id'=>$deluxeId,'room_number'=>'20','floor'=>3,'status'=>'available','created_at'=>now(),'updated_at'=>now()]);
$controller->rate(req(['rates'=>[$typeId=>100000,$deluxeId=>300000]],$owner));
check(ReferralRewards::rates($owner->id)[$typeId]===100000&&ReferralRewards::rates($owner->id)[$deluxeId]===300000,'owner saves distinct reward for each room type');
$token=ReferralRewards::registrationToken($owner->id);
req(['ref'=>$agent->code],null,'tenant.register');
$form=new TenantRegistrationForm();$form->mount($token);
check($form->referralCode===$agent->code&&$form->salesName==='Sales Satu','referral link resolves sales of correct owner');
foreach(['token','ownerId','referralCode','dpAmount','dpMode']as$field)check(count((new ReflectionProperty($form,$field))->getAttributes(Livewire\Attributes\Locked::class))===1,'locked public context: '.$field);
$png=$temp.'/fixture.png';file_put_contents($png,base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
$form->name='Calon Referral';$form->email='prospect@example.test';$form->phone='081234567899';$form->nik='1234567890123456';$form->ktp_photo=new Illuminate\Http\UploadedFile($png,'ktp.png','image/png',null,true);$form->submit();
$reg=TenantRegistration::withoutGlobalScopes()->firstOrFail();
check($reg->sales_agent_id===$agent->id&&(int)$reg->referral_reward_amount===100000,'submitted form persists source and reward snapshot');
$controller->rate(req(['rates'=>[$typeId=>250000,$deluxeId=>400000]],$owner));
check((int)$reg->fresh()->referral_reward_amount===100000&&$reg->fresh()->referral_reward_rates[$deluxeId]===300000,'new reward rate does not change existing referral');
req(['ref'=>$foreign->code],null,'tenant.register');rejected(fn()=>(new TenantRegistrationForm())->mount($token),404,'foreign-owner referral rejected');
req([],null,'tenant.register');$organic=new TenantRegistrationForm();$organic->mount($token);check($organic->referralCode==='','organic registration remains supported');
Auth::forgetUser();
$tenant=App\Models\Tenant::create(['owner_id'=>$owner->id,'name'=>$reg->name,'email'=>$reg->email,'phone'=>$reg->phone,'nik'=>$reg->nik,'status'=>'active']);
$reg->update(['status'=>'approved','tenant_id'=>$tenant->id,'dp_method'=>'manual','dp_amount'=>200000,'dp_status'=>'submitted','dp_proof'=>'dp-proof.png']);
$lease=Lease::create(['owner_id'=>$owner->id,'tenant_id'=>$tenant->id,'room_id'=>$roomId,'start_date'=>today()->toDateString(),'end_date'=>today()->addMonth()->toDateString(),'due_date_per_month'=>1,'deposit_amount'=>200000,'status'=>'active']);
check(DB::table('referral_rewards')->where('tenant_id',$tenant->id)->count()===1,'contract creation snapshots reward and rent');
ReferralRewards::syncOwner($owner->id);check(DB::table('referral_rewards')->whereNotNull('earned_at')->count()===0,'DP/proof alone cannot earn reward');
$invoice=App\Models\Invoice::create(['owner_id'=>$owner->id,'lease_id'=>$lease->id,'amount'=>1800000,'month_year'=>today()->format('Y-m'),'due_date'=>today(),'status'=>'pending','reference_number'=>'TEST-INV-001']);
ReferralRewards::syncOwner($owner->id);check(DB::table('referral_rewards')->whereNotNull('earned_at')->count()===0,'pending first invoice cannot earn reward');
$invoice->update(['status'=>'paid']);ReferralRewards::syncOwner($owner->id);
check(DB::table('referral_rewards')->whereNotNull('earned_at')->count()===0,'unverified DP not counted toward full rent');
$controller->verifyDp(req([],$owner),$reg->id);
$reward=DB::table('referral_rewards')->where('tenant_id',$tenant->id)->first();
check($reward->earned_at!==null&&(int)$reward->amount===100000,'verified DP plus verified remaining first rent earns snapshot reward');
ReferralRewards::syncOwner($owner->id);ReferralRewards::syncOwner($owner->id);check(DB::table('referral_rewards')->count()===1,'reconciliation is idempotent');
$controller->paid(req(['reference'=>'TEST-TRANSFER-001'],$owner),$reward->id);
$controller->paid(req(['reference'=>'DUPLICATE'],$owner),$reward->id);
check(DB::table('referral_rewards')->where('id',$reward->id)->value('payment_reference')==='TEST-TRANSFER-001','reward paid record cannot be counted or overwritten twice');
$reg2=TenantRegistration::create(['owner_id'=>$owner->id,'sales_agent_id'=>$agent2->id,'referral_reward_amount'=>250000,'name'=>'Private other-sales lead','email'=>'otherlead@example.test','phone'=>'081298765432','status'=>'pending']);
$salesHtml=$controller->sales(req([],$sales,'referrals.sales'))->getContent();
check(str_contains($salesHtml,'Calon Referral')&&!str_contains($salesHtml,'Private other-sales lead'),'sales sees only own leads');
check(!str_contains($salesHtml,'1234567890123456')&&!str_contains($salesHtml,'081234567899')&&!str_contains($salesHtml,'dp-proof.png'),'sales response excludes identity/proof data and masks phone');
check(str_contains($salesHtml,'Terisi')&&str_contains($salesHtml,'Tersedia')&&str_contains($salesHtml,'2.000.000'),'sales inventory includes prices and computed occupied/available status');
$middleware=new App\Http\Middleware\RestrictSalesAccess();
rejected(fn()=>$middleware->handle(req([],$sales,'tenants.index'),fn()=>response('BAD')),403,'sales blocked from owner tenant endpoints');
rejected(fn()=>$controller->rate(req(['rates'=>[$typeId=>1]],$sales)),403,'sales cannot set reward rate');
$livewireRequest=req([],$sales,'referrals.sales');
$livewireRoute=new Illuminate\Routing\Route('POST','livewire/update',['as'=>'livewire.update']);$livewireRoute->bind($livewireRequest);$livewireRequest->setRouteResolver(fn()=>$livewireRoute);
rejected(fn()=>$middleware->handle($livewireRequest,fn()=>response('BAD')),403,'sales cannot call owner Livewire actions');
rejected(fn()=>$controller->paid(req(['reference'=>'BAD'],$sales),$reward->id),403,'sales cannot mark reward paid');
rejected(fn()=>$controller->toggle(req([],$otherOwner),$agent->id),404,'owner cannot change another owner sales');
$controller->toggle(req([],$owner),$agent->id);
rejected(fn()=>$controller->sales(req([],$sales,'referrals.sales')),403,'inactive sales cannot access dashboard');
req(['ref'=>$agent->code],null,'tenant.register');rejected(fn()=>(new TenantRegistrationForm())->mount($token),404,'inactive referral link cannot register');
$controller->toggle(req([],$owner),$agent->id);
// A later month payment must not substitute for the first month.
req([],$owner);
$reg2->update(['status'=>'approved','tenant_id'=>App\Models\Tenant::create(['owner_id'=>$owner->id,'name'=>'Second Tenant','nik'=>'2222222222222222','phone'=>'081211111111','status'=>'active'])->id]);
$lease2=Lease::create(['owner_id'=>$owner->id,'tenant_id'=>$reg2->tenant_id,'room_id'=>$freeId,'start_date'=>today()->subMonth()->toDateString(),'end_date'=>today()->addMonth()->toDateString(),'due_date_per_month'=>1,'deposit_amount'=>0,'status'=>'active']);
$late=App\Models\Invoice::create(['owner_id'=>$owner->id,'lease_id'=>$lease2->id,'amount'=>2000000,'month_year'=>today()->format('Y-m'),'due_date'=>today(),'status'=>'pending','reference_number'=>'TEST-INV-002']);$late->update(['status'=>'paid']);ReferralRewards::syncOwner($owner->id);
check(DB::table('referral_rewards')->where('tenant_id',$reg2->tenant_id)->value('earned_at')===null,'later-month invoice cannot earn first-rent reward');
$first=App\Models\Invoice::create(['owner_id'=>$owner->id,'lease_id'=>$lease2->id,'amount'=>2000000,'month_year'=>today()->subMonth()->format('Y-m'),'due_date'=>today(),'status'=>'paid','reference_number'=>'TEST-INV-003']);
ReferralRewards::syncOwner($owner->id);check(DB::table('referral_rewards')->where('tenant_id',$reg2->tenant_id)->value('earned_at')===null,'paid flag without verification timestamp is insufficient');
DB::table('invoices')->where('id',$first->id)->update(['verified_at'=>now(),'amount'=>100000]);ReferralRewards::syncOwner($owner->id);
check(DB::table('referral_rewards')->where('tenant_id',$reg2->tenant_id)->value('earned_at')===null,'verified partial amount cannot earn reward');
DB::table('invoices')->where('id',$first->id)->update(['amount'=>2000000]);ReferralRewards::syncOwner($owner->id);
check(DB::table('referral_rewards')->where('tenant_id',$reg2->tenant_id)->value('earned_at')!==null,'verified full first invoice earns reward without DP');
// The same registration-time card selects a different reward for Deluxe.
req([],$owner);
$t3=App\Models\Tenant::create(['owner_id'=>$owner->id,'name'=>'Deluxe Referral','nik'=>'3333333333333333','phone'=>'081233333333','status'=>'active']);
$r3=TenantRegistration::create(['owner_id'=>$owner->id,'sales_agent_id'=>$agent->id,'name'=>'Deluxe Referral','phone'=>'081233333333','status'=>'approved','tenant_id'=>$t3->id,'referral_reward_amount'=>100000,'referral_reward_rates'=>$reg->fresh()->referral_reward_rates]);
$l3=Lease::create(['owner_id'=>$owner->id,'tenant_id'=>$t3->id,'room_id'=>$deluxeRoom,'start_date'=>today()->toDateString(),'end_date'=>today()->addMonth()->toDateString(),'due_date_per_month'=>1,'deposit_amount'=>0,'status'=>'active']);
check((int)DB::table('referral_rewards')->where('tenant_id',$t3->id)->value('amount')===300000,'Deluxe uses saved type reward rather than flat or current rate');
$i3=App\Models\Invoice::create(['owner_id'=>$owner->id,'lease_id'=>$l3->id,'amount'=>2200000,'month_year'=>today()->format('Y-m'),'due_date'=>today(),'status'=>'pending','reference_number'=>'TEST-INV-004']);$i3->update(['status'=>'paid']);ReferralRewards::syncOwner($owner->id);
check(DB::table('referral_rewards')->where('tenant_id',$t3->id)->value('earned_at')!==null,'type-specific reward still requires verified first rent');
$controller->rate(req(['rates'=>[$typeId=>0,$deluxeId=>0]],$owner));ReferralRewards::syncOwner($owner->id);
check((int)DB::table('referral_rewards')->where('tenant_id',$t3->id)->value('amount')===300000,'earned reward unchanged after owner edits type rates');
check(ReferralRewards::rates($owner->id)[$typeId]===0,'explicit zero reward respected');
try{$controller->rate(req(['rates'=>[$typeId=>100,$deluxeId=>200,999999=>300]],$owner));throw new RuntimeException('Unknown room type accepted');}catch(Illuminate\Validation\ValidationException $e){check(true,'unknown or foreign room type cannot be configured');}
// Keep representative current rates in the visual test fixture.
$controller->rate(req(['rates'=>[$typeId=>250000,$deluxeId=>400000]],$owner));
$salesHtml=$controller->sales(req([],$sales,'referrals.sales'))->getContent();
check(str_contains($salesHtml,'Reward referral baru')&&str_contains($salesHtml,'400.000'),'sales room list displays current type-specific reward');
foreach(glob($source.'/resources/views/referrals/*.blade.php')as$file){token_get_all($app->make('blade.compiler')->compileString(file_get_contents($file)),TOKEN_PARSE);}
check(true,'all referral Blade templates compile');
// Render owner page with its normal sidebar and the test sales page as visual fixtures.
$ownerHtml=$controller->manage(req([],$owner))->getContent();check(str_contains($ownerHtml,'Pengaturan reward')&&str_contains($ownerHtml,'Sales Satu'),'owner management dashboard renders');
if(isset($argv[2])){if(!is_dir($argv[2]))mkdir($argv[2],0700,true);file_put_contents($argv[2].'/sales.html',$salesHtml);file_put_contents($argv[2].'/owner.html',$ownerHtml);}
echo 'All referral checks passed; production records and real payments unchanged.'.PHP_EOL;
