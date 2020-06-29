<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * Implements the YITH_YCTPW_Elementor class.
 *
 * @class   YITH_YCTPW_Elementor
 * @package YITH
 * @since   1.2.2
 * @author  YITH
 */
if ( ! class_exists( 'YITH_YCTPW_Elementor' ) ) {

	/**
	 * Class YITH_YCTPW_Elementor
	 */
	class YITH_YCTPW_Elementor {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_YCTPW_Elementor
		 */

		protected static $instance;

		/**
		 * Store the order to use in widget previews
		 *
		 * @var int
		 */
		public $order_to_test = 0;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_YCTPW_Elementor
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * YITH_YCTPW_Elementor constructor.
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_yith_widget_category' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' ) );

				// load common functions to use on rendering widgets.
				require_once YITH_CTPW_PATH . 'includes/functions.yith-ctpw-common.php';

			}

			add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_styles' ) );

		}

		/**
		 * Load frontend styles
		 */
		public function load_frontend_styles() {
			wp_register_style( 'yith-ctpw-style', YITH_CTPW_ASSETS_URL . 'css/style.css', null, true, 'all' );
			wp_enqueue_style( 'yith-ctpw-style' );
			wp_register_script( 'yith-ctpw-tabs', YITH_CTPW_ASSETS_URL . 'js/yith_ctpw_tabs.js', array( 'jquery' ), true, true );
			wp_enqueue_script( 'yith-ctpw-tabs' );
		}

		/**
		 * Add YITH category
		 *
		 * @param object $elements_manager .
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
		 * Init Widget
		 *
		 * @throws Exception .
		 */
		public function elementor_init_widgets() {

			$this->order_to_test = apply_filters( 'yctpw_elementor_test_order', yith_ctpw_get_available_order_to_preview() );

			// Include Widget files.
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-upsells-widget.php';
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-order-header-section-widget.php';
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-order-table-section-widget.php';
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-customer-details-section-widget.php';
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-pdf-button-widget.php';
			require_once YITH_CTPW_PATH . 'integrations/elementor/class.yctpw-social-box-widget.php';

			// Register widget.
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_UpSells_Elementor_widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_Order_Header_Section_Elementor_widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_Order_Table_Section_Elementor_widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_Order_Customer_Details_Elementor_widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_Pdf_Button_Elementor_widget() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \YCTPW_SocialBox_Elementor_widget() );

		}
	}

}

/**
 * Unique access to instance of YITH_YCTPW_Elementor class
 *
 * @return YITH_YCTPW_Elementor
 */
function YITH_YCTPW_Elementor() {
	return YITH_YCTPW_Elementor::get_instance();
}

YITH_YCTPW_Elementor();
