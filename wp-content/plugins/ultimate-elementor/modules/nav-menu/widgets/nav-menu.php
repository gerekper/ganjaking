<?php
/**
 * UAEL Navigation Menu.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\NavMenu\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\NavMenu\Widgets\Menu_Walker;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Nav Menu.
 */
class Nav_Menu extends Common_Widget {

	/**
	 * Menu index.
	 *
	 * @access protected
	 * @var $nav_menu_index
	 */
	protected $nav_menu_index = 1;

	/**
	 * Retrieve Nav Menu Widget name.
	 *
	 * @since 1.21.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Nav_Menu' );
	}

	/**
	 * Retrieve Nav Menu Widget title.
	 *
	 * @since 1.21.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Nav_Menu' );
	}

	/**
	 * Retrieve Nav Menu Widget icon.
	 *
	 * @since 1.21.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Nav_Menu' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.21.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Nav_Menu' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.21.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-nav-menu', 'uael-element-resize', 'uael-cookie-lib' );
	}

	/**
	 * Retrieve the menu index.
	 *
	 * Used to get index of nav menu.
	 *
	 * @since 1.21.0
	 * @access protected
	 *
	 * @return string nav index.
	 */
	protected function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	/**
	 * Retrieve the list of available menus.
	 *
	 * Used to get the list of available menus.
	 *
	 * @since 1.21.0
	 * @access private
	 *
	 * @return array get WordPress menus list.
	 */
	private function get_available_menus() {

		$menus = wp_get_nav_menus();

		$options = array();

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Register Nav Menu controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_general_content_controls();
		$this->register_style_content_controls();
		$this->register_dropdown_content_controls();
		$this->register_helpful_information();
		$this->register_cta_btn_style_controls();
	}

