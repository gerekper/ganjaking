<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCGPF_EU_Energy_Label_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCGPF_EU_Energy_Label_Compatibility' ) ) {

    class YITH_WCGPF_EU_Energy_Label_Compatibility
    {
        public function __construct()
        {
            add_filter('yith_wcgpf_values_in_feed', array($this,'energy_efficiency_feed'),10,4);
        }

        public function energy_efficiency_feed( $value,$field,$product_fields, $product ) {

            if( 'energy_efficiency_class' == $field && apply_filters('yith_wcgpf_show_energy_efficiency_class_yith_plugin',true)) {

                $energy_label = yit_get_prop($product,'_yith_wceue_eu_energy_label',true);
                if ($energy_label) {
                    $yith_wceue_get_energy_label_array = yith_wceue_get_energy_label_array();
                    $value = $yith_wceue_get_energy_label_array[$energy_label];
                }
            }

            return $value;
        }

    }
}

return new YITH_WCGPF_EU_Energy_Label_Compatibility();