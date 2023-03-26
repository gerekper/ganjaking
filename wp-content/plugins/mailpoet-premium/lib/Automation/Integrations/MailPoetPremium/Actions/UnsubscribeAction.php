<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Statistics\Track\Unsubscribes;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class UnsubscribeAction implements Action {

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var Unsubscribes */
  private $unsubscribesTracker;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    TrackingConfig $trackingConfig,
    Unsubscribes $unsubscribesTracker
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->trackingConfig = $trackingConfig;
    $this->unsubscribesTracker = $unsubscribesTracker;
  }

  public function getKey(): string {
    return 'mailpoet:unsubscribe';
  }

  public function getName(): string {
    return __('Unsubscribe', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
    // TODO: we may want to add some checks here
  }

  public function run(StepRunArgs $args): void {
    $subscriberId = $args->getSinglePayloadByClass(SubscriberPayload::class)->getId();
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber) {
      throw InvalidStateException::create()->withMessage(sprintf("subscriber with ID '%s' not found", $subscriberId));
    }

    if ($this->trackingConfig->isEmailTrackingEnabled()) {
      $meta = json_encode([
        'automation' => $args->getAutomation()->getId(),
        'automation_run' => $args->getAutomationRun()->getId(),
        'step' => $args->getStep()->getId(),
      ]);
      $this->unsubscribesTracker->track(
        (int)$subscriber->getId(),
        StatisticsUnsubscribeEntity::SOURCE_AUTOMATION,
        null,
        $meta ?: null
      );
    }

    $subscriber->setStatus(SubscriberEntity::STATUS_UNSUBSCRIBED);
    $this->subscribersRepository->persist($subscriber);
    $this->subscribersRepository->flush();
  }
}
