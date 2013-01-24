<?php

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
        $this->app['config']->package('rcrowe/statsd', __DIR__.'/../../config');

        $this->registerStatsdClient();
        $this->registerSendEvent();
    }

    public function registerStatsdClient()
    {
        $this->app['statsd'] = new Statsd(
            $this->app['config']->get('statsd::host', 'localhost'),
            $this->app['config']->get('statsd::port', 8126),
            $this->app['config']->get('statsd::protocol', 'udp')
        );
    }

    public function registerSendEvent()
    {
        $statsd = $this->app['statsd'];

        $this->app->after(function() use($statsd) {
            $statsd->send();
        });
    }
}
