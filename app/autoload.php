<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'                        => __DIR__.'/../vendor_full/symfony/src',
    'Nutellove'                      => __DIR__.'/../src',
    'Doctrine\\Common\\DataFixtures' => __DIR__.'/../vendor_full/doctrine-data-fixtures/lib',
    'Doctrine\\Common'               => __DIR__.'/../vendor_full/doctrine-common/lib',
    'Doctrine\\DBAL\\Migrations'     => __DIR__.'/../vendor_full/doctrine-migrations/lib',
    'Doctrine\\MongoDB'              => __DIR__.'/../vendor_full/doctrine-mongodb/lib',
    'Doctrine\\ODM\\MongoDB'         => __DIR__.'/../vendor_full/doctrine-mongodb-odm/lib',
    'Doctrine\\DBAL'                 => __DIR__.'/../vendor_full/doctrine-dbal/lib',
    'Doctrine'                       => __DIR__.'/../vendor_full/doctrine/lib',
    'Zend'                           => __DIR__.'/../vendor_full/zend/library',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor_full/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor_full/twig/lib',
    'Swift_'           => __DIR__.'/../vendor_full/swiftmailer/lib/classes',
));
$loader->register();
