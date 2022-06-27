<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


class Hooks {
  public const INITIALIZE = 'mailpoet/automation/initialize';
  public const API_INITIALIZE = 'mailpoet/automation/api/initialize';
  public const STEP_RUNNER_INITIALIZE = 'mailpoet/automation/step_runner/initialize';
  public const TRIGGER = 'mailpoet/automation/trigger';
  public const WORKFLOW_STEP = 'mailpoet/automation/workflow/step';
}
