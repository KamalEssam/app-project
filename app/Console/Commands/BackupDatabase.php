<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    protected $process;

    public function __construct()
    {
        parent::__construct();

        $this->process = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('backup.mysql.username'),
            config('backup.mysql.password'),
            config('backup.mysql.database'),
            storage_path("backups/rklinic_" . Carbon::now()->format('Y-m-d_h:i:s_A') . "_.sql")
        ));
    }


    public function handle()
    {
        try {
            $this->process->mustRun();

            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            \Log::info($exception->getMessage());
            $this->error('The backup process has been failed.');
        }
    }
}
