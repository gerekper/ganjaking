<?php

namespace MailPoet\Premium\Subscriber\Stats;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsOpenEntity;

class SubscriberNewsletterStats {
  /** @var NewsletterEntity */
  private $newsletter;

  /** @var StatisticsOpenEntity */
  private $open;

  /** @var StatisticsClickEntity[] */
  private $clicks;

  /**
   * SubscriberNewsletterStats constructor.
   *
   * @param NewsletterEntity $newsletter
   * @param StatisticsOpenEntity $open
   * @param array<int, StatisticsClickEntity> $clicks
   */
  public function __construct(
    NewsletterEntity $newsletter,
    StatisticsOpenEntity $open,
    array $clicks = []
  ) {
    $this->newsletter = $newsletter;
    $this->open = $open;
    $this->clicks = $clicks;
  }

  public function getNewsletter(): NewsletterEntity {
    return $this->newsletter;
  }

  /**
   * @return StatisticsOpenEntity|null
   */
  public function getOpen() {
    return $this->open;
  }

  /**
   * @return StatisticsClickEntity[]
   */
  public function getClicks(): array {
    return $this->clicks;
  }
}
