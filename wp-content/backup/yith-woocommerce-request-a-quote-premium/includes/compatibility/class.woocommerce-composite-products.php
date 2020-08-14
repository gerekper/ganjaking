<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements the YWRAQ_WooCommerce_Composite_Products class.
 *
 * @class   YWRAQ_WooCommerce_Composite_Products
 * @package YITH
 * @since   1.3.6
 * @author  YITH
 */
if ( !class_exists( 'YWRAQ_WooCommerce_Composite_Products' ) ) {

	/**
	 * Class YWRAQ_WooCommerce_Composite_Products
	 */
	class YWRAQ_WooCommerce_Composite_Products {

        /**
         * Single instance of the class
         *
         * @var \YWRAQ_WooCommerce_Composite_Products
         */

        protected static $instance;




        /**
         * Returns single instance of the class
         *
         * @return \YWRAQ_WooCommerce_Composite_Products
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @author Emanuela Castorina
         */
        public function __construct() {

            add_filter( 'ywraq_add_item', array( $this, 'add_item' ), 10, 2 );
            add_filter( 'yith_ywraq_item_class', array( $this, 'add_class_to_composite_parent' ), 10, 3 );
            add_filter( 'yith_ywraq_item_attributes', array( $this, 'add_attributes_to_composite_parent' ), 10, 3 );
            add_action( 'ywraq_after_request_quote_view_item', array( $this, 'show_composit_data' ), 10, 2 );
            add_action( 'ywraq_after_request_quote_view_item_on_email', array( $this, 'show_composit_data_on_email' ), 10, 2 );
            add_filter( 'ywraq_add_to_cart_from_request', array( $this, 'add_to_cart_from_request' ), 10, 3 );
            add_action( 'woocommerce_before_calculate_totals', array( $this, 'add_custom_price' ) );

        }

		/**
		 * @param $cart
		 * @param $values
		 * @param $item
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_to_cart_from_request( $cart, $values, $item ) {

            if( array_key_exists( 'composite_data', $values ) ){
                foreach ( $values['composite_data'] as $key => $cdata ) {

                    $product_id = ( isset( $cdata['variation_id'] ) && $cdata['variation_id'] != '' ) ? $cdata['variation_id'] : $cdata['product_id'];

                    if( ! $cdata['price'] ){
                        add_filter('woocommerce_add_cart_item', array(&$this, 'add_cart_item'), 10, 2);
                    }

                    $new_cart_item_key = $cart->add_to_cart(
                        $product_id,
                        $values['quantity']*$cdata['quantity'],
                        ( isset( $cdata['variation_id'] ) ? $cdata['variation_id'] : '' ),
                        ( isset( $cdata['variation'] ) ? $cdata['variation'] : '' )
                    );


                    if( $new_cart_item_key && ! empty( $cdata['discount'] ) ){
                        $price                                                                       = $cart->cart_contents[ $new_cart_item_key ]['data']->price;
                        $price_with_discount                                                         = $price - ( $price * $cdata['discount'] ) / 100;
                        $cart->cart_contents[ $new_cart_item_key ]['data']->price                    = $price_with_discount;
                        $cart->cart_contents[ $new_cart_item_key ]['line_total']                     = $price_with_discount *  $values['quantity']*$cdata['quantity'];
                        $cart->cart_contents[ $new_cart_item_key ]['line_subtotal']                  = $price_with_discount *  $values['quantity']*$cdata['quantity'];
                        $cart->cart_contents[ $new_cart_item_key ]['price']                          = $price_with_discount;
                    }

                }

                if( isset( $cart->cart_contents[$item]['data']->per_product_pricing) && $cart->cart_contents[$item]['data']->per_product_pricing =='yes'){
                    $cart->cart_contents[ $item ]['data']->ywraq_composite_price = 0;
                    $cart->cart_contents[ $item ]['line_total']                  = 0;
                    $cart->cart_contents[ $item ]['line_subtotal']               = 0;
                    $cart->cart_contents[ $item ]['price']                       = 0;
                }


            }
          //  die;
            return $cart;
        }

		/**
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_cart_item( $cart_item, $cart_item_key ) {
            $cart_item['data']->ywraq_composite_price = 0;
            return $cart_item;
        }

		/**
		 * @param $cart_object
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_custom_price( $cart_object ) {
            foreach ( $cart_object->cart_contents as $key => $value ) {
                if( isset( $value['data']->ywraq_composite_price) ){
                    $value['data']->price =  $value['data']->ywraq_composite_price;
                }
            }
        }

		/**
		 * @param $class
		 * @param $raq
		 * @param $key
		 *
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_class_to_composite_parent( $class, $raq, $key  ) {
            if ( array_key_exists( 'composite_data', $raq[ $key ] ) ) {
                $class .= ' composite-parent';
            }
            return $class;
        }


		/**
		 * @param $attributes
		 * @param $raq
		 * @param $key
		 *
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_attributes_to_composite_parent( $attributes, $raq, $key  ) {
            if ( array_key_exists( 'composite_data', $raq[ $key ] ) ) {
                $attributes .= ' data-composite-id=' . $raq[ $key ]['product_id']. '';
            }
            return $attributes;
        }

		/**
		 * @param $raq
		 * @param $product_raq
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_item( $raq, $product_raq ) {
         
            $composit = WC_CP()->cart->wc_cp_validation( $product_raq, $product_raq['product_id'], $product_raq['quantity'] );

            if ( $composit ) {
                $raq = WC_CP()->cart->wc_cp_add_cart_item_data( $raq, $product_raq['product_id'] );
            }

            return $raq;
        }

		/**
		 * @param $raq
		 * @param $key
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function show_composit_data( $raq, $key){


            if ( array_key_exists( 'composite_data', $raq[ $key ] ) ) {
                $composite_data =$raq[ $key ]['composite_data'];

                $composite_quantity = $raq[ $key ]['quantity'];
                foreach ( $composite_data as $key => $cdata ){
                    $_product = wc_get_product( ( isset( $cdata['variation_id'] ) && $cdata['variation_id'] != '' ) ? $cdata['variation_id'] : $cdata['product_id'] );
                    
                    if( ! $_product ){
                        continue;
                    }
                    
                    ?>
                    <tr class="cart_item composite-data" data-composite-id="<?php echo $cdata['composite_id']   ?>">
                        <td class="product-remove">
                        </td>
                        <td class="product-thumbnail">
                            <?php $thumbnail =  $_product->get_image();

                            if ( ! $_product->is_visible() )
                                echo $thumbnail;
                            else
                                printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                            ?>
                        </td>

                        <td class="product-name">
                            <?php

                            $title = $_product->get_title();

                            if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
	                            $sku = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
	                            $title .= apply_filters( 'ywraq_sku_label_html', $sku, $_product );
                            }

                            echo sprintf( '<strong>%s</strong><br>', $cdata['title'] );
                            ?>
                            <a href="<?php echo $_product->get_permalink() ?>"><?php echo $title ?></a>
                            <?php
                            // Meta data

                            $item_data = array();

                            // Variation data
                            if ( ! empty( $cdata['variation_id'] ) && is_array( $cdata['variations'] ) ) {

                                foreach ( $cdata['variations'] as $name => $value ) {
                                    $label = '';

                                    if ( '' === $value )
                                        continue;

                                    $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

                                    // If this is a term slug, get the term's nice name
                                    if ( taxonomy_exists( $taxonomy ) ) {
                                        $term = get_term_by( 'slug', $value, $taxonomy );
                                        if ( ! is_wp_error( $term ) && $term && $term->name ) {
                                            $value = $term->name;
                                        }
                                        $label = wc_attribute_label( $taxonomy );

                                    }else {

                                        if( strpos( $name, 'attribute_') !== false ) {
                                            $custom_att = str_replace( 'attribute_', '', $name );

                                            if ( $custom_att != '' ) {
                                                $label = wc_attribute_label( $custom_att );
                                            }
                                            else {
                                                $label = $name;
                                            }
                                        }

                                    }

                                    $item_data[] = array(
                                        'key'   => $label,
                                        'value' => $value
                                    );
                                }
                            }

                            $item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data , $raq , $_product);


                            // Output flat or in list format
                            if ( sizeof( $item_data ) > 0 ) {
                                foreach ( $item_data as $data ) {
                                    echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
                                }
                            }


                            ?>
                        </td>


                        <td class="product-quantity">
                            <?php
                            echo $cdata['quantity'] * $composite_quantity;
                            ?>
                        </td>

                        <td class="product-subtotal">
                            <?php
                            echo ( $cdata['price']) ? __('Option subtotal: ', 'yith-woocommerce-request-a-quote') . wc_price( $cdata['price'] * $composite_quantity *  $cdata['quantity'] ) : '';
                            ?>
                        </td>
                    </tr>
                    <?php

                }

            }
        }

		/**
		 * @param $raq
		 * @param $key
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function show_composit_data_on_email( $raq, $key){

            if ( array_key_exists( 'composite_data', $raq[ $key ] ) ) {
                $composite_data =$raq[ $key ]['composite_data'];
              
                $composite_quantity = $raq[ $key ]['quantity'];
                foreach ( $composite_data as $key => $cdata ){
                    $_product = wc_get_product( ( isset( $cdata['variation_id'] ) && $cdata['variation_id'] != '' ) ? $cdata['variation_id'] : $cdata['product_id'] );

                    if( ! $_product ){
                        continue;
                    }
                    
                    $title = $_product->get_title();

                    if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
	                    $sku = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
	                    $title .= apply_filters( 'ywraq_sku_label_html', $sku, $_product );
                    }
                    ?>
                    <tr>
                        <?php if( get_option('ywraq_show_preview') == 'yes'): ?>
                            <td scope="col" class="td" style="text-align:center;border: 1px solid #eee;">
                                <?php

                                $dimensions = wc_get_image_size( 'shop_thumbnail' );
                                $height     = esc_attr( $dimensions['height'] );
                                $width      = esc_attr( $dimensions['width'] );
                                $src        = ( $_product->get_image_id() ) ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'shop_thumbnail' ) ) : wc_placeholder_img_src();

                                ?>
                                <a href="<?php echo $_product->get_permalink(); ?>"><img src="<?php echo $src; ?>" height="<?php echo $height; ?>" width="<?php echo $width; ?>" /></a>
                            </td>
                        <?php endif ?>

                        <td scope="col" style="text-align:left;"><?php echo sprintf( '<strong>%s</strong><br>', $cdata['title'] ); ?><a href="<?php echo  $_product->get_permalink() ?>"><?php  echo $title ?></a>
                            <?php  if( isset($cdata['variations']) || isset($cdata['addons']) ): ?><small><?php echo yith_ywraq_get_product_meta($cdata); ?></small><?php endif ?></td>
                        <td scope="col" style="text-align:left;"><?php echo $cdata['quantity'] * $composite_quantity ?></td>
                        <td scope="col" style="text-align:left;"><?php echo apply_filters( 'yith_ywraq_hide_price_template' ,  ( $cdata['price']) ? __('Option subtotal: ', 'yith-woocommerce-request-a-quote') . wc_price( $cdata['price'] * $composite_quantity *  $cdata['quantity'] ) : '', $_product->get_id(), $cdata ); ?></td>
                    </tr>
                    <?php

                }

            }
        }

    }

	/**
	 * Unique access to instance of YWRAQ_WooCommerce_Product_Addon class
	 *
	 * @return YWRAQ_WooCommerce_Composite_Products
	 */
    function YWRAQ_WooCommerce_Composite_Products() {
        return YWRAQ_WooCommerce_Composite_Products::get_instance();
    }

    if ( class_exists( 'WC_Composite_Products' ) ) {
        YWRAQ_WooCommerce_Composite_Products();
    }

}