<?php

/**
 * Talk to Statsd from Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace rcrowe\Statsd;

use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Liuggio\StatsdClient\StatsdClient;
use Liuggio\StatsdClient\Sender\SocketSender;
use Liuggio\StatsdClient\Factory\StatsdDataFactory;

class Statsd implements StatsdDataFactoryInterface
{
    /**
     * @var \Liuggio\StatsdClient\StatsdClientInterface
     */
    protected $client;

    /**
     * @var \Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface
     */
    protected $factory;

    /**
     * @var array Holds collected data for sending
     */
    protected $data = array();

    /**
     * @var bool If false don't send the data to Statsd
     */
    protected $enabled = true;

    /**
     * Create a new Statsd instance.
     *
     * @param string $host
     * @param int    $port
     * @param string $protocol
     */
    public function __construct($host = 'localhost', $port = 8126, $protocol = 'udp')
    {
        $sender        = new SocketSender($host, $port, $protocol);
        $this->client  = new StatsdClient($sender);
        $this->factory = new StatsdDataFactory('\\Liuggio\\StatsdClient\\Entity\\StatsdData');
    }

    /**
     * Disable sending to Statsd.
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Enable of sending to Statsd.
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Set the client used to send data to Statsd with.
     *
     * @param Liuggio\StatsdClient\StatsdClientInterface $client
     * @return void
     */
    public function setClient(StatsdClient $client)
    {
        $this->client = $client;
    }

    /**
     * Set the factory used to collect data with.
     *
     * @param Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface
     * @return void
     */
    public function setFactory(StatsdDataFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the current data that will be sent to Statsd.
     *
     * @return array
     */
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
    function updateCount($key, $delta)
    {
        $this->data[] = $this->factory->updateCount($key, $delta);
    }

    /**
     * @inherit
     **/
    function produceStatsdData($key, $value = 1, $metric = StatsdDataInterface::STATSD_METRIC_COUNT)
    {
        return $this->factory->produceStatsdData($key, $value, $metric);
    }

    /**
     * Sends stored metrics to Statsd if there are any and sending is enabled.
     *
     * @see \rcrowe\Statsd\Statsd::disable()
     * @see \rcrowe\Statsd\Statsd::enable()
     *
     * @return void
     */
    function send()
    {
        // Only call send if enabled and we have data
        if ($this->enabled AND count($this->data) > 0) {
            $this->client->send($this->data);
            $this->data = [];
        }
    }
}
