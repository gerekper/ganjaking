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

if ( ! class_exists( 'YITH_FAQ_Elementor' ) ) {

	/**
	 * Implements compatibility with Elementor
	 *
	 * @class   YITH_FAQ_Elementor
	 * @since   1.1.5
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YITH_FAQ_Elementor {

		/**
		 * Single instance of the class
		 *
		 * @since 1.1.5
		 * @var YITH_FAQ_Elementor
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_FAQ_Elementor
		 * @since 1.1.5
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
		 * @since   1.1.5
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
		 * @since   1.1.5
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
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function elementor_init_widgets() {
			// Include Widget files
			require_once( YITH_FWP_DIR . 'includes/integrations/elementor/class-yith-faq-elementor-widget.php' );
			// Register widget
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YITH_FAQ_Elementor_Widget() );
		}
	}

}

/**
 * Unique access to instance of YITH_FAQ_Elementor class
 *
 * @return YITH_FAQ_Elementor
 */
function YITH_FAQ_Elementor() { //phpcs:ignore
	return YITH_FAQ_Elementor::get_instance();
}

YITH_FAQ_Elementor();
