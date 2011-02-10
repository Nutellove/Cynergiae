#!/bin/sh

# @author Tonioth
# this script HAS to be executed from project's root folder
# ... and needs access to the internet, obviously

echo "----- Updating Symfony and Friends -----"

CURRENT=`pwd`/vendor

# Symfony
cd $CURRENT/symfony && git pull

echo "---------- Symfony Pulled !"

# Doctrine ORM
cd $CURRENT/doctrine && git pull

# Doctrine Data Fixtures Extension
cd $CURRENT/doctrine-data-fixtures && git pull

# Doctrine DBAL
cd $CURRENT/doctrine-dbal && git pull

# Doctrine common
cd $CURRENT/doctrine-common && git pull

# Doctrine migrations
cd $CURRENT/doctrine-migrations && git pull

# Doctrine MongoDB
cd $CURRENT/doctrine-mongodb && git pull

# Doctrine MongoDB ODM
cd $CURRENT/doctrine-mongodb-odm && git pull

echo "---------- Doctrine Pulled !"

# Swiftmailer
cd $CURRENT/swiftmailer && git pull

echo "---------- Swiftmailer Pulled !"

# Twig
cd $CURRENT/twig && git pull

echo "---------- Twig Pulled !"

# Zend Framework
cd $CURRENT/zend && git pull

echo "---------- Zend Pulled !"

echo "========== DONE !"

