<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
class ProcessHelper extends Helper
{
 public function run(OutputInterface $output, $cmd, $error = null, callable $callback = null, $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE)
 {
 if (!class_exists(Process::class)) {
 throw new \LogicException('The ProcessHelper cannot be run as the Process component is not installed. Try running "compose require symfony/process".');
 }
 if ($output instanceof ConsoleOutputInterface) {
 $output = $output->getErrorOutput();
 }
 $formatter = $this->getHelperSet()->get('debug_formatter');
 if ($cmd instanceof Process) {
 $cmd = [$cmd];
 }
 if (!\is_array($cmd)) {
 @trigger_error(sprintf('Passing a command as a string to "%s()" is deprecated since Symfony 4.2, pass it the command as an array of arguments instead.', __METHOD__), \E_USER_DEPRECATED);
 $cmd = [method_exists(Process::class, 'fromShellCommandline') ? Process::fromShellCommandline($cmd) : new Process($cmd)];
 }
 if (\is_string($cmd[0] ?? null)) {
 $process = new Process($cmd);
 $cmd = [];
 } elseif (($cmd[0] ?? null) instanceof Process) {
 $process = $cmd[0];
 unset($cmd[0]);
 } else {
 throw new \InvalidArgumentException(sprintf('Invalid command provided to "%s()": the command should be an array whose first element is either the path to the binary to run or a "Process" object.', __METHOD__));
 }
 if ($verbosity <= $output->getVerbosity()) {
 $output->write($formatter->start(spl_object_hash($process), $this->escapeString($process->getCommandLine())));
 }
 if ($output->isDebug()) {
 $callback = $this->wrapCallback($output, $process, $callback);
 }
 $process->run($callback, $cmd);
 if ($verbosity <= $output->getVerbosity()) {
 $message = $process->isSuccessful() ? 'Command ran successfully' : sprintf('%s Command did not run successfully', $process->getExitCode());
 $output->write($formatter->stop(spl_object_hash($process), $message, $process->isSuccessful()));
 }
 if (!$process->isSuccessful() && null !== $error) {
 $output->writeln(sprintf('<error>%s</error>', $this->escapeString($error)));
 }
 return $process;
 }
 public function mustRun(OutputInterface $output, $cmd, $error = null, callable $callback = null)
 {
 $process = $this->run($output, $cmd, $error, $callback);
 if (!$process->isSuccessful()) {
 throw new ProcessFailedException($process);
 }
 return $process;
 }
 public function wrapCallback(OutputInterface $output, Process $process, callable $callback = null)
 {
 if ($output instanceof ConsoleOutputInterface) {
 $output = $output->getErrorOutput();
 }
 $formatter = $this->getHelperSet()->get('debug_formatter');
 return function ($type, $buffer) use ($output, $process, $callback, $formatter) {
 $output->write($formatter->progress(spl_object_hash($process), $this->escapeString($buffer), Process::ERR === $type));
 if (null !== $callback) {
 $callback($type, $buffer);
 }
 };
 }
 private function escapeString(string $str): string
 {
 return str_replace('<', '\\<', $str);
 }
 public function getName()
 {
 return 'process';
 }
}
