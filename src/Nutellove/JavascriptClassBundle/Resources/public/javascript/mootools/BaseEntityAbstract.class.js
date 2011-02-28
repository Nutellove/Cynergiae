/**
 * This is the abstract class for the generated Mootools Base Entity Classes
 * It takes care of the XHR requests for synchronization with the PHP Entities
 *
 * @author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */

var BaseEntityAbstract = new Class({

  Implements: [Options],
  options: {
    controllerBaseUrl: 'jsclass' // The left-most part of the controller route
   ,debugMode: true
  },

  //entityProperties: null,
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
    this.entityProperties = new Object();

    //return this; // TODO : Find out what that would change
  },

  /**
   * Logs msg (which can be any type) to the console if it exists and debugMode is set to true
   * @param  msg
   * @return this
   */
  log: function(msg)
  {
    if (this.options.debugMode && console && console.log) {
      console.log (this._getLogPrefix(), msg);
    }

    return this;
  },

  /**
   * Logs error msg (which can be any type) to the console if it exists
   * @param  msg
   * @return this
   */
  error: function(msg)
  {
    if (console && console.error) {
      console.error (this._getLogPrefix(), msg);
    } else {
      alert ("Error with "+this._getLogPrefix()+". Activate the console and reload for more details.");
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
    if (this.id) {
      r += ' - #' + this.id;
    }

    return r;
  },

  _getControllerUrl: function()
  {
    return '/'+this.options.controllerBaseUrl +'/'+ this.bundle +'/'+ this.entity +'/';
  },

////////////////////////////////////////////////////////////////////////////////

  _getProperty: function(varName)
  {
    if (typeof this.entityProperties[varName] != "undefined" && this.entityProperties[varName] !== null) {
      return this.entityProperties[varName];
    } else {
      this.log ("Property '"+varName+"' is null.");
      return null;
    }
  },

  _setProperty: function(varName, varValue)
  {
    if (typeof this.entityProperties[varName] != "undefined" && this.entityProperties[varName] !== null) {
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
      var that = this; // better than bind() sometimes, but here I can't think of a reason. For the lulz ?
      this.saveRequest = new Request.JSON ({
        url:       this._getControllerUrl() + 'save/' + this.id,
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
        url:       this._getControllerUrl() + 'load/' + this.id,
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
  loadJSON: function(json)
  {
    this.log ('Loading following JSON :');
    this.log (json);
    if (json['id'] != this.id) {
      this.error ('JSON received has ID \'' + json['id'] + '\'.');
    } else {
      this.entityProperties = json['parameters'];
      this.hasLoaded = true;
    }
  },

  /**
   * Returns the JSON to send to the PHP Controller
   */
  saveJSON: function()
  {
    var jsonObject = new Object();
    jsonObject['id'] = this.id;
    jsonObject['parameters'] = this.entityProperties;
    
    return JSON.encode(jsonObject);
  },

////////////////////////////////////////////////////////////////////////////////

  /**
   * Fetches data from PHP/Database and inject it into the Class
   */
  load: function(id)
  {
    this.id = id;
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
      var json = this.saveJSON();
      this.saveRequest.post('json='+json);
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