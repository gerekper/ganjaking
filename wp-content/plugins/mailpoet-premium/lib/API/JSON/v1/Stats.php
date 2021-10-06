<?php

namespace MailPoet\Premium\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Newsletter\Url as NewsletterUrl;
use MailPoet\Premium\API\JSON\v1\ResponseBuilders\StatsResponseBuilder;
use MailPoet\Premium\Newsletter\StatisticsClicksRepository;
use MailPoet\Premium\Newsletter\Stats as CampaignStats;
use MailPoet\Premium\Newsletter\Stats\SubscriberEngagement;
use MailPoet\WP\Functions as WPFunctions;

class Stats extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  /** @var CampaignStats\PurchasedProducts */
  private $purchasedProducts;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterStatisticsRepository */
  private $newsletterStatisticsRepository;

  /** @var StatsResponseBuilder */
  private $statsResponseBuilder;

  /** @var StatisticsClicksRepository */
  private $statisticsClicksRepository;

  /** @var SubscriberEngagement */
  private $listings;

  /** @var NewsletterUrl */
  private $newsletterUrl;

  public function __construct(
    CampaignStats\PurchasedProducts $purchasedProducts,
    NewslettersRepository $newslettersRepository,
    StatsResponseBuilder $statsResponseBuilder,
    StatisticsClicksRepository $statisticsClicksRepository,
    SubscriberEngagement $listings,
    NewsletterStatisticsRepository $newsletterStatisticsRepository,
    NewsletterUrl $newsletterUrl
  ) {
    $this->purchasedProducts = $purchasedProducts;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
    $this->statsResponseBuilder = $statsResponseBuilder;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    $this->listings = $listings;
    $this->newsletterUrl = $newsletterUrl;
  }

  public function get($data = []) {
    $newsletter = isset($data['id'])
      ? $this->newslettersRepository->findOneById((int)$data['id'])
      : null;
    if (!$newsletter instanceof NewsletterEntity) {
      return $this->errorResponse(
        [
          APIError::NOT_FOUND => WPFunctions::get()->__('This newsletter does not exist.', 'mailpoet-premium'),
        ]
      );
    }
    $statistics = $this->newsletterStatisticsRepository->getStatistics($newsletter);

    if (!$this->isNewsletterSent($newsletter)) {
      return $this->errorResponse(
        [
          APIError::NOT_FOUND => WPFunctions::get()->__('This newsletter is not sent yet.', 'mailpoet-premium'),
        ]
      );
    }

    $clickedLinks = $this->statisticsClicksRepository->getClickedLinks($newsletter);
    $previewUrl = $this->newsletterUrl->getViewInBrowserUrl(
      (object)[
        'id' => $newsletter->getId(),
        'hash' => $newsletter->getHash(),
      ]
    );

    return $this->successResponse(
      $this->statsResponseBuilder->build(
        $newsletter,
        $statistics,
        $clickedLinks,
        $previewUrl
      )
    );
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
      'filters' => $listingData['filters'],
      'groups' => $listingData['groups'],
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

  /**
   * @param array<string, int> $data
   *
   * @return \MailPoet\API\JSON\SuccessResponse
   */
  public function getProducts(array $data = []) {
    $id = (isset($data['newsletter_id']) ? (int)$data['newsletter_id'] : false);
    return $this->successResponse($this->purchasedProducts->getStats($id));
  }
}
