<?php

namespace Rezky\LaravelResponseFormatter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Rezky\LaravelResponseFormatter\Command\CacheResponseCodeRemark;
use Rezky\LaravelResponseFormatter\Command\CreateApiCode;
use Rezky\LaravelResponseFormatter\Exception\Handler;
use Rezky\LaravelResponseFormatter\Http\Response;


class LaravelResponseFormatterServiceProvider extends ServiceProvider
{

    protected $commands = [
        CreateApiCode::class,
        CacheResponseCodeRemark::class
    ];


    public function register()
    {
        $this->commands($this->commands);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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


    public function boot()
    {
        if (!$this->app->runningInConsole() && Cache::has('response_formatter.codes')) {
            $this->app['config']->set('code.code', Cache::get('response_formatter.codes'));
        }
        if (!$this->app->runningInConsole() && Cache::has('response_formatter.groups')) {
            $this->app['config']->set('code.group', Cache::get('response_formatter.groups'));
        }
        $this->publishes([__DIR__."/../config/code.php" => config_path('code.php')],'config');
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')],'migrations');
        $this->publishes([__DIR__.'/../database/seeders' => database_path('seeders')],'seeders');
    }
}
