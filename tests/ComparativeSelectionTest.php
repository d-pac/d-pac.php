<?php
namespace Dpac\Dpac;

/**
 * Comparative Selection Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class ComparativeSelectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group success
     * @group selection
     */
    public function testCanSelect()
    {
        $items[0] = new Item('3', 12, ['2', '4']);
        $items[1] = new Item('2', 9, ['3']);
        $items[2] = new Item('1', 10, []);
        $items[3] = new Item('4', 6, ['3', '5', '6']);

        $result = ComparativeSelection::select($items);

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertNotEmpty($result->getA());
        $this->assertNotEmpty($result->getB());
    }

    /**
     * @group failure
     * @group selection
     */
    public function testThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $items = '';

        ComparativeSelection::select($items);
    }
}
