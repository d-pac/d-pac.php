<?php
namespace Dpac\Dpac;

/**
 * Class Comparison
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Comparison
{
    private $a;
    private $b;

    /**
     * Constructor.
     *
     * @param string $a
     * @param string $b
     */
    public function __construct($a, $b)
    {
        $this->setIds($a, $b);
    }

    /**
     * Validate and set the item ids
     *
     * @param string $a
     * @param string $b
     */
    protected function setIds($a, $b)
    {
        if (empty($a) || empty($b)) {
            throw new \InvalidArgumentException('Ids cannot be empty');
        }

        $this->a = (string) $a;
        $this->b = (string) $b;
    }
}