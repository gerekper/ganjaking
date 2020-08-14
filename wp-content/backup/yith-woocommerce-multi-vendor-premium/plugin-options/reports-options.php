<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return apply_filters( 'yith_wcqw_panel_reports_options', array(

        'reports' => array(

            'reports_options_start' => array(
                'type' => 'sectionstart',
            ),

            'reports_options_title' => array(
                'title' => __( 'Report settings.', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'id'    => 'yith_wpv_reports_options_title'
            ),

            'reports_limit'         => array(
                'id'      => 'yith_wpv_reports_limit',
                'type'    => 'select',
                'title'   => __( 'Vendor Report limit', 'yith-woocommerce-product-vendors' ),
                'desc'    => __( 'Choose the maximum number of rows you can show in the reports', 'yith-woocommerce-product-vendors' ),
                'options' => array(
                    1  => __( '10', 'yith-woocommerce-product-vendors' ),
                    2  => __( '20', 'yith-woocommerce-product-vendors' ),
                    '' => __( 'All', 'yith-woocommerce-product-vendors' ),
                ),
                'default' => 10
            ),
            'reports_options_end'   => array(
                'type' => 'sectionend',
            ),
        ),
    )
);