<?php

 // FIXME : INITIALIZE THIS FILE !

namespace Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle;

use Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle\Base\BaseAntController;

class AntController extends BaseAntController
{

  public function indexAction()
  {
    $entity = $this->getEntity(1);
    return $this->render('JavascriptClassBundle:Entity:index.html.php', array(
      'entity' => print_r($entity,1)
    ));
  }



}
