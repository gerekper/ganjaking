<?php
/**
 * Class Helper generate file
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Generate_File' ) ) {
    /**
     * YITH_WCGPF_Generate_File
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Generate_File
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCGPF_Generate_File
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCGPF_Generate_File
         * @since 1.0.0
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
         * @type array
         */
        public $allowed_merchant = array();

        public function __construct( )
        {

        }

        /**
         * Select if the feed is created by xml or txt
         */
        public function create_feed($feed_id, $feed_type,$product_ids)
        {
            $values = get_post_meta($feed_id, 'yith_wcgpf_save_feed', true);

            switch ($feed_type) {
                case 'xml':
                    return $this->create_feed_xml($values, $product_ids);
                    break;
                case 'txt':
                    return $this->create_feed_txt($values, $product_ids);
                    break;
            }
        }


        /**
         * Create feed xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */

        function create_feed_xml( $values, $product_ids )
        {

            $content = '';

            $product_ids = apply_filters('yith_wcgpf_product_ids',$product_ids,$values);

            foreach ($product_ids as $product_id) {
                $product = $this->get_products_mapping($product_id);

                if ($product && isset( $values['feed_template'] ) ) {

                    $content .= $this->get_content_xml($product, $values['feed_template']);
                }
            }
            return $content;
        }

        /**
         * Get content xml for each product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $content
         */
        function get_content_xml($product, $values)
        {

            $content = '';
            $product_field_array = array();
            foreach ($values as $fields) {
                $value = false;
                if (isset($fields['value'])) {
                    $current_product = wc_get_product($product['id']);
                    $current_product_type = $product['type'];

                    if (  isset($product[$fields['value']]) && 0 != strlen( ( (string)$product[$fields['value']] ) ) ) {

                        if ('default' == $product[$fields['value']]) {
                            $variable = 'yith_wcgpf_tab_google_' . $fields['value'];
                            $value = get_option($variable, false);

                        } else {
                            $value = $product[$fields['value']];
                        }

                    } elseif (substr($fields['value'], 0, strlen('yith_wcgpf_pfd_')) == 'yith_wcgpf_pfd_') {
                        $product_field = substr($fields['value'], strlen('yith_wcgpf_pfd_'));

                        if (isset($product[$product_field]) && 'default' != $product[$product_field] && '99999' != $product[$product_field] && !empty($product[$product_field])) {

                            $value = apply_filters('yith_wcgpf_values_in_feed', $product[$product_field], $product_field, $product, $current_product);

                        } elseif ('shipping' == $product_field) {

                            $value = $this->get_shipping_information($current_product, 'xml');

                        } else {
                            $variable = 'yith_wcgpf_tab_google_' . $product_field;
                            $value = apply_filters('yith_wcgpf_values_in_feed', get_option($variable, false), $product_field, $product, $current_product);
                        }

                    } elseif ('variation' != $current_product_type && $custom_field_variation = apply_filters('yith_wcgpf_custom_fields_for_variations', yit_get_prop($current_product, $fields['value'], true), $current_product, $fields['value'])) {

                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed', $custom_field_variation, $fields['value'], $product, $current_product));

                    } elseif ( 'variation' == $current_product_type ) {
                        $post_parent_id = wp_get_post_parent_id($product['id']);
                        $parent = wc_get_product($post_parent_id);
                        $parent_value = yit_get_prop($parent,$fields['value'],true);
                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed',$parent_value, $fields['value'], $product, $current_product));
                    } else {

                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed', yit_get_prop($current_product, $fields['attributes'], true), $fields['attributes'], $product, $current_product));
                    }
                }
                if (0 != strlen(($value)) || !empty($value)) {

                    $product_field_array[] = $fields['attributes'];


                    if (substr($fields['value'], 0, strlen('images_')) == 'images_' && !empty($value)) {
                        $fields['attributes'] = 'additional_image_link';
                    }

                    $content .= $this->print_content($fields['attributes'], $value, $fields['prefix'], $fields['suffix']);
                }
            }
            if ( apply_filters('yith_wcgpf_indentifier_exists',$content) ) {
                $content = $this->add_identifier_exists($content, $product_field_array, 1);
            }
            return '<item>' . apply_filters('yith_wcgpf_get_content_xml', $content) . '</item>';
        }


        /**
         * Create feed txt
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */
        function create_feed_txt($values, $posts)
        {
            $content = array();
            foreach ($posts as $post) {
                $product = $this->get_products_mapping($post);
                if ($product) {
                    $content[] = $this->get_content_txt($product, $values['feed_template']);
                }
            }
            return $content;
        }


        /**
         * Get header txt
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */
        function get_header_txt($values)
        {

            $filename = 'product-feed-' . date('Ym-d_His', time()) . '.txt';
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=\"" . $filename . "\";");

            $head = array();
            foreach ($values['feed_template'] as $template_row) {
                $head[] = $template_row['attributes'];
            }
            $head[] = 'identifier_exists';
            $head = implode("\t", $head);
            $header[] = $head;
            return $header;
        }

        /**
         * Get content txt
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */
        function get_content_txt($product, $values)
        {
            $content = array();
            $product_field_array = array();
            foreach ($values as $fields) {
                $value = false;
                if (isset($fields['value'])) {
                    $current_product = wc_get_product($product['id']);
                    $current_product_type = $product['type'];
                    if (!empty($product[$fields['value']]) && isset($product[$fields['value']])) {

                        if ('default' == $product[$fields['value']]) {
                            $variable = 'yith_wcgpf_tab_google_' . $fields['value'];
                            $value = get_option($variable, false);

                        } else {
                            $value = $product[$fields['value']];

                        }

                    } elseif (substr($fields['value'], 0, strlen('yith_wcgpf_pfd_')) == 'yith_wcgpf_pfd_') {
                        $product_field = substr($fields['value'], strlen('yith_wcgpf_pfd_'));
                        if (isset($product[$product_field]) && 'default' != $product[$product_field] && '99999' != $product[$product_field]) {

                            $value = apply_filters('yith_wcgpf_values_in_feed', $product[$product_field], $product_field, $product, $current_product);

                        } elseif ('shipping' == $product_field) {
                            $value = $this->get_shipping_information($current_product, 'txt');

                        } else {
                            $variable = 'yith_wcgpf_tab_google_' . $product_field;
                            $value = apply_filters('yith_wcgpf_values_in_feed', get_option($variable, false), $product_field, $product, $current_product);
                        }

                    } elseif ('variation' != $current_product_type && $custom_field_variation = apply_filters('yith_wcgpf_custom_fields_for_variations', yit_get_prop($current_product, $fields['value'], true), $current_product, $fields['value'])) {

                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed', $custom_field_variation, $fields['value'], $product, $current_product));

                    } elseif ( 'variation' == $current_product_type ) {
                        $post_parent_id = wp_get_post_parent_id($product['id']);
                        $parent = wc_get_product($post_parent_id);
                        $parent_value = yit_get_prop($parent,$fields['value'],true);
                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed',$parent_value, $fields['value'], $product, $current_product));
                    } else {

                        $value = strip_tags(apply_filters('yith_wcgpf_values_in_feed', yit_get_prop($current_product, $fields['attributes'], true), $fields['attributes'], $product, $current_product));
                    }
                }

                if ($value || !empty($value)) {

                    $product_field_array[] = $fields['attributes'];

                    if (substr($fields['value'], 0, strlen('images_')) == 'images_' && !empty($value)) {
                        $fields['attributes'] = 'additional_image_link';
                    }
                    $content[] = $this->print_content_txt($value, $fields['prefix'], $fields['suffix'] );
                } else {
                    $content[] = '';
                }
            }

            if ( apply_filters('yith_wcgpf_indentifier_exists',$content) ) {
                $content = $this->add_identifier_exists($content, $product_field_array);
            }


            $content = implode("\t", $content);

            return apply_filters('yith_wcgpf_get_content_txt', $content);
        }


        /**
         * Mapping Product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */

        function get_products_mapping($post)
        {

            $products = array();

            $product = wc_get_product($post);


            if (!$product || apply_filters('yith_wcgpf_product_condition','variable' == $product->get_type(),$product )) {

                return false;
            }

            $product_id = $product->get_id();

            // Prevent get orphan product or not variation product
            $post_parent_id = wp_get_post_parent_id($product_id);
            if ($post_parent_id) {
                $parent_post = wc_get_product($post_parent_id);
                if (!$parent_post || 'variable' != $parent_post->get_type()) {
                    return false;
                }
            }

            $products['id'] = $product_id;
            $products['title'] = $this->get_product_title($product);
            $products['description'] = strip_tags ( ($post_parent = wp_get_post_parent_id($product_id))? $this->get_parent_description($post_parent,$product): apply_filters('yith_wcgpf_preg_replace_description', yit_get_prop($product, 'description', true),$product) );   //variation get parent description
            $products['short_description'] = $this->get_short_description($product, $post);
            $products['product_type'] = $this->get_the_term_list($product_id, 'product_cat', '', '>');
            $products['link'] = apply_filters('yith_wcgpf_get_product_permalink',get_permalink($product_id),$product_id);
            $products['item_group_id'] = ($post_parent = wp_get_post_parent_id($product_id)) ? $post_parent : '';
            $products['sku'] = $product->get_sku();
            $products['parent_sku'] = ($post_parent = wp_get_post_parent_id($product_id)) ? $this->get_parent_sku($post_parent) : '';
            $products['availability'] = ($availability = $product->is_in_stock()) ? 'in stock' : 'out of stock';
            $products['quantity'] = apply_filters('yith_wcgpf_get_products_mapping_quantity',(string)$product->get_stock_quantity(),$product);
            $products['price'] = ($product->get_regular_price()) ? $this->get_product_price($product, $product->get_regular_price()) : '';
            $sale_price = apply_filters('yith_wcgpf_get_sale_price',$product->get_price(),$product);
            $products['sale_price'] = ($product->get_regular_price() != $sale_price) ? $this->get_product_price($product, $sale_price ) : '';
            $products['sale_price_sdate'] = (yit_get_prop($product, 'sale_price_dates_from', true)) ? date('Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product, 'sale_price_dates_from', true))) : '';
            $products['sale_price_edate'] = (yit_get_prop($product, 'sale_price_dates_to', true)) ? date('Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product, 'sale_price_dates_to', true))) : '';
            $products['weight'] = $product->get_weight();
            $products['width'] = $product->get_width();
            $products['height'] = $product->get_height();
            $products['length'] = $product->get_length();
            $products['type'] = $product->get_type();
            $products['variation_type'] = $this->get_variation_type($product);
            $products['visibility'] = yit_get_prop($product, 'visibility', true);
            $products['rating_total'] = $product->get_rating_count();
            $products['rating_average'] = $product->get_average_rating();
            $products['tags'] = $this->get_the_term_list($product_id, 'product_tag');
            $products['sale_price_effective_date'] = $this->get_sale_price_effective_date($product);
            $products['image_link'] = $this->get_image_link($product->get_image_id());
            $product_images = $this->get_product_images($product, $post);
            for ($j = 1; $j < 10; $j++) {
                $products['image_' . $j] = (isset($product_images[$j-1])) ? $product_images[$j-1] : '';
            }

            $product_information = yit_get_prop($product, 'yith_wcgpf_product_feed_configuration', true);
            if ($product_information) {
                foreach ($product_information as $key => $value) {
                    $products[$key] = $value;
                }
            }

            $products = $this->get_attributes_wc($products, $product);

            return apply_filters('yith_wcgpf_get_products_mapping', $products, $post);
        }

        /**
         * Return product title
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_product_title($product)
        {
            $parent_title = $product->get_title();
            if ('variation' == $product->get_type()) {
                $attributes = implode(',', array_values($product->get_variation_attributes()));
                $title = apply_filters('yith_wcgpf_get_variation_title', $parent_title . ' - ' . $attributes, $product, $parent_title, $attributes);
            } else {
                $title = $parent_title;

            }
            return  apply_filters('yith_wcgpf_get_product_title',$title,$product);
        }

        /**
         * Return product price
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.3
         */
        function get_product_price($product, $price)
        {

            $product_price = 'yes' == get_option('yith_wcgpf_general_options_display_tax') ? yit_get_price_including_tax($product, 1, $price) : $price;

            $product_price = apply_filters('yith_wcgpf_get_sale_price_if_exist',$product_price,$product,get_option('yith_wcgpf_general_options_display_tax'));

            return $product_price . ' ' . get_woocommerce_currency();
        }

        /**
         * Return wc product attributes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        function get_attributes_wc($products, $product)
        {

            if ('variation' == $product->get_type()) {
                $product_attributes = $product->get_variation_attributes();
                foreach ($product_attributes as $product_attribute => $value) {
                    $product_attribute_name = substr($product_attribute, strlen('attribute_'));
                    $products[$product_attribute_name] = $value;


                }
            } else {
                $product_attributes = $product->get_attributes();
                foreach ($product_attributes as $product_attribute) {
                    if (is_object($product_attribute) && is_callable(array($product_attribute, 'get_name'))) {
                        $name = $product_attribute->get_name();
                        $products[$name] = $product->get_attribute($name);
                    }
                }
            }
            return apply_filters('yith_wcgpf_get_attributes_wc', $products, $product);
        }

        /**
         * Return parent description
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_parent_description($post_parent_id, $product)
        {
            $product_parent = wc_get_product($post_parent_id);
            $parent_description = yit_get_prop($product_parent,'description',true);
            return apply_filters('yith_wcgpf_preg_replace_description', $parent_description,$product);
        }

        /**
         * Return parent sku
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_parent_sku($post_parent_id)
        {
            $product_parent = wc_get_product($post_parent_id);
            $parent_sku = yit_get_prop($product_parent, 'sku', true);
            return $parent_sku;
        }

        /**
         * Return image link
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_image_link($product_image_id)
        {

            $image_link = wp_get_attachment_url($product_image_id);
            if (!$image_link) {
                $image_link = wc_placeholder_img_src();
            }
            return $image_link;
        }

        /**
         * Return variation type
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_variation_type($product)
        {

            switch ($product->get_type()) {
                case 'variation':
                    $type = 'child';
                    break;
                case 'variable':
                    $type = 'parent';
                    break;
                default:
                    $type = 'simple';
                    break;
            }

            return $type;
        }

        /**
         * Return product short description
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_short_description($product, $post)
        {
            if (version_compare(WC()->version, '3.0.0', '>=')) {
                return strip_tags($product->get_short_description());
            } else {
                if ('variation' == $product->get_type() && $product->get_variation_description()) {
                    return strip_tags($product->get_variation_description());
                }
                return yit_get_prop($product, 'short_description', true);
            }

        }

        /**
         * Return sale price effective date
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_sale_price_effective_date($product)
        {
            $sale_price_effective_date = '';
            $from = yit_get_prop($product, 'sale_price_dates_from', true) ? date('Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product, 'sale_price_dates_from', true))) : '';
            $to = yit_get_prop($product, 'sale_price_dates_to', true) ? date('Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product, 'sale_price_dates_to', true))) : '';
            if ($from && $to) {
                $sale_price_effective_date = "$from" . "/" . "$to";

            }
            return $sale_price_effective_date;
        }

        /**
         * Return attachment images
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_product_images($product, $post)
        {

            if (version_compare(WC()->version, '3.0.0', '>=')) {

                $attachment_ids = $product->get_gallery_image_ids();

            } else {
                $attachment_ids = $product->get_gallery_attachment_ids();

            }

            $gallery_urls = array();
            if ($attachment_ids && 'grouped' != $product->get_type()) {
                foreach ($attachment_ids as $attachment_id) {
                    $props = wc_get_product_attachment_props($attachment_id, $post);
                    if (!$props['url']) {
                        continue;
                    }
                    $image_url = $props['url'];
                    $gallery_urls[] = $image_url;
                }
            }
            return $gallery_urls;
        }

        /**
         * Retrieve a post's terms as a list with specified format.
         *
         * @since 1.0
         *
         * @param int $id Post ID.
         * @param string $taxonomy Taxonomy name.
         * @param string $before Optional. Before list.
         * @param string $sep Optional. Separate items using this.
         * @param string $after Optional. After list.
         *
         * @return list of term
         */
        function get_the_term_list($id, $taxonomy, $before = '', $sep = ',', $after = '')
        {
            $post_parent_id = wp_get_post_parent_id($id);
            if ($post_parent_id) {
                $id = $post_parent_id;
            }
            $terms = get_the_terms($id, $taxonomy);
            if (is_wp_error($terms)) {
                return $terms;
            }

            if (empty($terms)) {
                return '';
            }

            $links = array();
            $aux_array = array();

            foreach ($terms as $term) {
                if ($term->parent == 0 && empty($aux_array)) {
                    $aux_array[] = $term->term_id;
                    $links[] = $term->name;
                } else {
                    if (in_array($term->parent, $aux_array) && end($aux_array) == $term->parent) {
                        $aux_array[] = $term->term_id;
                        $links[] = $term->name;
                    } else {
                        $aux_array[] = $term->term_id;
                        $links[] = $term->name;
                    }
                }
            }

            ksort($links);
            return apply_filters('yith_wcgpf_get_the_term_list',$before . join($sep, $links) . $after , $links, $id, $taxonomy, $before, $sep, $after);
        }

        /**
         * Print the content for each field
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function print_content($attributes, $value, $prefix = '', $suffix = '')
        {

            return '<g:' . $attributes . '>' . $this->CDATA($prefix . $value . $suffix) . '</g:' . $attributes . '>';
        }

        /**
         * Print the content for each field in txt
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        function print_content_txt( $value,$prefix = '',$suffix = '' ) {

            return $prefix.$value.$suffix;
        }

        /**
         * Add identifier_exists_attribute
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function add_identifier_exists($content, $array, $flag = '')
        {

            $attribute = 'identifier_exists';
            if ((in_array('brand', $array)) && ((in_array('mpn', $array) || in_array('gtin', $array)))) {
                $value = 'yes';
            } else {
                $value = 'no';
            }

            if ($flag) {
                $content .= $this->print_content($attribute, $value);
            } else {
                $content[] = $this->print_content_txt($value);
            }

            return $content;
        }

        /**
         * get shipping information
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        function get_shipping_information($product, $type)
        {
            $shipping_information = '';
            $shipping = yit_get_prop($product, 'yith_wcgpf_shipping_feed_configuration', true);

            $shipping = apply_filters( 'yith_wcgpf_change_shipping_service',(isset($shipping)) ? $shipping : array(),$product);
            if ($shipping) {
                if (array_key_exists('price', $shipping) && isset($shipping['price'])) {
                    $shipping['price'] = $shipping['price'] . ' ' . get_woocommerce_currency();
                    if ('xml' == $type) {
                        foreach ($shipping as $key => $value) {
                            if (!empty($value)) {
                                $shipping_information .= '<g:' . $key . '>' . $value . '</g:' . $key . '>';
                            }
                        }
                    } else {
                        $shipping_information = implode(':', array_filter(array_values($shipping), 'strlen'));
                    }
                }
            }
            return $shipping_information;
        }


        /**
         * Get header xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $header
         */
        function get_header_xml(){
            $filename = 'product-feed-' . date( 'Ym-d_His', time() ) . '.xml';
            header ( "X-Robots-Tag: noindex, nofollow", true );
            header ( "Content-Type: application/xml" );
            header ( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
            $header = '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>
                        <rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
                        <channel>
                            <title><![CDATA['.htmlentities(get_option('blogname')).']]></title>
                            <link><![CDATA['.site_url().']]></link>
                            <description><![CDATA['.get_option('blogdescription','Feed').']]></description>';
            return $header;
        }

        /**
         * Get footer xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $footer
         */
        function get_footer_xml()
        {
            $footer = "</channel>
                       </rss>";
            return $footer;
        }

        /**
         * function CDATA
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         */
        function CDATA ( $value ) {
            $cdata = '<![CDATA['.$value.']]>';

            return $cdata;
        }

        /**
         * Save Feed
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         */
    }
}