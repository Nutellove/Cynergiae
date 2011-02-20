<?php

namespace Nutellove\JavascriptClassBundle\Entity;

/**
 * Nutellove\JavascriptClassBundle\Entity\Ant
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
     * @var boolean $is_hungry
     */
    private $is_hungry;


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
     * Set is_hungry
     *
     * @param boolean $isHungry
     */
    public function setIsHungry($isHungry)
    {
        $this->is_hungry = $isHungry;
    }

    /**
     * Get is_hungry
     *
     * @return boolean $isHungry
     */
    public function getIsHungry()
    {
        return $this->is_hungry;
    }
}