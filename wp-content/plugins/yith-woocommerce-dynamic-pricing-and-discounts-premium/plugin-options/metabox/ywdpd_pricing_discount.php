<?php
/**
 * Pricing discount metabox options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$key                   = uniqid();
$discount_pricing_mode = ywdpd_discount_pricing_mode();
$last_priority         = ywdpd_get_last_priority( 'pricing' ) + 1;
$pricing_rules_options = YITH_WC_Dynamic_Pricing()->pricing_rules_options;

return apply_filters(
	'ywdpd_pricing_discount_metabox_options',
	array(
		'label'    => __( 'Pricing Discount Settings', 'ywdpd' ),
		'pages'    => 'ywdpd_discount', // or array( 'post-type1', 'post-type2').
		'context'  => 'normal', // ('normal', 'advanced', or 'side').
		'priority' => 'default',
		'tabs'     => array(

			'settings' => array(
				'label'  => __( 'Settings', 'ywdpd' ),
				'fields' => apply_filters(
					'ywdpd_pricing_discount_metabox',
					array(
						'discount_type'                    => array(
							'type' => 'hidden',
							'std'  => 'pricing',
							'val'  => 'pricing',
						),
						'key'                              => array(
							'type' => 'hidden',
							'std'  => $key,
							'val'  => $key,
						),
						'active'                           => array(
							'label' => __( 'Active', 'ywdpd' ),
							'desc'  => __( 'Choose if activate or deactivate', 'ywdpd' ),
							'type'  => 'onoff',
							'std'   => 'yes',
						),
						// @since 1.1.0
						'discount_mode'                    => array(
							'label'   => __( 'Discount mode', 'ywdpd' ),
							'desc'    => '',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => array(
								'bulk'          => __( 'Quantity Discount', 'ywdpd' ),
								'special_offer' => __( 'Special Offer', 'ywdpd' ),
								'gift_products' => __( 'Gift Products', 'ywdpd' ),
								'exclude_items' => __( 'Exclude items from rules', 'ywdpd' ),
							),
							'std'     => 'bulk',
						),
						'show_table_price'                 => array(
							'label' => __( 'Show price table', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'onoff',
							'std'   => 'no',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk',
							),
						),
						'show_in_loop'                     => array(
							'label' => __( 'Show in loop', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'onoff',
							'std'   => 'no',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk',
							),
						),
						'priority'                         => array(
							'label' => __( 'Priority', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'text',
							'std'   => $last_priority,
						),
						'quantity_based'                   => array(
							'label'   => __( 'Quantity Based', 'ywdpd' ),
							'desc'    => '',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => array(
								'cart_line'                => __( 'Item quantity in cart line', 'ywdpd' ),
								'single_product'           => __( 'Single product', 'ywdpd' ),
								'single_variation_product' => __( 'Single product variation', 'ywdpd' ),
								'cumulative'               => __( 'Sum of all products in list or category list', 'ywdpd' ),
							),
							'std'     => 'cart_line',
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),


						'schedule_from'                    => array(
							'label' => __( 'Discount Schedule from', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'text',
						),
						'schedule_to'                      => array(
							'label' => __( 'Discount Schedule to', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'text',
						),
						/***************
						 * USER RULES
						 */
						'user_rules'                       => array(
							'label'   => __( 'User Status', 'ywdpd' ),
							'desc'    => '',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => array(
								'everyone'                => __( 'Everyone', 'ywdpd' ),
								'role_list'               => __( 'Include a list of roles', 'ywdpd' ),
								'role_list_excluded'      => __( 'Exclude a list of roles', 'ywdpd' ),
								'customers_list'          => __( 'Include a list of customers', 'ywdpd' ),
								'customers_list_excluded' => __( 'Exclude a list of customers', 'ywdpd' ),
							),
							'std'     => 'bulk',
						),
						'user_rules_role_list'             => array(
							'label'    => __( 'Select Roles', 'ywdpd' ),
							'desc'     => '',
							'type'     => 'select',
							'class'    => 'wc-enhanced-select',
							'multiple' => true,
							'options'  => YITH_WC_Dynamic_Pricing_Helper()->get_roles(),
							'std'      => array(),
							'deps'     => array(
								'ids'    => '_user_rules',
								'values' => 'role_list',
							),
						),
						'user_rules_role_list_excluded'    => array(
							'label'    => __( 'Select roles to exclude', 'ywdpd' ),
							'desc'     => '',
							'type'     => 'select',
							'class'    => 'wc-enhanced-select',
							'multiple' => true,
							'options'  => YITH_WC_Dynamic_Pricing_Helper()->get_roles(),
							'std'      => array(),
							'deps'     => array(
								'ids'    => '_user_rules',
								'values' => 'role_list_excluded',
							),
						),
						'user_rules_customers_list'        => array(
							'label'       => __( 'Select customers', 'ywdpd' ),
							'type'        => 'customers',
							'desc'        => '',
							'placeholder' => __( 'Select customers', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_user_rules',
								'values' => 'customers_list',
							),
						),
						'user_rules_customers_list_excluded' => array(
							'label'       => __( 'Select customers to exclude', 'ywdpd' ),
							'type'        => 'customers',
							'desc'        => '',
							'placeholder' => __( 'Select customers', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_user_rules',
								'values' => 'customers_list_excluded',
							),
						),
						/***************
						 * APPLY TO
						 */
						'apply_to'                         => array(
							'label'   => __( 'Apply to', 'ywdpd' ),
							'desc'    => __( 'Select the products to which applying the rule', 'ywdpd' ),
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => $pricing_rules_options['apply_to'],
							'std'     => 'all_products',

						),

						'apply_to_products_list'           => array(
							'label'       => __( 'Search for a product', 'ywdpd' ),
							'type'        => 'products',
							'desc'        => '',
							'placeholder' => __( 'Search for a product', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'products_list',
							),
						),
						'apply_to_products_list_excluded'  => array(
							'label'       => __( 'Search for a product', 'ywdpd' ),
							'type'        => 'products',
							'desc'        => '',
							'placeholder' => __( 'Search for a product', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'products_list_excluded',
							),
						),
						'apply_to_categories_list'         => array(
							'label'       => __( 'Search for a category', 'ywdpd' ),
							'type'        => 'categories',
							'desc'        => '',
							'placeholder' => __( 'Search for a category', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'categories_list',
							),
						),
						'apply_to_categories_list_excluded' => array(
							'label'       => __( 'Search for a category', 'ywdpd' ),
							'type'        => 'categories',
							'desc'        => '',
							'placeholder' => __( 'Search for a category', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'categories_list_excluded',
							),
						),
						'apply_to_tags_list'               => array(
							'label'       => __( 'Search for a tags', 'ywdpd' ),
							'type'        => 'tags',
							'desc'        => '',
							'placeholder' => __( 'Search for a tags', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'tags_list',
							),
						),
						'apply_to_tags_list_excluded'      => array(
							'label'       => __( 'Search for a tags', 'ywdpd' ),
							'type'        => 'tags',
							'desc'        => '',
							'placeholder' => __( 'Search for a tags', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'tags_list_excluded',
							),
						),
						'n_items_in_cart'                  => array(
							'label' => __( 'Total items in cart', 'ywcdpd' ),
							'type'  => 'gift_items_in_cart',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'gift_products',
							),
							'desc'  => __( 'Select how many items must be in the cart to apply the rule', 'ywdpd' ),
						),

						/*GIFT PRODUCTS*/
						'gift_product_selection'           => array(
							'label'    => __( 'Gift Products', 'ywdpd' ),
							'type'     => 'ajax-products',
							'desc'     => __( 'Select gift products', 'ywdpd' ),
							'multiple' => true,
							'deps'     => array(
								'ids'    => '_discount_mode',
								'values' => 'gift_products',
							),
						),
						'amount_gift_product_allowed'      => array(
							'label'   => __( 'Gift products allowed', 'ywdpd' ),
							'type'    => 'number',
							'min'     => 0,
							'step'    => 1,
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'gift_products',
							),
							'default' => 1,
							'desc'    => __( 'Set how many gift product can be added for this rule', 'ywdpd' ),

						),

						/***************
						 * ADJUSTMENT TO
						 */
						'apply_adjustment'                 => array(
							'label'   => __( 'Apply adjustment to', 'ywdpd' ),
							'desc'    => __( 'Select the products to which apply the adjustments', 'ywdpd' ),
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => $pricing_rules_options['apply_adjustment'],
							'std'     => 'same_product',
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),
						'apply_adjustment_products_list'   => array(
							'label'       => __( 'Search for a product', 'ywdpd' ),
							'type'        => 'products',
							'desc'        => '',
							'placeholder' => __( 'Search for a product', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'products_list',
							),
						),
						'apply_adjustment_products_list_excluded' => array(
							'label'       => __( 'Search for a product', 'ywdpd' ),
							'type'        => 'products',
							'desc'        => '',
							'placeholder' => __( 'Search for a product', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'products_list_excluded',
							),
						),
						'apply_adjustment_categories_list' => array(
							'label'       => __( 'Search for a category', 'ywdpd' ),
							'type'        => 'categories',
							'desc'        => '',
							'placeholder' => __( 'Search for a category', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'categories_list',
							),
						),
						'apply_adjustment_categories_list_excluded' => array(
							'label'       => __( 'Search for a category', 'ywdpd' ),
							'type'        => 'categories',
							'desc'        => '',
							'placeholder' => __( 'Search for a category', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'categories_list_excluded',
							),
						),
						'apply_adjustment_tags_list'       => array(
							'label'       => __( 'Search for a tags', 'ywdpd' ),
							'type'        => 'tags',
							'desc'        => '',
							'placeholder' => __( 'Search for a tags', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'tags_list',
							),
						),
						'apply_adjustment_tags_list_excluded' => array(
							'label'       => __( 'Search for a tags', 'ywdpd' ),
							'type'        => 'tags',
							'desc'        => '',
							'placeholder' => __( 'Search for a tags', 'ywdpd' ),
							'deps'        => array(
								'ids'    => '_apply_adjustment',
								'values' => 'tags_list_excluded',
							),
						),

						/***************
						 * DISCOUNT TABLES
						 */
						'rules'                            => array(
							'label'   => __( 'Discount Rules', 'ywdpd' ),
							'desc'    => '',
							'type'    => 'quantity_discount',
							'private' => false,
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk',
							),
						),
						'so-rule'                          => array(
							'label'   => __( 'Special Offer Rules', 'ywdpd' ),
							'desc'    => '',
							'type'    => 'special_offer_discount',
							'private' => false,
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'special_offer',
							),
						),

						/***************
						 * NOTES
						 */
						'table_note_apply_to'              => array(
							'label' => __( 'Notes shown on "Apply to" products', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'textarea',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer,gift_products',
							),
						),

						'table_note_adjustment_to'         => array(
							'label' => __( 'Notes shown on "Apply adjustment to" products', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'textarea',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),

						'table_note'                       => array(
							'label' => __( 'Notes shown in quantity table', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'textarea',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),

						'apply_with_other_rules'           => array(
							'label' => __( 'With other rules', 'ywdpd' ),
							'desc'  => __( 'Only one quantity discount per product can be applied at the same time', 'ywdpd' ),
							'type'  => 'onoff',
							'std'   => 'no',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),

						'apply_on_sale'                    => array(
							'label' => __( 'Apply if the product is on sale', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'onoff',
							'std'   => 'no',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),

						'disable_with_other_coupon'        => array(
							'label' => __( 'Disable with other coupon', 'ywdpd' ),
							'desc'  => '',
							'type'  => 'onoff',
							'std'   => 'no',
							'deps'  => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk,special_offer',
							),
						),

					)
				),

			),
		),
	)
);
