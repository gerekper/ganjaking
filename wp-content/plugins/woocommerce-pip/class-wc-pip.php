<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Print Invoices/Packing Lists main plugin class.
 *
 * @since 3.0.0
 */
class WC_PIP extends Framework\SV_WC_Plugin {


	/** string version number */
	const VERSION = '3.8.6';

	/** @var WC_PIP single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'pip';

	/** @var \WC_PIP_Document instance */
	protected $document;

	/** @var \WC_PIP_Handler instance */
	protected $handler;

	/** @var \WC_PIP_Print instance */
	protected $print;

	/** @var \WC_PIP_Emails instance */
	protected $emails;

	/** @var \WC_PIP_Ajax instance */
	protected $ajax;

	/** @var \WC_PIP_Admin instance */
	protected $admin;

	/** @var \WC_PIP_Customizer instance */
	protected $customizer;

	/** @var \WC_PIP_Orders_Admin instance */
	protected $orders_admin;

	/** @var \WC_PIP_Settings instance */
	protected $settings;

	/** @var \WC_PIP_Frontend instance */
	protected $frontend;

	/** @var \WC_PIP_Integrations instance */
	protected $integrations;


	/**
	 * Sets up the plugin main class.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-pip',
				'dependencies' => array(
					'php_extensions' => array(
						'dom',
					),
				),
			)
		);

		$this->add_milestone_hooks();
	}


	/**
	 * Loads and initializes the plugin lifecycle handler.
	 *
	 * @since 3.6.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-pip-lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\PIP\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 3.6.0
	 */
	public function init_plugin() {

		$this->includes();
	}


	/**
	 * Loads any required files.
	 *
	 * @since 3.0.0
	 */
	public function includes() {

		// template functions
		require_once( $this->get_plugin_path() . '/includes/wc-pip-template-functions.php' );

		// abstract class
		require_once( $this->get_plugin_path() . '/includes/abstract-wc-pip-document.php' );

		// invoices
		require_once( $this->get_plugin_path() . '/includes/class-wc-pip-document-invoice.php' );

		// packing lists
		require_once( $this->get_plugin_path() . '/includes/class-wc-pip-document-packing-list.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-pip-document-pick-list.php' );

		// handler
		$this->handler = $this->load_class( '/includes/class-wc-pip-handler.php', 'WC_PIP_Handler' );

		// print documents
		$this->print = $this->load_class( '/includes/class-wc-pip-print.php', 'WC_PIP_Print' );

		// document emails
		$this->emails = $this->load_class( '/includes/class-wc-pip-emails.php', 'WC_PIP_Emails' );

		if ( is_admin() ) {
			// admin side
			$this->admin_includes();
		} else {
			// frontend side
			$this->frontend = $this->load_class( '/includes/frontend/class-wc-pip-frontend.php', 'WC_PIP_Frontend' );
		}

		// ajax
		if ( is_ajax() ) {
			$this->ajax = $this->load_class( '/includes/class-wc-pip-ajax.php', 'WC_PIP_Ajax' );
		}

		// template customizer
		$this->customizer = $this->load_class( '/includes/admin/class-wc-pip-customizer.php', 'WC_PIP_Customizer' );

		// integrations
		$this->integrations = $this->load_class( '/includes/integrations/class-wc-pip-integrations.php', 'WC_PIP_Integrations' );
	}


	/**
	 * Loads required admin files
	 *
	 * @since 3.0.0
	 */
	private function admin_includes() {

		// load admin classes
		$this->admin        = $this->load_class( '/includes/admin/class-wc-pip-admin.php', 'WC_PIP_Admin' );
		$this->orders_admin = $this->load_class( '/includes/admin/class-wc-pip-orders-admin.php', 'WC_PIP_Orders_Admin' );
	}


