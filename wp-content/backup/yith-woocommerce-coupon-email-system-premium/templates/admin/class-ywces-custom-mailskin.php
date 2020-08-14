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
 * Implements a custom select in YWCES plugin admin tab
 *
 * @class   YWCES_Mail_Skin
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWCES_Mail_Skin {

    /**
     * Single instance of the class
     *
     * @var \YWCES_Mail_Skin
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YWCES_Mail_Skin
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

        add_action( 'woocommerce_admin_field_ywces-mailskin', array( $this, 'output' ) );

    }

    /**
     * Implements a custom select in YWCES plugin admin tab
     *
     * @since   1.0.0
     * @return  void
     * @author  Alberto Ruggiero
     */
    public function output( $option ) {

        $custom_attributes = array();

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
                <select
                    name="<?php echo esc_attr( $option['id'] ); ?><?php if ( $option['type'] == 'multiselect' ) {
                        echo '[]';
                    } ?>"
                    id="<?php echo esc_attr( $option['id'] ); ?>"
                    style="<?php echo esc_attr( $option['css'] ); ?>"
                    class="<?php echo esc_attr( $option['class'] ); ?>"
                    <?php echo implode( ' ', $custom_attributes ); ?>
                    <?php echo ( 'multiselect' == $option['type'] ) ? 'multiple="multiple"' : ''; ?>
                    >
                    <?php
                    foreach ( $option['options'] as $key => $val ) {
                        ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php

                        if ( is_array( $option_value ) ) {
                            selected( in_array( $key, $option_value ), true );
                        }
                        else {
                            selected( $option_value, $key );
                        }

                        ?>><?php echo $val ?></option>
                    <?php
                    }
                    ?>
                </select>
                <span class="description">
                    <?php echo $option['desc']; ?>
                </span>

                <div class="ywces-mailskin">
                    <img src="<?php echo YWCES_ASSETS_URL . '/images/skins/' . $option_value ?>.png" />
                </div>
                <script type="text/javascript">
                    jQuery(function ($) {

                        $(document).ready(function () {

                            $('select#<?php echo esc_attr( $option['id'] ); ?>').change(function () {

                                var skin = $(this).val(),
                                    preview = $('.ywces-mailskin img');

                                preview.fadeOut('slow', function () {

                                    preview.attr('src', '<?php echo YWCES_ASSETS_URL; ?>/images/skins/' + skin + '.png').fadeIn('slow');

                                });


                            }).change();

                        });

                    });
                </script>
            </td>
        </tr>
    <?php
    }

}

/**
 * Unique access to instance of YWCES_Mail_Skin class
 *
 * @return \YWCES_Mail_Skin
 */
function YWCES_Mail_Skin() {

    return YWCES_Mail_Skin::get_instance();

}

new YWCES_Mail_Skin();