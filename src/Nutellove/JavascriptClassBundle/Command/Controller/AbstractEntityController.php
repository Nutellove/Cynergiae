<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Util\Inflector; // Inflector::camelize()

class AbstractEntityController extends Controller
{
    public $bundleName;
    public $entityName;

    public $entity;

    public function indexAction($bundle, $entity)
    {
        // return $this->render('HelloBundle:Hello:index.html.php', array('name' => $name));
    }

    public function getEntity($bundleName, $entityName, $id)
    {
      // TODO
      // Get Entity from Doctrine
    }

    public function loadParametersInEntity(array $parameters, $entity)
    {
      $map = $this->getJavascriptMapping();
      foreach ($map as $field => $params){
        if ($params['role'] == 'parameter' && $params['write']){
          if (isset($parameters[$field])){
            $setterName = 'set'.ucfirst(Inflector::camelize($field));
            call_user_func_array (array($entity,$setterName),array($parameters[$field]));
          }
        }
      }
    }


}
