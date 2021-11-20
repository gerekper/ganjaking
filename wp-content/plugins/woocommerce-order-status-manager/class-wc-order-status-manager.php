<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * # WooCommerce Order Status Manager Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin allows adding custom order statuses to WooCommerce
 *
 * @since 1.10.0
 */
class WC_Order_Status_Manager extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.13.3';

	/** @var \WC_Order_Status_Manager single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'order_status_manager';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_order_status_manager_';

	/** plugin deactivation modal option name */
	const PLUGIN_DEACTIVATION_MODAL_OPTION = self::PLUGIN_PREFIX . 'confirm_deactivation_modal_disabled';

	/** @var \WC_Order_Status_Manager_Admin instance */
	protected $admin;

	/** @var \WC_Order_Status_Manager_Frontend instance */
	protected $frontend;

	/** @var \WC_Order_Status_Manager_AJAX instance */
	protected $ajax;

	/** @var \WC_Order_Status_Manager_Order_Statuses instance */
	protected $order_statuses;

	/** @var \WC_Order_Status_Manager_Emails instance */
	protected $emails;

	/** @var \WC_Order_Status_Manager_Icons instance */
	protected $icons;

	/** @var \WC_Order_Status_Manager_Integrations instance */
	protected $integrations;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-order-status-manager',
			)
		);

		// functions required before we hook into init
		require_once( $this->get_plugin_path() . '/src/wc-order-status-manager-functions.php' );

		add_action( 'init', array( $this, 'init' ) );

		// make sure email template files are searched for in our plugin
		add_filter( 'woocommerce_locate_template',      array( $this, 'locate_template' ), 20, 3 );
		add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_template' ), 20, 3 );

		// permit download for order custom statuses marked as paid:
		// we must keep this filter before init because WC_Download_Handler
		// instantiates early
		add_filter( 'woocommerce_order_is_download_permitted', array( $this, 'is_download_permitted' ), 10, 2 );

		// rename core order status labels with custom ones
		// this needs to be in main class to hook early before init
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( $this, 'rename_core_order_status_labels' ), 20 );
		add_filter( 'wc_order_statuses',                             array( $this, 'rename_core_order_status_labels' ), 20 );

		// adds a confirmation modal to plugin deactivation
		add_action( 'admin_footer', [ $this, 'add_plugin_deactivation_popup' ] );
	}


	/**
	 * Initializes plugin resources that need to be available after plugins_loaded and before init.
	 *
	 * @internal
	 *
	 * @since 1.11.1
	 */
	public function init_plugin() {

		$this->emails = $this->load_class( '/src/class-wc-order-status-manager-emails.php', 'WC_Order_Status_Manager_Emails' );
	}


	/**
	 * Include Order Status Manager required files
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		if ( null === $this->order_statuses ) {
			$this->order_statuses = $this->load_class( '/src/class-wc-order-status-manager-order-statuses.php', 'WC_Order_Status_Manager_Order_Statuses' );
		}

		require_once( $this->get_plugin_path() . '/src/class-wc-order-status-manager-post-types.php' );
		\WC_Order_Status_Manager_Post_Types::initialize();

		$this->icons = $this->load_class( '/src/class-wc-order-status-manager-icons.php', 'WC_Order_Status_Manager_Icons' );

		$this->integrations = $this->load_class( '/src/integrations/class-wc-order-status-manager-integrations.php', 'WC_Order_Status_Manager_Integrations' );

		// load Frontend
		if ( ! is_admin() || is_ajax() ) {
			$this->frontend = $this->load_class( '/src/class-wc-order-status-manager-frontend.php', 'WC_Order_Status_Manager_Frontend' );
		}

		// load Admin
		if ( is_admin() && ! is_ajax() ) {
			$this->admin_includes();
		}

		// load Ajax
		if ( is_ajax() ) {
			$this->ajax_includes();
		}
	}


	/**
	 * Include required admin files
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		$this->admin = $this->load_class( '/src/admin/class-wc-order-status-manager-admin.php', 'WC_Order_Status_Manager_Admin' );
	}


	/**
	 * Include required AJAX files
	 *
	 * @since 1.0.0
	 */
	private function ajax_includes() {

		$this->ajax = $this->load_class( '/src/class-wc-order-status-manager-ajax.php', 'WC_Order_Status_Manager_AJAX' );
	}


	/**
	 * Initialize translation and post types
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// include required files
		$this->includes();
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.10.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Order_Status_Manager\Lifecycle( $this );
	}


	/**
	 * Locates the WooCommerce template files from our templates directory
	 *
	 * @since 1.0.0
	 * @param string $template Already found template
	 * @param string $template_name Searchable template name
	 * @param string $template_path Template path
	 * @return string Search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// Only keep looking if no custom theme template was found or if
		// a default WooCommerce template was found.
		if ( ! $template || Framework\SV_WC_Helper::str_starts_with( $template, WC()->plugin_path() ) ) {

			// Set the path to our templates directory
			$plugin_path = $this->get_plugin_path() . '/templates/';

			// If a template is found, make it so
			if ( is_readable( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		}

		return $template;
	}


	/**
	 * Rename custom order statuses with custom labels
	 *
	 * We run this filter callback for both
	 * 'woocommerce_register_shop_order_post_statuses'
	 * and 'wc_order_statuses'
	 *
	 * This callback needs to run before init as it hooks
	 * into WooCommerce post status registration
	 *
	 * @since 1.5.0
	 * @param array $order_statuses Associative array of order statuses
	 * @return array
	 */
	public function rename_core_order_status_labels( $order_statuses ) {

		// get custom statuses
		$custom_order_statuses = wc_order_status_manager_get_order_status_posts( array(
			'suppress_filters' => false,
		) );

		if ( ! empty( $custom_order_statuses ) ) {

			foreach ( $custom_order_statuses as $custom_order_status_post ) {

				if ( ! empty( $custom_order_status_post->post_name ) && isset( $order_statuses[ 'wc-' . $custom_order_status_post->post_name ] ) ) {

					$slug  = 'wc-' . $custom_order_status_post->post_name;
					$label = $custom_order_status_post->post_title;

					if ( ! isset( $order_statuses[ $slug ] ) ) {
						continue;
					}

					if ( 'woocommerce_register_shop_order_post_statuses' === current_filter() ) {

						if ( is_array( $order_statuses[ $slug ] ) && isset( $order_statuses[ $slug ]['label'], $order_statuses[ $slug ]['label_count'] ) ) {

							// do not rename if a custom label is is identical
							if ( $label === $order_statuses[ $slug ]['label'] ) {
								continue;
							}

							$count = is_rtl() ? '<span class="count">(%s)</span> ' . $label : $label . ' <span class="count">(%s)</span>';

							$order_statuses[ $slug ]['label']       = $custom_order_status_post->post_title;
							$order_statuses[ $slug ]['label_count'] = _n_noop( $count, $count );
						}

					} elseif ( 'wc_order_statuses' === current_filter() ) {

						if ( $label !== $order_statuses[ $slug ] && is_string( $order_statuses[ $slug ] ) ) {
							$order_statuses[ $slug ] = $label;
						}
					}
				}
			}
		}

		return $order_statuses;
	}


	/**
	 * Permit downloads if a custom order status is marked as paid
	 *
	 * @see \WC_Download_Handler::check_order_is_valid()
	 *
	 * @since 1.3.0
	 * @param bool $maybe_permitted
	 * @param \WC_Order $order
	 * @return bool
	 */
	public function is_download_permitted( $maybe_permitted, $order ) {

		// callback runs early so we need to manually include necessary classes
		require_once( $this->get_plugin_path() . '/src/class-wc-order-status-manager-order-status.php' );

		if ( null === $this->order_statuses ) {
			$this->order_statuses = $this->load_class( '/src/class-wc-order-status-manager-order-statuses.php', 'WC_Order_Status_Manager_Order_Statuses' );
		}

		$order_status = new \WC_Order_Status_Manager_Order_Status( $order->get_status() );

		if ( $order_status->get_id() > 0 ) {
			return $maybe_permitted || ( ! $order_status->is_core_status() && $order_status->is_paid() && 'yes' === get_option( 'woocommerce_downloads_grant_access_after_payment' ) );
		}

		return $maybe_permitted;
	}


	/** Getter methods ******************************************************/


	/**
	 * Get the Admin instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Get the Ajax instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Get the Frontend instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_Frontend
	 */
	public function get_frontend_instance() {
		return $this->frontend;
	}


	/**
	 * Get the Order Statuses instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_Order_Statuses
	 */
	public function get_order_statuses_instance() {
		return $this->order_statuses;
	}


	/**
	 * Get the Emails instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_Emails
	 */
	public function get_emails_instance() {
		return $this->emails;
	}


	/**
	 * Get the Icons instance
	 *
	 * @since 1.5.0
	 * @return \WC_Order_Status_Manager_Icons
	 */
	public function get_icons_instance() {
		return $this->icons;
	}

	/**
	 * Get the integrations handler instance
	 *
	 * @since 1.13.3
	 *
	 * @return \WC_Order_Status_Manager_Integrations
	 */
	public function get_integrations_instance() {
		return $this->integrations;
	}


	/** Admin methods ******************************************************/


	/**
	 * Render a notice for the user to read the docs before using the plugin
	 *
	 * @since 1.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$this->get_admin_notice_handler()->add_admin_notice(
			sprintf(
				/* translators: 1$s - opening <a> link tag, 2$s - closing </a> link tag */
				__( 'Thanks for installing Order Status Manager! Before you get started, please take a moment to %1$sread through the documentation%2$s.', 'woocommerce-order-status-manager' ),
				'<a href="' . $this->get_documentation_url() . '">', '</a>'
			),
			'read-the-docs',
			[
				'always_show_on_settings' => false,
				'notice_class'            => 'updated',
			]
		);

		if ( 'yes' === get_option( 'wc_order_status_manager_show_paid_pending_status_notice' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholder: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag */
					__( '%1$sHeads up!%2$s Order Status Manager now requires the "Pending Payment" status to only refer to orders that are awaiting payment, to avoid payment processing issues for your orders. We have automatically made this change in the %3$sPending Payment status settings%4$s.', 'woocommerce-order-status-manager' ),
					'<strong>', '</strong>',
					'<a href="' . esc_url( $this->get_settings_url() ) . '">', '</a>'
				),
				'pending-status-set-to-paid',
				[
					'always_show_on_settings' => false,
					'dismissible'             => true,
					'notice_class'            => 'notice-warning',
				]
			);
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Order Status Manager Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.1.0
	 * @see wc_order_status_manager()
	 * @return \WC_Order_Status_Manager
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Order Status Manager', 'woocommerce-order-status-manager' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.0.0
	 *
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {
		return admin_url( 'edit.php?post_type=wc_order_status' );
	}


	/**
	 * Gets the plugin documentation URL
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {
		return 'https://docs.woocommerce.com/document/woocommerce-order-status-manager/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns true if on the Order Status Manager settings page
	 *
	 * @since 1.0.0
	 *
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {
		return isset( $_GET['post_type'] ) && 'wc_order_status' === $_GET['post_type'];
	}


	/**
	 * Check if an object, id or a slug matches that of an Order Status post type
	 *
	 * Will return the order status object if true
	 *
	 * @since 1.3.0
	 * @param int|\WP_Post|string $status Post ID, post object or post slug
	 * @return false|\WC_Order_Status_Manager_Order_Status
	 */
	public function is_order_status_cpt( $status ) {

		if ( is_numeric( $status ) ) {
			$order_status_cpt = get_post( $status );
		} elseif ( is_object( $status ) ) {
			$order_status_cpt = $status;
		} else {
			$order_status_cpt = get_page_by_path( $status, OBJECT, 'wc_order_status' );
		}

		if ( $order_status_cpt && isset( $order_status_cpt->post_type ) && 'wc_order_status' === $order_status_cpt->post_type ) {
			return new \WC_Order_Status_Manager_Order_Status( $order_status_cpt );
		}

		return false;
	}


	/**
	 * Adds a popup to confirm the deactivation of the plugin.
	 *
	 * @internal
	 *
	 * @since 1.12.1-dev.1
	 */
	public function add_plugin_deactivation_popup() {
		global $pagenow;

		if ( 'plugins.php' === $pagenow && ! wc_string_to_bool( get_user_meta( get_current_user_id(), self::PLUGIN_DEACTIVATION_MODAL_OPTION, true ) ) && $this->get_order_statuses_instance()->is_any_custom_status_in_use() ) : ?>

			<div id="order-status-plugin-deactivation-popup" style="display: none;">
				<h3><?php esc_html_e( 'Heads up!', 'woocommerce-order-status-manager' ); ?></h3>

				<p>
					<?php esc_html_e( 'When you deactivate Order Status Manager, all orders in a custom status will be hidden. If this deactivation is not temporary, please first:', 'woocommerce-order-status-manager' ); ?>
				</p>

				<ul>
					<li>
						<?php
						/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
						echo sprintf( __( '%1$sReassign orders%2$s with a custom status to a WooCommerce core status.', 'woocommerce-order-status-manager' ), '<a href="/wp-admin/edit.php?post_type=shop_order">', '</a>' );
						?>
					</li>
					<li>
						<?php
						/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
						echo sprintf( __( '%1$sDelete custom statuses%2$s and select a replacement status for orders.', 'woocommerce-order-status-manager' ), '<a href="/wp-admin/edit.php?post_type=wc_order_status">', '</a>' );
						?>
					</li>
				</ul>

				<p>
					<input
						id="order-status-plugin-deactivation-popup-dont-show-me-again"
						type="checkbox" />

					<em>
						<label for="order-status-plugin-deactivation-popup-dont-show-me-again"><?php esc_html_e( 'Don\'t show me this again', 'woocommerce-order-status-manager' ) ?></label>
					</em>
				</p>

				<button class="button cancel"><?php esc_html_e( 'Cancel', 'woocommerce-order-status-manager' ); ?></button>
				<button class="button button-primary deactivate"><?php esc_html_e( 'Deactivate plugin', 'woocommerce-order-status-manager' ); ?></button>
			</div>

			<a href="#order-status-plugin-deactivation-popup" id="order-status-plugin-deactivation">&nbsp;</a><?php

		endif;
	}


}


/**
 * Returns the One True Instance of Order Status Manager.
 *
 * @since 1.10.0
 *
 * @return \WC_Order_Status_Manager
 */
function wc_order_status_manager() {

	return WC_Order_Status_Manager::instance();
}
