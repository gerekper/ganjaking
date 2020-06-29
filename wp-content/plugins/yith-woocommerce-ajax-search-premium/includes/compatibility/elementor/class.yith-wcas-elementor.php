<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Ajax Search Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCAS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_WCAS_Elementor class.
 *
 * @class   YITH_WCAS_Elementor
 * @package YITH
 * @since   1.3.6
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WCAS_Elementor' ) ) {

	/**
	 * Class YITH_WCAS_Elementor
	 */
	class YITH_WCAS_Elementor {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCAS_Elementor
		 */

		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCAS_Elementor
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * YITH_WCAS_Elementor constructor.
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' ) );
			}
		}

		/**
		 * Init widget
		 *
		 * @throws Exception To return Error.
		 */
		public function elementor_init_widgets() {
			// Include Widget files.
			require_once YITH_WCAS_DIR . 'includes/compatibility/elementor/class.yith-wcas-search-form-elementor.php';

			// Register widget.
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YITH_WCAS_Search_Form_Elementor_Widget() );
		}
	}

}

/**
 * Unique access to instance of YITH_WCAS_Elementor class
 *
 * @return YITH_WCAS_Elementor
 */
function YITH_WCAS_Elementor() {
	return YITH_WCAS_Elementor::get_instance();
}

YITH_WCAS_Elementor();
