<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWCTM_Elementor' ) ) {

	/**
	 * Implements compatibility with Elementor
	 *
	 * @class   YWCTM_Elementor
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Elementor {

		/**
		 * Single instance of the class
		 *
		 * @since 2.0.0
		 * @var YWCTM_Elementor
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWCTM_Elementor
		 * @since 2.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_yith_widget_category' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' ) );
			}
		}

		/**
		 * Add elementor widget group
		 *
		 * @param $elements_manager \Elementor\Elements_Manager
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_elementor_yith_widget_category( $elements_manager ) {
			$elements_manager->add_category(
				'yith',
				array(
					'title' => 'YITH',
					'icon'  => 'fa fa-plug',
				)
			);
		}

		/**
		 * Add elementor widget group
		 *
		 * @return  void
		 * @throws  Exception
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function elementor_init_widgets() {
			// Include Widget files

			require_once( YWCTM_DIR . 'includes/integrations/elementor/class-ywctm-button-elementor-widget.php' );
			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YWCTM_Button_Elementor_Widget() );

			require_once( YWCTM_DIR . 'includes/integrations/elementor/class-ywctm-inquiry-form-elementor-widget.php' );
			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YWCTM_Inquiry_Form_Elementor_Widget() );
		}
	}

}

/**
 * Unique access to instance of YWCTM_Elementor class
 *
 * @return YWCTM_Elementor
 */
function YWCTM_Elementor() { //phpcs:ignore
	return YWCTM_Elementor::get_instance();
}

YWCTM_Elementor();
