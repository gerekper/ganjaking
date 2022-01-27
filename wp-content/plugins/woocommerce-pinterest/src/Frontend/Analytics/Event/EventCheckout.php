<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

use Premmerce\PrimaryCategory\Model\Model;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use \WC_Order;
use WC_Order_Item_Product;

class EventCheckout extends AbstractEvent implements EventInterface {

	/**
	 * WC_Order instance
	 *
	 * @var WC_Order
	 */
	private $order;

	/**Primary Category Model instance
	 *
	 * @var Model
	 */
	private $primaryCategoryModel;

	/**
	 * EventCheckout constructor.
	 *
	 * @param PinterestIntegration $integration
	 * @param Model $primaryCategoryModel
	 */
	public function __construct( PinterestIntegration $integration, Model $primaryCategoryModel ) {
		$this->primaryCategoryModel = $primaryCategoryModel;
		parent::__construct( $integration );

		$this->init();
	}

	public function init() {
		if ( $this->enabled() ) {

			$orderKey = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : false;

			if ( $orderKey ) {

				$order = wc_get_order( wc_get_order_id_by_order_key( $orderKey ) );

				if ( $order ) {
					$this->order     = $order;
					$this->userEmail = $order->get_billing_email();
				}

			}
		}
	}

	/**
	 * Return event status
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->isEnabledInOptions();
	}

	/**
	 * Return if event was fired
	 *
	 * @return bool
	 */
	public function fired() {
		return (bool) $this->order;
	}

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName() {
		return 'Checkout';
	}

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData() {
		
		$data = array();

		if ( $this->order instanceof WC_Order ) {
			$data = array(
				'order_id'       => $this->order->get_id(),
				'value'          => $this->order->get_total(),
				'currency'       => get_woocommerce_currency(),
				'order_quantity' => $this->order->get_item_count(),
				'line_items'     => $this->getLineItems( $this->order )
			);
		}

		return $data;
	}

	/**
	 * Return product category name
	 *
	 * @return string
	 * @var int $productId
	 *
	 */
	private function getProductCategoryName( $productId ) {
		$categoryId = $this->getProductCategoryId( $productId );
		$result     = get_term_field( 'name', $categoryId, 'product_cat' );

		return is_string( $result ) ? $result : '';
	}

	/**
	 * Get product primary category id or just one of product categories ids
	 *
	 * @param $productId
	 *
	 * @return int|null
	 */
	private function getProductCategoryId( $productId ) {
		$categoryId = $this->primaryCategoryModel->getPrimaryCategoryId( $productId );

		if ( ! $categoryId ) {
			$categoriesIds = wc_get_product_term_ids( $productId, 'product_cat' );
			$categoryId    = reset( $categoriesIds );
		}

		return $categoryId ? $categoryId : null;
	}

	/**
	 * Return order line items data
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	private function getLineItems( WC_Order $order ) {
		$line_items = array();

		foreach ( $order->get_items() as $item ) {
			if ( $item instanceof WC_Order_Item_Product ) {
				$line_items[] = $this->getOrderProductData( $item );
			}
		};

		return $line_items;
	}

	/**
	 * Get Order Item Product data
	 *
	 * @param WC_Order_Item_Product $item
	 *
	 * @return array
	 */
	private function getOrderProductData( WC_Order_Item_Product $item ) {
		$product = $item->get_product();

		return array(
			'product_category' => $this->getProductCategoryName( $product->get_id() ),
			'product_name'     => $product->get_name(),
			'product_id'       => $product->get_id(),
			'product_price'    => $product->get_price(),
			'product_quantity' => $item->get_quantity()
		);
	}

	/**
	 * Return deferred status
	 * If event is deferred, it will be saved to transients and fired on next request handling
	 *
	 * @return bool
	 */
	public function isDeferred() {
		return true;
	}
}
