<?php
namespace Dpac\Dpac;

/**
 * Class Util
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Util
{
    /**
     * Compare given Item objects by the amount of comparisons they contain
     *
     * @param Item $a
     * @param Item $b
     * @return int
     */
    public static function compareByLength(Item $a, Item $b)
    {
        if (!is_a($a, Item::class) || !is_a($b, Item::class)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be instances of Item');
        }

        return count($a->getCompared()) - count($b->getCompared());
    }

    /**
     * Compare given Item objects by their ability
     *
     * @param Item $a
     * @param Item $b
     * @return mixed
     */
    public static function compareByAbility(Item $a, Item $b)
    {
        if (!is_a($a, Item::class) || !is_a($b, Item::class)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be instances of Item');
        }

        return $a->getAbility() - $b->getAbility();
    }
}
