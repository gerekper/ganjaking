<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
require __DIR__ . '/classes/Swift.php';
Swift::registerAutoload(function () {
 // Load in dependency maps
 require __DIR__ . '/dependency_maps/cache_deps.php';
 require __DIR__ . '/dependency_maps/mime_deps.php';
 require __DIR__ . '/dependency_maps/message_deps.php';
 require __DIR__ . '/dependency_maps/transport_deps.php';
 // Load in global library preferences
 require __DIR__ . '/preferences.php';
});
