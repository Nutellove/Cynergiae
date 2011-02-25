<?php

 // NOT USED, FOR SNIPPET PURPOSE ONLY (Removed the Base in filename)

namespace Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle\Base;

use Nutellove\JavascriptClassBundle\Controller\AbstractEntityController;

class BaseAntController extends AbstractEntityController
{

    public function getBundleName()
    {
      return 'JavascriptClassBundle';
    }

    public function getEntityName()
    {
      return 'Ant';
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
