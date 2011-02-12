/**
 * This is the abstract class for the generated Mootools Base Entity Classes
 * It takes care of the XHR requests for synchronization with the PHP Entities
 */

var BaseEntityAbstract = new Class({
  Implements: [Options],
  options: {},
  
  /**
   * Constructor
   * 
   * @param string bundle  The Bundle name
   * @param string entity  The Entity name
   * @param object options The usual options object, defaults above
   */
  initialize: function(bundle, entity, options)
  {
    this.bundle = bundle;
    this.entity = entity;
    this.setOptions (options);
  },

  /**
   * 
   */
  initializeSaveRequest: function(){
	if (!this.saveRequest) {
    var that = this; // better than bind() sometimes
      this.saveRequest = new Request.JSON ({
        url: this.url,
        onSuccess: function(json, text){
          
        }
      });
    }
  },


  loadJSON: function(json)
  {

  },

  saveJSON: function()
  {

  },

  executeMethod: function(methodName, params)
  {
    // TODO : later
  }
});