<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Shortcodes Class
 *
 * @class   YITH_WCPB_Shortcodes
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCPB_Shortcodes {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Shortcodes
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Shortcodes
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
    public function __construct() {
        add_shortcode( 'bundled_items', array( $this, 'render_bundled_items' ) );

        add_shortcode( 'bundle_add_to_cart', array( $this, 'render_bundle_add_to_cart' ) );
    }

    /**
     * Render Bundled Items
     *
     * EXAMPLE:
     * <code>
     *  [bundled_items]
     * </code>
     * this code displays the bundled items and add to cart for a bundle product
     *
     * EXAMPLE 2:
     * <code>
     *  [bundled_items type="list"]
     * </code>
     * this code displays only the bundled items for a bundle product [without add to cart]
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_bundled_items( $atts, $content = null ) {
        global $product;
        if ( !$product || !$product->is_type( 'yith_bundle' ) )
            return;

        $type = isset( $atts[ 'type' ] ) ? $atts[ 'type' ] : 'list-add-to-cart';
        ob_start();
        switch ( $type ) {
            case 'list':
                global $product;
                if ( $product->is_type( 'yith_bundle' ) ) {
                    $bundled_items = $product->get_bundled_items();
                    if ( $bundled_items ) {
                        $args = array(
                            'available_variations' => $product->get_available_bundle_variations(),
                            'attributes'           => $product->get_bundle_variation_attributes(),
                            'selected_attributes'  => $product->get_selected_bundle_variation_attributes(),
                            'bundled_items'        => $bundled_items
                        );
                        wc_get_template( '/single-product/add-to-cart/yith-bundle-items-list.php', $args, '', YITH_WCPB_TEMPLATE_PATH . '/premium' );
                    }
                }
                break;
            case 'list-add-to-cart':
            default:
                YITH_WCPB_Frontend()->woocommerce_yith_bundle_add_to_cart();
                break;

        }

        return ob_get_clean();
    }

    public function render_bundle_add_to_cart( $atts ) {
        $html = '';
        $atts = wp_parse_args( $atts, array(
            'id' => false,
        ) );

        /**
         * @var int $id
         */
        extract( $atts );
        if ( $id ) {
            global $product;
            $old_product = $product;

            $product = wc_get_product( $id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                ob_start();
                echo "<div class='product'>";
                echo "<div class='price'>" . $product->get_price_html() . "</div>";
                YITH_WCPB_Frontend()->woocommerce_yith_bundle_add_to_cart();
                echo "</div>";
                $html = ob_get_clean();
            }

            $product = $old_product;
        }

        return $html;
    }

}

/**
 * Unique access to instance of YITH_WCPB_Shortcodes class
 *
 * @return YITH_WCPB_Shortcodes
 * @since 1.0.0
 */
function YITH_WCPB_Shortcodes() {
    return YITH_WCPB_Shortcodes::get_instance();
}