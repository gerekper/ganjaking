<?php
namespace Composer\Command;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
class DependsCommand extends BaseDependencyCommand
{
 protected function configure()
 {
 $this
 ->setName('depends')
 ->setAliases(array('why'))
 ->setDescription('Shows which packages cause the given package to be installed.')
 ->setDefinition(array(
 new InputArgument(self::ARGUMENT_PACKAGE, InputArgument::REQUIRED, 'Package to inspect'),
 new InputOption(self::OPTION_RECURSIVE, 'r', InputOption::VALUE_NONE, 'Recursively resolves up to the root package'),
 new InputOption(self::OPTION_TREE, 't', InputOption::VALUE_NONE, 'Prints the results as a nested tree'),
 ))
 ->setHelp(
 <<<EOT
Displays detailed information about where a package is referenced.
<info>php composer.phar depends composer/composer</info>
Read more at https://getcomposer.org/doc/03-cli.md#depends-why-
EOT
 )
 ;
 }
 protected function execute(InputInterface $input, OutputInterface $output)
 {
 return parent::doExecute($input, $output);
 }
}
