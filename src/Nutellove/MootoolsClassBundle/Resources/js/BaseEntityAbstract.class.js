/**
 * This is the abstract class for the generated Mootools Base Entity Classes
 * It takes care of the XHR requests for synchronization with the PHP Entities
 * 
 * @author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */

var BaseEntityAbstract = new Class({
  Implements: [Options],
  options: {},
  
  entityProperties: {},
  entityMethods: {},
  
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
  
  log: function(msg){
    if (console && console.log) {
      console.log (msg);
    }
  },

  error: function(msg){
    if (console && console.error) {
      console.error (msg);
    }
  },
  
  getFullEntityName: function(){
    return this.bundle+'/'+this.entity;
  },
  
  getControllerUrl: function(){
    // FIXME
  },
  
////////////////////////////////////////////////////////////////////////////////
  
  /**
   * Initializes (if needed) the XHR request that'll be used by the save method
   */
  initializeSaveRequest: function(){
    if (!this.saveRequest) {
      var that = this; // better than bind() sometimes
      this.saveRequest = new Request.JSON ({
        url:       this.getControllerUrl(),
        method:    'post',
        onSuccess: function(json, text){
          that.log ("Save of "+that.getFullEntityName()+" successful.");
        },
        onFailure: function(){
          that.error ("Save of "+that.getFullEntityName()+" failed.");
        }
      });
    }
  },

  /**
   * Initializes (if needed) the XHR request that'll be used by the load method
   */
  initializeLoadRequest: function(){
    if (!this.loadRequest) {
      var that = this; // better than bind() sometimes
      this.loadRequest = new Request.JSON ({
        url:       this.getControllerUrl(),
        method:    'get',
        onSuccess: function(json, text){
          that.loadJSON (json);
          that.log ("Load of "+that.getFullEntityName()+" successful.");
        },
        onFailure: function(){
          that.error ("Load of "+that.getFullEntityName()+" failed.");
        }
      });
    }
  },

////////////////////////////////////////////////////////////////////////////////

  /**
   * Loads the passed JSON in the properties of the JS Entity
   */
  loadJSON: function(json)
  {
    this.log (json);
    this.entityProperties = json;
  },

  /**
   * Returns the JSON to send to the PHP Controller
   */
  saveJSON: function()
  {
    var json = JSON.encode(this.entityProperties);

    return json;
  },

////////////////////////////////////////////////////////////////////////////////

  load: function()
  {
    this.initializeLoadRequest();
    this.loadRequest.send();
  },
  
  save: function()
  {
    this.initializeSaveRequest();
    this.saveRequest.post(this.entityProperties);
  },

////////////////////////////////////////////////////////////////////////////////

  executeMethod: function(methodName, params)
  {
    // TODO : for later, execute custom method defined in PHP controller
  }
});