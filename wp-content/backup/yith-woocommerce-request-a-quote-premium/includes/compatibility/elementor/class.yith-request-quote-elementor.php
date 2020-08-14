<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_Request_Quote_Elementor class.
 *
 * @class   YITH_Request_Quote_Elementor
 * @package YITH
 * @since   1.3.6
 * @author  YITH
 */
if ( !class_exists( 'YITH_Request_Quote_Elementor' ) ) {

	/**
	 * Class YITH_Request_Quote_Elementor
	 */
	class YITH_Request_Quote_Elementor{
		/**
		 * Single instance of the class
		 *
		 * @var YITH_Request_Quote_Elementor
		 */

		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_Request_Quote_Elementor
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * YITH_Request_Quote_Elementor constructor.
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_yith_widget_category' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' )   );
			}
		}

		/**
		 * @param $elements_manager
		 */
		public function add_elementor_yith_widget_category( $elements_manager ) {
			$elements_manager->add_category(
				'yith',
				array(
					'title' => 'YITH',
					'icon' => 'fa fa-plug',
				)
			);
		}

		/**
		 * @throws Exception
		 */
		public function elementor_init_widgets(  ) {
			// Include Widget files

			require_once( YITH_YWRAQ_INC . '/compatibility/elementor/class.yith-request-quote-button-elementor-widget.php');

			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \YITH_Request_Quote_Button_Elementor_Widget());
		}
	}

}

/**
 * Unique access to instance of YITH_Request_Quote_Elementor class
 *
 * @return YITH_Request_Quote_Elementor
 */
function YITH_Request_Quote_Elementor() {
	return YITH_Request_Quote_Elementor::get_instance();
}

YITH_Request_Quote_Elementor();
