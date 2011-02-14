# PROBLEM
Find out how to create custom column attribute in property mapping (see http://www.doctrine-project.org/docs/orm/2.0/en/reference/basic-mapping.html#property-mapping)

# IMPORTANT

This bundle is NOT finished nor working right now. Its aim is to provide easy Mootools Class integration for each Entity you define in the Doctrine 2 ORM.
The generated classes will provide the usual save() and load() methods (or maybe persist() ?)

There are many, **many** things to do to improve this Bundle, such as :

* Making it work :
  * Javascript
    * Abstract Class (50%)
    * Base Class Generation (50%)
    * Class Initialization (10%)
  * PHP
    * Abstract Controller (0%)
    * Base Controller Generation (0%)
    * Controller Initialization (0%)
  * Routing
  * Find a way to regroup all HTML `<script type="text/javascript">` inclusions needed, maybe generate symlinks under `/web`
  * Find how to require Mootools ≥ 1.3 in dependencies
* Cleaning up my messy, feature-testing code
* Move Ant & Anthill Entities to Tests
* Writing up Tests (I swear I'll take time to write some, and then some more)
* Writing a command that does all the Entities within a Bundle

# USAGE

    $ app/console mootools:generate:entity <bundle> <entity>

Replace <bundle> and <entity> by the names of the Bundle and Entity you want to
generate the Mootools Classes of.

## WHAT IS IT?

Each Doctrine 2 Entity converted to Mootools will need a total of 6 files :

### Javascript Mootools Classes
* `Nutellove/MootoolsClassBundle/Resources/js/BaseEntityAbstract.class.js` is a static (non-generated) Class that holds the XHR magic
* `Entity/Mootools/Base/Base<Entity>.class.js` is a generated Mootools Entity Base Class that extends the above BaseEntityAbstract Class, you should *not* manually edit this file
* `Entity/Mootools/<Entity>.class.js` extends the above Base class, is initially generated empty and not overwritten, so you might write your custom logic inside 

### PHP AJAX Controllers
* Abstract (shared by all Controllers)
* Base (re-generated each time)
* Final (initially empty, then user-defined)
TODO :)
But there'll be 3 files, on the same model than JS ones : Abstract, Base, and Final.



Therefore, the first time you execute the command-line for a given Bundle/Entity pair, 4 files will be generated.
After that, only the 2 Base files will be re-generated.
If I'm not miskaten, the heritage behavior is similar to the Doctrine 1.2 PHP Classes generation from schema.

## WHY NOT JQUERY?

Hah! Be my guest!
Mootools is pure javascript awesomeness.

## THANKS

- Fabien Potencier, for he taught me *a lot*
- Jonathan Wage, cause he's awesome too
- Aaron Newton, kudos to his super-cow-powers





