<?php
/**
 * UAEL Grid Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\TemplateBlocks;

use UltimateElementor\Modules\Woocommerce\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Classic
 */
class Skin_Grid_Franko extends Skin_Style {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Loop Template.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function render_woo_loop_template() {

		$settings = self::$settings;

		wp_enqueue_script( 'wc-cart-fragments' );

		include UAEL_MODULES_DIR . 'woocommerce/templates/content-product-franko.php';
	}
}

