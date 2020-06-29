<?php
/**
 * Admin Premium class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WACP_Admin_Premium extends YITH_WACP_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WACP_Admin_Premium
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WACP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WACP_Admin_Premium
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
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// Add custom popup size.
			add_action( 'woocommerce_admin_field_yith_wacp_box_size', array( $this, 'set_box_size' ), 10, 1 );

			// Add custom draggable position type.
			add_action( 'woocommerce_admin_field_yith_wacp_drag_pos', array( $this, 'admin_fields_draggable_position' ), 10, 1 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_yith-wacp-mini-cart-position', array( $this, 'sanitize_option_draggable' ), 10, 3 );

			// Add custom image size type.
			add_action( 'woocommerce_admin_field_yith_wacp_image_size', array( $this, 'custom_image_size' ), 10, 1 );

			add_action( 'woocommerce_admin_field_yith_wacp_select_prod', array( $this, 'yith_wacp_select_prod' ), 10, 1 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_yith-wacp-related-products', array( $this, 'sanitize_option_products' ), 10, 3 );

			add_filter( 'yith_wacp_admin_tabs', array( $this, 'add_tabs' ), 1 );

			// Add exclusions tables.
			add_action( 'yith_wacp_exclusions_prod_table', array( $this, 'exclusions_prod_table' ) );
			add_action( 'yith_wacp_exclusions_cat_table', array( $this, 'exclusions_cat_table' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_premium' ) );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WACP_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WACP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WACP_INIT, YITH_WACP_SECRET_KEY, YITH_WACP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WACP_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WACP_SLUG, YITH_WACP_INIT );
		}

		/**
		 * Add box size to standard WC types
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $value The size values array.
		 */
		public function set_box_size( $value ) {

			$option_values = get_option( $value['id'] );
			$width         = isset( $option_values['width'] ) ? $option_values['width'] : $value['default']['width'];
			$height        = isset( $option_values['height'] ) ? $option_values['height'] : $value['default']['height'];

			?>
			<tr valign="top">
				<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
				<td class="forminp yith_box_size_settings">

					<input name="<?php echo esc_attr( $value['id'] ); ?>[width]"
						id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3"
						value="<?php echo esc_attr( $width ); ?>"/>
					&times;
					<input name="<?php echo esc_attr( $value['id'] ); ?>[height]"
						id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3"
						value="<?php echo esc_attr( $height ); ?>"/> px
					<div>
						<span class="description"><?php echo wp_kses_post( $value['desc'] ); ?></span>
					</div>

				</td>
			</tr>
			<?php

		}

		/**
		 * Add premium tabs in settings panel
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param mixed $tabs An array of plugin admin settings tabs.
		 * @return mixed
		 */
		public function add_tabs( $tabs ) {
			$tabs['style']           = __( 'Style', 'yith-woocommerce-added-to-cart-popup' );
			$tabs['mini-cart']       = __( 'Mini Cart', 'yith-woocommerce-added-to-cart-popup' );
			$tabs['exclusions-prod'] = __( 'Product Exclusion List', 'yith-woocommerce-added-to-cart-popup' );
			$tabs['exclusions-cat']  = __( 'Category Exclusion List', 'yith-woocommerce-added-to-cart-popup' );

			return $tabs;
		}

		/**
		 * Add products exclusion table
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function exclusions_prod_table() {

			$table = new YITH_WACP_Exclusions_Prod_Table();
			$table->prepare_items();

			if ( file_exists( YITH_WACP_TEMPLATE_PATH . '/admin/exclusions-prod-table.php' ) ) {
				include_once YITH_WACP_TEMPLATE_PATH . '/admin/exclusions-prod-table.php';
			}
		}

		/**
		 * Add categories exclusion table
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function exclusions_cat_table() {

			$table = new YITH_WACP_Exclusions_Cat_Table();
			$table->prepare_items();

			if ( file_exists( YITH_WACP_TEMPLATE_PATH . '/admin/exclusions-cat-table.php' ) ) {
				include_once YITH_WACP_TEMPLATE_PATH . '/admin/exclusions-cat-table.php';
			}
		}

		/**
		 * Enqueue premium styles and scripts
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts_premium() {

			wp_register_style( 'yith-wacp-admin-style', YITH_WACP_ASSETS_URL . '/css/wacp-admin.css', false, 'all' );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['page'] ) && $_GET['page'] === $this->panel_page ) {
				wp_enqueue_style( 'yith-wacp-admin-style' );
			}
		}

		/**
		 * Add custom image size to standard WC types
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $value The size values array.
		 */
		public function custom_image_size( $value ) {

			$option_values = get_option( $value['id'] );
			$width         = isset( $option_values['width'] ) ? $option_values['width'] : $value['default']['width'];
			$height        = isset( $option_values['height'] ) ? $option_values['height'] : $value['default']['height'];
			$crop          = isset( $option_values['crop'] ) ? $option_values['crop'] : $value['default']['crop'];

			$deps = '';
			if ( ! empty( $value['deps'] ) ) {
				// Get one target to get all!
				$deps = 'data-dep-target="' . $value['id'] . '-width" data-dep-id="' . $value['deps']['id'] . '" data-dep-value="' . $value['deps']['value'] . '" data-dep-type="hide"';
			}

			?>
			<tr valign="top" class="yith-plugin-fw-panel-wc-row fade-in" <?php echo $deps ?>>
				<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
				<td class="forminp yith_image_size_settings"
					<?php
					if ( isset( $value['custom_attributes'] ) ) {
						foreach ( $value['custom_attributes'] as $key => $data ) {
							echo ' ' . esc_attr( $key ) . '="' . esc_attr( $data ) . '"';
						}
					}
					?>
				>
					<input name="<?php echo esc_attr( $value['id'] ); ?>[width]"
						id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3"
						value="<?php echo esc_attr( $width ); ?>"/>
					&times;
					<input name="<?php echo esc_attr( $value['id'] ); ?>[height]"
						id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3"
						value="<?php echo esc_attr( $height ); ?>"/> px

					<label for="<?php echo esc_attr( $value['id'] ); ?>-crop">
						<input name="<?php echo esc_attr( $value['id'] ); ?>[crop]"
							id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox"
							value="1" <?php checked( 1, $crop ); ?> />
						<?php esc_html_e( 'Do you want to hard crop the image?', 'yith-woocommerce-added-to-cart-popup' ); ?>
					</label>

					<div>
						<span class="description"><?php echo wp_kses_post( $value['desc'] ); ?></span>
					</div>

				</td>
			</tr>
			<?php

		}

		/**
		 * Create draggable position type
		 *
		 * @access public
		 * @since  1.0.0
		 * @param array $value The field array.
		 *
		 * @return void
		 */
		public function admin_fields_draggable_position( $value ) {

			$position = get_option( $value['id'], $value['default'] );

			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label
						for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				</th>
				<td class="forminp"
					<?php
					if ( isset( $value['custom_attributes'] ) ) {
						foreach ( $value['custom_attributes'] as $key => $data ) {
							echo ' ' . esc_attr( $key ) . '="' . esc_attr( $data ) . '"';
						}
					}
					?>
				>
					<div id="<?php echo esc_attr( $value['id'] ); ?>_draggable_container">
						<div id="<?php echo esc_attr( $value['id'] ); ?>_draggable"></div>
					</div>
					<input name="<?php echo esc_attr( $value['id'] ); ?>[top]"
						id="<?php echo esc_attr( $value['id'] ); ?>_top" type="hidden"
						value="<?php echo esc_attr( $position['top'] ); ?>"/>
					<input name="<?php echo esc_attr( $value['id'] ); ?>[left]"
						id="<?php echo esc_attr( $value['id'] ); ?>_left" type="hidden"
						value="<?php echo esc_attr( $position['left'] ); ?>"/>
					<span class="description"><?php echo wp_kses_post( $value['desc'] ); ?></span>
				</td>
			</tr>


			<script>
				jQuery(document).ready(function ($) {
					$('#<?php echo esc_attr( $value['id'] ); ?>_draggable').draggable({
						containment: '#<?php echo esc_attr( $value['id'] ); ?>_draggable_container',
						create: function (event, ui) {
							let top = 270 * ('<?php echo esc_attr( $position['top'] ); ?>' / 100),
								left = 320 * ('<?php echo esc_attr( $position['left'] ); ?>' / 100);

							$(this).css({'top': top, 'left': left}).show();
						},
						stop: function (event, ui) {
							let top = (ui.position.top + 30) / 3,
								left = (ui.position.left + 30) / 3.5;

							$("#<?php echo esc_attr( $value['id'] ); ?>_top").val(top);
							$("#<?php echo esc_attr( $value['id'] ); ?>_left").val(left);
						}
					});
				});
			</script>
			<?php
		}

		/**
		 * Add select product ajax in plugin settings
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param mixed $value The field array.
		 */
		public function yith_wacp_select_prod( $value ) {

			$products = get_option( $value['id'], $value['default'] );

			// Build data selected array.
			$data_selected = array();
			if ( ! is_array( $products ) ) {
				$products = explode( ',', $products );
			}

			foreach ( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$data_selected[ $product_id ] = $product->get_formatted_name();
				}
			}

			$deps = '';
			if ( ! empty( $value['deps'] ) ) {
				$deps = 'data-dep-target="' . $value['id'] . '" data-dep-id="' . $value['deps']['id'] . '" data-dep-value="' . $value['deps']['value'] . '" data-dep-type="hide"';
			}

			?>
			<tr valign="top" class="yith-plugin-fw-panel-wc-row fade-in" <?php echo $deps ?>>
				<th scope="row" class="image_upload">
					<label
						for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				</th>
				<td class="forminp yith_wacp_select_prod">
					<?php
					yit_add_select2_fields(
						array(
							'style'            => 'width: 50%;',
							'class'            => 'wc-product-search',
							'id'               => $value['id'],
							'name'             => $value['id'],
							'data-placeholder' => esc_html__( 'Search product...', 'yith-woocommerce-added-to-cart-popup' ),
							'data-multiple'    => true,
							'data-action'      => 'woocommerce_json_search_products',
							'data-selected'    => $data_selected,
							'value'            => implode( ',', $products ),
						)
					);
					?>

					<span class="description"><?php echo wp_kses_post( $value['desc'] ); ?></span>
				</td>
			</tr>
			<?php
		}


		/**
		 * Sanitize option for select products
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 * @param mixed $value
		 * @param array $option
		 * @param mixed $raw_value
		 * @return mixed
		 */
		public function sanitize_option_products( $value, $option, $raw_value ) {
			return is_null( $value ) ? array() : $value;
		}

		/**
		 * Sanitize option for draggable position
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param mixed $value
		 * @param array $option
		 * @param mixed $raw_value
		 * @return mixed
		 */
		public function sanitize_option_draggable( $value, $option, $raw_value ) {

			foreach ( $value as $key => $pos ) {
				$value[ $key ] = intval( $pos );
				$value[ $key ] = $pos > 100 ? 100 : ( $pos < 0 ) ? 0 : $value[ $key ];
			}

			return $value;
		}
	}
}
/**
 * Unique access to instance of YITH_WACP_Admin_Premium class
 *
 * @since 1.0.0
 * @return YITH_WACP_Admin_Premium
 */
function YITH_WACP_Admin_Premium() { // phpcs:ignore
	return YITH_WACP_Admin_Premium::get_instance();
}
