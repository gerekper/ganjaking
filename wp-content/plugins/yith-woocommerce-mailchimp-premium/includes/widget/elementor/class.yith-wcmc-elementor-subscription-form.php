<?php
/**
 * Subscription Form widget for Elementor
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.3.8
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Elementor_Subscription_Form' ) ) {
	class YITH_WCMC_Elementor_Subscription_Form extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve YITH_WCMC_Elementor_Subscription_Form widget name.
		 *
		 * @return string Widget name.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_name() {
			return 'yith_wcmc_subscription_form';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve YITH_WCMC_Elementor_Subscription_Form widget title.
		 *
		 * @return string Widget title.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_title() {
			return _x( 'YITH MailChimp Subscrption Form', 'Elementor widget name', 'yith-woocommerce-mailchimp' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve YITH_WCMC_Elementor_Subscription_Form widget icon.
		 *
		 * @return string Widget icon.
		 * @since  1.0.0
		 * @access public
		 */
		public function get_icon() {
			return 'eicon-form-horizontal';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the YITH_WCMC_Elementor_Subscription_Form widget belongs to.
		 *
		 * @return array Widget categories.
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function get_categories() {
			return [ 'general', 'yith' ];
		}

		/**
		 * Register YITH_WCMC_Elementor_Subscription_Form widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function _register_controls() {

			$selected_fields = get_option( 'yith_wcmc_shortcode_custom_fields' );
			$textual_fields  = '';

			if ( ! empty( $selected_fields ) ) {
				$first = true;
				foreach ( $selected_fields as $field ) {
					if ( ! isset( $field['merge_var'] ) ) {
						continue;
					}

					if ( ! $first ) {
						$textual_fields .= '|';
					}

					$textual_fields .= $field['name'] . ',' . $field['merge_var'];

					$first = false;
				}
			}

			$this->start_controls_section(
				'general_section',
				[
					'label' => _x( 'General', 'Elementor section title', 'yith-woocommerce-mailchimp' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'title',
				[
					'label'       => _x( 'Title', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'default'     => get_option( 'yith_wcmc_shortcode_title' ),
					'placeholder' => '',
				]
			);

			$this->add_control(
				'submit_label',
				[
					'label'       => _x( '"Submit" button label', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'default'     => get_option( 'yith_wcmc_shortcode_submit_button_label' ),
					'placeholder' => '',
				]
			);

			$this->add_control(
				'success_message',
				[
					'label'       => _x( '"Successfully Registered" message', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'default'     => get_option( 'yith_wcmc_shortcode_success_message' ),
					'placeholder' => '',
				]
			);

			$this->add_control(
				'show_privacy_field',
				[
					'label'   => _x( 'Show privacy checkbox', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Show privacy checkbox', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Hide privacy checkbox', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_show_privacy_field' ),
				]
			);

			$this->add_control(
				'privacy_label',
				[
					'label'       => _x( 'Privacy field label', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'input_type'  => 'text',
					'default'     => get_option( 'yith_wcmc_shortcode_privacy_label' ),
					'placeholder' => '',
				]
			);

			$this->add_control(
				'hide_form_after_registration',
				[
					'label'   => _x( 'Hide form after registration', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Hide form after registration', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Do not hide form after registration', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_hide_after_registration' ),
				]
			);

			$this->add_control(
				'email_type',
				[
					'label'   => _x( 'Email type', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
						'text' => __( 'Text', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_email_type' ),
				]
			);

			$this->add_control(
				'double_optin',
				[
					'label'   => _x( 'Double Opt-in', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Enable double opt-in', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Disable double opt-in', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_double_optin' ),
				]
			);

			$this->add_control(
				'update_existing',
				[
					'label'   => _x( 'Update existing', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Do not update existing', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_update_existing' ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'mailchimp_settings_section',
				[
					'label' => _x( 'Mailchimp Settings', 'Elementor section title', 'yith-woocommerce-mailchimp' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'list',
				[
					'label'   => _x( 'MailChimp list', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => get_option( 'yith_wcmc_shortcode_mailchimp_list' ),
				]
			);

			$this->add_control(
				'groups',
				[
					'label'   => _x( 'Auto-subscribe interest groups (list of GROUP_ID-INTEREST_ID separated by special token #%,%#)', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => implode( '#%,%#', get_option( 'yith_wcmc_shortcode_mailchimp_groups', array() ) ),
				]
			);

			$this->add_control(
				'groups_to_prompt',
				[
					'label'   => _x( 'Show the following interest groups (list of GROUP_ID-INTEREST_ID separated by special token #%,%#)', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => implode( '#%,%#', get_option( 'yith_wcmc_shortcode_mailchimp_groups_selectable', array() ) ),
				]
			);

			$this->add_control(
				'fields',
				[
					'label'   => _x( 'Fields (list of LABEL,MERGE_VAR separated by special token |)', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'default' => $textual_fields,
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				[
					'label' => _x( 'Style', 'Elementor section title', 'yith-woocommerce-mailchimp' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'enable_style',
				[
					'label'   => _x( 'Enable custom CSS', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Enable custom style', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Do not enable custom style', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_style_enable' ),
				]
			);

			$this->add_control(
				'round_corners',
				[
					'label'   => _x( 'Round Corners for "Subscribe" Button', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'yes' => __( 'Round Corners', 'yith-woocommerce-mailchimp' ),
						'no'  => __( 'Do not round corners', 'yith-woocommerce-mailchimp' ),
					],
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_round_corners', 'no' ),
				]
			);

			$this->add_control(
				'background_color',
				[
					'label'   => _x( 'Button background color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_background_color' ),
				]
			);

			$this->add_control(
				'text_color',
				[
					'label'   => _x( 'Button text color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_color' ),
				]
			);

			$this->add_control(
				'border_color',
				[
					'label'   => _x( 'Button border color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_border_color' ),
				]
			);

			$this->add_control(
				'background_hover_color',
				[
					'label'   => _x( 'Button background hover color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_background_hover_color' ),
				]
			);

			$this->add_control(
				'text_hover_color',
				[
					'label'   => _x( 'Button text hover color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_hover_color' ),
				]
			);

			$this->add_control(
				'border_hover_color',
				[
					'label'   => _x( 'Button border hover color', 'Elementor control label', 'yith-woocommerce-mailchimp' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_border_hover_color' ),
				]
			);

			$this->end_controls_section();

		}

		/**
		 * Render YITH_WCMC_Elementor_Subscription_Form widget output on the frontend.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		protected function render() {

			$attribute_string = '';
			$settings         = $this->get_settings_for_display();

			foreach ( $settings as $key => $value ) {
				if ( empty( $value ) || ! is_scalar( $value ) ) {
					continue;
				}
				$attribute_string .= " {$key}=\"{$value}\"";
			}

			echo do_shortcode( "[yith_wcmc_subscription_form {$attribute_string}]" );
		}

	}
}