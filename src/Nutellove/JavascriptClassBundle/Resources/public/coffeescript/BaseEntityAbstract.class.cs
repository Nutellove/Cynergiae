class AbstractBaseEntity
  entityProperties: {}
  hasLoaded: false
  hasChanged: false
  
  constructor: (bundle, entity, options)->
    this.bundle = bundle
    this.entity = entity
  log: (msg) -> console?.log? msg ; this
  _getProperty: (fieldName) ->
    if this.entityProperties[fieldName]?
      this.entityProperties[fieldName]
    else
      this.log "Problem" ; null
  _setProperty: (fieldName, fieldValue) ->
    if this.entityProperties[fieldName]?
      this.entityProperties[fieldName] = fieldValue
      this.hasChanged = true : this
    else
      this.log "Problem" ; null


class BaseEntity extends AbstractBaseEntity
  /* FOO VAR */
  foo
  constructor: (@name) ->

  getFoo: () -> this._getProperty "foo"
  setFoo: () -> this._setProperty "foo"

class Entity extends BaseEntity
  move: ->
    alert "Slithering..."
    super 5
