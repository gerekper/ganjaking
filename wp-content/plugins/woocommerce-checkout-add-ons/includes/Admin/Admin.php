<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Handlers\Shop_Order;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Plugin;

defined( 'ABSPATH' ) or exit;

/**
 * Admin class
 *
 * @since 1.0
 */
class Admin {


	/** @var string page suffix ID */
	protected $page_id;

	/** @var Shop_Order instance */
	private $shop_order_handler;

	/** @var AJAX admin AJAX handler instance */
	private $ajax;

	/** @var Add_On memoization for current add-on being edited */
	protected $admin_screen_add_on;

	/** @var Add_On_Screen|Add_On_List_Screen screen handler class instance */
	protected $screen_handler;


	/**
	 * Init the class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// set admin AJAX handler
		$this->ajax = new AJAX();

		if ( is_admin() ) {

			// add checkout add-ons value column header to order items table
			add_action( 'woocommerce_admin_order_item_headers', array( $this, 'add_order_item_headers' ) );

			// add checkout add-ons value column to order items table
			add_action( 'woocommerce_admin_order_item_values', array( $this, 'add_order_item_values' ), 10, 3 );

			if ( ! is_ajax() ) {

				// load view order list table / edit order screen customizations
				$this->shop_order_handler = new Shop_Order();

				// load styles/scripts
				add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

				// load WC styles / scripts on editor screen
				add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );

				// setup admin components
				add_action( 'admin_menu', array( $this, 'add_menu_link' ) );
				add_action( 'current_screen', array( $this, 'load_add_on_screen' ) );
			}
		}

		// must be called outside of `is_admin()` or else the control is hidden
		add_action( 'customize_register', [ $this, 'add_customizer_settings' ], 15, 1 );
	}


	/**
	 * Load admin styles and scripts
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function load_styles_scripts() {

		if ( $this->is_checkout_add_on_screen() || $this->is_shop_order_screen() ) {

			$this->load_styles();
			$this->load_scripts();
		}
	}


	/**
	 * Loads admin styles.
	 *
	 * @since 2.0.0
	 */
	protected function load_styles() {

		wp_enqueue_style(
			'wc-checkout-add-ons-admin',
			wc_checkout_add_ons()->get_plugin_url() . '/assets/css/admin/wc-checkout-add-ons.min.css',
			array( 'woocommerce_admin_styles' ),
			Plugin::VERSION
		);

		if ( $this->is_checkout_add_on_screen() ) {

			wp_enqueue_style(
				'jquery-ui-style',
				'//ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jquery_ui_version() . '/themes/smoothness/jquery-ui.css'
			);
		}
	}


	/**
	 * Loads admin scripts.
	 *
	 * @since 2.0.0
	 */
	protected function load_scripts() {

		$admin_js_url     = wc_checkout_add_ons()->get_plugin_url() . '/assets/js/admin/';
		$main_script_deps = array( 'jquery', 'jquery-ui-sortable', 'woocommerce_admin' );

		if ( $this->is_new_or_edit_add_on_screen() ) {

			wp_register_script(
				'wc-checkout-add-ons-meta-box',
				$admin_js_url . 'wc-checkout-add-ons-meta-box.min.js',
				array( 'jquery' ),
				Plugin::VERSION
			);

			wp_register_script(
				'wc-checkout-add-ons-meta-box-add-on-data',
				$admin_js_url . 'wc-checkout-add-ons-meta-box-add-on-data.min.js',
				array( 'jquery', 'wc-checkout-add-ons-meta-box' ),
				Plugin::VERSION
			);

			wp_register_script(
				'wc-checkout-add-ons-meta-box-add-on-publish',
				$admin_js_url . 'wc-checkout-add-ons-meta-box-add-on-publish.min.js',
				array( 'jquery', 'wc-checkout-add-ons-meta-box' ),
				Plugin::VERSION
			);

			$main_script_deps = array_merge( $main_script_deps, array( 'wc-checkout-add-ons-meta-box-add-on-data', 'wc-checkout-add-ons-meta-box-add-on-publish' ) );
		}

		wp_enqueue_script(
			'wc-checkout-add-ons-admin',
			$admin_js_url . 'wc-checkout-add-ons.min.js',
			$main_script_deps,
			Plugin::VERSION
		);

		wp_localize_script(
			'wc-checkout-add-ons-admin',
			'wc_checkout_add_ons_params',
			$this->get_admin_script_params()
		);
	}


