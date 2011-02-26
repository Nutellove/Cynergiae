<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TestController extends Controller
{
    public function indexAction()
    {
        return $this->render('JavascriptClassBundle:Test:index.html.php');

        // render a PHP template instead
        // return $this->render('HelloBundle:Hello:index.html.php', array('name' => $name));
    }
}
