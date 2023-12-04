<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Payloads\WooCommerceSubscriptionStatusChangePayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<WooCommerceSubscriptionStatusChangePayload>
 */
class WooCommerceSubscriptionStatusChangeSubject implements Subject {


  const KEY = 'woocommerce-subscriptions:subscription-status-changed';

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce subscription status change', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'from' => Builder::string()->required(),
      'to' => Builder::string()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $from = $subjectData->getArgs()['from'];
    $to = $subjectData->getArgs()['to'];

    return new WooCommerceSubscriptionStatusChangePayload($from, $to);
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getFields(): array {
    return [];
  }
}
