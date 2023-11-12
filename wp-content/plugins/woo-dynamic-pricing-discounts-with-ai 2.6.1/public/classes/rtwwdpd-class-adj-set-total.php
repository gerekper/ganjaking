<?php
/**
 * Class RTWWDPD_Adjustment_Set_Totals to perform discount rule based query.
 *
 * @since    1.0.0
 */
class RTWWDPD_Adjustment_Set_Totals extends RTWWDPD_Adjustment_Set {
	/**
	 * variable to get target product.
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
		parent::__construct( $rtwwdpd_set_id, $rtwwdpd_set_data, $rtwwdpd_rule_name );

		//Normalize the targeted items for version differences.
		$rtwwdpd_targets = false;
		if ( isset( $rtwwdpd_set_data['targets'] ) ) {
			$rtwwdpd_targets = $rtwwdpd_set_data['targets'];
		} else {
			$rtwwdpd_targets = array();
		}

		$this->rtwwdpd_targets = apply_filters( 'rtwwdpd_get_adjustment_set_targets', $rtwwdpd_targets, $this );
	}

}
