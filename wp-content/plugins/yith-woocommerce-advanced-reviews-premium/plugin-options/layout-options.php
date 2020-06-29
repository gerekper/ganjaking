<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


return array (

    'layout' => array (

        'reviews_summary_section_title'          => array (
            'name' => esc_html__( 'Layout options', 'yith-woocommerce-advanced-reviews' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywar_settings_review_summary_title',
        ),
        'review_summary_bar_color'               => array (
            'name'    => esc_html__( 'Fixed background bar color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
            'desc'    => '',
            'id'      => 'ywar_summary_bar_color',
            'default' => '#f4f4f4',
        ),
        'reviews_summary_percentage_bar_color'   => array (
            'name'    => esc_html__( 'Percentage bar color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
            'desc'    => '',
            'id'      => 'ywar_summary_percentage_bar_color',
            'default' => '#a9709d',
        ),
        'reviews_summary_percentage_value'       => array (
            'name'    => esc_html__( 'Show percentage value', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'desc'    => esc_html__( 'Show % value on percentage bars.', 'yith-woocommerce-advanced-reviews' ),
            'id'      => 'ywar_summary_percentage_value',
            'default' => 'yes',
        ),
        'ywar_summary_percentage_title' => array (
	        'name'    => esc_html__( 'Rating label color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
	        'desc'    => '',
	        'id'      => 'ywar_summary_rating_label_color',
	        'default' => '#a9709d',
        ),
        'ywar_summary_percentage_value_color' => array (
            'name'    => esc_html__( 'Percentage value color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
            'desc'    => '',
            'id'      => 'ywar_summary_percentage_value_color',
            'default' => '#a9709d',
        ),
        'ywar_summary_count_value' => array (
	        'name'    => esc_html__( 'Rating count color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
	        'desc'    => '',
	        'id'      => 'ywar_summary_count_color',
            'default' => '#000000',

        ),
        'ywar_tab_bottom_border' => array (
            'name'    => esc_html__( 'Tabs bottom border color', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'colorpicker',
            'desc'    => '',
            'id'      => 'ywar_tab_bottom_border_color',
            'default' => '#a9709d',
        ),
        'reviews_summary_end'                    => array (
            'type' => 'sectionend',
            'id'   => 'ywar_settings_reviews_summary_end',
        ),
    ),
);