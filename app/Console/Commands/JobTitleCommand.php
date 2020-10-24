<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\JobTitle;

class JobTitleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job-title:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is to populate the job_titles table with data from the json';

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
        echo "Truncating the job title table\r\n";
        JobTitle::all()->each(function($d){$d->delete();});
        echo "The job title table truncated\r\n";

        $path = base_path() . "/resources/json/job_titles.json";
        $array = \json_decode(File::get($path));
        $storeArray = array();
        natcasesort($array);

        echo "Inserting values into the database\r\n";
        foreach($array as $a){
            JobTitle::create(["name" => $a]);
        }

        echo "Database population complete.\r\n\n";
    }//end method handle
}
