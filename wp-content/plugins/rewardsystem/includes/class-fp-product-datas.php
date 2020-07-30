<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! function_exists( 'fp_product_category' ) ) {

    function fp_product_category() {
        $ProductCategory = get_terms( 'product_cat' ) ;
        if ( is_wp_error( $ProductCategory ) )
            return array() ;

        if ( ! srp_check_is_array( $ProductCategory ) )
            return array() ;

        $categoryname = array() ;
        $categoryid   = array() ;
        foreach ( $ProductCategory as $category ) {
            $categoryname[] = $category->name ;
            $categoryid[]   = $category->term_id ;
        }
        return array_combine( ( array ) $categoryid , ( array ) $categoryname ) ;
    }

}

if ( ! function_exists( 'srp_product_filter_for_quick_setup' ) ) {

    function srp_product_filter_for_quick_setup( $productid , $variationid , $Options ) {
        $product_id = $variationid != ('' || 0) ? $variationid : $productid ;
        if ( $Options[ 'applicable_for' ] == '1' ) {
            return '2' ;
        } elseif ( $Options[ 'applicable_for' ] == '2' ) {
            $included_product = is_array( $Options[ 'included_products' ] ) ? $Options[ 'included_products' ] : explode( ',' , $Options[ 'included_products' ] ) ;
            if ( in_array( $product_id , $included_product ) )
                return '2' ;
        } elseif ( $Options[ 'applicable_for' ] == '3' ) {
            $excluded_product = is_array( $Options[ 'excluded_products' ] ) ? $Options[ 'excluded_products' ] : explode( ',' , $Options[ 'excluded_products' ] ) ;
            if ( srp_check_is_array( $excluded_product ) ) {
                foreach ( $excluded_product as $productid ) {
                    if ( srp_check_is_array( get_variation_id( $productid ) ) )
                        $excluded_product = array_merge( $excluded_product , get_variation_id( $productid ) ) ;
                }
            }
            if ( ! in_array( $product_id , $excluded_product ) )
                return '2' ;
        } elseif ( $Options[ 'applicable_for' ] == '4' ) {
            $term = get_the_terms( $productid , 'product_cat' ) ;
            if ( srp_check_is_array( $term ) )
                return '2' ;
        } elseif ( $Options[ 'applicable_for' ] == '5' ) {
            $included_cat = is_array( $Options[ 'included_categories' ] ) ? $Options[ 'included_categories' ] : explode( ',' , $Options[ 'included_categories' ] ) ;
            if ( srp_check_is_array( $included_cat ) ) {
                foreach ( $included_cat as $eachcat ) {
                    $term = get_the_terms( $productid , 'product_cat' ) ;
                    if ( ! srp_check_is_array( $term ) )
                        continue ;

                    foreach ( $term as $termidlist ) {
                        if ( $eachcat == $termidlist->term_id ) {
                            return '2' ;
                        }
                    }
                }
            }
        } else {
            $excluded_cat = is_array( $Options[ 'excluded_categories' ] ) ? $Options[ 'excluded_categories' ] : explode( ',' , $Options[ 'excluded_categories' ] ) ;
            $count        = 0 ;
            if ( ! empty( $excluded_cat ) ) {
                $term = get_the_terms( $productid , 'product_cat' ) ;
                if ( srp_check_is_array( $term ) ) {
                    foreach ( $term as $termidlist ) {
                        if ( in_array( $termidlist->term_id , $excluded_cat ) ) {
                            $count ++ ;
                        }
                    }
                    if ( $count == 0 ) {
                        return '2' ;
                    }
                }
            }
        }
    }

}

if ( ! function_exists( 'srp_include_tax_with_price' ) ) {

    function srp_include_tax_with_price() {

        if ( get_option( 'woocommerce_calc_taxes' ) == 'no' )
            return true ;

        if ( get_option( 'rs_display_earn_point_tax_based' ) == 'no' && get_option( 'woocommerce_tax_display_cart' ) == 'incl' )
            return true ;

        return false ;
    }

}

