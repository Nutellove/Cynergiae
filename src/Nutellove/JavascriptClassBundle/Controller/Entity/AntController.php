<?php

namespace Nutellove\JavascriptClassBundle\Controller\Entity;

use Nutellove\JavascriptClassBundle\Controller\Entity\Base\BaseAntController;

class AntController extends BaseAntController
{
    public function indexAction($bundle, $entity)
    {
        // render a PHP template instead
        return $this->render('JavascriptClassBundle:Entity:index.html.php', array('name' => $bundle));
    }
}
