<?php
namespace Symfony\Component\Console\Tester;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
class ApplicationTester
{
 use TesterTrait;
 private $application;
 private $input;
 private $statusCode;
 public function __construct(Application $application)
 {
 $this->application = $application;
 }
 public function run(array $input, $options = [])
 {
 $this->input = new ArrayInput($input);
 if (isset($options['interactive'])) {
 $this->input->setInteractive($options['interactive']);
 }
 if ($this->inputs) {
 $this->input->setStream(self::createStream($this->inputs));
 }
 $this->initOutput($options);
 return $this->statusCode = $this->application->run($this->input, $this->output);
 }
}
