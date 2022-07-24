<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Workflows\Action;
use MailPoet\Automation\Engine\Workflows\Step;
use MailPoet\Automation\Engine\Workflows\Subject;
use MailPoet\Automation\Engine\Workflows\Workflow;
use MailPoet\Automation\Engine\Workflows\WorkflowRun;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Statistics\Track\Unsubscribes;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;

class UnsubscribeAction implements Action {
  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var Unsubscribes */
  private $unsubscribesTracker;

  public function __construct(
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscribersRepository $subscribersRepository,
    TrackingConfig $trackingConfig,
    Unsubscribes $unsubscribesTracker
  ) {
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->trackingConfig = $trackingConfig;
    $this->unsubscribesTracker = $unsubscribesTracker;
  }

  public function getKey(): string {
    return 'mailpoet:unsubscribe';
  }

  public function getName(): string {
    return __('Unsubscribe', 'mailpoet');
  }

  /**
   * @param Subject[] $subjects
   */
  public function isValid(array $subjects, Step $step, Workflow $workflow): bool {
    $subscriberSubjects = array_filter($subjects, function (Subject $subject) {
      return $subject->getKey() === SubscriberSubject::KEY;
    });

    return count($subscriberSubjects) === 1;
  }

  public function run(Workflow $workflow, WorkflowRun $workflowRun, Step $step): void {
    $subscriberSubject = $workflowRun->requireSingleSubject(SubscriberSubject::class);
    $subscriber = $subscriberSubject->getSubscriber();

    if ($subscriber->getStatus() !== SubscriberEntity::STATUS_SUBSCRIBED) {
      throw InvalidStateException::create()->withMessage(sprintf("Cannot unsubscribe subscriber ID '%s' because their status is '%s'.", $subscriber->getId(), $subscriber->getStatus()));
    }

    if ($this->trackingConfig->isEmailTrackingEnabled()) {
      $meta = json_encode([
        'workflow' => $workflow->getId(),
        'workflow_run' => $workflowRun->getId(),
        'step' => $step->getId(),
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

    $this->subscriberSegmentRepository->unsubscribeFromSegments($subscriber);
  }
}
