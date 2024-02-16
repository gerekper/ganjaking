<?php
/**
 * UAEL Gravity Forms Styler.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\GfStyler\Widgets;

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
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Gf_Styler.
 */
class GfStyler extends Common_Widget {

	/**
	 * Retrieve GForms Styler Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'GfStyler' );
	}

	/**
	 * Retrieve GForms Styler Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'GfStyler' );
	}

	/**
	 * Retrieve GForms Styler Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'GfStyler' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'GfStyler' );
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script' );
	}

	/**
	 * Returns all gravity forms with ids
	 *
	 * @since 0.0.1
	 * @return array Key Value paired array.
	 */
	protected function get_gravity_forms() {

		$field_options = array();

		if ( class_exists( 'GFForms' ) ) {
			$forms              = \RGFormsModel::get_forms( null, 'title' );
			$field_options['0'] = 'Select';
			if ( is_array( $forms ) ) {
				foreach ( $forms as $form ) {
					$field_options[ $form->id ] = $form->title;
				}
			}
		}

		if ( empty( $field_options ) ) {
			$field_options = array(
				'-1' => __( 'You have not added any Gravity Forms yet.', 'uael' ),
			);
		}

		return $field_options;
	}

	/**
	 * Returns gravity forms id
	 *
	 * @since 0.0.1
	 * @return integer Key id for Gravity Form.
	 */
	protected function get_gravity_form_id() {
		if ( class_exists( 'GFForms' ) ) {
			$forms = \RGFormsModel::get_forms( null, 'title' );

			if ( is_array( $forms ) ) {
				foreach ( $forms as $form ) {
					return $form->id;
				}
			}
		}

		return -1;
	}

