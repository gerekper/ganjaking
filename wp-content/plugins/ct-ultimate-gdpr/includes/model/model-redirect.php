<?php

/**
 * Class CT_Ultimate_GDPR_Model_Redirect
 */
class CT_Ultimate_GDPR_Model_Redirect {

	/**
	 *
	 */
	const PRIORITY_STANDARD = 100;

	/**
	 * @var array
	 */
	private static $stack = array();

	/**
	 * Perform redirect
	 */
	public static function redirect() {
		$index = min( array_keys( self::$stack ) );
		$url =  self::$stack[ $index ];
		$url && apply_filters( 'ct_ultimate_gdpr_redirect', true, $url, self::$stack ) && wp_redirect( $url ) && exit;
	}

	/**
	 * CT_Ultimate_GDPR_Model_Redirect constructor.
	 * Register urls and redirect action for later
	 *
	 * @param $url
	 * @param $priority
	 */
	public function __construct( $url, $priority ) {
		self::$stack[ $priority ] = $url;
		$url && add_action( current_action(), array( 'CT_Ultimate_GDPR_Model_Redirect', 'redirect' ), 100 );
	}

}