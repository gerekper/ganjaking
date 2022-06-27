<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Workflows\Step;
use MailPoet\Automation\Engine\Workflows\Subject;

class ConditionalStep extends Step {
  public const TYPE_CONDITIONAL = 'conditional';

  /** @var array<string, Subject> */
  private $subjects;

  /** @param array<string, Subject> $subjects */
  public function setSubjects(array $subjects) {
    $this->subjects = $subjects;
  }

  public function determineNextStepId() {
    $result = $this->checkCondition();
    $this->nextStepId = $result ? $this->args['next_step_id_1'] : $this->args['next_step_id_2'];
    return $result;
  }

  private function checkCondition() {
    if (empty($this->subjects[$this->args['operand1']])) {
      throw InvalidStateException::create()->withMessage(sprintf("Subject with key '%s' not found.", $this->args['operand1']));
    }
    $operand1 = $this->subjects[$this->args['operand1']];
    $operand2 = $this->args['operand2'];
    // phpcs:ignore Squiz.PHP.Eval.Discouraged
    return eval('$operand1' . $this->args['operator'] . '$operand2');
  }
}
