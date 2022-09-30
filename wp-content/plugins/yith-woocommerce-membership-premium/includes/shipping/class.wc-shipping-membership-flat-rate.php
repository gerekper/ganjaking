<?php
/**
 * Membership Shipping Method Flat Rate.
 * A simple shipping method for membership flat rate.
 *
 * @class   WC_Shipping_Membership_Flat_Rate
 * @since   1.3.10
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 */
defined( 'ABSPATH' ) || exit;


class WC_Shipping_Membership_Flat_Rate extends WC_Shipping_Flat_Rate {

	/**
	 * Cost passed to [fee] shortcode.
	 *
	 * @var string Cost.
	 */
	protected $fee_cost = '';

	/**
	 * Requires option.
	 *
	 * @var string
	 */
	public $requires = '';

	/**
	 * WC_Shipping_Membership_Flat_Rate constructor.
	 *
	 * @param int $instance_id
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'membership_flat_rate';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Membership Flat Rate', 'yith-woocommerce-membership' );
		$this->method_description = __( 'Membership Flat Rate is a special method enabled only for members in the plan specified below.', 'yith-woocommerce-membership' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->init();
	}

	/**
	 * Set setting form fields for instances of this shipping method within zones.
	 */
	public function init() {
		parent::init();

		$this->init_form_fields();

		$this->requires = $this->get_option( 'requires' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$plan_ids      = yith_wcmbs_get_plans( array( 'fields' => 'ids' ) );
		$plans         = array_combine( $plan_ids, array_map( 'get_the_title', $plan_ids ) );
		$plans_options = array(
			'' => __( 'an active membership plan', 'yith-woocommerce-membership' ),
		);

		$plans_options = $plans_options + $plans;

		$this->instance_form_fields = array_merge( $this->instance_form_fields, array(
			'title'    => array(
				'title'       => __( 'Title', 'yith-woocommerce-membership' ),
				'type'        => 'text',
				'description' => __( 'This is the shipping method title shown to users in checkout page.', 'yith-woocommerce-membership' ),
				'default'     => $this->method_title,
				'desc_tip'    => true,
			),
			'requires' => array(
				'title'   => __( 'Membership Flat Rate requires...', 'yith-woocommerce-membership' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'default' => '',
				'options' => $plans_options,
			),
		) );
	}

	/**
	 * check if available
	 *
	 * @param array $package
	 *
	 * @return bool
	 */
	public function is_available( $package ) {
		$is_available = parent::is_available( $package );

		if ( $is_available ) {
			$member   = YITH_WCMBS_Members()->get_member( get_current_user_id() );
			$requires = $this->get_option( 'requires' );
			if ( $member->is_valid() ) {
				if ( ! $requires ) {
					$is_available = $member->is_member();
				} else {
					$is_available = $member->has_active_plan( $requires, false );
				}
			}else{
				$is_available = false;
			}
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}
}
