<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Integrations;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for Elementor Builder plugin.
 *
 * @since 1.20.0
 */
class Elementor_Builder {


	/** @var string visibility section ID */
	private $section_visibility = 'wcm_element_visibility_section';

	/** @var string visibility condition control */
	private $control_visibility_condition = 'wcm_element_visibility_condition';

	/** @var string visibility condition control "All members" option value */
	private $visibility_condition_all_members_option_value = 'wcm-all';

	/** @var string visibility condition control "Non members" option value */
	private $visibility_condition_non_members_option_value = 'wcm-none';

	/** @var string visibility condition control default value */
	private $control_visibility_condition_default = 'visible_to_everyone';

	/** @var string show for members plans control */
	private $control_visibility_plans = 'wcm_element_visibility_show_plans';

	/** @var string hide from members plans control */
	private $control_hidden_plans = 'wcm_element_visibility_hide_plans';

	/** @var string "Content restricted" message toggle control */
	private $control_show_content_restricted_message = 'wcm_element_visibility_show_content_restricted_message';

	/** @var string "Content restricted" message content control */
	private $control_content_restricted_message = 'wcm_element_visibility_content_restricted_message';

	/** @var string[] associative array of Memberships Plans slugs and names */
	private $available_plans_list = [];

	/** @var string[] list of user active memberships plans slugs */
	private $user_active_plans = [];

	/** @var boolean[] Associative array to cache widgets visibility status */
	private $widgets_visibility_cache = [];


