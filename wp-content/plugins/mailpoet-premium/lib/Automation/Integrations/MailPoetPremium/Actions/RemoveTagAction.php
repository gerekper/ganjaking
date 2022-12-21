<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
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

  public function __construct(
    SubscriberTagRepository $subscriberTagRepository,
    TagRepository $tagRepository
  ) {
    $this->subscriberTagRepository = $subscriberTagRepository;
    $this->tagRepository = $tagRepository;
  }

  public function getKey(): string {
    return 'mailpoet:remove-tag';
  }

  public function getName(): string {
    return __('Remove tag', 'mailpoet-premium');
  }

  public function run(StepRunArgs $args): void {

    $subscriber = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber();
    $tags = $args->getStep()->getArgs()['tag_ids'] ?? [];

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
    }
    $this->subscriberTagRepository->flush();
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'tag_ids' => Builder::array(Builder::number())->minItems(1)->required(),
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
