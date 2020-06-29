<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Auctions Compatibility Class
 *
 * @class   YITH_WCBM_Auctions_Compatibility
 * @package Yithemes
 * @since   1.2.23
 * @author  Yithemes
 *
 */
class YITH_WCBM_Auctions_Compatibility {

    /**
     * Single instance of the class
     *
     * @var YITH_WCBM_Auctions_Compatibility
     */
    protected static $_instance;


    /**
     * Returns single instance of the class
     *
     * @return YITH_WCBM_Auctions_Compatibility
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct() {
        add_filter( 'yith_wcbm_settings_admin_tabs', array( $this, 'add_admin_tabs' ) );

        add_filter( 'yith_wcmb_get_badges_premium', array( $this, 'add_auction_badges' ), 10, 2 );
    }

    /**
     * @param string $badge_html
     * @param WC_Product    $product
     *
     * @return string
     */
    public function add_auction_badges( $badge_html, $product ) {
        $product = wc_get_product( $product );
        if ( $product && $product->is_type( 'auction' ) ) {
            $auction_status = $product->get_auction_status();

            switch ( $auction_status ) {
                case 'started':
                case 'started-reached-reserve':
                    $status = 'started';
                    break;
                case 'finished':
                case 'finnish-buy-now':
                case 'finished-reached-reserve':
                    $status = 'finished';
                    break;
                default:
                    $status = 'not-started';
                    break;

            }

            $auction_badge = get_option( 'yith-wcbm-auction-badge-' . $status );

            if ( !empty( $auction_badge ) && $auction_badge != 'none' ) {
                $badge_html .= yith_wcbm_get_badge_premium( $auction_badge, $product->get_id() );
            }
        }

        return $badge_html;
    }

    /**
     * Add Admin Setting Tabs
     *
     * @param $admin_tabs_free
     *
     * @return mixed
     */
    public function add_admin_tabs( $admin_tabs_free ) {
        $admin_tabs_free[ 'auctions' ] = __( 'Auctions', 'yith-woocommerce-badges-management' );

        return $admin_tabs_free;
    }

}