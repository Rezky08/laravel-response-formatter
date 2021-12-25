<?php

namespace Rezky\ApiFormatter;

use Illuminate\Support\ServiceProvider;
use Rezky\ApiFormatter\Command\CreateApiCode;

class ApiFormatterServiceProvider extends ServiceProvider
{

    protected $commands = [
        CreateApiCode::class
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        if ($this->app['config']->get('code') === null){
            $this->app['config']->set('code',require __DIR__."/../config/code.php");
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__."/../config/code.php" => config_path('code.php')],'config');
    }
}
