<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\Util\License\License;
use MailPoet\WP\DateTime;
use MailPoet\WP\Notice as WPNotice;

class ServicesChecker {

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  public function __construct() {
    $this->settings = SettingsController::getInstance();
    $this->subscribersFeature = ContainerWrapper::getInstance()->get(SubscribersFeature::class);
  }

  public function isPremiumPluginActive() {
    return true;
  }

  public function isMailPoetAPIKeyValid($displayErrorNotice = true, $forceCheck = false) {
    if (!$forceCheck && !Bridge::isMPSendingServiceEnabled()) {
      return null;
    }

    $mssKeySpecified = Bridge::isMSSKeySpecified();
    $mssKey = $this->settings->get(Bridge::API_KEY_STATE_SETTING_NAME);

    if (
      !$mssKeySpecified
      || empty($mssKey['state'])
      || $mssKey['state'] == Bridge::KEY_INVALID
    ) {
      if ($displayErrorNotice) {
        $error = '<h3>' . __('All sending is currently paused!', 'mailpoet') . '</h3>';
        $error .= '<p>' . __('Your key to send with MailPoet is invalid.', 'mailpoet') . '</p>';
        $error .= '<p><a '
          . ' href="https://account.mailpoet.com?s=' . ($this->subscribersFeature->getSubscribersCount() + 1) . '"'
          . ' class="button button-primary" '
          . ' target="_blank"'
          . '>' . __('Purchase a key', 'mailpoet') . '</a></p>';

        WPNotice::displayError($error, '', '', false, false);
      }
      return false;
    } elseif (
      $mssKey['state'] == Bridge::KEY_EXPIRING
      && !empty($mssKey['data']['expire_at'])
    ) {
      if ($displayErrorNotice) {
        $dateTime = new DateTime();
        $date = $dateTime->formatDate(strtotime($mssKey['data']['expire_at']));
        $error = Helpers::replaceLinkTags(
          // translators: %s is a date.
          __("Your newsletters are awesome! Don't forget to [link]upgrade your MailPoet email plan[/link] by %s to keep sending them to your subscribers.", 'mailpoet'),
          'https://account.mailpoet.com?s=' . $this->subscribersFeature->getSubscribersCount(),
          ['target' => '_blank']
        );
        $error = sprintf($error, $date);
        WPNotice::displayWarning($error);
      }
      return true;
    } elseif ($mssKey['state'] == Bridge::KEY_VALID) {
      return true;
    }

    return false;
  }

  public function isPremiumKeyValid($displayErrorNotice = true) {
    return true;
  }

  public function isMailPoetAPIKeyPendingApproval(): bool {
    $mssActive = Bridge::isMPSendingServiceEnabled();
    $mssKeyValid = $this->isMailPoetAPIKeyValid();
    $isApproved = $this->settings->get('mta.mailpoet_api_key_state.data.is_approved');
    $mssKeyPendingApproval = $isApproved === false || $isApproved === 'false'; // API unfortunately saves this as a string
    return $mssActive && $mssKeyValid && $mssKeyPendingApproval;
  }

  public function isUserActivelyPaying(): bool {
    $isPremiumKeyValid = $this->isPremiumKeyValid(false);

    $mssActive = Bridge::isMPSendingServiceEnabled();
    $isMssKeyValid = $this->isMailPoetAPIKeyValid(false);

    if (!$mssActive || ($isPremiumKeyValid && !$isMssKeyValid)) {
      return $this->subscribersFeature->hasPremiumSupport();
    } else {
      return $this->subscribersFeature->hasMssPremiumSupport();
    }
  }

  /**
   * Returns MSS or Premium valid key.
   */
  public function getAnyValidKey(): ?string {
    if ($this->isMailPoetAPIKeyValid(false, true)) {
      return $this->settings->get(Bridge::API_KEY_SETTING_NAME);
    }
    if ($this->isPremiumKeyValid(false)) {
      return $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    }
    return null;
  }

  public function generatePartialApiKey(): string {
    $key = (string)($this->getAnyValidKey());
    if ($key) {
      $halfKeyLength = (int)(strlen($key) / 2);

      return substr($key, 0, $halfKeyLength);
    }
    return '';
  }
}