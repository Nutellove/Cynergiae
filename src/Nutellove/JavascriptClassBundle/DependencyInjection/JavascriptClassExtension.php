<?php

/**
 * UNUSED
 */

namespace Nutellove\JavascriptClassBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;

class JavascriptClassExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) // maybe configLoad ?
    {
        // ...
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
        return 'nutellove_jsclass';
    }
}
 
