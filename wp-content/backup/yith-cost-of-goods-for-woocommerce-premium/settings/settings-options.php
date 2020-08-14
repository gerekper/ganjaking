<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


$order_status = array( esc_html__( 'Completed', 'yith-cost-of-goods-for-woocommerce' ), esc_html__( 'Processing', 'yith-cost-of-goods-for-woocommerce' ), esc_html__( 'On-hold', 'yith-cost-of-goods-for-woocommerce' ) );

return array(

    'settings' => apply_filters( 'yith_cog_settings_options', array(

            /* YITH CoG Settings Section. */
            'settings_tab_settings_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_settings_tab_start'
            ),

            'settings_tab_settings_options_title'    => array(
                'title' => esc_html__( 'General settings', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_cog_settings_tab_title'
            ),
            'settings_tab_fees' => array(
                'title'   => esc_html__( 'Include orders fees', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'The cost related to the orders fees will be included in the total product cost', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_settings_tab_fees',
                'default' => 'no'
            ),
            'settings_tab_shipping' => array(
                'title'   => esc_html__( 'Include shipping total cost', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'Shipping costs will be included in the total product cost', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_settings_tab_shipping',
                'default' => 'no'
            ),

            'settings_tab_tax' => array(
                'title'   => esc_html__( 'Include taxes cost for each product', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'Tax costs will be included in the total product cost', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_settings_tab_tax',
                'default' => 'no'
            ),

            'settings_tab_settings_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_settings_tab_end'
            ),



            /* Apply Costs to Previous Orders Section. */
            'previous_orders_tab_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_previous_orders_settings_tab_start'
            ),
            'previous_orders_tab_title'    => array(
                'title' => esc_html__( 'Apply costs to previous orders', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_cog_previous_orders_tab'
            ),
            'previous_orders_tab_no_costs_set' => array(
                'title'   => '',
                'desc'    => '',
                'id'      => '',
                'type'  => 'yith_cog_apply_cost_html',
                'html'  => '',
            ),
            'previous_orders_tab_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_settings_tab_end'
            ),

            /* Import Costs from WooCommerce Section. */
            'import_cost_tab_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_previous_orders_settings_tab_start'
            ),
            'import_cost_tab_title'    => array(
                'title' => esc_html__( 'Import Cost of Goods from WooCommerce', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_cog_import_cost_tab'
            ),
            'import_cost_tab_button' => array(
                'title'   => '',
                'desc'    => '',
                'id'      => '',
                'type'  => 'yith_cog_import_cost_html',
                'html'  => '',
            ),
            'import_cost_tab_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_settings_tab_end'
            ),




            /* Add columns settings. */
            'add_columns_tab_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_add_columns_settings_tab_start'
            ),
            'add_columns_tab_title'    => array(
                'title' => esc_html__( 'Add custom columns to the report', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_cog_add_columns_tab'
            ),
            'add_columns_custom_fields' => array(
                'title'   => esc_html__( 'Add custom field', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'text',
                'placeholder' => esc_html__( 'Write a Custom Field name', 'yith-cost-of-goods-for-woocommerce' ),
                'desc'    => esc_html__( 'Add a custom field to the report table (Separate them with commas for different columns).', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_add_columns',
                'default' => ''
            ),
            'settings_tab_tag_column' => array(
                'title'   => esc_html__( 'Add a tag column to report.', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'Show a tag column for products in the report table', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_tag_column',
                'default' => 'no'
            ),
            'settings_tab_percentage_column' => array(
                'title'   => esc_html__( 'Add a percentage margin column to report.', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'Show a percentage margin column for products in the report table', 'yith-cost-of-goods-for-woocommerce' ),
                'id'      => 'yith_cog_percentage_column',
                'default' => 'no'
            ),
            'add_columns_tab_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_settings_tab_end'
            ),


            /* Add columns settings. */
            'pagination_settings_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_pagination_settings_start'
            ),
            'set_pagination_section_title'    => array(
                'title' => esc_html__( 'Set the pagination to the reports', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_pagination_settings_title'
            ),
            'set_pagination_report_table'    => array(
                'title' => esc_html__( 'Number of items per page in the Report table', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'number',
                'desc'  => '',
                'id'    => 'yith_cog_set_pagination_report_table',
                'min'     => 0,
                'step'    => 1,
                'default' => 20

            ),
            'set_pagination_stock_table' => array(
                'title'   => esc_html__( 'Number of items per page in the Stock table', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'number',
                'desc'  => '',
                'id'      => 'yith_cog_set_pagination_stock_table',
                'min'     => 0,
                'step'    => 1,
                'default' => 20
            ),
            'pagination_settings_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_pagination_settings_end'
            ),

            /* Add order status settings. */
            'order_status_settings_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_order_status_settings_start'
            ),
            'order_status_section_title'    => array(
                'title' => esc_html__( 'Order Status Settings', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_order_status_settings_title'
            ),
            'order_status_settings'    => array(
                'title' => esc_html__( 'Select the minimum order status required to display the data in the reports', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'select',
                'options'  => $order_status,
                'desc'  => '',
                'id'    => 'yith_cog_order_status_report',
                'default' => 'yes'
            ),
            'order_status_settings_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_order_status_settings_end'
            ),



            /* Add currency settings. */
            'currency_settings_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_currency_settings_start'
            ),
            'currency_section_title'    => array(
                'title' => esc_html__( 'Currency Settings', 'yith-cost-of-goods-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_currency_settings_title'
            ),
            'currency_settings'    => array(
                'title' => esc_html__( 'Display the currency symbol in the reports', 'yith-cost-of-goods-for-woocommerce' ),
                'type'    => 'yith-field',
                'yith-type' => 'onoff',
                'desc'    => esc_html__( 'If this option is disable, the report will not display the currency symbol of the values', 'yith-cost-of-goods-for-woocommerce' ),
                'id'    => 'yith_cog_currency_report',
                'default' => 'yes'

            ),
            'currency_settings_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_currency_settings_end'
            ),



        )
    )
);


