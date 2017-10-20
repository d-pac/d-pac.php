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
     * Compare given item arrays by their ability
     *
     * @throws \InvalidArgumentException
     * @throws ComparisonException
     *
     * @param array $a
     * @param array $b
     * @return mixed
     */
    public static function compareByAbility($a, $b)
    {
        if (!is_array($a) || !is_array($b)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be arrays');
        }

        if (!is_float($a['ability']) || !is_float($b['ability'])) {
            throw new ComparisonException('Expected both ability values to be of type float');
        }

        return (float) $a['ability'] - (float) $b['ability'];
    }

    /**
     * Compare given item arrays by the amount of comparisons they contain
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareByLength($a, $b)
    {
        if (!is_array($a) || !is_array($b)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be arrays');
        }

        return count($a['compared']) - count($b['compared']);
    }

    /**
     * Get the index for the given id in an items array
     *
     * @param string $id
     * @param array $items
     * @return int|bool $index
     */
    public static function findIndex($id, $items)
    {
        foreach ($items as $index => $item) {
            if ($item['id'] === (string) $id) {
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
