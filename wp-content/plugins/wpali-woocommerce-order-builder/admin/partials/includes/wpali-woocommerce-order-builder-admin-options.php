<?php
/**
 * options-page registration with CMB2
 *
 * This file is used to create and manage the WWOB options page.
 *
 * @link       https://wpali.com
 * @since      1.1.0
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/admin/partials
 */

 add_action( 'cmb2_admin_init', 'wwob_register_theme_options_metabox' );
/**
 * Hook in and register a metabox to handle the plugin's options page and adds a menu item to producds menu.
 */
function wwob_register_theme_options_metabox() {
	
	/**
	 * Registers options page menu item and form.
	 */
	$wwob_options = new_cmb2_box( array(
		'id'           => 'wwob_option_metabox',
		'title'        => esc_html__( 'WpAli: WooCommerce Order Builder Settings', 'wpali-woocommerce-order-builder' ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'wwob_options', 
		'icon_url'        => 'dashicons-cart',
		'menu_title'      => esc_html__( 'WWOB Settings', 'wpali-woocommerce-order-builder' ), 
		'parent_slug'     => 'edit.php?post_type=product',
		'capability'      => 'manage_product_terms', 
		'save_button'     => esc_html__( 'Save WWOB Options', 'wpali-woocommerce-order-builder' ), 
        'tabs'      => array(
            'styling' => array(
                'label' => __('Styling options', 'cmb2_tabs'),
                'icon' => 'dashicons-admin-customizer',
            ),
            'display'  => array(
                'label' => __('Display Options', 'cmb2_tabs'),
                'icon'  => 'dashicons-grid-view', 
            ),
            'responsive'    => array(
                'label' => __('Responsiveness', 'cmb2_tabs'),
                'icon'  => 'dashicons-smartphone',
            ),
            'product'    => array(
                'label' => __('Product Options', 'cmb2_tabs'),
                'icon'  => 'dashicons-grid-view',
            ),
        ),
	) );
	
	// Styling
	$wwob_options->add_field( array(
		'name'    => __( 'Primary Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'primary_color',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Secondary Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'secondary_color',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Styling', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_styling',
		'type'    => 'title',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Container Background', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Pick background color for items container', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_container_bg',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Background', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Pick background color of every item within items container ', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'item_bg',
		'type'    => 'colorpicker',
		'default' => 'transparent',
		'classes' => 'wwob-parameters',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Text Color', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Pick color for items text (name & price) ', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'item_text_color',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Label Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_label_color',
		'desc'    => __( 'Leave empty to use primary color', 'wpali-woocommerce-order-builder' ),
		'type'    => 'colorpicker',
		'default' => 'transparent',
		'classes' => 'wwob-parameters',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Description Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_description_color',
		'desc'    => __( 'Leave empty to use secondary color', 'wpali-woocommerce-order-builder' ),
		'type'    => 'colorpicker',
		'default' => 'transparent',
		'classes' => 'wwob-parameters',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Container Padding', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Padding around items container in pixels', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'item_container_padding',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'default' => '20',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Styling', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sticky_styling',
		'type'    => 'title',
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );	
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Background', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Pick background color for sticky sidebar (optional)', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'siderbar_container_bg',
		'type'    => 'colorpicker',
		'default' => '#fff',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Heading Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'siderbar_heading_color',
		'type'    => 'colorpicker',
		'default' => '#fff',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Heading Background', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'siderbar_heading_background',
		'desc'    => __( 'Fallback background color if product featured image is not available or disabled', 'wpali-woocommerce-order-builder' ),
		'type'    => 'colorpicker',
		'default' => '#333',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Top Position', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Top position for the sticky sidebar (eg: fixed navigation height)', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sidebar_top_position',
		'default' => '30',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Button Text Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sticky_button_text_color',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Button Hover Text Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sticky_button_hover_text_color',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Button Background Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sticky_button_background',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	$wwob_options->add_field( array(
		'name'    => __( 'Button Hover Background Color', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sticky_button_background_hover',
		'type'    => 'colorpicker',
		'classes' => 'wwob-parameters', 
        'tab'  => 'styling',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	// Responsiveness
	$wwob_options->add_field( array(
		'name'    => __( 'Desktop Items height', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Leave empty to use dynamic height based on tallest item.', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_height',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters', 
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Tablet Items height', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Leave empty to use dynamic height based on tallest item.', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'tablet_items_height',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters',
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),

	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Mobile Items height', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Leave empty to use dynamic height based on tallest item.', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'mobile_items_height',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters',
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),

	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Breakpoint', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Sticky-bar breakpoint by pixels (leave empty to use default "780px").', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'sidebar_breakpoint',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters', 
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Tablet Breakpoint', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Items breakpoint by pixels (leave empty to use default "768px").', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_breakpoint',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters', 
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Tablet: items per row', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Number of items per row in tablet display.', 'wpali-woocommerce-order-builder' ),
		'id'      => 'tablet_items_per_row',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'2' => __( '2', 'wpali-woocommerce-order-builder' ),
			'3'   => __( '3', 'wpali-woocommerce-order-builder' ),
		),
		'default' => '3',
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Items Mobile Breakpoint', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Items breakpoint by pixels (leave empty to use default "480px")', 'wpali-woocommerce-order-builder' ),
		'id'   	  => 'items_mobile_breakpoint',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'classes' => 'wwob-parameters', 
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	$wwob_options->add_field( array(
		'name'    => __( 'Mobile: items per row', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Number of items per row in mobile display.', 'wpali-woocommerce-order-builder' ),
		'id'      => 'mobile_items_per_row',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'1' => __( '1', 'wpali-woocommerce-order-builder' ),
			'2'   => __( '2', 'wpali-woocommerce-order-builder' ),
		),
		'default' => '2',
        'tab'  => 'responsive',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	// Display
	$wwob_options->add_field( array(
		'name'    => __( 'Product Display', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Choose product layout ( <a href="https://plugins.wpali.com/wp-content/uploads/2017/10/wwob_layout.png">WWOB Layout</a> | <a href="https://plugins.wpali.com/wp-content/uploads/2017/10/WC_layout.png">WooCommerce Layout</a> )', 'wpali-woocommerce-order-builder' ),
		'id'      => 'product_layout',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'wwob' => __( 'WWOB Layout', 'wpali-woocommerce-order-builder' ),
			'woocommerce' => __( 'WooCommerce Layout', 'wpali-woocommerce-order-builder' ),
		),
		'default' => 'wwob',
        'tab'  => 'display',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	$wwob_options->add_field( array(
		'name'    => __( 'Sticky-bar Background Image', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Show or hide background image on the floating sidebar', 'wpali-woocommerce-order-builder' ),
		'id'      => 'sidebar_background_display',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'show' => __( 'Show', 'wpali-woocommerce-order-builder' ),
			'hide'   => __( 'Hide', 'wpali-woocommerce-order-builder' ),
		),
		'default' => 'show',
        'tab'  => 'display',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );

	$wwob_options->add_field( array(
		'name'    => __( 'Items style', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Choose items style from available designs ( <a href="http://plugins.wpali.com/wp-content/uploads/2017/10/1.png">Style 1</a> | <a href="http://plugins.wpali.com/wp-content/uploads/2017/10/2.png">Style 2</a> | <a href="http://plugins.wpali.com/wp-content/uploads/2017/10/3-1.png">Style 3</a> )<br><b>Note:</b> Make sure to select primary color and secondary color before using this option.', 'wpali-woocommerce-order-builder' ),
		'id'      => 'items_display_style',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'default' => __( 'default', 'wpali-woocommerce-order-builder' ),
			'first' => __( 'Style 1', 'wpali-woocommerce-order-builder' ),
			'second'   => __( 'Style 2', 'wpali-woocommerce-order-builder' ),
			'third'   => __( 'Stlye 3', 'wpali-woocommerce-order-builder' ),
		),
		'default' => 'default',
        'tab'  => 'display',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	$wwob_options->add_field( array(
		'name'    => __( 'Enable Enhanced Calculator', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Check this box to enable enhanced sticky bar', 'wpali-woocommerce-order-builder' ),
		'id'      => 'product_enhanced_calculator',
		'type'    => 'checkbox',
		'classes' => 'wwob-parameters',
		// 'default' => 'on',
        'tab'  => 'display',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	$wwob_options->add_field( array(
		'name'    => __( 'Product Options Position', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Choose where you want to display product options on the page', 'wpali-woocommerce-order-builder' ),
		'id'      => 'product_options_position',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'sticky'   => __( 'Inside Sticky-bar', 'wpali-woocommerce-order-builder' ),
			'items'   => __( 'Below Product Items', 'wpali-woocommerce-order-builder' ),
		),
		'items' => 'items',
        'tab'  => 'display',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
	
	// Product
	$wwob_options->add_field( array(
		'name'    => __( 'Restrict sub-items quantity', 'wpali-woocommerce-order-builder' ),
		'desc'    => __( 'Include sub-items quantity in the \'max select\' count.', 'wpali-woocommerce-order-builder' ),
		'id'      => 'product_quantity_count',
		'type'    => 'radio_inline',
		'classes' => 'wwob-parameters',
		'options' => array(
			'no' => __( 'No', 'wpali-woocommerce-order-builder' ),
			'yes' => __( 'Yes', 'wpali-woocommerce-order-builder' ),
		),
		'default' => 'wwob',
        'tab'  => 'product',
        'render_row_cb' => array('CMB2_Tabs', 'tabs_render_row_cb'),
	) );
}

// Wrapper function around cmb2_get_option

function wwob_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( 'wwob_options', $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'wwob_options', $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}
