<?php
namespace App\PDFGenerators;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Exceptions\PDFGeneratorException;

class PDFGenerator{
    private $url;
    private $path;


    public function run(){
        $generatePDFScript = base_path("generatePDF.js");
        
        $c = system("node $generatePDFScript --path \"{$this->path}\" --url \"{$this->url}\"");
        
        if(!$c)
            return;
        throw new PDFGeneratorException($c);
        
        
        
        // $process = new Process([
        //         "node", 
        //         $generatePDFScript, 
        //         "--url", 
        //         $this->url,
        //         "--path",
        //         $this->path
        //         // "This-pdf.pdf"
        //     ]);
        // // shell_exec("cd \'" . base_path() . "\' && node generatePDF.js --url {$this->url} --path sadfsdf.pdf");
        // try {
        //     $process->mustRun();

        //     echo $process->getOutput();
        // } catch (ProcessFailedException $exception) {
        //     echo $exception->getMessage();
        // }
    }//end method run

    public function url(string $url): PDFGenerator{
        $this->url = $url;
        return $this;
    }//end method url

    public function path(string $path): PDFGenerator{
        $this->path = $path;
        return $this;
    }//end method path
}//end class PDFGenerator