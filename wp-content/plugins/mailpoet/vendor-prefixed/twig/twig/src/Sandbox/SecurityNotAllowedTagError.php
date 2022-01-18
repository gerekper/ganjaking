<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
class SecurityNotAllowedTagError extends SecurityError
{
 private $tagName;
 public function __construct(string $message, string $tagName, int $lineno = -1, string $filename = null, \Exception $previous = null)
 {
 if (-1 !== $lineno) {
 @\trigger_error(\sprintf('Passing $lineno as a 3th argument of the %s constructor is deprecated since Twig 2.8.1.', __CLASS__), \E_USER_DEPRECATED);
 }
 if (null !== $filename) {
 @\trigger_error(\sprintf('Passing $filename as a 4th argument of the %s constructor is deprecated since Twig 2.8.1.', __CLASS__), \E_USER_DEPRECATED);
 }
 if (null !== $previous) {
 @\trigger_error(\sprintf('Passing $previous as a 5th argument of the %s constructor is deprecated since Twig 2.8.1.', __CLASS__), \E_USER_DEPRECATED);
 }
 parent::__construct($message, $lineno, $filename, $previous);
 $this->tagName = $tagName;
 }
 public function getTagName()
 {
 return $this->tagName;
 }
}
\class_alias('MailPoetVendor\\Twig\\Sandbox\\SecurityNotAllowedTagError', 'MailPoetVendor\\Twig_Sandbox_SecurityNotAllowedTagError');
