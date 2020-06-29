<?php
/**
 * WooCommerce Instagram Debug Tools
 *
 * @package WC_Instagram/Admin
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Debug_Tools.
 */
class WC_Instagram_Debug_Tools {

	/**
	 * Constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_debug_tools', array( $this, 'add_debug_tools' ) );
	}

	/**
	 * Adds custom tools to display on the WooCommerce > System Status > Tools administration screen.
	 *
	 * @since 2.2.0
	 *
	 * @param array $tools An array with the system tools.
	 * @return array
	 */
	public function add_debug_tools( $tools ) {
		$tools['wc_instagram_clear_image_transients'] = array(
			'name'     => _x( 'Instagram image transients', 'debug tool title', 'woocommerce-instagram' ),
			'button'   => _x( 'Clear transients', 'debug tool button', 'woocommerce-instagram' ),
			'desc'     => _x( 'This tool will clear the Instagram image transients cache. Images displayed on products pages will be refreshed immediately.', 'debug tool desc', 'woocommerce-instagram' ),
			'callback' => array( $this, 'clear_image_transients' ),
		);

		return $tools;
	}

	/**
	 * Clear Instagram image transients.
	 *
	 * @since 2.2.0
	 */
	public function clear_image_transients() {
		wc_instagram_clear_product_hashtag_images_transients();
		wc_instagram_clear_hashtag_media_transients();

		return _x( 'Instagram image transients cleared successfully.', 'debug tool notice', 'woocommerce-instagram' );
	}
}

return new WC_Instagram_Debug_Tools();
