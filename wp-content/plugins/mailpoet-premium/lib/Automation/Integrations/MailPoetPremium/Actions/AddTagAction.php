<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Exceptions\RuntimeException;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\Subscribers\SubscriberTagRepository;
use MailPoet\Tags\TagRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class AddTagAction implements Action {

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
    return 'mailpoet:add-tag';
  }

  public function getName(): string {
    return __('Add tag', 'mailpoet-premium');
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    $subscriber = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber();

    $tags = $args->getStep()->getArgs()['tag_ids'] ?? [];

    $tagsAdded = [];
    foreach ($tags as $tagId) {
      $tag = $this->tagRepository->findOneById((int)$tagId);
      if (!$tag) {
          throw new RuntimeException(
              // translators: %d is the ID of the tag, which was not found.
              sprintf(__("Tag %d was not found.", 'mailpoet-premium'), $tagId)
          );
      }
      if ($subscriber->getSubscriberTag($tag)) {
        continue;
      }
      $subscriberTagEntity = new SubscriberTagEntity($tag, $subscriber);
      $this->subscriberTagRepository->persist($subscriberTagEntity);
      $tagsAdded[] = $subscriberTagEntity;
    }
    $this->subscriberTagRepository->flush();

    $skipTriggers = $args->getStep()->getArgs()['skip_triggers'] ?? false;
    if ($skipTriggers) {
      return;
    }
    foreach ($tagsAdded as $tag) {
      $this->wp->doAction('mailpoet_subscriber_tag_added', $tag);
    }
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'tag_ids' => Builder::array(Builder::integer())->minItems(1)->required(),
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
