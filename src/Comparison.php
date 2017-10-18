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
     * Get the id of the A item
     *
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * Get the id of the B item
     *
     * @return mixed
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * Get an array of both ids
     *
     * @return array
     */
    public function getIds()
    {
        return ['a' => $this->a, 'b' => $this->b];
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
