<?php
/**
 * Hotspots widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Hotspots extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Hotspots', 'happy-addons-pro' );
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
        return 'hm hm-hot-spot';
    }

    public function get_keywords() {
        return [ 'hot', 'spots', 'point', 'product' ];
    }

	/**
     * Register widget content controls
     */
    protected function register_content_controls() {
		$this->__image_content_controls();
		$this->__spots_content_controls();
		$this->__options_content_controls();
	}

    protected function __image_content_controls() {

        $this->start_controls_section(
            '_section_image',
            [
                'label' => __( 'Image', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'image',
            [
                'show_label' => false,
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __( 'Image Size', 'happy-addons-pro' ),
                'default' => 'large',
            ]
        );

        $this->end_controls_section();
	}

    protected function __spots_content_controls() {

        $this->start_controls_section(
            '_section_spots',
            [
                'label' => __( 'Spots', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs( '_tabs_spots' );

        $repeater->start_controls_tab(
            '_tab_spot',
            [
                'label' => __( 'Spot', 'happy-addons-pro' )
            ]
        );

        $repeater->add_control(
            'type',
            [
                'label' => __( 'Type', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text' => [
                        'title' => __( 'Text', 'happy-addons-pro' ),
                        'icon' => 'eicon-text-area',
                    ],
                    'icon' => [
                        'title' => __( 'Icon', 'happy-addons-pro' ),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => __( 'Image', 'happy-addons-pro' ),
                        'icon' => 'eicon-image',
                    ],
                ],
                'default' => 'icon',
                'toggle' => false,
            ]
        );

        $repeater->add_control(
            'text',
            [
                'default' => '+',
                'type' => Controls_Manager::TEXT,
                'label' => __( 'Text', 'happy-addons-pro' ),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type'	=> 'text'
                ]
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => __( 'Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'library' => 'solid',
                    'value' => 'fas fa-plus',
                ],
                'condition' => [
                    'type' => 'icon'
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'show_label' => false,
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'type' => 'image'
                ],
            ]
        );

        $repeater->add_responsive_control(
            'x_pos',
            [
                'label' => __( 'X Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'separator' => 'before',
                'size_units' => ['%'],
                'desktop_default' => [
                    'size' => 50,
                    'unit' => '%'
                ],
                'tablet_default' => [
                    'unit' => '%'
                ],
                'mobile_default' => [
                    'unit' => '%'
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => .1
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'ui',
                'frontend_available' => true,
            ]
        );

        $repeater->add_responsive_control(
            'y_pos',
            [
                'label' => __( 'Y Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'desktop_default' => [
                    'size' => 45,
                    'unit' => '%'
                ],
                'tablet_default' => [
                    'unit' => '%'
                ],
                'mobile_default' => [
                    'unit' => '%'
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => .1
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'ui',
                'frontend_available' => true,
            ]
        );

        $repeater->add_control(
            'css_id',
            [
                'label' => __( 'CSS ID', 'happy-addons-pro' ),
                'title' => __( 'Add your custom id. e.g: my-custom-id', 'happy-addons-pro' ),
                'separator' => 'before',
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true
                ],
            ]
        );

        $repeater->add_control(
            'css_classes',
            [
                'label' => __( 'CSS Classes', 'happy-addons-pro' ),
                'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-custom-class', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'prefix_class' => '',
                'dynamic' => [
                    'active' => true
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            '_tab_tooltip',
            [
                'label' => __( 'Tooltip', 'happy-addons-pro' )
            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => __( 'Content', 'happy-addons-pro' ),
                'separator' => 'before',
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Hotspot tooltip content goes here', 'happy-addons-pro' ),
            ]
        );

        $repeater->add_control(
            'position',
            [
                'label' => __( 'Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'separator' => 'before',
                'default' => '',
                'options' => [
                    '' => __( 'Default', 'happy-addons-pro' ),
                    'left' => __( 'Left', 'happy-addons-pro' ),
                    'top' => __( 'Top', 'happy-addons-pro' ),
                    'right' => __( 'Right', 'happy-addons-pro' ),
                    'bottom' => __( 'Bottom', 'happy-addons-pro' ),
                    'top-left' => __( 'Top Left', 'happy-addons-pro' ),
                    'top-right' => __( 'Top Right', 'happy-addons-pro' ),
                    'bottom-left' => __( 'Bottom Left', 'happy-addons-pro' ),
                    'bottom-right' => __( 'Bottom Right', 'happy-addons-pro' ),
                ]
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'spots',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'type' => 'icon',
                        'icon' => [
                            'library' => 'solid',
                            'value' => 'fas fa-plus',
                        ],
                        'x_pos' => [
                            'size' => 47,
                            'unit' => '%'
                        ],
                        'y_pos' => [
                            'size' => 43,
                            'unit' => '%'
                        ],
                        'content' => 'Tooltip content goes here'
                    ]
                ]
            ]
        );

        $this->end_controls_section();
	}

    protected function __options_content_controls() {

        $this->start_controls_section(
            '_section_options',
            [
                'label' => __( 'Options', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'tooltip_position',
            [
                'label' => __( 'Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'frontend_available' => true,
                'default' => 'top',
                'options' => [
                    'left' => __( 'Left', 'happy-addons-pro' ),
                    'top' => __( 'Top', 'happy-addons-pro' ),
                    'right' => __( 'Right', 'happy-addons-pro' ),
                    'bottom' => __( 'Bottom', 'happy-addons-pro' ),
                    'top-left' => __( 'Top Left', 'happy-addons-pro' ),
                    'top-right' => __( 'Top Right', 'happy-addons-pro' ),
                    'bottom-left' => __( 'Bottom Left', 'happy-addons-pro' ),
                    'bottom-right' => __( 'Bottom Right', 'happy-addons-pro' ),
                ],
                'render_type' => 'ui'
            ]
        );

        $this->add_control(
            'tooltip_speed',
            [
                'label' => __( 'Speed', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'step' => 10,
                'max' => 10000,
                'title' => __( 'Speed in milliseconds (default 400)', 'happy-addons-pro' ),
                'frontend_available' => true,
                'placeholder' => 400,
                'render_type' => 'ui'
            ]
        );

        $this->add_control(
            'tooltip_delay',
            [
                'label' => __( 'Delay', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'step' => 10,
                'max' => 10000,
                'title' => __( 'Delay in milliseconds (default 200)', 'happy-addons-pro' ),
                'frontend_available' => true,
                'placeholder' => 200,
                'render_type' => 'ui'
            ]
        );

        $this->add_control(
            'tooltip_hide_delay',
            [
                'label' => __( 'Hide Delay', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 10,
                'max' => 100000,
                'title' => __( 'Hide delay in milliseconds (default 0)', 'happy-addons-pro' ),
                'frontend_available' => true,
                'placeholder' => 0,
                'render_type' => 'ui'
            ]
        );

        $this->add_control(
            'tooltip_hide_arrow',
            [
                'label' => __( 'Hide Arrow', 'happy-addons-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'happy-addons-pro' ),
                'label_off' => __( 'No', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'frontend_available' => true,
                'render_type' => 'ui',
            ]
        );

        $this->add_control(
            'tooltip_hover',
            [
                'label' => __( 'Hover', 'happy-addons-pro' ),
                'description' => __( 'Make sure to enable this option when you have a link in tooltip content.', 'happy-addons-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'happy-addons-pro' ),
                'label_off' => __( 'No', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'frontend_available' => true,
                'render_type' => 'ui',
            ]
        );

        $this->end_controls_section();
    }

	/**
     * Register widget style controls
     */
    protected function register_style_controls() {
		$this->__image_style_controls();
		$this->__spots_style_controls();
		$this->__tooltip_style_controls();
	}

    protected function __image_style_controls() {

        $this->start_controls_section(
            '_section_style_image',
            [
                'label' => __( 'Image', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'desktop_default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__figure' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __( 'Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__figure' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__figure img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .ha-hotspots__figure img',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__figure img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .ha-hotspots__figure img',
            ]
        );

        $this->end_controls_section();
	}

    protected function __spots_style_controls() {

        $this->start_controls_section(
            '_section_style_spots',
            [
                'label' => __( 'Spots', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'spot_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 500,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'spot_height',
            [
                'label' => __( 'Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 500,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'spot_font_size',
            [
                'label' => __( 'Font / Icon Size', 'happy-addons-pro' ),
                'description' => __( 'Applicable for icon and text spot type', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'spot_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'spot_border',
                'selector' => '{{WRAPPER}} .ha-hotspots__item-inner'
            ]
        );

        $this->add_responsive_control(
            'spot_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( '_tabs_spot' );

        $this->start_controls_tab(
            '_tab_spot_normal',
            [
                'label' => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'spot_text_color',
            [
                'label' => __( 'Text Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-hotspots__item svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'spot_bg_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'spot_box_shadow',
                'selector' => '{{WRAPPER}} .ha-hotspots__item'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_spot_hover',
            [
                'label' => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'spot_hover_text_color',
            [
                'label' => __( 'Text Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item:hover, {{WRAPPER}} .ha-hotspots__item:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-hotspots__item:hover svg, {{WRAPPER}} .ha-hotspots__item:focus svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'spot_hover_bg_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item:hover, {{WRAPPER}} .ha-hotspots__item:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'spot_hover_border_color',
            [
                'label' => __( 'Border Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-hotspots__item:hover, {{WRAPPER}} .ha-hotspots__item:focus' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'spot_border_border!' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'spot_hover_box_shadow',
                'selector' => '{{WRAPPER}} .ha-hotspots__item:hover, {{WRAPPER}} .ha-hotspots__item:focus'
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

    protected function __tooltip_style_controls() {

        $this->start_controls_section(
            '_section_style_tooltip',
            [
                'label' => __( 'Tooltip', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tooltip_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                    ],
                ],
                'frontend_available' => true,
                'render_type' => 'ui'
            ]
        );

        $this->add_responsive_control(
            'tooltip_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '.ha-hotspots--{{ID}}.tipso_bubble' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '.ha-hotspots--{{ID}}.tipso_bubble' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'tooltip_color',
            [
                'label' => __( 'Text Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'frontend_available' => true,
                'default' => '#fff',
                'render_type' => 'ui'
            ]
        );

        $this->add_control(
            'tooltip_bg_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'frontend_available' => true,
                'default' => '#562dd4',
                'render_type' => 'ui'
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tooltip_typograhpy',
                'selector' => '.tipso_bubble.ha-hotspots--{{ID}} .tipso_content'
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tooltip_box_shadow',
                'selector' => '.tipso_bubble.ha-hotspots--{{ID}}'
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="ha-hotspots__inner">
            <figure class="ha-hotspots__figure">
                <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' ); ?>
            </figure>

            <?php
            if ( ! empty( $settings['spots'] ) ) :
                foreach ( $settings['spots'] as $index => $spot ) :
                    $tooltip_id = $this->get_id() . $spot['_id'];

                    $this->add_render_attribute( 'spot-' . $index, [
                        'href' => '#',
                        'data-index' => $index,
                        'data-target' => $tooltip_id,
                        'class' => 'ha-hotspots__item elementor-repeater-item-' . $spot['_id'],
                        'data-settings' => json_encode( [
                            'position' => $spot['position'],
                        ] )
                    ] );

                    if ( ! empty( $spot['css_classes'] ) ) {
                        $this->add_render_attribute( 'spot-' . $index, 'class', esc_attr( $spot['css_classes'] ) );
                    }

                    if ( ! empty( $spot['css_id'] ) ) {
                        $this->add_render_attribute( 'spot-' . $index, 'id', esc_attr( $spot['css_id'] ) );
                    }
                    ?>
                    <div role="tooltip" id="ha-<?php echo $tooltip_id; ?>" class="screen-reader-text"><?php echo $this->parse_text_editor( $spot['content'] ); ?></div>
                    <a <?php echo $this->print_render_attribute_string( 'spot-' . $index ); ?>>
                        <span class="ha-hotspots__item-inner">
                            <?php
                            if ( $spot['type'] === 'icon' ) {
                                Icons_Manager::render_icon( $spot['icon'], ['aria-hidden' => true ] );
                            } elseif ( $spot['type'] === 'image') {
                                echo wp_get_attachment_image( $spot['image']['id'] );
                            } else {
                                echo esc_html( $spot['text'] );
                            }
                            ?>
                        </span>
                    </a>
                <?php endforeach;
            endif;
            ?>
        </div>
        <?php
    }
}
