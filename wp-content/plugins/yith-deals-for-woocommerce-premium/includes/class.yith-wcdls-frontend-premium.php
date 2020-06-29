<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Deals_Frontend_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Deals_Frontend_Premium' ) ) {
    /**
     * Class YITH_Deals_Frontend_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Deals_Frontend_Premium extends YITH_Deals_Frontend
    {

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            add_filter( 'woocommerce_add_cart_item', array($this,'change_cart_item'), 10, 2 );
            add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'change_cart_item_from_session'), 10, 3 );
            add_action('woocommerce_after_calculate_totals',array($this,'check_deal_in_cart'));
            add_filter( 'woocommerce_cart_item_quantity',array( $this,'yith_wcdls_deals_sold_individualy' ), 10, 3 );


            parent::__construct();
        }


        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function enqueue_scripts()
        {
            //Publicar popup y pasar parámetros
            if( is_checkout() || apply_filters( 'yith_wcdls_enqueue_scripts',false )  ) {

                wp_register_style('yith-wcdls-frontend-css', YITH_WCDLS_ASSETS_URL . 'css/wcdls-frontend.css');

                wp_register_script('yith-wcdls-frontend-premium', YITH_WCDLS_ASSETS_URL . 'js/wcdls-frontend-premium.js', array('jquery', 'jquery-ui-datepicker'), '1.0.0', 'true');
                wp_localize_script('yith-wcdls-frontend-premium', 'yith_wcdls', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'popup_size' => get_option( 'yith-wcdls-box-size-pixel' ),

                ));
                wp_enqueue_style('yith-wcdls-frontend-css');
                wp_enqueue_script('yith-wcdls-frontend-premium');
                do_action('yith_wcdls_enqueue_fontend_scripts');

                add_action( 'woocommerce_after_checkout_form', array( $this, 'load_template' ) );
            }

        }

        /**
         * Load template
         *
         * Get offer
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function load_template() {

            if( !did_action('woocommerce_checkout_process') && !is_order_received_page() || apply_filters( 'yith_wcdls_load_template',false ) ) {

                $offer = $this->get_offer();



	            if ( $offer ) {
                    $deals_offer = get_post_meta($offer->ID, 'yith_wcdls_offer', true);

                    $automatic_deal = get_post_meta ( $offer->ID,'yith_wcdls_automatic_deal', true );

                    if( $automatic_deal ) { //Accept directly the offer

                        $cart = WC()->cart;
                        $function = YITH_Deals()->functions;
                        $function->accept_offer($offer->ID,$cart);

                    } else {

                        $args = apply_filters('yith_wcdls_popup_template_args', array(
                            'animation' => $deals_offer['type_layout'],
                            'content' => do_shortcode($offer->post_content),
                            'offer_id' => $offer->ID,
                        ));

                        ?>
                        <div class="yith-wcdls-deals-offer">
                            <?php
                            wc_get_template('yith-deals-popup.php', $args, '', YITH_WCDLS_TEMPLATE_PATH . 'frontend/');
                            ?>
                        </div>
                        <?php

                    }
                }
            }
        }

        /**
         * Get Offer
         *
         * Get offer
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return
         */
        public function get_offer() {
            $deals = get_deals();
            $function = YITH_Deals()->functions;
            $user = wp_get_current_user();
            $offer = false;
            foreach ($deals as $deal) {
                //delete_post_meta($deal->ID,'yith_wcdls_user_list');
                 $show_deal = $function->check_deal_to_show($deal,$user);
                 if($show_deal) {
                     $offer = $deal;
                     break;
                 }
            }

            if( $offer ) {
                return apply_filters('yith_wcdls_get_offer',$offer,$user);
            }

            return false;
        }
        /**
         * Change cart item
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function change_cart_item($cart_item_data, $cart_item_key) {
            if ( isset( $cart_item_data[ 'yith_wcdls_type_offer' ] ) && isset( $cart_item_data['yith_wcdls_offer_value'] ) ) {
                $type = $cart_item_data[ 'yith_wcdls_type_offer' ];
                $value = $cart_item_data[ 'yith_wcdls_offer_value' ];

                switch ($type) {

                    case 'fixed_product_discount' :

                        $product = $cart_item_data[ 'data' ];
                        $price   = $product->get_price();
                        $price   = (float)$price - (float)$value;
                        if($price < 0) {
                            $price = 0;
                        }
                        $product->set_price( $price );
                        $cart_item_data[ 'data' ] = $product;

                        break;
                    case 'percentage_product_discount' :
                        $product = $cart_item_data[ 'data' ];
                        $price   = $product->get_price();
                        $percentage_discount = ((float)$price * (float)$value)/100;
                        $price = (float)$price - (float)$percentage_discount;
                        $product->set_price( $price );
                        $cart_item_data[ 'data' ] = $product;

                        break;
                    case 'fixed_product_price' :

                        $product = $cart_item_data[ 'data' ];
                        $product->set_price( (float)$value );
                        $cart_item_data[ 'data' ] = $product;

                        break;
                }
            }

            return $cart_item_data;
        }

        /**
         * Change cart item from session
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function change_cart_item_from_session( $session_data, $cart_item, $cart_item_key ) {
            if ( isset( $session_data[ 'yith_wcdls_type_offer' ] ) && isset( $session_data['yith_wcdls_offer_value'] ) ) {

                $type = $session_data[ 'yith_wcdls_type_offer' ];
                $value = $session_data[ 'yith_wcdls_offer_value' ];

                switch ($type) {

                    case 'fixed_product_discount' :

                        $product = $session_data[ 'data' ];
                        $price   = $product->get_price();
                        $price   = (float)$price - (float)$value;
                        if($price < 0) {
                            $price = 0;
                        }
                        $product->set_price( (float)$price );
                        $session_data[ 'data' ] = $product;

                        break;

                    case 'percentage_product_discount' :

                        $product = $session_data[ 'data' ];
                        $price   = $product->get_price();
                        $percentage_discount = ((float)$price * (float)$value)/100;
                        $price = (float)$price - (float)$percentage_discount;
                        $product->set_price( (float)$price );
                        $session_data[ 'data' ] = $product;

                        break;

                    case 'fixed_product_price' :

                        $product = $session_data[ 'data' ];
                        $product->set_price( (float)$value );
                        $session_data[ 'data' ] = $product;

                        break;
                }
            }

            return $session_data;
        }
        /**
         * check_offer_in_cart
         *
         * Check if the cart change, remove all the product in offer
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return
         */
        function check_deal_in_cart($cart) {

          $func = YITH_Deals_Function::get_instance();
          foreach ($cart->get_cart() as $cart_item_key => $cart_item_data) {

              if(isset( $cart_item_data[ 'yith_wcdls_cart' ] ) && isset( $cart_item_data['yith_wcdls_product_ids_add'] ) && isset($cart_item_data[ 'yith_wcdls_deals_conditions' ])  ) {

                  $conditions = $cart_item_data[ 'yith_wcdls_deals_conditions' ];
	              $no_remove_product = true;

                  if ( empty($conditions) && WC()->cart->get_cart_contents_count() == 1 /*&& $cart_item_data['yith_wcdls_product_ids_add'][0]*/  ) {

                      $no_remove_product = false;

                  } else {

                  foreach ( $conditions as $condition ) {

	                  if ( $no_remove_product == false ) {
		                  continue;
	                  }
	                  switch ( $condition['type_restriction'] ) {

		                  case 'price' :
			                  $no_remove_product = $this->restriction_by_price_change_cart( $condition['restriction_by_price'], $condition['price'], $order_cart = "" );

			                  break;
		                  case 'category' :
			                  $no_remove_product = $func->restriction_by_categories( $condition['restriction_by'], $condition['categories_selected'], $order_cart = "" );

			                  break;
		                  case 'tag' :
			                  $no_remove_product = $func->restriction_by_tags( $condition['restriction_by'], $condition['tags_selected'], $order_cart = "" );

			                  break;
		                  case 'product' :
			                  $no_remove_product = $func->restriction_by_products( $condition['restriction_by'], $condition['products_selected'], $order_cart = "" );

			                  break;
		                  case 'geolocalization' :

			                  $no_remove_product = $func->restriction_by_geolocalization( $condition['restriction_by'], $condition['geolocalization'] );

			                  break;

		                  case 'user' :
			                  $user              = wp_get_current_user();
			                  $no_remove_product = $func->restriction_by_user( $condition['restriction_by'], $condition['users_selected'], $user );

			                  break;

		                  case 'role' :
			                  $user              = wp_get_current_user();
			                  $no_remove_product = $func->restriction_by_role( $condition['restriction_by'], $condition['roles'], $user );
			                  break;

		                  default :
			                  $no_remove_product = false;
			                  break;
	                  }
                  }
              }
              if( apply_filters('yith_wcdls_check_deal_to_show_in_cart',!$no_remove_product ) ) { //condition == false --> add product to the list
                 $cart->remove_cart_item( $cart_item_key );
              }
          }

        }
      }

    /**
     * Restriction by price when the cart is updated
     *
     * Return false if the restriction is not met
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0.0
     * @return boolean
     */

      function restriction_by_price_change_cart( $restriction_by, $threshold, $order_cart ) {

            $item_cart = ($order_cart) ? $order_cart['items'] : WC()->cart->get_cart();
            $cart_total = 0;
            foreach ( $item_cart as $cart_item_key => $cart_item ) {
                if( !isset( $cart_item['yith_wcdls_type_offer'] ) ) {

                    $sum = $cart_item['line_total'];
                    if( isset($cart_item['line_tax']) && $cart_item['line_tax'] > 0 ) {
                        $sum+=$cart_item['line_tax'];
                    }

                    $cart_total = $cart_total + $sum;

                }
            }
          switch( $restriction_by ){
                case 'less_than':
                    if( ! ( $cart_total < $threshold ) ){
                        return  false;
                    }
                    break;
                case 'less_or_equal':
                    if( ! ( $cart_total <= $threshold ) ){
                        return false;
                    }
                    break;
                case 'equal':
                    if( ! ( $cart_total == $threshold ) ){
                        return false;
                    }
                    break;
                case 'greater_or_equal':
                    if( ! ( $cart_total >= $threshold ) ){
                        return false;
                    }
                    break;
                case 'greater_than':
                    if( ! ( $cart_total > $threshold ) ){
                        return  false;
                    }
                    break;
                default :
                    return false;
                    break;
            }
            return true;
        }

        /**
         * Restrict the quantity
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return string
         */
        public function yith_wcdls_deals_sold_individualy ( $product_quantity, $cart_item_key, $cart_item) {

            if (isset($cart_item['yith_wcdls_type_offer'])) {

                return sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
            }

            return $product_quantity;
        }

    }
}
