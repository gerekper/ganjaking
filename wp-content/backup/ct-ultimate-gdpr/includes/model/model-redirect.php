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
     *
     */
    const PRIORITY_HIGH = 200;

	/**
	 * @var array
	 */
	private static $stack = array();

	/**
	 * Perform redirect
	 */
	public static function redirect() {

        $index = max(array_keys(self::$stack));
        $url   = self::$stack[$index];

        if( $url == get_permalink() ){
            return;
        }

        $url && apply_filters( 'ct_ultimate_gdpr_redirect', true, $url, self::$stack, $index ) && wp_redirect( $url ) && exit;
	}

    /**
     * @return string
     */
    public static function get_scheduled_redirection_url()
    {
        if (!self::$stack) {
            return '';
        }

        $index = max(array_keys(self::$stack));
        $url   = self::$stack[$index];
        return $url;
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