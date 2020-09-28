<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Members Class
 *
 * @class   YITH_WCMBS_Advanced_Administration
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
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
		if ( ! $this->is_enabled() ) {
			return;
		}

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
	 */
	public function is_enabled() {
		if ( ! isset( $this->_enabled ) ) {
			$this->_enabled = get_option( 'yith-wcmbs-advanced-membership-admin', 'no' ) === 'yes';
		}

		return $this->_enabled;
	}

	/**
	 * Add Metaboxes
	 *
	 * @param string $post_type
	 *
	 * @since    1.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	public function register_metaboxes( $post_type ) {
		add_meta_box( 'yith-wcmbs-advanced-membership-administration', __( 'Advanced Administration', 'yith-woocommerce-membership' ), array(
			$this,
			'advanced_administration_metabox_render',
		), YITH_WCMBS_Membership_Helper()->post_type_name, 'normal', 'default' );
	}

	/**
	 * Save Membership Advanced Edit
	 *
	 * @param int $post_id the id of the membership
	 *
	 * @since  1.0.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com
	 */
	public function save_membership( $post_id ) {
		if ( YITH_WCMBS_Membership_Helper()->post_type_name == get_post_type( $post_id ) && ! empty( $_POST['yith_wcmbs_advanced_admin_edit'] ) ) {
			$membership = new YITH_WCMBS_Membership( $post_id );
			if ( $membership->is_valid() ) {
				$advanced_edit      = $_POST['yith_wcmbs_advanced_admin_edit'];
				$editable_post_meta = $this->get_editable_membership_post_meta();
				$gmt_offset         = floatval( get_option( 'gmt_offset' ) );
				if ( is_array( $advanced_edit ) ) {
					foreach ( $advanced_edit as $meta => $value ) {

						if ( isset( $editable_post_meta[ $meta ] ) ) {
							$is_timestamp = isset( $editable_post_meta[ $meta ]['type'] ) && 'timestamp' == $editable_post_meta[ $meta ]['type'];

							if ( $is_timestamp ) {
								$is_end_date_unlimited = 'unlimited' == $value && $meta == 'end_date';

								if ( ! $is_end_date_unlimited ) {
									$value = strtotime( $value . 'midnight' );
									$value -= ( $gmt_offset * HOUR_IN_SECONDS );
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
		$fields  = array();
		$printer = YITH_WCMBS_Printer();

		foreach ( $this->get_editable_membership_post_meta() as $meta => $meta_data ) {
			$type     = $meta_data['type'];
			$statuses = array();

			$value       = get_post_meta( $post->ID, '_' . $meta, true );
			$extra_class = '';
			switch ( $type ) {
				case 'status':
					$type     = 'select';
					$statuses = yith_wcmbs_get_membership_statuses();
					break;
				case 'timestamp':
					$type        = 'text';
					$value       = $value != 'unlimited' && ! ! $value ? date( 'Y-m-d', absint( $value ) ) : $value;
					$value       = ! ! $value ? $value : '';
					$extra_class = 'yith-wcmbs-date';
					break;
			}

			$field = array(
				'type'   => 'section',
				'class'  => 'yith-wcmbs-advanced-admin-section',
				'fields' => array(
					array(
						'type'              => $type,
						'title'             => $meta_data['label'],
						'class'             => 'yith-wcmbs-advanced-admin-section-field ' . $extra_class,
						'value'             => $value,
						'name'              => 'yith_wcmbs_advanced_admin_edit[' . $meta . ']',
						'options'           => $statuses,
						'custom_attributes' => 'disabled="disabled"',
						'data'              => array(
							'value' => $value,
						),
					),
					array(
						'type'  => 'button',
						'value' => __( 'Enable', 'yith-woocommerce-membership' ),
						'class' => 'yith-wcmbs-advanced-edit button yith-wcmbs-advanced-hide-if-editable',
					),
					array(
						'type'  => 'button',
						'value' => __( 'Undo', 'yith-woocommerce-membership' ),
						'class' => 'yith-wcmbs-advanced-undo button yith-wcmbs-advanced-show-if-editable',
					),
				),
			);

			$set_unlimited_button = array(
				'type'  => 'button',
				'value' => __( 'Set unlimited', 'yith-woocommerce-membership' ),
				'class' => 'yith-wcmbs-advanced-set-unlimited button yith-wcmbs-advanced-show-if-editable',
			);

			if ( $meta == 'end_date' ) {
				$field['fields'][] = $set_unlimited_button;
			}

			$fields[] = $field;
		}

		$printer->print_fields( $fields );
	}


	/**
	 * @return array
	 */
	public function get_editable_membership_post_meta() {
		$editable_post_meta = array(
			'plan_id'             => array(
				'type'  => 'number',
				'label' => __( 'Plan ID', 'yith-woocommerce-membership' ),
			),
			'start_date'          => array(
				'type'  => 'timestamp',
				'label' => __( 'Starting Date', 'yith-woocommerce-membership' ),
			),
			'end_date'            => array(
				'type'  => 'timestamp',
				'label' => __( 'Expiration Date', 'yith-woocommerce-membership' ),
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
				'type'  => 'status',
				'label' => __( 'Status', 'yith-woocommerce-membership' ),
			),
			'paused_days'         => array(
				'type'  => 'number',
				'label' => __( 'Paused days', 'yith-woocommerce-membership' ),
			),
			'credits'             => array(
				'type'  => 'number',
				'label' => __( 'Remaining Credits', 'yith-woocommerce-membership' ),
			),
			'credits_update'      => array(
				'type'  => 'timestamp',
				'label' => __( 'Last Credit Update', 'yith-woocommerce-membership' ),
			),
			'next_credits_update' => array(
				'type'  => 'timestamp',
				'label' => __( 'Next Credit Update', 'yith-woocommerce-membership' ),
			),
		);

		return apply_filters( 'yith_wcmbs_advanced_editable_membership_post_meta', $editable_post_meta );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( 'ywcmbs-membership' == $screen->id ) {
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