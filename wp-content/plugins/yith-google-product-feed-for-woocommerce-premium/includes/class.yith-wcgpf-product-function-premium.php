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
 * @class      YITH_WCGPF_Product_Functions_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Product_Functions_Premium' ) ) {

    class YITH_WCGPF_Product_Functions_Premium extends YITH_WCGPF_Product_Functions
    {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Product_Functions_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        public $create_feed = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Product_Functions_Premium instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . (class_exists(__CLASS__ . '_Premium') ? '_Premium' : '');

            if (is_null($self::$_instance)) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function __construct()
        {
            add_filter('yith_wcgpf_get_product_condition',array($this,'add_default_option'));
            add_filter('yith_wcgpf_adult_option',array($this,'add_default_option'));
            parent::__construct();
        }

        /**
         * Get energy efficiency
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function energy_efficiency() {

            $energy_efficiency = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
                'A+++'         => esc_html__( 'A+++', 'yith-google-product-feed-for-woocommerce' ),
                'A++'         => esc_html__( 'A++', 'yith-google-product-feed-for-woocommerce' ),
                'A+'         => esc_html__( 'A+', 'yith-google-product-feed-for-woocommerce' ),
                'A'         => esc_html__( 'A', 'yith-google-product-feed-for-woocommerce' ),
                'B'         => esc_html__( 'B', 'yith-google-product-feed-for-woocommerce' ),
                'C'         => esc_html__( 'C', 'yith-google-product-feed-for-woocommerce' ),
                'D'         => esc_html__( 'D', 'yith-google-product-feed-for-woocommerce' ),
                'E'         => esc_html__( 'E', 'yith-google-product-feed-for-woocommerce' ),
                'F'         => esc_html__( 'F', 'yith-google-product-feed-for-woocommerce' ),
                'G'         => esc_html__( 'G', 'yith-google-product-feed-for-woocommerce' ),
            );

            return apply_filters('yith_wcgpf_energy_efficiency_option',$energy_efficiency);
        }
        /**
         * Get gender
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function gender() {

            $energy_efficiency = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
                'male'         => esc_html__( 'Male', 'yith-google-product-feed-for-woocommerce' ),
                'female'         => esc_html__( 'Female', 'yith-google-product-feed-for-woocommerce' ),
                'unisex'         => esc_html__( 'Unisex', 'yith-google-product-feed-for-woocommerce' ),
            );
            return apply_filters('yith_wcgpf_gender_option',$energy_efficiency);
        }
        /**
         * Get age group
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function age_group() {

            $energy_efficiency = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
                'newborn'         => esc_html__( 'Newborn', 'yith-google-product-feed-for-woocommerce' ),
                'infant'         => esc_html__( 'Infant', 'yith-google-product-feed-for-woocommerce' ),
                'toddler'         => esc_html__( 'Toddler', 'yith-google-product-feed-for-woocommerce' ),
                'kids'            => esc_html__( 'Kid', 'yith-google-product-feed-for-woocommerce' ),
                'adult'           => esc_html__( 'Adult', 'yith-google-product-feed-for-woocommerce' ),
            );
            return apply_filters('yith_wcgpf_age_group_option',$energy_efficiency);
        }

        /**
         * Get size type
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function size_type() {

            $size_type = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
                'regular'           => esc_html__( 'Regular', 'yith-google-product-feed-for-woocommerce' ),
                'petite'            => esc_html__( 'Petite', 'yith-google-product-feed-for-woocommerce' ),
                'plus'              => esc_html__( 'Plus', 'yith-google-product-feed-for-woocommerce' ),
                'big-and-tall'      => esc_html__( 'Big and tall', 'yith-google-product-feed-for-woocommerce' ),
                'maternity'         => esc_html__( 'Maternity', 'yith-google-product-feed-for-woocommerce' ),
            );
            return apply_filters('yith_wcgpf_size_type_option',$size_type);
        }
        /**
         * Get size system
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function size_system() {

            $energy_efficiency = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
                'US'         => esc_html__( 'United States sizing', 'yith-google-product-feed-for-woocommerce' ),
                'UK'         => esc_html__( 'United Kingdom sizing ', 'yith-google-product-feed-for-woocommerce' ),
                'EU'         => esc_html__( 'European sizing', 'yith-google-product-feed-for-woocommerce' ),
                'DE'            => esc_html__( 'German sizing', 'yith-google-product-feed-for-woocommerce' ),
                'FR'           => esc_html__( 'French sizing', 'yith-google-product-feed-for-woocommerce' ),
                'JP'           => esc_html__( 'Japanese sizing', 'yith-google-product-feed-for-woocommerce' ),
                'CN'           => esc_html__( 'Chinese sizing', 'yith-google-product-feed-for-woocommerce' ),
                'IT'           => esc_html__( 'Italian sizing', 'yith-google-product-feed-for-woocommerce' ),
                'BR'           => esc_html__( 'Brazilian sizing', 'yith-google-product-feed-for-woocommerce' ),
                'MEX'           => esc_html__( 'Mexican sizing', 'yith-google-product-feed-for-woocommerce' ),
                'AU'           => esc_html__( 'Australian sizing', 'yith-google-product-feed-for-woocommerce' ),

            );
            return apply_filters('yith_wcgpf_size_system_option',$energy_efficiency);
        }

        public function add_default_option($options) {
            $default_option = array(
                'default' => esc_html__('General field','yith-google-product-feed-for-woocommerce'),
            );

            $options = array_merge($default_option,$options);

            return $options;
        }
    }
}

