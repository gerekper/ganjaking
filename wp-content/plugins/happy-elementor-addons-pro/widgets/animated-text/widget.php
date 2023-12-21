<?php
/**
 * animated-text
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Text_Shadow;
use Happy_Addons\Elementor\Controls\Group_Control_Foreground;

defined('ABSPATH') || die();

class Animated_Text extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Animated Text', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-text-animation';
	}

	public function get_keywords() {
		return ['animated-text', 'animated', 'text'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__animated_text_content_controls();
		$this->__settings_content_controls();
	}

	protected function __animated_text_content_controls() {

		$this->start_controls_section(
			'_section_animated_text',
			[
				'label' => __('Content', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'animation_type',
			[
				'label' => __('Animation Type', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'default' => 'rotate-1',
				'options' => [
					'rotate-1' => __('Rotate 1', 'happy-addons-pro'),
					'letters type' => __('Type', 'happy-addons-pro'),
					'letters rotate-2' => __('Rotate 2', 'happy-addons-pro'),
					'loading-bar' => __('Loading Bar', 'happy-addons-pro'),
					'slide' => __('Slide', 'happy-addons-pro'),
					'clip' => __('Clip', 'happy-addons-pro'),
					'zoom' => __('Zoom', 'happy-addons-pro'),
					'letters rotate-3' => __('Rotate 3', 'happy-addons-pro'),
					'letters scale' => __('Scale', 'happy-addons-pro'),
					'push' => __('Push', 'happy-addons-pro'),
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => __('Before Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => __('HappyAddons is', 'happy-addons-pro'),
				'placeholder' => __('Before Text', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true,
                ]
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => __('After Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __('After Text', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true,
                ]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text', [
				'label' => __('Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Awesome', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true,
                ]
			]
		);

		$repeater->add_control(
			'text_customize',
			[
				'label' => __('Want To Customize Text?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'happy-addons-pro'),
				'label_off' => __('No', 'happy-addons-pro'),
				'return_value' => 'yes',
                'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'text_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}} ' => 'color: {{VALUE}}; -webkit-background-clip: initial; -webkit-text-fill-color:initial; background: none;',
					'{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}} i' => 'color: {{VALUE}}; -webkit-background-clip: initial; -webkit-text-fill-color:initial; background: none;',
					'{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}} i em' => 'color: {{VALUE}}; -webkit-background-clip: initial; -webkit-text-fill-color:initial; background: none;',
				],
				'condition' => [
					'text_customize' => 'yes'
				],
                'style_transfer' => true,
			]
		);


		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'font_size',
					'line_height',
				],
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}}, {{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}} i, {{WRAPPER}} .ha-animated-text-wrap .ha-animated-text > {{CURRENT_ITEM}} em',
				'condition' => [
					'text_customize' => 'yes'
				],
                'style_transfer' => true,
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'label' => __('Text Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-animated-text > {{CURRENT_ITEM}}',
				'condition' => [
					'text_customize' => 'yes'
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'animated_text',
			[
				'label' => __('Animated Text', 'happy-addons-pro'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'text' => __('Awesome', 'happy-addons-pro'),
					],
					[
						'text' => __('Cool', 'happy-addons-pro'),
					],
					[
						'text' => __('Nice', 'happy-addons-pro'),
					],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => __('HTML Tag', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => __('H1', 'happy-addons-pro'),
					'h2' => __('H2', 'happy-addons-pro'),
					'h3' => __('H3', 'happy-addons-pro'),
					'h4' => __('H4', 'happy-addons-pro'),
					'h5' => __('H5', 'happy-addons-pro'),
					'h6' => __('H6', 'happy-addons-pro'),
					'p' => __('P', 'happy-addons-pro'),
				],
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __('Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
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
					],
					'justify' => [
						'title' => __('Justify', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .cd-headline' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_animation_settings',
			[
				'label' => __('Animation Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'letters type',
						],
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'loading-bar',
						],
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'clip',
						],
					],
				],
			]
		);

		$this->add_control(
			'animation_delay',
			[
				'label' => __('Animation Delay', 'happy-addons-pro'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1000,
				'step' => 100,
				'max' => 30000,
				'default' => 2500,
				'description' => __('Animation Delay in milliseconds. Min 1000 and Max 30000.', 'happy-addons-pro'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'letters type',
						],
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'loading-bar',
						],
						[
							'name' => 'animation_type',
							'operator' => '!=',
							'value' => 'clip',
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__before_text_style_controls();
		$this->__after_text_style_controls();
		$this->__animated_text_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_animated_text_common_style',
			[
				'label' => __('Common Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'common_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-animated-text-wrap' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'common_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'line_height',
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap, {{WRAPPER}} .ha-animated-text-wrap b, {{WRAPPER}} .ha-animated-text-wrap i, {{WRAPPER}} .ha-animated-text-wrap em',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'common_shadow',
				'label' => __('Text Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap',
			]
		);

		$this->add_control(
			'loading_bar_color',
			[
				'label' => __('Loading Bar Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-animated-text-wrap.loading-bar .ha-animated-text::after' => 'background: {{VALUE}}',
				],
				'condition' => [
					'animation_type' => 'loading-bar'
				]
			]
		);

		$this->add_control(
			'cursor_color',
			[
				'label' => __('Cursor Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-animated-text-wrap.clip .ha-animated-text::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-animated-text-wrap.type .ha-animated-text::after' => 'background-color: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'animation_type',
							'operator' => '==',
							'value' => [
								'clip',
							],
						],
						[
							'name' => 'animation_type',
							'operator' => '==',
							'value' => [
								'letters type',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'select_text_color',
			[
				'label' => __('Select Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-animated-text-wrap.type .ha-animated-text.selected' => 'background-color: {{VALUE}}'
				],
				'condition' => [
					'animation_type' => 'letters type',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __before_text_style_controls() {

		$this->start_controls_section(
			'_section_animated_text_before_style',
			[
				'label' => __('Before Text Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'before_text_color',
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-before-text',
			]
		);

		$this->add_control(
			'before_text_color_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'before_text_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'font_size',
					'line_height',
				],
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-before-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'before_text_shadow',
				'label' => __('Text Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-before-text',
			]
		);

		$this->end_controls_section();
	}

	protected function __after_text_style_controls() {

		$this->start_controls_section(
			'_section_animated_text_after_style',
			[
				'label' => __('After Text Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'after_text_color',
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-after-text',
			]
		);

		$this->add_control(
			'after_text_color_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'after_text_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'font_size',
					'line_height',
				],
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-after-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'after_text_shadow',
				'label' => __('Text Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-after-text',
			]
		);

		$this->end_controls_section();
	}

	protected function __animated_text_style_controls() {

		$this->start_controls_section(
			'_section_animated_text_style',
			[
				'label' => __('Animated Text Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'animated_text_color',
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text b,{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text i,{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text em',
			]
		);

		$this->add_control(
			'animated_text_color_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'animated_text_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'font_size',
					'line_height',
				],
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text b,{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text i,{{WRAPPER}} .ha-animated-text-wrap .ha-animated-text em',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'animated_text_shadow',
				'label' => __('Text Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-animated-text-wrap b',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$animation_type = $settings['animation_type'];
		$this->add_render_attribute( 'ha-animated', 'class',
            [
                'ha-animated-text-wrap',
                'cd-headline',
                $animation_type
            ]
        );

		if ( $animation_type &&
            'letters type' !== $animation_type &&
            'letters type' !== $animation_type &&
            'clip' !== $animation_type
        ) {
			$this->add_render_attribute( 'ha-animated', 'data-animation-delay', $settings['animation_delay'] );
		}

		$animated_text = '';

        if ( $settings['before_text'] ) {
			$this->add_render_attribute( 'ha-animated', 'class', 'ha-animated-has-before-text' );
            $animated_text .= sprintf( '<span class="ha-animated-before-text">%s</span>', esc_html( $settings['before_text'] ) );
        }

        if ( $settings['animated_text'] && is_array( $settings['animated_text'] ) ) {
            $animated_animation_text = '';

            foreach ( $settings['animated_text'] as $key => $item ) {
                $animated_animation_text .= sprintf(
                    '<b class="elementor-repeater-item-%s">%s</b>',
                    esc_attr( $item['_id'] . ( $key === 0 ? ' is-visible' : '' ) ),
                    esc_html( $item['text'] )
                );
            }

            $animated_text .= sprintf(
                ' <span class="ha-animated-text cd-words-wrapper">%s</span>',
                $animated_animation_text
            );
        }

        if ( $settings['after_text'] ) {
			$this->add_render_attribute( 'ha-animated', 'class', 'ha-animated-has-after-text' );
            $animated_text .= sprintf( ' <span class="ha-animated-after-text">%s</span>', esc_html( $settings['after_text'] ) );
        }

		printf(
            '<%1$s %2$s>%3$s</%1$s>',
            ha_escape_tags( $settings['html_tag'] ),
            $this->get_render_attribute_string( 'ha-animated' ),
            $animated_text
            );
	}
}
