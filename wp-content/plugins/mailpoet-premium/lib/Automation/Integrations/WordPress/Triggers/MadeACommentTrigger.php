<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WordPress\Triggers;

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
use MailPoet\Automation\Integrations\Core\Filters\EnumFilter;
use MailPoet\Automation\Integrations\Core\Filters\StringFilter;
use MailPoet\Automation\Integrations\WordPress\Payloads\CommentPayload;
use MailPoet\Automation\Integrations\WordPress\Subjects\CommentSubject;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class MadeACommentTrigger implements Trigger {
  const KEY = 'wordpress:made-a-comment';

  /** @var WordPress */
  protected $wp;

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  /** @var FilterHandler  */
  protected $filterHandler;

  public function __construct(
    WordPress $wp,
    AutomationRunStorage $automationRunStorage,
    FilterHandler $filterHandler
  ) {
    $this->wp = $wp;
    $this->automationRunStorage = $automationRunStorage;
    $this->filterHandler = $filterHandler;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('User makes a comment', 'mailpoet-premium');
  }

  public function getSubjectKeys(): array {
    return [
      CommentSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
    $postIds = $args->getStep()->getArgs()['post_ids'] ?? [];
    $postIds = array_map('absint', $postIds);
    $postTypes = $args->getStep()->getArgs()['post_types'] ?? [];
    $terms = $args->getStep()->getArgs()['terms'] ?? [];

    if ($postTypes) {
      $this->validatePostTypes($postTypes);
    }

    if ($postIds) {
      $this->validatePostIds($postIds);
    }

    if ($terms) {
      $this->validateTerms($terms);
    }
  }

  /**
   * @param string[] $postTypes
   * @return void
   * @throws ValidationException
   */
  private function validatePostTypes(array $postTypes) {
    $registeredPostTypes = $this->wp->getPostTypes();
    foreach ($postTypes as $postType) {
      if (!in_array($postType, $registeredPostTypes, true)) {
        throw ValidationException::create()
          // translators: %s is the name of the post type.
          ->withError('post_type', sprintf(__("Post Type “%s” is not registered.", 'mailpoet-premium'), $postType));
      }
    }
  }

  /**
   * @param int[] $postIds
   * @return void
   * @throws ValidationException
   */
  private function validatePostIds(array $postIds): void {
    $foundPostIds = (new \WP_Query([
      'post__in' => $postIds,
      'post_type' => 'any',
      'post_status' => 'any',
      'ignore_sticky_posts' => 1,
      'fields' => 'ids',
    ]))->get_posts();
    $foundPostIds = array_map('absint', $foundPostIds);
    foreach ($postIds as $postId) {
      if (!in_array($postId, $foundPostIds, true)) {
        throw ValidationException::create()
          // translators: %d is the ID of the post not found.
          ->withError('post_ids', sprintf(__("Post with ID '%d' not found.", 'mailpoet-premium'), $postId));
      }
    }
  }

  /**
   * @param int[][] $termTaxonomies
   * @return void
   * @throws ValidationException
   */
  private function validateTerms(array $termTaxonomies) {

    foreach ($termTaxonomies as $taxonomyName => $terms) {
      if (!$terms) {
        continue;
      }
      $taxonomy = $this->wp->getTaxonomy($taxonomyName);
      if (!$taxonomy) {
        throw ValidationException::create()
          ->withError(
            'terms',
            sprintf(
            // translators: %s is the name of the taxonomy
              __('The taxonomy “%s” is not registered.', 'mailpoet-premium'),
              $taxonomyName
            )
          );
      }
    }
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'transition_comment_status',
      [$this, 'handleCommentTransition'],
      10,
      3
    );

    $this->wp->addAction(
      'wp_insert_comment',
      [$this, 'handleCommentCreation'],
      20
    );

    $this->wp->addAction(
        'comment_post',
        [$this, 'handleCommentCreation'],
        20
    );

  }

  public function handleCommentTransition($oldStatus, $newStatus, $comment): void {
    if (!$comment instanceof \WP_Comment) {
      return;
    }
    $this->handleComment($comment);
  }

  public function handleCommentCreation($id): void {
    $comment = $this->wp->getComment($id);
    if (!$comment instanceof \WP_Comment) {
      return;
    }
    $this->handleComment($comment);
  }

  protected function handleComment(\WP_Comment $comment): void {
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      new Subject(CommentSubject::KEY, ['comment_id' => $comment->comment_ID]),
    ]);
  }

  protected function validCommentType(): string {
    return 'comment';
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $commentFilters = $this->getCommentFilters($args);
    $commentGroup = new FilterGroup(
      '',
      FilterGroup::OPERATOR_AND,
      $commentFilters
    );
    if (!$this->filterHandler->matchesGroup($commentGroup, $args)) {
      return false;
    }

    $postFilters = $this->getPostFilters($args);
    $postGroup = new FilterGroup(
      '',
      $this->postFilterOperator(),
      $postFilters
    );
    if ($postFilters && !$this->filterHandler->matchesGroup($postGroup, $args)) {
      return false;
    }

    return $this->applyTermCondition($args) && $this->applyRunOnlyOncePerCommentCondition($args);
  }

  protected function postFilterOperator(): string {
    return FilterGroup::OPERATOR_OR;
  }

  private function applyTermCondition(StepRunArgs $args): bool {

    $comment = $args->getSinglePayloadByClass(CommentPayload::class)->getComment();
    if (!$comment) {
      return false;
    }
    //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $post = $this->wp->getPost((int)$comment->comment_post_ID);
    if (!$post instanceof \WP_Post) {
      return false;
    }

    $triggerArgs = $args->getStep()->getArgs();
    $terms = array_filter(
      $triggerArgs['terms'] ?? [],
      function($termIds): bool {
        return count($termIds) > 0;
      }
    );
    $termsMatch = count($terms) === 0;
    foreach ($terms as $taxonomy => $termIds) {
      /** @var array<int, int> $attachedTermIds */
      $attachedTermIds = $this->wp->wpGetPostTerms($post->ID, $taxonomy, ['fields' => 'ids']);
      if (array_intersect($termIds, $attachedTermIds)) {
        $termsMatch = true;
      }
    }
    return $termsMatch;
  }

  private function applyRunOnlyOncePerCommentCondition(StepRunArgs $args): bool {
    // Each comment can only trigger the automation once
    $subject = $args->getSingleSubjectEntry(CommentSubject::KEY)->getSubjectData();
    $existingRuns = $this->automationRunStorage->getCountByAutomationAndSubject(
      $args->getAutomation(),
      $subject
    );
    return $existingRuns === 0;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'comment_statuses' => Builder::array(Builder::string())->default([]),
      'post_types' => Builder::array(Builder::string())->default([]),
      'post_ids' => Builder::array(Builder::integer())->default([]),
      'terms' => $this->termsBuilder(),
    ]);
  }

  protected function termsBuilder(): ObjectSchema {
    /** @var string[] $taxonomies */
    $taxonomies = $this->wp->getTaxonomies();
    $object = [];
    foreach ($taxonomies as $taxonomy) {
      $object[$taxonomy] = Builder::array(Builder::integer())->default([]);
    }
    return Builder::object($object);
  }

  /**
   * @return Filter[]
   */
  protected function getCommentFilters(StepRunArgs $args): array {
    $triggerArgs = $args->getStep()->getArgs();
    $filters = [
      Filter::fromArray([
        'id' => '',
        'field_type' => Field::TYPE_STRING,
        'field_key' => 'wordpress:comment:comment-type',
        'condition' => StringFilter::CONDITION_IS,
        'args' => [
          'value' => $this->validCommentType(),
        ],
      ]),
    ];

    if (count($triggerArgs['comment_statuses'] ?? []) > 0) {
      $filters[] = Filter::fromArray([
        'id' => '',
        'field_type' => Field::TYPE_ENUM,
        'field_key' => 'wordpress:comment:status',
        'condition' => EnumFilter::IS_ANY_OF,
        'args' => [
          'value' => $triggerArgs['comment_statuses'] ?? [],
        ],
      ]);
    }
    return $filters;
  }

  /**
   * @return Filter[]
   */
  protected function getPostFilters(StepRunArgs $args): array {
    $triggerArgs = $args->getStep()->getArgs();
    $filters = [];

    if (count($triggerArgs['post_ids'] ?? []) > 0) {
      $filters[] = Filter::fromArray([
      'id' => '',
      'field_type' => Field::TYPE_ENUM,
      'field_key' => 'wordpress:post:id',
      'condition' => EnumFilter::IS_ANY_OF,
      'args' => [
        'value' => $triggerArgs['post_ids'],
      ],
      ]);
    }
    if (count($triggerArgs['post_types'] ?? []) > 0) {
      $filters[] = Filter::fromArray([
        'id' => '',
        'field_type' => Field::TYPE_ENUM,
        'field_key' => 'wordpress:post:type',
        'condition' => EnumFilter::IS_ANY_OF,
        'args' => [
          'value' => $triggerArgs['post_types'],
        ],
      ]);
    }
    return $filters;
  }
}
