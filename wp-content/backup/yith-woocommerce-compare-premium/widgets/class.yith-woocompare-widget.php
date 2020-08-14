<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.1.4
 */

if ( !defined( 'YITH_WOOCOMPARE' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'YITH_WOOCOMPARE' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class YITH_Woocompare_Widget extends WP_Widget {

        function __construct() {
            $widget_ops = array (
	            'classname' => 'yith-woocompare-widget',
	            'description' => __( 'The widget shows the list of products added in the comparison table.', 'yith-woocommerce-compare'
	            )
            );

	        parent::__construct( 'yith-woocompare-widget', __( 'YITH WooCommerce Compare Widget', 'yith-woocommerce-compare' ), $widget_ops );
        }


        function widget( $args, $instance ) {
            global $yith_woocompare;

            /**
             * WPML Support
             */
            $lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false;

            extract( $args );

            do_action ( 'wpml_register_single_string', 'Widget', 'widget_yit_compare_title_text', $instance['title'] );
            $localized_widget_title = apply_filters ( 'wpml_translate_single_string', $instance['title'], 'Widget', 'widget_yit_compare_title_text' );

            echo $before_widget . $before_title . $localized_widget_title . $after_title; ?>

            <ul class="products-list" data-lang="<?php echo $lang ?>" <?php echo $instance['hide_empty'] ? 'data-hide="1"' : ''; ?> >
                <?php echo $yith_woocompare->obj->list_products_html(); ?>
            </ul>

            <a href="<?php echo $yith_woocompare->obj->remove_product_url('all') ?>" data-product_id="all" class="clear-all" rel="nofollow"><?php _e( 'Clear all', 'yith-woocommerce-compare' ) ?></a>
            <a href="<?php echo $yith_woocompare->obj->view_table_url() ?>" class="compare-widget button" rel="nofollow"><?php echo apply_filters( 'yith_woocompare_widget_view_table_button',__( 'Compare', 'yith-woocommerce-compare' )) ?></a>

            <?php echo $after_widget;
        }


        function form( $instance ) {
            global $woocommerce;

            $defaults = array(
                'title' => '',
                'hide_empty'  => 0
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>

            <p>
                <label>
                    <?php _e( 'Title', 'yith-woocommerce-compare' ) ?>:<br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" <?php if( $instance['hide_empty'] ) echo 'checked="checked"'; ?>/>
                    <?php _e( 'Hide if compare table is empty', 'yith-woocommerce-compare' ) ?>
                </label>
            </p>
        <?php
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['hide_empty'] = isset( $new_instance['hide_empty'] );

            return $instance;
        }

    }
}