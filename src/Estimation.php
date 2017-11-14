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
    protected static function getOrCreate(&$lookup, $id)
    {
        if (!empty($lookup['objectsById'][$id])) {
            return $lookup['objectsById'][$id];
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
    protected static function prepValuesOnFirstComparison(&$lookup, $id)
    {
        $object = self::getOrCreate($lookup, $id);
        $object = $lookup['objectsById'][$object['id']];

        $lookup['objectsById'][$object['id']]['comparedNum']++;

        if ($lookup['objectsById'][$object['id']]['comparedNum'] === 1 && !$lookup['objectsById'][$object['id']]['ranked']) {
            $lookup['objectsById'][$object['id']]['ability'] = 0;
            $lookup['objectsById'][$object['id']]['se'] = 0;

            $lookup['unrankedById'][$id] = $lookup['objectsById'][$object['id']];
        }
    }

    /**
     * Conditional maximum likelihood
     *
     * @param $lookup
     * @param $comparisons
     * @param $iteration
     */
    protected static function CML(&$lookup, $comparisons, $iteration)
    {
        $previousUnranked = &$lookup['unrankedById'];

        foreach (array_keys($lookup['unrankedById']) as $id) {
            $current = &$lookup['objectsById'][$id];
            $prev = &$previousUnranked[$id];

            $expected = array_reduce(
                $comparisons,
                function ($memo, $comparison) use ($id, $previousUnranked, &$lookup, $prev) {

                    $filteredIds = array_filter([$comparison['a'], $comparison['b']], function ($value) use ($id) {

                        return $value == $id;
                    });

                    if ($comparison['selected'] && count($filteredIds) === 1) {
                        $opponent = $previousUnranked[reset($filteredIds)] ?: $lookup['objectsById'][reset($filteredIds)];
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
                $current['se'] = Util::clamp(1 / sqrt($expected['info']), -3000, 200);
            }

        }
    }

    /**
     * Estimates the items featured in comparisons
     *
     * @param array $payload an array containing items and comparisons in separate sub-arrays
     * @return array|mixed
     */
    public static function estimate($payload)
    {
        $items = $payload['items'];
        $comparisons = $payload['comparisons'];
        $lookup = [
            'itemsById' => [],
            'objectsById' => [],
            'unrankedById' => []
        ];

        if (is_array($items)) {
            foreach ($items as $item) {
                $lookup['itemsById'][$item['id']] = $item;
            }
        } else {
            $lookup['itemsById'] = $items;
        }

        // We need to tally some stuff:
        // - which item has been selected
        // - which items have been compared
        foreach ($comparisons as $comparison) {
            // We only want to take "finished" comparisons into account
            if ($comparison['selected']) {
                $valueObject = self::getOrCreate($lookup, $comparison['selected']);
                $lookup['objectsById'][$valueObject['id']]['selectedNum']++;
                //$valueObject['selectedNum']++;

                self::prepValuesOnFirstComparison($lookup, $comparison['a']);
                self::prepValuesOnFirstComparison($lookup, $comparison['b']);
            }
        }

        foreach (array_keys($lookup['unrankedById']) as $id) {
            // Correction to avoid infinity values later on
            $object = $lookup['unrankedById'][$id];
            $interm = $object['comparedNum'] - (2 * 0.003);
            $interm = ($interm * $object['selectedNum']) / $object['comparedNum'];

            $lookup['unrankedById'][$id]['selectedNum'] = 0.003 + $interm;
        }

        // Loop 4 times (+1) through the estimation
        for ($i = 4; $i >= 0; $i--) {
            self::CML($lookup, $comparisons, $i);
        }

        return $lookup['unrankedById'];
    }
}
