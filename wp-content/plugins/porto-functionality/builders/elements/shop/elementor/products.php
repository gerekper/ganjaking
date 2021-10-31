<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder Products Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_SB_Products_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_products';
	}

	public function get_title() {
		return __( 'Products', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'products', 'shop', 'woocommerce' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-basket';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js', 'isotope' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$order_by_values  = array_slice( porto_vc_woo_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$slider_options   = porto_update_vc_options_to_elementor( porto_vc_product_slider_fields() );

		$slider_options['nav_pos2']['condition']['navigation']       = 'yes';
		$slider_options['nav_type']['condition']['navigation']       = 'yes';
		$slider_options['autoplay_timeout']['condition']['autoplay'] = 'yes';

		$this->start_controls_section(
			'section_products_layout',
			array(
				'label' => __( 'Products Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'notice_skin',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'To change the Products Archive’s layout, go to Porto / Theme Options / WooCommerce / Product Archives.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'notice_wrong_data',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The editor\'s preview might look different from the live site. Please check the frontend.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array_combine( array_values( porto_sh_commons( 'products_view_mode' ) ), array_keys( porto_sh_commons( 'products_view_mode' ) ) ),
			)
		);

		$this->add_control(
			'grid_layout',
			array(
				'label'     => __( 'Grid Layout', 'porto-functionality' ),
				'type'      => 'image_choose',
				'default'   => '1',
				'options'   => array_combine( array_values( porto_sh_commons( 'masonry_layouts' ) ), array_keys( porto_sh_commons( 'masonry_layouts' ) ) ),
				'condition' => array(
					'view' => 'creative',
				),
			)
		);

		$this->add_control(
			'grid_height',
			array(
				'label'     => __( 'Grid Height', 'porto-functionality' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '600px',
				'condition' => array(
					'view' => 'creative',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Column Spacing (px)', 'porto-functionality' ),
				'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'condition'   => array(
					'view' => array( 'grid', 'creative', 'products-slider' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'products-slider', 'grid', 'divider' ),
				),
				'default'   => '4',
				'options'   => porto_sh_commons( 'products_columns' ),
			)
		);

		$this->add_control(
			'columns_mobile',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'products-slider', 'grid', 'divider', 'list' ),
				),
				'default'   => '',
				'options'   => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'addlinks_pos',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Product Layout', 'porto-functionality' ),
				'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
				'options'     => array_combine( array_values( porto_sh_commons( 'products_addlinks_pos' ) ), array_keys( porto_sh_commons( 'products_addlinks_pos' ) ) ),
			)
		);

		$this->add_control(
			'overlay_bg_opacity',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
				'range'     => array(
					'%' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'condition' => array(
					'addlinks_pos' => array( 'onimage2', 'onimage3' ),
				),
				'default'   => array(
					'unit' => '%',
					'size' => 30,
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Image Size', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
				'default'   => '',
				'condition' => array(
					'view' => array( 'products-slider', 'grid', 'divider', 'list' ),
				),
			)
		);

		$this->add_control(
			'el_class',
			array(
				'label' => __( 'Custom Class', 'porto-functionality' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Slider Options', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'products-slider',
				),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$this->add_control( $key, $opt );
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_style',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_google_font_style',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Product Title Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__title',
			)
		);

		$this->add_control(
			'title_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Product Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_google_font_style',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Price Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .price',
			)
		);

		$this->add_control(
			'price_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Price Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( empty( $atts['spacing'] ) ) {
			$atts['spacing'] = '';
		}
		if ( is_singular( PortoBuilders::BUILDER_SLUG ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) ) {
			if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
				echo '<div class="archive-products">';
				include $template;
				echo '</div>';
			}
		} else {
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/products.php';
		}
	}
}
