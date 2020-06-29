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

if ( ! class_exists( 'YITH_WCAN_Navigation_Widget' ) ) {

        /**
         * YITH WooCommerce Ajax Navigation Widget
         *
         * @since 1.0.0
         */
    class YITH_WCAN_Navigation_Widget extends WP_Widget {

        /**
         * YITH Brands Taxonomy Name
         */
        public $brand_taxonomy = '';

        /**
         * Use to print or not widget
         */
        public $found = false;

        function __construct() {
            $classname = 'yith-woocommerce-ajax-product-filter yith-woo-ajax-navigation woocommerce widget_layered_nav';
            $classname .= defined( 'YITH_WCAN_PREMIUM' ) && 'checkboxes' == yith_wcan_get_option( 'yith_wcan_ajax_shop_filter_style', 'standard' ) ? ' with-checkbox' : '';
            $widget_ops  = array( 'classname' => $classname, 'description' => __( 'Filter the list of products without reloading the page', 'yith-woocommerce-ajax-navigation' ) );
            $control_ops = array( 'width' => 400, 'height' => 350 );
            add_action('wp_ajax_yith_wcan_select_type', array( $this, 'ajax_print_terms') );
            parent::__construct( 'yith-woo-ajax-navigation', _x( 'YITH Ajax Product Filter', '[Plugin Name] Admin: Widget Title', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );
        }


        function widget( $args, $instance ) {
	        global $wc_product_attributes;

            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
            $queried_object     = get_queried_object();

            extract( $args );
            $_attributes_array = yit_wcan_get_product_taxonomy();

            if( apply_filters( 'yith_wcan_is_search', is_search() ) ){
                return;
            }

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
                return;
            }

            if( yith_wcan_brands_enabled() ) {
                $this->brand_taxonomy = YITH_WCBR::$brands_taxonomy;
            }

            $filter_term_field  = YITH_WCAN()->filter_term_field;
            $current_term       = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->$filter_term_field : '';
            $title              = apply_filters( 'yith_widget_title_ajax_navigation', ( isset( $instance['title'] ) ? $instance['title'] : '' ), $instance, $this->id_base );
            $query_type         = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
            $display_type       = isset( $instance['type'] ) ? $instance['type'] : 'list';
            $is_child_class     = 'yit-wcan-child-terms';
            $is_parent_class    = 'yit-wcan-parent-terms';
            $is_chosen_class    = 'chosen';
            $terms_type_list    = ( isset( $instance['display'] ) ) ? $instance['display'] : 'all';
            $in_array_function  = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
            $instance['id']     = $this->id;

            $instance['attribute']      = empty( $instance['attribute'] ) ? '' : $instance['attribute'];
            $instance['extra_class']    = empty( $instance['extra_class'] ) ? '' : $instance['extra_class'];
	        $rel_nofollow            = yith_wcan_add_rel_nofollow_to_url( true );

            /* FIX TO WOOCOMMERCE 2.1 */
            if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
                $taxonomy = wc_attribute_taxonomy_name( $instance['attribute'] );
            }
            else {
                $taxonomy = WC()->attribute_taxonomy_name( $instance['attribute'] );
            }

            $taxonomy        = apply_filters( 'yith_wcan_get_terms_params', $taxonomy, $instance, 'taxonomy_name' );
            $terms_type_list = apply_filters( 'yith_wcan_get_terms_params', $terms_type_list, $instance, 'terms_type' );

            if ( ! taxonomy_exists( $taxonomy ) ) {
                return;
            }

            $terms = yit_get_terms( $terms_type_list, $taxonomy, $instance );

            if ( count( $terms ) > 0 ) {
                ob_start();

                $this->found = false;

                echo $before_widget;

                $title = html_entity_decode( apply_filters( 'widget_title', $title ) );

                if ( ! empty( $title ) ) {
                    echo  $before_title . $title . $after_title;
                }

                // Force found when option is selected - do not force found on taxonomy attributes
                if ( ! $_attributes_array ||  ! is_tax( $_attributes_array ) ) {
                    if ( is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
                        $this->found = true;
                    }
                }

                if ( in_array( $display_type, apply_filters( 'yith_wcan_display_type_list', array( 'list' ) ) ) ) {

                     $ancestors = yith_wcan_wp_get_terms(
                        array(
                            'taxonomy'      => $taxonomy,
                            'parent'        => 0,
                            'hierarchical'  => true,
                            'hide_empty'    => false,
                        )
                    );

                    if( ! empty( $ancestors ) && ! is_wp_error( $ancestors ) ){

                        if( 'product' == yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' )  ){
                            usort( $ancestors, 'yit_terms_sort' );
                        }

                        elseif( 'alphabetical' == yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ){
                            usort( $ancestors, 'yit_alphabetical_terms_sort' );
                        }

                        foreach( $ancestors as $ancestor ){
                            $tree[ $ancestor->term_id ] = 'parent' == $terms_type_list ? array() : yit_reorder_hierachical_categories( $ancestor->term_id, $taxonomy );
                        }
                    }

                    do_action( 'yith_wcan_before_print_list', $taxonomy );

                    $this->add_reset_taxonomy_link( $taxonomy, $instance );

                    // List display
                    echo "<ul class='yith-wcan-list yith-wcan {$instance['extra_class']}'>";

                    $this->get_list_html( $tree, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, 0, $filter_term_field, $rel_nofollow );

                    echo "</ul>";
                }
                elseif ( in_array( $display_type, apply_filters( 'yith_wcan_display_type_select', array( 'select' ) ) ) ) {
                    $dropdown_label = apply_filters( 'yith_wcan_dropdown_label', __( 'Filters:', 'yith-woocommerce-ajax-navigation' ), $this, $instance, $instance['attribute'] );
                    ?>

                    <a class="yit-wcan-select-open" href="#"><?php echo apply_filters( 'yith_wcan_dropdown_default_label', $dropdown_label, $this ) ?></a>

                    <?php
                    // Select display
                    echo "<div class='yith-wcan-select-wrapper'>";

                    echo "<ul class='yith-wcan-select yith-wcan {$instance['extra_class']}'>";

                    foreach ( $terms as $term ) {
	                    $this->found = false;
                        // Get count based on current view - uses transients
                        //$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                        //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        //set_transient( $transient_name, $_products_in_term );
                        //}

                        $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) );

                        // If this is an AND query, only show options with count > 0
                        if ( $query_type == 'and' ) {

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

                            if ( ( $terms_type_list != 'hierarchical' || ! yit_term_has_child( $term, $taxonomy ) ) && $count == 0 && ! $option_is_set ) {
                                continue;
                            }

                            // If this is an OR query, show all options so search can be expanded
                        }
                        else {

                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

	                        if ( apply_filters( 'yith_wcan_skip_no_products_dropdown', $count == 0 ) ) {
		                        continue;
	                        }
                        }

	                    $arg = apply_filters('yith_wcan_select_type_query_arg', 'filter_' . sanitize_title($instance['attribute']), $display_type, $term);

	                    $current_filter = (isset($_GET[$arg])) ? explode(apply_filters('yith_wcan_select_filter_operator', ',', $display_type), apply_filters("yith_wcan_select_filter_query_{$arg}", $_GET[$arg] ) ) : array();
                        if ( ! is_array( $current_filter ) ) {
                            $current_filter = array();
                        }

                        $current_filter = array_map( 'esc_attr', $current_filter );

                        if ( ! $in_array_function( $term->$filter_term_field, $current_filter ) ) {
                            $current_filter[] = $term->$filter_term_field;
                        }

                        $link = yit_get_woocommerce_layered_nav_link();

                        // All current filters
                        if ( $_chosen_attributes ) {
                            foreach ( $_chosen_attributes as $name => $data ) {
                                if ( $name !== $taxonomy ) {

                                    // Exclude query arg for current term archive term
                                    while ( $in_array_function( $term->slug, $data['terms'] ) ) {
                                        $key = array_search( $current_term, $data );
                                        unset( $data['terms'][$key] );
                                    }

                                    // Remove pa_ and sanitize
                                    $filter_name = urldecode( sanitize_title( str_replace( 'pa_', '', $name ) ) );

                                    if ( ! empty( $data['terms'] ) ) {
                                        $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                                    }

                                    if ( $data['query_type'] == 'or' ) {
                                        $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                    }
                                }
                            }
                        }

                        // Min/Max
                        if ( isset( $_GET['min_price'] ) ) {
                            $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                        }

                        if ( isset( $_GET['max_price'] ) ) {
                            $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                        }

                        if ( isset( $_GET['product_tag'] ) ) {
                            $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                        }

                        elseif( is_product_tag() && $queried_object ){
                            $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
                        }

                        if ( isset( $_GET[ $this->brand_taxonomy ] ) ) {
                            $brands = get_term_by( 'slug', $_GET[ $this->brand_taxonomy ], $this->brand_taxonomy );
                            if( $brands instanceof WP_Term && $brands->term_id != $term->term_id ){
                                $link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link );
                            }
                        }

                        elseif( is_tax( $this->brand_taxonomy ) && $queried_object ) {
                            $link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
                        }

                        if( isset( $_GET['product_cat'] ) ){
                            $categories_filter_operator = 'and' == $query_type ? '+' : ',';
                            $_chosen_categories = explode( $categories_filter_operator, urlencode( $_GET['product_cat'] ) );
                            $link  = add_query_arg(
                                'product_cat',
                                implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $_chosen_categories ),
                                $link
                            );
                        }

                        elseif( is_product_category() && $queried_object ){
                            //Removed @JoseCostaRos
                            $link = add_query_arg( array( 'product_cat' => $queried_object->slug ), $link );
                        }

                        if( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id != $queried_object->term_id ){

                            $link = add_query_arg(
                                array(
                                    'source_id'                 => $queried_object->term_id,
                                    'source_tax'                => $queried_object->taxonomy,
                                    $queried_object->taxonomy   => $queried_object->slug
                                ), $link );
                        }

                        if ( isset( $_GET['source_id'] ) && isset( $_GET['source_tax'] ) ) {
                            $add_source_id = true;
                            if( property_exists( $term, 'term_id' ) && property_exists( $queried_object, 'term_id' ) && $term->term_id == $queried_object->term_id ){
                                if( ! yit_is_filtered_uri() ){
                                    $add_source_id = false;
                                }
                            }

                            if( $add_source_id ) {
                                $args = array( 'source_id' => $_GET['source_id'], 'source_tax' => $_GET['source_tax'] );
                                if( property_exists( $queried_object, 'taxonomy' ) && isset( $_GET[ $queried_object->taxonomy ] ) ){
                                    $args[ $queried_object->taxonomy ] = $_GET[ $queried_object->taxonomy ];
                                }
                                $link = add_query_arg( $args, $link );
                            }
                        }

                        if( isset( $_GET['yith_shop_vendor'] ) ){
                            $link = add_query_arg( array( 'yith_shop_vendor' => $_GET['yith_shop_vendor'] ), $link );
                        }

                        // Current Filter = this widget
	                    $term_param = apply_filters('yith_wcan_term_param_uri', $term->$filter_term_field, $display_type, $term);
	                    $check_for_current_widget = isset( $_chosen_attributes[$taxonomy] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] );
	                    $check_for_current_widget =  apply_filters('yith_wcan_select_type_current_widget_check', $check_for_current_widget, $current_filter, $display_type, $term_param );
                        if ( $check_for_current_widget ) {

                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                            // Remove this term is $current_filter has more than 1 term filtered
                            if ( sizeof( $current_filter ) > 1 ) {
                                $current_filter_without_this = array_diff( $current_filter, array( $term->$filter_term_field ) );
                                $link                        = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
                            }

                        }
                        else {
                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                            $link  = add_query_arg( $arg, implode( ',', $current_filter ), $link );
                        }

                        // Search Arg
                        if ( get_search_query() ) {
	                        $link = add_query_arg( 's', urlencode( get_search_query() ), $link );
                        }

                        // Post Type Arg
                        if ( isset( $_GET['post_type'] ) ) {
                            $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
                        }

                        // Query type Arg
                        if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                            $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                        }

                        $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

						$show_count = $count != 0 && ! empty( $instance['show_count'] ) && ! $instance['show_count'];

						$show_count = apply_filters( "{$this->id}-show_product_count", $show_count, $instance );

                        echo '<li ' . $class . '>';

                        echo ( $this->found || $option_is_set ) ? '<a ' . $rel_nofollow . ' data-type="select" href="' . $link . '">' : '<span>';

                        echo $term->name;

						if ( $this->found && apply_filters( 'yith_wcan_force_show_count', $show_count ) ) {
							echo ' <small class="count">' . $count . '</small><div class="clear"></div>';
						}

                        echo ( $this->found || $option_is_set ) ? '</a>' : '</span>';

                        echo '</li>';

                    }

                    echo "</ul>";

                    echo "</div>";
                }
                elseif ( $display_type == 'color' ) {
                    // List display
                    echo "<ul class='yith-wcan-color yith-wcan yith-wcan-group {$instance['extra_class']}'>";

                    foreach ( $terms as $term ) {

                        // Get count based on current view - uses transients
                        $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                        //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

						$_products_in_term = get_objects_in_term( apply_filters( 'yith_wcan_color_get_objects_in_term', $term->term_id, $taxonomy, $term ), $taxonomy );
                        //set_transient( $transient_name, $_products_in_term );
                        //}

                        $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) );

                        // If this is an AND query, only show options with count > 0
                        if ( $query_type == 'and' ) {

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );
                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

                            if ( apply_filters( 'yith_wcan_skip_no_products_color', $count == 0 && ! $option_is_set ) ) {
                                continue;
                            }

                            // If this is an OR query, show all options so search can be expanded
                        }
                        else {

                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

	                        if ( apply_filters( 'yith_wcan_skip_no_products_color', $count == 0 ) ){
		                        continue;
	                        }
                        }

                        $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                        $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                        if ( ! is_array( $current_filter ) ) {
                            $current_filter = array();
                        }

                        $current_filter = array_map( 'esc_attr', $current_filter );

                        if ( property_exists( $term, $filter_term_field ) && ! $in_array_function( $term->$filter_term_field, $current_filter ) ) {
                            $current_filter[] = $term->$filter_term_field;
                        }

                        $link = yit_get_woocommerce_layered_nav_link();

                        // All current filters
                        if ( $_chosen_attributes ) {
                            foreach ( $_chosen_attributes as $name => $data ) {
                                if ( $name !== $taxonomy ) {

                                    // Exclude query arg for current term archive term
                                    while ( $in_array_function( $term->slug, $data['terms'] ) ) {
                                        $key = array_search( $current_term, $data );
                                        unset( $data['terms'][$key] );
                                    }

                                    // Remove pa_ and sanitize
                                    $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                    if ( ! empty( $data['terms'] ) ) {
                                        $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                                    }

                                    if ( $data['query_type'] == 'or' ) {
                                        $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                    }
                                }
                            }
                        }

                        // Min/Max
                        if ( isset( $_GET['min_price'] ) ) {
                            $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                        }

                        if ( isset( $_GET['max_price'] ) ) {
                            $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                        }

                        if ( isset( $_GET['product_tag'] ) ) {
                            $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                        }

                        elseif( is_product_tag() && $queried_object ){
                            $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
                        }

                        if ( isset( $_GET[ $this->brand_taxonomy ] ) ) {
                            $brands = get_term_by( 'slug', $_GET[ $this->brand_taxonomy ], $this->brand_taxonomy );
                            if( $brands instanceof WP_Term && $brands->term_id != $term->term_id ){
                                $link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link );
                            }
                        }

                        elseif( is_tax( $this->brand_taxonomy ) && $queried_object ) {
                            $link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
                        }

                        if( isset( $_GET['product_cat'] ) ){
                            $categories_filter_operator = 'and' == $query_type ? '+' : ',';
                            $_chosen_categories = explode( $categories_filter_operator, urlencode( $_GET['product_cat'] ) );
                            $link  = add_query_arg(
                                'product_cat',
                                implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $_chosen_categories ),
                                $link
                            );
                        }

                        elseif( is_product_category() && $queried_object ){
                            //Removed @JoseCostaRos
                            $link = add_query_arg( array( 'product_cat' => $queried_object->slug ), $link );
                        }

                        if( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id != $queried_object->term_id ){
                            $link = add_query_arg(
                                array(
                                    'source_id'                 => $queried_object->term_id,
                                    'source_tax'                => $queried_object->taxonomy,
                                    $queried_object->taxonomy   => $queried_object->slug
                                ), $link );
                        }

                        if ( isset( $_GET['source_id'] ) && isset( $_GET['source_tax'] ) ) {
                            $add_source_id = true;
                            if( property_exists( $term, 'term_id' ) && property_exists( $queried_object, 'term_id' ) && $term->term_id == $queried_object->term_id ){
                                if( ! yit_is_filtered_uri() ){
                                    $add_source_id = false;
                                }
                            }

                            if( $add_source_id ) {
                                $args = array( 'source_id' => $_GET['source_id'], 'source_tax' => $_GET['source_tax'] );
                                $link = add_query_arg( $args, $link );
                            }
                        }

                        if( isset( $_GET['yith_shop_vendor'] ) ){
                            $link = add_query_arg( array( 'yith_shop_vendor' => $_GET['yith_shop_vendor'] ), $link );
                        }

                        // Current Filter = this widget
                        if ( isset( $_chosen_attributes[$taxonomy] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) ) {

                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                            // Remove this term is $current_filter has more than 1 term filtered
                            if ( sizeof( $current_filter ) > 1 ) {
                                $current_filter_without_this = array_diff( $current_filter, array( $term->$filter_term_field ) );
                                $link                        = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
                            }
                        }
                        else {
                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                            $link  = add_query_arg( $arg, implode( ',', $current_filter ), $link );
                        }

                        // Search Arg
                        if ( get_search_query() ) {
                            $link = add_query_arg( 's', urlencode( get_search_query() ), $link );
                        }

                        // Post Type Arg
                        if ( isset( $_GET['post_type'] ) ) {
                            $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
                        }

                        // Query type Arg
                        if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                            $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                        }

                        $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );
                        $term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

	                    $color = '';

	                    if( ! empty( $instance['colors'][$term_id] ) ){
		                    $color = $instance['colors'][$term_id];
	                    }

	                    elseif( function_exists( 'ywccl_get_term_meta' ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'colorpicker' == $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
		                    $colors = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );
		                    if( ! empty( $colors ) ){
		                        $colors = explode( ',', $colors );
		                        $color = $colors[0];
                            }
	                    }

                        if ( $color ) {
                            $li_style = apply_filters( "{$this->id}-li_style", 'background-color:' . $color . ';', $instance );

                            echo '<li ' . $class . '>';

                            echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' style="' . $li_style . '" href="' . $link . '" title="' . $term->name . '" >' : '<span class="yith-wcan-color-not-available" style="' . $li_style . ';" >';

                            echo $term->name;

                            echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                        }
                    }

                    echo "</ul>";

                }
                elseif ( $display_type == 'label' ) {
                    // List display
                    echo "<ul class='yith-wcan-label yith-wcan yith-wcan-group {$instance['extra_class']}'>";

                    foreach ( $terms as $term ) {

	                    // Get count based on current view - uses transients
//                        $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                        //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        //set_transient( $transient_name, $_products_in_term );
                        //}

                        $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) );

                        // If this is an AND query, only show options with count > 0
                        if ( $query_type == 'and' ) {

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

                            if ( $count == 0 && ! $option_is_set ) {
                                continue;
                            }

                            // If this is an OR query, show all options so search can be expanded
                        }
                        else {

                            // skip the term for the current archive
//                            if ( $current_term == $term->$filter_term_field ) {
//                                continue;
//                            }

                            $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

                            if ( $count > 0 ) {
                                $this->found = true;
                            }

	                        if ( apply_filters( 'yith_wcan_skip_no_products_label', $count == 0 ) ) {
		                        continue;
	                        }
                        }

                        $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                        $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                        if ( ! is_array( $current_filter ) ) {
                            $current_filter = array();
                        }

                        $current_filter = array_map( 'esc_attr', $current_filter );

                        if ( ! $in_array_function( $term->$filter_term_field, $current_filter ) ) {
                            $current_filter[] = $term->$filter_term_field;
                        }

                        $link = yit_get_woocommerce_layered_nav_link();

                        // All current filters
                        if ( $_chosen_attributes ) {
                            foreach ( $_chosen_attributes as $name => $data ) {
                                if ( $name !== $taxonomy ) {

                                    // Exclude query arg for current term archive term
                                    while ( $in_array_function( $term->slug, $data['terms'] ) ) {
                                        if( ! empty( $current_term ) ){
	                                        $key = array_search( $current_term, $data );
	                                        unset( $data['terms'][$key] );
                                        }
                                    }

                                    // Remove pa_ and sanitize
                                    $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                    if ( ! empty( $data['terms'] ) ) {
                                        $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                                    }

                                    if ( $data['query_type'] == 'or' ) {
                                        $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                    }
                                }
                            }
                        }

                        // Min/Max
                        if ( isset( $_GET['min_price'] ) ) {
                            $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                        }

                        if ( isset( $_GET['max_price'] ) ) {
                            $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                        }

                        if ( isset( $_GET['product_tag'] ) ) {
                            $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                        }

                        elseif( is_product_tag() && $queried_object ){
                            $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
                        }

                        if ( isset( $_GET[ $this->brand_taxonomy ] ) ) {
                            $brands = get_term_by( 'slug', $_GET[ $this->brand_taxonomy ], $this->brand_taxonomy );
                            if( $brands instanceof WP_Term && $brands->term_id != $term->term_id ){
                                $link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link );
                            }
                        }

                        elseif( is_tax( $this->brand_taxonomy ) && $queried_object ) {
                            $link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
                        }

                        if( isset( $_GET['product_cat'] ) ){
                            $categories_filter_operator = 'and' == $query_type ? '+' : ',';
                            $_chosen_categories = explode( $categories_filter_operator, urlencode( $_GET['product_cat'] ) );
                            $link  = add_query_arg(
                                'product_cat',
                                implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $_chosen_categories ),
                                $link
                            );
                        }

                        elseif( is_product_category() && $queried_object ){
                            //Removed @JoseCostaRos
                            $link = add_query_arg( array( 'product_cat' => $queried_object->slug ), $link );
                        }

                        if( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id != $queried_object->term_id ){
                            $link = add_query_arg(
                                array(
                                    'source_id'                 => $queried_object->term_id,
                                    'source_tax'                => $queried_object->taxonomy,
                                    $queried_object->taxonomy   => $queried_object->slug
                                ), $link );
                        }

                        if ( isset( $_GET['source_id'] ) && isset( $_GET['source_tax'] ) ) {
                            $add_source_id = true;
                            if( property_exists( $term, 'term_id' ) && property_exists( $queried_object, 'term_id' ) && $term->term_id == $queried_object->term_id ){
                                if( ! yit_is_filtered_uri() ){
                                    $add_source_id = false;
                                }
                            }

                            if( $add_source_id ) {
                                $args = array( 'source_id' => $_GET['source_id'], 'source_tax' => $_GET['source_tax'] );
                                $link = add_query_arg( $args, $link );
                            }
                        }

                        if( isset( $_GET['yith_shop_vendor'] ) ){
                            $link = add_query_arg( array( 'yith_shop_vendor' => $_GET['yith_shop_vendor'] ), $link );
                        }

                        // Current Filter = this widget
                        if ( isset( $_chosen_attributes[$taxonomy] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) ) {

                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                            // Remove this term is $current_filter has more than 1 term filtered
                            if ( sizeof( $current_filter ) > 1 ) {
                                $current_filter_without_this = array_diff( $current_filter, array( $term->$filter_term_field ) );
                                $link                        = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
                            }

                        }
                        else {

                            $class = ( $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                            $link  = add_query_arg( $arg, implode( ',', $current_filter ), $link );

                        }

                        // Search Arg
                        if ( get_search_query() ) {
	                        $link = add_query_arg( 's', urlencode( get_search_query() ), $link );
                        }

                        // Post Type Arg
                        if ( isset( $_GET['post_type'] ) ) {
                            $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
                        }

                        // Query type Arg
                        if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                            $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                        }

                        $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

                        $term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

                        $label = '';

                        if( ! empty( $instance['labels'][$term_id] ) ){
                            $label = $instance['labels'][ $term_id ];
                        }

                        elseif( function_exists( 'ywccl_get_term_meta' ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'label' == $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
	                        $label = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );
                        }

                        if ( $label ) {

                            echo '<li ' . $class . '>';

                            echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' title="' . $term->name . '" href="' . $link . '">' : '<span>';

                            echo $label;

                            echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                        }
                    }
                    echo "</ul>";

                }
                else {
                    do_action( "yith_wcan_widget_display_{$display_type}", $args, $instance, $display_type, $terms, $taxonomy, $filter_term_field );
                }
                // End display type conditional

                echo $after_widget;

                if ( ! $this->found ) {
                    ob_end_clean();
                }
                else {
                    echo ob_get_clean();
                }
            }
        }

        function form( $instance ) {
            $defaults = array(
                'title'         => '',
                'attribute'     => '',
                'query_type'    => 'and',
                'type'          => 'list',
                'colors'        => '',
                'multicolor'    => array(),
                'labels'        => '',
                'display'       => 'all',
                'extra_class'   => ''
            );

            $instance = wp_parse_args( (array) $instance, $defaults );

            $widget_types = apply_filters( 'yith_wcan_widget_types', array(
                    'list'   => __( 'List', 'yith-woocommerce-ajax-navigation' ),
                    'color'  => __( 'Color', 'yith-woocommerce-ajax-navigation' ),
                    'label'  => __( 'Label', 'yith-woocommerce-ajax-navigation' ),
                    'select' => __( 'Dropdown', 'yith-woocommerce-ajax-navigation' )
                )
            );
            ?>

            <p>
                <label>
                    <strong><?php _e( 'Title', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </label>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'type' ); ?>"><strong><?php _e( 'Type:', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith_wcan_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
                    <?php foreach ( $widget_types as $type => $label ) : ?>
                        <option value="<?php echo $type ?>" <?php selected( $type, $instance['type'] ) ?>><?php echo $label ?></option>
                    <?php endforeach; ?>
                </select>
            </p>

            <?php do_action( 'yith_wcan_after_widget_type' );  ?>

            <p>
                <label for="<?php echo $this->get_field_id( 'query_type' ); ?>"><?php _e( 'Query Type:', 'yith-woocommerce-ajax-navigation' ) ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
                    <option value="and" <?php selected( $instance['query_type'], 'and' ); ?>><?php _e( 'AND', 'yith-woocommerce-ajax-navigation' ); ?></option>
                    <option value="or" <?php selected( $instance['query_type'], 'or' ); ?>><?php _e( 'OR', 'yith-woocommerce-ajax-navigation' ); ?></option>
                </select>
            </p>

            <p class="yith-wcan-attribute-list" style="display: <?php echo $instance['type'] == 'tags' || $instance['type'] == 'brands' || $instance['type'] == 'categories' ? 'none' : 'block' ?>;">

                <label for="<?php echo $this->get_field_id( 'attribute' ); ?>"><strong><?php _e( 'Attribute:', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith_wcan_attributes widefat" id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
                    <?php yith_wcan_dropdown_attributes( $instance['attribute'] ); ?>
                </select>
            </p>

            <p id="yit-wcan-display" class="yit-wcan-display-<?php echo $instance['type'] ?>">
                <label for="<?php echo $this->get_field_id( 'display' ); ?>"><strong><?php _e( 'Display (default All):', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith_wcan_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
                    <option value="all"          <?php selected( 'all', $instance['display'] ) ?>>          <?php _e( 'All (no hierarchical)', 'yith-woocommerce-ajax-navigation' ) ?></option>
                    <option value="hierarchical" <?php selected( 'hierarchical', $instance['display'] ) ?>> <?php _e( 'All (hierarchical)', 'yith-woocommerce-ajax-navigation' ) ?>   </option>
                    <option value="parent"       <?php selected( 'parent', $instance['display'] ) ?>>       <?php _e( 'Only Parent', 'yith-woocommerce-ajax-navigation' ) ?>        </option>
                </select>
            </p>

            <?php if( defined( 'YITH_WCAN_PREMIUM' ) ) : ?>
            <p>
                <label>
                    <strong><?php _e( 'CSS custom class', 'yith-woocommerce-ajax-navigation' ) ?>:</strong><br />
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'extra_class' ); ?>" name="<?php echo $this->get_field_name( 'extra_class' ); ?>" value="<?php echo $instance['extra_class']; ?>" />
                </label>
            </p>
            <?php endif; ?>

            <div class="yith_wcan_placeholder">
                <?php
                $values = array();

                if ( $instance['type'] == 'color' ) {
                    $values = $instance['colors'];
                }

                if ( $instance['type'] == 'multicolor' ) {
                    $values = $instance['multicolor'];
                }

                elseif ( $instance['type'] == 'label' ) {
                    $values = $instance['labels'];
                }

                yith_wcan_attributes_table(
                    $instance['type'],
                    $instance['attribute'],
                    'widget-' . $this->id . '-',
                    'widget-' . $this->id_base . '[' . $this->number . ']',
                    $values,
                    $instance['display']
                );
                ?>
            </div>
            <span class="spinner" style="display: none;"></span>

        <input type="hidden" name="widget_id" value="widget-<?php echo $this->id ?>-" />
        <input type="hidden" name="widget_name" value="widget-<?php echo $this->id_base ?>[<?php echo $this->number ?>]" />

            <script>jQuery(document).trigger('yith_colorpicker');</script>
        <?php
        }

        function update( $new_instance, $old_instance ) {
            $instance                   = $old_instance;
            $instance['title']          = strip_tags( $new_instance['title'] );
            $instance['attribute']      = ! empty( $new_instance['attribute'] ) ? stripslashes( $new_instance['attribute'] ) : array();
            $instance['query_type']     = stripslashes( $new_instance['query_type'] );
            $instance['type']           = stripslashes( $new_instance['type'] );
            $instance['colors']         = ! empty( $new_instance['colors'] ) ? $new_instance['colors'] : array();
            $instance['multicolor']     = ! empty( $new_instance['multicolor'] ) ? $new_instance['multicolor'] : array();
            $instance['labels']         = ! empty( $new_instance['labels'] ) ? $new_instance['labels'] : array();
            $instance['display']        = $new_instance['display'];
            $instance['extra_class']    = ! empty ( $new_instance['extra_class'] ) ? $new_instance['extra_class'] : '';

            return $instance;
        }

        /**
         * Print terms for the element selected
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function ajax_print_terms() {
            $type      = $_POST['value'];
            $attribute = $_POST['attribute'];
            $return    = array( 'message' => '', 'content' => $_POST );

            $terms = yith_wcan_wp_get_terms( array( 'taxonomy' => 'pa_' . $attribute, 'hide_empty' => '0' ) );

            $settings        = $this->get_settings();
            $widget_settings = $settings[ $this->number ];
            $value           = '';

            if( 'label' == $type ){
                $value = $widget_settings['labels'];
            }

            elseif( 'color' == $type ){
                $value = $widget_settings['colors'];
            }

            elseif( 'multicolor' == $type ) {
                $value = $widget_settings['multicolor'];
            }

            if ( $type ) {
                $return['content'] = yith_wcan_attributes_table(
                    $type,
                    $attribute,
                    $_POST['id'],
                    $_POST['name'],
                    $value,
                    false
                );
            }

            echo json_encode( $return );
            die();
        }

        public function get_list_html( $terms, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, $level = 0, $filter_term_field = 'slug', $rel_nofollow = '' ){
            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
            $in_array_function  = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
            $terms              = apply_filters( 'yith_wcan_get_list_html_terms', $terms, $taxonomy, $instance );
            $queried_object     = get_queried_object();
            foreach ( $terms as $parent_id => $term_ids ) {
                $term = get_term_by( 'id', $parent_id, $taxonomy );

                $exclude    = apply_filters( 'yith_wcan_exclude_terms', array(), $instance );
                $include    = apply_filters( 'yith_wcan_include_terms', array(), $instance );
                $echo       = false;

                if( 'tags' == $instance['type'] ) {
                    $term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );
                    if ( 'exclude' ==  $instance['tags_list_query'] ){
                        $echo = ! $in_array_function( $term_id, $exclude );
                    }

                    elseif ( 'include' ==  $instance['tags_list_query'] ){
                        $echo = $in_array_function( $term_id, $include );
                    }
                }

                else {
                    $echo = true;
                }

                $filter_by_tags_hierarchical = ($terms_type_list == 'tags' && $instance['display'] == 'hierarchical');

                if( $echo ) {

                    // Get count based on current view - uses transients
                    //$transient_name = 'wc_ln_count_' . md5(sanitize_key($taxonomy) . sanitize_key($term->term_id));

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                    $_products_in_term = get_objects_in_term($term->term_id, $taxonomy);

                    //set_transient($transient_name, $_products_in_term);
                    //}

					$option_is_set = (isset($_chosen_attributes[$taxonomy]) && $in_array_function($term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms']));


                    $term_param = apply_filters('yith_wcan_term_param_uri', $term->$filter_term_field, $display_type, $term);

                    $count = 0;

                    // If this is an AND query, only show options with count > 0
                    if ($query_type == 'and') {
                        $count = sizeof(array_intersect($_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );
                    } else {
                        // If this is an OR query, show all options so search can be expanded
                        $count = sizeof(array_intersect($_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids));
                    }

                    if ($count > 0 ) {
                        $this->found = true;
                    }

                    $arg = apply_filters('yith_wcan_list_type_query_arg', 'filter_' . sanitize_title($instance['attribute']), $display_type, $term, $instance['attribute']);

                    $current_filter = (isset($_GET[$arg])) ? explode(apply_filters('yith_wcan_list_filter_operator', ',', $display_type), apply_filters("yith_wcan_list_filter_query_{$arg}", $_GET[$arg] ) ) : array();

                    if (!is_array($current_filter)) {
                        $current_filter = array();
                    }

                    $current_filter = array_map('esc_attr', $current_filter);

                    if ( ! $in_array_function( $term_param, $current_filter ) ) {
                        $current_filter[] = $term_param;
                    }

                    $link = yit_get_woocommerce_layered_nav_link();

                    // All current filters
                    if ($_chosen_attributes) {
                        foreach ($_chosen_attributes as $name => $data) {
                            if ($name !== $taxonomy) {
								// Remove pa_ and sanitize
								$filter_name = sanitize_title(str_replace('pa_', '', $name));
                                // Exclude query arg for current term archive
                                if ($in_array_function($term->slug, $data['terms'])) {
                                    $key = array_search($current_term, $data);
                                    unset($data['terms'][$key]);
                                }

                                if (!empty($data['terms'])) {
                                    $link = add_query_arg('filter_' . $filter_name, implode(',', $data['terms']), $link);
                                }

                                if ($data['query_type'] == 'or') {
                                    $link = add_query_arg('query_type_' . $filter_name, 'or', $link);
                                }
                            }

							else {
								$filter_name = sanitize_title(str_replace('pa_', '', $name));
								$link = remove_query_arg('filter_' . $filter_name, $link);
							}
                        }
                    }

                    // Min/Max
                    if (isset($_GET['min_price'])) {
                        $link = add_query_arg('min_price', $_GET['min_price'], $link);
                    }

                    if (isset($_GET['max_price'])) {
                        $link = add_query_arg('max_price', $_GET['max_price'], $link);
                    }

                    if (isset($_GET['product_tag']) && $display_type != 'tags') {
                        $link = add_query_arg('product_tag', urlencode($_GET['product_tag']), $link);
                    }

                    elseif( is_product_tag() && $queried_object && $current_term != $queried_object->slug ){
                        $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
                    }

                   if (isset($_GET[$this->brand_taxonomy])) {
                       $brands = is_array( $_GET[$this->brand_taxonomy] ) ? array() : get_term_by('slug', $_GET[$this->brand_taxonomy], $this->brand_taxonomy);
                       if ( $brands instanceof WP_Term && $brands->term_id != $term->term_id ) {
                           $link = add_query_arg($this->brand_taxonomy, urlencode($brands->slug), $link);
                       }
                    }

                   elseif( ! empty( $this->brand_taxonomy ) && is_tax( $this->brand_taxonomy ) && $queried_object ) {
                       $link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
                   }

                    if ( isset( $_GET['source_id'] ) && isset( $_GET['source_tax'] ) ) {
                        $add_source_id = true;

                        if( property_exists( $term, 'term_id' ) && property_exists( $queried_object, 'term_id' ) && $term->term_id == $queried_object->term_id ){
                            if( ! yit_is_filtered_uri() ){
                                $add_source_id = false;
                            }
                        }

                        if( $add_source_id ) {
                            $args = array( 'source_id' => $_GET['source_id'], 'source_tax' => $_GET['source_tax'] );
                            $link = add_query_arg( $args, $link );
                        }
                    }

                    if( isset( $_GET['yith_shop_vendor'] ) ){
                        $link = add_query_arg( array( 'yith_shop_vendor' => $_GET['yith_shop_vendor'] ), $link );
                    }

                    if (isset($_GET['product_cat'])) {
                        $categories_filter_operator = 'and' == $query_type ? '+' : ',';
                        $_chosen_categories = explode($categories_filter_operator, urlencode($_GET['product_cat']));
                        $link = add_query_arg(
                            'product_cat',
                            implode(apply_filters('yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type), $_chosen_categories),
                            $link
                        );
                    }

                    elseif( is_product_category() && $queried_object ){
                        //Removed @JoseCostaRos
                        $link = add_query_arg( array( 'product_cat' => $queried_object->slug ), $link );
                    }

                    if( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id != $queried_object->term_id ){
                        $link = add_query_arg(
                            array(
                                'source_id'                 => $queried_object->term_id,
                                'source_tax'                => $queried_object->taxonomy,
                                $queried_object->taxonomy   => $queried_object->slug
                            ), $link );
                    }

                    $check_for_current_widget = isset($_chosen_attributes[$taxonomy]) && is_array($_chosen_attributes[$taxonomy]['terms']) && $in_array_function($term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms']);
                    $class = '';

                    // Current Filter = this widget
                    if ( apply_filters('yith_wcan_list_type_current_widget_check', $check_for_current_widget, $current_filter, $display_type, $term_param ) ) {
                        if (($terms_type_list == 'hierarchical' || ($terms_type_list == 'tags' && $instance['display'] == 'hierarchical'))) {
                            if (yit_term_is_child($term)) {
                                $class = "class='{$is_chosen_class}  {$is_child_class}'";
                            }

                            elseif (yit_term_is_parent($term)) {
                                $class = "class='{$is_chosen_class}  {$is_parent_class}'";
                            }
                        }

                        else {
                            $class = "class='{$is_chosen_class}'";
                        }

                        // Remove this term is $current_filter has more than 1 term filtered

                        if (sizeof($current_filter) > 1) {
                            $current_filter_without_this = array_diff($current_filter, array($term_param));
                            $link = add_query_arg($arg, implode(apply_filters('yith_wcan_list_filter_operator', ',', $display_type), $current_filter_without_this), $link);
                        }
                    }

                    else {

                        if (($terms_type_list == 'hierarchical' || $terms_type_list == 'tags')) {
                            if (yit_term_is_child($term)) {

                                $class = "class='{$is_child_class}'";
                            }

                            elseif (yit_term_is_parent($term)) {
                                $class = "class='{$is_parent_class}'";
                            }
                        }

                        $link = add_query_arg($arg, implode(apply_filters('yith_wcan_list_filter_operator', ',', $display_type), $current_filter), $link);
                    }

                    // Search Arg
                    if (get_search_query()) {
	                    $link = add_query_arg('s', urlencode( get_search_query() ), $link);
                    }

                    // Post Type Arg
                    if (isset($_GET['post_type'])) {
                        $link = add_query_arg('post_type', $_GET['post_type'], $link);
                    }

                    $is_attribute = apply_filters('yith_wcan_is_attribute_check', true, $instance);

                    // Query type Arg
                    if ($is_attribute && $query_type == 'or' && !(sizeof($current_filter) == 1 && isset($_chosen_attributes[$taxonomy]['terms']) && is_array($_chosen_attributes[$taxonomy]['terms']) && $in_array_function($term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms']))) {
                        $link = add_query_arg('query_type_' . sanitize_title($instance['attribute']), 'or', $link);
                    }

                    $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

                    $li_printed = false;
                    $to_print = false;

                    if( $count > 0 || $option_is_set ) {
                        $to_print = true;
                        $term_name = apply_filters( 'yith_wcan_term_name_to_show',$term->name, $term );
                        printf( '<li %s><a %s href="%s">%s</a>', $class, $rel_nofollow, $link, $term_name );
                        $li_printed = true;
                    }

                    else {
                        $to_print = apply_filters( 'yith_wcan_show_no_products_attributes', ( ! $filter_by_tags_hierarchical && $query_type != 'and' ), $count, $term );
                        $class = apply_filters( 'yith_wcan_list_type_empty_filter_class', $class );

                        if( $to_print ){
                            printf( '<li %s><span>%s</span>', $class, $term->name );
                            $li_printed = true;
                        }
                    }

                    $show_count = $count != 0 && ! empty( $instance['show_count'] ) && ! $instance['show_count'];

                    $show_count = apply_filters( "{$this->id}-show_product_count", $show_count, $instance );

                    if ( $to_print && apply_filters( 'yith_wcan_force_show_count', $show_count ) ) {
                        echo ' <small class="count">' . $count . '</small><div class="clear"></div>';
                    }

                    if( $li_printed ){
                        echo '</li>';
                    }

                }

                if( ! empty( $term_ids ) && is_array( $term_ids ) ){
	                echo '<ul class="yith-child-terms level-' . $level . '">';
                    $temp_level = $level;
                    $temp_level++;
                    $this->get_list_html( $term_ids, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, $temp_level, $filter_term_field, $rel_nofollow );
	                echo '</ul>';
                }
            }
        }

        //Override in Premium classes
        public function add_reset_taxonomy_link( $taxonomy, $instance ){}
    }
}