	/**
	 * Register GForms Styler controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_general_content_controls();
		$this->register_input_style_controls();
		$this->register_radio_content_controls();
		$this->register_section_field_controls();
		$this->register_button_content_controls();
		$this->register_error_style_controls();
		$this->register_spacing_controls();
		$this->register_typography_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register GForms Styler General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'General', 'uael' ),
			)
		);

		$this->add_control(
			'form_id',
			array(
				'label'   => __( 'Select Form', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_gravity_forms(),
				'default' => '0',

			)
		);

		$this->add_control(
			'form_ajax_option',
			array(
				'label'        => __( 'Enable AJAX Form Submission', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'default'      => 'true',
				'label_block'  => false,
				'prefix_class' => 'uael-gf-ajax-',
			)
		);

		$this->add_control(
			'mul_form_option',
			array(
				'label'        => __( 'Keyboard Tab Key Support', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'default'      => 'no',
				'label_block'  => false,
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'form_tab_index_option',
			array(
				'label'     => __( 'Set Tabindex Value', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'condition' => array(
					'mul_form_option' => 'yes',
				),
			)
		);
		if ( parent::is_internal_links() ) {

			$this->add_control(
				'help_doc_tabindex',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'You need to change above tabindex value if pressing tab on your keyboard not works as expected. Please read %1$s this article %2$s for more information.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/gravity-form-tab-index/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'mul_form_option' => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'form_title_option',
			array(
				'label'       => __( 'Title & Description', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'yes',
				'label_block' => false,
				'options'     => array(
					'yes'  => __( 'From Gravity Form', 'uael' ),
					'no'   => __( 'Enter Your Own', 'uael' ),
					'none' => __( 'None', 'uael' ),
				),
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'     => __( 'Form Title', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'form_title_option' => 'no',
				),
				'dynamic'   => array(
					'active' => true,
				),

			)
		);

		$this->add_control(
			'form_desc',
			array(
				'label'     => __( 'Form Description', 'uael' ),
				'type'      => Controls_Manager::TEXTAREA,
				'condition' => array(
					'form_title_option' => 'no',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_responsive_control(
			'form_title_desc_align',
			array(
				'label'     => __( 'Title & Description </br>Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
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
				'default'   => 'left',
				'condition' => array(
					'form_title_option!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-form-desc,
					{{WRAPPER}} .uael-gf-form-title,
					{{WRAPPER}} .uael-gf-style .gform_description,
					{{WRAPPER}} .uael-gf-style .gform_heading' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register GForms Styler Input Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_input_style_controls() {
		$this->start_controls_section(
			'form_input_style',
			array(
				'label' => __( 'Form Fields', 'uael' ),
			)
		);

		$this->add_control(
			'gf_style',
			array(
				'label'        => __( 'Field Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'box',
				'options'      => array(
					'box'       => __( 'Box', 'uael' ),
					'underline' => __( 'Underline', 'uael' ),
				),
				'prefix_class' => 'uael-gf-style-',
			)
		);

		$this->add_control(
			'form_input_size',
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
				'prefix_class' => 'uael-gf-input-size-',
			)
		);

		$this->add_responsive_control(
			'form_input_padding',
			array(
				'label'      => __( 'Field Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper form .gform_body input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]),
					{{WRAPPER}} .uael-gf-style .gform_wrapper textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-gf-style .ginput_container select,
					{{WRAPPER}} .uael-gf-style .ginput_container .chosen-single' => 'padding-top: calc( {{TOP}}{{UNIT}} - 2{{UNIT}} ); padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: calc( {{BOTTOM}}{{UNIT}} - 2{{UNIT}} ); padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-gf-check-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-check-style .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes)  .uael-gf-check-style .gfield_radio .gchoice_label label:before,
					{{WRAPPER}} .uael-gf-check-style .ginput_container_consent input[type="checkbox"] + label:before' => 'height: {{BOTTOM}}{{UNIT}}; width: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .uael-gf-check-style .gfield_checkbox input[type="checkbox"]:checked + label:before,
					{{WRAPPER}} .uael-gf-check-style .ginput_container_consent input[type="checkbox"]:checked + label:before'  => 'font-size: calc( {{BOTTOM}}{{UNIT}} / 1.2 );',
				),
			)
		);

		$this->add_control(
			'form_input_bgcolor',
			array(
				'label'     => __( 'Field Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fafafa',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=email],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=text],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=password],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=url],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=tel],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=number],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=date],
					{{WRAPPER}} .uael-gf-style .gform_wrapper select,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container-single .chosen-single,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container-multi .chosen-choices,
					{{WRAPPER}} .uael-gf-style .gform_wrapper textarea,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes)  .uael-gf-style .gfield_radio .gchoice_label label:before,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .gf_progressbar,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-gf-style .gsection' => 'border-bottom-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_label_color',
			array(
				'label'     => __( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield_label,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox li label,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent label,
					{{WRAPPER}} .uael-gf-style .gfield_radio li label,
					{{WRAPPER}} .uael-gf-style .gsection_title,
					{{WRAPPER}} .uael-gf-style .gfield_html,
					{{WRAPPER}} .uael-gf-style .ginput_product_price,
					{{WRAPPER}} .uael-gf-style .ginput_product_price_label,
					{{WRAPPER}} .uael-gf-style .gf_progressbar_title,
					{{WRAPPER}} .uael-gf-style .gf_page_steps,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox div label,
					{{WRAPPER}} .uael-gf-style .gfield_radio div label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_input_color',
			array(
				'label'     => __( 'Input Text', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]),
					{{WRAPPER}} .uael-gf-style .ginput_container select,
					{{WRAPPER}} .uael-gf-style .ginput_container .chosen-single,
					{{WRAPPER}} .uael-gf-style .ginput_container textarea,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield input::placeholder,
					{{WRAPPER}} .uael-gf-style .ginput_container textarea::placeholder,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"]:checked + label:before,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .uael-gf-select-custom:after ' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"]:checked + label:before,
					{{WRAPPER}} .uael-gf-style .gfield_radio .gchoice_button.uael-radio-active + .gchoice_label label:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{form_input_bgcolor.VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_input_placeholder_color',
			array(
				'label'     => __( 'Placeholder Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-uael-gf-styler .uael-gf-style .gform_wrapper .gfield input::placeholder,
					{{WRAPPER}}.elementor-widget-uael-gf-styler .uael-gf-style .ginput_container textarea::placeholder' => 'color: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->add_control(
			'form_input_desc_color',
			array(
				'label'     => __( 'Field Description Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield .gfield_description,
					{{WRAPPER}} .uael-gf-style .ginput_container .gfield_post_tags_hint,
					{{WRAPPER}} .uael-gf-style .ginput_container .gform_fileupload_rules,
					{{WRAPPER}} .uael-gf-style .ginput_container_name input + label,
					{{WRAPPER}} .uael-gf-style .ginput_container_creditcard input + span + label,
					{{WRAPPER}} .uael-gf-style .ginput_container input + label,
					{{WRAPPER}} .uael-gf-style .ginput_container select + label,
					{{WRAPPER}} .uael-gf-style .ginput_container .chosen-single + label,
					{{WRAPPER}} .uael-gf-style .gfield_time_hour label,
					{{WRAPPER}} .uael-gf-style .gfield_time_minute label,
					{{WRAPPER}} .uael-gf-style .ginput_container_address label,
					{{WRAPPER}} .uael-gf-style .ginput_container_total span,
					{{WRAPPER}} .uael-gf-style .ginput_shipping_price,
					{{WRAPPER}} .uael-gf-select-custom + label,
					{{WRAPPER}} .uael-gf-style .gsection_description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_required_color',
			array(
				'label'     => __( 'Required Asterisk Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield_required' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_border_style',
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
					'gf_style' => 'box',
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=email],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=text],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=password],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=url],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=tel],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=number],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=date],
					{{WRAPPER}} .uael-gf-style .gform_wrapper select,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-single,
					{{WRAPPER}} .uael-gf-style .gform_wrapper textarea,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes)  .uael-gf-style .gfield_radio .gchoice_label label:before' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_border_size',
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
					'gf_style'            => 'box',
					'input_border_style!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=email],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=text],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=password],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=url],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=tel],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=number],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=date],
					{{WRAPPER}} .uael-gf-style .gform_wrapper select,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-single,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-choices,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container .chosen-drop,
					{{WRAPPER}} .uael-gf-style .gform_wrapper textarea,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					.gchoice_label label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes)  .uael-gf-style .gfield_radio .gchoice_label label:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'gf_style'            => 'box',
					'input_border_style!' => 'none',
				),
				'default'   => '#eaeaea',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=email],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=text],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=password],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=url],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=tel],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=number],
						{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=date],
						{{WRAPPER}} .uael-gf-style .gform_wrapper select,
						{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-single,
						{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-choices,
						{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container .chosen-drop,
						{{WRAPPER}} .uael-gf-style .gform_wrapper textarea,
						{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
						{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
						{{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"] + label:before,
						{{WRAPPER}}:not(.uael-gf-check-default-yes)  .uael-gf-style .gfield_radio .gchoice_label label:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gf_border_bottom',
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
					'gf_style' => 'underline',
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=email],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=text],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=password],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=url],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=tel],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=number],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=date],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper select,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-single,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-choices,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper textarea' => 'border-width: 0 0 {{SIZE}}{{UNIT}} 0; border-style: solid;',
					'{{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-container .chosen-drop' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
					'{{WRAPPER}}.uael-gf-style-underline .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-style-underline .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-style-underline .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-style-underline .gfield_radio .gchoice_label label:before' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid; box-sizing: content-box;',
				),
			)
		);

		$this->add_control(
			'gf_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'gf_style' => 'underline',
				),
				'default'   => '#c4c4c4',
				'selectors' => array(
					'{{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=email],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=text],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=password],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=url],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=tel],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=number],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper input[type=date],
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper select,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-single,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-choices,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper .chosen-container .chosen-drop,
					 {{WRAPPER}}.uael-gf-style-underline .gform_wrapper textarea,
					 {{WRAPPER}}.uael-gf-style-underline .gfield_checkbox input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-style-underline .ginput_container_consent input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-style-underline .gfield_radio input[type="radio"] + label:before,
					 {{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-style-underline .gfield_radio .gchoice_label label:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gf_border_active_color',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'gf_style'            => 'box',
					'input_border_style!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield input:focus,
					 {{WRAPPER}} .uael-gf-style .gfield textarea:focus,
					 {{WRAPPER}} .uael-gf-style .gfield select:focus,
					 {{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container-active.chosen-with-drop .chosen-single,
					 {{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container-active.chosen-container-multi .chosen-choices,
					 {{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"]:checked + label:before,
					 {{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"]:checked + label:before,
					 {{WRAPPER}} .uael-gf-style .gfield_radio input[type="radio"]:checked + label:before,
					 {{WRAPPER}} .uael-gf-style .gfield_radio .gchoice_button.uael-radio-active + .gchoice_label label:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gf_border_active_color_underline',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'gf_style' => 'underline',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield input:focus,
					 {{WRAPPER}} .uael-gf-style .gfield textarea:focus,
					 {{WRAPPER}} .uael-gf-style .gfield select:focus,
					 {{WRAPPER}} .uael-gf-style .gfield .chosen-single:focus,
					 {{WRAPPER}}.uael-gf-style-underline .gfield_checkbox input[type="checkbox"]:checked + label:before,
					 {{WRAPPER}}.uael-gf-style-underline .ginput_container_consent input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-style-underline .gfield_radio input[type="radio"]:checked + label:before,
					 {{WRAPPER}}.uael-gf-style-underline .gfield_radio .gchoice_button.uael-radio-active + .gchoice_label label:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_border_radius',
			array(
				'label'      => __( 'Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'    => '0',
					'bottom' => '0',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=email],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=text],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=password],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=url],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=tel],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=number],
					{{WRAPPER}} .uael-gf-style .gform_wrapper input[type=date],
					{{WRAPPER}} .uael-gf-style .gform_wrapper select,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-single,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-choices,
					{{WRAPPER}} .uael-gf-style .gform_wrapper .chosen-container .chosen-drop,
					{{WRAPPER}} .uael-gf-style .gform_wrapper textarea,
					{{WRAPPER}} .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}} .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'enable_gforms_css_classes',
			array(
				'label'        => __( 'Support to CSS Ready Classes', 'uael' ),
				/* translators: %1$s doc link, %2$s link close */
				'description'  => sprintf( __( 'Enable this option to add support to Gravity Forms CSS Ready Classes. %1$s Learn More. %2$s', 'uael' ), '<a href="https://www.gravityforms.com/css-ready-classes/" target="_blank" rel="noopener">', '</a>' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-gf-enable-classes-',
			)
		);

		$this->end_controls_section();
	}


	/**
	 * Register GForms Styler Radio & Checkbox Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_radio_content_controls() {
		$this->start_controls_section(
			'gf_radio_check_style',
			array(
				'label' => __( 'Radio & Checkbox', 'uael' ),
			)
		);

		$this->add_control(
			'gf_radio_check_custom',
			array(
				'label'        => __( 'Override Current Style', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'uael-gf-check-',
			)
		);

		$this->add_control(
			'gf_radio_check_default',
			array(
				'label'        => __( 'Default Checkboxes/Radio Buttons', 'uael' ),
				'description'  => __( 'This option lets you use browser default checkboxes and radio buttons. Enable this if you face any issues with custom checkboxes and radio buttons.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-gf-check-default-',
				'condition'    => array(
					'gf_radio_check_custom!' => '',
				),
			)
		);

		$this->add_control(
			'gf_radio_check_size',
			array(
				'label'      => _x( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'condition'  => array(
					'gf_radio_check_custom!' => '',
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
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-check-style .gfield_checkbox input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-check-yes .uael-gf-check-style .gfield_radio input[type="radio"] + label:before,
					 {{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-check-yes .uael-gf-check-style .gfield_radio .gchoice_label label:before,
					 {{WRAPPER}}.uael-gf-check-yes .uael-gf-check-style .ginput_container_consent input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-check-style .gfield_checkbox input[type="checkbox"],
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-check-style .gfield_radio input[type="radio"],
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-check-style .ginput_container_consent input[type="checkbox"]' => 'width: {{SIZE}}{{UNIT}}!important; height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-check-style .gfield_checkbox input[type="checkbox"]:checked + label:before,
					 {{WRAPPER}}.uael-gf-check-yes .uael-gf-check-style .ginput_container_consent input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-check-style .gfield_checkbox input[type="checkbox"]:checked,
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-check-style .ginput_container_consent input[type="checkbox"]'  => 'font-size: calc( {{SIZE}}{{UNIT}} / 1.2 );',
				),
			)
		);

		$this->add_control(
			'gf_radio_check_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					 {{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-check-yes .uael-gf-style .gfield_radio .gchoice_label label:before,
					 {{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"],
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"],
					 {{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]' => 'background-color: {{VALUE}};',
				),
				'default'   => '#fafafa',
			)
		);

		$this->add_control(
			'gf_selected_color',
			array(
				'label'     => __( 'Selected Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"]:checked + label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"]:checked:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]:checked + label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]:checked:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"]:checked + label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_radio .gchoice_button.uael-radio-active + .gchoice_label label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"]:checked:before'    => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{gf_radio_check_bgcolor.VALUE}};',
				),
			)
		);

		$this->add_control(
			'gf_select_color',
			array(
				'label'     => __( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox div label,
					{{WRAPPER}} .uael-gf-style .gfield_radio div label,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent label,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox li label,
					{{WRAPPER}} .uael-gf-style .gfield_radio li label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gf_check_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eaeaea',
				'condition' => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-check-yes .uael-gf-style .gfield_radio .gchoice_label label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"],
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"],
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'gf_check_border_width',
			array(
				'label'      => __( 'Border Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'size' => '1',
					'unit' => 'px',
				),
				'condition'  => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"] + label:before,
					{{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-check-yes .uael-gf-style .gfield_radio .gfield_radio .gchoice_label label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"],
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_radio input[type="radio"],
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->add_control(
			'gf_check_border_radius',
			array(
				'label'      => __( 'Checkbox Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'gf_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"] + label:before,
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .gfield_checkbox input[type="checkbox"],
					{{WRAPPER}}.uael-gf-check-default-yes.uael-gf-check-yes .uael-gf-style .ginput_container_consent input[type="checkbox"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	 * Register GForms Styler Section Fields Controls.
	 *
	 * @since 1.32.0
	 * @access protected
	 */
	protected function register_section_field_controls() {
		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Section Field', 'uael' ),
			)
		);

		$this->add_control(
			'section_field_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield.gsection .gsection_title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'section_field_typography',
				'label'     => __( 'Typography', 'uael' ),
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector'  => '{{WRAPPER}} .uael-gf-style .gfield.gsection .gsection_title',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'section_field_border_type',
			array(
				'label'     => __( 'Border Type', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield.gsection' => 'border-bottom-style: {{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'section_field_border_height',
			array(
				'label'      => __( 'Border Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 1,
				),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gfield.gsection' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'section_field_border_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'section_field_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield.gsection' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'section_field_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'section_field_bottom_spacing',
			array(
				'label'     => __( 'Bottom Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gfield.gsection' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register GForms Styler Button Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_button_content_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Submit Button', 'uael' ),
			)
		);
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
				'prefix_class' => 'uael%s-gf-button-',
				'toggle'       => false,
			)
		);
		$this->add_control(
			'btn_size',
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
				'prefix_class' => 'uael-gf-btn-size-',
			)
		);

		$this->add_responsive_control(
			'gf_button_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *))' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-gf-style input[type="submit"],
            		{{WRAPPER}} .uael-gf-style input[type="button"],
            		{{WRAPPER}} .uael-gf-style .gf_progressbar_percentage,
            		{{WRAPPER}} .uael-gf-style .gform_wrapper .percentbar_blue' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *))' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-gf-style input[type="submit"],
					{{WRAPPER}} .uael-gf-style input[type="button"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'btn_background_color',
				'label'          => __( 'Background Color', 'uael' ),
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
					),
				),
				'selector'       => '{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
				{{WRAPPER}} [type="button"],
				{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
				{{WRAPPER}} [type="button"],
				{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .uael-gf-style input[type="submit"],
				{{WRAPPER}} .uael-gf-style input[type="button"],
				{{WRAPPER}} .uael-gf-style .gf_progressbar_percentage,
				{{WRAPPER}} .uael-gf-style .gform_wrapper .percentbar_blue',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'btn_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .uael-gf-style input[type="submit"],
            		{{WRAPPER}} .uael-gf-style input[type="button"]',
			)
		);

		$this->add_responsive_control(
			'btn_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style input[type="submit"],
					{{WRAPPER}} .uael-gf-style input[type="button"],
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
					{{WRAPPER}} [type="button"],
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *))' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .uael-gf-style input[type="submit"],
				{{WRAPPER}} .uael-gf-style input[type="button"],
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
				{{WRAPPER}} [type="button"],
				{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"],
				{{WRAPPER}} [type="button"],
				{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)),
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *))',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'btn_hover_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-gf-style input[type="submit"]:hover, {{WRAPPER}} .uael-gf-style input[type="button"]:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gf_button_hover_border_color',
			array(
				'label'     => __( 'Border Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-gf-style input[type="submit"]:hover, {{WRAPPER}} .uael-gf-style input[type="button"]:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover_color',
				'label'    => __( 'Background Color', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
				{{WRAPPER}} [type="button"], 
				{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
				{{WRAPPER}} [type="button"], 
				{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
				{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover,
 				{{WRAPPER}} .uael-gf-style input[type="submit"]:hover, {{WRAPPER}} .uael-gf-style input[type="button"]:hover',
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .gform-theme-button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper .button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper :where(:not(.mce-splitbtn)) > button:not([id*="mceu_"]):not(.mce-open):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper button.button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]):where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input:is([type="submit"], 
					{{WRAPPER}} [type="button"], 
					{{WRAPPER}} [type="reset"]).button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover, 
					{{WRAPPER}} .gform-theme.gform-theme--framework.gform_wrapper input[type="submit"].button.gform_button:where(:not(.gform-theme-no-framework):not(.gform-theme__disable):not(.gform-theme__disable *):not(.gform-theme__disable-framework):not(.gform-theme__disable-framework *)):hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-gf-style input[type="submit"]:hover, {{WRAPPER}} .uael-gf-style input[type="button"]:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register GForms Styler Error Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_error_style_controls() {
		$this->start_controls_section(
			'form_error_field',
			array(
				'label' => __( 'Success / Error Message', 'uael' ),
			)
		);
		$this->add_control(
			'form_error',
			array(
				'label' => __( 'Field Validation', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'form_error_msg_color',
			array(
				'label'     => __( 'Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff0000',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield_description.validation_message' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'gf_message_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gform_wrapper .validation_message',
			)
		);
		$this->add_responsive_control(
			'field_validation_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .gfield_description.validation_message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_error_field_background',
			array(
				'label'        => __( 'Advanced Settings', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'uael-gf-error-',
			)
		);

			$this->add_control(
				'form_error_field_bgcolor',
				array(
					'label'     => __( 'Field Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'condition' => array(
						'form_error_field_background!' => '',
					),
					'selectors' => array(
						'{{WRAPPER}}.uael-gf-error-yes .gform_wrapper .gfield.gfield_error' => 'background-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'form_error_border_color',
				array(
					'label'     => __( 'Highlight Border Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ff0000',
					'condition' => array(
						'form_error_field_background!' => '',
					),
					'selectors' => array(
						'{{WRAPPER}}.uael-gf-error-yes .gform_wrapper li.gfield_error input:not([type="submit"]):not([type="button"]):not([type="image"]),
						 {{WRAPPER}}.uael-gf-error-yes .gform_wrapper .gfield_error .ginput_container select,
						 {{WRAPPER}}.uael-gf-error-yes .gform_wrapper .gfield_error .ginput_container .chosen-single,
						 {{WRAPPER}}.uael-gf-error-yes .gform_wrapper .gfield_error .ginput_container textarea,
						 {{WRAPPER}}.uael-gf-error-yes .gform_wrapper li.gfield.gfield_error,
						 {{WRAPPER}}.uael-gf-error-yes .gform_wrapper li.gfield.gfield_error.gfield_contains_required.gfield_creditcard_warning,
						 {{WRAPPER}}.uael-gf-error-yes li.gfield_error .gfield_checkbox input[type="checkbox"] + label:before,
						 {{WRAPPER}}.uael-gf-error-yes li.gfield_error .ginput_container_consent input[type="checkbox"] + label:before,
						 {{WRAPPER}}.uael-gf-error-yes li.gfield_error .gfield_radio input[type="radio"] + label:before,
						 {{WRAPPER}}:not(.uael-gf-check-default-yes).uael-gf-error-yes li.gfield_error .gfield_radio .gchoice_label label:before' => 'border-color: {{VALUE}};',
						'{{WRAPPER}}.uael-gf-error-yes .gform_wrapper li.gfield_error input[type="text"]' =>
						'border: {{input_border_size.BOTTOM}}px {{input_border_style.VALUE}} {{VALUE}} !important;',
						'{{WRAPPER}}.uael-gf-style-underline.uael-gf-error-yes .gform_wrapper li.gfield_error input[type="text"]' =>
						'border-width: 0 0 {{gf_border_bottom.SIZE}}px 0 !important; border-style: solid; border-color:{{VALUE}};',
					),
				)
			);

		$this->add_control(
			'form_validation_message',
			array(
				'label'     => __( 'Form Error Validation', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_valid_message_color',
			array(
				'label'     => __( 'Error Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cccccc',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors h2' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'form_valid_bgcolor',
			array(
				'label'     => __( 'Error Message Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_valid_border_color',
			array(
				'label'     => __( 'Error Message Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff0000',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'form_border_size',
			array(
				'label'      => __( 'Border Size', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '2',
					'bottom' => '2',
					'left'   => '2',
					'right'  => '2',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors' => 'border-top: {{TOP}}{{UNIT}}; border-right: {{RIGHT}}{{UNIT}}; border-bottom: {{BOTTOM}}{{UNIT}}; border-left: {{LEFT}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->add_control(
			'form_valid_border_radius',
			array(
				'label'      => __( 'Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_valid_message_padding',
			array(
				'label'      => __( 'Message Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '10',
					'bottom' => '10',
					'left'   => '10',
					'right'  => '10',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
					{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cf7_error_validation_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gform_wrapper div.validation_error,
				{{WRAPPER}} .uael-gf-style .gform_wrapper div.gform_validation_errors',
			)
		);

		$this->add_control(
			'form_success_message',
			array(
				'label'     => __( 'Form Success Validation', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_success_message_color',
			array(
				'label'     => __( 'Success Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#008000',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-style .gform_confirmation_message'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cf7_success_validation_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gform_confirmation_message',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register GForms Styler Spacing Controls.
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
				'form_title_margin_bottom',
				array(
					'label'      => __( 'Form Title Bottom Margin', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'condition'  => array(
						'form_title_option!' => 'none',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-gf-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_desc_margin_bottom',
				array(
					'label'      => __( 'Form Description Bottom Margin', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-gf-form-desc, {{WRAPPER}} .uael-gf-style .gform_heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'form_title_option!' => 'none',
					),
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
						'{{WRAPPER}} .uael-gf-style .gform_wrapper li.gfield,
						{{WRAPPER}} .uael-gf-style .gform_wrapper div.gfield,
						{{WRAPPER}} .uael-gf-style .gform_wrapper .gf_progressbar_wrapper,
						{{WRAPPER}} .uael-gf-style .gform_wrapper fieldset.gfield' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .uael-gf-style .gfield_label, {{WRAPPER}} .uael-gf-style .gsection_title, {{WRAPPER}} .uael-gf-style .gf_progressbar_title,{{WRAPPER}} .uael-gf-style .gf_page_steps' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					),
				)
			);

			$this->add_responsive_control(
				'form_input_margin_top',
				array(
					'label'      => __( 'Input Top Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-gf-style .ginput_container' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_input_margin_bottom',
				array(
					'label'      => __( 'Input Bottom Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-gf-style .ginput_container input' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register GForms Styler Typography Controls.
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
			'form_title_typo',
			array(
				'label'     => __( 'Form Title', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'form_title_option!' => 'none',
				),
			)
		);
		$this->add_control(
			'form_title_tag',
			array(
				'label'     => __( 'HTML Tag', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'  => __( 'H1', 'uael' ),
					'h2'  => __( 'H2', 'uael' ),
					'h3'  => __( 'H3', 'uael' ),
					'h4'  => __( 'H4', 'uael' ),
					'h5'  => __( 'H5', 'uael' ),
					'h6'  => __( 'H6', 'uael' ),
					'div' => __( 'div', 'uael' ),
					'p'   => __( 'p', 'uael' ),
				),
				'condition' => array(
					'form_title_option!' => 'none',
				),
				'default'   => 'h3',
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .uael-gf-form-title',
				'condition' => array(
					'form_title_option!' => 'none',
				),

			)
		);
		$this->add_control(
			'form_title_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'form_title_option!' => 'none',
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-form-title' => 'color: {{VALUE}};',
				),

			)
		);

		$this->add_control(
			'form_desc_typo',
			array(
				'label'     => __( 'Form Description', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'form_title_option!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector'  => '{{WRAPPER}} .uael-gf-form-desc, {{WRAPPER}} .uael-gf-style .gform_description',
				'condition' => array(
					'form_title_option!' => 'none',
				),
			)
		);
		$this->add_control(
			'form_desc_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'form_title_option!' => 'none',
				),
				'default'   => '',
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .uael-gf-form-desc, {{WRAPPER}} .uael-gf-style .gform_description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_input_typo',
			array(
				'label' => __( 'Form Fields', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_label_typography',
				'label'    => 'Label Typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gfield_label,
				{{WRAPPER}} .uael-gf-style .gfield_checkbox li label,
				{{WRAPPER}} .uael-gf-style .gfield_radio li label,
				{{WRAPPER}} .uael-gf-style .gsection_title,
				{{WRAPPER}} .uael-gf-style .ginput_product_price,
				{{WRAPPER}} .uael-gf-style .ginput_product_price_label,
				{{WRAPPER}} .uael-gf-style .gf_progressbar_title,
				{{WRAPPER}} .uael-gf-style .ginput_container_consent label,
				{{WRAPPER}} .uael-gf-style .gf_page_steps,
				{{WRAPPER}} .uael-gf-style .gfield_checkbox div label,
				{{WRAPPER}} .uael-gf-style .gfield_radio div label',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',
				'label'    => 'Text Typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]),
				 {{WRAPPER}} .uael-gf-style .ginput_container select,
				 {{WRAPPER}} .uael-gf-style .ginput_container .chosen-single,
				 {{WRAPPER}} .uael-gf-style .ginput_container textarea,
				 {{WRAPPER}} .uael-gf-style .uael-gf-select-custom',
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_desc_typography',
				'label'    => 'Description Typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style .gform_wrapper .gfield .gfield_description,
				{{WRAPPER}} .uael-gf-style .ginput_container .gfield_post_tags_hint,
				{{WRAPPER}} .uael-gf-style .ginput_container .gform_fileupload_rules,
				{{WRAPPER}} .uael-gf-style .ginput_container_name input + label,
				{{WRAPPER}} .uael-gf-style .ginput_container_creditcard input + span + label,
				{{WRAPPER}} .uael-gf-style .ginput_container input + label,
				{{WRAPPER}} .uael-gf-style .ginput_container select + label,
				{{WRAPPER}} .uael-gf-style .ginput_container .chosen-single + label,
				{{WRAPPER}} .uael-gf-style .gfield_time_hour label,
				{{WRAPPER}} .uael-gf-style .gfield_time_minute label,
				{{WRAPPER}} .uael-gf-style .ginput_container_address label,
				{{WRAPPER}} .uael-gf-style .ginput_container_total span,
				{{WRAPPER}} .uael-gf-style .ginput_shipping_price,
				{{WRAPPER}} .uael-gf-select-custom + label,
				{{WRAPPER}} .uael-gf-style .gsection_description',
			)
		);

		$this->add_control(
			'btn_typography_label',
			array(
				'label'     => __( 'Button Typography', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .uael-gf-style input[type=submit], {{WRAPPER}} .uael-gf-style input[type="button"], 
				{{WRAPPER}} .uael-gf-style .gform-theme.gform-theme--framework.gform_wrapper input[type=submit], 
				{{WRAPPER}} .uael-gf-style .gform_wrapper input[type="button"]',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
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
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=OCD3oZas60w&index=4&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * GForms Styler refresh button.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Render GForms Styler output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();
		ob_start();
		include UAEL_MODULES_DIR . 'gf-styler/widgets/template.php';
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

