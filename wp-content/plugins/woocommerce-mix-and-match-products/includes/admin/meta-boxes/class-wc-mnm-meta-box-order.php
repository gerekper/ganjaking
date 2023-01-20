<?php
/**
 * WC_MNM_Meta_Box_Order class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Meta-Boxes/Order
 * @since    1.7.0
 * @version  2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mix and Match edit-order functions and filters.
 */
class WC_MNM_Meta_Box_Order {

	/**
	 * Order object to use in 'display_edit_button'.
	 * @var WC_Order
	 */
	protected static $order;

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		// Auto-populate child order-items for containers that have a pre-set configuration.
		add_action( 'woocommerce_ajax_add_order_item_meta', array( __CLASS__, 'add_child_items' ), 10, 3 );

		// Save order object to use in 'display_edit_button'.
		add_action( 'woocommerce_admin_order_item_headers', array( __CLASS__, 'set_order' ) );

		// Display "Configure/Edit" button next to configurable container items in the edit-order screen.
		add_action( 'woocommerce_after_order_itemmeta', array( __CLASS__, 'display_edit_button' ), 10, 3 );
		
		// Force some styles when editing.
		add_action( 'wc_mnm_edit_container_order_item_in_shop_order', array( __CLASS__, 'force_container_styles' ), 0, 4 );
		add_action( 'wc_mnm_edit_container_order_item_in_shop_subscription', array( __CLASS__, 'force_container_styles' ), 0, 4 );

