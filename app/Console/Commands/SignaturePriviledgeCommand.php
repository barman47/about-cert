<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\SignaturePriviledge;
use File;

class SignaturePriviledgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signature-priviledge:populate';

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
        $path = resource_path("/json/signature_priviledges.json");
        $array = json_decode(File::get($path));

        echo "Pupulating the signature priviledge table\n";

        foreach($array as $a){
            $signaturePriviledge = SignaturePriviledge::where("code" , $a->code)->first();
            if($signaturePriviledge == null){
                $signaturePriviledge = SignaturePriviledge::create([
                    "name" => $a->name,
                    "code" => $a->code,
                    "price" => $a->price,
                    "duration_unit" => $a->duration_unit,
                    "duration" => $a->duration,
                    "max_within_duration" => $a->max_within_duration
                ]);
            }else{
                $signaturePriviledge->fill([
                    "name" => $a->name,
                    "code" => $a->code,
                    "price" => $a->price,
                    "duration_unit" => $a->duration_unit,
                    "duration" => $a->duration,
                    "max_within_duration" => $a->max_within_duration
                ]);

                $signaturePriviledge->save();
            }
        }


        echo "Population completed\n\n\n";
    }//end method handle
}
