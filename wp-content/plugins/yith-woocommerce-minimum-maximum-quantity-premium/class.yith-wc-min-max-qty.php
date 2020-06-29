<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_WC_Min_Max_Qty
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_WC_Min_Max_Qty' ) ) {

	class YITH_WC_Min_Max_Qty {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WC_Min_Max_Qty
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-minimum-maximum-quantity/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-minimum-maximum-quantity/';

		/**
		 * @var string YITH WooCommerce Minimum Maximum Quantity panel page
		 */
		protected $_panel_page = 'yith-wc-min-max-qty';

		/**
		 * @var string message container for notifications
		 */
		public $message_filter = '';

		/**
		 * @var boolean
		 */
		var $excluded_products = false;

		/**
		 * @var boolean
		 */
		var $product_with_errors = false;

		/**
		 * @var string id for Minimum Maximum tab in product edit page
		 */
		var $_product_tab = 'yith_min_max_qty';

		public $contents_to_validate = null;
		public $contents_type = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Min_Max_Qty
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			//Load plugin framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWMMQ_DIR . '/' . basename( YWMMQ_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );

			$this->includes();

			if ( is_admin() ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
				add_action( 'ywmmq_howto', array( $this, 'get_howto_content' ) );
				add_action( 'ywmmq_bulk_operations', array( $this, 'get_bulk_tabs' ) );

				add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'add_ywmmq_tab' ), 98 );
				add_action( 'woocommerce_product_data_panels', array( $this, 'ywmmq_write_tab_options' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save_ywmmq_tab' ), 10, 2 );

				add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_ywmmq_variations' ), 10, 3 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_ywmmq_variations' ), 10, 2 );

				add_action( 'product_cat_edit_form_fields', array( $this, 'ywmmq_write_category_options' ), 99 );
				add_action( 'product_tag_edit_form_fields', array( $this, 'ywmmq_write_tag_options' ), 99 );

				add_action( 'edited_product_cat', array( $this, 'ywmmq_save_category_options' ) );
				add_action( 'edited_product_tag', array( $this, 'ywmmq_save_tag_options' ) );

			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				add_action( 'wp', array( $this, 'ywmmq_cart_validation' ) );
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'ywmmq_checkout_validation' ), 10, 2 );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'ywmmq_add_to_cart_validation' ), 11, 6 );
				add_filter( 'woocommerce_cart_item_name', array( $this, 'ywmmq_cart_notification_products' ), 10, 3 );
				add_filter( 'ywraq_quote_item_name', array( $this, 'ywmmq_cart_notification_products' ), 10, 3 );
				add_filter( 'ywmmq_additional_notification', array( $this, 'ywmmq_cart_additional_notification' ), 10 );

				add_action( 'woocommerce_before_main_content', array( $this, 'ywmmq_show_rules' ), 5 );

				//Compatibility mode for X-theme
				if ( class_exists( 'TCO_1_0' ) ) {

					add_action( 'woocommerce_before_single_product', array( $this, 'ywmmq_show_rules' ), 5 );

				}

				add_filter( 'woocommerce_quantity_input_max', array( $this, 'ywmmq_max_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_min', array( $this, 'ywmmq_min_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_step', array( $this, 'ywmmq_step_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_quantity_input_max', array( $this, 'ywmmq_max_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_quantity_input_min', array( $this, 'ywmmq_min_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_quantity_input_step', array( $this, 'ywmmq_step_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_available_variation', array( $this, 'ywmmq_set_variable_quantity' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_args', array( $this, 'ywmmq_set_quantity' ), 10, 2 );
				add_filter( 'ywraq_request_validate_fields', array( $this, 'ywmmq_validate_raq' ) );
			}

			add_filter( 'ywmmq_check_exclusion', array( $this, 'ywmmq_check_exclusion' ), 10, 3 );

			if ( $this->is_wcpb_active() ) {

				add_filter( 'ywmmq_bundle_check', array( $this, 'ywmmq_bundle_check' ), 10, 2 );

			}

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		private function includes() {

			include_once( 'includes/class-ywmmq-error-messages.php' );
			include_once( 'includes/class-ywmmq-ajax.php' );

			if ( is_admin() ) {

				include( 'includes/class-yith-custom-table.php' );
				include_once( 'templates/admin/ywmmq-products-bulk-ops.php' );
				include_once( 'templates/admin/ywmmq-categories-bulk-ops.php' );
				include_once( 'templates/admin/ywmmq-tags-bulk-ops.php' );

			}

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'  => __( 'General Settings', 'yith-woocommerce-minimum-maximum-quantity' ),
				'messages' => __( 'Message Settings', 'yith-woocommerce-minimum-maximum-quantity' ),
				'bulk'     => __( 'Bulk Actions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'howto'    => __( 'How To', 'yith-woocommerce-minimum-maximum-quantity' )
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'Minimum Maximum Quantity', 'plugin name in admin page title', 'yith-woocommerce-minimum-maximum-quantity' ),
				'menu_title'       => 'Minimum Maximum Quantity',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWMMQ_DIR . 'plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Validates cart and checkout on page load.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_validation() {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return;
			}

			$on_cart_page     = is_page( wc_get_page_id( 'cart' ) );
			$on_quote_page    = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			$on_checkout_page = is_checkout() && ! is_checkout_pay_page() && ! is_order_received_page();

			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) == 'yes' && $on_quote_page && function_exists( 'YITH_Request_Quote' ) ) {
				$this->contents_type        = 'quote';
				$this->contents_to_validate = YITH_Request_Quote()->get_raq_return();
			} else {
				$this->contents_type        = 'cart';
				$this->contents_to_validate = WC()->cart->cart_contents;
			}

			if ( $on_cart_page || $on_checkout_page || $on_quote_page ) {

				if ( function_exists( 'YITH_Request_Quote' ) ) {

					if ( YITH_YWRAQ_Order_Request()->get_current_order_id() ) {
						return;
					}

				}

				$cart_update_notice = __( 'Cart updated.', 'woocommerce' );
				$cart_update        = wc_has_notice( $cart_update_notice );
				wc_clear_notices();

				if ( $this->contents_to_validate ) {

					if ( $on_cart_page || $on_quote_page ) {
						$current_page = 'cart';
					} else {
						$current_page = '';
					}

					$errors = $this->ywmmq_validate( $current_page, ( $on_cart_page || $on_quote_page ) );

					if ( $errors ) {

						ob_start();

						?>

						<ul>
							<?php foreach ( $errors as $error ): ?>
								<li><?php echo $error ?></li>
							<?php endforeach; ?>
							<?php echo apply_filters( 'ywmmq_additional_notification', '' ); ?>
						</ul>

						<?php

						$error_list = ob_get_clean();

						wc_add_notice( $error_list, 'error' );

					}

					if ( $cart_update ) {
						wc_add_notice( $cart_update_notice );
					}

				}

			}

		}

		/**
		 * Validates cart contents.
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate( $current_page, $on_cart_page ) {

			$errors = array();

			$is_premium = defined( 'YWMMQ_PREMIUM' ) && YWMMQ_PREMIUM;

			if ( $is_premium && get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$this->ywmmq_product_quantity_cart( $current_page, $on_cart_page, $errors );

			}

			if ( get_option( 'ywmmq_cart_quantity_limit', 'yes' ) == 'yes' ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_cart_quantity( $current_page ), $on_cart_page, $errors );

			}

			if ( $is_premium && get_option( 'ywmmq_cart_value_limit' ) == 'yes' ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_cart_value( $current_page ), $on_cart_page, $errors );

			}

			if ( $is_premium && get_option( 'ywmmq_category_quantity_limit' ) == 'yes' ) {

				$this->ywmmq_category_quantity_cart( $current_page, $on_cart_page, $errors );

			}

			if ( $is_premium && get_option( 'ywmmq_category_value_limit' ) == 'yes' ) {

				$this->ywmmq_category_value_cart( $current_page, $on_cart_page, $errors );

			}

			if ( $is_premium && get_option( 'ywmmq_tag_quantity_limit' ) == 'yes' ) {

				$this->ywmmq_tag_quantity_cart( $current_page, $on_cart_page, $errors );

			}

			if ( $is_premium && get_option( 'ywmmq_tag_value_limit' ) == 'yes' ) {

				$this->ywmmq_tag_value_cart( $current_page, $on_cart_page, $errors );

			}

			return $errors;

		}

		/**
		 * Validates cart.
		 *
		 * @param   $data
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_checkout_validation( $data, $errors ) {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return;
			}

			if ( function_exists( 'YITH_Request_Quote' ) ) {

				if ( YITH_YWRAQ_Order_Request()->get_current_order_id() ) {
					return;
				}

			}

			$mmq_errors = $this->ywmmq_validate( 'cart', true );

			if ( $mmq_errors ) {
				$errors->add( 'ywmmq', implode( '<br/>', $mmq_errors ) );
			}

		}

		/**
		 * Check the return value, if it is invalid returns an error message
		 *
		 * @param   $data
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return   void
		 * @since    1.0.0
		 *
		 * @author   Alberto Ruggiero
		 */
		public function ywmmq_check_validation_cart( $data, $on_cart_page, &$errors ) {

			if ( ! $data['is_valid'] ) {

				if ( $on_cart_page ) {

					$errors[] = $data['message'];

				} else {

					$cart_url = wc_get_cart_url();

					wp_safe_redirect( $cart_url, 302 );
					exit;

				}

			}

		}

		/**
		 * CART QUANTITY RULES FUNCTIONS
		 */

		/**
		 * Validate the cart quantity limit and return error messages
		 *
		 * @param   $current_page
		 * @param   $added_qty
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_cart_quantity( $current_page = '', $added_qty = 0 ) {

			$return = array(
				'is_valid' => true
			);

			$cart_limit = $this->ywmmq_cart_limits( 'quantity' );

			$total_cart_qty = $this->ywmmq_cart_total_qty();
			$total_cart_qty += $added_qty;

			if ( (int) $cart_limit['min'] != 0 && $total_cart_qty < (int) $cart_limit['min'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'min';

				if ( $current_page ) {

					$return['message'] = apply_filters( 'ywmmq_cart_qty_error', sprintf( __( 'Your cart must contain at least %s products.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_limit['min'] ), 'min', $cart_limit['min'], $total_cart_qty, $current_page, 'quantity' );

				}

			} elseif ( (int) $cart_limit['max'] != 0 && $total_cart_qty > (int) $cart_limit['max'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'max';

				if ( $current_page ) {

					$return['message'] = apply_filters( 'ywmmq_cart_qty_error', sprintf( __( 'Your cart cannot contain more than %s products.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_limit['max'] ), 'max', $cart_limit['max'], $total_cart_qty, $current_page, 'quantity' );

				}

			} elseif ( (int) $cart_limit['step'] > 1 && fmod( $total_cart_qty, (int) $cart_limit['step'] ) > 0 ) {

				$return['is_valid'] = false;
				$return['limit']    = 'step';

				if ( $current_page ) {

					$return['message'] = apply_filters( 'ywmmq_cart_qty_error', sprintf( __( 'Your cart must contain products in group of %s.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_limit['step'] ), 'step', $cart_limit['step'], $total_cart_qty, $current_page, 'quantity' );

				}

			}

			return $return;

		}

		/**
		 * Return the total quantity of all items in the cart
		 *
		 * @return  int
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_total_qty() {

			$total_qty = 0;

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_bundle_check', false, $item ) ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					$total_qty += $item['quantity'];

				}
			}

			return $total_qty;

		}

		/**
		 * Return quantity/value limits for specified category
		 *
		 * @param   $type
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_limits( $type = 'quantity' ) {

			$limit = array(
				'min'  => get_option( 'ywmmq_cart_minimum_' . $type ),
				'max'  => get_option( 'ywmmq_cart_maximum_' . $type ),
				'step' => get_option( 'ywmmq_cart_step_' . $type )
			);

			return $limit;

		}

		/**
		 * WC 2.6 BACKWARD COMPATIBILITY FUNCTIONS
		 */

		/**
		 * Get ter meta function based on WC Version
		 *
		 * @param   $term_id
		 * @param   $key
		 * @param   $single
		 *
		 * @return  mixed
		 * @since   1.5.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_term_meta( $term_id, $key = '', $single = false ) {


			return get_term_meta( $term_id, $key, $single );


		}

		/**
		 * Update term meta function based on WC Version
		 *
		 * @param   $term_id
		 * @param   $key
		 * @param   $single
		 *
		 * @return  void
		 * @since   1.5.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function update_term_meta( $term_id, $key = '', $single = false ) {


			update_term_meta( $term_id, $key, $single );


		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Initializes CSS and javascript
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'ywmmq-admin-premium', YWMMQ_ASSETS_URL . '/js/ywmmq-admin-premium' . $suffix . '.js', array( 'jquery' ) );

			wp_enqueue_style( 'ywmmq-admin-premium', YWMMQ_ASSETS_URL . '/css/ywmmq-admin-premium.css' );

		}

		/**
		 * Get placeholder reference content.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_howto_content() {

			?>
			<div id="plugin-fw-wc">
				<h3>
					<?php _e( 'Placeholder reference', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
				</h3>

				<p>
					<?php _e( 'For further information', 'yith-woocommerce-minimum-maximum-quantity' ); ?>:
					<a href="<?php echo $this->_official_documentation ?>" target="_blank"><?php _e( 'Plugin Documentation', 'yith-woocommerce-minimum-maximum-quantity' ) ?></a>
				</p>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{limit}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with not reached or exceeded quantity or spend restriction.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{cart_quantity}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with cart quantity.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{cart_value}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with cart value.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{product_name}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with product name.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{category_name}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with category name.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{tag_name}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with tag name.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{rules}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with active rules.', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<b>{cart_quote}</b>
						</th>
						<td class="forminp">
							<?php _e( 'Replaced with cart or quote list name (only if YITH WooCommerce Request a Quote Premium is active)', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<?php

		}

		/**
		 * Get content for bulk operations tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_bulk_tabs() {

			$sections        = array(
				'products'   => __( 'Products', 'yith-woocommerce-minimum-maximum-quantity' ),
				'categories' => __( 'Categories', 'yith-woocommerce-minimum-maximum-quantity' ),
				'tags'       => __( 'Tags', 'yith-woocommerce-minimum-maximum-quantity' ),
			);
			$array_keys      = array_keys( $sections );
			$current_section = isset( $_GET['section'] ) ? $_GET['section'] : 'products';

			?>
			<ul class="subsubsub">
				<?php

				foreach ( $sections as $id => $label ) :

					$query_args = array(
						'page'    => $_GET['page'],
						'tab'     => $_GET['tab'],
						'section' => $id
					);
					$section_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
					?>
					<li>
						<a href="<?php echo $section_url; ?>" class="<?php echo( $current_section == $id ? 'current' : '' ); ?>">
							<?php echo $label; ?>
						</a>
						<?php echo( end( $array_keys ) == $id ? '' : '|' ); ?>
					</li>
				<?php
				endforeach;
				?>
			</ul>
			<br class="clear" />
			<?php

			switch ( $current_section ) {

				case 'categories':
					YWMMQ_Categories_Bulk_Ops()->output();
					break;

				case 'tags':
					YWMMQ_Tags_Bulk_Ops()->output();
					break;

				default:
					YWMMQ_Products_Bulk_Ops()->output();

			}

		}

		/**
		 * Add YWMMQ tab in product edit page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_ywmmq_tab() {

			?>

			<li class="<?php echo $this->_product_tab; ?>_options <?php echo $this->_product_tab; ?>_tab">
				<a href="#<?php echo $this->_product_tab; ?>_tab"><?php echo _x( 'Minimum Maximum Quantity', 'plugin name in product edit tab', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
			</li>

			<?php

		}

		/**
		 * Add YWMMQ tab content in product edit page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_write_tab_options() {

			global $post;

			?>

			<div id="<?php echo $this->_product_tab; ?>_tab" class="panel woocommerce_options_panel">
				<div class="options_group ywmmq-product-tab">
					<?php

					woocommerce_wp_checkbox(
						array(
							'id'          => '_ywmmq_product_exclusion',
							'label'       => __( 'Exclude product', 'yith-woocommerce-minimum-maximum-quantity' ),
							'description' => __( 'Do not apply any of the plugin restrictions to this product', 'yith-woocommerce-minimum-maximum-quantity' )
						)
					);

					if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

						woocommerce_wp_checkbox(
							array(
								'id'          => '_ywmmq_product_quantity_limit_override',
								'label'       => __( 'Override product restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
								'description' => __( 'Global product restrictions will be overridden by these ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' )
							)
						);

						$product = wc_get_product( $post->ID );

						if ( $product->is_type( 'variable' ) ) {

							woocommerce_wp_checkbox(
								array(
									'id'          => '_ywmmq_product_quantity_limit_variations_override',
									'label'       => __( 'Enable variation restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
									'description' => __( 'Set plugin restrictions for product variation instead of for the entire product.', 'yith-woocommerce-minimum-maximum-quantity' )
								)
							);

						}

						$min_qty  = $product->get_meta( '_ywmmq_product_minimum_quantity' );
						$max_qty  = $product->get_meta( '_ywmmq_product_maximum_quantity' );
						$step_qty = $product->get_meta( '_ywmmq_product_step_quantity' );

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_minimum_quantity',
								'label'             => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-minimum',
								'value'             => ( $min_qty ? $min_qty : 0 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '0'
								)
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_maximum_quantity',
								'label'             => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-maximum',
								'value'             => ( $max_qty ? $max_qty : 0 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '0'
								)
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_step_quantity',
								'label'             => _x( 'Allow users to select products only in groups of', '[single product page]', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-step',
								'value'             => ( $step_qty ? $step_qty : 1 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '1'
								)
							)
						);
					}

					?>
				</div>
			</div>
			<?php

		}

		/**
		 * Save YWMMQ tab options
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function save_ywmmq_tab() {

			global $post;

			$product             = wc_get_product( $post->ID );
			$exclude             = isset( $_POST['_ywmmq_product_exclusion'] ) ? 'yes' : 'no';
			$override            = isset( $_POST['_ywmmq_product_quantity_limit_override'] ) ? 'yes' : 'no';
			$override_variations = isset( $_POST['_ywmmq_product_quantity_limit_variations_override'] ) ? 'yes' : 'no';
			$min_limit           = isset( $_POST['_ywmmq_product_minimum_quantity'] ) ? $_POST['_ywmmq_product_minimum_quantity'] : 0;
			$max_limit           = isset( $_POST['_ywmmq_product_maximum_quantity'] ) ? $_POST['_ywmmq_product_maximum_quantity'] : 0;
			$step                = isset( $_POST['_ywmmq_product_step_quantity'] ) ? $_POST['_ywmmq_product_step_quantity'] : 1;

			if ( $max_limit != 0 && $min_limit > $max_limit ) {

				$max_limit = 0;

			}

			$product->update_meta_data( '_ywmmq_product_exclusion', $exclude );
			$product->update_meta_data( '_ywmmq_product_quantity_limit_override', $override );
			$product->update_meta_data( '_ywmmq_product_quantity_limit_variations_override', $override_variations );
			$product->update_meta_data( '_ywmmq_product_minimum_quantity', esc_attr( $min_limit ) );
			$product->update_meta_data( '_ywmmq_product_maximum_quantity', esc_attr( $max_limit ) );
			$product->update_meta_data( '_ywmmq_product_step_quantity', esc_attr( $step ) );
			$product->save();

		}

		/**
		 * Add YWMMQ to product variation
		 *
		 * @param   $loop
		 * @param   $variation_data
		 * @param   $variation
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_ywmmq_variations( $loop, $variation_data, $variation ) {

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$variation_object = wc_get_product( $variation->ID );
				$min_qty          = $variation_object->get_meta( '_ywmmq_product_minimum_quantity' );
				$max_qty          = $variation_object->get_meta( '_ywmmq_product_maximum_quantity' );
				$step_qty         = $variation_object->get_meta( '_ywmmq_product_step_quantity' );

				?>
				<div class="ywmmq-variations-row">
					<?php

					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_minimum_quantity[' . $loop . ']',
							'label'             => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $min_qty ? $min_qty : 0 ),
							'wrapper_class'     => 'form-row-first',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0',
							)
						)
					);

					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_maximum_quantity[' . $loop . ']',
							'label'             => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $max_qty ? $max_qty : 0 ),
							'wrapper_class'     => 'form-row-last',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0'
							)
						)
					);

					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_step_quantity[' . $loop . ']',
							'label'             => _x( 'Allow users to select product variations only in groups of',
							                           '[single product page - variations]', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $step_qty ? $step_qty : 1 ),
							'wrapper_class'     => 'form-row-first',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '1'
							)
						)
					);

					?>
				</div>

				<?php

			}

		}

		/**
		 * Save YWMMQ of product variations
		 *
		 * @param   $variation_id
		 * @param   $loop
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_ywmmq_variations( $variation_id, $loop ) {

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$variation_object = wc_get_product( $variation_id );
				$min_limit        = ( isset( $_POST['_ywmmq_product_minimum_quantity'][ $loop ] ) ? $_POST['_ywmmq_product_minimum_quantity'][ $loop ] : 0 );
				$max_limit        = ( isset( $_POST['_ywmmq_product_maximum_quantity'][ $loop ] ) ? $_POST['_ywmmq_product_maximum_quantity'][ $loop ] : 0 );
				$step             = ( isset( $_POST['_ywmmq_product_step_quantity'][ $loop ] ) ? $_POST['_ywmmq_product_step_quantity'][ $loop ] : 1 );

				if ( $max_limit != 0 && $min_limit > $max_limit ) {

					$max_limit = 0;

				}

				$variation_object->update_meta_data( '_ywmmq_product_minimum_quantity', esc_attr( $min_limit ) );
				$variation_object->update_meta_data( '_ywmmq_product_maximum_quantity', esc_attr( $max_limit ) );
				$variation_object->update_meta_data( '_ywmmq_product_step_quantity', esc_attr( $step ) );
				$variation_object->save();

			}

		}

		/**
		 * Add YWMMQ fields in category edit page
		 *
		 * @param   $category
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_write_category_options( $category ) {

			$exclusion = get_term_meta( $category->term_id, '_ywmmq_category_exclusion', true ) == 'yes' ? 'checked' : '';

			?>
			<tr>
				<th colspan="2">
					<h3><?php _e( 'Category restrictions', 'yith-woocommerce-minimum-maximum-quantity' ) ?></h3></th>
			</tr>
			<tr class="form-field">
				<th>
					<label for="_ywmmq_category_exclusion"><?php _e( 'Exclude category', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="_ywmmq_category_exclusion" id="_ywmmq_category_exclusion" <?php echo $exclusion; ?> />

					<p class="description"><?php _e( 'Do not apply restrictions to product belonging to this category', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
				</td>
			</tr>
			<?php

			if ( get_option( 'ywmmq_category_quantity_limit' ) == 'yes' ) {

				$override_quantity = get_term_meta( $category->term_id, '_ywmmq_category_quantity_limit_override', true ) == 'yes' ? 'checked' : '';
				$quantity_limit    = $this->ywmmq_category_limits( $category->term_id, 'quantity' );
				?>

				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_quantity_limit_override"><?php _e( 'Override quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="_ywmmq_category_quantity_limit_override" id="_ywmmq_category_quantity_limit_override" <?php echo $override_quantity; ?> />

						<p class="description"><?php _e( 'Global category quantity restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_minimum_quantity"><?php _e( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="number" min="0" step="1" placeholder="0" value="<?php echo $quantity_limit['min']; ?>" name="_ywmmq_category_minimum_quantity" id="_ywmmq_category_minimum_quantity" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_maximum_quantity"><?php _e( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="number" min="0" step="1" placeholder="0" value="<?php echo $quantity_limit['max']; ?>" name="_ywmmq_category_maximum_quantity" id="_ywmmq_category_maximum_quantity" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_step_quantity"><?php _e( 'Products belonging to this category can be purchased only in groups of', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="number" min="1" step="1" placeholder="0" value="<?php echo $quantity_limit['step']; ?>" name="_ywmmq_category_step_quantity" id="_ywmmq_category_step_quantity" />
					</td>
				</tr>

				<?php

			}

			if ( get_option( 'ywmmq_category_value_limit' ) == 'yes' ) {

				$override_value = get_term_meta( $category->term_id, '_ywmmq_category_value_limit_override', true ) == 'yes' ? 'checked' : '';
				$value_limit    = $this->ywmmq_category_limits( $category->term_id, 'value' ); ?>

				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_value_limit_override"><?php _e( 'Override spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="_ywmmq_category_value_limit_override" id="_ywmmq_category_value_limit_override" <?php echo $override_value; ?> />

						<p class="description"><?php _e( 'Global category spend restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_minimum_value"><?php _e( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
					</th>
					<td>
						<input type="text" class="wc_input_price" placeholder="0" value="<?php echo $value_limit['min']; ?>" name="_ywmmq_category_minimum_value" id="_ywmmq_category_minimum_value" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_category_maximum_value"><?php _e( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
					</th>
					<td>
						<input type="text" class="wc_input_price" placeholder="0" value="<?php echo $value_limit['max']; ?>" name="_ywmmq_category_maximum_value" id="_ywmmq_category_maximum_value" />
					</td>
				</tr>

				<?php

			}

		}

		/**
		 * Save YWMMQ category options
		 *
		 * @param   $category_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_save_category_options( $category_id ) {

			if ( ! $category_id ) {
				return;
			}

			$exclude            = isset( $_POST['_ywmmq_category_exclusion'] ) ? 'yes' : 'no';
			$override_quantity  = isset( $_POST['_ywmmq_category_quantity_limit_override'] ) ? 'yes' : 'no';
			$override_value     = isset( $_POST['_ywmmq_category_value_limit_override'] ) ? 'yes' : 'no';
			$min_quantity_limit = isset( $_POST['_ywmmq_category_minimum_quantity'] ) ? $_POST['_ywmmq_category_minimum_quantity'] : 0;
			$max_quantity_limit = isset( $_POST['_ywmmq_category_maximum_quantity'] ) ? $_POST['_ywmmq_category_maximum_quantity'] : 0;
			$step_quantity      = isset( $_POST['_ywmmq_category_step_quantity'] ) ? $_POST['_ywmmq_category_step_quantity'] : 1;
			$min_value_limit    = isset( $_POST['_ywmmq_category_minimum_value'] ) ? $_POST['_ywmmq_category_minimum_value'] : 0;
			$max_value_limit    = isset( $_POST['_ywmmq_category_maximum_value'] ) ? $_POST['_ywmmq_category_maximum_value'] : 0;

			update_term_meta( $category_id, '_ywmmq_category_exclusion', $exclude );
			update_term_meta( $category_id, '_ywmmq_category_quantity_limit_override', $override_quantity );
			update_term_meta( $category_id, '_ywmmq_category_value_limit_override', $override_value );

			if ( $min_quantity_limit != 0 && $min_quantity_limit > $max_quantity_limit ) {

				$max_quantity_limit = 0;

			}

			if ( $min_value_limit != 0 && $min_value_limit > $max_value_limit ) {

				$max_value_limit = 0;

			}

			update_term_meta( $category_id, '_ywmmq_category_minimum_quantity', $min_quantity_limit );
			update_term_meta( $category_id, '_ywmmq_category_maximum_quantity', $max_quantity_limit );
			update_term_meta( $category_id, '_ywmmq_category_step_quantity', $step_quantity );
			update_term_meta( $category_id, '_ywmmq_category_minimum_value', $min_value_limit );
			update_term_meta( $category_id, '_ywmmq_category_maximum_value', $max_value_limit );

		}

		/**
		 * Add YWMMQ fields in tag edit page
		 *
		 * @param   $tag
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_write_tag_options( $tag ) {

			$exclusion = get_term_meta( $tag->term_id, '_ywmmq_tag_exclusion', true ) == 'yes' ? 'checked' : '';

			?>
			<tr>
				<th colspan="2"><h3><?php _e( 'Tag restrictions', 'yith-woocommerce-minimum-maximum-quantity' ) ?></h3>
				</th>
			</tr>
			<tr class="form-field">
				<th>
					<label for="_ywmmq_tag_exclusion"><?php _e( 'Exclude tag', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="_ywmmq_tag_exclusion" id="_ywmmq_tag_exclusion" <?php echo $exclusion; ?> />

					<p class="description"><?php _e( 'Do not apply restrictions to products with this tag', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
				</td>
			</tr>
			<?php

			if ( get_option( 'ywmmq_tag_quantity_limit' ) == 'yes' ) {

				$override_quantity = get_term_meta( $tag->term_id, '_ywmmq_tag_quantity_limit_override', true ) == 'yes' ? 'checked' : '';
				$quantity_limit    = $this->ywmmq_tag_limits( $tag->term_id, 'quantity' );
				?>

				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_quantity_limit_override"><?php _e( 'Override quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="_ywmmq_tag_quantity_limit_override" id="_ywmmq_tag_quantity_limit_override" <?php echo $override_quantity; ?> />

						<p class="description"><?php _e( 'Global tag quantity restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_minimum_quantity"><?php _e( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>

					</th>
					<td>
						<input type="number" min="0" step="1" placeholder="0" value="<?php echo $quantity_limit['min']; ?>" name="_ywmmq_tag_minimum_quantity" id="_ywmmq_tag_minimum_quantity" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_maximum_quantity"><?php _e( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>

					</th>
					<td>
						<input type="number" min="0" step="1" placeholder="0" value="<?php echo $quantity_limit['max']; ?>" name="_ywmmq_tag_maximum_quantity" id="_ywmmq_tag_maximum_quantity" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_step_quantity"><?php _e( 'Products belonging to this tag can be purchased only in groups of', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="number" min="1" step="1" placeholder="0" value="<?php echo $quantity_limit['step']; ?>" name="_ywmmq_tag_step_quantity" id="_ywmmq_tag_step_quantity" />
					</td>
				</tr>

				<?php

			}

			if ( get_option( 'ywmmq_tag_value_limit' ) == 'yes' ) {

				$override_value = get_term_meta( $tag->term_id, '_ywmmq_tag_value_limit_override', true ) == 'yes' ? 'checked' : '';
				$value_limit    = $this->ywmmq_tag_limits( $tag->term_id, 'value' );

				?>

				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_value_limit_override"><?php _e( 'Override spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="_ywmmq_tag_value_limit_override" id="_ywmmq_tag_value_limit_override" <?php echo $override_value; ?> />

						<p class="description"><?php _e( 'Global spend restrictions for tag will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_minimum_value"><?php _e( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>

					</th>
					<td>
						<input type="text" class="wc_input_price" placeholder="0" value="<?php echo $value_limit['min']; ?>" name="_ywmmq_tag_minimum_value" id="_ywmmq_tag_minimum_value" />
					</td>
				</tr>
				<tr class="form-field">
					<th>
						<label for="_ywmmq_tag_maximum_value"><?php _e( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>

					</th>
					<td>
						<input type="text" class="wc_input_price" placeholder="0" value="<?php echo $value_limit['max']; ?>" name="_ywmmq_tag_maximum_value" id="_ywmmq_tag_maximum_value" />
					</td>
				</tr>

				<?php

			}
		}

		/**
		 * Save YWMMQ tag options
		 *
		 * @param   $tag_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_save_tag_options( $tag_id ) {

			if ( ! $tag_id ) {
				return;
			}

			$exclude            = isset( $_POST['_ywmmq_tag_exclusion'] ) ? 'yes' : 'no';
			$override_quantity  = isset( $_POST['_ywmmq_tag_quantity_limit_override'] ) ? 'yes' : 'no';
			$override_value     = isset( $_POST['_ywmmq_tag_value_limit_override'] ) ? 'yes' : 'no';
			$min_quantity_limit = isset( $_POST['_ywmmq_tag_minimum_quantity'] ) ? $_POST['_ywmmq_tag_minimum_quantity'] : 0;
			$max_quantity_limit = isset( $_POST['_ywmmq_tag_maximum_quantity'] ) ? $_POST['_ywmmq_tag_maximum_quantity'] : 0;
			$step_quantity      = isset( $_POST['_ywmmq_tag_step_quantity'] ) ? $_POST['_ywmmq_tag_step_quantity'] : 1;
			$min_value_limit    = isset( $_POST['_ywmmq_tag_minimum_value'] ) ? $_POST['_ywmmq_tag_minimum_value'] : 0;
			$max_value_limit    = isset( $_POST['_ywmmq_tag_maximum_value'] ) ? $_POST['_ywmmq_tag_maximum_value'] : 0;

			update_term_meta( $tag_id, '_ywmmq_tag_exclusion', $exclude );
			update_term_meta( $tag_id, '_ywmmq_tag_quantity_limit_override', $override_quantity );
			update_term_meta( $tag_id, '_ywmmq_tag_value_limit_override', $override_value );

			if ( $min_quantity_limit != 0 && $min_quantity_limit > $max_quantity_limit ) {

				$max_quantity_limit = 0;

			}

			if ( $min_value_limit != 0 && $min_value_limit > $max_value_limit ) {

				$max_value_limit = 0;

			}

			update_term_meta( $tag_id, '_ywmmq_tag_minimum_quantity', $min_quantity_limit );
			update_term_meta( $tag_id, '_ywmmq_tag_maximum_quantity', $max_quantity_limit );
			update_term_meta( $tag_id, '_ywmmq_tag_step_quantity', $step_quantity );
			update_term_meta( $tag_id, '_ywmmq_tag_minimum_value', $min_value_limit );
			update_term_meta( $tag_id, '_ywmmq_tag_maximum_value', $max_value_limit );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Enqueue frontend script files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function frontend_scripts() {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return;
			}

			global $post;

			if ( empty( $post ) ) {
				return;
			}

			$product_id = $post->ID;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $post->ID, $post->post_type, true, wpml_get_default_language() );
			}

			$product  = wc_get_product( $product_id );
			$is_cart  = ( $post->ID == wc_get_page_id( 'cart' ) );
			$is_quote = ( $post->ID == get_option( 'ywraq_page_id' ) );

			if ( $is_cart || $is_quote || $product ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_style( 'ywmmq-frontend-premium', YWMMQ_ASSETS_URL . '/css/ywmmq-frontend-premium.css' );

				if ( ! $product ) {

					wp_register_style( 'font-awesome', YWMMQ_ASSETS_URL . '/css/font-awesome.min.css', array(), '4.7.0' );
					wp_enqueue_style( 'font-awesome' );

				} else {

					if ( ! $product->is_type( 'simple' ) ) {

						wp_enqueue_script( 'ywmmq-frontend-premium', YWMMQ_ASSETS_URL . '/js/ywmmq-frontend-premium' . $suffix . '.js', array( 'jquery' ) );

						$args = array(
							'ajax_url'   => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
							'variations' => ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' && ( $product->get_meta( '_ywmmq_product_quantity_limit_override' ) == 'yes' && $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' ) == 'yes' ) || apply_filters( 'ywmmq_set_variation_quantity_locked', true ) == false )
						);

						wp_localize_script( 'ywmmq-frontend-premium', 'ywmmq', $args );

					}

				}

			}

		}

		/**
		 * Output icons next to products if there are notifications
		 *
		 * @param   $title
		 * @param   $cart_item
		 * @param   $cart_item_key
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_cart_notification_products( $title, $cart_item, $cart_item_key ) {

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) != 'yes' && $on_quote_page ) {
				return $title;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $title;
			}

			if ( isset( $this->contents_to_validate[ $cart_item_key ]['excluded'] ) && $this->contents_to_validate[ $cart_item_key ]['excluded'] == true && $this->excluded_products ) {
				return '<i class="fa fa-ban ywmmq-excluded"></i> ' . $title;
			}

			if ( isset( $this->contents_to_validate[ $cart_item_key ]['has_error'] ) && $this->contents_to_validate[ $cart_item_key ]['has_error'] == true && $this->product_with_errors ) {
				return '<i class="fa fa-exclamation-circle ywmmq-error"></i> ' . $title;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {
				return '<i class="fa fa-check-circle ywmmq-correct"></i> ' . $title;
			}

			return $title;

		}

		/**
		 * Output additional notification for explaining eventual icons next to products
		 *
		 * @param   $message string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_cart_additional_notification( $message ) {

			if ( $this->excluded_products || $this->product_with_errors ) {

				$message = '<li>&nbsp;</li>';

			}

			if ( $this->excluded_products ) {

				$message .= '<li>' . sprintf( __( 'Items marked with %s do not contribute to reaching the purchase objective set', 'yith-woocommerce-minimum-maximum-quantity' ), '<i class="fa fa-ban ywmmq-excluded"></i>' ) . '</li>';

			}

			if ( $this->product_with_errors ) {

				$message .= '<li>' . sprintf( __( 'Check items marked with %s', 'yith-woocommerce-minimum-maximum-quantity' ), '<i class="fa fa-exclamation-circle ywmmq-error"></i>' ) . '</li>';

			}

			return $message;
		}

		/**
		 * Set quantity limit for variation
		 *
		 * @param   $args
		 * @param   $product
		 *
		 * @return  array
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_set_variable_quantity( $args, $product ) {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $args;
			}

			$args['min_qty'] = apply_filters( 'woocommerce_quantity_input_min', $args['min_qty'], $product );
			$args['max_qty'] = apply_filters( 'woocommerce_quantity_input_max', $args['max_qty'], $product );

			return $args;

		}

		/**
		 * Set quantity limit
		 *
		 * @param   $args
		 * @param   $product
		 *
		 * @return  array
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_set_quantity( $args, $product ) {

			if ( ! $product ) {
				return $args;
			}

			if ( $product->is_type( 'variation' ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) == false ) {
				return $args;
			}

			$args['min_value'] = apply_filters( 'woocommerce_quantity_input_min', $args['min_value'], $product );
			$args['max_value'] = apply_filters( 'woocommerce_quantity_input_max', $args['max_value'], $product );
			$args['step']      = apply_filters( 'woocommerce_quantity_input_step', $args['step'], $product );

			return $args;

		}

		/**
		 * Check if page is cart or quote page
		 *
		 * @return  boolean
		 * @since   1.3.0
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_cart_or_raq() {

			global $post;
			$is_raq = false;

			if ( function_exists( 'YITH_Request_Quote' ) ) {

				$raq_page_id = YITH_Request_Quote()->get_raq_page_id();
				$is_raq      = $post && $post->ID === (int) $raq_page_id;
			}

			return is_cart() || $is_raq || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}

		/**
		 * Set maximum quantity
		 *
		 * @param   $value
		 * @param   $product
		 *
		 * @return  string
		 * @since   1.0.9
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_max_quantity_block( $value, $product ) {

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) != 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$variation_id  = ( $this->ywmmq_cart_or_raq() ? $product->get_id() : 0 );
				$product_limit = $this->ywmmq_product_limits( yit_get_base_product_id( $product ), $variation_id );

				if ( $product_limit['max'] > 0 ) {

					$value = $product_limit['max'];
				}

				if ( 'yith_wcpb_quantity_input_max' === current_filter() && $product->is_type( 'variable' ) && 'yes' === $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' ) ) {
					$value = 10;
				}

			}

			return $value;

		}

		/**
		 * Set minimum quantity
		 *
		 * @param   $value
		 * @param   $product
		 *
		 * @return  string
		 * @since   1.0.9
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_min_quantity_block( $value, $product ) {

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) != 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$variation_id  = ( $this->ywmmq_cart_or_raq() ? $product->get_id() : 0 );
				$product_limit = $this->ywmmq_product_limits( yit_get_base_product_id( $product ), $variation_id );

				if ( $product_limit['min'] > 0 ) {

					$value = $product_limit['min'];
				}

			}

			return $value;

		}

		/**
		 * Set step quantity
		 *
		 * @param   $value
		 * @param   $product
		 *
		 * @return  string
		 * @since   1.1.6
		 *
		 * @author  Alberto Ruggiero
		 *
		 */
		public function ywmmq_step_quantity_block( $value, $product ) {

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) != 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$variation_id  = ( $this->ywmmq_cart_or_raq() ? $product->get_id() : 0 );
				$product_limit = $this->ywmmq_product_limits( yit_get_base_product_id( $product ), $variation_id );

				if ( $product_limit['step'] > 1 ) {

					$value = $product_limit['step'];
				}

			}

			return $value;

		}

		/**
		 * Get the position and show YWMMQ rules in product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_show_rules() {

			if ( get_option( 'ywmmq_rules_enable' ) != 'no' ) {

				if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
					return;
				}

				$position = get_option( 'ywmmq_rules_position' );

				switch ( $position ) {

					case '1':
						$args = array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 15
						);
						break;

					case '2':
						$args = array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 25
						);
						break;

					case '3':
						$args = array(
							'hook'     => 'woocommerce_after_single_product_summary',
							'priority' => 5
						);
						break;

					default:
						$args = array(
							'hook'     => 'woocommerce_before_single_product',
							'priority' => 20
						);

				}

				$hook     = apply_filters( 'ywmmq_rules_hook', $args['hook'] );
				$priority = apply_filters( 'ywmmq_rules_priority', $args['priority'] );

				add_action( $hook, array( $this, 'ywmmq_add_rules_text' ), $priority );

			}

		}

		/**
		 * Add YWMMQ rules to product page
		 *
		 * @param   $product_id
		 * @param   $variation_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_add_rules_text( $product_id = 0, $variation_id = 0 ) {

			if ( $product_id == 0 ) {

				global $post;

				if ( empty( $post ) ) {
					return;
				}

				$product_id = $post->ID;

			}

			global $sitepress;
			$has_wpml   = ! empty( $sitepress ) ? true : false;
			$rules_text = '';

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return;
			}

			$rules_message = array();

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
				return;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

				$product_limit = $this->ywmmq_product_limits( $product_id, $variation_id );

				if ( $product_limit['min'] == 0 && $product_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_product_max_quantity_limit_label', sprintf( __( 'Maximum quantity allowed for this product: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['max'] ), $product_limit['max'] );

				} elseif ( $product_limit['max'] == 0 && $product_limit['min'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_product_min_quantity_limit_label', sprintf( __( 'Minimum quantity required for this product: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['min'] ), $product_limit['min'] );

				} elseif ( $product_limit['min'] > 0 && $product_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_product_min_max_quantity_limit_label', sprintf( __( 'Quantities allowed for this product: minimum %d - maximum %d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['min'], $product_limit['max'] ), $product_limit['min'], $product_limit['max'] );

				}

			}

			if ( get_option( 'ywmmq_cart_quantity_limit' ) == 'yes' ) {

				$cart_qty_limit = $this->ywmmq_cart_limits( 'quantity' );

				if ( $cart_qty_limit['min'] == 0 && $cart_qty_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_max_quantity_limit_label', sprintf( __( 'Cart can contain %d items at most.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['max'] ), $cart_qty_limit['max'] );

				} elseif ( $cart_qty_limit['max'] == 0 && $cart_qty_limit['min'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_min_quantity_limit_label', sprintf( __( 'Cart must contain %d items at least.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['min'] ), $cart_qty_limit['min'] );

				} elseif ( $cart_qty_limit['min'] > 0 && $cart_qty_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_min_max_quantity_limit_label', sprintf( __( 'Cart must contain at least %d items but no more than %d', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['min'], $cart_qty_limit['max'] ), $cart_qty_limit['min'], $cart_qty_limit['max'] );

				}

			}

			if ( get_option( 'ywmmq_cart_value_limit' ) == 'yes' ) {

				$cart_val_limit = $this->ywmmq_cart_limits( 'value' );

				if ( $cart_val_limit['min'] == 0 && $cart_val_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_max_value_limit_label', sprintf( __( 'Cart can contain no more than %s items.', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( $cart_val_limit['max'] ) ), wc_price( $cart_val_limit['max'] ) );

				} elseif ( $cart_val_limit['max'] == 0 && $cart_val_limit['min'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_min_value_limit_label', sprintf( __( 'Cart must contain %s items at least', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( $cart_val_limit['min'] ) ), wc_price( $cart_val_limit['min'] ) );

				} elseif ( $cart_val_limit['min'] > 0 && $cart_val_limit['max'] > 0 ) {

					$rules_message[] = apply_filters( 'ywmmq_cart_min_max_value_limit_label', sprintf( __( 'Cart must contain at least %s items but no more than %s', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( $cart_val_limit['min'] ), wc_price( $cart_val_limit['max'] ) ), wc_price( $cart_val_limit['min'] ), wc_price( $cart_val_limit['max'] ) );

				}

			}

			$product_categories = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'all' ) );

			foreach ( $product_categories as $category ) {

				$category_exclusion = get_term_meta( $category->term_id, '_ywmmq_category_exclusion', true );

				if ( $category_exclusion == 'yes' ) {
					return;
				}

				$category_link = '<a href="' . get_term_link( $category ) . '">' . $category->name . '</a>';

				if ( get_option( 'ywmmq_category_quantity_limit' ) == 'yes' ) {

					$category_qty_limit = $this->ywmmq_category_limits( $category->term_id, 'quantity' );

					if ( $category_qty_limit['min'] == 0 && $category_qty_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_max_quantity_limit_label', sprintf( __( 'Maximum quantity allowed for category %s: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['max'] ), $category_link, $category_qty_limit['max'] );

					} elseif ( $category_qty_limit['max'] == 0 && $category_qty_limit['min'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_min_quantity_limit_label', sprintf( __( 'Minimum quantity required for category %s: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['min'] ), $category_link, $category_qty_limit['min'] );

					} elseif ( $category_qty_limit['min'] > 0 && $category_qty_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_min_max_quantity_limit_label', sprintf( __( 'Quantities allowed for category %s: minimum %d - maximum %d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['min'], $category_qty_limit['max'] ), $category_link, $category_qty_limit['min'], $category_qty_limit['max'] );

					}

				}

				if ( get_option( 'ywmmq_category_value_limit' ) == 'yes' ) {

					$category_val_limit = $this->ywmmq_category_limits( $category->term_id, 'value' );

					if ( $category_val_limit['min'] == 0 && $category_val_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_max_value_limit_label', sprintf( __( 'Maximum spend allowed for category %s: %s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_val_limit['max'] ), $category_link, $category_val_limit['max'] );

					} elseif ( $category_val_limit['max'] == 0 && $category_val_limit['min'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_min_value_limit_label', sprintf( __( 'Minimum spend required for category %s: %s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, wc_price( $category_val_limit['min'] ) ), $category_link, wc_price( $category_val_limit['min'] ) );

					} elseif ( $category_val_limit['min'] > 0 && $category_val_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_category_min_max_value_limit_label', sprintf( __( 'Spend allowed for category %s: minimum %s - maximum %s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, wc_price( $category_val_limit['min'] ), wc_price( $category_val_limit['max'] ) ), $category_link, wc_price( $category_val_limit['min'] ), wc_price( $category_val_limit['max'] ) );

					}

				}

			}

			$product_tag = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'all' ) );

			foreach ( $product_tag as $tag ) {

				$tag_exclusion = get_term_meta( $tag->term_id, '_ywmmq_tag_exclusion', true );

				if ( $tag_exclusion == 'yes' ) {
					return;
				}

				$tag_link = '<a href="' . get_term_link( $tag ) . '">' . $tag->name . '</a>';

				if ( get_option( 'ywmmq_tag_quantity_limit' ) == 'yes' ) {

					$tag_qty_limit = $this->ywmmq_tag_limits( $tag->term_id, 'quantity' );

					if ( $tag_qty_limit['min'] == 0 && $tag_qty_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_max_quantity_limit_label', sprintf( __( 'Maximum quantity allowed for tag %s: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['max'] ), $tag_link, $tag_qty_limit['max'] );

					} elseif ( $tag_qty_limit['max'] == 0 && $tag_qty_limit['min'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_min_quantity_limit_label', sprintf( __( 'Minimum quantity required for tag %s: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['min'] ), $tag_link, $tag_qty_limit['min'] );

					} elseif ( $tag_qty_limit['min'] > 0 && $tag_qty_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_min_max_quantity_limit_label', sprintf( __( 'Quantities allowed for tag %s: minimum %d - maximum %d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['min'], $tag_qty_limit['max'] ), $tag_link, $tag_qty_limit['min'], $tag_qty_limit['max'] );

					}

				}

				if ( get_option( 'ywmmq_tag_value_limit' ) == 'yes' ) {

					$tag_val_limit = $this->ywmmq_tag_limits( $tag->term_id, 'value' );

					if ( $tag_val_limit['min'] == 0 && $tag_val_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_max_value_limit_label', sprintf( __( 'Maximum spend allowed for tag %s: %s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( $tag_val_limit['max'] ) ), $tag_link, wc_price( $tag_val_limit['max'] ) );

					} elseif ( $tag_val_limit['max'] == 0 && $tag_val_limit['min'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_min_value_limit_label', sprintf( __( 'Minimum spend required for tag %s: %s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( $tag_val_limit['min'] ) ), $tag_link, wc_price( $tag_val_limit['min'] ) );

					} elseif ( $tag_val_limit['min'] > 0 && $tag_val_limit['max'] > 0 ) {

						$rules_message[] = apply_filters( 'ywmmq_tag_min_max_value_limit_label', sprintf( __( 'Spend allowed for tag %s: minimum %s - maximum %s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( $tag_val_limit['min'] ), wc_price( $tag_val_limit['max'] ) ), $tag_link, wc_price( $tag_val_limit['min'] ), wc_price( $tag_val_limit['max'] ) );

					}

				}

			}

			if ( $rules_message ) {

				ob_start();

				?>
				<ul>
					<?php foreach ( $rules_message as $rule ): ?>
						<li><?php echo $rule; ?></li>
					<?php endforeach; ?>
				</ul>

				<?php $rules = ob_get_clean(); ?>

				<?php $rules_text = str_replace( '{rules}', $rules, get_option( 'ywmmq_rules_before_text' ) ); ?>

				<?php

			}

			?>
			<div class="ywmmq-rules-wrapper entry-summary">
				<?php echo $rules_text ?>
			</div>
			<?php

		}

		/**
		 * Add-to-cart validation.
		 *
		 * @param   $passed
		 * @param   $product_id
		 * @param   $quantity
		 * @param   $variation_id
		 * @param   $variation
		 * @param   $cart_item_data
		 *
		 * @return  boolean
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = 0, $variation = null, $cart_item_data = array() ) {

			if ( get_option( 'ywmmq_message_enable_atc' ) == 'yes' ) {

				if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
					return $passed;
				}

				$this->contents_type        = 'cart';
				$this->contents_to_validate = WC()->cart->cart_contents;

				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;

				if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					return $passed;
				}

				if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
					return $passed;
				}

				$error        = '';
				$current_page = 'atc';

				if ( get_option( 'ywmmq_product_quantity_limit' ) == 'yes' ) {

					$limit_var_override = $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' );

					if ( $variation_id && $limit_var_override == 'yes' ) {
						$cart_quantity = $this->ywmmq_cart_product_qty( $variation_id, true );
						$product_data  = array(
							'product_id'   => $product_id,
							'quantity'     => $cart_quantity + $quantity,
							'variation_id' => $variation_id,
							'variation'    => $variation
						);
					} else {

						$cart_quantity = $this->ywmmq_cart_product_qty( $product_id );
						$product_data  = array(
							'product_id'   => $product_id,
							'quantity'     => $cart_quantity + $quantity,
							'variation_id' => 0,
							'variation'    => false
						);
					}


					$this->ywmmq_check_validation_atc( $this->ywmmq_validate_product_quantity( $product_data, false, $current_page ), $error, $passed );
				}


				if ( $variation_id ) {
					$product = wc_get_product( $variation_id );

				}

				$bundle_quantity = 0;
				$product_value   = yit_get_price_including_tax( $product, $quantity );

				if ( $this->is_wcpb_active() ) {

					if ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) == 'elements' ) {

						$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

						foreach ( $cart_item_data['cartstamp'] as $item ) {

							$bundle_quantity += $item['quantity'];

						}

					}

				}

				if ( $passed && get_option( 'ywmmq_cart_quantity_limit' ) == 'yes' ) {

					$qty = ( $bundle_quantity == 0 ) ? $quantity : $bundle_quantity;

					$this->ywmmq_check_validation_atc( $this->ywmmq_validate_cart_quantity( $current_page, $qty ), $error, $passed );

				}

				if ( $passed && get_option( 'ywmmq_cart_value_limit' ) == 'yes' ) {

					$this->ywmmq_check_validation_atc( $this->ywmmq_validate_cart_value( $current_page, $product_value ), $error, $passed );

				}

				if ( $passed && get_option( 'ywmmq_category_quantity_limit' ) == 'yes' ) {

					if ( ! ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) == 'elements' ) ) {

						$cart_quantities = $this->ywmmq_cart_category_qty();
						$product_cats    = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

						foreach ( $product_cats as $cat_id ) {

							$total_quantity = ( array_key_exists( $cat_id, $cart_quantities ) ) ? $cart_quantities[ $cat_id ] + $quantity : $quantity;

							$this->ywmmq_check_validation_atc( $this->ywmmq_validate_category( $cat_id, $total_quantity, $current_page, 'quantity' ), $error, $passed );

						}

					}

				}

				if ( $passed && get_option( 'ywmmq_category_value_limit' ) == 'yes' ) {

					$cart_values  = $this->ywmmq_cart_category_value();
					$product_cats = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

					foreach ( $product_cats as $cat_id ) {

						$total_value = ( array_key_exists( $cat_id, $cart_values ) ) ? $cart_values[ $cat_id ] + (int) $product_value : $product_value;

						$this->ywmmq_check_validation_atc( $this->ywmmq_validate_category( $cat_id, $total_value, $current_page, 'value' ), $error, $passed );

					}

				}

				if ( $passed && get_option( 'ywmmq_tag_quantity_limit' ) == 'yes' ) {

					if ( ! ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) == 'elements' ) ) {

						$cart_quantities = $this->ywmmq_cart_tag_qty();
						$product_tags    = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

						foreach ( $product_tags as $tag_id ) {

							$total_quantity = ( array_key_exists( $tag_id, $cart_quantities ) ) ? $cart_quantities[ $tag_id ] + $quantity : $quantity;

							$this->ywmmq_check_validation_atc( $this->ywmmq_validate_tag( $tag_id, $total_quantity, $current_page, 'quantity' ), $error, $passed );

						}

					}

				}

				if ( $passed && get_option( 'ywmmq_tag_value_limit' ) == 'yes' ) {

					$cart_values  = $this->ywmmq_cart_tag_value();
					$product_tags = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

					foreach ( $product_tags as $tag_id ) {

						$total_value = ( array_key_exists( $tag_id, $cart_values ) ) ? $cart_values[ $tag_id ] + (int) $product_value : $product_value;

						$this->ywmmq_check_validation_atc( $this->ywmmq_validate_tag( $tag_id, $total_value, $current_page, 'value' ), $error, $passed );

					}

				}

				if ( ! empty( $error ) ) {

					if ( $passed ) {

						$this->message_filter = $error;
						add_filter( 'woocommerce_add_message', array( $this, 'ywmmq_add_to_cart_message' ) );

					} else {

						if ( function_exists( 'wc_add_notice' ) ) {

							wc_add_notice( $error, 'error' );

						}

					}

				}

			}

			return $passed;
		}

		/**
		 * Check the return value, if it is invalid returns an error message
		 *
		 * @param   $data
		 * @param   $error
		 * @param   $passed
		 *
		 * @return   string
		 * @since    1.0.0
		 *
		 * @author   Alberto Ruggiero
		 */
		public function ywmmq_check_validation_atc( $data, &$error, &$passed ) {

			if ( ! $data['is_valid'] ) {

				if ( $data['limit'] == 'min' ) {

					if ( empty( $error ) ) {

						$error = $data['message'];

					}

				} elseif ( $data['limit'] == 'step' ) {

					if ( empty( $error ) ) {

						$error = $data['message'];

					}

				} elseif ( $data['limit'] == 'max' ) {

					$passed = false;
					$error  = $data['message'];

				}

			}

		}

		/**
		 * Replace the default message on add to cart
		 *
		 * @param   $error
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_add_to_cart_message( $error ) {

			if ( ! empty( $this->message_filter ) ) {

				$error = $this->message_filter;

			}

			return $error;

		}

		/**
		 * PRODUCT RULES FUNCTIONS
		 */

		/**
		 * Validate the product quantity from cart page
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_product_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$variable_products = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $key => $item ) {

					if ( isset( $item['yith_wcp_child_component_data'] ) ||
					     ( isset( $item['yith_wapo_sold_individually'] ) && $item['yith_wapo_sold_individually'] == true ) ||
					     ( ! isset( $item['product_id'] ) || $key == 'cart' ) ||
					     apply_filters( 'ywmmq_bundle_check', false, $item )
					) {
						continue;
					}

					$this->contents_to_validate[ $key ]['excluded']  = false;
					$this->contents_to_validate[ $key ]['has_error'] = false;

					if ( apply_filters( 'ywmmq_check_exclusion', false, $key, $item['product_id'] ) ) {
						continue;
					}

					$product            = wc_get_product( $item['product_id'] );
					$limit_var_override = $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' );

					if ( isset( $item['variation_id'] ) && $item['variation_id'] && $limit_var_override != 'yes' ) {

						if ( array_key_exists( $item['product_id'], $variable_products ) ) {
							$variable_products[ $item['product_id'] ]['quantity'] += $item['quantity'];
							$variable_products[ $item['product_id'] ]['key'][]    = $key;

						} else {
							$variable_products[ $item['product_id'] ]['quantity'] = $item['quantity'];
							$variable_products[ $item['product_id'] ]['key']      = array( $key );
						}

					} else {

						$this->ywmmq_check_validation_cart( $this->ywmmq_validate_product_quantity( $item, $key, $current_page ), $on_cart_page, $errors );

					}

				}
			}

			if ( ! empty( $variable_products ) ) {

				foreach ( $variable_products as $parent_id => $info ) {
					$this->ywmmq_check_validation_cart( $this->ywmmq_validate_product_quantity( array( 'product_id' => $parent_id, 'quantity' => $info['quantity'] ), $info['key'], $current_page ), $on_cart_page, $errors );

				}

			}

		}

		/**
		 * Validate the product quantity limit and return error messages
		 *
		 * @param   $item
		 * @param   $key
		 * @param   $current_page
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_product_quantity( $item, $key = false, $current_page = '' ) {

			if ( ! isset( $item['product_id'] ) ) {
				return array();
			}

			$return = array(
				'is_valid' => true
			);

			if ( ! isset( $item['variation_id'] ) ) {

				$item['variation_id'] = 0;

			}

			if ( $key ) {

				if ( is_array( $key ) ) {

					foreach ( $key as $k ) {

						$this->contents_to_validate[ $k ]['has_error'] = false;

					}

				} else {

					$this->contents_to_validate[ $key ]['has_error'] = false;

				}

			}

			$product_limit = $this->ywmmq_product_limits( $item['product_id'], $item['variation_id'] );

			if ( (int) $product_limit['min'] != 0 && $item['quantity'] < (int) $product_limit['min'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'min';

				if ( $current_page ) {

					if ( $key ) {


						if ( is_array( $key ) ) {

							foreach ( $key as $k ) {

								$this->contents_to_validate[ $k ]['has_error'] = true;

							}

						} else {

							$this->contents_to_validate[ $key ]['has_error'] = true;

						}

						$this->product_with_errors = true;
					}

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_product_quantity_error( 'min', $product_limit['min'], $item, $current_page );

				}

			} elseif ( (int) $product_limit['max'] != 0 && $item['quantity'] > (int) $product_limit['max'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'max';

				if ( $current_page ) {

					if ( $key ) {
						if ( is_array( $key ) ) {

							foreach ( $key as $k ) {

								$this->contents_to_validate[ $k ]['has_error'] = true;

							}

						} else {

							$this->contents_to_validate[ $key ]['has_error'] = true;

						}
						$this->product_with_errors = true;
					}

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_product_quantity_error( 'max', $product_limit['max'], $item, $current_page );

				}

			} elseif ( (int) $product_limit['step'] > 1 && fmod( $item['quantity'], (int) $product_limit['step'] ) > 0 ) {

				$return['is_valid'] = false;
				$return['limit']    = 'step';

				if ( $current_page ) {

					if ( $key ) {
						if ( is_array( $key ) ) {

							foreach ( $key as $k ) {

								$this->contents_to_validate[ $k ]['has_error'] = true;

							}

						} else {

							$this->contents_to_validate[ $key ]['has_error'] = true;

						}
						$this->product_with_errors = true;
					}

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_product_quantity_error( 'step', $product_limit['step'], $item, $current_page );

				}

			}

			return $return;

		}

		/**
		 * Return quantity limit for specified product/variation
		 *
		 * @param   $product_id
		 * @param   $variation_id
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_product_limits( $product_id, $variation_id ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1
			);

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product            = wc_get_product( $product_id );
			$limit_override     = $product->get_meta( '_ywmmq_product_quantity_limit_override' );
			$limit_var_override = $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' );
			$limit_var_override = $variation_id == 0 ? 'no' : $limit_var_override;

			if ( $limit_override == 'yes' ) {

				if ( ( $variation_id > 0 ) && $limit_var_override == 'yes' ) {

					if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
						$variation_id = yit_wpml_object_id( $variation_id, 'product', true, wpml_get_default_language() );
					}

					$variation     = wc_get_product( $variation_id );
					$limit['min']  = $variation->get_meta( '_ywmmq_product_minimum_quantity' );
					$limit['max']  = $variation->get_meta( '_ywmmq_product_maximum_quantity' );
					$limit['step'] = $variation->get_meta( '_ywmmq_product_step_quantity' );

				} elseif ( ( $variation_id > 0 && $limit_var_override != 'yes' ) || ( $variation_id == 0 && $limit_var_override != 'yes' ) ) {

					$limit['min']  = $product->get_meta( '_ywmmq_product_minimum_quantity' );
					$limit['max']  = $product->get_meta( '_ywmmq_product_maximum_quantity' );
					$limit['step'] = $product->get_meta( '_ywmmq_product_step_quantity' );

				}

			} else {

				$limit['min']  = get_option( 'ywmmq_product_minimum_quantity' );
				$limit['max']  = get_option( 'ywmmq_product_maximum_quantity' );
				$limit['step'] = get_option( 'ywmmq_product_step_quantity' );

			}

			return apply_filters( 'ywmmq_override_product_limits', $limit, $product_id, $variation_id );

		}

		/**
		 * Return cart quantity for specified product.
		 *
		 * @param   $product_id
		 * @param   $is_variation
		 *
		 * @return  int
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_product_qty( $product_id, $is_variation = false ) {

			$cart = WC()->cart->get_cart();

			$cart_qty = 0;

			foreach ( $cart as $item_id => $item ) {

				if ( $is_variation ) {

					if ( $item['variation_id'] == $product_id ) {
						return $item['quantity'];
					}

				} else {

					if ( $item['product_id'] == $product_id ) {
						$cart_qty += $item['quantity'];
					}

				}
			}

			return $cart_qty;

		}

		/**
		 * CATEGORY RULES FUNCTIONS
		 */

		/**
		 * Validate the category quantity from cart page
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_category_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_quantities = $this->ywmmq_cart_category_qty();

			foreach ( $cart_quantities as $category_id => $quantity ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_category( $category_id, $quantity, $current_page, 'quantity' ), $on_cart_page, $errors );

			}
		}

		/**
		 * Validate the category value from cart page
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_category_value_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_values = $this->ywmmq_cart_category_value();

			foreach ( $cart_values as $category_id => $value ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_category( $category_id, $value, $current_page, 'value' ), $on_cart_page, $errors );

			}
		}

		/**
		 * Validate the category quantity/value limit and return error messages
		 *
		 * @param   $category_id
		 * @param   $qty_val
		 * @param   $current_page
		 * @param   $limit_type
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_category( $category_id, $qty_val, $current_page, $limit_type ) {

			$return = array(
				'is_valid' => true
			);

			$category_limit = $this->ywmmq_category_limits( $category_id, $limit_type );

			if ( (int) $category_limit['min'] != 0 && $qty_val < (int) $category_limit['min'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'min';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_category_error( 'min', $category_limit['min'], $category_id, $current_page, $limit_type );

				}

			} elseif ( (int) $category_limit['max'] != 0 && $qty_val > (int) $category_limit['max'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'max';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_category_error( 'max', $category_limit['max'], $category_id, $current_page, $limit_type );

				}

			} elseif ( $limit_type == 'quantity' && (int) $category_limit['step'] > 1 && fmod( $qty_val, (int) $category_limit['step'] ) > 0 ) {

				$return['is_valid'] = false;
				$return['limit']    = 'step';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_category_error( 'step', $category_limit['step'], $category_id, $current_page, $limit_type );

				}

			}

			return $return;

		}

		/**
		 * Return quantity/value limits for specified category
		 *
		 * @param   $category_id
		 * @param   $type
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_category_limits( $category_id, $type = 'quantity' ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1
			);

			$category_exclusion = get_term_meta( $category_id, '_ywmmq_category_exclusion', true );

			if ( $category_exclusion == 'yes' ) {
				return $limit;
			}

			$category_limit_override = get_term_meta( $category_id, '_ywmmq_category_' . $type . '_limit_override', true );

			if ( $category_limit_override == 'yes' ) {

				$limit['min']  = get_term_meta( $category_id, '_ywmmq_category_minimum_' . $type, true );
				$limit['max']  = get_term_meta( $category_id, '_ywmmq_category_maximum_' . $type, true );
				$limit['step'] = get_term_meta( $category_id, '_ywmmq_category_step_' . $type, true );

			} else {

				$limit['min']  = get_option( 'ywmmq_category_minimum_' . $type );
				$limit['max']  = get_option( 'ywmmq_category_maximum_' . $type );
				$limit['step'] = get_option( 'ywmmq_category_step_' . $type );

			}

			return $limit;

		}

		/**
		 * Return cart quantity for each category.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_category_qty() {

			$category_qts = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_bundle_check', false, $item ) ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					$product_categories = wp_get_object_terms( $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

					foreach ( $product_categories as $cat_id ) {

						if ( array_key_exists( $cat_id, $category_qts ) ) {
							$category_qts[ $cat_id ] += $item['quantity'];
						} else {
							$category_qts[ $cat_id ] = $item['quantity'];
						}

					}

				}

			}

			return $category_qts;

		}

		/**
		 * Return cart value for each category.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_category_value() {

			$category_values = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					$product_value      = round( $item['line_total'] + $item['line_tax'], wc_get_price_decimals() );
					$product_categories = wp_get_object_terms( $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

					foreach ( $product_categories as $cat_id ) {

						if ( array_key_exists( $cat_id, $category_values ) ) {
							$category_values[ $cat_id ] += (float) $product_value;
						} else {
							$category_values[ $cat_id ] = $product_value;
						}

					}

				}

			}

			return $category_values;

		}

		/**
		 * TAG RULES FUNCTIONS
		 */

		/**
		 * Validate the tag quantity from cart page
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_tag_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_quantities = $this->ywmmq_cart_tag_qty();

			foreach ( $cart_quantities as $tag_id => $quantity ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_tag( $tag_id, $quantity, $current_page, 'quantity' ), $on_cart_page, $errors );

			}
		}

		/**
		 * Validate the tag value from cart page
		 *
		 * @param   $current_page
		 * @param   $on_cart_page
		 * @param   $errors
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_tag_value_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_values = $this->ywmmq_cart_tag_value();

			foreach ( $cart_values as $tag_id => $value ) {

				$this->ywmmq_check_validation_cart( $this->ywmmq_validate_tag( $tag_id, $value, $current_page, 'value' ), $on_cart_page, $errors );

			}
		}

		/**
		 * Validate the tag quantity/value limit and return error messages
		 *
		 * @param   $tag_id
		 * @param   $qty_val
		 * @param   $current_page
		 * @param   $limit_type
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_tag( $tag_id, $qty_val, $current_page, $limit_type ) {

			$return = array(
				'is_valid' => true
			);

			$tag_limit = $this->ywmmq_tag_limits( $tag_id, $limit_type );

			if ( (int) $tag_limit['min'] != 0 && $qty_val < (int) $tag_limit['min'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'min';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_tag_error( 'min', $tag_limit['min'], $tag_id, $current_page, $limit_type );

				}

			} elseif ( (int) $tag_limit['max'] != 0 && $qty_val > (int) $tag_limit['max'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'max';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_tag_error( 'max', $tag_limit['max'], $tag_id, $current_page, $limit_type );

				}

			} elseif ( $limit_type == 'quantity' && (int) $tag_limit['step'] > 1 && fmod( $qty_val, (int) $tag_limit['step'] ) > 0 ) {

				$return['is_valid'] = false;
				$return['limit']    = 'step';

				if ( $current_page ) {

					$return['message'] = YWMMQ_Error_Messages()->ywmmq_tag_error( 'step', $tag_limit['step'], $tag_id, $current_page, $limit_type );

				}

			}

			return $return;

		}

		/**
		 * Return quantity/value limits for specified tag
		 *
		 * @param   $tag_id
		 * @param   $type
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_tag_limits( $tag_id, $type = 'quantity' ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1,
			);

			$tag_exclusion = get_term_meta( $tag_id, '_ywmmq_tag_exclusion', true );

			if ( $tag_exclusion == 'yes' ) {
				return $limit;
			}

			$tag_limit_override = get_term_meta( $tag_id, '_ywmmq_tag_' . $type . '_limit_override', true );

			if ( $tag_limit_override == 'yes' ) {

				$limit['min']  = get_term_meta( $tag_id, '_ywmmq_tag_minimum_' . $type, true );
				$limit['max']  = get_term_meta( $tag_id, '_ywmmq_tag_maximum_' . $type, true );
				$limit['step'] = get_term_meta( $tag_id, '_ywmmq_tag_step_' . $type, true );

			} else {

				$limit['min']  = get_option( 'ywmmq_tag_minimum_' . $type );
				$limit['max']  = get_option( 'ywmmq_tag_maximum_' . $type );
				$limit['step'] = get_option( 'ywmmq_tag_step_' . $type );

			}

			return $limit;

		}

		/**
		 * Return cart quantity for each tag.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_tag_qty() {

			$tag_qts = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_bundle_check', false, $item ) ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					$product_tag = wp_get_object_terms( $item['product_id'], 'product_tag', array( 'fields' => 'ids' ) );

					foreach ( $product_tag as $tag_id ) {

						if ( array_key_exists( $tag_id, $tag_qts ) ) {
							$tag_qts[ $tag_id ] += $item['quantity'];
						} else {
							$tag_qts[ $tag_id ] = $item['quantity'];
						}

					}

				}

			}

			return $tag_qts;

		}

		/**
		 * Return cart value for each tag.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_tag_value() {

			$tag_values = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					$product_value = round( $item['line_total'] + $item['line_tax'], wc_get_price_decimals() );
					$product_tag   = wp_get_object_terms( $item['product_id'], 'product_tag', array( 'fields' => 'ids' ) );

					foreach ( $product_tag as $tag_id ) {

						if ( array_key_exists( $tag_id, $tag_values ) ) {
							$tag_values[ $tag_id ] += (float) $product_value;
						} else {
							$tag_values[ $tag_id ] = $product_value;
						}

					}

				}
			}

			return $tag_values;

		}

		/**
		 * CART VALUE RULES FUNCTIONS
		 */

		/**
		 * Validate the cart quantity value and return error messages
		 *
		 * @param   $current_page
		 * @param   $added_value
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_cart_value( $current_page, $added_value = 0 ) {

			$return = array(
				'is_valid' => true
			);

			$cart_limit = $this->ywmmq_cart_limits( 'value' );

			if ( $cart_limit['min'] != 0 || $cart_limit['max'] != 0 ) {

				if ( $this->contents_type == 'cart' ) {
					if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
						define( 'WOOCOMMERCE_CART', true );
					}
					WC()->cart->calculate_totals();
				}

				$excluded_products_value = $this->ywmmq_cart_total_excluded_value();

				if ( $excluded_products_value > 0 ) {
					$this->excluded_products = true;
				}
				$calculate_taxes = apply_filters( 'ywmmq_calculate_taxes', true );

				if ( $this->contents_type == 'cart' ) {
					$cart_tax         = $calculate_taxes ? WC()->cart->get_subtotal_tax() : 0;
					$total_cart_value = apply_filters( 'yith_min_max_cart_total', ( WC()->cart->get_subtotal() + $cart_tax ) - $excluded_products_value, WC()->cart );
				} else {
					$total_cart_value = apply_filters( 'yith_min_max_cart_total_raq', ( $this->total_raq_value() ) - $excluded_products_value );
				}

				if ( get_option( 'ywmmq_cart_value_shipping' ) == 'yes' && $this->contents_type == 'cart' ) {
					$shipping_tax     = $calculate_taxes ? WC()->cart->get_shipping_tax() : 0;
					$total_cart_value += ( WC()->cart->get_shipping_total() + $shipping_tax );
				}

				if ( get_option( 'ywmmq_cart_value_calculate_coupons' ) == 'no' && $this->contents_type == 'cart' ) {
					$discount_tax     = $calculate_taxes ? WC()->cart->get_discount_tax() : 0;
					$total_cart_value -= ( WC()->cart->get_discount_total() + $discount_tax );
				}

				if ( get_option( 'ywmmq_cart_value_calculate_giftcard' ) == 'no' && $this->is_ywgc_active() && $this->contents_type == 'cart' ) {

					$gift_cards = WC()->cart->applied_gift_cards_amounts;
					$discount   = 0;

					if ( ! empty( $gift_cards ) ) {

						foreach ( $gift_cards as $code => $amount ) {

							$discount += $amount;

						}

					}

					$total_cart_value -= $this->get_gift_card_total( $total_cart_value );

				}

				$total_cart_value += $added_value;

				if ( $cart_limit['min'] != 0 && $total_cart_value < $cart_limit['min'] ) {

					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {

						$return['message'] = YWMMQ_Error_Messages()->ywmmq_cart_error( '', 'min', $cart_limit['min'], $total_cart_value, $current_page, 'value' );

					}

				} elseif ( $cart_limit['max'] != 0 && $total_cart_value > $cart_limit['max'] ) {

					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {

						$return['message'] = YWMMQ_Error_Messages()->ywmmq_cart_error( '', 'max', $cart_limit['max'], $total_cart_value, $current_page, 'value' );

					}

				}

			}

			return $return;

		}

		/**
		 * Return the total value of all items in the quote list
		 *
		 * @return  int
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function total_raq_value() {

			$total_value = 0;

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					$_product    = wc_get_product( $item['product_id'] );
					$total_value += round( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ), wc_get_rounding_precision() - wc_get_price_decimals() );

				}

			}

			return $total_value;

		}

		/**
		 * Return the total value of all excluded items in the cart
		 *
		 * @return  int
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_total_excluded_value() {

			$total_value = 0;

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || $item_id == 'cart' ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {

						if ( $this->contents_type == 'cart' ) {
							$total_value += round( $item['line_total'] + $item['line_tax'], wc_get_rounding_precision() - wc_get_price_decimals() );
						} else {

							$_product    = wc_get_product( $item['product_id'] );
							$total_value += round( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ), wc_get_rounding_precision() - wc_get_price_decimals() );
						}

					}

				}
			}

			return $total_value;

		}

		/**
		 * Check the active exclusions for each product in the cart
		 *
		 * @param   $value
		 * @param   $item_key
		 * @param   $product_id
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_check_exclusion( $value, $item_key, $product_id ) {

			$this->contents_to_validate[ $item_key ]['excluded'] = false;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $product_id );

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) == 'yes' ) {
				$this->contents_to_validate[ $item_key ]['excluded'] = true;
				$this->excluded_products                             = true;

				return true;
			}

			$product_categories = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

			foreach ( $product_categories as $cat_id ) {

				$category_exclusion = get_term_meta( $cat_id, '_ywmmq_category_exclusion', true );

				if ( $category_exclusion == 'yes' ) {
					$this->contents_to_validate[ $item_key ]['excluded'] = true;
					$this->excluded_products                             = true;

					return true;
				}

			}

			$product_tag = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

			foreach ( $product_tag as $tag_id ) {

				$tag_exclusion = get_term_meta( $tag_id, '_ywmmq_tag_exclusion', true );

				if ( $tag_exclusion == 'yes' ) {
					$this->contents_to_validate[ $item_key ]['excluded'] = true;
					$this->excluded_products                             = true;

					return true;
				}

			}

			return $value;
		}

		/** PRODUCT BUNDLES COMPATIBILITY */

		/**
		 * Check if YITH WooCommerce Product Bundles is active
		 *
		 * @return  bool
		 * @since   1.1.5
		 * @author  Alberto Ruggiero
		 */
		public function is_wcpb_active() {

			return defined( 'YITH_WCPB' ) && YITH_WCPB;

		}

		/**
		 * Check if product is a bundle or belongs to a bundle
		 *
		 * @param   $value
		 * @param   $product
		 *
		 * @return  bool
		 * @since   1.1.5
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_bundle_check( $value, $product ) {

			if ( get_option( 'ywmmq_bundle_quantity' ) == 'bundle' && isset( $product['bundled_by'] ) ) {
				return true;
			}

			if ( get_option( 'ywmmq_bundle_quantity' ) == 'elements' && isset( $product['cartstamp'] ) && ! isset( $product['bundled_by'] ) ) {
				return true;
			}

			if ( get_option( 'ywmmq_bundle_quantity' ) == 'elements' && isset( $product['yith_wcpb_hidden'] ) && $product['yith_wcpb_hidden'] == true ) {
				return true;
			}

			return $value;

		}

		/** GIFT CARDS COMPATIBILITY */
		/**
		 * Check if YITH WooCommerce Gift cards is active
		 *
		 * @return  bool
		 * @since   1.3.0
		 * @author  Alberto Ruggiero
		 */
		public function is_ywgc_active() {

			return function_exists( 'YITH_YWGC' ) && defined( 'YITH_YWGC_PREMIUM' ) && YITH_YWGC_PREMIUM;

		}

		/**
		 * Get Gift Card total
		 *
		 * @param   $total_cart_value
		 *
		 * @return  bool
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_gift_card_total( $total_cart_value ) {
			$gift_cards = WC()->cart->applied_gift_cards_amounts;
			$discount   = 0;

			if ( ! empty( $gift_cards ) ) {

				foreach ( $gift_cards as $code => $amount ) {

					$discount += $amount;

				}

			}

			return min( $discount, $total_cart_value );

		}

		/** REQUEST A QUOTE COMPATIBILITY */

		/**
		 * Check if YITH WooCommerce Request a quote is active
		 *
		 * @return  bool
		 * @since   1.3.3
		 * @author  Alberto Ruggiero
		 */
		public function is_wraq_active() {

			return defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM;

		}

		/**
		 * Validate Request a quote on submit
		 *
		 * @param   $errors
		 *
		 * @return  array
		 * @since   1.3.3
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_validate_raq( $errors ) {

			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) != 'yes' ) {
				return $errors;
			}

			$this->contents_type        = 'quote';
			$this->contents_to_validate = YITH_Request_Quote()->get_raq_return();

			$mmq_errors = $this->ywmmq_validate( 'cart', true );

			if ( $mmq_errors ) {
				$errors = array_merge( $errors, $mmq_errors );
			}

			return $errors;
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWMMQ_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWMMQ_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWMMQ_INIT, YWMMQ_SECRET_KEY, YWMMQ_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWMMQ_SLUG, YWMMQ_INIT );
		}

		/**
		 * Add Plugin Requirements
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_plugin_requirements() {

			$plugin_data  = get_plugin_data( plugin_dir_path( __FILE__ ) . '/init.php' );
			$plugin_name  = $plugin_data['Name'];
			$requirements = array(
				'min_wp_version' => '5.2.0',
				'min_wc_version' => '4.0.0',
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}

	}

}