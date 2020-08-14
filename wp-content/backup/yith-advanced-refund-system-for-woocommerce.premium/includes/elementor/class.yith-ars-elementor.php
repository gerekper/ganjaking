<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_ARS_Elementor class.
 *
 * @class   YITH_ARS_Elementor
 * @package YITH
 * @since   1.3.6
 * @author  YITH
 */
if ( ! class_exists( 'YITH_ARS_Elementor' ) ) {

	/**
	 * Class YITH_ARS_Elementor
	 */
	class YITH_ARS_Elementor {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_ARS_Elementor
		 */

		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_ARS_Elementor
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * YITH_ARS_Elementor constructor.
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_yith_widget_category' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' )   );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

			require_once( YITH_WCARS_PATH . '/includes/elementor/class.yith-ars-refund-requests-elementor-widget.php');
			require_once( YITH_WCARS_PATH . '/includes/elementor/class.yith-ars-view-request-elementor-widget.php');

			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YITH_ARS_Refund_Requests_Elementor_Widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YITH_ARS_View_Request_Elementor_Widget() );
		}

		public function enqueue_scripts() {
			wp_enqueue_style( 'ywcars-frontend',
				YITH_WCARS_ASSETS_URL . 'css/ywcars-frontend.css',
				array(),
				YITH_WCARS_VERSION
			);
			wp_enqueue_style( 'ywcars-common',
				YITH_WCARS_ASSETS_URL . 'css/ywcars-common.css',
				array(),
				YITH_WCARS_VERSION
			);
		}
	}

}

/**
 * Unique access to instance of YITH_ARS_Elementor class
 *
 * @return YITH_ARS_Elementor
 */
function YITH_ARS_Elementor() {
	return YITH_ARS_Elementor::get_instance();
}

YITH_ARS_Elementor();
