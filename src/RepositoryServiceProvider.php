<?php

namespace MrAtiebatie;

use Illuminate\Support\ServiceProvider;
use MrAtiebatie\Commands\MakeRepositoryCommand;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class
            ]);
        }
    }
}
