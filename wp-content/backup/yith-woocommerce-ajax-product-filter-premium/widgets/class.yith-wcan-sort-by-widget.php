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

if ( ! class_exists( 'YITH_WCAN_Sort_By_Widget' ) ) {
    /**
     * YITH_WCAN_Sort_By_Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Sort_By_Widget extends WP_Widget {

        protected $_id_base = 'yith-woo-ajax-navigation-sort-by';

        public function __construct() {
            $classname = 'yith-woocommerce-ajax-product-filter yith-wcan-sort-by';
            $classname .= 'checkboxes' == yith_wcan_get_option( 'yith_wcan_ajax_shop_filter_style', 'standard' ) ? ' with-checkbox' : '';
            $widget_ops  = array( 'classname' => $classname, 'description' => __( 'Choose how to sort WooCommerce products', 'yith-woocommerce-ajax-navigation' ) );
            $control_ops = array( 'width' => 400, 'height' => 350 );
            parent::__construct( $this->_id_base, __( 'YITH Ajax Sort By', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

            if ( ! is_admin() ) {
                $sidebars_widgets = wp_get_sidebars_widgets();
                $regex            = '/^' . $this->_id_base . '-\d+/';

                if( isset( $sidebars_widgets['wp_inactive_widgets'] ) ){
                    unset( $sidebars_widgets['wp_inactive_widgets'] );
                }

                foreach ( $sidebars_widgets as $sidebar => $widgets ) {
                    if ( is_array( $widgets ) ) {
                        foreach ( $widgets as $widget ) {
                            if ( preg_match( $regex, $widget ) ) {
                                $this->actions();
                                break;
                            }
                        }
                    }
                }
            }
        }

        public function actions(){
            /* === Hooks and Actions === */
            add_filter( 'wc_get_template', array( $this, 'sort_by_template' ), 10, 5 );
            add_filter( 'woocommerce_layered_nav_link', array( $this, 'sortby_filter_args' ) );
            remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

            /* === Dropdown === */
            add_filter( "yith_widget_title_sort_by", array( $this, 'widget_title' ), 10, 3 );

            /* === YITHEMES Themes Support === */

            // FW 2.0
            remove_action( 'shop-page-meta', 'yit_wc_catalog_ordering' );
            remove_action( 'shop-page-meta', 'yit_wc_catalog_ordering', 15 );
            // FW 1.0
            remove_action( 'shop_page_meta', 'yit_woocommerce_catalog_ordering' );
            // Old FW
            remove_action( 'woocommerce_before_main_content' , 'yiw_woocommerce_ordering' );

	        /* === 3rd-party Themes Support === */

	        //Adrenalin
	        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 20 );
	        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
	        remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );

	        //Themify
	        remove_action( 'woocommerce_before_shop_loop', 'themify_catalog_ordering', 8 );
        }

        public function widget( $args, $instance ) {
            global $wp_query;
            extract( $instance );
            extract( $args );

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if( ! yith_wcan_can_be_displayed() ){
                return;
            }

            if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
                return;
            }

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
                return;
            }

            if ( empty( $wp_query->found_posts ) ) {
                return;
            }

            echo $before_widget;

            $title = apply_filters( 'widget_title', $title );

            if ( $title ) {
                echo $before_title . apply_filters( 'yith_widget_title_sort_by', $title, $instance, $this->id_base ) . $after_title;
            }

            woocommerce_catalog_ordering();

            echo $after_widget;

        }


        public function form( $instance ) {
            $defaults = array(
                'title'             => _x( 'Sort by', 'Product sorting', 'yith-woocommerce-ajax-navigation' ),
                'dropdown'          => 0,
                'dropdown_type'     => 'open'
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>

            <p>
                <label>
                    <strong><?php _e( 'Title', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-<?php echo $this->number ?>" class="yith-wcan-dropdown">
                <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true )?> class="yith-wcan-dropdown-check widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo $instance['dropdown_type'] ?>" style="display: <?php echo ! empty( $instance['dropdown'] ) ? 'block' : 'none'?>;">
                <label for="<?php echo $this->get_field_id( 'dropdown_type' ); ?>"><strong><?php _ex( 'Dropdown style:', 'Select this if you want to show the widget as open or closed', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
                    <option value="open" <?php selected( 'open', $instance['dropdown_type'] ) ?>> <?php _e( 'Opened', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="close"  <?php selected( 'close', $instance['dropdown_type'] ) ?>>  <?php _e( 'Closed', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                </select>
            </p>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('change', '.yith-wcan-dropdown-check', function () {
                        jQuery.select_dropdown(jQuery(this));
                    });
                });
            </script>
        <?php
        }

        public function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title']          = strip_tags( $new_instance['title'] );
            $instance['dropdown']       = isset( $new_instance['dropdown'] ) ? 1 : 0;
            $instance['dropdown_type']  = $new_instance['dropdown_type'];
            return $instance;
        }

        public function sort_by_template( $located, $template_name, $args, $template_path, $default_path ){

            if( 'loop/orderby.php' == $template_name ){
                $default_path   = apply_filters( 'yith_wcan_sort_by_default_path', YITH_WCAN_DIR . 'templates/woocommerce/loop/' );
                $template_path  = apply_filters( 'yith_wcan_sort_by_template_path', '' );
                $located        = wc_locate_template( 'sortby.php', $template_path, $default_path );
            }

            return $located;
        }

        public function sortby_filter_args( $link ){
            if( isset( $_GET['orderby'] ) ){
                $link = add_query_arg( array( 'orderby' => $_GET['orderby'] ), $link );
            }

            return $link;
        }

        public function widget_title( $title, $instance, $id_base ) {
            $span_class = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
            $dropdown_type = apply_filters( 'yith_wcan_dropdown_type', $instance['dropdown_type'], $instance );
            $title = ! empty( $instance['dropdown'] ) ? $title . '<span class="' . $span_class .'" data-toggle="' . $dropdown_type . '"></span>' : $title;

            return $title;
        }

    }
}
