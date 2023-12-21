<?php
/**
 * Hover Box widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Hover_Box extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Hover Box', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-finger-point';
	}

	public function get_keywords() {
		return [ 'image', 'hover', 'box', 'hover box' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__image_content_controls();
		$this->__text_content_controls();
		$this->__common_content_controls();
	}

	protected function __image_content_controls() {

		$this->start_controls_section(
            '_section_content_image',
            [
                'label' => __( 'Image', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_image',
                'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					"image" => [
						'default' => [
							'url' => Utils::get_placeholder_image_src(),
						]
					]
				],
                'selector' => '{{WRAPPER}} .ha-hover-box-wrapper',
            ]
        );

        $this->add_control(
            'background_overlay',
            [
                'label' => __( 'Overlay', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'background_image_background' => 'classic'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-wrapper:before' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

	protected function __text_content_controls() {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __( 'Text', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'sub_title',
            [
                'label' => __( 'Sub Title', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'WordPress', 'happy-addons-pro' ),
                'placeholder' => __( 'Type your sub title here', 'happy-addons-pro' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __( 'Title', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Happy Addons', 'happy-addons-pro' ),
                'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				]
            ]
        );

		$this->add_control(
			'detail',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Best Elementor Addons', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your description here', 'happy-addons-pro' ),
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				]
			]
		);

        $this->add_control(
            'link',
            [
                'label' => __( 'Link', 'happy-addons-pro' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://example.com', 'happy-addons-pro' ),
                'separator' => 'before',
                'label_block' => true,
				'dynamic' => [
					'active' => true,
				]
            ]
        );

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

		$this->end_controls_section();
	}

	protected function __common_content_controls() {

        $this->start_controls_section(
            '_section_common',
            [
                'label' => __( 'Common', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label' => __( 'Content Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => __( 'Top', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors_dictionary' => [
                    'top' => 'align-items: flex-start',
                    'center' => 'align-items: center',
                    'bottom' => 'align-items: flex-end',
                ],
                'default' => 'bottom',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-wrapper' => '{{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'content_alignment',
            [
                'label' => __( 'Content Alignment', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-content'  => 'text-align: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'sub_title_position',
            [
                'label' => __( 'Sub Title Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => __( 'Top', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'toggle' => false,
                'default' => 'top',
                'prefix_class' => 'ha-pre--',
                'selectors_dictionary' => [
                    'top' => 'flex-direction: column',
                    'bottom' => 'flex-direction: column-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-content'  => '{{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'display_type',
            [
                'label' => __( 'Text Display Control', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'default' => [
                        'title' => __( 'Default', 'happy-addons-pro' ),
                        'icon' => 'eicon-animation-text',
                    ],
                    'hover' => [
                        'title' => __( 'On Hover', 'happy-addons-pro' ),
                        'icon' => 'eicon-click',
                    ],
                ],
                'toggle' => false,
                'default' => 'default',
            ]
        );

        $this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__subtitle_style_controls();
		$this->__title_style_controls();
		$this->__desc_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_hover_box_style',
			[
				'label' => __( 'Common', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
            'box_height',
            [
                'label' => __( 'Box Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hover_box_border',
                'selector' => '{{WRAPPER}} .ha-hover-box-main',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __( 'Border radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-box-main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hover_box_shadow',
                'selector' => '{{WRAPPER}} .ha-hover-box-main',
            ]
        );

		$this->end_controls_section();
	}

	protected function __subtitle_style_controls() {

		$this->start_controls_section(
			'_section_sub_title_style',
			[
				'label' => __( 'Sub Title', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
            'pre_title_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}.ha-pre--top .ha-hover-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-pre--bottom .ha-hover-sub-title' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pre_title_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pre_title_border',
				'selector' => '{{WRAPPER}} .ha-hover-sub-title',
			]
		);

		$this->add_control(
			'pre_title_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-hover-sub-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'pre_title_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-sub-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pre_title_background_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-sub-title' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pre_title_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'selector' => '{{WRAPPER}} .ha-hover-sub-title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->end_controls_section();
	}

	protected function __title_style_controls() {

        $this->start_controls_section(
            '_section_title_style',
            [
                'label' => __( 'Title', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'title_border',
                'selector' => '{{WRAPPER}} .ha-hover-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_background_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-title' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .ha-hover-title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->end_controls_section();
	}

	protected function __desc_style_controls() {

        $this->start_controls_section(
            '_section_description_style',
            [
                'label' => __( 'Description', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .ha-hover-description',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hover-description' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'sub_title', 'class', 'ha-hover-sub-title' );
        $this->add_inline_editing_attributes( 'sub_title', 'basic' );

        $this->add_render_attribute( 'title', 'class', 'ha-hover-title' );
        $this->add_inline_editing_attributes( 'title', 'basic' );

        $this->add_render_attribute( 'detail', 'class', 'ha-hover-description' );
        $this->add_inline_editing_attributes( 'detail', 'basic' );

		$this->add_render_attribute( 'link', 'class', 'ha-flip-btn');
		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}

        if ( $settings['display_type'] === 'default' ) {
            $this->add_render_attribute( 'display-type', 'class', 'ha-hover-box-wrapper' );
        } elseif ( $settings['display_type'] === 'hover' ) {
            $this->add_render_attribute( 'display-type', 'class', 'ha-hover-box-wrapper reverse' );
        }
		?>

        <?php if( $settings['link']['url'] ): ?>
            <a <?php $this->print_render_attribute_string( 'link' ); ?>>
        <?php endif;?>

            <div class="ha-hover-box-main">
                <div <?php $this->print_render_attribute_string( 'display-type' ); ?>>
                    <div class="ha-hover-box-content">

                        <?php if( $settings['sub_title'] ): ?>
                            <div>
                                <p <?php $this->print_render_attribute_string( 'sub_title' ); ?>>
                                    <?php echo esc_html( $settings['sub_title'] ); ?>
                                </p>
                            </div>
                        <?php endif;?>

                        <div>
                            <?php
								if ( $settings['title'] ) {
									printf( '<%1$s %2$s>%3$s</%1$s>',
										ha_escape_tags( $settings['title_tag'], 'h4' ),
										$this->get_render_attribute_string( 'title' ),
										ha_kses_basic( $settings['title'] )
									);
								}
							?>

                            <?php if( $settings['detail'] ): ?>
                                <p <?php $this->print_render_attribute_string( 'detail' ); ?>>
                                    <?php echo $settings['detail']; ?>
                                </p>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        <?php if( $settings['link']['url'] ): ?>
           </a>
        <?php endif; ?>

        <?php
	}
}
