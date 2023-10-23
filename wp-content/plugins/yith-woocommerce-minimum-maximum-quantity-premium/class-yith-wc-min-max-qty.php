<?php
/**
 * Main plugin class
 *
 * @package YITH\MinimumMaximumQuantity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Min_Max_Qty' ) ) {

	/**
	 * Main class
	 *
	 * @class   YITH_WC_Min_Max_Qty
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH
	 */
	class YITH_WC_Min_Max_Qty {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WC_Min_Max_Qty
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @since  1.0.0
		 * @var    /Yit_Plugin_Panel object
		 * @see    plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $panel = null;

		/**
		 * YITH WooCommerce Minimum Maximum Quantity panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith-wc-min-max-qty';

		/**
		 * Message container for notifications
		 *
		 * @var string
		 */
		public $message_filter = '';

		/**
		 * Excluded products
		 *
		 * @var boolean
		 */
		public $excluded_products = false;

		/**
		 * Products with errors
		 *
		 * @var boolean
		 */
		public $product_with_errors = false;

		/**
		 * ID for Minimum Maximum tab in product edit page
		 *
		 * @var string
		 */
		public $product_tab = 'yith_min_max_qty';

		/**
		 * Cart or RAQ contents that will be validated
		 *
		 * @var array
		 */
		public $contents_to_validate = null;

		/**
		 * ID for contents that will be evaluated
		 *
		 * @var string
		 */
		public $contents_type = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Min_Max_Qty
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load plugin framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWMMQ_DIR . '/' . basename( YWMMQ_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			$this->includes();

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 15 );
				add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'add_ywmmq_tab' ), 98 );
				add_action( 'woocommerce_product_data_panels', array( $this, 'write_tab_options' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save_ywmmq_tab' ), 10, 2 );
				add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_ywmmq_variations' ), 10, 3 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_ywmmq_variations' ), 10, 2 );
				add_action( 'product_cat_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
				add_action( 'product_tag_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
				add_action( 'edited_product_cat', array( $this, 'save_taxonomy_options' ) );
				add_action( 'edited_product_tag', array( $this, 'save_taxonomy_options' ) );
			}

			if ( ! is_admin() || ( wp_doing_ajax() && isset( $_POST['action'] ) && 'woocommerce_add_order_item' !== $_POST['action'] ) ) { //phpcs:ignore
				add_action( 'wp', array( $this, 'cart_validation' ) );
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'checkout_validation' ), 10, 2 );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
				add_filter( 'ywmmq_add_scripts', array( $this, 'use_elementor' ), 10, 2 );
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 11, 6 );
				add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_notification_products' ), 10, 3 );
				add_filter( 'ywraq_quote_item_name', array( $this, 'cart_notification_products' ), 10, 3 );
				add_filter( 'ywmmq_additional_notification', array( $this, 'cart_additional_notification' ), 10 );
				add_filter( 'woocommerce_quantity_input_max', array( $this, 'max_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_min', array( $this, 'min_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_step', array( $this, 'step_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_bundled_item_quantity_input_max', array( $this, 'max_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_bundled_item_quantity_input_min', array( $this, 'min_quantity_block' ), 10, 2 );
				add_filter( 'yith_wcpb_bundled_item_quantity_input_step', array( $this, 'step_quantity_block' ), 10, 2 );
				add_filter( 'woocommerce_available_variation', array( $this, 'set_variable_quantity' ), 10, 2 );
				add_filter( 'woocommerce_quantity_input_args', array( $this, 'set_quantity' ), 10, 2 );
				add_filter( 'woocommerce_cart_item_quantity', array( $this, 'check_wapo_sold_individual' ), 10, 3 );
				add_filter( 'ywraq_request_validate_fields', array( $this, 'validate_raq' ) );
				add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'set_min_quantity_loop' ), 10, 2 );
				add_filter( 'ywraq_add_item', array( $this, 'set_min_quantity_loop_raq' ), 10, 2 );
				add_filter( 'ywmmq_set_variation_quantity_locked', array( $this, 'lock_variations_quantity' ), 10 );
			}

			add_shortcode( 'ywmmq-rules', array( $this, 'set_shortcode' ) );
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_action( 'init', array( $this, 'init_template_hooks' ) );

			add_filter( 'ywmmq_check_exclusion', array( $this, 'check_exclusion' ), 10, 3 );

			if ( ywmmq_is_wcpb_active() ) {
				add_filter( 'ywmmq_bundle_check', array( $this, 'bundle_check' ), 10, 2 );
			}

		}

		/**
		 * Init hooks for block template
		 *
		 * @return void
		 * @since  1.32.0
		 */
		public function init_template_hooks() {
			if ( 'block' !== get_option( 'ywmmq_rules_position' ) ) {
				if ( yith_plugin_fw_wc_is_using_block_template_in_single_product() ) {
					ywmmq_show_rules_blocks();
				} else {
					add_action( 'woocommerce_before_main_content', array( $this, 'show_rules' ), 5 );

					// Compatibility mode for X-theme.
					if ( class_exists( 'TCO_1_0' ) ) {
						add_action( 'woocommerce_before_single_product', array( $this, 'show_rules' ), 5 );
					}
				}
			}
		}

		/**
		 * Set quick checkout shortcode
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function set_shortcode() {
			ob_start();
			$this->add_rules_text();

			return ob_get_clean();
		}

		/**
		 * Set shortcode
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function gutenberg_block() {

			$blocks = array(
				'ywmmq-rules' => array(
					'style'          => 'ywmmq-frontend',
					'title'          => esc_html_x( 'Minimum Maximum Quantity', '[gutenberg]: block name', 'yith-woocommerce-minimum-maximum-quantity' ),
					'description'    => esc_html_x( 'Add the block with Minimum Maximum Quantity rules', '[gutenberg]: block description', 'yith-woocommerce-minimum-maximum-quantity' ),
					'shortcode_name' => 'ywmmq-rules',
					'do_shortcode'   => true,
					'keywords'       => array(
						esc_html_x( 'Quick Checkout', '[gutenberg]: keywords', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );
			yith_plugin_fw_register_elementor_widgets( $blocks, true );

		}

		/**
		 * Files inclusion
		 *
		 * @return void
		 * @since  1.0.0
		 */
		private function includes() {

			include_once 'includes/class-ywmmq-ajax.php';
			include_once 'includes/ywmmq-error-messages.php';
			include_once 'includes/ywmmq-functions.php';

			if ( is_admin() ) {
				include_once 'includes/admin/class-yith-custom-table.php';
				include_once 'includes/admin/tables/class-ywmmq-bulk-table.php';
			}

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return void
		 * @since  1.0.0
		 * @use    /Yit_Plugin_Panel class
		 * @see    plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'  => esc_html__( 'General Settings', 'yith-woocommerce-minimum-maximum-quantity' ),
				'messages' => esc_html__( 'Message Settings', 'yith-woocommerce-minimum-maximum-quantity' ),
				'bulk'     => esc_html__( 'Bulk Actions', 'yith-woocommerce-minimum-maximum-quantity' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YWMMQ_SLUG,
				'is_premium'       => true,
				'page_title'       => 'YITH WooCommerce Minimum Maximum Quantity',
				'menu_title'       => 'Minimum Maximum Quantity',
				'capability'       => apply_filters( 'ywmmq_capability_menu', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWMMQ_DIR . 'plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function admin_scripts() {
			wp_register_script( 'ywmmq-admin', yit_load_js_file( YWMMQ_ASSETS_URL . '/js/admin.js' ), array( 'jquery' ), YWMMQ_VERSION, false );
			wp_register_style( 'ywmmq-admin', yit_load_css_file( YWMMQ_ASSETS_URL . '/css/admin.css' ), array(), YWMMQ_VERSION );

			$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$screen = get_current_screen();

			if ( ( isset( $getted['page'] ) && $this->panel_page === $getted['page'] ) || in_array( $screen->id, array( 'product', 'edit-product' ), true ) ) {
				wp_enqueue_script( 'ywmmq-admin' );
				wp_enqueue_style( 'ywmmq-admin' );
			}

			if ( ! empty( $getted['taxonomy'] ) && ( 'product_cat' === $getted['taxonomy'] || 'product_tag' === $getted['taxonomy'] ) ) {
				wp_enqueue_script( 'ywmmq-admin' );
				wp_enqueue_style( 'ywmmq-admin' );
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}
		}

		/**
		 * Add YWMMQ tab in product edit page
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function add_ywmmq_tab() {

			global $post;

			$product = wc_get_product( $post->ID );

			if ( $product->is_type( 'grouped' ) || $product->is_type( 'external' ) ) {
				return;
			}

			?>
			<li class="<?php echo esc_attr( $this->product_tab ); ?>_options <?php echo esc_attr( $this->product_tab ); ?>_tab">
				<a href="#<?php echo esc_attr( $this->product_tab ); ?>_tab"><span><?php echo esc_html_x( 'Minimum Maximum Quantity', 'plugin name in product edit tab', 'yith-woocommerce-minimum-maximum-quantity' ); ?></span></a>
			</li>
			<?php
		}

		/**
		 * Add YWMMQ tab content in product edit page
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function write_tab_options() {

			global $post;

			$product = wc_get_product( $post->ID );

			if ( ! $product ) {
				$product = isset( $_GET['post'] ) ? wc_get_product( sanitize_text_field( wp_unslash( $_GET['post'] ) ) ) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if ( ! $product || $product->is_type( 'grouped' ) || $product->is_type( 'external' ) ) {
				return;
			}

			?>
			<div id="<?php echo esc_attr( $this->product_tab ); ?>_tab" class="panel woocommerce_options_panel">
				<div class="options_group ywmmq-product-tab">
					<?php

					woocommerce_wp_checkbox(
						array(
							'id'          => '_ywmmq_product_exclusion',
							'label'       => esc_html__( 'Exclude product', 'yith-woocommerce-minimum-maximum-quantity' ),
							'description' => esc_html__( 'Do not apply any of the plugin restrictions to this product', 'yith-woocommerce-minimum-maximum-quantity' ),
						)
					);

					if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

						woocommerce_wp_checkbox(
							array(
								'id'          => '_ywmmq_product_quantity_limit_override',
								'label'       => esc_html__( 'Override product restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
								'description' => esc_html__( 'Global product restrictions will be overridden by these ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
							)
						);

						if ( $product->is_type( 'variable' ) ) {

							woocommerce_wp_checkbox(
								array(
									'id'          => '_ywmmq_product_quantity_limit_variations_override',
									'label'       => esc_html__( 'Enable variation restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
									'description' => esc_html__( 'Enable to set up a custom quantity value for every single variation (e.g. minimum quantity 7 for "Blue" variation, and 3 for "Yellow" variation).', 'yith-woocommerce-minimum-maximum-quantity' ),
								)
							);

						}

						$min_qty  = $product->get_meta( '_ywmmq_product_minimum_quantity' );
						$max_qty  = $product->get_meta( '_ywmmq_product_maximum_quantity' );
						$step_qty = $product->get_meta( '_ywmmq_product_step_quantity' );

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_minimum_quantity',
								'label'             => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-minimum',
								'value'             => ( $min_qty ? $min_qty : 0 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '0',
								),
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_maximum_quantity',
								'label'             => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-maximum',
								'value'             => ( $max_qty ? $max_qty : 0 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '0',
								),
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'                => '_ywmmq_product_step_quantity',
								'label'             => esc_html_x( 'Allow users to select products only in groups of', '[single product page]', 'yith-woocommerce-minimum-maximum-quantity' ),
								'class'             => 'ywmmq-step',
								'value'             => ( $step_qty ? $step_qty : 1 ),
								'type'              => 'number',
								'custom_attributes' => array(
									'step' => 'any',
									'min'  => '1',
								),
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
		 * @return void
		 * @since  1.0.0
		 */
		public function save_ywmmq_tab() {

			global $post;

			$posted = $_POST; //phpcs:ignore

			$product             = wc_get_product( $post->ID );
			$exclude             = isset( $posted['_ywmmq_product_exclusion'] ) ? 'yes' : 'no';
			$override            = isset( $posted['_ywmmq_product_quantity_limit_override'] ) ? 'yes' : 'no';
			$override_variations = isset( $posted['_ywmmq_product_quantity_limit_variations_override'] ) ? 'yes' : 'no';
			$min_limit           = isset( $posted['_ywmmq_product_minimum_quantity'] ) ? $posted['_ywmmq_product_minimum_quantity'] : 0;
			$max_limit           = isset( $posted['_ywmmq_product_maximum_quantity'] ) ? $posted['_ywmmq_product_maximum_quantity'] : 0;
			$step                = isset( $posted['_ywmmq_product_step_quantity'] ) ? $posted['_ywmmq_product_step_quantity'] : 1;

			if ( (int) $max_limit > 0 && $min_limit > $max_limit ) {

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
		 * @param integer $loop           The variation loop index.
		 * @param array   $variation_data Unused.
		 * @param WP_Post $variation      The variation object.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function add_ywmmq_variations( $loop, $variation_data, $variation ) {

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

				$variation_object = wc_get_product( $variation->ID );
				$min_qty          = $variation_object->get_meta( '_ywmmq_product_minimum_quantity' );
				$max_qty          = $variation_object->get_meta( '_ywmmq_product_maximum_quantity' );
				$step_qty         = $variation_object->get_meta( '_ywmmq_product_step_quantity' );

				?>
				<div class="ywmmq-variations-row">
					<?php

					//phpcs:ignore
					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_minimum_quantity[' . $loop . ']',
							'label'             => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $min_qty ? $min_qty : 0 ),
							'wrapper_class'     => 'form-row-first',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0',
							),
						)
					);

					//phpcs:ignore
					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_maximum_quantity[' . $loop . ']',
							'label'             => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $max_qty ? $max_qty : 0 ),
							'wrapper_class'     => 'form-row-last',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0',
							),
						)
					);

					//phpcs:ignore
					@woocommerce_wp_text_input(
						array(
							'id'                => '_ywmmq_product_step_quantity[' . $loop . ']',
							'label'             => esc_html_x( 'Allow users to select product variations only in groups of', '[single product page - variations]', 'yith-woocommerce-minimum-maximum-quantity' ),
							'class'             => 'ywmmq-variation-field',
							'value'             => ( $step_qty ? $step_qty : 1 ),
							'wrapper_class'     => 'form-row-first',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '1',
							),
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
		 * @param integer $variation_id The ID of the variation.
		 * @param integer $loop         The variation loop index.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function save_ywmmq_variations( $variation_id, $loop ) {

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

				$variation_object = wc_get_product( $variation_id );
				$posted           = $_POST; //phpcs:ignore
				$min_limit        = ( isset( $posted['_ywmmq_product_minimum_quantity'][ $loop ] ) ? (int) $posted['_ywmmq_product_minimum_quantity'][ $loop ] : 0 );
				$max_limit        = ( isset( $posted['_ywmmq_product_maximum_quantity'][ $loop ] ) ? (int) $posted['_ywmmq_product_maximum_quantity'][ $loop ] : 0 );
				$step             = ( isset( $posted['_ywmmq_product_step_quantity'][ $loop ] ) ? (int) $posted['_ywmmq_product_step_quantity'][ $loop ] : 1 );

				if ( $max_limit > 0 && $min_limit > $max_limit ) {
					$max_limit = 0;
				}

				$variation_object->update_meta_data( '_ywmmq_product_minimum_quantity', esc_attr( $min_limit ) );
				$variation_object->update_meta_data( '_ywmmq_product_maximum_quantity', esc_attr( $max_limit ) );
				$variation_object->update_meta_data( '_ywmmq_product_step_quantity', esc_attr( $step ) );
				$variation_object->save();

			}

		}

		/**
		 * Add YWMMQ fields in category/tag edit page
		 *
		 * @param WP_Term $taxonomy The Term Object.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function write_taxonomy_options( $taxonomy ) {

			$item_type = 'product_cat' === $_GET['taxonomy'] ? 'category' : 'tag'; //phpcs:ignore
			$item      = array(
				'_exclusion'               => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_exclusion', true ),
				'_quantity_limit_override' => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_quantity_limit_override', true ),
				'_minimum_quantity'        => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_minimum_quantity', true ),
				'_maximum_quantity'        => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_maximum_quantity', true ),
				'_step_quantity'           => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_step_quantity', true ),
				'_value_limit_override'    => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_value_limit_override', true ),
				'_minimum_value'           => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_minimum_value', true ),
				'_maximum_value'           => get_term_meta( $taxonomy->term_id, '_ywmmq_' . $item_type . '_maximum_value', true ),
			);

			if ( ! $item ) {

				$item = array(
					'_exclusion'               => 'no',
					'_quantity_limit_override' => 'no',
					'_minimum_quantity'        => 0,
					'_maximum_quantity'        => 0,
					'_step_quantity'           => 0,
					'_value_limit_override'    => 'no',
					'_minimum_value'           => 0,
					'_maximum_value'           => 0,
				);
			}

			$fields = ywmmq_get_rules_fields( $item, $item_type );

			?>
			<div class="ywmmq-taxonomy-panel ywmmq-rules yith-plugin-ui woocommerce">
				<h2><?php esc_html_e( 'Minimum Maximum Quantity Options', 'yith-woocommerce-minimum-maximum-quantity' ); ?></h2>
				<table class="form-table ywmmq-table">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['name'] ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
								<?php if ( isset( $field['desc'] ) ) : ?>
									<span class="description"><?php echo wp_kses_post( $field['desc'] ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php

		}

		/**
		 * Save YWCTM category/tag options
		 *
		 * @param integer $taxonomy_id The term ID.
		 *
		 * @return void
		 * @since  1.3.0
		 */
		public function save_taxonomy_options( $taxonomy_id ) {

			global $pagenow;

			if ( ! $taxonomy_id || 'edit-tags.php' !== $pagenow ) {
				return;
			}

			$posted    = $_POST;                                                     //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$item_type = 'product_cat' === $posted['taxonomy'] ? 'category' : 'tag'; //phpcs:ignore

			$exclude            = isset( $posted['_exclusion'] ) ? 'yes' : 'no';
			$override_quantity  = isset( $posted['_quantity_limit_override'] ) ? 'yes' : 'no';
			$override_value     = isset( $posted['_value_limit_override'] ) ? 'yes' : 'no';
			$min_quantity_limit = isset( $posted['_minimum_quantity'] ) ? (int) $posted['_minimum_quantity'] : 0;
			$max_quantity_limit = isset( $posted['_maximum_quantity'] ) ? (int) $posted['_maximum_quantity'] : 0;
			$step_quantity      = isset( $posted['_step_quantity'] ) ? (int) $posted['_step_quantity'] : 1;
			$min_value_limit    = isset( $posted['_minimum_value'] ) ? (int) $posted['_minimum_value'] : 0;
			$max_value_limit    = isset( $posted['_maximum_value'] ) ? (int) $posted['_maximum_value'] : 0;

			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_exclusion', $exclude );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_quantity_limit_override', $override_quantity );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_value_limit_override', $override_value );

			if ( $min_quantity_limit > 0 && $min_quantity_limit > $max_quantity_limit ) {
				$max_quantity_limit = 0;
			}

			if ( $min_value_limit > 0 && $min_value_limit > $max_value_limit ) {
				$max_value_limit = 0;
			}

			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_minimum_quantity', $min_quantity_limit );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_maximum_quantity', $max_quantity_limit );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_step_quantity', $step_quantity );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_minimum_value', $min_value_limit );
			update_term_meta( $taxonomy_id, '_ywmmq_' . $item_type . '_maximum_value', $max_value_limit );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Enqueue frontend script files
		 *
		 * @return void
		 * @since  1.0.0
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
			$is_cart  = ( (int) wc_get_page_id( 'cart' ) === $post->ID );
			$is_quote = ( (int) get_option( 'ywraq_page_id' ) === $post->ID );

			wp_register_script( 'ywmmq-frontend', yit_load_js_file( YWMMQ_ASSETS_URL . '/js/frontend.js' ), array( 'jquery' ), YWMMQ_VERSION, false );

			if ( $is_cart || $is_quote || $product ) {
				wp_enqueue_style( 'ywmmq-frontend', yit_load_css_file( YWMMQ_ASSETS_URL . '/css/frontend.css' ), array(), YWMMQ_VERSION );
			}

			if ( $product ) {
				if ( ! $product->is_type( 'simple' ) ) {
					wp_enqueue_script( 'ywmmq-frontend' );

					$args = array(
						'ajax_url'   => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
						'variations' => ( 'yes' === get_option( 'ywmmq_product_quantity_limit' ) && ( 'yes' === $product->get_meta( '_ywmmq_product_quantity_limit_override' ) && 'yes' === $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' ) ) || apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ),
						'yith_eop'   => false,
					);

					wp_localize_script( 'ywmmq-frontend', 'ywmmq', $args );
				}
			}

			// APPLY_FILTERS: ywmmq_add_scripts: add plugin script in the page anyway. this is useful with some page builder.
			if ( has_shortcode( $post->post_content, 'yith_product_list' ) || apply_filters( 'ywmmq_add_scripts', false, $post ) ) {
				wp_enqueue_script( 'ywmmq-frontend' );

				$args = array(
					'ajax_url'   => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
					'variations' => true,
					'yith_eop'   => apply_filters( 'ywmmq_add_scripts_eop', true, $post ),
				);

				wp_localize_script( 'ywmmq-frontend', 'ywmmq', $args );

			}

		}

		/**
		 * Check if current page uses Elementor
		 *
		 * @param boolean $value Control value.
		 * @param WP_Post $post  Current post.
		 *
		 * @return  boolean
		 * @since   1.0.0
		 */
		public function use_elementor( $value, $post ) {
			return ywmmq_get_elementor_item_for_page( 'yith-wceop-product-list', $post->ID );
		}

		/**
		 * Validates cart and checkout on page load.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function cart_validation() {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) || isset( $_POST['woocommerce-login-nonce'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				return;
			}

			$on_cart_page     = is_page( wc_get_page_id( 'cart' ) );
			$on_quote_page    = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			$on_checkout_page = is_checkout() && ! is_checkout_pay_page() && ! is_order_received_page();

			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) === 'yes' && $on_quote_page && function_exists( 'YITH_Request_Quote' ) ) {
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

				$cart_update_notice = esc_html__( 'Cart updated.', 'woocommerce' );
				$cart_update        = wc_has_notice( $cart_update_notice );

				wc_clear_notices();

				if ( $this->contents_to_validate ) {

					if ( $on_cart_page || $on_quote_page ) {
						$current_page = 'cart';
					} else {
						$current_page = '';
					}

					$errors = $this->validate( $current_page, ( $on_cart_page || $on_quote_page ) );

					if ( $errors ) {

						ob_start();

						?>

						<ul>
							<?php foreach ( $errors as $error ) : ?>
								<li><?php echo wp_kses_post( $error ); ?></li>
							<?php endforeach; ?>
							<?php echo wp_kses_post( apply_filters( 'ywmmq_additional_notification', '' ) ); ?>
						</ul>

						<?php

						$error_list = ob_get_clean();

						wc_add_notice( $error_list, 'error' );

					}

					if ( $cart_update && empty( $errors ) ) {
						wc_add_notice( $cart_update_notice );
					}
				}
			}

		}

		/**
		 * Validates cart contents.
		 *
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate( $current_page, $on_cart_page ) {

			$errors = array();

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {
				$this->product_quantity_cart( $current_page, $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_cart_quantity_limit', 'yes' ) === 'yes' ) {
				$this->check_validation_cart( $this->validate_cart_quantity( $current_page ), $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_cart_value_limit' ) === 'yes' ) {
				$this->check_validation_cart( $this->validate_cart_value( $current_page ), $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_category_quantity_limit' ) === 'yes' ) {
				$this->category_quantity_cart( $current_page, $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_category_value_limit' ) === 'yes' ) {
				$this->category_value_cart( $current_page, $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_tag_quantity_limit' ) === 'yes' ) {
				$this->tag_quantity_cart( $current_page, $on_cart_page, $errors );
			}

			if ( get_option( 'ywmmq_tag_value_limit' ) === 'yes' ) {
				$this->tag_value_cart( $current_page, $on_cart_page, $errors );
			}

			return $errors;

		}

		/**
		 * Validates cart.
		 *
		 * @param mixed    $data   Unused.
		 * @param WP_Error $errors The errors list.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function checkout_validation( $data, $errors ) {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return;
			}

			if ( function_exists( 'YITH_Request_Quote' ) ) {
				if ( YITH_YWRAQ_Order_Request()->get_current_order_id() ) {
					return;
				}
			}

			$mmq_errors = $this->validate( 'cart', true );

			if ( $mmq_errors ) {
				$errors->add( 'ywmmq', implode( '<br/>', $mmq_errors ) );
			}

		}

		/**
		 * Check the return value, if it is invalid returns an error message
		 *
		 * @param array   $data         The validated data.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function check_validation_cart( $data, $on_cart_page, &$errors ) {

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
		 * Output icons next to products if there are notifications
		 *
		 * @param string $product_name  The product name.
		 * @param array  $cart_item     Unused.
		 * @param string $cart_item_key The cart item key.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function cart_notification_products( $product_name, $cart_item, $cart_item_key ) {

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) !== 'yes' && $on_quote_page ) {
				return $product_name;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $product_name;
			}

			if ( isset( $this->contents_to_validate[ $cart_item_key ]['excluded'] ) && true === $this->contents_to_validate[ $cart_item_key ]['excluded'] && $this->excluded_products ) {
				return '<i class="ywmmq-icon ywmmq-excluded"></i> ' . $product_name;
			}

			if ( isset( $this->contents_to_validate[ $cart_item_key ]['has_error'] ) && true === $this->contents_to_validate[ $cart_item_key ]['has_error'] && $this->product_with_errors ) {
				return '<i class="ywmmq-icon ywmmq-error"></i> ' . $product_name;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {
				return '<i class="ywmmq-icon ywmmq-correct"></i> ' . $product_name;
			}

			return $product_name;

		}

		/**
		 * Output additional notification for explaining eventual icons next to products
		 *
		 * @param string $message Error Message.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function cart_additional_notification( $message ) {

			if ( $this->excluded_products || $this->product_with_errors ) {

				$message = '<li>&nbsp;</li>';

			}

			if ( $this->excluded_products ) {
				/* translators: %s icon placeholder */
				$message .= '<li>' . sprintf( esc_html__( 'Items marked with %s do not contribute to reaching the purchase objective set', 'yith-woocommerce-minimum-maximum-quantity' ), '<i class="ywmmq-icon ywmmq-excluded"></i>' ) . '</li>';

			}

			if ( $this->product_with_errors ) {
				/* translators: %s icon placeholder */
				$message .= '<li>' . sprintf( esc_html__( 'Check items marked with %s', 'yith-woocommerce-minimum-maximum-quantity' ), '<i class="ywmmq-icon ywmmq-error"></i>' ) . '</li>';

			}

			return $message;
		}

		/**
		 * Check if page is cart or quote page
		 *
		 * @return boolean
		 * @since  1.3.0
		 */
		public function cart_or_raq() {

			global $post;
			$is_raq = false;

			if ( function_exists( 'YITH_Request_Quote' ) ) {
				$raq_page_id = YITH_Request_Quote()->get_raq_page_id();
				$is_raq      = $post && $post->ID === (int) $raq_page_id;
			}

			return is_cart() || $is_raq || ( defined( 'YITH_QOF_PAGE' ) && YITH_QOF_PAGE ) || wp_doing_ajax();
		}

		/**
		 * Set variations lock
		 *
		 * @return boolean
		 * @since  1.6.8
		 */
		public function lock_variations_quantity() {
			return 'yes' !== get_option( 'ywmmq_product_variations_unlocked' );
		}

		/**
		 * Set quantity limit for loop
		 *
		 * @param array      $args    Product arguments.
		 * @param WC_Product $product Product object.
		 *
		 * @return array
		 * @since  1.5.4
		 */
		public function set_min_quantity_loop( $args, $product ) {

			if ( yith_plugin_fw_wc_is_using_block_template_in_product_catalogue() ) {
				$args['attributes']['data-quantity'] = apply_filters( 'woocommerce_quantity_input_min', 1, $product );
			} else {
				$args['quantity'] = apply_filters( 'woocommerce_quantity_input_min', $args['quantity'], $product );
			}

			return $args;
		}

		/**
		 * Set quantity limit
		 *
		 * @param array      $args    Product arguments.
		 * @param WC_Product $product Product object.
		 *
		 * @return array
		 * @since  1.3.0
		 */
		public function set_quantity( $args, $product ) {

			if ( ( isset( $args['wapo_individual'] ) && $args['wapo_individual'] ) || ! $product ) {
				return $args;
			}

			if ( $product->is_type( 'variation' ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ) {
				return $args;
			}

			$min_value = apply_filters( 'woocommerce_quantity_input_min', $args['min_value'], $product );
			$is_raq    = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;

			if ( ! is_cart() && ! $is_raq ) {
				$args['input_value'] = $min_value > 0 ? $min_value : 1;
			}
			$args['min_value'] = $min_value;
			$args['max_value'] = apply_filters( 'woocommerce_quantity_input_max', $args['max_value'], $product );
			$args['step']      = apply_filters( 'woocommerce_quantity_input_step', $args['step'], $product );

			return $args;

		}

		/**
		 * If it's a product add-on that should be sold individually, reset the quantity box
		 *
		 * @param string $product_quantity Quantity box.
		 * @param string $cart_item_key    Cart item key.
		 * @param array  $cart_item        Cart item object.
		 *
		 * @return string
		 * @since  1.7.0
		 */
		public function check_wapo_sold_individual( $product_quantity, $cart_item_key, $cart_item ) {

			if ( isset( $cart_item['yith_wapo_sold_individually'] ) && 1 === (int) $cart_item['yith_wapo_sold_individually'] ) {

				$product_quantity = woocommerce_quantity_input(
					array(
						'input_name'      => "cart[$cart_item_key][qty]",
						'input_value'     => $cart_item['quantity'],
						'max_value'       => $cart_item['data']->get_max_purchase_quantity(),
						'min_value'       => '0',
						'product_name'    => $cart_item['data']->get_name(),
						'wapo_individual' => true,
					),
					$cart_item['data'],
					false
				);
			}

			return $product_quantity;

		}

		/**
		 * Set quantity limit for variation
		 *
		 * @param array                $args    Product arguments.
		 * @param WC_Product_Variation $product Product object.
		 *
		 * @return array
		 * @since  1.3.0
		 */
		public function set_variable_quantity( $args, $product ) {

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $args;
			}

			if ( $product->is_type( 'variation' ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ) {
				return $args;
			}

			$args['min_qty'] = apply_filters( 'woocommerce_quantity_input_min', $args['min_qty'], $product );
			$args['max_qty'] = apply_filters( 'woocommerce_quantity_input_max', $args['max_qty'], $product );

			return $args;

		}

		/**
		 * Set maximum quantity
		 *
		 * @param integer    $value   Limit value.
		 * @param WC_Product $product The product.
		 *
		 * @return string
		 * @since  1.0.9
		 */
		public function max_quantity_block( $value, $product ) {

			if ( ! $product ) {
				return $value;
			}

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) !== 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			if ( ywmmq_is_wcpb_active() && get_option( 'ywmmq_bundle_quantity' ) === 'elements' && $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}

			if ( ywmmq_is_wcpb_active() && 'yith_wcpb_bundled_item_quantity_input_max' === current_filter() && get_option( 'ywmmq_bundle_quantity' ) === 'bundle' && ! $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

				if ( ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ) {
					$product_limit = array(
						'min'  => 0,
						'max'  => 0,
						'step' => 1,
					);
				} else {
					$variation_id  = ( $this->cart_or_raq() ? $product->get_id() : 0 );
					$product_limit = $this->product_limits( yit_get_base_product_id( $product ), $variation_id );
				}

				if ( $product_limit['max'] > 0 ) {
					$value = $product_limit['max'];
				}
			}

			return $value;

		}

		/**
		 * Set minimum quantity
		 *
		 * @param integer    $value   Limit value.
		 * @param WC_Product $product The product.
		 *
		 * @return string
		 * @since  1.0.9
		 */
		public function min_quantity_block( $value, $product ) {

			if ( ! $product ) {
				return $value;
			}

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) !== 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			if ( ywmmq_is_wcpb_active() && get_option( 'ywmmq_bundle_quantity' ) === 'elements' && $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}

			if ( ywmmq_is_wcpb_active() && 'yith_wcpb_bundled_item_quantity_input_min' === current_filter() && get_option( 'ywmmq_bundle_quantity' ) === 'bundle' && ! $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

				if ( ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ) {
					$product_limit = array(
						'min'  => 0,
						'max'  => 0,
						'step' => 1,
					);
				} else {
					$variation_id  = ( $this->cart_or_raq() ? $product->get_id() : 0 );
					$product_limit = $this->product_limits( yit_get_base_product_id( $product ), $variation_id );
				}
				if ( $product_limit['min'] > 0 ) {
					$value = $product_limit['min'];
				}
			}

			return $value;

		}

		/**
		 * Set step quantity
		 *
		 * @param integer    $value   Limit value.
		 * @param WC_Product $product The product.
		 *
		 * @return string
		 * @since  1.1.6
		 */
		public function step_quantity_block( $value, $product ) {

			if ( ! $product ) {
				return $value;
			}

			$on_quote_page = function_exists( 'YITH_Request_Quote' ) ? is_page( YITH_Request_Quote()->get_raq_page_id() ) : false;
			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) !== 'yes' && $on_quote_page ) {
				return $value;
			}

			if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
				return $value;
			}

			if ( ywmmq_is_wcpb_active() && get_option( 'ywmmq_bundle_quantity' ) === 'elements' && $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}
			if ( ywmmq_is_wcpb_active() && 'yith_wcpb_bundled_item_quantity_input_step' === current_filter() && get_option( 'ywmmq_bundle_quantity' ) === 'bundle' && ! $product->is_type( 'yith_bundle' ) ) {
				return $value;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product->get_id(), 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
				return $value;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {

				if ( ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) && apply_filters( 'ywmmq_set_variation_quantity_locked', true ) === false ) {
					$product_limit = array(
						'min'  => 0,
						'max'  => 0,
						'step' => 1,
					);
				} else {
					$variation_id  = ( $this->cart_or_raq() ? $product->get_id() : 0 );
					$product_limit = $this->product_limits( yit_get_base_product_id( $product ), $variation_id );
				}

				if ( $product_limit['step'] > 1 ) {
					$value = $product_limit['step'];
				}
			}

			return $value;

		}

		/**
		 * Get the position and show YWMMQ rules in product page
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_rules() {

			if ( get_option( 'ywmmq_rules_enable' ) !== 'no' ) {

				if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
					return;
				}

				$position = get_option( 'ywmmq_rules_position' );

				switch ( $position ) {

					case '1':
						$args = array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 15,
						);
						break;

					case '2':
						$args = array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 25,
						);
						break;

					case '3':
						$args = array(
							'hook'     => 'woocommerce_after_single_product_summary',
							'priority' => 5,
						);
						break;

					default:
						$args = array(
							'hook'     => 'woocommerce_before_single_product',
							'priority' => 20,
						);

				}

				$hook     = apply_filters( 'ywmmq_rules_hook', $args['hook'] );
				$priority = apply_filters( 'ywmmq_rules_priority', $args['priority'] );

				add_action( $hook, array( $this, 'add_rules_text' ), $priority );

			}

		}

		/**
		 * Add YWMMQ rules to product page
		 *
		 * @param integer $product_id   The product ID.
		 * @param integer $variation_id The variation ID.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function add_rules_text( $product_id = 0, $variation_id = 0 ) {

			if ( 0 === (int) $product_id ) {

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

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
				return;
			}

			if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {
				$product_limit = $this->product_limits( $product_id, $variation_id );
				if ( 0 === (int) $product_limit['min'] && $product_limit['max'] > 0 ) {
					/* translators: %d quantity */
					$rules_message[] = apply_filters( 'ywmmq_product_max_quantity_limit_label', sprintf( esc_html__( 'Maximum quantity allowed for this product: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['max'] ), $product_limit['max'] );
				} elseif ( 0 === (int) $product_limit['max'] && $product_limit['min'] > 0 ) {
					/* translators: %d quantity */
					$rules_message[] = apply_filters( 'ywmmq_product_min_quantity_limit_label', sprintf( esc_html__( 'Minimum quantity required for this product: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['min'] ), $product_limit['min'] );
				} elseif ( $product_limit['min'] > 0 && $product_limit['max'] > 0 ) {
					/* translators: %1$d min quantity - %2%d max quantity */
					$rules_message[] = apply_filters( 'ywmmq_product_min_max_quantity_limit_label', sprintf( esc_html__( 'Quantities allowed for this product: minimum %1$d - maximum %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $product_limit['min'], $product_limit['max'] ), $product_limit['min'], $product_limit['max'] );
				}
			}

			if ( get_option( 'ywmmq_cart_quantity_limit' ) === 'yes' ) {
				$cart_qty_limit = $this->cart_limits( 'quantity' );
				if ( 0 === (int) $cart_qty_limit['min'] && $cart_qty_limit['max'] > 0 ) {
					/* translators: %d quantity */
					$rules_message[] = apply_filters( 'ywmmq_cart_max_quantity_limit_label', sprintf( esc_html__( 'Cart can contain %d items at most.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['max'] ), $cart_qty_limit['max'] );
				} elseif ( 0 === (int) $cart_qty_limit['max'] && $cart_qty_limit['min'] > 0 ) {
					/* translators: %d quantity */
					$rules_message[] = apply_filters( 'ywmmq_cart_min_quantity_limit_label', sprintf( esc_html__( 'Cart must contain %d items at least.', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['min'] ), $cart_qty_limit['min'] );
				} elseif ( $cart_qty_limit['min'] > 0 && $cart_qty_limit['max'] > 0 ) {
					/* translators: %1$d min quantity - %2%d max quantity */
					$rules_message[] = apply_filters( 'ywmmq_cart_min_max_quantity_limit_label', sprintf( esc_html__( 'Cart must contain at least %1$d items but no more than %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $cart_qty_limit['min'], $cart_qty_limit['max'] ), $cart_qty_limit['min'], $cart_qty_limit['max'] );
				}
			}

			if ( get_option( 'ywmmq_cart_value_limit' ) === 'yes' ) {
				$cart_val_limit = $this->cart_limits( 'value' );
				if ( 0 === (int) $cart_val_limit['min'] && $cart_val_limit['max'] > 0 ) {
					/* translators: %s amount */
					$rules_message[] = apply_filters( 'ywmmq_cart_max_value_limit_label', sprintf( esc_html__( 'Cart can contain no more than %s items.', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['max'] ) ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['max'] ) ) );
				} elseif ( 0 === (int) $cart_val_limit['max'] && $cart_val_limit['min'] > 0 ) {
					/* translators: %s amount */
					$rules_message[] = apply_filters( 'ywmmq_cart_min_value_limit_label', sprintf( esc_html__( 'Cart must contain %s items at least', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['min'] ) ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['min'] ) ) );

				} elseif ( $cart_val_limit['min'] > 0 && $cart_val_limit['max'] > 0 ) {
					/* translators: %1$s min amount - %2%s max amount */
					$rules_message[] = apply_filters( 'ywmmq_cart_min_max_value_limit_label', sprintf( esc_html__( 'Cart must contain at least %1$s items but no more than %2$s', 'yith-woocommerce-minimum-maximum-quantity' ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['max'] ) ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $cart_val_limit['max'] ) ) );
				}
			}

			$product_categories = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'all' ) );

			foreach ( $product_categories as $category ) {
				$category_exclusion = get_term_meta( $category->term_id, '_ywmmq_category_exclusion', true );
				if ( 'yes' === $category_exclusion ) {
					return;
				}
				$category_link = '<a href="' . get_term_link( $category ) . '">' . $category->name . '</a>';
				if ( get_option( 'ywmmq_category_quantity_limit' ) === 'yes' ) {
					$category_qty_limit = $this->category_limits( $category->term_id, 'quantity' );
					if ( 0 === (int) $category_qty_limit['min'] && $category_qty_limit['max'] > 0 ) {
						/* translators: %1$s category name - %2%d quantity */
						$rules_message[] = apply_filters( 'ywmmq_category_max_quantity_limit_label', sprintf( esc_html__( 'Maximum quantity allowed for category %1$s: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['max'] ), $category_link, $category_qty_limit['max'] );
					} elseif ( 0 === (int) $category_qty_limit['max'] && $category_qty_limit['min'] > 0 ) {
						/* translators: %1$s category name - %2%d quantity */
						$rules_message[] = apply_filters( 'ywmmq_category_min_quantity_limit_label', sprintf( esc_html__( 'Minimum quantity required for category %1$s: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['min'] ), $category_link, $category_qty_limit['min'] );
					} elseif ( $category_qty_limit['min'] > 0 && $category_qty_limit['max'] > 0 ) {
						/* translators: %1$s category name - %2%d min quantity - %3%d max quantity */
						$rules_message[] = apply_filters( 'ywmmq_category_min_max_quantity_limit_label', sprintf( esc_html__( 'Quantities allowed for category %1$s: minimum %2$d - maximum %3$d', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, $category_qty_limit['min'], $category_qty_limit['max'] ), $category_link, $category_qty_limit['min'], $category_qty_limit['max'] );
					}
				}

				if ( get_option( 'ywmmq_category_value_limit' ) === 'yes' ) {
					$category_val_limit = $this->category_limits( $category->term_id, 'value' );
					if ( 0 === (int) $category_val_limit['min'] && $category_val_limit['max'] > 0 ) {
						/* translators: %1$s category name - %2%s amount */
						$rules_message[] = apply_filters( 'ywmmq_category_max_value_limit_label', sprintf( esc_html__( 'Maximum spend allowed for category %1$s: %2$s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['max'] ) ) ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['max'] ) ) );
					} elseif ( 0 === (int) $category_val_limit['max'] && $category_val_limit['min'] > 0 ) {
						/* translators: %1$s category name - %2%s amount */
						$rules_message[] = apply_filters( 'ywmmq_category_min_value_limit_label', sprintf( esc_html__( 'Minimum spend required for category %1$s: %2$s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['min'] ) ) ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['min'] ) ) );
					} elseif ( $category_val_limit['min'] > 0 && $category_val_limit['max'] > 0 ) {
						/* translators: %1$s category name - %2%d min amount - %3%d max amount */
						$rules_message[] = apply_filters( 'ywmmq_category_min_max_value_limit_label', sprintf( esc_html__( 'Spend allowed for category %1$s: minimum %2$s - maximum %3$s', 'yith-woocommerce-minimum-maximum-quantity' ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['max'] ) ) ), $category_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $category_val_limit['max'] ) ) );
					}
				}
			}

			$product_tag = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'all' ) );

			foreach ( $product_tag as $tag ) {
				$tag_exclusion = get_term_meta( $tag->term_id, '_ywmmq_tag_exclusion', true );
				if ( 'yes' === $tag_exclusion ) {
					return;
				}
				$tag_link = '<a href="' . get_term_link( $tag ) . '">' . $tag->name . '</a>';
				if ( get_option( 'ywmmq_tag_quantity_limit' ) === 'yes' ) {
					$tag_qty_limit = $this->tag_limits( $tag->term_id, 'quantity' );
					if ( 0 === (int) $tag_qty_limit['min'] && $tag_qty_limit['max'] > 0 ) {
						/* translators: %1$s tag name - %2%d quantity */
						$rules_message[] = apply_filters( 'ywmmq_tag_max_quantity_limit_label', sprintf( esc_html__( 'Maximum quantity allowed for tag %1$s: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['max'] ), $tag_link, $tag_qty_limit['max'] );
					} elseif ( 0 === (int) $tag_qty_limit['max'] && $tag_qty_limit['min'] > 0 ) {
						/* translators: %1$s tag name - %2%d quantity */
						$rules_message[] = apply_filters( 'ywmmq_tag_min_quantity_limit_label', sprintf( esc_html__( 'Minimum quantity required for tag %1$s: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['min'] ), $tag_link, $tag_qty_limit['min'] );
					} elseif ( $tag_qty_limit['min'] > 0 && $tag_qty_limit['max'] > 0 ) {
						/* translators: %1$s tag name - %2%d min quantity - %3%d max quantity */
						$rules_message[] = apply_filters( 'ywmmq_tag_min_max_quantity_limit_label', sprintf( esc_html__( 'Quantities allowed for tag %1$s: minimum %2$d - maximum %3$d', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, $tag_qty_limit['min'], $tag_qty_limit['max'] ), $tag_link, $tag_qty_limit['min'], $tag_qty_limit['max'] );
					}
				}

				if ( get_option( 'ywmmq_tag_value_limit' ) === 'yes' ) {
					$tag_val_limit = $this->tag_limits( $tag->term_id, 'value' );
					if ( 0 === (int) $tag_val_limit['min'] && $tag_val_limit['max'] > 0 ) {
						/* translators: %1$s tag name - %2%s amount */
						$rules_message[] = apply_filters( 'ywmmq_tag_max_value_limit_label', sprintf( esc_html__( 'Maximum spend allowed for tag %1$s: %2$s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['max'] ) ) ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['max'] ) ) );
					} elseif ( 0 === (int) $tag_val_limit['max'] && $tag_val_limit['min'] > 0 ) {
						/* translators: %1$s tag name - %2%s amount */
						$rules_message[] = apply_filters( 'ywmmq_tag_min_value_limit_label', sprintf( esc_html__( 'Minimum spend required for tag %1$s: %2$s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['min'] ) ) ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['min'] ) ) );
					} elseif ( $tag_val_limit['min'] > 0 && $tag_val_limit['max'] > 0 ) {
						/* translators: %1$s tag name - %2%d min amount - %3%d max amount */
						$rules_message[] = apply_filters( 'ywmmq_tag_min_max_value_limit_label', sprintf( esc_html__( 'Spend allowed for tag %1$s: minimum %2$s - maximum %3$s', 'yith-woocommerce-minimum-maximum-quantity' ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['max'] ) ) ), $tag_link, wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['min'] ) ), wc_price( apply_filters( 'yith_wcmcs_convert_price', $tag_val_limit['max'] ) ) );
					}
				}
			}

			if ( $rules_message ) {

				ob_start();

				?>
				<ul>
					<?php foreach ( $rules_message as $rule ) : ?>
						<li><?php echo $rule; //phpcs:ignore ?></li>
					<?php endforeach; ?>
				</ul>

				<?php $rules = ob_get_clean(); ?>

				<?php $rules_text = str_replace( '{rules}', $rules, get_option( 'ywmmq_rules_before_text' ) ); ?>

				<?php

			}

			?>
			<div class="ywmmq-rules-wrapper entry-summary">
				<?php echo $rules_text //phpcs:ignore ?>
			</div>
			<?php

		}

		/**
		 * Add-to-cart validation.
		 *
		 * @param boolean                   $passed         Check if add to cart is successful.
		 * @param integer                   $product_id     The product ID.
		 * @param integer                   $quantity       The product quantity.
		 * @param integer                   $variation_id   The variation ID.
		 * @param WC_Product_Variation|null $variation      The variation object.
		 * @param array                     $cart_item_data The cart item data.
		 *
		 * @return boolean
		 * @since  1.0.0
		 */
		public function add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = 0, $variation = null, $cart_item_data = array() ) {

			if ( get_option( 'ywmmq_message_enable_atc' ) === 'yes' ) {

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

				if ( ywmmq_is_wcpb_active() && get_option( 'ywmmq_bundle_quantity' ) === 'elements' && $product->is_type( 'yith_bundle' ) ) {
					return $passed;
				}

				if ( ! $product ) {
					return $passed;
				}

				if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
					return $passed;
				}

				$error        = '';
				$current_page = 'atc';

				if ( get_option( 'ywmmq_product_quantity_limit' ) === 'yes' ) {
					$cart_quantity = $variation_id ? $this->cart_product_qty( $variation_id, true ) : $this->cart_product_qty( $product_id );
					$product_data  = array(
						'product_id'   => $product_id,
						'quantity'     => $cart_quantity + $quantity,
						'variation_id' => $variation_id,
						'variation'    => $variation,
					);
					$this->check_validation_atc( $this->validate_product_quantity( $product_data, false, $current_page ), $error, $passed );
				}

				if ( $variation_id ) {
					$product = wc_get_product( $variation_id );

				}

				if ( function_exists( 'yith_wcmcs_set_currency' ) ) {
					yith_wcmcs_set_currency( get_user_meta( get_current_user_id(), 'yith_wcmcs_client_currency_id', true ) );
				}

				$bundle_quantity = 0;
				$product_value   = wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', wc_get_price_including_tax( $product, array( 'qty' => $quantity ) ) ) );

				if ( ywmmq_is_wcpb_active() ) {

					if ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) === 'elements' ) {

						$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

						foreach ( $cart_item_data['cartstamp'] as $item ) {
							$bundle_quantity += $item['quantity'];
						}
					}
				}

				if ( $passed && get_option( 'ywmmq_cart_quantity_limit' ) === 'yes' ) {
					$qty = ( 0 === (int) $bundle_quantity ) ? $quantity : $bundle_quantity;
					$this->check_validation_atc( $this->validate_cart_quantity( $current_page, $qty ), $error, $passed );
				}

				if ( $passed && get_option( 'ywmmq_cart_value_limit' ) === 'yes' ) {
					$this->check_validation_atc( $this->validate_cart_value( $current_page, $product_value ), $error, $passed );
				}

				if ( $passed && get_option( 'ywmmq_category_quantity_limit' ) === 'yes' ) {

					if ( ! ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) === 'elements' ) ) {

						$cart_quantities = $this->cart_category_qty();
						$product_cats    = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

						foreach ( $product_cats as $cat_id ) {

							$total_quantity = ( array_key_exists( $cat_id, $cart_quantities ) ) ? $cart_quantities[ $cat_id ] + $quantity : $quantity;

							$this->check_validation_atc( $this->validate_category( $cat_id, $total_quantity, $current_page, 'quantity' ), $error, $passed );

						}
					}
				}

				if ( $passed && get_option( 'ywmmq_category_value_limit' ) === 'yes' ) {

					$cart_values  = $this->cart_category_value();
					$product_cats = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

					foreach ( $product_cats as $cat_id ) {
						$total_value = ( array_key_exists( $cat_id, $cart_values ) ) ? $cart_values[ $cat_id ] + (float) $product_value : $product_value;
						$this->check_validation_atc( $this->validate_category( $cat_id, $total_value, $current_page, 'value' ), $error, $passed );
					}
				}

				if ( $passed && get_option( 'ywmmq_tag_quantity_limit' ) === 'yes' ) {

					if ( ! ( $product->is_type( 'yith_bundle' ) && get_option( 'ywmmq_bundle_quantity' ) === 'elements' ) ) {

						$cart_quantities = $this->cart_tag_qty();
						$product_tags    = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

						foreach ( $product_tags as $tag_id ) {
							$total_quantity = ( array_key_exists( $tag_id, $cart_quantities ) ) ? $cart_quantities[ $tag_id ] + $quantity : $quantity;
							$this->check_validation_atc( $this->validate_tag( $tag_id, $total_quantity, $current_page, 'quantity' ), $error, $passed );

						}
					}
				}

				if ( $passed && get_option( 'ywmmq_tag_value_limit' ) === 'yes' ) {

					$cart_values = $this->cart_tag_value();

					$product_tags = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

					foreach ( $product_tags as $tag_id ) {
						$total_value = ( array_key_exists( $tag_id, $cart_values ) ) ? $cart_values[ $tag_id ] + (float) $product_value : $product_value;
						$this->check_validation_atc( $this->validate_tag( $tag_id, $total_value, $current_page, 'value' ), $error, $passed );
					}
				}

				if ( ! empty( $error ) ) {

					if ( $passed ) {

						$this->message_filter = $error;
						add_filter( 'woocommerce_add_message', array( $this, 'add_to_cart_message' ) );

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
		 * @param array   $data   Validation data.
		 * @param string  $error  Error message.
		 * @param boolean $passed Check passed.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function check_validation_atc( $data, &$error, &$passed ) {

			if ( ! $data['is_valid'] ) {

				if ( 'min' === $data['limit'] ) {
					if ( empty( $error ) ) {
						$error = $data['message'];
					}
				} elseif ( 'step' === $data['limit'] ) {
					if ( empty( $error ) ) {
						$error = $data['message'];
					}
				} elseif ( 'max' === $data['limit'] ) {
					$passed = false;
					$error  = $data['message'];
				}
			}

		}

		/**
		 * Replace the default message on add to cart
		 *
		 * @param string $error Error message.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function add_to_cart_message( $error ) {

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
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function product_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$variable_products = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $key => $item ) {

					if ( isset( $item['yith_wcp_child_component_data'] ) || ( isset( $item['yith_wapo_sold_individually'] ) && 1 === (int) $item['yith_wapo_sold_individually'] ) || ( ! isset( $item['product_id'] ) || 'cart' === $key ) || apply_filters( 'ywmmq_bundle_check', false, $item ) ) {
						continue;
					}

					$this->contents_to_validate[ $key ]['excluded']  = false;
					$this->contents_to_validate[ $key ]['has_error'] = false;

					if ( apply_filters( 'ywmmq_check_exclusion', false, $key, $item['product_id'] ) ) {
						continue;
					}

					$product            = wc_get_product( $item['product_id'] );
					$limit_override     = $product->get_meta( '_ywmmq_product_quantity_limit_override' );
					$limit_var_override = $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' );

					if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
						if ( apply_filters( 'ywmmq_set_variation_quantity_locked', true ) ) {
							$this->check_validation_cart( $this->validate_product_quantity( $item, $key, $current_page ), $on_cart_page, $errors );
						} else {
							if ( array_key_exists( $item['product_id'], $variable_products ) ) {
								$variable_products[ $item['product_id'] ]['quantity'] += $item['quantity'];

								$variable_products[ $item['product_id'] ]['key'][] = $key;

							} else {
								$variable_products[ $item['product_id'] ]['quantity'] = $item['quantity'];
								$variable_products[ $item['product_id'] ]['key']      = array( $key );
							}
						}
					} else {
						$this->check_validation_cart( $this->validate_product_quantity( $item, $key, $current_page ), $on_cart_page, $errors );
					}
				}
			}

			if ( ! empty( $variable_products ) ) {

				foreach ( $variable_products as $parent_id => $info ) {
					$this->check_validation_cart(
						$this->validate_product_quantity(
							array(
								'product_id' => $parent_id,
								'quantity'   => $info['quantity'],
							),
							$info['key'],
							$current_page
						),
						$on_cart_page,
						$errors
					);
				}
			}

		}

		/**
		 * Validate the product quantity limit and return error messages
		 *
		 * @param array  $item         The current item array.
		 * @param mixed  $key          The key of the array of keys.
		 * @param string $current_page The current page.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate_product_quantity( $item, $key = false, $current_page = '' ) {

			if ( ! isset( $item['product_id'] ) ) {
				return array();
			}

			$return = array(
				'is_valid' => true,
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

			$product_limit = $this->product_limits( $item['product_id'], $item['variation_id'] );

			if ( (int) $product_limit['min'] > 0 && $item['quantity'] < (int) $product_limit['min'] ) {

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

					$return['message'] = ywmmq_product_quantity_error( 'min', $product_limit['min'], $item, $current_page );

				}
			} elseif ( (int) $product_limit['max'] > 0 && $item['quantity'] > (int) $product_limit['max'] ) {

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

					$return['message'] = ywmmq_product_quantity_error( 'max', $product_limit['max'], $item, $current_page );

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

					$return['message'] = ywmmq_product_quantity_error( 'step', $product_limit['step'], $item, $current_page );

				}
			}

			return $return;

		}

		/**
		 * Return quantity limit for specified product/variation
		 *
		 * @param integer $product_id   The product ID.
		 * @param integer $variation_id The variation ID.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function product_limits( $product_id, $variation_id ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1,
			);

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product            = wc_get_product( $product_id );
			$limit_override     = $product->get_meta( '_ywmmq_product_quantity_limit_override' );
			$limit_var_override = $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' );
			$limit_var_override = 0 === (int) $variation_id ? 'no' : $limit_var_override;

			if ( 'yes' === $limit_override ) {
				if ( ( $variation_id > 0 ) && 'yes' === $limit_var_override ) {
					if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
						$variation_id = yit_wpml_object_id( $variation_id, 'product', true, wpml_get_default_language() );
					}
					$variation     = wc_get_product( $variation_id );
					$limit['min']  = $variation->get_meta( '_ywmmq_product_minimum_quantity' );
					$limit['max']  = $variation->get_meta( '_ywmmq_product_maximum_quantity' );
					$limit['step'] = $variation->get_meta( '_ywmmq_product_step_quantity' );
				} elseif ( ( $variation_id > 0 && 'yes' !== $limit_var_override ) || ( 0 === (int) $variation_id && 'yes' !== $limit_var_override ) ) {
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
		 * @param integer $product_id   The product ID.
		 * @param boolean $is_variation Check if is a variation.
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function cart_product_qty( $product_id, $is_variation = false ) {

			$cart     = WC()->cart->get_cart();
			$cart_qty = 0;

			foreach ( $cart as $item_id => $item ) {

				if ( $is_variation ) {
					if ( (int) $item['variation_id'] === (int) $product_id ) {
						return $item['quantity'];
					}
				} else {
					if ( (int) $item['product_id'] === (int) $product_id ) {
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
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function category_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_quantities = $this->cart_category_qty();

			foreach ( $cart_quantities as $category_id => $quantity ) {
				$this->check_validation_cart( $this->validate_category( $category_id, $quantity, $current_page, 'quantity' ), $on_cart_page, $errors );
			}
		}

		/**
		 * Validate the category value from cart page
		 *
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function category_value_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_values = $this->cart_category_value();

			foreach ( $cart_values as $category_id => $value ) {
				$this->check_validation_cart( $this->validate_category( $category_id, $value, $current_page, 'value' ), $on_cart_page, $errors );
			}
		}

		/**
		 * Validate the category quantity/value limit and return error messages
		 *
		 * @param integer $category_id  The category ID.
		 * @param mixed   $qty_val      Quantity or amount value.
		 * @param string  $current_page The current page.
		 * @param string  $limit_type   The limit type.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate_category( $category_id, $qty_val, $current_page, $limit_type ) {

			$return = array(
				'is_valid' => true,
			);

			$category_limit = $this->category_limits( $category_id, $limit_type );

			if ( 'value' === $limit_type ) {

				if ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['min'] ) ) > 0 && $qty_val < wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['min'] ) ) ) {

					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {
						$return['message'] = ywmmq_category_error( 'min', wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['min'] ) ), $category_id, $current_page, $limit_type );
					}
				} elseif ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['max'] ) ) > 0 && $qty_val > wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['max'] ) ) ) {

					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {
						$return['message'] = ywmmq_category_error( 'max', wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $category_limit['max'] ) ), $category_id, $current_page, $limit_type );
					}
				}
			} else {
				if ( (int) $category_limit['min'] > 0 && $qty_val < (int) $category_limit['min'] ) {

					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {
						$return['message'] = ywmmq_category_error( 'min', $category_limit['min'], $category_id, $current_page, $limit_type );
					}
				} elseif ( (int) $category_limit['max'] > 0 && $qty_val > (int) $category_limit['max'] ) {

					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {
						$return['message'] = ywmmq_category_error( 'max', $category_limit['max'], $category_id, $current_page, $limit_type );
					}
				} elseif ( (int) $category_limit['step'] > 1 && fmod( $qty_val, (int) $category_limit['step'] ) > 0 ) {
					$return['is_valid'] = false;
					$return['limit']    = 'step';
					if ( $current_page ) {
						$return['message'] = ywmmq_category_error( 'step', $category_limit['step'], $category_id, $current_page, $limit_type );
					}
				}
			}

			return $return;

		}

		/**
		 * Return quantity/value limits for specified category
		 *
		 * @param integer $category_id The category ID.
		 * @param string  $limit_type  The limit type.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function category_limits( $category_id, $limit_type = 'quantity' ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1,
			);

			$category_exclusion = get_term_meta( $category_id, '_ywmmq_category_exclusion', true );

			if ( 'yes' === $category_exclusion ) {
				return $limit;
			}

			$category_limit_override = get_term_meta( $category_id, '_ywmmq_category_' . $limit_type . '_limit_override', true );

			if ( 'yes' === $category_limit_override ) {

				$limit['min']  = get_term_meta( $category_id, '_ywmmq_category_minimum_' . $limit_type, true );
				$limit['max']  = get_term_meta( $category_id, '_ywmmq_category_maximum_' . $limit_type, true );
				$limit['step'] = get_term_meta( $category_id, '_ywmmq_category_step_' . $limit_type, true );

			} else {

				$limit['min']  = get_option( 'ywmmq_category_minimum_' . $limit_type );
				$limit['max']  = get_option( 'ywmmq_category_maximum_' . $limit_type );
				$limit['step'] = get_option( 'ywmmq_category_step_' . $limit_type );

			}

			return $limit;

		}

		/**
		 * Return cart quantity for each category.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function cart_category_qty() {

			$category_qts = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
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
		 * @return array
		 * @since  1.0.0
		 */
		public function cart_category_value() {

			$category_values = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					if ( ! isset( $item['line_total'] ) ) {
						$_product      = wc_get_product( $item['product_id'] );
						$product_value = wc_format_decimal( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ) );
					} else {
						$product_value = wc_format_decimal( $item['line_total'] + $item['line_tax'] );
					}

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
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function tag_quantity_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_quantities = $this->cart_tag_qty();

			foreach ( $cart_quantities as $tag_id => $quantity ) {
				$this->check_validation_cart( $this->validate_tag( $tag_id, $quantity, $current_page, 'quantity' ), $on_cart_page, $errors );
			}
		}

		/**
		 * Validate the tag value from cart page
		 *
		 * @param string  $current_page The current page.
		 * @param boolean $on_cart_page Check if current page is cart.
		 * @param array   $errors       The errors array.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function tag_value_cart( $current_page, $on_cart_page, &$errors ) {

			$cart_values = $this->cart_tag_value();

			foreach ( $cart_values as $tag_id => $value ) {
				$this->check_validation_cart( $this->validate_tag( $tag_id, $value, $current_page, 'value' ), $on_cart_page, $errors );
			}
		}

		/**
		 * Validate the tag quantity/value limit and return error messages
		 *
		 * @param integer $tag_id       The tag ID.
		 * @param mixed   $qty_val      Quantity or amount value.
		 * @param string  $current_page The current page.
		 * @param string  $limit_type   The limit type.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate_tag( $tag_id, $qty_val, $current_page, $limit_type ) {

			$return = array(
				'is_valid' => true,
			);

			$tag_limit = $this->tag_limits( $tag_id, $limit_type );

			if ( 'value' === $limit_type ) {
				if ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $tag_limit['min'] ) ) > 0 && $qty_val < (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $tag_limit['min'] ) ) ) {
					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {
						$return['message'] = ywmmq_tag_error( 'min', apply_filters( 'yith_wcmcs_convert_price', wc_format_decimal( $tag_limit['min'] ) ), $tag_id, $current_page, $limit_type );
					}
				} elseif ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $tag_limit['max'] ) ) > 0 && $qty_val > (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $tag_limit['max'] ) ) ) {
					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {
						$return['message'] = ywmmq_tag_error( 'max', apply_filters( 'yith_wcmcs_convert_price', wc_format_decimal( $tag_limit['max'] ) ), $tag_id, $current_page, $limit_type );
					}
				}
			} else {
				if ( (int) $tag_limit['min'] > 0 && $qty_val < (int) $tag_limit['min'] ) {
					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {
						$return['message'] = ywmmq_tag_error( 'min', $tag_limit['min'], $tag_id, $current_page, $limit_type );
					}
				} elseif ( (int) $tag_limit['max'] > 0 && $qty_val > (int) $tag_limit['max'] ) {
					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {
						$return['message'] = ywmmq_tag_error( 'max', $tag_limit['max'], $tag_id, $current_page, $limit_type );
					}
				} elseif ( (int) $tag_limit['step'] > 1 && fmod( $qty_val, (int) $tag_limit['step'] ) > 0 ) {
					$return['is_valid'] = false;
					$return['limit']    = 'step';
					if ( $current_page ) {
						$return['message'] = ywmmq_tag_error( 'step', $tag_limit['step'], $tag_id, $current_page, $limit_type );
					}
				}
			}

			return $return;

		}

		/**
		 * Return quantity/value limits for specified tag
		 *
		 * @param integer $tag_id     The tag ID.
		 * @param string  $limit_type The limit type.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function tag_limits( $tag_id, $limit_type = 'quantity' ) {

			$limit = array(
				'min'  => 0,
				'max'  => 0,
				'step' => 1,
			);

			$tag_exclusion = get_term_meta( $tag_id, '_ywmmq_tag_exclusion', true );

			if ( 'yes' === $tag_exclusion ) {
				return $limit;
			}

			$tag_limit_override = get_term_meta( $tag_id, '_ywmmq_tag_' . $limit_type . '_limit_override', true );

			if ( 'yes' === $tag_limit_override ) {

				$limit['min']  = get_term_meta( $tag_id, '_ywmmq_tag_minimum_' . $limit_type, true );
				$limit['max']  = get_term_meta( $tag_id, '_ywmmq_tag_maximum_' . $limit_type, true );
				$limit['step'] = get_term_meta( $tag_id, '_ywmmq_tag_step_' . $limit_type, true );

			} else {

				$limit['min']  = get_option( 'ywmmq_tag_minimum_' . $limit_type );
				$limit['max']  = get_option( 'ywmmq_tag_maximum_' . $limit_type );
				$limit['step'] = get_option( 'ywmmq_tag_step_' . $limit_type );

			}

			return $limit;

		}

		/**
		 * Return cart quantity for each tag.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function cart_tag_qty() {

			$tag_qts = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
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
		 * @return array
		 * @since  1.0.0
		 */
		public function cart_tag_value() {

			$tag_values = array();

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {
						continue;
					}

					if ( ! isset( $item['line_total'] ) ) {
						$_product      = wc_get_product( $item['product_id'] );
						$product_value = wc_format_decimal( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ) );
					} else {
						$product_value = wc_format_decimal( $item['line_total'] + $item['line_tax'] );
					}
					$product_tag = wp_get_object_terms( $item['product_id'], 'product_tag', array( 'fields' => 'ids' ) );

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
		 * CART QUANTITY RULES FUNCTIONS
		 */

		/**
		 * Validate the cart quantity limit and return error messages
		 *
		 * @param string  $current_page The current page.
		 * @param integer $added_qty    The quantity added to the cart.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate_cart_quantity( $current_page = '', $added_qty = 0 ) {

			$return = array(
				'is_valid' => true,
			);

			$cart_limit = $this->cart_limits( 'quantity' );

			$total_cart_qty = $this->cart_total_qty();

			$total_cart_qty += $added_qty;

			if ( (int) $cart_limit['min'] > 0 && $total_cart_qty < (int) $cart_limit['min'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'min';

				if ( $current_page ) {
					$return['message'] = ywmmq_cart_error( 'min', $cart_limit['min'], $total_cart_qty, $current_page, 'quantity' );
				}
			} elseif ( (int) $cart_limit['max'] > 0 && $total_cart_qty > (int) $cart_limit['max'] ) {

				$return['is_valid'] = false;
				$return['limit']    = 'max';

				if ( $current_page ) {
					$return['message'] = ywmmq_cart_error( 'max', $cart_limit['max'], $total_cart_qty, $current_page, 'quantity' );

				}
			} elseif ( (int) $cart_limit['step'] > 1 && fmod( $total_cart_qty, (int) $cart_limit['step'] ) > 0 ) {

				$return['is_valid'] = false;
				$return['limit']    = 'step';

				if ( $current_page ) {
					$return['message'] = ywmmq_cart_error( 'step', $cart_limit['step'], $total_cart_qty, $current_page, 'quantity' );
				}
			}

			return $return;

		}

		/**
		 * Return the total quantity of all items in the cart
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function cart_total_qty() {

			$total_qty = 0;

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
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
		 * @param string $type Limit to check.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function cart_limits( $type = 'quantity' ) {

			$limit = array(
				'min'  => get_option( 'ywmmq_cart_minimum_' . $type ),
				'max'  => get_option( 'ywmmq_cart_maximum_' . $type ),
				'step' => get_option( 'ywmmq_cart_step_' . $type ),
			);

			return $limit;

		}

		/**
		 * CART VALUE RULES FUNCTIONS
		 */

		/**
		 * Validate the cart quantity value and return error messages
		 *
		 * @param string $current_page The current page.
		 * @param mixed  $added_value  The value added to the cart.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function validate_cart_value( $current_page, $added_value = 0 ) {

			$return = array(
				'is_valid' => true,
			);

			$cart_limit = $this->cart_limits( 'value' );

			if ( (float) $cart_limit['min'] > 0 || (float) $cart_limit['max'] > 0 ) {

				if ( 'cart' === $this->contents_type ) {
					WC()->cart->calculate_totals();
				}

				$excluded_products_value = $this->cart_total_excluded_value();

				if ( $excluded_products_value > 0 ) {
					$this->excluded_products = true;
				}
				$calculate_taxes = apply_filters( 'ywmmq_calculate_taxes', true );

				if ( 'cart' === $this->contents_type ) {
					$cart_tax         = $calculate_taxes ? WC()->cart->get_subtotal_tax() : 0;
					$total_cart_value = apply_filters( 'yith_min_max_cart_total', ( WC()->cart->get_subtotal() + $cart_tax ) - $excluded_products_value, WC()->cart );
				} else {
					$total_cart_value = apply_filters( 'yith_min_max_cart_total_raq', ( $this->total_raq_value() ) - $excluded_products_value );
				}

				if ( get_option( 'ywmmq_cart_value_shipping' ) === 'yes' && 'cart' === $this->contents_type ) {
					$shipping_tax = $calculate_taxes ? WC()->cart->get_shipping_tax() : 0;

					$total_cart_value += ( WC()->cart->get_shipping_total() + $shipping_tax );
				}

				if ( get_option( 'ywmmq_cart_value_calculate_coupons' ) === 'no' && 'cart' === $this->contents_type ) {
					$discount_tax = $calculate_taxes ? WC()->cart->get_discount_tax() : 0;

					$total_cart_value -= ( WC()->cart->get_discount_total() + $discount_tax );
				}

				if ( get_option( 'ywmmq_cart_value_calculate_giftcard' ) === 'no' && ywmmq_is_ywgc_active() && 'cart' === $this->contents_type ) {

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

				if ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['min'] ) ) > 0 && $total_cart_value < wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['min'] ) ) ) {
					$return['is_valid'] = false;
					$return['limit']    = 'min';

					if ( $current_page ) {
						$return['message'] = ywmmq_cart_error( 'min', wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['min'] ) ), $total_cart_value, $current_page, 'value' );
					}
				} elseif ( (float) wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['max'] ) ) > 0 && $total_cart_value > wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['max'] ) ) ) {
					$return['is_valid'] = false;
					$return['limit']    = 'max';

					if ( $current_page ) {
						$return['message'] = ywmmq_cart_error( 'max', wc_format_decimal( apply_filters( 'yith_wcmcs_convert_price', $cart_limit['max'] ) ), $total_cart_value, $current_page, 'value' );
					}
				}
			}

			return $return;

		}

		/**
		 * Return the total value of all excluded items in the cart
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function cart_total_excluded_value() {

			$total_value = 0;

			if ( $this->contents_to_validate ) {

				foreach ( $this->contents_to_validate as $item_id => $item ) {

					if ( ! isset( $item['product_id'] ) || 'cart' === $item_id ) {
						continue;
					}

					if ( apply_filters( 'ywmmq_check_exclusion', false, $item_id, $item['product_id'] ) ) {

						if ( 'cart' === $this->contents_type ) {
							$total_value += wc_format_decimal( $item['line_total'] + $item['line_tax'] );
						} else {
							$_product = wc_get_product( $item['product_id'] );

							$total_value += wc_format_decimal( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ) );
						}
					}
				}
			}

			return $total_value;

		}

		/**
		 * Check the active exclusions for each product in the cart
		 *
		 * @param mixed   $value      Quantity or amount value.
		 * @param string  $item_key   Cart item key.
		 * @param integer $product_id The product ID.
		 *
		 * @return boolean
		 * @since  1.0.0
		 */
		public function check_exclusion( $value, $item_key, $product_id ) {

			$this->contents_to_validate[ $item_key ]['excluded'] = false;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $product_id );

			if ( $product->get_meta( '_ywmmq_product_exclusion' ) === 'yes' ) {
				$this->contents_to_validate[ $item_key ]['excluded'] = true;
				$this->excluded_products                             = true;

				return true;
			}

			$product_categories = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

			foreach ( $product_categories as $cat_id ) {

				$category_exclusion = get_term_meta( $cat_id, '_ywmmq_category_exclusion', true );

				if ( 'yes' === $category_exclusion ) {
					$this->contents_to_validate[ $item_key ]['excluded'] = true;
					$this->excluded_products                             = true;

					return true;
				}
			}

			$product_tag = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

			foreach ( $product_tag as $tag_id ) {

				$tag_exclusion = get_term_meta( $tag_id, '_ywmmq_tag_exclusion', true );

				if ( 'yes' === $tag_exclusion ) {
					$this->contents_to_validate[ $item_key ]['excluded'] = true;
					$this->excluded_products                             = true;

					return true;
				}
			}

			return $value;
		}

		/** PRODUCT BUNDLES COMPATIBILITY */

		/**
		 * Check if product is a bundle or belongs to a bundle
		 *
		 * @param mixed      $value   Quantity or amount value.
		 * @param WC_Product $product The product.
		 *
		 * @return boolean
		 * @since  1.1.5
		 */
		public function bundle_check( $value, $product ) {

			if ( get_option( 'ywmmq_bundle_quantity' ) === 'bundle' && isset( $product['bundled_by'] ) ) {
				return true;
			}

			if ( get_option( 'ywmmq_bundle_quantity' ) === 'elements' && isset( $product['cartstamp'] ) && ! isset( $product['bundled_by'] ) ) {
				return true;
			}

			if ( get_option( 'ywmmq_bundle_quantity' ) === 'elements' && isset( $product['yith_wcpb_hidden'] ) && true === $product['yith_wcpb_hidden'] ) {
				return true;
			}

			return $value;

		}

		/** GIFT CARDS COMPATIBILITY */

		/**
		 * Get Gift Card total
		 *
		 * @param float $total_cart_value Total cart value.
		 *
		 * @return boolean
		 * @since  1.3.0
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
		 * Validate Request a quote on submit
		 *
		 * @param array $errors Errors triggered.
		 *
		 * @return array
		 * @since  1.3.3
		 */
		public function validate_raq( $errors ) {

			if ( get_option( 'ywmmq_enable_rules_on_quotes' ) !== 'yes' ) {
				return $errors;
			}

			$this->contents_type        = 'quote';
			$this->contents_to_validate = YITH_Request_Quote()->get_raq_return();

			$mmq_errors = $this->validate( 'cart', true );

			if ( $mmq_errors ) {
				$errors = array_merge( $errors, $mmq_errors );
			}

			return $errors;
		}

		/**
		 * Return the total value of all items in the quote list
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function total_raq_value() {

			$total_value = 0;

			if ( $this->contents_to_validate ) {
				foreach ( $this->contents_to_validate as $item_id => $item ) {

					$_product = wc_get_product( $item['product_id'] );

					$total_value += wc_format_decimal( wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ) );
				}
			}

			return $total_value;

		}

		/**
		 * Set quantity limit for loop when using RAQ
		 *
		 * @param array $raq         RAQ arguments.
		 * @param array $product_raq Product RAQ object.
		 *
		 * @return array
		 * @since  1.5.4
		 */
		public function set_min_quantity_loop_raq( $raq, $product_raq ) {

			if ( ! is_array( $product_raq['quantity'] ) ) {

				global $sitepress;
				$has_wpml   = ! empty( $sitepress ) ? true : false;
				$product_id = $product_raq['product_id'];
				if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_raq['product_id'], 'product', true, wpml_get_default_language() );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->is_type( 'variable' ) ) {
					$min_quantity    = apply_filters( 'ywraq_quantity_min_value', $product_raq['quantity'], $product );
					$raq['quantity'] = $min_quantity > $product_raq['quantity'] ? $min_quantity : $product_raq['quantity'];
				}
			}

			return $raq;
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array $links links plugin array.
		 *
		 * @return array
		 * @since  1.0.0
		 * @use    plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->panel_page, true, YWMMQ_SLUG );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array  $new_row_meta_args Row meta args.
		 * @param array  $plugin_meta       Plugin meta.
		 * @param string $plugin_file       Plugin File.
		 * @param array  $plugin_data       Plugin data.
		 * @param string $status            Status.
		 * @param string $init_file         Init file.
		 *
		 * @return array
		 * @since  1.0.0
		 * @use    plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWMMQ_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug']       = YWMMQ_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since  2.0.0
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
		 * @return void
		 * @since  2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YWMMQ_SLUG, YWMMQ_INIT );
		}

	}

}
