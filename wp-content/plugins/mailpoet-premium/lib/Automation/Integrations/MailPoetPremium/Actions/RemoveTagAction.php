<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Subscribers\SubscriberTagRepository;
use MailPoet\Tags\TagRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class RemoveTagAction implements Action {

  /** @var SubscriberTagRepository  */
  private $subscriberTagRepository;

  /** @var TagRepository */
  private $tagRepository;

  /** @var WordPress */
  private $wp;

  public function __construct(
    SubscriberTagRepository $subscriberTagRepository,
    TagRepository $tagRepository,
    WordPress $wp
  ) {
    $this->subscriberTagRepository = $subscriberTagRepository;
    $this->tagRepository = $tagRepository;
    $this->wp = $wp;
  }

  public function getKey(): string {
    return 'mailpoet:remove-tag';
  }

  public function getName(): string {
    return __('Remove tag', 'mailpoet-premium');
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {

    $subscriber = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber();
    $tags = $args->getStep()->getArgs()['tag_ids'] ?? [];

    $removedTags = [];
    foreach ($tags as $id) {
      $tag = $this->tagRepository->findOneById($id);
      if (!$tag) {
        continue;
      }
      $subscriberTag = $subscriber->getSubscriberTag($tag);
      if (!$subscriberTag) {
        continue;
      }
      $this->subscriberTagRepository->remove($subscriberTag);
      $removedTags[] = $subscriberTag;
    }
    $this->subscriberTagRepository->flush();

    $skipTriggers = $args->getStep()->getArgs()['skip_triggers'] ?? false;
    if ($skipTriggers) {
      return;
    }
    foreach ($removedTags as $tag) {
      $this->wp->doAction('mailpoet_subscriber_tag_removed', $tag);
    }
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'tag_ids' => Builder::array(Builder::number())->minItems(1)->required(),
      'skip_triggers' => Builder::boolean()->required()->default(false),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {

  }
}
