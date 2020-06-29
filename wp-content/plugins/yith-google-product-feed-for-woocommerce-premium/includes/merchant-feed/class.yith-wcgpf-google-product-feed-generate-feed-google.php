<?php
/**
 * Class Generate Google Product Feed
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Generate_Feed_google' ) ) {
    /**
     * YITH_WCGPF_Generate_Feed_google
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Generate_Feed_google extends YITH_WCGPF_Generate_Feed
    {
        public function __construct($feed_id = '' ,$feed_type = 'xml',$merchant='google')
        {
            parent::__construct($feed_id,$feed_type,$merchant );
        }

        /**
         * Create the feed
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */

        function create_feed( $feed_id,$feed_type,$merchant ) {

            if (!$feed_id) {
                $google_merchant =  YITH_Google_Product_Feed()->merchant_google;
                $values = $google_merchant->google_rows();
            } else {
                $values = get_post_meta($feed_id,'yith_wcgpf_save_feed',true);
            }
            $products = YITH_Google_Product_Feed()->products;
            $product_ids = $products->get_products();
            $this->create_feed_xml( $values,$product_ids );
        }

        /**
         * Create feed xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */

        function create_feed_xml( $values, $product_ids ){
            $head = $this->get_header_xml();
            $content ='';
            foreach ($product_ids as $product_id) {
                $product = $this->get_products_mapping($product_id);
                if($product) {
                    $content .= $this->get_content_xml( $product, $values );
                }
            }
            $footer = $this->get_footer_xml();

            $feed = $head.$content.$footer;

            echo $feed;

            die();
        }

        /**
         * Get content xml for each product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $content
         */
        function get_content_xml( $product, $values ) {

            $content = '';
            $product_field_array = array();
            foreach( $values as $fields ) {
                $value = false;
                if( isset($fields['value']) ) {
                    $current_product = wc_get_product($product['id']);

                    if  ( !empty($product[$fields['value']] )  && isset( $product[$fields['value']] ) ) {

                        if ( 'default' == $product[$fields['value']] ) {
                            $variable = 'yith_wcgpf_tab_google_' . $fields['value'] ;
                            $value = get_option($variable,false);

                        } else {
                            $value = $product[$fields['value']];

                        }

                    } elseif( substr($fields['value'],0,strlen('yith_wcgpf_pfd_')) == 'yith_wcgpf_pfd_' ){
                        $product_field = substr($fields['value'],strlen('yith_wcgpf_pfd_'));

                        if( isset( $product[$product_field] ) &&  'default' != $product[$product_field] ) {

                            $value = $product[$product_field];

                        } elseif('shipping' == $product_field) {

                            $value = $this->get_shipping_information($current_product,'xml');

                        } else {
                            $variable = 'yith_wcgpf_tab_google_' . $product_field;
                            $value = get_option($variable,false);
                        }

                        if( $value && !empty($value ) ) {

                            $product_field_array[] = $product_field;
                        }

                    } elseif ( yit_get_prop($current_product,$fields['value'],true)  ){

                        $value =  yit_get_prop($current_product,$fields['value'],true);
                    } else {

                        $value = yit_get_prop($current_product,$fields['attributes'],true);
                    }
                }
                if ($value || !empty($value)) {
                    if (substr($fields['value'], 0, strlen('images_')) == 'images_' && !empty($value)) {
                        $fields['attributes'] = 'additional_image_link';
                    }

                    $content.= $this->print_content( $fields['attributes'], $value);
                }
            }
            if ( $content ) {
                $content = $this->add_identifier_exists($content, $product_field_array,1);
            }
            return '<item>'.apply_filters('yith_wcgpf_get_content_xml',$content).'</item>';
        }

        /**
         * Mapping Product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */

        function get_products_mapping($post){

            $products = array();
            $i = 0;
                $product = wc_get_product($post);
                if(!$product || 'variable' == $product->get_type() ) {
                    return false;
                }

                $products['id'] =  $product->get_id();
                $products['title'] =  $product->get_title();
                $products['description'] = strip_tags ( ($post_parent = wp_get_post_parent_id($product->get_id()))? $this->get_parent_description($post_parent,$product): apply_filters('yith_wcgpf_preg_replace_description', yit_get_prop($product, 'description', true),$product) );   //variation get parent description
                $products['short_description'] = $this->get_short_description($product,$post);
                $products['product_type'] = $this->get_the_term_list($product->get_id(),'product_cat','','>');
                $products['link'] =  apply_filters('yith_wcgpf_get_product_permalink',get_permalink( $product->get_id() ),$product->get_id());
                $products['item_group_id'] = ($post_parent = wp_get_post_parent_id($product->get_id())) ?  $post_parent :'';
                $products['sku'] = $product->get_sku();
                $products['parent_sku'] = ($post_parent = wp_get_post_parent_id($product->get_id()))? $this->get_parent_sku($post_parent):'';
                $products['availability'] = ($availability = $product->is_in_stock()) ? 'in stock' : 'out of stock';
                $products['quantity'] = apply_filters('yith_wcgpf_get_products_mapping_quantity',(string)$product->get_stock_quantity(),$product);
                $products['price'] = ($product->get_regular_price()) ? $product->get_regular_price().' '.get_woocommerce_currency() : '';
                $products['sale_price'] = ($product->get_regular_price() != $product->get_price()) ? $product->get_price().' '.get_woocommerce_currency() : '';
                $products['sale_price_sdate'] = ( yit_get_prop($product,'sale_price_dates_from',true) ) ? date('Y-m-d\TH:iO',yit_datetime_to_timestamp(yit_get_prop($product,'sale_price_dates_from',true) )) : '';
                $products['sale_price_edate'] = ( yit_get_prop($product,'sale_price_dates_to',true) ) ? date('Y-m-d\TH:iO',yit_datetime_to_timestamp(yit_get_prop($product,'sale_price_dates_to',true) )) : '';
                $products['weight'] = $product->get_weight();
                $products['width'] =  $product->get_width();
                $products['height'] = $product->get_height();
                $products['length'] = $product->get_length();
                $products['type'] = $product->get_type();
                $products['variation_type'] = $this->get_variation_type($product) ;
                $products['visibility'] = yit_get_prop($product,'visibility',true) ;
                $products['rating_total'] = $product->get_rating_count();
                $products['rating_average'] = $product->get_average_rating();
                $products['tags'] = $this->get_the_term_list($product->get_id(),'product_tag');
                $products['sale_price_effective_date'] = $this->get_sale_price_effective_date($product);
                $products['image_link'] = $this->get_image_link($product->get_image_id());
                $product_images = $this->get_product_images($product,$post);
                for($j=1; $j<=10; $j++) {
                    $products['images_'.$j]= (isset($product_images[$j]))? $product_images[$j]:'';
                }

                $product_information = yit_get_prop($product,'yith_wcgpf_product_feed_configuration',true);
                if ( $product_information ) {
                    foreach ($product_information as $key => $value ) {
                        $products[$key] = $value;
                    }
                }

                $products = $this->get_attributes_wc($products,$product);

                $i++;

            return apply_filters('yith_wcgpf_get_products_mapping',$products,$post);
        }

        /**
         * Return wc product attributes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */


        function get_attributes_wc($products,$product){

            if('variation' == $product->get_type()){
                $product_attributes = $product->get_variation_attributes();
                foreach ($product_attributes as $product_attribute => $value) {
                    $product_attribute_name = substr($product_attribute,strlen('attribute_'));
                    $products[$product_attribute_name] = $value;
                }
            }else {
                $product_attributes = $product->get_attributes();
                foreach ($product_attributes as $product_attribute) {
                    $name = $product_attribute->get_name();
                    $products[$name] = $product->get_attribute($name);
                }
            }
            return $products;
        }

        /**
         * Return parent description
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_parent_description($post_parent_id, $product) {
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
        function get_parent_sku($post_parent_id) {
            $product_parent = wc_get_product($post_parent_id);
            $parent_sku = yit_get_prop($product_parent,'sku',true);
            return $parent_sku;
        }
        /**
         * Return image link
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_image_link($product_image_id) {

            $image_link = wp_get_attachment_url($product_image_id);
            if( !$image_link ) {
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
        function get_variation_type($product) {

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
        function get_short_description( $product,$post ) {
            if ( version_compare( WC()->version , '3.0.0', '>=' ) ) {
                return strip_tags($product->get_description());
            } else {
                if('variation' == $product->get_type() && $product->get_variation_description()){
                    return strip_tags($product->get_variation_description());
                }
                return yit_get_prop($product,'description',true);
            }

        }
        /**
         * Return sale price effective date
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_sale_price_effective_date( $product ) {
            $sale_price_effective_date = '';
            $from = yit_get_prop($product,'sale_price_dates_from',true) ? date( 'Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product,'sale_price_dates_from',true)) ) : '';
            $to = yit_get_prop($product,'sale_price_dates_to',true) ? date( 'Y-m-d\TH:iO', yit_datetime_to_timestamp(yit_get_prop($product,'sale_price_dates_to',true)) ) : '';
            if($from && $to) {
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
        function get_product_images($product,$post) {

            if ( version_compare( WC()->version , '3.0.0', '>=' ) ) {

                $attachment_ids = $product->get_gallery_image_ids();

            } else {
                $attachment_ids = $product->get_gallery_attachment_ids();

            }

            $gallery_urls = array();
            if ($attachment_ids && 'grouped' != $product->get_type()) {
                foreach ( $attachment_ids as $attachment_id ) {
                    $props       = wc_get_product_attachment_props( $attachment_id, $post );
                    if ( ! $props['url'] ) {
                        continue;
                    }
                    $image_url = $props['url'] ;
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
            $terms = get_the_terms($id, $taxonomy);

            if (is_wp_error($terms)) {
                return $terms;
            }

            if (empty($terms)) {
                return '';
            }

            $links = array();

            foreach ($terms as $term) {
                $links[] = $term->name;
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
        function print_content($attributes,$value,$prefix ='',$suffix=''){

            return '<g:'.$attributes.'>'.$this->CDATA( $prefix.$value.$suffix ).'</g:'.$attributes.'>';
        }

        /**
         * Add identifier_exists_attribute
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function add_identifier_exists( $content,$array,$flag='' ) {

            $attribute = 'identifier_exists';
            if ( ( in_array('brand',$array ) ) && ( ( in_array('mpn',$array ) || in_array('gtin',$array ) ) ) ) {
                $value = 'yes';
            } else {
                $value = 'no';
            }

            if ($flag) {
                $content.= $this->print_content($attribute,$value);
            }else {
                $content[] = $value;
            }

            return $content;
        }

        /**
         * get shipping information
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        function get_shipping_information($product,$type) {
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


    }
}