if ( ! function_exists( 'get_regular_price' ) ) {

    function get_regular_price( $productid , $variationid , $item , $itemquantity , $payment_price ) {
        $productid  = empty( $variationid ) ? $productid : $variationid ;
        $ProductObj = srp_product_object( $productid ) ;

        if ( ! $ProductObj )
            return 0 ;

        $getregularprice        = 0 ;
        $initial_payment_chk    = is_initial_payment( $productid ) ;
        $payment_price_frontend = get_payment_data_for_payment_plan( $productid ) ;

        if ( ! is_shop() && ! is_product() && ! is_cart() && ! is_product_category() && ! is_cart() && ! is_checkout() ) {
            if ( ((is_object( $item )) || (srp_check_is_array( $item )) ) ) {
                $line_subtotal     = isset( $item[ 'line_subtotal' ] ) ? $item[ 'line_subtotal' ] : 0 ;
                $line_subtotal_tax = (srp_include_tax_with_price() && isset( $item[ 'line_subtotal_tax' ] )) ? $item[ 'line_subtotal_tax' ] : 0 ;
                if ( isset( $item[ 'qty' ] ) && ! $line_subtotal ) {
                    if ( ! empty( $variationid ) )
                        $ProductObj = new WC_Product_Variation( $variationid ) ;

                    $getregularprice = srp_price_with_tax( $ProductObj , $itemquantity ) ;
                } else {
                    $price           = ( $payment_price == 0 ) ? $line_subtotal : $payment_price ;
                    $getregularprice = (get_option( 'woocommerce_tax_display_cart' ) == 'incl') ? ($price + $line_subtotal_tax) : $price ;
                }
            } else {
                $getregularprice = is_object( $ProductObj ) ? ( float ) $ProductObj->get_price() * $itemquantity : 0 ;
            }
        } elseif ( is_cart() || is_checkout() ) {
            $line_subtotal              = isset( $item[ 'line_subtotal' ] ) ? $item[ 'line_subtotal' ] : 0 ;
            $line_subtotal_tax          = isset( $item[ 'line_subtotal_tax' ] ) ? $item[ 'line_subtotal_tax' ] : 0 ;
            $price                      = ( $initial_payment_chk == false ) ? $line_subtotal : $payment_price_frontend ;
            $getregularprice            = srp_include_tax_with_price() ? ($price + $line_subtotal_tax) : $price ;
            global $woocommerce ;
            $variation_parent_id        = '' ;
            $simple_productid           = '' ;
            $overallvariation_id        = '' ;
            $related_cross_sell_product = array() ;
            $cart_item_product_id       = array() ;
            $cart_item_variation_id     = array() ;

            if ( $woocommerce->version >= ( float ) '3.0' && ! empty( $woocommerce->cart->cart_contents ) ) {
                foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
                    $cart_item_product_id[]   = $cart_item[ 'product_id' ] ;
                    $cart_item_variation_id[] = $cart_item[ 'variation_id' ] ;
                    $object_data              = isset( $cart_item[ 'data' ] ) ? $cart_item[ 'data' ] : '' ;
                    if ( is_object( $object_data ) && ( ! empty( $object_data )) ) {
                        $related_cross_sell_product[] = ( array ) $object_data->get_cross_sell_ids() ;
                    }
                }
            }

            $related_product  = false ;
            $simple_product   = false ;
            $variable_product = false ;
            global $woocommerce_loop ;
            if ( ( float ) $woocommerce->version >= ( float ) '3.3.0' ) {
                if ( (isset( $woocommerce_loop[ 'name' ] ) && $woocommerce_loop[ 'name' ] == 'cross-sells' ) )
                    $related_product = true ;
            }

            if ( srp_product_type( $productid ) == 'variation' ) {
                $variation_parent_id = wp_get_post_parent_id( $variationid ) ;
                if ( srp_check_is_array( $cart_item_variation_id ) ) {
                    if ( ! in_array( $variation_parent_id , $cart_item_variation_id ) ) {
                        $overallvariation_id = $variation_parent_id ;
                    }
                }
            } else {
                if ( srp_check_is_array( $cart_item_product_id ) ) {
                    if ( ! in_array( $productid , $cart_item_product_id ) ) {
                        $simple_productid = $productid ;
                    }
                }
            }
            if ( srp_check_is_array( $related_cross_sell_product ) ) {
                foreach ( $related_cross_sell_product as $related_cross_sell_products ) {
                    if ( srp_check_is_array( $related_cross_sell_products ) ) {

                        if ( in_array( $simple_productid , $related_cross_sell_products ) )
                            $simple_product = true ;

                        if ( in_array( $overallvariation_id , $related_cross_sell_products ) )
                            $variable_product = true ;
                    }
                }
            }

            if ( $related_product == true && ($simple_product == true || $variable_product == true) ) {
                if ( srp_product_type( $productid ) == 'variation' )
                    $ProductObj = new WC_Product_Variation( $variationid ) ;

                $getregularprice = srp_price_with_tax( $ProductObj , $itemquantity ) ;
            }
        } elseif ( is_shop() || is_product() || is_product_category() ) {
            if ( srp_product_type( $productid ) == 'variation' )
                $ProductObj = new WC_Product_Variation( $variationid ) ;

            $getregularprice = srp_price_with_tax( $ProductObj , $itemquantity ) ;
        }
        if ( get_option( 'rs_calculate_point_based_on_reg_or_sale' ) == '1' ) {
            if ( WC_VERSION >= 3.0 ) {
                $getregularprice = ( float ) $ProductObj->get_regular_price() * $itemquantity ;
            } else {
                $getregularprice = ( float ) $ProductObj->regular_price * $itemquantity ;
            }
        }
        return floatval( $getregularprice ) ;
    }

}

