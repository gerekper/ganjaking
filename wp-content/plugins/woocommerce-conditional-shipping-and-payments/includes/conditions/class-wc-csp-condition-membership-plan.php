<?php
/**
 * WC_CSP_Condition_Membership_Plan class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Membership Plan Condition.
 *
 * @class    WC_CSP_Condition_Membership_Plan
 * @version  1.15.0
 */
class WC_CSP_Condition_Membership_Plan extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'membership_plan';
		$this->title                          = __( 'Membership Plan', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                       = 100;
		$this->supported_global_restrictions  = array( 'payment_gateways', 'shipping_methods', 'shipping_countries' );
		$this->supported_product_restrictions = array( 'payment_gateways', 'shipping_methods', 'shipping_countries' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'all-in' ) ) ) {
			$message = __( 'make sure that your active membership plan(s) qualify', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in', 'not-all-in' ) ) ) {
			$message = __( 'make sure you have purchased a qualifying membership plan', 'woocommerce-conditional-shipping-and-payments' );
		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  string $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		$user_id = get_current_user_id();

		// User is not logged in, apply the condition.
		if ( ! $user_id ) {

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in', 'not-all-in' ) ) ) {
				return ( ! $this->allow_guest_checkout() );
			}

			return false;
		}

		// Get condition plans.
		$plans = is_array( $data[ 'value' ] ) ? $data[ 'value' ] : array();

		// Init search flag.
		$found_items = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {

			foreach ( $plans as $plan_id ) {
				if ( wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
					$found_items = true;
					break;
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {

			foreach ( $plans as $plan_id ) {
				if ( ! wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
					$found_items = true;
					break;
				}
			}
		}

		if ( $found_items ) {
			return $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-all-in' ) );
		} else {
			return $this->modifier_is( $data[ 'modifier' ], array( 'not-in', 'all-in' ) );
		}
	}

	/**
	 * Guest checkout is allowed when checking for not active plans.
	 *
	 * @return bool
	 */
	private function allow_guest_checkout() {
		return apply_filters( 'woocommerce_csp_memberships_allow_guest_checkout', false );
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {

			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'intval', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier      = '';
		$current_plans = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		} else {
			$modifier = 'in';
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$current_plans = $condition_data[ 'value' ];
		}

		$membership_plans = wc_memberships_get_membership_plans();

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'active exists', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'all inactive', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ); ?>><?php esc_html_e( 'inactive exists', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="all-in" <?php selected( $modifier, 'all-in', true ); ?>><?php esc_html_e( 'all active', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select plans&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $membership_plans as $plan ) {
							echo '<option value="' . esc_attr( $plan->get_id() ) . '" ' . selected( in_array( $plan->get_id(), $current_plans ), true, false ) . '>' . esc_html( $plan->get_name() ) . '</option>';
						}
					?>
				</select>
			</div>
		</div>
		<?php
	}
}
