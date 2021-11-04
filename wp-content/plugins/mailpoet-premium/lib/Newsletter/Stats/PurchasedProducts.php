<?php

namespace MailPoet\Premium\Newsletter\Stats;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Statistics\StatisticsWooCommercePurchasesRepository;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoet\WP\Functions as WPFunctions;

class PurchasedProducts {

  /** @var WCHelper */
  private $woocommerceHelper;

  /** @var WPFunctions */
  private $wp;

  /** @var StatisticsWooCommercePurchasesRepository */
  private $statisticsWooCommercePurchasesRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  public function __construct(
    WCHelper $woocommerceHelper,
    StatisticsWooCommercePurchasesRepository $statisticsWooCommercePurchasesRepository,
    NewslettersRepository $newslettersRepository,
    WPFunctions $wp
  ) {
    $this->woocommerceHelper = $woocommerceHelper;
    $this->wp = $wp;
    $this->statisticsWooCommercePurchasesRepository = $statisticsWooCommercePurchasesRepository;
    $this->newslettersRepository = $newslettersRepository;
  }

  public function getStats($newsletterId) {
    if (!$newsletterId || !$this->woocommerceHelper->isWooCommerceActive()) {
      return [];
    }
    $newsletter = $this->newslettersRepository->findOneById($newsletterId);
    if (!$newsletter instanceof NewsletterEntity) {
      return [];
    }

    $currency = $this->woocommerceHelper->getWoocommerceCurrency();
    $purchases = $this->statisticsWooCommercePurchasesRepository->findBy([
      'newsletter' => $newsletter,
      'orderCurrency' => $currency,
    ]);

    $result = $this->getStatsForPurchases($purchases);
    $result = $this->formatPrices($result, $currency);
    $result = $this->addThumbnails($result);
    $result = $this->sortProducts($result);
    return $result;
  }

  /**
   * @param StatisticsWooCommercePurchaseEntity[] $purchases
   * @return array<int, array>
   */
  private function getStatsForPurchases($purchases) {
    $result = [];
    foreach ($purchases as $purchase) {
      foreach ($this->getOrderItems($purchase) as $orderItem) {
        $productId = $orderItem->get_product_id();
        if (!isset($result[$productId])) {
          $result[$productId] = [
            'name' => $orderItem->get_name(),
            'count' => $orderItem->get_quantity(),
            'total' => (float)$orderItem->get_total(),
            'product_id' => $productId,
          ];
        } else {
          $result[$productId]['count'] += $orderItem->get_quantity();
          $result[$productId]['total'] += (float)$orderItem->get_total();
        }
      }
    }
    return $result;
  }

  /**
   * @return \WC_Order_Item_Product[]
   */
  private function getOrderItems(StatisticsWooCommercePurchaseEntity $purchase) {
    $order = $this->woocommerceHelper->wcGetOrder($purchase->getOrderId());
    if (!$order) {
      return [];
    }
    // get_items returns by default only product items, no shipping or coupons or anything
    return $order->get_items();
  }

  private function formatPrices($products, $currency) {
    $result = [];
    foreach ($products as $productId => $product) {
      $product['formatted_total'] = $this->woocommerceHelper->getRawPrice($product['total'], ['currency' => $currency]);
      $result[$productId] = $product;
    }
    return $result;
  }

  private function addThumbnails($products) {
    $result = [];
    foreach ($products as $productId => $product) {
      $image = $this->getProductImage($productId);
      if (is_array($image)) {
        list($src) = $image;
        $product['image_url'] = $src;
      }
      $result[$productId] = $product;
    }
    return $result;
  }

  private function getProductImage($productId) {
    $wooProduct = $this->woocommerceHelper->wcGetProduct($productId);
    if (!$wooProduct) {
      return false;
    }
    return $this->wp->wpGetAttachmentImageSrc($wooProduct->get_image_id());
  }

  private function sortProducts($products) {
    usort($products, function($a, $b) {
      $retval = $b['count'] - $a['count'];
      if ($retval === 0) {
        $retval = $b['total'] - $a['total'];
      }
      return (int)ceil($retval);
    });
    return $products;
  }
}
