<?php
namespace ElementPack\Modules\CharitableDonations\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Donations extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-donations';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Donations', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-donations';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation', 'donor', 'history', 'charitable', 'wall', 'form', 'donations' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-donations'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/C38sbaKx9x0';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __('Header', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'header_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7ebef',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'header_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'header_border_style',
			[
				'label'     => __('Border Style', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'none'   => __('None', 'bdthemes-element-pack'),
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
					'groove' => __('Groove', 'bdthemes-element-pack'),
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'border-style: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'header_border_width',
			[
				'label'     => __('Border Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'header_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'    => 1,
					'bottom' => 1,
					'left'   => 1,
					'right'  => 2,
					'unit'   => 'em'
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donations .charitable-table th',
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_style_body',
			[
				'label' => __('Body', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'cell_border_style',
			[
				'label'     => __('Border Style', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'none'   => __('None', 'bdthemes-element-pack'),
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
					'groove' => __('Groove', 'bdthemes-element-pack'),
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'border-style: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'cell_border_width',
			[
				'label'     => __('Border Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'cell_padding',
			[
				'label'      => __('Cell Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'    => 0.5,
					'bottom' => 0.5,
					'left'   => 1,
					'right'  => 1,
					'unit'   => 'em'
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'body_text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donations .charitable-table td',
			]
		);
		
		$this->start_controls_tabs('tabs_body_style');
		
		$this->start_controls_tab(
			'tab_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);
		
		$this->add_control(
			'normal_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'normal_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'normal_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td' => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'tab_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);
		
		$this->add_control(
			'row_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table tr:hover td' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'row_hover_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table tr:hover td' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'tab_stripe',
			[
				'label'     => __('Stripe', 'bdthemes-element-pack'),
			]
		);
		
		$this->add_control(
			'stripe_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'stripe_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table tr:nth-child(even) td' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_text',
			[
				'label'     => __('Link', 'bdthemes-element-pack'),
			]
		);
		
		$this->add_control(
			'link_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donations .charitable-table td a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();
		
		$this->end_controls_section();


	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$attributes = [];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_my_donations %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-donations' );
		
		?>

		<div <?php echo $this->get_render_attribute_string('charitable_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
	
}