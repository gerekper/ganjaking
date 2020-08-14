<?php
if ( !defined( 'ABSPATH' ) || ! defined( 'YITH_YWSL_INIT' ) ) {
    exit; // Exit if accessed directly
}

/**
 * YWSL_Social_Login_Widget
 *
 * @class 	YWSL_Social_Login_Widget
 * @package YITH Woocommerce Social Login Premium
 * @since   1.0.0
 * @author  YITH
 */

if( !class_exists( 'YWSL_Social_Login_Widget' ) ) {
    /**
     * YWSL_Social_Login_Widget
     *
     * @since 1.0.0
     */
    class YWSL_Social_Login_Widget extends WP_Widget {


        /**
         * constructor
         *
         * @access public
         */
        function __construct() {

            /* Widget variable settings. */
            $this->woo_widget_cssclass = 'woocommerce widget_ywsl_social_login';
            $this->woo_widget_description = __( 'Show social login', 'yith-woocommerce-social-login' );
            $this->woo_widget_idbase = 'yith_ywsl_social_login';
            $this->woo_widget_name = __( 'YITH WooCommerce Social Login', 'yith-woocommerce-social-login' );


            /* Widget settings. */
            $widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

            /* Create the widget. */
            parent::__construct('yith_ywsl_social_login', $this->woo_widget_name, $widget_ops);

        }


        /**
         * widget function.
         *
         * @see WP_Widget
         * @access public
         * @param array $args
         * @param array $instance
         * @return void
         */
        function widget( $args, $instance ) {

            extract($args);

            $this->istance = $instance;
            $title = isset( $instance['title'] ) ? $instance['title'] : '';
            $description = isset( $instance['description'] ) ? $instance['description'] : '';
            $redirect_to= isset( $instance['redirect_to'] ) ? $instance['redirect_to'] : '';
            $title = apply_filters('widget_title', $title, $instance, $this->id_base);

            if( is_user_logged_in() ){
                return;
            }

            echo $before_widget;

            if ($title) echo $before_title . $title . $after_title;

            echo do_shortcode('[yith_wc_social_login label="'.$description .'" redirect_to="'.$redirect_to.'"]');

            echo $after_widget;
        }

        /**
         * update function.
         *
         * @see WP_Widget->update
         * @access public
         * @param array $new_instance
         * @param array $old_instance
         * @return array
         */
        function update( $new_instance, $old_instance ) {
            $instance['title']       = strip_tags( stripslashes( $new_instance['title'] ) );
            $instance['description'] = strip_tags( stripslashes( $new_instance['description'] ) );
            $instance['redirect_to'] = $new_instance['redirect_to'];

            $this->istance = $istance;
            return $instance;
        }

        /**
         * form function.
         *
         * @see WP_Widget->form
         * @access public
         * @param array $instance
         * @return void
         */
        function form( $instance ) {
            $defaults = array(
                'title'           => __( 'Social Login', 'yith-woocommerce-social-login' ),
                'description'  => __( 'Login width:', 'yith-woocommerce-social-login' ),
                'redirect_to'  => '',
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'yith-woocommerce-social-login' ) ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'yith-woocommerce-social-login' ) ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('description') ); ?>" name="<?php echo esc_attr( $this->get_field_name('description') ); ?>" value="<?php if (isset ( $instance['description'])) {echo esc_attr( $instance['description'] );} ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('redirect_to'); ?>"><?php _e('Redirect To (optional):', 'yith-woocommerce-social-login' ) ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('redirect_to') ); ?>" name="<?php echo esc_attr( $this->get_field_name('redirect_to') ); ?>" value="<?php if (isset ( $instance['redirect_to'])) {echo esc_attr( $instance['redirect_to'] );} ?>" />
            </p>

        <?php
        }


    }
}