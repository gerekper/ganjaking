<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Templates\AutomationBuilder;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;

class PremiumTemplatesFactory {
  /** @var AutomationBuilder */
  private $builder;

  /** @var WooCommerce */
  private $woocommerce;

  public function __construct(
    AutomationBuilder $builder,
    WooCommerce $woocommerce
  ) {
    $this->builder = $builder;
    $this->woocommerce = $woocommerce;
  }

  /** @return AutomationTemplate[] */
  public function createTemplates(): array {
    $templates = [
      $this->createSubscriberWelcomeSeriesTemplate(),
      $this->createUserWelcomeSeriesTemplate(),
    ];

    if ($this->woocommerce->isWooCommerceActive()) {
      $templates[] = $this->createPurchasedProductTemplate();
      $templates[] = $this->createPurchasedProductWithTagTemplate();
      $templates[] = $this->createPurchasedInCategoryTemplate();
    }

    return $templates;
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

  private function createPurchasedProductTemplate(): AutomationTemplate {
    return new AutomationTemplate(
      'purchased-product',
      AutomationTemplate::CATEGORY_WOOCOMMERCE,
      __('Purchased a product', 'mailpoet-premium'),
      __(
        'Share care instructions or simply thank the customer for making an order.',
        'mailpoet-premium'
      ),
      function (): Automation {
        return $this->builder->createFromSequence(
          __('Purchased a product', 'mailpoet-premium'),
          $this->createPurchasedTemplateBody('woocommerce:order:products')
        );
      },
      AutomationTemplate::TYPE_DEFAULT
    );
  }

  private function createPurchasedProductWithTagTemplate(): AutomationTemplate {
    return new AutomationTemplate(
      'purchased-product-with-tag',
      AutomationTemplate::CATEGORY_WOOCOMMERCE,
      __('Purchased a product with a tag', 'mailpoet-premium'),
      __(
        'Share care instructions or simply thank the customer for making an order.',
        'mailpoet-premium'
      ),
      function (): Automation {
        return $this->builder->createFromSequence(
          __('Purchased a product with a tag', 'mailpoet-premium'),
          $this->createPurchasedTemplateBody('woocommerce:order:tags')
        );
      },
      AutomationTemplate::TYPE_DEFAULT
    );
  }

  private function createPurchasedInCategoryTemplate(): AutomationTemplate {
    return new AutomationTemplate(
      'purchased-in-category',
      AutomationTemplate::CATEGORY_WOOCOMMERCE,
      __('Purchased in a category', 'mailpoet-premium'),
      __(
        'Share care instructions or simply thank the customer for making an order.',
        'mailpoet-premium'
      ),
      function (): Automation {
        return $this->builder->createFromSequence(
          __('Purchased in a category', 'mailpoet-premium'),
          $this->createPurchasedTemplateBody('woocommerce:order:categories')
        );
      },
      AutomationTemplate::TYPE_DEFAULT
    );
  }

  private function createPurchasedTemplateBody(string $filterField) {
    return [
      [
        'key' => 'woocommerce:order-status-changed',
        'args' => [
          'from' => 'any',
          'to' => 'wc-completed',
        ],
        'filters' => [
          'operator' => 'and',
          'groups' => [
            [
              'operator' => 'and',
              'filters' => [
                ['field' => $filterField, 'condition' => 'matches-any-of', 'value' => null],
              ],
            ],
          ],
        ],
      ],
      [
        'key' => 'mailpoet:send-email',
        'args' => [
          'name' => __('Important information about your order', 'mailpoet-premium'),
          'subject' => __('Important information about your order', 'mailpoet-premium'),
        ],
      ],
    ];
  }
}
