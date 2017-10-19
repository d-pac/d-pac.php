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
    private $selected;

    /**
     * Constructor.
     *
     * @param string $a
     * @param string $b
     */
    public function __construct($a, $b)
    {
        $this->setIds($a, $b);
        $this->selected = '';
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
     * Get the selected id
     *
     * @return string
     */
    public function getSelected()
    {
        return $this->selected;
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

    /**
     * Set the selected id
     *
     * @param $id
     */
    public function setSelected($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }

        $this->selected = $id;
    }
}
