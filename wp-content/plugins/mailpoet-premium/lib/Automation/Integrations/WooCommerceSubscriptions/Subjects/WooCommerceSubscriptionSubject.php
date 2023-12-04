<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Exceptions\NotFoundException;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Fields\SubscriptionFields;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Payloads\WooCommerceSubscriptionPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WooCommerce\WooCommerceSubscriptions\Helper as WCS;

/**
 * @implements Subject<WooCommerceSubscriptionPayload>
 */
class WooCommerceSubscriptionSubject implements Subject {


  public const KEY = 'woocommerce-subscriptions:subscription';

  /** @var WCS */
  private $wcs;

  /** @var SubscriptionFields */
  private $subscriptionFields;

  public function __construct(
    WCS $wcs,
    SubscriptionFields $subscriptionFields
  ) {
    $this->wcs = $wcs;
    $this->subscriptionFields = $subscriptionFields;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce Subscription', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'subscription_id' => Builder::integer()->required(),
    ]);
  }

  public function getFields(): array {
    return $this->subscriptionFields->getFields();
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['subscription_id'];

    $subscription = $this->wcs->wcsGetSubscription($id);
    if (!$subscription instanceof \WC_Subscription) {
      // translators: %d is the order ID.
      throw NotFoundException::create()->withMessage(sprintf(__("Subscription with ID '%d' not found.", 'mailpoet-premium'), $id));
    }
    return new WooCommerceSubscriptionPayload($subscription);
  }
}
