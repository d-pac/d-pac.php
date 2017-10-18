<?php
namespace Dpac\Dpac;

/**
 * Item Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * We can create a new Item
     *
     * @group success
     */
    public function testCanBeCreated()
    {
        $item = new Item('2', 3, ['3', '5', '12']);

        $this->assertInstanceOf(Item::class, $item);

        unset($item);
    }
}
