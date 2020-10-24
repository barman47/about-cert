<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Priviledge;

class PriviledgeResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'priviledge:reset';

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
        echo "Deleting all records in the priviledges table\n";
        Priviledge::all()->each(function($priviledge) {
            $priviledge->delete();
        });
        echo "Records Deleted\n";

        $this->call("priviledge:populate");
    }//end method handle
}// end class PriviledgeResetCommand
