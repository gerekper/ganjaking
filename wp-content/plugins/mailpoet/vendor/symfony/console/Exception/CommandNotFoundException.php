<?php
namespace Symfony\Component\Console\Exception;
if (!defined('ABSPATH')) exit;
class CommandNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
 private $alternatives;
 public function __construct(string $message, array $alternatives = [], int $code = 0, \Throwable $previous = null)
 {
 parent::__construct($message, $code, $previous);
 $this->alternatives = $alternatives;
 }
 public function getAlternatives()
 {
 return $this->alternatives;
 }
}
