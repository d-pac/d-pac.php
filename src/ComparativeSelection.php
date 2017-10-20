<?php
namespace Dpac\Dpac;

use Dpac\Dpac\Exception\SelectionException as SelectionException;
use Dpac\Dpac\Util as Util;

/**
 * Comparative Selection
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class ComparativeSelection
{
    /**
     * @param array $items
     *
     * @throws \InvalidArgumentException
     * @throws SelectionException
     *
     * @return array $comparison
     */
    public static function select($items)
    {
        if (!is_array($items) || empty($items)) {
            throw new \InvalidArgumentException('Invalid Item array supplied for comparative selection');
        }

        // Assign the items to individual arrays because shuffle and sort don't return copies
        $sortedByCompared = $items;
        $sortedByAbility = $items;

        shuffle($sortedByCompared);

        usort($sortedByCompared, ['Dpac\Dpac\Util', 'compareByLength']);
        usort($sortedByAbility, ['Dpac\Dpac\Util','compareByAbility']);

        $selected = array_shift($sortedByCompared);

        $position = Util::findIndex($selected['id'], $sortedByAbility);

        if ($position === false) {
            throw new SelectionException("Invalid position for selected Item with id {$selected['id']}");
        }

        $N2 = count($sortedByAbility) / 2;
        $range = [];

        if ($position > $N2) {
            $range['bottom'] = 0;
            $range['top'] = (int) floor($N2);
        } else {
            $range['bottom'] = (int) round($N2);
            $range['top'] = count($sortedByAbility);
        }

        $sliced = array_slice($sortedByAbility, $range['bottom'], $range['top']);

        shuffle($sliced);

        // We use an anonymous custom sorting function because we need the external $selected->getId() value
        usort($sliced, function ($a, $b) use ($selected) {

            if (!is_array($a) || !is_array($b)) {
                throw new \InvalidArgumentException('Expected both parameters $a and $b to be arrays');
            }

            $aN = Util::getNumberOfComparisons($a['compared'], $selected['id']);
            $bN = Util::getNumberOfComparisons($b['compared'], $selected['id']);

            return $aN - $bN;
        });

        $opponent = array_shift($sliced);

        return [
            'a' => $selected['id'],
            'b' => $opponent['id']
        ];
    }
}
