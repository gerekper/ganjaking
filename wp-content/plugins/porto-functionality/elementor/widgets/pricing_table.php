<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Pricing Table Widget
 *
 * Porto Elementor widget to display a pricing table.
 *
 * @since 1.7.2
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
class Porto_Elementor_Pricing_Table_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_price_box';
	}

	public function get_title() {
		return __( 'Porto Pricing Table', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'pricing table', 'price', 'box', 'price box' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_pricing_table',
			array(
				'label' => __( 'Pricing Table', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'title',
				array(
					'type'    => Controls_Manager::TEXT,
					'label'   => __( 'Title', 'porto-functionality' ),
					'default' => __( 'Professional', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'desc',
				array(
					'type'    => Controls_Manager::TEXT,
					'label'   => __( 'Description', 'porto-functionality' ),
					'default' => __( 'Most Popular', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'price',
				array(
					'type'  => Controls_Manager::TEXT,
					'label' => __( 'Price', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'price_unit',
				array(
					'type'  => Controls_Manager::TEXT,
					'label' => __( 'Price Unit', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'price_label',
				array(
					'type'        => Controls_Manager::TEXT,
					'label'       => __( 'Price Label', 'porto-functionality' ),
					'description' => __( 'For example, "Per Month"', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'content',
				array(
					'type'      => Controls_Manager::WYSIWYG,
					'label'     => __( 'Content', 'porto-functionality' ),
					'default'   => __(
						'<ul>
							<li><strong>5GB</strong> Disk Space</li>
							<li><strong>50GB</strong> Monthly Bandwidth</li>
							<li><strong>10</strong> Email Accounts</li>
							<li><strong>Unlimited</strong> subdomains</li>
						</ul>',
						'porto-functionality'
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'is_popular',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'label'       => __( 'Popular Price Box', 'porto-functionality' ),
					'description' => __( 'Choose to apply featured styling to the pricing box.', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'popular_label',
				array(
					'type'      => Controls_Manager::TEXT,
					'label'     => __( 'Popular Label', 'porto-functionality' ),
					'default'   => __( 'Popular', 'porto-functionality' ),
					'condition' => array(
						'is_popular' => 'yes',
					),
				)
			);

			$this->add_control(
				'style',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Style', 'porto-functionality' ),
					'options' => array_combine( array_values( porto_sh_commons( 'price_boxes_style' ) ), array_keys( porto_sh_commons( 'price_boxes_style' ) ) ),
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
				'size',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Size', 'porto-functionality' ),
					'options' => array_combine( array_values( porto_sh_commons( 'price_boxes_size' ) ), array_keys( porto_sh_commons( 'price_boxes_size' ) ) ),
				)
			);

			$this->add_control(
				'border',
				array(
					'type'    => Controls_Manager::SWITCHER,
					'label'   => __( 'Show Border', 'porto-functionality' ),
					'default' => 'yes',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pricing_button',
			array(
				'label' => __( 'Button', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'show_btn',
				array(
					'type'  => Controls_Manager::SWITCHER,
					'label' => __( 'Show Button', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'btn_label',
				array(
					'type'      => Controls_Manager::TEXT,
					'label'     => __( 'Button Label', 'porto-functionality' ),
					'default'   => __( 'Get In Touch', 'porto-functionality' ),
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

			$this->add_control(
				'btn_action',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Button Action', 'porto-functionality' ),
					'options'   => array_combine( array_values( porto_sh_commons( 'popup_action' ) ), array_keys( porto_sh_commons( 'popup_action' ) ) ),
					'default'   => 'open_link',
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

			$this->add_control(
				'btn_link',
				array(
					'label'     => __( 'Link', 'porto-functionality' ),
					'type'      => Controls_Manager::URL,
					'condition' => array(
						'show_btn'   => 'yes',
						'btn_action' => 'open_link',
					),
				)
			);

			$this->add_control(
				'popup_iframe',
				array(
					'label'     => __( 'Video or Map URL (Link)', 'porto-functionality' ),
					'type'      => Controls_Manager::URL,
					'condition' => array(
						'btn_action' => 'popup_iframe',
					),
				)
			);

			$this->add_control(
				'popup_block',
				array(
					'type'        => Controls_Manager::TEXT,
					'label'       => __( 'Popup Block', 'porto-functionality' ),
					'description' => __( 'Please add block slug name.', 'porto-functionality' ),
					'condition'   => array(
						'btn_action' => 'popup_block',
					),
				)
			);

			$this->add_control(
				'popup_size',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Popup Size', 'porto-functionality' ),
					'options'   => array(
						'md' => __( 'Medium', 'porto-functionality' ),
						'lg' => __( 'Large', 'porto-functionality' ),
						'sm' => __( 'Small', 'porto-functionality' ),
						'xs' => __( 'Extra Small', 'porto-functionality' ),
					),
					'default'   => 'md',
					'condition' => array(
						'btn_action' => 'popup_block',
					),
				)
			);

			$this->add_control(
				'popup_animation',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Popup Animation', 'porto-functionality' ),
					'options'   => array(
						'mfp-fade'            => __( 'Fade', 'porto-functionality' ),
						'mfp-with-zoom'       => __( 'Zoom', 'porto-functionality' ),
						'my-mfp-zoom-in'      => __( 'Fade Zoom', 'porto-functionality' ),
						'my-mfp-slide-bottom' => __( 'Fade Slide', 'porto-functionality' ),
					),
					'default'   => 'mfp-fade',
					'condition' => array(
						'btn_action' => 'popup_block',
					),
				)
			);

			$this->add_control(
				'btn_style',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Button Style', 'porto-functionality' ),
					'options'   => array(
						''        => __( 'Default', 'porto-functionality' ),
						'borders' => __( 'Outline', 'porto-functionality' ),
					),
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

			$this->add_control(
				'btn_size',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Button Size', 'porto-functionality' ),
					'options'   => array_combine( array_values( porto_sh_commons( 'size' ) ), array_keys( porto_sh_commons( 'size' ) ) ),
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

			$this->add_control(
				'btn_pos',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Button Position', 'porto-functionality' ),
					'options'   => array(
						''       => __( 'Top', 'porto-functionality' ),
						'bottom' => __( 'Bottom', 'porto-functionality' ),
					),
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

			$this->add_control(
				'btn_skin',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Button Skin Color', 'porto-functionality' ),
					'options'   => array_combine( array_values( porto_sh_commons( 'colors' ) ), array_keys( porto_sh_commons( 'colors' ) ) ),
					'default'   => 'custom',
					'condition' => array(
						'show_btn' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_table_style',
			array(
				'label' => __( 'Price Table', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'box_shadow',
					'selector' => '.elementor-element-{{ID}} .plan',
				)
			);
			$this->add_control(
				'price_table_bg',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan' => 'background-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'price_table_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .plan' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'price_table_margin',
				array(
					'label'      => esc_html__( 'Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .plan' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'price_table_wrap_margin',
				array(
					'label'      => esc_html__( 'Wrap Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .pricing-table' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_header_style',
			array(
				'label'       => __( 'Header', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.pricing-table h3',
			)
		);
			$this->add_control(
				'header_bg',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .pricing-table h3 strong' => 'background-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'header_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .pricing-table h3 strong' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'after',
				)
			);

			$this->add_control(
				'header_wrap_bg',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Wrap Background Color', 'porto-functionality' ),
					'description' => __( 'Controls the background color including Price.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .pricing-table h3' => 'background-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'header_wrap_padding',
				array(
					'label'       => esc_html__( 'Wrap Padding', 'porto-functionality' ),
					'description' => __( 'Controls the padding including Price.', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .pricing-table h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'   => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Title', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .pricing-table h3 strong',
				)
			);
			$this->add_control(
				'title_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Title Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .pricing-table h3 strong' => 'color: {{VALUE}};',
					),
					'separator' => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'desc_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Description', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .pricing-table h3 .desc',
				)
			);
			$this->add_control(
				'desc_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Description Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .pricing-table h3 .desc' => 'color: {{VALUE}};',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_style',
			array(
				'label'       => __( 'Pricing', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.plan-price',
			)
		);
			$this->add_control(
				'price_bg',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan-price' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'price_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .plan-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'price_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Price', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .plan-price .price',
				)
			);
			$this->add_control(
				'price_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Price Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan-price .price' => 'color: {{VALUE}};',
					),
					'separator' => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'price_unit_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Price Unit', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .price-unit',
				)
			);
			$this->add_control(
				'price_unit_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Unit Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .price-unit' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'price_unit_pos',
				array(
					'label'     => esc_html__( 'Unit Position', 'porto-functionality' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => esc_html__( 'Top', 'porto-functionality' ),
							'icon'  => 'eicon-v-align-top',
						),
						'center'     => array(
							'title' => esc_html__( 'Middle', 'porto-functionality' ),
							'icon'  => 'eicon-v-align-middle',
						),
						'flex-end'   => array(
							'title' => esc_html__( 'Bottom', 'porto-functionality' ),
							'icon'  => 'eicon-v-align-bottom',
						),
					),
					'selectors' => array(
						'.elementor-element-{{ID}} .price' => 'align-items: {{VALUE}};',
					),
					'separator' => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'price_label_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Price Label', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .price-label',
				)
			);
			$this->add_control(
				'price_label_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Label Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .price-label' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'price_label_spacing',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Spacing', 'porto-functionality' ),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .price-label' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			array(
				'label'       => __( 'Content', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.plan ul',
			)
		);
			$this->add_control(
				'content_bg',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan ul' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'content_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .plan ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'after',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'content_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Content', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .plan ul',
				)
			);
			$this->add_control(
				'content_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Content Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan li' => 'color: {{VALUE}};',
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'content_item_padding',
				array(
					'label'       => esc_html__( 'Item Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .pricing-table .plan ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'qa_selector' => '.pricing-table .plan ul li:nth-child(2)',
				)
			);
			$this->add_control(
				'content_item_br_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Border Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan ul li' => 'border-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'content_item_br_width',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Border Width', 'porto-functionality' ),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .plan li' => 'border-top-width: {{SIZE}}{{UNIT}};',
						'.elementor-element-{{ID}} .pricing-table-flat .plan-btn-bottom li:last-child' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
						'.elementor-element-{{ID}} .pricing-table-classic .plan li' => 'border-top-width: 0;border-bottom-width: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'       => __( 'Button', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.btn',
				'condition'   => array(
					'show_btn' => 'yes',
				),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'button_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Button', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .btn',
				)
			);
			$this->add_control(
				'button_margin',
				array(
					'label'      => esc_html__( 'Button Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'button_padding',
				array(
					'label'      => esc_html__( 'Button Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_button_style' );
				$this->start_controls_tab(
					'tab_button_normal',
					array(
						'label' => __( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'button_text_color',
						array(
							'label'     => __( 'Text Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .btn' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'button_br_color',
						array(
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .btn' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'     => 'button_background',
							'types'    => array( 'classic', 'gradient' ),
							'exclude'  => array( 'image' ),
							'selector' => '.elementor-element-{{ID}} .btn',
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_button_hover',
					array(
						'label' => __( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'button_text_hover_color',
						array(
							'label'     => __( 'Text Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .btn:hover' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'button_br_hover_color',
						array(
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} .btn:hover' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'     => 'button_background_hover',
							'types'    => array( 'classic', 'gradient' ),
							'exclude'  => array( 'image' ),
							'selector' => '.elementor-element-{{ID}} .btn:hover',
						)
					);
				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ribbon_style',
			array(
				'label'       => __( 'Ribbon', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.plan-ribbon-wrapper',
				'condition'   => array(
					'is_popular' => 'yes',
				),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'ribbon_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Ribbon', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .plan-ribbon',
				)
			);
			$this->add_control(
				'ribbon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan-ribbon' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'ribbon_bg_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .plan-ribbon' => 'background-color: {{VALUE}};',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_price_box' ) ) {
			$this->add_inline_editing_attributes( 'title' );
			$this->add_inline_editing_attributes( 'desc' );
			$this->add_render_attribute( 'desc', 'class', 'desc' );
			$title_attrs_escaped = ' ' . $this->get_render_attribute_string( 'title' );
			$desc_attrs_escaped  = ' ' . $this->get_render_attribute_string( 'desc' );

			$classes = 'pricing-table';
			if ( ! isset( $atts['border'] ) || ! $atts['border'] ) {
				$classes .= ' no-borders';
			}

			if ( isset( $atts['size'] ) && 'sm' === $atts['size'] ) {
				$classes .= ' pricing-table-sm';
			}

			if ( ! empty( $atts['style'] ) ) {
				$classes .= ' pricing-table-' . $atts['style'];
			}
			echo '<div class="' . esc_attr( $classes ) . '">';

			if ( isset( $atts['content'] ) ) {
				$content = $atts['content'];
			}
			include $template;
			echo '</div>';
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'pricing-table' );
			if ( ! settings.border ) {
				view.addRenderAttribute( 'wrapper', 'class', 'no-borders' );
			}
			if ( settings.size && 'sm' === settings.size ) {
				view.addRenderAttribute( 'wrapper', 'class', 'pricing-table-sm' );
			}
			if ( settings.style ) {
				view.addRenderAttribute( 'wrapper', 'class', 'pricing-table-' + settings.style );
			}

			view.addRenderAttribute( 'inner_wrapper', 'class', 'porto-price-box plan' );
			if ( settings.is_popular ) {
				view.addRenderAttribute( 'inner_wrapper', 'class', 'most-popular' );
			}
			if ( settings.skin ) {
				view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-' + settings.skin );
			}

			let btn_class = 'btn btn-modern';
			if ( settings.btn_style ) {
				btn_class += ' btn-' + settings.btn_style;
			}
			let btn_html = '';
			if ( settings.btn_size ) {
				btn_class += ' btn-' + settings.btn_size;
			}
			if ( 'custom' !== settings.btn_skin ) {
				btn_class += ' btn-' + settings.btn_skin;
			} else {
				btn_class += ' btn-default';
			}
			if ( 'bottom' !== settings.btn_pos ) {
				btn_class += ' btn-top';
			} else {
				btn_class += ' btn-bottom';
			}
			view.addRenderAttribute( 'btn', 'class', btn_class );

			if ( 'open_link' === settings.btn_action ) {
				if ( settings.btn_link && settings.btn_link.url ) {
					view.addRenderAttribute( 'btn', 'href', settings.btn_link.url );
				}
				if ( settings.btn_link ) {
					btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
				} else {
					btn_html += '<span ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</span>';
				}
			} else if ( 'popup_iframe' === settings.btn_action && settings.popup_iframe ) {
				view.addRenderAttribute( 'btn', 'class', 'porto-popup-iframe' );
				view.addRenderAttribute( 'btn', 'href', settings.popup_iframe );
				btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
			} else if ( 'popup_block' === settings.btn_action && settings.popup_block ) {
				let uid = 'popup' + Math.floor(Math.random()*999999);
				view.addRenderAttribute( 'btn', 'class', 'porto-popup-content' );
				view.addRenderAttribute( 'btn', 'href', '#' + uid );
				view.addRenderAttribute( 'btn', 'data-animation', settings.popup_animation );
				btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
				btn_html += '<div id="' + uid + '" class="dialog dialog-' + settings.popup_size + ' zoom-anim-dialog mfp-hide">[porto_block name="' + settings.popup_block + '"]</div>';
			}

			if ( btn_html ) {
				if ( 'bottom' === settings.btn_pos ) {
					view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-btn-bottom' );
				} else {
					view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-btn-top' );
				}
			}

			view.addInlineEditingAttributes( 'title' );
			view.addInlineEditingAttributes( 'desc' );
			view.addRenderAttribute( 'desc', 'class', 'desc' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'inner_wrapper' ) }}}>
			<# if ( settings.is_popular && settings.popular_label ) { #>
				<div class="plan-ribbon-wrapper"><div class="plan-ribbon">{{ settings.popular_label }}</div></div>
			<# } #>
			<# if ( settings.title || settings.price || settings.desc ) { #>
				<h3>
				<# if ( settings.title ) { #>
					<strong {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</strong>
				<# } #>
				<# if ( settings.desc ) { #>
					<em {{{ view.getRenderAttributeString( 'desc' ) }}}>{{ settings.desc }}</em>
				<# } #>
				<# if ( settings.price ) { #>
					<span class="plan-price"><span class="price">
					<# if ( settings.price_unit ) { #>
						<span class="price-unit">{{ settings.price_unit }}</span>
					<# } #>
					{{ settings.price }}
					</span>
					<# if ( settings.price_label ) { #>
						<label class="price-label">{{ settings.price_label }}</label>
					<# } #>
					</span>
				<# } #>
				</h3>
			<# } #>
			<#
				if ( settings.show_btn && 'bottom' !== settings.btn_pos ) {
					print( btn_html );
				}
				print( settings.content );
				if ( settings.show_btn && 'bottom' === settings.btn_pos ) {
					print( btn_html );
				}
			#>
			</div>
		</div>
		<?php
	}
}
