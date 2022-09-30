<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Mailchimp Compatibility Class
 * the admin can set Mailchimp lists to a Membership plan,
 * so when an user will become a member he/she will be added to the Mailchimp lists
 *
 * @class   YITH_WCMBS_Mailchimp_Compatibility
 * @since   1.3.3
 */
class YITH_WCMBS_Mailchimp_Compatibility {

	/** @var \YITH_WCMBS_Mailchimp_Compatibility */
	private static $_instance;

	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	private function __construct() {
		if ( ! $this->_check_methods_exist() ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_options' ) );
		add_action( 'yith_wcmbs_membership_created', array( $this, 'add_email_to_mailchimp_list' ) );

		add_action( 'yith_wcmbs_membership_status_expired', array( $this, 'unsubscribe' ), 10, 2 );
		add_action( 'yith_wcmbs_membership_status_cancelled', array( $this, 'unsubscribe' ), 10, 2 );
	}

	/**
	 * return true if all required methods exist
	 *
	 * @return bool
	 */
	private function _check_methods_exist() {
		return function_exists( 'YITH_WCMC' ) && class_exists( 'YITH_WCMC' ) && method_exists( 'YITH_WCMC', 'subscribe' ) && method_exists( 'YITH_WCMC', 'retrieve_lists' );
	}

	/**
	 * return true if it's possible unsubscribe the user email
	 * requires YITH Mailchimp >= 2.1.1
	 *
	 * @return bool
	 */
	private function _can_unsubscribe() {
		return function_exists( 'YITH_WCMC' ) && class_exists( 'YITH_WCMC' ) && method_exists( 'YITH_WCMC', 'unsubscribe' ) && method_exists( 'YITH_WCMC', 'retrieve_lists' );
	}

	/**
	 * @param $plan_id
	 *
	 * @return array|mixed
	 */
	public function get_plan_lists( $plan_id ) {
		$lists = get_post_meta( $plan_id, 'yith_wcmbs_mailchimp_list', true );

		return ! ! $lists && is_array( $lists ) ? $lists : array();
	}

	/**
	 * @param YITH_WCMBS_Membership $membership
	 *
	 * @return string
	 */
	public function get_user_email_by_membership( $membership ) {
		$user_email = '';
		if ( $membership->order_id && $order = wc_get_order( $membership->order_id ) ) {
			if ( $billing_email = yit_get_prop( $order, 'billing_email' ) ) {
				$user_email = $billing_email;
			}
		}

		if ( ! $user_email && $membership->user_id && $user = get_user_by( 'id', $membership->user_id ) ) {
			$user_email = $user->user_email;
		}

		return $user_email;
	}

	/**
	 * add the user email to te mailchimp list
	 *
	 * @param YITH_WCMBS_Membership $membership
	 */
	public function add_email_to_mailchimp_list( $membership ) {
		if ( ! ! $membership->plan_id ) {
			$lists      = $this->get_plan_lists( $membership->plan_id );
			$user_email = $this->get_user_email_by_membership( $membership );

			if ( $lists && $user_email ) {
				foreach ( $lists as $list ) {
					YITH_WCMC()->subscribe( $list, $user_email );
				}
			}
		}
	}

	/**
	 * unsubscribe the user email from the mailchimp list
	 * requires YITH Mailchimp >= 2.1.1
	 *
	 * @param int                   $membership_id
	 * @param YITH_WCMBS_Membership $membership
	 *
	 * @since 1.3.14
	 */
	public function unsubscribe( $membership_id, $membership ) {
		if ( $this->_can_unsubscribe() ) {
			if ( ! ! $membership->plan_id ) {
				$unsubscribe_on_expiring = get_post_meta( $membership->plan_id, 'yith_wcmbs_mailchimp_unsubscribe_when_expired_or_cancelled', true );
				if ( 'yes' === $unsubscribe_on_expiring ) {
					$lists      = $this->get_plan_lists( $membership->plan_id );
					$user_email = $this->get_user_email_by_membership( $membership );

					if ( $lists && $user_email ) {
						foreach ( $lists as $list ) {
							$args = apply_filters( 'yith_wcmbs_mailchimp_delete_member_when_unsubscribe', false, $membership_id ) ? array( 'delete_member' => true ) : array();
							$args = apply_filters( 'yith_wcmbs_mailchimp_unsubscribe_args', $args, $membership_id );
							YITH_WCMC()->unsubscribe( $list, $user_email, $args );
						}
					}
				}
			}
		}
	}

	/**
	 * register the mailchimp metabox
	 */
	public function register_metabox() {
		add_meta_box( 'yith-wcmbs-mailchimp', __( 'Mailchimp', 'yith-woocommerce-membership' ), array( $this, 'render_metabox' ), 'yith-wcmbs-plan', 'side', 'default' );
	}

	/**
	 * render the mailchimp metabox
	 *
	 * @param $post
	 */
	public function render_metabox( $post ) {
		$lists           = $this->get_plan_lists( $post->ID );
		$mailchimp_lists = YITH_WCMC()->retrieve_lists();

		echo '<p>' . esc_html__( 'Add user email to the following lists when they become members:', 'yith-woocommerce-membership' ) . '</p>';

		echo "<select name='yith_wcmbs_mailchimp_list[]' class='yith-wcmbs-select2' style='width:100%; display: block' multiple>";
		foreach ( $mailchimp_lists as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $lists, true ), true, false ) . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select>';

		if ( $this->_can_unsubscribe() ) {
			$unsubscribe_on_expiring = get_post_meta( $post->ID, 'yith_wcmbs_mailchimp_unsubscribe_when_expired_or_cancelled', true );
			echo '<p>';
			echo "<input type='checkbox' name='yith_wcmbs_mailchimp_unsubscribe_when_expired_or_cancelled' value='yes' " . checked( $unsubscribe_on_expiring, 'yes', false ) . '>';
			echo '<span>' . esc_html__( 'Unsubscribe when expired or cancelled', 'yith-woocommerce-membership' ) . '</span>';
			echo '</p>';
		}
	}

	/**
	 * save the mailchimp lists in plan
	 *
	 * @param $post_id
	 */
	public function save_options( $post_id ) {
		if ( 'yith-wcmbs-plan' == get_post_type( $post_id ) ) {
			if ( isset( $_REQUEST['yith_wcmbs_mailchimp_list'] ) ) {
				update_post_meta( $post_id, 'yith_wcmbs_mailchimp_list', $_REQUEST['yith_wcmbs_mailchimp_list'] );
			}

			update_post_meta( $post_id, 'yith_wcmbs_mailchimp_unsubscribe_when_expired_or_cancelled', isset( $_REQUEST['yith_wcmbs_mailchimp_unsubscribe_when_expired_or_cancelled'] ) ? 'yes' : 'no' );

		}
	}
}

/**
 * Unique access to instance of YITH_WCMBS_Mailchimp_Compatibility class
 *
 * @return YITH_WCMBS_Mailchimp_Compatibility
 */
function YITH_WCMBS_Mailchimp_Compatibility() {
	return YITH_WCMBS_Mailchimp_Compatibility::get_instance();
}