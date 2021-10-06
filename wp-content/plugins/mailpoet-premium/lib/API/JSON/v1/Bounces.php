<?php

namespace MailPoet\Premium\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Premium\Newsletter\Stats\Bounces as BouncesStats;
use MailPoet\WP\Functions as WPFunctions;

class Bounces extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var BouncesStats */
  private $listings;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    BouncesStats $listings
  ) {
    $this->newslettersRepository = $newslettersRepository;
    $this->listings = $listings;
  }

  public function listing($data = []) {
    $newsletter = isset($data['params']['id'])
      ? $this->newslettersRepository->findOneById((int)$data['params']['id'])
      : null;
    if (!$newsletter instanceof NewsletterEntity) {
      return $this->errorResponse([
        APIError::NOT_FOUND => WPFunctions::get()->__('This newsletter does not exist.', 'mailpoet-premium'),
      ]);
    }

    if (!$this->isNewsletterSent($newsletter)) {
      return $this->errorResponse(
        [
          APIError::NOT_FOUND => WPFunctions::get()->__('This newsletter is not sent yet.', 'mailpoet-premium'),
        ]
      );
    }

    $listingData = $this->listings->get($data);

    foreach ($listingData['items'] as &$item) {
      $item['subscriber_url'] = WPFunctions::get()->adminUrl(
        'admin.php?page=mailpoet-subscribers#/edit/' . $item['subscriber_id']
      );
    }
    unset($item);

    return $this->successResponse($listingData['items'], [
      'count' => (int)$listingData['count'],
    ]);
  }

  public function isNewsletterSent(NewsletterEntity $newsletter): bool {
    // for statistics purposes, newsletter (except for welcome notifications) is sent
    // when it has a queue record and it's status is not scheduled
    $queue = $newsletter->getLatestQueue();
    if (!$queue) return false;
    $task = $queue->getTask();
    if (!$task) return false;

    if (
      ($newsletter->getType() === NewsletterEntity::TYPE_WELCOME)
      || ($newsletter->getType() === NewsletterEntity::TYPE_AUTOMATIC)
    ) return true;

    return $task->getStatus() !== ScheduledTaskEntity::STATUS_SCHEDULED;
  }
}
