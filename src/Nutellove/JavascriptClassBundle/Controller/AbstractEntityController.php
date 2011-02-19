<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractEntityController extends Controller
{
    public function indexAction($bundle, $entity)
    {
        // return $this->render('HelloBundle:Hello:index.html.php', array('name' => $name));
    }
}
