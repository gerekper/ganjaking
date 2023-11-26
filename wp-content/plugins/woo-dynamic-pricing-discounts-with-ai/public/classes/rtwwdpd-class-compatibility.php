<?php

/**
 * Class RTWWDPD_Compatibility to check discount query.
 *
 * @since    1.0.0
 */
class RTWWDPD_Compatibility
{
	/**
	 * function to get price to targeted product.
	 *
	 * @since    1.0.0
	 */
	public static function rtw_wc_price( $price ) {
		return wc_price( $price );
	}
}