<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

use App\Priviledge;
use App\PriviledgeType;

class PriviledgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'priviledge:populate';

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
        echo "Fetching priviledge data from json file\n";
        
        $path = base_path() . "/resources/json/priviledges.json";

        $array = json_decode(File::get($path), true);

        echo "Inserting fetched data into the database\n";
        foreach($array as $a){
            $name = $a["name"];
            $code = $a["code"];
            $type = $a["type"];

            if(!array_key_exists($type, PriviledgeType::TYPES)){
                echo "Type '{$type}' does not exist\n";
                continue;
            }
            
            $priviledge = Priviledge::where("code", $code)->first();

            if($priviledge == null)
                Priviledge::create([
                    "name" => $name,
                    "code" => $code,
                    "type" => $type
                ]);
            else{
                $priviledge->name = $name;
                $priviledge->code = $code;
                $priviledge->type = $type;

                $priviledge->save();
            }
        }//end foreach

        echo "Record inserted\n\n";
    }//end method handle
}
