<?php
/**
 * WooCommerce Cost of Goods
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Admin Class.
 *
 * Adds general COG settings and loads the orders/product admin classes.
 *
 * @since 1.0
 */
class WC_COG_Admin {


	/** @var \WC_COG_Admin_Orders class instance */
	protected $orders;

	/** @var \WC_COG_Admin_Products class instance */
	protected $products;


	/**
	 * Admin handler.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->init_hooks();

		$this->load_classes();
	}


	/**
	 * Initialize hooks
	 *
	 * @since 2.0.0
	 */
	protected function init_hooks() {

		// add general settings
		add_filter( 'woocommerce_inventory_settings', array( $this, 'add_global_settings' ) );

		// Add a apply costs woocommerce_admin_fields() field type
		add_action( 'woocommerce_admin_field_wc_cog_apply_costs_to_previous_orders', array( $this, 'render_apply_costs_section' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// WordPress would automatically convert some HTML entities into emoji in the settings page
		add_action( 'init', array( $this, 'disable_settings_wp_emoji' ) );
	}


	/**
	 * Load Orders/Products admin classes
	 *
	 * @since 2.0.0
	 */
	protected function load_classes() {

		$this->orders   = wc_cog()->load_class( '/src/admin/class-wc-cog-admin-orders.php', 'WC_COG_Admin_Orders' );
		$this->products = wc_cog()->load_class('/src/admin/class-wc-cog-admin-products.php', 'WC_COG_Admin_Products' );
	}


	/**
	 * Return the admin orders class instance
	 *
	 * @since 2.0.0
	 * @return \WC_COG_Admin_Orders
	 */
	public function get_orders_instance() {

		return $this->orders;
	}


	/**
	 * Return the admin products class instance
	 *
	 * @since 2.0.0
	 * @return \WC_COG_Admin_Products
	 */
	public function get_products_instance() {

		return $this->products;
	}


	/**
	 * Inject global settings into the Settings > Products > Inventory page, immediately after the 'Inventory Options' section
	 *
	 * @since 1.0
	 * @param array $settings associative array of WooCommerce settings
	 * @return array associative array of WooCommerce settings
	 */
	public function add_global_settings( $settings ) {

		$updated_settings = array();

		foreach ( $settings as $setting ) {

			$updated_settings[] = array( $setting );

			// add settings after `product_inventory_options` section
			if ( isset( $setting['id'], $setting['type'] ) && 'product_inventory_options' === $setting['id'] && 'sectionend' === $setting['type'] ) {

				$updated_settings[] = self::get_global_settings();
			}
		}

		return call_user_func_array( 'array_merge', $updated_settings );
	}


	/**
	 * Returns the global settings array for the plugin
	 *
	 * @since 1.0
	 * @return array the global settings
	 */
	public static function get_global_settings() {

		return apply_filters( 'wc_cog_global_settings', array(

			// section start
			array(
				'name' => __( 'Cost of Goods Options', 'woocommerce-cost-of-goods' ),
				'type' => 'title',
				'id'   => 'wc_cog_global_settings',
			),

			// include fees
			array(
				'title'         => __( 'Exclude these item(s) from income when calculating profit. ', 'woocommerce-cost-of-goods' ),
				'desc'          => __( 'Fees charged to customer (e.g. Checkout Add-Ons, Payment Gateway Based Fees)', 'woocommerce-cost-of-goods' ),
				'id'            => 'wc_cog_profit_report_exclude_gateway_fees',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			),

			// include shipping costs
			array(
				'desc'          => __( 'Shipping charged to customer', 'woocommerce-cost-of-goods' ),
				'id'            => 'wc_cog_profit_report_exclude_shipping_costs',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			),

			// include taxes
			array(
				'desc'          => __( 'Tax charged to customer', 'woocommerce-cost-of-goods' ),
				'id'            => 'wc_cog_profit_report_exclude_taxes',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			),

			// custom section for applying costs to previous orders
			array(
				'id'          => 'wc_cog_apply_costs_to_previous_orders',
				'type'        => 'wc_cog_apply_costs_to_previous_orders',
			),

			// section end
			array( 'type' => 'sectionend', 'id' => 'wc_cog_profit_reports' ),

		) );
	}


	/**
	 * Determines whether the store has at least one order.
	 *
	 * @since 2.11.0
	 *
	 * @return bool
	 */
	protected function store_has_orders() : bool {

		$orders_count = count( wc_get_orders( [ 'return', 'ids' ] ) );

		return $orders_count && $orders_count > 0;
	}


	/**
	 * Renders the "Apply Costs to Previous Orders" setting section HTML.
	 *
	 * @internal
	 *
	 * @since 1.1
	 *
	 * @param array $field associative array of field parameters
	 */
	public function render_apply_costs_section( $field ) {

		if ( ! $this->store_has_orders() ) {
			return;
		}

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>">
					<?php esc_html_e( 'Apply Costs to Previous Orders', 'woocommerce-cost-of-goods' ); ?>
				</label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $field['type'] ) ?>">
				<?php

				/**
				 * Fires when outputting options to apply costs to previous orders.
				 *
				 * This allows third parties or integrations to insert more options before the action button.
				 *
				 * @since 2.8.0
				 *
				 * @param array $field parameters of custom WooCommerce field being output
				 */
				do_action( 'wc_cost_of_goods_apply_costs_to_previous_orders_options_html', $field );

				$job_in_progress = wc_cog()->get_previous_orders_handler_instance()->get_job();

				?>
				<fieldset>
					<input
						type="hidden"
						name="wc_cog_apply_costs_job_id"
						value="<?php echo $job_in_progress ? esc_attr( $job_in_progress->id ) : ''; ?>"
					/>
					<span class="description"><?php esc_html_e( 'Apply costs to all orders, overriding previous costs, this action is not reversible!', 'woocommerce-cost-of-goods' ); ?></span>
					<br /><br />
					<button
						id="<?php echo esc_attr( $field['id'] ); ?>"
						class="button"
						<?php disabled( (bool) $job_in_progress, true, true ); ?>><?php
						esc_html_e( 'Apply Costs', 'woocommerce-cost-of-goods' ); ?></button>
					<span class="spinner applying-costs-progress <?php echo (bool) $job_in_progress ? 'is-active' : ''; ?>" style="float: none;"></span>
					<p></p><?php // holds job progress updates ?>
					<br />
					<span class="description">
						<?php
						printf(
							/* translators: Placeholders: %1$s - <a>, %2$s - </a> */
							esc_html__( 'Need to bulk update some products but not all? You can do it with Customer/Order/Coupon Export and Customer/Order/Coupon CSV Import, which are compatible with Cost of Goods. %1$sRead documentation%2$s', 'woocommerce-cost-of-goods' ),
							'<a href="https://docs.woocommerce.com/document/cost-of-goods-sold/#section-20" target="_blank">',
							'</a>'
						);
						?>
					</span>
				</fieldset>
			</td>
		</tr>
		<?php
	}


	/**
	 * Load admin styles and scripts
	 *
	 * @since 1.8.0
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $post_type;

		$is_settings_screen = wc_cog()->is_plugin_settings();

		if ( $is_settings_screen || ( $post_type && in_array( $post_type, array( 'product', 'shop_order' ), true ) && in_array( $hook_suffix, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) ) {

			if ( $is_settings_screen && ( $background_job = wc_cog()->get_previous_orders_handler_instance()->get_job() ) ) {
				$background_job_id = $background_job->id;
			} else {
				$background_job_id = false;
			}

			$dependencies = 'products' === $post_type ? array( 'jquery', 'wc-admin-product-meta-boxes', 'woocommerce_admin' ) : array( 'jquery', 'woocommerce_admin' );

			wp_enqueue_script( 'wc-cog-admin', wc_cog()->get_plugin_url() . '/assets/js/admin/wc-cog-admin.min.js', $dependencies, \WC_COG::VERSION );

			wp_localize_script( 'wc-cog-admin', 'wc_cog_admin', array(

				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'woocommerce_currency_symbol'             => get_woocommerce_currency_symbol(),
				'existing_background_job_id'              => $background_job_id,
				'get_cost_of_goods_nonce'                 => wp_create_nonce( 'get-cost-of-goods' ),
				'apply_cost_of_goods_nonce'               => wp_create_nonce( 'apply-cost-of-goods' ),
				'get_applying_cost_of_goods_status_nonce' => wp_create_nonce( 'get-applying-cost-of-goods-status' ),

				'i18n' => array(
					'apply_costs_confirm_message_all' => __( 'Are you sure you want to apply costs to ALL previous orders, overriding those with existing costs? This cannot be reversed! Note that this can take some time in shops with a large number of orders.', 'woocommerce-cost-of-goods' ),
					'apply_costs_error'               => __( 'Oops! Something went wrong. Please try again.', 'woocommerce-cost-of-goods' ),
					'apply_costs_success'             => __( 'Cost of goods applied to previous orders.', 'woocommerce-cost-of-goods' ),
					'apply_costs_in_progress'         => __( 'Applying costs of goods to previous orders...', 'woocommerce-cost-of-goods' ),
					'apply_costs_notice'             => __( 'Process is running in the background, you can safely navigate away from this page without disrupting the process.', 'woocommerce-cost-of-goods' ),
				),
			) );

			wp_enqueue_style( 'wc-cog-admin', wc_cog()->get_plugin_url() . '/assets/css/admin/wc-cog-admin.min.css', array(), \WC_COG::VERSION );
		}

		if ( wc_cog()->is_reports_page() ) {

			// hide WooCommerce Analytics error notice via JS
			wc_enqueue_js( "
				jQuery( function( $ ) {
					$( 'a[href$=\"page=wc-admin&path=/analytics/overview\"]' ).closest( 'div.error' ).remove();
				} );
			" );

			// fallback with CSS
			echo '<style>body.woocommerce_page_wc-reports .woocommerce #message.error.inline { display: none; }</style>';
		}
	}


	/**
	 * Prevents the conversion of some HTML entities used in the plugin settings page into emojis.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 */
	public function disable_settings_wp_emoji() {

		if ( wc_cog()->is_plugin_settings() ) {

			remove_action( 'admin_print_styles',  'print_emoji_styles' );
			remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		}
	}


}
