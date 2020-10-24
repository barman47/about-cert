<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\Country;

class CountryPopulateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'country:populate';

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
        $path = resource_path() . "/json/countries.json";
        $array = json_decode(File::get($path), true);

        echo "Emptying the countries table\n";
        Country::all()->each(function($country){$country->delete();});
        echo "Countries table emptied\n";

        /**
         * Populating the Countries table
        */

        echo "Populating the Countries table\n";
        foreach($array as $a){
            Country::create($a);
        }
        echo "Table populated\n\n";
    }//end method handle
}
