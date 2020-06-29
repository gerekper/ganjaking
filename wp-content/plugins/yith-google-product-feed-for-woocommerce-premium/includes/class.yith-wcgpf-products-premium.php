<?php
/**
 * Product Premium class
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Products_Premium' ) ) {
    /**
     * YITH_WCGPF_Products_Premium
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Products_Premium extends YITH_WCGPF_Products
    {
        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Products_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Products_Premium instance
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
        protected function __construct()
        {
           parent::__construct();
        }

        /**
         * Get products
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function get_products( $filters = array(),$limit='',$offset='' ) {
            $default_filters = array(
                'include_products'    => array(),
                'exclude_products'    => array(),
                'categories_selected' => array(),
                'tags_selected'       => array(),
            );

            $filters = wp_parse_args( $filters, $default_filters );

            if( is_array($filters['include_products']) && is_array( $filters['exclude_products'] ) ) {
                $product_params_post_in = array(
                    'post_type' => array('product'),
                    'posts_per_page' => !empty($limit) ? $limit : '-1', //-1 is all products
                    'post__in'       => $filters[ 'include_products' ],
                    'fields'         => 'ids',
                    'offset'        => $offset
                );

                $product_ids_post_in = get_posts( $product_params_post_in );


                $product_params = array(
                    'post_type' => array('product'),
                    'posts_per_page' => !empty($limit) ? $limit : '-1', //-1 is all products
                    'post__not_in'   => $filters[ 'exclude_products' ],
                    'fields'         => 'ids',
                    'offset'        => $offset
                );

                if ( !!$filters[ 'categories_selected' ] && is_array( $filters[ 'categories_selected' ] ) ) {
                    $product_params[ 'tax_query' ][ 'relation' ] = 'OR';
                    $product_params[ 'tax_query' ][]             = array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $filters[ 'categories_selected' ],
                        'operator' => 'IN'
                    );
                }

                if ( !!$filters[ 'tags_selected' ] && is_array( $filters[ 'tags_selected' ] ) ) {
                    $product_params[ 'tax_query' ][ 'relation' ] = 'OR';
                    $product_params[ 'tax_query' ][]             = array(
                        'taxonomy' => 'product_tag',
                        'field'    => 'term_id',
                        'terms'    => $filters[ 'tags_selected' ],
                        'operator' => 'IN'
                    );
                }


                $product_ids = get_posts( $product_params );

                if ( is_array($product_ids_post_in) && !empty($product_ids_post_in)  ) {

                    $product_ids = array_merge($product_ids,$product_ids_post_in);
                }


            } else {

                $product_params = array(
                    'post_type' => array('product'),
                    'posts_per_page' => !empty($limit) ? $limit : '-1', //-1 is all products
                    'post__in'       => $filters[ 'include_products' ],
                    'post__not_in'   => $filters[ 'exclude_products' ],
                    'fields'         => 'ids',
                    'offset'        => $offset
                );

                if ( !!$filters[ 'categories_selected' ] && is_array( $filters[ 'categories_selected' ] ) ) {
                    $product_params[ 'tax_query' ][ 'relation' ] = 'OR';
                    $product_params[ 'tax_query' ][]             = array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $filters[ 'categories_selected' ],
                        'operator' => 'IN'
                    );
                }

                if ( !!$filters[ 'tags_selected' ] && is_array( $filters[ 'tags_selected' ] ) ) {
                    $product_params[ 'tax_query' ][ 'relation' ] = 'OR';
                    $product_params[ 'tax_query' ][]             = array(
                        'taxonomy' => 'product_tag',
                        'field'    => 'term_id',
                        'terms'    => $filters[ 'tags_selected' ],
                        'operator' => 'IN'
                    );
                }

                $product_ids = get_posts( $product_params );

            }


            if ( !!$product_ids && apply_filters( 'yith_wcgpf_enable_variation_in_feed', true ) ) {
                // VARIATIONS QUERY
                $product_params = array(
                    'post_type'       => 'product_variation',
                    'posts_per_page' => -1,
                    'post_parent__in' => $product_ids,
                    'fields'         => 'ids'
                );

                $variation_product_ids = get_posts( $product_params );

                if ( $variation_product_ids )
                    $product_ids = array_merge( $product_ids, $variation_product_ids );
            }
            return apply_filters('yith_wcgpf_get_product_ids',$product_ids,$filters);
        }
        /**
         * Return grouped options from a selector
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function get_attributes($selected = "") {
            $attributes = $this->get_product_properties_wc();
            $str = "<option></option>";
            foreach ($attributes as $attribute) {
                $str.= '<optgroup label="'.$attribute['id_group'].'">';
                foreach($attribute['content'] as $value=>$valu) {
                    $sltd = "";
                    if ($selected == $value)
                        $sltd = 'selected="selected"';
                    $str .= "<option $sltd value='$value'>" . $valu . "</option>";
                }
                $str.= '</optgroup>';
            }
            return $str;
        }


        /**
         * Get products properties WooCommerce by default
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function get_product_properties_wc()
        {
            $attributes = array(
                array(
                    'id_group' => esc_html__('Primary Attributes','yith-google-product-feed-for-woocommerce'),
                    'content' => array(
                        "id" => "Product Id",
                        "title" => "Product Title",
                        "description" => "Product Description",
                        "short_description" => "Product Short Description",
                        "product_type" => "Product Local Category",
                        "link" => "Product URL",
                        "item_group_id" => "Parent Id [Group Id]",
                        "sku" => "SKU",
                        "parent_sku" => "Parent SKU",
                        "availability" => "Stock",
                        "quantity" => "Quantity",
                        "price" => "Regular Price",
                        "sale_price" => "Sale Price",
                        "sale_price_sdate" => "Sale Start Date",
                        "sale_price_edate" => "Sale End Date",
                        "weight" => "Weight",
                        "width" => "Width",
                        "height" => "Height",
                        "length" => "Length",
                        "type" => "Product Type",
                        "variation_type" => "Variation Type",
                        "visibility" => "Visibility",
                        "rating_total" => "Total Rating",
                        "rating_average" => "Average Rating",
                        "tags" => "Tags",
                        "sale_price_effective_date" => "Sale Price Effective Date",
                    ),
                ),
                array(
                    'id_group'  => esc_html__('Image Attributes','yith-google-product-feed-for-woocommerce'),
                    'content'   => array(
                        "image_link" => "Main Image",
                        "images_1" => "Additional Image 1",
                        "images_2" => "Additional Image 2",
                        "images_3" => "Additional Image 3",
                        "images_4" => "Additional Image 4",
                        "images_5" => "Additional Image 5",
                        "images_6" => "Additional Image 6",
                        "images_7" => "Additional Image 7",
                        "images_8" => "Additional Image 8",
                        "images_9" => "Additional Image 9",
                        "images_10" => "Additional Image 10",
                    ),
                ),
            );

            return apply_filters('yith_wcgpf_product_properties_wc', $attributes);

        }

        /**
         * Add products attributes in product properties
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function add_wc_product_attributes($attributes) {
            $product_attributes = wc_get_attribute_taxonomies();
            if (count($product_attributes)) {
                $pattributes = array();
                foreach ($product_attributes as $key) {
                    $pattributes['pa_'.$key->attribute_name] = $key->attribute_name;
                }

                $product_attributes_wc = array(
                    'id_group'  => __('Product Attributes','yith-google-product-feed-for-woocommerce'),
                    'content'   => $pattributes,
                );

                $attributes[] = $product_attributes_wc;
            }
            return $attributes;
        }
    }
}