<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use Schema;
use App\Interest;
use Illuminate\Support\Str;

class InterestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:populate';

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
        echo "Processing the interests table \n";
        Interest::destroy(Interest::pluck("id"));
        $array = json_decode(File::get(base_path() . "/resources/json/interests.json"));

        asort($array);

        foreach($array as $value){
            Interest::create( ["name" => $value] );
        }//end foreach
        echo "Processing complete\n";
    }//end method handle
}
