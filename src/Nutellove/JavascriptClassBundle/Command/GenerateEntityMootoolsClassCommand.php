<?php

/*
 * This file is part of the Nutellove project.
 *
 * © author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nutellove\JavascriptClassBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
//use Doctrine\ORM\EntityManager;

/**
 * Generate new Mootools Classes and associated Controllers and Routes
 * for an entity inside a bundle.
 *
 * @author Antoine Goutenoir <antoine.goutenoir@gmail.com>
 */
class GenerateEntityMootoolsClassCommand extends MootoolsClassCommand
{
  protected function configure()
  {
    $this
      ->setName('mootools:generate:entity')
      ->setDescription('Generate a new Mootools Class entity inside a bundle.')
      ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle to initialize the entity in.')
      ->addArgument('entity', InputArgument::REQUIRED, 'The entity class to initialize.')
      ->addOption('mapping-type', null, InputOption::VALUE_OPTIONAL, 'The mapping type to to use for the entity.', 'yaml')
      ->addOption('fields', null, InputOption::VALUE_OPTIONAL, 'The fields to create with the new entity.')
      ->setHelp(<<<EOT
The <info>mootools:generate:entity</info> task (re)generates a new Mootools Class Base entity, initializes if needed an extended Mootools Class entity in which you'll write your custom own javascript logic, and (re)generates the Routes and Controllers needed for PHP/JS synchronization via AJAX, all that inside a bundle :

  <info>./app/console mootools:generate:entity "MyCustomBundle" "MyEntity"</info>

EOT
    );
  }

  /**
   * @throws \InvalidArgumentException When the bundle doesn't end with Bundle (Example: "Bundle\MySampleBundle")
   * @FIXME
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $bundle = $this->application->getKernel()->getBundle($input->getArgument('bundle'));

    $entity = $input->getArgument('entity');
    $fullEntityClassName = $bundle->getNamespace().'\\Entity\\'.$entity;
    $mappingType = $input->getOption('mapping-type');

////////////////////////////////////////////////////////////////////////////////
    // Fetching the metadata for this Bundle/Entity
    $metadatas = $this->getBundleMetadatas($bundle);
    $class = $metadatas[$fullEntityClassName];

////////////////////////////////////////////////////////////////////////////////
    // Setup a new exporter for the mapping type specified
    $cme = new ClassMetadataExporter();
    $exporter = $cme->getExporter($mappingType);

////////////////////////////////////////////////////////////////////////////////
    // Generation of the Base Mootools Entity
    $output->writeln(sprintf('Generating Mootools Entities for "<info>%s</info>"', $bundle->getName()));

    $baseEntityPath = $bundle->getPath().'/Entity/Mootools/Base/Base'.$entity.'.class.js';

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($this->getBaseEntityGenerator());
      $baseEntityCode = $exporter->exportClassMetadata($class);
      //$mappingPath = $mappingCode = false;
    } else {
      $baseEntityGenerator = $this->getBaseEntityGenerator();
      $baseEntityCode = $baseEntityGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Base Entity for <comment>%s</comment> into <info>%s</info>', $fullEntityClassName, $baseEntityPath));

    if (file_exists($baseEntityPath)) {
      $output->writeln(sprintf('  > Mootools Base Entity <info>%s</info> already exists, overwriting.', $baseEntityPath));
      //throw new \RuntimeException(sprintf("Mootools Base Entity %s already exists.", $class->name));
    }

    if (!is_dir($dir = dirname($baseEntityPath))) {
      mkdir($dir, 0777, true);
    }
    file_put_contents($baseEntityPath, $baseEntityCode);

////////////////////////////////////////////////////////////////////////////////
    // Generation (if needed) of the Mootools Entity
    $entityPath = $bundle->getPath().'/Entity/Mootools/'.$entity.'.class.js';

    // TODO

    $entityGenerator = $this->getEntityGenerator();
    $entityGenerator->setClassToExtend ("Base".$bundle->getName().$entity);

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($entityGenerator);
      $entityCode = $exporter->exportClassMetadata($class);
      //$mappingPath = $mappingCode = false;
    } else {
      $entityCode = $entityGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Entity for <comment>%s</comment> into <info>%s</info>', $fullEntityClassName, $entityPath));

    if (file_exists($entityPath)) {
      $output->writeln(sprintf('  > Mootools Entity <info>%s</info> already exists.', $entityPath));
      //throw new \RuntimeException(sprintf("Mootools Base Entity %s already exists.", $class->name));
    } else {

      if (!is_dir($dir = dirname($baseEntityPath))) {
        mkdir($dir, 0777, true);
      }
      file_put_contents($entityPath, $entityCode);

    }

  }
}

