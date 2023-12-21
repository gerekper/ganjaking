<?php
/**
 * Feature List widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

class Feature_List extends Base {
    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Feature List', 'happy-addons-pro' );
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
        return 'hm hm-list-2';
    }

    public function get_keywords() {
        return [ 'list', 'feature' ];
    }

	/**
     * Register widget content controls
     */
    protected function register_content_controls() {
		$this->__lists_content_controls();
		$this->__settings_content_controls();
	}

    protected function __lists_content_controls() {

        $this->start_controls_section(
            '_section_lists',
            [
                'label' => __( 'Feature List', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'icon_type',
            [
                'label' => __( 'Media Type', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'icon',
				'options' => [
					'icon' => [
						'title' => __( 'Icon', 'happy-addons-pro' ),
						'icon' => 'eicon-star',
					],
					'number' => [
						'title' => __( 'Number', 'happy-addons-pro' ),
						'icon' => 'eicon-number-field',
					],
					'image' => [
						'title' => __( 'Image', 'happy-addons-pro' ),
						'icon' => 'eicon-image',
					],
				],
				'toggle' => false,
                'style_transfer' => true,
            ]
        );

		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'default' => [
					'value' => 'fas fa-check',
					'library' => 'regular',
				],
				'condition' => [
					'icon_type' => 'icon'
				],
			]
		);

        $repeater->add_control(
            'number',
            [
                'label' => __( 'Item Number', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'List Item Number', 'happy-addons-pro' ),
                'default' => __( '1', 'happy-addons-pro' ),
                'condition' => [
                    'icon_type' => 'number'
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __( 'Image', 'happy-addons-pro' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'icon_type' => 'image'
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'text_heading',
            [
                'label' => __( 'Text & Link', 'happy-addons-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => __( 'Title', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __( 'List Item', 'happy-addons-pro' ),
                'default' => __( 'List Item', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __( 'Link', 'happy-addons-pro' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://example.com', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $this->add_control(
            'list_item',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default' => [
                    [
                        'title' => __( 'WordPress', 'happy-addons-pro' ),
						'icon' => [
							'value' => 'fas fa-check',
							'library' => 'regular',
						],
                    ],
                    [
                        'title' => __( 'Elementor', 'happy-addons-pro' ),
						'icon' => [
							'value' => 'fas fa-check',
							'library' => 'regular',
						],
                    ],
                    [
                        'title' => __( 'Happy Elementor Addons', 'happy-addons-pro' ),
						'icon' => [
							'value' => 'fas fa-smile',
							'library' => 'solid',
						],
                    ],
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __settings_content_controls() {

        $this->start_controls_section(
            '_section_settings',
            [
                'label' => __( 'Settings', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'list_layout',
            [
                'label' => __( 'Layout', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'column' => [
                        'title' => __( 'Default', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-list-ul',
                    ],
                    'row' => [
                        'title' => __( 'Inline', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-h',
                    ],
                ],
                'toggle' => false,
                'default' => 'column',
                'prefix_class' => 'ha-content--',
                'selectors' => [
                    '{{WRAPPER}} .ha-feature-list-wrap' => 'flex-direction: {{VALUE}};',
                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'show_separator',
            [
                'label' => __( 'Show Separator', 'happy-addons-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'happy-addons-pro' ),
                'label_off' => __( 'Hide', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'list_layout' => 'row'
                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'content_alignment',
            [
                'label' => __( 'Alignment', 'happy-addons-pro' ),
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
                'toggle' => false,
                'default' => 'flex-start',
                'prefix_class' => 'ha-align--',
                'selectors' => [
                    '{{WRAPPER}}.ha-content--column .ha-list-item, {{WRAPPER}}.ha-content--column .ha-list-item' => 'align-items: {{VALUE}};',
                    '{{WRAPPER}}.ha-content--row .ha-feature-list-wrap' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}}.ha-content--column.ha-icon--column .ha-content' => 'align-items: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_position',
            [
                'label' => __( 'Bullet Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'row' => [
                        'title' => __( 'Left', 'happy-addons-pro' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'column' => [
                        'title' => __( 'Top', 'happy-addons-pro' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'row-reverse' => [
                        'title' => __( 'Right', 'happy-addons-pro' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => false,
                'default' => 'row',
                'prefix_class' => 'ha-icon--',
                'selectors' => [
                    '{{WRAPPER}} .ha-content' => 'flex-direction: {{VALUE}};',
                ],
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();
    }

	/**
     * Register widget style controls
     */
    protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__media_style_controls();
		$this->__text_style_controls();
	}

    protected function __common_style_controls() {

        $this->start_controls_section(
            '_section_common_style',
            [
                'label' => __( 'Common', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'list_separator_width',
            [
                'label' => __( 'Separator Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40
                ],
                'condition' => [
                    'show_separator' => 'yes',
                    'list_layout' => 'row'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-list-item:after' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'list_separator_color',
            [
                'label' => __( 'Separator Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_separator' => 'yes',
                    'list_layout' => 'row'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-list-item:after' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ]
                ],
                'condition' => [
                    'list_layout' => 'row'
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-content--row .ha-list-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'list_border_type',
            [
                'label' => __( 'Border Type', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'happy-addons-pro' ),
                    'solid' => __( 'Solid', 'happy-addons-pro' ),
                    'double' => __( 'Double', 'happy-addons-pro' ),
                    'dotted' => __( 'Dotted', 'happy-addons-pro' ),
                    'dashed' => __( 'Dashed', 'happy-addons-pro' ),
                ],
                'default' => 'none',
                'condition' => [
                    'list_layout' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-content--column .ha-feature-list-wrap' => 'border-style: {{VALUE}}',
                    '{{WRAPPER}}.ha-content--column .ha-list-item:not(:last-child)' => 'border-bottom-style: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_border_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'condition' => [
                    'list_border_type!' => 'none',
                    'list_layout' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-content--column .ha-feature-list-wrap' => 'border-width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.ha-content--column .ha-list-item:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'list_border_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'list_border_type!' => 'none',
                    'list_layout' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-feature-list-wrap' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .ha-list-item' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'list_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}.ha-content--column .ha-feature-list-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}.ha-content--row .ha-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}}.ha-content--row .ha-list-item, {{WRAPPER}}.ha-content--column .ha-feature-list-wrap',
            ]
        );

        $this->add_control(
            'list_background_color',
            [
                'label' => __( 'background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-list-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __media_style_controls() {

        $this->start_controls_section(
            '_section_icon_style',
            [
                'label' => __( 'Media Type', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Size', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 250,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-icon.icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-icon.number' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-icon.image img' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.ha-icon--row .ha-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.ha-icon--row-reverse .ha-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.ha-icon--column .ha-icon' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'selector' => '{{WRAPPER}} .ha-icon',
            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ha-icon.image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-icon i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-icon svg' => 'fill: {{VALUE}};color: {{VALUE}}',
                    '{{WRAPPER}} .ha-icon span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'icon_background',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-icon' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __text_style_controls() {

        $this->start_controls_section(
            '_section_icon_text',
            [
                'label' => __( 'Text', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .ha-text',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text_link_color',
            [
                'label' => __( 'Link Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.ha-content .ha-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text_link_hover_color',
            [
                'label' => __( 'Hover Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.ha-content:hover .ha-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

		if ( empty($settings['list_item'] ) ) {
			return;
		}
        ?>

        <ul class="ha-feature-list-wrap">
            <?php foreach ( $settings['list_item'] as $index => $item ) :

				// link
				$repeater_key = 'list_item' . $index;
				if ( $item['link']['url'] ) {
					$this->add_render_attribute( $repeater_key, 'class', 'ha-content' );
					$this->add_link_attributes( $repeater_key, $item['link'] );
				}

                // title
                $title = $this->get_repeater_setting_key( 'title', 'list_item', $index );
                // $this->add_inline_editing_attributes( $title, 'basic' );
                $this->add_render_attribute( $title, 'class', 'ha-text' );
                ?>
                <li class="ha-list-item">
                    <?php if ( ! empty( $item['link']['url'] ) ) : ?>
                        <a <?php $this->print_render_attribute_string( $repeater_key ); ?>>
                    <?php else: ?>
                        <div class="ha-content">
                    <?php endif; ?>

						<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
                            <div class="ha-icon icon">
								<?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </div>
                        <?php elseif( $item['number'] ) : ?>
                            <div class="ha-icon number">
                                <span><?php echo esc_html( $item['number'] ); ?></span>
                            </div>
                        <?php elseif( $item['image'] ) :
                            $image = wp_get_attachment_image_url( $item['image']['id'], 'thumbnail', false );
                            if( ! $image ) {
                                $image = $item['image']['url'];
                            }
                            ?>
                            <div class="ha-icon image">
                                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
                            </div>
                        <?php
                        endif;
                        ?>

                        <div <?php $this->print_render_attribute_string( $title ); ?>>
                            <?php echo $item['title']; ?>
                        </div>

                    <?php if ( !empty( $item['link']['url'] ) ) : ?>
                    </a>
                    <?php else: ?>
                    </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php
    }

}
