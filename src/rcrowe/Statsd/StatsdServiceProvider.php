<?php

/**
 * Talk to Statsd from Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace rcrowe\Statsd;

use Illuminate\Support\ServiceProvider;

class StatsdServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['config']->package('rcrowe/laravel-statsd', __DIR__.'/../../config');

        $this->registerStatsdClient();
        $this->registerSendEvent();
    }

    /**
     * Creates the Statsd client.
     *
     * @return void
     */
    public function registerStatsdClient()
    {
        $this->app['statsd'] = new Statsd(
            $this->app['config']->get('laravel-statsd::host', 'localhost'),
            $this->app['config']->get('laravel-statsd::port', 8126),
            $this->app['config']->get('laravel-statsd::protocol', 'udp')
        );

        // Disable logging if we aren't on the right environment
        $environments        = $this->app['config']->get('laravel-statsd::environments', array());
        $current_environment = $this->app['env'];

        if (is_array($environments) AND !in_array($current_environment, $environments)) {
            $this->app['statsd']->disable();
        }
    }

    /**
     * Send any stored data to Statsd.
     *
     * @return void
     */
    public function registerSendEvent()
    {
        $statsd = $this->app['statsd'];

        $this->app->after(function() use($statsd) {
            $statsd->send();
        });
    }
}
