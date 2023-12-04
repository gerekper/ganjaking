<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\InvalidStateException;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads\TagPayload;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects\TagSubject;
use MailPoet\Tags\TagRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class TagRemovedTrigger implements Trigger {
  public const KEY = 'mailpoet:subscriber-tag-removed';

  /** @var WordPress  */
  private $wp;

  /** @var TagRepository  */
  private $tagRepository;

  public function __construct(
    WordPress $wp,
    TagRepository $tagRepository
  ) {
    $this->wp = $wp;
    $this->tagRepository = $tagRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Tag removed', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'tag_ids' => Builder::array(Builder::number()),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
      TagSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction('mailpoet_subscriber_tag_removed', [$this, 'handleTagRemoved']);
  }

  public function handleTagRemoved(SubscriberTagEntity $entity) {
    $tag = $entity->getTag();
    $subscriber = $entity->getSubscriber();
    if (!$tag || !$subscriber || !$tag->getId() || !$subscriber->getId()) {
      throw new InvalidStateException();
    }
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(TagSubject::KEY, ['tag_id' => $tag->getId()]),
      new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {

    $tagId = $args->getSinglePayloadByClass(TagPayload::class)->getId();
    $tag = $this->tagRepository->findOneById($tagId);
    if (!$tag) {
      return false;
    }

    // Triggers when no tag IDs defined (= any tag) or the current tag payload matches the defined tags.
    $triggerArgs = $args->getStep()->getArgs();
    $tagIds = $triggerArgs['tag_ids'] ?? [];
    return !is_array($tagIds) || !$tagIds || in_array($tagId, $tagIds, true);
  }
}
