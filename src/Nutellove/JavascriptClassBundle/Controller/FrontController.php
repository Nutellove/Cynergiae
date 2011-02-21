<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontController extends Controller
{

  public function loadAction($bundleName, $entityName, $id)
  {
    $controllerClass = __NAMESPACE__."\\Entity\\$bundleName\\$entityName"."Controller";
    if ( ! class_exists($controllerClass) ) {
      throw new NotFoundHttpException('The targeted Controller does not exist.');
    }

    $controllerABC = "JavascriptClassBundle:Entity\\$bundleName\\$entityName:load";
    $response = $this->forward ($controllerABC, array('id' => $id));

    return $response;
  }


  public function saveAction($bundleName, $entityName, $id)
  {
    $controllerClass = __NAMESPACE__."\\Entity\\$bundleName\\$entityName"."Controller";
    if ( ! class_exists($controllerClass) ) {
      throw new NotFoundHttpException('The targeted Controller does not exist.');
    }

    $controllerABC = "JavascriptClassBundle:Entity\\$bundleName\\$entityName:save";
    $response = $this->forward ($controllerABC, array('id' => $id));

    return $response;
  }
}
