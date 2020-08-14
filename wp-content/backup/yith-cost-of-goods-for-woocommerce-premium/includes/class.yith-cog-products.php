<?php

defined( 'ABSPATH' ) or exit;

/**
 * @class      YITH_COG_Product
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Product {

    /**
     * Returns the product cost (int)
     */
    public static function get_cost( $product ) {

        $product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

        // get the product cost
        if ( $product->is_type( 'variable' ) ) {
            $cost = get_post_meta( $product->get_id(), 'yith_cog_cost_variable', true );
        }
        else {
            $cost = get_post_meta(  $product->get_id(), 'yith_cog_cost', true );
        }
        // if no cost set for product variation
        if ( '' === $cost && $product->is_type( 'variation' ) ) {
            $cost = $cost = get_post_meta(  $product->get_id(), 'yith_cog_cost_variable', true );
        }

        return apply_filters( 'yith_cog_product_cost', $cost, $product );
    }

    /**
     * Returns the minimum/maximum variations cost
     */
    public static function get_variable_product_min_max_costs( $product ) {

        $product_id = is_object( $product ) ? $product->get_id() : $product;

        $children = get_posts( array(
            'post_parent'    => $product_id,
            'posts_per_page' => -1,
            'post_type'      => 'product_variation',
            'fields'         => 'ids',
            'post_status'    => 'publish',
        ) );

        $min_variation_cost = '';
        $max_variation_cost = '';

        if ( $children ) {
            foreach ( $children as $child_product_id ) {
                $child_cost = self::get_cost( $child_product_id );
                if ( '' === $child_cost ) {
                    continue;
                }
                $min_variation_cost = '' === $min_variation_cost ? $child_cost : min( $min_variation_cost, $child_cost );
                $max_variation_cost = '' === $max_variation_cost ? $child_cost : max( $max_variation_cost, $child_cost );
            }
        }
        return array( $min_variation_cost, $max_variation_cost );
    }


    /**
     * Returns the product cost with html format
     */
    public static function get_cost_html( $product ) {

        $product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

        if ( $product->get_type() == 'variable' ) {

            list( $min_variation_cost, $max_variation_cost ) = self::get_variable_product_min_max_costs( $product );

            if ( '' === $min_variation_cost ) {
                $variable_cost = get_post_meta( $product->get_id(), 'yith_cog_cost_variable', true );

                if ( $variable_cost != '' ){
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        $cost = (float)$variable_cost;
                    }
                    else{
                        $cost =  wc_price ( (float) $variable_cost );
                    }
                }
                else{
                    $cost = apply_filters( 'yith_cog_variable_empty_cost_html', '', $product );
                }
            }
            else {
                if ( $min_variation_cost !== $max_variation_cost ) {
                    if (get_option('yith_cog_currency_report') == 'no') {
                        $cost = (float) $min_variation_cost . ' – ' . (float) $max_variation_cost ;
                    } else {
                        $cost = wc_price ( (float) $min_variation_cost ) . ' – ' . wc_price ( (float) $max_variation_cost );
                    }
                }
                else{
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        $cost = $min_variation_cost;
                    }
                    else{
                        $cost = wc_price ( (float) $min_variation_cost );
                    }
                }
            }
            $cost = apply_filters( 'yith_cog_variable_cost_html', $cost, $product );
        }
        else {
            $cost = self::get_cost( $product );

            if ( '' === $cost ) {
                $cost = apply_filters( 'yith_cog_empty_cost_html', '', $product );
            }
            else {
                if (  get_option('yith_cog_currency_report') == 'no' ) {
                    $cost = apply_filters( 'yith_cog_cost_html', (float) $cost, $product );
                }
                else{
                    $cost = apply_filters( 'yith_cog_cost_html', wc_price ( (float) $cost ), $product );
                }
            }
        }

        return apply_filters( 'yith_cog_get_cost_html', $cost, $product );
    }

}
