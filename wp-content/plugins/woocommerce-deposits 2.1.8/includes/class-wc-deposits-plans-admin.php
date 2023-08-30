<?php
/**
 * Deposits plans admin
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Plans_Admin class.
 */
class WC_Deposits_Plans_Admin {

	/**
	 * Class Instance
	 *
	 * @var WC_Deposits_Plans_Admin
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_item' ), 25 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
	}

	/**
	 * Scripts.
	 */
	public function styles_and_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'woocommerce-deposits-payment-plans', WC_DEPOSITS_PLUGIN_URL . '/assets/js/payment-plans' . $suffix . '.js', array( 'jquery' ), WC_DEPOSITS_VERSION, true );
		wp_localize_script(
			'woocommerce-deposits-payment-plans',
			'wc_deposits_payment_plans_params',
			array(
				'i18n_delete_plan' => __( 'Are you sure you want to delete this plan? This action cannot be undone.', 'woocommerce-deposits' ),
			)
		);
		wp_enqueue_style( 'wc-deposits-admin', plugins_url( '/assets/css/admin.css', WC_DEPOSITS_FILE ), array(), WC_DEPOSITS_VERSION );
	}

	/**
	 * Add a menu item for the payment plans screen.
	 */
	public function add_menu_item() {
		$page = add_submenu_page( 'edit.php?post_type=product', __( 'Payment Plans', 'woocommerce-deposits' ), __( 'Payment Plans', 'woocommerce-deposits' ), 'manage_woocommerce', 'deposit_payment_plans', array( $this, 'output' ) );
	}
	/**
	 * Register the deposits screen ID.
	 *
	 * @param array $ids Existing screens IDs.
	 * @return array
	 */
	public function add_screen_id( $ids = array() ) {
		$ids[] = 'product_page_deposit_payment_plans';
		return $ids;
	}

	/**
	 * Output the admin screen.
	 */
	public function output() {
		global $wpdb;

		wp_enqueue_script( 'woocommerce-deposits-payment-plans' );

		if ( ! empty( $_POST ) ) {
			$plan_amounts = filter_input( INPUT_POST, 'plan_amount', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$result       = $this->validate_plan_amounts( $plan_amounts ) ? $this->maybe_save_plan() : false;

			if ( is_wp_error( $result ) ) {
				echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
			} elseif ( $result ) {
				echo '<div class="updated success"><p>' . esc_html__( 'Plan saved successfully', 'woocommerce-deposits' ) . '</p></div>';
			}
		}

		if ( isset( $_GET['delete_plan'] ) && check_admin_referer( 'delete_plan' ) ) {
			$deleting_id = absint( $_GET['delete_plan'] );
			$wpdb->delete( $wpdb->wc_deposits_payment_plans, array( 'ID' => $deleting_id ) );
			$wpdb->delete( $wpdb->wc_deposits_payment_plans_schedule, array( 'plan_id' => $deleting_id ) );
			echo '<div class="updated success"><p>' . esc_html__( 'Plan deleted successfully', 'woocommerce-deposits' ) . '</p></div>';
		}

		$plan_name        = '';
		$plan_description = '';
		$payment_schedule = array( (object) array( 'amount' => 0 ) );

		if ( ! empty( $_REQUEST['plan_id'] ) ) {
			$editing          = absint( $_REQUEST['plan_id'] );
			$plan             = WC_Deposits_Plans_Manager::get_plan( $editing );
			$plan_name        = $plan->get_name();
			$plan_description = $plan->get_description();
			$payment_schedule = $plan->get_schedule();
			include 'views/html-edit-payment-plan.php';
		} else {

			$editing = false;
			include 'views/html-payment-plans.php';
		}
	}

	/**
	 * Output a table of plans.
	 */
	public function output_plans() {
		if ( ! class_exists( 'WC_Deposits_Plans_Table' ) ) {
			require_once 'list-tables/class-wc-deposits-plans-table.php';
		}
		$table = new WC_Deposits_Plans_Table();
		$table->prepare_items();
		$table->display();
	}

	/**
	 * Validate plan amounts. Decline empty or 0.
	 *
	 * @param array $plan_amounts Plan amounts.
	 *
	 * @return bool valid or invalid.
	 */
	public function validate_plan_amounts( $plan_amounts = array() ) {
		if ( count( $plan_amounts ) ) {
			if ( in_array( '', $plan_amounts, true ) ) {
				echo '<div class="notice error is-dismissible"><p>' . esc_html__( 'Please enter a valid amount', 'woocommerce-deposits' ) . '</p></div>';

				return false;
			}
		}

		return true;
	}

	/**
	 * Save a posted plan.
	 */
	public function maybe_save_plan() {
		global $wpdb;

		if ( isset( $_POST['plan_name'] ) ) {
			if ( ! empty( $_REQUEST['plan_id'] ) ) {
				$editing = absint( $_REQUEST['plan_id'] );
			} else {
				$editing = false;
			}

			if ( ! check_admin_referer( 'woocommerce_save_plan', 'woocommerce_save_plan_nonce' ) ) {
				return new WP_Error( 'error', __( 'Unable to save payment plan - please try again', 'woocommerce-deposits' ) );
			}

			$abs_round = function ( $value ) {
				/**
				 * Filters deposits payment plan amount decimals.
				 *
				 * @since 1.5.9
				 *
				 * @param string Plan amount decimals.
				 */
				$amount_decimals = apply_filters( 'woocommerce_deposits_plan_amount_decimals', 4 );
				return round( abs( $value ), absint( $amount_decimals ) );
			};

			$plan_id               = $editing;
			$plan_name             = empty( $_POST['plan_name'] ) ? __( 'Payment Plan', 'woocommerce-deposits' ) : sanitize_text_field( wp_unslash( $_POST['plan_name'] ) );
			$plan_description      = empty( $_POST['plan_description'] ) ? '' : wp_kses_post( wp_unslash( $_POST['plan_description'] ) );
			$plan_amounts          = array_map( $abs_round, $_POST['plan_amount'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized in $abs_round closure
			$plan_interval_amounts = array_map( 'absint', $_POST['plan_interval_amount'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized with absint
			$plan_interval_units   = array_map( 'sanitize_text_field', $_POST['plan_interval_unit'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized
			$payment_schedule      = array();

			if ( $editing ) {
				$wpdb->update(
					$wpdb->wc_deposits_payment_plans,
					array(
						'name'        => $plan_name,
						'description' => $plan_description,
					),
					array(
						'ID' => $editing,
					)
				);
			} else {
				$wpdb->insert(
					$wpdb->wc_deposits_payment_plans,
					array(
						'name'        => $plan_name,
						'description' => $plan_description,
					)
				);
				$plan_id = $wpdb->insert_id;
			}

			if ( ! $plan_id ) {
				return new WP_Error( 'error', __( 'Unable to save payment plan', 'woocommerce-deposits' ) );
			}

			// Get posted schedule.
			foreach ( $plan_amounts as $index => $plan_amount ) {
				$payment_schedule[] = array(
					'schedule_index'  => $index,
					'plan_id'         => $plan_id,
					'amount'          => $plan_amount,
					'interval_amount' => $plan_interval_amounts[ $index ],
					'interval_unit'   => $plan_interval_units[ $index ],
				);
			}

			// Get existing schedule.
			$existing_schedule = $wpdb->get_results( $wpdb->prepare( "SELECT schedule_index, plan_id, amount, interval_amount, interval_unit FROM {$wpdb->wc_deposits_payment_plans_schedule} WHERE plan_id = %d;", $plan_id ), ARRAY_A );

			// Clear and update.
			$wpdb->delete( $wpdb->wc_deposits_payment_plans_schedule, array( 'plan_id' => $plan_id ) );

			// Insert.
			foreach ( $payment_schedule as $payment_schedule_row ) {
				$wpdb->insert( $wpdb->wc_deposits_payment_plans_schedule, $payment_schedule_row );
			}

			return true;
		}

		return false;
	}
}

WC_Deposits_Plans_Admin::get_instance();
