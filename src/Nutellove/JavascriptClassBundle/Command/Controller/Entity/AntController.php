<?php

namespace Nutellove\JavascriptClassBundle\Controller\Entity;

use Nutellove\JavascriptClassBundle\Controller\Entity\Base\BaseAntController;

class AntController extends BaseAntController
{
    public function indexAction($bundle, $entity)
    {

        return $this->render('JavascriptClassBundle:Entity:index.html.php', array('name' => $bundle));
    }

    public function getJavascriptMapping()
    {
      static $map;
      if (!$map){
        $map = array (
          'is_hungry' => array (
            'read'  => true,
            'write' => true,
            'role'  => 'parameter',
          ),
        );
      }
      return $map;
    }
}
