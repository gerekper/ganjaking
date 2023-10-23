<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_WC_Category_Accordion_Elementor class.
 *
 * @class   YITH_WC_Category_Accordion_Elementor
 * @package YITH
 * @since   1.0.46
 * @author  YITH <plugins@yithemes.com>
 */
if ( ! class_exists( 'YITH_WC_Category_Accordion_Elementor' ) ) {

	/**
	 * Class YITH_WC_Category_Accordion_Elementor
	 */
	class YITH_WC_Category_Accordion_Elementor {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Category_Accordion_Elementor
		 */

		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Category_Accordion_Elementor
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * YITH_WC_Dynamic_Elementor constructor.
		 */
		public function __construct() {
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) {
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' ) );
				add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
			}
		}

		/**
		 * Include the style and script
		 *
		 */
		public function enqueue_styles() {
			if ( \Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode() ) {

				wp_enqueue_style( 'ywcca_accordion_style' );
				wp_enqueue_script( 'ywcca_accordion' );
				wp_enqueue_script( 'hover_intent' );
			}
		}

		/**
		 * Init widget
		 *
		 * @throws Exception To return Error.
		 */
		public function elementor_init_widgets() {
			// Include Widget files.
			require_once YWCCA_INC . '/elementor/class-yith-wc-category-accordion-widget.php';

			// Register widget.
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YITH_WC_Category_Accordion_Widget() );


			// Remove the default widget.
			\Elementor\Plugin::instance()->widgets_manager->unregister_widget_type( 'wp-widget-yith_wc_category_accordion' );
		}
	}

}

/**
 * Unique access to instance of YITH_WC_Category_Accordion_Elementor class
 *
 * @return YITH_WC_Category_Accordion_Elementor
 */
function YITH_WC_Category_Accordion_Elementor() {
	return YITH_WC_Category_Accordion_Elementor::get_instance();
}

YITH_WC_Category_Accordion_Elementor();
