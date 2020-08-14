<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Google_Product_Feed_Merchant
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Google_Product_Feed_Merchant' ) ) {

    /**
     * Class YITH_WCGPF_Google_Product_Feed_Merchant
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCGPF_Google_Product_Feed_Merchant
    {

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed_Merchant
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Construct
         *
         * @since 1.0
         */
        public function __construct()
        {

            /* === Premium Initializzation === */
            add_action('yith_wcgpf_get_body_template',array($this,'get_body_template_google_do_action'),10,2);
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_WCGPF_Google_Product_Feed_Merchant instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Get body template
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_body_template_google_do_action($merchant,$post) {
            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'merchant/'.$merchant.'.php' )){
                $template_id = $post->ID;
                require_once( YITH_WCGPF_TEMPLATE_PATH . 'merchant/'.$merchant.'.php' );
            }
        }

    }
}
