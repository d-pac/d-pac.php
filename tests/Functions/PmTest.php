<?php
namespace Dpac\Dpac;

use Dpac\Dpac\Exceptions\NotANumberError;
use Dpac\Dpac\Functions\Pm;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Pm Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class PmTest extends \PHPUnit_Framework_TestCase
{
    protected $raschFixtures;
    protected $fisherFixtures;

    /**
     * Initialize all our test arrays
     */
    public function setUp()
    {
        parent::setUp();

        $this->raschFixtures = json_decode(file_get_contents(__DIR__ . '/fixtures/rasch.json'), true);
        $this->fisherFixtures = json_decode(file_get_contents(__DIR__ . '/fixtures/fisher.json'), true);
    }

    /**
     * It should function correctly
     * @group pm
     * @group reliability
     */
    public function testReliability()
    {
        $this->assertEquals(0.75, Pm::reliability(4, 2));
    }

    /**
     * It should throw a division by zero exception if SD equals 0
     * @group pm
     * @group reliability
     */
    public function testReliabilityThrowsDivisionByZeroError()
    {
        $this->expectException(\DivisionByZeroError::class);

        Pm::reliability(0, 1);
    }

    /**
     * It should return NaN if RMSE equals 0
     * @group pm
     * @group reliability
     */
    public function testReliabilityThrowsNaNError()
    {
        $this->expectException(NotANumberError::class);

        Pm::reliability(1, 0);
    }

    /**
     * It should function correctly
     * @group pm
     * @group rasch
     */
    public function testRasch()
    {
        foreach ($this->raschFixtures as $fixture) {
            $actual = Pm::rasch($fixture['a'], $fixture['b']);
            $expected = $fixture['expected'];

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * It should function correctly for unfixed requests
     * @group pm
     * @group fisher
     */
    public function testFisherWithUnfixedRequests()
    {
        foreach ($this->fisherFixtures['unfixed'] as $fixture) {
            $actual = Pm::fisher($fixture['a'], $fixture['b']);
            $expected = $fixture['expected'];

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * It should return a number for fixed requests
     * @group pm
     * @group fisher
     */
    public function testFisherReturnsNumberWithFixedRequests()
    {
        $fixture = $this->fisherFixtures['fixed'];
        $actual = Pm::fisher($fixture['a'], $fixture['b'], $fixture['digits']);

        $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $actual);
    }

    /**
     * It should function correctly for fixed requests
     * @group pm
     * @group fisher
     */
    public function testFisherWithFixedRequests()
    {
        $fixture = $this->fisherFixtures['fixed'];
        $actual = Pm::fisher($fixture['a'], $fixture['b'], $fixture['digits']);
        $expected = $fixture['expected'];

        $this->assertEquals($expected, $actual);
    }
}
