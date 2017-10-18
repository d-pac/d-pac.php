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
     * Compare given arrays by their amount of comparisons
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareByLength(array $a, array $b)
    {
        if (!is_array($a) || !is_array($b)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be of type array');
        }

        return count($a['compared']) - count($b['compared']);
    }

    /**
     * Compare given arrays by their ability
     *
     * @param array $a
     * @param array $b
     * @return mixed
     */
    public static function compareByAbility(array $a, array $b)
    {
        if (!is_array($a) || !is_array($b)) {
            throw new \InvalidArgumentException('Expected both parameters $a and $b to be of type array');
        }

        return $a['ability'] - $b['ability'];
    }
}
