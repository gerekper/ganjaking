<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\NotFoundException;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads\TagPayload;
use MailPoet\Tags\TagRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<TagPayload>
 */
class TagSubject implements Subject {
  const KEY = 'mailpoet:tag';

  /** @var TagRepository */
  private $tagRepository;

  public function __construct(
    TagRepository $tagRepository
  ) {
    $this->tagRepository = $tagRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    return __('MailPoet tag', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'tag_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['tag_id'];
    $tag = $this->tagRepository->findOneById($id);
    if (!$tag) {
      // translators: %d is the ID.
      throw NotFoundException::create()->withMessage(sprintf(__("Tag with ID '%d' not found.", 'mailpoet-premium'), $id));
    }
    return new TagPayload($tag);
  }

  /** @return Field[] */
  public function getFields(): array {
    return [];
  }
}