		// Add JS template.
		add_action( 'admin_footer', array( __CLASS__, 'add_js_template' ) );

	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Auto-populate child order-items for Mix and Match containers that don't require configuration.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Order
	 */
	public static function add_child_items( $item_id, $item, $order ) {

		if ( 'line_item' === $item->get_type() ) {

			$product = $item->get_product();

			if ( $product && wc_mnm_is_product_container_type( $product ) ) {

				/**
				 * 'wc_mnm_auto_add_child_items' filter.
				 *
				 * In some cases you might want to auto-add a default configuration that's "good enough" and work from there, e.g. adjust quantities or remove items.
				 *
				 * @param  $auto_add  boolean
				 * @param  $product   WC_Product_Mix_and_Match
				 * @param  $item      WC_Order_Item
				 * @param  $order     WC_Order
				 */
				if ( apply_filters( 'wc_mnm_auto_add_child_items', false, $product, $item, $order ) ) {

					$added_to_order = WC_Mix_and_Match()->order->add_container_to_order(
						$product,
						$order,
						$item->get_quantity(),
						array(

						/**
						 * 'wc_mnm_auto_add_container_configuration' filter.
						 *
						 * See 'wc_mnm_auto_add_child_items' filter above. Use this filter to define the default configuration you want to use.
						 *
						 * @param  $config   array
						 * @param  $product  WC_Product_Mix_and_Match
						 * @param  $item     WC_Order_Item
						 * @param  $order    WC_Order
						 */
						'configuration' => apply_filters( 'wc_mnm_auto_add_container_configuration', array(), $product, $item, $order )
						)
					);

					if ( $added_to_order ) {

						$child_order_items = wc_mnm_get_child_order_items( $order->get_item( $added_to_order ), $order );
						$order_notes         = array();

						foreach ( $child_order_items as $order_item_id => $order_item ) {
							$product                       = $order_item->get_product();
							$order_notes[ $order_item_id ] = $product->get_formatted_name();

							if ( $product->managing_stock() ) {
								$qty                           = $order_item->get_quantity();
								$old_stock                     = $product->get_stock_quantity();
								$new_stock                     = wc_update_product_stock( $product, $qty, 'decrease' );
								$order_notes[ $order_item_id ] = $product->get_formatted_name() . ' &ndash; ' . $old_stock . '&rarr;' . $new_stock;

								$order_item->add_meta_data( '_reduced_stock', $qty, true );
								$order_item->save();
							}
						}
						// translators: order note listing all the child products added.
						$order->add_order_note( sprintf( __( 'Added line items: %s', 'woocommerce-mix-and-match-products' ), implode( ', ', $order_notes ) ), false, true );
					}

					$order->remove_item( $item_id );
					$order->save();
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
	 * Display "Configure/Edit" button next to configurable Mix and Match container items in the edit-order screen.
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Product
	 */
	public static function display_edit_button( $item_id, $item, $product ) {

		if ( self::$order && self::$order->is_editable() && 'line_item' === $item->get_type() && $product ) {

			/**
			 * 'wc_mnm_is_container_order_item_editable' filter.
			 *
			 * @param  $auto_add  boolean
			 * @param  $product   WC_Product_Mix_and_Match
			 * @param  $item      WC_Order_Item
			 * @param  $order     WC_Order
			 */
			if ( apply_filters( 'wc_mnm_is_container_order_item_editable',  wc_mnm_is_product_container_type( $product ), $product, $item, self::$order ) ) {

				// Load required ajax scripts.
				WC_MNM_Ajax::load_edit_scripts();

				// Already configured?
				$is_configured = wc_mnm_is_container_order_item( $item, self::$order );
				$button_text   = $is_configured ? esc_html__( 'Edit', 'woocommerce-mix-and-match-products' ) : esc_html__( 'Configure', 'woocommerce-mix-and-match-products' );

				?>
				<div class="configure_container_order_item">
					<button data-order_item_id="<?php echo esc_attr( $item->get_id() );?>" data-container_id="<?php echo esc_attr( $product->get_id() );?>" class="<?php echo $is_configured ? 'edit_container' : 'configure_container'; ?> button">
						<?php echo esc_html( $button_text ); ?>
					</button>
				</div>
				<?php
			}
		
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Edit-Order Modal.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Force tabular layout and hide child links.
	 * 
	 * @since 2.3.0
	 * 
	 * @param  $product  WC_Product_Mix_and_Match
	 * @param  $order_item WC_Order_Item
	 * @param  $order      WC_Order
	 * @param  string $source The originating source loading this template
	 */
	public static function force_container_styles( $product, $order_item, $order, $source ) {

		// Force default location.
		add_filter( 'woocommerce_product_get_add_to_cart_form_location', function() { return 'default'; }, 9999 );

		// Force tabular layout.
		add_filter( 'woocommerce_product_get_layout', function() { return 'tabular'; }, 9999 );

		// Prevent theme override of quantity-input.php template in admin.
		add_filter( 'wc_get_template', array( __CLASS__, 'force_core_template' ), 9999, 5 );
	
		// Hide links.
		add_filter( 'woocommerce_product_is_visible', '__return_false' );
		
	}

	/**
	 * Nuke any theme overrides of quantity-input.php template.
	 * 
	 * @since 2.3.0
	 *
	 * @param  $item_id  int
	 * @param  $item     WC_Order_Item
	 * @param  $order    WC_Product
	 * @return void
	 */
	public static function force_core_template( $template, $template_name, $args, $template_path, $default_path ) {
		if ( $template_name === 'global/quantity-input.php' ) {
			$default_path = WC()->plugin_path() . '/templates/';
			$template = $default_path . $template_name;
		}
		return $template;
	}

	/**
	 * JS template of modal for configuring/editing containers.
	 */
	public static function add_js_template() {

		if ( wp_script_is( 'wc-mnm-admin-order-panel' ) ) {
			?>
			<script type="text/template" id="tmpl-wc-modal-edit-container">
				<div class="wc-backbone-modal wc-mnm-backbone-modal">
					<div class="wc-backbone-modal-content">
						<section class="wc-backbone-modal-main" role="main">
							<header class="wc-backbone-modal-header">
								<h1>{{{ data.action }}}</h1>
								<button class="modal-close modal-close-link dashicons dashicons-no-alt">
									<span class="screen-reader-text"><?php _e( 'Close modal panel', 'woocommerce-mix-and-match-products' ); ?></span>
								</button>
							</header>
							<article>
							</article>
							<footer>
								<div class="inner">
									<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Update', 'woocommerce-mix-and-match-products' ); ?></button>
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
WC_MNM_Meta_Box_Order::init();
