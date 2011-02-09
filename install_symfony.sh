#!/bin/sh

# @author Tonioth
# this script HAS to be executed from project's root folder
# ... and needs access to the internet, obviously

echo "----- Installing Symfony and Friends -----"

# create/empty tmp files dirs
cd app
if [ -d "cache" ]; then
  echo "Emptying app/cache directory"
  #rm -rf cache/*
else
  echo "Creating app/cache directory"
  mkdir cache
fi
if [ -d "logs" ]; then
  echo "Emptying app/logs directory"
  #rm -rf logs/*
else
  echo "Creating app/logs directory"
  mkdir logs
fi
chmod 777 cache
chmod 777 logs
cd ..

# empty vendor dir
if [ -d "vendor" ]; then
  echo "Emptying vendor directory"
  rm -rf vendor/*
else
  echo "Creating vendor directory"
  mkdir vendor
fi

# move to sf installation dir
cd vendor

# make sure git has line feed correctly configured
git config --global core.autocrlf input

# clone from git read-only repo
git clone https://github.com/symfony/symfony.git symfony

echo "---------- Symfony Cloned !"

# Doctrine ORM
git clone git://github.com/doctrine/doctrine2.git doctrine

# Doctrine Data Fixtures Extension
git clone git://github.com/doctrine/data-fixtures doctrine-data-fixtures

# Doctrine DBAL
git clone git://github.com/doctrine/dbal.git doctrine-dbal

# Doctrine Common
git clone git://github.com/doctrine/common.git doctrine-common

# Doctrine migrations
git clone git://github.com/doctrine/migrations.git doctrine-migrations

# Doctrine MongoDB
git clone git://github.com/doctrine/mongodb.git doctrine-mongodb

# Doctrine MongoDB
git clone git://github.com/doctrine/mongodb-odm.git doctrine-mongodb-odm

echo "---------- Doctrine Cloned !"

# Swiftmailer
git clone git://github.com/swiftmailer/swiftmailer.git swiftmailer

echo "---------- Swiftmailer Cloned !"

# Twig
git clone git://github.com/fabpot/Twig.git twig

echo "---------- Twig Cloned !"

# Zend Framework
git clone git://github.com/zendframework/zf2.git zend

echo "---------- Zend Cloned !"
echo "========== DONE !"
