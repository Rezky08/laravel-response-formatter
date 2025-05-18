<?php

namespace Rezky\LaravelResponseFormatter\Command;

use Illuminate\Console\Command;
use Rezky\LaravelResponseFormatter\Models\ResponseRemark;

class GenerateConstant extends Command
{
    /**
     * @var string
     */
    protected string $prefix = "CONST";

    protected string $filePath = "";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'const:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate const list';

    /**
     * @var array
     */
    protected array $consts;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function createConst(&$codeName,$codeValue){
        //check is has prefix CODE
        $codeName = strtoupper($codeName);
        if (!str_starts_with($codeName,$this->prefix.'_')){
            $codeName = $this->prefix.'_'.$codeName;
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
        dd($this->hasOption('use-database'));
        if (!($this->hasOption('use-database') && filter_var($this->option('use-database'),FILTER_VALIDATE_BOOL) === false)) {
            $this->loadConfigFromDB();
        }
        $constStartRemark = "/** {$this->prefix} LIST HERE */";
        $constEndRemark = "/** END {$this->prefix} LIST HERE */";
        $this->info("find start line ".$constStartRemark);
        $this->info("find end line ".$constEndRemark);

        $scriptFilePath = $this->filePath;

        $scriptFile = file($scriptFilePath);

        // get code put location
        $lineConstStart = -1;
        $lineConstEnd = -1;
        foreach ($scriptFile as $line=>$lineContent){
            if (strpos($lineContent,$constStartRemark) > -1){
                $lineConstStart = $line;
            }else if (strpos($lineContent,$constEndRemark) > -1){
                $lineConstEnd = $line;
            }

            if ($lineConstStart != -1 && $lineConstEnd != -1){
                break;
            }
        }

        $scriptFileHead = array_slice($scriptFile,0,$lineConstStart+1);
        $scriptFileFooter = array_slice($scriptFile,$lineConstEnd);

        $scriptFileCodeConsts = [];
        $scriptFileCodeConsts[] = "\n";

        $outputTable = [];
        foreach ($this->consts as $constName => $constValue){
            $scriptFileCodeConsts[] = $this->createConst($constName,$constValue);
            $outputTable[]=[$constName,$constValue];
        }

        $this->table(["Constant Name","Value"],$outputTable);

        $scriptFile = array_merge(array_values($scriptFileHead),array_values($scriptFileCodeConsts),array_values($scriptFileFooter));

        $fp = fopen($scriptFilePath,'w');
        fwrite($fp,implode("",$scriptFile));
        fclose($fp);
        return true;
    }

    protected function loadConfigFromDB()
    {
        $responseRemarks = ResponseRemark::all();
        dd($responseRemarks);
    }
}
