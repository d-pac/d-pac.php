<?php
namespace Dpac\Dpac;

use Dpac\Dpac\ValueObject as ValueObject;

/**
 * Estimation Class
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Estimation
{
    /**
     * Retrieve a ValueObject from the lookup service, or create one and store it
     *
     * @param $lookup
     * @param $id
     * @return ValueObject
     */
    public function getOrCreate($lookup, $id)
    {
        $obj = $lookup['objectsById'][$id];

        if ($obj) {
            return $obj;
        }

        $item = $lookup['itemsById'][$id];

        $obj = new ValueObject();

        $obj->setSelected(0);
        $obj->setCompared(0);
        $obj->setAbility($item->getAbility());
        $obj->setStandardDeviation($item->getStandardDeviation());
        $obj->setRanked($item->getRanked());
        $obj->setId($item->getId());

        $lookup['objectsById'][$id] = $obj;

        return $obj;
    }
}
