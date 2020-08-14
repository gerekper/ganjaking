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
 * Outputs a custom checkbox template with button for immediate action plugin options panel
 *
 * @class   YWCES_Custom_Coupon_Purge
 * @package Yithemes
 * @since   1.0.5
 * @author  Your Inspiration Themes
 *
 */
class YWCES_Custom_Coupon_Purge {

    /**
     * Single instance of the class
     *
     * @var \YWCES_Custom_Coupon_Purge
     * @since 1.0.5
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YWCES_Custom_Coupon_Purge
     * @since 1.0.5
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
     * @since   1.0.5
     * @return  mixed
     * @author  Alberto Ruggiero
     */
    public function __construct() {

        add_action( 'woocommerce_admin_field_ywces-coupon-purge', array( $this, 'output' ) );
        add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'save' ), 10, 3 );

    }

    /**
     * Outputs a custom checkbox template with button for immediate action plugin options panel
     *
     * @since   1.0.5
     *
     * @param   $option
     *
     * @return  void
     * @author  Alberto Ruggiero
     */
    public function output( $option ) {

        $option_value = WC_Admin_Settings::get_option( $option['id'], $option['default'] );

        $custom_attributes = array();

        if ( !empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
            foreach ( $option['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <?php echo esc_html( $option['title'] ); ?>
            </th>
            <td class="forminp forminp-checkbox">
                <fieldset>
                    <?php if ( !empty( $option['title'] ) ) : ?>
                        <legend class="screen-reader-text"><span><?php echo esc_html( $option['title'] ) ?></span>
                        </legend>
                    <?php endif; ?>
                    <label for="<?php echo $option['id'] ?>">
                        <input
                            name="<?php echo esc_attr( $option['id'] ); ?>"
                            id="<?php echo esc_attr( $option['id'] ); ?>"
                            type="checkbox"
                            class="<?php echo esc_attr( isset( $option['class'] ) ? $option['class'] : '' ); ?>"
                            value="1"
                            <?php checked( $option_value, 'yes' ); ?>
                            <?php echo implode( ' ', $custom_attributes ); ?>
                        /> <?php echo $option['desc'] ?>
                    </label>
                </fieldset>
                <button type="button" class="button-secondary ywces-purge-coupon"><?php esc_html_e( 'Delete expired coupons', 'yith-woocommerce-coupon-email-system' ); ?></button>
                <div class="ywces-clear-result clear-progress"><?php esc_html_e( 'Deleting expired coupons...', 'yith-woocommerce-coupon-email-system' ); ?></div>
            </td>
        </tr>
        <?php
    }

    /**
     * Saves custom checkbox content
     *
     * @since   1.0.5
     *
     * @param   $value
     * @param   $option
     * @param   $raw_value
     *
     * @return  string
     * @author  Alberto Ruggiero
     */
    public function save( $value, $option, $raw_value ) {

        if ( $option['type'] == 'ywces-coupon-purge' ) {

            $value = is_null( $raw_value ) ? 'no' : 'yes';

        }

        return $value;

    }

}

/**
 * Unique access to instance of YWCES_Custom_Coupon_Purge class
 *
 * @return \YWCES_Custom_Coupon_Purge
 */
function YWCES_Custom_Coupon_Purge() {

    return YWCES_Custom_Coupon_Purge::get_instance();

}

new YWCES_Custom_Coupon_Purge();