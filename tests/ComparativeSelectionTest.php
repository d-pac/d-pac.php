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
        $this->items = json_decode(file_get_contents(__DIR__ . '/fixtures/items.json'), true);

        // Collection of Item objects with empty compared array
        $this->noneCompared = json_decode(file_get_contents(__DIR__ . '/fixtures/noneCompared.json'), true);

        // Collection of Item objects with one object being least compared
        $this->leastCompared = json_decode(file_get_contents(__DIR__ . '/fixtures/leastCompared.json'), true);
    }

    /**
     * @group success
     * @group selection
     */
    public function testCanSelect()
    {
        if (empty($this->items)) {
            $this->markTestSkipped('The items array was not set properly!');
        }

        $result = ComparativeSelection::select($this->items);

        $this->assertNotEmpty($result['a']);
        $this->assertNotEmpty($result['b']);

        unset($this->items, $result);
    }

    /**
     * @group failure
     * @group selection
     */
    public function testThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $items = [];

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

            $aIndex = Util::findIndex($selected['a'], $this->noneCompared);
            $bIndex = Util::findIndex($selected['b'], $this->noneCompared);

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
     */
    public function testGivePreferenceToLeastComparedWith()
    {
        if (!is_array($this->leastCompared) || empty($this->leastCompared)) {
            $this->markTestSkipped('The leastCompared array was not set properly!');
        }

        for ($i = 0; $i < $this->times; $i++) {
            $selected = ComparativeSelection::select($this->leastCompared);

            $this->assertEquals($selected['a'], "R10");
            $this->assertEquals($selected['b'], "R11");
        }
    }
}
