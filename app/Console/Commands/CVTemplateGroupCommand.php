<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

use App\CVTemplateGroup;

class CVTemplateGroupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv-template-group:reset';

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
        $path = base_path() . "/resources/json/c_v_template_groups.json";
        $array = \json_decode(File::get($path), true);

        echo "Populating CV template group table\n";
        foreach($array as $a){
            CVTemplateGroup::create([
                "name" => $a["name"],
                "group_code" => $a["group_code"],
                "price" => $a["price"] ?? "0"
            ]);
        }

        echo "CV Template group table populated \n";
    }//end method handle
}
