<?php
namespace Symfony\Component\Console\Command;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class ListCommand extends Command
{
 protected function configure()
 {
 $this
 ->setName('list')
 ->setDefinition($this->createDefinition())
 ->setDescription('List commands')
 ->setHelp(<<<'EOF'
The <info>%command.name%</info> command lists all commands:
 <info>php %command.full_name%</info>
You can also display the commands for a specific namespace:
 <info>php %command.full_name% test</info>
You can also output the information in other formats by using the <comment>--format</comment> option:
 <info>php %command.full_name% --format=xml</info>
It's also possible to get raw list of commands (useful for embedding command runner):
 <info>php %command.full_name% --raw</info>
EOF
 )
 ;
 }
 public function getNativeDefinition()
 {
 return $this->createDefinition();
 }
 protected function execute(InputInterface $input, OutputInterface $output)
 {
 $helper = new DescriptorHelper();
 $helper->describe($output, $this->getApplication(), [
 'format' => $input->getOption('format'),
 'raw_text' => $input->getOption('raw'),
 'namespace' => $input->getArgument('namespace'),
 ]);
 return 0;
 }
 private function createDefinition(): InputDefinition
 {
 return new InputDefinition([
 new InputArgument('namespace', InputArgument::OPTIONAL, 'The namespace name'),
 new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list'),
 new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
 ]);
 }
}
