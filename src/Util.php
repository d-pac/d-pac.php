<?php
namespace Dpac\Dpac;

use Dpac\Dpac\Exception\ComparisonException;

/**
 * Class Util
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Util
{
    /**
     * Compare given Item objects by their ability
     *
     * @throws \InvalidArgumentException
     * @throws ComparisonException
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

        if (!is_float($a->getAbility()) || !is_float($b->getAbility())) {
            throw new ComparisonException('Expected both ability values to be of type float');
        }

        return (float) $a->getAbility() - (float) $b->getAbility();
    }

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
     * Get the index for the given id in a collection of Items
     *
     * @param string $id
     * @param Item[] $items
     * @return int|bool $index
     */
    public static function findIndex($id, $items)
    {
        foreach ($items as $index => $item) {
            if ($item->getId() === (string) $id) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Get the amount of comparisons for a given collection of ids
     *
     * @param array $compared
     * @param string $id
     * @return int $counter
     */
    public static function getNumberOfComparisons($compared, $id)
    {
        $counter = 0;

        // Validate our parameters
        if (!is_array($compared)) {
            throw new \InvalidArgumentException('Expected compared to be an array');
        }

        if (empty($id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }

        // Loop over each id and compare with the referenced id
        foreach ($compared as $comparedId) {
            if ((string) $id === (string) $comparedId) {
                $counter++;
            }
        }

        return $counter;
    }
}
