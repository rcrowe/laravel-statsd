<?php

/**
 * Talk to Statsd from Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace rcrowe\Statsd\Facades;

use Illuminate\Support\Facades\Facade;

class Statsd extends Facade
{
    /**
     * Get the registered component.
     *
     * @return object
     */
    protected static function getFacadeAccessor(){ return 'statsd'; }

}