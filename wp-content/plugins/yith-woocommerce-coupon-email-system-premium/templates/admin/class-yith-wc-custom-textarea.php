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

if ( !class_exists( 'YITH_WC_Custom_Textarea' ) ) {

    /**
     * Outputs a custom textarea template in plugin options panel
     *
     * @class   YITH_WC_Custom_Textarea
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     *
     */
    class YITH_WC_Custom_Textarea {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Custom_Textarea
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Custom_Textarea
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

            add_action( 'woocommerce_admin_field_yith-wc-textarea', array( $this, 'output' ) );
            add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'save' ), 10, 3 );

        }

        /**
         * Outputs a custom textarea template in plugin options panel
         *
         * @since   1.0.0
         *
         * @param   $option
         *
         * @author  Alberto Ruggiero
         * @return  void
         */
        public function output( $option ) {

            $custom_attributes = array();

            $style = 'resize: vertical; width: 100%; min-height: 40px;';

            if ( !empty( $option['css'] ) ) {

                $style = esc_attr( $option['css'] );
            }

            if ( !empty( $option['class'] ) ) {

                $style = '';
            }

            if ( !empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
                foreach ( $option['custom_attributes'] as $attribute => $attribute_value ) {
                    $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
                }
            }

            $option_value = WC_Admin_Settings::get_option( $option['id'], $option['default'] );

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">

                    <textarea
                        name="<?php echo esc_attr( $option['id'] ); ?>"
                        id="<?php echo esc_attr( $option['id'] ); ?>"
                        style="<?php echo $style; ?>"
                        class="<?php echo esc_attr( $option['class'] ); ?>"
                        <?php echo implode( ' ', $custom_attributes ); ?>
                        ><?php echo esc_textarea( $option_value ); ?></textarea>
                    <span class="description"><?php echo $option['desc']; ?></span>
                </td>
            </tr>
        <?php
        }

        /**
         * Saves custom textarea content
         *
         * @since   1.0.0
         *
         * @param $value
         * @param $option
         * @param $raw_value
         *
         * @return string
         * @author  Alberto ruggiero
         */
        public function save( $value, $option, $raw_value ) {

            if ( $option['type'] == 'yith-wc-textarea' ) {

                $value = wp_kses_post( trim( $raw_value ) );

            }

            return $value;

        }

    }

    /**
     * Unique access to instance of YITH_WC_Custom_Textarea class
     *
     * @return \YITH_WC_Custom_Textarea
     */
    function YITH_WC_Custom_Textarea() {

        return YITH_WC_Custom_Textarea::get_instance();

    }

    new YITH_WC_Custom_Textarea();

}