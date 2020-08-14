<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Woocommerce_Store_Location_Widget' ) ) {
    /**
     * YITH_Woocommerce_Vendors_Widget
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @since  1.0.0
     */
    class YITH_Vendor_Store_Location_Widget extends WP_Widget {

        /**
         * Construct
         */
        function __construct() {
            $id_base        = 'yith-vendor-store-location';
            $name           = __( 'YITH Vendor Store Location', 'yith-woocommerce-product-vendors' );
            $widget_options = array(
                'description' => __( 'Display the vendor\'s store location in Google Maps', 'yith-woocommerce-product-vendors' )
            );

            parent::__construct( $id_base, $name, $widget_options );
        }

        /**
         * Echo the widget content.
         *
         * Subclasses should over-ride this function to generate their widget code.
         *
         * @param array $args     Display arguments including before_title, after_title,
         *                        before_widget, and after_widget.
         * @param array $instance The settings for the particular instance of the widget.
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function widget( $args, $instance ) {
            $vendor = yith_get_vendor( get_query_var( 'term' ) );
            if( ! empty( YITH_Vendors()->frontend ) && YITH_Vendors()->frontend->is_vendor_page() && ! empty( $vendor->location ) ){
	            $args = array(
		            'instance'        => $instance,
		            'vendor'          => $vendor,
		            'gmaps_link'      => esc_url( add_query_arg( array( 'q' => urlencode( $vendor->location ) ), '//maps.google.com/' ) ),
		            'show_gmaps_link' => 'yes' == get_option( 'yith_wpv_frontpage_show_gmaps_link', 'yes' )
	            );
                yith_wcpv_get_template( 'store-location', $args, 'widgets' );
            }
        }

        /**
         * Output the settings update form.
         *
         * @param array $instance Current settings.
         *
         * @return string Default return is 'noform'.
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */

        public function form( $instance ) {
            $defaults = array(
                'title' => __( 'Store Location', 'yith-woocommerce-product-vendors' ),
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'yith-woocommerce-product-vendors' ) ?>:
                    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
                </label>
            </p>

            <p>
                <?php printf( '%s <a href="%s">%s</a>. %s <a href="%s" target="_blank">%s</a>',
                    __( 'If you have an API KEY for Google Maps, you can add it', 'yith-woocommerce-product-vendors' ),
                    esc_url( add_query_arg( array( 'page' => 'yith_wpv_panel', 'tab' => 'frontpage' ),admin_url( 'admin.php' ) ) ),
                    _x( 'here', '[admin] placeholder link', 'yith-woocommerce-product-vendors' ),
                    __( 'Don’t know what an API KEY is or how to use it? If you need further information, please click', 'yith-woocommerce-product-vendors' ),
                    esc_url( '//developers.google.com/maps/documentation/javascript/get-api-key' ),
                    _x( 'here', '[admin] placeholder link', 'yith-woocommerce-product-vendors' )
                    ) ?>
            </p>
        <?php
        }

        /**
         * Update a particular instance.
         *
         * This function should check that $new_instance is set correctly. The newly-calculated
         * value of `$instance` should be returned. If false is returned, the instance won't be
         * saved/updated.
         *
         * @param array $new_instance New settings for this instance as input by the user via.
         * @param array $old_instance Old settings for this instance.
         *
         * @return array Settings to save or bool false to cancel saving.
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @see    WP_Widget::form()
         */
        public function update( $new_instance, $old_instance ) {
            $instance          = $old_instance;
            $instance['title'] = strip_tags( $new_instance['title'] );
            return $instance;
        }
    }
}
