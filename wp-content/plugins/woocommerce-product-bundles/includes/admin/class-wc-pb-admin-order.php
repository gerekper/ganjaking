<?php
/**
 * WC_PB_Admin_Order class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundles edit-order functions and filters.
 *
 * @class    WC_PB_Admin_Order
 * @version  6.14.1
 */
class WC_PB_Admin_Order {

	/**
	 * Order object to use in 'display_edit_button'.
	 * @var WC_Order
	 */
	protected static $order;

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		// Auto-populate bundled order-items for Bundles that don't require configuration.
		add_action( 'woocommerce_ajax_add_order_item_meta', array( __CLASS__, 'add_bundled_items' ), 10, 3 );

		// Save order object to use in 'display_edit_button'.
		add_action( 'woocommerce_admin_order_item_headers', array( __CLASS__, 'set_order' ) );

		// Display "Configure/Edit" button next to configurable bundle container items in the edit-order screen.
		add_action( 'woocommerce_after_order_itemmeta', array( __CLASS__, 'display_edit_button' ), 10, 3 );

		// Add JS template.
		add_action( 'admin_footer', array( __CLASS__, 'add_js_template' ) );
	}

	/**
	 * Whether a bundle is configurable in admin-order context.
	 *
	 * If a bundled item:
	 *
	 * - is optional;
	 * - is variable and has attributes that require user input;
	 * - has configurable quantities,
	 *
	 * then the bundle is configurable.
	 *
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function is_bundle_configurable( $bundle ) {

		$is_configurable = false;
		$bundled_items   = $bundle->get_bundled_items();

		foreach ( $bundled_items as $bundled_item ) {

			if ( $bundled_item->is_optional() ) {
				$is_configurable = true;
			} elseif ( $bundled_item->get_quantity( 'min' ) !== $bundled_item->get_quantity( 'max' ) ) {
				$is_configurable = true;
			} elseif ( ( $configurable_attributes = $bundled_item->get_product_variation_attributes( true ) ) && count( $configurable_attributes ) > 0 ) {
				$is_configurable = true;
			}

			if ( $is_configurable ) {
				break;
			}
		}

		return $is_configurable;
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Auto-populate bundled order-items for Bundles that don't require configuration.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Order
	 * @return void
	 */
	public static function add_bundled_items( $item_id, $item, $order ) {

		if ( 'line_item' === $item->get_type() ) {

			$product = $item->get_product();

			if ( $product && $product->is_type( 'bundle' ) ) {

				/**
				 * 'woocommerce_auto_add_bundled_items' filter.
				 *
				 * In some cases you might want to auto-add a default configuration that's "good enough" and work from there, e.g. adjust quantities or remove items.
				 *
				 * @param  $auto_add  boolean
				 * @param  $product   WC_Product_Bundle
				 * @param  $item      WC_Order_Item
				 * @param  $order     WC_Order
				 */
				if ( apply_filters( 'woocommerce_auto_add_bundled_items', false === self::is_bundle_configurable( $product ), $product, $item, $order ) ) {

					$added_to_order = WC_PB()->order->add_bundle_to_order( $product, $order, $item->get_quantity(), array(

						/**
						 * 'woocommerce_auto_added_bundle_configuration' filter.
						 *
						 * See 'woocommerce_auto_add_bundled_items' filter above. Use this filter to define the default configuration you want to use.
						 *
						 * @param  $config   array
						 * @param  $product  WC_Product_Bundle
						 * @param  $item     WC_Order_Item
						 * @param  $order    WC_Order
						 */
						'configuration' => apply_filters( 'woocommerce_auto_added_bundle_configuration', WC_PB()->cart->get_posted_bundle_configuration( $product ), $product, $item, $order )
					) );

					if ( ! is_wp_error( $added_to_order ) ) {

						$new_container_item = $order->get_item( $added_to_order );

						$bundled_order_items = wc_pb_get_bundled_order_items( $new_container_item, $order );
						$product_ids         = array();
						$order_notes         = array();

						foreach ( $bundled_order_items as $order_item_id => $order_item ) {

							$bundled_item_id = $order_item->get_meta( '_bundled_item_id', true );
							$product_id      = $order_item->get_product_id();

							if ( $variation_id = $order_item->get_variation_id() ) {
								$product_id = $variation_id;
							}

							$product_ids[ $bundled_item_id ] = $product_id;
						}

						$duplicate_product_ids              = array_diff_assoc( $product_ids, array_unique( $product_ids ) );
						$duplicate_product_bundled_item_ids = array_keys( array_intersect( $product_ids, $duplicate_product_ids ) );

						foreach ( $bundled_order_items as $order_item_id => $order_item ) {

							$bundled_item_id     = $order_item->get_meta( '_bundled_item_id', true );
							$bundled_product     = $order_item->get_product();
							$bundled_product_sku = $bundled_product->get_sku();

							if ( ! $bundled_product_sku ) {
								$bundled_product_sku = '#' . $bundled_product->get_id();
							}

							if ( in_array( $bundled_item_id, $duplicate_product_bundled_item_ids ) ) {
								$stock_id = sprintf( _x( '%1$s:%2$s', 'bundled items stock change note sku with id format', 'woocommerce-product-bundles' ), $bundled_product_sku, $item_id );
							} else {
								$stock_id = $bundled_product_sku;
							}

							/* translators: %1$s: Product title, %2$s: Product identifier */
							$order_notes[] =  sprintf( _x( '%1$s (%2$s)', 'bundled items stock change note format', 'woocommerce-product-bundles' ), $order_item->get_name(), $stock_id );
						}

						if ( ! empty( $order_notes ) ) {
							$order->add_order_note( sprintf( __( 'Added bundled line items: %s', 'woocommerce-product-bundles' ), implode( ', ', $order_notes ) ), false, true );
						}

						$order->remove_item( $item_id );
						$order->save();
					}
				}
			}
		}
	}

	/**
	 * Save order object to use in 'display_edit_button'.
	 *
	 * Although the order object can be retrieved via 'WC_Order_Item::get_order', we've seen a significant performance hit when using that method.
	 *
	 * @param  WC_Order  $order
	 */
	public static function set_order( $order ) {
		self::$order = $order;
	}

	/**
	 * Display "Configure/Edit" button next to configurable bundle container items in the edit-order screen.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Product
	 * @return void
	 */
	public static function display_edit_button( $item_id, $item, $product ) {

		if ( self::$order && self::$order->is_editable() && 'line_item' === $item->get_type() ) {

			if ( $product && $product->is_type( 'bundle' ) ) {

				// Is this part of a Composite?
				if ( WC_PB()->compatibility->is_composited_order_item( $item, self::$order ) ) {
					return;
				}

				/**
				 * 'woocommerce_is_bundle_container_order_item_editable' filter.
				 *
				 * @param  $auto_add  boolean
				 * @param  $product   WC_Product_Bundle
				 * @param  $item      WC_Order_Item
				 * @param  $order     WC_Order
				 */
				if ( apply_filters( 'woocommerce_is_bundle_container_order_item_editable', self::is_bundle_configurable( $product ), $product, $item, self::$order ) ) {

					// Already configured?
					$is_configured = wc_pb_is_bundle_container_order_item( $item, self::$order );

					?>
					<div class="configure_order_item">
						<button class="<?php echo $is_configured ? 'edit_bundle' : 'configure_bundle' ?> button"><?php

							if ( $is_configured ) {
								esc_html_e( 'Edit', 'woocommerce-product-bundles' );
							} else {
								esc_html_e( 'Configure', 'woocommerce-product-bundles' );
							}

						 ?></button>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * JS template of modal for configuring/editing bundles.
	 */
	public static function add_js_template() {

		if ( wp_script_is( 'wc-pb-admin-order-panel' ) ) {
			?>
			<script type="text/template" id="tmpl-wc-modal-edit-bundle">
				<div class="wc-backbone-modal">
					<div class="wc-backbone-modal-content">
						<section class="wc-backbone-modal-main" role="main">
							<header class="wc-backbone-modal-header">
								<h1>{{{ data.action }}}</h1>
								<button class="modal-close modal-close-link dashicons dashicons-no-alt">
									<span class="screen-reader-text">Close modal panel</span>
								</button>
							</header>
							<article>
								<form action="" method="post">
								</form>
							</article>
							<footer>
								<div class="inner">
									<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Done', 'woocommerce-product-bundles' ); ?></button>
								</div>
							</footer>
						</section>
					</div>
				</div>
				<div class="wc-backbone-modal-backdrop modal-close"></div>
			</script>
			<?php
		}
	}
}

WC_PB_Admin_Order::init();
