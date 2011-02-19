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
 * Copied and then hacked from Doctrine's PHP EntityGenerator.
 * This ain't going to get me a Cleanest Code award soon... -_- But it WORKS !
 *
 */

namespace Nutellove\JavascriptClassBundle\Tools\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\Common\Util\Inflector;

/**
 * Generic class used to generate Mootools Entity Base classes
 * from ClassMetadataInfo instances
 *
 * @author  Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class MootoolsEntityGenerator
{

  /** The extension to use for written javascript files */
  private $_extension = '.class.js';

  /** The name of the attribute that holds mootools information **/
  private $_mootoolsAttributeName = 'mootools';

  /** Whether or not the current ClassMetadataInfo instance is new or old */
  private $_isNew = true;

  private $_staticReflection = array();

  /** Number of spaces to use for indentation in generated code */
  private $_numSpaces = 2;

  /** The actual spaces to use for indentation */
  private $_spaces = '  ';

  /** The class all generated entities should extend */
  private $_classToExtend;

  /**
   * @var string
   */
  private $_annotationsPrefix = '';

  private static $_classTemplate =
'
/**
 * Initialized by JavascriptClassBundle,
 * This is the Class in which you can write your custom JS business logic
 * @require Javascript Framework Mootools ≥ 1.3
 */

var <entityClassName> = new Class({
<entityExtends>
<entityImplements>

});
';

  private static $_getMethodTemplate =
'
/**
 * <description>
 *
 * @return <variableType> <variableName>
 */
<methodName>: function ()
{
<spaces>return this.getParameter("<fieldName>");
},
';

  private static $_setMethodTemplate =
'
/**
 * <description>
 *
 * @param <variableType> <variableName>
 */
<methodName>: function (<variableName>)
{
<spaces>this.setParameter("<fieldName>", <variableName>);
},
';

  private static $_addMethodTemplate =
'
/**
 * <description>
 *
 * @param <variableType> <variableName>
 */
<methodName>: function (<variableName>)
{
<spaces>if ( Array.isArray(this.<fieldName>) ) {
<spaces><spaces>this.<fieldName>.push (<variableName>);
<spaces>} else {
<spaces><spaces>this.<fieldName> = new Array (<variableName>);
<spaces>}
},
';

  private static $_constructorMethodTemplate =
'
initialize: function ()
{
<spaces>// Call parent constructor
<spaces>this.parent (<bundleName>, <entityName>);
<spaces>/* COLLECTIONS FROM ASSOCIATIONS
<spaces><collections>
<spaces>*/
},
';

//  private static $_lifecycleCallbackMethodTemplate =
//'
///**
// * @<name>
// */
//<methodName>: function ()
//{
//<spaces>// Add your code here
//},
//';



////////////////////////////////////////////////////////////////////////////////
//// PUBLIC OPTION MUTATORS ////////////////////////////////////////////////////

  /**
   * Set the number of spaces the exported class should base its indentation on
   *
   * @param integer $numSpaces
   * @return void
   */
  public function setNumSpaces($numSpaces)
  {
    $this->_spaces = str_repeat(' ', $numSpaces);
    $this->_numSpaces = $numSpaces;
  }


  /**
   * Set the name of the class the generated classes should extend from
   * TODO : use this !
   * @return void
   */
  public function setClassToExtend($classToExtend)
  {
    $this->_classToExtend = $classToExtend;
  }

  /**
   * Set an annotation prefix.
   *
   * @param string $prefix
   */
  public function setAnnotationPrefix($prefix)
  {
    $this->_annotationsPrefix = $prefix;
  }

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
//      '<namespace>',
      '<entityAnnotation>',
      '<entityExtends>',
      '<entityImplements>',
      '<entityClassName>',
      '<entityBody>'
    );

    $replacements = array(
//      $this->_generateEntityNamespace($metadata),
      $this->_generateEntityDocBlock($metadata),
      $this->_generateEntityExtends($metadata),
      $this->_generateEntityImplements($metadata),
      $this->_generateEntityClassName($metadata),
      $this->_generateEntityBody($metadata)
    );

    $code = str_replace($placeHolders, $replacements, self::$_classTemplate);
    return str_replace('<spaces>', $this->_spaces, $code);
  }

