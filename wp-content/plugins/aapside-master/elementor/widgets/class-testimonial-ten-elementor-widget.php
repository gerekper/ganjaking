<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Testimonial_Ten_Widget extends Widget_Base {

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
    public function get_name() {
        return 'appside-testimonial-ten-widget';
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
    public function get_title() {
        return esc_html__( 'Testimonial: 10', 'aapside-master' );
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
    public function get_icon() {
        return 'eicon-blockquote';
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
    public function get_categories() {
        return [ 'appside_widgets' ];
    }

    /**
     * Register Elementor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {

        $this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__( 'General Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control( 'theme', [
            'label'   => esc_html__( 'Theme', 'aapside-master' ),
            'description' => esc_html__('select theme','aapside-master'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'black' => esc_html__( 'Black', 'aapside-master' ),
                'white' => esc_html__( 'White', 'aapside-master' ),
            ],
            'default' => 'black'
        ] );
        $this->add_control(
            'description_bottom_gap',
            [
                'label'      => esc_html__( 'Description Bottom Gap', 'aapside-master' ),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 500,
                        'step' => 2,
                    ],
                    '%' => [
                        'min'  => 0,
                        'max'  => 500,
                        'step' => 2,
                    ]
                ],
                'size_units' => [ 'px','%' ],
                'selectors' => [
                    "{{WRAPPER}} .single-testimonial-item-10 .author-meta" => 'margin-top: {{SIZE}}{{UNIT}};'
                ]
            ]
        );
        if (version_compare(ELEMENTOR_VERSION, '2.6.0', '>=')) {
            $this->add_control(
                'icon',
                [
                    'label' => esc_html__('Icon', 'aapside-master'),
                    'type' => Controls_Manager::ICONS,
                    'description' => esc_html__('select Icon.', 'aapside-master'),
                    'default' => [
                        'value' => 'fa fa-star',
                        'library' => 'solid',
                    ]
                ]
            );
        } else {
            $this->add_control(
                'icon',
                [
                    'label' => esc_html__('Icon', 'aapside-master'),
                    'type' => Controls_Manager::ICON,
                    'description' => esc_html__('select Icon.', 'aapside-master'),
                ]
            );
        }
        $this->add_control(
            'testimonial_alignment',
            [
                'label' => esc_html__( 'Alignment', 'aapside-master' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'aapside-master' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'aapside-master' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'aapside-master' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    "{{WRAPPER}} .single-testimonial-item-10" => "text-align:{{VALUE}}"
                ]
            ]
        );
        $this->add_control('content_devider',[
            'type' => Controls_Manager::DIVIDER
        ]);
        $this->add_control( 'testimonial_items', [
            'label'       => esc_html__( 'Testimonial Item', 'aapside-master' ),
            'type'        => Controls_Manager::REPEATER,
            'default'     => [
                [
                    'name'        => esc_html__( 'Riley Cassidy', 'aapside-master' ),
                    'image'       => array(
                        'url' => Utils::get_placeholder_image_src()
                    ),
                    'designation' => esc_html__( 'Technician', 'aapside-master' ),
                    'description' => esc_html__( 'Unwilling departure education is be dashwoods or an. Use off agreeable law unwilling sir deficient curiosity instantly. ', 'aapside-master' )
                ],
                [
                    'name'        => esc_html__( 'Riley Cassidy', 'aapside-master' ),
                    'image'       => array(
                        'url' => Utils::get_placeholder_image_src()
                    ),
                    'designation' => esc_html__( 'Technician', 'aapside-master' ),
                    'description' => esc_html__( 'Unwilling departure education is be dashwoods or an. Use off agreeable law unwilling sir deficient curiosity instantly. ', 'aapside-master' )
                ],
            ],
            'fields'      => [
                [
                    'name'        => 'image_status',
                    'label'       => esc_html__( 'Image Show/Hide', 'aapside-master' ),
                    'type'        => Controls_Manager::SWITCHER,
                    'description' => esc_html__( 'show/hide image', 'aapside-master' ),
                ],
                [
                    'name'        => 'image',
                    'label'       => esc_html__( 'Image', 'aapside-master' ),
                    'type'        => Controls_Manager::MEDIA,
                    'description' => esc_html__( 'enter title.', 'aapside-master' ),
                    'default'     => array(
                        'url' => Utils::get_placeholder_image_src()
                    ),
                    'condition' => ['image_status' => 'yes']
                ],
                [
                    'name'        => 'name',
                    'label'       => esc_html__( 'Name', 'aapside-master' ),
                    'type'        => Controls_Manager::TEXT,
                    'description' => esc_html__( 'enter name', 'aapside-master' ),
                    'default'     => esc_html__( 'Lara Croft', 'aapside-master' )
                ],
                [
                    'name'        => 'designation_status',
                    'label'       => esc_html__( 'Designation Show/Hide', 'aapside-master' ),
                    'type'        => Controls_Manager::SWITCHER,
                    'description' => esc_html__( 'show/hide designation', 'aapside-master' ),
                ],
                [
                    'name'        => 'designation',
                    'label'       => esc_html__( 'Designation', 'aapside-master' ),
                    'type'        => Controls_Manager::TEXT,
                    'description' => esc_html__( 'enter designation', 'aapside-master' ),
                    'default'     => esc_html__( 'CEO, Appside', 'aapside-master' ),
                    'condition' => ['designation_status' => 'yes']
                ],
                [
                    'name'        => 'description',
                    'label'       => esc_html__( 'Description', 'aapside-master' ),
                    'type'        => Controls_Manager::TEXTAREA,
                    'description' => esc_html__( 'enter description', 'aapside-master' ),
                    'default'     => esc_html__( 'They  provide innovative solutions with the best.  tempor incididunt utla bore et dolor  tempor incididunt .', 'aapside-master' )
                ]

            ],
            'title_field' => '{{name}}'
        ] );
        $this->end_controls_section();

        $this->start_controls_section(
            'slider_settings_section',
            [
                'label' => esc_html__( 'Slider Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'items',
            [
                'label'       => esc_html__( 'Items', 'aapside-master' ),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__( 'you can set how many item show in slider', 'aapside-master' ),
                'default'     => '1'
            ]
        );
        $this->add_control(
            'margin',
            [
                'label'       => esc_html__( 'Margin', 'aapside-master' ),
                'description' => esc_html__( 'you can set margin for slider', 'aapside-master' ),
                'type'        => Controls_Manager::SLIDER,
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 5,
                    ]
                ],
                'default'     => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'size_units'  => [ 'px' ]
            ]
        );
        $this->add_control(
            'loop',
            [
                'label'       => esc_html__( 'Loop', 'aapside-master' ),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
                'default'     => 'yes'
            ]
        );
        $this->add_control(
            'autoplay',
            [
                'label'       => esc_html__( 'Autoplay', 'aapside-master' ),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
                'default'     => 'yes'
            ]
        );
        $this->add_control(
            'autoplaytimeout',
            [
                'label'      => esc_html__( 'Autoplay Timeout', 'aapside-master' ),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 10000,
                        'step' => 2,
                    ]
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 5000,
                ],
                'size_units' => [ 'px' ],
                'condition'  => array(
                    'autoplay' => 'yes'
                )
            ]

        );
        $this->end_controls_section();

        $this->start_controls_section( 'styling_section', [
            'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $this->add_control(
            'author_meta_top_gap',
            [
                'label' => esc_html__( 'Author Top Gap', 'aapside-master' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
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
                    '{{WRAPPER}} .single-testimonial-item-10 .author-meta' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control('styling_settings_divider_01',[
            'type' => Controls_Manager::DIVIDER
        ]);
        $this->add_control( 'name_color', [
            'type'      => Controls_Manager::COLOR,
            'label'     => esc_html__( 'Name Color', 'aapside-master' ),
            'selectors' => [
                "{{WRAPPER}} .single-testimonial-item-10 .author-meta .title" => "color: {{VALUE}}"
            ]
        ] );
        $this->add_control( 'description_color', [
            'type'      => Controls_Manager::COLOR,
            'label'     => esc_html__( 'Description Color', 'aapside-master' ),
            'selectors' => [
                "{{WRAPPER}} .single-testimonial-item-10 .content p" => "color: {{VALUE}}"
            ]
        ] );
        $this->add_control( 'designation_color', [
            'type'      => Controls_Manager::COLOR,
            'label'     => esc_html__( 'Designation Color', 'aapside-master' ),
            'selectors' => [
                "{{WRAPPER}} .single-testimonial-item-10 .author-meta .designation" => "color: {{VALUE}}"
            ]
        ] );
        $this->end_controls_section();
        $this->start_controls_section(
            'typogrpahy_settings_section',
            [
                'label' => esc_html__( 'Tyography Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'name_typography',
            'label'    => esc_html__( 'Name Typography', 'aapside-master' ),
            "selector" => "{{WRAPPER}} .single-testimonial-item-10 .author-meta .title"
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'description_typography',
            'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
            "selector" => "{{WRAPPER}} .single-testimonial-item-10 .content p"
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'designation_typography',
            'label'    => esc_html__( 'Designation Typography', 'aapside-master' ),
            "selector" => "{{WRAPPER}} .single-testimonial-item-10 .author-meta .designation"
        ] );
        $this->end_controls_section();
    }

    /**
     * Render Elementor widget output on the frontend.
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings              = $this->get_settings_for_display();
        $all_testimonial_items = $settings['testimonial_items'];
        $rand_numb             = rand( 333, 999999999 );

        //slider settings
        $loop            = $settings['loop'] ? 'true' : 'false';
        $items           = $settings['items'] ? $settings['items'] : 4;
        $autoplay        = $settings['autoplay'] ? 'true' : 'false';
        $autoplaytimeout = $settings['autoplaytimeout']['size'];
        ?>
        <div class="testimonial-carousel-wrapper appside-rtl-slider">
            <div class="appside-testimonial-carousel-04 owl-carousel"
                 id="testimonial-one-carousel-04-<?php echo esc_attr( $rand_numb ); ?>"
                 data-loop="<?php echo esc_attr( $loop ); ?>"
                 data-margin="<?php echo esc_attr( $settings['margin']['size'] ); ?>"
                 data-items="<?php echo esc_attr( $items ); ?>"
                 data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
                 data-autoplaytimeout="<?php echo esc_attr( $autoplaytimeout ); ?>"
            >
                <?php
                foreach ( $all_testimonial_items as $item ):
                    $image_id = $item['image']['id'];
                    $image_url = wp_get_attachment_image_src( $image_id, 'full', false );
                    $image_alt = get_post_meta( '_wp_attachment_image_alt', $image_id, true );
                    ?>
                    <div class="single-testimonial-item-10 <?php echo esc_attr($settings['theme']);?>">
                        <div class="thumb <?php echo esc_attr($settings['testimonial_alignment'])?>">
                            <?php
                            if (!empty($image_id) && !empty($item['image_status'])){
                                printf('<img src="%1$s" alt="%2$s" />',esc_url( $image_url[0] ),esc_attr( $image_alt ));
                            }
                            ?>
                        </div>
                        <div class="content">
                            <div class="content-wrap">
                                <div class="icon">
                                    <?php
                                    if (version_compare(ELEMENTOR_VERSION, '2.6.0', '>=')) {
                                        Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
                                    } else {
                                        printf('<i class="%1$s"></i>', esc_attr($settings['icon']));
                                    }
                                    ?>
                                </div>
                                <p class="description"><?php echo esc_html( $item['description'] ); ?></p>
                                <div class="author-details">
                                    <div class="author-meta">
                                        <h4 class="title"><?php echo esc_html( $item['name'] ); ?></h4>
                                        <?php
                                        if (!empty($item['designation_status'])){
                                            printf('<div class="designation">%1$s</div>',esc_html( $item['designation'] ));
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Testimonial_Ten_Widget() );
