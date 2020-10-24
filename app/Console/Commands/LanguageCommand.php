<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use File;
use App\Language;

class LanguageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language:populate';

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
        echo "Processing the languages table \n";
        Language::destroy(Language::pluck("id"));
        $array = json_decode(File::get(base_path() . "/resources/json/languages.json"));

        foreach($array as $value){
            Language::create( ["name" => $value] );
        }//end foreach
        echo "Processing complete\n";
    }
}
