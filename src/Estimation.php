<?php
namespace Dpac\Dpac;

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
            'selected' => 0,
            'compared' => 0,
            'ability' => $item['ability'],
            'se' => $item['se'],
            'ranked' => $item['ranked'],
        ];

        $lookup['objectsById'][$id] = $valueObject;

        return $valueObject;
    }
}
