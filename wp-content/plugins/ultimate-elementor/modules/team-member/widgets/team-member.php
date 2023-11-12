<?php
/**
 * UAEL Team Member.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\TeamMember\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Team.
 */
class Team_Member extends Common_Widget {

	/**
	 * Retrieve Team Member Widget name.
	 *
	 * @since 1.16.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Team_Member' );
	}

	/**
	 * Retrieve Team Member Widget heading.
	 *
	 * @since 1.16.0
	 * @access public
	 *
	 * @return string Widget heading.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Team_Member' );
	}

	/**
	 * Retrieve Team Member Widget icon.
	 *
	 * @since 1.16.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Team_Member' );
	}

	/**
	 * Retrieve Team Member Widget Keywords.
	 *
	 * @since 1.16.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Team_Member' );
	}

	/**
	 * Register Team Member widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'Team_Member', $this );

		$this->render_team_member_content_control();
		$this->register_content_separator();
		$this->register_content_social_icons_controls();
		$this->register_helpful_information();

		/* Style */
		$this->register_style_team_member_image();
		$this->register_style_team_member_content();
		$this->register_style_team_member_name();
		$this->register_style_team_member_designation();
		$this->register_style_team_member_desc();
		$this->register_style_team_member_icon();
		$this->register_content_spacing_control();
	}

	/**
	 * Register Team Member controls.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function render_team_member_content_control() {
		$this->start_controls_section(
			'section_team_member',
			array(
				'label' => __( 'General', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'uael' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'medium',
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'member_image_size',
			array(
				'label'      => __( 'Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 2000,
					),
				),
				'default'    => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-member-image img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_image_height',
			array(
				'label'      => __( 'Height', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 2000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-member-image img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'team_member_name',
			array(
				'label'       => __( 'Name', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter Name', 'uael' ),
				'default'     => __( 'John Doe', 'uael' ),
			)
		);

		$this->add_control(
			'show_team_member_desig',
			array(
				'label'        => __( 'Show Designation', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'team_member_desig',
			array(
				'label'       => __( 'Designation', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter Designation', 'uael' ),
				'default'     => __( 'CEO', 'uael' ),
				'condition'   => array(
					'show_team_member_desig' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_team_member_desc',
			array(
				'label'        => __( 'Show Description', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'team_member_desc',
			array(
				'label'       => __( 'Description', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'Describe here', 'uael' ),
				'default'     => __( 'Enter description text here.Lorem ipsum dolor sit amet consectetur adipiscing.', 'uael' ),
				'condition'   => array(
					'show_team_member_desc' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Team member separator style and controls.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_content_separator() {
		$this->start_controls_section(
			'section_separator',
			array(
				'label' => __( 'Separator', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'separator_settings',
			array(
				'label'        => __( 'Separator', 'uael' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'separator_position',
			array(
				'label'     => __( 'Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'below_desig',
				'options'   => array(
					'below_name'  => __( 'Below Name', 'uael' ),
					'below_desig' => __( 'Below Designation', 'uael' ),
					'below_desc'  => __( 'Below Description', 'uael' ),
				),
				'condition' => array(
					'separator_settings' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'separator_size',
			array(
				'label'      => __( 'Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => '20',
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-separator ' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'separator_settings' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'separator_thickness',
			array(
				'label'      => __( 'Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default'    => array(
					'size' => 2,
					'unit' => 'px',
				),
				'condition'  => array(
					'separator_settings' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-separator' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array(
					'separator_settings' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register social icons controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_content_social_icons_controls() {
		$this->start_controls_section(
			'section_social_icon',
			array(
				'label' => 'Social Icons',
			)
		);

		$this->add_control(
			'social_icons_settings',
			array(
				'label'        => __( 'Social Icons', 'uael' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater = new Repeater();

		if ( UAEL_Helper::is_elementor_updated() ) {
			$repeater->add_control(
				'new_social',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'social',
					'label_block'      => true,
					'default'          => array(
						'value'   => 'fab fa-wordpress',
						'library' => 'fa-brands',
					),
					'recommended'      => array(
						'fa-brands' => array(
							'android',
							'apple',
							'behance',
							'bitbucket',
							'codepen',
							'delicious',
							'deviantart',
							'digg',
							'dribbble',
							'elementor',
							'facebook',
							'flickr',
							'foursquare',
							'free-code-camp',
							'github',
							'gitlab',
							'globe',
							'google-plus',
							'houzz',
							'instagram',
							'jsfiddle',
							'linkedin',
							'medium',
							'meetup',
							'mixcloud',
							'odnoklassniki',
							'pinterest',
							'product-hunt',
							'reddit',
							'shopping-cart',
							'skype',
							'slideshare',
							'snapchat',
							'soundcloud',
							'spotify',
							'stack-overflow',
							'steam',
							'stumbleupon',
							'telegram',
							'thumb-tack',
							'tripadvisor',
							'tumblr',
							'twitch',
							'twitter',
							'viber',
							'vimeo',
							'vk',
							'weibo',
							'weixin',
							'whatsapp',
							'wordpress',
							'xing',
							'yelp',
							'youtube',
							'500px',
						),
						'fa-solid'  => array(
							'envelope',
							'link',
							'rss',
						),
					),
				)
			);
		} else {
			$repeater->add_control(
				'social',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'default'     => 'fa fa-wordpress',
					'include'     => array(
						'fa fa-android',
						'fa fa-apple',
						'fa fa-behance',
						'fa fa-bitbucket',
						'fa fa-codepen',
						'fa fa-delicious',
						'fa fa-deviantart',
						'fa fa-digg',
						'fa fa-dribbble',
						'fa fa-envelope',
						'fa fa-facebook',
						'fa fa-flickr',
						'fa fa-foursquare',
						'fa fa-free-code-camp',
						'fa fa-github',
						'fa fa-gitlab',
						'fa fa-google-plus',
						'fa fa-houzz',
						'fa fa-instagram',
						'fa fa-jsfiddle',
						'fa fa-linkedin',
						'fa fa-medium',
						'fa fa-meetup',
						'fa fa-mixcloud',
						'fa fa-odnoklassniki',
						'fa fa-pinterest',
						'fa fa-product-hunt',
						'fa fa-reddit',
						'fa fa-rss',
						'fa fa-shopping-cart',
						'fa fa-skype',
						'fa fa-slideshare',
						'fa fa-snapchat',
						'fa fa-soundcloud',
						'fa fa-spotify',
						'fa fa-stack-overflow',
						'fa fa-steam',
						'fa fa-stumbleupon',
						'fa fa-telegram',
						'fa fa-thumb-tack',
						'fa fa-tripadvisor',
						'fa fa-tumblr',
						'fa fa-twitch',
						'fa fa-twitter',
						'fa fa-vimeo',
						'fa fa-vk',
						'fa fa-weibo',
						'fa fa-weixin',
						'fa fa-whatsapp',
						'fa fa-wordpress',
						'fa fa-xing',
						'fa fa-yelp',
						'fa fa-youtube',
						'fa fa-500px',
					),
				)
			);
		}

		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'uael' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => array(
					'is_external' => 'true',
				),
				'placeholder' => __( 'https://your-link.com', 'uael' ),
			)
		);

		$this->add_control(
			'social_icon_list',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'new_social' => array(
							'value'   => 'fab fa-facebook',
							'library' => 'fa-brands',
						),
					),
					array(
						'new_social' => array(
							'value'   => 'fab fa-twitter',
							'library' => 'fa-brands',
						),
					),
					array(
						'new_social' => array(
							'value'   => 'fab fa-google-plus',
							'library' => 'fa-brands',
						),
					),

				),
				'condition'   => array(
					'social_icons_settings' => 'yes',
				),
				'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( new_social, social, true, migrated, true ) }}}', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'        => __( 'Shape', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'square',
				'options'      => array(
					'square'  => __( 'Square', 'uael' ),
					'rounded' => __( 'Rounded', 'uael' ),
					'circle'  => __( 'Circle', 'uael' ),
				),
				'prefix_class' => 'elementor-shape-',
				'condition'    => array(
					'social_icons_settings' => 'yes',
				),
				'default'      => 'rounded',
			)
		);

		$this->add_control(
			'social_icons_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '10',
					'unit'   => '%',
					'right'  => '10',
					'unit'   => '%',
					'bottom' => '10',
					'unit'   => '%',
					'left'   => '10',
					'unit'   => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-social-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'shape'                 => array( 'rounded' ),
					'social_icons_settings' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/team-member-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					/* translators: %1$s Doc Link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);
			$this->end_controls_section();
		}
	}


	/**
	 * Register team member image style.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_style_team_member_image() {
		$this->start_controls_section(
			'section_team_member_image_style',
			array(
				'label' => __( 'Image', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'        => __( 'Image Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'above',
				'options'      => array(
					'above' => __( 'Top', 'uael' ),
					'left'  => __( 'Left', 'uael' ),
					'right' => __( 'Right', 'uael' ),
				),
				'prefix_class' => 'uael-member-image-pos-',
			)
		);

		$this->add_control(
			'member_mob_view',
			array(
				'label'       => __( 'Responsive Support', 'uael' ),
				'description' => __( 'Choose the breakpoint you want the layout will stack.', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'   => __( 'No', 'uael' ),
					'tablet' => __( 'For Tablet & Mobile ', 'uael' ),
					'mobile' => __( 'For Mobile Only', 'uael' ),
				),
				'condition'   => array(
					'image_position' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'member_image_valign',
			array(
				'label'       => __( 'Vertical Alignment', 'uael' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'top'    => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'uael' ),
						'icon'  => 'eicon-v-align-middle',
					),
				),
				'default'     => 'top',
				'condition'   => array(
					'image_position' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'image_shape',
			array(
				'label'        => __( 'Shape', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'square',
				'options'      => array(
					'square'  => __( 'Default', 'uael' ),
					'rounded' => __( 'Rounded', 'uael' ),
					'circle'  => __( 'Circle', 'uael' ),
				),
				'prefix_class' => 'uael-shape-',
			)
		);

		$this->add_control(
			'team_member_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '10',
					'unit'   => '%',
					'right'  => '10',
					'unit'   => '%',
					'bottom' => '10',
					'unit'   => '%',
					'left'   => '10',
					'unit'   => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-member-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'image_shape' => array( 'rounded' ),
				),
			)
		);

		$this->add_control(
			'image_border',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-team-member-image img' => 'border-style: {{VALUE}};',
				),
			)
		);

			$this->add_control(
				'image_border_size',
				array(
					'label'      => __( 'Border Width', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'default'    => array(
						'top'    => '1',
						'bottom' => '1',
						'left'   => '1',
						'right'  => '1',
						'unit'   => 'px',
					),
					'condition'  => array(
						'image_border!' => 'none',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-team-member-image img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing:border-box;',
					),
				)
			);

			$this->add_control(
				'image_border_color',
				array(
					'label'     => __( 'Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'condition' => array(
						'image_border!' => 'none',
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-team-member-image img' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'image_border_hover_color',
				array(
					'label'     => __( 'Border Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-team-member-image img:hover' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'image_border!' => 'none',
					),
				)
			);

		$this->add_control(
			'hover_animation',
			array(
				'label' => __( 'Hover Animation', 'uael' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register team member content style.
	 *
	 * @since 1.33.0
	 * @access protected
	 */
	protected function register_style_team_member_content() {

		$this->start_controls_section(
			'section_team_member_content_style',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align_team_member',
			array(
				'label'        => __( 'Overall Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'condition'    => array(
					'image_position' => 'above',
				),
				'default'      => 'center',
				'prefix_class' => 'uael%s-team-member-align-',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'     => __( 'Padding', 'uael' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .uael-team-member-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Team member Name style.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_style_team_member_name() {
		$this->start_controls_section(
			'section_team_member_name_style',
			array(
				'label'     => __( 'Name', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'team_member_name!' => '',
				),
			)
		);

		$this->add_control(
			'name_size',
			array(
				'label'   => __( 'HTML Tag', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h3',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .uael-team-name',
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-team-name' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Register Team member designation style.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_style_team_member_designation() {
		$this->start_controls_section(
			'section_team_member_designation_style',
			array(
				'label'     => __( 'Designation', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_team_member_desig' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'designation_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-team-desig',
			)
		);

		$this->add_control(
			'designation_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-team-desig' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Team member description style.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_style_team_member_desc() {
		$this->start_controls_section(
			'section_team_member_desc_style',
			array(
				'label'     => __( 'Description', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_team_member_desc' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-team-desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-team-desc' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Register social icons style.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_style_team_member_icon() {
		$this->start_controls_section(
			'section_social_style',
			array(
				'label'     => __( 'Social Icons', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'social_icons_settings' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => __( 'Icon Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-social-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_bg_size',
			array(
				'label'     => __( 'Icon Background Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon, {{WRAPPER}} .elementor-social-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'          => __( 'Padding', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'selectors'      => array(
					'{{WRAPPER}} .elementor-social-icon' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'tablet_default' => array(
					'unit' => 'em',
				),
				'mobile_default' => array(
					'unit' => 'em',
				),
				'range'          => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
					),
				),
			)
		);

		$icon_spacing = is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};';

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:not(:last-child)' => $icon_spacing,
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'   => __( 'Color', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Official Color', 'uael' ),
					'custom'  => __( 'Custom', 'uael' ),
				),
			)
		);

		$this->start_controls_tabs(
			'icon_style_tabs'
		);

		$this->start_controls_tab(
			'icon_style_normal_tab',
			array(
				'label'     => __( 'Normal', 'uael' ),
				'condition' => array(
					'icon_color' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_primary_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'icon_color' => 'custom',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:not(:hover), {{WRAPPER}} .elementor-social-icon:not(:hover) svg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_secondary_color_normal',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'icon_color' => 'custom',
				),

				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:not(:hover) i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon:not(:hover) svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_style_hover_tab',
			array(
				'label'     => __( 'Hover', 'uael' ),
				'condition' => array(
					'icon_color' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_primary_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'icon_color' => 'custom',
				),

				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:hover, {{WRAPPER}} .elementor-social-icon:hover svg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_secondary_color_hover',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'icon_color' => 'custom',
				),

				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'social_icon_border',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .elementor-social-icon' => 'border-style: {{VALUE}};',
				),
			)
		);

			$this->add_control(
				'social_icon_border_size',
				array(
					'label'      => __( 'Border Width', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'default'    => array(
						'top'    => '1',
						'bottom' => '1',
						'left'   => '1',
						'right'  => '1',
						'unit'   => 'px',
					),
					'condition'  => array(
						'social_icon_border!' => 'none',
					),
					'selectors'  => array(
						'{{WRAPPER}} .elementor-social-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing:content-box;',
					),
				)
			);

			$this->add_control(
				'social_icon_border_color',
				array(
					'label'     => __( 'Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'condition' => array(
						'social_icon_border!' => 'none',
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .elementor-social-icon' => 'border-color: {{VALUE}};',
					),
				)
			);

		$this->add_control(
			'social_icon_border_hover_color',
			array(
				'label'     => __( 'Border Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-social-icon:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'social_icon_border!' => 'none',
				),
			)
		);

		$this->add_control(
			'icon_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'uael' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Team Member spacing controls.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function register_content_spacing_control() {

		$this->start_controls_section(
			'section_content_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_margin_bottom',
			array(
				'label'      => __( 'Image Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.uael-member-image-pos-above .uael-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-member-image-pos-left .uael-team-member-image' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-member-image-pos-right .uael-team-member-image' => 'margin-left: {{SIZE}}{{UNIT}};',
					'(tablet){{WRAPPER}}.uael-member-image-pos-right .uael-member-stacked-tablet .uael-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-left:0;',
					'(tablet){{WRAPPER}}.uael-member-image-pos-left .uael-member-stacked-tablet .uael-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-right:0;',
					'(mobile){{WRAPPER}}.uael-member-image-pos-right .uael-member-stacked-mobile .uael-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-left:0;',
					'(mobile){{WRAPPER}}.uael-member-image-pos-left .uael-member-stacked-mobile .uael-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-right:0;',
				),
			)
		);

		$this->add_responsive_control(
			'name_margin',
			array(
				'label'      => __( 'Name Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-name' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'team_member_name!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'desig_margin',
			array(
				'label'      => __( 'Designation Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-desig' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_team_member_desig' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'separator_margin',
			array(
				'label'      => __( 'Separator Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-separator-wrapper' => 'padding-bottom:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'separator_settings' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'desc_margin',
			array(
				'label'      => __( 'Description Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-team-desc' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_team_member_desc' => 'yes',
				),
			)
		);
	}

	/**
	 * Get the position of Separator.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function get_separator_position() { ?>
		<div class="uael-separator-wrapper">
			<span class="uael-separator"></span>
		</div>
		<?php
	}

	/**
	 * Render team member widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HteamL.
	 *
	 * @since 1.16.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		if ( 'left' === $settings['image_position'] || 'right' === $settings['image_position'] ) {
			if ( 'tablet' === $settings['member_mob_view'] ) {
				$this->add_render_attribute( 'member-classname', 'class', 'uael-member-stacked-tablet' );
			}
			if ( 'mobile' === $settings['member_mob_view'] ) {
				$this->add_render_attribute( 'member-classname', 'class', 'uael-member-stacked-mobile' );
			}
			if ( 'middle' === $settings['member_image_valign'] ) {
				$this->add_render_attribute( 'member-classname', 'class', 'uael-member-image-valign-middle' );
			} else {
				$this->add_render_attribute( 'member-classname', 'class', 'uael-member-image-valign-top' );
			}
		}
		$this->add_render_attribute( 'member-classname', 'class', 'uael-team-member' );

		$fallback_defaults = array(
			'fa fa-facebook',
			'fa fa-twitter',
			'fa fa-google-plus',
		);

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'member-classname' ) ); ?>>
			<div class = "uael-team-member-wrap" >
				<div class="uael-member-wrap" >
					<?php
					$has_image = $settings['image']['url'];
					if ( $has_image ) :
						?>
							<div class="uael-team-member-image">
							<?php
							$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings );
							echo '<div class=elementor-animation-' . esc_attr( $settings['hover_animation'] ) . '>' . wp_kses_post( $image_html ) . '</div>';
							?>
							</div>
					<?php endif; ?>
					<?php
					$this->add_render_attribute( 'member_content', 'class', 'uael-team-member-content' );
					$this->add_render_attribute( 'team_member_name', 'class', 'uael-team-name' );
					$this->add_inline_editing_attributes( 'team_member_name' );
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'member_content' ) ); ?>>
					<?php if ( '' !== $settings['team_member_name'] ) { ?>
						<div class="uael-team-member-name">
						<?php
							$name          = $settings['team_member_name'];
							$name_size_tag = UAEL_Helper::validate_html_tag( $settings['name_size'] );
							$name_html     = sprintf( '<%1$s %2$s>%3$s</%1$s>', $name_size_tag, $this->get_render_attribute_string( 'team_member_name' ), $name );
							echo wp_kses_post( $name_html );
						?>
						</div>
					<?php } ?>
					<?php
					if ( 'yes' === $settings['separator_settings'] ) {
						if ( 'below_name' === $settings['separator_position'] ) {
							$this->get_separator_position();
						}
					}

					?>
					<?php if ( 'yes' === $settings['show_team_member_desig'] && '' !== $settings['team_member_desig'] ) { ?>
						<?php
						$this->add_render_attribute( 'team_member_desig', 'class', 'uael-team-desig' );
						$this->add_inline_editing_attributes( 'team_member_desig' );
						?>
						<div class="uael-team-member-designation">
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'team_member_desig' ) ); ?>><?php echo wp_kses_post( $settings['team_member_desig'] ); ?>
							</div>
						</div>
					<?php } ?>
					<?php
					if ( 'yes' === $settings['separator_settings'] ) {
						if ( 'below_desig' === $settings['separator_position'] ) {
							$this->get_separator_position();
						}
					}

					?>
					<?php if ( 'yes' === $settings['show_team_member_desc'] && '' !== $settings['team_member_desc'] ) { ?>
						<?php
						$this->add_render_attribute( 'team_member_desc', 'class', 'uael-team-desc' );
						$this->add_inline_editing_attributes( 'team_member_desc' );
						?>
						<div class="uael-team-member-desc">
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'team_member_desc' ) ); ?>><?php echo wp_kses_post( $settings['team_member_desc'] ); ?></div>
						</div>
					<?php } ?>
					<?php
					if ( 'yes' === $settings['separator_settings'] ) {
						if ( 'below_desc' === $settings['separator_position'] ) {
							$this->get_separator_position();
						}
					}

					?>
					<?php if ( 'yes' === $settings['social_icons_settings'] ) { ?>
						<div class="elementor-social-icons-wrapper">
							<div class="uael-team-social-icon">
							<?php
							foreach ( $settings['social_icon_list'] as $index => $item ) {

								if ( UAEL_Helper::is_elementor_updated() ) {

									$migration_allowed = \Elementor\Icons_Manager::is_migration_allowed();

									$migrated = isset( $item['__fa4_migrated']['new_social'] );
									$is_new   = empty( $item['social'] ) && $migration_allowed;
									$social   = '';

									// add old default.
									if ( empty( $item['social'] ) && ! $migration_allowed ) {
										$item['social'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
									}

									if ( ! empty( $item['social'] ) ) {
										$social = str_replace( 'fa fa-', '', $item['social'] );
									}

									if ( ( $is_new || $migrated ) && 'svg' !== $item['new_social']['library'] ) {
										$social = explode( ' ', $item['new_social']['value'], 2 );
										if ( empty( $social[1] ) ) {
											$social = '';
										} else {
											$social = str_replace( 'fa-', '', $social[1] );
										}
									}
									if ( 'svg' === $item['new_social']['library'] ) {
										$social = '';
									}

									$link_key        = 'link_' . $index;
									$class_animation = ' elementor-animation-' . $settings['icon_hover_animation'];

									$this->add_render_attribute(
										$link_key,
										'class',
										array(
											'elementor-icon',
											'elementor-social-icon',
											'elementor-social-icon-' . $social . $class_animation,
											'elementor-repeater-item-' . $item['_id'],
										)
									);

									$this->add_link_attributes( $link_key, $item['link'] );

									?>
									<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
										<span class="elementor-screen-only"><?php echo esc_html( ucwords( $social ) ); ?></span>
										<?php
										if ( $is_new || $migrated ) {
											\Elementor\Icons_Manager::render_icon( $item['new_social'] );
										} elseif ( ! empty( $item['social'] ) ) {
											?>
											<i class="<?php echo esc_attr( $item['social'] ); ?>"></i>
										<?php } ?>
									</a>
									<?php
								} elseif ( ! empty( $item['social'] ) ) {
									$social   = str_replace( 'fa fa-', '', $item['social'] );
									$link_key = 'link_' . $index;
									$this->add_render_attribute( $link_key, 'href', $item['link']['url'] );
									if ( $item['link']['is_external'] ) {
										$this->add_render_attribute( $link_key, 'target', '_blank' );
									}
									if ( $item['link']['nofollow'] ) {
										$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
									}
									$class_animation = ' elementor-animation-' . $settings['icon_hover_animation'];
									?>
									<a class="elementor-icon elementor-social-icon elementor-social-icon-<?php echo esc_attr( $social ) . esc_attr( $class_animation ); ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
										<span class="elementor-screen-only"><?php echo esc_html( ucwords( $social ) ); ?></span>
										<i class="<?php echo esc_attr( $item['social'] ); ?>"></i>
									</a>
								<?php } ?>
							<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
		<?php
	}

	/**
	 * Render team member output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			if ( 'left' == settings.image_position || 'right' == settings.image_position ) {
				if ( 'tablet' == settings.member_mob_view  ) {

					view.addRenderAttribute( 'member-classname', 'class', 'uael-member-stacked-tablet' );
				}
				if ( 'mobile' == settings.member_mob_view  ) {

					view.addRenderAttribute( 'member-classname', 'class', 'uael-member-stacked-mobile' );
				}
				if ( 'middle' == settings.member_image_valign ) {
					view.addRenderAttribute( 'member-classname', 'class', 'uael-member-image-valign-middle' );
				} else {
					view.addRenderAttribute( 'member-classname', 'class', 'uael-member-image-valign-top' );
				}
			}
			view.addRenderAttribute( 'member-classname', 'class', 'uael-team-member' );
		#>
		<div {{{ view.getRenderAttributeString( 'member-classname' ) }}} > <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
			<div class="uael-team-member-wrap">
				<div class="uael-member-wrap">
					<#
					var image = {
						id: settings.image.id,
						url: settings.image.url,
						size: settings.image_size,
						dimension: settings.image_custom_dimension,
						model: view.getEditModel()
					};

					var image_url = elementor.imagesManager.getImageUrl( image );
					if( image_url !== '' ) {
					#>
						<div class="uael-team-member-image"><img src="{{ image_url }}" class="elementor-animation-{{settings.hover_animation}}" ></div>
						<#
					}
					view.addRenderAttribute( 'member_content', 'class', 'uael-team-member-content' ); #>
					<div {{{ view.getRenderAttributeString( 'member_content' )}}} > <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<div class="uael-team-member-name">
							<# if ( '' !== settings.team_member_name ) {
								view.addRenderAttribute( 'team_member_name', 'class', 'uael-team-name' );
								view.addInlineEditingAttributes( 'team_member_name' );
								var memberNameHtml = settings.team_member_name;

								var nameSizeTag = settings.name_size;

								if ( typeof elementor.helpers.validateHTMLTag === "function" ) {
									nameSizeTag = elementor.helpers.validateHTMLTag( nameSizeTag );
								} else if( UAEWidgetsData.allowed_tags ) {
									nameSizeTag = UAEWidgetsData.allowed_tags.includes( nameSizeTag.toLowerCase() ) ? nameSizeTag : 'div';
								}
								#>
								<{{ nameSizeTag }} {{{ view.getRenderAttributeString( 'team_member_name' )}}}>{{settings.team_member_name}}</{{ nameSizeTag }}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
							<# } #>
						</div>
						<#
						if( 'yes' == settings.separator_settings) {
							if( 'below_name' == settings.separator_position ) { #>
								<div class="uael-separator-wrapper">
									<div class="uael-separator"></div>
								</div>
							<# } #>
						<# } #>
						<div class="uael-team-member-designation">
							<# if ( 'yes' === settings.show_team_member_desig && '' !== settings.team_member_desig ) {
								view.addRenderAttribute( 'team_member_desig', 'class', 'uael-team-desig' );

								view.addInlineEditingAttributes( 'team_member_desig' );

								var memberDesignHtml = settings.team_member_desig;
								#>
								<div {{ view.getRenderAttributeString( 'team_member_desig' ) }}>{{ memberDesignHtml }}</div>
							<# } #>
						</div>
						<# if( 'yes' == settings.separator_settings) {
							if( 'below_desig' == settings.separator_position ) { #>
								<div class="uael-separator-wrapper">
									<div class="uael-separator"></div>
								</div>
							<# } #>
						<# } #>
						<div class="uael-team-member-desc">
							<# if ( 'yes' === settings.show_team_member_desc && '' !== settings.team_member_desc ) {
								view.addRenderAttribute( 'team_member_desc', 'class', 'uael-team-desc' );

								view.addInlineEditingAttributes( 'team_member_desc' );

								var memberDescHtml = settings.team_member_desc;
								#>
								<div {{ view.getRenderAttributeString( 'team_member_desc' ) }}>{{ memberDescHtml }}</div>
							<# } #>
						</div>
						<# if( 'yes' == settings.separator_settings) {
							if( 'below_desc' == settings.separator_position ) { #>
								<div class="uael-separator-wrapper">
									<div class="uael-separator"></div>
								</div>
							<# } #>
						<# } #>
						<# if( 'yes' == settings.social_icons_settings) { #>
							<# var iconsHTML = {}; #>
							<div class="elementor-social-icons-wrapper">
								<div class="uael-team-social-icon">
									<# _.each( settings.social_icon_list, function( item, index ) { #>
										<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
											<# var link = item.link ? item.link.url : '',
												migrated = elementor.helpers.isIconMigrated( item, 'new_social' ),
												social = elementor.helpers.getSocialNetworkNameFromIcon( item.new_social, item.social, false, migrated );
												#>
												<a class="elementor-icon elementor-social-icon elementor-social-icon-{{ social }} elementor-animation-{{ settings.icon_hover_animation }} elementor-repeater-item-{{item._id}}" href="{{ link }}">

												<span class="elementor-screen-only">{{ social }}</span>

												<#
													iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.new_social, {}, 'i', 'object' );
													if ( ( ! item.social || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
														{{{ iconsHTML[ index ].value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
													<# } else { #>
														<i class="{{ item.social }}"></i>
													<# }
												#>
											</a>
										<?php } else { ?>
											<# var link = item.link ? item.link.url : '',
											social = item.social.replace( 'fa fa-', '' ); #>
											<a class="elementor-icon elementor-social-icon elementor-social-icon-{{ social }} elementor-animation-{{ settings.icon_hover_animation }}" href="{{ link }}">
												<span class="elementor-screen-only">{{ social }}</span>
												<i class="{{ item.social }}"></i>
											</a>
										<?php } ?>
									<# } ); #>
								</div>
							</div>
						<# } #>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
