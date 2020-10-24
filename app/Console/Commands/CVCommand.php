<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\CVTemplate;
use App\CVTemplateGroup;

class CVCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cv:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will populate the c_v_templates table";

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
        echo "Fetching cv template data from json file\n";

        $path = base_path() . "/resources/json/c_v_templates.json";

        $previewImgFolder = "/cv/preview_img/";

        $array = json_decode(File::get($path), true);

        echo "Inserting fetched data into the database\n";

        $groups = CVTemplateGroup::all();

        foreach($array as $a){
            $previewImg = $a["preview_img"] ? $previewImgFolder . $a["preview_img"] : "";
            $templateFile = $a["template_file"];
            $name  = $a["name"];
            $price = $a["price"] ?? null;

            $group = $groups->where("group_code", $a["group"])->first();

            if($group == null)
                continue;

            $template = CVTemplate::where("name", $name)->first();

            if($template == null)
                $group->templates()->create([
                    "name" => $name,
                    "preview_img" => $previewImg,
                    "template_file" => $templateFile,
                    "price" => $price,
                ]);
            else{
                $template->preview_img = $previewImg;
                $template->template_file = $templateFile;
                $template->c_v_template_group_id = $group->id;
                $template->price = $price;

                $template->save();
            }
        }//end foreach

        echo "Record inserted\n\n";
    }//end method handle
}//end class CVCommand
