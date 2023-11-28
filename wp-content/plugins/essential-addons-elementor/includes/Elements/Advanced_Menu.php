<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use Elementor\Plugin;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Pro\Classes\Helper;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Default;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Five;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Four;
use \Essential_Addons_Elementor\Pro\Skins\Skin_One;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Seven;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Six;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Three;
use \Essential_Addons_Elementor\Pro\Skins\Skin_Two;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

class Advanced_Menu extends Widget_Base
{

    protected $_has_template_content = false;

    public function get_name()
    {
        return 'eael-advanced-menu';
    }

    public function get_title()
    {
        return esc_html__('Advanced Menu', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-advanced-menu';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'advanced menu',
            'ea advanced menu',
            'nav menu',
            'ea nav menu',
            'navigation',
            'ea navigation',
            'navigation menu',
            'ea navigation menu',
            'header menu',
            'megamenu',
            'mega menu',
            'ea megamenu',
            'ea mega menu',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/ea-advanced-menu/';
    }

    protected function register_skins()
    {
        $this->add_skin(new Skin_Default($this));
        $this->add_skin(new Skin_One($this));
        $this->add_skin(new Skin_Two($this));
        $this->add_skin(new Skin_Three($this));
        $this->add_skin(new Skin_Four($this));
        $this->add_skin(new Skin_Five($this));
        $this->add_skin(new Skin_Six($this));
        $this->add_skin(new Skin_Seven($this));
    }

    protected function register_controls()
    {
        /**
         * Content: General
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_general',
            [
                'label' => esc_html__('General', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_advanced_menu_menu',
            [
                'label' => esc_html__('Select Menu', 'essential-addons-elementor'),
                'description' => sprintf(__('Go to the <a href="%s" target="_blank">Menu screen</a> to manage your menus.', 'essential-addons-elementor'), admin_url('nav-menus.php')),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'options' => Helper::get_menus(),

            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_advanced_menu_section_hamburger',
            [
                'label' => esc_html__('Hamburger Options', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_advanced_menu_hamburger_disable_selected_menu',
            [
                'label' => esc_html__('Disable Selected Menu', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'essential-addons-elementor' ),
                'label_off' => __( 'No', 'essential-addons-elementor' ),
                'return_value' => 'hide',
                'default' => 'no',
                'prefix_class' => 'eael_advanced_menu_hamburger_disable_selected_menu_',
            ]
        );

        $this->add_control(
            'eael_advanced_menu_hamburger_alignment',
            [
                'label' => __('Hamburger Alignment', 'essential-addons-elementor'),
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
                ],
                'default' => 'right',
                'prefix_class' => 'eael-advanced-menu-hamburger-align-',
            ]
        );

        $this->add_control(
            'eael_advanced_menu_full_width',
            [
            'label' => __( 'Full Width', 'essential-addons-elementor' ),
            'type' => Controls_Manager::SWITCHER,
            'description' => __( 'Stretch the dropdown of the menu to full width.', 'essential-addons-elementor' ),
            'label_on' => __( 'Yes', 'essential-addons-elementor' ),
            'label_off' => __( 'No', 'essential-addons-elementor' ),
            'return_value' => 'stretch',
            'default' => 'no',
            'prefix_class' => 'eael-advanced-menu--',
            ]
        );

        $this->add_control(
            'eael_advanced_menu_hamburger_icon',
            [
                'label'       => esc_html__('Icon', 'essential-addons-elementor'),
                'type'        => Controls_Manager::ICONS,
                'default'     => [
                    'value'   => 'fas fa-bars',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
			'eael_advanced_menu_heading_mobile_dropdown',
			[
				'label' => esc_html__( 'Mobile Dropdown', 'essential-addons-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $default_value = 'tablet';
		$dropdown_options = Helper::get_breakpoint_dropdown_options();
        
		$this->add_control(
			'eael_advanced_menu_dropdown',
			[
				'label' => esc_html__( 'Breakpoint', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => esc_html( $default_value ),
				'options' => $dropdown_options,
				'prefix_class' => 'eael-hamburger--',
			]
		);

        $this->end_controls_section();

        /**
         * Style: Main Menu
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_style_menu',
            [
                'label' => __('Main Menu', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->end_controls_section();

        /**
         * Style: Mobile Menu
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_style_mobile_menu',
            [
                'label' => __('Hamburger Menu', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_advanced_menu_hamburger_bg',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
		    'eael_advanced_menu_hamburger_size',
		    [
			    'label' => esc_html__( 'Icon Size', 'essential-addons-elementor' ),
			    'type' => Controls_Manager::SLIDER,
			    'range' => [
				    'px' => [
					    'max' => 30,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
				    '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle svg' => 'width: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );

	    $this->add_control(
            'eael_advanced_menu_hamburger_icon_color',
            [
                'label'     => __('Icon Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle svg' => 'fill: {{VALUE}}',
                ],

            ]
        );

        $this->add_control(
		    'eael_advanced_menu_hamburger_item_heading',
		    [
			    'label'     => __('Items', 'essential-addons-elementor'),
			    'type'      => Controls_Manager::HEADING,
			    'separator' => 'before',
		    ]
	    );

        $this->add_control(
		    'eael_advanced_menu_hamburger_menu_item_alignment',
		    [
			    'label'     => __('Alignment', 'essential-addons-elementor'),
			    'type'      => Controls_Manager::CHOOSE,
			    'options'   => [
				    'eael-hamburger-left'   => [
					    'title' => __('Left', 'essential-addons-elementor'),
					    'icon'  => 'eicon-text-align-left',
				    ],
				    'eael-hamburger-center' => [
					    'title' => __('Center', 'essential-addons-elementor'),
					    'icon'  => 'eicon-text-align-center',
				    ],
				    'eael-hamburger-right'  => [
					    'title' => __('Right', 'essential-addons-elementor'),
					    'icon'  => 'eicon-text-align-right',
				    ],
			    ],
		    ]
	    );

        // $this->add_control(
        //     'eael_advanced_menu_hamburger_icon',
        //     [
        //         'label' => __('Icon Color', 'essential-addons-elementor'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => '#ffffff',
        //         'selectors' => [
        //             '{{WRAPPER}} .eael-advanced-menu-container .eael-advanced-menu-toggle .eicon-menu-bar' => 'color: {{VALUE}}',
        //         ],

        //     ]
        // );

        $this->end_controls_section();

        /**
         * Style: Dropdown Menu
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_style_dropdown',
            [
                'label' => __('Dropdown Menu', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->end_controls_section();

        /**
         * Style: Top Level Items
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_style_top_level_item',
            [
                'label' => __('Top Level Item', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->end_controls_section();

        /**
         * Style: Main Menu (Hover)
         */
        $this->start_controls_section(
            'eael_advanced_menu_section_style_dropdown_item',
            [
                'label' => __('Dropdown Item', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->end_controls_section();
    }

}
