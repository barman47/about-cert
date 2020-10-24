<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CVTemplate;
use App\CVTemplateGroup;

class CVResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Note: Do not use this command if other records are depending on the ids in the cv templates. \r\nThis command will delete and poulate the data in the c_v_templates table hereby changing the ids";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }//end constructor

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Deleting all records in the c_v_templates table\n";
        CVTemplate::all()->each(function($template) {
            $template->delete();
        });
        echo "Records Deleted\n";

        echo "Deleting all records in the c_v_template_groups table\n";
        CVTemplateGroup::all()->each(function($template) {
            $template->delete();
        });
        echo "Records Deleted\n";

        $this->call("cv-template-group:reset");
        $this->call("cv:populate");
    }//end method handle
}//end class CVResetCommand
