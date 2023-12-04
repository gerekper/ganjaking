<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads\CustomDataPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<CustomDataPayload>
 */
class CustomDataSubject implements Subject {

  const KEY = 'mailpoet:custom-data';

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('Custom data', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'hook' => Builder::string()->required(),
      'data' => Builder::array(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $hook = $subjectData->getArgs()['hook'];
    $data = $subjectData->getArgs()['data'];
    return new CustomDataPayload($hook, $data);
  }

  public function getFields(): array {
    return [];
  }
}
