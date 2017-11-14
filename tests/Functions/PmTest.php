<?php
namespace Dpac\Dpac;

use Dpac\Dpac\Exceptions\NotANumberError;
use Dpac\Dpac\Functions\Pm;

/**
 * Pm Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class PmTest extends \PHPUnit_Framework_TestCase
{
    /**
     * It should function correctly
     * @group pm
     */
    public function testReliability()
    {
        $this->assertEquals(0.75, Pm::reliability(4, 2));
    }

    /**
     * It should throw a division by zero exception if SD equals 0
     */
    public function testReliabilityThrowsDivisionByZeroError()
    {
        $this->expectException(\DivisionByZeroError::class);

        Pm::reliability(0, 1);
    }

    /**
     * It should return NaN if RMSE equals 0
     */
    public function testReliabilityThrowsNaNError()
    {
        $this->expectException(NotANumberError::class);

        Pm::reliability(1, 0);
    }
}
