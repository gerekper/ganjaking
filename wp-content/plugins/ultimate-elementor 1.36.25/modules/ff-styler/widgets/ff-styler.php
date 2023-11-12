<?php
/**
 * UAEL WP Fluent Forms Styler.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\FfStyler\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class FF Styler.
 */
class FfStyler extends Common_Widget {

	/**
	 * Retrieve FF Styler Widget name.
	 *
	 * @since 1.26.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'FfStyler' );
	}

	/**
	 * Retrieve FF Styler title.
	 *
	 * @since 1.26.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'FfStyler' );
	}

	/**
	 * Retrieve FF Styler Widget icon.
	 *
	 * @since 1.26.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'FfStyler' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.26.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'FfStyler' );
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.26.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script' );
	}

	/**
	 * Get all forms of WP Fluent Forms plugin.
	 */
	public static function get_fluent_forms() {

		$forms = array();

		if ( function_exists( 'wpFluentForm' ) ) {

			$ff_list = wpFluent()->table( 'fluentform_forms' )
					->select( array( 'id', 'title' ) )
					->orderBy( 'id', 'DESC' )
					->get();

			if ( $ff_list ) {

				$forms[0] = esc_html__( 'Select', 'uael' );
				foreach ( $ff_list as $form ) {
					$forms[ $form->id ] = $form->title . ' (' . $form->id . ')';
				}
			} else {

				$forms[0] = esc_html__( 'No Forms Found!', 'uael' );
			}
		}

		return $forms;
	}

