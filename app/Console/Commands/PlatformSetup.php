<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Artisan;

class PlatformSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->call("key:generate");
        $this->call("storage:link");
        $this->call("config:cache");
        $this->call("migrate:refresh");
        try{
            $this->call("queue:table");
        }catch(\InvalidArgumentException $e){

        }
        $this->call("migrate");

        $this->call("interest:populate");
        $this->call("job-title:populate");
        $this->call("country:populate");
        $this->call("cv:reset");
        $this->call("priviledge:reset");
        $this->call("signature-priviledge:populate");
        $this->call("language:populate");
        $this->call('vendor:publish', [
            "--provider" => "Laravel\Scout\ScoutServiceProvider"
        ]);
        $this->call("passport:install");
        $this->call("passport:keys", ["--force" => true]);
        $this->call("passport:client", [
            "--password" => true
        ]);
    }//end method handle
}
