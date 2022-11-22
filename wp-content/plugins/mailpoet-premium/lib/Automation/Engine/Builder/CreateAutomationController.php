<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\Validation\AutomationValidator;

class CreateAutomationController {
  /** @var AutomationStorage */
  private $storage;

  /** @var AutomationValidator */
  private $automationValidator;

  public function __construct(
    AutomationStorage $storage,
    AutomationValidator $automationValidator
  ) {
    $this->storage = $storage;
    $this->automationValidator = $automationValidator;
  }

  public function createAutomation(array $data): Automation {
    $steps = [];
    foreach ($data['steps'] as $index => $step) {
      $steps[(string)$index] = Step::fromArray($step);
    }

    $automation = new Automation($data['name'], $steps, wp_get_current_user());
    $this->automationValidator->validate($automation);
    $automationId = $this->storage->createAutomation($automation);
    $automation = $this->storage->getAutomation($automationId);
    if (!$automation) {
      throw new InvalidStateException("Could not find automation $automationId");
    }
    return $automation;
  }
}
