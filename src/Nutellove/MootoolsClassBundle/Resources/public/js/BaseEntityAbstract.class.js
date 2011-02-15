/**
 * This is the abstract class for the generated Mootools Base Entity Classes
 * It takes care of the XHR requests for synchronization with the PHP Entities
 *
 * @author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */

var BaseEntityAbstract = new Class({

  Implements: [Options],
  options: {
    controllerBaseUrl: 'mootools' // The left-most part of the controller route
  },

  entityProperties: {},
  entityMethods: {},

  hasLoaded: false,
  hasChanged: false,

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

  log: function(msg)
  {
    if (console && console.log) {
      console.log (this._getLogPrefix(), msg);
    }

    return this;
  },

  error: function(msg)
  {
    if (console && console.error) {
      console.error (this._getLogPrefix(), msg);
    }

    return this;
  },

////////////////////////////////////////////////////////////////////////////////

  _getFullEntityName: function()
  {
    return this.bundle+'/'+this.entity;
  },

  _getLogPrefix: function()
  {
    var r = this._getFullEntityName();
    if (this.getId && this.getId()) {
      r += ' - #' + this.getId();
    }

    return r;
  },

  _getControllerUrl: function()
  {
    return this.options.controllerBaseUrl +'/'+ this.bundle +'/'+ this.entity +'/';
  },

////////////////////////////////////////////////////////////////////////////////

  _getProperty: function(varName)
  {
    if (this.entityProperties[varName] != null) {
      return this.entityProperties[varName];
    } else {
      this.log ("Property '"+varName+"' is null.");
      return null;
    }
  },

  _setProperty: function(varName, varValue)
  {
    if (this.entityProperties[varName] != null) {
      if (this.entityProperties[varName] != varValue) {
        this.entityProperties[varName] = varValue;
        this.hasChanged = true;
      } else {
        this.log ("Setting the same value property '"+varName+"' already has.");
      }
    } else {
      this.log ("Setting a value to the null-defined property '"+varName+"'.");
      this.entityProperties[varName] = varValue;
      this.hasChanged = true;
    }

    return this;
  },

////////////////////////////////////////////////////////////////////////////////

  /**
   * Initializes (if needed) the XHR request that'll be used by the save method
   */
  initializeSaveRequest: function(){
    if (!this.saveRequest) {
      var that = this; // better than bind() sometimes
      this.saveRequest = new Request.JSON ({
        url:       this._getControllerUrl() + 'save',
        method:    'post',
        onSuccess: function(json, text){
          that.log ("Save of "+that._getFullEntityName()+" successful.");
        },
        onFailure: function(){
          that.error ("Save of "+that._getFullEntityName()+" failed.");
        }
      });
    }

    return this;
  },

  /**
   * Initializes (if needed) the XHR request that'll be used by the load method
   */
  initializeLoadRequest: function(){
    if (!this.loadRequest) {
      var that = this; // better than bind() sometimes
      this.loadRequest = new Request.JSON ({
        url:       this._getControllerUrl() + 'load',
        method:    'get',
        onSuccess: function(json, text){
          that.loadJSON (json);
          that.log ("Load of "+that._getFullEntityName()+" successful.");
        },
        onFailure: function(){
          that.error ("Load of "+that._getFullEntityName()+" failed.");
        }
      });
    }

    return this;
  },

////////////////////////////////////////////////////////////////////////////////

  /**
   * Loads the passed JSON in the properties of the JS Entity
   */
  loadJSON: function(jsonProperties)
  {
    this.log (jsonProperties);
    this.entityProperties = jsonProperties;
    this.hasLoaded = true;
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

  /**
   * Fetches data from PHP/Database and inject it into the Class
   */
  load: function()
  {
    this.initializeLoadRequest();
    this.loadRequest.send();

    return this;
  },

  /**
   * Sends data to PHP/Database
   */
  save: function()
  {
    if (this.hasLoaded && this.hasChanged) {
      this.initializeSaveRequest();
      this.saveRequest.post(this.entityProperties);
    } else {
      this.log("Trying to save though nothing has changed.");
    }

    return this;
  },

////////////////////////////////////////////////////////////////////////////////

  executeMethod: function(methodName, params)
  {
    // TODO : for later, execute custom method defined in PHP controller
  }
});