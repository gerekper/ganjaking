<?php

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Menu;
use MailPoet\Config\ServicesChecker;
use MailPoet\Util\Helpers;
use MailPoet\WP\Notice as WPNotice;

if (empty($mailpoetPremium)) exit;

require_once($mailpoetPremium['autoloader']);

preg_match('/(\d+\.\d+)\.\d+/i', $mailpoetPremium['version'], $matches);
$requiredVersion = end($matches);

define('MAILPOET_PREMIUM_VERSION', $mailpoetPremium['version']);
define('MAILPOET_VERSION_REQUIRED', $requiredVersion);
define('MAILPOET_PREMIUM_LICENSE', true);

if (is_plugin_active(plugin_basename($mailpoetPremium['filename']))) {
  // This is to ensure MailPoet is loaded before we proceed
  $GLOBALS['mailpoet_premium'] = $mailpoetPremium;
  // Free 'plugins_loaded' hook is set with a default priority of 10, we need to run before it.
  // It is halfway between 0 and 10 so there's a place for hooks before and after.
  add_action('plugins_loaded', 'mailpoet_premium_init', 5);
} else {
  // Activation, MailPoet should been already loaded
  mailpoet_premium_init($mailpoetPremium);
}

function mailpoet_premium_init($mailpoetPremium = null) {
  try {
    $mailpoetPremium = $mailpoetPremium ?: $GLOBALS['mailpoet_premium'];

    if (mailpoet_premium_check_mailpoet_version()) {
      $initializer = MailPoet\DI\ContainerWrapper::getInstance()
        ->getPremiumContainer()
        ->get(MailPoet\Premium\Config\Initializer::class);
      $initializer->init([
        'file' => $mailpoetPremium['filename'],
        'version' => $mailpoetPremium['version'],
      ]);
    }
  } catch (\Exception $e) {
    WPNotice::displayError($e);
  }
}

// Check for a required MailPoet free version
function mailpoet_premium_check_mailpoet_version() {
  $freeMinorVersion = false;
  if (defined('MAILPOET_VERSION')) {
    // Get the minor version or fall back to using the version as is
    preg_match('/^3\.\d+/', MAILPOET_VERSION, $match);
    $freeMinorVersion = !empty($match[0]) ? $match[0] : MAILPOET_VERSION;
  }
  if (
    !$freeMinorVersion
    || version_compare($freeMinorVersion, MAILPOET_VERSION_REQUIRED) < 0
  ) {
    add_action('admin_notices', 'mailpoet_premium_free_version_required_notice');
    return false;
  } elseif (version_compare($freeMinorVersion, MAILPOET_VERSION_REQUIRED) > 0) {
    if (Menu::isOnMailPoetAdminPage()) {
      add_action('admin_notices', 'mailpoet_premium_upgrade_required_notice');
    }
    return false;
  }

  return true;
}

// Display MailPoet free version error notice
function mailpoet_premium_free_version_required_notice() {
  $notice = sprintf(
    __('You need to have MailPoet version %s or higher activated before using this version of MailPoet Premium.', 'mailpoet-premium'),
    MAILPOET_VERSION_REQUIRED
  );
  printf('<div class="error"><p>%1$s</p></div>', $notice);
}

// Display MailPoet Premium upgrade error notice
function mailpoet_premium_upgrade_required_notice() {
  $checker = new ServicesChecker();
  $isKeyValid = $checker->isPremiumKeyValid($showNotices = false);
  if ($isKeyValid) {
    $notice = __('You have an older version of the Premium plugin. The features have been disabled in order not to break MailPoet. Please update it in the Plugins page now.', 'mailpoet-premium');
  } else {
    $notice = __('Your MailPoet Premium plugin is incompatible with the free MailPoet plugin. [link1]Register[/link1] your key or [link2]purchase one now[/link2] to update the Premium to the latest version.', 'mailpoet');
    $notice = Helpers::replaceLinkTags($notice, 'admin.php?page=mailpoet-settings#premium', [], 'link1');
    $notice = Helpers::replaceLinkTags($notice, 'admin.php?page=mailpoet-premium', [], 'link2');
  }

  printf('<div class="error"><p>%1$s</p></div>', $notice);
}