if ( ! function_exists( 'block_points_for_salepriced_product' ) ) {

    function block_points_for_salepriced_product( $ProductId , $Variationid ) {
        if ( get_option( 'rs_point_not_award_when_sale_price' ) != 'yes' )
            return 'no' ;

        if ( empty( $Variationid ) && ! empty( get_post_meta( $ProductId , '_sale_price' , true ) ) )
            return 'yes' ;

        $VarObj = empty( wp_get_post_parent_id( $Variationid ) ) ? srp_product_object( $ProductId ) : new WC_Product_Variation( $Variationid ) ;

        if ( $VarObj ) {
            $SalePrice = (WC_VERSION >= ( float ) '3.0') ? $VarObj->get_sale_price() : $VarObj->sale_price ;
            if ( ! empty( $SalePrice ) )
                return 'yes' ;
        }

        return 'no' ;
    }

}

if ( ! function_exists( 'srp_product_object' ) ) {

    function srp_product_object( $ProductId ) {
        return function_exists( 'wc_get_product' ) ? wc_get_product( $ProductId ) : get_product( $ProductId ) ;
    }

}

if ( ! function_exists( 'srp_price_with_tax' ) ) {

    function srp_price_with_tax( $ProductObj , $Qty ) {
        if ( get_option( 'woocommerce_tax_display_shop' ) == 'incl' && get_option( 'woocommerce_prices_include_tax' ) == 'no' ) {
            $Price = srp_price_including_tax( $ProductObj ) ;
        } elseif ( get_option( 'woocommerce_tax_display_shop' ) == 'incl' && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
            $Price = srp_price_including_tax( $ProductObj ) ;
        } elseif ( get_option( 'woocommerce_tax_display_shop' ) == 'excl' && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
            $Price = srp_price_excluding_tax( $ProductObj ) ;
        } else {
            $Price = is_object( $ProductObj ) ? ( float ) $ProductObj->get_price() * $Qty : 0 ;
        }
        return $Price ;
    }

}

if ( ! function_exists( 'srp_price_excluding_tax' ) ) {

    function srp_price_excluding_tax( $ProductObj ) {
        if ( ! $ProductObj )
            return 0 ;

        return function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $ProductObj ) : $ProductObj->get_price_excluding_tax() ;
    }

}

