<?php
/**
 * Installation related functions and actions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'FUE_Install' ) ) :
class FUE_Install {

	private $updates = array(
		'7.0',
		'7.1',
		'7.3',
		'7.4',
		'7.5',
		'7.6',
		'7.9',
		'7.13',
		'7.14',
		'7.15',
		'7.17',
		'7.18',
		'7.19.4',
		'20160113',
		'20160203',
		'20160308',
		'20160602',
		'20171201',
		'20171211',
		'20180215',
	);

	public function __construct() {
		// Update notice.
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );

		// Install.
		register_activation_hook( FUE_FILE, array( $this, 'install' ) );

		// Deactivate.
		register_deactivation_hook( FUE_FILE, array( $this, 'deactivate' ) );

		if ( get_option( 'fue_init_daily_summary', false ) ) {
			add_action( 'init', array( $this, 'init_daily_summary' ) );
		}

		if ( get_option( 'fue_init_usage_import', false ) ) {
			add_action( 'init', array( $this, 'init_usage_import' ) );
		}

		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'admin_init', array( $this, 'actions' ), 1 );
	}

	/**
	 * Register admin notices.
	 */
	public function add_notices() {
		if ( 'yes' === get_option( 'fue_staging' ) ) {
			add_action( 'admin_notices', array( $this, 'staging_notice' ) );
		}

		if ( true !== (bool) get_option( 'fue_welcome_notice', false ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}

		if ( get_option( 'fue_needs_update' ) == 1 ) {
			add_action( 'admin_notices', array( $this, 'install_notice' ) );
		}

		if ( ! empty( $_GET['fue-data-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'admin_notices', array( $this, 'updated_notice' ) );
		}
	}

	/**
	 * Display a welcome notice.
	 */
	public function welcome_notice() {
		update_option( 'fue_welcome_notice', true );
	?>
		<div class="updated">
			<p>
				<strong><?php esc_html_e( 'Thanks for installing Follow-Up Emails.', 'follow_up_emails' ); ?></strong>
			</p>
			<p>
				<?php
					printf(
						/* translators: %1$s email types documentation link, %3$s campaigns link, %4$s documentation link */
						esc_html__( 'Before diving in, we highly recommend taking the time to read about %1$semail types%2$s and %3$scampaigns%2$s in the %4$sdocumentation%2$s.', 'follow_up_emails' ),
						'<a href="https://docs.woocommerce.com/document/automated-follow-up-emails-docs/">',
						'</a>',
						'<a href="https://docs.woocommerce.com/document/automated-follow-up-emails-docs/email-campaigns/">',
						'<a href="https://docs.woocommerce.com/document/automated-follow-up-emails-docs/">'
					);
				?>
			</p>
			<p>
				<?php
					printf(
						/* translators: %1$s create new email campaign link, %2$s link end */
						esc_html__( 'Ready to get started? %1$sCreate a new email campaign%2$s.', 'follow_up_emails' ),
						'<a href="' . esc_url( admin_url( 'post-new.php?post_type=follow_up_email' ) ) . '">',
						'</a>'
					);
				?>
			</p>
		</div>
	<?php
	}

	/**
	 * Display a notice requiring a data update.
	 */
	public function install_notice() {
		// If we need to update, include a message with the update button
		if ( get_option( 'fue_needs_update' ) == 1 ) {
			?>
			<div id="message" class="updated">
				<p><strong><?php esc_html_e( 'Follow-Up Emails Data Update Required', 'follow_up_emails' ); ?></strong></p>
				<p class="submit"><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'fue_update', 'true', admin_url( 'admin.php?page=followup-emails' ) ), 'fue-update' ) ); ?>" class="fue-update-now button-primary"><?php esc_html_e( 'Run the updater', 'follow_up_emails' ); ?></a></p>
			</div>
			<script type="text/javascript">
				jQuery( '.fue-update-now' ).on( 'click', function() {
					var answer = confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'follow_up_emails' ) ); ?>' );
					return answer;
				} );
			</script>
			<?php
		}
	}

	/**
	 * Display a notice that staging mode is enabled.
	 * @since 4.7.0
	 */
	public function staging_notice() {
		// If we need to update, include a message with the update button
		if ( 'yes' === get_option( 'fue_staging' ) ) {
			?>
			<div id="message" class="updated">
				<p><strong><?php esc_html_e( 'Follow-Up Emails Staging Mode', 'follow_up_emails' ); ?></strong></p>
				<p><?php esc_html_e( 'Follow-Up Emails is in staging mode. All emails will be prevented from being sent out.', 'follow_up_emails' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Display a notice after the FUE data has been updated
	 */
	public function updated_notice() {
		?>
		<div id="message" class="updated">
			<p><?php esc_html_e( 'Data updates have been successfully applied!', 'follow_up_emails' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Checks for changes in the version and prompt to update if necessary
	 */
	public function check_version() {
		$db_version = get_option( 'fue_db_version' );
		if ( ! defined( 'IFRAME_REQUEST' ) && $db_version != Follow_Up_Emails::$db_version ) {
			$this->install();

			do_action( 'fue_updated' );
		}
	}

	/**
	 * Listens for button actions such as clicking on the 'Update Data' button
	 */
	public function actions() {

		if ( ! empty( $_GET['fue_update'] ) ) {
			if ( ! current_user_can( 'manage_follow_up_emails' ) || ! check_admin_referer( 'fue-update' ) ) {
				wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
			}
			$this->update();

			// Update complete
			delete_option( 'fue_needs_update' );

			wp_safe_redirect( add_query_arg( 'fue-data-updated', 1, wp_get_referer() ) );
			exit;
		}

	}

	/**
	 * The install method that is ran when Follow_Up_Emails is activated
	 */
	public function install() {
		require_once FUE_INC_DIR . '/class-follow-up-emails.php';
		require_once FUE_INC_DIR . '/fue-functions.php';

		$this->create_options();
		$this->create_tables();
		$this->create_role();

		// delete the pages if they exist
		$this->delete_pages();

		// setup the daily summary emails on the next page load
		update_option( 'fue_init_daily_summary', true );
		update_option( 'fue_init_usage_import', true );

		// Queue upgrades
		$current_version    = get_option( 'fue_version', null );
		$current_db_version = get_option( 'fue_db_version', null );
		$major_version      = substr( FUE_VERSION, 0, strrpos( FUE_VERSION, '.' ) );

		flush_rewrite_rules();

		if ( version_compare( $current_db_version, Follow_Up_Emails::$db_version, '<' ) && null !== $current_db_version ) {
			update_option( 'fue_needs_update', 1 );
		} else {
			update_option( 'fue_db_version', Follow_Up_Emails::$db_version );
		}

		// update version
		update_option( 'fue_version', FUE_VERSION );

		do_action( 'fue_install' );
	}

	/**
	 * Update scripts
	 */
	public function update() {
		// Do updates
		$db_version = get_option( 'fue_db_version' );

		foreach ( $this->updates as $version ) {
			if ( version_compare( $db_version, $version, '<' ) ) {
				include( 'updates/update-'. $version .'.php' );
				update_option( 'fue_db_version', $version );
			}
		}
	}

	/**
	 * Triggered when FUE is deactivated. Remove the scheduled action for sending emails
	 */
	public function deactivate() {
		wp_clear_scheduled_hook('sfn_followup_emails');

		do_action( 'fue_uninstall' );
	}

	/**
	 * Install the default options
	 */
	private function create_options() {

	}

	/**
	 * Schedule the daily summary recurring emails
	 */
	public function init_daily_summary() {
		if ( !function_exists('as_next_scheduled_action') ) {
			return;
		}

		if ( as_next_scheduled_action( 'fue_send_summary' ) ) {
			as_unschedule_action( 'fue_send_summary' );
		}

		FUE_Sending_Scheduler::queue_daily_summary_email();

		delete_option( 'fue_init_daily_summary' );

	}

	/**
	 * Make sure the action-scheduler for the data importer is running
	 */
	public function init_usage_import() {
		if ( !function_exists('as_next_scheduled_action') ) {
			return;
		}

		if ( !as_next_scheduled_action( 'sfn_send_usage_report' ) ) {
			as_schedule_recurring_action( time(), 86400, 'sfn_send_usage_report', array(), 'FUE' );
		}

		delete_option( 'fue_init_usage_import' );
	}

	/**
	 * Create the database tables used by FUE
	 *
	 * @return void
	 */
	private function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/*
		 * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
		 * As of WP 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
		 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
		 */
		$max_index_length = 191;

		$fue_tables = "
		CREATE TABLE {$wpdb->prefix}followup_email_excludes (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  email_id bigint(20) NOT NULL DEFAULT 0,
		  order_id bigint(20) NOT NULL DEFAULT 0,
		  email_name varchar(255) NOT NULL,
		  email varchar(100) NOT NULL,
		  date_added DATETIME NOT NULL,
		  KEY email (email),
		  KEY email_id (email_id),
		  KEY order_id (order_id),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_email_orders (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  user_id bigint(20) NOT NULL,
		  user_email varchar(255) NOT NULL,
		  order_id bigint(20) NOT NULL,
		  product_id bigint(20) NOT NULL,
		  email_id varchar(100) NOT NULL,
		  send_on bigint(20) NOT NULL,
		  is_cart int(1) DEFAULT 0 NOT NULL,
		  is_sent int(1) DEFAULT 0 NOT NULL,
		  date_sent datetime NOT NULL,
		  email_trigger varchar(100) NOT NULL,
		  meta TEXT NOT NULL,
		  status INT(1) DEFAULT 1 NOT NULL,
		  KEY user_id (user_id),
		  KEY user_email (user_email($max_index_length)),
		  KEY order_id (order_id),
		  KEY is_sent (is_sent),
		  KEY date_sent (date_sent),
		  KEY status (status),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_coupon_logs (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  coupon_id bigint(20) NOT NULL,
		  coupon_name varchar(100) NOT NULL,
		  email_name varchar(100) NOT NULL,
		  email_address varchar(255) NOT NULL,
		  coupon_code varchar(100) NOT NULL,
		  coupon_used INT(1) DEFAULT 0 NOT NULL,
		  date_sent datetime NOT NULL,
		  date_used datetime NOT NULL,
		  email_id bigint(20) NULL,
		  KEY coupon_id (coupon_id),
		  KEY date_sent (date_sent),
		  KEY email_id (email_id),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_coupons (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  coupon_name varchar(100) NOT NULL,
		  coupon_type varchar(25) default 0 NOT NULL,
		  coupon_prefix varchar(255) default '' NOT NULL,
		  amount double(12,2) default 0.00 NOT NULL,
		  individual int(1) default 0 NOT NULL,
		  exclude_sale_items int(1) default 0 NOT NULL,
		  before_tax int(1) default 0 NOT NULL,
		  free_shipping int(1) default 0 NOT NULL,
		  usage_count bigint(20) default 0 NOT NULL,
		  expiry_value varchar(3) NOT NULL DEFAULT 0,
		  expiry_type varchar(25) NOT NULL DEFAULT '',
		  product_ids varchar(255) NOT NULL DEFAULT '',
		  exclude_product_ids varchar(255) NOT NULL DEFAULT '',
		  product_categories TEXT,
		  exclude_product_categories TEXT,
		  minimum_amount varchar(50) NOT NULL DEFAULT '',
		  maximum_amount varchar(50) NOT NULL DEFAULT '',
		  usage_limit varchar(3) NOT NULL DEFAULT '',
		  usage_limit_per_user varchar(3) NOT NULL DEFAULT '',
		  KEY coupon_name (coupon_name),
		  KEY usage_count (usage_count),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_email_tracking (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  event_type varchar(20) NOT NULL,
		  email_order_id bigint(20) DEFAULT 0 NOT NULL,
		  email_id bigint(20) NOT NULL,
		  user_id bigint(20) DEFAULT 0 NOT NULL,
		  user_email varchar(255) NOT NULL,
		  target_url varchar(255) NOT NULL,
		  client_name varchar(100) NOT NULL,
		  client_version varchar(25) NOT NULL,
		  client_type varchar(50) NOT NULL,
		  user_ip varchar(100) NOT NULL,
		  user_country varchar(100) NOT NULL,
		  date_added datetime NOT NULL,
		  KEY email_id (email_id),
		  KEY user_id (user_id),
		  KEY user_email (user_email($max_index_length)),
		  KEY date_added (date_added),
		  KEY event_type (event_type),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_email_logs (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  email_order_id bigint(20) DEFAULT 0 NOT NULL,
		  email_id bigint(20) NOT NULL,
		  user_id bigint(20) DEFAULT 0 NOT NULL,
		  email_name varchar(100) NOT NULL,
		  customer_name varchar(255) NOT NULL,
		  email_address varchar(255) NOT NULL,
		  date_sent datetime NOT NULL,
		  order_id bigint(20) NOT NULL,
		  product_id bigint(20) NOT NULL,
		  email_trigger varchar(100) NOT NULL,
		  KEY email_name (email_name),
		  KEY user_id (user_id),
		  KEY date_sent (date_sent),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_customers (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  user_id bigint(20) NOT NULL,
		  email_address varchar(255) NOT NULL,
		  total_purchase_price double(10,2) DEFAULT 0 NOT NULL,
		  total_orders int(11) DEFAULT 0 NOT NULL,
		  KEY user_id (user_id),
		  KEY email_address (email_address($max_index_length)),
		  KEY total_purchase_price (total_purchase_price),
		  KEY total_orders (total_orders),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_customer_notes (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  followup_customer_id bigint(20) NOT NULL,
		  author_id bigint(20) NOT NULL,
		  note TEXT,
		  date_added DATETIME NOT NULL,
		  KEY followup_customer_id (followup_customer_id),
		  KEY date_added (date_added),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_customer_orders (
		  followup_customer_id bigint(20) NOT NULL,
		  order_id bigint(20) NOT NULL,
		  price double(10, 2) NOT NULL,
		  KEY followup_customer_id (followup_customer_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_customer_carts (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  user_id bigint(20) NOT NULL DEFAULT 0,
		  first_name varchar(100) NOT NULL,
		  last_name varchar(100) NOT NULL,
		  user_email varchar(100) NOT NULL,
		  cart_items longtext NOT NULL,
		  cart_total double(10, 2) NOT NULL,
		  date_updated DATETIME NOT NULL,
		  KEY user_id (user_id),
		  KEY user_email (user_email),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_order_items (
		  order_id bigint(20) NOT NULL,
		  product_id bigint(20) NOT NULL,
		  variation_id bigint(20) NOT NULL,
		  KEY order_id (order_id),
		  KEY product_id (product_id),
		  KEY variation_id (variation_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_order_categories (
		  order_id bigint(20) NOT NULL,
		  category_id bigint(20) NOT NULL,
		  KEY order_id (order_id),
		  KEY category_id (category_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_subscribers (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  email varchar(100) NOT NULL,
		  first_name varchar(100) NULL,
		  last_name varchar(100) NULL,
		  date_added DATETIME NOT NULL,
		  KEY email (email),
		  KEY date_added (date_added),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_subscriber_lists (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  list_name varchar(100) NOT NULL,
		  access INT(1) NOT NULL DEFAULT 0,
		  KEY list_name (list_name),
		  KEY access (access),
		  PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_subscribers_to_lists (
		  subscriber_id bigint(20) NOT NULL,
		  list_id bigint(20) NOT NULL,
		  KEY subscriber_id (subscriber_id),
		  KEY list_id (list_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}followup_followup_history (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  followup_id bigint(20) NOT NULL,
		  user_id bigint(20) NOT NULL,
		  content longtext NOT NULL,
		  date_added DATETIME NOT NULL,
		  KEY followup_id (followup_id),
		  KEY user_id (user_id),
		  KEY date_added (date_added),
		  PRIMARY KEY  (id)
		) $collate;
		ALTER IGNORE TABLE {$wpdb->prefix}followup_email_excludes ADD UNIQUE INDEX unique_email (email);
		";

		dbDelta( $fue_tables );

		update_option( 'fue_installed_tables', true );

	}

	/**
	 * Create frontend pages that FUE uses
	 * @return void
	 */
	public function create_pages() {
		$this->create_my_subscriptions_page();
		$this->create_unsubscribe_page();
	}

	/**
	 * Delete the created pages
	 */
	public function delete_pages() {
		$page_id = fue_get_page_id('followup_unsubscribe');

		if ( $page_id ) {
			wp_delete_post( $page_id, true );
			delete_option( 'fue_followup_unsubscribe_page_id' );
		}

		$page_id = fue_get_page_id('followup_my_subscriptions');

		if ( $page_id ) {
			wp_delete_post( $page_id, true );
			delete_option( 'fue_followup_my_subscriptions_page_id' );
		}
	}

	/**
	 * Add a new 'fue_manager' role and give it the 'manage_follow_up_emails' capability
	 */
	public function create_role() {
		global $wp_roles;

		//if ( get_role( 'fue_manager' ) !== null )
		//    return;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		add_role( 'fue_manager', __('Follow-Up Emails Manager', 'follow_up_emails'), array(
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true
		) );

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'fue_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Delete the roles and capabilities created by FUE
	 */
	public function remove_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'fue_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'fue_manager' );

	}

	/**
	 * Get capabilities - these are assigned to admin/fue manager during installation or reset
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_follow_up_emails'
		);

		$capability_types = array( 'follow_up_email' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

}
endif;

return new FUE_Install();