	/**
	 * Gets the params used to localize the main admin script.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_admin_script_params() {

		return [
			'ajax_url'                        => admin_url( 'admin-ajax.php' ),
			'list_sort_nonce'                 => wp_create_nonce( 'wc-checkout-add-ons-list-sort' ),
			'list_enable_disable_nonce'       => wp_create_nonce( 'wc-checkout-add-ons-list-enable-disable' ),
			'display_rule_other_add_on_nonce' => wp_create_nonce( 'wc-checkout-add-ons-render-other-add-on-fields' ),
			'is_new_add_on_screen'            => $this->is_new_add_on_screen(),
			'is_edit_add_on_screen'           => $this->is_edit_add_on_screen(),
			'is_shop_order_screen'            => $this->is_shop_order_screen(),
			'add_on_types_with_options'       => Add_On_Factory::get_add_on_types_with_options(),
			'add_on_supported_attributes'     => Add_On_Factory::get_add_on_supported_attributes(),
			'i18n'                            => [
				'delete_add_on_confirmation'  => __( 'Are you sure you want to delete this add-on? (Existing order data relating to this add-on will remain intact.)', 'woocommerce-checkout-add-ons' ),
				'delete_add_ons_confirmation' => __( 'Are you sure you want to delete these add-ons? (Existing order data relating to these add-ons will remain intact.)', 'woocommerce-checkout-add-ons' ),
				'disabled_options_tab_note'   => __( 'This input type does not have options', 'woocommerce-checkout-add-ons' ),
			],
		];
	}


	/**
	 * Gets the current version of jQuery UI registered on the site.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_jquery_ui_version() {
		global $wp_scripts;

		return   isset( $wp_scripts->registered['jquery-ui-core']->ver )
			   ? $wp_scripts->registered['jquery-ui-core']->ver
			   : '1.9.2';
	}


	/**
	 * Adds screen ID to the list of pages for WC to load its JS on
	 *
	 * @since 1.0
	 *
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {

		// sub-menu page screen ID
		$screen_ids[] = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc_checkout_add_ons' );

		return $screen_ids;
	}


	/**
	 * Add 'Order add-ons' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function add_menu_link() {

		$this->page_id = add_submenu_page(
			'woocommerce',
			_x( 'Checkout Add-Ons', 'page title', 'woocommerce-checkout-add-ons' ),
			_x( 'Checkout Add-Ons', 'menu title', 'woocommerce-checkout-add-ons' ),
			'manage_woocommerce',
			'wc_checkout_add_ons',
			array( $this, 'render_admin_screen' )
		);
	}


	/**
	 * Gets the page suffix ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	public function get_page_id() {

		return $this->page_id;
	}


	/**
	 * Loads the appropriate admin screen class to manage add-ons.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function load_add_on_screen() {

		if ( $this->is_checkout_add_on_screen() ) {

			if ( $this->is_new_or_edit_add_on_screen() || $this->is_delete_add_on_screen() || $this->is_duplicate_add_on_screen() ) {

				$this->screen_handler = new Add_On_Screen();

			} elseif ( 'edit' === $this->get_admin_screen_action() ) {

				// 'edit' action without a valid add-on ID
				wp_safe_redirect( $this->get_list_add_ons_screen_url() );
				exit;

			} else {

				$this->screen_handler = new Add_On_List_Screen();
			}
		}
	}


	/**
	 * Renders the admin screen.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function render_admin_screen() {

		if ( $this->screen_handler ) {

			wc_checkout_add_ons()->get_message_handler()->show_messages();

			$this->screen_handler->render();
		}
	}


	/**
	 * Sanitizes and returns the `action` URL param.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	protected function get_admin_screen_action() {

		$actions = [ 'new', 'edit', 'duplicate', 'delete' ];
		$action  = isset( $_GET['action'] ) ? strtolower( trim( $_GET['action'] ) ) : null;

		return in_array( $action, $actions, true ) ? $action : null;
	}


	/**
	 * Validates, memoizes, and returns the Checkout_Add_On specified by the
	 * `add_on` URL parameter.
	 *
	 * @since 2.0.0
	 *
	 * @return Add_On|bool false on failure
	 */
	public function get_admin_screen_add_on() {

		if ( null === $this->admin_screen_add_on ) {

			$add_on_id = isset( $_GET['add_on'] ) ? $_GET['add_on'] : null;
			$add_on    = Add_On_Factory::get_add_on( $add_on_id );
			$this->admin_screen_add_on = $add_on instanceof Add_On ? $add_on : false;
		}

		return $this->admin_screen_add_on;
	}


