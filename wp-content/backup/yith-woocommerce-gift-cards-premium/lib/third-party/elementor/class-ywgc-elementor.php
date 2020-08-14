<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements the YWGC_Elementor class.
 *
 * @class   YWGC_Elementor
 * @package YITH
 * @since   1.2.2
 * @author  YITH
 */
if ( !class_exists( 'YWGC_Elementor' ) ) {

    /**
     * Class YWGC_Elementor
     */
    class YWGC_Elementor{
        /**
         * Single instance of the class
         *
         * @var YWGC_Elementor
         */

        protected static $instance;

        /**
         * store the order to use in widget previews
         *
         * @var int
         */
        public $order_to_test = 0;

        /**
         * Returns single instance of the class
         *
         * @return YWGC_Elementor
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * YWGC_Elementor constructor.
         */
        public function __construct() {
            if ( did_action( 'elementor/loaded' ) ) {
                add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_yith_widget_category' ) );
                add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_init_widgets' )   );
            }


        }

        /**
         * load frontend styles
         */
        public function load_frontend_styles(){

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
            require_once( YITH_YWGC_DIR . 'lib/third-party/elementor/class-ywgc-form-widget.php');
            require_once( YITH_YWGC_DIR . 'lib/third-party/elementor/class-ywgc-check-balance-widget.php');
            require_once( YITH_YWGC_DIR . 'lib/third-party/elementor/class-ywgc-redeem-widget.php');
            require_once( YITH_YWGC_DIR . 'lib/third-party/elementor/class-ywgc-user-table-widget.php');

            // Register widget
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \YWGC_Elementor_Form_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \YWGC_Elementor_Check_Balance_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \YWGC_Elementor_Redeem_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \YWGC_Elementor_User_Table_Widget());



        }
    }

}

/**
 * Unique access to instance of YWGC_Elementor class
 *
 * @return YWGC_Elementor
 */
function YWGC_Elementor() {
    return YWGC_Elementor::get_instance();
}

YWGC_Elementor();
