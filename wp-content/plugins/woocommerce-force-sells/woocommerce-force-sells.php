<?php
/**
 * Plugin Name: WooCommerce Force Sells
 * Plugin URI: https://woocommerce.com/products/force-sells/
 * Description: Allows you to select products which will be used as force-sells - items which get added to the cart along with other items.
 * Version: 1.3.0
 * Author: KoiLab
 * Author URI: https://koilab.com
 * Requires PHP: 5.6
 * Requires at least: 4.7
 * Tested up to: 6.3
 * Domain: woocommerce-force-sells
 * Domain Path: /languages
 *
 * WC requires at least: 3.5
 * WC tested up to: 8.1
 * Woo: 18678:3ebddfc491ca168a4ea4800b893302b0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-force-sells
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \KoiLab\WC_Force_Sells\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_FORCE_SELLS_FILE' ) ) {
	define( 'WC_FORCE_SELLS_FILE', __FILE__ );
}

add_action( 'plugins_loaded', array( 'WC_Force_Sells', 'instance' ) );

use KoiLab\WC_Force_Sells\Utilities\Product_Utils;

if ( ! class_exists( 'WC_Force_Sells' ) ) :

	/**
	 * Main plugin class.
	 */
	class WC_Force_Sells extends \KoiLab\WC_Force_Sells\Plugin {

		/**
		 * Constructor
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'woocommerce_product_options_related', array( $this, 'write_panel_tab' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_extra_product_meta' ), 1 );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'show_force_sell_products' ) );
			add_action( 'woocommerce_add_to_cart', array( $this, 'add_force_sell_items_to_cart' ), 11, 6 );
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_force_sell_quantity_in_cart' ), 1, 2 );
			add_action( 'woocommerce_remove_cart_item', array( $this, 'update_force_sell_quantity_in_cart' ), 1, 1 );

			// Keep force sell data in the cart.
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
			add_filter( 'woocommerce_get_item_data', array( $this, 'get_linked_to_product_data' ), 10, 2 );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'remove_orphan_force_sells' ) );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'maybe_remove_duplicate_force_sells' ) );

			// Don't allow synced force sells to be removed or change quantity.
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 2 );

			// Sync with remove/restore link in cart.
			add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 30 );
			add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 30 );
		}

		/**
		 * If the single instance hasn't been set, set it now.
		 *
		 * @deprecated 1.3.0
		 *
		 * @return WC_Force_Sells
		 */
		public static function get_instance() {
			wc_deprecated_function( __FUNCTION__, '1.3.0', 'WC_Force_Sells::instance()' );

			return self::instance();
		}

		/**
		 * Looks to see if a product with the key of 'forced_by' actually exists and
		 * deletes it if not.
		 */
		public function remove_orphan_force_sells() {
			$cart_contents = WC()->cart->get_cart();

			foreach ( $cart_contents as $key => $value ) {
				if ( isset( $value['forced_by'] ) ) {
					if ( ! array_key_exists( $value['forced_by'], $cart_contents ) ) {
						WC()->cart->remove_cart_item( $key );
					}
				}
			}
		}

		/**
		 * Checks the cart contents to make sure we don't
		 * have duplicated force sell products.
		 *
		 * @since 1.1.19
		 */
		public function maybe_remove_duplicate_force_sells() {
			$cart_contents = WC()->cart->get_cart();
			$product_ids   = array();

			foreach ( $cart_contents as $key => $value ) {
				if ( isset( $value['forced_by'] ) ) {
					$product_ids[] = $value['product_id'];
				}
			}

			foreach ( WC()->cart->get_cart() as $key => $value ) {
				if ( ! isset( $value['forced_by'] ) && in_array( $value['product_id'], $product_ids, true ) ) {
					WC()->cart->remove_cart_item( $key );
				}
			}
		}

		/**
		 * Get forced product added again to cart when item is loaded from session.
		 *
		 * @param array $cart_item Item in cart.
		 * @param array $values    Item values.
		 *
		 * @return array Cart item.
		 */
		public function get_cart_item_from_session( $cart_item, $values ) {
			if ( isset( $values['forced_by'] ) ) {
				$cart_item['forced_by'] = $values['forced_by'];
			}
			return $cart_item;
		}

		/**
		 * Making sure linked products from an item is displayed in cart.
		 *
		 * @param array $data      Data.
		 * @param array $cart_item Cart item.
		 *
		 * @return array
		 */
		public function get_linked_to_product_data( $data, $cart_item ) {
			if ( isset( $cart_item['forced_by'] ) ) {
				$product_key = WC()->cart->find_product_in_cart( $cart_item['forced_by'] );

				if ( ! empty( $product_key ) ) {
					$product_name = WC()->cart->cart_contents[ $product_key ]['data']->get_title();
					$data[]       = array(
						'name'    => __( 'Linked to', 'woocommerce-force-sells' ),
						'display' => $product_name,
					);
				}
			}

			return $data;
		}

		/**
		 * Remove link in cart item for Synced Force Sells products.
		 *
		 * @param string $link          Remove link.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return string Link.
		 */
		public function cart_item_remove_link( $link, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['forced_by'] ) ) {
				return '';
			}

			return $link;
		}

		/**
		 * Makes quantity cart item for Synced Force Sells products uneditable.
		 *
		 * @param string $quantity      Quantity input.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return string Quantity input or static text of quantity.
		 */
		public function cart_item_quantity( $quantity, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['forced_by'] ) ) {
				return WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
			}

			return $quantity;
		}

		/**
		 * Render Force Sells and Synced Force Sells fields in Linked Products tab.
		 */
		public function write_panel_tab() {
			global $post;
			?>
			<p class="form-field">
				<label for="force_sell_ids"><?php esc_html_e( 'Force Sells', 'woocommerce-force-sells' ); ?></label>
				<select id="force_sell_ids" class="wc-product-search" multiple="multiple" style="width: 50%;" name="force_sell_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-force-sells' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>" data-exclude_type="variable">
					<?php
					$products = Product_Utils::get_force_sells( $post->ID, 'normal', 'objects' );

					foreach ( $products as $product ) :
						printf(
							'<option value="%1$s" selected="selected">%2$s</option>',
							esc_attr( $product->get_id() ),
							wp_kses_post( $product->get_formatted_name() )
						);
					endforeach;
					?>
				</select>
				<?php echo wc_help_tip( esc_html__( 'These products will be added to the cart when the main product is added. Quantity will not be synced in case the main product quantity changes.', 'woocommerce-force-sells' ) ); ?>
			</p>
			<p class="form-field">
				<label for="force_sell_synced_ids"><?php esc_html_e( 'Synced Force Sells', 'woocommerce-force-sells' ); ?></label>
				<select id="force_sell_synced_ids" class="wc-product-search" multiple="multiple" style="width: 50%;" name="force_sell_synced_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-force-sells' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>" data-exclude_type="variable">
					<?php
					$products = Product_Utils::get_force_sells( $post->ID, 'synced', 'objects' );

					foreach ( $products as $product ) :
						printf(
							'<option value="%1$s" selected="selected">%2$s</option>',
							esc_attr( $product->get_id() ),
							wp_kses_post( $product->get_formatted_name() )
						);
					endforeach;
					?>
				</select>
				<?php echo wc_help_tip( esc_html__( 'These products will be added to the cart when the main product is added and quantity will be synced in case the main product quantity changes.', 'woocommerce-force-sells' ) ); ?>
			</p>
			<?php
		}

		/**
		 * Save Force Sell Ids into post meta when product is saved.
		 *
		 * @param int $post_id Post ID.
		 */
		public function process_extra_product_meta( $post_id ) {
			// phpcs:disable WordPress.Security.NonceVerification
			foreach ( array( 'force_sell_ids', 'force_sell_synced_ids' ) as $field_name ) {
				$meta_name = "_$field_name";

				if ( isset( $_POST[ $field_name ] ) ) {
					$force_sells = array_filter( array_map( 'absint', wp_unslash( $_POST[ $field_name ] ) ) );

					update_post_meta( $post_id, $meta_name, $force_sells );
				} else {
					delete_post_meta( $post_id, $meta_name );
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Displays information of what linked products that will get added when current
		 * product is added to cart.
		 */
		public function show_force_sell_products() {
			global $post;

			$force_sells = Product_Utils::get_valid_force_sells( $post->ID, '', 'objects' );

			if ( ! $force_sells ) {
				return;
			}

			echo '<div class="clear"></div>';
			echo '<div class="wc-force-sells">';
			echo '<p>' . esc_html__( 'This will also add the following products to your cart:', 'woocommerce-force-sells' ) . '</p>';
			echo '<ul>';

			foreach ( $force_sells as $force_sell ) {
				echo '<li>' . esc_html( $force_sell->get_title() ) . '</li>';
			}

			echo '</ul></div>';
		}

		/**
		 * Add linked products when current product is added to the cart.
		 *
		 * @param string $cart_item_key  Cart item key.
		 * @param int    $product_id     Product ID.
		 * @param int    $quantity       Quantity added to cart.
		 * @param int    $variation_id   Product variation ID.
		 * @param array  $variation      Attribute values.
		 * @param array  $cart_item_data Extra cart item data.
		 *
		 * @throws Exception Notice message when the forced item is out of stock and parent isn't added.
		 */
		public function add_force_sell_items_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			// Check if this product is forced in itself, so it can't force in others (to prevent adding in loops).
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['forced_by'] ) ) {
				$forced_by_key = WC()->cart->cart_contents[ $cart_item_key ]['forced_by'];

				if ( isset( WC()->cart->cart_contents[ $forced_by_key ] ) ) {
					return;
				}
			}

			// Check if this product is already forcing a cart item. If so, we don't need to handle add to cart logic because qty will be updated by update_force_sell_quantity_in_cart.
			foreach ( WC()->cart->cart_contents as $value ) {
				if ( isset( $value['forced_by'] ) && $cart_item_key === $value['forced_by'] ) {
					return;
				}
			}

			// Don't force products on the manual payment page (they are already forced when creating the order).
			if ( is_checkout_pay_page() ) {
				return;
			}

			$product        = wc_get_product( $product_id );
			$force_sell_ids = Product_Utils::get_valid_force_sells( $product_id );
			$synced_ids     = Product_Utils::get_valid_force_sells( $product_id, 'synced' );

			if ( ! empty( $force_sell_ids ) ) {
				foreach ( $force_sell_ids as $id ) {
					$args = array();

					if ( $synced_ids ) {
						if ( in_array( $id, $synced_ids, true ) ) {
							$args['forced_by'] = $cart_item_key;
						}
					}

					$params = apply_filters( 'wc_force_sell_add_to_cart_product', array( 'id' => $id, 'quantity' => $quantity, 'variation_id' => '', 'variation' => '' ), WC()->cart->cart_contents[ $cart_item_key ] );
					$result = WC()->cart->add_to_cart( $params['id'], $params['quantity'], $params['variation_id'], $params['variation'], $args );

					// If the forced sell product was not able to be added, don't add the main product either. "Can be filtered".
					if ( empty( $result ) && apply_filters( 'wc_force_sell_disallow_no_stock', true ) ) {
						WC()->cart->remove_cart_item( $cart_item_key );
						/* translators: %s: Product title */
						throw new Exception( sprintf( esc_html__( '%s will also be removed as they\'re sold together.', 'woocommerce-force-sells' ), $product->get_title() ) );
					}
				}
			}
		}

		/**
		 * When a product in the cart has it's quantity updated, update any products it is forcing to match.
		 *
		 * @param string $cart_item_key Cart item key.
		 * @param int    $quantity      Quantity.
		 */
		public function update_force_sell_quantity_in_cart( $cart_item_key, $quantity = 0 ) {
			if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
				if ( 0 === $quantity || 0 > $quantity ) {
					$quantity = 0;
				} else {
					$quantity = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
				}

				foreach ( WC()->cart->cart_contents as $key => $value ) {
					if ( isset( $value['forced_by'] ) && $cart_item_key === $value['forced_by'] ) {
						$new_quantity = apply_filters( 'wc_force_sell_update_quantity', $quantity, WC()->cart->cart_contents[ $key ] );
						WC()->cart->set_quantity( $key, $new_quantity );
					}
				}
			}
		}

		/**
		 * When an item gets removed from the cart, do the same for forced sells.
		 *
		 * @param string $cart_item_key Cart item key.
		 */
		public function cart_item_removed( $cart_item_key ) {
			foreach ( WC()->cart->get_cart() as $key => $value ) {
				if ( isset( $value['forced_by'] ) && $cart_item_key === $value['forced_by'] ) {
					WC()->cart->remove_cart_item( $key );
				}
			}
		}

		/**
		 * When an item gets removed from the cart, do the same for forced sells.
		 *
		 * @param string $cart_item_key Cart item key.
		 */
		public function cart_item_restored( $cart_item_key ) {
			foreach ( WC()->cart->removed_cart_contents as $key => $value ) {
				if ( isset( $value['forced_by'] ) && $cart_item_key === $value['forced_by'] ) {
					WC()->cart->restore_cart_item( $key );
				}
			}
		}
	}

endif;
