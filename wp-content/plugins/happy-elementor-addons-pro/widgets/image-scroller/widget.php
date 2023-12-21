<?php
/**
 * Image Scroller
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Utils;

defined('ABSPATH') || die();

class Image_Scroller extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Single Image Scroll', 'happy-addons-pro');
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
		return 'hm hm-image-scroll';
	}

	public function get_keywords() {
		return ['single-image-scroll','image-scroll','scrolling-image', 'carousel', 'single','scroller', 'scrolling', 'scroll', 'image'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__image_scroller_content_controls();
		$this->__settings_content_controls();
	}

	//Image Scroller content
	protected function __image_scroller_content_controls() {

		$this->start_controls_section(
			'_section_image_scroller',
			[
				'label' => __('Image', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'scroller_image',
            [
                'label' => __('Scroller Image', 'happy-addons-pro'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'dynamic' => [
					'active' => true,
				],
            ]
		);

		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium_large',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'container_height',
            [
                'label' => __('Container Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} figure.ha-image-scroller-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
				// 'render_type' => 'template',
            ]
		);

		$this->add_control(
			'container_height_note',
			[
				'label' => __( 'Container Height Note', 'happy-addons-pro'),
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( "Make sure Container height/width is less than the Image's actual height/width. Otherwise scroll will not work.", 'happy-addons-pro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->end_controls_section();

	}

	//Settings content
	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_image_scroller_settings',
			[
				'label' => __('Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'link',
            [
                'label' => __('Link', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'happy-addons-pro'),
                    'lightbox' => __('Lightbox', 'happy-addons-pro'),
                    'custom_link' => __('Custom Link', 'happy-addons-pro'),
                ],
            ]
        );

        $this->add_control(
			'custom_link',
			[
				'label' => __('Custom Link', 'happy-addons-pro'),
				'show_label' => false,
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://example.com/', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				],
                'condition' => [
                    'link' => 'custom_link',
                ],
			]
		);

		$this->add_control(
			'lightbox_note',
			[
				'label' => __( 'Lightbox Note', 'happy-addons-pro'),
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( "Make sure elementor's Lightbox option is enable.", 'happy-addons-pro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'link' => 'lightbox',
                ],
			]
		);

		$this->add_control(
			'show_badge',
            [
                'label' => __('Show Badge', 'happy-addons-pro'),
				'type'  => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
		);

		$this->add_control(
			'badge_text',
			[
				'label' => __( 'Badge Text', 'happy-addons-pro' ),
				'show_label' => false,
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Badge Text', 'happy-addons-pro' ),
				'default' => __( 'Badge', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_badge' => 'yes',
				]
			]
		);

        /* $this->add_control(
			'overlay',
            [
                'label'         => __('Overlay','happy-addons-pro'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __('Show','happy-addons-pro'),
				'label_off'     => __('Hide','happy-addons-pro'),
				'return_value' => 'yes',

                'selectors_dictionary' => [
                    'yes' => 'content: ""',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-image-scroller-container:before' => '{{VALUE}}'
				],

            ]
        ); */

        $this->add_control(
			'trigger_type',
            [
                'label'         => __('Trigger', 'happy-addons-pro'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'hover'   => __('Hover', 'happy-addons-pro'),
                    'scroll'  => __('Mouse Scroll', 'happy-addons-pro'),
                ],
                'default'       => 'hover',
				'separator' => 'before',
            ]
        );

		$this->add_control(
			'scroll_type',
			[
				'label' => __('Scroll Type', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'vertical' => [
						'title' => __('Vertical', 'happy-addons-pro'),
						'icon' => 'eicon-navigation-vertical',
					],
					'horizontal' => [
						'title' => __('Horizontal', 'happy-addons-pro'),
						'icon' => 'eicon-navigation-horizontal',
					],
				],
				'default' => 'vertical',
				'toggle' => false,
                'style_transfer' => true,
                // 'prefix_class' => 'ha-image-scroller-',
			]
		);

		$this->add_control(
			'v__direction',
			[
				'label' => __('Direction', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __('Bottom', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'toggle' => false,
				'condition' => [
					'trigger_type' => 'hover',
					'scroll_type' => 'vertical',
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'h__direction',
			[
				'label' => __('Direction', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
				'condition' => [
					'trigger_type' => 'hover',
					'scroll_type' => 'horizontal',
				],
                'style_transfer' => true,
			]
		);

        $this->add_control(
			'duration_speed',
            [
                'label'			=> __( 'Speed', 'happy-addons-pro' ),
                'description'	=> __( 'Set the scroll speed in seconds. Default 3', 'happy-addons-pro' ),
				'type'			=> Controls_Manager::NUMBER,
				'min' => 0,
				// 'max' => 10000,
                'default'		=> 3,
                'condition'     => [
                    'trigger_type' => 'hover',
                ],
                'selectors' => [
                    '{{WRAPPER}} figure.ha-image-scroller-container img'   => 'transition-duration: {{Value}}s',
                ]
            ]
		);

		$this->add_control(
			'indicator_icon',
            [
                'label' => __('Indicator Icon', 'happy-addons-pro'),
				'type'  => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
                'condition'     => [
                    'trigger_type' => 'scroll',
                ],
            ]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__container_style_controls();
		$this->__image_style_controls();
		$this->__badge_style_controls();
		$this->__indicator_icon_style_controls();
	}

	//Container style
	protected function __container_style_controls() {

		$this->start_controls_section(
			'_section_image_scroller_container_style',
			[
				'label' => __('Container', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('container_style_tabs');

        $this->start_controls_tab('container_style_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .ha-image-scroller-wrapper',
            ]
        );

		$this->add_responsive_control(
			'container_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-image-scroller-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .ha-image-scroller-wrapper',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
			'container_style_hover',
            [
                'label'=> __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border_hover',
                'selector' => '{{WRAPPER}} .ha-image-scroller-wrapper:hover',
            ]
        );

		$this->add_responsive_control(
			'container_border_radius_hover',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-image-scroller-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow_hover',
                'selector' => '{{WRAPPER}} .ha-image-scroller-wrapper:hover',
            ]
        );

        $this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	//Image style
	protected function __image_style_controls() {

		$this->start_controls_section(
			'_section_image_scroller_style',
			[
				'label' => __('Image', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('image_style_tabs');

        $this->start_controls_tab(
			'image_style_tab_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
			'opacity',
			[
				'label' => __( 'Opacity', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'   => 1,
						'min'   => 0.10,
						'step'  => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img' => 'opacity: {{SIZE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} figure.ha-image-scroller-container img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
			'image_style_tab_hover',
			[
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
			'hover_opacity',
			[
				'label' => __( 'Opacity', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'   => 1,
						'min'   => 0.10,
						'step'  => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} figure.ha-image-scroller-container:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} figure.ha-image-scroller-container img:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->end_controls_section();


	}

	//Badge style
	protected function __badge_style_controls() {

		$this->start_controls_section(
			'_section_image_scroller_badge_style',
			[
				'label' => __('Badge', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition'     => [
                    'show_badge' => 'yes',
                ],
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __( 'Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_offset_x',
			[
				'label' => __( 'Offset Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => '--ha-badge-translate-x: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_offset_y',
			[
				'label' => __( 'Offset Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => '--ha-badge-translate-y: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_popover();

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-badge',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();


	}

	//Indicator Icon style
	protected function __indicator_icon_style_controls() {

		$this->start_controls_section(
			'_section_image_scroller_indicator_icon_style',
			[
				'label' => __('Indicator Icon', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition'     => [
                    'trigger_type' => 'scroll',
                    'indicator_icon' => 'yes',
                ],
			]
		);

        $this->add_control(
			'indicator_icon_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-image-scroller-indicator-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'indicator_icon_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-image-scroller-indicator-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_icon_bg_color',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-image-scroller-indicator-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (empty($settings['scroller_image']['url'])) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [
            'ha-image-scroller-wrapper',
            'ha-image-scroller-trigger-'.esc_attr($settings['trigger_type']),
            'ha-image-scroller-'.$settings['scroll_type'],
		] );

		$item_tag = 'div';
		if ( 'lightbox' === $settings['link'] ) {
			$item_tag = 'a';
			$this->add_render_attribute('wrapper', 'href', esc_url($settings['scroller_image']['url']));
		}
		if ( 'custom_link' === $settings['link'] && $settings['custom_link']['url'] ) {
			$item_tag = 'a';
			$this->add_link_attributes( 'wrapper', $settings['custom_link'] );
		}

		if( 'hover' === $settings['trigger_type'] ){

			$this->add_render_attribute('wrapper', 'class', 'ha-image-scroller-'.$settings['scroll_type']);
			$this->add_render_attribute('wrapper', 'data-trigger-type', $settings['trigger_type']);
			$this->add_render_attribute('wrapper', 'data-scroll-type', $settings['scroll_type']);

			if('horizontal'==$settings['scroll_type']){
				$this->add_render_attribute('wrapper', 'data-scroll-direction', $settings['h__direction']);
			}else{
				$this->add_render_attribute('wrapper', 'data-scroll-direction', $settings['v__direction']);
			}
		}

		$this->add_render_attribute( 'container', 'class', [
            'ha-image-scroller-container',
		] );

		if( 'yes'=== $settings['show_badge'] && $settings['badge_text'] ){
			$this->add_render_attribute( 'badge',
				'class', [
					'ha-image-scroller-badge',
					'ha-badge',
				]
			);
		}

		if( 'scroll'=== $settings['trigger_type'] && 'yes' === $settings['indicator_icon'] ){
			$this->add_render_attribute( 'indicator_icon',
				'class', [
					'ha-image-scroller-indicator-icon',
				]
			);
		}

		?>

		<<?php echo ha_escape_tags($item_tag,'div',['a']) . ' ' . $this->get_render_attribute_string( 'wrapper' ); ?>>
			<figure <?php $this->print_render_attribute_string( 'container' ); ?>>
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings,'thumbnail','scroller_image' ); ?>
			</figure>
			<?php if( 'yes'=== $settings['show_badge'] && $settings['badge_text'] ):?>
				<span <?php $this->print_render_attribute_string( 'badge' ); ?>>
					<?php esc_html_e($settings['badge_text']);?>
				</span>
			<?php endif;?>
			<?php if( 'scroll'=== $settings['trigger_type'] && 'yes' === $settings['indicator_icon'] ):?>
				<span <?php $this->print_render_attribute_string( 'indicator_icon' ); ?>>
					<i class="hm hm-scrolling-image"></i>
				</span>
			<?php endif;?>
		</<?php echo ha_escape_tags($item_tag,'div',['a']); ?>>
		<?php
	}

}
