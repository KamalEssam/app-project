<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ana bnsa kol 7aga 3shan kda karrart arya7 dmaghy';

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
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->call('passport:install');
        $this->call('key:generate');
        $this->call('config:cache');

        $this->info('|----------------------------------------------------------|');
        $this->info('|---------------| magic done, thanks Hisoka |--------------|');
        $this->info('|----------------------------------------------------------|');
    }
}
