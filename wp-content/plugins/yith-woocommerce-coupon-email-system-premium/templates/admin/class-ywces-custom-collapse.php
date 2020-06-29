<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Outputs a custom control for expand/collapse the table in plugin options panel
 *
 * @class   YWCES_Custom_Collapse
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWCES_Custom_Collapse {

    /**
     * Single instance of the class
     *
     * @var \YWCES_Custom_Collapse
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YWCES_Custom_Collapse
     * @since 1.0.0
     */
    public static function get_instance() {

        if ( is_null( self::$instance ) ) {

            self::$instance = new self( $_REQUEST );

        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since   1.0.0
     * @return  mixed
     * @author  Alberto Ruggiero
     */
    public function __construct() {

        add_action( 'woocommerce_admin_field_ywces-collapse', array( $this, 'output' ) );

    }

    /**
     * Outputs a custom control for expand/collapse the table in plugin options panel
     *
     * @since   1.0.0
     *
     * @param   $option
     *
     * @return  void
     * @author  Alberto Ruggiero
     */
    public function output( $option ) {

        ?>
        <tr valign="top" class="ywces-collapse">
            <th></th>
            <td>
                <?php esc_html_e( 'Click to collapse/expand the table', 'yith-woocommerce-coupon-email-system' ) ?>

                <span class="ywces-collapse-sign"></span>
                <span class="ywces-collapse-collapsed">
                    <?php esc_html_e( 'Expand', 'yith-woocommerce-coupon-email-system' ) ?>
                </span>
                <span class="ywces-collapse-expanded">
                    <?php esc_html_e( 'Collapse', 'yith-woocommerce-coupon-email-system' ) ?>
                </span>
            </td>
        </tr>
    <?php
    }

}

/**
 * Unique access to instance of YWCES_Custom_Collapse class
 *
 * @return \YWCES_Custom_Collapse
 */
function YWCES_Custom_Collapse() {

    return YWCES_Custom_Collapse::get_instance();

}

new YWCES_Custom_Collapse();