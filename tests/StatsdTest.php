<?php

namespace StatsdTests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Statsd\Statsd;

class StatsdTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testTiming()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('timing')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->timing('test', time());
        $statsd->timing('test', time());

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testGaugeMultipleCalls()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('gauge')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->gauge('test', 'value');
        $statsd->gauge('test', 'value');

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testSet()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('set')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->set('key', 'value');
        $statsd->set('key', 'value');

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testIncrement()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('increment')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->increment('key');
        $statsd->increment('key');

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testDecrement()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('decrement')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->decrement('key');
        $statsd->decrement('key');

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testUpdateCount()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('updateCount')->twice()->andReturn('one', 'two');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $this->assertTrue(count($statsd->getData()) === 0);

        $statsd->updateCount('key', 2);
        $statsd->updateCount('key', 4);

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 2);
        $this->assertEquals($data[0], 'one');
        $this->assertEquals($data[1], 'two');
    }

    public function testAllCalled()
    {
        $factory = m::mock('Liuggio\StatsdClient\Factory\StatsdDataFactory');
        $factory->shouldReceive('timing')->once()->andReturn('timing');
        $factory->shouldReceive('gauge')->once()->andReturn('gauge');
        $factory->shouldReceive('set')->once()->andReturn('set');
        $factory->shouldReceive('increment')->once()->andReturn('increment');
        $factory->shouldReceive('decrement')->once()->andReturn('decrement');
        $factory->shouldReceive('updateCount')->once()->andReturn('updateCount');

        $statsd = new Statsd;
        $statsd->setFactory($factory);

        $statsd->timing('key', time());
        $statsd->gauge('key', 'value');
        $statsd->set('key', 'value');
        $statsd->increment('key');
        $statsd->decrement('key');
        $statsd->updateCount('key', 2);

        $data = $statsd->getData();

        $this->assertTrue(count($data) === 6);
        $this->assertEquals($data[0], 'timing');
        $this->assertEquals($data[1], 'gauge');
        $this->assertEquals($data[2], 'set');
        $this->assertEquals($data[3], 'increment');
        $this->assertEquals($data[4], 'decrement');
        $this->assertEquals($data[5], 'updateCount');
    }

    public function testDataTypeStored()
    {
        $statsd = new Statsd;
        $statsd->timing('key.test', 123);

        $data = $statsd->getData();

        $this->assertEquals($data[0]->getKey(), 'key.test');
        $this->assertEquals($data[0]->getValue(), 123);
    }

    public function testNoDataNotSent()
    {
        $client = m::mock('Liuggio\StatsdClient\StatsdClient');
        $client->shouldReceive('send')->never();

        $statsd = new Statsd;
        $statsd->setClient($client);

        $statsd->send();
    }

    public function testDataSent()
    {
        $client = m::mock('Liuggio\StatsdClient\StatsdClient');
        $client->shouldReceive('send')->once();

        $statsd = new Statsd;
        $statsd->setClient($client);

        $statsd->timing('key', time());

        $statsd->send();
    }

    public function testDisable()
    {
        $client = m::mock('Liuggio\StatsdClient\StatsdClient');
        $client->shouldReceive('send')->never();

        $statsd = new Statsd;
        $statsd->setClient($client);

        $statsd->disable();
        $statsd->timing('key', time());

        $statsd->send();
    }

    public function testDisableCalledLate()
    {
        $client = m::mock('Liuggio\StatsdClient\StatsdClient');
        $client->shouldReceive('send')->never();

        $statsd = new Statsd;
        $statsd->setClient($client);

        $statsd->timing('key', time());
        $statsd->timing('key', time());
        $statsd->timing('key', time());
        $statsd->timing('key', time());
        $statsd->timing('key', time());

        $statsd->disable();
        $statsd->send();
    }

    public function testEnable()
    {
        $client = m::mock('Liuggio\StatsdClient\StatsdClient');
        $client->shouldReceive('send')->once();

        $statsd = new Statsd;
        $statsd->setClient($client);

        $statsd->disable();

        $statsd->timing('key', time());
        $statsd->timing('key', time());

        $statsd->enable();
        $statsd->send();
    }
}
