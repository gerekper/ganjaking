<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Integrations\MailPoet\Templates\AutomationBuilder;

class PremiumAutomationTemplates {


  private $builder;

  public function __construct(
    AutomationBuilder $builder
  ) {
    $this->builder = $builder;
  }

  /**
   * @param AutomationTemplate[] $templates
   * @return AutomationTemplate[]
   */
  public function integrate(array $templates): array {
    $templates = array_filter(
      $templates,
      function(AutomationTemplate $template): bool {
        $notVisibleOnPremium = [
          AutomationTemplate::TYPE_FREE_ONLY,
          AutomationTemplate::TYPE_PREMIUM,
        ];
        return !in_array($template->getType(), $notVisibleOnPremium, true);
      }
    );
    return array_merge($this->templates(), $templates);
  }

  /**
   * @return AutomationTemplate[]
   */
  public function templates(): array {
    $subscriberWelcomeSeries = new AutomationTemplate(
      'subscriber-welcome-series',
      AutomationTemplate::CATEGORY_WELCOME,
      __(
        "Welcome new subscribers and start building a relationship with them. Send an email immediately after someone subscribes to your list to introduce your brand and a follow-up two days later to keep the conversation going.",
        'mailpoet-premium'
      ),
      $this->builder->createFromSequence(
        __('Welcome series for new subscribers', 'mailpoet-premium'),
        [
          'mailpoet:someone-subscribes',
          'mailpoet:send-email',
          'core:delay',
          'mailpoet:send-email',
        ],
        [
          [],
          [
            'name' => __('Welcome email', 'mailpoet-premium'),
          ],
          [
            'delay' => 2,
            'delay_type' => 'DAYS',
          ],
          [
            'name' => __('Follow-up email', 'mailpoet-premium'),
          ],
        ]
      ),
      AutomationTemplate::TYPE_DEFAULT
    );

    $userWelcomeSeries = new AutomationTemplate(
      'user-welcome-series',
      AutomationTemplate::CATEGORY_WELCOME,
      __(
        "Welcome new WordPress users to your site. Send an email immediately after a WordPress user registers. Send a follow-up email two days later with more in-depth information.",
        'mailpoet-premium'
      ),
      $this->builder->createFromSequence(
        __('Welcome series for new WordPress users', 'mailpoet-premium'),
        [
          'mailpoet:wp-user-registered',
          'mailpoet:send-email',
          'core:delay',
          'mailpoet:send-email',
        ],
        [
          [],
          [
            'name' => __('Welcome email', 'mailpoet-premium'),
          ],
          [
            'delay' => 2,
            'delay_type' => 'DAYS',
          ],
          [
            'name' => __('Follow-up email', 'mailpoet-premium'),
          ],
        ]
      ),
      AutomationTemplate::TYPE_DEFAULT
    );

    return [
      $subscriberWelcomeSeries,
      $userWelcomeSeries,
    ];
  }
}
