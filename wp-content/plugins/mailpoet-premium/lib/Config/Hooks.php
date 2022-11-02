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

    $this->wp->addFilter(
      'plugin_action_links_' . Env::$pluginPath,
      [$this, 'setSettingsLinkInPluginPage']
    );
  }

  public function addPremiumAPIEndpoints(API $api) {
    $api->addEndpointNamespace('MailPoet\Premium\API\JSON\v1', 'v1');
  }

  public function pluginUpdateMessage() {
    $checker = new ServicesChecker();
    $isKeyValid = $checker->isPremiumKeyValid($showNotices = false);
    if (!$isKeyValid) {
      $error = __('[link1]Register[/link1] your copy of the MailPoet Premium plugin to receive access to automatic upgrades and support. Need a license key? [link2]Purchase one now.[/link2]', 'mailpoet-premium');
      $error = Helpers::replaceLinkTags($error, 'admin.php?page=mailpoet-settings#premium', [], 'link1');
      $error = Helpers::replaceLinkTags($error, 'admin.php?page=mailpoet-premium', [], 'link2');
      echo wp_kses(
        '<br><br>' . $error,
        [
          'br' => [],
          'a' => [
            'href' => true,
            'class' => true,
            'target' => true,
          ],
        ]
      );
    }
  }

  /**
   * @param array<string, string> $actionLinks
   * @return array<string, string>
   */
  public function setSettingsLinkInPluginPage(array $actionLinks): array {
    $customLinks = [
      'settings' => '<a href="' . $this->wp->adminUrl('admin.php?page=mailpoet-settings') . '" aria-label="' . $this->wp->escAttr(__('View MailPoet settings', 'mailpoet-premium')) . '">' . $this->wp->escHtml(__('Settings', 'mailpoet-premium')) . '</a>',
    ];

    return array_merge($customLinks, $actionLinks);
  }
}
