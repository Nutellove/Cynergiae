class Options
  options: {}
  setOptions: (o) ->
    for own key, value of o
      @options[key] = value
    this

class AbstractBaseEntity extends Options
  entityProperties: {}
  hasLoaded: false
  hasChanged: false
  
  constructor: (@bundle, @entity, options) -> @setOptions options

  log:   (msg) -> console?.log?   msg ; this
  error: (msg) -> console?.error? msg ; this

  _getBundleName:     () -> @bundle
  _getEntityName:     () -> @entity
  _getFullEntityName: () -> @bundle + '/' + @entity

  _getLogPrefix: () ->
    r = ' - #'+i if i=this.getId?()
    this._getFullEntityName() + r

  _getProperty: (fieldName) ->
    if this.entityProperties[fieldName]?
      this.entityProperties[fieldName]
    else
      this.log "Field Name "+fieldName+" inexistent." ; null
  _setProperty: (fieldName, fieldValue) ->
    if this.entityProperties[fieldName]?
      this.entityProperties[fieldName] = fieldValue
      this.hasChanged = true : this
    else
      this.log "Field Name "+fieldName+" inexistent." ; this


class BaseEntity extends AbstractBaseEntity
  /* FOO VAR */
  foo: 'prout'
  #constructor: (@bundle, @entity) ->

  getFoo: () -> this._getProperty "foo"
  setFoo: () -> this._setProperty "foo"

class Entity extends BaseEntity

#a = new Entity('JavascriptClassBundle', 'Ant', {})
#a.setFoo "BEER"
#alert a.getFoo()