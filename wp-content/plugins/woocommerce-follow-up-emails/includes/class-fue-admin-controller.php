<?php

/**
 * Class FUE_Admin_Controller
 *
 * Controller for the Admin Panel
 */
class FUE_Admin_Controller {
	/**
	 * Register the menu items
	 */
	public static function add_menu() {
		add_menu_page( __( 'Follow-Up', 'follow_up_emails' ), __( 'Follow-Up', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails', 'FUE_Admin_Controller::admin_controller', 'dashicons-email-alt', '54.51' );
		add_submenu_page( 'followup-emails', __( 'Follow-Up Emails', 'follow_up_emails' ), __( 'Emails', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails', 'FUE_Admin_Controller::admin_controller' );
		add_submenu_page( 'followup-emails', __( 'Campaigns', 'follow_up_emails' ), __( 'Campaigns', 'follow_up_emails' ), 'manage_follow_up_emails', 'fue_campaigns', 'FUE_Admin_Controller::admin_controller' );

		add_submenu_page( 'followup-emails', __( 'New Follow-Up', 'follow_up_emails' ), __( 'New Follow-Up', 'follow_up_emails' ), 'manage_follow_up_emails', 'fue_post_email', 'FUE_Admin_Controller::admin_controller' );
		add_submenu_page( 'followup-emails', __( 'New Tweet', 'follow_up_emails' ), __( 'New Tweet', 'follow_up_emails' ), 'manage_follow_up_emails', 'fue_post_tweet', 'FUE_Admin_Controller::admin_controller' );

		do_action( 'fue_menu' );

		add_submenu_page( 'followup-emails', __( 'Scheduled Emails', 'follow_up_emails' ), __( 'Scheduled Emails', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails-queue', 'FUE_Admin_Controller::queue_table' );
		add_submenu_page( 'followup-emails', __( 'Subscribers', 'follow_up_emails' ), __( 'Mailing Lists', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails-subscribers', 'FUE_Admin_Controller::subscribers_table' );
		add_submenu_page( 'followup-emails', __( 'Follow-Up Emails Templates', 'follow_up_emails' ), __( 'Templates', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails-templates', 'FUE_Admin_Controller::templates' );
		add_submenu_page( 'followup-emails', __( 'Follow-Up Emails Settings', 'follow_up_emails' ), __( 'Settings', 'follow_up_emails' ), 'manage_follow_up_emails', 'followup-emails-settings', 'FUE_Admin_Controller::settings' );
	}

	public static function get_screen_ids() {
		return apply_filters( 'fue_screen_ids', array(
			'toplevel_page_followup-emails',
			'follow_up_email',
			'edit-follow_up_email_campaign',
			'follow-up_page_followup-emails-reports',
			'follow-up_page_followup-emails-reports-customers',
			'follow-up_page_followup-emails-coupons',
			'follow-up_page_followup-emails-queue',
			'follow-up_page_followup-emails-subscribers',
			'follow-up_page_followup-emails-optouts',
			'follow-up_page_followup-emails-settings',
			'follow-up_page_followup-emails-addons',
			'follow-up_page_followup-emails-templates',
		) );
	}

	/**
	 * This appears to no longer be used.
	 *
	 * Enable all elements when working with HTML templates.
	 *
	 * @unused
	 * @param array $options
	 * @return array
	 */
	public static function enable_mce_elements( $options ) {
		if ( empty( $_GET['page'] ) || 'followup-emails-templates' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $options;
		}

		if ( ! isset( $options['extended_valid_elements'] ) ) {
			$options['extended_valid_elements'] = '';
		} else {
			$options['extended_valid_elements'] .= ',';
		}

		if ( ! isset( $options['custom_elements'] ) ) {
			$options['custom_elements'] = '';
		} else {
			$options['custom_elements'] .= ',';
		}

		$options['extended_valid_elements'] .= '*[*]';
		$options['custom_elements']         .= '*[*]';
		$options['plugins']                 .= ',fullpage';

		return $options;
	}

	/**
	 * Register the full_page plugin for TinyMCE to edit full HTML templates
	 * @param array $plugins_array
	 * @return array
	 */
	public static function register_mce_plugins( $plugins_array ) {

		if ( empty( $_GET['page'] ) || 'followup-emails-templates' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $plugins_array;
		}

		$plugins = array( 'fullpage' ); // Add any more plugins you want to load here.

		// Build the response - the key is the plugin name, value is the URL to the plugin JS.
		foreach ( $plugins as $plugin ) {
			$plugins_array[ $plugin ] = FUE_TEMPLATES_URL . '/js/tinymce/' . $plugin . '/plugin.min.js';
		}
		return $plugins_array;
	}

	/**
	 * Replace the placeholder URL we're using for the Email Form page with the actual URL.
	 *
	 * @param $url
	 * @param $original_url
	 * @param $_context
	 *
	 * @return string|void
	 */
	public static function replace_email_form_url( $url, $original_url, $_context ) {
		if ( 'admin.php?page=fue_post_email' === $url ) {
			return admin_url( 'post-new.php?post_type=follow_up_email' );
		} elseif ( 'admin.php?page=fue_post_tweet' === $url ) {
			return admin_url( 'post-new.php?post_type=follow_up_email&type=twitter' );
		} elseif ( 'admin.php?page=fue_campaigns' === $url ) {
			return admin_url( 'edit-tags.php?taxonomy=follow_up_email_campaign' );
		} elseif ( strpos( $url, 'edit.php?follow_up_email_campaign=' ) !== false ) {
			$parts = array();
			parse_str( $url, $parts );
			$terms = array_values( $parts );

			return esc_url( 'admin.php?page=followup-emails&campaign=' . $terms[0] );
		}

		return $url;
	}

	/**
	 * Set the current submenu item in the admin nav menu.
	 *
	 * @param string $parent_file
	 * @return string
	 */
	public static function set_active_submenu( $parent_file ) {
		global $submenu_file, $plugin_page;

		if ( 'edit.php?post_type=follow_up_email' === $parent_file ) {
			$parent_file = 'followup-emails';
			$submenu_file = null;
		} elseif ( 'edit-tags.php?taxonomy=follow_up_email_campaign' === $submenu_file ) {
			$parent_file = 'followup-emails';
			$submenu_file = 'fue_campaigns';
		}

		return $parent_file;
	}

	/**
	 * Routes the request to the correct page/file.
	 */
	public static function admin_controller() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'list'; // //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $tab ) {
			case 'list':
				self::list_emails_page();
				break;
			case 'edit':
				self::email_form( 1, absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				break;
			case 'send':
				$id    = absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$email = new FUE_Email( $id );
				if ( ! $email->exists() ) {
					wp_die( "The requested data could not be found!" );
				}

				self::send_manual_form( $email );
				break;
			case 'send_manual_emails':
				self::send_manual_emails();
				break;
			case 'send_manual_email_batches':
				self::send_manual_email_batches();
				break;
			case 'updater':
				self::updater_page();
				break;
			case 'data_updater':
				self::data_updater();
				break;
			case 'history':
				self::show_followup_history();
				break;
			default:
				// Allow add-ons to add tabs.
				do_action( 'fue_admin_controller', $tab );
				break;
		}
	}

	/**
	 * FUE Dashboard Widget.
	 */
	public static function dashboard_widget() {
		wp_add_dashboard_widget( 'fue-dashboard', __( 'Follow-Up Emails', 'follow_up_emails' ), array( 'FUE_Report_Dashboard_Widget', 'display' ) );
	}

	/**
	 * Page that lists all FUE_Emails.
	 */
	public static function list_emails_page() {
		$types          = Follow_Up_Emails::get_email_types();
		$campaigns      = get_terms( 'follow_up_email_campaign', array( 'hide_empty' => false ) );
		$from_addresses = get_option( 'fue_from_email_types', false );
		$from_names     = get_option( 'fue_from_name_types', false );

		include FUE_TEMPLATES_DIR . '/email-list/email-list.php';
	}

	/**
	 * Send Single Email form.
	 *
	 * @param $email
	 */
	public static function send_manual_form( $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		include FUE_TEMPLATES_DIR . '/send_manual_form.php';
	}

	/**
	 * Send manual emails in batches.
	 */
	public static function send_manual_emails() {
		include FUE_TEMPLATES_DIR . '/send_manual_emails.php';
	}

	/**
	 * If batched sending is enabled and the number of recipients exceeds the
	 * manual sending limit send the manual emails in batches.
	 */
	public static function send_manual_email_batches() {
		$args = array(
			'page_title'            => __( 'Processing Emails', 'follow_up_emails' ),
			'return_url'            => admin_url( 'admin.php?page=followup-emails' ),
			'ajax_endpoint'         => 'fue_send_manual_email_batches',
			'entity_label_singular' => __( 'batch', 'follow_up_emails' ),
			'entity_label_plural'   => __( 'batches', 'follow_up_emails' ),
			'action_label'          => _x( 'processed', 'Data updater action label', 'follow_up_emails' ),
		);

		include FUE_TEMPLATES_DIR . '/data-updater.php';
	}

	/**
	 * Admin interface for managing subscribers.
	 */
	public static function subscribers_table() {
		$wpdb   = Follow_Up_Emails::instance()->wpdb;
		$view   = ( empty( $_REQUEST['view'] ) ) ? 'subscribers' : sanitize_text_field( wp_unslash( $_REQUEST['view'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		include FUE_TEMPLATES_DIR . '/subscribers-page.php';
	}

	/**
	 * Admin Updater interface.
	 */
	public static function updater_page() {
		global $wpdb;

		include FUE_TEMPLATES_DIR . '/updater.php';
	}

	/**
	 * Admin Updater interface.
	 */
	public static function data_updater() {
		$act = isset( $_GET['act'] ) ? sanitize_text_field( wp_unslash( $_GET['act'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $act ) {
			case 'clear_scheduler':
				self::clear_scheduler_updater();
				break;
			case 'migrate_logs':
				self::migrate_logs();
				break;
		}
	}

	/**
	 * Handler for db version 20160203 that removes unnecessary scheduled-action
	 * rows.
	 */
	public static function clear_scheduler_updater() {
		$return = admin_url( 'admin.php?page=followup-emails' );

		$args = array(
			'page_title'            => __( 'Data Update', 'follow_up_emails' ),
			'return_url'            => $return,
			'ajax_endpoint'         => 'fue_clear_scheduled_actions',
			'entity_label_singular' => 'item',
			'entity_label_plural'   => 'items',
			'action_label'          => 'updated',
		);

		include FUE_TEMPLATES_DIR . '/data-updater.php';
	}

	/**
	 * Handler for db version 20160308 that moves follow-up history logs to
	 * the new followup_followup_history table.
	 */
	public static function migrate_logs() {
		$return = admin_url( 'admin.php?page=followup-emails' );

		$args = array(
			'page_title'            => __( 'Data Update', 'follow_up_emails' ),
			'return_url'            => $return,
			'ajax_endpoint'         => 'fue_migrate_logs',
			'entity_label_singular' => 'item',
			'entity_label_plural'   => 'items',
			'action_label'          => 'updated',
		);

		include FUE_TEMPLATES_DIR . '/data-updater.php';
	}

	/**
	 * Settings Interface
	 */
	public static function settings() {
		global $wpdb;

		$pages                  = get_pages();
		$emails                 = get_option( 'fue_daily_emails' );
		$bcc                    = get_option( 'fue_bcc', '' );
		$from                   = get_option( 'fue_from_email', '' );
		$from_name              = get_option( 'fue_from_name', get_bloginfo( 'name' ) );
		$bounce                 = get_option( 'fue_bounce_settings', '' );
		$email_batches          = get_option( 'fue_email_batches', 0 );
		$disable_logging        = get_option( 'fue_disable_action_scheduler_logging', 1 );
		$api_enabled            = get_option( 'fue_api_enabled', 'yes' );
		$emails_per_batch       = get_option( 'fue_emails_per_batch', 100 );
		$email_batch_interval   = get_option( 'fue_batch_interval', 10 );
		$spf                    = get_option( 'fue_spf', array() );
		$dkim                   = get_option( 'fue_dkim', array() );
		$tab                    = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'system'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		include FUE_TEMPLATES_DIR . '/settings/settings.php';
	}

	/**
	 * Render the templates page.
	 */
	public static function templates() {
		add_thickbox();

		include FUE_TEMPLATES_DIR . '/add-ons/templates.php';
	}

	/**
	 * System Info page.
	 */
	public static function system_info() {
		include FUE_TEMPLATES_DIR . '/settings/system-info.php';
	}

	/**
	 * Display the queue items.
	 */
	public static function queue_table() {
		$table = new FUE_Sending_Queue_List_Table();
		$table->prepare_items();
		$table->messages();
		?>
		<style>
			span.trash a {
				color: #a00 !important;
			}

		</style>
		<script>
			jQuery(document).ready(function($) {
				$( "#delete-all-submit" ).on( 'click', function( e ) {
					if ( confirm( "<?php echo esc_js( __( 'This will delete ALL scheduled emails! Continue?', 'follow_up_emails' ) ); ?>" ) ) {
						return true;
					}
					return false;
				} );
			});
		</script>
		<div class="wrap">
			<h2><?php esc_html_e( 'Scheduled Emails', 'follow_up_emails' ); ?></h2>

			<form id="queue-filter" action="" method="get">
				<?php $table->display(); ?>
			</form>
		</div>
		<?php
	}

	public static function show_followup_history() {
		$wpdb   = Follow_Up_Emails::instance()->wpdb;
		$id     = ( ! empty( $_GET['id'] ) ) ? absint( $_GET['id'] ) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$emails = fue_get_emails( 'any', null, array( 'orderby' => 'post_title' ) );

		if ( $id > 0 ) {
			$args['followup_id'] = $id;
		} else {
			$args['number'] = 100;
		}

		$logs = FUE_Followup_Logger::get_logs( $args );

		include FUE_TEMPLATES_DIR . '/email-log.php';
	}

	/**
	 * Register the scripts early so other addons can use them.
	 */
	public static function register_scripts() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, self::get_screen_ids() ) ) {
			return;
		}

		if ( ! wp_script_is( 'jquery-tiptip', 'registered' ) ) {
			wp_register_script( 'jquery-tiptip', FUE_URL . '/templates/js/jquery.tipTip.min.js', array( 'jquery' ), FUE_VERSION, true );
		}

		// blockUI.
		if ( ! wp_script_is( 'jquery-blockui', 'registered' ) ) {
			wp_register_script( 'jquery-blockui', FUE_URL . '/templates/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), FUE_VERSION, true );
		}

		// select2 (when WooCommerce is not installed).
		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), '4.0.13', false );
			wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.css', array(), '4.0.13' );
		}

		// Register select styles on non WooCommerce pages.
		if ( class_exists( 'WooCommerce' ) && ! wp_style_is( 'select2', 'registered' ) ) {
			wp_register_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css', array(), WC_VERSION );
		}

	}

	/**
	 * Load the necessary scripts.
	 */
	public static function settings_scripts() {
		$screen = get_current_screen();

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$fue_pages = array(
			'followup-emails',
			'followup-emails-form',
			'followup-emails-reports',
			'followup-emails-reports-customers',
			'followup-emails-queue',
			'followup-emails-subscribers',
		);

		if ( in_array( $page, $fue_pages ) || 'follow_up_email' === $screen->post_type ) {
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'editor' );

			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'thickbox' );

			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'jquery-ui-core', null, array( 'jquery' ) );
			wp_enqueue_script( 'jquery-ui-datepicker', null, array( 'jquery-ui-core' ) );
			wp_enqueue_script( 'jquery-ui-sortable', null, array( 'jquery-ui-core' ) );
			wp_enqueue_script( 'fue-list', plugins_url( 'templates/js/email-list.js', FUE_FILE ), array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), FUE_VERSION );
			wp_enqueue_script( 'raphael', plugins_url( 'templates/js/justgage/raphael.min.js', FUE_FILE ), null, '2.1.0', true );
			wp_enqueue_script( 'justgage', plugins_url( 'templates/js/justgage/justgage.1.1.min.js', FUE_FILE ), null, '1.1', true );

			wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );
			wp_enqueue_style( 'fue_email_form', plugins_url( 'templates/email-form.css', FUE_FILE ) );

			$translate = apply_filters( 'fue_script_locale', array(
				'email_name'            => __( 'Email Name', 'follow_up_emails' ),
				'processing_request'    => __( 'Processing request...', 'follow_up_emails' ),
				'dupe'                  => __( 'A follow-up email with the same settings already exists. Do you want to create it anyway?', 'follow_up_emails' ),
				'similar'               => __( 'A similar follow-up email already exists. Do you wish to continue?', 'follow_up_emails' ),
				'save'                  => isset( $_GET['mode'] ) ? __( 'Save', 'follow_up_emails' ) : __( 'Build your email', 'follow_up_emails' ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'ajax_loader'           => plugins_url() . '/woocommerce-follow-up-emails/templates/images/ajax-loader.gif',
			) );
			wp_localize_script( 'fue-list', 'FUE', $translate );
		}

		if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
			wp_enqueue_style( 'fue_admin_dashboard_styles', plugins_url( 'templates/dashboard.css', FUE_FILE ), array(), FUE_VERSION );

			wp_enqueue_script( 'jsapi', '//www.google.com/jsapi' );
			wp_enqueue_script( 'js-cookie', FUE_TEMPLATES_URL . '/js/js-cookie.js', array(), FUE_VERSION );
			wp_enqueue_script( 'fue-dashboard', FUE_TEMPLATES_URL . '/js/dashboard.js', array( 'justgage', 'jquery-tiptip', 'js-cookie' ), FUE_VERSION );

			wp_enqueue_script( 'raphael', plugins_url( 'templates/js/justgage/raphael.min.js', FUE_FILE ), null, '2.1.0', true );
			wp_enqueue_script( 'justgage', plugins_url( 'templates/js/justgage/justgage.1.1.min.js', FUE_FILE ), null, '1.1', true );

			wp_enqueue_script( 'jquery-tiptip' );
		}

		if ( 'followup-emails-reports' === $page ) {
			wp_enqueue_style( 'fue_admin_report_flags', plugins_url( 'templates/flags.css', FUE_FILE ), array(), FUE_VERSION );
			wp_enqueue_style( 'fue_admin_report_styles', plugins_url( 'templates/reports.css', FUE_FILE ), array(), FUE_VERSION );

			wp_enqueue_script( 'fue-user-view', plugins_url( 'templates/js/user-view.js', FUE_FILE ), array( 'jquery' ), FUE_VERSION );
			wp_enqueue_script( 'jquery-ui-datepicker', null, array( 'jquery' ) );
		}

		if ( in_array( $page, array( 'followup-emails-queue', 'followup-emails-settings', 'followup-emails', 'followup-emails-subscribers', 'followup-emails-reports-customers' ) ) ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'jquery-tiptip' );

			if ( isset( $_GET['tab'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				switch ( $_GET['tab'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					case 'send_manual_emails':
						wp_enqueue_script( 'fue_manual_send', FUE_TEMPLATES_URL . '/js/manual_send.js', array(
							'jquery',
							'jquery-ui-progressbar',
						), FUE_VERSION );
						break;

					case 'send_manual_email_batches':
					case 'data_updater':
						wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
						wp_enqueue_script(
							'fue_data_updater',
							FUE_TEMPLATES_URL . '/js/data-updater.js',
							array( 'jquery', 'jquery-ui-progressbar' ),
							FUE_VERSION
						);
						break;
				}
			}
		}

		if ( 'followup-emails-settings' === $page ) {
			wp_enqueue_script( 'fue_settings', FUE_TEMPLATES_URL . '/js/settings.js', array( 'jquery' ), FUE_VERSION, true );
			wp_enqueue_style( 'fue-settings', FUE_TEMPLATES_URL . '/add-ons/fue-settings.css' );
		}

		if ( 'followup-emails-templates' === $page ) {
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'fue_templates', FUE_TEMPLATES_URL . '/js/templates.js', array( 'jquery' ), FUE_VERSION );

			$translate = apply_filters( 'fue_script_locale', array(
				'ajax_loader'         => plugins_url() . '/woocommerce-follow-up-emails/templates/images/ajax-loader.gif',
				'get_template_nonce'  => wp_create_nonce( 'get_template_html' ),
				'save_template_nonce' => wp_create_nonce( 'save_template_html' ),
			) );
			wp_localize_script( 'fue_templates', 'FUE_Templates', $translate );

			wp_enqueue_style( 'fue-addons', FUE_TEMPLATES_URL . '/add-ons/templates.css' );
		}
	}
}
