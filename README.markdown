CYNERGIÆ SANDBOX
================

Hello !
Nothing much to be found here, this is mostly a learner's project.

This is a sandbox for Cynergiæ.

--------------------------------------------------------------------------------

INSTALL NOTES
=============

CD to your project directory and then clone the repo :

    $ git clone git@github.com:Nutellove/Cynergiae.git cynergiae

Execute `install_symfony.sh` to

* create/empty `app/logs` and `app/cache` and chmod them
* create/empty `vendor` directory
* clone sf2 in `vendor/symfony` from github
* clone usual sf2 vendors (doctrine, twig...)

You also need to configure these files :

* `app/config/config.yml` (you may rename `config.sample.yml` and work from it)

Later, you might execute `update_symfony.sh` to pull changes from vendors' repositories.

--------------------------------------------------------------------------------

Git CheatSheet
--------------

    $ git add -v .
    
    $ git commit -a -v -m 'Commit comment'
    
    $ git push origin master
    
    $ git pull origin master

