<?php

namespace App\Providers;

use Rollbar\Rollbar;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Don't register rollbar if it is not configured.
        if (empty($this->app['config']->get('services.rollbar.access_token'))) {
            return;
        }
        Rollbar::init(
            array(
                'access_token' => $this->app['config']->get('services.rollbar.access_token'),
                'environment'  => $this->app->environment(),
            )
        );

        $this->app['log']->listen(function () {
            $args = func_get_args();

            $level = $args[0]->level;
            $message = $args[0]->message;
            $context = $args[0]->context;

            Rollbar::log($level, $message, $context);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