////////////////////////////////////////////////////////////////////////////////

  /**
   * Generate the setters and getters stubs
   *
   * @param  ClassMetadataInfo $metadata
   * @return string The JS Code
   */
  private function _generateEntityStubMethods(ClassMetadataInfo $metadata)
  {
    $methods = array();

    // Properties Accessors
    foreach ($metadata->fieldMappings as $fieldMapping) {
      // Setter, do we need it ?
      if ( $this->_canJavascriptWriteField ($fieldMapping) ) {
        if ( ! isset($fieldMapping['id']) || ! $fieldMapping['id'] || $metadata->generatorType == ClassMetadataInfo::GENERATOR_TYPE_NONE) {
          if ($code = $this->_generateEntityStubMethod($metadata, 'set', $fieldMapping['fieldName'], $fieldMapping['type'])) {
            $methods[] = $code;
          }
        }
      }
      // Getter, do we need it ?
      if ( $this->_canJavascriptReadField ($fieldMapping) ) {
        if ($code = $this->_generateEntityStubMethod($metadata, 'get', $fieldMapping['fieldName'], $fieldMapping['type'])) {
          $methods[] = $code;
        }
      }
    }

    // Associations
//    foreach ($metadata->associationMappings as $associationMapping) {
//      if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
//        if ($code = $this->_generateEntityStubMethod($metadata, 'set', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
//          $methods[] = $code;
//        }
//        if ($code = $this->_generateEntityStubMethod($metadata, 'get', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
//          $methods[] = $code;
//        }
//      } else if ($associationMapping['type'] & ClassMetadataInfo::TO_MANY) {
//        if ($code = $this->_generateEntityStubMethod($metadata, 'add', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
//          $methods[] = $code;
//        }
//        if ($code = $this->_generateEntityStubMethod($metadata, 'get', $associationMapping['fieldName'], 'Doctrine\Common\Collections\Collection')) {
//          $methods[] = $code;
//        }
//      }
//    }

    return implode("\n\n", $methods);
  }

