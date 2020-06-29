<?php
/**
 * WooCommerce Customer/Order XML Export Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Admin Class
 *
 * Loads admin settings page and adds related hooks / filters
 *
 * @since 1.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Admin {


	/** @var string sub-menu page hook suffix */
	public $page;

	/** @var array tab IDs / titles */
	public $tabs;

	/** @var \SV_WP_Admin_Message_Handler instance */
	public $message_handler;

	/** @var \WC_Customer_Order_XML_Export_Suite_Admin_Settings instance */
	private $settings;

	/** @var string settings page name */
	protected $settings_page_name;

	/** @var \WC_Customer_Order_XML_Export_Suite_Admin_Custom_Format_Builder instance */
	private $custom_format_builder;


	/**
	 * Setup admin class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** General Admin Hooks */

		// adds the export capabilities
		add_action( 'init', array( $this, 'add_user_capabilities' ), 5 );

		// load custom admin styles / scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ), 11 );

		// load WC styles / scripts
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_styles_scripts' ) );

		add_action( 'current_screen', array( $this, 'process_export_bulk_actions' ) );

		// add 'XML Export' link under WooCommerce menu
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// render any admin notices
		add_action( 'admin_notices', array( $this, 'add_admin_notices' ), 10 );

		/** Order Hooks */

		// add 'Export Status' orders and customers page column header
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_status_column_header' ), 20 );
		add_filter( 'manage_users_columns',           array( $this, 'add_user_status_column_header' ), 20 );

		// add 'Export Status' orders and users page column content
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_status_column_content' ) );
		add_filter( 'manage_users_custom_column',            array( $this, 'add_user_export_status_column_content' ), 10, 3 );

		// add 'Export to XML' action on orders page
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_order_action' ), 10, 2 );

		// add 'Export to XML' order meta box order action
		add_action( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_actions' ) );

		// add bulk order filter for exported / non-exported orders
		add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_export_status') , 20 );
		add_filter( 'request',               array( $this, 'filter_orders_by_export_status_query' ) );

		/** Bulk Actions */
		if ( version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
			add_filter( 'bulk_actions-edit-shop_order',        array( $this, 'add_order_bulk_actions' ) );
			add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'process_order_bulk_actions' ), 10, 3 );
			add_filter( 'bulk_actions-users',        array( $this, 'add_user_bulk_actions' ) );
			add_filter( 'handle_bulk_actions-users', array( $this, 'process_user_bulk_actions' ), 10, 3 );
		} else {
			add_action( 'admin_footer-edit.php',  array( $this, 'add_bulk_actions_legacy' ) );
			add_action( 'admin_footer-users.php', array( $this, 'add_bulk_actions_legacy' ) );
			add_action( 'load-edit.php',          array( $this, 'process_bulk_actions_legacy' ) );
			add_action( 'load-users.php',         array( $this, 'process_bulk_actions_legacy' ) );
		}

		/** System Status Report */
		add_action( 'woocommerce_system_status_report', array( $this, 'add_system_status_report' ) );

		// add export modal to export-related admin screens
		add_action( 'admin_footer', array( $this, 'add_export_modals' ) );

		if ( isset( $_GET['export_id'] ) ) {

			if ( isset( $_GET['delete_xml_export'] ) ) {
				add_action( 'init', array( $this, 'delete_export' ) );
			}

			if ( isset( $_GET['transfer_xml_export'] ) ) {
				add_action( 'init', array( $this, 'transfer_export' ) );
			}
		}

		// render ajax-based wc-product-search field
		add_action( 'woocommerce_admin_field_xml_product_search', array( $this, 'render_product_search_field' ) );

		add_action( 'init', array( $this, 'set_settings_page_name' ) );

		// update XML exports folder protection when file download method is changed
		add_action( 'woocommerce_settings_saved', array( $this, 'check_exports_folder_protection' ) );
	}


	/**
	 * Adds export management capabilities to admins and shop managers.
	 *
	 * @since 2.3.0
	 */
	public function add_user_capabilities() {
		global $wp_roles;

		if ( ! isset( $wp_roles ) && class_exists( 'WP_Roles' ) ) {
			$wp_roles = new WP_Roles();
		}

		// it's fine if this gets executed more than once
		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_xml_exports' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_xml_exports' );
		}
	}


	/**
	 * Sets the settings page name in case "WooCommerce" is translated
	 * The constructor is too early to set this value
	 *
	 * @since 2.0.4
	 */
	public function set_settings_page_name() {
		$this->settings_page_name = SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc_customer_order_xml_export_suite' );
	}


	/**
	 * Load admin styles & scripts only on needed pages
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $wp_scripts;

		// only load if on an export screen
		if ( $this->is_export_screen() ) {

			// Admin CSS
			wp_enqueue_style( 'wc-customer-order-xml-export-suite_admin', wc_customer_order_xml_export_suite()->get_plugin_url() . '/assets/css/admin/wc-customer-order-xml-export-suite-admin.min.css', array( 'dashicons' ), WC_Customer_Order_XML_Export_Suite::VERSION );

			$modal_handle = 'wc-backbone-modal';

			// settings/export page
			if ( $this->page === $hook_suffix ) {

				// jQuery Timepicker JS
				wp_enqueue_script( 'wc-customer-order-xml-export-suite-jquery-timepicker', wc_customer_order_xml_export_suite()->get_plugin_url() . '/assets/js/jquery-timepicker/jquery.timepicker.min.js', array(), WC_Customer_Order_XML_Export_Suite::VERSION, true );

				// datepicker
				wp_enqueue_script( 'jquery-ui-datepicker' );

				// sortable
				wp_enqueue_script( 'jquery-ui-sortable' );

				// wc backbone modal
				// note - for some wicked reason, we have to explicitly declare backbone
				// as a dependency here, or backbone will be loaded after the modal script,
				// even though it's declared when the script was first registered ¯\_(ツ)_/¯
				wp_enqueue_script( $modal_handle, null, array( 'backbone' ) );

				// get jQuery UI version
				$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

				// enqueue UI CSS
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			}

			// admin JS
			wp_enqueue_script( 'wc-customer-order-xml-export-suite-admin', wc_customer_order_xml_export_suite()->get_plugin_url() . '/assets/js/admin/wc-customer-order-xml-export-suite-admin.min.js', array( 'wp-util', $modal_handle ), WC_Customer_Order_XML_Export_Suite::VERSION, true );

			// calendar icon
			wp_localize_script( 'wc-customer-order-xml-export-suite-admin', 'wc_customer_order_xml_export_suite_admin', array(
				'i18n' => array(
					'export_started'           => __( 'Export Started', 'woocommerce-customer-order-xml-export-suite' ),
					'export_completed'         => __( 'Export Completed', 'woocommerce-customer-order-xml-export-suite' ),
					'export_failed'            => __( 'Export Failed', 'woocommerce-customer-order-xml-export-suite' ),
					'export_resumed'           => __( 'Export Resumed', 'woocommerce-customer-order-xml-export-suite' ),
					'export_transfer_failed'   => __( 'Export Transfer Failed', 'woocommerce-customer-order-xml-export-suite' ),
					'export_not_found'         => __( 'Export Not Found', 'woocommerce-customer-order-xml-export-suite' ),
					'nothing_to_export'        => __( 'Nothing to Export', 'woocommerce-customer-order-xml-export-suite' ),
					'unexpected_error'         => __( 'Unexpected Error', 'woocommerce-customer-order-xml-export-suite' ),
					'unexpected_error_message' => sprintf( esc_html__( 'Something unexpected happened while exporting. Your export may or may have not completed. Please check the %1$sExport List%2$s and your site error log for possible clues as to what may have happened.', 'woocommerce-customer-order-xml-export-suite' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' ) . '">', '</a>' ),
					'load_mapping'             => __( 'Load mapping', 'woocommerce-customer-order-xml-export-suite' ),
					'done'                     => __( 'Done', 'woocommerce-customer-order-xml-export-suite' ),
					'load'                     => __( 'Load', 'woocommerce-customer-order-xml-export-suite' ),
					'close'                    => __( 'Close', 'woocommerce-customer-order-xml-export-suite' ),
					'cancel'                   => __( 'Cancel', 'woocommerce-customer-order-xml-export-suite' ),
					'confirm_export_delete'    => __( 'Are you sure you want to delete this export?', 'woocommerce-customer-order-xml-export-suite' ),
					'confirm_export_cancel'    => __( 'Are you sure you want to cancel this export?', 'woocommerce-customer-order-xml-export-suite' ),
					'confirm_export_transfer'  => __( 'Are you sure you want to send/upload this file?', 'woocommerce-customer-order-xml-export-suite' ),
					'default'                  => __( 'Default', 'woocommerce-customer-order-xml-export-suite' ),
				),
				'is_batch_enabled'     => wc_customer_order_xml_export_suite()->is_batch_processing_enabled(),
				'create_export_nonce'  => wp_create_nonce( 'create-export' ),
				'calendar_icon_url'    => WC()->plugin_url() . '/assets/images/calendar.png',
				'export_list_url'      => admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' ),
				'settings_page'        => $this->settings_page_name,
				'current_tab'          => empty( $_GET[ 'tab' ] ) ? 'export' : sanitize_title( $_GET[ 'tab' ] ),
				'current_section'      => empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] ),
			) );
		}
	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its CSS/JS on
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_styles_scripts( $screen_ids ) {

		$screen_ids[] = $this->settings_page_name;
		$screen_ids[] = 'users';

		return $screen_ids;

	}


	/**
	 * Add 'XML Export' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @since 1.0.0
	 */
	public function add_menu_link() {

		$this->page = add_submenu_page(
			'woocommerce',
			__( 'XML Export', 'woocommerce-customer-order-xml-export-suite' ),
			__( 'XML Export', 'woocommerce-customer-order-xml-export-suite' ),
			'manage_woocommerce_xml_exports',
			'wc_customer_order_xml_export_suite',
			array( $this, 'render_submenu_pages' )
		);
	}


	/**
	 * Add export finished notices for the current user
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notices() {

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$user_export_notices = get_user_meta( $user_id, '_wc_customer_order_xml_export_suite_notices', true );

		if ( ! empty( $user_export_notices ) ) {

			foreach ( $user_export_notices as $export_id ) {

				$message_id = 'wc_customer_order_xml_export_suite_finished_' . $export_id;

				if ( wc_customer_order_xml_export_suite()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $user_id ) ) {

					wc_customer_order_xml_export_suite()->get_export_handler_instance()->remove_export_finished_notice( $export_id, $user_id );

				} else {

					list( $message, $notice_class ) = $this->get_export_finished_message( $export_id );

					if ( $message ) {
						wc_customer_order_xml_export_suite()->get_admin_notice_handler()->add_admin_notice( $message, $message_id, array( 'always_show_on_settings' => false, 'notice_class' => $notice_class ) );
					}
				}
			}
		}

		if ( current_user_can( 'manage_woocommerce_xml_exports' ) ) {

			$auto_export_notices = get_option( 'wc_customer_order_xml_export_suite_failure_notices' );

			if ( ! empty( $auto_export_notices ) ) {

				foreach ( $auto_export_notices as $failure_type => $args ) {

					if ( empty( $args ) ) {
						return;
					}

					$message_id = 'wc_customer_order_xml_export_suite_auto_export_failure';

					if ( 'transfer' === $failure_type ) {
						$message_id = 'wc_customer_order_xml_export_suite_auto_export_transfer_failure';
					}

					$message = $this->get_failure_message( $failure_type, $args['export_id'], ! empty( $args['multiple_failures'] ) );

					if ( $message ) {
						wc_customer_order_xml_export_suite()->get_admin_notice_handler()->add_admin_notice( $message, $message_id, array( 'always_show_on_settings' => false, 'notice_class' => 'error' ) );
					}
				}
			}
		}
	}


	/**
	 * Returns the export finished message.
	 *
	 * @since 2.0.0
	 *
	 * @param string $export_id the export id
	 * @return array|bool message and notice class, or false on failure
	 */
	private function get_export_finished_message( $export_id ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export_id );

		if ( ! $export ) {
			return false;
		}

		$filename = $export->get_filename();

		if ( 'completed' === $export->get_status() ) {

			if ( 'failed' === $export->get_transfer_status() ) {

				$message      = $this->get_failure_message( 'transfer', $export );
				$notice_class = 'error';

			} else {

				/* translators: Placeholders: %1$s - exported file name, %2$s - opening <a> tag, %3$s - closing </a> tag */
				$message      = sprintf( __( 'Exported file %1$s is ready! You can download the exported file from the %2$sExport List%3$s.', 'woocommerce-customer-order-xml-export-suite' ), $filename, '<a href="' . admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' ) . '">', '</a>' );
				$notice_class = 'updated';
			}

		} elseif ( 'failed' === $export->get_status() ) {

			$message      = $this->get_failure_message( 'export', $export );
			$notice_class = 'error';

		}

		return isset( $message ) ? array( $message, $notice_class ) : false;
	}


	/**
	 * Returns the failure notice message.
	 *
	 * @since 2.0.0
	 *
	 * @param string $failure_type
	 * @param object|string $export export instance or id
	 * @param bool $multiple_failures defaults to false
	 * @return string|bool message string or false on failure
	 */
	private function get_failure_message( $failure_type, $export, $multiple_failures = false ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export ) {
			return false;
		}

		$filename = $export->get_filename();

		// strip random part from filename, which is prepended to the filename and
		// separated with a dash
		$filename = substr( $filename, strpos( $filename, '-' ) + 1 );

		/* translators: This phrase is always preceeded by an error message, such as "Exporting file failed". Thus, the details in this phrase refer to details about the error. Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
		$logs_message = sprintf( __( 'Additional details may be found in the XML Export Suite %1$slogs%2$s.', 'woocommerce-customer-order-xml-export-suite' ), '<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">', '</a>' );

		$export_list_url = admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' );

		if ( 'export' === $failure_type ) {

			if ( $multiple_failures ) {

				/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
				$message = __( 'Looks like automatic exports are failing.', 'woocommerce-customer-order-xml-export-suite' );


			} else {

				if ( 'auto' === $export->get_invocation() ) {

					/* translators: Placeholders: %s - file name */
					$message = sprintf( __( 'Automatically exporting file %s failed.', 'woocommerce-customer-order-xml-export-suite' ), $filename ) . ' ' . $logs_message;

				} else {

					/* translators: Placeholders: %s - file name */
					$message = sprintf( __( 'Exporting file %s failed.', 'woocommerce-customer-order-xml-export-suite' ), $filename );
				}

				$message .= ' ' . $logs_message;
			}

		} else {

			$label = wc_customer_order_xml_export_suite()->get_methods_instance()->get_export_method_label( $export->get_transfer_method() );

			if ( $multiple_failures ) {

				/* translators: Placeholders: %1$s - export method, such as "via Email", %2$s - opening <a> tag, %3$s - closing </a> tag */
				$message = sprintf( esc_html__( 'Looks like automatic exports are working, but the transfers %1$s are failing. Exported files are available under %2$sExport List%3$s.', 'woocommerce-customer-order-xml-export-suite' ), $label, '<a href="' . $export_list_url . '">', '</a>' );

				$message .= ' ' . $logs_message;

			} else {

				if ( 'auto' === $export->get_invocation() ) {

					/* translators: Placeholders: %1$s - file name, %2$s - export method, such as "via Email" */
					$message = sprintf( __( 'File %1$s was automatically exported, but the transfer %2$s failed.', 'woocommerce-customer-order-xml-export-suite' ), $filename, $label );

				} else {

					/* translators: Placeholders: %1$s - file name, %2$s - export method, such as "via Email" */
					$message = sprintf( __( 'File %1$s was exported, but the transfer %2$s failed.', 'woocommerce-customer-order-xml-export-suite' ), $filename, $label );
				}

				/* translators: %1$s - opening <a> tag, %2$s- closing </a> tag */
				$message .= ' ' . sprintf( esc_html__( 'Exported file is available under %1$sExport List%2$s.', 'woocommerce-customer-order-xml-export-suite' ), '<a href="' . $export_list_url . '">', '</a>' );

				$message .= ' ' . $logs_message;
			}
		}

		return $message;
	}


	/**
	 * Render a product search field
	 *
	 * @since 2.0.0
	 *
	 * @param array $value
	 */
	public function render_product_search_field( $value ) {

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {

			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {

				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		$field_description = WC_Admin_Settings::get_field_description( $value );
		$option_value      = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		$product_ids       = array_filter( array_map( 'absint', explode( ',', $option_value ) ) );
		$json_ids          = array();

		foreach ( $product_ids as $product_id ) {

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {
				$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
			}
		}

		?><tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $field_description['tooltip_html']; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $value['type'] ) ?>">
				<select class="<?php echo esc_attr( $value['class'] ); ?>" multiple="multiple" style="<?php echo esc_attr( $value['css'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>[]" data-placeholder="<?php echo esc_attr( $value['custom_attributes']['data-placeholder'] ); ?>" data-action="<?php echo esc_attr( $value['custom_attributes']['data-action'] ); ?>">
					<?php foreach ( $json_ids as $id => $name ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>" selected="selected"><?php echo $name; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr><?php
	}


	/**
	 * Render the sub-menu page for 'XML Export'
	 *
	 * @since 1.1.0
	 */
	public function render_submenu_pages() {

		global $current_tab, $current_section;

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_xml_exports' ) ) {
			return;
		}

		$this->tabs = array(
			'export'         => __( 'Export', 'woocommerce-customer-order-xml-export-suite' ),
			'export_list'    => __( 'Export List', 'woocommerce-customer-order-xml-export-suite' ),
			'settings'       => __( 'Settings', 'woocommerce-customer-order-xml-export-suite' ),
			'custom_formats' => __( 'Custom Formats', 'woocommerce-customer-order-xml-export-suite' ),
		);

		$current_tab     = empty( $_GET[ 'tab' ] ) ? 'export' : sanitize_title( $_GET[ 'tab' ] );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );

		// save settings
		if ( ! empty( $_POST ) && 'settings' === $current_tab ) {

			$this->get_settings_instance()->save();

			wc_customer_order_xml_export_suite()->get_cron_instance()->add_scheduled_export();
		}

		// save custom format
		if ( ! empty( $_POST ) && 'custom_formats' === $current_tab ) {

			$this->get_custom_format_builder_instance()->save();
		}

		?>
		<div class="wrap woocommerce">
		<form method="post" id="mainform" action="" enctype="multipart/form-data">
			<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				<?php
				foreach ( $this->tabs as $tab_id => $tab_title ) :

					$class = ( $tab_id === $current_tab ) ? array( 'nav-tab', 'nav-tab-active' ) : array( 'nav-tab' );
					$url   = add_query_arg( 'tab', $tab_id, admin_url( 'admin.php?page=wc_customer_order_xml_export_suite' ) );

					printf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $url ), implode( ' ', array_map( 'sanitize_html_class', $class ) ), esc_html( $tab_title ) );

				endforeach;
			?> </h2> <?php

		$this->message_handler->show_messages();

		if ( 'settings' === $current_tab ) {

			$this->get_settings_instance()->output();

		} elseif ( 'custom_formats' === $current_tab ) {

			$this->get_custom_format_builder_instance()->output();

		} elseif ( 'export_list' === $current_tab ) {

			$this->render_export_list_page();

		} else {

			$this->render_export_page();
		}

		?> </form>
		</div> <?php
	}


	/**
	 * Show Export page
	 *
	 * @since 1.1.0
	 */
	private function render_export_page() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_xml_exports' ) ) {
			return;
		}

		// show export form
		woocommerce_admin_fields( $this->get_export_options() );

		wp_nonce_field( __FILE__ );
		submit_button( __( 'Export', 'woocommerce-customer-order-xml-export-suite' ) );
	}


	/**
	 * Show export list page
	 *
	 * @since 2.0.0
	 */
	private function render_export_list_page() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce_xml_exports' ) ) {
			return;
		}

		// instantiate extended list table
		$export_list_table = $this->get_export_list_table();

		// prepare and display the list table
		$export_list_table->prepare_items();
		$export_list_table->display();
	}


	/**
	 * Get an instance of WC_Customer_Order_XML_Export_Suite_List_Table
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Customer_Order_XML_Export_Suite_List_Table
	 */
	private function get_export_list_table() {
		return wc_customer_order_xml_export_suite()->load_class( '/includes/admin/class-wc-customer-order-xml-export-suite-list-table.php', 'WC_Customer_Order_XML_Export_Suite_List_Table' );
	}


	/**
	 * Process exported files bulk actions
	 *
	 * Note this is hooked into `current_screen` as WC 2.1+ interferes with sending
	 * headers() from a sub-menu page, and `admin_init` is too early to detect current
	 * screen.
	 *
	 * @since 2.0.0
	 */
	public function process_export_bulk_actions() {

		$screen = get_current_screen();

		if ( $this->settings_page_name !== $screen->id ) {
			return;
		}

		$export_list_table = $this->get_export_list_table();
		$action            = $export_list_table->current_action();

		if ( ! $action ) {
			return;
		}

		check_admin_referer( 'bulk-exports' );

		$sendback = wp_get_referer();

		if ( ! $sendback ) {
			$sendback = admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' );
		}

		$pagenum  = $export_list_table->get_pagenum();

		if ( $pagenum > 1 ) {
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
		}

		if ( 'delete' === $action ) {

			if ( empty( $_POST['export'] ) ) {
				return;
			}

			$export_ids = (array) $_POST['export'];

			$background_export = wc_customer_order_xml_export_suite()->get_background_export_instance();

			foreach ( $export_ids as $export_id ) {
				$background_export->delete_job( $export_id );
			}

			$num_deleted = count( $export_ids );

			$this->message_handler->add_message( sprintf( _n( '%d exported file deleted.',  '%d exported files deleted.', 'woocommerce-customer-order-xml-export-suite', $num_deleted ), $num_deleted ) );

			wp_redirect( $sendback );
		}
	}


	/**
	 * Adds 'Export Status' column header to 'Orders' page immediately after 'Order Status' column
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 * @return array $new_columns
	 */
	public function add_order_status_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'order_status' === $column_name ) {

				$label = __( 'Export Status', 'woocommerce-customer-order-xml-export-suite' );

				if ( wc_customer_order_xml_export_suite()->is_plugin_active( 'woocommerce-customer-order-xml-export-suite.php' ) ) {
					$label = __( 'XML Export Status', 'woocommerce-customer-order-xml-export-suite' );
				}

				$new_columns['xml_export_status'] = $label;
			}
		}

		return $new_columns;
	}


