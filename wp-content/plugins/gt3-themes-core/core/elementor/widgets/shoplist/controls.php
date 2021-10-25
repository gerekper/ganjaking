<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ShopList $widget */

$widget->start_controls_section(
	'general',
	array(
		'label' => esc_html__( 'General', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'hiddenUpdate',
	array(
		'default'     => '1',
		'condition'   => array(
			'showHiddenUpdate' => 'never',
		),
	)
);

$widget->add_control(
	'woo_category',
	array(
		'label'       => esc_html__( 'Product Category', 'gt3_themes_core' ),
		'description' => esc_html__( 'Leave an empty if you want to display all categories', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT2,
		'default'     => '',
		'multiple'    => true,
		'options'     => $widget->get_woo_category(),
		'label_block' => true,
	)
);

$widget->add_responsive_control(
	'prod_per_row',
	array(
		'label'          => esc_html__( 'Products Per Row', 'gt3_themes_core' ),
		'description'    => esc_html__( 'How many products should be shown per row?', 'gt3_themes_core' ),
		'type'           => Controls_Manager::SELECT,
		'options'        => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'default'        => 3,
		'tablet_default' => 2,
		'mobile_default' => 1,
		'selectors'      => array(
			'{{WRAPPER}} .woocommerce ul.products:not(.list) li.product' => 'width: calc(100%/{{VALUE}} - {{grid_gap.SIZE}}{{grid_gap.UNIT}});',
		),
	)
);

$widget->add_control(
	'rows_per_page',
	array(
		'label'       => esc_html__( 'Rows Per Page', 'gt3_themes_core' ),
		'description' => esc_html__( 'How many rows per page should be shown?', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'1'  => '1',
			'2'  => '2',
			'3'  => '3',
			'4'  => '4',
			'5'  => '5',
			'6'  => '6',
			'7'  => '7',
			'8'  => '8',
			'9'  => '9',
			'10' => '10',
		),
		'default'     => 2,
	)
);


if( (bool)apply_filters('elementor_widget_shoplist_infinite_scroll_view_all', true)){
	$options_infinite_scroll = array(
		'none'     => esc_html__( 'None', 'gt3_themes_core' ),
		'view_all' => esc_html__( 'Activate after clicking on "View All"', 'gt3_themes_core' ),
		'always'   => esc_html__( 'Always', 'gt3_themes_core' ),
	);
}else{
	$options_infinite_scroll = array(
		'none'     => esc_html__( 'None', 'gt3_themes_core' ),
		'always'   => esc_html__( 'Always', 'gt3_themes_core' ),
	);
}

$widget->add_control(
	'infinite_scroll',
	array(
		'label'       => esc_html__( 'Infinite Scroll', 'gt3_themes_core' ),
		'description' => esc_html__( 'How many rows per page should be shown?', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => $options_infinite_scroll,
		'default'     => 'none',
	)
);

$widget->add_control(
	'orderby',
	array(
		'label'       => esc_html__( 'Order by', 'gt3_themes_core' ),
		'description' => esc_html__( 'Select how to sort retrieved products', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'date'          => esc_html__( 'Date', 'gt3_themes_core' ),
			'ID'            => esc_html__( 'ID', 'gt3_themes_core' ),
			'author'        => esc_html__( 'Author', 'gt3_themes_core' ),
			'modified'      => esc_html__( 'Modified', 'gt3_themes_core' ),
			'rand'          => esc_html__( 'Random', 'gt3_themes_core' ),
			'comment_count' => esc_html__( 'Comment count', 'gt3_themes_core' ),
			'menu_order'    => esc_html__( 'Menu Order', 'gt3_themes_core' ),
		),
		'default'     => 'menu_order',
	)
);

$widget->add_control(
	'order',
	array(
		'label'       => esc_html__( 'Order way', 'gt3_themes_core' ),
		'description' => esc_html__( 'Designates the ascending or descending order', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'DESC' => esc_html__( 'Descending', 'gt3_themes_core' ),
			'ASC'  => esc_html__( 'Ascending', 'gt3_themes_core' ),
		),
		'default'     => 'DESC',
	)
);

$widget->end_controls_section();

// Tab Style
$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__( 'Shop Styles', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'grid_gap',
	array(
		'label'      => esc_html__( 'Grid Gap', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'size_units' => [ 'px', '%' ],
		'range'      => [
			'px' => [
				'min' => 0,
				'max' => 50,
			],
			'%'  => [
				'min' => 0,
				'max' => 10,
			],
		],
		'default'    => [
			'size' => 30,
			'unit' => 'px',
		],
		'selectors'  => array(
			'{{WRAPPER}} .woocommerce ul.products' => 'margin-right: -{{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .woocommerce li.product'  => 'margin-right: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};',
		),
	)
);

/*$widget->add_control(
	'hiddenUpdate',
	array(
		'type' => Controls_Manager::HIDDEN,
	)
);*/

/*$widget->add_control(
    'products_shadow',
    array(
        'label' => esc_html__('Show shadow on hover?', 'gt3_themes_core'),
        'type'  => Controls_Manager::SWITCHER,
        'default'     => 'yes',
    )
);

$widget->add_control(
    'products_shadow_color',
    array(
        'label'     => esc_html__('Shadow Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'default'   => 'rgba(239,239,239,1)',
        'selectors' => array(
            '{{WRAPPER}} .woocommerce ul.products.shadow li.product:hover:before'      => 'color: {{COLOR}};',
            '.woocommerce-page {{WRAPPER}} ul.products.shadow li.product:hover:before' => 'color: {{COLOR}};',
        ),
        'condition' => array(
            'products_shadow' => 'yes',
        )
    )
);*/

/*$widget->add_control(
    'prod_bgcolor_1',
    array(
        'label'       => esc_html__('Background Color', 'gt3_themes_core'),
        'description' => esc_html__('Select the Background Color for each Products', 'gt3_themes_core'),
        'type'        => Controls_Manager::COLOR,
        'default'     => 'transparent',
    )
);

$widget->add_control(
    'prod_bgcolor_2',
    array(
        'label'       => esc_html__('Hover Background Color', 'gt3_themes_core'),
        'description' => esc_html__('Select the Background Color for each Products in hover', 'gt3_themes_core'),
        'type'        => Controls_Manager::COLOR,
        'default'     => 'transparent',
    )
);*/

$widget->add_control(
	'shop_grid_list',
	array(
		'label' => esc_html__( 'Show Grid/List toggle Buttons', 'gt3_themes_core' ),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'buttons',
	array(
		'label' => esc_html__( 'Button Layout', 'gt3_themes_core' ),
		'type'  => Controls_Manager::SECTION,
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);


if( (bool)apply_filters('elementor_widget_shoplist_dropdown_prod_per_page', true)){
	$widget->add_control(
		'dropdown_prod_per_page',
		array(
			'label'       => esc_html__( 'Dropdown on Frontend - Items Per Page', 'gt3_themes_core' ),
			'description' => esc_html__( 'Show the dropdown to change the Number of products displayed per page', 'gt3_themes_core' ),
			'type'        => Controls_Manager::SWITCHER,
			'condition'   => array(
				'infinite_scroll!' => 'always',
			)
		)
	);
}

$widget->add_control(
	'dropdown_prod_orderby',
	array(
		'label'       => esc_html__( 'Dropdown on Frontend - Order by', 'gt3_themes_core' ),
		'description' => esc_html__( 'Show the dropdown to change the Sorting of products displayed per page', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'pagination',
	array(
		'label'       => esc_html__( 'Pagination on Frontend', 'gt3_themes_core' ),
		'description' => esc_html__( 'Show Pagination on the page', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'top'        => esc_html__( 'Top', 'gt3_themes_core' ),
			'bottom'     => esc_html__( 'Bottom', 'gt3_themes_core' ),
			'bottom_top' => esc_html__( 'Bottom and Top', 'gt3_themes_core' ),
			'off'        => esc_html__( 'Off', 'gt3_themes_core' ),
		),
		'default'     => 'bottom_top',
		'condition'   => array(
			'infinite_scroll!' => 'always',
		)
	)
);

$widget->end_controls_section();
