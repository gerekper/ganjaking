<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\AutomationEmailController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\OrderController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Storage\OrderStatistics;
use WC_Order;
use WC_Order_Item;
use WC_Order_Item_Product;

/**
 * @phpstan-import-type RawOrderType from OrderStatistics as RawOrderType
 * @phpstan-type ResultItem array{date: string, customer: array{id:int, email:string, first_name:string, last_name:string,avatar:string}, details: array{id:int, status: array{id:string, name:string }, total: float, products: array<int, array{id:int,name:string,quantity:int}>}, email: array{subject:string, id:int}}
 */

class PremiumOrderController implements OrderController {


  /** @var OrderStatistics */
  private $statisticsRepository;

  /** @var WooCommerce */
  private $woocommerce;

  /** @var WordPress */
  private $wp;

  /** @var AutomationEmailController */
  private $automationEmailController;

  public function __construct(
    OrderStatistics $statisticsRepository,
    WooCommerce $woocommerce,
    WordPress $wp,
    AutomationEmailController $automationEmailController
  ) {
    $this->statisticsRepository = $statisticsRepository;
    $this->woocommerce = $woocommerce;
    $this->wp = $wp;
    $this->automationEmailController = $automationEmailController;
  }

  /**
   * @param Automation $automation
   * @param Query $query
   * @return array{items:ResultItem[], results:int}
   */
  public function getOrdersForAutomation(Automation $automation, Query $query): array {

    $emails = $this->automationEmailController->getAutomationEmailsInTimeSpan($automation, $query->getAfter(), $query->getBefore());
    $items = $this->statisticsRepository->getOrdersForNewsletters(
      $emails,
      $query
    );

    $allOrderIds = array_filter(array_map(
      function(array $item) {
        return $item['order_id'];
      },
      $items
    ));
    $allOrders = $this->woocommerce->wcGetOrders([
      'post__in' => $allOrderIds,
      'limit' => count($allOrderIds),
    ]);
    $allOrders = array_values(array_filter(
      is_array($allOrders) ? $allOrders : [],
      function($order): bool {
        return $order instanceof WC_Order;
      }
    ));

    return [
      'items' => array_map(
        function($item) use ($allOrders): array {
          return $this->mapItem($item, $allOrders);
        }, $items),
      'results' => $this->statisticsRepository->getLastCount(
        $emails,
        $query
      ),
    ];
  }

  /**
   * @param RawOrderType $item
   * @param WC_Order[] $allOrders
   * @return ResultItem
   */
  private function mapItem(array $item, array $allOrders) {
    $currentOrders = array_filter($allOrders, function($order) use ($item) {
      return $order->get_id() === (int)$item['order_id'];
    });
    $currentOrder = array_shift($currentOrders);
    $products = $currentOrder ? array_values(array_filter(array_map(
      function(WC_Order_Item $lineItem): ?array {
        if (!$lineItem instanceof WC_Order_Item_Product) {
          return null;
        }
        return [
          'id' => $lineItem->get_product_id(),
          'name' => $lineItem->get_name(),
          'quantity' => $lineItem->get_quantity(),
        ];
      },
      $currentOrder->get_items()
    ))) : [];

    $createdAt = new \DateTime($item['created_at']);
    $createdAt->setTimezone($this->wp->wpTimezone());
    return [
      'date' => $createdAt->format(\DateTimeImmutable::W3C),
      'customer' => [
        'id' => (int)$item['subscriber_id'],
        'email' => $item['email'],
        'first_name' => $item['first_name'],
        'last_name' => $item['last_name'],
        'avatar' => (string)$this->wp->getAvatarUrl($item['email'], ['size' => 20]),
      ],
      'details' => [
        'id' => $currentOrder ? $currentOrder->get_id() : 0,
        'status' => $currentOrder ? [
          'id' => $currentOrder->get_status(),
          'name' => $this->woocommerce->wcGetOrderStatusName($currentOrder->get_status()),
        ] : [
          'id' => '',
          'name' => '',
        ],
        'total' => (float)$item['total'],
        'products' => $products,
      ],
      'email' => [
        'subject' => $item['subject'],
        'id' => (int)$item['newsletter_id'],
      ],
    ];
  }
}
