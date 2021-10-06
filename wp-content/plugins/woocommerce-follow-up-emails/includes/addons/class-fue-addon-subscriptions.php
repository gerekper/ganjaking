<?php

/**
 * Class FUE_Addon_Subscriptions
 */
class FUE_Addon_Subscriptions extends FUE_Addon_Woocommerce_Scheduler {

	/**
	 * class constructor
	 */
	public function __construct() {
		if ( self::is_installed() ) {

			// update notice
			add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
			add_action( 'fue_admin_controller', array($this, 'subscription_update_page') );
			add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts') );

			// subscriptions integration
			add_filter( 'fue_email_types', array($this, 'register_email_type') );

			// trigger fields
			add_filter( 'fue_email_form_trigger_fields', array($this, 'register_trigger_fields') );

			// saving email
			add_filter( 'fue_save_email_data', array($this, 'apply_subscription_product_id'), 10, 3 );

			// manual emails
			add_action( 'fue_manual_types', array($this, 'manual_types') );
			add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
			add_action( 'fue_manual_js', array($this, 'manual_js') );

			add_action( 'fue_email_variables_list', array($this, 'email_variables_list') );

			if ( self::is_wcs_2() ) {
				$v2 = FUE_Addon_Subscriptions_V2::instance();

				// Migration task from v1.5 to v2
				add_action( 'admin_init', array($v2, 'set_subscription_data_update_flag') );

				add_filter( 'fue_manual_email_recipients', array($v2, 'manual_email_recipients'), 10, 2 );

				// test email field
				add_action( 'fue_test_email_fields', array($v2, 'test_email_form') );

				add_action( 'woocommerce_subscription_status_updated', array($v2, 'trigger_subscription_status_emails'), 10, 3 );
				add_action( 'woocommerce_subscription_date_updated', array($v2, 'update_reminder_dates'), 10, 3 );

				add_action( 'woocommerce_order_status_changed', array( $v2, 'remove_subscription_renewal_payment_failed_email' ), 10, 3 );
				add_action( 'woocommerce_order_status_changed', array( $v2, 'remove_subscription_renewal_order_created_email' ), 10, 3 );
				add_filter( 'wcs_renewal_order_created', array($v2, 'subscription_renewal_order_created'), 10, 2 );

				add_action( 'woocommerce_subscription_payment_failed', array($v2, 'subscription_payment_failed'), 10, 2 );
				add_action( 'woocommerce_subscription_status_updated', array($v2, 'remove_subscription_payment_failed_email'), 10, 3 );
				add_action( 'woocommerce_subscription_payment_complete', array($v2, 'set_renewal_reminder'), 10 );
				add_action( 'woocommerce_subscription_payment_complete', array($v2, 'set_expiration_reminder'), 11 );

				add_action( 'fue_before_variable_replacements', array($v2, 'register_variable_replacements'), 11, 4 );

				add_filter( 'fue_wc_get_orders_for_email', array($v2, 'get_orders_for_email'), 10, 2 );
				add_filter( 'fue_wc_filter_orders_for_email', array($v2, 'filter_orders_for_email'), 10, 2 );
				add_filter( 'fue_wc_import_insert', array($v2, 'add_subscription_id_to_meta'), 1, 2 );
				add_filter( 'fue_wc_import_insert', array($v2, 'set_followup_send_date'), 2, 2 );

				// listen for payment failures
				add_action( 'woocommerce_subscription_payment_failed', array($v2, 'payment_failed_for_subscription') );

				add_filter( 'fue_skip_email_sending', array($v2, 'skip_sending_if_status_changed'), 10, 3 );
				add_filter( 'fue_skip_email_sending', array($v2, 'skip_sending_if_reminder_changed'), 10, 3 );
			} else {
				add_filter( 'fue_manual_email_recipients', array($this, 'manual_email_recipients'), 10, 2 );

				// test email field
				add_action( 'fue_test_email_fields', array($this, 'test_email_form') );

				add_action( 'activated_subscription', array($this, 'subscription_activated'), 10, 2 );
				add_action( 'cancelled_subscription', array($this, 'subscription_cancelled'), 10, 2 );
				add_action( 'subscription_expired', array($this, 'subscription_expired'), 10, 2 );
				add_action( 'reactivated_subscription', array($this, 'subscription_reactivated'), 10, 2 );
				add_action( 'suspended_subscription', array($this, 'suspended_subscription'), 10, 2 );
				add_action( 'activated_subscription', array($this, 'set_renewal_reminder'), 10, 2 );
				add_action( 'activated_subscription', array($this, 'set_expiration_reminder'), 11, 2 );

				add_action( 'processed_subscription_payment', array($this, 'set_renewal_reminder'), 10, 2 );
				add_action( 'processed_subscription_payment', array($this, 'set_expiration_reminder'), 11, 2 );

				add_action( 'woocommerce_subscriptions_renewal_order_created', array($this, 'subscription_renewal_order_created'), 10, 3 );

				add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 10, 4 );

				add_filter( 'fue_wc_get_orders_for_email', array($this, 'get_orders_for_email'), 10, 2 );

				// listen for payment failure events
				add_action( 'processed_subscription_payment_failure_for_order', array($this, 'payment_failed_for_order') );

				add_filter( 'fue_skip_email_sending', array($this, 'skip_sending_if_status_changed'), 10, 3 );
				add_filter( 'fue_skip_email_sending', array($this, 'skip_sending_if_reminder_changed'), 10, 3 );
			}

			add_action( 'fue_email_form_scripts', array($this, 'email_form_script') );

			add_filter( 'fue_send_email_data', array($this, 'get_email_address_to_send'), 10, 3 );

			// settings page
			add_action( 'fue_settings_integration', array($this, 'settings_form') );
			add_action( 'fue_settings_saved', array( $this, 'save_settings' ), 10, 1 );

			// listen for payment date changes
			add_filter( 'woocommerce_subscription_set_next_payment_date', array($this, 'payment_date_changed'), 10, 4 );

			// Order Importer
			add_filter( 'fue_import_orders_supported_types', array($this, 'add_subscription_to_import_orders') );
			add_filter( 'fue_wc_import_insert', array($this, 'modify_insert_send_date'), 10, 2 );

		}
	}

	/**
	 * Register admin notices
	 */
	public function add_notices() {
		if ( get_option( 'fue_subscription_needs_update' ) == 1 ) {
			add_action( 'admin_notices', array( $this, 'update_notice' ) );
		}
	}

	/**
	 * Display a notice requiring a data update
	 */
	public function update_notice() {
		// If we need to update, include a message with the update button
		if ( get_option( 'fue_subscription_needs_update' ) == 1 ) {
			?>
			<div id="message" class="updated">
				<p><?php esc_html_e( '<strong>Follow-Ups Data Update Required</strong>', 'follow_up_emails' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'tab', 'subscription_update', admin_url( 'admin.php?page=followup-emails' ) ) ); ?>" class="fue-update-now button-primary"><?php esc_html_e( 'Run the updater', 'follow_up_emails' ); ?></a></p>
			</div>
			<script type="text/javascript">
				jQuery( '.fue-update-now' ).on( 'click', function() {
					var answer = confirm( '<?php esc_html_e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'follow_up_emails' ); ?>' );
					return answer;
				} );
			</script>
		<?php
		}
	}

	/**
	 * UI for update subscriptions to v2.0 via AJAX to avoid script timeout
	 * @param string $tab
	 */
	public function subscription_update_page( $tab ) {
		if ( $tab == 'subscription_update' ) {
			include FUE_TEMPLATES_DIR .'/subscriptions-update.php';
		}
	}

	public function admin_scripts() {
		if ( !empty( $_GET['tab'] ) && $_GET['tab'] === 'subscription_update' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
			wp_enqueue_script( 'fue_wc_subscriptions_updater', FUE_TEMPLATES_URL .'/js/wc_subscriptions_updater.js', array('jquery', 'jquery-ui-progressbar'), FUE_VERSION );
		}
	}

	/**
	 * Check if Subscriptions is installed
	 *
	 * @return bool
	 */
	public static function is_installed() {
		return ( class_exists( 'WC_Subscriptions' ) );
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {

		$triggers = array(
			'subs_activated'        => __('after subscription activated', 'follow_up_emails'),
			'subs_renewed'          => __('after subscription renewed', 'follow_up_emails'),
			'subs_cancelled'        => __('after subscription cancelled', 'follow_up_emails'),
			'subs_pending_cancel'   => __('after subscription pending cancellation', 'follow_up_emails'),
			'subs_expired'          => __('after subscription expired', 'follow_up_emails'),
			'subs_suspended'        => __('after subscription suspended', 'follow_up_emails'),
			'subs_reactivated'      => __('after subscription reactivated', 'follow_up_emails'),
			'subs_payment_failed'   => __('after subscription renewal payment failed', 'follow_up_emails'),
			'subs_renewal_order'    => __('after a renewal order has been created', 'follow_up_emails'),
			'subs_before_renewal'   => __('before next subscription payment', 'follow_up_emails'),
			'subs_before_expire'    => __('before active subscription expires', 'follow_up_emails'),
		);

		$props = array(
			'label'                 => __('Subscription Emails', 'follow_up_emails'),
			'singular_label'        => __('Subscription Email', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('Create emails to send to a user based upon the subscription statuses of your customers.', 'follow_up_emails'),
			'short_description'     => __('Create emails to send to a user based upon the subscription statuses of your customers.', 'follow_up_emails'),
			'list_template'         => FUE_TEMPLATES_DIR .'/email-list/storewide-list.php'
		);
		$types[] = new FUE_Email_Type( 'subscription', $props );

		return $types;
	}

	/**
	 * Add subscriptions action for manual emails
	 */
	public function manual_types() {
		?><option value="active_subscription"><?php esc_html_e('Customers with an active subscription', 'follow_up_emails'); ?></option><?php
	}

	/**
	 * Fields to show if subscription is selected
	 *
	 * @since 1.0.0
	 * @version 4.5.2
	 */
	public function manual_type_actions() {
		$subscriptions = array();

		$posts = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		foreach ( $posts as $post ) {
			$product = WC_FUE_Compatibility::wc_get_product( $post->ID );
			if ( $product->is_type( array( WC_Subscriptions::$name, 'subscription_variation', 'variable-subscription' ) ) ) {
				$subscriptions[] = $product;
			}
		}

		?>
		<div class="send-type-subscription send-type-div">
			<select id="subscription_id" name="subscription_id" class="select2" style="width: 400px;">
				<?php foreach ( $subscriptions as $subscription ) : ?>
				<option value="<?php echo esc_attr( WC_FUE_Compatibility::get_order_prop( $subscription, 'id' ) ); ?>"><?php echo esc_html( $subscription->get_title() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Javascript code for manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "active_subscription":
					jQuery(".send-type-subscription").show();
					break;
			}
		} );
		<?php
	}

	/**
	 * Get the users with active subscriptions for the selected product
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function manual_email_recipients( $recipients, $post ) {

		if ( $post['send_type'] == 'active_subscription' ) {
			$subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions();

			foreach ( $subscriptions as $user_id => $user_subscriptions ) {
				foreach ( $user_subscriptions as $sub_key => $subscription ) {
					if ( ! in_array( $subscription['status'], array( 'active', 'pending-cancel' ) ) ) {
						continue;
					}

					if ( $subscription['product_id'] == $post['subscription_id'] || $subscription['variation_id'] == $post['subscription_id'] ) {
						$order = WC_FUE_Compatibility::wc_get_order( $subscription['order_id'] );
						$user_email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
						$first_name = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' );
						$last_name  = WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );

						$recipients[$sub_key] = array($user_id, $user_email, $first_name .' '. $last_name);
					}
				}
			}

		}

		return $recipients;
	}

	/**
	 * Allow admin to simulate an email using real orders or products
	 * @param FUE_Email $email
	 */
	public function test_email_form( $email ) {
		if ($email->type == 'subscription') {
			include FUE_TEMPLATES_DIR .'/email-form/subscriptions/test-fields-subscriptions.php';
		}
	}

	/**
	 * List of available variables
	 * @param FUE_Email $email
	 */
	public function email_variables_list( $email ) {
		global $woocommerce;

		if ( $email->type != 'subscription' ) {
			return;
		}

		if ( $email->product_id > 0 ): ?>
			<li class="var hideable var_subscriptions"><strong>{item_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The subscription product\'s name', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
			<li class=""><strong>{item_quantity}</strong> <img class="help_tip" title="<?php esc_attr_e('The quantity of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
			<li class="var hideable var_subscriptions"><strong>{item_categories}</strong> <img class="help_tip" title="<?php esc_attr_e('The list of categories where the purchased items are under.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php else: ?>
			<li class="var hideable var_subscriptions"><strong>{item_names}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of purchased items.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
			<li class="var hideable var_subscriptions"><strong>{item_names_list}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a comma-separated list of purchased items.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
			<li class="var hideable var_subscriptions"><strong>{item_categories}</strong> <img class="help_tip" title="<?php esc_attr_e('The list of categories where the purchased items are under.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php endif; ?>
		<li class="var hideable var_subscriptions"><strong>{subs_renew_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The date that a customer\'s subscription renews', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_end_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The date that a customer\'s subscription ends', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{days_to_renew}</strong> <img class="help_tip" title="<?php esc_attr_e('The number of days before a subscription is up for renewal', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_start_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The start date of the subscription', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_trial_length}</strong> <img class="help_tip" title="<?php esc_attr_e('The length of the trial (e.g. 20 days)', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_first_payment}</strong> <img class="help_tip" title="<?php esc_attr_e('The initial cost + any signup fees', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_cost_term}</strong> <img class="help_tip" title="<?php esc_attr_e('The cost and term of the subscription', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_cost}</strong> <img class="help_tip" title="<?php esc_attr_e('The cost of the subscription', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_subscriptions"><strong>{subs_id}</strong> <img class="help_tip" title="<?php esc_attr_e('The ID of the subscription', 'follow_up_emails'); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<?php
	}

	/**
	 * Fired after a subscription gets activated. All unsent items in the queue
	 * with the same subscription key and the subs_cancelled and
	 * subs_suspended trigger will get deleted to avoid sending emails
	 * with incorrect subscription status
	 *
	 * @param int       $user_id
	 * @param string    $subs_key
	 */
	public static function subscription_activated( $user_id, $subs_key ) {
		global $wpdb;

		$parts = explode('_', $subs_key);
		$order_id       = $parts[0];
		$product_id     = $parts[1];

		// delete queued emails with the same product id and the 'subs_cancelled' or 'subs_suspended' trigger
		$rows = $wpdb->get_results( $wpdb->prepare("
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND eo.product_id = %d
			AND eo.email_id = pm.post_id
			AND pm.meta_key = '_interval_type'
			AND (
			  pm.meta_value = 'subs_cancelled' OR pm.meta_value = 'subs_suspended'
			)
		", $product_id) );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}

		$subscription = WC_Subscriptions_Manager::get_subscription( $subs_key );

		if ( count($subscription['completed_payments']) > 1 ) {
			$triggers[] = 'subs_renewed';
		} else {
			$triggers[] = 'subs_activated';
		}

		// Tell FUE that an email order has been created
		// to stop it from sending storewide emails
		if (! defined('FUE_ORDER_CREATED'))
			define('FUE_ORDER_CREATED', true);

		self::add_to_queue($order_id, $triggers, $subs_key, $user_id);

	}

	/**
	 * Fired after a subscription gets cancelled
	 *
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public static function subscription_cancelled( $user_id, $subs_key ) {
		global $wpdb;

		$parts = explode('_', $subs_key);
		$order_id       = $parts[0];
		$product_id     = $parts[1];

		// delete queued emails with the same product id/order id and the following triggers
		$triggers = array(
			'subs_activated', 'subs_renewed', 'subs_reactivated',
			'subs_suspended', 'subs_before_renewal', 'subs_before_expire'
		);
		$sql = $wpdb->prepare("
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND (eo.product_id = %d OR eo.product_id = 0)
			AND eo.order_id = %d
			AND eo.email_id = pm.post_id
			AND pm.meta_key = '_interval_type'
			AND pm.meta_value IN ('". implode( "','", $triggers ) ."')
		", $product_id, $order_id);
		$rows = $wpdb->get_results( $sql );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}

		$triggers = array('subs_cancelled');

		// get the user's email address
		$user = new WP_User($user_id);

		self::add_to_queue($order_id, $triggers, $subs_key, $user->user_email);
	}

	/**
	 * Fired after a subscription expires.
	 *
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public static function subscription_expired( $user_id, $subs_key ) {

		$parts = explode('_', $subs_key);
		$order_id       = $parts[0];
		$triggers[]     = 'subs_expired';

		self::add_to_queue($order_id, $triggers, $subs_key, $user_id);
	}

	/**
	 * Fired after a subscription get reactivated
	 *
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public static function subscription_reactivated( $user_id, $subs_key ) {
		global $wpdb;

		$parts = explode('_', $subs_key);
		$order_id       = $parts[0];
		$product_id     = $parts[1];

		// delete queued emails with the same product id and the 'subs_cancelled' or 'subs_suspended' trigger
		$rows = $wpdb->get_results( $wpdb->prepare("
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND eo.product_id = %d
			AND eo.email_id = pm.post_id
			AND pm.meta_key = '_interval_type'
			AND (
			  pm.meta_value = 'subs_cancelled' OR pm.meta_value = 'subs_suspended'
			)
		", $product_id) );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}

		$triggers[] = 'subs_reactivated';

		self::add_to_queue($order_id, $triggers, $subs_key, $user_id);
	}

	/**
	 * Fired after a subscription gets suspended
	 *
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public static function suspended_subscription( $user_id, $subs_key ) {

		$parts = explode('_', $subs_key);
		$order_id       = $parts[0];

		$triggers[]     = 'subs_suspended';

		self::add_to_queue($order_id, $triggers, $subs_key, $user_id);

	}

	/**
	 * Fires after a renewal order is created to allow admin to
	 * send emails after every subscription payment
	 *
	 * @param WC_Order $renewal_order
	 * @param WC_Order $original_order
	 * @param int $product_id
	 */
	public static function subscription_renewal_order_created( $renewal_order, $original_order, $product_id ) {
		global $wpdb;

		$subs_key = self::get_subscription_key( WC_FUE_Compatibility::get_order_prop( $original_order, 'id' ), $product_id );

		$triggers[]     = 'subs_renewal_order';

		self::add_to_queue( WC_FUE_Compatibility::get_order_prop( $original_order, 'id' ), $triggers, $subs_key, WC_FUE_Compatibility::get_order_prop( $original_order, 'user_id' ));
	}

	/**
	 * Add email to the queue
	 *
	 * @param $order_id
	 * @param $triggers
	 * @param string $subs_key
	 * @param string $user_id
	 */
	public static function add_to_queue($order_id, $triggers, $subs_key = '', $user_id = '') {

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN'
				)
			)
		) );

		foreach ( $emails as $email ) {
			$interval   = (int)$email->interval_num;

			$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
			$send_on    = current_time('timestamp') + $add;
			$prod_id    = 0;

			if ( $subs_key ) {
				$item_id        = WC_Subscriptions_Order::get_item_id_by_subscription_key( $subs_key );
				$product_id     = wc_get_order_item_meta( $item_id, '_product_id', true );
				$variation_id   = wc_get_order_item_meta( $item_id, '_variation_id', true );
				$meta           = maybe_unserialize($email->meta);
				$include_variations = isset($meta['include_variations']) && $meta['include_variations'] == 'yes';
				$match          = false;

				// exact product match
				if ( $email->product_id > 0 ) {
					if ( $email->product_id == $product_id ) {
						$match = true;
					} elseif ( $email->product_id == $variation_id ) {
						$match = true;
					}

					if ( !$match ) {
						continue;
					}
				} elseif ( $email->category_id > 0 ) {
					$cat_terms = wp_get_object_terms( $product_id, 'product_cat', array('fields' => 'ids') );
					$categories = array();

					if ( !is_wp_error( $cat_terms ) ) {
						foreach ( $cat_terms as $category_id ) {
							$categories[] = $category_id;
						}
					}

					if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
						continue;
					}
				}

			}

			$insert = array(
				'send_on'       => $send_on,
				'email_id'      => $email->id,
				'product_id'    => $prod_id,
				'order_id'      => $order_id
			);

			if ( $subs_key ) {
				$insert['meta']['subs_key'] = $subs_key;
			}

			if ($user_id) {
				$user = new WP_User($user_id);
				$insert['user_id']      = $user_id;
				$insert['user_email']   = $user->user_email;
			}

			FUE_Sending_Scheduler::queue_email( $insert, $email );
		}
	}

	/**
	 * Do not send email if the status has changed from the time it was queued
	 *
	 * @param bool      $skip
	 * @param FUE_Email $email
	 * @param object    $queue_item
	 *
	 * @return bool
	 */
	public function skip_sending_if_status_changed( $skip, $email, $queue_item ) {
		global $wpdb;

		if ( isset($queue_item->meta) && !empty($queue_item->meta) ) {

			$meta = maybe_unserialize($queue_item->meta);

			if ( isset($meta['subs_key']) ) {
				$delete         = false;

				if ( self::is_wcs_2() ) {
					return FUE_Addon_Subscriptions_V2::skip_sending_if_status_changed( $skip, $email, $queue_item );
				}

				$subscription   = WC_Subscriptions_Manager::get_subscription( $meta['subs_key'] );

				if ( $subscription ) {

					if ( $email->interval_type == 'subs_suspended' && $subscription['status'] != 'on-hold' ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_expired' && $subscription['status'] != 'expired' ) {
						$delete = true;
						$skip = true;
					} elseif ( ($email->interval_type == 'subs_activated' || $email->interval_type == 'subs_renewed' || $email->interval_type == 'subs_reactivated') && $subscription['status'] != 'active' ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_cancelled' && $subscription['status'] != 'cancelled' ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_before_renewal' && $subscription['status'] != 'active' ) {
						$delete = true;
						$skip = true;
					}

					if ( $delete ) {
						Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
					}

				} // if ($subscription)
			} // if ( isset($meta['subs_key']) )

		} // if ( isset($email_order->meta) && !empty($email_order->meta) )

		return $skip;

	}

	/**
	 * Skip sending reminder emails if the expiration/renewal has changed
	 *
	 * @param bool                      $skip
	 * @param FUE_Email                 $email
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @return bool
	 */
	public function skip_sending_if_reminder_changed( $skip, $email, $queue_item ) {
		if ( !in_array( $email->trigger, array( 'subs_before_renewal', 'subs_before_expire') ) ) {
			return $skip;
		}

		if ( !isset( $queue_item->meta ) || empty( $queue_item->meta ) || empty( $queue_item->meta['subs_key'] ) ) {
			return $skip;
		}

		$meta   = maybe_unserialize($queue_item->meta);
		$delete = false;

		if ( self::is_wcs_2() ) {
			return FUE_Addon_Subscriptions_V2::skip_sending_if_reminder_changed( $skip, $email, $queue_item );
		}

		$subscription = WC_Subscriptions_Manager::get_subscription( $meta['subs_key'] );

		if ( $subscription ) {

			if ( $email->trigger == 'subs_before_expire' ) {
				$expiry = $subscription['end_date'];
				$now    = current_time( 'timestamp', true );

				if ( $expiry == 0 ) {
					return $skip;
				}

				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $expiry - $add;

				if ( $send_on > $now ) {
					$skip = true;

					$queue_item->send_on = $send_on;
					$queue_item->save();

					// reschedule
					$param = Follow_Up_Emails::instance()->scheduler->get_scheduler_parameters( $queue_item->id );
					as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );
					as_schedule_single_action( $send_on, 'sfn_followup_emails', $param, 'fue' );
				}
			} elseif ( $email->trigger == 'subs_before_renewal' ) {
				$expiry = $subscription['expiry_date'];
				$now    = current_time( 'timestamp', true );

				if ( $expiry == 0 ) {
					return $skip;
				}

				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $expiry - $add;

				if ( $send_on > $now ) {
					$skip = true;

					$queue_item->send_on = $send_on;
					$queue_item->save();

					// reschedule
					$param = Follow_Up_Emails::instance()->scheduler->get_scheduler_parameters( $queue_item->id );
					as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );
					as_schedule_single_action( $send_on, 'sfn_followup_emails', $param, 'fue' );
				}
			}

		} // if ($subscription)

		return $skip;
	}

	/**
	 * Add renewal reminder emails to the queue right after the subscription has been activated
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public function set_renewal_reminder( $user_id, $subs_key ) {
		$parts      = explode('_', $subs_key);
		$order_id   = $parts[0];
		$product_id = $parts[1];
		$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
		$queued     = array();

		if ( ! self::order_contains_subscription($order) )
			return;

		$renewal_date = WC_Subscriptions_Manager::get_next_payment_date( $subs_key, $user_id );

		if (! $renewal_date )
			return;

		// convert to local time
		$renewal_timestamp = get_date_from_gmt( $renewal_date, 'U' );

		if ( current_time('timestamp', true) > $renewal_timestamp ) {
			return;
		}

		// look for renewal emails
		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'   => '_interval_type',
					'value' => 'subs_before_renewal'
				)
			)
		) );

		if ( count($emails) > 0 ) {

			foreach ( $emails as $email ) {
				// product_id filter
				if ( !empty( $email->product_id ) && $product_id != $email->product_id ) {
					continue;
				} elseif ( $email->category_id > 0 ) {
					$cat_terms = wp_get_object_terms( $product_id, 'product_cat', array('fields' => 'ids') );
					$categories = array();

					if ( !is_wp_error( $cat_terms ) ) {
						$categories = $cat_terms;
					}

					if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
						continue;
					}
				}

				// look for a possible duplicate item in the queue
				$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
					'email_id'  => $email->id,
					'is_sent'   => 0,
					'order_id'  => $order_id,
					'user_id'   => $user_id
				));

				if ( count( $dupes ) > 0 ) {
					// there already is an unsent queue item for the exact same order
					continue;
				}

				// add this email to the queue
				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $renewal_timestamp - $add;

				if ( $send_on < current_time( 'timestamp' ) ) {
					// Only queue future emails. Do not send a renewal notice if this subscription was just created.
					continue;
				}

				$insert = array(
					'user_id'       => $user_id,
					'send_on'       => $send_on,
					'email_id'      => $email->id,
					'product_id'    => 0,
					'order_id'      => $order_id
				);

				if ( $subs_key ) {
					$insert['meta']['subs_key'] = $subs_key;
				}

				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}

			}
		}

		if ( count( $queued ) > 0 ) {
			$this->add_order_notes_to_queued_emails( $queued );
		}

	}

	/**
	 * Set expiration reminder after the subscription gets activated
	 *
	 * @param int $user_id
	 * @param string $subs_key
	 */
	public function set_expiration_reminder( $user_id, $subs_key ) {
		$parts      = explode('_', $subs_key);
		$order_id   = $parts[0];
		$product_id = $parts[1];
		$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
		$queued     = array();

		if ( ! self::order_contains_subscription($order) )
			return;

		$expiry_date = WC_Subscriptions_Manager::get_subscription_expiration_date( $subs_key, $user_id );

		if (! $expiry_date )
			return;

		// convert to local time
		$expiry_timestamp = get_date_from_gmt( $expiry_date, 'U' );

		if ( current_time('timestamp', true) > $expiry_timestamp ) {
			return;
		}

		// look for renewal emails
		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'   => '_interval_type',
					'value' => 'subs_before_expire'
				)
			)
		) );

		if ( count($emails) > 0 ) {

			foreach ( $emails as $email ) {
				// product_id filter
				if ( !empty( $email->product_id ) && $product_id != $email->product_id ) {
					continue;
				} elseif ( $email->category_id > 0 ) {
					$cat_terms = wp_get_object_terms( $product_id, 'product_cat', array('fields' => 'ids') );
					$categories = array();

					if ( !is_wp_error( $cat_terms ) ) {
						$categories = $cat_terms;
					}

					if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
						continue;
					}
				}


				// look for a possible duplicate item in the queue
				$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
					'email_id'  => $email->id,
					'is_sent'   => 0,
					'order_id'  => $order_id,
					'user_id'   => $user_id
				));

				if ( count( $dupes ) > 0 ) {
					// there already is an unsent queue item for the exact same order
					continue;
				}

				// add this email to the queue
				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $expiry_timestamp - $add;

				$insert = array(
					'user_id'       => $user_id,
					'send_on'       => $send_on,
					'email_id'      => $email->id,
					'product_id'    => 0,
					'order_id'      => $order_id
				);

				if ( $subs_key ) {
					$insert['meta']['subs_key'] = $subs_key;
				}

				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		if ( count( $queued ) > 0 ) {
			$this->add_order_notes_to_queued_emails( $queued );
		}

	}

	/**
	 * Javascript for the email form
	 */
	public function email_form_script() {
		wp_enqueue_script( 'fue-form-subscriptions', FUE_TEMPLATES_URL .'/js/email-form-subscriptions.js' );
	}

	/**
	 * Add course selector to the Trigger tab
	 *
	 * @param FUE_Email $email
	 */
	public function register_trigger_fields( $email ) {
		// load the categories
		if ( $email->type == 'subscription' ) {
			$categories     = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC', 'hide_empty' => false ) );
			$storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';

			include FUE_TEMPLATES_DIR .'/email-form/subscriptions/subscription-selector.php';
		}
	}

	/**
	 * Apply the value of 'subscription_product_id' to the 'product_id' field
	 *
	 * @param array     $data
	 * @param int       $post_id
	 * @param WP_Post   $post
	 * @return array $data
	 */
	public function apply_subscription_product_id( $data, $post_id, $post ) {
		if ( $data['type'] == 'subscription' && !empty( $_POST['subscription_product_id'] ) ) {
			$data['product_id'] = absint( $_POST['subscription_product_id'] );
		}

		return $data;
	}

	/**
	 * Register subscription variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables   $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 */
	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		if ( $email->type != 'subscription' ) {
			return;
		}

		$variables = array(
			'subs_renew_date', 'subs_end_date', 'days_to_renew', 'item_name', 'item_url'
		);

		if ( $email->type == 'manual' ) {
			$variables = $this->add_manual_email_variables( $variables, $email_data, $queue_item, $email );
		} else {
			// use test data if the test flag is set
			if ( isset( $email_data['test'] ) && $email_data['test'] ) {
				$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
			} else {
				$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
			}
		}

		$var->register( $variables );
	}

	/**
	 * Apply variable replacements for manual emails
	 *
	 * @param array                     $variables
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	protected function add_manual_email_variables( $variables, $email_data, $queue_item, $email ) {
		if ( isset( $queue_item->meta['send_type'] ) && $queue_item->meta['send_type'] == 'active_subscription' ) {
			// recipient_key is the subscription ID
			if ( empty( $queue_item->meta['recipient_key'] ) ) {
				return $variables;
			}

			$subs_key       = $queue_item->meta['recipient_key'];
			$subscription   = WC_Subscriptions_Manager::get_subscription( $queue_item->meta['recipient_key'] );

			if ( empty( $subscription ) ) {
				return $variables;
			}

			$data = self::get_subscription_meta( $subs_key );
			$item = $data['item'];

			$item_url = FUE_Sending_Mailer::create_email_url(
				$queue_item->id,
				$queue_item->email_id,
				$email_data['user_id'],
				$email_data['email_to'],
				get_permalink($item['product_id'])
			);

			$variables['subs_renew_date']   = $data['next_payment_date'];
			$variables['subs_end_date']     = $data['end_date'];
			$variables['days_to_renew']     = $data['days_to_renew'];
			$variables['item_name']         = $item['name'];
			$variables['item_url']          = fue_replacement_url_var( $item_url );
			$variables['subs_start_date']   = $data['start_date'];
			$variables['subs_trial_length'] = $data['trial_length'];
			$variables['subs_first_payment']= $data['first_payment_cost'];
			$variables['subs_cost_term']    = $data['cost_term'];
			$variables['subs_cost']         = $data['cost'];
			$variables['subs_id']           = $subs_key;

		}

		return $variables;
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array     $variables
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
		if ( !$queue_item->order_id  ) {
			return $variables;
		}

		$order = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );

		if ( !self::order_contains_subscription( $order ) ) {
			return $variables;
		}

		$item       = WC_Subscriptions_Order::get_item_by_product_id($order);
		$item_id    = WC_Subscriptions_Order::get_items_product_id($item);
		$subs_key   = self::get_subscription_key( $queue_item->order_id, $item_id );

		$data = self::get_subscription_meta( $subs_key );
		$item = $data['item'];

		$item_url = FUE_Sending_Mailer::create_email_url(
			$queue_item->id,
			$queue_item->email_id,
			$email_data['user_id'],
			$email_data['email_to'],
			get_permalink($item['product_id'])
		);

		$renewals = wcs_get_subscriptions_for_renewal_order( $order );

		if ( ! empty( $renewals ) ) {
			$renewal_order = current( $renewals );
			$variables['order_pay_url'] = fue_replacement_url_var( $renewal_order->get_checkout_payment_url() );
		}

		$variables['subs_renew_date']   = $data['next_payment_date'];
		$variables['subs_end_date']     = $data['end_date'];
		$variables['days_to_renew']     = $data['days_to_renew'];
		$variables['item_name']         = $item['name'];
		$variables['item_url']          = fue_replacement_url_var( $item_url );
		$variables['subs_start_date']   = $data['start_date'];
		$variables['subs_trial_length'] = $data['trial_length'];
		$variables['subs_first_payment']= $data['first_payment_cost'];
		$variables['subs_cost_term']    = $data['cost_term'];
		$variables['subs_cost']         = $data['cost'];
		$variables['subs_id']           = $subs_key;

		return $variables;
	}

	/**
	 * Add variable replacements for test emails
	 *
	 * @param array     $variables
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	protected function add_test_variable_replacements( $variables, $email_data, $email ) {
		$variables['subs_start_date']   = date( wc_date_format(), time()-(86400*7) );
		$variables['subs_renew_date']   = date( wc_date_format(), time()+86400);
		$variables['subs_end_date']     = date( wc_date_format(), time()+(86400*7) );
		$variables['days_to_renew']     = 1;
		$variables['item_name']         = 'Test Subscription';
		$variables['subs_trial_length']  = '1 week';
		$variables['subs_first_payment'] = date( wc_date_format(), time()+(86400*7) );;
		$variables['subs_cost_term']     = '$5 / week';
		$variables['subs_cost']          = '$5';
		$variables['subs_id']            = 1121;

		if ( $email_data['order_id'] > 0 ) {
			$order = WC_FUE_Compatibility::wc_get_order( $email_data['order_id'] );

			if ( WC_Subscriptions_Order::order_contains_subscription( $order ) ) {
				$item       = WC_Subscriptions_Order::get_item_by_product_id($order);
				$item_id    = WC_Subscriptions_Order::get_items_product_id($item);
				$renewal    = self::calculate_next_payment_timestamp($order, $item_id);
				$subs_key   = self::get_subscription_key( $email_data['order_id'], $item_id );

				$variables['subs_renew_date'] = date( get_option('date_format'), $renewal );

				$end_date = WC_Subscriptions_Manager::get_subscription_expiration_date( $subs_key, '', 'timestamp' );

				if ( $end_date == 0 ) {
					$variables['subs_end_date'] = __('Until Cancelled', 'follow_up_emails');
				} else {
					$variables['end_date'] = date( get_option('date_format'), $end_date );
				}

				// calc days to renew
				$now    = current_time( 'timestamp' );
				$diff   = $renewal - $now;

				if ( $diff > 0 ) {
					$variables['days_to_renew'] = floor( $diff / 86400 );
				}

				$variables['item_name'] = get_the_title( $item_id );
			}
		}

		return $variables;
	}

	public function email_replacements( $reps, $email_data, $email_order, $email_row ) {
		global $wpdb, $woocommerce;

		$email_type     = $email_row->email_type;
		$order_date     = '';
		$order_datetime = '';
		$order_id       = '';

		if ( $email_order->order_id ) {
			$order          = WC_FUE_Compatibility::wc_get_order( $email_order->order_id );
			$order_date     = date(get_option('date_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));
			$order_datetime = date(get_option('date_format') .' '. get_option('time_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));

			$order_id = $order->get_order_number();

			$billing_address    = $order->get_formatted_billing_address();
			$shipping_address   = $order->get_formatted_shipping_address();

			$item       = WC_Subscriptions_Order::get_item_by_product_id($order);
			$item_id    = WC_Subscriptions_Order::get_items_product_id($item);
			$renewal    = self::calculate_next_payment_timestamp($order, $item_id);
			$subs_key   = self::get_subscription_key( $email_order->order_id, $item_id );

			$renew_date = date( get_option('date_format'), $renewal );
			$end_date   = WC_Subscriptions_Manager::get_subscription_expiration_date( $subs_key, '', 'timestamp' );

			if ( $end_date == 0 ) {
				$end_date = __('Until Cancelled', 'follow_up_emails');
			} else {
				$end_date = date( get_option('date_format'), $end_date );
			}

			// calc days to renew
			$now    = current_time( 'timestamp' );
			$diff   = $renewal - $now;
			$days_to_renew = 0;
			if ( $diff > 0 ) {
				$days_to_renew = floor( $diff / 86400 );
			}

			$item_url   = FUE_Sending_Mailer::create_email_url( $email_order->id, $email_row->id, $email_data['user_id'], $email_data['email_to'], get_permalink($item_id) );

			$categories = '';

			if ( $item_id ) {
				$cats   = get_the_terms($item_id, 'product_cat');

				if (is_array($cats) && !empty($cats)) {
					foreach ($cats as $cat) {
						$categories .= $cat->name .', ';
					}
					$categories = rtrim($categories, ', ');
				}

			}

			$reps = array_merge($reps, array(
					$order_id,
					$order_date,
					$order_datetime,
					$billing_address,
					$shipping_address,
					$email_data['username'],
					$email_data['first_name'],
					$email_data['first_name'] .' '. $email_data['last_name'],
					$email_data['email_to'],
					$renew_date,
					$end_date,
					$days_to_renew,
					'<a href="'. $item_url .'">'. get_the_title($item_id) .'</a>',
					$categories
				));
		}

		return $reps;
	}

	/**
	 * Override the email data to supply our own values based on the subscription's order

	 * @param array     $email_data
	 * @param object    $email_order
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	public function get_email_address_to_send( $email_data, $email_order, $email ) {

		if ($email->email_type != 'subscription')
			return $email_data;

		$meta = maybe_unserialize($email_order->meta);

		if (isset($meta['subs_key'])) {
			$subscription = WC_Subscriptions_Manager::get_subscription($meta['subs_key']);

			if (! empty($subscription)) {
				$order  = WC_FUE_Compatibility::wc_get_order($subscription['order_id']);
				$user   = new WP_User(WC_FUE_Compatibility::get_order_prop( $order, 'user_id' ));

				// Use the customer's billing data.
				$billing_email      = get_user_meta( $user->ID, 'billing_email', true );
				$billing_first_name = get_user_meta( $user->ID, 'billing_first_name', true );
				$billing_last_name  = get_user_meta( $user->ID, 'billing_last_name', true );

				$order_email        = get_post_meta( $order->get_id(), '_billing_email', true );
				$order_first_name   = get_post_meta( $order->get_id(), '_billing_first_name', true );
				$order_last_name    = get_post_meta( $order->get_id(), '_billing_last_name', true );

				// If the customer's billing data are empty, fallback to using data from WP_User.
				$email_data['email_to']   = $billing_email      ?: ( $order_email      ?: $wp_user->user_email );
				$email_data['username']   = $user->user_login;
				$email_data['first_name'] = $billing_first_name ?: ( $order_first_name ?: $wp_user->first_name );
				$email_data['last_name']  = $billing_last_name  ?: ( $order_last_name  ?: $wp_user->last_name );

				$email_data['cname']        = $email_data['first_name'] .' '. $email_data['last_name'];
			}
		}

		return $email_data;
	}

	/**
	 * FUE subscriptions settings form HTML
	 */
	public function settings_form() {
		include FUE_TEMPLATES_DIR .'/settings/settings-subscriptions.php';
	}

	/**
	 * Save the settings form
	 */
	public function save_settings( $post ) {
		$post = array_map( 'sanitize_text_field', wp_unslash( $post ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.

		if ( $post['section'] === 'integration' ) {
			$notification   = (isset($post['subscription_failure_notification']) && $post['subscription_failure_notification'] === 1) ? 1 : 0;
			$emails         = (isset($post['subscription_failure_notification_emails'])) ? $post['subscription_failure_notification_emails'] : '';

			$emails = implode( ',', array_map( 'sanitize_email', explode( ',', $emails ) ) );

			update_option( 'fue_subscription_failure_notification', $notification );
			update_option( 'fue_subscription_failure_notification_emails', $emails );
		}

	}

	/**
	 * Send an email notification when a subscription payment fails
	 * @param WC_Order $order
	 */
	public function payment_failed_for_order( $order ) {

		if ( 1 == get_option('fue_subscription_failure_notification', 0) ) {
			// notification enabled
			$emails_string = get_option('fue_subscription_failure_notification_emails', '');

			if ( empty($emails_string) )
				return;

			// get the product id to get the subscription string
			$order_items        = WC_Subscriptions_Order::get_recurring_items( $order );
			$first_order_item   = reset( $order_items );
			$product_id         = WC_Subscriptions_Order::get_items_product_id( $first_order_item );
			$subs_key           = self::get_subscription_key( WC_FUE_Compatibility::get_order_prop( $order, 'id' ), $product_id );

			$subject    = sprintf( __('Subscription payment failed for Order %s'), $order->get_order_number() );
			$message    = sprintf( __('A subscription payment for the order %s has failed. The subscription has now been automatically put on hold.'), $order->get_order_number() );

			$recipients = array();

			if ( strpos( $emails_string, ',') !== false ) {
				$recipients = array_map('trim', explode( ',', $emails_string ) );
			} else {
				$recipients = array($emails_string);
			}

			$scheduler = Follow_Up_Emails::instance()->scheduler;

			// FUE will use the billing_email by default. Remove the hook to stop it from changing the email
			remove_filter( 'fue_insert_email_order', array($scheduler, 'get_correct_email') );

			foreach ( $recipients as $email ) {
				$scheduler->queue_email(
					array(
						'user_email'    => $email,
						'meta'          => array(
							'subscription_notification' => true,
							'email'     => $email,
							'subject'   => $subject,
							'message'   => $message
						),
						'email_trigger' => 'After a subscription payment fails',
						'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
						'product_id'    => $product_id,
						'send_on'       => current_time('timestamp')
					),
					null, // ad-hoc email
					true
				);
			}
		}

	}

	/**
	 * Listen for changes in payment dates and adjust the sending schedule of matching emails
	 *
	 * @param bool      $is_set TRUE if a schedule has been set for the new date
	 * @param int       $next_payment Timestamp of the new payment date
	 * @param string    $subscription_key
	 * @param int       $user_id
	 * @return bool The unchanged value of $is_set
	 */
	public function payment_date_changed( $is_set, $next_payment, $subscription_key, $user_id ) {

		// look for unsent emails in the queue matching this subscription
		$serialized_key = serialize( array('subs_key' => $subscription_key) );
		$serialized_key = str_replace( 'a:1:{', '', $serialized_key );
		$serialized_key = str_replace( '}', '', $serialized_key );

		$scheduler  = new FUE_Sending_Scheduler( Follow_Up_Emails::instance() );
		$items      = $scheduler->get_items( array(
			'is_sent'   => 0,
			'meta'      => $serialized_key
		) );

		foreach ( $items as $item ) {
			$email = new FUE_Email( $item->email_id );

			if ( $email->trigger == 'subs_before_expire' || $email->trigger == 'subs_before_renewal' ) {
				// unschedule the email first
				$param = array( $item->id );
				as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );

				// get the new sending schedule
				$new_timestamp = 0;
				if ( $email->trigger == 'subs_before_expire' ) {
					$expiry_date = WC_Subscriptions_Manager::get_subscription_expiration_date( $subscription_key, $user_id );

					if ( $expiry_date ) {
						// convert to local time
						$new_timestamp = get_date_from_gmt( $expiry_date, 'U' );
					}

				} else {
					$renewal_date = WC_Subscriptions_Manager::get_next_payment_date( $subscription_key, $user_id );

					if ( $renewal_date ) {
						// convert to local time
						$new_timestamp = get_date_from_gmt( $renewal_date, 'U' );
					}

				}

				if ( $new_timestamp ) {
					// add this email to the queue
					$interval   = (int)$email->interval_num;
					$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
					$send_on    = $new_timestamp - $add;

					// update the send_on value of the queue item
					$item->send_on = $send_on;
					$item->save();

					// set a schedule using the new timestamp
					$scheduler->schedule_email( $item->id, $send_on );
				}

			}

		}

		return $is_set;

	}

	/**
	 * Declare support for importing existing orders
	 * @param array $types
	 * @return array
	 */
	public function add_subscription_to_import_orders( $types ) {
		$types[] = 'subscription';
		return $types;
	}

	/**
	 * Get orders that match the $email's criteria
	 * @param array     $orders Matching Order IDs
	 * @param FUE_Email $email
	 * @return array
	 */
	public function get_orders_for_email( $orders, $email ) {
		if ( $email->type != 'subscription' ) {
			return $orders;
		}

		$wpdb               = Follow_Up_Emails::instance()->wpdb;
		$all_subscriptions  = WC_Subscriptions_Manager::get_all_users_subscriptions();

		$status_array = array(
			'subs_activated'    => 'active',
			'subs_cancelled'    => 'cancelled',
			'subs_expired'      => 'expired',
			'subs_suspended'    => 'suspended'
		);
		$status_triggers = array_keys( $status_array );

		if ( in_array( $email->trigger, $status_triggers ) ) {
			$status = $status_array[ $email->trigger ];
			foreach ( $all_subscriptions as $user_id => $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( $subscription['status'] != $status ) {
						continue;
					}

					if ( $email->product_id > 0 && ( $subscription['product_id'] != $email->product_id && $subscription['variation_id'] != $email->product_id ) ) {
						continue;
					}

					$in_queue = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d",
						$subscription['order_id'],
						$email->id
					) );

					if ( $in_queue ) {
						continue;
					}

					$orders[] = $subscription['order_id'];
				}
			}
		} elseif ( $email->trigger == 'subs_renewed' ) {
			// get orders with active subscriptions AND renewals
			foreach ( $all_subscriptions as $user_id => $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( $subscription['status'] == 'active' && count( $subscription['completed_payments'] ) >= 2 ) {

						if ( $email->product_id > 0 && ( $subscription['product_id'] != $email->product_id && $subscription['variation_id'] != $email->product_id ) ) {
							continue;
						}

						$in_queue = $wpdb->get_var( $wpdb->prepare(
							"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d",
							$subscription['order_id'],
							$email->id
						) );

						if ( $in_queue ) {
							continue;
						}

						$orders[] = $subscription['order_id'];
					}
				}
			}
		} elseif ( $email->trigger == 'subs_reactivated' ) {
			// get active subscriptions with at least 1 suspension count
			foreach ( $all_subscriptions as $user_id => $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( $subscription['status'] == 'active' && absint($subscription['suspension_count']) > 0 ) {

						if ( $email->product_id > 0 && ( $subscription['product_id'] != $email->product_id && $subscription['variation_id'] != $email->product_id ) ) {
							continue;
						}

						$in_queue = $wpdb->get_var( $wpdb->prepare(
							"SELECT COUNT(*)
							FROM {$wpdb->prefix}followup_email_orders
							WHERE order_id = %d
							AND email_id = %d",
							$subscription['order_id'],
							$email->id
						) );

						if ( $in_queue ) {
							continue;
						}

						$orders[] = $subscription['order_id'];
					}
				}
			}
		} elseif ( $email->trigger == 'subs_renewal_order' ) {
			$order_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_parent > 0 AND post_type = 'shop_order' ORDER BY {$wpdb->posts}.post_date ASC" );

			foreach ( $order_ids as $order_id ) {
				$in_queue = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE order_id = %d
					AND email_id = %d",
					$order_id,
					$email->id
				) );

				if ( $in_queue ) {
					continue;
				}

				$orders[] = $order_id;
			}

		} elseif ( $email->trigger == 'subs_before_renewal' || $email->trigger == 'subs_before_expire' ) {
			foreach ( $all_subscriptions as $user_id => $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					if ( $subscription['status'] != 'active' ) {
						continue;
					}

					if ( $email->product_id > 0 && ( $subscription['product_id'] != $email->product_id && $subscription['variation_id'] != $email->product_id ) ) {
						continue;
					}

					$in_queue = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d
						AND is_sent = 0",
						$subscription['order_id'],
						$email->id
					) );

					if ( $in_queue ) {
						continue;
					}

					$orders[] = $subscription['order_id'];
				}
			}
		}

		if ( empty( $orders ) ) {
			return array();
		}

		return array( $email->id => $orders );
	}

	/**
	 * Change the send date of the email for 'before_renewal' and 'before_expire' triggers
	 * @param array $insert
	 * @param FUE_Email $email
	 * @return array
	 */
	public function modify_insert_send_date( $insert, $email ) {
		if ( $email->type != 'subscription' ) {
			return $insert;
		}

		if ( self::is_wcs_2() ) {
			if ( !empty( $insert['meta']['subs_key'] ) ) {
				$subscription = wcs_get_subscription( $insert['meta']['subs_key'] );

				if ( $subscription ) {
					if ( $email->trigger == 'subs_before_renewal' ) {
						$renewal_date = $subscription->get_date( 'next_payment' );

						if (! $renewal_date  ) {
							// return false because we cannot send 'before expiry' emails on
							// subscriptions that do not expire
							return false;
						}

						// convert to local time
						$renewal_timestamp  = get_date_from_gmt( $renewal_date, 'U' );
						$add                = FUE_Sending_Scheduler::get_time_to_add( $email->interval, $email->duration );

						$insert['send_on'] = $renewal_timestamp - $add;
					} elseif ( $email->trigger == 'subs_before_expire' ) {
						$expiry_date = $subscription->get_date( 'end' );

						if (! $expiry_date ) {
							// return false because we cannot send 'before expiry' emails on
							// subscriptions that do not expire
							return false;
						}

						// convert to local time
						$expiry_timestamp   = get_date_from_gmt( $expiry_date, 'U' );
						$add                = FUE_Sending_Scheduler::get_time_to_add( $email->interval, $email->duration );
						$insert['send_on']  = $expiry_timestamp - $add;
					}
				}
			}
		} else {
			if( ! $insert['order_id'] ) {
				return $insert;
			}

			$subs_key = self::get_subscription_key( $insert['order_id'] );

			if ( $email->trigger == 'subs_before_renewal' ) {
				$renewal_date = WC_Subscriptions_Manager::get_next_payment_date( $subs_key );

				if (! $renewal_date ) {
					// return false to tell FUE to skip importing this email/order
					return false;
				}

				// convert to local time
				$local_renewal_date = get_date_from_gmt( $renewal_date, 'U' );
				$add                = FUE_Sending_Scheduler::get_time_to_add( $email->interval, $email->duration );
				$insert['send_on']  = $local_renewal_date - $add;
			} elseif ( $email->trigger == 'subs_before_expire' ) {
				$expiry_date = WC_Subscriptions_Manager::get_subscription_expiration_date( $subs_key );

				if (! $expiry_date ) {
					return false;
				}

				// convert to local time
				$expiry_timestamp   = get_date_from_gmt( $expiry_date, 'U' );
				$add                = FUE_Sending_Scheduler::get_time_to_add( $email->interval, $email->duration );
				$insert['send_on']  = $expiry_timestamp - $add;
			}

			// Add the subscription key if it is not present in the meta
			if ( !isset( $insert['meta'] ) || empty( $insert['meta']['subs_key'] ) ) {
				$insert['meta']['subs_key'] = $subs_key;
			}
		}

		return $insert;
	}

	/**
	 * Calculate the timestamp for the next payment
	 *
	 * @param WC_Order  $order
	 * @param int       $product_id
	 *
	 * @return mixed|void
	 */
	private static function calculate_next_payment_timestamp( $order, $product_id ) {
		$type = 'timestamp';
		$from_date = '';

		$from_date_arg = $from_date;

		$subscription              = WC_Subscriptions_Manager::get_subscription( self::get_subscription_key( WC_FUE_Compatibility::get_order_prop( $order, 'id' ), $product_id ) );
		$subscription_period       = WC_Subscriptions_Order::get_subscription_period( $order, $product_id );
		$subscription_interval     = WC_Subscriptions_Order::get_subscription_interval( $order, $product_id );
		$subscription_trial_length = WC_Subscriptions_Order::get_subscription_trial_length( $order, $product_id );
		$subscription_trial_period = WC_Subscriptions_Order::get_subscription_trial_period( $order, $product_id );

		$trial_end_time   = ( ! empty( $subscription['trial_expiry_date'] ) ) ? $subscription['trial_expiry_date'] : WC_Subscriptions_Product::get_trial_expiration_date( $product_id, get_gmt_from_date( WC_FUE_Compatibility::get_order_prop( $order, 'order_date' ) ) );
		$trial_end_time   = strtotime( $trial_end_time );

		// If the subscription has a free trial period, and we're still in the free trial period, the next payment is due at the end of the free trial
		if ( $subscription_trial_length > 0 && $trial_end_time > ( gmdate( 'U' ) + 60 * 60 * 23 + 120 ) ) { // Make sure trial expiry is more than 23+ hours in the future to account for trial expiration dates incorrectly stored in non-UTC/GMT timezone (and also for any potential changes to the site's timezone)

			$next_payment_timestamp = $trial_end_time;

			// The next payment date is {interval} billing periods from the from date
		} else {

			// We have a timestamp
			if ( ! empty( $from_date ) && is_numeric( $from_date ) )
				$from_date = date( 'Y-m-d H:i:s', $from_date );

			if ( empty( $from_date ) ) {

				if ( ! empty( $subscription['completed_payments'] ) ) {
					$from_date = array_pop( $subscription['completed_payments'] );
					$add_failed_payments = true;
				} else if ( ! empty ( $subscription['start_date'] ) ) {
					$from_date = $subscription['start_date'];
					$add_failed_payments = true;
				} else {
					$from_date = gmdate( 'Y-m-d H:i:s' );
					$add_failed_payments = false;
				}

				$failed_payment_count = WC_Subscriptions_Order::get_failed_payment_count( $order, $product_id );

				// Maybe take into account any failed payments
				if ( true === $add_failed_payments && $failed_payment_count > 0 ) {
					$failed_payment_periods = $failed_payment_count * $subscription_interval;
					$from_timestamp = strtotime( $from_date );

					if ( 'month' == $subscription_period )
						$from_date = date( 'Y-m-d H:i:s', WC_Subscriptions::add_months( $from_timestamp, $failed_payment_periods ) );
					else // Safe to just add the billing periods
						$from_date = date( 'Y-m-d H:i:s', strtotime( "+ {$failed_payment_periods} {$subscription_period}", $from_timestamp ) );
				}
			}

			$from_timestamp = strtotime( $from_date );

			if ( 'month' == $subscription_period ) // Workaround potential PHP issue
				$next_payment_timestamp = WC_Subscriptions::add_months( $from_timestamp, $subscription_interval );
			else
				$next_payment_timestamp = strtotime( "+ {$subscription_interval} {$subscription_period}", $from_timestamp );

			// Make sure the next payment is in the future
			$i = 1;
			while ( $next_payment_timestamp < gmdate( 'U' ) && $i < 30 ) {
				if ( 'month' == $subscription_period ) {
					$next_payment_timestamp = WC_Subscriptions::add_months( $next_payment_timestamp, $subscription_interval );
				} else { // Safe to just add the billing periods
					$next_payment_timestamp = strtotime( "+ {$subscription_interval} {$subscription_period}", $next_payment_timestamp );
				}
				$i = $i + 1;
			}

		}

		// If the subscription has an expiry date and the next billing period comes after the expiration, return 0
		if ( isset( $subscription['expiry_date'] ) && 0 != $subscription['expiry_date'] && ( $next_payment_timestamp + 120 ) > strtotime( $subscription['expiry_date'] ) )
			$next_payment_timestamp =  0;

		$next_payment = ( 'mysql' == $type && 0 != $next_payment_timestamp ) ? date( 'Y-m-d H:i:s', $next_payment_timestamp ) : $next_payment_timestamp;

		return apply_filters( 'woocommerce_subscriptions_calculated_next_payment_date', $next_payment, $order, $product_id, $type, $from_date, $from_date_arg );

	}

	###
	# WCS 2.0 methods
	###

	/**
	 * Check if the WCS is >= 2.0
	 *
	 * @return bool
	 */
	public static function is_wcs_2() {
		return function_exists('wcs_get_subscription');
	}

	/**
	 * Backwards-compatible alias method for wcs_order_contains_subscription
	 * @param WC_Order $order
	 * @return bool
	 */
	public static function order_contains_subscription( $order ) {
		if ( self::is_wcs_2() ) {
			return wcs_order_contains_subscription( $order );
		} else {
			return WC_Subscriptions_Order::order_contains_subscription( $order );
		}
	}

	/**
	 * Get the subscription key from an order
	 *
	 * @param int $order_id
	 * @param int $product_id
	 * @return string
	 */
	public static function get_subscription_key( $order_id, $product_id = '' ) {
		if ( self::is_wcs_2() ) {
			$subs = wcs_get_subscription( $order_id );

			if ( !$subs ) {
				return $order_id .'_'. $product_id;
			}
			return wcs_get_old_subscription_key( $subs );
		} else {
			return WC_Subscriptions_Manager::get_subscription_key( $order_id, $product_id );
		}
	}

	public static function get_subscription( $id ) {
		if ( self::is_wcs_2() ) {
			return wcs_get_subscription( $id );
		} else {
			return WC_Subscriptions_Manager::get_subscription( $id );
		}
	}

	public static function get_failed_payment_count( $order, $product_id ) {
		if ( self::is_wcs_2() ) {
			$subscription = wcs_get_subscription( $order );
			return $subscription->get_failed_payment_count();
		} else {
			return WC_Subscriptions_Order::get_failed_payment_count( $order, $product_id );
		}
	}

	public static function get_next_payment_date( $subs_key ) {

	}

	public static function get_subscription_meta( $subs_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subs_key );

		$data = array(
			'item'              => WC_Subscriptions_Order::get_item_by_subscription_key( $subs_key ),
			'start_date'        => $subscription['start_date'],
			'trial_end_date'    => $subscription['trial_expiry_date'],
			'trial_length'      => '',
			'last_payment_date' => $subscription['last_payment_date'],
			'next_payment_date' => WC_Subscriptions_Manager::get_next_payment_date( $subs_key ),
			'end_date'          => WC_Subscriptions_Manager::get_subscription_expiration_date( $subs_key ),
			'days_to_renew'     => '',
			'first_payment_cost'=> wc_price( WC_Subscriptions_Order::get_total_initial_payment($subscription['order_id'], $subscription['product_id'] ) ),
			'cost'              => wc_price( WC_Subscriptions_Order::get_recurring_total( $subscription['order_id'] ) ),
			'cost_term'         => '',
		);

		$subscription_details = array(
			'currency'              => WC_Subscriptions_Order::get_order_currency( $subscription['order_id'] ),
			'subscription_interval' => WC_Subscriptions_Order::get_subscription_interval( $subscription['order_id'] ),
			'subscription_period'   => WC_Subscriptions_Order::get_subscription_period( $subscription['order_id'] ),
			'recurring_amount'      => WC_Subscriptions_Order::get_recurring_total( $subscription['order_id'] )
		);
		$data['cost_term'] = WC_Subscriptions_Manager::get_subscription_price_string( $subscription_details );

		if ( $data['start_date'] ) {
			$data['start_date'] = date_i18n( wc_date_format(), strtotime( $data['start_date'] ) );
		}

		if ( $data['trial_end_date'] ) {
			$data['trial_end_date'] = date_i18n( wc_date_format(), strtotime( $data['trial_end_date'] ) );
		}

		$trial_length = WC_Subscriptions_Order::get_subscription_trial_length( $subscription['order_id'], $subscription['product_id'] );

		if ( $trial_length > 0 ) {
			$trial_period = WC_Subscriptions_Order::get_subscription_trial_period( $subscription['order_id'], $subscription['product_id'] );

			if ( $trial_length == 1 ) {
				$data['trial_length'] = sprintf( __('1 %s', 'woocommerce-subscriptions'), $trial_period );
			} else {
				$data['trial_length'] = WC_Subscriptions_Manager::get_subscription_period_strings( $trial_length, $trial_period );
			}
		}

		if ( $data['next_payment_date'] ) {
			$timestamp  = strtotime( $data['next_payment_date'] );
			$data['next_payment_date'] = date_i18n( wc_date_format(), $timestamp );

			// calc days to renew
			$now    = current_time( 'timestamp', true );
			$diff   = $timestamp - $now;

			if ( $diff > 0 ) {
				$data['days_to_renew'] = floor( $diff / 86400 );
			}
		}

		if ( $data['end_date'] == 0 ) {
			$data['end_date'] = __('Until cancelled', 'follow_up_emails');
		} else {
			$data['end_date'] = date_i18n( wc_date_format(), strtotime( $data['end_date'] ) );
		}

		return $data;
	}

}

$GLOBALS['fue_subscriptions'] = new FUE_Addon_Subscriptions();
