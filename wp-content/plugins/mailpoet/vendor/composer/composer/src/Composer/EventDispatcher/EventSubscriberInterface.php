<?php
namespace Composer\EventDispatcher;
if (!defined('ABSPATH')) exit;
interface EventSubscriberInterface
{
 public static function getSubscribedEvents();
}
