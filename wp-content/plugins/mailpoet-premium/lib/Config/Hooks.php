<?php

namespace MailPoet\Premium\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\API;
use MailPoet\Config\ServicesChecker;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

class Hooks {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init() {
    $this->wp->addAction(
      'mailpoet_api_setup',
      [$this, 'addPremiumAPIEndpoints']
    );

    $this->wp->addAction(
      'in_plugin_update_message-mailpoet-premium/mailpoet-premium.php',
      [$this, 'pluginUpdateMessage']
    );
  }

  public function addPremiumAPIEndpoints(API $api) {
    $api->addEndpointNamespace('MailPoet\Premium\API\JSON\v1', 'v1');
  }

  public function pluginUpdateMessage() {
    $checker = new ServicesChecker();
    $isKeyValid = $checker->isPremiumKeyValid($showNotices = false);
    if (!$isKeyValid) {
      $error = WPFunctions::get()->__('[link1]Register[/link1] your copy of the MailPoet Premium plugin to receive access to automatic upgrades and support. Need a license key? [link2]Purchase one now.[/link2]', 'mailpoet-premium');
      $error = Helpers::replaceLinkTags($error, 'admin.php?page=mailpoet-settings#premium', [], 'link1');
      $error = Helpers::replaceLinkTags($error, 'admin.php?page=mailpoet-premium', [], 'link2');
      echo '<br><br>' . $error;
    }
  }
}
