<?php

namespace Bayer\DataDogClient\Tests;

use Bayer\DataDogClient\Series\Metric;
use Bayer\DataDogClient\Series\Metric\InvalidTypeException;
use Bayer\DataDogClient\Series\Metric\Point;

class MetricTest extends \PHPUnit_Framework_TestCase {

    public function testMetricName() {
        $metric = new Metric('test.metric.name', new Point(20));
        $this->assertEquals('test.metric.name', $metric->getName());
    }

    public function testMetricType() {
        $metric = new Metric('test.metric.name', new Point(20));
        $this->assertEquals(Metric::TYPE_GAUGE, $metric->getType());
        $metric->setType(Metric::TYPE_COUTNER);
        $this->assertEquals(Metric::TYPE_COUNTER, $metric->getType());
    }

    /**
     * @expectedException InvalidTypeException
     */
    public function testInvalidMetricTypeThrowsException() {
        $metric = new Metric('test.metric.name', new Point(20));
        $metric->setType('foo');
    }

    public function testMetricHost() {
        $metric = new Metric('test.metric.name', new Point(20));
        $this->assertNull($metric->getHost());
        $metric->setHost('foo.bar.com');
        $this->assertEquals('foo.bar.com', $metric->getHost());
    }

    public function testMetricTags() {
        $metric = new Metric('test.metric.name', new Point(20));
        $this->assertEmpty($metric->getTags());
        $this->assertEquals(array(), $metric->getTags());

        $metric->addTag('foo', 'bar');
        $this->assertCount(1, $metric->getTags());

        $metric->removeTag('foo');
        $this->assertCount(0, $metric->getTags());
    }

    public function testAddSinglePoint() {
        $point = new Point(20);

        // Set point in constructor
        $metric1 = new Metric('test.metric.name', $point);
        $this->assertEquals($point, $metric1->getPoints()[0]);

        // Set point in constructor as array
        $metric2 = new Metric('test.metric.name', array($point));
        $this->assertEquals($point, $metric2->getPoints()[0]);

        // Add point by method
        $metric3 = new Metric('test.metric.name', new Point(40));
        $metric3->addPoint($point);
        $this->assertCount(1, $metric3->getPoints());
        $this->assertEquals($point, $metric3->getPoints()[1]);
        $this->assertCount(2, $metric3->getPoints());
        $metric3->addPoint(new Point(30));
        $this->assertCount(3, $metric3->getPoints());

        // Set point by method
        $metric4 = new Metric('test.metric.name', new Point(10));
        $this->assertCount(1, $metric4->getPoints());
        $metric4->setPoints(array($point));
        $this->assertCount(1, $metric4->getPoints());
        $this->assertEquals($point, $metric4->getPoints()[0]);
    }

    public function testAddMultiplePoints() {
        // Some testing points
        $points = array(
            new Point(20),
            new Point(30),
            new Point(40)
        );

        // Set multiple point in constructor
        $metric1 = new Metric('test.metric.name', $points);
        $this->assertCount(3, $metric1->getPoints());
        $this->assertEquals(20, $metric1->getPoints()[0]->getValue());

        // Add multiple points by method
        $metric2 = new Metric('test.metric.name', new Point(40));
        $this->assertCount(1, $metric2->getPoints());
        foreach ($points as $point) {
            $metric2->addPoint($point);
        }
        $this->assertCount(4, $metric2->getPoints());
        $this->assertEquals(20, $metric2->getPoints()[1]->getValue());

        // Set multiple points by method
        $metric3 = new Metric('test.metric.name', new Point(30));
        $this->assertCount(1, $metric3->getPoints());
        $metric3->setPoints($points);
        $this->assertCount(3, $metric3->getPoints());
        $this->assertEquals($points[0], $metric3->getPoints()[0]);
    }
}