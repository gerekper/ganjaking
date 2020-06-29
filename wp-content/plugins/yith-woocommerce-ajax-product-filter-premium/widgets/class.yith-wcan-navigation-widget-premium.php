<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( !defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCAN_Navigation_Widget_Premium' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class YITH_WCAN_Navigation_Widget_Premium extends YITH_WCAN_Navigation_Widget {

        function __construct() {
            add_filter( 'yith_wcan_get_terms_list', array( $this, 'reorder_terms_list' ), 10, 3 );
            parent::__construct();
        }

        public function form( $instance ) {
            /* === Add Premium Widget Types === */
            add_filter( 'yith_wcan_widget_types', array( $this, 'premium_widget_types' ) );
            add_filter( 'yith-wcan-attribute-list-class', array( $this, 'set_attribute_style' ) );

            parent::form( $instance );

            $defaults = array(
                'type'             => 'list',
                'style'            => 'square',
                'show_count'       => 0,
                'dropdown'         => 0,
                'dropdown_type'    => 'open',
                'tags_list'        => array(),
                'tags_list_query'  => 'exclude',
                'see_all_tax_text' => ''
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            $terms    = yith_wcan_wp_get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => false ) ); ?>

            <p class="yit-wcan-see-all-taxonomies-text">
                <label for="<?php echo $this->get_field_id( 'see_all_tax_text' ); ?>">
                    <?php _e( '"See all categories/tags" link text', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="text" id="<?php echo $this->get_field_id( 'see_all_tax_text' ); ?>" name="<?php echo $this->get_field_name( 'see_all_tax_text' ); ?>" value="<?php echo $instance['see_all_tax_text']?>" class="yith-wcan-see-all-text-field widefat" />
                    <span class="yith-wcan-see-all-taxonomies-text-description">
                        <?php printf( '%s <a href="%s" target="_blank">%s</a> <span class="yith-wcan-see-all-taxonomies-text-default"> %s: <strong>%s</strong><br/>%s: <strong>%s</strong></span>',
                            __( 'Leave it empty to use the default text available', 'yith-woocommerce-ajax-navigation' ),
                            add_query_arg( array( 'page' => 'yith_wcan_panel', 'tab' => 'general' ), admin_url( 'admin.php' ) ),
                            __( 'here', 'yith-woocommerce-ajax-navigation' ),
                            __( 'current categories text', 'yith-woocommerce-ajax-navigation' ),
                            yith_wcan_get_option( "yith_wcan_enable_see_all_categories_link_text" ),
                            __( 'current tags text', 'yith-woocommerce-ajax-navigation' ),
                            yith_wcan_get_option( "yith_wcan_enable_see_all_tags_link_text" )
                        ); ?>
                    </span>
                </label>
            </p>

            <div class="yit-wcan-widget-tag-list <?php echo $instance['type'] ?>">
                <?php

                if ( is_wp_error( $terms ) || empty( $terms ) ) {
                    _e( 'No tags found.', 'yith-woocommerce-ajax-navigation' );
                }

                else { ?>
                    <strong><?php _ex( 'Tag List', 'Admin: Section title', 'yith-woocommerce-ajax-navigation' ) ?></strong>
                    <select class="yith_wcan_tags_query_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'tags_list_query' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags_list_query' ) ); ?>">
                        <option value="include" <?php selected( 'include', $instance['tags_list_query'] ) ?>> <?php _e( 'Show Selected', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                        <option value="exclude" <?php selected( 'exclude', $instance['tags_list_query'] ) ?>>  <?php _e( 'Hide Selected', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    </select>
                    <div class="yith-wcan-select-option">
                        <a href="#" class="select-all">
                            <?php _e( 'Select all', 'yith-woocommerce-ajax-navigation' ) ?>
                        </a>
                        <a href="#" class="unselect-all">
                            <?php _e( 'Unselect all', 'yith-woocommerce-ajax-navigation' ) ?>
                        </a>
                        <small class="yith-wcan-admin-note"><?php echo '* ' . _x( 'Note: tags with no products assigned will not be showed in the front end', 'Admin: user note', 'yith-woocommerce-ajax-navigation' ) ?></small>
                    </div>
                    <div class="yith_wcan_select_tag_wrapper">
                        <table class="yith_wcan_select_tag">
                            <thead>
                            <tr>
                                <td><?php _e( 'Tag name', 'yith-woocommerce-ajax-navigation' ) ?></td>
                                <td><?php _e( 'Count', 'yith-woocommerce-ajax-navigation' ) ?>
                                    <small class="yith-wcan-admin-note-star">(*)</small>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ( $terms as $term ) : ?>
                                <tr>
                                    <td class="term_name">
                                        <?php $checked = is_array( $instance['tags_list'] ) && array_key_exists( $term->term_id, $instance['tags_list'] ) ? 'checked' : ''; ?>
                                        <input type="checkbox" value="<?php echo $term->slug ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?>[<?php echo $term->term_id; ?>]" class="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?> yith_wcan_tag_list_checkbox" id="<?php echo esc_attr( $this->get_field_id( 'tags_list' ) ); ?>" <?php echo $checked; ?>/>
                                        <label for=""><?php echo $term->name; ?></label>
                                    </td>
                                    <td class="term_count">
                                        <?php echo $term->count; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <p id="yit-wcan-style" class="yit-wcan-style-<?php echo $instance['type'] ?>">
                <label for="<?php echo $this->get_field_id( 'style' ); ?>">
                    <strong><?php _ex( 'Color Style:', 'Select if you want to show round color box or square color box', 'yith-woocommerce-ajax-navigation' ) ?></strong>
                </label>
                <select class="yith_wcan_style widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                    <option value="square" <?php selected( 'square', $instance['style'] ) ?>> <?php _e( 'Square', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="round" <?php selected( 'round', $instance['style'] ) ?>>  <?php _e( 'Round', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                </select>
            </p>

            <p id="yit-wcan-show-count" class="yit-wcan-show-count-<?php echo $instance['type'] ?>">
                <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Hide product count', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1" <?php checked( $instance['show_count'], 1, true ) ?> class="widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-<?php echo $instance['type'] ?>" class="yith-wcan-dropdown">
                <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ) ?>:
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true ) ?> class="yith-wcan-dropdown-check widefat" />
                </label>
            </p>

            <p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo $instance['type'] ?>" style="display: <?php echo !empty( $instance['dropdown'] ) ? 'block' : 'none' ?>;">
                <label for="<?php echo $this->get_field_id( 'dropdown_type' ); ?>"><strong><?php _ex( 'Dropdown style:', 'Select this if you want to show the widget as open or closed', 'yith-woocommerce-ajax-navigation' ) ?></strong></label>
                <select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
                    <option value="open" <?php selected( 'open', $instance['dropdown_type'] ) ?>> <?php _e( 'Opened', 'yith-woocommerce-ajax-navigation' ) ?> </option>
                    <option value="close" <?php selected( 'close', $instance['dropdown_type'] ) ?>>  <?php _e( 'Closed', 'yith-woocommerce-ajax-navigation' ) ?> </option>
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

            $instance = parent::update( $new_instance, $old_instance );

            $instance['style']              = $new_instance['style'];
            $instance['show_count']         = isset( $new_instance['show_count'] ) ? 1 : 0;
            $instance['dropdown']           = isset( $new_instance['dropdown'] ) ? 1 : 0;
            $instance['dropdown_type']      = $new_instance['dropdown_type'];
            $instance['tags_list']          = ! empty( $new_instance['tags_list'] ) ? $new_instance['tags_list'] : array();
            $instance['tags_list_query']    = isset( $new_instance['tags_list_query'] ) ? $new_instance['tags_list_query'] : 'include';
            $instance['see_all_tax_text']   = $new_instance['see_all_tax_text'];

            return $instance;
        }

        public function widget( $args, $instance ) {
            add_filter( "{$this->id}-li_style", array( $this, 'color_and_label_style' ), 10, 2 );
            add_filter( "{$this->id}-show_product_count", array( $this, 'show_product_count' ), 10, 2 );
            add_filter( "yith_widget_title_ajax_navigation", array( $this, 'widget_title' ), 10, 3 );
            add_action( 'yith_wcan_widget_display_multicolor', array( $this, 'show_premium_widget' ), 10, 6 );
            add_action( 'yith_wcan_widget_display_categories', array( $this, 'show_premium_widget' ), 10, 6 );

            /* === Tag & Brand Filter === */
            add_filter( 'yith_wcan_get_terms_params', array( $this, 'get_terms_params' ), 10, 3 );
            add_filter( 'yith_wcan_display_type_list', array( $this, 'add_display_type_case' ) );
            add_filter( 'yith_wcan_list_type_query_arg', array( $this, 'type_query_args' ), 10, 3 );
            add_filter( 'yith_wcan_term_param_uri', array( $this, 'term_param_uri' ), 10, 3 );
            add_filter( 'yith_wcan_list_type_current_widget_check', array( $this, 'filter_current_widget' ), 10, 4 );
            add_filter( 'yith_wcan_is_attribute_check', array( $this, 'filter_by_attributes_check' ), 10, 2 );
            add_filter( 'yith_wcan_list_filter_operator', array( $this, 'tag_brands_filter_operator' ), 10, 2 );

            if ( ! empty( $instance['type'] ) && 'tags' == $instance['type'] ) {
                $query_option = isset( $instance['tags_list_query'] ) ? $instance['tags_list_query'] : 'include';
                add_filter( "yith_wcan_{$query_option}_terms", array( $this, 'yith_wcan_include_exclude_terms' ), 10, 2 );
                add_filter( 'yith_wcan_list_filter_query_product_tag', array( $this, 'yith_wcan_list_filter_query_product_tag' ) );
            }

            $brands_tax_name = $this->type_query_args( 'product_brand', 'brands' );

            add_filter( "yith_wcan_list_filter_query_{$brands_tax_name}" , array( $this, 'yith_wcan_list_filter_query_product_tag' ) );

            if( function_exists( 'yit_decode_title' ) ){
                remove_filter( 'widget_title', 'yit_decode_title' );
            }

            parent::widget( $args, $instance );
        }

        public function filter_by_attributes_check( $check, $instance ){
            if( 'tags' == $instance['type'] || 'brands' == $instance['type'] ) {
                $check = false;
            }
            return $check;
        }

        public function add_reset_taxonomy_link( $taxonomy, $instance ){
	        $rel_nofollow = yith_wcan_add_rel_nofollow_to_url( true );
	        $allowed_taxonomies_reset_link = apply_filters( 'yit_wcan_allowed_taxonomies_reset_link', array(
		        'product_cat',
		        'product_tag'
	        ) );
            $in_array_function = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
            if( ( yit_is_filtered_uri() || is_product_category() || is_product_taxonomy() || is_product_tag() ) && $in_array_function( $taxonomy, $allowed_taxonomies_reset_link ) ){

                if( 'product_cat' == $taxonomy ){
                    $taxonomy = 'categories';
                }

                elseif ( 'product_tag' == $taxonomy ){
                    $taxonomy = 'tags';
                }

                $show = 'yes' == yith_wcan_get_option( "yith_wcan_enable_see_all_{$taxonomy}_link", 'no' ) ? true : false;
                $show = apply_filters( "yith_wcan_enable_see_all_{$taxonomy}_link", $show );

                if( $show ){
                    $reset_categories_link = apply_filters( "yith_wcan_reset_{$taxonomy}_link", esc_url( get_the_permalink( wc_get_page_id( 'shop' ) ) ) );
                    $default_value_option = sprintf( '%s %s', __( 'See all', 'yith-woocommerce-ajax-navigation' ), $taxonomy );
                    $see_all_text = empty( $instance['see_all_tax_text'] ) ? yith_wcan_get_option( "yith_wcan_enable_see_all_{$taxonomy}_link_text", $default_value_option ) : $instance['see_all_tax_text'];

                    //$see_all_text = apply_filters( "yith_wcan_see_all_{$taxonomy}_link_text", $see_all_text, $instance );
                    printf( '<span id="yith-wcan-reset-all-%1$s" class="%2$s"><a class="yith-wcan-reset-%1$s-link" href="%3$s" %5$s>%4$s</a></span>',
                        $taxonomy,
                        apply_filters( "yith_wcan_show_all_{$taxonomy}_classes", "yith-wcan-show-all-{$taxonomy}" ),
                        $reset_categories_link,
                        $see_all_text,
                        $rel_nofollow
                    );
                }
            }
        }

        public function color_and_label_style( $li_style, $instance ) {

            if ( !empty( $instance['style'] ) && 'round' == $instance['style'] ) {
                $li_style .= 'border-radius: 50%;';
            }

            return $li_style;
        }

        public function show_product_count( $show, $instance ) {
            return empty( $instance['show_count'] ) ? true : false;
        }

        public function widget_title( $title, $instance, $id_base ) {
            $span_class = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
            $instance['dropdown_type'] = isset( $instance['dropdown_type'] ) ? $instance['dropdown_type'] : 'open';
            $dropdown_type = apply_filters( 'yith_wcan_dropdown_type', $instance['dropdown_type'], $instance );
            $span_html  =  sprintf( '<span class="%s" data-toggle="%s"></span>', $span_class, ! empty( $dropdown_type ) ? $dropdown_type : 'open' );
            $title      = ! empty( $instance['dropdown'] ) ? $title . ' ' . $span_html : $title;
            return $title;
        }

        public function premium_widget_types( $types ) {
            $types['categories'] = __( 'Categories', 'yith-woocommerce-ajax-navigation' );
            $types['multicolor'] = __( 'BiColor', 'yith-woocommerce-ajax-navigation' );
            $types['tags']       = __( 'Tag', 'yith-woocommerce-ajax-navigation' );

            if ( yith_wcan_brands_enabled() ) {
                $types['brands'] = __( 'Brand', 'yith-woocommerce-ajax-navigation' );
            }
            return $types;
        }

        public function show_premium_widget( $args, $instance, $display_type, $terms, $taxonomy, $filter_term_field = 'slug' ) {
	        global $wc_product_attributes;
	        $rel_nofollow       = yith_wcan_add_rel_nofollow_to_url( true );
            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
            $in_array_function = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
            $queried_object = get_queried_object();
            extract( $args );

            $_attributes_array = yit_wcan_get_product_taxonomy();

            if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
                return;
            }

            if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
                return;
            }

            $current_term       = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->term_id : '';
            $current_tax        = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->taxonomy : '';
            $title              = apply_filters( 'yith_widget_title_ajax_navigation', ( isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '' ), $instance, $this->id_base );
            $query_type         = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
            $display_type       = isset( $instance['type'] ) ? $instance['type'] : 'list';
            $is_child_class     = 'yit-wcan-child-terms';
            $is_parent_class    = 'yit-wcan-parent-terms';
            $is_chosen_class    = 'chosen';
            $terms_type_list    = ( isset( $instance['display'] ) && $display_type == 'categories' ) ? $instance['display'] : 'all';

            $instance['attribute'] = empty( $instance['attribute'] ) ? '' : $instance['attribute'];

            if ( 'multicolor' == $display_type ) {
                // List display
                echo "<ul class='yith-wcan-color yith-wcan yith-wcan-group {$instance['extra_class']}'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                    $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                    //set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

                        // skip the term for the current archive
//                        if ( $current_term == $term->$filter_term_field ) {
//                            continue;
//                        }

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
//                        if ( $current_term == $term->$filter_term_field ) {
//                            continue;
//                        }

                        $count = sizeof( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            $this->found = true;
                        }

                        elseif( apply_filters( 'yith_wcan_skip_no_product_count_bicolor', false ) ){
                            continue 1;
                        }
                    }



                    $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( !is_array( $current_filter ) ) {
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
                                while ( $in_array_function( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( !empty( $data['terms'] ) ) {
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

                    if ( isset( $_GET[$this->brand_taxonomy] ) ) {
                        $brands = get_term_by( 'slug', $_GET[$this->brand_taxonomy], $this->brand_taxonomy );
                        if ( $brands instanceof WP_Term && $brands->term_id != $term->term_id ) {
                            $link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link );
                        }
                    }

                    elseif( is_tax( $this->brand_taxonomy ) && $queried_object ) {
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
                    if ( $query_type == 'or' && !( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }

                    $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

                    $term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

                    $colors = array();

                    if( ! empty( $instance['multicolor'][$term_id] ) && is_array( $instance['multicolor'][$term_id] ) && !empty( $instance['multicolor'][$term_id][0] ) ){
                       $colors = $instance['multicolor'][$term_id];
                    }

                    elseif( function_exists( 'ywccl_get_term_meta' ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'colorpicker' == $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
	                    $colors = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );
	                    if( ! empty( $colors ) ){
		                    $colors = explode( ',', $colors );
	                    }
                    }

                    if ( $colors ) {

                        $a_style   = '';
                        $is_single = false;
                        $a_class   = '';

                        if ( empty( $colors[1] ) ) {
                            $a_style   = apply_filters( "{$this->id}-a_style", 'background-color:' . $colors[0] . ';', $instance );
                            $is_single = true;
                            $a_class   = 'singlecolor';
                        }

                        else {
                            $color_1_style = 'border-color: ' . $colors[0] . ' transparent;';
                            $color_2_style = 'border-color: ' . $colors[1] . ' transparent;';
                            $a_class       = 'multicolor ' . $instance['style'];
                        }

                        echo '<li ' . $class . '>';

                        echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' class="' . $a_class . '" style="' . $a_style . '" href="' . $link . '" title="' . $term->name . '" >' : '<span style="background-color:' . $instance['multicolor'][$term_id][0] . ';" >';

                        if ( !$is_single ) {
                            echo '<span class="multicolor color-1 ' . $instance['style'] . '" style=" ' . $color_1_style . ' "></span>';
                            echo '<span class="multicolor color-2 ' . $instance['style'] . '" style=" ' . $color_2_style . ' "></span>';
                        }

                        echo $term->name;

                        echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                    }else {
                        $this->found = apply_filters( 'yith_wcan_found_with_no_colors_set', $this->found, $instance, $term, $taxonomy );
                    }
                }
                echo "</ul>";
            }
            elseif ( 'categories' == $display_type ) {
                $ancestors = array();
                $tree = array();

                if( 'hierarchical' == $instance['display'] ){
                    $ancestors = yith_wcan_wp_get_terms(
                        array(
                            'taxonomy'      => 'product_cat',
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
                            $tree[ $ancestor->term_id ] = yit_reorder_hierachical_categories( $ancestor->term_id );
                        }
                    }
                }

                else {
                    foreach( $terms as $term ){
                        $tree[ $term->term_id ] = array();
                    }
                }

                $categories_filter_operator = 'and' == $query_type ? '+' : ',';

                $this->add_reset_taxonomy_link( $taxonomy, $instance );

                // List display
                echo "<ul class='yith-wcan-list yith-wcan categories {$instance['extra_class']}'>";

                $this->get_categories_list_html( $args, $tree, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, 0, $rel_nofollow );

                echo "</ul>";
            }
        }

        public function get_categories_list_html( $args, $terms, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, $level = 0, $rel_nofollow = '' ){
            $_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
            $brands_taxonomy    = yit_get_brands_taxonomy();
            $in_array_function = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
            $_get_current_filter = '';


            foreach ( $terms as $parent_id => $term_ids ) {

                $count = 0 ;
                $term = get_term_by( 'id', $parent_id, 'product_cat' );
                $filter_is_hierarchical = $instance['display'] == 'hierarchical';

                // Get count based on current view - uses transients
                //$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                //set_transient( $transient_name, $_products_in_term );
                //}

                $option_is_set = ( isset( $_chosen_attributes[$taxonomy] ) && $in_array_function( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) );

                $term_param = apply_filters( 'yith_wcan_term_param_uri', $term->slug, $display_type, $term );

                // If this is an AND query, only show options with count > 0
                if ( $query_type == 'and' ) {

                    $product_selection = apply_filters('yith_wcan_products_filter_category_and',array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ),$_products_in_term,YITH_WCAN()->frontend->layered_nav_product_ids);
                    $count = sizeof( $product_selection );

                    if ( $count > 0 && $current_term !== $term_param ) {
                        $this->found = true;
                    }

                    if ( apply_filters( 'yith_wcan_skip_no_products_in_category', ( ! yit_term_has_child( $term, $taxonomy ) ) && $count == 0 && ! $option_is_set, $terms, $term ) ) {
                        continue;
                    }

                    // If this is an OR query, show all options so search can be expanded
                }
                else {
	                //TODO: Temporary Fix
                    $to_exclude = get_transient( 'yith_wcan_exclude_from_catalog_product_ids' );

                    if( false === $to_exclude ){
	                    $unfiltered_args =  array(
		                    'post_type'              => 'product',
		                    'numberposts'            => - 1,
		                    'post_status'            => 'publish',
		                    'fields'                 => 'ids',
		                    'no_found_rows'          => true,
		                    'update_post_meta_cache' => false,
		                    'update_post_term_cache' => false,
		                    'pagename'               => '',
		                    'wc_query'               => 'get_products_in_view', //Only for WC <= 2.6.x
		                    'suppress_filters'       => true,
	                    );

	                    $wc_get_product_visibility_term_ids = function_exists( 'wc_get_product_visibility_term_ids' ) ? wc_get_product_visibility_term_ids() : array();

	                    if( ! empty( $wc_get_product_visibility_term_ids['exclude-from-catalog'] ) ){
		                    $unfiltered_args['tax_query'][] = array(
			                    'taxonomy' => 'product_visibility',
			                    'terms'    => $wc_get_product_visibility_term_ids['exclude-from-catalog'],
			                    'operator' => 'IN',
		                    );
	                    }

	                    $to_exclude = get_posts( $unfiltered_args );
	                    set_transient( 'yith_wcan_exclude_from_catalog_product_ids', $to_exclude, MONTH_IN_SECONDS );
                    }

                    $product_selection = apply_filters('yith_wcan_products_filter_category_or',array_intersect( $_products_in_term, array_diff( $_products_in_term, $to_exclude ) ),$_products_in_term, $to_exclude);

                    $count = sizeof( $product_selection );

                    $this->found = true;
                }

                $arg = apply_filters( 'yith_wcan_categories_type_query_arg', $taxonomy, $display_type, $term );

                if( ! empty( $_GET[$arg] ) ){
                    $_get_current_filter = 'and' == $query_type ? urlencode( $_GET[$arg] ) : $_GET[$arg];
                }


                $current_filter = ( isset( $_GET[$arg] ) ) ? explode( apply_filters( 'yith_wcan_list_filter_operator', $categories_filter_operator, $display_type ), apply_filters( "yith_wcan_list_filter_query_{$arg}", $_get_current_filter ) ) : array();

                if ( ! is_array( $current_filter ) ) {
                    $current_filter = array();
                }

                $current_filter = array_map( 'esc_attr', $current_filter );

                if ( ! $in_array_function( $term_param, $current_filter ) ) {
                    $current_filter[] = $term_param;
                }

                $link =  '';
                $link = yit_get_woocommerce_layered_nav_link();

                // All current filters
                if ( $_chosen_attributes ) {
                    foreach ( $_chosen_attributes as $name => $data ) {
                        if ( $name !== $taxonomy ) {

                            // Exclude query arg for current term archive term
                            while ( $in_array_function( $current_term, $data['terms'] ) ) {
                                $key = array_search( $current_term, $data );
                                unset( $data['terms'][$key] );
                            }

                            // Remove pa_ and sanitize
                            $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                            if ( !empty( $data['terms'] ) ) {
                                $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                            }

                            if ( $data['query_type'] == 'or' ) {
                                $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                            }
                        }
                    }
                }

                $_chosen_categories = array();
                $queried_object     = get_queried_object();

                // Min/Max
                if ( isset( $_GET['min_price'] ) ) {
                    $link = add_query_arg( 'min_price', $_GET['min_price'], $link );
                }

                if ( isset( $_GET['max_price'] ) ) {
                    $link = add_query_arg( 'max_price', $_GET['max_price'], $link );
                }

                if ( isset( $_GET['product_tag'] ) && $display_type != 'tags' ) {
                    $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
                }

                elseif( is_product_tag() && $queried_object ){
                    $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
                }

                if ( isset( $_GET[$this->brand_taxonomy] ) ) {
                    $brands = get_term_by( 'slug', $_GET[$this->brand_taxonomy], $this->brand_taxonomy );

                    if ( $brands instanceof WP_Term && $term && $brands->term_id != $term->term_id ) {
                        $link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link );
                    }
                }

                elseif( taxonomy_exists( $this->brand_taxonomy ) && is_tax( $this->brand_taxonomy ) && $queried_object ) {
                    $link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
                }

                if( isset( $_GET['product_cat'] ) ){
                    $_get_product_cat = 'and' == $query_type ? urlencode( $_GET['product_cat'] ) : $_GET['product_cat'];
                    $_chosen_categories = explode( $categories_filter_operator, $_get_product_cat );
                }

                elseif( is_product_category() && $queried_object ){
                    //Removed @JoseCostaRos
                    $current_filter[] = $_chosen_categories[] = $queried_object->slug;
                }

                $skip = false;

                if( ( is_product_category( $term->term_id ) && $queried_object->term_id == $term->term_id ) || ( is_post_type_archive( 'product' ) && isset( $_GET['source_id'] ) && $term->term_id == $_GET['source_id'] ) ){
                    $skip = apply_filters( 'yith_wcan_skip_current_category', 'yes' == yith_wcan_get_option( 'yith_wcan_show_current_categories_link', 'no' ) ? false : true );
                }

                if( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id != $queried_object->term_id ){

                    $link = add_query_arg(
                        array(
                            'source_id'                 => $queried_object->term_id,
                            'source_tax'                => $queried_object->taxonomy,
                            $queried_object->taxonomy   => $queried_object->slug
                        ), $link );
                }

                // Current Filter = this widget
                if ( apply_filters( 'yith_wcan_categories_type_current_widget_check', $in_array_function( $term->slug, $_chosen_categories ), $current_filter, $display_type, $term_param ) ) {
                    $class = '';
                    if( $terms_type_list == 'hierarchical' ){
                        if( yit_term_is_child( $term ) ){
                            $class = "class='{$is_chosen_class}  {$is_child_class}'";
                        }

                        elseif( yit_term_is_parent( $term ) ) {
                            $class = "class='{$is_chosen_class}  {$is_parent_class}'";
                        }

                    }

                    else {
                        $class = "class='{$is_chosen_class}'";
                    }

                    // Remove this term is $current_filter has more than 1 term filtered
                    if ( sizeof( $current_filter ) > 1 ) {
                        $current_filter = array_map( 'strtolower', $current_filter );
                        $term_param = strtolower( $term_param );
                        $current_filter_without_this = array_diff( $current_filter, array( $term_param ) );
                        $value                       = implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $current_filter_without_this );
                        if( ! empty( $value ) ){
                            $link = add_query_arg( $arg, $value, $link );
                        }
                    }
                }

                else {
                    //$class = $terms_type_list == 'hierarchical' && yit_term_is_child( $term ) ? "class='{$is_child_class}'" : '';
                    $class= '';
                    if( $terms_type_list == 'hierarchical' ){
                        if( yit_term_is_child( $term ) ){

                            $class = "class='{$is_child_class}'";
                        }

                        elseif( yit_term_is_parent( $term ) ) {
                            $class = "class='{$is_parent_class}'";
                        }

                    }

                    $link  = add_query_arg( $arg, implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type ), $current_filter ), $link );
                }

                // Search Arg
                if ( get_search_query() ) {
	                $link = add_query_arg( 's', urlencode( get_search_query() ), $link );
                }

                // Post Type Arg
                if ( isset( $_GET['post_type'] ) ) {
                    $link = add_query_arg( 'post_type', $_GET['post_type'], $link );
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

                $is_attribute = apply_filters( 'yith_wcan_is_attribute_check', true, $instance );

                // Query type Arg
                if ( $is_attribute && $query_type == 'or' && !( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array( $_chosen_attributes[$taxonomy]['terms'] ) && $in_array_function( $term->term_id, $_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                    $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                }

                $link = str_replace( '+', '%2B', $link );
                $link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

                $exclude = apply_filters( 'yith_wcan_exclude_category_terms', array(), $instance );

                if ( ! empty( $exclude ) && $in_array_function( $term->term_id, $exclude ) ){
                    $skip = true;
                }

                $li_printed = false;

                if( ! apply_filters( 'yith_wcan_skip_current_categories', $skip, $taxonomy, $terms, $count ) ){
                    $yith_wcan_skip_no_products_in_category = apply_filters( 'yith_wcan_skip_no_products_in_category', $filter_is_hierarchical, $terms, $term  );
                    $li_printed = $count > 0 || $option_is_set || $query_type == 'or' || ! $yith_wcan_skip_no_products_in_category;

                    if( $li_printed ){
                        echo '<li ' . apply_filters( 'yith_wcan_categories_item_class', $class, $term ) . '>';
                    }

                    if( $count > 0 || $option_is_set || $query_type == 'or' ) {
                        printf( '<a %s href="%s">%s</a>', $rel_nofollow, $link, $term->name );
                    }

                    elseif( ! $yith_wcan_skip_no_products_in_category ) {
                        printf( '<span>%s</span>', $term->name );
                    }

                    $hide_count = ! empty( $instance['show_count'] ) && ! $instance['show_count'];

                    if (
                            apply_filters( 'yith_wcan_force_show_count_in_category', $count != 0 && ! $hide_count )
                            &&
                            apply_filters( "{$this->id}-show_product_count", true, $instance )
                    ) {
                        echo ' <small class="count">' . $count . '</small><div class="clear"></div>';
                    }
                }

                if( ! empty( $term_ids ) && is_array( $term_ids ) ){
                    echo '<ul class="yith-child-terms level-' . $level . '">';
                    $temp_level = $level;
                    $temp_level++;
                    $this->get_categories_list_html( $args, $term_ids, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, $temp_level, $rel_nofollow );
	                echo '</ul>';
                }

	            if( $li_printed ) {
                    do_action('yith_wcan_before_closing_list_item',$term);
		            echo '</li>';
	            }
            }
        }

        public function get_terms_params( $param, $instance, $type ) {
            if( empty( $instance['type'] ) ){
	            $instance['type'] = 'list';
            }

            if ( 'tags' == $instance['type'] ) {
                if ( 'taxonomy_name' == $type ) {
                    $param = 'product_tag';
                }

            }

            elseif ( 'brands' == $instance['type'] && yith_wcan_brands_enabled() ) {
                if ( 'taxonomy_name' == $type ) {
                    $param = YITH_WCBR::$brands_taxonomy;
                }

            }

            elseif( 'categories' == $instance['type'] && 'taxonomy_name' == $type ){
                $param = 'product_cat';
            }
            return $param;
        }

        public function add_display_type_case( $args ) {
            $args[] = 'tags';
            $args[] = 'brands';
            return $args;
        }

        public function type_query_args( $arg, $type, $term = null ) {
            if ( 'tags' == $type ) {
                $arg = 'product_tag';
            }

            elseif ( 'brands' == $type && yith_wcan_brands_enabled() ) {
                $arg = YITH_WCBR::$brands_taxonomy;
            }

            return $arg;
        }

        public function term_param_uri( $term_param, $type, $term ) {
            if ( 'tags' == $type || 'brands' == $type ) {
                $term_param = $term->slug;
            }

            return $term_param;
        }

        public function filter_current_widget( $check_for_current_widget, $current_term, $type, $term_param ) {
            $brands_taxonomy = yith_wcan_brands_enabled() ? YITH_WCBR::$brands_taxonomy : '';
            $in_array_function = apply_filters( 'yith_wcan_in_array_ignor_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';

            if ( 'tags' == $type && isset( $_GET['product_tag'] ) ) {
                $current_filters = explode( '+', urlencode( $_GET['product_tag'] ) );
                if ( $in_array_function( $term_param, $current_filters ) ) {
                    $check_for_current_widget = true;
                }
            }

            elseif ( 'brands' == $type && isset( $_GET[$brands_taxonomy] ) ) {
                $current_filters = explode( '+', urlencode( $_GET[$brands_taxonomy] ) );
                if ( $in_array_function( $term_param, $current_filters ) ) {
                    $check_for_current_widget = true;
                }
            }
            return $check_for_current_widget;
        }

        public function tag_brands_filter_operator( $operator, $display_type ) {
            return ( 'tags' == $display_type || 'brands' == $display_type ) ? '+' : $operator;
        }

        public function yith_wcan_include_exclude_terms( $ids, $instance ) {
            $option_ids = empty( $instance['tags_list'] ) ? array() : $instance['tags_list'];

            if ( empty( $option_ids ) ) {
                if ( 'yith_wcan_include_terms' == current_filter() ) {
                    $option_ids = array();
                }

                elseif ( 'yith_wcan_exclude_terms' == current_filter() ) {
                    $option_ids = array();
                }
            }

            else {
                $option_ids = is_array( $option_ids ) ? array_keys( $option_ids ) : array();
            }
            
            return array_merge( $ids, $option_ids );
        }

        public function yith_wcan_list_filter_query_product_tag( $_get ) {
            return urlencode( $_get );
        }

        public function reorder_terms_list( $terms, $taxonomy, $instance ){
            if( 'product_tag' == $taxonomy && 'tags' == $instance['type'] ){
                $terms = yit_reorder_terms_by_parent( $terms, $taxonomy );
            }
            return $terms;
        }
    }
}

//TODO: Temporary Fix

add_action( 'save_post', 'yith_wcan_exclude_from_catalog_product_ids', 99 );

if( ! function_exists( 'yith_wcan_exclude_from_catalog_product_ids' ) ){
    function yith_wcan_exclude_from_catalog_product_ids(){
        delete_transient( 'yith_wcan_exclude_from_catalog_product_ids' );
    }
}