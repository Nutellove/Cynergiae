class AbstractBaseEntity
  entityProperties: {}
  hasLoaded: false
  hasChanged: false
  
  constructor: (@bundle, @entity, options) ->
  log:   (msg) -> console?.log?   msg ; this
  error: (msg) -> console?.error? msg ; this
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
  foo
  #constructor: (@bundle, @entity) ->

  _getBundleName: () -> 'JavascriptClassBundle'
  _getEntityName: () -> 'Ant'
  getFoo: () -> this._getProperty "foo"
  setFoo: () -> this._setProperty "foo"

class Entity extends BaseEntity
  move: ->
    alert "Slithering..."
    super 5