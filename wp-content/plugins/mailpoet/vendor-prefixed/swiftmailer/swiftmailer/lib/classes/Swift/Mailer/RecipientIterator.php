<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_Mailer_RecipientIterator
{
 public function hasNext();
 public function nextRecipient();
}
