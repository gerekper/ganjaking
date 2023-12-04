<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Entities\SegmentEntity;
use MailPoet\InvalidStateException;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class RemoveFromListAction implements Action {
  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  public function __construct(
    SegmentsRepository $segmentsRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository
  ) {
    $this->segmentsRepository = $segmentsRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
  }

  public function getKey(): string {
    return 'mailpoet:remove-from-list';
  }

  public function getName(): string {
    // translators: automation action title
    return __('Remove from list', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'segment_ids' => Builder::array(Builder::number())->required()->minItems(1)->default([]),
    ]);
  }

  public function getSubjectKeys(): array {
    return ['mailpoet:subscriber'];
  }

  public function validate(StepValidationArgs $args): void {
    $segmentIds = $args->getStep()->getArgs()['segment_ids'] ?? [];
    $this->getSegments($segmentIds);
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    $subscriber = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber();
    $segmentIds = $args->getStep()->getArgs()['segment_ids'] ?? [];
    $segments = $this->getSegments($segmentIds);
    foreach ($segments as $segment) {
      $subscriberSegment = $this->subscriberSegmentRepository->findOneBy([
        'subscriber' => $subscriber,
        'segment' => $segment,
      ]);

      if ($subscriberSegment) {
        $subscriber->getSubscriberSegments()->removeElement($subscriberSegment);
        $this->subscriberSegmentRepository->remove($subscriberSegment);
      }
    }
    $this->subscriberSegmentRepository->flush();
  }

  /**
   * @param string[] $segmentIds
   * @return SegmentEntity[]
   */
  private function getSegments(array $segmentIds): array {
    $segments = [];
    foreach ($segmentIds as $segmentId) {
      $segment = $this->segmentsRepository->findOneById($segmentId);
      if (!$segment) {
        throw InvalidStateException::create()->withMessage(
          // translators: %d is the ID of the segment
          sprintf(__("Segment with ID '%s' not found.", 'mailpoet-premium'), $segmentId)
        );
      }
      $segments[] = $segment;
    }
    return $segments;
  }
}
