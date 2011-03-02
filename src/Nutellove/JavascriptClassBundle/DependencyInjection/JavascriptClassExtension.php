<?php

/**
 * UNUSED
 */

namespace Nutellove\JavascriptClassBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;

class JavascriptClassExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) // maybe configLoad ?
    {
      $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
      $loader->load('config.xml');
//      print_r ($configs);
    }

  public function configLoad (array $configs, ContainerBuilder $container)
    {

    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/jsclass';
    }

    public function getAlias()
    {
        return 'javascript_class';
    }
}
 
