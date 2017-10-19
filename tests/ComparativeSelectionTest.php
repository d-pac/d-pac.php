<?php
namespace Dpac\Dpac;

/**
 * Comparative Selection Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class ComparativeSelectionTest extends \PHPUnit_Framework_TestCase
{
    protected $times;
    protected $items = [];
    protected $noneCompared = [];
    protected $leastCompared = [];

    /**
     * Prepare our test variables
     */
    protected function setUp()
    {
        $this->times = 2000;

        // Collection of Item objects with acceptable values
        $itemsJson = json_decode(file_get_contents(__DIR__ . '/fixtures/items.json'), true);

        foreach ($itemsJson as $item) {
            $this->items[] = new Item((string) $item['id'], (float) $item['ability'], $item['compared']);
        }

        // Collection of Item objects with empty compared array
        $noneComparedJson = json_decode(file_get_contents(__DIR__ . '/fixtures/noneCompared.json'), true);

        foreach ($noneComparedJson as $item) {
            $this->noneCompared[] = new Item((string) $item['id'], (float) $item['ability'], $item['compared']);
        }

        // Collection of Item objects with one object being least compared
        $leastComparedJson = json_decode(file_get_contents(__DIR__ . '/fixtures/leastCompared.json'), true);

        foreach ($leastComparedJson as $item) {
            $this->leastCompared[] = new Item((string) $item['id'], (float) $item['ability'], $item['compared']);
        }
    }

    /**
     * @group success
     * @group selection
     * @group skip
     */
    public function testCanSelect()
    {
        if (empty($this->items)) {
            $this->markTestSkipped('The items array was not set properly!');
        }

        $result = ComparativeSelection::select($this->items);

        $this->assertInstanceOf(Comparison::class, $result);
        $this->assertNotEmpty($result->getA());
        $this->assertNotEmpty($result->getB());

        unset($this->items, $result);
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

        unset($items);
    }

    /**
     * Should always select an item from the other half of list, when non have been compared
     *
     * @group success
     * @group selection
     */
    public function testSelectFromOtherHalfNoneCompared()
    {
        if (!is_array($this->noneCompared) || empty($this->noneCompared)) {
            $this->markTestSkipped('The noneCompared array was not set properly!');
        }

        $N2 = count($this->noneCompared) / 2;
        usort($this->noneCompared, ['Dpac\Dpac\Util', 'compareByAbility']);

        for ($i = 0; $i < $this->times; $i++) {
            $selected = ComparativeSelection::select($this->noneCompared);
            $this->assertInstanceOf(Comparison::class, $selected);

            $aIndex = Util::findIndex($selected->getA(), $this->noneCompared);
            $bIndex = Util::findIndex($selected->getB(), $this->noneCompared);

            if ($aIndex > $N2) {
                $this->assertLessThanOrEqual($N2, $bIndex);
            } else {
                $this->assertGreaterThanOrEqual($N2, $bIndex);
            }
        }
    }

    /**
     * Should give preference to the items the first one is least compared with
     *
     * @group success
     * @group selection
     * @group individual
     */
    public function testGivePreferenceToLeastComparedWith()
    {
        if (!is_array($this->leastCompared) || empty($this->leastCompared)) {
            $this->markTestSkipped('The leastCompared array was not set properly!');
        }

        for ($i = 0; $i < $this->times; $i++) {
            $selected = ComparativeSelection::select($this->leastCompared);
            $this->assertInstanceOf(Comparison::class, $selected);

            $this->assertEquals($selected->getA(), "R10");
            $this->assertEquals($selected->getB(), "R11");
        }
    }
}
