<?php

if ( ($NomineeIdsinMyaccount == '') && ($NomineeIdsinCheckout == '') ) {
    if ( $enabledisablemaxpoints == 'yes' ) {
        $this->check_point_restriction( $productlevelrewardpointss , $pointsredeemed , 'PPRP' , $orderuserid , $nomineeid   = '' , $referrer_id = '' , $productid , $variationid , $reasonindetail ) ;
    } else {
        $valuestoinsert = array( 'pointstoinsert'    => $productlevelrewardpointss ,
            'event_slug'        => 'PPRP' ,
            'user_id'           => $orderuserid ,
            'product_id'        => $productid ,
            'variation_id'      => $variationid ,
            'reasonindetail'    => $reasonindetail ,
            'totalearnedpoints' => $productlevelrewardpointss ) ;
        $this->total_points_management( $valuestoinsert ) ;
    }
} elseif ( ($NomineeIdsinMyaccount != '' && $EnableNomineeinMyaccount == 'yes') && ($NomineeIdsinCheckout != '') ) {
    $nomineeid   = $orderuserid ;
    $orderuserid = $NomineeIdsinCheckout ;
    $this->insert_points_for_product( $enabledisablemaxpoints , $order_id , $orderuserid , $nomineeid , $productlevelrewardpointss , $productid , $variationid , $reasonindetail ) ;
} elseif ( ($NomineeIdsinMyaccount != '' && $EnableNomineeinMyaccount == 'yes') && ($NomineeIdsinCheckout == '') ) {
    $nomineeid   = $orderuserid ;
    $orderuserid = $NomineeIdsinMyaccount ;
    $this->insert_points_for_product( $enabledisablemaxpoints , $order_id , $orderuserid , $nomineeid , $productlevelrewardpointss , $productid , $variationid , $reasonindetail ) ;
} elseif ( ($NomineeIdsinMyaccount != '' && $EnableNomineeinMyaccount == 'no') && ($NomineeIdsinCheckout != '') ) {
    $nomineeid   = $orderuserid ;
    $orderuserid = $NomineeIdsinCheckout ;
    $this->insert_points_for_product( $enabledisablemaxpoints , $order_id , $orderuserid , $nomineeid , $productlevelrewardpointss , $productid , $variationid , $reasonindetail ) ;
} elseif ( ($NomineeIdsinMyaccount != '' && $EnableNomineeinMyaccount == 'no') && ($NomineeIdsinCheckout == '') ) {
    if ( $enabledisablemaxpoints == 'yes' ) {
        $this->check_point_restriction( $productlevelrewardpointss , $pointsredeemed , 'PPRP' , $orderuserid , $nomineeid   = '' , $referrer_id = '' , $productid , $variationid , $reasonindetail ) ;
    } else {
        $valuestoinsert = array( 'pointstoinsert'    => $productlevelrewardpointss ,
            'event_slug'        => 'PPRP' ,
            'user_id'           => $orderuserid ,
            'product_id'        => $productid ,
            'variation_id'      => $variationid ,
            'reasonindetail'    => $reasonindetail ,
            'totalearnedpoints' => $productlevelrewardpointss ) ;
        $this->total_points_management( $valuestoinsert ) ;
    }
} elseif ( ($NomineeIdsinMyaccount == '') && ($NomineeIdsinCheckout != '') ) {
    $nomineeid   = $orderuserid ;
    $orderuserid = $NomineeIdsinCheckout ;
    $this->insert_points_for_product( $enabledisablemaxpoints , $order_id , $orderuserid , $nomineeid , $productlevelrewardpointss , $productid , $variationid , $reasonindetail ) ;
}
if ( $orderuserid != 0 )
    update_order_meta_if_points_awarded( $order_id , $orderuserid ) ;
