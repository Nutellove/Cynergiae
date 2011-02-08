<?php

namespace Nutellove\MootoolsClassBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MootoolsClassBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}
