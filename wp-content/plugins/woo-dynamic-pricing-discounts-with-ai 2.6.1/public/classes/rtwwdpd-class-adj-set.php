<?php
/**
 * Class RTWWDPD_Adjustment_Set to perform discount query.
 *
 * @since    1.0.0
 */
class RTWWDPD_Adjustment_Set {
	/**
	 * variable to set rule data.
	 *
	 * @since    1.0.0
	 */
	protected $rtwwdpd_set_data;
	/**
	 * variable to set rule id.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_set_id;
	/**
	 * variable to set rule name.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_rule_name;
	/**
	 * variable to set rule mode.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_mode;
	/**
	 * variable to set pricing rule.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_pricing_rules;
	/**
	 * variable to set target product.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_targets;
	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $rtwwdpd_set_id, $rtwwdpd_set_data, $rtwwdpd_rule_name ) {
		$this->rtwwdpd_set_id   = $rtwwdpd_set_id;
		$this->rtwwdpd_set_data = $rtwwdpd_set_data;
		
		if ( isset( $rtwwdpd_set_data['mode'] ) && $rtwwdpd_set_data['mode'] == 'block' ) {
			$this->mode = 'block';

			if ( !empty( $rtwwdpd_set_data['blockrules'] ) ) {
				$this->rtwwdpd_pricing_rules = $rtwwdpd_set_data['blockrules'];
			}
		} else {
			$this->rtwwdpd_mode = 'bulk';

			$this->rtwwdpd_pricing_rules = $rtwwdpd_set_id['rules'];
			$this->rtwwdpd_rule_name = $rtwwdpd_rule_name;

		}
	}

	/**
	 * Function to confirm the product.
	 *
	 * @since    1.0.0
	 */
	public function is_targeted_product( $product_id, $variation_id = false ) {
		return false;
	}

	/**
	 * Function to confirm the user.
	 *
	 * @since    1.0.0
	 */
	public function is_valid_for_user() {
		$rtwwdpd_result             = 0;
		$rtwwdpd_pricing_conditions = $this->rtwwdpd_set_data['conditions'];
		
		if ( is_array( $rtwwdpd_pricing_conditions ) && sizeof( $rtwwdpd_pricing_conditions ) > 0 ) {
			$rtwwdpd_conditions_met = 0;

			foreach ( $rtwwdpd_pricing_conditions as $condition ) {
				switch ( $condition['type'] ) {
					case 'apply_to':
						if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {
							if ( $condition['args']['applies_to'] == 'everyone' ) {
								$rtwwdpd_result = 1;
							} elseif ( $condition['args']['applies_to'] == 'unauthenticated' ) {
								if ( !is_user_logged_in() ) {
									$rtwwdpd_result = 1;
								}
							} elseif ( $condition['args']['applies_to'] == 'authenticated' ) {
								if ( is_user_logged_in() ) {
									$rtwwdpd_result = 1;
								}
							} elseif ( $condition['args']['applies_to'] == 'roles' && isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) ) {
								if ( is_user_logged_in() ) {
									foreach ( $condition['args']['roles'] as $role ) {
										if ( current_user_can( $role ) ) {
											$rtwwdpd_result = 1;
											break;
										}
									}
								}
							}
						}
						break;
					default:
						$rtwwdpd_result = 0;
						break;
				}

				$rtwwdpd_result = apply_filters( 'rtwwdpd_woocommerce_dynamic_pricing_is_rule_set_valid_for_user', $rtwwdpd_result, $condition, $this );

				$rtwwdpd_conditions_met += $rtwwdpd_result;
			}


			if ( $this->rtwwdpd_set_data['conditions_type'] == 'all' ) {
				$rtwwdpd_execute_rules = $rtwwdpd_conditions_met == count( $rtwwdpd_pricing_conditions );
			} elseif ( $this->rtwwdpd_set_data['conditions_type'] == 'any' ) {
				$rtwwdpd_execute_rules = $rtwwdpd_conditions_met > 0;
			}
		} else {
			//empty conditions - default match, process price adjustment rules
			$rtwwdpd_execute_rules = true;
		}

		if ( isset( $this->rtwwdpd_set_data['date_from'] ) && isset( $this->rtwwdpd_set_data['date_to'] ) ) {
			// Check date range

			$rtwwdpd_from_date = empty( $this->rtwwdpd_set_data['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $this->rtwwdpd_set_data['date_from'] ), false ) );
			$rtwwdpd_to_date   = empty( $this->rtwwdpd_set_data['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $this->rtwwdpd_set_data['date_to'] ), false ) );
			$rtwwdpd_now       = current_time( 'timestamp' );

			if ( $rtwwdpd_from_date && $rtwwdpd_to_date && !( $rtwwdpd_now >= $rtwwdpd_from_date && $rtwwdpd_now <= $rtwwdpd_to_date ) ) {
				$rtwwdpd_execute_rules = false;
			} elseif ( $rtwwdpd_from_date && !$rtwwdpd_to_date && !( $rtwwdpd_now >= $rtwwdpd_from_date ) ) {
				$rtwwdpd_execute_rules = false;
			} elseif ( $rtwwdpd_to_date && !$rtwwdpd_from_date && !( $rtwwdpd_now <= $rtwwdpd_to_date ) ) {
				$rtwwdpd_execute_rules = false;
			}
		}

		return $rtwwdpd_execute_rules;
	}

	/**
	 * Function to confirm the discounting rule.
	 *
	 * @since    1.0.0
	 */
	public function get_collector() {
		return $this->rtwwdpd_set_data['collector'];
	}

	/**
	 * Function to get the discounting rule object.
	 *
	 * @since    1.0.0
	 */
	public function get_collector_object() {
		return new RTWWDPD_Dynamic_Pricing_Collector( $this->rtwwdpd_set_data['collector'] );
	}

}