	/**
	 * Gets deprecated hooks for handling them.
	 *
	 * @since 3.6.2
	 *
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		return array(
			// TODO remove by January 2020 or by version 4.0.0, whichever comes first {FN 2018-12-11]
			'wc_pip_sort_order_items' => array(
				'version'     => '3.3.5',
				'replacement' => 'wc_pip_sort_order_item_rows',
				'removed'     => true,
				'map'         => false,
			),
			// TODO remove by December 2019 or by version 4.0.0, whichever comes first {FN 2018-12-11]
			'wc_pip_pick_list_document_table_row_cells' => array(
				'version'     => '3.6.2',
				'replacement' => 'wc_pip_pick_list_grouped_by_category_table_rows',
				'removed'     => true,
				'map'         => true,
			),
		);
	}


	/** Admin methods ******************************************************/


	/**
	 * Adds milestone hooks.
	 *
	 * @since 3.6.0
	 */
	protected function add_milestone_hooks() {

		add_action( 'wc_pip_print', function( $document_type ) {

			$milestone_message = null;

			if ( 'invoice' === $document_type ) {
				$milestone_message = __( 'You have generated your first invoice!', 'woocommerce-pip' );
			} elseif ( 'packing-list' === $document_type ) {
				$milestone_message = __( 'You have generated your first packing list!', 'woocommerce-pip' );
			} elseif ( 'pick-list' ) {
				$milestone_message = __( 'You have generated your first pick list!', 'woocommerce-pip' );
			}

			if ( null !== $milestone_message ) {

				wc_pip()->get_lifecycle_handler()->trigger_milestone(
					"{$document_type}-generated", lcfirst( $milestone_message )
				);
			}
		} );
	}


