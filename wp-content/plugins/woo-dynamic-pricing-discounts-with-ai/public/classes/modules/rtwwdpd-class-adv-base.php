<?php
/**
 * Class RTWWDPD_Advance_Base to calculate discount according to Discount rules.
 *
 * @since    1.0.0
 */
abstract class RTWWDPD_Advance_Base extends RTWWDPD_Module_Base {

	/**
	 * Construct Function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $module_id ) {
		parent::__construct( $module_id, 'advanced' );
	}
}