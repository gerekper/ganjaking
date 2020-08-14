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
 * @class      YITH_WCGPF_Merchant_Google
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Merchant_Google_Premium' ) ) {

    class YITH_WCGPF_Merchant_Google_Premium extends YITH_WCGPF_Merchant_Google{
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Merchant_Google_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Merchant_Google instance
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
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function __construct()
        {
            add_filter('yith_wcgpf_product_properties_wc',array($this,'add_google_custom_product_properties'),12);

        }

        /**
         * Get list of google attributes
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_attributes($selected = "") {
            $attributes = $this->google_attributes();
            $str = "<option></option>";
            foreach ($attributes as $attribute) {
                $str.= '<optgroup label="'.$attribute['id_group'].'">';
                foreach($attribute['content'] as $id=>$value) {
                    $sltd = "";
                    if ($selected == $id)
                        $sltd = 'selected="selected"';
                    $str .= "<option $sltd value='$id'>" . $value . "</option>";
                }
                $str.= '</optgroup>';
            }
            return $str;
        }

        function google_attributes() {

            $attributes =  array(
                array(
                    'id_group' => esc_html__('Basic product data','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "id"                             => esc_html__('Product Id [id]','yith-google-product-feed-for-woocommerce'),
                        "title"                          => esc_html__('Product Title [title]','yith-google-product-feed-for-woocommerce'),
                        "description"                    => esc_html__('Product Description [description]','yith-google-product-feed-for-woocommerce'),
                        "link"                           => esc_html__('Product URL [link]','yith-google-product-feed-for-woocommerce'),
                        "image_link"                     => esc_html__('Main Image [image_link]','yith-google-product-feed-for-woocommerce'),
                        "mobile_link"                    => esc_html__('Product URL [mobile_link]','yith-google-product-feed-for-woocommerce'),
                        "images_1"                       => esc_html__('Additional Image 1 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_2"                       => esc_html__('Additional Image 2 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_3"                       => esc_html__('Additional Image 3 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_4"                       => esc_html__('Additional Image 4 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_5"                       => esc_html__('Additional Image 5 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_6"                       => esc_html__('Additional Image 6 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_7"                       => esc_html__('Additional Image 7 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_8"                       => esc_html__('Additional Image 8 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_9"                       => esc_html__('Additional Image 9 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                        "images_10"                      => esc_html__('Additional Image 10 [additional_image_link]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Price & availability','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "availability"                   => esc_html__('Stock Status [availability]','yith-google-product-feed-for-woocommerce'),
                        "availability_date"              => esc_html__('Availability Date [availability_date]','yith-google-product-feed-for-woocommerce'),
                        "expiration_date"                => esc_html__('Expiration Date [expiration_date]','yith-google-product-feed-for-woocommerce'),
                        "price"                          => esc_html__('Regular Price [price]','yith-google-product-feed-for-woocommerce'),
                        "sale_price"                     => esc_html__('Sale Price [sale_price]','yith-google-product-feed-for-woocommerce'),
                        "sale_price_effective_date"      => esc_html__('Sale Price Effective Date [sale_price_effective_date]','yith-google-product-feed-for-woocommerce'),
                        "unit_pricing_measure"           => esc_html__('Unit Pricing Measure [unit_pricing_measure]','yith-google-product-feed-for-woocommerce'),
                        "unit_pricing_base_measure"      => esc_html__('Unit Pricing Base Measure [unit_pricing_base_measure]','yith-google-product-feed-for-woocommerce'),
                        "installment"                    => esc_html__('Installment [installment]','yith-google-product-feed-for-woocommerce'),
                        "loyalty_points"                 => esc_html__('loyalty_points [loyalty_points]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Product category','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "google_product_category"        => esc_html__('Google Product Category [google_product_category]','yith-google-product-feed-for-woocommerce'),
                        "product_type"                   => esc_html__('Product Categories [product_type]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Product identifiers','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "brand"                          => esc_html__('Manufacturer [brand]','yith-google-product-feed-for-woocommerce'),
                        "gtin"                           => esc_html__('GTIN [gtin]','yith-google-product-feed-for-woocommerce'),
                        "mpn"                            => esc_html__('MPN [mpn]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Detailed product description','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "condition"                      => esc_html__('Condition [condition]','yith-google-product-feed-for-woocommerce'),
                        "adult"                          => esc_html__('Adult [adult]','yith-google-product-feed-for-woocommerce'),
                        "multipack"                      => esc_html__('Multipack [multipack]','yith-google-product-feed-for-woocommerce'),
                        "is_bundle"                      => esc_html__('Is Bundle [is_bundle]','yith-google-product-feed-for-woocommerce'),
                        "energy_efficiency_class"        => esc_html__('Energy Efficiency Class [energy_efficiency_class]','yith-google-product-feed-for-woocommerce'),
                        "age_group"                      => esc_html__('Age Group [age_group]','yith-google-product-feed-for-woocommerce'),
                        "color"                          => esc_html__('Color [color]','yith-google-product-feed-for-woocommerce'),
                        "gender"                         => esc_html__('Gender [gender]','yith-google-product-feed-for-woocommerce'),
                        "material"                       => esc_html__('Material [material]','yith-google-product-feed-for-woocommerce'),
                        "pattern"                        => esc_html__('Pattern [pattern]','yith-google-product-feed-for-woocommerce'),
                        "size"                           => esc_html__('Size of the item [size]','yith-google-product-feed-for-woocommerce'),
                        "size_type"                      => esc_html__('Size Type [size_type]','yith-google-product-feed-for-woocommerce'),
                        "size_system"                    => esc_html__('Size System [size_system]','yith-google-product-feed-for-woocommerce'),
                        "item_group_id"                  => esc_html__('Item Group Id [item_group_id]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Shopping campaigns and other configurations','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "adwords_redirect"                => esc_html__('Adwords Redirect [adwords_redirect]','yith-google-product-feed-for-woocommerce'),
                        "excluded_destination"            => esc_html__('Excluded Destination [excluded_destination]','yith-google-product-feed-for-woocommerce'),
                        "custom_label_0"                  => esc_html__('Custom label 0 [custom_label_0]','yith-google-product-feed-for-woocommerce'),
                        "custom_label_1"                  => esc_html__('Custom label 1 [custom_label_1]','yith-google-product-feed-for-woocommerce'),
                        "custom_label_2"                  => esc_html__('Custom label 2 [custom_label_2]','yith-google-product-feed-for-woocommerce'),
                        "custom_label_3"                  => esc_html__('Custom label 3 [custom_label_3]','yith-google-product-feed-for-woocommerce'),
                        "custom_label_4"                  => esc_html__('Custom label 4 [custom_label_4]','yith-google-product-feed-for-woocommerce'),
                        "promotion_id"                    => esc_html__('Promotion Id [promotion_id]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Shipping','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "shipping"                      => esc_html__('Shipping [shipping]','yith-google-product-feed-for-woocommerce'),
                        "shipping_label"                => esc_html__('Shipping Label [shipping_label]','yith-google-product-feed-for-woocommerce'),
                        "shipping_weight"               => esc_html__('Shipping Weight [shipping_weight]','yith-google-product-feed-for-woocommerce'),
                        "shipping_length"               => esc_html__('Shipping Length [shipping_length]','yith-google-product-feed-for-woocommerce'),
                        "shipping_width"                => esc_html__('Shipping Width [shipping_width]','yith-google-product-feed-for-woocommerce'),
                        "shipping_height"               => esc_html__('Shipping Height [shipping_height]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
                array(
                    'id_group' => esc_html__('Tax','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "tax"                            => esc_html__('Tax[tax]','yith-google-product-feed-for-woocommerce'),
                    ),
                ),
            );

            return apply_filters('yith_wcgpf_get_google_merchant_attributes',$attributes);
        }



        /**
         * Get list of WooCommerce product attributes
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_values($value = ''){
            $product = YITH_Google_Product_Feed()->products;
            $attributes = $product->get_attributes($value);
            return $attributes;
        }


        /**
         * Get default_rows
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function default_rows() {
            $default_rows = array(
                array(
                    'attributes' => 'id',
                    'type' => 'attributes',
                    'value' => 'id',
                ),
            );
            return apply_filters('yith_wcgpf_default_rows',$default_rows);
        }

        /**
         * Get google_rows();
         *
         * @return array
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function google_rows(){
            $default_rows = array(
                array(
                    'attributes' => 'id',
                    'type' => 'attributes',
                    'value' => 'id',
                ),
                array(
                    'attributes' => 'title',
                    'type' => 'attributes',
                    'value' => 'title',
                ),
                array(
                    'attributes' => 'description',
                    'type' => 'attributes',
                    'value' => 'description'
                ),
                array(
                    'attributes' => 'link',
                    'type' => 'attributes',
                    'value' => 'link',
                ),
                array(
                    'attributes' => 'image_link',
                    'type' => 'attributes',
                    'value' => 'image_link',
                ),
                array(
                    'attributes' => 'availability',
                    'type' => 'attributes',
                    'value' => 'availability',
                ),
                array(
                    'attributes' => 'price',
                    'type' => 'attributes',
                    'value' => 'price',
                ),
                array(
                    'attributes' => 'google_product_category',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_google_product_category',
                ),
                array(
                    'attributes' => 'brand',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_brand',
                ),
                array(
                    'attributes' => 'gtin',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_gtin',
                ),
                array(
                    'attributes' => 'mpn',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_mpn',
                ),
                array(
                    'attributes' => 'condition',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_condition',
                ),
                array(
                    'attributes' => 'item_group_id',
                    'type' => 'attributes',
                    'value' => 'item_group_id'
                ),
                array(
                    'attributes' => 'adult',
                    'type' => 'attributes',
                    'value' => 'yith_wcgpf_pfd_adult',
                ),
            );
            return apply_filters('yith_wcgpf_google_default_rows',$default_rows);
        }

        /**
         * Add customizable products properties in google product properties
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function add_google_custom_product_properties($attributes) {
            $product_feed_information = array(
                'id_group'  => esc_html__('Product Feed Information','yith-google-product-feed-for-woocommerce'),
                'content'   => array(
                    "yith_wcgpf_pfd_brand"                      => __('Brand','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_gtin"                       => __('GTIN','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_mpn"                        => __('MPN','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_condition"                  => __('Condition','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_google_product_category"    => __('Google product category','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_adult"                      => __('Adult','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_energy_efficiency_class"    => __('Energy efficiency class','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_gender"                     => __('Gender','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_age_group"                  => __('Age group','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_material"                   => __('Material','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_pattern"                    => __('Pattern','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_size"                       => __('Size','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_size_type"                  => __('Size type','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_size_system"                => __('Size system','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_custom_label_0"             => __('Custom label 0','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_custom_label_1"             => __('Custom label 1','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_custom_label_2"             => __('Custom label 2','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_custom_label_3"             => __('Custom label 3','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_custom_label_4"             => __('Custom label 4','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping"                   => __('Shipping','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping_label"             => __('Shipping label','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping_weight"            => __('Shipping weight','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping_length"            => __('Shipping length','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping_width"             => __('Shipping width','yith-google-product-feed-for-woocommerce'),
                    "yith_wcgpf_pfd_shipping_height"            => __('Shipping height','yith-google-product-feed-for-woocommerce'),

                ),
            );
            $attributes[] = apply_filters('yith_wcgpf_add_google_product_feed_information',$product_feed_information);

            $google_custom_fields = get_option('yith_wcgpf_custom_fields',array());
            if ( !empty( $google_custom_fields ) && is_array( $google_custom_fields ) ) {
                $cgattributes = array();
                foreach ( $google_custom_fields as $google_custom_field ) {
                    if ( !empty( $google_custom_field ) ) {
                        $cgattributes[$google_custom_field] = $google_custom_field;
                    }
                }

                if ( !empty( $cgattributes ) ) {
                    $google_custom_attributes = array(
                        'id_group'  => __('Google custom fields','yith-google-product-feed-for-woocommerce'),
                        'content'   => $cgattributes,
                    );

                    $attributes[] = $google_custom_attributes;
                }

            }
            return $attributes;
        }
    }
}