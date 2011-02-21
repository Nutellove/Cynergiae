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
 * Abstract class to extend for making ControllerEntityGenerators
 * that use ClassMetadataInfo instances
 *
 * @author  Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class AbstractControllerEntityGenerator
{

  /** The extension to use for written controller files */
  protected $_extension = '.php';

  /** The name of the attribute that holds the javascript mapping information **/
  protected $_javascriptAttributeName = 'javascript';

  /** Whether or not the current ClassMetadataInfo instance is new or old */
  protected $_isNew = true;


  /** Number of spaces to use for indentation in generated code */
  protected $_numSpaces = 2;

  /** The actual spaces to use for indentation */
  protected $_spaces = '  ';

  /** The class all generated entities should extend */
  protected $_classToExtend;

  protected $_staticReflection = array();

  /**
   * @var string
   */
  protected $_annotationsPrefix = '';

  protected static $_classTemplate = 'Define a Class Template Property _classTemplate.';

  protected static $_getMethodTemplate = 'Define a getMethod Template Property _getMethodTemplate.';

  protected static $_setMethodTemplate = 'Define a setMethod Template Property _setMethodTemplate.';

  protected static $_addMethodTemplate = 'Define an addMethod Template Property _addMethodTemplate.';

  protected static $_constructorMethodTemplate = 'Define a constructorMethod Template Property _constructorMethodTemplate.';


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
    if ( isset($fieldMapping[$this->_javascriptAttributeName]) ) {
      switch ($fieldMapping[$this->_javascriptAttributeName]) {
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
    if ( isset($fieldMapping[$this->_javascriptAttributeName]) ) {
      switch ($fieldMapping[$this->_javascriptAttributeName]) {
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

  protected function _extendsClass()
  {
    return $this->_classToExtend ? true : false;
  }

  protected function _getClassToExtend()
  {
    return $this->_classToExtend;
  }

  protected function _hasProperty($property, ClassMetadataInfo $metadata)
  {
    return (
      isset($this->_staticReflection[$metadata->name]) &&
      in_array($property, $this->_staticReflection[$metadata->name]['properties'])
    );
  }

  protected function _hasMethod($method, ClassMetadataInfo $metadata)
  {
    return (
      isset($this->_staticReflection[$metadata->name]) &&
      in_array($method, $this->_staticReflection[$metadata->name]['methods'])
    );
  }

  protected function _hasNamespace(ClassMetadataInfo $metadata)
  {
    return strpos($metadata->name, '\\') ? true : false;
  }

  protected function _getClassName(ClassMetadataInfo $metadata)
  {
    return ($pos = strrpos($metadata->name, '\\'))
      ? substr($metadata->name, $pos + 1, strlen($metadata->name)) : $metadata->name;
  }

  protected function _getNamespace(ClassMetadataInfo $metadata)
  {
    return substr($metadata->name, 0, strrpos($metadata->name, '\\'));
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
//// WEIRD GURU STUFF //////////////////////////////////////////////////////////

  protected function _getInheritanceTypeString($type)
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

  protected function _getChangeTrackingPolicyString($policy)
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

  protected function _getIdGeneratorTypeString($type)
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


////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// GENERATORS ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


  /**
   * Generate a JavascriptClassBundle Controller for the Entity
   * from the given ClassMetadataInfo instance
   *
   * @param ClassMetadataInfo $metadata
   * @return string $code
   */
  public function generateEntityClass(ClassMetadataInfo $metadata)              // FIXME
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
  protected function _generateEntityStubMethods(ClassMetadataInfo $metadata)
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


  protected function _generateEntityClassName(ClassMetadataInfo $metadata)
  {
    return $this->_getClassName($metadata);
  }

  protected function _generateEntityExtends(ClassMetadataInfo $metadata)
  {
    $r = "\n";
    if ( $this->_extendsClass() ) {
      $r .= $this->_spaces . "Extends: [" . $this->_getClassToExtend() . "],";
    }
    return $r;
  }

  protected function _generateEntityImplements(ClassMetadataInfo $metadata)
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
  protected function _generateEntityBody(ClassMetadataInfo $metadata)
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
// CONSTRUCTOR /////////////////////////////////////////////////////////////////

  protected function _generateEntityConstructor(ClassMetadataInfo $metadata)
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




////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  /**
   * Generates the code for the definition of the class properties
   *
   * @param  ClassMetadataInfo $metadata
   * @return string
   */
  protected function _generateEntityFieldMappingProperties(ClassMetadataInfo $metadata)
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

  protected function _generateEntityStubMethod(ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = null)
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
//// ASSOCIATION DOCBLOCKS /////////////////////////////////////////////////////

  protected function _generateAssociationMappingPropertyDocBlock(array $associationMapping, ClassMetadataInfo $metadata)
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

////////////////////////////////////////////////////////////////////////////////
//// ENTITY DOCBLOCKS //////////////////////////////////////////////////////////

  protected function _generateEntityDocBlock(ClassMetadataInfo $metadata)
  {
    $lines = array();
    $lines[] = '/**';
    $lines[] = ' * '.$metadata->name;

//    if ($this->_generateAnnotations) {
//      $lines[] = ' *';
//
//      $methods = array(
//        '_generateTableAnnotation',
//				'_generateInheritanceAnnotation',
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

  protected function _generateFieldMappingPropertyDocBlock(array $fieldMapping, ClassMetadataInfo $metadata)
  {
    $lines = array();
    $lines[] = $this->_spaces . '/**';
    $lines[] = $this->_spaces . ' * @var ' . $fieldMapping['type'] . ' ' . $fieldMapping['fieldName'];
    $lines[] = $this->_spaces . ' */';

    return implode("\n", $lines);
  }

  protected function _prefixCodeWithSpaces($code, $num = 1)
  {
    $lines = explode("\n", $code);

    foreach ($lines as $key => $value) {
      $lines[$key] = str_repeat($this->_spaces, $num) . $lines[$key];
    }

    return implode("\n", $lines);
  }
////////////////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//// USELESS ///////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

//  /**
//   * Generated and write entity class to disk for the given ClassMetadataInfo instance
//   *
//   * @param ClassMetadataInfo $metadata
//   * @param string $outputDirectory
//   * @return void
//   */
//  public function writeEntityClass(ClassMetadataInfo $metadata, $outputDirectory)
//  {
//    $path = $outputDirectory . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $metadata->name) . $this->_extension;
//    $dir = dirname($path);
//
//    if ( ! is_dir($dir)) {
//      mkdir($dir, 0777, true);
//    }
//
//    $this->_isNew = !file_exists($path) || (file_exists($path) && $this->_regenerateEntityIfExists);
//
//    if ( ! $this->_isNew) {
//      $this->_parseTokensInEntityFile($path);
//    }
//
//    if ($this->_backupExisting && file_exists($path)) {
//      $backupPath = dirname($path) . DIRECTORY_SEPARATOR .  "~" . basename($path);
//      if (!copy($path, $backupPath)) {
//        throw new \RuntimeException("Attempt to backup overwritten entitiy file but copy operation failed.");
//      }
//    }
//
//    // If entity doesn't exist or we're re-generating the entities entirely
//    if ($this->_isNew) {
//      file_put_contents($path, $this->generateEntityClass($metadata));
//    // If entity exists and we're allowed to update the entity class
//    } else if ( ! $this->_isNew && $this->_updateEntityIfExists) {
//      file_put_contents($path, $this->generateUpdatedEntityClass($metadata, $path));
//    }
//  }


//  /**
//   * Generate and write entity classes for the given array of ClassMetadataInfo instances
//   *
//   * @param array $metadatas
//   * @param string $outputDirectory
//   * @return void
//   */
//  public function generate(array $metadatas, $outputDirectory)
//  {
//    foreach ($metadatas as $metadata) {
//      $this->writeEntityClass($metadata, $outputDirectory);
//    }
//  }




//  /**
//   * Generate the updated code for the given ClassMetadataInfo and entity at path
//   *
//   * @param ClassMetadataInfo $metadata
//   * @param string $path
//   * @return string $code;
//   */
//  public function generateUpdatedEntityClass(ClassMetadataInfo $metadata, $path)
//  {
//    $currentCode = file_get_contents($path);
//
//    $body = $this->_generateEntityBody($metadata);
//    $body = str_replace('<spaces>', $this->_spaces, $body);
//    $last = strrpos($currentCode, '}');
//
//    return substr($currentCode, 0, $last) . $body . (strlen($body) > 0 ? "\n" : ''). "}";
//  }



//  /**
//   * Set the extension to use when writing php files to disk
//   *
//   * @param string $extension
//   * @return void
//   */
//  public function setExtension($extension)
//  {
//    $this->_extension = $extension;
//  }


//  /**
//   * Set whether or not to generate annotations for the entity
//   *
//   * @param bool $bool
//   * @return void
//   */
//  public function setGenerateAnnotations($bool)
//  {
//    $this->_generateAnnotations = $bool;
//  }



//  /**
//   * Set whether or not to try and update the entity if it already exists
//   *
//   * @param bool $bool
//   * @return void
//   */
//  public function setUpdateEntityIfExists($bool)
//  {
//    $this->_updateEntityIfExists = $bool;
//  }

//  /**
//   * Set whether or not to regenerate the entity if it exists
//   *
//   * @param bool $bool
//   * @return void
//   */
//  public function setRegenerateEntityIfExists($bool)
//  {
//    $this->_regenerateEntityIfExists = $bool;
//  }

//  /**
//   * Set whether or not to generate stub methods for the entity
//   *
//   * @param bool $bool
//   * @return void
//   */
//  public function setGenerateStubMethods($bool)
//  {
//    $this->_generateEntityStubMethods = $bool;
//  }


//  protected function _generateEntityNamespace(ClassMetadataInfo $metadata)
//  {
//    if ($this->_hasNamespace($metadata)) {
//      return 'namespace ' . $this->_getNamespace($metadata) .';';
//    }
//  }

//  /**
//   * @voodo this won't work if there is a namespace in brackets and a class outside of it.
//   * @param string $path
//   */
//  protected function _parseTokensInEntityFile($path)
//  {
//    $tokens = token_get_all(file_get_contents($path));
//    $lastSeenNamespace = "";
//    $lastSeenClass = false;
//
//    for ($i = 0; $i < count($tokens); $i++) {
//      $token = $tokens[$i];
//      if ($token[0] == T_NAMESPACE) {
//        $lastSeenNamespace = $tokens[$i+2][1] . "\\";
//      } else if ($token[0] == T_CLASS) {
//        $lastSeenClass = $lastSeenNamespace . $tokens[$i+2][1];
//        $this->_staticReflection[$lastSeenClass]['properties'] = array();
//        $this->_staticReflection[$lastSeenClass]['methods'] = array();
//      } else if ($token[0] == T_FUNCTION) {
//        if ($tokens[$i+2][0] == T_STRING) {
//          $this->_staticReflection[$lastSeenClass]['methods'][] = $tokens[$i+2][1];
//        } else if ($tokens[$i+2][0] == T_AMPERSAND && $tokens[$i+3][0] == T_STRING) {
//          $this->_staticReflection[$lastSeenClass]['methods'][] = $tokens[$i+3][1];
//        }
//      } else if (in_array($token[0], array(T_VAR, T_PUBLIC, T_protected, T_PROTECTED)) && $tokens[$i+2][0] != T_FUNCTION) {
//        $this->_staticReflection[$lastSeenClass]['properties'][] = substr($tokens[$i+2][1], 1);
//      }
//    }
//  }


//  protected function _generateEntityLifecycleCallbackMethods(ClassMetadataInfo $metadata)
//  {
//    if (isset($metadata->lifecycleCallbacks) && $metadata->lifecycleCallbacks) {
//      $methods = array();
//
//      foreach ($metadata->lifecycleCallbacks as $name => $callbacks) {
//        foreach ($callbacks as $callback) {
//          if ($code = $this->_generateLifecycleCallbackMethod($name, $callback, $metadata)) {
//            $methods[] = $code;
//          }
//        }
//      }
//
//      return implode("\n\n", $methods);
//    }
//
//    return "";
//  }

//  protected function _generateLifecycleCallbackMethod($name, $methodName, $metadata)
//  {
//    if ($this->_hasMethod($methodName, $metadata)) {
//      return;
//    }
//
//    $replacements = array(
//      '<name>'    => $this->_annotationsPrefix . $name,
//      '<methodName>'  => $methodName,
//    );
//
//    $method = str_replace(
//      array_keys($replacements),
//      array_values($replacements),
//      self::$_lifecycleCallbackMethodTemplate
//    );
//
//    return $this->_prefixCodeWithSpaces($method);
//  }

//  protected function _generateTableAnnotation($metadata)
//  {
//    $table = array();
//    if ($metadata->table['name']) {
//      $table[] = 'name="' . $metadata->table['name'] . '"';
//    }
//
//    return '@' . $this->_annotationsPrefix . 'Table(' . implode(', ', $table) . ')';
//  }
//
//  protected function _generateInheritanceAnnotation($metadata)
//  {
//    if ($metadata->inheritanceType != ClassMetadataInfo::INHERITANCE_TYPE_NONE) {
//      return '@' . $this->_annotationsPrefix . 'InheritanceType("'.$this->_getInheritanceTypeString($metadata->inheritanceType).'")';
//    }
//  }
//
//  protected function _generateDiscriminatorColumnAnnotation($metadata)
//  {
//    if ($metadata->inheritanceType != ClassMetadataInfo::INHERITANCE_TYPE_NONE) {
//      $discrColumn = $metadata->discriminatorValue;
//      $columnDefinition = 'name="' . $discrColumn['name']
//        . '", type="' . $discrColumn['type']
//        . '", length=' . $discrColumn['length'];
//
//      return '@' . $this->_annotationsPrefix . 'DiscriminatorColumn(' . $columnDefinition . ')';
//    }
//  }
//
//  protected function _generateDiscriminatorMapAnnotation($metadata)
//  {
//    if ($metadata->inheritanceType != ClassMetadataInfo::INHERITANCE_TYPE_NONE) {
//      $inheritanceClassMap = array();
//
//      foreach ($metadata->discriminatorMap as $type => $class) {
//        $inheritanceClassMap[] .= '"' . $type . '" = "' . $class . '"';
//      }
//
//      return '@' . $this->_annotationsPrefix . 'DiscriminatorMap({' . implode(', ', $inheritanceClassMap) . '})';
//    }
//  }

//  protected function _generateJoinColumnAnnotation(array $joinColumn)
//  {
//    $joinColumnAnnot = array();
//
//    if (isset($joinColumn['name'])) {
//      $joinColumnAnnot[] = 'name="' . $joinColumn['name'] . '"';
//    }
//
//    if (isset($joinColumn['referencedColumnName'])) {
//      $joinColumnAnnot[] = 'referencedColumnName="' . $joinColumn['referencedColumnName'] . '"';
//    }
//
//    if (isset($joinColumn['unique']) && $joinColumn['unique']) {
//      $joinColumnAnnot[] = 'unique=' . ($joinColumn['unique'] ? 'true' : 'false');
//    }
//
//    if (isset($joinColumn['nullable'])) {
//      $joinColumnAnnot[] = 'nullable=' . ($joinColumn['nullable'] ? 'true' : 'false');
//    }
//
//    if (isset($joinColumn['onDelete'])) {
//      $joinColumnAnnot[] = 'onDelete=' . ($joinColumn['onDelete'] ? 'true' : 'false');
//    }
//
//    if (isset($joinColumn['onUpdate'])) {
//      $joinColumnAnnot[] = 'onUpdate=' . ($joinColumn['onUpdate'] ? 'true' : 'false');
//    }
//
//    if (isset($joinColumn['columnDefinition'])) {
//      $joinColumnAnnot[] = 'columnDefinition="' . $joinColumn['columnDefinition'] . '"';
//    }
//
//    return '@' . $this->_annotationsPrefix . 'JoinColumn(' . implode(', ', $joinColumnAnnot) . ')';
//  }

//  protected function _generateEntityAssociationMappingProperties(ClassMetadataInfo $metadata)
//  {
//    $lines = array();
//
//    foreach ($metadata->associationMappings as $associationMapping) {
//      if ($this->_hasProperty($associationMapping['fieldName'], $metadata)) {
//        continue;
//      }
//
//      $lines[] = $this->_generateAssociationMappingPropertyDocBlock($associationMapping, $metadata);
//      $lines[] = $this->_spaces . '' . $associationMapping['fieldName']
//           . ($associationMapping['type'] == 'manyToMany' ? ': []' : null) . ",\n";
//    }
//
//    return implode("\n", $lines);
//  }


//  protected function _getClassToExtendName()
//  {
//    $refl = new \ReflectionClass($this->_getClassToExtend());
//
//    return '\\' . $refl->getName();
//  }









}

