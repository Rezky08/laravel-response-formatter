<?php

namespace Rezky\LaravelResponseFormatter\Command;


class CreateApiCode extends GenerateConstant
{
    protected string $prefix = "CODE";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Perbarui signature untuk menjadi flag boolean sederhana
    protected $signature = 'code:create {--use-database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new string enum from the "code" config file or database.';

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
     * @return int
     */
    public function handle()
    {

        $name = 'ResponseCode';
        $this->filePath = app_path("Enums/Http/ResponseCode.php");

        $this->generationMode = 'enum';

        $this->targetNamespace = 'App\Enums\Http';

        $this->targetEnumName = $name;

        if (config('code') == null) {
            $this->error("cannot load 'code' config file");
            if (!$this->option('use-database')) {
                 return false;
            }
        } else {
            $this->configData = config('code.code');
        }
        return parent::handle();
    }

}
