<?php

namespace Rezky\LaravelResponseFormatter;

use Illuminate\Support\ServiceProvider;
use Rezky\LaravelResponseFormatter\Command\CreateApiCode;
use Rezky\LaravelResponseFormatter\Exception\Handler;
use Rezky\LaravelResponseFormatter\Http\Response;

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

        if ($this->app['config']->get('code.handler.override') !== null && $this->app['config']->get('code.handler.override')){
            $this->app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class,Handler::class);
        }

        $codes = array_merge(Response::getDefaultCode(),$this->app['config']->get('code.code'));
        $codes = array_change_key_case($codes,CASE_UPPER);
        $this->app['config']->set('code.code',$codes);

        foreach (Response::getDefaultGroup() as $httpCode => $values){
            $this->app['config']->set("code.group.{$httpCode}",array_merge($values,$this->app['config']->get("code.group.{$httpCode}")));
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
