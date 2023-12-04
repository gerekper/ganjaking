<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads\CustomDataPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class CustomAction implements Action {
  public function run(StepRunArgs $args, StepRunController $controller): void {
    $step = $args->getStep();
    $hook = $step->getArgs()['hook'];
    $email = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber()->getEmail();
    $customData = [];
    try {
      $customData = $args->getSinglePayloadByClass(CustomDataPayload::class)->getData();
    } catch (\Throwable $e) {
      // do nothing
    }
    do_action($hook, $email, $customData);
  }

  public function getKey(): string {
    return 'mailpoet:custom-action';
  }

  public function getName(): string {
    // translators: automation action title
    return __('Custom action', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'hook' => Builder::string()->default('my_custom_hook')->minLength(1)->required(),
    ]);
  }

  public function getSubjectKeys(): array {
    return ['mailpoet:subscriber'];
  }

  public function validate(StepValidationArgs $args): void {
  }
}
