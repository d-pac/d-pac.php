<?php
namespace Dpac\Dpac\Functions;

use Dpac\Dpac\Functions\Stat as Stat;

/**
 * Pm class containing various statistical functions
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Pm
{
    /**
     * Overall consistency of a measure
     *
     * @param float|int $sd Standard deviation
     * @param float|int $rmse RMS of the SE
     * @return float|int
     */
    public static function reliability($sd, $rmse)
    {
        $gsq = Stat::square($sd / $rmse);

        return ($gsq - 1) / $gsq;
    }

    /**
     * Creates a function by taking two getters, which are used to calculate the reliability of a set of values and SE's
     *
     * @param callable $getAbility
     * @param callable $getSE
     * @return \Closure
     */
    public static function reliabilityFunctor(callable $getAbility, callable $getSE)
    {
        return function ($list) use ($getAbility, $getSE) {
            return self::reliability(Stat::standardDeviation($list, $getAbility), Stat::quadraticMean($list, $getSE));
        };
    }

    /**
     * Rasch probability or Bradley-Terry-Luce probability
     *
     * @param $a
     * @param $b
     * @return float|int
     */
    public static function rasch($a, $b)
    {
        $expDiff = exp($a - $b);
        $result = $expDiff / (1 + $expDiff);

        return $result;
    }

    /**
     * Fisher information
     *
     * @param $a
     * @param $b
     * @param $digits
     * @return float
     */
    public static function fisher($a, $b, $digits = null)
    {
        $r = self::rasch($a, $b);
        $info = $r * (1 - $r);

        if ($digits) {
            return round($info, $digits);
        }

        return (float) $info;
    }
}