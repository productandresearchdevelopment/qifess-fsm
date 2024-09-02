<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseBackup extends Command
{
    protected $signature = 'database:backup';
    protected $description = 'Database Backup Scheduler';

    const PREFIX = 'db';
    const DIRECTORY = 'backup/db';
    const EXPIRED_DAY = 3;

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        try {
            $filename = self::PREFIX.'_'.date('ymdH').".gz";
            $pathfilename = self::DIRECTORY.'/'.$filename;
            $pathfiledb = self::DIRECTORY.'/'.self::PREFIX.'.gz';

            $filetmp = 'tmp/'.((string)Str::uuid()).'.gz';

            $process = sprintf(
                '/usr/bin/mysqldump --user="%s" --password="%s" --host="%s" "%s" | gzip > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                Storage::disk('local')->path($filetmp)
            );

            exec("$process");

            while(!Storage::disk('local')->exists($filetmp)){
                sleep(2);
            }

            $file = @file_get_contents(Storage::disk('local')->path($filetmp));

            if(Storage::disk(config('filesystems.upload_disk'))->exists($pathfiledb)){
                Storage::disk(config('filesystems.upload_disk'))->delete($pathfiledb);
            }

            Storage::disk(config('filesystems.upload_disk'))->put($pathfilename, $file, 'public');
            Storage::disk(config('filesystems.upload_disk'))->copy($pathfilename, $pathfiledb);

            Storage::disk('local')->delete($filetmp);

            $expired = strtotime (date('Y-m-d 23:00:00'));
            $expired = strtotime ( '-'.self::EXPIRED_DAY.' day', $expired);

            $this->info("EXPIRED >> ".date('Y-m-d', $expired));

            $expired = date('ymdH', $expired);

            $files = Storage::disk(config('filesystems.upload_disk'))->files("backup/db");
            foreach ($files AS $file){
                $datefile = str_replace(self::DIRECTORY, '', $file);
                $datefile = str_replace(self::PREFIX, '', $datefile);
                $datefile = str_replace('_', '', $datefile);
                $datefile = str_replace('/', '', $datefile);
                $datefile = str_replace('.gz', '', $datefile);

                if($datefile && $datefile < $expired){
                    Storage::disk(config('filesystems.upload_disk'))->delete($file);
                    $this->info("REMOVE FILE $file");
                }
            }

            $this->info("Backup DB successfully ($filename).");
        } catch (ProcessFailedException $e) {
            $this->error('The backup process has been failed, '.$e->getMessage());
        }
    }
}
