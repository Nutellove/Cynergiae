<?php

/*
 * This file is part of the Nutellove project.
 * 
 * © author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nutellove\MootoolsClassBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generate new Mootools Classes and associated Controllers and Routes
 * for an entity inside a bundle.
 *
 * @author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class GenerateEntityMootoolsClassCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('mootools:generate:entity')
            ->setDescription('Generate a new Mootools Class entity inside a bundle.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle to initialize the entity in.')
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity class to initialize.')
            ->addOption('mapping-type', null, InputOption::VALUE_OPTIONAL, 'The mapping type to to use for the entity.', 'xml')
            ->addOption('fields', null, InputOption::VALUE_OPTIONAL, 'The fields to create with the new entity.')
            ->setHelp(<<<EOT
The <info>mootools:generate:entity</info> task (re)generate a new Mootools Class Base entity, creates if needed an extended Mootools Class entity in which you'll write your custom own javascript logic, and (re)generates the Routes and Controllers needed for PHP/JS synchronization via AJAX, all that inside a bundle :

  <info>./app/console mootools:generate:entity "MyCustomBundle" "User\Group"</info>

--- DO NOT MIND TEXT BELOW ---

The above would initialize a new entity in the following entity namespace <info>Bundle\MyCustomBundle\Entity\User\Group</info>.

You can also optionally specify the fields you want to generate in the new entity:

  <info>./app/console doctrine:generate:entity "MyCustomBundle" "User\Group" --fields="name:string(255) description:text"</info>
EOT
        );
    }

    /**
     * @throws \InvalidArgumentException When the bundle doesn't end with Bundle (Example: "Bundle\MySampleBundle")
     * @FIXME
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	echo "Executed Mootools Command";
    	return;
        $bundle = $this->application->getKernel()->getBundle($input->getArgument('bundle'));

        $entity = $input->getArgument('entity');
        $fullEntityClassName = $bundle->getNamespace().'\\Entity\\'.$entity;
        $mappingType = $input->getOption('mapping-type');

        $class = new ClassMetadataInfo($fullEntityClassName);
        $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);

        // Map the specified fields
        $fields = $input->getOption('fields');
        if ($fields) {
            $e = explode(' ', $fields);
            foreach ($e as $value) {
                $e = explode(':', $value);
                $name = $e[0];
                if (strlen($name)) {
                    $type = isset($e[1]) ? $e[1] : 'string';
                    preg_match_all('/(.*)\((.*)\)/', $type, $matches);
                    $type = isset($matches[1][0]) ? $matches[1][0] : $type;
                    $length = isset($matches[2][0]) ? $matches[2][0] : null;
                    $class->mapField(array(
                        'fieldName' => $name,
                        'type' => $type,
                        'length' => $length
                    ));
                }
            }
        }

        // Setup a new exporter for the mapping type specified
        $cme = new ClassMetadataExporter();
        $exporter = $cme->getExporter($mappingType);

        $entityPath = $bundle->getPath().'/Entity/'.$entity.'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf("Entity %s already exists.", $class->name));
        }

        if ('annotation' === $mappingType) {
            $exporter->setEntityGenerator($this->getEntityGenerator());
            $entityCode = $exporter->exportClassMetadata($class);
            $mappingPath = $mappingCode = false;
        } else {
            $mappingType = 'yaml' == $mappingType ? 'yml' : $mappingType;
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/metadata/orm/'.str_replace('\\', '.', $fullEntityClassName).'.dcm.'.$mappingType;
            $mappingCode = $exporter->exportClassMetadata($class);

            $entityGenerator = $this->getEntityGenerator();
            $entityCode = $entityGenerator->generateEntityClass($class);

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf("Cannot generate entity when mapping <info>%s</info> already exists", $mappingPath));
            }
        }

        $output->writeln(sprintf('Generating entity for "<info>%s</info>"', $bundle->getName()));
        $output->writeln(sprintf('  > entity <comment>%s</comment> into <info>%s</info>', $fullEntityClassName, $entityPath));

        if (!is_dir($dir = dirname($entityPath))) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $output->writeln(sprintf('  > mapping into <info>%s</info>', $mappingPath));

            if (!is_dir($dir = dirname($mappingPath))) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($mappingPath, $mappingCode);
        }

    }
}