	/**
	 * Returns if this is a shop order screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_shop_order_screen() {

		$screen = get_current_screen();

		return isset( $screen->id ) && ( 'shop_order' === $screen->id || 'edit-shop_order' === $screen->id );
	}


	/**
	 * Returns whether we are on the checkout add-on screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_checkout_add_on_screen() {

		$screen = get_current_screen();

		return isset( $screen->id, $this->page_id ) && $screen->id === $this->page_id;
	}


	/**
	 * Returns whether we are on the new add-on screen or not.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_new_add_on_screen() {

		return $this->is_checkout_add_on_screen() && 'new' === $this->get_admin_screen_action();
	}


	/**
	 * Returns whether we are on the edit add-on screen or not.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_edit_add_on_screen() {

		return    $this->is_checkout_add_on_screen()
		       && 'edit' === $this->get_admin_screen_action()
		       && $this->get_admin_screen_add_on();
	}


	/**
	 * Returns true if the current screen is either the add or edit add-on screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_new_or_edit_add_on_screen() {

		return $this->is_new_add_on_screen() || $this->is_edit_add_on_screen();
	}


	/**
	 * Returns true if the current request is to delete an add-on.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_delete_add_on_screen() {

		return    $this->is_checkout_add_on_screen()
		       && 'delete' === $this->get_admin_screen_action()
		       && $this->get_admin_screen_add_on();
	}


	/**
	 * Returns true if the current request is to duplicate an add-on.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_duplicate_add_on_screen() {

		return    $this->is_checkout_add_on_screen()
		       && 'duplicate' === $this->get_admin_screen_action()
		       && $this->get_admin_screen_add_on();
	}


	/**
	 * Gets the list add-ons screen URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_list_add_ons_screen_url() {

		return add_query_arg( array(
			'page'   => 'wc_checkout_add_ons',
		), admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the new add-on screen URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_new_add_on_screen_url() {

		return add_query_arg( array(
			'page'   => 'wc_checkout_add_ons',
			'action' => 'new',
		), admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the edit add-on screen URL.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_id Add-on ID
	 * @return string
	 */
	public function get_edit_add_on_screen_url( $add_on_id ) {

		return add_query_arg( array(
			'page'   => 'wc_checkout_add_ons',
			'action' => 'edit',
			'add_on' => $add_on_id,
		), admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the duplicate add-on URL.
	 *
	 * @since 2.1.0
	 *
	 * @param string $add_on_id Add-on ID
	 * @return string
	 */
	public function get_duplicate_add_on_url( $add_on_id ) {

		return add_query_arg( [
			'page'     => 'wc_checkout_add_ons',
			'action'   => 'duplicate',
			'add_on'   => $add_on_id,
			'security' => wp_create_nonce( 'duplicate_checkout_add_on_' . $add_on_id ),
		], admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the delete add-on URL.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_id Add-on ID
	 * @return string
	 */
	public function get_delete_add_on_url( $add_on_id ) {

		return add_query_arg( array(
			'page'     => 'wc_checkout_add_ons',
			'action'   => 'delete',
			'add_on'   => $add_on_id,
			'security' => wp_create_nonce( 'delete_checkout_add_on_' . $add_on_id ),
		), admin_url( 'admin.php' ) );
	}


	/**
	 * Adds checkout add-ons headers to the order items table.
	 *
	 * @since 1.1.0
	 */
	public function add_order_item_headers() {
		global $post;

		echo '<th class="wc-checkout-add-ons-value">&nbsp;</th>';

		// enqueue ajax for saving add-on values
		$javascript = "
			jQuery( document.body ).on( 'items_saved', 'button.save-action', function() {
				jQuery.ajax( {
					type: 'POST',
					url: '" . admin_url( 'admin-ajax.php' ) . "',
					data: {
						action: 'wc_checkout_add_ons_save_order_items',
						security: '" . wp_create_nonce( "save-checkout-add-ons" ) . "',
						order_id: '" . ( isset( $post->ID ) ? $post->ID : '' ) . "',
						items: jQuery('table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]').serialize()
					}
				} );
			} );";

		wc_enqueue_js( $javascript );
	}


	/**
	 * Adds checkout add-ons values to the order items table.
	 *
	 * @since 1.1.0
	 *
	 * @param null $_ unused
	 * @param object $item the order item
	 * @param int $item_id the order item id
	 */
	public function add_order_item_values( $_, $item, $item_id ) {

		echo '<td class="wc-checkout-add-ons-value">';

		$add_on_id    = $item->get_meta( '_wc_checkout_add_on_id' );
		$add_on_value = wp_unslash( $item->get_meta( '_wc_checkout_add_on_value' ) );
		$add_on_label = wp_unslash( $item->get_meta( '_wc_checkout_add_on_label' ) );

		if ( $add_on_id && $add_on = Add_On_Factory::get_add_on( $add_on_id ) ) {

			$is_editable = in_array( $add_on->get_type(), array( 'text', 'textarea' ), false );

			if ( 'textarea' === $add_on->get_type() || 'file' === $add_on->get_type() ) {
				$value = $add_on->normalize_value( $add_on_value, false );
			} else {
				$value = maybe_unserialize( $add_on_label );
				$value = wp_kses_post( is_array( $value ) ? implode( ', ', $value ) : $value );
			}

			ob_start();
			?>

			<div class="view">
				<?php echo $value; ?>
			</div>

			<?php if ( $is_editable ) : ?>

				<div class="edit" style="display: none;">

					<?php if ( 'textarea' === $add_on->get_type() ) : ?>
						<textarea placeholder="<?php esc_attr_e( 'Checkout Add-on Value', 'woocommerce-checkout-add-ons' ); ?>" name="checkout_add_on_value[<?php echo esc_attr( $item_id ); ?>]"><?php echo esc_textarea( $add_on_value ); ?></textarea>
					<?php else : ?>
						<input type="text" placeholder="<?php esc_attr_e( 'Checkout Add-on Value', 'woocommerce-checkout-add-ons' ); ?>" name="checkout_add_on_value[<?php echo $item_id; ?>]" value="<?php echo $value; ?>" />
					<?php endif; ?>

					<input type="hidden" class="checkout_add_on_id" name="checkout_add_on_item_id[]" value="<?php echo $item_id; ?>" />
					<input type="hidden" class="checkout_add_on_id" name="checkout_add_on_id[<?php echo $item_id; ?>]" value="<?php echo $add_on->get_id(); ?>" />
				</div>

			<?php endif; ?>

			<?php
			echo ob_get_clean();
		}

		echo '</td>';
	}


	/**
	 * Add the custom settings.
	 *
	 * TODO: Remove this compatibility method and corresponding action once WC 3.3+ can be required. {JB 2018-11-28}
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $settings The default WooCommerce checkout settings.
	 * @return array The WooCommerce checkout settings.
	 */
	public function add_settings( $settings ) {

		$updated_settings = array();

		$new_settings = array(

			// Begin the Checkout Add-Ons section.
			array(
				'title' => __( 'Checkout Add-Ons', 'woocommerce-checkout-add-ons' ),
				'type'  => 'title',
				'id'    => 'checkout_add_on_options',
			),

			// Add the Display Position setting.
			array(
				'title'   => __( 'Display Position', 'woocommerce-checkout-add-ons' ),
				'desc'    => __( 'This controls where on the Checkout page your custom add-ons will be displayed.', 'woocommerce-checkout-add-ons' ),
				'id'      => 'wc_checkout_add_ons_position',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_checkout_after_customer_details',
				'type'    => 'select',
				'options' => $this->get_checkout_add_on_locations(),
				'desc_tip' => true,
			),

			// End the Checkout Add-Ons section.
			array(
				'type' => 'sectionend',
				'id'   => 'checkout_add_on_options',
			),
		);

		foreach ( $settings as $setting ) {

			$updated_settings[] = $setting;

			// Add our settings after the "Checkout Process" section
			if ( isset( $setting['id'] ) && 'checkout_process_options' === $setting['id'] && 'sectionend' === $setting['type'] ) {
				$updated_settings = array_merge( $updated_settings, $new_settings );
			}
		}

		return $updated_settings;
	}


	/**
	 * Adds settings to the customizer.
	 *
	 * @since 2.0.0
	 *
	 * @param $wp_customize \WP_Customize_Manager the customize manager
	 */
	public function add_customizer_settings( $wp_customize ) {

		$wp_customize->add_setting(
			'wc_checkout_add_ons_position',
			array(
				'default'           => 'woocommerce_checkout_after_customer_details',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'sanitize_checkout_add_on_location' ),
			)
		);

		$wp_customize->add_control(
			'woocommerce_checkout_add_ons_location_field',
			array(
				'label'    => __( 'Checkout Add-ons display', 'woocommerce-checkout-add-ons' ),
				'section'  => 'woocommerce_checkout',
				'priority' => 5,
				'settings' => 'wc_checkout_add_ons_position',
				'type'     => 'select',
				'choices'  => $this->get_checkout_add_on_locations()
			)
		);

		if ( null !== $wp_customize->selective_refresh ) {

			$wp_customize->selective_refresh->add_partial(
				'wc_checkout_add_ons_position', array(
					'selector'            => '#wc_checkout_add_ons',
					'container_inclusive' => true,
					'render_callback'     => array( wc_checkout_add_ons()->get_frontend_instance(), 'render_add_ons' ),
				)
			);
		}

		$wp_customize->add_setting(
			'woocommerce_checkout_add_ons_percentage_adjustment_from',
			[
				'default'           => Add_On::PERCENTAGE_ADJUSTMENT_SUBTOTAL,
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => [ $this, 'sanitize_checkout_add_on_percentage_adjustment_from' ],
			]
		);

		$wp_customize->add_control(
			'woocommerce_checkout_add_ons_percentage_adjustment_from_field',
			[
				'label'    => __( 'Calculate add-on percentage-based prices from', 'woocommerce-checkout-add-ons' ),
				'section'  => 'woocommerce_checkout',
				'priority' => 6,
				'settings' => 'woocommerce_checkout_add_ons_percentage_adjustment_from',
				'type'     => 'radio',
				'choices'  => $this->get_checkout_add_on_percentage_adjustment_from_options(),
			]
		);
	}


	/**
	 * Sanitizes the checkout add-on location setting.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value the setting value
	 * @return string the sanitized value
	 */
	public function sanitize_checkout_add_on_location( $value ) {

		$locations = $this->get_checkout_add_on_locations();

		return array_key_exists( $value, $locations ) ? $value : '';
	}


	/**
	 * Gets checkout add-on locations.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_checkout_add_on_locations() {

		return array(
			'woocommerce_checkout_before_customer_details' => __( 'Before Billing Details', 'woocommerce-checkout-add-ons' ),
			'woocommerce_checkout_billing'                 => __( 'After Billing Details', 'woocommerce-checkout-add-ons' ),
			'woocommerce_checkout_after_customer_details'  => __( 'Before Order Summary', 'woocommerce-checkout-add-ons' ),
		);
	}


	/**
	 * Sanitizes the checkout add-on percentage adjustment from setting.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @param string $value the setting value
	 * @return string the sanitized value
	 */
	public function sanitize_checkout_add_on_percentage_adjustment_from( $value ) {

		$locations = $this->get_checkout_add_on_percentage_adjustment_from_options();

		return array_key_exists( $value, $locations ) ? $value : '';
	}


	/**
	 * Gets options for percentage adjustment from field.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_checkout_add_on_percentage_adjustment_from_options() {

		return [
			Add_On::PERCENTAGE_ADJUSTMENT_SUBTOTAL => __( 'Order subtotal', 'woocommerce-checkout-add-ons' ),
			Add_On::PERCENTAGE_ADJUSTMENT_TOTAL    => __( 'Order total', 'woocommerce-checkout-add-ons' ),
		];
	}


}
