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
    private $se;
    private $ranked;
    private $compared = [];

    /**
     * Constructor
     *
     * @param string $id
     * @param float $ability
     * @param array $compared
     */
    public function __construct($id, $ability, $compared, $ranked, $se)
    {
        $this->setId($id);
        $this->setAbility($ability);
        $this->setCompared($compared);
        $this->setStandardDeviation($se);
        $this->setRanked($ranked);
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
     * Get the ranking
     *
     * @return bool
     */
    public function getRanked()
    {
        return (bool) $this->ranked;
    }

    /**
     * Get the standard deviation
     *
     * @return mixed
     */
    public function getStandardDeviation()
    {
        return $this->se;
    }

    /**
     * Validate and set the ability
     *
     * @param float $ability
     */
    public function setAbility($ability)
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
    public function setCompared($compared)
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
    public function setId($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }

        $this->id = (string) $id;
    }

    /**
     * Set the ranking
     *
     * @param $ranked
     */
    public function setRanked($ranked)
    {
        $this->ranked = (bool) $ranked;
    }

    /**
     * Set the standard deviation
     *
     * @param $se
     */
    public function setStandardDeviation($se)
    {
        $this->se = $se;
    }

}
