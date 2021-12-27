<?php

namespace Rezky\ApiFormatter\Command;

use Illuminate\Console\Command;
use Rezky\ApiFormatter\Http\Response;

class CreateApiCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'convert configs code into consts at '.Response::class;

    /**
     * @var array
     */
    protected array $codes;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (config('code') == null){
            throw new \Error("cannot load 'code' config");
        }
        $this->codes = config('code.code');

    }

    private function createConst($codeName,$codeValue){
        //check is has prefix CODE
        $codeName = strtoupper($codeName);
        if (!str_starts_with($codeName,'CODE_')){
            $codeName = 'CODE_'.$codeName;
        }

        return "\tconst {$codeName} = '{$codeValue}';\n";

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $prefix = "CODE";
        $constStartRemark = "/** CODE LIST HERE */";
        $constEndRemark = "/** END CODE LIST HERE */";

        $responseFilePath = __DIR__ . "/../Http/Code.php";

        $responseFile = file($responseFilePath);

        // get code put location
        $lineConstStart = -1;
        $lineConstEnd = -1;
        foreach ($responseFile as $line=>$lineContent){
            if (strpos($lineContent,$constStartRemark) > -1){
                $lineConstStart = $line;
            }else if (strpos($lineContent,$constEndRemark) > -1){
                $lineConstEnd = $line;
            }

            if ($lineConstStart != -1 && $lineConstEnd != -1){
                break;
            }
        }

        $responseFileHead = array_slice($responseFile,0,$lineConstStart+1);
        $responseFileFooter = array_slice($responseFile,$lineConstEnd);

        $responseFileCodeConsts = [];
        $responseFileCodeConsts[] = "\n";

        foreach ($this->codes as $codeName => $codeValue){
            $responseFileCodeConsts[] = $this->createConst($codeName,$codeValue);
        }

        $responseFile = array_merge(array_values($responseFileHead),array_values($responseFileCodeConsts),array_values($responseFileFooter));

        $fp = fopen($responseFilePath,'w');
        fwrite($fp,implode("",$responseFile));
        fclose($fp);
        return true;
    }
}
