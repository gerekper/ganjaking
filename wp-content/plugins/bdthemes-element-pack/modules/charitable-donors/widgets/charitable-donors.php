<?php
namespace ElementPack\Modules\CharitableDonors\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Donors extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-donors';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Donors', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-donors';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation', 'donor', 'history', 'charitable', 'wall', 'donors' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-donors'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/ljnbE8JVg7w';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_donors',
			[
				'label' => __( 'Charitable Donors', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'campaign',
			[
				'label' => __( 'Campaign', 'bethemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_charitable_forms_options(),
				'default' => 'all',
			]
		);

		$this->add_control(
			'custom_orientation',
			[
				'label' => __( 'Orientation', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'vertical' => esc_html__( 'Vertical', 'bdthemes-element-pack' ),
					'horizontal' => esc_html__( 'Horizontal', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-campaigns-orientation-',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_name',
			[
				'label' => esc_html__( 'Show Name', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_location',
			[
				'label' => esc_html__( 'Show Location', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_amount',
			[
				'label' => esc_html__( 'Show Amount', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		
		$this->add_control(
			'show_avatar',
			[
				'label' => esc_html__( 'Show Avatar', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'number',
			[
				'label' => esc_html__( 'Limit', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 12,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => esc_html__( 'Items Gap', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list' => 'grid-gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => esc_html__( 'ASC', 'bdthemes-element-pack' ),
					'DESC' => esc_html__( 'DESC', 'bdthemes-element-pack' ),
				],
				'default' => 'DESC',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'date'   => esc_html__( 'Date', 'bdthemes-element-apck' ),
					'amount' => esc_html__( 'Amount', 'bdthemes-element-apck' ),
				],
				'default' => 'date',
			]
		);
		
		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_items_style',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);
		
		$this->add_control(
			'item_background_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover',
			]
		);

		$this->add_control(
			'hover_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'name_hover_color',
			[
				'label' => esc_html__( 'Name Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover .donor-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'location_hover_color',
			[
				'label' => esc_html__( 'Location Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover .donor-location' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'amount_hover_color',
			[
				'label' => esc_html__( 'Amount Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor:hover .donor-donation-amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_avatar_style',
			[
				'label' => esc_html__( 'Avatar', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_avatar' => 'yes'
				]
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'avatar_border',
                'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .avatar'
            ]
        );

        $this->add_control(
            'iamge_radius',
            [
                'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'avatar_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .avatar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);
		
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'avatar_shadow',
                'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .avatar'
            ]
		);
		
		$this->add_responsive_control(
			'avatar_size',
			[
				'label' => esc_html__( 'Avatar Size', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'avatar_hr_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}.bdt-campaigns-orientation-horizontal .donors-list .donor .avatar' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_orientation' => 'horizontal'
				]
			]
		);
		
		$this->add_responsive_control(
			'avatar_vr_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}.bdt-campaigns-orientation-vertical .donors-list .donor .avatar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_orientation' => 'vertical'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_name_style',
			[
				'label' => esc_html__( 'Name', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-name' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'name_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-name' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_location_style',
			[
				'label' => esc_html__( 'Location', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_location' => 'yes'
				]
			]
		);

		$this->add_control(
			'location_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-location' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'location_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-location' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'location_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-location',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_amount_style',
			[
				'label' => esc_html__( 'amount', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_amount' => 'yes'
				]
			]
		);

		$this->add_control(
			'amount_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-donation-amount' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'amount_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-donation-amount' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'amount_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donors .donors-list .donor .donor-donation-amount',
			]
		);

		$this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['campaign']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select a Charitable Forms From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'campaign' => $settings['campaign'],
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
			'number' => $settings['number'],
			// 'orientation' => $settings['orientation'],
			'show_name' => $settings['show_name'],
			'show_location' => $settings['show_location'],
			'show_amount' => $settings['show_amount'],
			'show_avatar' => $settings['show_avatar'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_donors %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-donors' );
		
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