/**
	 * Adds 'Export Status' column header to 'Users' page immediately after 'Order Status' column
	 *
	 * @since 2.2.0
	 *
	 * @param array $columns
	 * @return array $new_columns
	 */
	public function add_user_status_column_header( $columns ) {

		$label = __( 'Export Status', 'woocommerce-customer-order-xml-export-suite' );

		if ( wc_customer_order_xml_export_suite()->is_plugin_active( 'woocommerce-customer-order-xml-export-suite.php' ) ) {
			$label = __( 'XML Export Status', 'woocommerce-customer-order-xml-export-suite' );
		}

		$columns['xml_export_status'] = $label;

		return $columns;
	}


	/**
	 * Adds 'Export Status' column content to 'Orders' page immediately after 'Order Status' column
	 *
	 * 'Not Exported' - if 'is_exported' order meta doesn't exist or is equal to 0
	 * 'Exported' - if 'is_exported' order meta exists and is equal to 1
	 *
	 * @since 1.0.0
	 *
	 * @param array $column name of column being displayed
	 */
	public function add_order_status_column_content( $column ) {
		global $post;

		if ( 'xml_export_status' === $column ) {

			$order = wc_get_order( $post->ID );

			$is_exported = false;

			if ( SV_WC_Order_Compatibility::get_meta( $order, '_wc_customer_order_xml_export_suite_is_exported' ) ) {

				$is_exported = true;
			}

			printf( '<mark class="%1$s">%2$s</mark>', $is_exported ? 'xml_exported' : 'xml_not_exported', $is_exported ? esc_html__( 'Exported', 'woocommerce-customer-order-xml-export-suite' ) : esc_html__( 'Not Exported', 'woocommerce-customer-order-xml-export-suite' ) );
		}
	}