	/**
	 * Constructor.
	 *
	 * @since 1.20.0
	 */
	public function __construct() {

		// add section for settings
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_wcm_visibility_section' ] );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_wcm_visibility_section' ] );

		// register section controls
		add_action( 'elementor/element/common/' . $this->section_visibility . '/before_section_end', [ $this, 'register_wcm_visibility_controls' ] );
		add_action( 'elementor/element/section/' . $this->section_visibility . '/before_section_end', [ $this, 'register_wcm_visibility_controls' ] );

		// determine whether element should be rendered or not
		add_filter( 'elementor/frontend/section/should_render', [ $this, 'elementor_should_render_element' ], 10, 2 );
		add_filter( 'elementor/frontend/widget/should_render', [ $this, 'elementor_should_render_element' ], 10, 2 );
		add_filter( 'elementor/frontend/repeater/should_render', [ $this, 'elementor_should_render_element' ], 10, 2 );

		// determine whether to replace widget's content with "Content Restricted" alert message or not
		add_filter( 'elementor/widget/render_content', [ $this, 'maybe_render_content_restricted_message_instead' ], 10, 2 );
	}


	/**
	 * Renders the "Content restricted" message instead of the widget's content.
	 *
	 * Applies if widget should be hidden from members and "Content restricted" message option is enabled.
	 *
	 * @internal
	 *
	 * @since 1.20.0
	 *
	 * @param string $widget_content Elementor Widget content
	 * @param \Elementor\Widget_Base $widget Elementor Widget object
	 * @return string
	 */
	public function maybe_render_content_restricted_message_instead( $widget_content, $widget ) {

		$widget_settings = $widget->get_settings();

		// should render "Content restricted" message instead?
		if (
		     false === $this->is_elementor_in_edit_mode() &&
		     false === $this->is_current_user_can_access_all_restricted_content() &&
		     $this->should_show_content_restricted_message( $widget_settings ) &&
		     false === $this->should_render_element( $widget_settings )
		) {

			return $this->get_content_restricted_message_html( $widget_settings );
		}

		return $widget_content;
	}


	/**
	 * Determines whether to render the Elementor element or not based on the visibility settings.
	 *
	 * @internal
	 *
	 * @since 1.20.0
	 *
	 * @param bool $should_render should render the widget or not boolean
	 * @param \Elementor\Element_Base $element Elementor Element object
	 * @return bool
	 */
	public function elementor_should_render_element( $should_render, $element ) {

		$element_settings = $element->get_settings();

		if ( false === $this->is_current_user_can_access_all_restricted_content() && false === $this->should_render_element( $element_settings ) ) {

			return $this->should_show_content_restricted_message( $element_settings );
		}

		return $should_render;
	}


	/**
	 * Registers visibility section controls.
	 *
	 * @internal
	 *
	 * @since 1.20.0
	 *
	 * @param \Elementor\Element_Base $element Elementor element object
	 */
	public function register_wcm_visibility_controls( $element ) {

		$element->add_control( $this->control_visibility_condition, [
			'label'       => __( 'Membership Visibility', 'woocommerce-memberships' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => $this->control_visibility_condition_default,
			'options'     => [
				'visible_to_everyone' => __( 'Everyone can see this widget', 'woocommerce-memberships' ),
				'visible_only_for'    => __( 'Show this widget to...', 'woocommerce-memberships' ),
				'hidden_from'         => __( 'Hide this widget from...', 'woocommerce-memberships' ),
			],
			'multiple'    => false,
			'show_label'  => false,
			'label_block' => true,
		] );

		$element->add_control( $this->control_visibility_plans, [
			'type'        => \Elementor\Controls_Manager::SELECT2,
			'label'       => __( 'Show for:', 'woocommerce-memberships' ),
			'options'     => $this->get_membership_plans_options( 'both' ),
			'default'     => [ $this->visibility_condition_all_members_option_value ], // by default show for all members only
			'multiple'    => true,
			'show_label'  => false,
			'label_block' => true,
			'condition'   => [
				$this->control_visibility_condition => 'visible_only_for',
			],
		] );

		$element->add_control( $this->control_hidden_plans, [
			'type'        => \Elementor\Controls_Manager::SELECT2,
			'label'       => __( 'Hidden from:', 'woocommerce-memberships' ),
			'options'     => $this->get_membership_plans_options( 'both' ),
			'default'     => [ $this->visibility_condition_non_members_option_value ], // by default, hide from non-members
			'multiple'    => true,
			'show_label'  => false,
			'label_block' => true,
			'condition'   => [
				$this->control_visibility_condition => 'hidden_from',
			],
		] );

		// additional controls for Widget based elements only
		if ( $this->is_widget_based_element( $element ) ) {

			$element->add_control( $this->control_show_content_restricted_message, [
				'label'        => __( 'Show restricted content message', 'woocommerce-memberships' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'woocommerce-memberships' ),
				'label_off'    => __( 'No', 'woocommerce-memberships' ),
				'return_value' => 'yes',
				'label_block'  => false,
				'condition'    => [
					$this->control_visibility_condition . '!' => 'visible_to_everyone',
				],
			] );

			$element->add_control( $this->control_content_restricted_message, [
				'label'       => __( 'Restricted content message:', 'woocommerce-memberships' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => $this->get_default_content_restricted_message(),
				'rows'        => 3,
				'show_label'  => false,
				'label_block' => true,
				'condition'   => [
					$this->control_show_content_restricted_message => 'yes',
					$this->control_visibility_condition . '!'      => 'visible_to_everyone',
				],
			] );
		}
	}


	/**
	 * Registers the new Memberships Visibility control section.
	 *
	 * @internal
	 *
	 * @since 1.20.0
	 *
	 * @param \Elementor\Element_Section $element_section
	 */
	public function register_wcm_visibility_section( $element_section ) {

		$element_section->start_controls_section( $this->section_visibility, [
			'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			'label' => __( 'Memberships visibility', 'woocommerce-memberships' ),
		] );

		$element_section->end_controls_section();
	}


	/**
	 * Determines whether a widget should be rendered or not based on the given settings.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor Widget associative array settings
	 * @return bool
	 */
	private function should_render_element( $widget_settings ) {

		$cache_key = md5( json_encode( $widget_settings ) );

		if ( array_key_exists( $cache_key, $this->widgets_visibility_cache ) ) {
			return $this->widgets_visibility_cache[ $cache_key ];
		}

		// by default it should render
		$should_render = true;

		if ( false === $this->is_widget_visible_for_everyone( $widget_settings ) ) {

			$all_user_active_memberships_plans = $this->get_logged_in_user_active_memberships_plans();

			if ( $this->is_widget_visible_for_members( $widget_settings ) ) {

				// show to members
				$should_render = $this->should_show_for_members( $widget_settings, $all_user_active_memberships_plans );

			} elseif ( $this->is_widget_hidden_from_members( $widget_settings ) ) {

				// hide from members
				$should_render = ! $this->should_hide_from_members( $widget_settings, $all_user_active_memberships_plans );
			}
		}

		$this->widgets_visibility_cache[ $cache_key ] = $should_render;

		return $should_render;
	}


	/**
	 * Determines whether the widget should be shown to members of the given plans.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor Widget associative array settings
	 * @param array $memberships_plans_slugs list of membership plan by slugs
	 * @return bool returns false to hide the widget from the current user, true otherwise
	 */
	private function should_show_for_members( $widget_settings, $memberships_plans_slugs ) {

		$show_for_plans = $this->get_show_for_plans_setting( $widget_settings );

		// show to members of any plans
		if ( ! empty( $memberships_plans_slugs ) && in_array( $this->visibility_condition_all_members_option_value, $show_for_plans, true ) ) {
			return true;
		}

		// show to non-members only
		if ( empty( $memberships_plans_slugs ) && in_array( $this->visibility_condition_non_members_option_value, $show_for_plans, true ) ) {
			return true;
		}

		// checks if the user is a member of a matching plan among those listed in the widget setting
		$matching_plans = array_intersect( $memberships_plans_slugs, $show_for_plans );

		return count( $matching_plans ) > 0;
	}


	/**
	 * Determines if should hide the widget from members.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor Widget associative array settings
	 * @param array $memberships_plans_slugs membership plan slugs list
	 * @return bool
	 */
	private function should_hide_from_members( $widget_settings, $memberships_plans_slugs ) {

		$hide_from_plans = $this->get_hide_for_plans_setting( $widget_settings );

		// hide from members of any plans
		if ( ! empty( $memberships_plans_slugs ) && in_array( $this->visibility_condition_all_members_option_value, $hide_from_plans, true ) ) {
			return true;
		}

		// hide from non-members
		if ( empty( $memberships_plans_slugs ) && in_array( $this->visibility_condition_non_members_option_value, $hide_from_plans, true ) ) {
			return true;
		}

		// checks if the user is a member of a matching plan among those listed in the widget setting
		$matching_plans = array_intersect( $memberships_plans_slugs, $hide_from_plans );

		return count( $matching_plans ) > 0;
	}


	/**
	 * Gets the "Content restricted" message from the Elementor widget's settings.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return string
	 */
	private function get_content_restricted_message( $widget_settings ) {

		return isset( $widget_settings[ $this->control_content_restricted_message ] ) ? $widget_settings[ $this->control_content_restricted_message ] : '';
	}


	/**
	 * Determines whether the content restricted message should be shown based on widget's settings.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return bool
	 */
	private function should_show_content_restricted_message( $widget_settings ) {

		return isset( $widget_settings[ $this->control_show_content_restricted_message ] ) && 'yes' === $widget_settings[ $this->control_show_content_restricted_message ];
	}


	/**
	 * Determines whether the widget's contents should be visibile to all users.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return bool
	 */
	private function is_widget_visible_for_everyone( $widget_settings ) {

		return 'visible_to_everyone' === $this->get_widget_visibility_condition_value( $widget_settings );
	}


	/**
	 * Determines whether the widget's content should be visible to members only.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return bool
	 */
	private function is_widget_visible_for_members( $widget_settings ) {

		return 'visible_only_for' === $this->get_widget_visibility_condition_value( $widget_settings );
	}


	/**
	 * Determines whether the widget's content should be visible to non-members.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return bool
	 */
	private function is_widget_hidden_from_members( $widget_settings ) {

		return 'hidden_from' === $this->get_widget_visibility_condition_value( $widget_settings );
	}


	/**
	 * Gets visibility condition value based on the given Elementor widget settings.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return string
	 */
	private function get_widget_visibility_condition_value( $widget_settings ) {

		if ( ! isset( $widget_settings[ $this->control_visibility_condition ] ) ) {
			return $this->control_visibility_condition_default;
		}

		return $widget_settings[ $this->control_visibility_condition ];
	}


	/**
	 * Gets a list of membership plans to show the widget's content to.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return array
	 */
	private function get_show_for_plans_setting( $widget_settings ) {

		return isset( $widget_settings[ $this->control_visibility_plans ] ) ? $widget_settings[ $this->control_visibility_plans ] : [];
	}


	/**
	 * Gets a list of membership plans to hiden the widget's content from.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return array
	 */
	private function get_hide_for_plans_setting( $widget_settings ) {

		return isset( $widget_settings[ $this->control_hidden_plans ] ) ? $widget_settings[ $this->control_hidden_plans ] : [];
	}


	/**
	 * Gets the "Content restricted" message HTML to display for a widget whose content is being restricted.
	 *
	 * @since 1.20.0
	 *
	 * @param array $widget_settings Elementor widget settings associative array
	 * @return string
	 */
	private function get_content_restricted_message_html( $widget_settings ) {

		$message = $this->get_content_restricted_message( $widget_settings );

		return \WC_Memberships_User_Messages::get_notice_html( $this->control_visibility_condition, $message, [] );
	}


	/**
	 * Gets the "Content restricted" message.
	 *
	 * @since 1.20.0
	 *
	 * @return string
	 */
	private function get_default_content_restricted_message() {

		return __( 'You are not authorized to view this content.', 'woocommerce-memberships' );
	}


	/**
	 * Checks if a given Elementor element is Widget based or not.
	 *
	 * @since 1.20.0
	 *
	 * @param \Elementor\Element_Base $element Elementor element object
	 * @return bool
	 */
	private function is_widget_based_element( $element ) {

		return $element instanceof \Elementor\Widget_Base;
	}


	/**
	 * Checks if Elementor currently is in edit mode or not.
	 *
	 * @since 1.20.0
	 *
	 * @return bool
	 */
	private function is_elementor_in_edit_mode() {

		return \Elementor\Plugin::$instance->editor->is_edit_mode();
	}


	/**
	 * Gets a list of available membership plans to populate the widget's memberships setting options.
	 *
	 * @since 1.20.0
	 *
	 * @param string $prepend prepended option ("none" or "all" or "both")
	 * @return string[]
	 */
	private function get_membership_plans_options( $prepend = '' ) {

		$prepend_option = [];
		$prepend_both   = 'both' === $prepend;

		if ( $prepend_both || 'none' === $prepend ) {
			// prepend non members option
			$prepend_option[ $this->visibility_condition_non_members_option_value ] = __( 'Non Members', 'woocommerce-memberships' );
		}

		if ( $prepend_both || 'all' === $prepend ) {
			// prepend all members option
			$prepend_option[ $this->visibility_condition_all_members_option_value ] = __( 'All Members', 'woocommerce-memberships' );
		}

		if ( empty( $this->available_plans_list ) ) {

			$membership_plans = wc_memberships_get_membership_plans();

			// build the options list
			foreach ( $membership_plans as $plan ) {
				$this->available_plans_list[ $plan->get_slug() ] = $plan->get_name();
			}
		}

		/**
		 * Filters list of membership plans options visible in Elementor Visibility Control section.
		 *
		 * @since 1.20.0
		 *
		 * @param string[] $plans_options list of plans options
		 */
		return (array) apply_filters( 'wc_memberships_elementor_widget_plans', array_merge( $prepend_option, $this->available_plans_list ) );
	}


	/**
	 * Gets a list of logged in user active memberships plans' slugs.
	 *
	 * @since 1.20.0
	 *
	 * @return array
	 */
	private function get_logged_in_user_active_memberships_plans() {

		if ( empty( $this->user_active_plans ) ) {

			$this->user_active_plans   = [];
			$current_logged_is_user_id = get_current_user_id();

			if ( empty( $current_logged_is_user_id ) ) {
				return [];
			}

			$user_active_memberships = wc_memberships_get_user_active_memberships( $current_logged_is_user_id );

			foreach ( $user_active_memberships as $membership ) {
				$this->user_active_plans[] = $membership->get_plan()->get_slug();
			}

		}

		return $this->user_active_plans;
	}


	/**
	 * Checks if the current logged in user has access to all restricted content.
	 *
	 * @since 1.20.0
	 *
	 * @return bool
	 */
	private function is_current_user_can_access_all_restricted_content() {

		return current_user_can( 'wc_memberships_access_all_restricted_content' );
	}


}
