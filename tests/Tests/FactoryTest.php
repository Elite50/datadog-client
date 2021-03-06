<?php
namespace Elite50\DataDogClient\Tests;

use Elite50\DataDogClient\Factory;
use Elite50\DataDogClient\Event;
use Elite50\DataDogClient\Series\Metric;

/**
 * Class FactoryTest
 * @package Elite50\DataDogClient\Tests
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryCanCreateMetric()
    {
        $metric = Factory::buildMetric(
            'test.metric.name',
            [
                [20],
                [time() - 20, 20],
            ],
            [
                'type' => Metric::TYPE_COUNTER,
                'host' => 'foo.bar.com',
                'tags' => ['foo' => 'bar']
            ]
        );

        $this->assertEquals(Metric::TYPE_COUNTER, $metric->getType());
        $this->assertEquals('foo.bar.com', $metric->getHost());
        $this->assertEquals(['foo' => 'bar'], $metric->getTags());

        $this->assertInstanceOf('Elite50\DataDogClient\Series\Metric', $metric);
    }

    public function testFactoryCanCreateEvent()
    {
        $event = Factory::buildEvent(
            'This is a dummy event',
            'My Event',
            [
                'date_happened'    => 123456,
                'priority'         => Event::PRIORITY_LOW,
                'alert_type'       => Event::TYPE_SUCCESS,
                'source_type_name' => Event::SOURCE_BITBUCKET,
                'aggregationKey'   => 'foo.bar',
                'tags'             => ['foo' => 'bar']
            ]
        );

        $this->assertEquals(123456, $event->getDateHappened());
        $this->assertEquals(Event::PRIORITY_LOW, $event->getPriority());
        $this->assertEquals(Event::TYPE_SUCCESS, $event->getAlertType());
        $this->assertEquals(Event::SOURCE_BITBUCKET, $event->getSourceTypeName());
        $this->assertEquals('foo.bar', $event->getAggregationKey());
        $this->assertEquals(['foo' => 'bar'], $event->getTags());

        $this->assertInstanceOf('Elite50\DataDogClient\Event', $event);
    }

    /**
     * @expectedException \Elite50\DataDogClient\Factory\InvalidPropertyException
     */
    public function testInvalidOptionThrowsException()
    {
        Factory::buildEvent(
            'Dummy event',
            'My Event',
            [
                'foo' => 'bar'
            ]
        );
    }
}
