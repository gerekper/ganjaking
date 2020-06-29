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
 * Outputs a custom table template for manage coupon creation in plugin options panel
 *
 * @class   YWCES_Custom_Coupon
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWCES_Custom_Coupon {

    /**
     * Single instance of the class
     *
     * @var \YWCES_Custom_Coupon
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YWCES_Custom_Coupon
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

        add_action( 'woocommerce_admin_field_ywces-coupon', array( $this, 'output' ) );

    }

    /**
     * Outputs a custom table template for manage coupon creation in plugin options panel
     *
     * @since   1.0.0
     *
     * @param   $option
     *
     * @return  void
     * @author  Alberto Ruggiero
     */
    public function output( $option ) {

        $option_value = WC_Admin_Settings::get_option( $option['id'], $option['default'] );

        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
            </th>
            <td class="forminp" id="<?php echo esc_attr( $option['id'] ); ?>_settings">

                <div class="ywces-coupon">

                    <p class="form-row form-row-first">
                        <label for="<?php echo esc_attr( $option['id'] ); ?>[discount_type]">
                            <?php esc_html_e( 'Discount type', 'woocommerce' ) ?>
                        </label>
                        <select
                            name="<?php echo esc_attr( $option['id'] ); ?>[discount_type]"
                            id="<?php echo esc_attr( $option['id'] ); ?>[discount_type]"
                            class="short ywces-discount-type">
                            <?php
                            $options = wc_get_coupon_types();

                            foreach ( $options as $key => $val ) {
                                ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php

                                if ( is_array( $option_value['discount_type'] ) ) {
                                    selected( in_array( $key, $option_value['discount_type'] ), true );
                                }
                                else {
                                    selected( $option_value['discount_type'], $key );
                                }

                                ?>><?php echo $val ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </p>

                    <p class="form-row form-row-last">
                        <label for="<?php echo esc_attr( $option['id'] ); ?>[coupon_amount]">
                            <?php esc_html_e( 'Coupon amount', 'woocommerce' ) ?>
                        </label>
                        <input
                            type="text"
                            class="wc_input_price ywces-coupon-amount"
                            name="<?php echo esc_attr( $option['id'] ); ?>[coupon_amount]"
                            id="<?php echo esc_attr( $option['id'] ); ?>[coupon_amount]"
                            value="<?php echo $option_value['coupon_amount'] ?>"
                            placeholder="<?php echo wc_format_localized_price( 0 ); ?>"
                            />
                    </p>

                    <p class="form-row form-row-first">
                        <label for="<?php echo esc_attr( $option['id'] ); ?>[expiry_days]">
                            <?php esc_html_e( 'Expiry days after coupon release', 'woocommerce' ) ?>
                        </label>
                        <input
                            type="number"
                            class="ywces-expiry-days"
                            name="<?php echo esc_attr( $option['id'] ); ?>[expiry_days]"
                            id="<?php echo esc_attr( $option['id'] ); ?>[expiry_days]"
                            value="<?php echo $option_value['expiry_days'] ?>"
                            placeholder="<?php esc_html_e( 'No expiration', 'woocommerce' ) ?>"
                            min="1"
                            />
                    </p>

                    <p class="form-row form-row-first">
                        <label for="<?php echo esc_attr( $option['id'] ); ?>[minimum_amount]">
                            <?php esc_html_e( 'Minimum spend', 'woocommerce' ) ?>
                        </label>
                        <input
                            type="text"
                            class="wc_input_price ywces-minimum-amount"
                            name="<?php echo esc_attr( $option['id'] ); ?>[minimum_amount]"
                            id="<?php echo esc_attr( $option['id'] ); ?>[minimum_amount]"
                            value="<?php echo $option_value['minimum_amount']; ?>"
                            placeholder="<?php esc_html_e( 'No minimum', 'woocommerce' ) ?>"
                            />
                    </p>

                    <p class="form-row form-row-last">
                        <label for="<?php echo esc_attr( $option['id'] ); ?>[maximum_amount]">
                            <?php esc_html_e( 'Maximum spend', 'woocommerce' ) ?>
                        </label>
                        <input
                            type="text"
                            class="wc_input_price ywces-maximum-amount"
                            name="<?php echo esc_attr( $option['id'] ); ?>[maximum_amount]"
                            id="<?php echo esc_attr( $option['id'] ); ?>[maximum_amount]"
                            value="<?php echo $option_value['maximum_amount']; ?>"
                            placeholder="<?php esc_html_e( 'No maximum', 'woocommerce' ) ?>"
                            />
                    </p>

                    <p class="form-row form-row-full checkboxes">
                        <label>
                            <input
                                type="checkbox"
                                class="checkbox ywces-free-shipping"
                                name="<?php echo esc_attr( $option['id'] ); ?>[free_shipping]"
                                id="<?php echo esc_attr( $option['id'] ); ?>[free_shipping]"
                                value="1"
                                <?php checked( ( isset( $option_value['free_shipping'] ) ? $option_value['free_shipping'] : '0' ), '1' ); ?>
                                />
                            <?php _e( 'Allow free shipping', 'woocommerce' ) ?>
                        </label>

                        <label>
                            <input
                                type="checkbox"
                                class="checkbox ywces-individual-use"
                                name="<?php echo esc_attr( $option['id'] ); ?>[individual_use]"
                                id="<?php echo esc_attr( $option['id'] ); ?>[individual_use]"
                                value="1"
                                <?php checked( ( isset( $option_value['individual_use'] ) ? $option_value['individual_use'] : '0' ), '1' ); ?>
                                />
                            <?php _e( 'Individual use only', 'woocommerce' ) ?>
                        </label>

                        <label>
                            <input
                                type="checkbox"
                                class="checkbox ywces-exclude-sale-items"
                                name="<?php echo esc_attr( $option['id'] ); ?>[exclude_sale_items]"
                                id="<?php echo esc_attr( $option['id'] ); ?>[exclude_sale_items]"
                                value="1"
                                <?php checked( ( isset( $option_value['exclude_sale_items'] ) ? $option_value['exclude_sale_items'] : '0' ), '1' ); ?>
                                />
                            <?php _e( 'Exclude sale items', 'woocommerce' ) ?>
                        </label>
                    </p>

                </div>
            </td>
        </tr>
    <?php
    }

}

/**
 * Unique access to instance of YWCES_Custom_Coupon class
 *
 * @return \YWCES_Custom_Coupon
 */
function YWCES_Custom_Coupon() {

    return YWCES_Custom_Coupon::get_instance();

}

new YWCES_Custom_Coupon();