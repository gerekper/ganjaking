<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use InvalidArgumentException;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Entities\NewsletterEntity;

/**
 * @phpstan-type RawOrderType array{created_at: string, newsletter_id: int, order_id: int, total: float, subscriber_id: int, first_name:string, last_name:string, email:string, subject:string, status:string}
 */

class OrderStatistics {


  /** @var WooCommerce */
  private $wooCommerce;

  /** @var WordPress */
  private $wordPress;

  private $validOrderByValues = ['created_at', 'last_name', 'subject', 'status', 'revenue'];

  public function __construct(
    WooCommerce $wooCommerce,
    WordPress $wordPress
  ) {
    $this->wooCommerce = $wooCommerce;
    $this->wordPress = $wordPress;
  }

  /**
   * @param NewsletterEntity[] $newsletters
   * @param Query $query
   * @return RawOrderType[]
   */
  public function getOrdersForNewsletters(
    array $newsletters,
    Query $query
  ): array {
    if (!$newsletters) {
      return [];
    }
    $from = $query->getAfter();
    $to = $query->getBefore();
    $limit = $query->getLimit();
    $offset = max(0, ($query->getPage() - 1) * $query->getLimit());
    $orderBy = !empty($query->getOrderBy()) ? $query->getOrderBy() : 'createdAt';
    $order = $query->getOrderDirection() === 'asc' ? 'asc' : 'desc';

    if (!in_array($orderBy, $this->validOrderByValues, true)) {
      throw new InvalidArgumentException('Invalid orderBy parameter');
    }
    $result = ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) ?
      $this->getOrdersForNewslettersFromHpos($newsletters, $from, $to, $limit, $offset, $orderBy, $order) :
      $this->getOrdersForNewslettersFromLegacy($newsletters, $from, $to, $limit, $offset, $orderBy, $order);
    return is_array($result) ? $result : [];
  }

  /**
   * @param NewsletterEntity[] $newsletters
   * @param Query $query
   * @return int
   */
  public function getLastCount(
    array $newsletters,
    Query $query
  ): int {
    if (!$newsletters) {
      return 0;
    }
    $from = $query->getAfter();
    $to = $query->getBefore();
    $result = ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) ?
      $this->getOrdersForNewslettersFromHpos($newsletters, $from, $to, 0, 0, '', '', true) :
      $this->getOrdersForNewslettersFromLegacy($newsletters, $from, $to, 0, 0, '', '', true);
    return !is_int($result) ? 0 : $result;
  }

  /**
   * @param NewsletterEntity[] $newsletters
   * @param DateTimeImmutable $from
   * @param DateTimeImmutable $to
   * @param int $limit
   * @param int $offset
   * @param string $orderBy
   * @param string $order
   * @param bool $count
   * @return RawOrderType[] | int
   */
  private function getOrdersForNewslettersFromHpos(
    array $newsletters,
    DateTimeImmutable $from,
    DateTimeImmutable $to,
    int $limit = 100,
    int $offset = 0,
    string $orderBy = 'createdAt',
    string $order = 'desc',
    bool $count = false
  ) {

    switch ($orderBy) {
      case 'last_name':
        $orderBy = 'subscriber.last_name';
        break;
      case 'subject':
        $orderBy = 'newsletter.subject';
        break;
      case 'status':
        $orderBy = 'order.status';
        break;
      case 'revenue':
        $orderBy = 'revenue.order_price_total';
        break;
      case 'created_at':
      default:
        $orderBy = 'revenue.created_at';
        break;
    }

    $wpdb = $this->wordPress->getWpdb();

    $newsletterIds = array_map(function ($newsletter) {
      return $newsletter->getId();
    }, $newsletters);
    $newsLetterIds = implode(',', $newsletterIds);
    $sqlWhere = "
      revenue.newsletter_id IN ($newsLetterIds)
      AND revenue.created_at BETWEEN '" . $from->format('Y-m-d H:i:s') . "' AND '" . $to->format('Y-m-d H:i:s') . "'";
    $sqlOrderBy = !$count ? "ORDER BY $orderBy $order, order.id $order" : "";
    $sqlLimit = !$count ? "LIMIT $offset, $limit" : "";
    $sqlSelect = !$count ? "
      `revenue`.`created_at`,
      `revenue`.`newsletter_id`,
      `revenue`.`order_id`,
       `revenue`.`order_price_total` as `total`,
       `revenue`.`subscriber_id`,
       `subscriber`.`first_name`,
       `subscriber`.`last_name`,
       `subscriber`.`email`,
       `newsletter`.`subject`,
       `order`.`status`" : "COUNT(`revenue`.`id`) as `count`";
    $sql = "SELECT
      $sqlSelect
      FROM " . $wpdb->prefix . "mailpoet_statistics_woocommerce_purchases as `revenue`
      INNER JOIN " . $wpdb->prefix . "wc_orders as `order` ON `revenue`.order_id = `order`.ID
      INNER JOIN " . $wpdb->prefix . "mailpoet_subscribers as `subscriber` ON `subscriber`.ID = `revenue`.subscriber_id
      INNER JOIN " . $wpdb->prefix . "mailpoet_newsletters as `newsletter` ON `newsletter`.ID = `revenue`.newsletter_id
      WHERE $sqlWhere
      $sqlOrderBy
      $sqlLimit";

    /** @var RawOrderType[] $result */
    $result = $wpdb->get_results($sql, ARRAY_A);
    if (!$count) {
      return is_array($result) ? $result : [];
    }
    return is_array($result) && isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
  }

  /**
   * @param NewsletterEntity[] $newsletters
   * @param DateTimeImmutable $from
   * @param DateTimeImmutable $to
   * @param int $limit
   * @param int $offset
   * @param string $orderBy
   * @param string $order
   * @param bool $count
   * @return RawOrderType[] | int
   */
  private function getOrdersForNewslettersFromLegacy(
    array $newsletters,
    DateTimeImmutable $from,
    DateTimeImmutable $to,
    int $limit = 100,
    int $offset = 0,
    string $orderBy = 'createdAt',
    string $order = 'desc',
    bool $count = false
  ) {

    switch ($orderBy) {
      case 'last_name':
        $orderBy = 'subscriber.last_name';
        break;
      case 'subject':
        $orderBy = 'newsletter.subject';
        break;
      case 'status':
        $orderBy = 'order.post_status';
        break;
      case 'revenue':
        $orderBy = 'revenue.order_price_total';
        break;
      case 'created_at':
      default:
        $orderBy = 'revenue.created_at';
        break;
    }

    $wpdb = $this->wordPress->getWpdb();

    $newsletterIds = array_map(function ($newsletter) {
      return $newsletter->getId();
    }, $newsletters);
    $newsLetterIds = implode(',', $newsletterIds);
    $sqlWhere = "
      revenue.newsletter_id IN ($newsLetterIds)
      AND revenue.created_at BETWEEN '" . $from->format('Y-m-d H:i:s') . "' AND '" . $to->format('Y-m-d H:i:s') . "'";
    $sqlOrderBy = !$count ? "ORDER BY $orderBy $order" : "";
    $sqlLimit = !$count ? "LIMIT $offset, $limit" : "";
    $sqlSelect = !$count ? "
      `revenue`.`created_at`,
      `revenue`.`newsletter_id`,
      `revenue`.`order_id`,
       `revenue`.`order_price_total` as `total`,
       `revenue`.`subscriber_id`,
       `subscriber`.`first_name`,
       `subscriber`.`last_name`,
       `subscriber`.`email`,
       `newsletter`.`subject`,
       `order`.`post_status` as `status`" : "COUNT(`revenue`.`id`) as `count`";
    $sql = "SELECT
      $sqlSelect
      FROM " . $wpdb->prefix . "mailpoet_statistics_woocommerce_purchases as `revenue`
      INNER JOIN $wpdb->posts as `order` ON `revenue`.order_id = `order`.ID
      INNER JOIN " . $wpdb->prefix . "mailpoet_subscribers as `subscriber` ON `subscriber`.ID = `revenue`.subscriber_id
      INNER JOIN " . $wpdb->prefix . "mailpoet_newsletters as `newsletter` ON `newsletter`.ID = `revenue`.newsletter_id
      WHERE $sqlWhere
      $sqlOrderBy
      $sqlLimit";

    /** @var RawOrderType[] $result */
    $result = $wpdb->get_results($sql, ARRAY_A);
    if (!$count) {
      return is_array($result) ? $result : [];
    }

    return is_array($result) && isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
  }
}
