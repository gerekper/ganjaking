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

if ( ! class_exists( 'YITH_WCAN_List_Price_Filter_Widget' ) ) {
    /**
     * YITH_WCAN_Sort_By_Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_List_Price_Filter_Widget extends WP_Widget {

        protected $_id_base = 'yith-woo-ajax-navigation-list-price-filter';

        public function __construct() {
            $classname = 'yith-wcan-list-price-filter yith-woocommerce-ajax-product-filter';
            $classname .= 'checkboxes' == yith_wcan_get_option( 'yith_wcan_ajax_shop_filter_style', 'standard' ) ? ' with-checkbox' : '';
            $widget_ops  = array( 'classname' => $classname, 'description' => __( 'Show a price filter widget with a list of preset price ranges that users can use to better narrow down the products', 'yith-woocommerce-ajax-navigation' ) );
            $control_ops = array( 'width' => 400, 'height' => 350 );
            parent::__construct( $this->_id_base, __( 'YITH Ajax Price List Filter', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

            if ( ! is_admin() ) {
                $sidebars_widgets = wp_get_sidebars_widgets();
                $regex            = '/^' . $this->_id_base . '-\d+/';
                $found            = false;

                foreach ( $sidebars_widgets as $sidebar => $widgets ) {
                    if ( is_array( $widgets ) ) {
                        foreach ( $widgets as $widget ) {
                            if ( preg_match( $regex, $widget ) ) {
                                $this->actions();
                                $found = true;
                            }

                            if( $found ){
                                break;
                            }
                        }
                    }

                    if( $found ){
		                break;
	                }
                }
            }
        }

        public function actions(){
            /* === Hooks and Actions === */
            add_filter( 'woocommerce_layered_nav_link', array( $this, 'price_filter_args' ) );
            ! is_active_widget( false, false, 'woocommerce_price_filter', true ) && ! is_admin() && add_filter( 'loop_shop_post_in', array( $this, 'price_filter' ) );

            /* === Dropdown === */
            add_filter( "yith_widget_title_list_price_filter", array( $this, 'widget_title' ), 10, 3 );

            /* === Yithemes Themes Support === */
            remove_action( 'shop-page-meta', 'yit_wc_catalog_ordering', 15 );
        }

        public function widget( $args, $instance ) {
            global $wp_query;

            if( ! yith_wcan_can_be_displayed() ){
                return;
            }

            if( apply_filters( 'yith_wcan_is_search', is_search() ) ){
                return;
            }

            extract( $instance );
            extract( $args );

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
                return;
            }

            echo $before_widget;

            $title = apply_filters( 'widget_title', $title );

            if ( $title ) {
                echo $before_title . apply_filters( 'yith_widget_title_list_price_filter', $title, $instance, $this->id_base ) . $after_title;
            }

	        if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
		        $aelia_obj       = $GLOBALS[ WC_Aelia_CurrencySwitcher::$plugin_slug ];
		        $base_currency   = is_callable( array( $aelia_obj, 'base_currency' ) ) ? $aelia_obj->base_currency() : get_woocommerce_currency();
		        $current_currency = is_callable( array( $aelia_obj, 'get_selected_currency' ) ) ? $aelia_obj->get_selected_currency() : get_woocommerce_currency();

		        if ( $base_currency != $current_currency && ! empty( $instance['prices'] ) ) {
			        foreach ( $instance['prices'] as & $price ) {
				        $price['min'] = apply_filters( 'wc_aelia_cs_convert', $price['min'], $base_currency, $current_currency );
				        $price['max'] = apply_filters( 'wc_aelia_cs_convert', $price['min'], $base_currency, $current_currency );
			        }
		        }
	        }

            $args = array(
                'prices'         => $instance['prices'],
                'shop_page_uri'  => yit_get_woocommerce_layered_nav_link(),
		        'instance'      => $instance,
		        'rel_nofollow'  => yith_wcan_add_rel_nofollow_to_url( true )
            );
            
            $template_path = apply_filters( 'yith_wcan_list-price_template_path', WC()->template_path() . 'loop' );
            $default_path  = apply_filters( 'yith_wcan_list_price_default_path', YITH_WCAN_DIR . 'templates/woocommerce/loop/' );

            wc_get_template( 'list-price-filter.php', $args, $template_path, $default_path );

            echo $after_widget;

        }


        public function form( $instance ) {
            global $wpdb;

            $is_ajax = defined('DOING_AJAX') && DOING_AJAX ;

	        $min = floor( $wpdb->get_var(
		        'SELECT min(meta_value + 0)
				FROM ' . $wpdb->posts . ' as p
				LEFT JOIN ' . $wpdb->postmeta . ' as pm ON p.ID = pm.post_id
				WHERE meta_key IN ("' . implode( '","', apply_filters( 'woocommerce_price_filter_meta_keys', array(
			        '_price',
			        '_min_variation_price'
		        ) ) ) . '") '
	        ) );

	        $max = ceil( $wpdb->get_var(
		        'SELECT max(meta_value + 0)
					FROM ' . $wpdb->posts . ' as p
				LEFT JOIN ' . $wpdb->postmeta . ' as pm ON p.ID = pm.post_id
					WHERE meta_key IN ("' . implode( '","', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) . '")'
	        ) );

            $defaults = array(
                'title'             => _x( 'Price Filter', 'refer to: product price', 'yith-woocommerce-ajax-navigation' ),
                'dropdown'          => 0,
                'dropdown_type'     => 'open',
                'prices'            => array(
                    array(
                        'min' => $min,
                        'max' => $max
                    )
                ),
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>

            <p>
                <label>
                    <strong><?php _e( 'Title', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-<?php echo $instance['dropdown_type'] ?>" class="yith-wcan-dropdown">
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

            <p class="yith-wcan-price-filter">
                <label>
                    <?php _e( 'Price Range', 'yith-woocommerce-ajax-navigation' ) ?>:
                </label>
                <span class="range-filter" data-field_name="<?php echo $this->get_field_name( 'prices' ); ?>">
                    <?php $i = 0; ?>
                    <?php if( is_array( $instance['prices'] ) ) : ?>
                        <?php foreach ( $instance['prices'] as $price ) : ?>
                            <input type="text" name="<?php echo $this->get_field_name( 'prices' ); ?>[<?php echo $i; ?>][min]" value="<?php echo $price['min'] ?>" class="yith-wcan-price-filter-input widefat" data-position="<?php echo $i; ?>" />
                            <input type="text" name="<?php echo $this->get_field_name( 'prices' ); ?>[<?php echo $i; ?>][max]" value="<?php echo $price['max'] ?>" class="yith-wcan-price-filter-input widefat" data-position="<?php echo $i; ?>"/>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </span>
            </p>

            <div class="yith-add-new-range-button">
                <input type="button" class="yith-wcan-price-filter-add-range button button-primary" value="<?php _e( 'Add new range', 'yith-woocommerce-ajax-navigation' ) ?>">
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.yith-wcan-price-filter-add-range').off('click').on('click', function (e) {
                        e.preventDefault();
                        var t = jQuery(this);
                        jQuery.add_new_range(t);
                    });

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
            $instance['prices']         = isset( $new_instance['prices'] ) ? $this->remove_empty_price_range( $new_instance['prices'] ) : array();
            return $instance;
        }

        public function price_filter_args( $link ) {

            if ( isset( $_GET['orderby'] ) ) {
                $link = add_query_arg( array( 'orderby' => $_GET['orderby'] ), $link );
            }

            return $link;
        }

        public function widget_title( $title, $instance, $id_base ) {
            $span_class = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
            $dropdown_type = apply_filters( 'yith_wcan_dropdown_type', $instance['dropdown_type'], $instance );
            $title = ! empty( $dropdown_type ) ? $title . '<span class="' . $span_class .'" data-toggle="' . $dropdown_type . '"></span>' : $title;

            return $title;
            }

        public function remove_empty_price_range( $prices ){
            foreach( $prices as $k => $price ){
                if( $price['min'] == '' && $price['max'] == ''  ){
                    unset( $prices[ $k ] );
                }
            }

            return $prices;
        }

        public function price_filter( $filtered_posts = array() ) {
            global $wpdb;
            $in_array_function = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';

            if ( isset( $_GET['max_price'] ) || isset( $_GET['min_price'] ) ) {

                $matched_products = array();
                $min              = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
                $max              = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : 9999999999;

                $matched_products_query = apply_filters( 'woocommerce_price_filter_results', $wpdb->get_results( $wpdb->prepare( '
                    SELECT DISTINCT ID, post_parent, post_type FROM ' . $wpdb->posts .' as p
                    INNER JOIN ' . $wpdb->postmeta .' as pm ON p.ID = pm.post_id
                    WHERE post_type IN ( "product", "product_variation" )
                    AND post_status = "publish"
                    AND meta_key IN ("' . implode( '","', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) . '")
                    AND meta_value BETWEEN %f AND %f
                ', $min, $max ), OBJECT_K ), $min, $max );

                if ( $matched_products_query ) {
                    foreach ( $matched_products_query as $product ) {
                        if ( $product->post_type == 'product' ) {
                            $matched_products[] = $product->ID;
                        }
                        if ( $product->post_parent > 0 && ! $in_array_function( $product->post_parent, $matched_products ) ) {
                            $matched_products[] = $product->post_parent;
                        }
                    }
                }

                // Filter the id's
                if ( 0 === sizeof( $filtered_posts ) ) {
                    $filtered_posts = $matched_products;
                }
                else {
                    $filtered_posts = array_intersect( $filtered_posts, $matched_products );

                }
                $filtered_posts[] = 0;
            }

            return (array) $filtered_posts;
        }

    }
}