<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://nutellove.free.fr>.
 *
 */

namespace Nutellove\JavascriptClassBundle\Tools\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
//use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\Common\Util\Inflector;
use Nutellove\JavascriptClassBundle\Tools\Generator\AbstractControllerEntityGenerator;

/**
 * Class used to generate Controllers
 * from ClassMetadataInfo instances
 *
 * @author  Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class ControllerEntityGenerator extends AbstractControllerEntityGenerator
{

//namespace Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle;
//
//use Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle\Base\BaseAntController;
//
//class AntController extends BaseAntController
//{
//
//  public function loadAction($id)
//  {
//    return parent::loadAction($id);
//  }
//
//  public function saveAction($id)
//  {
//    return parent::loadAction($id);
//  }
//
//
//}

  protected static $_classTemplate =
'<?php

/**
 * Auto-initialized by JavascriptClassBundle,
 * This is the Controller Class where you put your custom logic
 */

<namespace>

<use>

class <entityClassName>Controller <entityExtends>
{
<spaces>public function loadAction($id)
<spaces>{
<spaces><spaces>return parent::loadAction($id);
<spaces>}

<spaces>public function saveAction($id)
<spaces>{
<spaces><spaces>return parent::loadAction($id);
<spaces>}
}
';


////////////////////////////////////////////////////////////////////////////////

  /**
   * Generate a Javascript Mootools entity Class
   * from the given ClassMetadataInfo instance
   *
   * @param ClassMetadataInfo $metadata
   * @return string $code
   */
  public function generateEntityClass(ClassMetadataInfo $metadata)
  {
    $placeHolders = array(
      '<namespace>',
      '<use>',
      '<entityExtends>',
      '<entityClassName>',
    );

    $replacements = array(
      $this->_generateEntityNamespace($metadata),
      $this->_generateEntityUse($metadata),
      $this->_generateEntityExtends($metadata),
      $this->_generateEntityClassName($metadata),
    );

    $code = str_replace($placeHolders, $replacements, self::$_classTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////

  private function _generateEntityNamespace(ClassMetadataInfo $metadata)
  {
    return 'namespace Nutellove\\JavascriptClassBundle\\Controller\\Entity\\'.$this->_getBundleName($metadata).';';
  }

  private function _generateEntityUse(ClassMetadataInfo $metadata)
  {
    return 'use Nutellove\\JavascriptClassBundle\\Controller\\Entity\\'.$this->_getBundleName($metadata).'\\Base\\'.$this->_getClassName($metadata).'Controller;';
//    if ($this->_hasNamespace($metadata)) {
//      return 'use ' . $this->_getNamespace($metadata) .'\Base\Base'.$this->_getClassName($metadata).'Controller;';
//    }
  }

  protected function _generateEntityClassName(ClassMetadataInfo $metadata)
  {
    return $this->_getClassName($metadata);
  }

  protected function _generateEntityExtends(ClassMetadataInfo $metadata)
  {
    $r = "";
    if ( $this->_extendsClass() ) {
      $r .= $this->_spaces . " extends Base" . $this->_getClassName($metadata) . "Controller";
    }
    return $r;
  }

////////////////////////////////////////////////////////////////////////////////

}