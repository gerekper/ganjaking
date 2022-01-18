<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_CharacterReaderFactory
{
 public function getReaderFor($charset);
}
