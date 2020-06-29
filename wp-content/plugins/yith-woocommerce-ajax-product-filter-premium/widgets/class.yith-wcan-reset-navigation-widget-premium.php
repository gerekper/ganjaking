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

if ( ! class_exists( 'YITH_WCAN_Reset_Navigation_Widget_Premium' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Reset_Navigation_Widget_Premium extends YITH_WCAN_Reset_Navigation_Widget {


        public function __construct() {
            parent::__construct();
        }

        public function form( $instance ){
            parent::form( $instance );

            $defaults = array(
                'custom_style'           => 0,
                'background_color'       => '',
                'background_color_hover' => '',
                'text_color'             => '',
                'text_color_hover'       => '',
                'border_color'           => '',
                'border_color_hover'     => '',
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>

            <p id="yith-wcan-enable-custom-style-<?php echo $instance['custom_style'] ?>" class="yith-wcan-enable-custom-style">
                <label for="<?php echo $this->get_field_id( 'custom_style' ); ?>"><?php _e( 'Use custom style for reset button', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'custom_style' ); ?>" name="<?php echo $this->get_field_name( 'custom_style' ); ?>" value="1" <?php checked( $instance['custom_style'], 1, true )?> class="yith-wcan-enable-custom-style-check widefat"/>
                </label>
            </p>

            <div class="yith-wcan-reset-custom-style" style="display: <?php echo empty( $instance['custom_style'] ) ? 'none' : 'block'?>">
                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Background color', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'background_color' ); ?>" name="<?php echo $this->get_field_name( 'background_color' ); ?>" value="<?php echo $instance['background_color']; ?>" />
                </p>

                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Background color on hover', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'background_color_hover' ); ?>" name="<?php echo $this->get_field_name( 'background_color_hover' ); ?>" value="<?php echo $instance['background_color_hover']; ?>" />
                </p>

                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Text color', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'text_color' ); ?>" name="<?php echo $this->get_field_name( 'text_color' ); ?>" value="<?php echo $instance['text_color']; ?>" />
                </p>

                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Text color on hover', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'text_color_hover' ); ?>" name="<?php echo $this->get_field_name( 'text_color_hover' ); ?>" value="<?php echo $instance['text_color_hover']; ?>" />
                </p>

                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Border color', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" value="<?php echo $instance['border_color']; ?>" />
                </p>

                <p>
                    <label class="yith-wcan-reset-table">
                        <strong><?php _e( 'Border color on hover', 'yith-woocommerce-ajax-navigation' ) ?>:</strong>
                    </label>
                    <input class="widefat yith-colorpicker" type="text" id="<?php echo $this->get_field_id( 'border_color_hover' ); ?>" name="<?php echo $this->get_field_name( 'border_color_hover' ); ?>" value="<?php echo $instance['border_color_hover']; ?>" />
                </p>
            </div>
            <script>jQuery(document).trigger('yith_colorpicker');</script>
            <?php
        }

        public function update( $new_instance, $old_instance ) {

            $instance = parent::update( $new_instance, $old_instance );

            $instance['custom_style']           = isset( $new_instance['custom_style'] ) ? 1 : 0;
            $instance['background_color']       = $new_instance['background_color'];
            $instance['background_color_hover'] = $new_instance['background_color_hover'];
            $instance['text_color']             = $new_instance['text_color'];
            $instance['text_color_hover']       = $new_instance['text_color_hover'];
            $instance['border_color']           = $new_instance['border_color'];
            $instance['border_color_hover']     = $new_instance['border_color_hover'];

            return $instance;
        }

        public function widget( $args, $instance ) {

            if( ! empty( $instance['custom_style'] ) ){
                $css_selector = "#{$args['widget_id']} .yith-wcan .yith-wcan-reset-navigation.button";
                ob_start();?>
                <style>
                    <?php echo $css_selector ?> {
                        <?php if( ! empty( $instance['background_color'] ) ) : ?>
                        background-color: <?php echo $instance['background_color'] ?>;
                        <?php endif; ?>

                        <?php if( ! empty( $instance['text_color'] ) ) : ?>
                        color: <?php echo $instance['text_color'] ?>;
                        <?php endif; ?>

                        <?php if( ! empty( $instance['border_color'] ) ) : ?>
                        border: 1px solid <?php echo $instance['border_color'] ?>;
                        <?php endif; ?>
                    }

                    <?php echo $css_selector ?>:hover {
                         <?php if( ! empty( $instance['background_color_hover'] ) ) : ?>
                        background-color: <?php echo $instance['background_color_hover'] ?>;
                        <?php endif; ?>

                        <?php if( ! empty( $instance['text_color_hover'] ) ) : ?>
                        color: <?php echo $instance['text_color_hover'] ?>;
                        <?php endif; ?>

                        <?php if( ! empty( $instance['border_color_hover'] ) ) : ?>
                        border: 1px solid <?php echo $instance['border_color_hover'] ?>;
                        <?php endif; ?>
                    }
                </style>
                <?php
                echo ob_get_clean();
            }

            $brands = yit_get_brands_taxonomy();

            add_filter( 'yith_woocommerce_reset_filter_link', 'yith_remove_premium_query_arg' );

            if(
                isset( $_GET['orderby'] ) ||
                isset( $_GET['instock_filter'] ) ||
                isset( $_GET['onsale_filter'] ) ||
                isset( $_GET['product_tag'] ) ||
                isset( $_GET[ $brands ] )
            ) {
                add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
            }

            if( isset( $_GET['product_cat'] ) ){
                $_chosen_categories = preg_split( '/[,\+\%2C]/', urlencode( $_GET['product_cat'] ) );
                if( is_array( $_chosen_categories ) && count( $_chosen_categories ) == 1 ){
                    $category_slug = array_shift( $_chosen_categories );
                    $term = get_term_by( 'slug', $category_slug, 'product_cat' );
                    if( ! empty( $term ) && $term->count != 0 ){
                        add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
                    }
                }

                else {
                    add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
                }
            }

            parent::widget( $args, $instance );
        }
    }
}