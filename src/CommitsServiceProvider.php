<?php

namespace Tonning\Commits;

use Illuminate\Support\ServiceProvider;
use Tonning\Commits\Commands\PersistCommitMessages;
use Tonning\Commits\Commands\SendCommitMessagesNotification;

class CommitsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/commits.php' => config_path('commits.php'),
            ], 'commits');

            $this->commands([
                PersistCommitMessages::class,
                SendCommitMessagesNotification::class,
            ]);

        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/commits.php', 'commits'
        );
    }
}
