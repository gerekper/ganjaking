<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Pro\Classes\Helper;

if (!defined('ABSPATH')) {
    exit;
}
// If this file is called directly, abort.

/**
 * Offcanvas Content Widget
 */
class Offcanvas extends Widget_Base
{

    /**
     * Retrieve offcanvas widget name.
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'eael-offcanvas';
    }

    /**
     * Retrieve offcanvas widget title.
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Offcanvas', 'essential-addons-elementor');
    }

    /**
     * Retrieve offcanvas widget icon.
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eaicon-offcanvas';
    }

    /**
     * Retrieve the list of categories the offcanvas widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'offcanvas',
            'ea offcanvas',
            'off canvas',
            'ea off canvas',
            'mega menu',
            'sidebar menu',
            'sidebar panel',
            'navigation',
            'sidebar',
            'toggle',
            'hide content',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/essential-addons-elementor-offcanvas/';
    }

    protected function register_controls()
    {

        /*--------------------------------------*/
        ##  CONTENT TAB    ##
        /*--------------------------------------*/

        /**
         * Content Tab: Offcanvas
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_content_offcanvas',
            [
                'label' => __('Offcanvas Content', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_offcanvas_title',
            [
                'label' => __('Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'separator' => 'before',
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'sidebar' => __('Sidebar', 'essential-addons-elementor'),
                    'custom' => __('Custom Content', 'essential-addons-elementor'),
                    'section' => __('Saved Section', 'essential-addons-elementor'),
                    'widget' => __('Saved Widget', 'essential-addons-elementor'),
                    'template' => __('Saved Page Template', 'essential-addons-elementor'),
                ],
                'default' => 'custom',
            ]
        );

        $registered_sidebars = Helper::get_registered_sidebars();
        
        $this->add_control(
            'sidebar',
            [
                'label' => __('Choose Sidebar', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => array_shift($registered_sidebars),
                'options' => $registered_sidebars,
                'condition' => [
                    'content_type' => 'sidebar',
                ],
            ]
        );

        $this->add_control(
            'saved_widget',
            [
                'label' => __('Choose Widget', 'essential-addons-elementor'),
                'type'        => 'eael-select2',
                'source_name' => 'post_type',
                'source_type' => 'elementor_library',
                'condition' => [
                    'content_type' => 'widget',
                ],
            ]
        );

        $this->add_control(
            'saved_section',
            [
                'label' => __('Choose Section', 'essential-addons-elementor'),
                'type'        => 'eael-select2',
                'source_name' => 'post_type',
                'source_type' => 'elementor_library',
                'condition' => [
                    'content_type' => 'section',
                ],
            ]
        );

        $this->add_control(
            'templates',
	        [
		        'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
		        'type'        => 'eael-select2',
		        'source_name' => 'post_type',
		        'source_type' => 'elementor_library',
		        'label_block' => true,
		        'condition'   => [
			        'content_type' => 'template',
		        ],
	        ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Title', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'description',
            [
                'name' => 'description',
                'label' => __('Description', 'essential-addons-elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'custom_content',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'title' => __('Box 1', 'essential-addons-elementor'),
                        'description' => __('Text box description goes here', 'essential-addons-elementor'),
                    ],
                    [
                        'title' => __('Box 2', 'essential-addons-elementor'),
                        'description' => __('Text box description goes here', 'essential-addons-elementor'),
                    ],
                ],
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'condition' => [
                    'content_type' => 'custom',
                ],
            ]
        );

        $this->end_controls_section(); #section_content_offcanvas

        /**
         * Content Tab: Toggle Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_button_settings',
            [
                'label' => __('Toggle Button', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click Here', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'button_icon_new',
            [
                'label' => __('Button Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'button_icon',
            ]
        );

        $this->add_control(
            'button_icon_position',
            [
                'label' => __('Icon Position', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => __('Before', 'essential-addons-elementor'),
                    'after' => __('After', 'essential-addons-elementor'),
                ],
                'prefix_class' => 'eael-offcanvas-icon-',
                'condition' => [
                    'button_icon!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_icon_spacing',
            [
                'label' => __('Icon Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '5',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}}.eael-offcanvas-icon-before .eael-offcanvas-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.eael-offcanvas-icon-after .eael-offcanvas-toggle-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'button_icon!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Settings
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'direction',
            [
                'label' => __('Direction', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'toggle' => false,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'content_transition',
            [
                'label' => __('Content Transition', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'essential-addons-elementor'),
                    'reveal' => __('Reveal', 'essential-addons-elementor'),
                    'push' => __('Push', 'essential-addons-elementor'),
                    'slide-along' => __('Slide Along', 'essential-addons-elementor'),
                ],
                'frontend_available' => true,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'open_offcanvas_default',
            [
                'label' => __('Open OffCanvas by Default', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'close_button',
            [
                'label' => __('Show Close Button', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'esc_close',
            [
                'label' => __('Esc to Close', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'body_click_close',
            [
                'label' => __('Click anywhere to Close', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*    STYLE TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Offcanvas Bar
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_offcanvas_bar_style',
            [
                'label' => __('Offcanvas Bar', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'offcanvas_width',
            [
                'label' => __('Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 700,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '.eael-offcanvas-content.eael-offcanvas-content-{{ID}}' => 'width: {{SIZE}}{{UNIT}};',
                    '.eael-offcanvas-content-open.eael-offcanvas-content-left .eael-offcanvas-container-{{ID}}' => 'transform: translate3d({{SIZE}}{{UNIT}}, 0, 0);',
                    '.eael-offcanvas-content-open.eael-offcanvas-content-right .eael-offcanvas-container-{{ID}}' => 'transform: translate3d(-{{SIZE}}{{UNIT}}, 0, 0);',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'offcanvas_bar_bg',
                'label' => __('Background', 'essential-addons-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '.eael-offcanvas-content.eael-offcanvas-content-{{ID}}',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'offcanvas_bar_border',
                'label' => __('Border', 'essential-addons-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '.eael-offcanvas-content.eael-offcanvas-content-{{ID}}',
            ]
        );

        $this->add_control(
            'offcanvas_bar_border_radius',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.eael-offcanvas-content.eael-offcanvas-content-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'offcanvas_bar_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'offcanvas_bar_box_shadow',
                'selector' => '.eael-offcanvas-content.eael-offcanvas-content-{{ID}}',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Content
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_popup_content_style',
            [
                'label' => __('Content', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_responsive_control(
            'content_align',
            [
                'label' => __('Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'widget_heading',
            [
                'label' => __('Box', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'widgets_bg_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-custom-widget, .eael-offcanvas-content-{{ID}} .widget' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'widgets_border',
                'label' => __('Border', 'essential-addons-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '.eael-offcanvas-content-{{ID}} .eael-offcanvas-custom-widget, .eael-offcanvas-content-{{ID}} .widget',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'widgets_border_radius',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-custom-widget, .eael-offcanvas-content-{{ID}} .widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_responsive_control(
            'widgets_bottom_spacing',
            [
                'label' => __('Bottom Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '20',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-custom-widget, .eael-offcanvas-content-{{ID}} .widget' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_responsive_control(
            'widgets_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-custom-widget, .eael-offcanvas-content-{{ID}} .widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'text_heading',
            [
                'label' => __('Text', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label' => __('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body, .eael-offcanvas-content-{{ID}} .eael-offcanvas-body *:not(.fas):not(.eicon):not(.fab):not(.far):not(.fa)' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'selector' => '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body, .eael-offcanvas-content-{{ID}} .eael-offcanvas-body *:not(.fas):not(.eicon):not(.fab):not(.far):not(.fa)',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'links_heading',
            [
                'label' => __('Links', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->start_controls_tabs('tabs_links_style');

        $this->start_controls_tab(
            'tab_links_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'content_links_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body a' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'links_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'selector' => '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body a',
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_links_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->add_control(
            'content_links_color_hover',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-body a:hover' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'content_type' => ['sidebar', 'custom'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Offcanvas Title
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_offcanvas_title_style',
            [
                'label' => __('Offcanvas Title', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'offcanvas_title_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-title h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_offcanvas_title_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'selector' => '.eael-offcanvas-content-{{ID}} .eael-offcanvas-title h3',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Icon
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => __('Icon', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'trigger' => 'on-click',
                    'trigger_type!' => 'button',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-trigger-icon' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'trigger' => 'on-click',
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '28',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-trigger-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'trigger' => 'on-click',
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_image_width',
            [
                'label' => __('Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-trigger-image' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'trigger' => 'on-click',
                    'trigger_type' => 'image',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Toggle Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_toggle_button_style',
            [
                'label' => __('Toggle Button', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_align',
            [
                'label' => __('Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => __('Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'md',
                'options' => [
                    'xs' => __('Extra Small', 'essential-addons-elementor'),
                    'sm' => __('Small', 'essential-addons-elementor'),
                    'md' => __('Medium', 'essential-addons-elementor'),
                    'lg' => __('Large', 'essential-addons-elementor'),
                    'xl' => __('Extra Large', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_button_icon_size',
            [
                'label' => __('Icon Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '28',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap .eael-offcanvas-toggle-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap svg.eael-offcanvas-toggle-icon' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap .eael-offcanvas-toggle-icon.eael-offcanvas-toggle-svg-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_button_icon_space',
            [
                'label' => __('Icon Space', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '10',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap .eael-offcanvas-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .eael-offcanvas-toggle-wrap .eael-offcanvas-toggle-icon.eael-offcanvas-toggle-svg-icon' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'button_bg_color_normal',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_normal',
            [
                'label' => __('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-offcanvas-toggle svg.eael-offcanvas-toggle-icon' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border_normal',
                'label' => __('Border', 'essential-addons-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .eael-offcanvas-toggle',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector' => '{{WRAPPER}} .eael-offcanvas-toggle',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .eael-offcanvas-toggle',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-offcanvas-toggle:hover svg.eael-offcanvas-toggle-icon' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __('Border Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-offcanvas-toggle:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_animation',
            [
                'label' => __('Animation', 'essential-addons-elementor'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow_hover',
                'selector' => '{{WRAPPER}} .eael-offcanvas-toggle:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Close Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_close_button_style',
            [
                'label' => __('Close Button', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'close_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'close_button_icon_new',
            [
                'label' => __('Button Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'close_button_icon',
                'default' => [
                    'value' => 'fas fa-times',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'close_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'close_button_text_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-close-{{ID}}' => 'color: {{VALUE}}',
                    '.eael-offcanvas-close-{{ID}} svg' => 'fill: {{VALUE}}',
                ],
                'condition' => [
                    'close_button' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'close_button_size',
            [
                'label' => __('Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '28',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-close-{{ID}}' => 'font-size: {{SIZE}}{{UNIT}}',
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-close-{{ID}} svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-width: {{SIZE}}{{UNIT}}',
                    '.eael-offcanvas-content-{{ID}} .eael-offcanvas-close-{{ID}} .eael-offcanvas-close-svg-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'close_button' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Overlay
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_overlay_style',
            [
                'label' => __('Overlay', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_bg_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}}-open .eael-offcanvas-container:after' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => __('Opacity', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '.eael-offcanvas-content-{{ID}}-open .eael-offcanvas-container:after' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render close button for offcanvas output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render_close_button()
    {
        $settings = $this->get_settings_for_display();

        if ($settings['close_button'] != 'yes') {
            return;
        }

        $this->add_render_attribute(
            'close-button', 'class',
            [
                'eael-offcanvas-close',
                'eael-offcanvas-close-' . esc_attr($this->get_id()),
            ]
        );

        $this->add_render_attribute('close-button', 'role', 'button');
        ?>
        <div class="eael-offcanvas-header">
            <div class="eael-offcanvas-title">
                <?php if( ! empty( $settings['eael_offcanvas_title'] ) ) : ?>
                <h3><?php echo esc_html($settings['eael_offcanvas_title']); ?></h3>
                <?php endif; ?>
            </div>
            <div <?php echo $this->get_render_attribute_string('close-button'); ?>>
                <?php if (isset($settings['__fa4_migrated']['close_button_icon_new']) || empty($settings['close_button_icon'])) {?>
                    <?php if (isset($settings['close_button_icon_new']['value']['url'])): ?>
                        <img class="eael-offcanvas-close-svg-icon" src="<?php echo esc_url($settings['close_button_icon_new']['value']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['close_button_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>">
                    <?php else:
                        \Elementor\Icons_Manager::render_icon( $settings['close_button_icon_new'], [ 'aria-hidden' => 'true' ] );
                        endif;?>
                <?php } else {?>
                    <span class="<?php echo esc_attr($settings['close_button_icon']); ?>"></span>
                <?php }?>
            </div>
        </div>
        <?php
}

    /**
     * Render sidebars for output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render_sidebar()
    {
        $settings = $this->get_settings_for_display();
        $sidebar = $settings['sidebar'];

        if (empty($sidebar)) {
            return;
        }

        dynamic_sidebar($sidebar);
    }

    /**
     * Render custom template or saved template output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render_custom_content()
    {
        $settings = $this->get_settings_for_display();

        if (count($settings['custom_content'])) {
            foreach ($settings['custom_content'] as $key => $item) {
                ?>
                <div class="eael-offcanvas-custom-widget">
                    <?php if( ! empty( $item['title'] ) ) : ?>
                    <h3 class="eael-offcanvas-widget-title"><?php echo esc_html( $item['title'] ); ?></h3>
                    <?php endif; ?>

                    <div class="eael-offcanvas-widget-content">
                        <?php echo $item['description']; ?>
                    </div>
                </div>
                <?php
}
        }

    }

    /**
     * Render offcanvas content widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $setting_attr = [
            'content_id' => esc_attr($this->get_id()),
            'direction' => esc_attr($settings['direction']),
            'transition' => esc_attr($settings['content_transition']),
            'esc_close' => esc_attr($settings['esc_close']),
            'body_click_close' => esc_attr($settings['body_click_close']),
            'open_offcanvas' => esc_attr($settings['open_offcanvas_default']),
        ];

        $this->add_render_attribute(
            'content-wrap',
            [
                'class' => 'eael-offcanvas-content-wrap',
                'data-settings' => htmlspecialchars(json_encode($setting_attr)),
            ]
        );

        $this->add_render_attribute(
            'content',
            [
                'class' => [
                    'eael-offcanvas-content',
                    'eael-offcanvas-content-' . esc_attr($this->get_id()),
                    'eael-offcanvas-' . $setting_attr['transition'],
                    'elementor-element-' . $this->get_id(),
                    'eael-offcanvas-content-' . $setting_attr['direction'],
                ],
            ]
        );

        $this->add_render_attribute(
            'toggle-button',
            [
                'class' => [
                    'eael-offcanvas-toggle',
                    'eael-offcanvas-toogle-' . esc_attr($this->get_id()),
                    'elementor-button',
                    'elementor-size-' . esc_attr($settings['button_size']),
                ],
            ]
        );

        if ($settings['button_animation']) {
            $this->add_render_attribute('toggle-button', 'class', 'elementor-animation-' . esc_attr($settings['button_animation']));
        }

        ?>
        <div <?php echo $this->get_render_attribute_string('content-wrap'); ?>>

            <?php if ( !empty( $settings['button_text'] ) || !empty( $settings['button_icon_new']['value'] )): ?>
            <div class="eael-offcanvas-toggle-wrap">
                <div <?php echo $this->get_render_attribute_string('toggle-button'); ?>>
                    <?php if (isset($settings['__fa4_migrated']['button_icon_new']) || empty($settings['button_icon'])) {
                            Icons_Manager::render_icon( $settings['button_icon_new'], [ 'aria-hidden' => 'true', 'class' => 'eael-offcanvas-toggle-icon' ] );
                        }
                        else {?>
                        <span class="eael-offcanvas-toggle-icon <?php echo esc_attr($settings['button_icon']); ?>"></span>
                    <?php }?>
                    <span class="eael-toggle-text">
                        <?php echo $settings['button_text']; ?>
                    </span>
                </div>
            </div>
            <?php endif; // end of if( $settings['button_text'] != '' || $settings['button_text'] != '' ) ?>

            <div <?php echo $this->get_render_attribute_string('content'); ?>>
                <?php $this->render_close_button();?>
                <div class="eael-offcanvas-body">
	                <?php
	                if ( 'sidebar' == $settings['content_type'] ) {
		                $this->render_sidebar();
	                } else if ( 'custom' == $settings['content_type'] ) {
		                $this->render_custom_content();
	                } else if ( 'section' == $settings['content_type'] && ! empty( $settings['saved_section'] ) ) {
		                // WPML Compatibility
		                if ( ! is_array( $settings['saved_section'] ) ) {
			                $settings['saved_section'] = apply_filters( 'wpml_object_id', $settings['saved_section'], 'wp_template', true );
		                }
		                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_section'] );
	                } elseif ( 'template' == $settings['content_type'] && ! empty( $settings['templates'] ) ) {
		                // WPML Compatibility
		                if ( ! is_array( $settings['templates'] ) ) {
			                $settings['templates'] = apply_filters( 'wpml_object_id', $settings['templates'], 'wp_template', true );
		                }
		                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['templates'] );
	                } elseif ( 'widget' == $settings['content_type'] && ! empty( $settings['saved_widget'] ) ) {
		                // WPML Compatibility
		                if ( ! is_array( $settings['saved_widget'] ) ) {
			                $settings['saved_widget'] = apply_filters( 'wpml_object_id', $settings['saved_widget'], 'wp_template', true );
		                }
		                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_widget'] );
	                }
	                ?>
                </div><!-- /.eael-offcanvas-body -->
            </div>
        </div>
        <?php

    }

    protected function content_template()
    {}
}
