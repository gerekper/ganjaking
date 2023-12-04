<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Payloads\NewsletterLinkPayload;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\NewsletterLinkSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Premium\Newsletter\StatisticsClicksRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class ClicksEmailLinkTrigger implements Trigger {

  const KEY = 'mailpoet:clicks-email-link';

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  /** @var StatisticsClicksRepository */
  private $statisticsClicksRepository;

  /** @var WordPress */
  private $wp;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    NewsletterLinkRepository $newsletterLinkRepository,
    StatisticsClicksRepository $statisticsClicksRepository,
    WordPress $wp
  ) {
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Subscriber clicks a link in email', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object(
      [
        'newsletter_id' => Builder::integer()->default(0)->minimum(1)->required(),
        'link_ids' => Builder::array(Builder::number())->minItems(1)->default([])->required(),
        'operator' => Builder::string()->required()->default('any'),
      ]
    );
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
      NewsletterLinkSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
    $newsletterId = $args->getStep()->getArgs()['newsletter_id'];
    if (!$newsletterId) {
      throw InvalidStateException::create()->withMessage(
        __('Please select a newsletter.', 'mailpoet-premium')
      );
    }
    $newsletter = $this->newslettersRepository->findOneById($newsletterId);
    if (!$newsletter) {
      throw InvalidStateException::create()->withMessage(
      // translators: %d is the ID of the segment
        sprintf(__("Newsletter with ID '%d' not found.", 'mailpoet-premium'), $newsletterId)
      );
    }

    $operator = $args->getStep()->getArgs()['operator'];
    if (!in_array($operator, ['all', 'any', 'none'])) {
      throw InvalidStateException::create()->withMessage(
        __('Please select an operator.', 'mailpoet-premium')
      );
    }

    $links = $args->getStep()->getArgs()['link_ids'];
    foreach ($links as $link) {
      $linkEntity = $this->newsletterLinkRepository->findOneById($link);
      if (!$linkEntity) {
        throw InvalidStateException::create()->withMessage(
          // translators: %d is the ID of the segment
          sprintf(__("Link with ID '%d' not found.", 'mailpoet-premium'), $link)
        );
      }
    }
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'mailpoet_link_clicked',
      [
        $this,
        'handle',
      ],
      10,
      3
    );
  }

  public function handle(NewsletterLinkEntity $link, SubscriberEntity $subscriber, bool $isPreview): void {

    if ($isPreview) {
      return;
    }

    $this->wp->doAction(
      Hooks::TRIGGER,
      $this,
      [
        new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]),
        new Subject(NewsletterLinkSubject::KEY, ['link_id' => $link->getId()]),
      ]
    );
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $operator = $args->getStep()->getArgs()['operator'];
    $linkIds = $args->getStep()->getArgs()['link_ids'];
    $newsletterId = $args->getStep()->getArgs()['newsletter_id'];

    $subscriberPayload = $args->getSinglePayloadByClass(SubscriberPayload::class);
    $linkPayload = $args->getSinglePayloadByClass(NewsletterLinkPayload::class);

    if (
      !$linkPayload->getLink()->getNewsletter() ||
      $newsletterId !== $linkPayload->getLink()->getNewsletter()->getId()
    ) {
      return false;
    }

    $linkId = $linkPayload->getLink()->getId();
    if ($operator === 'any') {
      return in_array($linkId, $linkIds);
    } elseif ($operator === 'none') {
      return !in_array($linkId, $linkIds);
    }

    $allLinkClicks = $this->statisticsClicksRepository->findBy([
      'subscriber' => $subscriberPayload->getSubscriber(),
      'link' => $linkIds,
    ]);
    $clickedLinkIds = array_filter(array_map(
      function(StatisticsClickEntity $link) {
        return $link->getLink() ? $link->getLink()->getId() : null;
      },
      $allLinkClicks
    ));

    return count(array_diff($linkIds, $clickedLinkIds)) === 0;
  }
}
