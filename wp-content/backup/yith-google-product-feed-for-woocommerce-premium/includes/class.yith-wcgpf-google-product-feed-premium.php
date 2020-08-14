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
 * @class      YITH_Google_Product_Feed
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Google_Product_Feed_Premium' ) ) {

    /**
     * Class YITH_WCGPF_Google_Product_Feed_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCGPF_Google_Product_Feed_Premium extends YITH_WCGPF_Google_Product_Feed
    {


        public function __construct()
        {

            /* === Premium Initializzation === */
            add_filter('yith_wcgpf_require_class', array($this,'load_premium_classes'),11);
            parent::__construct();
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_WCGPF_Google_Product_Feed Main instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Add premium files to Require array
         *
         * @param $require The require files array
         *
         * @return Array
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         *
         */
        public function load_premium_classes( $require ){
            $admin = array(
                'includes/admin/class.yith-wcgpf-admin-premium.php',

            );
            $common = array(
                'includes/class.yith-wcgpf-google-product-feed-table.php',
                'includes/class.yith-wcgpf-google-product-feed-template-feed.php',
                'includes/class.yith-wcgpf-google-product-feed-template-feed-list-table.php',
                'includes/class.yith-wcgpf-google-product-feed-post-type.php',
                'includes/class.yith-wcgpf-google-product-feed-ajax.php',
                'includes/class.yith-wcgpf-google-product-feed-merchant.php',
                'includes/class.yith-wcgpf-google-feed-functions-premium.php',
                'includes/class.yith-wcgpf-products-premium.php',
                'includes/class.yith-wcgpf-google-product-feed-post-type-premium.php',
                'includes/merchant-feed/class.yith-wcgpf-google-product-feed-helper-premium.php',
                'includes/merchant-feed/class.yith-wcgpf-google-product-feed-generate-feed-google-premium.php',
                'includes/function-merchant/class.yith-wcgpf-merchant-google-premium.php',
                'includes/class.yith-wcgpf-product-function-premium.php',
                'includes/compatibility/class.yith-wcgpf-compatibility.php',
                'includes/feed-save-file/class.yith-wcgpf-google-product-feed-save-feed.php',
                'includes/feed-save-file/class.yith-wcgpf-feed-file.php',
                'includes/feed-save-file/class.yith-wcgpf-feed-file-helper.php',
                'includes/feed-save-file/class.yith-wcgpf-feed-generate-file.php'
            );
            $require['admin']   = array_merge($require['admin'],$admin);
            $require['common']  = array_merge($require['common'],$common);
            return $require;
        }

        /**
         * Init classes function
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        
        function init_classes(){
            $this->functions =  YITH_WCGPF_Feed_Functions::get_instance();
            $this->product_function = YITH_WCGPF_Product_Functions::get_instance();
            $this->merchant_google = YITH_WCGPF_Merchant_Google::get_instance();
            $this->merchant = YITH_WCGPF_Google_Product_Feed_Merchant::get_instance();
            $this->ajax = YITH_WCGPF_Google_Product_Feed_Ajax::get_instance();
            $this->products = YITH_WCGPF_Products::get_instance();
            YITH_WCGPF_Post_Types_Feed::get_instance();
            $this->helper = YITH_WCGPF_Helper::get_instance();
            $this->compatibility = YITH_WCGPF_Compatibility::get_instance();
            YITH_WCGPF_Feed_File::get_instance();
            YITH_WCGPF_File_Helper::get_instance();
            $this->generate_file = YITH_WCGPF_Generate_File::get_instance();
        }

        /**
         * Function init()
         *
         * Instance the admin or frontend classes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init() {
            if ( is_admin() ) {
                $this->admin =  YITH_WCGPF_Admin_Premium::get_instance();
            }
        }
    }
}
