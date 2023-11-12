<?php
/**
 * Class RTWWDPD_Adjustment_Set_Product to perform product rule based query.
 *
 * @since    1.0.0
 */
class RTWWDPD_Adjustment_Set_Product extends RTWWDPD_Adjustment_Set {
	/**
	 * variable to get target variation.
	 *
	 * @since    1.0.0
	 */
	public $target_variations;
	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $rtwwdpd_set_id, $rtwwdpd_set_data ) {
		parent::__construct( $rtwwdpd_set_id, $rtwwdpd_set_data );
		//Helper code to normalize the possibile variation arguments. 
		$rtwwdpd_variations = false;
		if ( isset( $rtwwdpd_set_data['variation_rules'] ) ) {
			$variation_rules = isset( $rtwwdpd_set_data['variation_rules'] ) ? $rtwwdpd_set_data['variation_rules'] : array();
			if ( isset( $variation_rules['args']['type'] ) && $variation_rules['args']['type'] == 'variations' ) {
				$rtwwdpd_variations = isset( $variation_rules['args']['variations'] ) ? $variation_rules['args']['variations'] : array();
			}
		}

		$this->target_variations = apply_filters( 'rtwwdpd_dynamic_pricing_get_adjustment_set_variations', $rtwwdpd_variations, $this );
	}

}
