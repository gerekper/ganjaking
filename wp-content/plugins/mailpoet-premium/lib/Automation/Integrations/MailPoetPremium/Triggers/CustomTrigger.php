<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads\CustomDataPayload;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects\CustomDataSubject;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WP\Functions;

/**
 * @phpstan-type CustomData array{value: bool|float|int|string}
 */
class CustomTrigger implements Trigger {
  const KEY = 'mailpoet:custom-trigger';

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var Functions */
  private $wp;

  public function __construct(
    AutomationStorage $automationStorage,
    SubscribersRepository $subscribersRepository,
    Functions $wp
  ) {
    $this->automationStorage = $automationStorage;
    $this->subscribersRepository = $subscribersRepository;
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    return __('Custom trigger', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'hook' => Builder::string()->default('my_custom_hook')->minLength(1)->required(),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
      CustomDataSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  /**
   * @param string $hook
   * @param string $email
   * @param mixed[] $customData
   * @return void
   */
  public function handle(string $hook, string $email, array $customData = []) {
    if (!$this->wp->isEmail($email)) {
      return;
    }
    $subscriber = $this->subscribersRepository->findOneBy(['email' => $email]);
    if (!$subscriber) {
      $subscriber = new SubscriberEntity();
      $subscriber->setEmail($email);
      $this->subscribersRepository->persist($subscriber);
      $this->subscribersRepository->flush();
    }
    $customData = $this->sanitizeCustomData($customData);
    $subscriberSubject = new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]);
    $customDataSubject = new Subject(CustomDataSubject::KEY, ['hook' => $hook, 'data' => $customData]);
    $this->wp->doAction(
      Hooks::TRIGGER,
      $this,
      [
        $subscriberSubject,
        $customDataSubject,
      ]
    );
  }

  /**
   * @param mixed[] $customData
   * @return CustomData[]
   */
  private function sanitizeCustomData(array $customData): array {
    $sanitized = [];
    foreach ($customData as $key => $value) {
      if (
        !is_array($value)
        || !array_key_exists('value', $value)
        || !is_scalar($value['value'])
      ) {
        continue;
      }

      /**
       * Only a scalar value property is currently allowed to be
       * stored.
       */
      $sanitized[$key] = ['value' => $value['value']];
    }
    return $sanitized;
  }

  public function registerHooks(): void {
    $automations = $this->automationStorage->getActiveAutomationsByTrigger($this);
    $hooks = [];
    foreach ($automations as $automation) {
      $trigger = $automation->getTrigger(self::KEY);
      if (!$trigger) {
        continue;
      }
      $hooks[] = $trigger->getArgs()['hook'];
    }
    $hooks = array_unique($hooks);
    foreach ($hooks as $hook) {
      add_action($hook, function (string $email, array $customData = []) use ($hook) {
        $this->handle($hook, $email, $customData);
      }, 10, 2);
    }
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $customData = $args->getSingleSubjectEntry(CustomDataSubject::KEY);
    $trigger = $args->getAutomation()->getTrigger(self::KEY);
    if (!$trigger) {
      return false;
    }
    $hook = $trigger->getArgs()['hook'];

    $payload = $customData->getPayload();
    return $payload instanceof CustomDataPayload && $payload->getHook() === $hook;
  }
}
