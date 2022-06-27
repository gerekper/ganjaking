<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Control\Steps;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Workflows\Step;
use MailPoet\Automation\Engine\Workflows\StepRunner;
use MailPoet\Automation\Engine\Workflows\Workflow;
use MailPoet\Automation\Engine\Workflows\WorkflowRun;
use MailPoet\Premium\Automation\Engine\Workflows\ConditionalStep;

class ConditionalStepRunner implements StepRunner {
  public function run(Step $step, Workflow $workflow, WorkflowRun $workflowRun): void {
    if (!$step instanceof ConditionalStep) {
      throw new InvalidStateException('$step should be an instance of ConditionalStep');
    }
    $step->setSubjects($workflowRun->getSubjects());
    $step->determineNextStepId();
  }
}
