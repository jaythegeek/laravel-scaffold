<?php

namespace JayTheGeek\LaravelScaffold\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class ScaffoldPasswordless extends Command
{
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
    private $files;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->loadMigrations();
    }

    private function loadMigrations()
    {
        Artisan::call("ui vue --auth");
        $this->info('Migrations sorted out!');

        return true;
    }
}