	/**
	 * Register Nav Menu General Controls.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function register_general_content_controls() {

		$this->start_controls_section(
			'section_menu',
			array(
				'label' => __( 'Menu', 'uael' ),
			)
		);

		$this->add_control(
			'menu_type',
			array(
				'label'   => __( 'Type', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wordpress_menu',
				'options' => array(
					'wordpress_menu' => __( 'WordPress Menu', 'uael' ),
					'custom'         => __( 'Custom', 'uael' ),
				),
			)
		);

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				array(
					'label'        => __( 'Menu', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => $menus,
					'default'      => array_keys( $menus )[0],
					'save_default' => true,
					/* translators: %s Nav menu URL */
					'description'  => sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'uael' ), admin_url( 'nav-menus.php' ) ),
					'condition'    => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);
		} else {
			$this->add_control(
				'menu',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s Nav menu URL */
					'raw'             => sprintf( __( '<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'uael' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'separator'       => 'after',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition'       => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => __( 'Item Type', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'item_menu',
				'options' => array(
					'item_menu'    => __( 'Menu', 'uael' ),
					'item_submenu' => __( 'Sub Menu', 'uael' ),
				),
			)
		);

		$repeater->add_control(
			'menu_content_type',
			array(
				'label'     => __( 'Content Type', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_content_type(),
				'default'   => 'sub_menu',
				'condition' => array(
					'item_type' => 'item_submenu',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Item', 'uael' ),
				'placeholder' => __( 'Item', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'item_menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'sub_menu',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'      => __( 'Link', 'uael' ),
				'type'       => Controls_Manager::URL,
				'default'    => array(
					'url'         => '#',
					'is_external' => '',
				),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'item_menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'sub_menu',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'content_saved_widgets',
			array(
				'label'     => __( 'Select Widget', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'widget' ),
				'default'   => '-1',
				'condition' => array(
					'menu_content_type' => 'saved_modules',
					'item_type'         => 'item_submenu',
				),
			)
		);

		$repeater->add_control(
			'content_saved_rows',
			array(
				'label'     => __( 'Select Section', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'section' ),
				'default'   => '-1',
				'condition' => array(
					'menu_content_type' => 'saved_rows',
					'item_type'         => 'item_submenu',
				),
			)
		);

		$repeater->add_control(
			'content_saved_container',
			array(
				'label'     => __( 'Select Container', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'container' ),
				'default'   => '-1',
				'condition' => array(
					'menu_content_type' => 'saved_container',
					'item_type'         => 'item_submenu',
				),
			)
		);

		$repeater->add_control(
			'dropdown_width',
			array(
				'label'     => __( 'Dropdown Width', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default'   => __( 'Default', 'uael' ),
					'custom'    => __( 'Custom', 'uael' ),
					'section'   => __( 'Equal to 	Section', 'uael' ),
					'container' => __( 'Equal to Container', 'uael' ),
					'column'    => __( 'Equal to 	Column', 'uael' ),
					'widget'    => __( 'Equal to 	Widget', 'uael' ),
				),
				'condition' => array(
					'item_type' => 'item_menu',
				),
			)
		);

		$repeater->add_control(
			'section_width',
			array(
				'label'     => __( 'Width (px)', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 1500,
					),
				),
				'default'   => array(
					'size' => '220',
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} ul.sub-menu' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'dropdown_width' => 'custom',
					'item_type'      => 'item_menu',
				),
			)
		);

		$repeater->add_control(
			'dropdown_position',
			array(
				'label'     => __( 'Dropdown Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'   => __( 'Left', 'uael' ),
					'center' => __( 'Center', 'uael' ),
					'right'  => __( 'Right', 'uael' ),
				),
				'condition' => array(
					'item_type'      => 'item_menu',
					'dropdown_width' => array( 'custom', 'default' ),
				),
			)
		);

		$this->add_control(
			'menu_items',
			array(
				'label'       => __( 'Menu Items', 'uael' ),
				'type'        => Controls_Manager::REPEATER,
				'show_label'  => true,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_type' => 'item_menu',
						'text'      => __( 'Menu Item 1', 'uael' ),
					),
					array(
						'item_type' => 'item_submenu',
						'text'      => __( 'Sub Menu', 'uael' ),
					),
					array(
						'item_type' => 'item_menu',
						'text'      => __( 'Menu Item 2', 'uael' ),
					),
					array(
						'item_type' => 'item_submenu',
						'text'      => __( 'Sub Menu', 'uael' ),
					),
				),
				'title_field' => '{{ text }}',
				'separator'   => 'before',
				'condition'   => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'menu_last_item',
			array(
				'label'     => __( 'Last Menu Item', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => array(
					'none' => __( 'Default', 'uael' ),
					'cta'  => __( 'Button', 'uael' ),
				),
				'default'   => 'none',
				'condition' => array(
					'layout!' => 'expandible',
				),
			)
		);

		$current_theme = wp_get_theme();

		if ( 'Twenty Twenty-One' === $current_theme->get( 'Name' ) ) {
			$this->add_control(
				'hide_twenty_twenty_one_theme_icons',
				array(
					'label'        => __( 'Hide + & - Sign', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'separator'    => 'before',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'prefix_class' => 'uael-nav-menu__theme-icon-',
					'condition'    => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);
		}

		$this->add_control(
			'schema_support',
			array(
				'label'        => __( 'Enable Schema Support', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'render_type'  => 'template',
			)
		);

		$this->end_controls_section();

			$this->start_controls_section(
				'section_layout',
				array(
					'label' => __( 'Layout', 'uael' ),
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'Layout', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'horizontal',
					'options' => array(
						'horizontal' => __( 'Horizontal', 'uael' ),
						'vertical'   => __( 'Vertical', 'uael' ),
						'expandible' => __( 'Expanded', 'uael' ),
						'flyout'     => __( 'Flyout', 'uael' ),
					),
				)
			);

			$this->add_control(
				'navmenu_align',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-h-align-right',
						),
						'justify' => array(
							'title' => __( 'Justify', 'uael' ),
							'icon'  => 'eicon-h-align-stretch',
						),
					),
					'default'      => 'left',
					'condition'    => array(
						'layout' => array( 'horizontal', 'vertical' ),
					),
					'prefix_class' => 'uael-nav-menu__align-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'flyout_layout',
				array(
					'label'     => __( 'Flyout Orientation', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'left',
					'options'   => array(
						'left'  => __( 'Left', 'uael' ),
						'right' => __( 'Right', 'uael' ),
					),
					'condition' => array(
						'layout' => 'flyout',
					),
				)
			);

			$this->add_control(
				'flyout_type',
				array(
					'label'       => __( 'Appear Effect', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'normal',
					'label_block' => false,
					'options'     => array(
						'normal' => __( 'Slide', 'uael' ),
						'push'   => __( 'Push', 'uael' ),
					),
					'render_type' => 'template',
					'condition'   => array(
						'layout' => 'flyout',
					),
				)
			);

			$this->add_responsive_control(
				'hamburger_align',
				array(
					'label'                => __( 'Hamburger Align', 'uael' ),
					'type'                 => Controls_Manager::CHOOSE,
					'default'              => 'center',
					'options'              => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'selectors_dictionary' => array(
						'left'   => 'margin-right: auto',
						'center' => 'margin: 0 auto',
						'right'  => 'margin-left: auto',
					),
					'selectors'            => array(
						'{{WRAPPER}} .uael-nav-menu__toggle' => '{{VALUE}}',
					),
					'default'              => 'center',
					'condition'            => array(
						'layout' => array( 'expandible', 'flyout' ),
					),
					'label_block'          => false,
				)
			);

			$this->add_responsive_control(
				'hamburger_menu_align',
				array(
					'label'        => __( 'Menu Items Align', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'flex-start'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center'        => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'      => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-h-align-right',
						),
						'space-between' => array(
							'title' => __( 'Justify', 'uael' ),
							'icon'  => 'eicon-h-align-stretch',
						),
					),
					'default'      => 'space-between',
					'condition'    => array(
						'layout' => array( 'expandible', 'flyout' ),
					),
					'selectors'    => array(
						'{{WRAPPER}} li.menu-item a' => 'justify-content: {{VALUE}};',
					),
					'prefix_class' => 'uael-menu-item-',
				)
			);

			$this->add_control(
				'show_submenu_on',
				array(
					'label'        => __( 'Show Submenu On', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'hover',
					'options'      => array(
						'hover' => __( 'Hover', 'uael' ),
						'click' => __( 'Click', 'uael' ),
					),
					'condition'    => array(
						'layout' => 'horizontal',
					),
					'prefix_class' => 'uael-submenu-open-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'submenu_icon',
				array(
					'label'        => __( 'Submenu Icon', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'arrow',
					'options'      => array(
						'arrow'   => __( 'Arrows', 'uael' ),
						'plus'    => __( 'Plus Sign', 'uael' ),
						'classic' => __( 'Classic', 'uael' ),
					),
					'condition'    => array(
						'menu_type' => array( 'custom', 'wordpress_menu' ),
					),
					'prefix_class' => 'uael-submenu-icon-',
				)
			);

			$this->add_control(
				'submenu_animation',
				array(
					'label'        => __( 'Submenu Animation', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'none',
					'options'      => array(
						'none'     => __( 'Default', 'uael' ),
						'slide_up' => __( 'Slide Up', 'uael' ),
					),
					'condition'    => array(
						'menu_type' => array( 'custom', 'wordpress_menu' ),
					),
					'prefix_class' => 'uael-submenu-animation-',
					'condition'    => array(
						'layout'          => 'horizontal',
						'show_submenu_on' => 'hover',
					),
				)
			);

			$this->add_control(
				'link_redirect',
				array(
					'label'        => __( 'Action On Menu Click', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'child',
					'description'  => __( 'For Horizontal layout, this will affect on the selected breakpoint.', 'uael' ),
					'options'      => array(
						'child'     => __( 'Open Submenu', 'uael' ),
						'self_link' => __( 'Redirect To Self Link', 'uael' ),
					),
					'prefix_class' => 'uael-link-redirect-',
				)
			);

			$this->add_control(
				'heading_responsive',
				array(
					'type'      => Controls_Manager::HEADING,
					'label'     => __( 'Responsive', 'uael' ),
					'separator' => 'before',
					'condition' => array(
						'layout' => array( 'horizontal', 'vertical' ),
					),
				)
			);

		$this->add_control(
			'dropdown',
			array(
				'label'        => __( 'Breakpoint', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'tablet',
				'options'      => array(
					'mobile' => __( 'Mobile (768px >)', 'uael' ),
					'tablet' => __( 'Tablet (1025px >)', 'uael' ),
					'none'   => __( 'None', 'uael' ),
				),
				'prefix_class' => 'uael-nav-menu__breakpoint-',
				'condition'    => array(
					'layout' => array( 'horizontal', 'vertical' ),
				),
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'resp_align',
			array(
				'label'       => __( 'Alignment', 'uael' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'     => 'center',
				'description' => __( 'This is the alignement of menu icon on selected responsive breakpoints.', 'uael' ),
				'condition'   => array(
					'layout'    => array( 'horizontal', 'vertical' ),
					'dropdown!' => 'none',
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-nav-menu__toggle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'full_width_dropdown',
			array(
				'label'        => __( 'Full Width', 'uael' ),
				'description'  => __( 'Enable this option to stretch the Sub Menu to Full Width.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'layout!'   => 'flyout',
					'dropdown!' => 'none',
				),
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'toggle_layout_heading',
			array(
				'label'     => __( 'Toggle Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'dropdown!' => 'none',
				),
			)
		);

		$this->add_control(
			'toggle_label_show',
			array(
				'label'        => __( 'Show Label', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'render_type'  => 'template',
				'prefix_class' => 'uael-nav-menu-toggle-label-',
				'condition'    => array(
					'dropdown!' => 'none',
				),
			)
		);

		$this->add_control(
			'toggle_label_text',
			array(
				'label'       => __( 'Label Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Menu', 'uael' ),
				'placeholder' => __( 'Type your label text', 'uael' ),
				'condition'   => array(
					'toggle_label_show' => 'yes',
					'dropdown!'         => 'none',
				),
			)
		);

		$this->add_control(
			'toggle_label_align',
			array(
				'label'                => __( 'Label Position', 'uael' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => array(
					'left'  => __( 'Before Icon', 'uael' ),
					'right' => __( 'After Icon', 'uael' ),
				),
				'default'              => 'right',
				'prefix_class'         => 'uael-nav-menu-label-align-',
				'selectors_dictionary' => array(
					'left'  => 'flex-direction: row-reverse',
					'right' => 'flex-direction: row',
				),
				'selectors'            => array(
					'{{WRAPPER}}.uael-nav-menu-toggle-label-yes .uael-nav-menu__toggle' => '{{VALUE}}',
				),
				'condition'            => array(
					'toggle_label_show' => 'yes',
					'dropdown!'         => 'none',
				),
			)
		);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$this->add_control(
				'dropdown_icon',
				array(
					'label'       => __( 'Menu Icon', 'uael' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => 'true',
					'default'     => array(
						'value'   => 'fas fa-align-justify',
						'library' => 'fa-solid',
					),
					'condition'   => array(
						'dropdown!' => 'none',
					),
				)
			);
		} else {
			$this->add_control(
				'dropdown_icon',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => 'true',
					'default'     => 'fa fa-align-justify',
					'condition'   => array(
						'dropdown!' => 'none',
					),
				)
			);
		}

		if ( UAEL_Helper::is_elementor_updated() ) {
			$this->add_control(
				'dropdown_close_icon',
				array(
					'label'       => __( 'Close Icon', 'uael' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => 'true',
					'default'     => array(
						'value'   => 'far fa-window-close',
						'library' => 'fa-regular',
					),
					'condition'   => array(
						'dropdown!' => 'none',
					),
				)
			);
		} else {
			$this->add_control(
				'dropdown_close_icon',
				array(
					'label'       => __( 'Close Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => 'true',
					'default'     => 'fa fa-close',
					'condition'   => array(
						'dropdown!' => 'none',
					),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Register Nav Menu General Controls.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function register_style_content_controls() {

		$this->start_controls_section(
			'section_style_main-menu',
			array(
				'label'     => __( 'Main Menu', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout!' => 'expandible',
				),
			)
		);

			$this->add_responsive_control(
				'width_flyout_menu_item',
				array(
					'label'       => __( 'Flyout Box Width', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'max' => 500,
							'min' => 100,
						),
					),
					'default'     => array(
						'size' => '300',
						'unit' => 'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} .uael-flyout-wrapper .uael-side' => 'width: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}} .uael-flyout-open.left'     => 'left: -{{SIZE}}{{UNIT}}',
						'{{WRAPPER}} .uael-flyout-open.right'    => 'right: -{{SIZE}}{{UNIT}}',
					),
					'condition'   => array(
						'layout' => 'flyout',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'padding_flyout_menu_item',
				array(
					'label'     => __( 'Flyout Box Padding', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 50,
						),
					),
					'default'   => array(
						'size' => 30,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-flyout-content' => 'padding: {{SIZE}}{{UNIT}}',
					),
					'condition' => array(
						'layout' => 'flyout',
					),
				)
			);

			$this->add_responsive_control(
				'padding_horizontal_menu_item',
				array(
					'label'       => __( 'Horizontal Padding', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'max' => 50,
						),
					),
					'default'     => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} .menu-item a.uael-menu-item,{{WRAPPER}} .menu-item a.uael-sub-menu-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'padding_vertical_menu_item',
				array(
					'label'       => __( 'Vertical Padding', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'max' => 50,
						),
					),
					'default'     => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} .menu-item a.uael-menu-item, {{WRAPPER}} .menu-item a.uael-sub-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'menu_space_between',
				array(
					'label'       => __( 'Space Between', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'max' => 100,
						),
					),
					'selectors'   => array(
						'body:not(.rtl) {{WRAPPER}} .uael-nav-menu__layout-horizontal .uael-nav-menu > li.menu-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
						'body.rtl {{WRAPPER}} .uael-nav-menu__layout-horizontal .uael-nav-menu > li.menu-item:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}} nav:not(.uael-nav-menu__layout-horizontal) .uael-nav-menu > li.menu-item:not(:last-child)' => 'margin-bottom: 0',
						'(tablet)body:not(.rtl) {{WRAPPER}}.uael-nav-menu__breakpoint-tablet .uael-nav-menu__layout-horizontal .uael-nav-menu > li.menu-item:not(:last-child)' => 'margin-right: 0px',
						'(mobile)body:not(.rtl) {{WRAPPER}}.uael-nav-menu__breakpoint-mobile .uael-nav-menu__layout-horizontal .uael-nav-menu > li.menu-item:not(:last-child)' => 'margin-right: 0px',
					),
					'render_type' => 'template',
					'condition'   => array(
						'layout' => 'horizontal',
					),
				)
			);

			$this->add_responsive_control(
				'menu_row_space',
				array(
					'label'       => __( 'Row Spacing', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'max' => 100,
						),
					),
					'selectors'   => array(
						'body:not(.rtl) {{WRAPPER}} .uael-nav-menu__layout-horizontal .uael-nav-menu > li.menu-item' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					),
					'condition'   => array(
						'layout' => 'horizontal',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'menu_top_space',
				array(
					'label'       => __( 'Menu Item Top Spacing', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%' ),
					'range'       => array(
						'px' => array(
							'max' => 100,
						),
					),
					'selectors'   => array(
						'{{WRAPPER}} .uael-flyout-wrapper .uael-nav-menu > li.menu-item:first-child' => 'margin-top: {{SIZE}}{{UNIT}}',
					),
					'condition'   => array(
						'layout' => 'flyout',
					),
					'render_type' => 'template',
				)
			);

			$this->add_control(
				'bg_color_flyout',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFFFFF',
					'selectors' => array(
						'{{WRAPPER}} .uael-flyout-content' => 'background-color: {{VALUE}}',
					),
					'condition' => array(
						'layout' => 'flyout',
					),
				)
			);

			$this->add_control(
				'pointer',
				array(
					'label'     => __( 'Link Hover Effect', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'none',
					'options'   => array(
						'none'        => __( 'None', 'uael' ),
						'underline'   => __( 'Underline', 'uael' ),
						'overline'    => __( 'Overline', 'uael' ),
						'double-line' => __( 'Double Line', 'uael' ),
						'framed'      => __( 'Framed', 'uael' ),
						'text'        => __( 'Text', 'uael' ),
					),
					'condition' => array(
						'layout' => array( 'horizontal' ),
					),
				)
			);

		$this->add_control(
			'animation_line',
			array(
				'label'     => __( 'Animation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				),
				'condition' => array(
					'layout'  => array( 'horizontal' ),
					'pointer' => array( 'underline', 'overline', 'double-line' ),
				),
			)
		);

		$this->add_control(
			'animation_framed',
			array(
				'label'     => __( 'Frame Animation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				),
				'condition' => array(
					'layout'  => array( 'horizontal' ),
					'pointer' => 'framed',
				),
			)
		);

		$this->add_control(
			'animation_text',
			array(
				'label'     => __( 'Animation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => array(
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				),
				'condition' => array(
					'layout'  => array( 'horizontal' ),
					'pointer' => 'text',
				),
			)
		);

		$this->add_control(
			'style_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'menu_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .menu-item a.uael-menu-item',
			)
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

				$this->start_controls_tab(
					'tab_menu_item_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'color_menu_item',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_TEXT,
							),
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .menu-item a.uael-menu-item:not(.elementor-button), {{WRAPPER}} .sub-menu a.uael-sub-menu-item' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'bg_color_menu_item',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .menu-item a.uael-menu-item, {{WRAPPER}} .sub-menu, {{WRAPPER}} nav.uael-dropdown, {{WRAPPER}} .uael-dropdown-expandible' => 'background-color: {{VALUE}}',
							),
							'condition' => array(
								'layout!' => 'flyout',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_menu_item_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'color_menu_item_hover',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} .menu-item a.uael-menu-item:not(.elementor-button):hover,
								{{WRAPPER}} .sub-menu a.uael-sub-menu-item:hover,
								{{WRAPPER}} .menu-item.current-menu-item a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} .menu-item a.uael-menu-item.highlighted:not(.elementor-button),
								{{WRAPPER}} .menu-item a.uael-menu-item:not(.elementor-button):focus' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'bg_color_menu_item_hover',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .menu-item a.uael-menu-item:hover,
								{{WRAPPER}} .sub-menu a.uael-sub-menu-item:hover,
								{{WRAPPER}} .menu-item.current-menu-item a.uael-menu-item,
								{{WRAPPER}} .menu-item a.uael-menu-item.highlighted,
								{{WRAPPER}} .menu-item a.uael-menu-item:focus' => 'background-color: {{VALUE}}',
							),
							'condition' => array(
								'layout!' => 'flyout',
							),
						)
					);

					$this->add_control(
						'pointer_color_menu_item_hover',
						array(
							'label'     => __( 'Link Hover Effect Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent a.uael-menu-item:before,
								{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent a.uael-menu-item:after' => 'background-color: {{VALUE}}',
								'{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent .sub-menu .uael-has-submenu-container a:after' => 'background-color: unset',
								'{{WRAPPER}} .uael-pointer__framed .menu-item.parent a.uael-menu-item:before,
								{{WRAPPER}} .uael-pointer__framed .menu-item.parent a.uael-menu-item:after' => 'border-color: {{VALUE}}',
							),
							'condition' => array(
								'pointer!' => array( 'none', 'text' ),
								'layout!'  => 'flyout',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_menu_item_active',
					array(
						'label' => __( 'Active', 'uael' ),
					)
				);

					$this->add_control(
						'color_menu_item_active',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .menu-item.current-menu-item a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} .menu-item.current-menu-ancestor a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} .menu-item.custom-menu-active a.uael-menu-item:not(.elementor-button)' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'bg_color_menu_item_active',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .menu-item.current-menu-item a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} .menu-item.current-menu-ancestor a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} .menu-item.custom-menu-active a.uael-menu-item:not(.elementor-button)' => 'background-color: {{VALUE}}',
							),
							'condition' => array(
								'layout!' => 'flyout',
							),
						)
					);

					$this->add_control(
						'pointer_color_menu_item_active',
						array(
							'label'     => __( 'Link Hover Effect Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent.current-menu-item a.uael-menu-item:before,
								{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent.current-menu-item a.uael-menu-item:after,
								{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent.custom-menu-active a.uael-menu-item:before,
								{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent.custom-menu-active a.uael-menu-item:after' => 'background-color: {{VALUE}}',
								'{{WRAPPER}} .uael-nav-menu-layout:not(.uael-pointer__framed) .menu-item.parent .sub-menu .uael-has-submenu-container a.current-menu-item:after' => 'background-color: unset',
								'{{WRAPPER}} .uael-pointer__framed .menu-item.parent.current-menu-item a.uael-menu-item:before,
								{{WRAPPER}} .uael-pointer__framed .menu-item.parent.current-menu-item a.uael-menu-item:after, {{WRAPPER}} .uael-pointer__framed .menu-item.parent.custom-menu-active a.uael-menu-item:before,
								{{WRAPPER}} .uael-pointer__framed .menu-item.parent.custom-menu-active a.uael-menu-item:after' => 'border-color: {{VALUE}}',
							),
							'condition' => array(
								'pointer!' => array( 'none', 'text' ),
								'layout!'  => 'flyout',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Nav Menu General Controls.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function register_dropdown_content_controls() {

		$this->start_controls_section(
			'section_style_dropdown',
			array(
				'label' => __( 'Dropdown', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'dropdown_description',
				array(
					'raw'             => __( '<b>Note:</b> On desktop, below style options will apply to the submenu. On mobile, this will apply to the entire menu.', 'uael' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'condition'       => array(
						'layout!' => array(
							'expandible',
							'flyout',
						),
					),
				)
			);

			$this->start_controls_tabs( 'tabs_dropdown_item_style' );

				$this->start_controls_tab(
					'tab_dropdown_item_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'color_dropdown_item',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .sub-menu a.uael-sub-menu-item,
								{{WRAPPER}} .elementor-menu-toggle,
								{{WRAPPER}} nav.uael-dropdown li a.uael-menu-item:not(.elementor-button),
								{{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item:not(.elementor-button),
								{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-menu-item,
								{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-sub-menu-item' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'background_color_dropdown_item',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#fff',
							'selectors' => array(
								'{{WRAPPER}} .sub-menu,
								{{WRAPPER}} nav.uael-dropdown,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item a.uael-menu-item,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item .sub-menu,
								{{WRAPPER}} nav.uael-dropdown .menu-item a.uael-menu-item,
								{{WRAPPER}} nav.uael-dropdown .menu-item a.uael-sub-menu-item' => 'background-color: {{VALUE}}',
							),
							'separator' => 'none',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_dropdown_item_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'color_dropdown_item_hover',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .sub-menu a.uael-sub-menu-item:hover,
								{{WRAPPER}} .elementor-menu-toggle:hover,
								{{WRAPPER}} nav.uael-dropdown li a.uael-menu-item:not(.elementor-button):hover,
								{{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item:not(.elementor-button):hover,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible li a.uael-menu-item:hover,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible li a.uael-sub-menu-item:hover' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'background_color_dropdown_item_hover',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .sub-menu a.uael-sub-menu-item:hover,
								{{WRAPPER}} nav.uael-dropdown li a.uael-menu-item:not(.elementor-button):hover,
								{{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item:not(.elementor-button):hover,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible li a.uael-menu-item:hover,
								{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible li a.uael-sub-menu-item:hover' => 'background-color: {{VALUE}}',
							),
							'separator' => 'none',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_dropdown_item_active',
					array(
						'label' => __( 'Active', 'uael' ),
					)
				);

				$this->add_control(
					'color_dropdown_item_active',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .sub-menu .menu-item.current-menu-item a.uael-sub-menu-item.uael-sub-menu-item-active,
						{{WRAPPER}} nav.uael-dropdown .menu-item.current-menu-item a.uael-menu-item,
						{{WRAPPER}} nav.uael-dropdown .menu-item.current-menu-ancestor a.uael-menu-item,
						{{WRAPPER}} nav.uael-dropdown .sub-menu .menu-item.current-menu-item a.uael-sub-menu-item.uael-sub-menu-item-active,
						{{WRAPPER}} .sub-menu .menu-item.custom-submenu-active a.uael-sub-menu-item,
						{{WRAPPER}} nav.uael-dropdown .menu-item.custom-menu-active a.uael-menu-item,
						{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item.current-menu-item a.uael-menu-item,
						{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item.current-menu-item a.uael-sub-menu-item' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'background_color_dropdown_item_active',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .sub-menu .menu-item.current-menu-item a.uael-sub-menu-item.uael-sub-menu-item-active,
							{{WRAPPER}} nav.uael-dropdown .menu-item.current-menu-item a.uael-menu-item,
							{{WRAPPER}} nav.uael-dropdown .menu-item.current-menu-ancestor a.uael-menu-item,
							{{WRAPPER}} nav.uael-dropdown .sub-menu .menu-item.current-menu-item a.uael-sub-menu-item.uael-sub-menu-item-active,
							{{WRAPPER}} .sub-menu .menu-item.custom-submenu-active a.uael-sub-menu-item,
							{{WRAPPER}} nav.uael-dropdown .menu-item.custom-menu-active a.uael-menu-item,
							{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item.current-menu-item a.uael-menu-item,
							{{WRAPPER}} .uael-nav-menu nav.uael-dropdown-expandible .menu-item.current-menu-item a.uael-sub-menu-item' => 'background-color: {{VALUE}}',
						),
						'separator' => 'after',
					)
				);

				$this->end_controls_tabs();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'dropdown_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'separator' => 'before',
					'exclude'   => array( 'line_height' ),
					'selector'  => '{{WRAPPER}} .sub-menu li a.uael-sub-menu-item,
							{{WRAPPER}} nav.uael-dropdown li a.uael-menu-item,
							{{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item,
							{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-menu-item',
					'{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-sub-menu-item',

				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'dropdown_border',
					'selector' => '{{WRAPPER}} nav.uael-nav-menu__layout-horizontal .sub-menu,
							{{WRAPPER}} nav:not(.uael-nav-menu__layout-horizontal) .sub-menu.sub-menu-open,
							{{WRAPPER}} nav.uael-dropdown,
						 	{{WRAPPER}} nav.uael-dropdown-expandible',
				)
			);

			$this->add_responsive_control(
				'dropdown_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .sub-menu'         => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .sub-menu li.menu-item:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};overflow:hidden;',
						'{{WRAPPER}} .sub-menu li.menu-item:last-child' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};overflow:hidden',
						'{{WRAPPER}} nav.uael-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} nav.uael-dropdown li.menu-item:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};overflow:hidden',
						'{{WRAPPER}} nav.uael-dropdown li.menu-item:last-child' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};overflow:hidden',
						'{{WRAPPER}} nav.uael-dropdown-expandible' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} nav.uael-dropdown-expandible li.menu-item:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};overflow:hidden',
						'{{WRAPPER}} nav.uael-dropdown-expandible li.menu-item:last-child' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};overflow:hidden',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'dropdown_box_shadow',
					'exclude'   => array(
						'box_shadow_position',
					),
					'selector'  => '{{WRAPPER}} .uael-nav-menu .sub-menu,
								{{WRAPPER}} nav.uael-dropdown,
						 		{{WRAPPER}} nav.uael-dropdown-expandible',
					'separator' => 'after',
				)
			);

			$this->add_responsive_control(
				'width_dropdown_item',
				array(
					'label'       => __( 'Dropdown Width (px)', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 0,
							'max' => 500,
						),
					),
					'default'     => array(
						'size' => '220',
						'unit' => 'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} ul.sub-menu' => 'width: {{SIZE}}{{UNIT}}',
					),
					'condition'   => array(
						'layout' => 'horizontal',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'padding_horizontal_dropdown_item',
				array(
					'label'       => __( 'Horizontal Padding', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} .sub-menu li a.uael-sub-menu-item,
						{{WRAPPER}} nav.uael-dropdown li a.uael-menu-item,
						{{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item,
						{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-menu-item,
						{{WRAPPER}} nav.uael-dropdown-expandible li a.uael-sub-menu-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'padding_vertical_dropdown_item',
				array(
					'label'       => __( 'Vertical Padding', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'size' => 15,
						'unit' => 'px',
					),
					'range'       => array(
						'px' => array(
							'max' => 50,
						),
					),
					'selectors'   => array(
						'{{WRAPPER}} .sub-menu a.uael-sub-menu-item,
						 {{WRAPPER}} nav.uael-dropdown li a.uael-menu-item,
						 {{WRAPPER}} nav.uael-dropdown li a.uael-sub-menu-item,
						 {{WRAPPER}} nav.uael-dropdown-expandible li a.uael-menu-item,
						 {{WRAPPER}} nav.uael-dropdown-expandible li a.uael-sub-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'distance_from_menu',
				array(
					'label'              => __( 'Top Distance', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} nav.uael-nav-menu__layout-horizontal ul.sub-menu, {{WRAPPER}} nav.uael-nav-menu__layout-expandible.menu-is-active,
						{{WRAPPER}} .uael-dropdown.menu-is-active' => 'margin-top: {{SIZE}}px;',
						'(tablet){{WRAPPER}}.uael-nav-menu__breakpoint-tablet nav.uael-nav-menu__layout-horizontal ul.sub-menu' => 'margin-top: 0px',
						'(mobile){{WRAPPER}}.uael-nav-menu__breakpoint-mobile nav.uael-nav-menu__layout-horizontal ul.sub-menu' => 'margin-top: 0px',
					),
					'condition'          => array(
						'layout' => array( 'horizontal', 'expandible' ),
					),
					'render_type'        => 'template',
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'heading_dropdown_divider',
				array(
					'label'     => __( 'Divider', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'dropdown_divider_border',
				array(
					'label'       => __( 'Border Style', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'solid',
					'label_block' => false,
					'options'     => array(
						'none'   => __( 'None', 'uael' ),
						'solid'  => __( 'Solid', 'uael' ),
						'double' => __( 'Double', 'uael' ),
						'dotted' => __( 'Dotted', 'uael' ),
						'dashed' => __( 'Dashed', 'uael' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .sub-menu li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown-expandible li.menu-item:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'divider_border_color',
				array(
					'label'     => __( 'Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#c4c4c4',
					'selectors' => array(
						'{{WRAPPER}} .sub-menu li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown-expandible li.menu-item:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
					),
					'condition' => array(
						'dropdown_divider_border!' => 'none',
					),
				)
			);

			$this->add_control(
				'dropdown_divider_width',
				array(
					'label'     => __( 'Border Width', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 50,
						),
					),
					'default'   => array(
						'size' => '1',
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .sub-menu li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown li.menu-item:not(:last-child),
						{{WRAPPER}} nav.uael-dropdown-expandible li.menu-item:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
					),
					'condition' => array(
						'dropdown_divider_border!' => 'none',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_toggle',
			array(
				'label' => __( 'Menu Trigger & Close Icon', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_toggle_style' );

		$this->start_controls_tab(
			'toggle_style_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'toggle_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} div.uael-nav-menu-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} div.uael-nav-menu-icon svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'toggle_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-nav-menu-icon' => 'background-color: {{VALUE}}; padding: 0.35em;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'toggle_hover_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} div.uael-nav-menu-icon:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} div.uael-nav-menu-icon:hover svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'toggle_hover_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-nav-menu-icon:hover' => 'background-color: {{VALUE}}; padding: 0.35em;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_size',
			array(
				'label'     => __( 'Icon Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-nav-menu-icon'     => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .uael-nav-menu-icon svg' => 'font-size: {{SIZE}}px;line-height: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'toggle_border_width',
			array(
				'label'     => __( 'Border Width', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-nav-menu-icon' => 'border-width: {{SIZE}}{{UNIT}}; padding: 0.35em;',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-nav-menu-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'close_color_flyout',
			array(
				'label'     => __( 'Close Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7A7A7A',
				'selectors' => array(
					'{{WRAPPER}} .uael-flyout-close'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .uael-flyout-close svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'layout' => 'flyout',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'close_flyout_size',
			array(
				'label'     => __( 'Close Icon Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-flyout-close svg, {{WRAPPER}} .uael-flyout-close' => 'height: {{SIZE}}px; width: {{SIZE}}px; font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
				),
				'condition' => array(
					'layout' => 'flyout',
				),
			)
		);

		$this->add_control(
			'toggle_styles_heading',
			array(
				'label'     => __( 'Label', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'toggle_label_show' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'toggle_label_typography',
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-nav-menu__toggle .uael-nav-menu-label',
				'condition' => array(
					'toggle_label_show' => 'yes',
				),
			)
		);

		$this->add_control(
			'toggle_label_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-nav-menu__toggle .uael-nav-menu-label' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'toggle_label_show' => 'yes',
				),
			)
		);

		$this->add_control(
			'toggle_label_spacing',
			array(
				'label'      => __( 'Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-nav-menu-label-align-left .uael-nav-menu__toggle .uael-nav-menu-label' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-nav-menu-label-align-right .uael-nav-menu__toggle .uael-nav-menu-label' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'toggle_label_show' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Nav Menu CTA button style Controls.
	 *
	 * @since 1.34.0
	 * @access protected
	 */
	protected function register_cta_btn_style_controls() {
		$this->start_controls_section(
			'cta_button_styles',
			array(
				'label'     => __( 'Button', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'menu_last_item' => 'cta',
					'layout!'        => 'expandible',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_button_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button',
			)
		);

		$this->add_responsive_control(
			'cta_button_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cta_style' );

		$this->start_controls_tab(
			'cta_style_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'cta_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'cta_background_normal',
				'label'          => __( 'Background Color', 'uael' ),
				'exclude'        => array( 'image' ),
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} ul.uael-nav-menu .menu-item a.uael-menu-item.elementor-button',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_normal',
				'label'    => __( 'Border', 'uael' ),
				'selector' => '{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button',
			)
		);

		$this->add_responsive_control(
			'cta_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_box_shadow_normal',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cta_style_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'cta_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'cta_background_hover',
				'label'          => __( 'Background Color', 'uael' ),
				'types'          => array( 'classic', 'gradient' ),
				'exclude'        => array( 'image' ),
				'selector'       => '{{WRAPPER}} ul.uael-nav-menu .menu-item a.uael-menu-item.elementor-button:hover',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
					),
				),
			)
		);

		$this->add_control(
			'cta_border_color_hover',
			array(
				'label'     => __( 'Border Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_box_shadow_hover',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} .menu-item a.uael-menu-item.elementor-button:hover',
			)
		);

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Nav Menu docs link.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

				$this->add_control(
					'help_doc_1',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/navigation-menu/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_2',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s How to design a custom menu? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/design-custom-menu-using-navigation-menu/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_3',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s Types of dropdown width and position options » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/dropdown-width-and-position/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

			$this->end_controls_section();
		}
	}

	/**
	 * Render content type list.
	 *
	 * @since 1.21.0
	 * @return array Array of content type
	 * @access public
	 */
	public function get_content_type() {

		$content_type = array(
			'sub_menu'        => __( 'Text', 'uael' ),
			'saved_rows'      => __( 'Saved Section', 'uael' ),
			'saved_container' => __( 'Saved Container', 'uael' ),
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$content_type['saved_modules'] = __( 'Saved Widget', 'uael' );
		}

		return $content_type;
	}

	/**
	 * Render custom style.
	 *
	 * @since 1.21.0
	 * @access public
	 */
	public function get_custom_style() {
		$settings         = $this->get_settings_for_display();
		$i                = 0;
		$output           = ' ';
		$is_sub_menu_item = false;

		$this->add_render_attribute(
			'uael-nav-menu-custom',
			'class',
			'uael-nav-menu uael-nav-menu-custom uael-custom-wrapper'
		);

		?>
		<nav <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-nav-menu' ) ); ?>>
			<?php
			$output      .= '<ul ' . $this->get_render_attribute_string( 'uael-nav-menu-custom' ) . '>';
				$i        = 0;
				$is_child = false;
			foreach ( $settings['menu_items'] as $menu => $item ) {
				$repeater_sub_menu_item = $this->get_repeater_setting_key( 'text', 'menu_items', $menu );
				$repeater_link          = $this->get_repeater_setting_key( 'link', 'menu_items', $menu );

				if ( ! empty( $item['link']['url'] ) ) {

					$this->add_link_attributes( $repeater_link, $item['link'] );
				}

				if ( 'yes' === $settings['schema_support'] ) {

					$this->add_render_attribute( $repeater_link, 'itemprop', 'url' );
					$this->add_render_attribute( 'menu-sub-item' . $item['_id'], 'itemprop', 'name' );
					$this->add_render_attribute( 'menu-item' . $item['_id'], 'itemprop', 'name' );
				}

				if ( 'item_submenu' === $item['item_type'] ) {
					if ( false === $is_child ) {
						$output .= "<ul class='sub-menu parent-do-not-have-template'>";
					}
					if ( 'sub_menu' === $item['menu_content_type'] ) {

							$this->add_render_attribute(
								'menu-sub-item' . $item['_id'],
								'class',
								'menu-item child menu-item-has-children elementor-repeater elementor-repeater-item-' . $item['_id']
							);

							$output .= '<li ' . $this->get_render_attribute_string( 'menu-sub-item' . $item['_id'] ) . '>';
							$output .= '<a ' . $this->get_render_attribute_string( $repeater_link ) . " class='uael-sub-menu-item'>" . $this->get_render_attribute_string( $repeater_sub_menu_item ) . $item['text'] . '</a>';
							$output .= '</li>';
					} else {
							$this->add_render_attribute(
								'menu-content-item' . $item['_id'],
								'class',
								'menu-item saved-content child elementor-repeater elementor-repeater-item-' . $item['_id']
							);

							$output .= '<div ' . $this->get_render_attribute_string( 'menu-content-item' . $item['_id'] ) . '>';

						if ( 'saved_rows' === $item['menu_content_type'] ) {
							$saved_section_shortcode = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( apply_filters( 'wpml_object_id', $item['content_saved_rows'], 'page' ) );
							$output                 .= do_shortcode( $saved_section_shortcode );
						} elseif ( 'saved_modules' === $item['menu_content_type'] ) {
							$saved_widget_shortcode = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['content_saved_widgets'] );
							$output                .= do_shortcode( $saved_widget_shortcode );
						} elseif ( 'saved_container' === $item['menu_content_type'] ) {
							$saved_container_shortcode = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['content_saved_container'] );
							$output                   .= do_shortcode( $saved_container_shortcode );
						}
							$output .= '</div>';
					}
					$is_child         = true;
					$is_sub_menu_item = true;
				} else {

						$this->add_render_attribute( 'menu-item' . $item['_id'], 'class', 'menu-item menu-item-has-children parent parent-has-no-child elementor-repeater-item-' . $item['_id'] );
						$this->add_render_attribute( 'menu-item' . $item['_id'], 'data-dropdown-width', $item['dropdown_width'] );
						$this->add_render_attribute( 'menu-item' . $item['_id'], 'data-dropdown-pos', $item['dropdown_position'] );

						$is_child = false;
					if ( true === $is_sub_menu_item ) {

						$is_sub_menu_item = false;
						$output          .= '</ul></li>';
					}

						$i++;
						$repeater_main_link = $this->get_repeater_setting_key( 'link', 'menu_items', $menu );

					if ( ! empty( $item['link']['url'] ) && $i === $i++ ) {

						$this->add_link_attributes( $repeater_main_link, $item['link'] );
					}

						$output .= '<li ' . $this->get_render_attribute_string( 'menu-item' . $item['_id'] ) . '>';

					if ( array_key_exists( $menu + 1, $settings['menu_items'] ) ) {
						if ( 'item_submenu' === $settings['menu_items'][ $menu + 1 ]['item_type'] ) {
							$output .= "<div class='uael-has-submenu-container'>";
						}
					}

							$output .= '<a ' . $this->get_render_attribute_string( $repeater_main_link ) . " class='uael-menu-item'>";

								$output .= $this->get_render_attribute_string( $repeater_sub_menu_item ) . $item['text'];
								$output .= "<span class='uael-menu-toggle sub-arrow parent-item'><i class='fa'></i></span>";
							$output     .= '</a>';
					if ( array_key_exists( $menu + 1, $settings['menu_items'] ) ) {
						if ( 'item_submenu' === $settings['menu_items'][ $menu + 1 ]['item_type'] ) {
							$output .= '</div>';
						}
					}
				}
			}
			$output .= '</ul>';

			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</nav>
		<?php
	}

	/**
	 * Add itemprop for Navigation Schema.
	 *
	 * @since 1.33.1
	 * @param string $atts link attributes.
	 * @access protected
	 */
	public function handle_link_attrs( $atts ) {

		$atts .= ' itemprop="url"';
		return $atts;
	}

	/**
	 * Get the menu and close icon HTML.
	 *
	 * @since 1.25.2
	 * @param array $settings Widget settings array.
	 * @access public
	 */
	public function get_menu_close_icon( $settings ) {
		$menu_icon     = '';
		$close_icon    = '';
		$icons         = array();
		$icon_settings = array(
			$settings['dropdown_icon'],
			$settings['dropdown_close_icon'],
		);

		foreach ( $icon_settings as $icon ) {
			if ( UAEL_Helper::is_elementor_updated() ) {
				ob_start();
				\Elementor\Icons_Manager::render_icon(
					$icon,
					array(
						'aria-hidden' => 'true',
						'tabindex'    => '0',
					)
				);
				$menu_icon = ob_get_clean();
			} else {
				$menu_icon = '<i class="' . esc_attr( $icon ) . '" aria-hidden="true" tabindex="0"></i>';
			}

			array_push( $icons, $menu_icon );
		}

		return $icons;
	}

	/**
	 * Add itemprop for Navigation Schema.
	 *
	 * @since 1.33.1
	 * @param string $atts link attributes.
	 * @access public
	 */
	public function handle_li_atts( $atts ) {
		$atts .= ' itemprop="name"';
		return $atts;
	}

	/**
	 * Render Nav Menu output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function render() {

		$settings         = $this->get_settings_for_display();
		$menu_close_icons = array();
		$menu_close_icons = $this->get_menu_close_icon( $settings );

		if ( 'yes' === $settings['schema_support'] ) {

			$this->add_render_attribute( 'uael-nav-menu', 'itemscope', 'itemscope' );

			$this->add_render_attribute( 'uael-nav-menu', 'itemtype', 'http://schema.org/SiteNavigationElement' );

		}

		if ( 'wordpress_menu' === $settings['menu_type'] ) {
			$menu_name = isset( $settings['menu'] ) ? $settings['menu'] : '';
			$args      = array(
				'echo'        => false,
				'menu'        => $menu_name,
				'menu_class'  => 'uael-nav-menu',
				'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
				'fallback_cb' => '__return_empty_string',
				'container'   => '',
				'walker'      => new Menu_Walker(),
			);

			if ( 'yes' === $settings['schema_support'] ) {

				add_filter( 'uael_nav_menu_attrs', array( $this, 'handle_link_attrs' ) );
				add_filter( 'nav_menu_values', array( $this, 'handle_li_atts' ) );
			}

			$menu_html = wp_nav_menu( $args );
		}

		if ( 'flyout' === $settings['layout'] ) {

			if ( 'flyout' === $settings['layout'] ) {

				$this->add_render_attribute( 'uael-flyout', 'class', 'uael-flyout-wrapper' );
				if ( 'cta' === $settings['menu_last_item'] ) {

					$this->add_render_attribute( 'uael-flyout', 'data-last-item', $settings['menu_last_item'] );
				}
			}
			?>
			<div class="uael-nav-menu__toggle elementor-clickable uael-flyout-trigger" tabindex="0">
					<div class="uael-nav-menu-icon">
						<?php echo isset( $menu_close_icons[0] ) ? $menu_close_icons[0] : ''; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( 'yes' === $settings['toggle_label_show'] ) { ?>
						<span class="uael-nav-menu-label"><?php echo esc_html( $settings['toggle_label_text'] ); ?></span>
					<?php } ?>
				</div>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-flyout' ) ); ?> >
			<div class="uael-flyout-overlay elementor-clickable"></div>
			<div class="uael-flyout-container">
				<div id="uael-flyout-content-id-<?php echo esc_attr( $this->get_id() ); ?>" class="uael-side uael-flyout-<?php echo esc_attr( $settings['flyout_layout'] ); ?> uael-flyout-open" data-layout="<?php echo wp_kses_post( $settings['flyout_layout'] ); ?>" data-flyout-type="<?php echo wp_kses_post( $settings['flyout_type'] ); ?>">
					<div class="uael-flyout-content push">
						<?php if ( 'wordpress_menu' === $settings['menu_type'] ) { ?>
							<nav <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-nav-menu' ) ); ?>><?php echo $menu_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
							<?php
						} else {
							$this->get_custom_style();
						}
						?>
						<div class="elementor-clickable uael-flyout-close" tabindex="0">
							<?php echo isset( $menu_close_icons[1] ) ? $menu_close_icons[1] : ''; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php
		} else {
			$this->add_render_attribute(
				'uael-main-menu',
				'class',
				array(
					'uael-nav-menu',
					'uael-layout-' . $settings['layout'],
				)
			);

			$this->add_render_attribute( 'uael-main-menu', 'class', 'uael-nav-menu-layout' );

			$this->add_render_attribute( 'uael-main-menu', 'data-layout', $settings['layout'] );

			if ( 'cta' === $settings['menu_last_item'] ) {

				$this->add_render_attribute( 'uael-main-menu', 'data-last-item', $settings['menu_last_item'] );
			}

			if ( $settings['pointer'] ) {

				if ( 'horizontal' === $settings['layout'] || 'vertical' === $settings['layout'] ) {
					$this->add_render_attribute( 'uael-main-menu', 'class', 'uael-pointer__' . $settings['pointer'] );

					if ( in_array( $settings['pointer'], array( 'double-line', 'underline', 'overline' ), true ) ) {

						$key = 'animation_line';
						$this->add_render_attribute( 'uael-main-menu', 'class', 'uael-animation__' . $settings[ $key ] );
					} elseif ( 'framed' === $settings['pointer'] || 'text' === $settings['pointer'] ) {

						$key = 'animation_' . $settings['pointer'];
						$this->add_render_attribute( 'uael-main-menu', 'class', 'uael-animation__' . $settings[ $key ] );
					}
				}
			}

			if ( 'expandible' === $settings['layout'] ) {

				$this->add_render_attribute( 'uael-nav-menu', 'class', 'uael-dropdown-expandible' );
			}

			$this->add_render_attribute(
				'uael-nav-menu',
				'class',
				array(
					'uael-nav-menu__layout-' . $settings['layout'],
					'uael-nav-menu__submenu-' . $settings['submenu_icon'],
				)
			);

			$this->add_render_attribute( 'uael-nav-menu', 'data-toggle-icon', $menu_close_icons[0] );

			$this->add_render_attribute( 'uael-nav-menu', 'data-close-icon', $menu_close_icons[1] );

			$this->add_render_attribute( 'uael-nav-menu', 'data-full-width', $settings['full_width_dropdown'] );

			?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-main-menu' ) ); ?>>
				<div role="button" class="uael-nav-menu__toggle elementor-clickable">
					<span class="screen-reader-text">Main Menu</span>
					<div class="uael-nav-menu-icon">
						<?php echo isset( $menu_close_icons[0] ) ? $menu_close_icons[0] : ''; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( 'yes' === $settings['toggle_label_show'] ) { ?>
						<span class="uael-nav-menu-label"><?php echo esc_html( $settings['toggle_label_text'] ); ?></span>
					<?php } ?>
				</div>
			<?php if ( 'wordpress_menu' === $settings['menu_type'] ) { ?>
				<nav <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-nav-menu' ) ); ?>><?php echo $menu_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
			<?php } else { ?>
					<?php $this->get_custom_style(); ?>
			<?php } ?>
		</div>
			<?php
		}
	}
}

