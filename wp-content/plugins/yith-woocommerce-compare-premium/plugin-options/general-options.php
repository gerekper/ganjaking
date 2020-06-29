<?php
/**
 * Main admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
	'general' => array(
		array(
			'name' => __( 'General Settings', 'yith-woocommerce-compare' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'yith_woocompare_general',
		),

		array(
			'title'     => __( 'Link or Button', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Choose if you want to use a link or a button for the compare actions.', 'yith-woocommerce-compare' ),
			'id'        => 'yith_woocompare_is_button',
			'default'   => 'button',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'link'   => __( 'Link', 'yith-woocommerce-compare' ),
				'button' => __( 'Button', 'yith-woocommerce-compare' ),
			),
		),

		array(
			'title'     => __( 'Page or Popup', 'yith-woocommerce-compare' ),
			'desc' => __( 'Choose if you want to use a page or a popup for the standard comparison table.', 'yith-woocommerce-compare' ),
			'id'       => 'yith_woocompare_use_page_popup',
			'default'  => 'popup',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'    => 'wc-enhanced-select',
			'options'  => array(
				'page'  => __( 'Page', 'yith-woocommerce-compare' ),
				'popup' => __( 'Popup', 'yith-woocommerce-compare' ),
			),
		),

		array(
			'title'     => __( 'Choose Compare Page', 'yith-woocommerce-compare' ),
			'desc' => __( 'Choose the page you want to use as default Compare Page. Make sure that page content is: <i>[yith_woocompare_table]</i>', 'yith-woocommerce-compare' ),
			'id'       => 'yith_woocompare_compare_page',
			'class'    => 'wc-enhanced-select-nostd',
			'default'  => get_option( 'yith-woocompare-page-id', '' ),
			'type'     => 'single_select_page',
		),

		array(
			'title'    => __( 'Link/Button text', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Type the text you want to use for the compare button/link.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_button_text',
			'default' => __( 'Compare', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
		),

		array(
			'title'    => __( 'Link/Button text for products already in compare', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Type the text you want to use for the compare button/link for products that already are in compare table.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_button_text_added',
			'default' => __( 'View Compare', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
		),

		array(
			'title'    => __( 'Show button in single product page', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Set this option to show the button in the single product page.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_compare_button_in_product_page',
			'default' => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'    => __( 'Show button in products list', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Set this option to show the button in the products list.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_compare_button_in_products_list',
			'default' => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'    => __( 'Open lightbox automatically', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Open the link after clicking on the "Compare" button.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_auto_open',
			'default' => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'    => __( 'Open lightbox when adding a second item', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Open the comparison lightbox after adding a second item to compare.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_open_after_second',
			'default' => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'    => __( 'Compare by category', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Compare products by category.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_use_category',
			'default' => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'    => __( 'Exclude category', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Choose category to exclude from the comparison.', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_excluded_category',
			'type'    => 'yith_woocompare_select_cat',
			'default' => '',
		),

		array(
			'title'    => __( 'Reverse exclusion list', 'yith-woocommerce-compare' ),
			'desc'    => __( 'Only categories in the exclusion list will have the compare feature', 'yith-woocommerce-compare' ),
			'id'      => 'yith_woocompare_excluded_category_inverse',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default' => 'no',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith_woocompare_general_end',
		),
	),
);

return apply_filters( 'yith_woocompare_general_settings', $options );
