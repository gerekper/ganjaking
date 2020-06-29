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
 * @class      YITH_Deals_Function_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Deals_Function_Premium' ) ) {
    /**
     * Class YITH_Deals_Frontend_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Deals_Function_Premium extends YITH_Deals_Function
    {

        private $amount;

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            parent::__construct();

        }


        public function check_deal_to_show($deal,$user) {

            $users_not_show_deal = get_post_meta($deal->ID,'yith_wcdls_user_list',true);
            if( is_array($users_not_show_deal) && in_array($user->ID,$users_not_show_deal)) {
                return false;
            }

            $time_now = time();

            $deals_from = ( $datetime = get_post_meta( $deal->ID, '_yith_wcdls_for', true )) ? absint( $datetime ) : false;
            $deals_to   = ( $datetime = get_post_meta( $deal->ID, '_yith_wcdls_to', true )) ? absint( $datetime ) : false;

            if( $deals_from && $time_now < $deals_from ) {
                return false;
            }

            if( $deals_to && $time_now > $deals_to ) {
                return false;
            }


            $deal_accepted  = get_post_meta( $deal->ID, 'yith_wcdls_offer', true );
            $product_for_add = isset($deal_accepted['add_product_check_product_select']) ? $deal_accepted['add_product_check_product_select'] : false ;
            if(!empty($product_for_add) && is_array($product_for_add) && apply_filters('yith_wcdls_check_if_products_are_in_cart',true)) {
                $cart = WC()->cart->get_cart();
                foreach ($cart as $cart_item_key => $cart_item) {

                	$product_id = isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0   ? $cart_item['variation_id'] :  $cart_item['product_id'];

                    if ( in_array( $product_id, $product_for_add ) ) {
                        return false;
                    }
                }
            }

            /////////////////////////////////////////////////////////////////////////////////

            $deal_conditions = get_post_meta($deal->ID,'yith_wcdls_rule',true);
            $conditions = $deal_conditions['conditions'];
            $show_deal = true;
            foreach ( $conditions as $condition ) {
                if ( $show_deal == false ) {
                    return apply_filters('yith_wcdls_check_deal_to_show',false);
                }
                switch($condition['type_restriction']) {

                    case 'price' :
                        $show_deal = $this->restriction_by_price($condition['restriction_by_price'],$condition['price'],$order_cart="" );

                        break;
                    case 'category' :
                        $show_deal = $this->restriction_by_categories($condition['restriction_by'],$condition['categories_selected'],$order_cart="" );

                        break;
                    case 'tag' :
                        $show_deal = $this->restriction_by_tags($condition['restriction_by'],$condition['tags_selected'],$order_cart="");

                        break;
                    case 'product' :
                        $show_deal = $this->restriction_by_products($condition['restriction_by'],$condition['products_selected'],$order_cart="" );

                        break;
                    case 'geolocalization' :

                        $show_deal = $this->restriction_by_geolocalization($condition['restriction_by'],$condition['geolocalization']);

                       break;

                    case 'user' :

                        $show_deal = $this->restriction_by_user($condition['restriction_by'],$condition['users_selected'],$user);

                        break;

                    case 'role' :
                        $show_deal = $this->restriction_by_role($condition['restriction_by'],$condition['roles'],$user);
                        break;

                    default :
                        $show_deal = false;
                        break;
                }
            }
            return apply_filters('yith_wcdls_check_deal_to_show',$show_deal);

        }


        /**
         * Restriction by price
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_price( $restriction_by, $threshold, $order_cart ) {
            $cart_total = ( $order_cart ) ? $order_cart['cart_total'] : WC()->cart->total;
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
         * Restriction by categories
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_categories( $restriction_by, $selected_cats, $order_cart ) {
            $item_cart = ($order_cart) ? $order_cart['items'] : WC()->cart->get_cart();
            $cats_in_cart = array();
			$status = true;
            foreach ( $item_cart as $cart_item_key => $cart_item ) {
                if( !isset( $cart_item['yith_wcdls_type_offer'] ) ) {

                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    $item_terms = get_the_terms($product_id, 'product_cat');

                    if (!empty($item_terms)) {
                        foreach ($item_terms as $term) {
                            if (!in_array($term->term_id, $cats_in_cart)) {
                                $cats_in_cart[] = $term->term_id;
                            }
                        }
                    }
                }
            }
            switch ( $restriction_by ) {
                case 'include_or' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_cats as $cat ){
                            if( in_array( $cat, $cats_in_cart ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
	                        $status =  false;
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
	                    $status =  false;
                    }
                    break;

                case 'include_and' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        foreach( (array) $selected_cats as $cat ){
                            if( ! in_array( $cat, $cats_in_cart ) ){
	                            $status = false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
	                    $status = false;
                    }
                    break;

                case 'exclude_or' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        foreach( (array) $selected_cats as $cat ){
                            if( in_array( $cat, $cats_in_cart ) ){
	                            $status = false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
	                    $status = false;
                    }
                    break;

                default :
	                $status = false;
                    break;
            }
            return apply_filters( 'yith_wcdls_check_restriction_by_categories',$status,$item_cart,$cats_in_cart,$selected_cats );;
        }

        /**
         * Restriction by tag
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */

        function restriction_by_tags( $restriction_by,$selected_tags, $order_cart ) {
            $item_cart = ($order_cart) ? $order_cart['items'] : WC()->cart->get_cart();

            $tags_in_cart = array();
            foreach ( $item_cart as $cart_item_key => $cart_item ) {

                if( !isset( $cart_item['yith_wcdls_type_offer'] ) ) {

                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    $item_terms = get_the_terms($product_id, 'product_tag');
                    if (!empty($item_terms)) {
                        foreach ($item_terms as $term) {
                            if (!in_array($term->term_id, $tags_in_cart)) {
                                $tags_in_cart[] = $term->term_id;
                            }
                        }
                    }
                }
            }
            switch ( $restriction_by ) {
                case 'include_or' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_tags as $tag ){
                            if( in_array( $tag, $selected_tags ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return  false;
                    }
                    break;

                case 'include_and' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        foreach( (array) $selected_tags as $tag ){
                            if( ! in_array( $tag, $tags_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return false;
                    }
                    break;

                case 'exclude_or' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        foreach( (array) $selected_tags as $tag ){
                            if( in_array( $tag, $tags_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return false;
                    }
                    break;

                default :
                    return false;
                    break;
            }
            return true;
        }
        /**
         * Restriction by product
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_products( $restriction_by,$selected_products,$order_cart ) {
            $item_cart = ($order_cart) ? $order_cart['items'] : WC()->cart->get_cart();
            $products_in_cart = array();
            $status = true;
            foreach ( $item_cart as $cart_item_key => $cart_item ) {
                if( !isset( $cart_item['yith_wcdls_type_offer'] ) ) {
                    $product_id = apply_filters('woocommerce_cart_item_product_id', isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0   ? $cart_item['variation_id'] :  $cart_item['product_id'], $cart_item, $cart_item_key);
                    $products_in_cart[] = $product_id;

                }
            }
            switch( $restriction_by ){
                case 'include_or':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_products as $product ){
                            if( in_array( $product, $products_in_cart ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            $status = false;
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        $status = false;
                    }

                    break;
                case 'include_and':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        foreach( (array) $selected_products as $product ){
                            if( ! in_array( $product, $products_in_cart ) ){
                                $status = false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        $status = false;
                    }

                    break;
                case 'exclude_or':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        foreach( (array) $selected_products as $product ){
                            if( in_array( $product, $products_in_cart ) ){
                                $status = false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        $status = false;
                    }

                    break;
                default :
                    $status = false;
            }

            return apply_filters( 'yith_wcdls_check_restriction_by_products',$status,$item_cart,$products_in_cart,$selected_products );
        }

        /**
         * Restriction by geolocalization
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_geolocalization($restriction_by,$countries) {

            $customer = yith_wcdls_get_country_customer();
            $country = $customer['country'];

            if( empty( $countries ) ){
                return false;
            }

            switch( $restriction_by ){
                case 'include_or':
                case 'include_and':

                    if( ! in_array( $country, $countries ) ){
                        return false;
                        break;
                    }

                    break;
                case 'exclude_or':

                    if( in_array( $country, $countries ) ){
                        return false;
                        break;
                    }

                    break;
                default :
                    return false;
            }
            return true;
        }

        /**
         * Restriction by user
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_user( $restriction_by,$user_selected,$user ) {
            $user_id = $user->ID;

            switch( $restriction_by ){
                case 'include_or':
                case 'include_and':

                    if( ! empty( $user_selected ) && ! empty( $user_id ) ){
                        $found = false;
                            if( in_array( $user_id, $user_selected ) ){
                                $found = true;
                            }


                        if( ! $found ){
                            return false;
                        }
                    } elseif( ! empty( $user_selected ) ){
                        return false;
                    }

                    break;
                case 'exclude_or':

                    if( ! empty( $user_selected ) && ! empty( $user_id ) ){
                        if( in_array( $user_id, $user_selected ) ){
                            return false;
                        }

                    } elseif ( ! empty( $user_selected ) ) {
                        return false;
                    }

                    break;
                default :
                    return false;
            }
            return true;
        }

        /**
         * Restriction by role
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_role( $restriction_by,$selected_roles,$user ) {
            $user_roles = $user->roles;


            switch( $restriction_by ){
                case 'include_or':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        $found = false;
                        foreach( (array) $selected_roles as $role ){
                            if( in_array( $role, $user_roles ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_roles ) ){
                        return false;
                    }

                    break;
                case 'include_and':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        foreach( (array) $selected_roles as $role ){
                            if( ! in_array( $role, $user_roles ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_roles ) ){
                        return false;
                    }

                    break;
                case 'exclude_or':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        foreach( (array) $selected_roles as $role ){
                            if( in_array( $role, $user_roles ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_roles ) ){
                        return false;
                    }

                    break;
                default :
                    return false;
            }
            return true;
        }

        /**
         * Accept conditions
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         */
         public function accept_offer($offer_id,$cart)
         {
	         $offer_accepted = get_post_meta($offer_id, 'yith_wcdls_offer', true);

	         $product_id_add = isset( $offer_accepted['add_product_check_product_select'] )  ? $offer_accepted['add_product_check_product_select'] : '';

	         //Check product is not in cart
	         if(!empty($product_id_add) && is_array($product_id_add) && apply_filters('yith_wcdls_check_if_products_are_in_cart',true)) {
		         $cart_aux = WC()->cart->get_cart();
		         foreach ($cart_aux as $cart_item_key => $cart_item) {
			         if (in_array($cart_item['product_id'], $product_id_add)) {
				         return false;
			         }
		         }
	         }

             //Remove Products
             switch ($offer_accepted['remove_product_check_product_radio']) {

                 case 'remove_all_products' :
                     $cart->empty_cart();
                     break;

                 case 'remove_some_products' :
                     $products_remove = apply_filters('yith_wcdls_remove_some_products',isset($offer_accepted['remove_product_check_product_select']) ? $offer_accepted['remove_product_check_product_select'] : '' );
                     if (is_array($products_remove) && !empty($products_remove)) {
                         foreach ($cart->get_cart() as $cart_item_key => $values) {

                             if ((in_array($values['product_id'], $products_remove)) || (in_array($values['variation_id'], $products_remove))) {
                                 $cart->remove_cart_item($cart_item_key);
                             }
                         }
                     }
                     break;
             }
             //Add Products
             if (is_array($product_id_add) && !empty($product_id_add)) {
             	$original_cart = array();
                 foreach ($cart->get_cart() as $cart_item_key => $values) {
                        $original_cart[]= $values['product_id'];
                 }

                 $conditions = get_post_meta($offer_id,'yith_wcdls_rule',true);

                 $deals_value = $offer_accepted['type_offer_value'];
                 switch ($offer_accepted['type_offer_selected']) {


                     case 'fixed_product_discount' :
                         $this->amount = (float)$deals_value;

                         foreach ($product_id_add as $product_id) {

                             $cart->add_to_cart( apply_filters('yith_wcdls_product_add_to_cart',$product_id), 1, 0, 0, array( 'yith_wcdls_type_offer' => $offer_accepted['type_offer_selected'],'yith_wcdls_offer_value' => $deals_value,'yith_wcdls_cart'=>$original_cart,'yith_wcdls_product_ids_add' => $product_id_add,'yith_wcdls_deals_conditions' =>$conditions['conditions']) );
                         }

                         break;
                     case 'percentage_product_discount' :

                         foreach ($product_id_add as $product_id) {

                             $cart->add_to_cart( apply_filters('yith_wcdls_product_add_to_cart',$product_id), 1, 0, 0, array( 'yith_wcdls_type_offer' => $offer_accepted['type_offer_selected'],'yith_wcdls_offer_value' => $deals_value,'yith_wcdls_cart'=>$original_cart,'yith_wcdls_product_ids_add' => $product_id_add,'yith_wcdls_deals_conditions' =>$conditions['conditions']) );

                         }
                         break;
                     case 'fixed_product_price' :

                         foreach ($product_id_add as $product_id) {

                             $cart->add_to_cart( apply_filters('yith_wcdls_product_add_to_cart',$product_id), 1, 0, 0, array( 'yith_wcdls_type_offer' => $offer_accepted['type_offer_selected'],'yith_wcdls_offer_value' => $deals_value,'yith_wcdls_cart'=>$original_cart,'yith_wcdls_product_ids_add' => $product_id_add,'yith_wcdls_deals_conditions' =>$conditions['conditions']) );
                         }
                         break;

                     default:
                         foreach ($product_id_add as $product_id) {
                             $cart->add_to_cart(apply_filters('yith_wcdls_product_add_to_cart',$product_id), 1, 0, 0, array('yith_wcdls_type_offer' => $offer_accepted['type_offer_selected'], 'yith_wcdls_offer_value' => $deals_value,'yith_wcdls_cart'=>$original_cart,'yith_wcdls_product_ids_add' => $product_id_add,'yith_wcdls_deals_conditions' =>$conditions['conditions']));
                         }
                         break;
                 }

            }

             do_action('yith_wcdls_after_accept_offer',$offer_id,$cart);

        }

        /**
         * Decline conditions
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         */
        public function decline_offer( $offer_id ) {
            $offer_decline = get_post_meta($offer_id, 'yith_wcdls_offer', true);
            switch ($offer_decline['no_accepted_option']) {

                case 'hide_offer' :

                    switch ($offer_decline['hide_offer_select']) {

                        case 'only_now' :

                            break;
                        case 'forever' :
                            $user_id = get_current_user_id();
                            if ( $user_id ) {
                                $hide_offer_user = get_post_meta($offer_id,'yith_wcdls_user_list',true);
                                $hide_offer_user = is_array($hide_offer_user) ? $hide_offer_user[] = $user_id : array($user_id);
                                update_post_meta($offer_id,'yith_wcdls_user_list',$hide_offer_user);
                            }
                            break;


                    }
                    break;

                case 'show_another_offer' :
                    do_action('yith_wcdls_before_show_another_offer',$offer_id);
                    $new_offer = $offer_decline['show_another_offer'];
                    return $new_offer;
                    break;

            }

            return false;

        }
    }
}
