<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Templates\AutomationBuilder;

class PremiumTemplatesFactory {
  /** @var AutomationBuilder */
  private $builder;

  public function __construct(
    AutomationBuilder $builder
  ) {
    $this->builder = $builder;
  }

  /** @return AutomationTemplate[] */
  public function createTemplates(): array {
    return [
      $this->createSubscriberWelcomeSeriesTemplate(),
      $this->createUserWelcomeSeriesTemplate(),
    ];
  }

  private function createSubscriberWelcomeSeriesTemplate(): AutomationTemplate {
    return new AutomationTemplate(
      'subscriber-welcome-series',
      AutomationTemplate::CATEGORY_WELCOME,
      __('Welcome series for new subscribers', 'mailpoet-premium'),
      __(
        "Welcome new subscribers and start building a relationship with them. Send an email immediately after someone subscribes to your list to introduce your brand and a follow-up two days later to keep the conversation going.",
        'mailpoet-premium'
      ),
      function (): Automation {
        return $this->builder->createFromSequence(
          __('Welcome series for new subscribers', 'mailpoet-premium'),
          [
            ['key' => 'mailpoet:someone-subscribes'],
            ['key' => 'mailpoet:send-email', 'args' => ['name' => __('Welcome email', 'mailpoet-premium')]],
            ['key' => 'core:delay', 'args' => ['delay' => 2, 'delay_type' => 'DAYS']],
            ['key' => 'mailpoet:send-email', 'args' => ['name' => __('Follow-up email', 'mailpoet-premium')]],
          ],
          [
            'mailpoet:run-once-per-subscriber' => true,
          ]
        );
      },
      AutomationTemplate::TYPE_DEFAULT
    );
  }

  private function createUserWelcomeSeriesTemplate(): AutomationTemplate {
    return new AutomationTemplate(
      'user-welcome-series',
      AutomationTemplate::CATEGORY_WELCOME,
      __('Welcome series for new WordPress users', 'mailpoet-premium'),
      __(
        "Welcome new WordPress users to your site. Send an email immediately after a WordPress user registers. Send a follow-up email two days later with more in-depth information.",
        'mailpoet-premium'
      ),
      function (): Automation {
        return $this->builder->createFromSequence(
          __('Welcome series for new WordPress users', 'mailpoet-premium'),
          [
            ['key' => 'mailpoet:wp-user-registered'],
            ['key' => 'mailpoet:send-email', 'args' => ['name' => __('Welcome email', 'mailpoet-premium')]],
            ['key' => 'core:delay', 'args' => ['delay' => 2, 'delay_type' => 'DAYS']],
            ['key' => 'mailpoet:send-email', 'args' => ['name' => __('Follow-up email', 'mailpoet-premium')]],
          ],
          [
            'mailpoet:run-once-per-subscriber' => true,
          ]
        );
      },
      AutomationTemplate::TYPE_DEFAULT
    );
  }
}
