<?php
namespace Dpac\Dpac;

/**
 * Class Item
 *
 * @author William Blommaert <william@lunargravity.be>
 * @author Camille Reynders <camille.reynders@imec.be>
 */
class Item
{
    private $id;
    private $ability;
    private $compared = [];

    /**
     * Constructor
     *
     * @param string $id
     * @param float $ability
     * @param array $compared
     */
    public function __construct($id, $ability, $compared)
    {
        $this->setId($id);
        $this->setAbility($ability);
        $this->setCompared($compared);
    }

    /**
     * Get the ability
     *
     * @return float
     */
    public function getAbility()
    {
        return (float) $this->ability;
    }

    /**
     * Get the compared array
     *
     * @return array
     */
    public function getCompared()
    {
        return (array) $this->compared;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return (string) $this->id;
    }

    /**
     * Validate and set the ability
     *
     * @param float $ability
     */
    protected function setAbility($ability)
    {
        if (!is_float($ability)) {
            throw new \InvalidArgumentException('Expected ability to be a float value');
        }

        $this->ability = (float) $ability;
    }

    /**
     * Validate and set the compared array
     *
     * @param array $compared
     */
    protected function setCompared($compared)
    {
        if (!is_array($compared)) {
            throw new \InvalidArgumentException('Expected compared to be an array');
        }

        $this->compared = $compared;
    }

    /**
     * Validate and set the id
     *
     * @param string $id
     */
    protected function setId($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }

        $this->id = (string) $id;
    }
}
