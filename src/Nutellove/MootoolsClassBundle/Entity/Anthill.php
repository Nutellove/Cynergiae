<?php

namespace Nutellove\MootoolsClassBundle\Entity;

/**
 * Nutellove\MootoolsClassBundle\Entity\Anthill
 */
class Anthill
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
     * @var Nutellove\MootoolsClassBundle\Entity\Ant
     */
    private $ants;

    public function __construct()
    {
        $this->ants = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Add ants
     *
     * @param Nutellove\MootoolsClassBundle\Entity\Ant $ants
     */
    public function addAnts(\Nutellove\MootoolsClassBundle\Entity\Ant $ants)
    {
        $this->ants[] = $ants;
    }

    /**
     * Get ants
     *
     * @return Doctrine\Common\Collections\Collection $ants
     */
    public function getAnts()
    {
        return $this->ants;
    }
}