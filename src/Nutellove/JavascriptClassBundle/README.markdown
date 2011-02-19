# IMPORTANT

This bundle is **NOT finished nor working** right now. Its would-be aim is to provide a generated doctrine-like environment in javascript based on the same mapping.
However, its realistic, first-release aim will *not* provide an Entity Manager nor support for Entity Associations.
Therefore, for now, only basic stuff like accessors and mutators for Entity fields, but hey, it's better than nothing !

## TODO

There are many, **many** things to do to improve this Bundle, such as :

* Making it work :
  * Javascript (mootools)
    * Abstract Class (80%)
    * Base Class Generation (90%)
    * Class Initialization (done)
  * PHP
    * Abstract Controller (10%)
    * Base Controller Generation (10%)
    * Controller Initialization (10%)
    * Template loading all files for one Entity, prelude to EM. (=>asset)
  * Routing (10%)
  * Asset deployment to `web/` (hook global cmd-line or create one ?)
  * Mootools ≥ 1.3 in dependencies (Quote : A bundle should not embed third-party libraries written in JavaScript)
* Cleaning up my student code :p
  * Validate [the guidelines](http://docs.symfony-reloaded.org/guides/bundles/best_practices.html)
  * Move Ant & Anthill Entities to Tests
  * Writing up Tests (I swear I'll take time to write some, and then some more)
* Release
  * Writing a command that does all the Entities within a Bundle
  * Add options in config
* Next
  * Abstract Class for Generators (code refactorization)
  * Minifying => Assetic :)
* Future
  * Adding new JS Entity Classes :
    * Pure JS (CoffeeScript looks good)
    * jQuery
  * JS Entity Manager
  * Collections
  * Entity Associations
    * One-to-One
      * Unidirectional
      * Bidirectional
      * Self-referencing
    * One-to-Many
      * Unidirectional with Join Table
      * Bidirectional
      * Self-referencing
    * Many-To-One, Unidirectional
    * Many-to-Many
      * Unidirectional
      * Bidirectional
      * Self-referencing

# USAGE

    $ app/console mootools:generate:entity <bundle> <entity>

Replace `<bundle>` and `<entity>` by the names of the Bundle and Entity you want to
generate the Mootools Classes of.

## WHAT IS IT?

Each Doctrine 2 Entity converted to Mootools will need a total of 6 files :

### Javascript Mootools Classes

* `Nutellove/JavascriptClassBundle/Resources/js/BaseEntityAbstract.class.js` is a static (non-generated) Class that holds the XHR magic
* `Entity/Mootools/Base/Base<Entity>.class.js` is a generated Mootools Entity Base Class that extends the above BaseEntityAbstract Class, you should *not* manually edit this file
* `Entity/Mootools/<Entity>.class.js` extends the above Base class, is initially generated empty and not overwritten, so you might write your custom logic inside 

### PHP AJAX Controllers

* Abstract (shared by all Controllers)
* Base (re-generated each time)
* Final (initially empty, then custom) <- Editing only this !

NOTE : In the above paths, `<Entity>` is your Entity Name, without namespace. (may cause problems!)

TODO :)
But there'll be 3 files, on the same model than JS ones : Abstract, Base, and Final.



Therefore, the first time you execute the command-line for a given Bundle/Entity pair, 4 files will be generated.
After that, only the 2 Base files will be re-generated.


## WHY MOOTOOLS FIRST ?

Easy. ;)

## THANKS

- Fabien Potencier, for he taught me *a lot*
- Jonathan Wage, cause he's awesome too
- Aaron Newton, kudos to his super-cow-powers
- *You*, sneaky FOSS lover !

## FREE

**The Carcase**


The object that we saw, let us recall,

This summer morn when warmth and beauty mingle —

At the path's turn, a carcase lay asprawl

Upon a bed of shingle.



Legs raised, like some old whore far-gone in passion,

The burning, deadly, poison-sweating mass

Opened its paunch in careless, cynic fashion,

Ballooned with evil gas.



On this putrescence the sun blazed in gold, 

Cooking it to a turn with eager care — 

So to repay to Nature, hundredfold, 

What she had mingled there.



The sky, as on the opening of a flower, 

On this superb obscenity smiled bright. 

The stench drove at us, with such fearsome power 

You thought you'd swoon outright.



Flies trumpeted upon the rotten belly 

Whence larvae poured in legions far and wide, 

And flowed, like molten and liquescent jelly, 

Down living rags of hide.



The mass ran down, or, like a wave elated 

Rolled itself on, and crackled as if frying: 

You'd think that corpse, by vague breath animated, 

Drew life from multiplying.



Through that strange world a rustling rumour ran 

Like rushing water or a gust of air, 

Or grain that winnowers, with rhythmic fan, 

Sweep simmering here and there.



It seemed a dream after the forms grew fainter, 

Or like a sketch that slowly seems to dawn 

On a forgotten canvas, which the painter 

From memory has drawn.



Behind the rocks a restless cur that slunk 

Eyed us with fretful greed to recommence 

His feast, amidst the bonework, on the chunk 

That he had torn from thence.



Yet you'll resemble this infection too 

One day, and stink and sprawl in such a fashion, 

Star of my eyes, sun of my nature, you, 

My angel and my passion!



Yes, you must come to this, O queen of graces, 

At length, when the last sacraments are over, 

And you go down to moulder in dark places 

Beneath the grass and clover.



Then tell the vermin as it takes its pleasance 

And feasts with kisses on that face of yours, 

I've kept intact in form and godlike essence 

Our decomposed amours!


— Roy Campbell, Poems of Baudelaire (New York: Pantheon Books, 1952)


# ESSAY

Compare this scene to children watching the adults' world.

I want your copies by next monday.