<?php
namespace MailPoetVendor\Twig\Error;
if (!defined('ABSPATH')) exit;
class SyntaxError extends Error
{
 public function addSuggestions($name, array $items)
 {
 $alternatives = [];
 foreach ($items as $item) {
 $lev = \levenshtein($name, $item);
 if ($lev <= \strlen($name) / 3 || \false !== \strpos($item, $name)) {
 $alternatives[$item] = $lev;
 }
 }
 if (!$alternatives) {
 return;
 }
 \asort($alternatives);
 $this->appendMessage(\sprintf(' Did you mean "%s"?', \implode('", "', \array_keys($alternatives))));
 }
}
\class_alias('MailPoetVendor\\Twig\\Error\\SyntaxError', 'MailPoetVendor\\Twig_Error_Syntax');
