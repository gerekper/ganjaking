<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Contact_Single_Item_02_Widget extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve Elementor widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'appside-contact-single-item-two-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Elementor widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Contact Single 02', 'aapside-master' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve Elementor widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-editor-list-ul';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Elementor widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
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

        $repeater = new Repeater();
        if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
            $repeater->add_control(
                'icon',
                [
                    'label'       => esc_html__( 'Icon', 'aapside-master' ),
                    'type'        => Controls_Manager::ICONS,
                    'description' => esc_html__( 'select Icon.', 'aapside-master' ),
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
        }
        $repeater->add_control('text',[
            'label' => esc_html__('Text','aapside-master'),
            'type' => Controls_Manager::TEXTAREA,
            'description' => esc_html__('you can use multiple line','aapside-master')
        ]);
        $this->add_control(
            'icon_list_items',
            [
                'label' => __( 'Icon List Items', 'aapside-master' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'icon_styling_section',
            [
                'label' => esc_html__( 'Icon Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_gap',
            [
                'label' => esc_html__( 'Icon Gap', 'aapside-master' ),
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
                    '{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control('icon_color',[
            'label' => esc_html__('Icon Color','aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .icon" => "color: {{VALUE}}",
                "{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .icon svg path" => "fill: {{VALUE}}"
            ]
        ]);
        $this->end_controls_section();

        $this->start_controls_section(
            'styling_section',
            [
                'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'gap_between_items',
            [
                'label' => esc_html__( 'Gap Between Items', 'aapside-master' ),
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
                    '{{WRAPPER}} .contact-info-list-style-one li+li' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control('text_color',[
            'label' => esc_html__('Text Color','aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .content .details" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control(
            'gap_between_text',
            [
                'label' => esc_html__( 'Gap Between Text', 'aapside-master' ),
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
                    '{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .content .details + .details' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'typography_section',
            [
                'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(Group_Control_Typography::get_type(),[
            'name' => 'text_typography',
            'label' => esc_html__('Text Typography','aapside-master'),
            'selector' => "{{WRAPPER}} .contact-info-list-style-one .single-contact-item-02 .content .details"
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
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <ul class="contact-info-list-style-one">
            <?php
            $all_list_items = $settings['icon_list_items'];
            foreach ($all_list_items as $item):?>
                <li class="single-contact-item-02">
                    <div class="icon">
                        <?php
                        if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
                            ! empty( $item['icon']['value'] ) ? Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ) : '';
                        } else {
                            ! empty( $item['icon'] ) ? sprintf( '<i class="%1$s"></i>', $item['icon'] ) : '';
                            echo $item['icon'];
                        }
                        ?>
                    </div>
                    <div class="content">
                        <?php
                        $details_text = explode("\n",$item['text']);
                        foreach ($details_text as $detail){
                            printf('<span class="details">%1$s</span>',esc_html($detail));
                        }
                        ?>
                    </div>
                </li>
            <?php
            endforeach;
            ?>
        </ul>
        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Contact_Single_Item_02_Widget() );