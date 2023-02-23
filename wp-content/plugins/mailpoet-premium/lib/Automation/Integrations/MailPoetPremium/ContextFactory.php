<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;

class ContextFactory {
  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
  }

  /** @return mixed[] */
  public function getContextData(): array {
    return [
      'custom_fields' => array_map(function (CustomFieldEntity $customField) {
        return $this->buildCustomField($customField);
      }, $this->customFieldsRepository->findAll()),
    ];
  }

  /** @return mixed[] */
  private function buildCustomField(CustomFieldEntity $customField): array {
    $params = $customField->getParams() ?? [];
    $data = [
      'id' => $customField->getId(),
      'name' => $customField->getName(),
      'type' => $customField->getType(),
      'params' => [
        'label' => $params['label'] ?? '',
        'required' => (bool)($params['required'] ?? false),
      ],
    ];

    if (isset($params['validate']) && $params['validate'] !== '') {
      $data['params']['validate'] = $params['validate'];
    }

    if (isset($params['values'])) {
      $data['params']['values'] = array_map(function (array $value) {
        return [
          'value' => $value['value'],
          'is_checked' => (bool)$value['is_checked'],
        ];
      }, $params['values']);
    }

    if (isset($params['date_type'])) {
      $data['params']['date_type'] = $params['date_type'];
    }

    if (isset($params['date_format'])) {
      $data['params']['date_format'] = $params['date_format'];
    }

    return $data;
  }
}
