<?php
namespace Dpac\Dpac;

/**
 * Estimation Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class EstimationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Converts representations to a usable format
     *
     * @param array $representations
     * @return array
     */
    private function convertRepresentations($representations)
    {
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'ability' => $item['ability']['value'],
                'se' => $item['ability']['se'],
                'ranked' => $item['rankType'] !== 'to rank'
            ];
        }, $representations);
    }

    /**
     * Converts comparisons to a usable format
     *
     * @param $comparisons
     * @return array
     */
    private function convertComparisons($comparisons)
    {
        return array_map(function ($item) {
            return [
                'selected' => isset($item['data']['selection']) ? $item['data']['selection'] : null,
                'a' => $item['representations']['a'],
                'b' => $item['representations']['b']
            ];
        }, $comparisons);
    }

    /**
     * @param $memo
     * @param $r
     * @return mixed
     */
    private function mapToLookupHash($memo, $r)
    {
        $memo[$r['id']] = $r;

        return $memo;
    }

    /**
     * @param $o
     * @return array
     */
    private function prepResult($o)
    {
        $o['ability'] = round($o['ability'], 4);
        $o['se'] = round($o['se'], 4);

        return [
            'id' => $o['id'],
            'ability' => $o['ability'],
            'se' => $o['se'],
            'ranked' => $o['ranked']
        ];
    }
}
