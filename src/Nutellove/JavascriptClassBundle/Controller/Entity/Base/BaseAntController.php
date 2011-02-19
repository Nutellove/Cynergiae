<?php

namespace Nutellove\JavascriptClassBundle\Controller\Entity\Base;

use Nutellove\JavascriptClassBundle\Controller\AbstractEntityController;

class BaseAntController extends AbstractEntityController
{
    public function indexAction($bundle, $entity)
    {
        // render a PHP template instead
        // return $this->render('HelloBundle:Hello:index.html.php', array('name' => $name));
    }
}
