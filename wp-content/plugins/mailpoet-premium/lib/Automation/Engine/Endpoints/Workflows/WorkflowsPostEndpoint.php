<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Endpoints\Workflows;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Data\NextStep;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\Workflow;
use MailPoet\Automation\Engine\Validation\WorkflowSchema;
use MailPoet\Premium\Automation\Engine\Builder\CreateWorkflowController;
use MailPoet\Validator\Builder;

class WorkflowsPostEndpoint extends Endpoint {
  /** @var CreateWorkflowController */
  private $createController;

  public function __construct(
    CreateWorkflowController $createController
  ) {
    $this->createController = $createController;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    $workflow = $this->createController->createWorkflow($data);
    return new Response($this->buildWorkflow($workflow));
  }

  /**
   * @return array<string,mixed>
   */
  private function buildWorkflow(Workflow $workflow): array {
    return [
      'id' => $workflow->getId(),
      'name' => $workflow->getName(),
      'status' => $workflow->getStatus(),
      'created_at' => $workflow->getCreatedAt()->format(DateTimeImmutable::W3C),
      'updated_at' => $workflow->getUpdatedAt()->format(DateTimeImmutable::W3C),
      'activated_at' => $workflow->getActivatedAt() ? $workflow->getActivatedAt()->format(DateTimeImmutable::W3C) : null,
      'author' => [
        'id' => $workflow->getAuthor()->ID,
        'name' => $workflow->getAuthor()->display_name,
      ],
      'steps' => array_map(function (Step $step) {
        return [
          'id' => $step->getId(),
          'type' => $step->getType(),
          'key' => $step->getKey(),
          'args' => $step->getArgs(),
          'next_steps' => array_map(function (NextStep $nextStep) {
            return $nextStep->toArray();
          }, $step->getNextSteps()),
        ];
      }, $workflow->getSteps()),
    ];
  }

  public static function getRequestSchema(): array {
    return [
      'name' => Builder::string()->required(),
      'steps' => WorkflowSchema::getStepsSchema(),
    ];
  }
}
