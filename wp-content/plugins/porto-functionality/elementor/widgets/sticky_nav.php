<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Sticky Navigation Widget
 *
 * Porto Elementor widget to display sticky navigation
 *
 * @since 6.3.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Sticky_Nav_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sticky_nav';
	}

	public function get_title() {
		return __( 'Porto Sticky Navigation', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'sticky', 'navigation', 'menu' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_sticky_nav',
			array(
				'label' => __( 'Sticky Navigation', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'container',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Wrap as Container', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'min_width',
			array(
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Min Width (unit: px)', 'porto-functionality' ),
				'description' => __( 'Wll be disable sticky if window width is smaller than min width', 'porto-functionality' ),
				'min'         => 320,
				'max'         => 1920,
				'default'     => 991,
			)
		);

		$this->add_control(
			'bg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-sticky-nav' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Link Typograhy', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .nav-pills > li > a, .elementor-element-{{ID}} .nav-pills > li > span',
			)
		);

		$this->add_control(
			'skin',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Skin Color', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'colors' ) ), array_keys( porto_sh_commons( 'colors' ) ) ),
				'default' => 'custom',
			)
		);

		$this->add_control(
			'link_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills > li > a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);

		$this->add_control(
			'link_bg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills > li > a' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);

		$this->add_control(
			'link_acolor1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Active Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills > li.active > a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);

		$this->add_control(
			'link_abg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Active Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills > li.active > a' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Link Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => array(
					'.elementor-element-{{ID}} .nav-pills > li > a, .elementor-element-{{ID}} .nav-pills > li > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$repeater = new Elementor\Repeater();

		$repeater->start_controls_tabs(
			'sticky_nav_items'
		);

		$repeater->start_controls_tab(
			'sticky_nav_item',
			array(
				'label' => esc_html__( 'Content', 'porto-functionality' ),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Label', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'link',
			array(
				'type'  => Controls_Manager::URL,
				'label' => __( 'Link', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'show_icon',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Icon', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon to display', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'  => __( 'Icon Fonts', 'porto-functionality' ),
					'image' => __( 'Custom Image Icon', 'porto-functionality' ),
				),
				'default'     => 'icon',
				'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
				'condition'   => array(
					'show_icon' => 'yes',
				),
			)
		);
		$repeater->add_control(
			'icon_cl',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_icon' => 'yes',
					'icon_type' => 'icon',
				),
			)
		);
		$repeater->add_control(
			'icon_image',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image Icon:', 'porto-functionality' ),
				'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_icon' => 'yes',
					'icon_type' => array( 'image' ),
				),
			)
		);
		$repeater->add_control(
			'skin',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Skin Color', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'colors' ) ), array_keys( porto_sh_commons( 'colors' ) ) ),
				'default' => 'custom',
			)
		);
		$repeater->add_control(
			'link_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills {{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);
		$repeater->add_control(
			'link_bg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills {{CURRENT_ITEM}} > a' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);
		$repeater->add_control(
			'link_acolor1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Active Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills {{CURRENT_ITEM}}.active > a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);
		$repeater->add_control(
			'link_abg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Link Active Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .nav-pills {{CURRENT_ITEM}}.active > a' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => 'custom',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$presets = array(
			array(
				'label' => 'Item 1',
				'link'  => '#',
			),
			array(
				'label' => 'Item 2',
				'link'  => '#',
			),
		);
		$this->add_control(
			'sticky_nav_item_list',
			array(
				'label'   => esc_html__( 'Sticky Nav Items', 'porto-functionality' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => $presets,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_sticky_nav' ) ) {
			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'porto-sticky-nav nav-secondary' );
		view.addRenderAttribute( 'wrapper', 'data-plugin-options', "{'minWidth': " + Number( settings.min_width ) + "}" );
		view.addRenderAttribute( 'nav', 'class', 'nav nav-pills' );
		if ( 'custom' != settings.skin ) {
			view.addRenderAttribute( 'nav', 'class', 'nav-pills-' + settings.skin );
		}
		#>
		<div class="sticky-nav-wrapper">
			<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# if ( settings.container ) { #>
				<div class="container">
			<# } #>
				<ul {{{ view.getRenderAttributeString( 'nav' ) }}}>
				<#
				_.each( settings.sticky_nav_item_list, function( item, index ) {
					if ( item.show_icon ) {
						if ( 'image' == item.icon_type ) {
							view.addRenderAttribute( 'nav-link', 'class', 'icon-image' );
						} else {
							view.addRenderAttribute( 'nav-link', 'class', item.icon_cl.value );
						}
					}
				#><li class="elementor-repeater-item-{{ item._id }}">
					<a href="#"><# if ( item.show_icon ) { #><i {{{ view.getRenderAttributeString( 'nav-link' ) }}}><# if ( 'image' == item.icon_type && item.icon_image.url ) { #><img class="img-icon" src="{{ item.icon_image.url }}" /><# } #></i><# } #>{{{ item.label }}}</a>
					</li><#
				} );
				#>
				</ul>
			<# if ( settings.container ) { #>
				</div>
			<# } #>
			</div>
		</div>
		<?php
	}
}
