<?php
/**
 * WC_CP_Admin_Order class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.14.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products edit-order functions and filters.
 *
 * @class    WC_CP_Admin_Order
 * @version  6.1.4
 */
class WC_CP_Admin_Order {

	/**
	 * Order object to use in 'display_edit_button'.
	 * @var WC_Order
	 */
	protected static $order;

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		// Auto-populate composited order-items for Composites that don't require configuration.
		add_action( 'woocommerce_ajax_add_order_item_meta', array( __CLASS__, 'add_components' ), 10, 3 );

		// Save order object to use in 'display_edit_button'.
		add_action( 'woocommerce_admin_order_item_headers', array( __CLASS__, 'set_order' ) );

		// Display "Configure/Edit" button next to configurable composite container items in the edit-order screen.
		add_action( 'woocommerce_after_order_itemmeta', array( __CLASS__, 'display_edit_button' ), 10, 3 );

		// Add JS template.
		add_action( 'admin_footer', array( __CLASS__, 'add_js_template' ) );
	}

	/**
	 * Whether a composite is configurable in admin-order context.
	 *
	 * @param  WC_Product_Composite  $composite
	 * @return boolean
	 */
	public static function is_composite_configurable( $composite ) {

		$is_configurable = false;
		$components      = $composite->get_components();

		foreach ( $components as $component ) {

			if ( $component->is_optional() ) {
				$is_configurable = true;
			} elseif ( $component->get_quantity( 'min' ) !== $component->get_quantity( 'max' ) ) {
				$is_configurable = true;
			} elseif ( sizeof( $component->get_options() ) > 1 ) {
				$is_configurable = true;
			} elseif ( $component->is_static() && 'simple' !== WC_Product_Factory::get_product_type( $component->get_default_option() ) ) {
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
	 * Auto-populate composited order-items for Composites that don't require configuration.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Order
	 * @return void
	 */
	public static function add_components( $item_id, $item, $order ) {

		if ( 'line_item' === $item->get_type() ) {

			$product = $item->get_product();

			if ( $product && $product->is_type( 'composite' ) ) {

				/**
				 * 'woocommerce_auto_add_composited_items' filter.
				 *
				 * In some cases you might want to auto-add a default configuration that's "good enough" and work from there, e.g. adjust quantities or remove items.
				 *
				 * @param  $auto_add  boolean
				 * @param  $product   WC_Product_Composite
				 * @param  $item      WC_Order_Item
				 * @param  $order     WC_Order
				 */
				if ( apply_filters( 'woocommerce_auto_add_composited_items', false === self::is_composite_configurable( $product ), $product, $item, $order ) ) {

					$added_to_order = WC_CP()->order->add_composite_to_order( $product, $order, $item->get_quantity(), array(

						/**
						 * 'woocommerce_auto_added_composite_configuration' filter.
						 *
						 * See 'woocommerce_auto_add_composited_items' filter above. Use this filter to define the default configuration you want to use.
						 *
						 * @param  $config   array
						 * @param  $product  WC_Product_Composite
						 * @param  $item     WC_Order_Item
						 * @param  $order    WC_Order
						 */
						'configuration' => apply_filters( 'woocommerce_auto_added_composite_configuration', WC_CP()->cart->get_posted_composite_configuration( $product ), $product, $item, $order )
					) );

					if ( $added_to_order ) {


						if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

							$new_container_item = $order->get_item( $added_to_order );

							if ( $item_reduced_stock = $item->get_meta( '_reduced_stock', true ) ) {
								$new_container_item->add_meta_data( '_reduced_stock', $item_reduced_stock, true );
								$new_container_item->save();
							}

							$components_to_add = wc_cp_get_composited_order_items( $new_container_item, $order );
							$order_notes       = array();

							foreach ( $components_to_add as $order_item_id => $order_item ) {

								$component_id           = $order_item->get_meta( '_composite_item', true );
								$component              = $product->get_component( $component_id );
								$component_title        = $component ? $component->get_title() : sprintf( __( 'Component #%s', 'woocommerce-composite-products' ), $component_id );
								$composited_product     = $order_item->get_product();
								$composited_product_sku = $composited_product->get_sku();

								if ( ! $composited_product_sku ) {
									$composited_product_sku = '#' . $composited_product->get_id();
								}

								$order_note = sprintf( _x( '%1$s: %2$s (%3$s)', 'component stock change note format', 'woocommerce-composite-products' ), $component_title, $order_item->get_name(), $composited_product_sku );

								if ( $composited_product->managing_stock() ) {

									$qty           = $order_item->get_quantity();
									$old_stock     = $composited_product->get_stock_quantity();
									$new_stock     = wc_update_product_stock( $composited_product, $qty, 'decrease' );
									$stock_from_to = $old_stock . '&rarr;' . $new_stock;
									$order_note    = sprintf( _x( '%1$s: %2$s (%3$s)', 'component change note format', 'woocommerce-composite-products' ), $component_title, $order_item->get_name(), $composited_product_sku, $stock_from_to );

									$order_item->add_meta_data( '_reduced_stock', $qty, true );
									$order_item->save();
								}

								$order_notes[] = $order_note;
							}

							if ( ! empty( $order_notes ) ) {
								$order->add_order_note( sprintf( __( 'Added component line items: %s', 'woocommerce-composite-products' ), implode( ', ', $order_notes ) ), false, true );
							}
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
	 * Display "Configure/Edit" button next to configurable composite container items in the edit-order screen.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $product  WC_Product
	 * @return void
	 */
	public static function display_edit_button( $item_id, $item, $product ) {

		if ( self::$order && self::$order->is_editable() && 'line_item' === $item->get_type() ) {

			if ( $product && $product->is_type( 'composite' ) ) {

				/**
				 * 'woocommerce_is_composite_container_order_item_editable' filter.
				 *
				 * @param  $auto_add  boolean
				 * @param  $product   WC_Product_Composite
				 * @param  $item      WC_Order_Item
				 * @param  $order     WC_Order
				 */
				if ( apply_filters( 'woocommerce_is_composite_container_order_item_editable', self::is_composite_configurable( $product ), $product, $item, self::$order ) ) {

					// Already configured?
					$is_configured = wc_cp_is_composite_container_order_item( $item, self::$order );

					?>
					<div class="configure_order_item">
						<button class="<?php echo $is_configured ? 'edit_composite' : 'configure_composite' ?> button"><?php

							if ( $is_configured ) {
								esc_html_e( 'Edit', 'woocommerce-composite-products' );
							} else {
								esc_html_e( 'Configure', 'woocommerce-composite-products' );
							}

						 ?></button>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * JS template of modal for configuring/editing composites.
	 */
	public static function add_js_template() {

		if ( wp_script_is( 'wc-composite-admin-order-panel' ) ) {
			?>
			<script type="text/template" id="tmpl-wc-modal-edit-composite">
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
									<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Done', 'woocommerce-composite-products' ); ?></button>
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

WC_CP_Admin_Order::init();
