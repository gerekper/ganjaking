<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\InvalidStateException;
use MailPoet\Subscribers\SubscriberCustomFieldRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class UpdateSubscriberAction implements Action {
  /** @var SubscriberCustomFieldRepository */
  private $subscriberCustomFieldRepository;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository,
    SubscriberCustomFieldRepository $subscriberCustomFieldRepository
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
    $this->subscriberCustomFieldRepository = $subscriberCustomFieldRepository;
  }

  public function getKey(): string {
    return 'mailpoet:update-subscriber';
  }

  public function getName(): string {
    return __('Update subscriber', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    $id = Builder::integer()->required();

    $textual = Builder::object([
      'id' => $id,
      'type' => Builder::string()->required()->pattern('^(text|textarea|radio|select)$'),
      'value' => Builder::string()->required()->minLength(1),
    ]);

    $checkbox = Builder::object([
      'id' => $id,
      'type' => Builder::string()->required()->pattern('^checkbox$'),
      'value' => Builder::boolean()->required(),
    ]);

    $year = Builder::number()->required()->minimum(0);
    $month = Builder::number()->required()->minimum(1)->maximum(12);
    $day = Builder::number()->required()->minimum(1)->maximum(31);

    $date = Builder::object([
      'id' => $id,
      'type' => Builder::string()->required()->pattern('^date$'),
      'value' => Builder::oneOf([
        Builder::object([
          'date_type' => Builder::string()->required()->pattern('^year_month_day$'),
          'year' => $year,
          'month' => $month,
          'day' => $day,
        ]),
        Builder::object([
          'date_type' => Builder::string()->required()->pattern('^year_month$'),
          'year' => $year,
          'month' => $month,
        ]),
        Builder::object([
          'date_type' => Builder::string()->required()->pattern('^year$'),
          'year' => $year,
        ]),
        Builder::object([
          'date_type' => Builder::string()->required()->pattern('^month$'),
          'month' => $month,
        ]),
        Builder::object([
          'date_type' => Builder::string()->required()->pattern('^day'),
          'day' => $day,
        ]),
      ])->required(),
    ]);

    $field = Builder::oneOf([$textual, $checkbox, $date]);

    return Builder::object([
      'custom_fields' => Builder::array($field)->required()->minItems(1),
    ]);
  }

  public function getSubjectKeys(): array {
    return ['mailpoet:subscriber'];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    $subscriber = $args->getSinglePayloadByClass(SubscriberPayload::class)->getSubscriber();

    foreach ($args->getStep()->getArgs()['custom_fields'] ?? [] as $data) {
      $customField = $this->customFieldsRepository->findOneById($data['id']);
      if (!$customField) {
        throw InvalidStateException::create()->withMessage(
          // translators: %d is the ID of the custom field
          sprintf(__("Custom field with ID '%d' not found.", 'mailpoet-premium'), $data['id'])
        );
      }

      if ($customField->getType() !== $data['type']) {
        throw InvalidStateException::create()->withMessage(
          // translators: %1$s is name of the custom field, %2$d is its ID, %3$s is the expected type, %4$s is the actual type
          sprintf(
            __("Custom field '%1\$s' with ID '%2\$d' expects type '%3\$s' but type '%4\$s' was provided.", 'mailpoet-premium'),
            $customField->getName(),
            $customField->getId(),
            $customField->getType(),
            $data['type']
          )
        );
      }

      $subscriberCustomField = $this->subscriberCustomFieldRepository->findOneBy([
        'subscriber' => $subscriber,
        'customField' => $customField,
      ]);

      if ($subscriberCustomField) {
        $subscriberCustomField->setValue($data['value']);
      } else {
        $subscriberCustomField = new SubscriberCustomFieldEntity($subscriber, $customField, $data['value']);
        $this->subscriberCustomFieldRepository->persist($subscriberCustomField);
      }
    }

    $this->subscriberCustomFieldRepository->flush();
  }
}
