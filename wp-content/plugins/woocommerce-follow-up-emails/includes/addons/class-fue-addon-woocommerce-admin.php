<?php

/**
 * Class FUE_Addon_Woocommerce_Admin
 */
class FUE_Addon_Woocommerce_Admin {

	/**
	 * @var FUE_Addon_Woocommerce
	 */
	private $fue_wc;

	/**
	 * Class constructor
	 */
	public function __construct( $fue_wc ) {
		$this->fue_wc = $fue_wc;

		$this->register_hooks();

		include_once 'class-fue-addon-woocommerce-admin-products.php';
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		// initial order import
		add_action( 'admin_notices', array($this, 'order_import_check'), 15 );
		add_action( 'fue_admin_controller', array($this, 'order_import_page') );
		add_action( 'fue_admin_controller', array($this, 'data_update_page') );

		// force order rescan
		add_action( 'admin_post_fue_rescan_orders', array($this, 'scan_customer_orders') );

		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts') );
		add_filter( 'fue_script_locale', array($this, 'create_product_search_nonce') );

		// email list - import order action link
		add_action( 'fue_table_status_actions', array($this, 'add_import_action_link') );
		add_action( 'fue_active_bulk_actions', array($this, 'add_bulk_import_option') );

		add_action( 'fue_execute_bulk_action', array($this, 'execute_bulk_import'), 10, 2 );

		// email forms
		add_action( 'fue_email_form_scripts', array($this, 'email_form_scripts') );

		add_action( 'fue_email_form_settings', array($this, 'signup_email_form_option') );
		add_action( 'fue_email_form_settings', array($this, 'import_orders_form_option') );
		add_action( 'fue_email_form_settings', array($this, 'unqueue_emails_form_option') );
		add_action( 'fue_email_form_after_interval', array($this, 'email_form'), 9, 3 );
		add_action( 'fue_email_form_interval_meta', array($this, 'email_interval_meta') );

		// trigger fields
		add_filter( 'fue_email_form_trigger_fields', array($this, 'register_trigger_fields') );

		add_filter( 'fue_email_details_tabs', array( $this, 'inject_email_details_tabs'), 10, 2 );
		add_action( 'fue_email_form_email_details', array( $this, 'email_form_custom_fields_panel' ) );
		add_action( 'fue_email_form_email_details', array( $this, 'email_form_exclusions_panel' ) );

		add_action( 'fue_email_form_trigger_fields', array($this, 'downloadables_form'), 10 );
		add_action( 'fue_email_form_trigger_fields', array($this, 'coupons_form'), 10 );

		// email form custom fields
		add_action( 'fue_email_form_before_message', array($this, 'custom_fields_form') );

		// importing of existing orders to the email queue
		add_filter( 'fue_after_save_email', array($this, 'schedule_email_order_import') );

		add_action( 'fue_email_created', array($this, 'fix_storewide_type_meta') );
		add_action( 'fue_email_updated', array($this, 'fix_storewide_type_meta') );

		add_action( 'fue_email_created', array($this, 'fix_storewide_type_meta') );
		add_action( 'fue_email_updated', array($this, 'fix_storewide_type_meta') );

		// settings page
		add_action( 'fue_settings_integration', array($this, 'addon_integrations_settings') );
		add_action( 'fue_settings_tools', array($this, 'addon_tools_settings') );
		add_action( 'fue_settings_subscribers', array($this, 'subscribers_settings') );
		add_action( 'fue_settings_saved', array( $this, 'addon_save_settings' ), 10, 1 );
		add_action( 'fue_settings_subscribers_save', array($this, 'addon_save_subscribers_settings') );
		// link from settings page
		add_action( 'fue_settings_email', array($this, 'link_to_addon_settings') );

		// test email field
		add_action( 'fue_test_email_fields', array($this, 'test_email_form') );

		// email form variables list
		add_action( 'fue_email_variables_list', array($this, 'storewide_variables') );
		add_action( 'fue_email_variables_list', array($this, 'product_variables') );
		add_action( 'fue_email_variables_list', array($this, 'customer_variables') );
		add_action( 'fue_email_variables_list', array($this, 'reminder_variables') );
		add_action( 'fue_email_variables_list', array( $this, 'non_manual_variables' ) );

		// Send Manual
		add_action( 'fue_manual_types', array($this, 'manual_types') );
		add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
		add_action( 'fue_manual_js', array($this, 'manual_js') );

		// Add import link to the email update message
		add_filter( 'fue_update_messages', array( $this, 'add_import_link_to_post_message' ) );

		// adds the coupon filtering dropdown to the orders page
		add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_email' ) );

		// makes coupons filterable
		add_filter( 'posts_join',  array( $this, 'add_order_items_join' ) );
		add_filter( 'posts_where', array( $this, 'add_filterable_where' ) );

		// Order items
		add_action( 'woocommerce_before_delete_order_item', array( $this, 'order_item_deleted' ) );
		add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'order_item_added' ), 10, 2 );
	}

	/**
	 * Check if we need to import orders
	 */
	public function order_import_check() {
		if (
			!get_option('fue_orders_imported', false)   &&
			!get_transient( 'fue_importing_orders')     &&
			!get_option('fue_disable_order_scan', false)&&
			( empty( $_GET['tab'] ) || $_GET['tab'] !== 'order_import' ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			?>
			<div id="message" class="updated">
				<p><strong><?php esc_html_e( 'Initial order scanning is required to accurately send conditional emails', 'follow_up_emails' ); ?></strong></p>
				<p class="submit">
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'order_import', admin_url( 'admin.php?page=followup-emails' ) ) ); ?>" class="fue-update-now button-primary"><?php esc_html_e( 'Scan Orders', 'follow_up_emails' ); ?></a>
					<?php esc_html_e('or', 'follow_up_emails'); ?> <a href="#" class="fue-disable-scan"><?php esc_html_e("don't show this again", 'follow_up_emails'); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				jQuery( '.fue-update-now' ).on( 'click', function() {
					var answer = confirm( '<?php esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the scanner now?', 'follow_up_emails' ) ); ?>' );
					return answer;
				} );

				jQuery('.fue-disable-scan').on( 'click', function() {
					var container   = jQuery(this).parents("div#message");
					var message     = '<?php esc_js( __(' Not scanning your existing orders may cause customer and conditional emails to not work accurately. Do you wish to continue?', 'follow_up_emails' ) ); ?>';

					if ( confirm( message ) ) {
						post = {
							action: "fue_wc_disable_order_scan"
						};
						jQuery.getJSON( ajaxurl, post, function(resp) {
							if ( resp && resp.status == 'success' ) {
								container.remove();
							}
						} );
					}

					return false;
				} );
			</script>
		<?php
		}
	}

	/**
	 * UI for importing existing orders via AJAX to avoid script timeout
	 * @param string $tab
	 */
	public function order_import_page( $tab ) {
		if ( $tab == 'order_import' ) {
			include FUE_TEMPLATES_DIR .'/order-import.php';
		}
	}

	/**
	 * UI for updating data via AJAX to avoid script timeout
	 * @param string $tab
	 */
	public function data_update_page( $tab ) {
		if ( $tab == 'data_update' ) {
			$act = empty( $_GET['act'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['act'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $act ) ) {
				wp_die( esc_html__('No action specified', 'follow_up_emails' ) );
			}
			$return = admin_url( 'admin.php?page=followup-emails' );
			if ( ! empty( $_GET['ref'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$return = esc_url_raw( admin_url( sanitize_text_field( wp_unslash( $_GET['ref'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
			if ( $act == 'sum_orders' ) {
				$args = array(
					'page_title'            => 'Data Update',
					'return_url'            => $return,
					'ajax_endpoint'         => 'fue_wc_update_customer_order_total',
					'entity_label_singular' => 'order',
					'entity_label_plural'   => 'orders',
					'action_label'          => 'updated'
				);
			}
			include FUE_TEMPLATES_DIR .'/data-updater.php';
		}
	}

	/**
	 * Reset customer data and redirect admin to the update page
	 */
	public function scan_customer_orders() {
		delete_option( 'fue_orders_imported' );
		delete_transient( 'fue_importing_orders' );
		update_option( 'fue_disable_order_scan', false );

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_fue_recorded'");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_customers");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_customer_notes");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_customer_orders");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_order_categories");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_order_items");

		wp_safe_redirect( 'admin.php?page=followup-emails&tab=order_import');
		exit;
	}

	/**
	 * Register styles and scripts used in rendering the Admin UI
	 */
	public function admin_scripts() {

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $page == 'followup-emails' || $page == 'followup-emails-settings' || $page == 'followup-emails-queue' ) {
			wp_enqueue_script( 'fue-select', plugins_url( 'templates/js/fue-select.js', FUE_FILE ), array( 'jquery', 'select2' ), FUE_VERSION );

			wp_enqueue_style('select2');
			wp_enqueue_script( 'fue-queue', FUE_TEMPLATES_URL .'/js/queue.js', array('jquery'), FUE_VERSION );

			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script('farbtastic');
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete', null, array('jquery-ui-core') );

			?>
			<style type="text/css">
				.chzn-choices li.search-field .default {
					width: auto !important;
				}
				select option[disabled] {display:none;}
			</style>
			<?php

			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );

			if ( ! empty( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( $_GET['tab'] === 'order_import' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
					wp_enqueue_script( 'fue_wc_order_import', FUE_TEMPLATES_URL .'/js/wc_order_import.js', array('jquery', 'jquery-ui-progressbar'), FUE_VERSION );
				}
				if ( $_GET['tab'] === 'data_update' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
					wp_enqueue_script( 'fue_data_updater', FUE_TEMPLATES_URL .'/js/data-updater.js', array('jquery', 'jquery-ui-progressbar'), FUE_VERSION );
				}
			}

		} elseif ( $page == 'followup-emails-form' || $page == 'followup-emails-reports' ) {

			if ( $page == 'followup-emails-form' ) {
				wp_enqueue_script( 'fue-form-woocommerce', plugins_url( 'templates/js/email-form-woocommerce.js', FUE_FILE ), array('jquery'), FUE_VERSION );
			}

			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'fue-select', plugins_url( 'templates/js/fue-select.js', FUE_FILE ), array( 'jquery', 'select2' ), FUE_VERSION );

			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script('farbtastic');
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-core', null, array('jquery') );
			wp_enqueue_script( 'jquery-ui-datepicker', null, array('jquery-ui-core') );
			wp_enqueue_script( 'jquery-ui-autocomplete', null, array('jquery-ui-core') );

			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );
		}

		$screen = get_current_screen();
		if ( $screen->id == 'follow_up_email' ) {
			wp_enqueue_script( 'fue-form-woocommerce', plugins_url( 'templates/js/email-form-woocommerce.js', FUE_FILE ), array('jquery'), FUE_VERSION );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_script( 'fue-select', plugins_url( 'templates/js/fue-select.js', FUE_FILE ), array( 'jquery', 'select2' ), FUE_VERSION );
		} elseif ( $screen->id == 'product' ) {
			wp_enqueue_style( 'fue_wc_admin', FUE_TEMPLATES_URL .'/add-ons/woocommerce.css' );
		}
	}

	/**
	 * Generate an nonce to be injected to the FUE JS variable for searching products
	 *
	 * @param array $translation
	 * @return array
	 */
	public function create_product_search_nonce( $translation ) {
		$translation['search_customers_nonce']  = wp_create_nonce('search-customers');
		$translation['nonce']                   = wp_create_nonce("search-products");

		return $translation;
	}

	/**
	 * add_import_action_link method
	 * @param FUE_Email $email
	 */
	public function add_import_action_link( $email ) {
		if ( $email->import_order_flag == 1 ) {
			echo '| <small><a href="'. esc_url( $this->get_email_import_url( $email->id ) ) .'" class="email-import-orders" data-id="' . esc_attr( $email->id ) .'">'. esc_html__('Import Orders', 'follow_up_emails') .'</a></small>';
		}
	}

	/**
	 * Get the URL of the import page for the $email
	 *
	 * @param int|array $email_id
	 * @return string
	 */
	public function get_email_import_url( $email_id ) {
		$args = array(
			'tab'   => 'order_import',
			'email' => $email_id,
			'ref'   => urlencode( admin_url( 'admin.php?page=followup-emails' ) )
		);

		return add_query_arg( $args, admin_url( 'admin.php?page=followup-emails' ) );
	}

	/**
	 * Add the 'Import Orders' action in the bulk update dropdown for WC email types
	 *
	 * @param FUE_Email_Type $type
	 * @since 4.0
	 */
	public function add_bulk_import_option( $type ) {

		$supported_types = apply_filters( 'fue_import_orders_supported_types', array( 'storewide', 'customer' ) );

		if ( ! in_array( $type, $supported_types ) ) {
			return;
		}

		?>
		<option value="import"><?php esc_html_e( 'Import Orders', 'follow_up_emails' ); ?></option>
	<?php
	}

	/**
	 * Execute bulk import on the selected emails
	 *
	 * @param string    $action
	 * @param array     $emails
	 * @since 4.0
	 */
	public function execute_bulk_import( $action, $emails ) {

		if ( $action != 'import' ) {
			return;
		}

		$url = $this->get_email_import_url( $emails );

		wp_safe_redirect( $url );
		exit;

	}

	/**
	 * Enqueue styles and scripts for the email form
	 * @since 4.1
	 */
	public function email_form_scripts() {
		wp_enqueue_script( 'fue-form-woocommerce', plugins_url( 'templates/js/email-form-woocommerce.js', FUE_FILE ), array('jquery'), FUE_VERSION );
	}

	/**
	 * Signup email form option
	 * @param FUE_Email $email
	 */
	public function signup_email_form_option( $email ) {
		if ( $email->type != 'signup' )
			return;

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/signup-options.php';
	}

	/**
	 * Option to allow admin to import existing orders that matches the email's criteria
	 * @param FUE_Email $email
	 */
	public function import_orders_form_option( $email ) {
		$supported_types = apply_filters( 'fue_import_orders_supported_types', array('storewide', 'customer' ) );

		if ( !in_array( $email->type, $supported_types ) ) {
			return;
		}

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/import-orders-option.php';
	}

	/**
	 * Add the 'Remove emails on status change' option
	 * @param FUE_Email $email
	 */
	public function unqueue_emails_form_option( $email ) {
		$supported_types = apply_filters( 'fue_import_orders_supported_types', array('storewide', 'customer' ) );

		if ( !in_array( $email->type, $supported_types ) ) {
			return;
		}

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/remove-emails-status-option.php';
	}

	/**
	 * Set a flag to import existing orders that match the email after the email is created/activated
	 *
	 * The flag will be set if the following conditions are met:
	 * - $data['meta']['import_order'] is set to 'yes'
	 * - FUE_Email->meta['import_order'] is not 'yes'
	 * - postmeta _imported_order is not 'yes'
	 *
	 * @param array $data
	 * @param array $post
	 * @return array
	 */
	public function schedule_email_order_import( $data ) {

		if ( empty( $data['meta']['import_orders'] ) || $data['meta']['import_orders'] != 'yes' ) {
			return $data;
		}

		$email = new FUE_Email( $data['ID'] );

		if ( !$email->exists() ) {
			return $data;
		}

		if ( !empty( $email->imported_order ) && $email->imported_order == 'yes' ) {
			return $data;
		}

		update_post_meta( $email->id, '_import_order_flag', true );

		return $data;

	}

	/**
	 * Toggle the correct value of the 'storewide_type' meta depending on whether or not
	 * a product or category ID has been selected
	 *
	 * @since 4.0
	 * @param int   $email_id
	 */
	public function fix_storewide_type_meta( $email_id ) {
		$email  = new FUE_Email( $email_id );
		$type   = 'all';

		if ( !empty( $email->product_id ) ) {
			$type = 'products';
		} elseif ( !empty( $email->category_id ) ) {
			$type = 'categories';
		}

		$meta = maybe_unserialize( $email->meta );

		if ( !$meta ) {
			$meta = array();
		}

		$meta['storewide_type'] = $type;

		update_post_meta( $email_id, '_meta', $meta );
	}

	/**
	 * Insert WC fields into the email form
	 *
	 * @param FUE_Email $email
	 */
	public function email_form( $email ) {
		$types = apply_filters('fue_wc_form_products_selector_email_types', array('storewide', 'reminder', 'customer') );
		if ( !in_array($email->type, $types) ) {
			return;
		}

		// load the categories
		$categories     = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC', 'hide_empty' => false ) );
		$has_variations = (!empty($email->product_id) && FUE_Addon_Woocommerce::product_has_children($email->product_id)) ? true : false;
		$storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/email-form.php';

	}

	/**
	 * Insert interval fields that are unique to WC emails
	 * @param FUE_Email $email
	 */
	public function email_interval_meta( $email ) {
		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/interval-fields.php';
	}

	/**
	 * Add course selector to the Trigger tab
	 *
	 * @param FUE_Email $email
	 */
	public function register_trigger_fields( $email ) {

		if ( in_array( $email->type, apply_filters('fue_wc_form_products_selector_email_types', array('storewide', 'reminder', 'customer') ) ) ) {
			// load the categories
			$categories     = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
			$has_variations = (!empty($email->product_id) && FUE_Addon_Woocommerce::product_has_children($email->product_id)) ? true : false;
			$storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';

			include FUE_TEMPLATES_DIR .'/email-form/woocommerce/email-form.php';
		}
	}

	/**
	 * Add the Exclusions tab to the Email Details panel
	 * @param array $tabs
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	public function inject_email_details_tabs( $tabs, $email ) {
		$insert = array (
			'custom_fields' => array(
				'label'  => __( 'Custom Fields', 'follow_up_emails' ),
				'icon'   => 'dashicons-admin-appearance',
				'target' => 'custom_fields_details',
				'class'  => array('custom-fields'),
			),
			'coupons' => array(
				'label'  => __( 'Coupons', 'follow_up_emails' ),
				'icon'   => 'dashicons-tickets-alt',
				'target' => 'coupons_details',
				'class'  => array('wc-coupons'),
			)
		);

		if ( $email->type == 'storewide' || $email->type == 'wc_bookings' ) {
			$insert['exclusions'] = array(
				'label'  => __( 'Exclusions', 'follow_up_emails' ),
				'icon'   => 'dashicons-dismiss',
				'target' => 'exclusions_details',
				'class'  => array('show-if-storewide'),
			);
		}

		array_splice( $tabs, 2, 0, $insert );

		return $tabs;
	}

	/**
	 * Render the Exclusion panel in the email form
	 * @param FUE_Email $email
	 */
	public function email_form_exclusions_panel( $email ) {
		?>
		<div id="exclusions_details" class="panel fue_panel">
			<?php $this->excluded_categories_form( $email ); ?>
			<?php $this->exclude_customers_form( $email ); ?>
		</div>
		<?php
	}

	/**
	 * Render the Custom Fields panel in the email form
	 * @param FUE_Email $email
	 */
	public function email_form_custom_fields_panel( $email ) {
		?>
		<div id="custom_fields_details" class="panel fue_panel">
			<?php $this->custom_fields_form( $email ); ?>
		</div>
		<?php
	}

	/**
	 * Select box for selecting a downloadable file
	 * @param $email
	 */
	public function downloadables_form( $email ) {
		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/downloadables.php';
	}

	/**
	 * Select box for selecting a coupon
	 * @param $email
	 */
	public function coupons_form( $email ) {
		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/coupons-selector.php';
	}

	/**
	 * Select box for selecting excluded categories
	 *
	 * @param FUE_Email $email
	 */
	public function excluded_categories_form( $email ) {

		// load the categories
		$categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/excluded-categories.php';
	}

	/**
	 * Form to exclude sending email to customers who have previously purchased the
	 * selected products or from the selected categories
	 *
	 * @param FUE_Email $email
	 */
	public function exclude_customers_form( $email ) {
		// load the categories
		$categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/excluded-customers.php';
	}

	/**
	 * Custom post meta selector
	 *
	 * @param FUE_Email $email
	 */
	public function custom_fields_form( $email ) {

		if ( $email->type == 'storewide' ):
			$use_custom_field = (isset($email->meta['use_custom_field'])) ? $email->meta['use_custom_field'] : 0;

			include FUE_TEMPLATES_DIR .'/email-form/woocommerce/custom-fields.php';
		endif;
	}

	/**
	 * Add a link in the Permissions and Styling tab to the new WC addon settings
	 */
	public function link_to_addon_settings() {
		?>
		<h3><?php esc_html_e('WooCommerce Email Settings', 'follow_up_email'); ?></h3>
		<a href="admin.php?page=followup-emails-settings&tab=integration"><?php esc_html_e('Click here to manage your WooCommerce email styles', 'follow_up_emails'); ?></a>
		<?php
	}

	/**
	 * Add a settings block specifically for FUE WC
	 */
	public function addon_integrations_settings() {
		include FUE_TEMPLATES_DIR .'/settings/settings-woocommerce-integrations.php';
	}

	/**
	 * Add a settings block for the Tools settings tab
	 */
	public function addon_tools_settings() {
		include FUE_TEMPLATES_DIR .'/settings/settings-woocommerce-tools.php';
	}

	/**
	 * Add a settings block specifically for FUE WC
	 */
	public function subscribers_settings() {
		include FUE_TEMPLATES_DIR .'/settings/settings-woocommerce-subscribers.php';
	}

	/**
	 * Save addon settings
	 */
	public function addon_save_settings( $post ) {
		$post = array_map( 'sanitize_text_field', wp_unslash( $post ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.

		if ( $post['section'] == 'integration' ) {
			// disable email wrapping
			$disable = ( isset ($post['disable_email_wrapping'] ) ) ? (int) $post['disable_email_wrapping'] : 0;
			update_option( 'fue_disable_wrapping', $disable );

			$conversion = (isset($post['wc_conversion_days'])) ? absint($post['wc_conversion_days']) : 14;
			update_option( 'fue_wc_conversion_days', $conversion );

			$abandoned_cart_value   = (isset($post['wc_abandoned_cart_value'])) ? absint($post['wc_abandoned_cart_value']) : 3;
			$abandoned_cart_unit    = (isset($post['wc_abandoned_cart_unit'])) ? sanitize_text_field( wp_unslash( $post['wc_abandoned_cart_unit'] ) ) : 'hours';
			update_option( 'fue_wc_abandoned_cart_value', $abandoned_cart_value );
			update_option( 'fue_wc_abandoned_cart_unit', $abandoned_cart_unit );

		}

	}

	/**
	 * Save addon subscribers settings
	 *
	 * @param array $post
	 */
	public function addon_save_subscribers_settings( $post ) {
		// checkout subscription.
		$enable_checkout_subscription = ( isset( $post['enable_checkout_subscription'] ) ) ? (int) $post['enable_checkout_subscription'] : 0;
		$enable_account_subscription  = ( isset( $post['enable_account_subscription'] ) ) ? (int) $post['enable_account_subscription'] : 0;

		$checkout_label   = $post['checkout_subscription_field_label'];
		$checkout_list    = $post['checkout_subscription_list'];
		$checkout_default = $post['checkout_subscription_default'];

		update_option( 'fue_enable_checkout_subscription', $enable_checkout_subscription );
		update_option( 'fue_enable_account_subscription', $enable_account_subscription );
		update_option( 'fue_checkout_subscription_field_label', $checkout_label );
		update_option( 'fue_checkout_subscription_list', $checkout_list );
		update_option( 'fue_checkout_subscription_default', $checkout_default );
	}

	/**
	 * Allow admin to simulate an email using real orders or products
	 * @param FUE_Email $email
	 */
	public function test_email_form( $email ) {
		if ($email->type == 'storewide') {
			include FUE_TEMPLATES_DIR .'/email-form/woocommerce/test-fields-storewide.php';
		}
	}

	/**
	 * Storewide Email Variables
	 * @param FUE_Email $email
	 */
	public function storewide_variables( $email ) {
		if ($email->type !== 'storewide') return;

		?>
		<li class=""><strong>{item_names}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of purchased items.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_names_list}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a comma-separated list of purchased items.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_prices}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of purchased items, quantities, and prices.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_prices_image}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of purchased items, quantities, and prices with thumbnails.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_codes_prices}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of the purchased items with their quantities, codes, and prices.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_prices_categories}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of the purchased items, with their quantities, prices, and categories.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_categories}</strong> <img class="help_tip" title="<?php esc_attr_e('The list of categories where the purchased items are under.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{dollars_spent_order}</strong> <img class="help_tip" title="<?php esc_attr_e('The the amount spent on an order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php

		if ( in_array( $email->trigger, array( 'refund_manual', 'refund_successful' ), true ) ) :
			?>
			<li class=""><strong>{refund_amount}</strong> <img class="help_tip" title="<?php esc_attr_e('The amount of the refund', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
			<li class=""><strong>{refund_reason}</strong> <img class="help_tip" title="<?php esc_attr_e('The reason for the refund', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php
		endif;
	}

	/**
	 * Product Variables
	 * @param FUE_Email $email
	 */
	public function product_variables( $email ) {
		if ( $email->type !== 'storewide' || empty($email->product_id) ) {
			return;
		}
		?>
		<li class="var hideable"><strong>{download_url}</strong>  <img class="help_tip" title="<?php esc_attr_e('The URL of the downloadable file.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable"><strong>{download_filename}</strong>  <img class="help_tip" title="<?php esc_attr_e('The name of the downloadable file.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_code}</strong> <img class="help_tip" title="<?php esc_attr_e('The code number of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL of the purchased item. Only works when the product is specified in the trigger setting.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_category}</strong> <img class="help_tip" title="<?php esc_attr_e('The list of categories where the purchased item is under.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_price}</strong> <img class="help_tip" title="<?php esc_attr_e('The price of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{item_quantity}</strong> <img class="help_tip" title="<?php esc_attr_e('The quantity of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{dollars_spent_order}</strong> <img class="help_tip" title="<?php esc_attr_e('The the amount spent on an order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<?php
	}

	/**
	 * Customer Variables
	 * @param FUE_Email $email
	 */
	public function customer_variables( $email ) {
		if ($email->type !== 'customer') return;

		?>
		<li class=""><strong>{amount_spent_order}</strong> <img class="help_tip" title="<?php esc_attr_e('The the amount spent on an order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{amount_spent_total}</strong> <img class="help_tip" title="<?php esc_attr_e('Total amount spent by the customer', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{number_orders}</strong> <img class="help_tip" title="<?php esc_attr_e('Total amount spent by the customer', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{last_purchase_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The date the customer last ordered', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<?php
	}

	/**
	 * Reminder Variables
	 * @param FUE_Email $email
	 */
	public function reminder_variables( $email ) {
		if ($email->type !== 'reminder') return;
		?>
		<li class=""><strong>{first_email}...{/first_email}</strong> <img class="help_tip" title="<?php esc_attr_e('The first email description...', 'wc_followup_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{quantity_email}...{/quantity_email}</strong> <img class="help_tip" title="<?php esc_attr_e('The quantity email description...', 'wc_followup_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class=""><strong>{final_email}...{/final_email}</strong> <img class="help_tip" title="<?php esc_attr_e('The last email description...', 'wc_followup_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<?php
	}

	/**
	 * Variables for all email types except manual
	 * @param FUE_Email $email
	 */
	public function non_manual_variables( $email ) {
		if ( 'manual' === $email->type ) {
			return;
		}
		?>
		<li class="var hideable var_order var_order_number non-signup"><strong>{order_number}</strong> <img class="help_tip" title="<?php esc_attr_e('The generated Order Number for the puchase', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_date non-signup"><strong>{order_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The date that the order was made', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_datetime non-signup"><strong>{order_datetime}</strong> <img class="help_tip" title="<?php esc_attr_e('The date and time that the order was made', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_subtotal non-signup"><strong>{order_subtotal}</strong> <img class="help_tip" title="<?php esc_attr_e('The subtotal of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_tax non-signup"><strong>{order_tax}</strong> <img class="help_tip" title="<?php esc_attr_e('The tax total of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_pay_method non-signup"><strong>{order_pay_method}</strong> <img class="help_tip" title="<?php esc_attr_e('The payment method used to pay for the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_pay_url non-signup"><strong>{order_pay_url}</strong> <img class="help_tip" title="<?php esc_attr_e('URL for customer to pay their (unpaid - pending) order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_billing_address non-signup"><strong>{order_billing_address}</strong> <img class="help_tip" title="<?php esc_attr_e('The billing address of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_shipping_address non-signup"><strong>{order_shipping_address}</strong> <img class="help_tip" title="<?php esc_attr_e('The shipping address of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_billing_phone non-signup"><strong>{order_billing_phone}</strong> <img class="help_tip" title="<?php esc_attr_e('The billing phone of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_order var_order_shipping_phone non-signup"><strong>{order_shipping_phone}</strong> <img class="help_tip" title="<?php esc_attr_e('The shipping phone of the order', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<?php
	}

	/**
	 * Additional recipient options for manual emails
	 */
	public function manual_types() {
		$options = array(
			'users'     => __('All Users', 'follow_up_emails'),
			'storewide' => __('All Customers', 'follow_up_emails'),
			'customer'  => __('This Customer', 'follow_up_emails'),
			'product'   => __('Customers who bought these products', 'follow_up_emails'),
			'category'  => __('Customers who bought from these categories', 'follow_up_emails'),
			'timeframe' => __('Customers who bought between these dates', 'follow_up_emails'),
			'not_product'   => __('Customers who never bought these products', 'follow_up_emails')
		);

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/manual-email-types.php';

	}

	/**
	 * The actions for the additional manual email options
	 * @param FUE_Email $email
	 */
	public function manual_type_actions($email) {
		$categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );

		include FUE_TEMPLATES_DIR .'/email-form/woocommerce/manual-email-actions.php';
	}

	/**
	 * Inline JS for sending manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "customer":
					jQuery(".send-type-customer").show();
					break;

				case "product":
				case "not_product":
					jQuery(".send-type-product").show();
					break;

				case "category":
					jQuery(".send-type-category").show();
					break;

				case "timeframe":
					jQuery(".send-type-timeframe").show();
					break;
			}
		} );

		init_fue_product_search();
		init_fue_select();
		init_fue_customer_search();
	<?php
	}

	/**
	 * Add a link to the import page after an email is saved with the import flag checked
	 *
	 * @param array $messages
	 * @return array
	 * @since 4.0
	 */
	public function add_import_link_to_post_message( $messages ) {
		$post = get_post();

		if ( $post->post_type != 'follow_up_email' ) {
			return $messages;
		}

		$import = get_post_meta( $post->ID, '_import_order_flag', true );

		if ( $import ) {
			$url = $this->get_email_import_url( $post->ID );

			$messages[1] = __( 'Your email has been saved. You chose to import existing orders. Please click this button to initiate a scan of your orders to schedule emails. <a href="'. esc_url( $url ) .'" class="button">Import existing orders</a>', 'follow_up_emails' );
		}

		return $messages;
	}

	/**
	 * Adds the email filtering dropdown to the orders list
	 */
	public function filter_orders_by_email() {
		global $typenow;

		if ( 'shop_order' != $typenow ) {
			return;
		}

		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_type'        => 'follow_up_email',
			'post_status'      => array('fue-active', 'fue-inactive', 'fue-archived'),
			'meta_query'        => array(
				array(
					'key'       => '_interval_type',
					'value'     => 'cart',
					'compare'   => '!='
				)
			)
		);

		$emails = get_posts( $args );

		if ( ! empty( $emails ) ) {
			?>

			<select name="_email_id" id="dropdown_emails_used">
				<option value=""><?php esc_html_e( 'Filter by emails sent/queued', 'follow_up_emails' ); ?></option>
				<?php foreach ( $emails as $email ) : ?>
					<option value="<?php echo esc_attr( $email->ID) ; ?>">
						<?php echo esc_html( $email->post_title ) ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php }
	}

	/**
	 * Modify SQL JOIN for filtering the orders by any emails sent
	 *
	 * @param string $join JOIN part of the sql query
	 * @return string $join modified JOIN part of sql query
	 */
	public function add_order_items_join( $join ) {

		global $typenow, $wpdb;

		if ( 'shop_order' != $typenow ) {
			return $join;
		}

		if ( ! empty( $_GET['_email_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$join .=  "
				LEFT JOIN {$wpdb->prefix}followup_email_orders eo ON {$wpdb->posts}.ID = eo.order_id";
		}

		return $join;
	}



	/**
	 * Modify SQL WHERE for filtering the orders by any emails used
	 *
	 * @param string $where WHERE part of the sql query
	 * @return string $where modified WHERE part of sql query
	 */
	public function add_filterable_where( $where ) {
		global $typenow, $wpdb;

		if ( 'shop_order' != $typenow ) {
			return $where;
		}

		if ( ! empty( $_GET['_email_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// Main WHERE query part
			$where .= $wpdb->prepare( " AND eo.email_id=%d", fue_clean( wp_unslash( $_GET['_email_id'] ) ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return $where;
	}

	/**
	 * Update the followup_order_items table when order items get deleted
	 *
	 * @param int $item_id
	 */
	public function order_item_deleted( $item_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$order_id = $wpdb->get_var($wpdb->prepare(
			"SELECT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_id = %d",
			$item_id
		));

		if ( $order_id ) {
			$product_id     = wc_get_order_item_meta( $item_id, '_product_id', true );
			$variation_id   = wc_get_order_item_meta( $item_id, '_variation_id', true );

			$wpdb->query($wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}followup_order_items
				WHERE order_id = %d
				AND product_id = %d
				AND variation_id = %d",
				$order_id,
				$product_id,
				$variation_id
			));
		}
	}

	public function order_item_added( $item_id, $item ) {
		$wpdb               = Follow_Up_Emails::instance()->wpdb;
		$order_categories   = array();

		$order_id = $wpdb->get_var($wpdb->prepare(
			"SELECT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_id = %d",
			$item_id
		));

		$insert = array(
			'order_id'     => $order_id,
			'product_id'   => $item['product_id'],
			'variation_id' => $item['variation_id']
		);
		$wpdb->insert( $wpdb->prefix . 'followup_order_items', $insert );

		// get the categories
		$cat_ids = wp_get_post_terms( $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

		if ( $cat_ids ) {
			foreach ( $cat_ids as $cat_id ) {
				$order_categories[] = $cat_id;
			}
		}

		$order_categories = array_unique($order_categories);

		foreach ( $order_categories as $category_id ) {
			$insert = array(
				'order_id'      => $order_id,
				'category_id'   => $category_id
			);
			$wpdb->insert( $wpdb->prefix .'followup_order_categories', $insert );
		}

	}

}
