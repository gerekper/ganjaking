<?php
$event_slug = ! empty( $event_slug ) ? $event_slug : 'PPRP';
if ( ( '' == $NomineeIdsinMyaccount ) && ( '' == $NomineeIdsinCheckout ) ) {
	if ( 'yes' == $enabledisablemaxpoints ) {
		$this->check_point_restriction( $productlevelrewardpointss, $pointsredeemed, $event_slug, $orderuserid, $nomineeid   = '', $referrer_id = '', $productid, $variationid, $reasonindetail );
	} else {
		$valuestoinsert = array(
			'pointstoinsert'    => $productlevelrewardpointss,
			'event_slug'        => $event_slug,
			'user_id'           => $orderuserid,
			'product_id'        => $productid,
			'variation_id'      => $variationid,
			'reasonindetail'    => $reasonindetail,
			'totalearnedpoints' => $productlevelrewardpointss,
		);
		$this->total_points_management( $valuestoinsert );
	}
} elseif ( ( '' != $NomineeIdsinMyaccount && 'yes' == $EnableNomineeinMyaccount ) && ( '' != $NomineeIdsinCheckout ) ) {
	$nomineeid   = $orderuserid;
	$orderuserid = $NomineeIdsinCheckout;
	$this->insert_points_for_product( $enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid, $reasonindetail );
} elseif ( ( '' != $NomineeIdsinMyaccount && 'yes' == $EnableNomineeinMyaccount ) && ( '' == $NomineeIdsinCheckout ) ) {
	$nomineeid   = $orderuserid;
	$orderuserid = $NomineeIdsinMyaccount;
	$this->insert_points_for_product( $enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid, $reasonindetail );
} elseif ( ( '' != $NomineeIdsinMyaccount && 'no' == $EnableNomineeinMyaccount ) && ( '' != $NomineeIdsinCheckout ) ) {
	$nomineeid   = $orderuserid;
	$orderuserid = $NomineeIdsinCheckout;
	$this->insert_points_for_product( $enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid, $reasonindetail );
} elseif ( ( '' != $NomineeIdsinMyaccount && 'no' == $EnableNomineeinMyaccount ) && ( '' == $NomineeIdsinCheckout ) ) {
	if ( 'yes' == $enabledisablemaxpoints ) {
		$this->check_point_restriction( $productlevelrewardpointss, $pointsredeemed, $event_slug, $orderuserid, $nomineeid   = '', $referrer_id = '', $productid, $variationid, $reasonindetail );
	} else {
		$valuestoinsert = array(
			'pointstoinsert'    => $productlevelrewardpointss,
			'event_slug'        => $event_slug,
			'user_id'           => $orderuserid,
			'product_id'        => $productid,
			'variation_id'      => $variationid,
			'reasonindetail'    => $reasonindetail,
			'totalearnedpoints' => $productlevelrewardpointss,
		);
		$this->total_points_management( $valuestoinsert );
	}
} elseif ( ( '' == $NomineeIdsinMyaccount ) && ( '' != $NomineeIdsinCheckout ) ) {
	$nomineeid   = $orderuserid;
	$orderuserid = $NomineeIdsinCheckout;
	$this->insert_points_for_product( $enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid, $reasonindetail );
}
if ( 0 !== $orderuserid ) {
	update_order_meta_if_points_awarded( $order_id, $orderuserid );
	$order_obj = wc_get_order( $order_id );
	$order_obj->update_meta_data( 'srp_pp_reward_points_awarded', 'yes' );
	$order_obj->save();
}
