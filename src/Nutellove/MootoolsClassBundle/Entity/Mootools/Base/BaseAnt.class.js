
/**
 * Auto-generated by MootoolsClassBundle,
 * This is the Base Class upon which you extend your JS Entities
 * @require Javascript Framework Mootools ≥ 1.3
 */

/**
 * Nutellove\MootoolsClassBundle\Entity\Ant
 */
var BaseAnt = new Class({

  Extends: [BaseEntityAbstract],

  /**
   * @var integer id
   */
  id,

  /**
   * @var string name
   */
  name,

  /**
   * @var integer size
   */
  size,

  /**
   * @var boolean is_hungry
   */
  is_hungry,

  /**
   * @var Nutellove\MootoolsClassBundle\Entity\Anthill
   */
  anthill,


  
  /**
   * Set is_hungry
   *
   * @param boolean  isHungry
   */
  setIsHungry: function (isHungry)
  {
    this.is_hungry = isHungry;
  },
  

  
  /**
   * Get is_hungry
   *
   * @return boolean  isHungry
   */
  getIsHungry: function ()
  {
    return this.is_hungry;
  },
  

  
  /**
   * Set anthill
   *
   * @param Nutellove\MootoolsClassBundle\Entity\Anthill  anthill
   */
  setAnthill: function (anthill)
  {
    this.anthill = anthill;
  },
  

  
  /**
   * Get anthill
   *
   * @return Nutellove\MootoolsClassBundle\Entity\Anthill  anthill
   */
  getAnthill: function ()
  {
    return this.anthill;
  },
  
});
