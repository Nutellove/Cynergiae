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
 * <http://www.doctrine-project.org>.
 */

namespace Nutellove\JavascriptClassBundle\Tools\Mapping\Driver;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\Driver\YamlDriver;


/**
 * The YamlDriver reads the mapping metadata from yaml schema files.
 * That driver looks for the `mootools` attribute in addition of the usual field
 * attributes (type, length, ...)
 */
class JavascriptClassYamlDriver extends YamlDriver {

  /**
   * @override
   */
  public function loadMetaDataForClass ($className, ClassMetadataInfo $metadata)
  {
    // Load the parent, which loads the metadata with usual field attributes
    parent::loadMetaDataForClass ($className, $metadata);

    // We add to metadata the info about mootools field attribute
    $element = $this->getElement($className);
    if (isset($element['fields'])) {
      foreach ($element['fields'] as $name => $fieldMapping) {

        $mapping = $metadata->getFieldMapping($name);

        if (isset($fieldMapping['mootools'])) {
            $mapping['mootools'] = $fieldMapping['mootools'];
        }

        //$metadata->mapField($mapping); // Throws Duplicate Exception, of course ^^
        // So, Hackish way to replace the existing fieldMapping (it's READ-ONLY)
        $metadata->fieldMappings[$name] = $mapping;

      }
    }
  }
}
