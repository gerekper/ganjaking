<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\AutomationTimeSpanController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage\SubscriberStatistics;

/**
 * @phpstan-import-type RawSubscriberType from SubscriberStatistics as RawSubscriberType
 * @phpstan-type ResultItem array{date: string, subscriber: array{id:int, email:string, first_name:string, last_name:string,avatar:string}, run: array{id:int, step: array{id:string, name:string }, status: string}}
 */
class SubscriberController {
  /** @var WordPress */
  private $wp;

  /** @var SubscriberStatistics */
  private $subscriberStatistics;

  /** AutomationTimeSpanController */
  private $automationTimeSpanController;

  /** @var Registry */
  private $registry;

  public function __construct(
    WordPress $wp,
    SubscriberStatistics $subscriberStatistics,
    AutomationTimeSpanController $automationTimeSpanController,
    Registry $registry
  ) {
    $this->wp = $wp;
    $this->subscriberStatistics = $subscriberStatistics;
    $this->automationTimeSpanController = $automationTimeSpanController;
    $this->registry = $registry;
  }

  /**
   * @param Automation $automation
   * @param Query $query
   * @return array{results:int,items: array<ResultItem>}
   * }
   */
  public function getSubscribersForAutomation(Automation $automation, Query $query): array {
    $automations = $this->automationTimeSpanController->getAutomationsInTimespan($automation, $query->getAfter(), $query->getBefore());
    if (!count($automations)) {
      return [
        'results' => 0,
        'items' => [],
        'steps' => (object)[],
      ];
    }

    $items = [];
    $steps = [];
    $data = $this->subscriberStatistics->getSubscribersForAutomations($automations, $query);
    foreach ($data as $item) {
      $step = $this->getStep($item['step_id'], $automations);
      if ($step) {
        $steps[$step->getId()] = $step->toArray();
      }
      $items[] = $this->mapResult($item, $step);
    }

    return [
      'results' => $this->subscriberStatistics->getLastCount($automations, $query),
      'items' => $items,
      'steps' => $steps,
    ];
  }

  /**
   * @param RawSubscriberType $result
   * @param Step|null $stepData
   * @return ResultItem
   */
  private function mapResult(array $result, ?Step $stepData): array {
    $updatedAt = new \DateTime($result['updated_at']);
    $updatedAt->setTimezone($this->wp->wpTimezone());

    $step = $stepData ? $this->registry->getStep($stepData->getKey()) : null;
    return [
      'date' => $updatedAt->format(\DateTimeImmutable::W3C),
      'subscriber' => [
        'id' => (int)$result['subscriber_id'],
        'email' => (string)$result['email'],
        'first_name' => (string)$result['first_name'],
        'last_name' => (string)$result['last_name'],
        'avatar' => (string)$this->wp->getAvatarUrl($result['email'], ['size' => 40]),
      ],
      'run' => [
        'id' => (int)$result['id'],
        'status' => (string)$result['status'],
        'step' => [
          'id' => (string)$result['step_id'],
          'name' => $step ? $step->getName() : '',
        ],
      ],
    ];
  }

  /** @param Automation[] $automations */
  private function getStep(string $stepId, array $automations): ?Step {
    foreach ($automations as $automation) {
      foreach ($automation->getSteps() as $stepData) {
        if ($stepData->getId() === $stepId) {
          return $stepData;
        }
      }
    }
    return null;
  }
}
