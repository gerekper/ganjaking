<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
class Swift_Plugins_ImpersonatePlugin implements Swift_Events_SendListener
{
 private $sender;
 public function __construct($sender)
 {
 $this->sender = $sender;
 }
 public function beforeSendPerformed(Swift_Events_SendEvent $evt)
 {
 $message = $evt->getMessage();
 $headers = $message->getHeaders();
 // save current recipients
 $headers->addPathHeader('X-Swift-Return-Path', $message->getReturnPath());
 // replace them with the one to send to
 $message->setReturnPath($this->sender);
 }
 public function sendPerformed(Swift_Events_SendEvent $evt)
 {
 $message = $evt->getMessage();
 // restore original headers
 $headers = $message->getHeaders();
 if ($headers->has('X-Swift-Return-Path')) {
 $message->setReturnPath($headers->get('X-Swift-Return-Path')->getAddress());
 $headers->removeAll('X-Swift-Return-Path');
 }
 }
}
