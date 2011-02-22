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
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\Common\Util\Inflector;
use Nutellove\JavascriptClassBundle\Tools\Generator\AbstractControllerEntityGenerator;

/**
 * Class used to generate Controllers
 * from ClassMetadataInfo instances
 *
 * @author  Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class BaseControllerEntityGenerator extends AbstractControllerEntityGenerator
{

////////////////////////////////////////////////////////////////////////////////
//namespace Nutellove\JavascriptClassBundle\Controller\Entity\JavascriptClassBundle\Base;
//
//use Nutellove\JavascriptClassBundle\Controller\AbstractEntityController;
//
//class BaseAntController extends AbstractEntityController
//{
//
//    public function getBundleName()
//    {
//      return 'JavascriptClassBundle';
//    }
//
//    public function getEntityName()
//    {
//      return 'Ant';
//    }
//
//    public function getJavascriptMapping()
//    {
//      static $map;
//      if (!$map){
//        $map = array (
//          'is_hungry' => array (
//            'read'  => true,
//            'write' => true,
//            'role'  => 'parameter',
//          ),
//        );
//      }
//      return $map;
//    }
//
//}
////////////////////////////////////////////////////////////////////////////////

  protected static $_classTemplate =
'
/**
 * Auto-generated by JavascriptClassBundle.
 * This is the Base Controller Class for <entityClassName>Controller
 * It holds the Javascript-related mapping and data identifying the PHP Entity used
 *
 * YOU SHOULD NOT EDIT THIS FILE
 */

<namespace>

<use>

class <entityClassName>Controller <entityExtends>
{
<methodGetBundleName>
<methodGetEntityName>
<methodGetJavascriptMapping>
}
';

  protected static $_getBundleNameTemplate =
'
<spaces>public function getBundleName()
<spaces>{
<spaces><spaces>return \'<bundleName>\';
<spaces>}
';

  protected static $_getEntityNameTemplate =
'
<spaces>public function getEntityName()
<spaces>{
<spaces><spaces>return \'<entityName>\';
<spaces>}
';

  protected static $_getJavascriptMappingTemplate =
'
  public function getJavascriptMapping()
  {
    static $map;
    if (!$map){
      $map = array (
<mappingArrays>
      );
    }
    return $map;
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
      '<methodGetBundleName>',
      '<methodGetEntityName>',
      '<methodGetJavascriptMapping>',
    );

    $replacements = array(
      $this->_generateEntityNamespace($metadata),
      $this->_generateEntityUse($metadata),
      $this->_generateEntityExtends($metadata),
      $this->_generateEntityClassName($metadata),
      $this->_generateMethodGetBundleName($metadata),
      $this->_generateMethodGetEntityName($metadata),
      $this->_generateMethodGetJavascriptMapping($metadata),
    );

    $code = str_replace($placeHolders, $replacements, self::$_classTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////

  private function _generateEntityNamespace(ClassMetadataInfo $metadata)
  {
    if ($this->_hasNamespace($metadata)) {
      return 'namespace ' . $this->_getNamespace($metadata) .';';
    }
  }

  private function _generateEntityUse(ClassMetadataInfo $metadata)
  {
    if ($this->_hasNamespace($metadata)) {
      return 'use ' . $this->_getNamespace($metadata) .'\Base\Base'.$this->_getClassName($metadata).'Controller;';
    }
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

  protected function generateMethodGetBundleName(ClassMetadataInfo $metadata)
  {
    $placeHolders = array(
      '<bundleName>',
    );

    $replacements = array(
      $this->_getBundleName($metadata),
    );

    $code = str_replace($placeHolders, $replacements, self::$_getBundleNameTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

////////////////////////////////////////////////////////////////////////////////

  protected function generateMethodGetEntityName(ClassMetadataInfo $metadata)
  {
    $placeHolders = array(
      '<entityName>',
    );

    $replacements = array(
      $this->_getClassName($metadata),
    );

    $code = str_replace($placeHolders, $replacements, self::$_getEntityNameTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

////////////////////////////////////////////////////////////////////////////////

  protected function generateMethodGetJavascriptMapping(ClassMetadataInfo $metadata)
  {
    $placeHolders = array(
      '<mappingArrays>',
    );

    $replacements = array(
      $this->_generateJavascriptMappingArrays($metadata),
    );

    $code = str_replace($placeHolders, $replacements, self::$_getJavascriptMappingTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

  // FIXME

  protected function generateJavascriptMappingArrays(ClassMetadataInfo $metadata)
  {

  }





















}