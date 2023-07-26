<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\AutomationTimeSpanController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\SubscriberController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage\SubscriberStatistics;

/**
 * @phpstan-import-type RawSubscriberType from SubscriberStatistics as RawSubscriberType
 * @phpstan-type ResultItem array{date: string, subscriber: array{id:int, email:string, first_name:string, last_name:string,avatar:string}, run: array{id:int, step: array{id:string, name:string }, status: string}}
 */

class PremiumSubscriberController implements SubscriberController {


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
      ];
    }
    $results = $this->subscriberStatistics->getSubscribersForAutomations($automations, $query);

    return [
      'results' => $this->subscriberStatistics->getLastCount($automations, $query),
      'items' => array_map(function($result) use ($automations) {
        return $this->mapResult($result, $automations);
      }, $results),
    ];
  }

  /**
   * @param RawSubscriberType $result
   * @param Automation[] $automations
   * @return ResultItem
   */
  private function mapResult(array $result, array $automations): array {

    $updatedAt = new \DateTime($result['updated_at']);
    $updatedAt->setTimezone($this->wp->wpTimezone());

    return [
      'date' => $updatedAt->format(\DateTimeImmutable::W3C),
      'subscriber' => [
        'id' => (int)$result['subscriber_id'],
        'email' => (string)$result['email'],
        'first_name' => (string)$result['first_name'],
        'last_name' => (string)$result['last_name'],
        'avatar' => (string)$this->wp->getAvatarUrl($result['email'], ['size' => 20]),
      ],
      'run' => [
        'id' => (int)$result['id'],
        'status' => (string)$result['status'],
        'step' => [
          'id' => (string)$result['step_id'],
          'name' => $this->getNameForStep($result['step_id'], $automations),
        ],
      ],
    ];
  }

  /**
   * @param string $stepId
   * @param Automation[] $automations
   * @return string
   */
  private function getNameForStep(string $stepId, array $automations): string {
    foreach ($automations as $automation) {
      foreach ($automation->getSteps() as $stepData) {
        if ($stepData->getId() === $stepId) {
          $step = $this->registry->getStep($stepData->getKey());
          return $step ? $step->getName() : '';
        }
      }
    }
    return '';
  }
}
