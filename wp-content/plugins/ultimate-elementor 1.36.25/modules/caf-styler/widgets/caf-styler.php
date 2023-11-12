<?php
/**
 * UAEL Caldera Forms Styler Widget.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\CafStyler\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class CafStyler.
 */
class CafStyler extends Common_Widget {

	/**
	 * Retrieve Caldera Forms Styler Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'CafStyler' );
	}

	/**
	 * Retrieve Caldera Forms Styler Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'CafStyler' );
	}

	/**
	 * Retrieve Caldera Forms Styler Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'CafStyler' );
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
		return parent::get_widget_keywords( 'CafStyler' );
	}

	/**
	 * Retrieve the list of scripts the Caldera Forms Styler widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-caf-styler' );
	}

	/**
	 * Get Caldera Forms List
	 *
	 * @return array
	 */
	public function uael_select_caldera_forms() {
		$forms         = \Caldera_Forms_Forms::get_forms( true );
		$options['-1'] = 'Select';
		if ( ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				if ( is_array( $form ) && ! empty( $form['ID'] ) && ! empty( $form['name'] ) ) {
					$options[ $form['ID'] ] = $form['name'];
				}
			}
		}
		return $options;
	}

	/**
	 * Register Caldera Form Styler controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_general_content_controls();
		$this->register_input_content_controls();
		$this->register_radio_content_controls();
		$this->register_button_content_controls();
		$this->register_error_content_controls();
		$this->register_spacing_controls();
		$this->register_typography_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Caldera Form Styler General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {

		// Caldera Form - Section starts.
		$this->start_controls_section(
			'caf_section_general_caldera',
			array(
				'label' => esc_html__( 'General', 'uael' ),
			)
		);

		// Select Caldera Form.
		$this->add_control(
			'caf_select_caldera_form',
			array(
				'label'       => esc_html__( 'Select Form', 'uael' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->uael_select_caldera_forms(),
				'default'     => '-1',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Caldera Form Styler Fields Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_input_content_controls() {

		// Caldera Form - Section starts.
		$this->start_controls_section(
			'caf_section_caldera_form',
			array(
				'label' => esc_html__( 'Form Fields', 'uael' ),
			)
		);

		// Caldera Field Style.
		$this->add_control(
			'caf_field_style',
			array(
				'label'        => __( 'Field Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'box',
				'options'      => array(
					'box'       => __( 'Box', 'uael' ),
					'underline' => __( 'Underline', 'uael' ),
				),
				'prefix_class' => 'uael-caf-form',
			)
		);

		// Field Size.
		$this->add_control(
			'caf_field_size',
			array(
				'label'        => __( 'Field Size', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'sm',
				'options'      => array(
					'xs' => __( 'Extra Small', 'uael' ),
					'sm' => __( 'Small', 'uael' ),
					'md' => __( 'Medium', 'uael' ),
					'lg' => __( 'Large', 'uael' ),
					'xl' => __( 'Extra Large', 'uael' ),
				),
				'prefix_class' => 'uael-caf-input-size-',
			)
		);

		// Field Padding.
		$this->add_responsive_control(
			'caf_input_field_padding',
			array(
				'label'      => __( 'Field Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form textarea, {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};height: auto;',
					'{{WRAPPER}} form .ccselect2-container .ccselect2-choice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};font-size: calc({{TOP}}{{UNIT}} / 1.2);line-height: calc(40px / 1.2);height: auto;',
					'{{WRAPPER}} .uael-caf-form .caldera-grid form input[type=checkbox]:checked:after' => 'font-size: calc({{BOTTOM}}{{UNIT}} / 1.2);',
					'{{WRAPPER}} .uael-caf-form .caldera-grid form input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid form input[type=radio] + span:before' => 'height: {{TOP}}{{UNIT}}; width: {{TOP}}{{UNIT}};',
				),
			)
		);

		// Field Background.
		$this->add_control(
			'caf_input_field_bgcolor',
			array(
				'label'     => __( 'Field Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => ' #fafafa',
				'selectors' => array(
					'{{WRAPPER}} .caldera-grid .form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .trumbowyg-editor, {{WRAPPER}} .caldera-grid .form-control .ccselect2-choice, {{WRAPPER}} .uael-caf-form .caldera-grid form input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid form input[type=radio] + span:before, {{WRAPPER}} input[type=file]' => 'background: {{VALUE}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid hr' => 'border-color: {{VALUE}};',
				),
			)
		);

		// Label color.
		$this->add_control(
			'caf_all_label_color',
			array(
				'label'     => __( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-caf-form label,{{WRAPPER}} .uael-caf-styler span, {{WRAPPER}} .uael-caf-styler .file-type, {{WRAPPER}} .uael-caf-styler .file-size, {{WRAPPER}} .caldera-forms-summary-field, {{WRAPPER}}.uael-caf-error-highlight-yes .has-error label, {{WRAPPER}} .uael-form-editor-message' => 'color: {{VALUE}};',
				),
			)
		);

		// Input field color.
		$this->add_control(
			'caf_input_field_color',
			array(
				'label'     => __( 'Input Text / Placeholder Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .uael-caf-form .caldera-grid form input[type=checkbox]:checked:after, {{WRAPPER}} .uael-caf-form .ccselect2-chosen, {{WRAPPER}} form .trumbowyg-editor, {{WRAPPER}} .uael-caf-form form input::placeholder, {{WRAPPER}} .uael-caf-form form textarea::placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .uael-caf-form .uael-caf-select-custom:after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-caf-form .rangeslider__fill' => 'background: {{VALUE}} !important;',
					'{{WRAPPER}} .uael-caf-form .raty-cancel, {{WRAPPER}} .uael-caf-form .raty-star-on, {{WRAPPER}} .uael-caf-form .raty-heart-off, {{WRAPPER}} .uael-caf-form .raty-heart-on, {{WRAPPER}} .uael-caf-form .raty-face-off, {{WRAPPER}} .uael-caf-form .raty-face-on, {{WRAPPER}} .uael-caf-form .raty-dot-off, {{WRAPPER}} .uael-caf-form .raty-dot-on' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .cf-toggle-switch .btn-success.active, {{WRAPPER}} .cf-toggle-switch .btn-success:active, {{WRAPPER}} .cf-toggle-switch .btn-success:focus, {{WRAPPER}} .cf-toggle-switch .btn-success:hover, {{WRAPPER}} .cf-toggle-switch .open .dropdown-toggle.btn-success, {{WRAPPER}} .cf-toggle-switch .btn-success' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid input[type="radio"]:checked + span:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{caf_input_field_bgcolor.VALUE}};',
				),
			)
		);

		// Description field color.
		$this->add_control(
			'caf_all_description_color',
			array(
				'label'     => __( 'Field Description Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-caf-form .help-block, {{WRAPPER}}.uael-caf-error-highlight-yes .has-error .help-block' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caf_required_color',
			array(
				'label'     => __( 'Required Asterisk Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-caf-form .field_required' => 'color: {{VALUE}} !important;',
				),
			)
		);

		// Input border style.
		$this->add_control(
			'caf_input_border_style',
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
				'condition'   => array(
					'caf_field_style' => 'box',
				),
				'selectors'   => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} form input[type="checkbox"], {{WRAPPER}} form input[type="radio"] + span:before, {{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-style: {{VALUE}};',
				),
			)
		);

		// Input border Width.
		$this->add_responsive_control(
			'caf_input_border_size',
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
					'caf_field_style'         => 'box',
					'caf_input_border_style!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} .uael-caf-form .caldera-grid form input[type="checkbox"], {{WRAPPER}} .uael-caf-form .caldera-grid form input[type="radio"] + span:before, {{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid form input[type="checkbox"], {{WRAPPER}} .uael-caf-form .caldera-grid form input[type="radio"] + span:before' => 'box-sizing: content-box;',
				),
			)
		);

		// Input border color.
		$this->add_control(
			'caf_input_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'caf_field_style'         => 'box',
					'caf_input_border_style!' => 'none',
				),
				'default'   => '#eaeaea',
				'selectors' => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid .radio input[type=radio] + span:before,{{WRAPPER}} .uael-caf-form .caldera-grid .radio-inline input[type=radio] + span:before, {{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-color: {{VALUE}};',
				),
			)
		);

		// Border size.
		$this->add_responsive_control(
			'caf_border_bottom',
			array(
				'label'      => __( 'Border Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'default'    => array(
					'size' => '2',
					'unit' => 'px',
				),
				'condition'  => array(
					'caf_field_style' => 'underline',
				),
				'selectors'  => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-width: 0 0 {{SIZE}}{{UNIT}} 0; border-style: solid;',
					'{{WRAPPER}} .uael-caf-form .caldera-grid form input[type="checkbox"], {{WRAPPER}} .uael-caf-form .caldera-grid form input[type="radio"] + span:before' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid; box-sizing: content-box;',
				),
			)
		);

		// Border color.
		$this->add_control(
			'caf_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'caf_field_style' => 'underline',
				),
				'default'   => '#c4c4c4',
				'selectors' => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox], {{WRAPPER}} .uael-caf-form .caldera-grid .radio input[type=radio] + span:before,{{WRAPPER}} .uael-caf-form .caldera-grid .radio-inline input[type=radio] + span:before, {{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-color: {{VALUE}};',
				),
			)
		);

		// Border active color.
		$this->add_control(
			'caf_ipborder_active',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} form input[type="text"]:focus, {{WRAPPER}} form input[type="file"]:focus, {{WRAPPER}} form input[type="color_picker"]:focus, {{WRAPPER}} form input[type="credit_card_cvc"]:focus, {{WRAPPER}} form input[type="password"]:focus, {{WRAPPER}} form input[type="email"]:focus, {{WRAPPER}} form input[type="url"]:focus, {{WRAPPER}} form input[type="date"]:focus, {{WRAPPER}} form input[type="month"]:focus, {{WRAPPER}} form input[type="time"]:focus, {{WRAPPER}} form input[type="datetime"]:focus, {{WRAPPER}} form input[type="datetime-local"]:focus, {{WRAPPER}} form input[type="week"]:focus, {{WRAPPER}} form input[type="number"]:focus, {{WRAPPER}} form input[type="search"]:focus, {{WRAPPER}} form input[type="tel"]:focus, {{WRAPPER}} form input[type="color"]:focus, {{WRAPPER}} form select:focus, {{WRAPPER}} form textarea:focus, {{WRAPPER}} .trumbowyg-box:focus, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover:focus, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:focus, {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox input[type=checkbox]:checked, {{WRAPPER}} .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox]:checked, {{WRAPPER}} .uael-caf-form .caldera-grid .radio input[type=radio]:checked + span:before, {{WRAPPER}} .uael-caf-form .caldera-grid .radio-inline input[type=radio]:checked + span:before, {{WRAPPER}} form input[type="phone"]:focus' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'caf_input_border_style!' => 'none',
				),
			)
		);

		// Field rounded corners.
		$this->add_responsive_control(
			'caf_input_field_radius',
			array(
				'label'      => __( 'Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} input.form-control, {{WRAPPER}} form input[type="text"], {{WRAPPER}} form input[type="file"], {{WRAPPER}} form input[type="password"], {{WRAPPER}} form input[type="email"], {{WRAPPER}} form input[type="url"], {{WRAPPER}} form input[type="date"], {{WRAPPER}} form input[type="month"], {{WRAPPER}} form input[type="time"], {{WRAPPER}} form input[type="datetime"], {{WRAPPER}} form input[type="datetime-local"], {{WRAPPER}} form input[type="week"], {{WRAPPER}} form input[type="number"], {{WRAPPER}} form input[type="search"], {{WRAPPER}} form input[type="tel"], {{WRAPPER}} form input[type="color"], {{WRAPPER}} form select, {{WRAPPER}} form textarea, {{WRAPPER}} .trumbowyg-box, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control, {{WRAPPER}} input[type="checkbox"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .live-gravatar span:nth-of-type(1)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;overflow: hidden;',
				),
				'default'    => array(
					'top'    => '0',
					'bottom' => '0',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
				),
			)
		);

		$this->add_control(
			'caf_shadow_box',
			array(
				'label'        => __( 'Disable Field Shadow Effect', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-caf-shadow-',
			)
		);

		// Field Alignment.
		$this->add_responsive_control(
			'caf_text_align',
			array(
				'label'        => __( 'Field Alignment', 'uael' ),
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
				'render_type'  => 'template',
				'prefix_class' => 'uael%s-field-',
				'selectors'    => array(
					'{{WRAPPER}} .uael-caf-form label'  => 'text-align: {{VALUE}};width: 100%',
					'{{WRAPPER}} .uael-caf-form input:not([type=submit]), {{WRAPPER}} .uael-caf-form textarea' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-caf-form span'   => 'text-align:{{VALUE}};',
					'{{WRAPPER}} .uael-caf-form select' => 'text-align-last:{{VALUE}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid .file-prevent-overflow' => 'text-align:{{VALUE}};',
					'{{WRAPPER}} .uael-caf-form'        => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid .live-gravatar' => 'text-align: {{VALUE}} !important;',
				),
			)
		);

		// Caldera Form - Section ends.
		$this->end_controls_section();
	}

	/**
	 * Register CalderaForm Styler Radio Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_radio_content_controls() {

		// Radio/Checkbox style.
		$this->start_controls_section(
			'caf_radio_check_style',
			array(
				'label' => __( 'Radio & Checkbox', 'uael' ),
			)
		);

		// Override switch.
		$this->add_control(
			'caf_radio_check_custom',
			array(
				'label'        => __( 'Override Current Style', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		// Radio and checkbox - size.
		$this->add_control(
			'caf_radio_check_size',
			array(
				'label'      => _x( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'condition'  => array(
					'caf_radio_check_custom!' => '',
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 15,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-caf-form .caldera-grid .checkbox input[type=checkbox],
					{{WRAPPER}} .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox],
					{{WRAPPER}} .uael-caf-form .caldera-grid .radio input[type=radio] + span:before,
					{{WRAPPER}} .uael-caf-form .caldera-grid .radio-inline input[type=radio] + span:before' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-caf-form .caldera-grid form input[type=checkbox]:checked:after' => 'font-size: calc( {{SIZE}}{{UNIT}} / 1.2 );',
				),
			)
		);

		// Radio & Checkbox background color.
		$this->add_control(
			'caf_radio_check_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fafafa',
				'condition' => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid .radio input[type=radio] + span:before,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid .radio-inline input[type=radio] + span:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid .checkbox input[type=checkbox],
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox]' => 'background-color: {{VALUE}};',
				),
			)
		);

		// Radio and Checkbox -> Checked color.
		$this->add_control(
			'caf_selected_color',
			array(
				'label'     => __( 'Selected Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox input[type=checkbox]:after,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox-inline input[type=checkbox]:after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .radio input[type=radio]:checked + span:before,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .radio-inline input[type=radio]:checked + span:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{caf_radio_check_bgcolor.VALUE}};',
				),
			)
		);

		$this->add_control(
			'caf_checkbox_radio_text_color',
			array(
				'label'     => esc_html__( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .checkbox label,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .radio label,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .checkbox-inline label,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .radio-inline label' => 'color: {{VALUE}};',
				),
			)
		);

		// Radio and Checkbox -> Border color.
		$this->add_control(
			'caf_check_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox input[type=checkbox],
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .radio input[type=radio] + span:before,
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox-inline input[type=checkbox],
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .radio-inline input[type=radio] + span:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		// Chechbox and Radio -> Border width.
		$this->add_control(
			'caf_check_border_width',
			array(
				'label'      => __( 'Border Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default'    => array(
					'size' => '1',
					'unit' => 'px',
				),
				'condition'  => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-caf-form .caldera-grid .checkbox input[type=checkbox],
					{{WRAPPER}} .uael-caf-form .caldera-grid .radio input[type=radio] + span:before,
					{{WRAPPER}} .uael-caf-form .caldera-grid .checkbox-inline input[type=checkbox],
					{{WRAPPER}} .uael-caf-form .caldera-grid .radio-inline input[type=radio] + span:before' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		// Checkbox rounded corners.
		$this->add_control(
			'caf_check_border_radius',
			array(
				'label'      => __( 'Checkbox Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'caf_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox input[type=checkbox],
					{{WRAPPER}} .uael-caldera-form-wrapper .uael-caf-form .caldera-grid form .checkbox-inline input[type=checkbox]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => '0',
					'bottom' => '0',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
				),
			)
		);
		$this->end_controls_section();

	}

	/**
	 * Register CalderaForm Styler Button Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_button_content_controls() {

		// General - Style Tab Starts.
		$this->start_controls_section(
			'section_caldera_button_styles',
			array(
				'label' => esc_html__( 'Submit Button', 'uael' ),
			)
		);

		// Button alignment.
		$this->add_responsive_control(
			'button_align',
			array(
				'label'        => __( 'Button Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'    => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'uael' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'default'      => 'left',
				'prefix_class' => 'uael%s-caf-button-',
				'toggle'       => false,
			)
		);

		// Button Size.
		$this->add_control(
			'caf_btn_size',
			array(
				'label'        => __( 'Size', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'sm',
				'options'      => array(
					'xs' => __( 'Extra Small', 'uael' ),
					'sm' => __( 'Small', 'uael' ),
					'md' => __( 'Medium', 'uael' ),
					'lg' => __( 'Large', 'uael' ),
					'xl' => __( 'Extra Large', 'uael' ),
				),
				'prefix_class' => 'uael-caf-btn-size-',
			)
		);

		// Button Padding.
		$this->add_responsive_control(
			'caf_btn_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form input[type="button"], {{WRAPPER}} .uael-caf-form .cf-uploader-trigger, {{WRAPPER}} .uael-caf-form a.btn-default' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		// Tabs start here for button.
		$this->start_controls_tabs( 'caf_tabs_button_style' );

			// Default Tab.
			$this->start_controls_tab(
				'caf_tab_button_default',
				array(
					'label' => __( 'Normal', 'uael' ),
				)
			);
				// Default text color.
				$this->add_control(
					'caf_button_text_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#ffffff',
						'selectors' => array(
							'{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger' => 'color: {{VALUE}};',
						),
					)
				);

				// Default background color.
				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'caf_btn_background_color',
						'label'          => __( 'Background Color', 'uael' ),
						'types'          => array( 'classic', 'gradient' ),
						'fields_options' => array(
							'color' => array(
								'global' => array(
									'default' => Global_Colors::COLOR_ACCENT,
								),
							),
						),
						'selector'       => '{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger',
					)
				);

				// Default Border.
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'        => 'caf_btn_border',
						'label'       => __( 'Border', 'uael' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'selector'    => '{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger, {{WRAPPER}} .uael-caf-form .btn-success',
					)
				);

				// Default border radius.
				$this->add_responsive_control(
					'caf_btn_border_radius',
					array(
						'label'      => __( 'Border Radius', 'uael' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors'  => array(
							'{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .cf-toggle-switch .btn-group > .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'default'    => array(
							'top'    => '0',
							'bottom' => '0',
							'left'   => '0',
							'right'  => '0',
							'unit'   => 'px',
						),
					)
				);

				// Dafault Box Shadow.
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'caf_button_box_shadow',
						'selector' => '{{WRAPPER}} .uael-caf-form input[type="submit"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger',
					)
				);

			$this->end_controls_tab();

			// Botton Hover Tab.
			$this->start_controls_tab(
				'caf_tab_button_hover',
				array(
					'label' => __( 'Hover', 'uael' ),
				)
			);

				// Hover text color.
				$this->add_control(
					'caf_btn_hover_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-caf-form input[type="submit"]:hover, {{WRAPPER}} .uael-caf-form .btn-default:hover, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger:hover, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .btn-success:hover' => 'color: {{VALUE}};',
						),
					)
				);

				// Border Hover color.
				$this->add_control(
					'caf_button_border_color',
					array(
						'label'     => __( 'Border Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-caf-form input[type="submit"]:hover, {{WRAPPER}} .uael-caf-form .btn-default:hover, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger:hover, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .btn-success:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				// Button background hover color.
				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'caf_button_background_hover_color',
						'label'    => __( 'Background Color', 'uael' ),
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .uael-caf-form input[type="submit"]:hover, {{WRAPPER}} .uael-caf-form .btn-default:hover, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger:hover, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .btn-success:hover',
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register CalderaForm Styler Error Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_error_content_controls() {

		// Error/Success - Style Tab Starts.
		$this->start_controls_section(
			'section_caldera_error_success_styles',
			array(
				'label' => esc_html__( 'Success / Error Message', 'uael' ),
			)
		);

		$this->add_control(
			'field_error_heading',
			array(
				'label' => __( 'Error Field Validation', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

			$this->add_control(
				'caf_highlight_style',
				array(
					'label'        => __( 'Message Position', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'default',
					'options'      => array(
						'default'      => __( 'Default', 'uael' ),
						'bottom_right' => __( 'Bottom Right Side of Field', 'uael' ),
					),
					'prefix_class' => 'uael-caf-highlight-style-',
				)
			);

			// Validation Message color.
			$this->add_control(
				'caf_message_highlight_color',
				array(
					'label'     => __( 'Message Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'condition' => array(
						'caf_highlight_style' => 'bottom_right',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-caf-form .has-error .caldera_ajax_error_block span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'caf_message_bgcolor',
				array(
					'label'     => __( 'Message Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'rgba(255, 0, 0, 0.6)',
					'condition' => array(
						'caf_highlight_style' => 'bottom_right',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-caf-form .has-error .caldera_ajax_error_block span' => 'background-color: {{VALUE}}; padding: 0.1em 0.8em;',
					),
				)
			);

			$this->add_control(
				'caf_form_error_msg_color',
				array(
					'label'     => __( 'Message Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ff0000',
					'condition' => array(
						'caf_highlight_style!' => 'bottom_right',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-caf-form .has-error .caldera_ajax_error_block, {{WRAPPER}}.uael-caf-error-highlight-yes .has-error .caldera_ajax_error_block' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'caf_message_typo',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-caf-form .has-error .caldera_ajax_error_block',
				)
			);

			$this->add_control(
				'caf_error_default_layout',
				array(
					'label'        => __( 'Advanced settings', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'prefix_class' => 'uael-caf-error-highlight-',
				)
			);

			$this->add_control(
				'caf_error_field_label_color',
				array(
					'label'     => __( 'Label Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => array(
						'caf_error_default_layout' => 'yes',
					),
					'selectors' => array(
						'{{WRAPPER}}.uael-caf-error-highlight-yes .has-error label' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'caf_error_border_color',
				array(
					'label'     => __( 'Highlight Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ff0000',
					'condition' => array(
						'caf_error_default_layout' => 'yes',
					),
					'selectors' => array(
						'{{WRAPPER}} .has-error input.form-control, {{WRAPPER}} form .has-error input[type="text"], {{WRAPPER}} form .has-error input[type="password"], {{WRAPPER}} form .has-error input[type="email"], {{WRAPPER}} form .has-error input[type="url"], {{WRAPPER}} form .has-error input[type="date"], {{WRAPPER}} form .has-error input[type="month"], {{WRAPPER}} form .has-error input[type="time"], {{WRAPPER}} form .has-error input[type="datetime"], {{WRAPPER}} form .has-error input[type="datetime-local"], {{WRAPPER}} form .has-error input[type="week"], {{WRAPPER}} form .has-error input[type="number"], {{WRAPPER}} form .has-error input[type="search"], {{WRAPPER}} form .has-error input[type="tel"], {{WRAPPER}} form .has-error input[type="color"], {{WRAPPER}} form .has-error select, {{WRAPPER}} form .has-error textarea, {{WRAPPER}} .has-error .trumbowyg-box, {{WRAPPER}} .has-error .caldera-grid .ccselect2-container.form-control.parsley-error:hover, {{WRAPPER}} .caldera-grid .ccselect2-container.form-control.parsley-error, {{WRAPPER}} .caldera-grid .has-error.cf-toggle-switch .cf-toggle-group-buttons>a, {{WRAPPER}} .uael-caf-form .has-error .checkbox input[type=checkbox], {{WRAPPER}} .uael-caf-form .has-error .checkbox-inline input[type=checkbox], {{WRAPPER}} .uael-caf-form .has-error .radio input[type=radio] + span:before, {{WRAPPER}} .uael-caf-form .has-error .radio-inline input[type=radio] + span:before, {{WRAPPER}} .uael-caf-form .has-error input[type="file"]' => 'border-color: {{VALUE}};',
					),
				)
			);

		$this->add_control(
			'caf_success_message',
			array(
				'label'     => __( 'Form Success Validation', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'caf_success_message_color',
			array(
				'label'     => __( 'Success Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#008000',
				'selectors' => array(
					'{{WRAPPER}} .uael-caf-form .caldera-grid .alert-success'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caf_success_message_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-caf-form .caldera-grid .alert-success'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'caf_success_validation_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-caf-form .caldera-grid .alert-success',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/caldera-form-styler-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		$help_link_2 = UAEL_DOMAIN . 'docs/unable-to-see-caldera-form-styler-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s Doc Link */
					'raw'             => sprintf( __( '%1$s Unable to See Caldera Form Styler Widget? » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Register CalderaForm Styler Spacing Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_spacing_controls() {

		$this->start_controls_section(
			'form_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'form_fields_margin',
				array(
					'label'      => __( 'Between Two Fields', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-caf-form .form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_label_margin_bottom',
				array(
					'label'      => __( 'Label Bottom Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-caf-form label.control-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_input_margin_top',
				array(
					'label'      => __( 'Description Top Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-caf-form .help-block' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register CalderaForm Styler Typography Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_typography_controls() {

		$this->start_controls_section(
			'form_typo',
			array(
				'label' => __( 'Typography', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'form_input_typo',
				array(
					'label'     => __( 'Field Label', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'form_label_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-caf-form .form-group label.control-label',
				)
			);

			$this->add_control(
				'caf_input_typo',
				array(
					'label'     => __( 'Input Text / Placeholder', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'input_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-caf-form input:not([type=submit]):not([type=button]):not([type=image]), {{WRAPPER}} .uael-caf-form .form-group textarea, {{WRAPPER}} .uael-caf-form .form-group select,{{WRAPPER}} .uael-caf-form .form-group .ccselect2-choice, {{WRAPPER}} .uael-caf-form .checkbox label, {{WRAPPER}} .uael-caf-form .checkbox-inline label, {{WRAPPER}} .uael-caf-form .radio label,{{WRAPPER}} .uael-caf-form .radio-inline label, {{WRAPPER}} .uael-caf-form .uael-caf-select-custom',
				)
			);

			$this->add_control(
				'caf_desc_typo',
				array(
					'label'     => __( 'Field Description', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'input_desc_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-caf-form .help-block',
				)
			);

			$this->add_control(
				'btn_typography_label',
				array(
					'label'     => __( 'Button', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'btn_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} .uael-caf-form .form-group input[type="submit"], {{WRAPPER}} .uael-caf-form .form-group input[type="button"], {{WRAPPER}} .uael-caf-form .btn-default, {{WRAPPER}} .uael-caf-form .btn-success, {{WRAPPER}} .uael-caf-form .cf-uploader-trigger',
				)
			);

		$this->end_controls_section();
	}


	/**
	 * Render Editor Script. Which will show error at editor.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_editor_script() {

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() === false ) {
			return;
		}
	}

	/**
	 * Render Caldera Styler output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		$node_id  = $this->get_id();
		ob_start();
		include UAEL_MODULES_DIR . 'caf-styler/widgets/template.php';
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$this->render_editor_script();
	}
}
