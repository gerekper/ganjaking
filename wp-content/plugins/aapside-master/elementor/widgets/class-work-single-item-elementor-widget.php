<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Work_Single_Item_Widget extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve Elementor widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name()
    {
        return 'appside-work-single-item-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Elementor widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('Work Single Item', 'aapside-master');
    }

    /**
     * Get widget icon.
     *
     * Retrieve Elementor widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon()
    {
        return 'eicon-alert';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Elementor widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_categories()
    {
        return ['appside_widgets'];
    }

    /**
     * Register Elementor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {

        $this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__('General Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'aapside-master'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('enter  title.', 'aapside-master'),
                'default' => esc_html__('Strategy & Design', 'aapside-master')
            ]
        );

        if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
            $repeater->add_control(
                'icon',
                [
                    'label'       => esc_html__( 'Icon', 'aapside-master' ),
                    'type'        => Controls_Manager::ICONS,
                    'description' => esc_html__( 'select Icon.', 'aapside-master' ),
                    'default'     => [
                        'value'   => 'fa fa-star',
                        'library' => 'solid',
                    ]
                ]
            );
        } else {
            $repeater->add_control(
                'icon',
                [
                    'label'       => esc_html__( 'Icon', 'aapside-master' ),
                    'type'        => Controls_Manager::ICON,
                    'description' => esc_html__( 'select Icon.', 'aapside-master' ),
                ]
            );
        };
       
        $repeater->add_control(
            'icon_style',
            [
                'label' => esc_html__( 'Icon Style', 'attorg-master' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'  => esc_html__( 'Default', 'aapside-master' ),
                    'style-01'  => esc_html__( 'Style 01', 'aapside-master' ),
                    'style-02' => esc_html__( 'Style 02', 'aapside-master' ),
                    'style-03' => esc_html__( 'Style 03', 'aapside-master' ),
                ],
            ]
        );
        $repeater->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'aapside-master'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => esc_html__('enter text.', 'aapside-master'),
                'default' => esc_html__('Dozens of leading utility providers like National Grid are gaining enhanced', 'aapside-master')
            ]
        );

        $this->add_control(
            'work_list',
            [
                'label' => esc_html__( 'Repeater List', 'plugin-domain' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => esc_html__( 'Eyes on the Ground', 'aapside-master' ),
                        'description' => esc_html__( 'Dozens of leading utility providers like National Grid are gaining enhanced', 'aapside-master' ),
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'icon_styling_section',
            [
                'label' => esc_html__('Icon Styling Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_icon_border_radius',
            [
                'label' => esc_html__('Icon Border Radius', 'aapside-master'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .work-single-item .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'icon_bottom_space',
            [
                'label' => esc_html__('Icon bottom Space', 'aapside-master'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .work-single-item .icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'icon_box_styling_settings_section',
            [
                'label' => esc_html__('Icon Box Styling', 'attorg-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'icon_box_style_tabs'
        );

        $this->start_controls_tab(
            'icon_box_style_normal_tab',
            [
                'label' => esc_html__('Normal', 'attorg-master'),
            ]
        );
        $this->add_control('background-color', [
            'label' => esc_html__('Background Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item" => "background-color: {{VALUE}}"
            ]
        ]);
        $this->add_control('.icon_color', [
            'label' => esc_html__('Icon Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item .icon" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control('.icon_bg_color', [
            'label' => esc_html__('Icon Background Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item .icon" => "background-color: {{VALUE}}"
            ]
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_box_style_hover_tab',
            [
                'label' => esc_html__('Hover', 'attorg-master'),
            ]
        );
        $this->add_control('background-hover-color', [
            'label' => esc_html__('Background Hover Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item:hover" => "background-color: {{VALUE}}"
            ]
        ]);
        $this->add_control('.icon_hover_color', [
            'label' => esc_html__('Icon Hover Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item:hover .icon" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control('.text_icon_bg_color', [
            'label' => esc_html__('Icon Hover Background Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item:hover .icon" => "background-color: {{VALUE}}"
            ]
        ]);
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->start_controls_section(
            'styling_section',
            [
                'label' => esc_html__('Styling Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_bottom_space',
            [
                'label' => esc_html__('Title Bottom Space', 'aapside-master'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .work-single-item .content .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control('title_color', [
            'label' => esc_html__('Title Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item .content .title" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control('description_color', [
            'label' => esc_html__('Description Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .work-single-item .content p" => "color: {{VALUE}}"
            ]
        ]);
        $this->end_controls_section();
        $this->start_controls_section(
            'typography_styling_section',
            [
                'label' => esc_html__('Typography Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'label' => esc_html__('Title Typography', 'aapside-master'),
            'selector' => "{{WRAPPER}} .work-single-item .content .title"
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'description_typography',
            'label' => esc_html__('Description Typography', 'aapside-master'),
            'selector' => "{{WRAPPER}} .work-single-item .content p"
        ]);
        $this->end_controls_section();
    }

    /**
     * Render Elementor widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        ?>
        <ul class="work-list-item">
        <?php foreach ( $settings['work_list'] as $item ):?>
        <li class="work-single-item margin-bottom-30">
            <div class="content">
                <div class="icon <?php echo esc_attr($item['icon_style'])?>">
                    <?php
                    if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
                        Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        printf( '<i class="%1$s"></i>', esc_attr( $item['icon'] ) );
                    }
                    ?>
                </div>
                <h4 class="title"><?php echo esc_html__($item['title']) ?></h4>
                <p><?php echo esc_html__($item['description']) ?></p>
            </div>
        </li>
        <?php endforeach; ?>
        </ul>
        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type(new Appside_Work_Single_Item_Widget());