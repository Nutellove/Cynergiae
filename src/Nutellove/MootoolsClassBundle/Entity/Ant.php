<?php

namespace Nutellove\MootoolsClassBundle\Entity;

/**
 * Nutellove\MootoolsClassBundle\Entity\Ant
 */
class Ant
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $size
     */
    private $size;

    /**
     * @var Nutellove\MootoolsClassBundle\Entity\Anthill
     */
    private $anthill;


    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set size
     *
     * @param integer $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get size
     *
     * @return integer $size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set anthill
     *
     * @param Nutellove\MootoolsClassBundle\Entity\Anthill $anthill
     */
    public function setAnthill(\Nutellove\MootoolsClassBundle\Entity\Anthill $anthill)
    {
        $this->anthill = $anthill;
    }

    /**
     * Get anthill
     *
     * @return Nutellove\MootoolsClassBundle\Entity\Anthill $anthill
     */
    public function getAnthill()
    {
        return $this->anthill;
    }
}