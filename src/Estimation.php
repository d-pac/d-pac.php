<?php
namespace Dpac\Dpac;

use Dpac\Dpac\Functions\Pm as Pm;
use Dpac\Dpac\Util as Util;

/**
 * Estimation Class
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Estimation
{
    /**
     * Retrieve a valueObject array from the lookup array, or create one and store it in the lookup array
     *
     * @param array $lookup an array containing sub-arrays, passed by reference
     * @param string $id
     * @return array $valueObject
     */
    public static function getOrCreate(&$lookup, $id)
    {
        $valueObject = $lookup['objectsById'][$id];

        if ($valueObject) {
            return $valueObject;
        }

        $item = $lookup['itemsById'][$id];

        $valueObject = [
            'id' => $item['id'],
            'selectedNum' => 0,
            'comparedNum' => 0,
            'ability' => $item['ability'],
            'se' => $item['se'],
            'ranked' => $item['ranked'],
        ];

        $lookup['objectsById'][$id] = $valueObject;

        return $valueObject;
    }

    /**
     * Increments the comparedNum property of an object array
     * and if it's the first comparison for an unranked item also
     * initializes its abilities and stores it in the unranked lookup array
     *
     * @throws \InvalidArgumentException
     * @param $lookup
     * @param $id
     */
    public static function prepValuesOnFirstComparison(&$lookup, $id)
    {
        $object = self::getOrCreate($lookup, $id);

        $object['comparedNum']++;

        if ($object['comparedNum'] === 1 && !$object['ranked']) {
            $object['ability'] = 0;
            $object['se'] = 0;

            if (empty($lookup['unrankedById'])) {
                throw new \InvalidArgumentException('Expected lookup to contain sub-array unrankedById');
            }

            $lookup['unrankedById'][$id] = $object;
        }
    }

    /**
     * Conditional maximum likelihood
     *
     * @param $lookup
     * @param $comparisons
     * @param $iteration
     */
    public static function CML(&$lookup, $comparisons, $iteration)
    {
        if (empty($lookup['unrankedById'])) {
            throw new \InvalidArgumentException('Expected lookup to contain sub-array unrankedById');
        }

        $previousUnranked = $lookup['unrankedById'];

        $ids = array_keys($lookup['unrankedById']);

        foreach ($ids as $id) {
            $current = $lookup['objById'][$id];
            $prev = $previousUnranked[$id];

            $expected = array_reduce(
                $comparisons,
                function ($memo, $comparison) use ($id, $previousUnranked, $lookup, $prev) {

                    $filteredIds = array_filter([$comparison['a'], $comparison['b']], function ($value) use ($id) {

                        return $value == $id;
                    });

                    if ($comparison['selected'] && count($filteredIds) === 1) {
                        $opponent = $previousUnranked[$filteredIds[0]] || $lookup['objById'][$filteredIds[0]];
                        $memo['value'] += Pm::rasch($prev['ability'], $opponent['ability']);
                        $memo['info'] += Pm::fisher($prev['ability'], $opponent['ability']);
                    }

                    return $memo;
                },
                ['value' => 0, 'info' => 0]
            );

            if ($iteration > 0) {
                $current['ability'] = Util::clamp(
                    $current['ability'] + (($current['selectedNum'] - $expected['value']) / $expected['info']),
                    -10,
                    10
                );
            } else {
                $current['se'] = Util::clamp(1 / sqrt($expected['info']), -200, 200);
            }
        }
    }
}
