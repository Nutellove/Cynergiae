<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Util\Inflector; // Inflector::camelize()

class AbstractEntityController extends Controller
{
  public $bundleName;
  public $entityName;

  public $entity;

// TODO : throw, as this should be overriden
//  public function indexAction()
//  {
//    // return $this->render('HelloBundle:Hello:index.html.php', array('name' => $name));
//  }



  public function loadAction($id)
  {
    $entity = $this->getEntity($id);
    if (!$entity) {
      throw new NotFoundHttpException('The entity does not exist.');
    }
    $parameters = $this->getParametersFromEntity ($entity);
    $json = json_encode($parameters);

    return $this->render('JavascriptClassBundle:Entity:load.html.php', array(
      'entity' => print_r($entity,1),
      'parameters_dump' => print_r ($parameters,1),
      'json' => $json,
    ));
  }


////////////////////////////////////////////////////////////////////////////////
//// TOOLS /////////////////////////////////////////////////////////////////////


  public function getEntity($id)
  {
    $bundleName = $this->getBundleName();
    $entityName = $this->getEntityName();

    // Get Entity from Doctrine
    $em = $this->get('doctrine.orm.entity_manager');
    $qe = $em->createQueryBuilder()
      ->select ('e')
      ->from   ($bundleName.':'.$entityName, 'e')
      ->add('where', 'e.id = :id');


    $entity = $qe->setParameter('id', $id)->getQuery()->getResult();

    return $entity;
  }

  public function setParametersInEntity(array $parameters, $entity)
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

  public function getParametersFromEntity($entity)
  {
    $parameters = array();
    $map = $this->getJavascriptMapping();
    foreach ($map as $field => $params){
      if ($params['role'] == 'parameter' && $params['read']){
        if (!isset($parameters[$field])){
          $getterName = 'get'.ucfirst(Inflector::camelize($field));
          $parameters[$field] = call_user_func (array($entity,$getterName));
        }
      }
    }
  }

  protected function getRenderer()
  {
    return $this->container->getParameter('jsclass.template.renderer');
  }

}
