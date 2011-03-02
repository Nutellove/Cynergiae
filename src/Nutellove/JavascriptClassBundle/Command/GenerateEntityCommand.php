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
class GenerateEntityCommand extends AbstractCommand
{
  //protected $_js_framework_name   = 'mootools';
  protected $_js_framework_folder = 'Mootools';



  protected function configure()
  {
    $this
      ->setName('jsclass:generate:entity')
      ->setDescription('Generate javascript mootools classes providing xhr-managed persistence for an entity in a bundle from its yaml mapping.')
      ->addArgument('bundle', InputArgument::REQUIRED, 'The name of the bundle (case-sensitive).')
      ->addArgument('entity', InputArgument::REQUIRED, 'The name of the entity (case-sensitive).')
      ->addOption('mapping-type', null, InputOption::VALUE_OPTIONAL, 'The mapping type to to use for the entity. (USELESS OPTION)', 'yaml')
      ->addOption('fields', null, InputOption::VALUE_OPTIONAL, 'The fields to create with the new entity. (USELESS TOO)')
      ->setHelp(<<<EOT
The <info>jsclass:generate:entity</info> task (re)generates a new Mootools Class Base entity, initializes if needed an extended Mootools Class entity in which you'll write your custom own javascript logic, and (re)generates the Controllers needed for PHP/JS synchronization via AJAX, all that inside a bundle :

  <info>./app/console jsclass:generate:entity MyBundle MyEntity</info>

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
    $javascriptClassBundle = $this->application->getKernel()->getBundle('JavascriptClassBundle');

    $entity = $input->getArgument('entity');
    $fullEntityClassName = $bundle->getNamespace().'\\Entity\\'.$entity;
    $mappingType = $input->getOption('mapping-type');

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Fetching the metadata for this Bundle/Entity
    $metadatas = $this->getBundleMetadatas($bundle);
    $class = $metadatas[$fullEntityClassName];

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Setup a new exporter for the mapping type specified
    $cme = new ClassMetadataExporter();
    $exporter = $cme->getExporter($mappingType);

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Generation of the Base Mootools Entity
    $output->writeln(sprintf('Generating Mootools Javascript Entities for "<info>%s</info>"', $fullEntityClassName));

    $baseEntityPath = $bundle->getPath().'/Resources/public/jsclass/'.strtolower($this->getJsFrameworkFolder()).
                      '/entity/'.strtolower($bundle->getName()).'/base/Base'.$entity.'.class.js';
    $baseEntityGenerator = $this->getMootoolsBaseEntityGenerator();

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($baseEntityGenerator);
      $baseEntityCode = $exporter->exportClassMetadata($class);
      //$mappingPath = $mappingCode = false;
    } else {
      $baseEntityCode = $baseEntityGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Base Js Entity for into <info>%s</info>', $baseEntityPath));

    if (file_exists($baseEntityPath)) {
      $output->writeln(sprintf('    > Already existing, overwriting.'));
      //throw new \RuntimeException(sprintf("Mootools Base Entity %s already exists.", $class->name));
    }

    if (!is_dir($dir = dirname($baseEntityPath))) {
      mkdir($dir, 0777, true);
    }
    file_put_contents($baseEntityPath, $baseEntityCode);

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Generation (if needed) of the Mootools Entity
    $entityPath = $bundle->getPath().'/Resources/public/jsclass/'.strtolower($this->getJsFrameworkFolder()).
                  '/entity/'.strtolower($bundle->getName()).'/'.$entity.'.class.js';

    $entityGenerator = $this->getMootoolsEntityGenerator();
    $entityGenerator->setClassToExtend ("Base".$bundle->getName().$entity);

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($entityGenerator);
      $entityCode = $exporter->exportClassMetadata($class);
      //$mappingPath = $mappingCode = false;
    } else {
      $entityCode = $entityGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Js Entity into <info>%s</info>', $entityPath));

    if (file_exists($entityPath)) {
      $output->writeln(sprintf('    > Already exists, left untouched'));
    } else {

      if (!is_dir($dir = dirname($entityPath))) {
          mkdir($dir, 0777, true);
      }
      file_put_contents($entityPath, $entityCode);

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Generation of the Base Controller
    $output->writeln(sprintf('Generating Controllers for "<info>%s</info>"', $fullEntityClassName));

    $baseControllerPath = $javascriptClassBundle->getPath().'/Controller/Entity/'.$bundle->getName().'/Base/'.$entity.'Controller.php';

    $baseControllerGenerator = $this->getBaseControllerGenerator();

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($baseControllerGenerator);
      $baseControllerCode = $exporter->exportClassMetadata($class);
      //$mappingPath = $mappingCode = false;
    } else {
      $baseControllerCode = $baseControllerGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Base Entity Controller into <info>%s</info>', $baseControllerPath));

    if (file_exists($baseControllerPath)) {
      $output->writeln(sprintf('    > Already existing, overwriting.'));
    }

    if (!is_dir($dir = dirname($baseControllerPath))) {
      mkdir($dir, 0777, true);
    }
    file_put_contents($baseControllerPath, $baseControllerCode);

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Generation of the Controller (if needed)

    $controllerPath = $javascriptClassBundle->getPath().'/Controller/Entity/'.$bundle->getName().'/'.$entity.'Controller.php';

    $controllerGenerator = $this->getControllerGenerator();

    if ('annotation' === $mappingType) {
      $exporter->setEntityGenerator($controllerGenerator);
      $controllerCode = $exporter->exportClassMetadata($class);
    } else {
      $controllerCode = $controllerGenerator->generateEntityClass($class);
    }

    $output->writeln(sprintf('  > Entity Controller into <info>%s</info>', $controllerPath));

    if (file_exists($controllerPath)) {
      $output->writeln(sprintf('    > Already exists, left untouched'));
    } else {
      if (!is_dir($dir = dirname($controllerPath))) {
        mkdir($dir, 0777, true);
      }
      file_put_contents($controllerPath, $controllerCode);
    }
  }
}