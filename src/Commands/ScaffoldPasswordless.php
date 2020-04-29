<?php

namespace JayTheGeek\LaravelScaffold\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class ScaffoldPasswordless extends Command
{
    protected $packageFolder;
    protected $stubsFolder;
    protected $migrationsFolder;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:passwordless';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold it out';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $fileSystem;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $fileSystem
     * @param Composer $composer
     */
    public function __construct(Filesystem $fileSystem, Composer $composer)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;

        $this->composer = $composer;

        $this->packageFolder = dirname(__DIR__, 2);
        $this->stubsFolder = dirname(__DIR__, 2) . '/stubs';
        $this->migrationsFolder = dirname(__DIR__, 2) . '/migrations';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->testingCleanUp();

        $this->setupMultipleEnvs();

        $this->setupSelfUpdate();

        $this->setupJsFile();

        $this->setupLoginController();

        $this->setupMigrations();

        $this->setupViews();
    }

    private function testingCleanUp()
    {
        $this->fileSystem->deleteDirectory(app_path() . '/Console/Commands');
        $this->fileSystem->deleteDirectory(app_path() . '/Handlers');
        $this->fileSystem->deleteDirectory(app_path() . '/Mail');
        $this->fileSystem->deleteDirectory(app_path() . '/Handlers');

        $this->fileSystem->deleteDirectory(resource_path('views') . '/mails');
        $this->fileSystem->deleteDirectory(resource_path('js') . '/areas');
        $this->fileSystem->deleteDirectory(resource_path('js') . '/auth');
        $this->fileSystem->deleteDirectory(resource_path('js') . '/layouts');
        $this->fileSystem->deleteDirectory(resource_path('views') . '/pages');
    }

    private function setupMultipleEnvs()
    {

        $envContents = $this->fileSystem->get($this->stubsFolder . '/env.stub');

        // Setup local env
        if ($this->fileSystem->exists(base_path() . '/.local.env')) {
            $this->fileSystem->delete(base_path() . '/.local.env');
        }
        $this->fileSystem->put(base_path() . '/.local.env', $envContents);

        // Setup production env
        if ($this->fileSystem->exists(base_path() . '/.production.env')) {
            $this->fileSystem->delete(base_path() . '/.production.env');
        }
        $this->fileSystem->put(base_path() . '/.production.env', $envContents);

        // Setup main env
        if ($this->fileSystem->exists(base_path() . '/.env')) {
            $this->fileSystem->delete(base_path() . '/.env');
            $this->fileSystem->append(base_path() . '/.env', 'APP_ENV=local');
        } else {
            $this->fileSystem->put(base_path() . '/.env', 'APP_ENV=local');
        }

        // Setup environment detection
        $appEnvironmentContents = $this->fileSystem->get($this->stubsFolder . '/environment.php.stub');
        $this->fileSystem->put(base_path('bootstrap') . '/environment.php', $appEnvironmentContents);

        // Delete the basic app file and replace with modified one
        $appContents = $this->fileSystem->get($this->stubsFolder . '/app.php.stub');
        $this->fileSystem->delete(base_path('bootstrap') . '/app.php');
        $this->fileSystem->put(base_path('bootstrap') . '/app.php', $appContents);

        $this->info('Local and production environments setup');
        return true;
    }

    private function setupSelfUpdate()
    {

        $selfUpdatingRoutes = $this->fileSystem->get($this->stubsFolder . '/routes.stub');
        $this->fileSystem->delete(base_path('routes') . '/web.php');
        $this->fileSystem->put(base_path('routes') . '/web.php', $selfUpdatingRoutes);

        $verifyCsrfTokenContents = $this->fileSystem->get($this->stubsFolder . '/verifycsrftoken.php.stub');
        $this->fileSystem->delete(base_path('app/Http/Middleware') . '/VerifyCsrfToken.php');
        $this->fileSystem->put(base_path('app/Http/Middleware') . '/VerifyCsrfToken.php', $verifyCsrfTokenContents);

        // Setup self updating artisan command which can be run by the server
        $selfUpdateCommandContents = $this->fileSystem->get($this->stubsFolder . '/selfupdatecommand.php.stub');
        if ($this->fileSystem->isDirectory(base_path('app/Console') . '/Commands')) {
            $this->fileSystem->deleteDirectory(base_path('app/Console') . '/Commands');
        }
        $this->fileSystem->makeDirectory(base_path('app/Console') . '/Commands');
        $this->fileSystem->put(base_path('app/Console/Commands') . '/SelfUpdate.php', $selfUpdateCommandContents);

        $this->info('Self updater setup');
        return true;
    }

    private function setupJsFile()
    {
        $jsFolder = base_path('resources/js');

        $this->fileSystem->deleteDirectory($jsFolder . '/components');

        $this->fileSystem->makeDirectory($jsFolder . '/areas');
        $this->fileSystem->makeDirectory($jsFolder . '/areas/dashboard');

        $dashboardVueContents = $this->fileSystem->get($this->stubsFolder . '/dashboardindex.vue.stub');
        $this->fileSystem->put($jsFolder . '/areas/dashboard' . '/Index.vue', $dashboardVueContents);

        $dashController = $this->fileSystem->get($this->stubsFolder . '/dashboardcontroller.php.stub');
        $this->fileSystem->put(base_path('app/Http/Controllers') . '/DashboardController.php', $dashController);
        $this->fileSystem->delete(base_path('app/Http/Controllers') . '/HomeController.php');


        $this->fileSystem->makeDirectory($jsFolder . '/auth');
        $loginVueContents = $this->fileSystem->get($this->stubsFolder . '/login.vue.stub');
        $this->fileSystem->put($jsFolder . '/auth' . '/LoginPage.vue', $loginVueContents);

        $this->fileSystem->makeDirectory($jsFolder . '/layouts');
        $mainNavContents = $this->fileSystem->get($this->stubsFolder . '/mainnav.vue.stub');
        $this->fileSystem->put($jsFolder . '/layouts' . '/MainNav.vue', $mainNavContents);


        $this->fileSystem->delete(base_path('resources/js') . '/app.js');
        $appJsContents = $this->fileSystem->get($this->stubsFolder . '/app.js.stub');
        $this->fileSystem->put($jsFolder . '/app.js', $appJsContents);


        $this->fileSystem->delete(base_path() . '/webpack.mix.js');
        $webpackContents = $this->fileSystem->get($this->stubsFolder . '/webpack.stub');
        $this->fileSystem->put(base_path() . '/webpack.mix.js', $webpackContents);

        $this->info('JS scaffolding completed');

        $newRoutes = "// Dashboard route\r\nRoute::get('/dashboard', 'DashboardController@index');\r\n";

        $this->fileSystem->append(base_path('routes') . '/web.php', $newRoutes);

        $this->info('Dashboard routes added');


        return true;
    }

    private function setupLoginController()
    {

        $loginControllerContents = $this->fileSystem->get($this->stubsFolder . '/logincontroller.php.stub');
        $this->fileSystem->delete(base_path('app/Http/Controllers/Auth') . '/LoginController.php');
        $this->fileSystem->put(base_path('app/Http/Controllers/Auth') . '/LoginController.php', $loginControllerContents);

        $this->info('Login controller modified for passwordless access');


        $this->fileSystem->makeDirectory(base_path('app') . '/Mail');
        $loginTokenEmailStub = $this->fileSystem->get($this->stubsFolder . '/userlogintokenmail.php.stub');
        $this->fileSystem->put(base_path('app/Mail') . '/UserLoginTokenEmail.php', $loginTokenEmailStub);

        $this->fileSystem->makeDirectory(base_path('resources/views') . '/mails');
        $this->fileSystem->makeDirectory(base_path('resources/views') . '/mails/auth');
        $this->fileSystem->makeDirectory(base_path('resources/views') . '/mails/auth/users');

        $mailBladeContents = $this->fileSystem->get($this->stubsFolder . '/usertokenmailblade.php.stub');
        $this->fileSystem->put(base_path('resources/views/mails/auth/users') . '/login-token.blade.php', $mailBladeContents);

        $this->info('Mail setup for login token to be sent');


        $this->fileSystem->makeDirectory(base_path('app') . '/Handlers');
        $this->fileSystem->makeDirectory(base_path('app') . '/Handlers/Login');

        $loginHandler = $this->fileSystem->get($this->stubsFolder . '/loginhandler.php.stub');
        $this->fileSystem->put(base_path('app/Handlers/Login') . '/LoginHandler.php', $loginHandler);

        $this->info('Login handler setup');

        $newRoutes = "// Accept login token\r\nRoute::get('/login/{token}', 'Auth\LoginController@acceptEmailToken');\r\n";

        $this->fileSystem->append(base_path('routes') . '/web.php', $newRoutes);

        $this->info('Login route added to routes');

        // Login token model / migration

        $loginTokenModel = $this->fileSystem->get($this->stubsFolder . '/logintokenmodel.php.stub');
        $this->fileSystem->put(base_path('app') . '/LoginToken.php', $loginTokenModel);

        $this->info('Login token model created');

        $this->fileSystem->delete(base_path('app') . '/User.php');
        $userModel = $this->fileSystem->get($this->stubsFolder . '/usermodel.php.stub');
        $this->fileSystem->put(base_path('app') . '/User.php', $userModel);

        $this->info('Login token model created');

        return true;
    }

    private function setupMigrations()
    {
        $this->fileSystem->deleteDirectory(base_path('database') . '/migrations');
        $this->fileSystem->makeDirectory(base_path('database') . '/migrations');

        $this->fileSystem->copy($this->packageFolder . '/migrations/2014_10_12_000000_create_users_table.php', base_path('database/migrations') . '/2014_10_12_000000_create_users_table.php');
        $this->fileSystem->copy($this->packageFolder . '/migrations/2019_08_19_000000_create_failed_jobs_table.php', base_path('database/migrations') . '/2019_08_19_000000_create_failed_jobs_table.php');
        $this->fileSystem->copy($this->packageFolder . '/migrations/2019_11_22_191036_create_login_tokens_table.php', base_path('database/migrations') . '/2019_11_22_191036_create_login_tokens_table.php');

        $this->info('Migrations updated');
    }

    private function setupViews()
    {
        $this->fileSystem->delete(resource_path('views/layouts') . '/app.blade.php');
        $appBladeStub = $this->fileSystem->get($this->stubsFolder . '/app.blade.php.stub');
        $this->fileSystem->put(resource_path('views/layouts') . '/app.blade.php', $appBladeStub);
        $this->info('App view setup');

        $this->fileSystem->delete(resource_path('views/auth') . '/login.blade.php');
        $loginBladeStub = $this->fileSystem->get($this->stubsFolder . '/login.blade.php.stub');
        $this->fileSystem->put(resource_path('views/auth') . '/login.blade.php', $loginBladeStub);
        $this->info('Login view setup');


        $this->fileSystem->makeDirectory(resource_path('views') . '/pages');
        $this->fileSystem->copy(resource_path('views') . '/welcome.blade.php', resource_path('views/pages') . '/home.blade.php');
        $this->fileSystem->delete(resource_path('views') . '/welcome.blade.php');
        $this->fileSystem->delete(resource_path('views') . '/home.blade.php');

        $this->fileSystem->deleteDirectory(resource_path('views/auth') . '/passwords');
        $this->info('Home page setup');
    }
}
