<?php
/**
 * Main class for counter widget
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.1.4
 */

if ( !defined( 'YITH_WOOCOMPARE' ) ) { exit; } // Exit if accessed directly

if( ! class_exists( 'YITH_WOOCOMPARE' ) ) {
    /**
     * YITH WooCommerce Compare Counter Widget
     *
     * @since 1.0.0
     */
    class YITH_Woocompare_Widget_Counter extends WP_Widget {

        function __construct() {
            $widget_ops = array (
	            'classname' => 'yith-woocompare-counter-widget',
	            'description' => __( 'The widget shows a counter of products added in the comparison table.', 'yith-woocommerce-compare'
	            )
            );

	        parent::__construct( 'yith-woocompare-counter-widget', _x( 'YITH WooCommerce Compare Counter Widget', 'The widget name', 'yith-woocommerce-compare' ), $widget_ops );
        }


        function widget( $args, $instance ) {
            global $yith_woocompare;

            /**
             * WPML Support
             */
            extract( $args );

            do_action ( 'wpml_register_single_string', 'Widget', 'widget_yit_compare_title_text', $instance['title'] );
            $localized_widget_title = apply_filters ( 'wpml_translate_single_string', $instance['title'], 'Widget', 'widget_yit_compare_title_text' );

            echo $before_widget . $before_title . $localized_widget_title . $after_title;
            echo do_shortcode( '[yith_woocompare_counter type="'.$instance['type'].'" show_icon="'.$instance['show_icon'].'" text="'.$instance['text'].'" icon="'.$instance['icon'].'"]');
            echo $after_widget;
        }


        function form( $instance ) {

            $defaults = array(
                'title'     => '',
                'type'      => 'text',
                'show_icon' => 'yes',
                'text'      => '',
                'icon'      => ''
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>

            <p>
                <label>
                    <?php _e( 'Title', 'yith-woocommerce-compare' ) ?>:<br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'type' ); ?>">
                    <?php _ex( 'Counter style', 'The widget counter style', 'yith-woocommerce-compare' ) ?>:<br />
                    <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
                        <option value="text" <?php selected( 'text', $instance['type'] ) ?>><?php echo __( 'Number and text', 'yith-woocommerce-compare' ); ?></option>
                        <option value="number" <?php selected( 'number', $instance['type'] ) ?>><?php echo __( 'Only number', 'yith-woocommerce-compare' ); ?></option>
                    </select>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'show_icon' ); ?>">
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'show_icon' ); ?>" name="<?php echo $this->get_field_name( 'show_icon' ); ?>" <?php checked( 'yes', $instance['show_icon'] ); ?> value="yes"/>
                    <?php _e( 'Show counter icon', 'yith-woocommerce-compare' ) ?>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'icon' ); ?>">
                    <?php _e( 'Icon url', 'yith-woocommerce-compare' ) ?>:<br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="<?php echo $instance['icon']; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'text' ); ?>">
                    <?php _e( 'Counter text', 'yith-woocommerce-compare' ) ?>:<br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo $instance['text']; ?>" />
                </label>
                <span class="description">
                    <?php _e( 'Use {{count}} as placeholder of products counter.', 'yith-woocommerce-compare' ); ?>
                </span>
            </p>

        <?php
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['type'] = $new_instance['type'];
            $instance['show_icon'] = $new_instance['show_icon'];
            $instance['text'] = $new_instance['text'];
            $instance['icon'] = esc_url( $new_instance['icon'] );

            return $instance;
        }

    }
}