if ( ! function_exists( 'srp_price_including_tax' ) ) {

    function srp_price_including_tax( $ProductObj ) {
        if ( ! $ProductObj )
            return 0 ;

        return function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $ProductObj ) : $ProductObj->get_price_including_tax() ;
    }

}

if ( ! function_exists( 'product_id_from_obj' ) ) {

    function product_id_from_obj( $product ) {
        if ( WC_VERSION >= ( float ) '3.0' ) {
            $id = $product->get_id() ;
        } else {
            $id = ($product->get_type() == 'variation') ? $product->variation_id : $product->id ;
        }
        return $id ;
    }

}

if ( ! function_exists( 'get_parent_id' ) ) {

    function get_parent_id( $VarObj ) {
        global $woocommerce ;
        return (( float ) $woocommerce->version >= ( float ) '3.0') ? $VarObj->get_parent_id() : $VarObj->parent->id ;
    }

}

if ( ! function_exists( 'get_post_parent' ) ) {

    function get_post_parent( $object ) {
        global $woocommerce ;
        if ( ( float ) $woocommerce->version >= ( float ) '3.0' ) {
            $parent_id = $object->get_parent_id() ;
        } else {
            $parent_id = $object->post->post_parent ;
        }
        return $parent_id ;
    }

}

if ( ! function_exists( 'srp_product_price' ) ) {

    function srp_product_price( $product ) {
        global $woocommerce ;
        if ( ( float ) $woocommerce->version >= ( float ) '3.0' ) {
            $price = $product->get_sale_price() != '' ? $product->get_sale_price() : $product->get_regular_price() ;
        } else {
            $price = $product->sale_price != '' ? $product->sale_price : $product->regular_price ;
        }
        return $price ;
    }

}

if ( ! function_exists( 'check_if_variable_product' ) ) {

    function check_if_variable_product( $ProductObj ) {
        if ( is_object( $ProductObj ) && ($ProductObj->get_type() == 'variation' || $ProductObj->get_type() == 'variable') )
            return true ;

        return false ;
    }

}

if ( ! function_exists( 'srp_product_type' ) ) {

    function srp_product_type( $ProductId ) {
        $ProductObj = srp_product_object( $ProductId ) ;
        return $ProductObj->get_type() ;
    }

}

if ( ! function_exists( 'get_variation_id' ) ) {

    function get_variation_id( $Productid ) {

        if ( isset( FPRewardSystem::$variation_ids[ $Productid ] ) ):
            return FPRewardSystem::$variation_ids[ $Productid ] ;
        endif ;

        $args = array(
            'post_parent' => $Productid ,
            'post_type'   => 'product_variation' ,
            'orderby'     => 'menu_order' ,
            'order'       => 'ASC' ,
            'fields'      => 'ids' ,
            'post_status' => 'publish' ,
            'numberposts' => -1
                ) ;

        $meta_query                                  = rs_get_stock_status_args() ;
        $args                                        = array_merge( $args , $meta_query ) ;
        FPRewardSystem::$variation_ids[ $Productid ] = get_posts( $args ) ;
        if ( ! srp_check_is_array( FPRewardSystem::$variation_ids[ $Productid ] ) ) :
            return array() ;
        endif ;

        return FPRewardSystem::$variation_ids[ $Productid ] ;
    }

}

if ( ! function_exists( 'rs_get_stock_status_args' ) ) {

    function rs_get_stock_status_args() {
        $args = array(
            'meta_query' => array(
                array(
                    'key'   => '_stock_status' ,
                    'value' => 'instock'
                ) ,
            ) ,
                ) ;
        if ( is_shop() || is_product_category() ) {
            if ( '1' == get_option( 'rs_show_or_hide_message_for_outofstock' ) ) {
                return array() ;
            }
        }
        if ( is_page() ) {
            if ( '1' == get_option( 'rs_show_or_hide_message_for_customshop' ) ) {
                return array() ;
            }
        }
        if ( is_product() ) {
            if ( '1' == get_option( 'rs_message_outofstockproducts_product_page' ) ) {
                return array() ;
            }
        }
        return $args ;
    }

}
