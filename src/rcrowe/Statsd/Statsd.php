<?php

namespace rcrowe\Statsd;

use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Liuggio\StatsdClient\StatsdClient;
use Liuggio\StatsdClient\Sender\SocketSender;
use Liuggio\StatsdClient\Factory\StatsdDataFactory;

class Statsd implements StatsdDataFactoryInterface
{
    /**
     * @var Liuggio\StatsdClient\StatsdClient
     */
    protected $client;

    /**
     * @var Liuggio\StatsdClient\Factory\StatsdDataFactory
     */
    protected $factory;

    /**
     * @var array Holds collected data for sending
     */
    protected $data = array();

    public function __construct($host = 'localhost', $port = 8126, $protocol = 'udp')
    {
        $sender        = new SocketSender();
        $this->client  = new StatsdClient($sender, $host, $port, $protocol);
        $this->factory = new StatsdDataFactory('\\Liuggio\\StatsdClient\\Entity\\StatsdData');
    }

    public function setClient(StatsdClient $client)
    {
        $this->client = $client;
    }

    public function setFactory(StatsdDataFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @inherit
     **/
    function timing($key, $time)
    {
        $this->data[] = $this->factory->timing($key, $time);
    }

    /**
     * @inherit
     **/
    function gauge($key, $value)
    {
        $this->data[] = $this->factory->gauge($key, $value);
    }

    /**
     * @inherit
     **/
    function set($key, $value)
    {
        $this->data[] = $this->factory->set($key, $value);
    }

    /**
     * @inherit
     **/
    function increment($key)
    {
        $this->data[] = $this->factory->increment($key);
    }

    /**
     * @inherit
     **/
    function decrement($key)
    {
        $this->data[] = $this->factory->decrement($key);
    }

    /**
     * @inherit
     **/
    function produceStatsdData($key, $value = 1, $metric = StatsdDataInterface::STATSD_METRIC_COUNT)
    {
        return $this->factory->produceStatsdData($key, $value, $metric);
    }

    function send()
    {
        // Only call send if we have data
        if (count($this->data) > 0) {
            $this->client->send($this->data);
        }
    }
}