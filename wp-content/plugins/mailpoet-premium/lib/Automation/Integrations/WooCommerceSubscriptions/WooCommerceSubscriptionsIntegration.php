<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionStatusChangeSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionCreatedTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionExpiredTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionPaymentFailedTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionRenewedTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionStatusChangedTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionTrialEndedTrigger;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers\SubscriptionTrialStartedTrigger;

class WooCommerceSubscriptionsIntegration implements Integration {


  /** @var ContextFactory */
  private $contextFactory;

  /** @var SubscriptionCreatedTrigger */
  private $subscriptionCreatedTrigger;

  /** @var SubscriptionStatusChangedTrigger */
  private $subscriptionStatusChangedTrigger;

  /** @var SubscriptionTrialEndedTrigger */
  private $subscriptionTrialEndedTrigger;

  /** @var SubscriptionTrialStartedTrigger */
  private $subscriptionTrialStartedTrigger;

  /** @var SubscriptionRenewedTrigger */
  private $subscriptionRenewedTrigger;

  /** @var SubscriptionPaymentFailedTrigger */
  private $subscriptionPaymentFailedTrigger;

  /** @var SubscriptionExpiredTrigger */
  private $subscriptionExpiredTrigger;

  /** @var WooCommerceSubscriptionSubject */
  private $wooCommerceSubscriptionSubject;

  /** @var WooCommerceSubscriptionStatusChangeSubject */
  private $wooCommerceSubscriptionStatusChangeSubject;

  public function __construct(
    ContextFactory $contextFactory,
    SubscriptionCreatedTrigger $subscriptionCreatedTrigger,
    SubscriptionStatusChangedTrigger $subscriptionStatusChangedTrigger,
    SubscriptionTrialEndedTrigger $subscriptionTrialEndedTrigger,
    SubscriptionTrialStartedTrigger $subscriptionTrialStartedTrigger,
    SubscriptionRenewedTrigger $subscriptionRenewedTrigger,
    SubscriptionPaymentFailedTrigger $subscriptionPaymentFailedTrigger,
    SubscriptionExpiredTrigger $subscriptionExpiredTrigger,
    WooCommerceSubscriptionSubject $wooCommerceSubscriptionSubject,
    WooCommerceSubscriptionStatusChangeSubject $wooCommerceSubscriptionStatusChangeSubject
  ) {
    $this->contextFactory = $contextFactory;
    $this->subscriptionCreatedTrigger = $subscriptionCreatedTrigger;
    $this->subscriptionStatusChangedTrigger = $subscriptionStatusChangedTrigger;
    $this->subscriptionTrialEndedTrigger = $subscriptionTrialEndedTrigger;
    $this->subscriptionTrialStartedTrigger = $subscriptionTrialStartedTrigger;
    $this->subscriptionRenewedTrigger = $subscriptionRenewedTrigger;
    $this->subscriptionPaymentFailedTrigger = $subscriptionPaymentFailedTrigger;
    $this->subscriptionExpiredTrigger = $subscriptionExpiredTrigger;
    $this->wooCommerceSubscriptionSubject = $wooCommerceSubscriptionSubject;
    $this->wooCommerceSubscriptionStatusChangeSubject = $wooCommerceSubscriptionStatusChangeSubject;
  }

  public function register(Registry $registry): void {
    $registry->addContextFactory('woocommerce-subscriptions', function () {
      return $this->contextFactory->getContextData();
    });

    $registry->addTrigger($this->subscriptionCreatedTrigger);
    $registry->addTrigger($this->subscriptionStatusChangedTrigger);
    $registry->addTrigger($this->subscriptionTrialEndedTrigger);
    $registry->addTrigger($this->subscriptionTrialStartedTrigger);
    $registry->addTrigger($this->subscriptionRenewedTrigger);
    $registry->addTrigger($this->subscriptionPaymentFailedTrigger);
    $registry->addTrigger($this->subscriptionExpiredTrigger);
    $registry->addSubject($this->wooCommerceSubscriptionSubject);
    $registry->addSubject($this->wooCommerceSubscriptionStatusChangeSubject);
  }
}
