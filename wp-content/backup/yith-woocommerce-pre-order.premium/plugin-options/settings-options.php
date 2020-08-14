<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

    'settings' => apply_filters( 'yith_wcpo_settings_options', array(

            'settings_general_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcpo_settings_general_start'
            ),

            'settings_general_title'    => array(
                'title' => esc_html_x( 'General settings', 'Panel: page title', 'yith-pre-order-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcpo_settings_options_title'
            ),

            'settings_enable_pre_order' => array(
                'title'   => esc_html_x( 'Enable Pre-Order on Frontend', 'Admin option: Enable plugin', 'yith-pre-order-for-woocommerce' ),
                'type'    => 'checkbox',
                'desc'    => esc_html_x( 'Uncheck this option to disable all Pre-Order features on Frontend', 'Admin option description: Enable plugin', 'yith-pre-order-for-woocommerce' ),
                'id'      => 'yith_wcpo_enable_pre_order',
                'default' => 'yes'
            ),

            'option1' => array(),

            'option2' => array(),

            'option3' => array(),

            'settings_remove_completed'  => array(
                "name"          => esc_html__( 'Remove selected order statuses from Pre-Ordered view:', 'yith-pre-order-for-woocommerce' ),
                "desc"          => esc_html__( 'Completed', 'yith-pre-order-for-woocommerce' ),
                "id"            => 'yith_wcpo_wc-completed',
                'default'       => 'yes',
                "type"          => "checkbox",
                'checkboxgroup' => 'start'
            ),

            'settings_remove_cancelled'    => array(
                'desc'          => esc_html__( 'Cancelled', 'yith-pre-order-for-woocommerce' ),
                'id'            => 'yith_wcpo_wc-cancelled',
                'default'       => 'no',
                'type'          => 'checkbox',
                'checkboxgroup' => ''
            ),

            'settings_remove_refunded'    => array(
                'desc'          => esc_html__( 'Refunded', 'yith-pre-order-for-woocommerce' ),
                'id'            => 'yith_wcpo_wc-refunded',
                'default'       => 'no',
                'type'          => 'checkbox',
                'checkboxgroup' => ''
            ),

            'settings_remove_failed' => array(
                'desc'          => esc_html__( 'Failed', 'yith-pre-order-for-woocommerce' ),
                'id'            => 'yith_wcpo_wc-failed',
                'default'       => 'no',
                'type'          => 'checkbox',
                'checkboxgroup' => 'end',
            ),

            'option4' => array(),

            'option5' => array(),

            'option6' => array(),

            'option7' => array(),

            'settings_general_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcpo_settings_general_end'
            ),

            'settings_label_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcpo_settings_label_start'
            ),

            'settings_label_title'    => array(
                'title' => esc_html_x( 'Label settings', 'Panel: page title', 'yith-pre-order-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcpo_settings_label_title'
            ),

            'settings_default_add_to_cart_label' => array(
                'title'   => esc_html_x( 'Default Add to Cart text', 'Admin option: customize Add to Cart label', 'yith-pre-order-for-woocommerce' ),
                'type'    => 'text',
                'desc'    => esc_html_x( 'This text will be replaced on \'Add to Cart\' button. By leaving it blank, it will be \'Pre-Order Now\'.', 'Admin option description: customize Add to Cart label', 'yith-pre-order-for-woocommerce' ),
                'id'      => 'yith_wcpo_default_add_to_cart_label',
                'default' => esc_html_x( 'Pre-Order Now', 'Default label for add to cart button(Pre-Order Now)', 'yith-pre-order-for-woocommerce' )
            ),

            'option8' => array(),

            'option9' => array(),

            'option10' => array(),

            'option11' => array(),

            'option12' => array(),

            'option13' => array(),

            'option14' => array(),

            'option15' => array(),

            'option16' => array(),

            'option17' => array(),

            'settings_label_end' => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcpo_settings_label_end'
            ),

        )
    )
);