	/**
	 * Register controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		// content tab.
		$this->register_general_content_controls();
		$this->register_input_style_controls();
		$this->register_radio_checkbox_content_controls();
		$this->register_star_rating_controls();
		$this->register_section_controls();
		$this->register_button_content_controls();
		$this->register_error_style_controls();

		// Style tab.
		$this->register_spacing_controls();
		$this->register_typography_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register WP Fluent Forms Styler General Controls.
	 *
	 * @since 1.26.0
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
				'options' => $this->get_fluent_forms(),
				'default' => '0',
			)
		);

		$this->add_control(
			'form_title_option',
			array(
				'label'       => __( 'Title & Description', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'yes',
				'label_block' => false,
				'options'     => array(
					'yes' => __( 'Enter Your Own', 'uael' ),
					'no'  => __( 'None', 'uael' ),
				),
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'     => __( 'Form Title', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'form_title_option' => 'yes',
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
					'form_title_option' => 'yes',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_responsive_control(
			'form_title_desc_align',
			array(
				'label'              => __( 'Title & Description </br>Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
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
				'default'            => 'left',
				'condition'          => array(
					'form_title_option' => 'yes',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-form-desc,
					{{WRAPPER}} .uael-ff-form-title' => 'text-align: {{VALUE}};',
				),
				'toggle'             => false,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Input Style Controls.
	 *
	 * @since 1.26.0
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
			'ff_style',
			array(
				'label'        => __( 'Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'box',
				'options'      => array(
					'box'       => __( 'Box', 'uael' ),
					'underline' => __( 'Underline', 'uael' ),
				),
				'prefix_class' => 'uael-ff-style-',
			)
		);

		$this->add_control(
			'form_input_size',
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
				'prefix_class' => 'uael-ff-input-size-',
			)
		);

		$this->add_responsive_control(
			'form_input_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input' => 'height: {{BOTTOM}}{{UNIT}}; width: {{BOTTOM}}{{UNIT}}; font-size: calc( {{BOTTOM}}{{UNIT}} / 1.2 );',
					'{{WRAPPER}} .uael-ff-style .fluentform select.ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'padding-top: calc( {{TOP}}{{UNIT}} - 2{{UNIT}} ); padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: calc( {{BOTTOM}}{{UNIT}} - 2{{UNIT}} ); padding-left: {{LEFT}}{{UNIT}};',
				),
				'separator'          => 'after',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'form_input_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fafafa',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-net-label,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'background-color:{{VALUE}};',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--label label,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input + span,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-section-title,
					{{WRAPPER}} .uael-ff-style .ff-section_break_desk,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_tc_checkbox +  div.ff_t_c' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_input_color',
			array(
				'label'     => __( 'Input Text / Placeholder Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control::-webkit-input-placeholder, {{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform input[type=checkbox]:checked:before,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-net-label span,
					{{WRAPPER}} .uael-ff-style .uael-ff-select-custom:after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings label.active svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .uael-ff-style .fluentform input[type=radio]:checked:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{form_input_bgcolor.VALUE}};',
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
					'{{WRAPPER}} .uael-ff-style .ff-el-input--label.ff-el-is-required.asterisk-right label:after' => 'color: {{VALUE}};',
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
				'selectors'   => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-style: {{VALUE}};',
				),
				'condition'   => array(
					'ff_style' => 'box',
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
					'input_border_style!' => 'none',
					'ff_style'            => 'box',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'input_border_style!' => 'none',
					'ff_style'            => 'box',
				),
				'default'   => '#eaeaea',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ff_border_bottom',
			array(
				'label'              => __( 'Border Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em', 'rem' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'default'            => array(
					'size' => '2',
					'unit' => 'px',
				),
				'condition'          => array(
					'ff_style' => 'underline',
				),
				'selectors'          => array(
					'{{WRAPPER}}.uael-ff-style-underline .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-width: 0 0 {{SIZE}}{{UNIT}} 0; border-style: solid;',
					'{{WRAPPER}}.uael-ff-style-underline .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid; box-sizing: content-box;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'ff_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'ff_style' => 'underline',
				),
				'default'   => '#c4c4c4',
				'selectors' => array(
					'{{WRAPPER}}.uael-ff-style-underline .fluentform .ff-el-form-control,
					{{WRAPPER}}.uael-ff-style-underline .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_border_active_color',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'input_border_style!' => 'none',
					'ff_style'            => 'box',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform input:focus,
					{{WRAPPER}} .uael-ff-style .fluentform select:focus,
					{{WRAPPER}} .uael-ff-style .fluentform textarea:focus,
					{{WRAPPER}} .uael-ff-style .fluentform input[type=checkbox]:checked:before' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_border_active_color_underline',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'ff_style' => 'underline',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform input:focus,
					 {{WRAPPER}} .uael-ff-style .fluentform textarea:focus,
					 {{WRAPPER}}.uael-ff-style-underline .fluentform input[type="checkbox"]:checked' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_border_radius',
			array(
				'label'              => __( 'Rounded Corners', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px' ),
				'default'            => array(
					'top'    => '0',
					'bottom' => '0',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .uael-ff-style .fluentform input[type=checkbox],
					{{WRAPPER}} .uael-ff-style .fluentform .select2-selection' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td:first-of-type' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_net_table tbody tr td:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Radio & Checkbox Controls.
	 *
	 * @since 1.26.0
	 * @access protected
	 */
	protected function register_radio_checkbox_content_controls() {
		$this->start_controls_section(
			'ff_radio_check_style',
			array(
				'label' => __( 'Radio & Checkbox', 'uael' ),
			)
		);

		$this->add_control(
			'ff_radio_check_custom',
			array(
				'label'        => __( 'Override Current Style', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'uael-ff-check-',
			)
		);

		$this->add_control(
			'ff_radio_check_size',
			array(
				'label'      => _x( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'condition'  => array(
					'ff_radio_check_custom!' => '',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input'  => 'width: {{SIZE}}{{UNIT}}!important; height:{{SIZE}}{{UNIT}}; font-size: calc( {{SIZE}}{{UNIT}} / 1.2 );',
				),
				'separator'  => 'after',
			)
		);

		$this->add_control(
			'ff_radio_check_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'ff_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input' => 'background-color: {{VALUE}};',
				),
				'default'   => '#fafafa',
			)
		);

		$this->add_control(
			'ff_selected_color',
			array(
				'label'     => __( 'Selected Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'ff_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-ff-check-yes .uael-ff-style .fluentform input[type=checkbox]:checked:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.uael-ff-check-yes .uael-ff-style .fluentform input[type=radio]:checked:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{ff_radio_check_bgcolor.VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_select_color',
			array(
				'label'     => __( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => array(
					'ff_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input + span,
					{{WRAPPER}}.uael-ff-check-yes .uael-ff-style .fluentform .ff_tc_checkbox +  div.ff_t_c' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_check_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eaeaea',
				'condition' => array(
					'ff_radio_check_custom!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'ff_check_border_width',
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
					'ff_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->add_control(
			'ff_check_border_radius',
			array(
				'label'      => __( 'Checkbox Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'ff_radio_check_custom!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-ff-check-yes .uael-ff-style .fluentform input[type=checkbox]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	 * Register WP Fluent Forms Styler Button Controls.
	 *
	 * @since 1.26.0
	 * @access protected
	 */
	protected function register_button_content_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Button', 'uael' ),
			)
		);

		$this->add_control(
			'ff_buttons',
			array(
				'label' => __( 'Submit And Navigation Button', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label'              => __( 'Submit Button Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
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
				'default'            => 'left',
				'condition'          => array(
					'form_title_option' => 'yes',
				),
				'prefix_class'       => 'uael-ff-button-align-',
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper' => 'text-align: {{VALUE}};',
				),
				'toggle'             => false,
				'frontend_available' => true,
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
				'prefix_class' => 'uael-ff-btn-size-',
			)
		);

		$this->add_responsive_control(
			'ff_button_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'color: {{VALUE}};',
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
				'selector'       => '{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'btn_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary',
			)
		);

		$this->add_responsive_control(
			'btn_border_radius',
			array(
				'label'              => __( 'Border Radius', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_button_hover_border_color',
			array(
				'label'     => __( 'Border Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover_color',
				'label'    => __( 'Background Color', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
				{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary:hover',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
					{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ff_secondary_button',
			array(
				'label'     => __( 'Secondary Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'ff_secondary_button_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->start_controls_tabs( 'tabs_secondary_button_style' );

		$this->start_controls_tab(
			'tab_secondary_button_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'secondary_button_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'secondary_button_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'secondary_button_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn',
			)
		);

		$this->add_responsive_control(
			'secondary_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'secondary_button_box_shadow',
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_secondary_button_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'secondary_button_hover_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ff_secondary_button_hover_border_color',
			array(
				'label'     => __( 'Border Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'secondary_button_background_hover_color',
				'label'    => __( 'Background Color', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn:hover',
			)
		);

		$this->add_control(
			'secondary_button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Error Style Controls.
	 *
	 * @since 1.26.0
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ff_message_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff-el-is-error .error',
			)
		);

		$this->add_control(
			'form_error_msg_color',
			array(
				'label'     => __( 'Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff0000',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-is-error .error' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'field_validation_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-is-error .error' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'prefix_class' => 'uael-ff-error-',
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
						'{{WRAPPER}}.uael-ff-error-yes .uael-ff-style .fluentform .ff-el-is-error .ff-el-form-control' => 'background-color: {{VALUE}};',
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
						'{{WRAPPER}}.uael-ff-error-yes .uael-ff-style .fluentform .ff-el-is-error .ff-el-form-control' => 'border-color: {{VALUE}};',
						'{{WRAPPER}}.uael-ff-error-yes .uael-ff-style .fluentform .ff-el-is-error .ff-el-form-control' => 'border: {{input_border_size.BOTTOM}}px {{input_border_style.VALUE}} {{VALUE}} !important;',
						'{{WRAPPER}}.uael-ff-error-yes .uael-ff-style .fluentform .ff-el-is-error .ff-el-form-control' => 'border-width: 0 0 {{ff_border_bottom.SIZE}}px 0 !important; border-style: solid; border-color:{{VALUE}};',
					),
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

		$this->add_responsive_control(
			'success_align',
			array(
				'label'        => __( 'Alignment', 'uael' ),
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
				'default'      => 'left',
				'selectors'    => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'text-align: {{VALUE}};',
				),
				'toggle'       => false,
				'prefix_class' => 'uael-ff-message-align-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cf7_success_validation_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success',
			)
		);

		$this->add_responsive_control(
			'form_valid_message_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_success_message_color',
			array(
				'label'     => __( 'Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#008000',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'form_valid_bgcolor',
			array(
				'label'     => __( 'Message Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'border-top: {{TOP}}{{UNIT}}; border-right: {{RIGHT}}{{UNIT}}; border-bottom: {{BOTTOM}}{{UNIT}}; border-left: {{LEFT}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->add_control(
			'form_valid_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-message-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Error Style Controls.
	 *
	 * @since 1.26.0
	 * @access protected
	 */
	protected function register_star_rating_controls() {

		$this->start_controls_section(
			'star_rating_field',
			array(
				'label' => __( 'Star Rating', 'uael' ),
			)
		);

		$this->add_control(
			'ff_star_rating_custom',
			array(
				'label'        => __( 'Override Current Style', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'uael-ff-star-',
			)
		);

		$this->add_responsive_control(
			'ff_star_rating_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'condition'  => array(
					'ff_star_rating_custom' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-ff-star-yes .uael-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->add_control(
			'active_stars_color',
			array(
				'label'     => __( 'Selected Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}}.uael-ff-star-yes .uael-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings label.active svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'ff_star_rating_custom' => 'yes',
				),
			)
		);

		$this->add_control(
			'inactive_stars_color',
			array(
				'label'     => __( 'Inactive Stars Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}}.uael-ff-star-yes .uael-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'ff_star_rating_custom' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Section Break Controls.
	 *
	 * @since 1.26.0
	 * @access protected
	 */
	protected function register_section_controls() {

		$this->start_controls_section(
			'section_field',
			array(
				'label' => __( 'Section Break', 'uael' ),
			)
		);

		$this->add_control(
			'form_section_title_style',
			array(
				'label' => __( 'Title', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff-el-section-title',
			)
		);

		$this->add_control(
			'form_section_title_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-section-title' => 'color: {{VALUE}};',
				),

			)
		);

		$this->add_control(
			'form_section_desc_style',
			array(
				'label'     => __( 'Description', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_desc_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .uael-ff-style .ff-section_break_desk',
			)
		);

		$this->add_control(
			'form_section_desc_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-style .ff-section_break_desk' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Spacing Controls.
	 *
	 * @since 1.26.0
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
						'form_title_option!' => 'no',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-ff-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .uael-ff-form-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'form_title_option!' => 'no',
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
						'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--label' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
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
						'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--content' => 'margin-top: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_section_title_margin_bottom',
				array(
					'label'      => __( 'Section Break Title Bottom Margin', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'condition'  => array(
						'form_title_option!' => 'no',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-ff-style .fluentform .ff-el-section-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'form_section_desc_margin_bottom',
				array(
					'label'      => __( 'Section Break Description Bottom Margin', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-ff-style .ff-section_break_desk' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'form_title_option!' => 'no',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register WP Fluent Forms Styler Typography Controls.
	 *
	 * @since 1.26.0
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
					'form_title_option!' => 'no',
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
					'form_title_option!' => 'no',
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
				'selector'  => '{{WRAPPER}} .uael-ff-form-title',
				'condition' => array(
					'form_title_option!' => 'no',
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
					'form_title_option!' => 'no',
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-form-title' => 'color: {{VALUE}};',
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
					'form_title_option!' => 'no',
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
				'selector'  => '{{WRAPPER}} .uael-ff-form-desc',
				'condition' => array(
					'form_title_option!' => 'no',
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
					'form_title_option!' => 'no',
				),
				'default'   => '',
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .uael-ff-form-desc' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .uael-ff-style .fluentform .ff-el-input--label label,
					{{WRAPPER}} .uael-ff-style .fluentform .ff-el-form-check-input + span',
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
				'selector' => '{{WRAPPER}} .uael-ff-style .ff-el-input--content input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]),
				{{WRAPPER}} .uael-ff-style .ff-el-input--content textarea,
				{{WRAPPER}} .uael-ff-style .fluentform select,
				{{WRAPPER}} .uael-ff-style .uael-ff-select-custom',
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
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .uael-ff-style .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .uael-ff-style .fluentform .step-nav button.ff-btn-secondary,
				{{WRAPPER}} .uael-ff-style .fluentform .ff_upload_btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.26.0
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
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/wp-fluent-forms-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.26.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$form_title  = '';
		$description = '';

		if ( 'yes' === $settings['form_title_option'] ) {
			$form_title  = $this->get_settings_for_display( 'form_title' );
			$description = $this->get_settings_for_display( 'form_desc' );
		}
		?>
		<div class="uael-ff-style elementor-clickable">
			<?php

			if ( '' !== $form_title ) {
				$title_size_tag = UAEL_Helper::validate_html_tag( $settings['form_title_tag'] );
				?>

				<<?php echo esc_attr( $title_size_tag ); ?> class="uael-ff-form-title"><?php echo wp_kses_post( $form_title ); ?></<?php echo esc_attr( $title_size_tag ); ?>>
				<?php
			}

			if ( '' !== $description ) {
				?>

				<p class="uael-ff-form-desc"><?php echo wp_kses_post( $description ); ?></p>

				<?php
			}

			if ( '0' === $settings['form_id'] ) {

				esc_attr_e( 'Please select a WP Fluent Form', 'uael' );
			} elseif ( $settings['form_id'] ) {

				$shortcode_extra = '';
				$shortcode_extra = apply_filters( 'uael_ff_shortcode_extra_param', '', absint( $settings['form_id'] ) );

				echo do_shortcode( '[fluentform id=' . absint( $settings['form_id'] ) . $shortcode_extra . ']' );
			}
			?>

		</div>
		<?php
	}
}
