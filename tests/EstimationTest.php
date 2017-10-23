<?php
namespace Dpac\Dpac;

/**
 * Estimation Test Class
 *
 * @author William Blommaert <william@lunargravity.be>
 */
class EstimationTest extends \PHPUnit_Framework_TestCase
{
    protected $mixedRankComparisons;
    protected $mixedRankRepresentations;
    protected $mixedRankResults;

    protected $noRankedComparisons;
    protected $noRankedRepresentations;
    protected $noRankedResults;

    protected $realComparisons;
    protected $realRepresentations;
    protected $realResults;

    protected $nulledAbilityRepresentations;
    protected $uncomparedRepresentations;

    /**
     * Initialize all our test arrays
     */
    public function setUp()
    {
        $path = __DIR__ . '/fixtures/';

        $this->mixedRankComparisons = $this->getJsonContent($path . 'mixedRankedComparisons.json');
        $this->mixedRankRepresentations = $this->getJsonContent($path . 'mixedRankedRepresentations.json');
        $this->mixedRankResults = $this->getJsonContent($path . 'mixedRankedResults.json');

        $this->noRankedComparisons = $this->getJsonContent($path . 'noRankedComparisons.json');
        $this->noRankedRepresentations = $this->getJsonContent($path . 'noRankedRepresentations.json');
        $this->noRankedResults = $this->getJsonContent($path . 'noRankedResults.json');

        $this->realComparisons = $this->getJsonContent($path . 'realComparisons.json');
        $this->realRepresentations = $this->getJsonContent($path . 'realRepresentations.json');
        $this->realResults = $this->getJsonContent($path . 'realResults.json');

        $this->nulledAbilityRepresentations = $this->getJsonContent($path . 'nulledAbilityRepresentations.json');
        $this->uncomparedRepresentations = $this->getJsonContent($path . 'uncomparedRepresentations.json');
    }

    /**
     * Fetch the body of a json file for a given path
     *
     * @param $path
     * @return mixed
     */
    private function getJsonContent($path)
    {
        return json_decode(file_get_contents($path), true);
    }

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
                'id' => $item['_id'],
                'ability' => isset($item['ability']['value']) ? $item['ability']['value'] : null,
                'se' => isset($item['ability']['se']) ? $item['ability']['se'] : null,
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

    /**
     * If not all representations are to rank
     * it should equal R generated results if
     * all data are provided at once
     * @group rasch
     * @group raschfail
     */
    public function testNotAllRankEqualsRGeneratedResultsIfAllProvided()
    {
        $expected = array_filter($this->mixedRankResults, function ($item) {
            return $item['rankType'] == 'to rank';
        });

        $expected = $this->convertRepresentations($expected);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->mixedRankRepresentations);
        $comparisons = $this->convertComparisons($this->mixedRankComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If not all representations are to rank
     * it should equal R generated results if
     * estimates were first based on a part of the data
     * @group rasch
     * @group raschfail
     */
    public function testNotAllRankEqualsRGeneratedResultsFirstBasedPartOfData()
    {
        $expected = array_filter($this->mixedRankResults, function ($item) {
            return $item['rankType'] == 'to rank';
        });

        $expected = $this->convertRepresentations($expected);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->mixedRankRepresentations);
        $comparisons = $this->convertComparisons($this->mixedRankComparisons);

        $firstComparisons = array_rand($comparisons, count($comparisons) / 2);
        Estimation::estimate(['comparisons' => $firstComparisons, 'items' => $representations]);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If all representations are to rank
     * it should equal R generated results if
     * all data are provided at once
     * @group rasch
     * @group raschfail
     */
    public function testAllRankEqualsRGeneratedResultsIfAllProvided()
    {
        $expected = $this->convertRepresentations($this->noRankedResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->noRankedRepresentations);
        $comparisons = $this->convertComparisons($this->noRankedComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If all representations are to rank
     * it should equal R generated results if
     * estimates were first based on a part of the data
     * @group rasch
     * @group raschfail
     */
    public function testAllRankEqualsRGeneratedResultsFirstBasedPartOfData()
    {
        $expected = $this->convertRepresentations($this->noRankedResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->noRankedRepresentations);
        $comparisons = $this->convertComparisons($this->noRankedComparisons);

        $firstComparisons = array_rand($comparisons, count($comparisons) / 2);
        Estimation::estimate(['comparisons' => $firstComparisons, 'items' => $representations]);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If real data
     * it should equal R generated results
     * @group rasch
     * @group raschfail
     */
    public function testRealDataEqualsRGeneratedResults()
    {
        $expected = $this->convertRepresentations($this->realResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->realRepresentations);
        $comparisons = $this->convertComparisons($this->realComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If nulled abilities
     * it should equals R generated results
     * @group rasch
     * @group raschfail
     */
    public function testNulledAbilitiesEqualsRGeneratedResults()
    {
        $expected = $this->convertRepresentations($this->noRankedResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->nulledAbilityRepresentations);
        $comparisons = $this->convertComparisons($this->noRankedComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If uncompared representations
     * it should ignore them
     * @group rasch
     * @group raschfails
     */
    public function testUncomparedRepresentationsShouldIgnoreThem()
    {
        $expected = $this->convertRepresentations($this->noRankedResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->uncomparedRepresentations);
        $comparisons = $this->convertComparisons($this->noRankedComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);
        $actual = array_reduce($actual, [$this, 'mapToLookupHash'], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * If map of items
     * it should accept them and output in the same type
     * @group rasch
     * @group raschfails
     */
    public function testMapOfItemsShouldBeAcceptedAndOutputSameType()
    {
        $expected = $this->convertRepresentations($this->noRankedResults);
        $expected = array_reduce($expected, [$this, 'mapToLookupHash'], []);

        $representations = $this->convertRepresentations($this->uncomparedRepresentations);
        $representations = array_reduce($representations, [$this, 'mapToLookupHash'], []);

        $comparisons = $this->convertComparisons($this->noRankedComparisons);

        $actual = Estimation::estimate(['comparisons' => $comparisons, 'items' => $representations]);
        $actual = array_map([$this, 'prepResult'], $actual);

        $this->assertEquals($expected, $actual);
    }
}
