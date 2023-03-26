<?php

namespace Rezky\LaravelResponseFormatter\Command;

use Illuminate\Console\Command;
use Rezky\LaravelResponseFormatter\Console\Commands\GenerateConstant;
use Rezky\LaravelResponseFormatter\Http\Response;

class CreateApiCode extends GenerateConstant
{
    protected string $prefix = "CODE";
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
        $this->filePath = __DIR__ . "/../Http/Code.php";

    }

}
