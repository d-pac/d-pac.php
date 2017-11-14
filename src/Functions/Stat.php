<?php
namespace Dpac\Dpac\Functions;

/**
 * Stat class containing various statistical functions
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Stat
{
    /**
     * Squares a number
     *
     * @param $value
     * @return float|int
     */
    public static function square($value)
    {
        if (!is_float($value) && !is_int($value)) {
            throw new \InvalidArgumentException('Expected value to be of type float or int');
        }

        return pow($value, 2);
    }

    /**
     * Addition of a sequence of numbers
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function sum($values, callable $callback = null)
    {
        if (!count($values)) {
            return 0;
        }

        if (!isset($callback) || !is_callable($callback)) {
            return array_sum($values);
        } else {
            return array_sum($callback($values));
        }
    }

    /**
     * Arithmetic mean, sum of a sequence of numbers divided by sequence length
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function mean($values, callable $callback = null)
    {
        if (!count($values)) {
            return 0;
        }

        $sum = 0;

        if (isset($callback) && is_callable($callback)) {
            $values = $callback($values);
        }

        for ($i = 0; $i < count($values); $i++) {
            $sum += $values[$i];
        }

        return $sum / count($values);
    }

    /**
     * The distance between numbers in a set
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function variance($values, callable $callback = null)
    {
        if (!count($values)) {
            return 0;
        }

        if (isset($callback) && is_callable($callback)) {
            $values = $callback($values);
        }

        $mean = self::mean($values);

        $sumOfSquares = 0;

        for ($i = 0; $i < count($values); $i++) {
            $sumOfSquares += ($values[$i] - $mean) * ($values[$i] - $mean);
        }

        return $sumOfSquares / (count($values) - 1);
    }

    /**
     * Standard deviation
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function standardDeviation($values, callable $callback = null)
    {
        return sqrt(self::variance($values, $callback));
    }

    /**
     * Root mean square, quadratic mean
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function quadraticMean($values, callable $callback = null)
    {
        return sqrt(self::mean($values, $callback));
    }

    /**
     * Median of a sequence of numbers
     *
     * @param $values
     * @param callable|null $callback (optional) function to retrieve the values from $values
     * @return float|int
     */
    public static function median($values, callable $callback = null)
    {
        if (!count($values)) {
            return 0;
        }

        if (isset($callback) && is_callable($callback)) {
            $values = $callback($values);
        }

        sort($values, SORT_NUMERIC);

        $n = count($values);
        $n2 = (int) floor($n / 2);
        $mod = $n % 2;

        return ($mod) ? $values[$n2] : ($values[$n2 - 1] +  $values[$n2]) / 2;
    }

    /**
     * Calculate standard scores of z scores: z = (x - mu) / sigma
     *
     * @param $values
     * @param callable|null $getter
     * @param callable|null $setter
     * @return array|int
     */
    public static function standardize($values, callable $getter = null, callable $setter = null)
    {
        if (!count($values)) {
            return 0;
        }

        $meanValue = self::mean($values, $getter);
        $sdValue = self::standardDeviation($values, $getter);

        if (isset($getter) && is_callable($getter)) {
            $values = $getter($values);
        }

        $values = array_map(function ($value) use ($sdValue, $meanValue) {
            $v = $value / $sdValue;

            if (isset($setter) && is_callable($setter)) {
                $setter($value, $v);
            }

            return $v;
        }, $values);

        return $values;
    }
}
