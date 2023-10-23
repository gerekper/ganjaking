<?php
/**
 * Stamps account balance handler class.
 *
 * @package WC_Stamps_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stamps account balance handler.
 */
class WC_Stamps_Balance {

	/**
	 * Instance of WC_Logger.
	 *
	 * @var WC_Logger
	 */
	private static $logger;

	/**
	 * Whether logging is enabled or not ('yes' or 'no').
	 *
	 * @since 1.3.17
	 * @var string
	 */
	private static $logging_enabled = null;

	/**
	 * Constructor
	 *
	 * Set callbacks to WP hooks.
	 */
	public function __construct() {
		// Cron jobs.
		add_action( 'wc_stamps_do_top_up', array( $this, 'top_up' ), 10, 3 );
		add_action( 'wc_stamps_check_payment_status', array( $this, 'check_payment_status' ), 10, 4 );

		// Admin only hooks.
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 999 );
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'admin_init', array( $this, 'stamps_redirect' ) );
		}
	}

	/**
	 * Add log entry.
	 *
	 * @since 1.3.17 Check if logging is enabled before logging.
	 * @param string $message Message to log.
	 */
	public static function log( $message ) {
		// Cache it, so we don't call `get_option()` everytime log is called.
		if ( is_null( self::$logging_enabled ) ) {
			self::$logging_enabled = get_option( 'wc_settings_stamps_logging', 'no' );
		}

		if ( 'yes' !== self::$logging_enabled ) {
			return;
		}

		if ( ! self::$logger ) {
			self::$logger = new WC_Logger();
		}
		self::$logger->add( 'stamps-balance', $message );
	}

	/**
	 * Show balance on admin bar.
	 */
	public function admin_bar() {
		global $wp_admin_bar;

		if ( ! is_admin() || ! is_admin_bar_showing() || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$balance = $this->get_current_balance( isset( $_GET['wc-stamps-refresh'] ) && check_admin_referer( 'wc-stamps-refresh' ) );

		if ( false === $balance ) {
			$balance_string     = __( 'API Error', 'woocommerce-shipping-stamps' );
			$balance_node_class = 'error-api';
		} else {
			$balance_string     = '$' . number_format( $balance, 2, '.', ',' );
			$balance_node_class = '';
		}

		$wp_admin_bar->add_node( array(
			'id'     => 'stamps-com',
			'parent' => 'top-secondary',
			'title'  => '<span class="ab-icon"></span> Stamps: ' . $balance_string,
			'href'   => wp_nonce_url( add_query_arg( 'wc-stamps-refresh', 'true' ), 'wc-stamps-refresh' ),
			'meta'   => array( 'class' => $balance_node_class ),
		) );

		$wp_admin_bar->add_menu( array(
			'parent' => 'stamps-com',
			'id'     => 'stamps-com-topup',
			'title'  => __( 'Top-up Balance', 'woocommerce-shipping-stamps' ),
			'href'   => wp_nonce_url( admin_url( 'index.php?page=wc-stamps-topup' ), 'wc-stamps-topup', '_wpnonce_stamps-topup' ),
			'meta'   => false,
		) );

		$menu_links = array(
			'StoreMyProfile'         => __( 'My profile', 'woocommerce-shipping-stamps' ),
			'StorePaymentMethods'    => __( 'Stamps payment methods', 'woocommerce-shipping-stamps' ),
			'OnlineReportingClaim'   => __( 'Online claim form', 'woocommerce-shipping-stamps' ),
			'OnlineReportingSCAN'    => __( 'Online SCAN form', 'woocommerce-shipping-stamps' ),
			'OnlineReportingPickup'  => __( 'Schedule a pickup', 'woocommerce-shipping-stamps' ),
			'OnlineReportingRefund'  => __( 'Refunds', 'woocommerce-shipping-stamps' ),
			'OnlineReportingHistory' => __( 'History', 'woocommerce-shipping-stamps' ),
		);

		foreach ( $menu_links as $key => $value ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'stamps-com',
				'id'     => 'stamps-com-' . sanitize_title( $key ),
				'title'  => $value,
				'href'   => wp_nonce_url( add_query_arg( 'stamps_redirect', $key, admin_url() ), 'stamps-redirect' ),
				'meta'   => false,
			) );
		}
	}

	/**
	 * Add admin menus/screens.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menus() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}

		switch ( $_GET['page'] ) {
			case 'wc-stamps-topup' :
				check_admin_referer( 'wc-stamps-topup','_wpnonce_stamps-topup' );
				$page = add_dashboard_page( __( 'Stamps.com balance top-up', 'woocommerce-shipping-stamps' ), __( 'Stamps.com top-up', 'woocommerce-shipping-stamps' ), 'manage_woocommerce', 'wc-stamps-topup', array( $this, 'topup_screen' ) );
			break;
		}
	}

	/**
	 * Remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'wc-stamps-topup' );
	}

	/**
	 * Redirect to stamps.com.
	 */
	public function stamps_redirect() {
		if ( ! empty( $_GET['stamps_redirect'] ) && check_admin_referer( 'stamps-redirect' ) ) {
			$url = WC_Stamps_API::get_url( sanitize_text_field( $_GET['stamps_redirect'] ) );

			if ( $url ) {
				// Redirect to Stamps.com.
				// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				// nosemgrep:audit.php.wp.security.unsafe-wp-redirect
				wp_redirect( $url );
				exit;
			}
		}
	}

	/**
	 * Get the current balance for the user.
	 *
	 * @param bool $force_update Whether to force update. Default false.
	 *
	 * @return mixed False in API error OR float from API.
	 */
	public static function get_current_balance( $force_update = false ) {
		$wc_stamps_balance = get_transient( 'wc_stamps_balance' );

		if ( false === $wc_stamps_balance || $force_update ) {
			$info = WC_Stamps_API::get_account_info();

			if ( isset( $info->AccountInfo ) && isset( $info->AccountInfo->PostageBalance ) ) {
				$wc_stamps_balance = $info->AccountInfo->PostageBalance->AvailablePostage;
				set_transient( 'wc_stamps_balance', $info->AccountInfo->PostageBalance->AvailablePostage, DAY_IN_SECONDS );
				set_transient( 'wc_stamps_control_total', $info->AccountInfo->PostageBalance->ControlTotal, DAY_IN_SECONDS );
			} else {
				$wc_stamps_balance = 'API_ERROR';
				set_transient( 'wc_stamps_balance', $wc_stamps_balance , DAY_IN_SECONDS );
				set_transient( 'wc_stamps_control_total', $wc_stamps_balance, DAY_IN_SECONDS );
			}
		}

		// Caller of this method expects bool or float.
		if ( 'API_ERROR' === $wc_stamps_balance ) {
			$wc_stamps_balance = false;
		}

		return $wc_stamps_balance;
	}

	/**
	 * Get current control total.
	 *
	 * Control total is the amount of postage that the user has CONSUMED over
	 * the LIFETIME of the account.
	 *
	 * @param bool $force_update Whether to force update to retrieve control
	 *                           total. Default to false.
	 *
	 * @return float
	 */
	public static function get_current_control_total( $force_update = false ) {
		$wc_stamps_control_total = get_transient( 'wc_stamps_control_total' );
		if ( false === $wc_stamps_control_total || $force_update ) {
			$info = WC_Stamps_API::get_account_info();

			if ( isset( $info->AccountInfo ) && isset( $info->AccountInfo->PostageBalance ) ) {
				$wc_stamps_control_total = $info->AccountInfo->PostageBalance->ControlTotal;
				set_transient( 'wc_stamps_balance', $info->AccountInfo->PostageBalance->AvailablePostage, DAY_IN_SECONDS );
				set_transient( 'wc_stamps_control_total', $info->AccountInfo->PostageBalance->ControlTotal, DAY_IN_SECONDS );
			} else {
				$wc_stamps_control_total = false;
			}
		}

		return $wc_stamps_control_total;
	}

	/**
	 * Top up user balance with amount `$amount`.
	 *
	 * Hooked to the `wc_stamps_do_top_up` action, which is called while handling
	 * the `wp_schedule_single_event` task scheduled by `self::schedule_top_up`.
	 *
	 * @param int   $amount        Amount of postage to purchase.
	 * @param float $control_total The amount of postage that the user has CONSUMED
	 *                             over the LIFETIME of the account.
	 * @param int   $attempt       Which attempt this is.
	 *
	 * @return bool|WP_Error
	 */
	public function top_up( $amount, $control_total, $attempt ) {
		self::log( sprintf( '%1$s - Topping up with amount = %2$s, control total = %3$s.', __METHOD__, $amount, $control_total ) );

		wp_clear_scheduled_hook( 'wc_stamps_do_top_up', array( $amount, $control_total ) );

		// Check control total for event matches stored control total before
		// proceeding.
		//
		// Note that `this->get_current_control_total()` ALSO updates (on success)
		// the transient.
		if ( $control_total != $this->get_current_control_total() ) {
			self::log( sprintf( '%1$s - Control total (%2$s) does not match current balance (%3$s).', __METHOD__, $control_total, $this->get_current_control_total() ) );
			return;
		}

		$result = WC_Stamps_API::purchase_postage( $amount, $control_total );

		if ( is_wp_error( $result ) ) {
			// If there was an error, either the token was invalid or the control
			// total was wrong. Refresh total and reschedule.
			if ( 3 >= $attempt ) {
				// If we haven't re-tried more than 3 times already, schedule
				// another not-forced top-up.
				self::schedule_top_up( $amount, $this->get_current_control_total( true ), false, $attempt );
				self::log( sprintf( '%1$s - Topping up error: %2$s.', __METHOD__, $result->get_error_message() ) );
			} else {
				$notice = 'Hi, '
						. 'Stamps.com plugin top up attempts have failed. There may be problem on your server. '
						. 'If you are unable to see your account balance on the WordPress admin contact Stamps.com for more
						 information on the connection issue.';
				self::log( $notice );
				wp_mail( get_bloginfo( 'admin_email' ), get_bloginfo( 'name' ) . ': Stamps.com top up Failed', $notice );
			}
		} else {
			switch ( $result->PurchaseStatus ) {
				case 'Pending' :
				case 'Processing' :
					wp_schedule_single_event( time() + 8, 'wc_stamps_check_payment_status', array( $amount, $control_total, $result->TransactionID, 1 ) );
					self::log( sprintf( '%s - Top up pending.' ) );
				break;
				case 'Rejected' :
					wp_mail( get_option( 'admin_email' ), __( 'Stamps.com top-up failure', 'woocommerce-shipping-stamps' ), $result->RejectionReason );
					self::log( sprintf( '%1$s - Top up rejected: %2$s.', __METHOD__, $result->RejectionReason ) );
				break;
				case 'Success' :
					self::log( sprintf( '%s - Top up successful.', __METHOD__ ) );
				break;
			}
		}
	}

	/**
	 * Check status of a top up.
	 *
	 * @param int    $amount          Amount to top up. Must be an integer.
	 * @param float  $control_total   Current balance.
	 * @param string $transaction_id Transaction ID.
	 * @param int    $attempt         Number to retry if failed to top up.
	 */
	public function check_payment_status( $amount, $control_total, $transaction_id, $attempt ) {
		self::log( 'Checking payment status: ' . $amount . '. Attempt #' . $attempt );

		wp_clear_scheduled_hook( 'wc_stamps_check_payment_status', array( $amount, $control_total, $transaction_id, $attempt ) );

		$result = WC_Stamps_API::get_purchase_status( $transaction_id );

		if ( ! is_wp_error( $result ) ) {
			switch ( $result->PurchaseStatus ) {
				case 'Pending' :
				case 'Processing' :
					if ( $attempt < 5 ) {
						wp_schedule_single_event( time() + ( min( 8 * $attempt, 32 ) ), 'wc_stamps_check_payment_status', array( $amount, $control_total, $transaction_id, $attempt + 1 ) );
						self::log( sprintf( '%s - Top up still pending.', __METHOD__ ) );
					} else {
						self::log( sprintf( '%s - Top up payment status check failed', __METHOD__ ) );
					}
				break;
				case 'Rejected' :
					wp_mail( get_option( 'admin_email' ), __( 'Stamps.com top-up failure', 'woocommerce-shipping-stamps' ), $result->RejectionReason );
					self::log( sprintf( '%1$s - Top up rejected: %2$s.', __METHOD__, $result->RejectionReason ) );
				break;
				case 'Success' :
					self::log( sprintf( '%s - Top up successful.', __METHOD__ ) );
				break;
			}
		}
	}

	/**
	 * Checks whether automatic top-up should happen or not.
	 *
	 * @param mixed $balance Balance.
	 *
	 * @since 1.3.3
	 * @version 1.3.3
	 *
	 * @return bool
	 */
	protected static function is_automatic_topup_needed( $balance ) {
		if ( ! is_numeric( $balance ) ) {
			return false;
		}

		$purchase_amount = (float) get_option( 'wc_settings_stamps_purchase_amount' );
		if ( ! $purchase_amount ) {
			self::log( sprintf( '%s - No top-up amount specified, skipping automatic topup.', __METHOD__ ) );
			return false;
		}

		// Minimum purchase is $10.
		if ( $purchase_amount < 10 ) {
			self::log( sprintf( '%1$s - Top-up purchase amount %2$s is too low. Must be at least 10.', __METHOD__, $purchase_amount ) );
			return false;
		}

		$threshold = (float) get_option( 'wc_settings_stamps_top_up_threshold' );
		if ( ! $threshold ) {
			self::log( sprintf( '%s - Invalid top-up threshold.', __METHOD__ ) );
			return false;
		}

		$topup_needed = $balance < $threshold;
		if ( ! $topup_needed ) {
			self::log( sprintf( '%1$s - Automatic top-up is NOT needed. Balance (%2$s) >= threshold (%3$s).', __METHOD__, $balance, $threshold ) );
			return false;
		}

		self::log( sprintf( '%s - Automatic top-up is needed. Balance (%2$s) < threshold (%3$s).', __METHOD__, $balance, $threshold ) );

		return true;
	}

	/**
	 * See if we need to top up soon.
	 *
	 * Called by `WC_Stamps_API::update_balance()`.
	 *
	 * @param float $balance Balance.
	 */
	public static function check_balance( $balance ) {
		if ( self::is_automatic_topup_needed( $balance ) ) {
			self::schedule_top_up( absint( get_option( 'wc_settings_stamps_purchase_amount' ) ), self::get_current_control_total() );
		}
	}

	/**
	 * Schedule events for topping up event.
	 *
	 * Called either by `self::topup_screen()` (when the user requests a manual top-up)
	 * or by `self::check_balance()`.
	 *
	 * Note: `$force` is `true` when called by `self::topup_screen()` (when the
	 * user requests a manual top-up). The top up event is always schedule for
	 * just 8 seconds in the future.
	 *
	 * @param int   $amount        The amount of postage to purchase.
	 * @param float $control_total This is the amount of postage that the user
	 *                             has CONSUMED over the LIFETIME of the account.
	 * @param bool  $force         Whether or not to schedule a top-up even if
	 *                             a top-up is already scheduled.
	 * @param int   $attempt       Which attempt this is.
	 */
	public static function schedule_top_up( $amount, $control_total, $force = false, $attempt = 0 ) {
		// If this is not a user-requested (or forced) top-up, double check
		// before scheduling the job.
		if ( ! $force ) {
			$balance = get_transient( 'wc_stamps_balance' );
			if ( ! self::is_automatic_topup_needed( $balance ) ) {
				return;
			}
		}

		++$attempt;
		if ( ! wp_next_scheduled( 'wc_stamps_do_top_up', array( $amount, $control_total, $attempt ) ) || $force ) {
			// Schedule top up.
			wp_schedule_single_event( time() + 8, 'wc_stamps_do_top_up', array( $amount, $control_total, $attempt ) );

			self::log( sprintf( '%1$s - Top up scheduled with amount = %2$s.', __METHOD__, $amount ) );
		} else {
			self::log( sprintf( '%1$s - Top up already scheduled with amount = %2$s.', __METHOD__, $amount ) );
		}
	}

	/**
	 * Screen for adding stamps balance manually.
	 */
	public function topup_screen() {
		if ( ! empty( $_POST['stamps_topup_amount'] ) && check_admin_referer( 'woocommerce-stamps-topup' ) ) {
			// Schedule a forced top-up (i.e. a top-up that happens even if other
			// top-ups are already scheduled).
			self::schedule_top_up( absint( $_POST['stamps_topup_amount'] ), $this->get_current_control_total(), true );

			echo '<div class="updated"><p>' . esc_html__( 'Top-up request sent. Your balance should appear shortly if successful.', 'woocommerce-shipping-stamps' ) . '</p></div>';
		}
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Add Stamps.com Balance', 'woocommerce-shipping-stamps' ); ?></h2>
			<p><?php esc_html_e( 'Enter the amount of postage (in dollars) you wish to purchase. It can take a few minutes for this postage to show up in your account.', 'woocommerce-shipping-stamps' ); ?></p>

			<form method="POST">
				<table class="form-table">
					<tr>
						<th><label for="stamps_topup_amount"><?php esc_html_e( 'Amount', 'woocommerce-shipping-stamps' ); ?></label></th>
						<td>
							<input name="stamps_topup_amount" id="stamps_topup_amount" type="number" pattern="\d*" placeholder="<?php echo esc_attr(wc_format_localized_price( 0 )); ?>" min="10" value="10" />
							<p class="description"><?php esc_html_e( 'How much balance you wish to purchase in whole dollars e.g. <code>100</code>.', 'woocommerce-shipping-stamps' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<?php wp_nonce_field( 'woocommerce-stamps-topup' ); ?>
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Purchase postage', 'woocommerce-shipping-stamps' ); ?></button>
				</p>
			</form>
		</div>
		<?php
	}
}
new WC_Stamps_Balance();
