<?php
/**
 * Class RTWWDPD_Adjustment_Set_Category to perform product category rule based query.
 *
 * @since    1.0.0
 */
class RTWWDPD_Adjustment_Set_Category extends RTWWDPD_Adjustment_Set {
	/**
	 * variable to get target product.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_targets;
	/**
	 * variable check if rule is valid
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_is_valid_rule = false;
	/**
	 * variable check if user is valid
	 *
	 * @since    1.0.0
	 */
	public $is_valid_for_user = false;
	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $set_id, $set_data ) {
		parent::__construct( $set_id, $set_data );
		//Normalize the targeted items for version differences.
		$rtwwdpd_targets = false;
		if ( isset( $set_data['targets'] ) ) {
			$rtwwdpd_targets = $set_data['targets'];
		} else {
			//Backwards compatibility for v 1.x, target the collected quantities.
			$rtwwdpd_targets = isset( $set_data['collector']['args']['cats'] ) ? $set_data['collector']['args']['cats'] : false;
		}

		$this->targets = apply_filters( 'rtwwdpd_dynamic_pricing_get_adjustment_set_targets', $rtwwdpd_targets, $this );
		$this->rtwwdpd_is_valid_rule &= count( $this->targets ) > 0;

	}

	/**
	 * Function to check product has allready disocunted.
	 *
	 * @since    1.0.0
	 */
	public function get_collector_object() {
		return new rtwwdpd_Dynamic_Pricing_Collector_Category( $this->set_data['collector'] );
	}

}
