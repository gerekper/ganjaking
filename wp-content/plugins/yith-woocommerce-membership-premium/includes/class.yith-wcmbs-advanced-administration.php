<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Members Class
 *
 * @class   YITH_WCMBS_Advanced_Administration
 * @author  Yithemes
 * @since   1.0.0
 * @package Yithemes
 */
class YITH_WCMBS_Advanced_Administration {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Advanced_Administration
	 * @since 1.0.0
	 */
	private static $_instance;

	private $_enabled;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Advanced_Administration
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	private function __construct() {
		/* Add metabox to edit membership filds */
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_membership' ) );

		/* enable trash for memberships */
		add_filter( 'yith_wcmbs_enable_membership_trash', '__return_true' );

		// Enqueue Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * check if is enabled Advanced Administration
	 *
	 * @return bool
	 * @deprecated 1.4.0 | it's always enabled!
	 */
	public function is_enabled() {
		return true;
	}

	/**
	 * Add Metaboxes
	 *
	 * @param string $post_type
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0
	 */
	public function register_metaboxes( $post_type ) {
		add_meta_box(
			'yith-wcmbs-advanced-membership-administration',
			__( 'Advanced Administration', 'yith-woocommerce-membership' ),
			array( $this, 'advanced_administration_metabox_render' ),
			YITH_WCMBS_Post_Types::$membership,
			'normal',
			'default' );
	}

	/**
	 * Save Membership Advanced Edit
	 *
	 * @param int $post_id the id of the membership
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com
	 * @since  1.0.0
	 */
	public function save_membership( $post_id ) {
		if ( get_post_type( $post_id ) === YITH_WCMBS_Post_Types::$membership && ! empty( $_POST['yith_wcmbs_advanced_admin_edit'] ) ) {
			$membership = yith_wcmbs_get_membership( $post_id );
			if ( $membership->is_valid() ) {
				$data   = $_POST['yith_wcmbs_advanced_admin_edit'];
				$fields = $this->get_fields();

				// On-off.
				if ( ! isset( $data['discount_enabled'] ) ) {
					$data['discount_enabled'] = 'no';
				}

				if ( is_array( $data ) ) {
					foreach ( $data as $meta => $value ) {

						if ( isset( $fields[ $meta ] ) && empty( $fields[ $meta ]['yith-wcmbs-fake'] ) ) {
							$is_date_field         = isset( $fields[ $meta ]['type'] ) && 'datepicker' === $fields[ $meta ]['type'];
							$is_end_date_unlimited = 'unlimited' === $value && 'end_date' === $meta;

							if ( $is_date_field && ! $is_end_date_unlimited ) {

								if ( 'credits_update' === $meta && ! $value && isset( $data['credits'] ) && intval( $data['credits'] ) > - 1 ) {
									$value = yith_wcmbs_local_strtotime_midnight_to_utc();
								} else {
									$value = ! ! $value ? yith_wcmbs_local_strtotime_midnight_to_utc( $value ) : $value;
								}
							}

							$membership->set( $meta, $value );
						}
					}
				}
			}
		}
	}


	/**
	 * Render Advanced Administration metabox for Memberships
	 *
	 * @param WP_Post $post
	 */
	public function advanced_administration_metabox_render( $post ) {
		$fields     = $this->get_fields();
		$membership = yith_wcmbs_get_membership( $post->ID );
		foreach ( $fields as $field_key => &$field ) {
			$field['id']                = $field_key;
			$field['name']              = 'yith_wcmbs_advanced_admin_edit[' . $field_key . ']';
			$field['custom_attributes'] = 'disabled="disabled"';

			switch ( $field_key ) {
				case 'has_end_date':
					$value = ! $membership->is_unlimited() ? 'yes' : 'no';
					break;
				case 'end_date':
					$value = ! $membership->is_unlimited() && $membership->end_date ? yith_wcmbs_date( absint( $membership->end_date ), 'Y-m-d' ) : $membership->end_date;
					$value = ! ! $value ? $value : '';
					break;
				case 'has_credits':
					$value = $membership->has_credit_management() ? 'yes' : 'no';
					break;

				case 'discount_enabled':
					$value = $membership->get_discount_enabled( 'edit' );
					break;

				case 'discount':
					$value = $membership->get_discount( 'edit' );
					break;
				default:
					$value = $membership->$field_key;
					if ( 'datepicker' === $field['type'] ) {
						$value = ! ! $value ? yith_wcmbs_date( absint( $value ), 'Y-m-d' ) : '';
					}
					break;
			}

			$field['value'] = $value;

			$data                   = isset( $field['data'] ) ? $field['data'] : array();
			$data['original-value'] = $value;
			$field['data']          = $data;
		}

		yith_wcmbs_get_view( '/metaboxes/membership-advanced-administration.php', compact( 'fields' ) );
	}


	/**
	 * @return array
	 */
	public function get_fields() {
		$editable_post_meta = array(
			'plan_id'             => array(
				'type'  => 'number',
				'label' => __( 'Plan ID', 'yith-woocommerce-membership' ),
			),
			'start_date'          => array(
				'type'  => 'datepicker',
				'label' => __( 'Starting Date', 'yith-woocommerce-membership' ),
				'data'  => array( 'date-format' => 'yy-mm-dd' ),
			),
			'has_end_date'        => array(
				'type'            => 'onoff',
				'label'           => __( 'Set an expiration date', 'yith-woocommerce-membership' ),
				'yith-wcmbs-fake' => true,
			),
			'end_date'            => array(
				'type'  => 'datepicker',
				'label' => __( 'Expiration Date', 'yith-woocommerce-membership' ),
				'data'  => array( 'date-format' => 'yy-mm-dd' ),
			),
			'order_id'            => array(
				'type'  => 'number',
				'label' => __( 'Order ID', 'yith-woocommerce-membership' ),
			),
			'order_item_id'       => array(
				'type'  => 'number',
				'label' => __( 'Order Item ID', 'yith-woocommerce-membership' ),
			),
			'user_id'             => array(
				'type'  => 'number',
				'label' => __( 'User ID', 'yith-woocommerce-membership' ),
			),
			'status'              => array(
				'type'    => 'select',
				'label'   => __( 'Status', 'yith-woocommerce-membership' ),
				'options' => yith_wcmbs_get_membership_statuses(),
			),
			'paused_days'         => array(
				'type'  => 'number',
				'label' => __( 'Paused days', 'yith-woocommerce-membership' ),
			),
			'has_credits'         => array(
				'type'            => 'onoff',
				'label'           => __( 'Enable credits', 'yith-woocommerce-membership' ),
				'yith-wcmbs-fake' => true,
			),
			'credits'             => array(
				'type'  => 'number',
				'label' => __( 'Remaining Credits', 'yith-woocommerce-membership' ),
			),
			'credits_update'      => array(
				'type'  => 'datepicker',
				'label' => __( 'Last Credit Update', 'yith-woocommerce-membership' ),
				'data'  => array( 'date-format' => 'yy-mm-dd' ),
			),
			'next_credits_update' => array(
				'type'  => 'datepicker',
				'label' => __( 'Next Credit Update', 'yith-woocommerce-membership' ),
				'data'  => array( 'date-format' => 'yy-mm-dd' ),
			),
		);

		if ( 'membership' === yith_wcmbs_settings()->get_option( 'yith-wcmbs-retrieve-membership-discount-settings' ) ) {
			$editable_post_meta['discount_enabled'] = array(
				'type'  => 'onoff',
				'label' => __( 'Give a discount', 'yith-woocommerce-membership' ),
			);

			$editable_post_meta['discount'] = array(
				'type'  => 'number',
				'label' => __( 'Discount on all products (%)', 'yith-woocommerce-membership' ),
			);
		}

		return apply_filters( 'yith_wcmbs_advanced_editable_membership_post_meta', $editable_post_meta );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( 'ywcmbs-membership' == $screen->id ) {
			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'yith-wcmbs-admin-advanced-styles', YITH_WCMBS_ASSETS_URL . '/css/advanced-admin.css', array(), YITH_WCMBS_VERSION );
			wp_enqueue_script( 'yith-wcmbs-admin-advanced-js', YITH_WCMBS_ASSETS_URL . '/js/advanced-admin' . $suffix . '.js', array( 'jquery' ), YITH_WCMBS_VERSION );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMBS_Advanced_Administration class
 *
 * @return YITH_WCMBS_Advanced_Administration
 * @since 1.0.0
 */
function YITH_WCMBS_Advanced_Administration() {
	return YITH_WCMBS_Advanced_Administration::get_instance();
}