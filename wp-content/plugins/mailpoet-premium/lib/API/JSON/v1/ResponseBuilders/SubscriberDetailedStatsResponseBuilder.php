<?php

namespace MailPoet\Premium\API\JSON\v1\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Newsletter\Url as NewsletterUrl;
use MailPoet\Premium\Subscriber\Stats\SubscriberNewsletterStats;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoet\WP\Functions as WPFunctions;

class SubscriberDetailedStatsResponseBuilder {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var WPFunctions */
  private $wp;

  /** @var WCHelper */
  private $wooCommerce;

  /** @var NewsletterUrl */
  private $newsletterUrl;

  public function __construct(
    NewsletterUrl $newsletterUrl,
    WPFunctions $wp,
    WCHelper $wooCommerce
  ) {
    $this->newsletterUrl = $newsletterUrl;
    $this->wp = $wp;
    $this->wooCommerce = $wooCommerce;
  }

  /**
   * @param SubscriberNewsletterStats[] $newslettersStats
   * @return array<array>
   */
  public function build(array $newslettersStats): array {
    $response = [];

    foreach ($newslettersStats as $stats) {
      $item = $this->buildNewsletter($stats->getNewsletter());
      $item['actions'][] = $this->buildOpen($stats->getOpen());
      foreach ($stats->getClicks() as $click) {
        $item['actions'][] = $this->buildClick($click);
      }
      $response[] = $item;
    }
    return $response;
  }

  /**
   * @param NewsletterEntity $newsletter
   *
   * @return array<string, mixed>
   */
  private function buildNewsletter(NewsletterEntity $newsletter): array {
    $sentAt = $newsletter->getSentAt();
    $previewUrl = $this->newsletterUrl->getViewInBrowserUrl(
      (object)[
        'id' => $newsletter->getId(),
        'hash' => $newsletter->getHash(),
      ],
      null,
      in_array($newsletter->getStatus(), [NewsletterEntity::STATUS_SENT, NewsletterEntity::STATUS_SENDING], true)
        ? $newsletter->getLatestQueue()
        : false
    );
    return [
      'id' => $newsletter->getId(),
      'preview_url' => $previewUrl,
      'subject' => $newsletter->getSubject(),
      'sent_at' => $sentAt ? $sentAt->format(self::DATE_FORMAT) : null,
      'actions' => [],
    ];
  }

  /**
   * @param StatisticsOpenEntity $open
   *
   * @return array<string, int|string|null>
   */
  private function buildOpen(StatisticsOpenEntity $open): array {
    return [
      'id' => $open->getId(),
      'type' => $open->getUserAgentType() === UserAgentEntity::USER_AGENT_TYPE_MACHINE ? 'machine-open' : 'open',
      'created_at' => ($createdAt = $open->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
    ];
  }

  /**
   * @param StatisticsClickEntity $click
   *
   * @return array<string, array<int, array<string, mixed>>|int|string|null>
   */
  private function buildClick(StatisticsClickEntity $click): array {
    $link = $click->getLink();
    $linkUrl = ($link instanceof NewsletterLinkEntity) ? $link->getUrl() : '';
    $purchases = [];
    foreach ($click->getWooCommercePurchases() as $purchase) {
      $purchases[] = [
        'id' => $purchase->getId(),
        'created_at' => ($createdAt = $purchase->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
        'order_id' => $purchase->getOrderId(),
        'order_url' => $this->wp->getEditPostLink($purchase->getOrderId(), 'code'),
        'revenue' => $this->wooCommerce->getRawPrice(
          $purchase->getOrderPriceTotal(),
          ['currency' => $purchase->getOrderCurrency()]
        ),
      ];
    }
    return [
      'id' => $click->getId(),
      'type' => 'click',
      'created_at' => ($createdAt = $click->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'count' => $click->getCount(),
      'url' => $linkUrl,
      'purchases' => $purchases,
    ];
  }
}
