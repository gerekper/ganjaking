<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Outputs a custom select template in plugin options panel
 *
 * @class   YITH_FL_Ajax_Products
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_FL_Ajax_Products {

    /**
     * Single instance of the class
     *
     * @var \YITH_FL_Ajax_Products
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_FL_Ajax_Products
     * @since 1.0.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    private function __construct() {
        add_action( 'woocommerce_admin_field_yith-wcmbs-ajax-products', array( $this, 'output' ) );

        add_action( 'wp_ajax_yith_wcmbs_json_search_products_and_variations', array( $this, 'json_search_products' ) );
        add_action( 'wp_ajax_nopriv_yith_wcmbs_json_search_products_and_variations', array( $this, 'json_search_products' ) );
    }


    /**
     * Outputs a custom select template in plugin options panel
     *
     * @since   1.0.0
     *
     * @param   $option
     *
     * @return  void
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function output( $option ) {
        $is_multiple        = isset( $option[ 'multiple' ] ) && $option[ 'multiple' ];
        $include_variations = isset( $option[ 'include_variations' ] ) && $option[ 'include_variations' ];
        $action             = $include_variations ? 'woocommerce_json_search_products_and_variations' : 'woocommerce_json_search_products';

        $custom_attributes = array();

        if ( !empty( $option[ 'custom_attributes' ] ) && is_array( $option[ 'custom_attributes' ] ) ) {
            foreach ( $option[ 'custom_attributes' ] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        $option_value = WC_Admin_Settings::get_option( $option[ 'id' ], $option[ 'default' ] );
        
        if ( !is_array( $option_value ) && !!$option_value && is_string( $option_value ) ) {
            $product_ids = explode( ',', $option_value );
            $value       = $option_value;
        } else {
            $product_ids = (array) $option_value;
            $value       = implode( ',', $product_ids );
        }

        $data_selected = array();

        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( is_object( $product ) ) {
                $title                        = $product->get_formatted_name();
                $data_selected[ $product_id ] = $title;
            }
        }
        ?>
        <tr valign="top" class="titledesc">
            <th scope="row">
                <label for="<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php echo esc_html( $option[ 'name' ] ); ?></label>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <?php
                yit_add_select2_fields( array(
                                            'id'               => esc_attr( $option[ 'id' ] ),
                                            'name'             => esc_attr( $option[ 'id' ] ),
                                            'class'            => 'wc-product-search',
                                            'style'            => 'width:400px',
                                            'data-placeholder' => __( 'Search for a product...', 'yith-woocommerce-membership' ),
                                            'data-selected'    => $data_selected,
                                            'data-allow_clear' => isset( $data[ 'allow_clear' ] ) ? $data[ 'allow_clear' ] : false,
                                            'data-multiple'    => $is_multiple,
                                            'data-action'      => $action,
                                            'value'            => $value,
                                        ) );
                ?>
                <span class="description"><?php echo $option[ 'desc' ] ?></span>
            </td>
        </tr>
        <?php
    }

    public function json_search_products() {
        ob_start();

        check_ajax_referer( 'search-products', 'security' );

        $term    = (string) wc_clean( stripslashes( $_GET[ 'term' ] ) );
        $exclude = array();

        if ( empty( $term ) ) {
            die();
        }

        if ( !empty( $_GET[ 'exclude' ] ) ) {
            $exclude = array_map( 'intval', explode( ',', $_GET[ 'exclude' ] ) );
        }

        $found_products = array();

        $args = array(
            'post_type'        => 'product',
            'post_status'      => 'publish',
            'numberposts'      => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_parent'      => 0,
            'suppress_filters' => 0,
            's'                => $term,
            'fields'           => 'ids',
            'exclude'          => $exclude,
            'lang'             => false, // support for Polylang
        );

        $posts = get_posts( $args );

        if ( !empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $product = wc_get_product( $post );

                if ( !current_user_can( 'read_product', $post ) ) {
                    continue;
                }

                $found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
            }
        }

        $found_products = apply_filters( 'yith_wcmbs_json_search_found_products', $found_products );

        wp_send_json( $found_products );
    }
}

/**
 * Unique access to instance of YITH_FL_Ajax_Products class
 *
 * @return \YITH_FL_Ajax_Products
 * @since 1.0.0
 */
function YITH_FL_Ajax_Products() {
    return YITH_FL_Ajax_Products::get_instance();
}