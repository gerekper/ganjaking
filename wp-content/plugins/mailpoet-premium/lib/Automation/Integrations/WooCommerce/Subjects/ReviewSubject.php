<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerce\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Premium\Automation\Integrations\WooCommerce\Payloads\ReviewPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<ReviewPayload>
 */
class ReviewSubject implements Subject {


  public const KEY = 'woocommerce:review';

  private $wp;

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    return __('Review', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'review_id' => Builder::integer()->required(),
    ]);
  }

  public function getFields(): array {
    return [
      new Field(
        'woocommerce:review:rating',
        Field::TYPE_INTEGER,
        __('Review rating', 'mailpoet-premium'),
        function (ReviewPayload $payload) {
          return $payload->getRating();
        }
      ),
    ];
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $reviewId = (int)$subjectData->getArgs()['review_id'];
    return new ReviewPayload($reviewId, $this->wp);
  }
}
