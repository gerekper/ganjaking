<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Elementor\Plugin;
use \Elementor\Control_Media;

use \Essential_Addons_Elementor\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Toggle Widget
 */
class Toggle extends Widget_Base {
    /**
     * Retrieve toggle widget name.
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'eael-toggle';
    }
    
    /**
     * Retrieve toggle widget title.
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Toggle', 'essential-addons-elementor' );
    }
    
    /**
     * Retrieve the list of categories the toggle widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'essential-addons-elementor' ];
    }
    
    public function get_keywords()
    {
        return [
            'toggle',
            'ea toggle',
            'ea content toggle',
            'content toggle',
            'content switcher',
            'switcher',
            'ea switcher',
            'ea',
            'essential addons'
        ];
    }
    
    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/content-toggle/';
    }
    
    /**
     * Retrieve toggle widget icon.
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eaicon-content-toggle';
    }
    
    /**
     * Register toggle widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
        
        /*-----------------------------------------------------------------------------------*/
        /*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/
        
        /**
         * Content Tab: Primary
         */
        $this->start_controls_section(
            'section_primary',
            [
                'label'                 => __( 'Primary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
            'primary_label',
            [
                'label'                 => __( 'Label', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'	            => [ 'active' => true ],
                'default'               => __( 'Annual', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );
        
        $this->add_control(
            'primary_content_type',
            [
                'label'                 => __( 'Content Type', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'image'         => __( 'Image', 'essential-addons-elementor' ),
                    'content'       => __( 'Content', 'essential-addons-elementor' ),
                    'template'      => __( 'Saved Templates', 'essential-addons-elementor' ),
                ],
                'default'               => 'content',
            ]
        );
        
        $this->add_control(
            'primary_templates',
	        [
		        'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
		        'type'        => 'eael-select2',
		        'source_name' => 'post_type',
		        'source_type' => 'elementor_library',
		        'label_block' => true,
		        'condition'   => [
			        'primary_content_type' => 'template',
		        ],
	        ]
        );
        
        $this->add_control(
            'primary_content',
            [
                'label'                 => __( 'Content', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::WYSIWYG,
                'default'               => __( 'Primary Content', 'essential-addons-elementor' ),
                'condition'             => [
                    'primary_content_type'      => 'content',
                ],
            ]
        );
        
        $this->add_control(
            'primary_image',
            [
                'label'                 => __( 'Image', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'             => [
                    'primary_content_type'      => 'image',
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );
        
        $this->end_controls_section();
        
        /**
         * Content Tab: Secondary
         */
        $this->start_controls_section(
            'section_secondary',
            [
                'label'                 => __( 'Secondary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
            'secondary_label',
            [
                'label'                 => __( 'Label', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'	            => [ 'active' => true ],
                'default'               => __( 'Lifetime', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );
        
        $this->add_control(
            'secondary_content_type',
            [
                'label'                 => __( 'Content Type', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'image'         => __( 'Image', 'essential-addons-elementor' ),
                    'content'       => __( 'Content', 'essential-addons-elementor' ),
                    'template'      => __( 'Saved Templates', 'essential-addons-elementor' ),
                ],
                'default'               => 'content',
            ]
        );
        
        $this->add_control(
            'secondary_templates',
	        [
		        'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
		        'type'        => 'eael-select2',
		        'source_name' => 'post_type',
		        'source_type' => 'elementor_library',
		        'label_block' => true,
		        'condition'   => [
			        'secondary_content_type' => 'template',
		        ],
	        ]
        );
        
        $this->add_control(
            'secondary_content',
            [
                'label'                 => __( 'Content', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::WYSIWYG,
                'default'               => __( 'Secondary Content', 'essential-addons-elementor' ),
                'condition'             => [
                    'secondary_content_type'      => 'content',
                ],
            ]
        );
        
        $this->add_control(
            'secondary_image',
            [
                'label'                 => __( 'Image', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'             => [
                    'secondary_content_type'      => 'image',
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Overlay
         */
        $this->start_controls_section(
            'section_toggle_switch_style',
            [
                'label'             => __( 'Switch', 'essential-addons-elementor' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'toggle_switch_alignment',
            [
                'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'default'               => 'center',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'center'        => [
                        'title'     => __( 'Center', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-center',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class'          => 'eael-toggle-',
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'switch_style',
            [
                'label'                 => __( 'Switch Style', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'round'         => __( 'Round', 'essential-addons-elementor' ),
                    'rectangle'     => __( 'Rectangle', 'essential-addons-elementor' ),
                ],
                'default'               => 'round',
            ]
        );
        
        $this->add_responsive_control(
            'toggle_switch_size',
            [
                'label'                 => __( 'Switch Size', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' => 26,
                    'unit' => 'px',
                ],
                'size_units'            => [ 'px' ],
                'range'                 => [
                    'px'   => [
                        'min' => 15,
                        'max' => 60,
                    ],
                ],
                'tablet_default'        => [
                    'unit' => 'px',
                ],
                'mobile_default'        => [
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-container' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'toggle_switch_spacing',
            [
                'label'                 => __( 'Headings Spacing', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'size_units'            => [ 'px', '%' ],
                'range'                 => [
                    'px'   => [
                        'max' => 80,
                    ],
                ],
                'tablet_default'        => [
                    'unit' => 'px',
                ],
                'mobile_default'        => [
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-container' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'toggle_switch_gap',
            [
                'label'                 => __( 'Margin Bottom', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'size_units'            => [ 'px', '%' ],
                'range'                 => [
                    'px'   => [
                        'max' => 80,
                    ],
                ],
                'tablet_default'        => [
                    'unit' => 'px',
                ],
                'mobile_default'        => [
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->start_controls_tabs( 'tabs_switch' );
        
        $this->start_controls_tab(
            'tab_switch_primary',
            [
                'label'             => __( 'Primary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'toggle_switch_primary_background',
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .eael-toggle-slider',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'toggle_switch_primary_border',
                'label'                 => __( 'Border', 'essential-addons-elementor' ),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .eael-toggle-switch-container',
            ]
        );
        
        $this->add_control(
            'toggle_switch_primary_border_radius',
            [
                'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'tab_switch_secondary',
            [
                'label'             => __( 'Secondary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'toggle_switch_secondary_background',
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .eael-toggle-switch-on .eael-toggle-slider',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'toggle_switch_secondary_border',
                'label'                 => __( 'Border', 'essential-addons-elementor' ),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .eael-toggle-switch-container.eael-toggle-switch-on',
            ]
        );
        
        $this->add_control(
            'toggle_switch_secondary_border_radius',
            [
                'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-container.eael-toggle-switch-on' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
            'switch_controller_heading',
            [
                'label'                 => __( 'Controller', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'toggle_controller_background',
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .eael-toggle-slider::before',
            ]
        );
        
        $this->add_control(
            'toggle_controller_border_radius',
            [
                'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-slider::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Label
         */
        $this->start_controls_section(
            'section_label_style',
            [
                'label'             => __( 'Label', 'essential-addons-elementor' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'label_horizontal_position',
            [
                'label'                 => __( 'Position', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
                'default'               => 'middle',
                'options'               => [
                    'top'          => [
                        'title'    => __( 'Top', 'essential-addons-elementor' ),
                        'icon'     => 'eicon-v-align-top',
                    ],
                    'middle'       => [
                        'title'    => __( 'Middle', 'essential-addons-elementor' ),
                        'icon'     => 'eicon-v-align-middle',
                    ],
                    'bottom'       => [
                        'title'    => __( 'Bottom', 'essential-addons-elementor' ),
                        'icon'     => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors_dictionary'  => [
                    'top'      => 'flex-start',
                    'middle'   => 'center',
                    'bottom'   => 'flex-end',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-toggle-switch-inner' => 'align-items: {{VALUE}}',
                ],
            ]
        );
        
        $this->start_controls_tabs( 'tabs_label_style' );
        
        $this->start_controls_tab(
            'tab_label_primary',
            [
                'label'             => __( 'Primary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
            'label_text_color_primary',
            [
                'label'             => __( 'Text Color', 'essential-addons-elementor' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .eael-primary-toggle-label' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'label_active_text_color_primary',
            [
                'label'             => __( 'Active Text Color', 'essential-addons-elementor' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .eael-primary-toggle-label.active' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'label_typography_primary',
                'label'             => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'          => '{{WRAPPER}} .eael-primary-toggle-label',
                'separator'         => 'before',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'tab_label_secondary',
            [
                'label'             => __( 'Secondary', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
            'label_text_color_secondary',
            [
                'label'             => __( 'Text Color', 'essential-addons-elementor' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .eael-secondary-toggle-label' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'label_active_text_color_secondary',
            [
                'label'             => __( 'Active Text Color', 'essential-addons-elementor' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .eael-secondary-toggle-label.active' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'label_typography_secondary',
                'label'             => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'          => '{{WRAPPER}} .eael-secondary-toggle-label',
                'separator'         => 'before',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Content
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'             => __( 'Content', 'essential-addons-elementor' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'content_alignment',
            [
                'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'default'               => 'center',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'center'        => [
                        'title'     => __( 'Center', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-center',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                ],
                'selectors'         => [
                    '{{WRAPPER}} .eael-toggle-content-wrap' => 'text-align: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'content_text_color',
            [
                'label'             => __( 'Text Color', 'essential-addons-elementor' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .eael-toggle-content-wrap' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'content_typography',
                'label'             => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'          => '{{WRAPPER}} .eael-toggle-content-wrap',
            ]
        );
        
        $this->end_controls_section();
        
    }
    
    /**
     * Render toggle widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'toggle-container', 'class', 'eael-toggle-container' );
        
        $this->add_render_attribute( 'toggle-container', 'id', 'eael-toggle-container-' . esc_attr( $this->get_id() ) );
        $this->add_render_attribute( 'toggle-switch-wrap', 'class', 'eael-toggle-switch-wrap' );
        
        $this->add_render_attribute( 'toggle-switch-container', 'class', 'eael-toggle-switch-container' );
        
        $this->add_render_attribute( 'toggle-switch-container', 'class', 'eael-toggle-switch-' . $settings['switch_style'] );
        
        $this->add_render_attribute( 'toggle-content-wrap', 'class', 'eael-toggle-content-wrap primary' );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'toggle-container' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'toggle-switch-wrap' ); ?>>
                <div class="eael-toggle-switch-inner">

                    <div class="eael-primary-toggle-label">
                        <?php echo esc_attr( $settings['primary_label'] ); ?>
                    </div>
                    <div <?php echo $this->get_render_attribute_string( 'toggle-switch-container' ); ?>>
                        <label class="eael-toggle-switch">
                            <input type="checkbox">
                            <span class="eael-toggle-slider"></span>
                        </label>
                    </div>

                    <div class="eael-secondary-toggle-label">
                        <?php echo esc_attr( $settings['secondary_label'] ); ?>
                    </div>

                </div>
            </div>
            <div <?php echo $this->get_render_attribute_string( 'toggle-content-wrap' ); ?>>
                <div class="eael-toggle-primary-wrap">
                    <?php
                    if ( $settings['primary_content_type'] == 'content' ) {
                        echo $this->parse_text_editor( $settings['primary_content'] );
                    } elseif ( $settings['primary_content_type'] == 'image' ) {
                        $this->add_render_attribute( 'primary-image', 'src', $settings['primary_image']['url'] );
                        $this->add_render_attribute( 'primary-image', 'alt', Control_Media::get_image_alt( $settings['primary_image'] ) );
                        $this->add_render_attribute( 'primary-image', 'title', Control_Media::get_image_title( $settings['primary_image'] ) );
                        
                        printf( '<img %s />', $this->get_render_attribute_string( 'primary-image' ) );
                    } elseif ( $settings['primary_content_type'] == 'template' ) {
	                    if ( ! empty( $settings['primary_templates'] ) ) {
		                    // WPML Compatibility
		                    if ( ! is_array( $settings['primary_templates'] ) ) {
			                    $settings['primary_templates'] = apply_filters( 'wpml_object_id', $settings['primary_templates'], 'wp_template', true );
		                    }
		                    echo Plugin::$instance->frontend->get_builder_content( $settings['primary_templates'], true );
	                    }
                    }
                    ?>
                </div>
                <div class="eael-toggle-secondary-wrap">
                    <?php
                    if ( $settings['secondary_content_type'] == 'content' ) {
                        echo $this->parse_text_editor( $settings['secondary_content'] );
                    } elseif ( $settings['secondary_content_type'] == 'image' ) {
                        $this->add_render_attribute( 'secondary-image', 'src', $settings['secondary_image']['url'] );
                        $this->add_render_attribute( 'secondary-image', 'alt', Control_Media::get_image_alt( $settings['secondary_image'] ) );
                        $this->add_render_attribute( 'secondary-image', 'title', Control_Media::get_image_title( $settings['secondary_image'] ) );
                        
                        printf( '<img %s />', $this->get_render_attribute_string( 'secondary-image' ) );
                    } elseif ( $settings['secondary_content_type'] == 'template' ) {
	                    if ( ! empty( $settings['secondary_templates'] ) ) {
		                    // WPML Compatibility
		                    if ( ! is_array( $settings['secondary_templates'] ) ) {
			                    $settings['secondary_templates'] = apply_filters( 'wpml_object_id', $settings['secondary_templates'], 'wp_template', true );
		                    }
		                    echo Plugin::$instance->frontend->get_builder_content( $settings['secondary_templates'], true );
	                    }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render toggle widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @access protected
     */
    protected function content_template() {
    }
}
