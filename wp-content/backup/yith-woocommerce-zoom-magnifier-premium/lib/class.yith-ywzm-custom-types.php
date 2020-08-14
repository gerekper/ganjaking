<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('YITH_YWZM_Custom_Types')) {

    /**
     * custom types fields
     *
     * @class YITH_YWZM_Custom_Types
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     */
    class YITH_YWZM_Custom_Types
    {

        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            /**
             * Register actions and filters for custom types used on the current plugin
             */

            /** Custom types : slider */
            add_action('woocommerce_admin_field_slider', array($this, 'admin_fields_slider'));

            add_action('woocommerce_admin_field_yith_ywzm_image_width', array(
                $this,
                'admin_fields_yith_ywzm_image_width'
            ));
        }

        /**
         * Create new Woocommerce admin field: slider
         *
         * @access public
         *
         * @param array $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_fields_slider($value)
        {
            $slider_value = (get_option($value['id']) !== false && get_option($value['id']) !== null) ?
                esc_attr(stripslashes(get_option($value['id']))) :
                esc_attr($value['std']);

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr($value['id']); ?>"><?php echo $value['name']; ?></label>
                </th>
                <td class="forminp">
                    <div id="<?php echo esc_attr($value['id']); ?>_slider" class="yith_woocommerce_slider"
                         style="width: 300px; float: left;"></div>
                    <div id="<?php echo esc_attr($value['id']); ?>_value"
                         class="yith_woocommerce_slider_value ui-state-default ui-corner-all"><?php echo $slider_value ?></div>
                    <input name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>"
                           type="hidden" value="<?php echo $slider_value ?>"/> <?php echo $value['desc']; ?>
                </td>
            </tr>


            <script>
                jQuery(document).ready(function ($) {
                    $('#<?php echo esc_attr( $value['id'] ); ?>_slider').slider({
                        min: <?php echo $value['min'] ?>,
                        max: <?php echo $value['max'] ?>,
                        step: <?php echo $value['step'] ?>,
                        value: <?php echo $slider_value ?>,
                        slide: function (event, ui) {
                            $("#<?php echo esc_attr( $value['id'] ); ?>").val(ui.value);
                            $("#<?php echo esc_attr( $value['id'] ); ?>_value").text(ui.value);
                        }
                    });
                });
            </script>

            <?php
        }

        /**
         * Save the admin field: slider
         *
         * @access public
         *
         * @param mixed $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_update_option($value)
        {
            global $woocommerce;

            if (version_compare(preg_replace('/-beta-([0-9]+)/', '', $woocommerce->version), '2.1', '<')) {
                $wc_clean = 'woocommerce_clean';
            } else {
                $wc_clean = 'wc_clean';
            }

            update_option($value['id'], woocommerce_clean($_POST[$value['id']]));
        }


        /**
         * Create new Woocommerce admin field: yith_ywzm_image_width
         *
         * @access public
         * @param array $value
         * @return void
         * @since 1.1.3
         */
        public function admin_fields_yith_ywzm_image_width($value)
        {

            $width = WC_Admin_Settings::get_option($value['id'] . '[width]', $value['default']['width']);
            $height = WC_Admin_Settings::get_option($value['id'] . '[height]', $value['default']['height']);
            $crop = WC_Admin_Settings::get_option($value['id'] . '[crop]');
            $crop = ($crop == 'on' || $crop == '1') ? 1 : 0;
            $crop = checked(1, $crop, false);

            ?>
            <tr valign="top">
            <th scope="row" class="titledesc"><?php echo esc_html($value['title']) ?></th>
            <td class="forminp image_width_settings">
                <input name="<?php echo esc_attr($value['id']); ?>[width]"
                       id="<?php echo esc_attr($value['id']); ?>-width" type="text" size="3"
                       value="<?php echo $width; ?>"/> &times; <input
                    name="<?php echo esc_attr($value['id']); ?>[height]"
                    id="<?php echo esc_attr($value['id']); ?>-height" type="text" size="3"
                    value="<?php echo $height; ?>"/>px <span class="description"><?php echo $value['desc'] ?></span>
                <br>
                <label><input name="<?php echo esc_attr($value['id']); ?>[crop]"
                              id="<?php echo esc_attr($value['id']); ?>-crop"
                              type="checkbox" <?php echo $crop; ?> /> <?php esc_html_e('Do you want to hard crop the image?', 'yith-woocommerce-zoom-magnifier'); ?>
                </label>

            </td>
            </tr><?php

        }

        /**
         * Update plugin options.
         *
         * @return void
         * @since 1.0.0
         */
        public function update_options()
        {
            foreach ($this->options as $option) {
                woocommerce_update_options($option);
            }
        }

    }
}