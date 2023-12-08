<?php
    
    namespace ElementPack\Modules\BookedCalendar\Widgets;
    
    use ElementPack\Base\Module_Base;
    use Elementor\Controls_Manager;
    use Elementor\Group_Control_Border;
    use Elementor\Group_Control_Background;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Group_Control_Typography;
    
    if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class BookedCalendar extends Module_Base {
        
        protected $_has_template_content = false;
        
        public function get_name() {
            return 'bdt-booked';
        }
        
        public function get_title() {
            return BDTEP . __( 'Booked Calendar', 'bdthemes-element-pack' );
        }
        
        public function get_icon() {
            return 'bdt-wi-booked-calendar';
        }
        
        public function get_categories() {
            return ['element-pack'];
        }
        
        public function get_keywords() {
            return ['booked', 'calendar', 'appointment', 'schedule'];
        }
        
        public function get_style_depends() {
            if ( $this->ep_is_edit_mode() ) {
                return ['ep-styles'];
            } else {
                return ['ep-booked-calendar'];
            }
        }
        
        public function get_custom_help_url() {
            return 'https://youtu.be/bodvi_5NkDU';
        }
        
        protected function register_controls() {
            $this->start_controls_section(
                'section_content_layout',
                [
                    'label' => __( 'Layout', 'bdthemes-element-pack' ),
                ]
            );
            
            $custom_calendars = get_terms( ['taxonomy' => 'booked_custom_calendars', 'hide_empty' => false] );
            
            $calendar_list = ['' => 'Default Calendar'];
            foreach ( $custom_calendars as $category ) {
                $calendar_list[$category->term_id] = $category->name;
            }
            
            $this->add_control(
                'calendar_type',
                [
                    'label'   => esc_html__( 'Calendar Type', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'options' => $calendar_list,
                    'default' => '',
                ]
            );
            
            $this->add_control(
                'calendar_style',
                [
                    'label'   => esc_html__( 'Layout', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        ''     => esc_html__( 'Default', 'bdthemes-element-pack' ),
                        'list' => esc_html__( 'List', 'bdthemes-element-pack' ),
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_day',
                [
                    'label'   => esc_html__( 'Day', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => date( 'd' ),
                    'options' => [
                        '01' => '01',
                        '02' => '02',
                        '03' => '03',
                        '04' => '04',
                        '05' => '05',
                        '06' => '06',
                        '07' => '07',
                        '08' => '08',
                        '09' => '09',
                        '10' => '10',
                        '11' => '11',
                        '12' => '12',
                        '13' => '13',
                        '14' => '14',
                        '15' => '15',
                        '16' => '16',
                        '17' => '17',
                        '18' => '18',
                        '19' => '19',
                        '20' => '20',
                        '21' => '21',
                        '22' => '22',
                        '23' => '23',
                        '24' => '24',
                        '25' => '25',
                        '26' => '26',
                        '27' => '27',
                        '28' => '28',
                        '29' => '29',
                        '30' => '30',
                        '31' => '31',
                    ],
                    'condition' => [
                        'calendar_style' => 'list'
                    ]
                ]
            );
            
            $this->add_control(
                'calendar_month',
                [
                    'label'   => esc_html__( 'Month', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => date( 'm' ),
                    'options' => [
                        '01' => esc_html__( 'January', 'bdthemes-element-pack' ),
                        '02' => esc_html__( 'February', 'bdthemes-element-pack' ),
                        '03' => esc_html__( 'March', 'bdthemes-element-pack' ),
                        '04' => esc_html__( 'April', 'bdthemes-element-pack' ),
                        '05' => esc_html__( 'May', 'bdthemes-element-pack' ),
                        '06' => esc_html__( 'June', 'bdthemes-element-pack' ),
                        '07' => esc_html__( 'July', 'bdthemes-element-pack' ),
                        '08' => esc_html__( 'August', 'bdthemes-element-pack' ),
                        '09' => esc_html__( 'September', 'bdthemes-element-pack' ),
                        '10' => esc_html__( 'October', 'bdthemes-element-pack' ),
                        '11' => esc_html__( 'November', 'bdthemes-element-pack' ),
                        '12' => esc_html__( 'December', 'bdthemes-element-pack' ),
                    ],
                    'condition' => [
                        'calendar_style' => 'list'
                    ]
                ]
            );
            
            $this->add_control(
                'calendar_year',
                [
                    'label'   => esc_html__( 'Year', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => date( 'Y' ),
                    'options' => [
                        '2018' => '2018',
                        '2019' => '2019',
                        '2020' => '2020',
                        '2021' => '2021',
                        '2022' => '2022',
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                        '2027' => '2027',
                        '2028' => '2028',
                        '2029' => '2029',
                        '2030' => '2030',
                    ],
                    'condition' => [
                        'calendar_style' => 'list'
                    ]
                ]
            );
            
            $this->add_control(
                'calendar_size',
                [
                    'label'   => esc_html__( 'Calendar Size', 'bdthemes-element-pack' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        ''      => esc_html__( 'Default', 'bdthemes-element-pack' ),
                        'small' => esc_html__( 'Small', 'bdthemes-element-pack' ),
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_members_only',
                [
                    'label' => esc_html__( 'Members Only', 'bdthemes-element-pack' ),
                    'type'  => Controls_Manager::SWITCHER,
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_design_header',
                [
                    'label'     => __( 'Header', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style!' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'header_background',
                [
                    'label'     => __( 'Header Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar thead th' => 'background-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar tr.days'  => 'background-color: transparent !important',
                        '{{WRAPPER}} table.booked-calendar thead'    => 'background-color: transparent !important',
                    ],
                ]
            );
            
            $this->add_control(
                'header_color',
                [
                    'label'     => __( 'Header Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar thead th' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_control(
                'border_color',
                [
                    'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar thead th'                    => 'border-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar tr.days th'                  => 'border-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td:first-child'              => 'border-left-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td'                          => 'border-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar'                             => 'border-bottom-color: {{VALUE}} !important;',
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot' => 'border-color: {{VALUE}} ;',
                    ],
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_design_date',
                [
                    'label'     => __( 'Date', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style!' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'date_background',
                [
                    'label'     => __( 'Date Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td .date' => 'background-color: {{VALUE}};',
                    ],
                    'separator' => 'before',
                ]
            );
            
            $this->add_control(
                'date_color',
                [
                    'label'     => __( 'Date Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'date_hover_background',
                [
                    'label'     => __( 'Date Hover Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td:hover .date span' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'date_hover_color',
                [
                    'label'     => __( 'Date Hover Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td:hover .date span' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'current_date_color',
                [
                    'label'     => __( 'Current Date Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.today .date span' => 'color: {{VALUE}} !important;',
                    ],
                    'separator' => 'before',
                ]
            );
            
            $this->add_control(
                'current_date_border_color',
                [
                    'label'     => __( 'Current Date Border Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.today .date span' => 'border-color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'current_date_hover_background',
                [
                    'label'     => __( 'Current Date Hover Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.today:hover .date span' => 'background-color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_control(
                'current_date_hover_color',
                [
                    'label'     => __( 'Current Date Hover Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.today:hover .date span' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_control(
                'prev_next_date_background',
                [
                    'label'     => __( 'Prev Date/Next Month Date Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.prev-month .date'           => 'background-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td.next-month .date'           => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} table.booked-calendar td.prev-date:hover .date'      => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} table.booked-calendar td.prev-date .date'            => 'background-color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td.prev-date:hover .date span' => 'background-color: {{VALUE}} !important;',
                    ],
                    'separator' => 'before',
                ]
            );
            
            $this->add_control(
                'prev_next_date_color',
                [
                    'label'     => __( 'Prev/Next Date Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar td.prev-date .date'            => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td.prev-month .date span'      => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td.next-month .date span'      => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} table.booked-calendar td.prev-date:hover .date span' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_design_apointments',
                [
                    'label'     => __( 'Appointments', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style!' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'background',
                [
                    'label'     => __( 'Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar .booked-appt-list'                 => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot:hover' => 'background-color: rgba(255, 255, 255, 0.3);',
                    ],
                ]
            );
            
            $this->add_control(
                'text_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list h2'                                     => 'color: {{VALUE}};',
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot .timeslot-time'               => 'color: {{VALUE}};',
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot .timeslot-time i.booked-icon' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'active_date_background_color',
                [
                    'label'     => __( 'Active Date Background Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.booked-calendar tr.week td.active .date'                      => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} table.booked-calendar tr.week td.active:hover .date'                => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} table.booked-calendar tr.entryBlock'                                => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot .spots-available' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_style_heading',
                [
                    'label'     => __( 'Heading', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'list_heading_color',
                [
                    'label'     => __( 'Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .booked-appt-list > h2' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'list_heading_typography',
                    //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                    'selector' => '{{WRAPPER}} .booked-appt-list > h2',
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_style_time',
                [
                    'label'     => __( 'Time', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'list_time_color',
                [
                    'label'     => __( 'Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .timeslot-range' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'list_time_icon_color',
                [
                    'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .timeslot-range .booked-icon.booked-icon-clock' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'list_text_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .spots-available' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'list_time_typography',
                    //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                    'selector' => '{{WRAPPER}} .timeslot-range',
                ]
            );
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_style_navigation_button',
                [
                    'label'     => __( 'Navigation Button', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style' => 'list',
                    ],
                ]
            );
            
            $this->start_controls_tabs( 'tabs_navigation_button_style' );
            
            $this->start_controls_tab(
                'tab_navigation_button_normal',
                [
                    'label' => __( 'Normal', 'bdthemes-element-pack' ),
                ]
            );
            
            $this->add_control(
                'navigation_button_text_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} [class*="booked-list-view-date-"]' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'      => 'navigation_button_background',
                    'types'     => ['classic', 'gradient'],
                    'selector'  => '{{WRAPPER}} [class*="booked-list-view-date-"]',
                    'separator' => 'after',
                ]
            );
            
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'        => 'navigation_button_border',
                    'placeholder' => '1px',
                    'default'     => '1px',
                    'selector'    => '{{WRAPPER}} [class*="booked-list-view-date-"]',
                    'separator'   => 'before',
                ]
            );
            
            $this->add_control(
                'navigation_button_radius',
                [
                    'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} [class*="booked-list-view-date-"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'     => 'navigation_button_shadow',
                    'selector' => '{{WRAPPER}} [class*="booked-list-view-date-"]',
                ]
            );
            
            $this->add_control(
                'navigation_button_padding',
                [
                    'label'      => __( 'Padding', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} [class*="booked-list-view-date-"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator'  => 'before',
                ]
            );
            
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'      => 'navigation_button_typography',
                    //'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
                    'selector'  => '{{WRAPPER}} [class*="booked-list-view-date-"]',
                    'separator' => 'before',
                ]
            );
            
            $this->end_controls_tab();
            
            $this->start_controls_tab(
                'tab_navigation_button_hover',
                [
                    'label' => __( 'Hover', 'bdthemes-element-pack' ),
                ]
            );
            
            $this->add_control(
                'navigation_button_hover_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} [class*="booked-list-view-date-"]:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'      => 'navigation_button_hover_background',
                    'types'     => ['classic', 'gradient'],
                    'selector'  => '{{WRAPPER}} [class*="booked-list-view-date-"]:hover',
                    'separator' => 'after',
                ]
            );
            
            $this->add_control(
                'navigation_button_hover_border_color',
                [
                    'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'condition' => [
                        'navigation_button_border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} [class*="booked-list-view-date-"]:hover' => 'border-color: {{VALUE}};',
                    ],
                    'separator' => 'before',
                ]
            );
            
            $this->end_controls_tab();
            
            $this->end_controls_tabs();
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_style_appointment_button',
                [
                    'label'     => __( 'Appointment Button', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style' => 'list',
                    ],
                ]
            );
            
            $this->start_controls_tabs( 'tabs_appointment_button_style' );
            
            $this->start_controls_tab(
                'tab_appointment_button_normal',
                [
                    'label' => __( 'Normal', 'bdthemes-element-pack' ),
                ]
            );
            
            $this->add_control(
                'appointment_button_text_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .new-appt.button' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'      => 'appointment_button_background',
                    'types'     => ['classic', 'gradient'],
                    'selector'  => '{{WRAPPER}} .new-appt.button',
                    'separator' => 'after',
                ]
            );
            
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'        => 'appointment_button_border',
                    'placeholder' => '1px',
                    'default'     => '1px',
                    'selector'    => '{{WRAPPER}} .new-appt.button',
                    'separator'   => 'before',
                ]
            );
            
            $this->add_control(
                'appointment_button_radius',
                [
                    'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .new-appt.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'     => 'appointment_button_shadow',
                    'selector' => '{{WRAPPER}} .new-appt.button',
                ]
            );
            
            $this->add_control(
                'appointment_button_padding',
                [
                    'label'      => __( 'Padding', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .new-appt.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator'  => 'before',
                ]
            );
            
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'      => 'appointment_button_typography',
                    //'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
                    'selector'  => '{{WRAPPER}} .new-appt.button',
                    'separator' => 'before',
                ]
            );
            
            $this->end_controls_tab();
            
            $this->start_controls_tab(
                'tab_appointment_button_hover',
                [
                    'label' => __( 'Hover', 'bdthemes-element-pack' ),
                ]
            );
            
            $this->add_control(
                'appointment_button_hover_color',
                [
                    'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .new-appt.button:hover' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_control(
                'appointment_button_hover_background',
                [
                    'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .new-appt.button:hover' => 'background-color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_control(
                'appointment_button_hover_border_color',
                [
                    'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'condition' => [
                        'navigation_button_border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .new-appt.button:hover' => 'border-color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->end_controls_tab();
            
            $this->end_controls_tabs();
            
            $this->end_controls_section();
            
            $this->start_controls_section(
                'section_style_additional',
                [
                    'label'     => __( 'Additional', 'bdthemes-element-pack' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'calendar_style' => 'list',
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_icon_color',
                [
                    'label'     => __( 'Calendar Icon Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger' => 'color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_icon_background',
                [
                    'label'     => __( 'Calendar Icon Background', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'        => 'calendar_icon_border',
                    'placeholder' => '1px',
                    'default'     => '1px',
                    'selector'    => '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger',
                    'separator'   => 'before',
                ]
            );
            
            $this->add_control(
                'calendar_icon_radius',
                [
                    'label'      => __( 'Calendar Icon Radius', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'     => 'calendar_icon_shadow',
                    'selector' => '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger'
                ]
            );
            
            $this->add_control(
                'calendar_icon_padding',
                [
                    'label'      => __( 'Calendar Icon Padding', 'bdthemes-element-pack' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .booked-list-view a.booked_list_date_picker_trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_row_border_color',
                [
                    'label'     => __( 'Row Border Color', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot' => 'border-color: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'calendar_row_border_width',
                [
                    'label'     => __( 'Row Border Width', 'bdthemes-element-pack' ),
                    'type'      => Controls_Manager::SLIDER,
                    'range'     => [
                        'px' => [
                            'min' => 0,
                            'max' => 10,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .booked-calendar-wrap .booked-appt-list .timeslot' => 'border-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->end_controls_section();
        }
        
        private function get_shortcode() {
            $settings = $this->get_settings_for_display();
            
            $attributes = [
                'style'        => $settings['calendar_style'],
                'year'         => $settings['calendar_year'],
                'month'        => $settings['calendar_month'],
                'day'          => $settings['calendar_day'],
                'size'         => $settings['calendar_size'],
                'members-only' => ( 'yes' === $settings['calendar_members_only'] ) ? 'true' : '',
                'calendar'     => $settings['calendar_type'],
            ];
            
            $this->add_render_attribute( 'shortcode', $attributes );
            
            $shortcode = [];
            $shortcode[] = sprintf( '[booked-calendar %s]', $this->get_render_attribute_string( 'shortcode' ) );
            
            return implode( "", $shortcode );
        }
        
        public function render() {
            echo do_shortcode( $this->get_shortcode() );
        }
        
        public function render_plain_content() {
            echo $this->get_shortcode();
        }
    }
