<?php

namespace Nutellove\JavascriptClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Inflector; // Inflector::camelize()

class AbstractEntityController extends Controller
{
  public $bundleName;
  public $entityName;

  public $entity;

  public function indexAction()
  {
    throw new NotFoundHttpException('The indexAction should be overriden.');
  }



  public function loadAction($id)
  {
    $entity = $this->getEntity($id);
    if (!$entity) {
      throw new NotFoundHttpException('The entity does not exist.');
    }

    $parameters = $this->getParametersFromEntity ($entity);
    $json = array (
      'id'         => $id,
      'parameters' => $parameters,
    );

    return new Response(json_encode($json));
//    return $this->render('JavascriptClassBundle:Entity:load.json.php', array(
//      'json' => json_encode($json),
//      'entity' => $entity,
//      'parameters' => $parameters,
//    ));
  }

  public function saveAction($id)
  {
    $entity = $this->getEntity($id);
    if (!$entity) {
      throw new NotFoundHttpException('The entity does not exist.');
    }

    $request  = $this->get('request');
    $json = $request->request->get('json');
    $json = json_decode($json);

    if ( !isset($json['parameters']) || empty($json['parameters'])) {
      throw new NotFoundHttpException('JSON Parameters are missing.');
    }

    $this->setParametersInEntity($parameters, $entity);

    $parameters = $this->getParametersFromEntity ($entity);
    $json = array (
      'id'         => $id,
      'parameters' => $parameters,
    );

    return new Response(json_encode($json));
//    return $this->render('JavascriptClassBundle:Entity:load.json.php', array(
//      'json' => json_encode($json),
//      'entity' => $entity,
//      'parameters' => $parameters,
//    ));
  }


////////////////////////////////////////////////////////////////////////////////
//// TOOLS /////////////////////////////////////////////////////////////////////

  /**
   * Gets the PHP Entity the javascript asks for manipulation
   * @param $id
   */
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

    $entity = $qe->setParameter('id', $id)->getQuery()->getSingleResult();
    //$entity = $em->find("Bug", (int)$id);

    return $entity;
  }

  /**
   * Sets the array $parameters in the $entity given, while making sure the
   * mapping for javascript writing is respected. Unmapped parameters are ignored.
   *
   * @param $parameters
   * @param $entity
   */
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
    // Save TODO
    $em = $this->get('doctrine.orm.entity_manager');
    $em->flush();
  }

  /**
   * Gets the array of parameters (fieldName=>value) mapped for javascript reading
   * @param $entity
   */
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

    return $parameters;
  }

  // FIXME
  protected function getRenderer()
  {
    return $this->container->getParameter('jsclass.template.renderer');
  }

}
