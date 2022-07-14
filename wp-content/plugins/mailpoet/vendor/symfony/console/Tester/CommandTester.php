<?php
namespace Symfony\Component\Console\Tester;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
class CommandTester
{
 use TesterTrait;
 private $command;
 private $input;
 private $statusCode;
 public function __construct(Command $command)
 {
 $this->command = $command;
 }
 public function execute(array $input, array $options = [])
 {
 // set the command name automatically if the application requires
 // this argument and no command name was passed
 if (!isset($input['command'])
 && (null !== $application = $this->command->getApplication())
 && $application->getDefinition()->hasArgument('command')
 ) {
 $input = array_merge(['command' => $this->command->getName()], $input);
 }
 $this->input = new ArrayInput($input);
 // Use an in-memory input stream even if no inputs are set so that QuestionHelper::ask() does not rely on the blocking STDIN.
 $this->input->setStream(self::createStream($this->inputs));
 if (isset($options['interactive'])) {
 $this->input->setInteractive($options['interactive']);
 }
 if (!isset($options['decorated'])) {
 $options['decorated'] = false;
 }
 $this->initOutput($options);
 return $this->statusCode = $this->command->run($this->input, $this->output);
 }
}