/**
	 * Adds 'Export Status' column content to 'Users' page.
	 *
	 * 'Not Exported' - if 'is_exported' user meta doesn't exist or is equal to 0.
	 * 'Exported' - if 'is_exported' user meta exists and is equal to 1.
	 *
	 * @since 2.2.0
	 *
	 * @param string $output the column contents
	 * @param string $column name of column being displayed
	 * @param int $user_id the user id
	 * @return string the column contents
	 */
	public function add_user_export_status_column_content( $output, $column, $user_id ) {

		if ( 'xml_export_status' === $column ) {

			$is_exported = get_user_meta( $user_id, '_wc_customer_order_xml_export_suite_is_exported', true );

			$output = sprintf( '<mark class="%1$s">%2$s</mark>', $is_exported ? 'xml_exported' : 'xml_not_exported', $is_exported ? esc_html__( 'Exported', 'woocommerce-customer-order-xml-export-suite' ) : esc_html__( 'Not Exported', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		return $output;
	}



	/**
	 * Adds 'Download to XML' order action to 'Order Actions' column
	 *
	 * Processed via AJAX
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order
	 */
	public function add_order_action( $actions, $order ) {

		if ( ! get_post_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), '_wc_customer_order_xml_export_suite_is_exported', true ) ) {

			$actions['download_to_xml'] = array(
				'url'    => '#',
				'name'   => __( 'Download to XML', 'woocommerce-customer-order-xml-export-suite' ),
				'action' => 'download_to_xml',
			);
		}

		return $actions;
	}


	/**
	 * Add 'Download to XML' link to order actions select box on edit order page
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions order actions array to display
	 * @return array
	 */
	public function add_order_meta_box_actions( $actions ) {

		// add download to XML action
		$actions['wc_customer_order_xml_export_suite_download'] = __( 'Download to XML', 'woocommerce-customer-order-xml-export-suite' );

		// add export to XML via [method] action
		if ( $auto_export_method = $this->get_methods_instance()->get_auto_export_method( 'orders' ) ) {

			$label = $this->get_methods_instance()->get_export_method_label( $auto_export_method );

			/* translators: Placeholders: %s - via [method], full example: Export to XML via Email */
			$actions['wc_customer_order_xml_export_suite_via_auto_export_method'] = sprintf( __( 'Export to XML %s', 'woocommerce-customer-order-xml-export-suite' ), $label );
		}

		return $actions;
	}


	/**
	 * Add bulk filter for Exported / Un-Exported orders
	 *
	 * @since 1.0.0
	 */
	public function filter_orders_by_export_status() {
		global $typenow;

		if ( 'shop_order' === $typenow ) {

			$count = $this->get_order_count();

			$terms = array(
				0 => (object) array( 'count' => $count['not_exported'], 'term' => __( 'Not Exported to XML', 'woocommerce-customer-order-xml-export-suite' ) ),
				1 => (object) array( 'count' => $count['exported'],     'term' => __( 'Exported to XML', 'woocommerce-customer-order-xml-export-suite' ) ),
			);

			?>
			<select name="_shop_order_xml_export_status" id="dropdown_shop_order_xml_export_status">
				<option value=""><?php _e( 'Show all orders', 'woocommerce-customer-order-xml-export-suite' ); ?></option>
				<?php foreach ( $terms as $value => $term ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( isset( $_GET['_shop_order_xml_export_status'] ) ? selected( $value, $_GET['_shop_order_xml_export_status'], false ) : '' ); ?>>
					<?php printf( '%1$s (%2$s)', esc_html( $term->term ), esc_html( $term->count ) ); ?>
				</option>
				<?php endforeach; ?>
			</select>
			<?php
		}
	}


	/**
	 * Process bulk filter action for Export / Un-Exported orders
	 *
	 * @since 1.0.0
	 *
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_export_status_query( $vars ) {
		global $typenow;

		if ( 'shop_order' === $typenow && isset( $_GET['_shop_order_xml_export_status'] ) && is_numeric( $_GET['_shop_order_xml_export_status'] ) ) {

			$vars['meta_key']   = '_wc_customer_order_xml_export_suite_is_exported';
			$vars['meta_value'] = (int) $_GET['_shop_order_xml_export_status'];
		}

		return $vars;
	}



	/**
	 * Add 'Download to XML' custom bulk action to the 'Orders' page bulk action drop-down
	 *
	 * In 2.2.0 added the $bulk_actions param and the return value.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $bulk_actions associative array of bulk actions and their labels
	 * @return string[]
	 */
	public function add_order_bulk_actions( $bulk_actions ) {
		return array_merge( $bulk_actions, $this->get_bulk_actions( 'orders' ) );
	}


	/**
	 * Add 'Download to XML' custom bulk action to the 'Users' page bulk action drop-down
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param string[] $bulk_actions associative array of bulk actions and their labels
	 * @return string[]
	 */
	public function add_user_bulk_actions( $bulk_actions ) {
		return array_merge( $bulk_actions, $this->get_bulk_actions( 'customers' ) );
	}


	/**
	 * Returns XML export bulk actions for the given export type.
	 *
	 * @since 2.2.0
	 *
	 * @param string $export_type the export type, `orders` or `customers`
	 * @return string[] associative array of bulk actions and their labels
	 */
	private function get_bulk_actions( $export_type ) {

		$bulk_actions = array(
			'mark_exported_to_xml'     => __( 'Mark exported to XML', 'woocommerce-customer-order-xml-export-suite' ),
			'mark_not_exported_to_xml' => __( 'Mark not exported to XML', 'woocommerce-customer-order-xml-export-suite' ),
			'download_to_xml'          => __( 'Download to XML', 'woocommerce-customer-order-xml-export-suite' ),
		);

		// add export to XML via [method] action
		if ( $auto_export_method = $this->get_methods_instance()->get_auto_export_method( $export_type ) ) {

			$label = $this->get_methods_instance()->get_export_method_label( $auto_export_method );

			/* translators: Placeholders: %s - via [method], full example: Export to XML via Email */
			$label = sprintf( __( 'Export to XML %s', 'woocommerce-customer-order-xml-export-suite' ), $label );

			$bulk_actions['export_to_xml_via_auto_export_method'] = $label;
		}

		return $bulk_actions;
	}


	/**
	 * Adds XML export bulk actions to supported screens using JS.
	 *
	 * Workaround for adding bulk actions to WP list table pre WP 4.7.
	 *
	 * @since 2.2.0
	 */
	public function add_bulk_actions_legacy() {
		global $post_status;

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-shop_order', 'users' ) ) && 'trash' !== $post_status ) {

			$export_type  = $this->map_screen_to_export_type( $screen->id );
			$bulk_actions = $this->get_bulk_actions( $export_type );

			if ( ! empty( $bulk_actions ) ) {
				?>
				<script type="text/javascript">
					jQuery( document ).ready( function ( $ ) {
						<?php foreach ( $bulk_actions as $action => $label ) : ?>
							$( 'select[name^="action"]' ).append( $( '<option>' ).val( '<?php echo esc_html( $action ); ?>' ).text( '<?php echo esc_html( $label ); ?>' ) );
						<?php endforeach; ?>
					});
				</script>
				<?php
			}
		}
	}


	/**
	 * Processes the 'Download to XML' custom bulk action on the 'Orders' page bulk action drop-down.
	 *
	 * In 2.2.0 added the $redirect_to, $doactions and $post_ids params, as well as the return value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $redirect_to the redirect url
	 * @param string $doaction the action being taken
	 * @param int[] $order_ids the orders to take the action on
	 * @return string the redirect url
	 */
	public function process_order_bulk_actions( $redirect_to, $doaction, $order_ids ) {

		$this->process_bulk_actions( $doaction, $order_ids, 'orders' );

		return $redirect_to;
	}


	/**
	 * Processes the custom bulk actions on the 'Users' page bulk action drop-down.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param string $redirect_to the redirect url
	 * @param string $doaction the action being taken
	 * @param int[] $user_ids the users to take the action on
	 * @return string the redirect url
	 */
	public function process_user_bulk_actions( $redirect_to, $doaction, $user_ids ) {

		$this->process_bulk_actions( $doaction, $user_ids, 'customers' );

		return $redirect_to;
	}


	/**
	 * Processes custom bulk actions for an export type.
	 *
	 * @since 2.2.0
	 *
	 * @param string $action the action being taken
	 * @param int[] $object_ids the items to take the action on
	 * @param string $export_type he export type, one of `orders` or `customers`
	 */
	private function process_bulk_actions( $action, $object_ids, $export_type ) {

		$meta_type = 'customers' === $export_type ? 'user' : 'post';

		switch ( $action ) {

			case 'mark_exported_to_xml':

				// mark each object as exported
				foreach( $object_ids as $object_id ) {
					update_metadata( $meta_type, $object_id, '_wc_customer_order_xml_export_suite_is_exported', 1 );
				}

				$message = '';

				switch ( $export_type ) {
					case 'customers':
						$message = sprintf( _n( '%d customer marked as exported to XML', '%d customers marked as exported to XML', count( $object_ids ), 'woocommerce-customer-order-xml-export-suite' ), count( $object_ids ) );
					break;

					case 'orders':
						$message = sprintf( _n( '%d order marked as exported to XML', '%d orders marked as exported to XML', count( $object_ids ), 'woocommerce-customer-order-xml-export-suite' ), count( $object_ids ) );
					break;
				}

				wc_customer_order_xml_export_suite()->get_message_handler()->add_message( $message );

			break;

			case 'mark_not_exported_to_xml':

				// mark each object as not exported
				foreach( $object_ids as $object_id ) {
					update_metadata( $meta_type, $object_id, '_wc_customer_order_xml_export_suite_is_exported', 0 );
				}

				$message = '';

				switch ( $export_type ) {
					case 'customers':
						$message = sprintf( _n( '%d customer marked as not exported to XML', '%d customers marked as not exported to XML', count( $object_ids ), 'woocommerce-customer-order-xml-export-suite' ), count( $object_ids ) );
					break;

					case 'orders':
						$message = sprintf( _n( '%d order marked as not exported to XML', '%d orders marked as not exported to XML', count( $object_ids ), 'woocommerce-customer-order-xml-export-suite' ), count( $object_ids ) );
					break;
				}
				wc_customer_order_xml_export_suite()->get_message_handler()->add_message( $message );

			break;
		}
	}


	/**
	 * Processes custom bulk actions in WP < 4.7
	 *
	 * @since 2.2.0
	 */
	public function process_bulk_actions_legacy() {
		global $post_status;

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-shop_order', 'users' ) ) && 'trash' !== $post_status ) {

			$export_type = $this->map_screen_to_export_type( $screen->id );
			$list_table  = 'customers' === $export_type ? 'WP_Users_List_Table' : 'WP_Posts_List_Table';

			// get the action
			$wp_list_table = _get_list_table( $list_table );
			$action        = $wp_list_table->current_action();

			// bail if not processing one of our actions
			if ( ! array_key_exists( $action, $this->get_bulk_actions( $export_type ) ) ) {
				return;
			}

			// security check & get object ids
			if ( 'customers' === $export_type ) {
				check_admin_referer( 'bulk-users' );
				$object_ids = array_map( 'absint', ! empty( $_REQUEST['users'] ) ? $_REQUEST['users'] : null );
			} else {
				check_admin_referer( 'bulk-posts' );
				$object_ids = array_map( 'absint', ! empty( $_REQUEST['post'] ) ? $_REQUEST['post'] : null );
			}

			// return if there are no objects to export
			if ( empty( $object_ids ) ) {
				return;
			}

			// give ourselves an unlimited timeout if possible
			@set_time_limit( 0 );

			$this->process_bulk_actions( $action, $object_ids, $export_type );

			// redirect back, since WP < 4.7 doesn't do it for us
			wp_safe_redirect( wp_get_referer() );
			exit;
		}
	}


	/**
	 * Maps screen id to an export type.
	 *
	 * @since 2.2.0
	 *
	 * @param string $screen_id the screen id
	 * @return string|null the export type or null if no match found
	 */
	private function map_screen_to_export_type( $screen_id ) {

		$export_type = null;

		// match screen id to export type
		switch ( $screen_id ) {
			case 'edit-shop_order':
				$export_type = 'orders';
			break;

			case 'users':
				$export_type = 'customers';
			break;
		}

		return $export_type;
	}


	/**
	 * Get the order count for exported/not exported orders
	 *
	 * Orders placed prior to the installation / activation of the plugin will be counted as exported
	 *
	 * @since 1.0.0
	 *
	 * @return array { 'not_exported' => count, 'exported' => count }
	 */
	private function get_order_count() {

		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_order',
			'post_status' => isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'any',
			'meta_query'  => array(
				array(
					'key'   => '_wc_customer_order_xml_export_suite_is_exported',
					'value' => 0
				)
			),
			'nopaging'    => true,
		);

		$not_exported_query = new WP_Query( $query_args );

		$query_args['meta_query'][0]['value'] = 1;

		$exported_query = new WP_Query( $query_args );

		return array( 'not_exported' => $not_exported_query->found_posts, 'exported' => $exported_query->found_posts );
	}


	/**
	 * Returns options array for the export page
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_export_options() {

		$order_statuses     = wc_get_order_statuses();
		$product_categories = array();

		foreach ( get_terms( 'product_cat' ) as $term ) {
			$product_categories[ $term->term_id ] = $term->name;
		}

		$options = array(

			'export_section_title' => array(
				'name' => __( 'Export', 'woocommerce-customer-order-xml-export-suite' ),
				'type' => 'title',
			),

			'type' => array(
				'id'      => 'type',
				'name'    => __( 'Export Type', 'woocommerce-customer-order-xml-export-suite' ),
				'type'    => 'radio',
				'options' => array(
					'orders'    => __( 'Orders', 'woocommerce-customer-order-xml-export-suite' ),
					'customers' => __( 'Customers', 'woocommerce-customer-order-xml-export-suite' ),
				),
				'default'  => 'orders',
			),

			'export_section_end' => array( 'type' => 'sectionend' ),

			'export_options_title' => array(
				'name' => __( 'Export Options', 'woocommerce-customer-order-xml-export-suite' ),
				'type' => 'title',
			),

			'statuses' => array(
				'id'                => 'statuses',
				'name'              => __( 'Order Statuses', 'woocommerce-customer-order-xml-export-suite' ),
				'desc_tip'          => __( 'Orders with these statuses will be included in the export.', 'woocommerce-customer-order-xml-export-suite' ),
				'type'              => 'multiselect',
				'options'           => $order_statuses,
				'default'           => '',
				'class'             => 'wc-enhanced-select show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Leave blank to export orders with any status.', 'woocommerce-customer-order-xml-export-suite' ),
				),
			),

			'product_categories' => array(
				'id'                => 'product_categories',
				'name'              => __( 'Product Categories', 'woocommerce-customer-order-xml-export-suite' ),
				'desc_tip'          => __( 'Orders with products in these categories will be included in the export.', 'woocommerce-customer-order-xml-export-suite' ),
				'type'              => 'multiselect',
				'options'           => $product_categories,
				'default'           => '',
				'class'             => 'wc-enhanced-select show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Leave blank to export orders with products in any category.', 'woocommerce-customer-order-xml-export-suite' ),
				),
			),

			'products' => array(
				'id'                => 'products',
				'name'              => __( 'Products', 'woocommerce-customer-order-xml-export-suite' ),
				'desc_tip'          => __( 'Orders with these products will be included in the export.', 'woocommerce-customer-order-xml-export-suite' ),
				'type'              => 'xml_product_search',
				'default'           => '',
				'class'             => 'wc-product-search show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export orders with any products.', 'woocommerce-customer-order-xml-export-suite' ),
				),
			),

			'start_date' => array(
				'id'   => 'start_date',
				'name' => __( 'Start Date', 'woocommerce-customer-order-xml-export-suite' ),
				'desc' => __( 'Start date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-xml-export-suite' ),
				'type' => 'text',
			),

			'end_date' => array(
				'id'   => 'end_date',
				'name' => __( 'End Date', 'woocommerce-customer-order-xml-export-suite' ),
				'desc' => __( 'End date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-xml-export-suite' ),
				'type' => 'text',
			),

			// coupon-specific options

			'coupon_product_categories' => array(
				'id'                => 'coupon_product_categories',
				'name'              => __( 'Product Categories', 'woocommerce-customer-order-xml-export-suite' ),
				'desc_tip'          => __( 'Coupons that apply to these categories will be included in the export.', 'woocommerce-customer-order-xml-export-suite' ),
				'type'              => 'multiselect',
				'options'           => $product_categories,
				'default'           => '',
				'class'             => 'wc-enhanced-select show_if_coupons',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Leave blank to export coupons that apply to any category.', 'woocommerce-customer-order-xml-export-suite' ),
				),
			),

			'coupon_products' => array(
				'id'                => 'coupon_products',
				'name'              => __( 'Products', 'woocommerce-customer-order-xml-export-suite' ),
				'desc_tip'          => __( 'Coupons that apply to these products will be included in the export.', 'woocommerce-customer-order-xml-export-suite' ),
				'type'              => 'xml_product_search',
				'default'           => '',
				'class'             => 'wc-product-search show_if_coupons',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export coupons that apply to any products.', 'woocommerce-customer-order-xml-export-suite' ),
				),
			),

			'export_options_section_end' => array( 'type' => 'sectionend' ),

		);

		// add coupons to export types only if enabled
		if ( wc_customer_order_xml_export_suite()->is_coupon_export_enabled() ) {
			$options['type']['options']['coupons'] = __( 'Coupons', 'woocommerce-customer-order-xml-export-suite' );
		}

		if ( wc_customer_order_xml_export_suite()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			$options['subscription_options_section_title'] = array(
				'name' => __( 'Subscriptions Options', 'woocommerce-customer-order-xml-export-suite' ),
				'type' => 'title',
			);

			$options['subscription_orders'] = array(
				'id'            => 'subscription_orders',
				'title'         => __( 'Export Subscriptions Orders Only', 'woocommerce-customer-order-xml-export-suite' ),
				'desc'          => __( 'Export subscription orders', 'woocommerce-customer-order-xml-export-suite' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			);

			$options['subscription_renewals'] = array(
				'id'            => 'subscription_renewals',
				'desc'          => __( 'Export renewal orders', 'woocommerce-customer-order-xml-export-suite' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			);

			$options['subscription_options_section_end'] = array( 'type' => 'sectionend' );

		}


		/**
		 * Allow actors to add or remove options from the XML export page.
		 *
		 * @since 2.0.0
		 *
		 * @param array $options an array of options for the export tab
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_options', $options );
	}


	/**
	 * Output the System Status report table
	 *
	 * @since 1.6.0
	 */
	public function add_system_status_report() {

		include( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/admin/views/html-system-status-table.php' );
	}



	/**
	 * Print export modal templates
	 *
	 * @since 2.0.0
	 */
	public function add_export_modals() {

		if ( ! $this->is_export_screen() ) {
			return;
		}

		include( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/admin/views/html-export-modals.php' );
	}


	/**
	 * Check whether we are currently on one of the export screens
	 *
	 * @since 2.0.0
	 */
	private function is_export_screen() {

		$screen = get_current_screen();

		return in_array( $screen->id, array(
			$this->settings_page_name,
			'shop_order',
			'edit-shop_order',
			'users',
		), true );
	}


	/**
	 * Delete an exported file
	 *
	 * @since 2.0.0
	 */
	public function delete_export() {

		check_admin_referer( 'delete-export' );

		$export = wc_customer_order_xml_export_suite_get_export( $_GET['export_id'] );

		if ( ! $export ) {
			wp_safe_redirect( wp_get_referer() );
		}

		if ( ! in_array( $export->get_status(), array( 'completed', 'failed' ), true ) ) {

			$message = __( 'Export cancelled.', 'woocommerce-customer-order-xml-export-suite' );

		} else {

			$message = __( 'Exported file deleted.', 'woocommerce-customer-order-xml-export-suite' );
		}

		$export->delete();

		$this->message_handler->add_message( $message );

		wp_redirect( wp_get_referer() );
	}


	/**
	 * Transfer an exported file using the auto-export method
	 *
	 * @since 2.0.0
	 */
	public function transfer_export() {

		check_admin_referer( 'transfer-export' );

		$export_id = $_GET['export_id'];

		if ( ! $export_id ) {
			return;
		}

		$export = wc_customer_order_xml_export_suite_get_export( $export_id );

		if ( ! $export ) {
			return;
		}

		$filename = $export->get_filename();

		// strip random part from filename
		$filename = substr( $filename, strpos( $filename, '-' ) + 1 );

		$auto_export_method = $this->get_methods_instance()->get_auto_export_method( $export->get_type() );

		if ( ! $auto_export_method ) {

			/* translators: Placeholders: %s - file name */
			$this->message_handler->add_message( sprintf( __( 'Could not transfer file %s - no auto export method configured.', 'woocommerce-customer-order-xml-export-suite' ), $filename ) );

			wp_safe_redirect( wp_get_referer() );
		}

		$label = $this->get_methods_instance()->get_export_method_label( $auto_export_method );

		try {

			wc_customer_order_xml_export_suite()->get_export_handler_instance()->transfer_export( $export_id, $auto_export_method );

			/* translators: Placeholders: %1$s - file name, %2$3 - transfer method, such as "via Email" */
			$this->message_handler->add_message( sprintf( __( 'File %1$s transferred %2$s.', 'woocommerce-customer-order-xml-export-suite' ), $filename, $label ) );

		} catch ( SV_WC_Plugin_Exception $e ) {

			/* translators: Placeholders: %1$s - file name, %2$3 - transfer method, such as "via Email", %3$s - error message */
			$error = sprintf( __( 'Could not transfer %1$s %2$s: %3$s', 'woocommerce-customer-order-xml-export-suite' ), $filename, $label, $e->getMessage() );

			wc_customer_order_xml_export_suite()->log( $error );
			$this->message_handler->add_error( $error );
		}

		wp_redirect( wp_get_referer() );
	}


	/**
	 * Get the settings class instance
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Customer_Order_XML_Export_Suite_Admin_Settings instance
	 */
	public function get_settings_instance() {

		if ( ! isset( $this->settings ) ) {

			$this->settings = wc_customer_order_xml_export_suite()->load_class( '/includes/admin/class-wc-customer-order-xml-export-suite-admin-settings.php', 'WC_Customer_Order_XML_Export_Suite_Admin_Settings' );
		}

		return $this->settings;
	}


	/**
	 * Get the custom format builder class instance
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Customer_Order_XML_Export_Suite_Admin_Custom_Format_Builder instance
	 */
	public function get_custom_format_builder_instance() {

		if ( ! isset( $this->custom_format_builder ) ) {

			$this->custom_format_builder = wc_customer_order_xml_export_suite()->load_class( '/includes/admin/class-wc-customer-order-xml-export-suite-admin-custom-format-builder.php', 'WC_Customer_Order_XML_Export_Suite_Admin_Custom_Format_Builder' );
		}

		return $this->custom_format_builder;
	}


	/**
	 * Get the export methods class instance
	 *
	 * Shortcut method
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Customer_Order_XML_Export_Suite_Methods instance
	 */
	private function get_methods_instance() {

		return wc_customer_order_xml_export_suite()->get_methods_instance();
	}


	/**
	 * Checks which method we're using to serve downloads.
	 *
	 * If using force or x-sendfile, this ensures the .htaccess is in place.
	 *
	 * In 2.2.0 moved here from WC_Customer_Order_XML_Export_Suite_Admin_Settings
	 *
	 * @since 2.0.0
	 */
	public function check_exports_folder_protection() {

		$upload_dir      = wp_upload_dir();
		$exports_dir     = $upload_dir['basedir'] . '/xml_exports';
		$download_method = get_option( 'woocommerce_file_download_method' );

		if ( 'redirect' === $download_method ) {

			// Redirect method - don't protect
			if ( file_exists( $exports_dir . '/.htaccess' ) ) {
				unlink( $exports_dir . '/.htaccess' );
			}

		} else {

			// Force method - protect, add rules to the htaccess file
			if ( ! file_exists( $exports_dir . '/.htaccess' ) && $file_handle = @fopen( $exports_dir . '/.htaccess', 'w' ) ) {

				fwrite( $file_handle, 'deny from all' );
				fclose( $file_handle );
			}
		}
	}


}
