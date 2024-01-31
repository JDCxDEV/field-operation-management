<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--prod}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear database and seed data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info(PHP_EOL);
        $this->warn($this->description);
        $this->info("<fg=yellow;>/*--------------------------------------------");
        $this->info("<fg=yellow;>| <fg=red;>WARNING! WARNING! WARNING! WARNING! WARNING!");
        $this->info("<fg=yellow;>|---------------------------------------------");
        $this->info("<fg=yellow;>|");
        $this->info("<fg=yellow;>| <fg=red;>{$this->description}");
        $this->info("<fg=yellow;>| <fg=yellow;>EXERCISE EXTREME CAUTION!");
        $this->info("<fg=yellow;>|");        
        $this->info("<fg=yellow;>|--------------------------------------------*/");

        if (!file_exists('public/storage')) {
            $this->call('storage:link');
        }

        if (!config('app.key')) {
            $this->call('key:generate');
        }

        $this->call('migrate:fresh');

        if ($this->option('prod')) {
            
            $this->info("<fg=yellow;>|--------------------------------------------*/");            
            $this->info("<fg=yellow;>|");                        
            $this->info("<fg=yellow;>| <fg=red;>Seeding production data");                        
            $this->info("<fg=yellow;>|");
            $this->info("<fg=yellow;>|--------------------------------------------*/");            

            $this->call('db:seed', [
                '--class' => 'ProductionSeeder'
            ]);
        } else {
            $this->call('db:seed', [
                '--class' => 'DatabaseSeeder',
            ]);
        }
        
        return Command::SUCCESS;
    }
}
