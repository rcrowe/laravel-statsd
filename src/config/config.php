<?php

return array(

    /**
     * Statsd host.
     */
    'host' => 'localhost',

    /**
     * Statsd port.
     */
    'port' => 8126,

    /**
     * Statsd protocol.
     */
    'protocol' => 'udp',

    /**
     * Environments in which we allow sending to Statsd.
     */
    'environments' => ['prod', 'production'],
);