////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////

  /**
   * Check if the field associated to the passed $fieldMapping is the ID
   * @param  array $fieldMapping
   * @return boolean
   */
  protected function _isIdField ($fieldMapping)
  {
    if ( ! isset($fieldMapping['id']) || ! $fieldMapping['id'] ) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Checks if Mootools Class has write access to the field associated to passed $fieldMapping
   * @param  array $fieldMapping
   * @return boolean
   */
  protected function _canJavascriptWriteField ($fieldMapping)
  {
    // Can never write ID
    if ( $this->_isIdField($fieldMapping) ) {
      return false;
    }
    if ( isset($fieldMapping[$this->_mootoolsAttributeName]) ) {
      switch ($fieldMapping[$this->_mootoolsAttributeName]) {
        case 'read write':
        case 'readwrite':
        case 'write':
        case 'rw':
        case 'w':
          return true;
        default:
          return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Checks if Mootools Class has read access to the field associated to passed fieldMapping
   * @param  array $fieldMapping
   * @return boolean
   */
  protected function _canJavascriptReadField ($fieldMapping)
  {
    // Can always read ID
    if ( $this->_isIdField($fieldMapping) ) {
      return true;
    }
    if ( isset($fieldMapping[$this->_mootoolsAttributeName]) ) {
      switch ($fieldMapping[$this->_mootoolsAttributeName]) {
        case 'read write':
        case 'readwrite':
        case 'read':
        case 'rw':
        case 'r':
          return true;
        default:
          return false;
      }
    } else {
      return false;
    }
  }

////////////////////////////////////////////////////////////////////////////////

  private function _extendsClass()
  {
    return $this->_classToExtend ? true : false;
  }

  private function _getClassToExtend()
  {
    return $this->_classToExtend;
  }

////////////////////////////////////////////////////////////////////////////////

  private function _generateEntityClassName(ClassMetadataInfo $metadata)
  {
    return $this->_getClassName($metadata);
  }

  private function _generateEntityExtends(ClassMetadataInfo $metadata)
  {
    $r = "\n";
    if ( $this->_extendsClass() ) {
      $r .= $this->_spaces . "Extends: [" . $this->_getClassToExtend() . "],";
    }
    return $r;
  }

  private function _generateEntityImplements(ClassMetadataInfo $metadata)
  {
    return '';
  }


////////////////////////////////////////////////////////////////////////////////
//// ENTITY BODY ///////////////////////////////////////////////////////////////

  /**
   * Generate the code for the Entity Body
   *
   * @param  ClassMetaDataInfo $metadata
   * @return string
   */
  private function _generateEntityBody(ClassMetadataInfo $metadata)
  {
    $fieldMappingProperties = $this->_generateEntityFieldMappingProperties($metadata);
    //$associationMappingProperties = $this->_generateEntityAssociationMappingProperties($metadata);
    //$lifecycleCallbackMethods = $this->_generateEntityLifecycleCallbackMethods($metadata);

    $code = array();

    if ($fieldMappingProperties) {
      $code[] = $fieldMappingProperties;
    }

    //if ($associationMappingProperties) {
    //  $code[] = $associationMappingProperties;
    //}

    $code[] = $this->_generateEntityConstructor($metadata);

    $code[] = $this->_generateEntityStubMethods($metadata);

    //if ($lifecycleCallbackMethods) {
    //  $code[] = $lifecycleCallbackMethods;
    //}

    return implode("\n", $code);
  }

////////////////////////////////////////////////////////////////////////////////

  private function _generateEntityConstructor(ClassMetadataInfo $metadata)
  {
    if ($this->_hasMethod('__construct', $metadata)) {
      return '';
    }

    $search = array (
      '<collections>',
      '<entityName>',
      '<bundleName>',
    );


//    $collections = array();
//    foreach ($metadata->associationMappings AS $mapping) {
//      if ($mapping['type'] & ClassMetadataInfo::TO_MANY) {
//        $collections[] = 'this.'.$mapping['fieldName'].' = new Array();';
//      }
//    }

//    if ($collections) {
//      return $this->_prefixCodeWithSpaces(str_replace("<collections>", implode("\n", $collections), self::$_constructorMethodTemplate));
//    }
    return '';
  }

  private function _hasProperty($property, ClassMetadataInfo $metadata)
  {
    return (
      isset($this->_staticReflection[$metadata->name]) &&
      in_array($property, $this->_staticReflection[$metadata->name]['properties'])
    );
  }

  private function _hasMethod($method, ClassMetadataInfo $metadata)
  {
    return (
      isset($this->_staticReflection[$metadata->name]) &&
      in_array($method, $this->_staticReflection[$metadata->name]['methods'])
    );
  }

  private function _hasNamespace(ClassMetadataInfo $metadata)
  {
    return strpos($metadata->name, '\\') ? true : false;
  }




  private function _getClassName(ClassMetadataInfo $metadata)
  {
    return ($pos = strrpos($metadata->name, '\\'))
      ? substr($metadata->name, $pos + 1, strlen($metadata->name)) : $metadata->name;
  }

  private function _getNamespace(ClassMetadataInfo $metadata)
  {
    return substr($metadata->name, 0, strrpos($metadata->name, '\\'));
  }


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  /**
   * Generates the code for the definition of the class properties
   *
   * @param  ClassMetadataInfo $metadata
   * @return string
   */
  private function _generateEntityFieldMappingProperties(ClassMetadataInfo $metadata)
  {
    $lines = array();

    foreach ($metadata->fieldMappings as $fieldMapping) {
      if ($this->_hasProperty($fieldMapping['fieldName'], $metadata)) {
        continue;
      }

      $lines[] = $this->_generateFieldMappingPropertyDocBlock($fieldMapping, $metadata);
      $lines[] = $this->_spaces . '' . $fieldMapping['fieldName']
           . (isset($fieldMapping['default']) ? ': ' . var_export($fieldMapping['default'], true) : null) . ",\n";
    }

    return implode("\n", $lines);
  }

  private function _generateEntityStubMethod(ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = null)
  {
    $methodName = $type . Inflector::classify($fieldName);

    if ($this->_hasMethod($methodName, $metadata)) {
      return;
    }

    $var = sprintf('_%sMethodTemplate', $type);
    $template = self::$$var;

    $variableType = $typeHint ? $typeHint . ' ' : null;

    $types = \Doctrine\DBAL\Types\Type::getTypesMap();
    $methodTypeHint = $typeHint && ! isset($types[$typeHint]) ? '\\' . $typeHint . ' ' : null;

    $replacements = array(
      '<description>'     => ucfirst($type) . ' ' . $fieldName,
      '<methodTypeHint>'  => $methodTypeHint,
      '<variableType>'    => $variableType,
      '<variableName>'    => Inflector::camelize($fieldName),
      '<methodName>'    => $methodName,
      '<fieldName>'     => $fieldName,
    );

    $method = str_replace(
      array_keys($replacements),
      array_values($replacements),
      $template
    );

    return $this->_prefixCodeWithSpaces($method);
  }

////////////////////////////////////////////////////////////////////////////////
//// DOCBLOCKS /////////////////////////////////////////////////////////////////

  private function _generateAssociationMappingPropertyDocBlock(array $associationMapping, ClassMetadataInfo $metadata)
  {
    $lines = array();
    $lines[] = $this->_spaces . '/**';
    $lines[] = $this->_spaces . ' * ASSOCIATION';
    $lines[] = $this->_spaces . ' * @var ' . $associationMapping['targetEntity'];

//    if ($this->_generateAnnotations) {
//      $lines[] = $this->_spaces . ' *';
//
//      $type = null;
//      switch ($associationMapping['type']) {
//        case ClassMetadataInfo::ONE_TO_ONE:
//          $type = 'OneToOne';
//          break;
//        case ClassMetadataInfo::MANY_TO_ONE:
//          $type = 'ManyToOne';
//          break;
//        case ClassMetadataInfo::ONE_TO_MANY:
//          $type = 'OneToMany';
//          break;
//        case ClassMetadataInfo::MANY_TO_MANY:
//          $type = 'ManyToMany';
//          break;
//      }
//      $typeOptions = array();
//
//      if (isset($associationMapping['targetEntity'])) {
//        $typeOptions[] = 'targetEntity="' . $associationMapping['targetEntity'] . '"';
//      }
//
//      if (isset($associationMapping['inversedBy'])) {
//        $typeOptions[] = 'inversedBy="' . $associationMapping['inversedBy'] . '"';
//      }
//
//      if (isset($associationMapping['mappedBy'])) {
//        $typeOptions[] = 'mappedBy="' . $associationMapping['mappedBy'] . '"';
//      }
//
//      if ($associationMapping['cascade']) {
//        $cascades = array();
//
//        if ($associationMapping['isCascadePersist']) $cascades[] = '"persist"';
//        if ($associationMapping['isCascadeRemove']) $cascades[] = '"remove"';
//        if ($associationMapping['isCascadeDetach']) $cascades[] = '"detach"';
//        if ($associationMapping['isCascadeMerge']) $cascades[] = '"merge"';
//        if ($associationMapping['isCascadeRefresh']) $cascades[] = '"refresh"';
//
//        $typeOptions[] = 'cascade={' . implode(',', $cascades) . '}';
//      }
//
//      if (isset($associationMapping['orphanRemoval']) && $associationMapping['orphanRemoval']) {
//        $typeOptions[] = 'orphanRemoval=' . ($associationMapping['orphanRemoval'] ? 'true' : 'false');
//      }
//
//      $lines[] = $this->_spaces . ' * @' . $this->_annotationsPrefix . '' . $type . '(' . implode(', ', $typeOptions) . ')';
//
//      if (isset($associationMapping['joinColumns']) && $associationMapping['joinColumns']) {
//        $lines[] = $this->_spaces . ' * @' . $this->_annotationsPrefix . 'JoinColumns({';
//
//        $joinColumnsLines = array();
//
//        foreach ($associationMapping['joinColumns'] as $joinColumn) {
//          if ($joinColumnAnnot = $this->_generateJoinColumnAnnotation($joinColumn)) {
//            $joinColumnsLines[] = $this->_spaces . ' *   ' . $joinColumnAnnot;
//          }
//        }
//
//        $lines[] = implode(",\n", $joinColumnsLines);
//        $lines[] = $this->_spaces . ' * })';
//      }
//
//      if (isset($associationMapping['joinTable']) && $associationMapping['joinTable']) {
//        $joinTable = array();
//        $joinTable[] = 'name="' . $associationMapping['joinTable']['name'] . '"';
//
//        if (isset($associationMapping['joinTable']['schema'])) {
//          $joinTable[] = 'schema="' . $associationMapping['joinTable']['schema'] . '"';
//        }
//
//        $lines[] = $this->_spaces . ' * @' . $this->_annotationsPrefix . 'JoinTable(' . implode(', ', $joinTable) . ',';
//        $lines[] = $this->_spaces . ' *   joinColumns={';
//
//        foreach ($associationMapping['joinTable']['joinColumns'] as $joinColumn) {
//          $lines[] = $this->_spaces . ' *   ' . $this->_generateJoinColumnAnnotation($joinColumn);
//        }
//
//        $lines[] = $this->_spaces . ' *   },';
//        $lines[] = $this->_spaces . ' *   inverseJoinColumns={';
//
//        foreach ($associationMapping['joinTable']['inverseJoinColumns'] as $joinColumn) {
//          $lines[] = $this->_spaces . ' *   ' . $this->_generateJoinColumnAnnotation($joinColumn);
//        }
//
//        $lines[] = $this->_spaces . ' *   }';
//        $lines[] = $this->_spaces . ' * )';
//      }
//
//      if (isset($associationMapping['orderBy'])) {
//        $lines[] = $this->_spaces . ' * @' . $this->_annotationsPrefix . 'OrderBy({';
//
//        foreach ($associationMapping['orderBy'] as $name => $direction) {
//          $lines[] = $this->_spaces . ' *   "' . $name . '"="' . $direction . '",';
//        }
//
//        $lines[count($lines) - 1] = substr($lines[count($lines) - 1], 0, strlen($lines[count($lines) - 1]) - 1);
//        $lines[] = $this->_spaces . ' * })';
//      }
//    }

    $lines[] = $this->_spaces . ' */';

    return implode("\n", $lines);
  }

  private function _generateEntityDocBlock(ClassMetadataInfo $metadata)
  {
    $lines = array();
    $lines[] = '/**';
    $lines[] = ' * '.$metadata->name;

//    if ($this->_generateAnnotations) {
//      $lines[] = ' *';
//
//      $methods = array(
//        '_generateTableAnnotation',
//        '_generateInheritanceAnnotation',
//        '_generateDiscriminatorColumnAnnotation',
//        '_generateDiscriminatorMapAnnotation'
//      );
//
//      foreach ($methods as $method) {
//        if ($code = $this->$method($metadata)) {
//          $lines[] = ' * ' . $code;
//        }
//      }
//
//      if ($metadata->isMappedSuperclass) {
//        $lines[] = ' * @' . $this->_annotationsPrefix . 'MappedSupperClass';
//      } else {
//        $lines[] = ' * @' . $this->_annotationsPrefix . 'Entity';
//      }
//
//      if ($metadata->customRepositoryClassName) {
//        $lines[count($lines) - 1] .= '(repositoryClass="' . $metadata->customRepositoryClassName . '")';
//      }
//
//      if (isset($metadata->lifecycleCallbacks) && $metadata->lifecycleCallbacks) {
//        $lines[] = ' * @' . $this->_annotationsPrefix . 'HasLifecycleCallbacks';
//      }
//    }

    $lines[] = ' */';

    return implode("\n", $lines);
  }

  private function _generateFieldMappingPropertyDocBlock(array $fieldMapping, ClassMetadataInfo $metadata)
  {
    $lines = array();
    $lines[] = $this->_spaces . '/**';
    $lines[] = $this->_spaces . ' * @var ' . $fieldMapping['type'] . ' ' . $fieldMapping['fieldName'];
    $lines[] = $this->_spaces . ' */';

    return implode("\n", $lines);
  }

  private function _prefixCodeWithSpaces($code, $num = 1)
  {
    $lines = explode("\n", $code);

    foreach ($lines as $key => $value) {
      $lines[$key] = str_repeat($this->_spaces, $num) . $lines[$key];
    }

    return implode("\n", $lines);
  }
////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////
//// WEIRD STUFF ///////////////////////////////////////////////////////////////

  private function _getInheritanceTypeString($type)
  {
    switch ($type) {
      case ClassMetadataInfo::INHERITANCE_TYPE_NONE:
        return 'NONE';

      case ClassMetadataInfo::INHERITANCE_TYPE_JOINED:
        return 'JOINED';

      case ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE:
        return 'SINGLE_TABLE';

      case ClassMetadataInfo::INHERITANCE_TYPE_TABLE_PER_CLASS:
        return 'PER_CLASS';

      default:
        throw new \InvalidArgumentException('Invalid provided InheritanceType: ' . $type);
    }
  }

  private function _getChangeTrackingPolicyString($policy)
  {
    switch ($policy) {
      case ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT:
        return 'DEFERRED_IMPLICIT';

      case ClassMetadataInfo::CHANGETRACKING_DEFERRED_EXPLICIT:
        return 'DEFERRED_EXPLICIT';

      case ClassMetadataInfo::CHANGETRACKING_NOTIFY:
        return 'NOTIFY';

      default:
        throw new \InvalidArgumentException('Invalid provided ChangeTrackingPolicy: ' . $policy);
    }
  }

  private function _getIdGeneratorTypeString($type)
  {
    switch ($type) {
      case ClassMetadataInfo::GENERATOR_TYPE_AUTO:
        return 'AUTO';

      case ClassMetadataInfo::GENERATOR_TYPE_SEQUENCE:
        return 'SEQUENCE';

      case ClassMetadataInfo::GENERATOR_TYPE_TABLE:
        return 'TABLE';

      case ClassMetadataInfo::GENERATOR_TYPE_IDENTITY:
        return 'IDENTITY';

      case ClassMetadataInfo::GENERATOR_TYPE_NONE:
        return 'NONE';

      default:
        throw new \InvalidArgumentException('Invalid provided IdGeneratorType: ' . $type);
    }
  }
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//// USELESS ///////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////





}

