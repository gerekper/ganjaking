<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Builder\UpdateAutomationController as FreePluginUpdateAutomationController;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Integration\Step;

class UpdateAutomationController extends FreePluginUpdateAutomationController {
  /** @param Step[] $steps */
  protected function validateAutomationSteps(Automation $automation, array $steps): void {
    // allow full automation eiditing
  }
}
