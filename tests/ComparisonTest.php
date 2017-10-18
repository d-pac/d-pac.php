<?php
namespace Dpac\Dpac;

/**
 * Comparison Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class ComparisonTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeCreated()
    {
        $comparison = new Comparison('2', '3');

        $this->assertInstanceOf(Comparison::class, $comparison);

        unset($comparison);
    }

    /**
     * We can't leave out one of the parameters
     *
     * @group failure
     */
    public function testThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $comparison = new Comparison('', '12');

        unset($comparison);
    }

    /**
     * We can set the ids
     *
     * @group success
     */
    public function testCanSetIds()
    {
        $comparison = new Comparison('2', '3');

        $this->assertEquals('2', $comparison->getA());
        $this->assertEquals('3', $comparison->getB());

        unset($comparison);
    }
}