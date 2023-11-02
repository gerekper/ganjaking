<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerce\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\FilterHandler;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter;
use MailPoet\Automation\Engine\Data\FilterGroup;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Integration\ValidationException;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\Core\Filters\NumberFilter;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Automation\Integrations\WordPress\Subjects\CommentSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerce\Subjects\ReviewSubject;
use MailPoet\Premium\Automation\Integrations\WordPress\Triggers\MadeACommentTrigger;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class MadeAReviewTrigger extends MadeACommentTrigger implements Trigger {

  public const KEY = 'woocommerce:made-a-review';

  /** @var WooCommerce */
  private $wc;

  public function __construct(
    WordPress $wp,
    AutomationRunStorage $automationRunStorage,
    FilterHandler $filterHandler,
    WooCommerce $wc
  ) {
    parent::__construct($wp, $automationRunStorage, $filterHandler);
    $this->wc = $wc;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    return __('Made a review', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'comment_statuses' => Builder::array(Builder::string())->default([]),
      'post_ids' => Builder::array(Builder::integer())->default([]),
      'post_types' => Builder::array(Builder::string())->default(['product']),
      'terms' => $this->termsBuilder(),
      'rating' => Builder::object(
        [
          'is_active' => Builder::boolean()->default(false),
          'from' => Builder::integer()->minimum(0)->maximum(5)->default(0),
          'to' => Builder::integer()->minimum(0)->maximum(5)->default(5),
        ]
      ),
    ]);
  }

  public function validate(StepValidationArgs $args): void {
    parent::validate($args);

    $ratingSetting = $this->getRatingSettings($args->getStep()->getArgs());
    if ($ratingSetting['is_active'] && !$this->wc->wcReviewRatingsEnabled()) {
      throw ValidationException::create()
        ->withError('rating', __("The WooCommerce rating system is not enabled.", 'mailpoet-premium'));
    }
  }

  public function getSubjectKeys(): array {
    return array_merge(parent::getSubjectKeys(), [ReviewSubject::KEY]);
  }

  protected function handleComment(\WP_Comment $comment): void {
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      new Subject(CommentSubject::KEY, ['comment_id' => $comment->comment_ID]),
      //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      new Subject(ReviewSubject::KEY, ['review_id' => $comment->comment_ID]),
    ]);
  }

  public function registerHooks(): void {
    parent::registerHooks();
    $this->wp->addAction(
      'woocommerce_rest_insert_product_review',
      [
        $this,
        'handleInsertReview',
      ],
      10,
      1
    );
    parent::registerHooks();
  }

  public function handleInsertReview($review): void {
    if (!$review instanceof \WP_Comment) {
      return;
    }
    $this->handleComment($review);
  }

  protected function validCommentType(): string {
    return 'review';
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    if (!parent::isTriggeredBy($args)) {
      return false;
    }

    $triggerArgs = $args->getStep()->getArgs();
    $ratingSetting = $this->getRatingSettings($triggerArgs);
    if (!$ratingSetting['is_active']) {
      return true;
    }

    $filter = Filter::fromArray([
      'id' => '',
      'field_type' => Field::TYPE_INTEGER,
      'field_key' => 'woocommerce:review:rating',
      'condition' => NumberFilter::CONDITION_BETWEEN,
      'args' => [
        'value' => [
          $ratingSetting['from'] - 1,
          $ratingSetting['to'] + 1,
        ],
      ],
    ]);

    $filterGroup = new FilterGroup('', FilterGroup::OPERATOR_AND, [$filter]);
    return $this->filterHandler->matchesGroup($filterGroup, $args);
  }

  protected function postFilterOperator(): string {
    return FilterGroup::OPERATOR_AND;
  }

  /**
   * @param array{rating?:int[]|bool[]} $args
   * @return array{is_active:bool, from:int, to:int}
   */
  private function getRatingSettings(array $args): array {
    $rating = [
      'is_active' => $args['rating']['is_active'] ?? false,
      'from' => $args['rating']['from'] ?? 0,
      'to' => $args['rating']['to'] ?? 5,
    ];

    return [
      'is_active' => (bool)$rating['is_active'],
      'from' => (int)$rating['from'],
      'to' => (int)$rating['to'],
    ];
  }
}
