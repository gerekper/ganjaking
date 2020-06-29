<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WFBT' ) ) {
	/**
	 * YITH WooCommerce Frequently Bought Together Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WFBT {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WFBT
		 */
		protected static $instance;

		/**
		 * Action add to cart group
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $actionadd = 'yith_bought_together';

		/**
		 * Old meta array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $old_metas = array();

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WFBT_VERSION;


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return \YITH_WFBT
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
		 * @since 1.0.0
		 * @return mixed YITH_WFBT_Admin | YITH_WFBT_Frontend
		 */
		public function __construct() {

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// Class admin
			if ( $this->is_admin() ) {
				// require admin class
				require_once( 'class.yith-wfbt-admin.php' );
				// admin class
				YITH_WFBT_Admin();

				if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {
					// required class
					include_once( 'compatibility/yith-woocommerce-product-vendors.php' );
					YITH_WFBT_Multivendor();
				}
			} else {
				// require frontend class
				require_once( 'class.yith-wfbt-discount.php' );
				require_once( 'class.yith-wfbt-frontend.php' );
				// the class
				YITH_WFBT_Frontend();
			}

			$this->set_image_size();

			// add group to cart
			add_action( 'wp_loaded', array( $this, 'add_group_to_cart' ), 20 );

			$this->old_metas = array(
				'_yith_wfbt_ids'               => 'products',
				'_yith_wfbt_default_variation' => 'default_variation',
				'_yith_wfbt_num'               => 'num_visible',
			);

			// retro compatibility with older metas
			add_filter( 'get_post_metadata', array( $this, 'get_meta' ), 10, 4 );
			add_filter( 'update_post_metadata', array( $this, 'update_meta' ), 10, 4 );

			// register Gutenberg Block
			add_action( 'init', array( $this, 'register_gutenberg_block' ), 10 );

			// import/export plugin meta
			add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_plugin_export_column' ), 10, 1 );
			add_filter( 'woocommerce_product_export_product_column_frequently_bought_together', array( $this, 'export_column_value' ), 10, 3 );
			add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'import_mapping_default' ), 10, 1 );
			add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'import_mapping_option' ), 10, 2 );
			add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'import_parse_plugin_data' ), 10, 2 );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
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
		 * Check if is admin
		 *
		 * @since  1.1.0
		 * @access public
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_admin() {
			$context_check = isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend';
			$is_admin      = is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && $context_check );
			return apply_filters( 'yith_wfbt_check_is_admin', $is_admin );
		}

		/**
		 * Add upselling group to cart
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_group_to_cart() {

			if ( ! ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == $this->actionadd && wp_verify_nonce( $_REQUEST['_wpnonce'], $this->actionadd ) ) ) {
				return;
			}

			wc_nocache_headers();

			$products_added = $message = array();
			$offered        = ( isset( $_POST['offeringID'] ) && is_array( $_POST['offeringID'] ) ) ? $_POST['offeringID'] : [];

			if ( empty( $offered ) ) {
				return;
			}

			$main_product = isset( $_POST['yith-wfbt-main-product'] ) ? intval( $_POST['yith-wfbt-main-product'] ) : $_POST['offeringID'][0];

			foreach ( $offered as $id ) {

				$product = wc_get_product( $id );

				$attr         = array();
				$variation_id = '';

				if ( $product->is_type( 'variation' ) ) {
					$attr         = $product->get_variation_attributes();
					$variation_id = $product->get_id();
					$product_id   = yit_get_base_product_id( $product );
				} else {
					$product_id = yit_get_prop( $product, 'id', true );
				}

				$cart_item_key = WC()->cart->add_to_cart( $product_id, 1, $variation_id, $attr );
				if ( $cart_item_key ) {
					$products_added[ $cart_item_key ] = $variation_id ? $variation_id : $product_id;
					$message[ $product_id ]           = 1;
				}
			}

			do_action( 'yith_wfbt_group_added_to_cart', $products_added, $main_product, $offered );

			if ( ! empty( $message ) ) {
				wc_add_to_cart_message( $message );
			}

			if ( get_option( 'yith-wfbt-redirect-checkout', 'no' ) == 'yes' ) {
				$url = wc_get_checkout_url();
			} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				$url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
			} else {
				//redirect to product page
				$url = remove_query_arg( array( 'action', '_wpnonce' ) );
			}

			wp_redirect( esc_url( $url ) );
			exit;

		}

		/**
		 * Set Image size yith_wfbt_image_size
		 *
		 * @since  1.1.2
		 * @author Francesco Licandro
		 * @return void
		 */
		public function set_image_size() {
			// set image size
			$size   = get_option( 'yith-wfbt-image-size' );
			$width  = isset( $size['width'] ) ? $size['width'] : '70';
			$height = isset( $size['height'] ) ? $size['height'] : '70';
			$crop   = isset( $size['crop'] ) ? $size['crop'] : true;

			add_image_size( 'yith_wfbt_image_size', $width, $height, $crop );
		}

		/**
		 * Filter get post meta for new metas
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param mixed   $value
		 * @param integer $object_id
		 * @param string  $meta_key
		 * @param boolean $single
		 * @return mixed
		 */
		function get_meta( $value, $object_id, $meta_key, $single ) {
			if ( is_string( $meta_key ) && array_key_exists( $meta_key, $this->old_metas ) ) {
				$value = yith_wfbt_get_meta( $object_id, $this->old_metas[ $meta_key ] );
			}

			return $value;
		}

		/**
		 * Filter update post meta for new metas
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param null|boolean $check
		 * @param integer      $object_id
		 * @param string       $meta_key
		 * @param mixed        $value
		 * @return mixed
		 */
		function update_meta( $check, $object_id, $meta_key, $value ) {
			if ( array_key_exists( $meta_key, $this->old_metas ) ) {
				$key = $this->old_metas[ $meta_key ];
				yith_wfbt_set_meta( $object_id, array( $key => $value ) );
				return true;
			}

			return $check;
		}

		/**
		 * Register plugin Gutenberg block
		 *
		 * @since  1.3.7
		 * @author Francesco Licandro
		 * @return void
		 */
		public function register_gutenberg_block() {
			$block = array(
				'ywfbt-blocks' => array(
					'title'          => _x( 'Frequently Bought Form', '[gutenberg]: block name', 'yith-woocommerce-frequently-bought-together' ),
					'description'    => _x( 'With this block you can print a product "frequently bought together" form.', '[gutenberg]: block description', 'yith-woocommerce-frequently-bought-together' ),
					'shortcode_name' => 'ywfbt_form',
					'do_shortcode'   => false,
					'attributes'     => array(
						'product_id' => array(
							'type'    => 'text',
							'label'   => _x( 'Add the product id (leave blank to get global product value)', '[gutenberg]: attributes description', 'yith-woocommerce-frequently-bought-together' ),
							'default' => '',
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $block );
		}

		/**
		 * Add plugin column to product export CSV
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array $columns
		 * @return array
		 */
		public function add_plugin_export_column( $columns ) {
			$columns['frequently_bought_together'] = __( 'Frequently Bought Together Data', 'yith-woocommerce-frequently-bought-together' );
			return $columns;
		}

		/**
		 * Handle plugin export column content
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param string     $value
		 * @param WC_Product $product
		 * @param string     $column_id
		 * @return string
		 */
		public function export_column_value( $value, $product, $column_id ) {

			$data = yith_wfbt_get_meta( $product );
			if ( empty( $data ) ) {
				return $value;
			}

			// format products for export
			empty( $data['products'] ) || $data['products'] = $this->prepare_products_for_export( $data['products'] );

			return maybe_serialize( $data );
		}

		/**
		 * Prepare products option for export
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array $products
		 * @return array
		 */
		protected function prepare_products_for_export( $products ) {
			$prepared_products = array();
			foreach ( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( empty( $product ) ) {
					continue;
				}

				$prepared_products[] = 'id:' . $product->get_id();
			}

			return $prepared_products;
		}

		/**
		 * Add import mapping options to mapping array
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array $options
		 * @param       $item
		 * @return array
		 */
		public function import_mapping_option( $options, $item ) {
			$options['frequently_bought_together'] = __( 'Frequently Bought Together Data', 'yith-woocommerce-frequently-bought-together' );
			return $options;
		}

		/**
		 * Parse plugin data before insert
		 *
		 * @param array                    $parsed_data
		 * @param \WC_Product_CSV_Importer $importer
		 * @return array
		 */
		public function import_parse_plugin_data( $parsed_data, $importer ) {

			if ( empty( $parsed_data['frequently_bought_together'] ) ) {
				return $parsed_data;
			}

			$data = maybe_unserialize( $parsed_data['frequently_bought_together'] );
			// remove the useless data from parsed array
			unset( $parsed_data['frequently_bought_together'] );

			is_array( $data['products'] ) || $data['products'] = [];

			$parsed_products = [];
			foreach ( $data['products'] as $product_data ) {
				$parsed_products[] = $importer->parse_relative_field( $product_data );
			}
			$data['products'] = array_filter( $parsed_products );

			$parsed_data['meta_data'][] = [
				'key'   => YITH_WFBT_META,
				'value' => $data,
			];

			return $parsed_data;
		}

		/**
		 * Mapping default columns name
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array $columns
		 * @return array
		 */
		public function import_mapping_default( $columns ) {
			$columns[ __( 'Frequently Bought Together Data', 'yith-woocommerce-frequently-bought-together' ) ] = 'frequently_bought_together';
			return $columns;
		}
	}
}

/**
 * Unique access to instance of YITH_WFBT class
 *
 * @since 1.0.0
 * @return \YITH_WFBT
 */
function YITH_WFBT() {
	return YITH_WFBT::get_instance();
}