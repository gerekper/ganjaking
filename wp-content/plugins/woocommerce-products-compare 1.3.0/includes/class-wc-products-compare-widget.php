<?php
/**
 * The widget class.
 *
 * @package WC_Products_Compare
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Products_Compare_Widget class.
 *
 * @since 1.0.0
 * @deprecated 1.3.0
 */
class WC_Products_Compare_Widget extends \KoiLab\WC_Products_Compare\Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @deprecated 1.3.0
	 */
	public function __construct() {
		wc_deprecated_function( __FUNCTION__, '1.3.0', '\KoiLab\WC_Products_Compare\Widget' );

		parent::__construct();
	}
}