	/**
	 * Renders admin notices (such as upgrade notices).
	 *
	 * @since 3.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$screen = get_current_screen();

		// only render on plugins or settings screen
		if ( $screen && ( 'plugins' === $screen->id || $this->is_plugin_settings() ) ) {

			if ( 'yes' === get_option( 'woocommerce_pip_upgraded_to_3_0_0' ) ) {

				// display a notice for installations that are upgrading
				$message_id  = 'wc_pip_upgrade_install';

				/* translators: Placeholders: %1$s - this plugin name, %2$s - opening HTML <a> anchor tag, %3$s - closing HTML </a> tag */
				$message_content = sprintf( __( 'Hi there! It looks like you have upgraded %1$s from an older version. We have added lots of new features, please %2$scheck out the documentation%3$s for an overview and some helpful upgrading tips!', 'woocommerce-pip' ), $this->get_plugin_name(), '<a target="_blank" href="https://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/#upgrade">', '</a>' );

			} else {

				// display a notice for fresh installs
				$message_id = 'wc_pip_fresh_install';

				/* translators: Placeholders: %1$s - the plugin name, %2$s - opening HTML <a> anchor tag, %3$s closing HTML </a> tag */
				$message_content = sprintf( __( 'Thanks for installing %1$s! To get started, take a minute to %2$sread the documentation%3$s :)', 'woocommerce-pip' ), $this->get_plugin_name(), '<a href="' . $this->get_documentation_url()  . '" target="_blank">', '</a>' );
			}

			// add notice
			$this->get_admin_notice_handler()->add_admin_notice( $message_content, $message_id, array(
				'always_show_on_settings' => false,
				'notice_class'            => 'updated',
			) );
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the plugin main instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_pip()
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/** Getter methods ******************************************************/


	/**
	 * Return the main admin handler class instance
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the frontend handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the AJAX handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Ajax
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Return the customizer handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Customizer
	 */
	public function get_customizer_instance() {

		return $this->customizer;
	}


	/**
	 * Returns the orders handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Orders_Admin
	 */
	public function get_orders_instance() {

		return $this->orders_admin;
	}


	/**
	 * Returns the settings handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Settings
	 */
	public function get_settings_instance() {

		if ( ! $this->settings instanceof \WC_PIP_Settings ) {

			// Include settings so we can install defaults
			require_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );

			$this->settings = $this->load_class( '/includes/admin/class-wc-pip-settings.php', 'WC_PIP_Settings' );
		}

		return $this->settings;
	}


	/**
	 * Returns the integrations handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Returns the email handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Emails
	 */
	public function get_email_instance() {

		return $this->emails;
	}


	/**
	 * Return the print handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Print
	 */
	public function get_print_instance() {

		return $this->print;
	}


	/**
	 * Returns the documents handler class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_PIP_Handler
	 */
	public function get_handler_instance() {

		return $this->handler;
	}


	/**
	 * Returns the available document types.
	 *
	 * @since 3.0.0
	 *
	 * @return array associative array of PIP document types with their names
	 */
	public function get_document_types() {

		/**
		 * Filters the document types.
		 *
		 * @since 3.0.0
		 *
		 * @param array $types the document types
		 */
		return (array) apply_filters( 'wc_pip_document_types', array(
			'invoice'      => __( 'Invoice', 'woocommerce-pip' ),
			'packing-list' => __( 'Packing List', 'woocommerce-pip' ),
			'pick-list'    => __( 'Pick List', 'woocommerce-pip' ),
		) );
	}


	/**
	 * Returns a PIP document object.
	 *
	 * @see \WC_PIP_Document::__construct()
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Document type, such as 'invoice' or 'packing-list'
	 * @param array $args Array of arguments passed to make a WC_PIP_Document object
	 * @return \WC_PIP_Document|\WC_PIP_Document_Invoice|\WC_PIP_Document_Packing_List|\WC_PIP_Document_Pick_List|null
	 */
	public function get_document( $type, array $args = array() ) {

		if ( $this->document instanceof \WC_PIP_Document ) {

			// ensure if there's a request for a document
			// which is already instantiated by comparing the order id
			// and the document type
			if ( $type === $this->document->type && $this->get_document_args_order_id( $args ) === $this->document->order_id ) {

				return $this->document;
			}
		}

		$class = 'WC_PIP_Document_' . implode( '_', array_map( 'ucfirst', explode( '-', strtolower( $type ) ) ) );

		return $this->document = class_exists( $class ) ? new $class( $args ) : null;
	}


	/**
	 * Gets an order id from document args.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args
	 * @return int
	 */
	private function get_document_args_order_id( $args ) {

		$order_id = 0;

		if ( isset( $args['order'] ) && $args['order'] instanceof \WC_Order ) {
			$order_id = $args['order']->get_id();
		} elseif ( isset( $args['order_id'] ) ) {
			$order_id = $args['order_id'];
		} elseif ( isset( $args['order_ids'] ) && is_array( $args['order_ids'] ) ) {
			$order_id = isset( $args['order_ids'][0] ) ? $args['order_ids'][0] : $order_id;
		}

		return (int) $order_id;
	}


	/**
	 * Returns one PIP plugin template.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template
	 * @param array $args
	 */
	public function get_template( $template, array $args = array() ) {

		// load the template
		wc_get_template( 'pip/' . $template . '.php', $args, '', $this->get_plugin_path() . '/templates/' );
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 3.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Print Invoices/Packing Lists', 'woocommerce-pip' );
	}


	/**
	 * Returns the plugin main file.
	 *
	 * @since 3.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the plugin configuration URL.
	 *
	 * @see \SV_WC_Plugin::get_settings_url()
	 *
	 * @since 3.0.0
	 *
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=pip' );
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 3.6.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/print-invoices-packing-lists/';
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @see \SV_WC_Plugin::get_documentation_url()
	 *
	 * @since 3.0.0
	 *
	 * @return string plugin documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @see \SV_WC_Plugin::get_support_url()
	 *
	 * @since 3.0.0
	 *
	 * @return string plugin support URL
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Checks if we are on the plugin's settings page.
	 *
	 * @see \SV_WC_Plugin::is_plugin_settings()
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'] ) && 'wc-settings' === $_GET['page'] && 'pip' === $_GET['tab'];
	}


}


/**
 * Returns the One True Instance of Print Invoices/Packing Lists.
 *
 * @since 3.0.0
 *
 * @return \WC_PIP
 */
function wc_pip() {

	return \WC_PIP::instance();
}
