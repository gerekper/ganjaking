<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Reset_Navigation_Widget' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Reset_Navigation_Widget extends WP_Widget {

        function __construct() {
            $widget_ops  = array( 'classname' => 'yith-woocommerce-ajax-product-filter yith-woo-ajax-reset-navigation yith-woo-ajax-navigation woocommerce widget_layered_nav', 'description' => _x( 'Reset all filters set by YITH WooCommerce Ajax Product Filter', '[Plugin Name]', 'yith-woocommerce-ajax-navigation' ) );
            $control_ops = array( 'width' => 400, 'height' => 350 );
            parent::__construct( 'yith-woo-ajax-reset-navigation', __( 'YITH Ajax Reset Filter', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );
        }


        function widget( $args, $instance ) {
            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();

            extract( $args );

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
                return;
            }

            // Price
            $min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : 0;
            $max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : 0;

            $after_widget  = apply_filters( 'yith_wcan_after_reset_widget', $after_widget );
            $before_widget = apply_filters( 'yith_wcan_before_reset_widget', $before_widget );

            ob_start();

            if ( count( $_chosen_attributes ) > 0 || $min_price > 0 || $max_price > 0 || apply_filters( 'yith_woocommerce_reset_filters_attributes', false ) ) {
                $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
                $label = isset( $instance['label'] ) ? apply_filters( 'yith-wcan-reset-navigation-label', $instance['label'], $instance, $this->id_base ) : '';

                $link = '';

                //clean the url
                if( ! isset( $_GET['source_id'] ) ){
                    $link = '';

                    //Check if the user have enabled only WC PRice Filter
                    if( yit_is_filtered_uri() && ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) && is_product_taxonomy() ){
                        $queried_object = get_queried_object();

                        if( $queried_object instanceof WP_Term && ! isset( $_GET[ $queried_object->taxonomy ] ) ){
                            $link = get_term_link( $queried_object );
                        }
                    }

	                $link = empty( $link ) || $link instanceof WP_Error ? get_post_type_archive_link( 'product' ) : $link;

                    foreach ( (array) $_chosen_attributes as $taxonomy => $data ) {
                        $taxonomy_filter = str_replace( 'pa_', '', $taxonomy );
                        $link            = remove_query_arg( 'filter_' . $taxonomy_filter, $link );
                    }

                    $link = remove_query_arg( array( 'min_price', 'max_price', 'product_tag' ), $link );
                }

                else{
                    //Start filter from Product category Page
                    $term = null;

                    if( ! empty( $_GET['source_id'] ) && ! empty( $_GET['source_tax'] ) ){
	                    $term = get_term_by( 'term_id', $_GET['source_id'], $_GET['source_tax'] );
                    }

                    if( $term instanceof WP_Term ){
                        $link = get_term_link( $term, $term->taxonomy  );
                    }
                }

	            if( is_search() && isset( $_GET['s'] ) && isset( $_GET['post_type'] ) ){
		            $s = urlencode( stripslashes( $_GET['s'] ) );
		            $link = add_query_arg( array( 's' => $s, 'post_type' => $_GET['post_type'] ), get_home_url() );
	            }

                $link = apply_filters( 'yith_woocommerce_reset_filter_link', $link );

                echo $before_widget;
                if ( $title ) {
                    echo $before_title . $title . $after_title;
                }
                $button_class = apply_filters( 'yith-wcan-reset-navigation-button-class', "yith-wcan-reset-navigation button" );
                $rel_nofollow = yith_wcan_add_rel_nofollow_to_url( true );

                echo "<div class='yith-wcan'><a {$rel_nofollow} class='{$button_class}' href='{$link}'>" . __( $label, 'yith-woocommerce-ajax-navigation' ) . "</a></div>";
                echo $after_widget;
                echo ob_get_clean();
            }
            else {
                ob_end_clean();
                printf( '%s%s', str_replace( '>',  ' style="display:none">', $before_widget ), $after_widget );
            }
        }


        function form( $instance ) {
            $defaults = array(
                'title' => '',
                'label' => __( 'Reset All Filters', 'yith-woocommerce-ajax-navigation' )
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>

            <p>
                <label>
                    <strong><?php _e( 'Title', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>
            <p>
                <label>
                    <strong><?php _e( 'Button Label', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
                </label>
            </p>

        <?php
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['label'] = strip_tags( $new_instance['label'] );

            return $instance;
        }

    }
}
