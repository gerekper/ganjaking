<?php
/**
 * UAEL HowTo.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\HowTo\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Control_Media;
// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class HowTo.
 */
class HowTo extends Common_Widget {

	/**
	 * Retrieve HowTo Widget name.
	 *
	 * @since 1.23.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'HowTo' );
	}

	/**
	 * Retrieve HowTo Widget title.
	 *
	 * @since 1.23.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'HowTo' );
	}

	/**
	 * Retrieve HowTo Widget icon.
	 *
	 * @since 1.23.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'HowTo' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.23.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'HowTo' );
	}

	/**
	 * Register HowTo controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		// Content Tab.
		$this->register_general_controls();
		$this->register_total_time_controls();
		$this->register_tools_controls();
		$this->register_supply_controls();
		$this->register_steps_controls();

		// Style Tab.
		$this->register_box_style_controls();
		$this->register_heading_style_controls();
		$this->register_desc_style_controls();
		$this->register_image_style_controls();
		$this->register_time_cost_style_controls();
		$this->register_content_style_controls();
		$this->register_steps_content_controls();
		$this->register_spacing_controls();

		$this->register_helpful_information();
	}

	/**
	 * Register HowTo General Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_general_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'uael' ),
			)
		);
			$this->add_control(
				'title',
				array(
					'label'   => __( 'Heading', 'uael' ),
					'type'    => Controls_Manager::TEXTAREA,
					'rows'    => '2',
					'default' => __( 'How to configure HowTo Schema in UAE?', 'uael' ),
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'description',
				array(
					'label'   => __( 'Description', 'uael' ),
					'type'    => Controls_Manager::WYSIWYG,
					'rows'    => '5',
					'default' => __( 'So to get started, you will just need to drag-n-drop the How-to Schema widget in the Elementor editor. The How-to Schema widget can be used on pages which contain a How-to in their title and describe steps to achieve certain requirements.', 'uael' ),
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'image',
				array(
					'label'   => __( 'Image', 'uael' ),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array(
						'active' => true,
					),
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'image_size',
					'default' => 'thumbnail',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo time Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_total_time_controls() {
		$this->start_controls_section(
			'section_total_time',
			array(
				'label' => __( 'Time & Cost', 'uael' ),
			)
		);

			$this->add_control(
				'show_time',
				array(
					/* translators: 1: <b> 2: <b> */
					'label'       => sprintf( __( '%1$sShow Total Time%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'default'     => 'yes',
					'label_block' => false,
				)
			);

			$this->add_control(
				'show_time_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( 'Note: The Total Time field is recommended. If disabled this will show warning in the Schema.', 'uael' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'show_time!' => 'yes',
					),
				)
			);

			$this->add_control(
				'time_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Total Time Needed:', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'total_time_heading',
				array(
					'label'       => __( 'Time', 'uael' ),
					'type'        => Controls_Manager::HEADING,
					'description' => __( 'How much time this process will take', 'uael' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'total_time_years',
				array(
					'label'     => __( 'Years', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '',
					'units'     => array( 'years' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'total_time_months',
				array(
					'label'     => __( 'Months', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '',
					'units'     => array( 'months' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'total_time_days',
				array(
					'label'     => __( 'Days', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '',
					'units'     => array( 'days' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'total_time_hours',
				array(
					'label'     => __( 'Hours', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '',
					'units'     => array( 'hours' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'time_needed',
				array(
					'label'     => __( 'Time ( Minutes )', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => __( '30', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_time' => 'yes',
					),
				)
			);

			$this->add_control(
				'show_cost',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sShow Estimated Cost%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'default'     => 'yes',
					'label_block' => false,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'cost_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Total Cost:', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_cost' => 'yes',
					),
				)
			);

			$this->add_control(
				'estimated_cost',
				array(
					'label'     => __( 'Cost', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => __( '69', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_cost' => 'yes',
					),
				)
			);

			$this->add_control(
				'cost_iso_code',
				array(
					'label'       => __( 'Country ISO Code', 'uael' ),
					/* translators: %1$s ISO code link */
					'description' => sprintf( __( 'Click %1$s here %2$s to find your country\'s ISO code.', 'uael' ), '<a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" target="_blank" rel="noopener">', '</a>' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'USD', 'uael' ),
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'show_cost' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}


	/**
	 * Register HowTo Tools Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_tools_controls() {
		$this->start_controls_section(
			'section_tools',
			array(
				'label' => __( 'Tools', 'uael' ),
			)
		);

			$this->add_control(
				'show_tools',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sAdd Tools%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'default'     => 'yes',
					'label_block' => false,
				)
			);

			$this->add_control(
				'show_tools_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( 'Note: The Tools field is recommended. If disabled this will show warning in the Schema.', 'uael' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'show_tools!' => 'yes',
					),
				)
			);

			$this->add_control(
				'tools_text',
				array(
					'label'     => __( 'Title', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Required Tools:', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_tools' => 'yes',
					),
				)
			);

			$repeater = new Repeater();

				$repeater->start_controls_tabs( 'tool_repeater' );

					$repeater->add_control(
						'tool_item_text',
						array(
							'label'       => __( 'Text', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'label_block' => true,
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

					$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'tool',
				array(
					'label'       => __( 'Add Required Tools', 'uael' ),
					'type'        => Controls_Manager::REPEATER,
					'show_label'  => true,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ tool_item_text }}',
					'default'     => array(
						array(
							'tool_item_text' => __( '- A Computer.', 'uael' ),
						),
						array(
							'tool_item_text' => __( '- Internet Connection.', 'uael' ),
						),
						array(
							'tool_item_text' => __( '- Google Structured Data Testing Tool.', 'uael' ),
						),
					),
					'condition'   => array(
						'show_tools' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo Supply Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_supply_controls() {
		$this->start_controls_section(
			'section_supply',
			array(
				'label' => __( 'Materials', 'uael' ),
			)
		);

			$this->add_control(
				'show_supply',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sAdd Materials%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'default'     => 'yes',
					'label_block' => false,
				)
			);

			$this->add_control(
				'show_supply_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( 'Note: The Materials field is recommended. If disabled this will show warning in the Schema.', 'uael' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'show_supply!' => 'yes',
					),
				)
			);

			$this->add_control(
				'supply_text',
				array(
					'label'     => __( 'Title', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Things Needed?', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_supply' => 'yes',
					),
				)
			);

			$repeater = new Repeater();

				$repeater->start_controls_tabs( 'supply_repeater' );

					$repeater->add_control(
						'supply_item_text',
						array(
							'label'       => __( 'Text', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'label_block' => true,
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

					$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'supply',
				array(
					'label'       => __( 'Add Required Materials', 'uael' ),
					'type'        => Controls_Manager::REPEATER,
					'show_label'  => true,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ supply_item_text }}',
					'default'     => array(
						array(
							'supply_item_text' => __( '- A WordPress Website.', 'uael' ),
						),
						array(
							'supply_item_text' => __( '- Elementor Plugin.', 'uael' ),
						),
						array(
							'supply_item_text' => __( '- UAE Plugin.', 'uael' ),
						),
					),
					'condition'   => array(
						'show_supply' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo Steps Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_steps_controls() {
		$this->start_controls_section(
			'section_steps',
			array(
				'label' => __( 'Steps', 'uael' ),
			)
		);

			$this->add_control(
				'steps_text',
				array(
					'label'       => __( 'Title', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Steps to configure the How-to Schema widget:', 'uael' ),
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
				)
			);

			$repeater = new Repeater();

				$repeater->start_controls_tabs( 'steps_repeater' );

					$repeater->add_control(
						'steps_item_title',
						array(
							'label'       => __( 'Title', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'label_block' => true,
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

					$repeater->add_control(
						'steps_item_desc',
						array(
							'label'   => __( 'Description', 'uael' ),
							'type'    => Controls_Manager::TEXTAREA,
							'dynamic' => array(
								'active' => true,
							),
						)
					);

					$repeater->add_control(
						'steps_item_url',
						array(
							'label'       => __( 'Link', 'uael' ),
							'type'        => Controls_Manager::URL,
							'placeholder' => __( 'https://your-link.com', 'uael' ),
							'default'     => array(
								'url' => '',
							),
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

					$repeater->add_control(
						'steps_item_image',
						array(
							'label'   => __( 'Image', 'uael' ),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => array(
								'active' => true,
							),
							'default' => array(
								'url' => Utils::get_placeholder_image_src(),
							),
						)
					);

					$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'steps',
				array(
					'label'       => __( 'Add Steps', 'uael' ),
					'type'        => Controls_Manager::REPEATER,
					'show_label'  => true,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ steps_item_title }}',
					'default'     => array(
						array(
							'steps_item_title' => __( 'Step 1 : Enter the HowTo Schema title you want', 'uael' ),
							'steps_item_desc'  => __( 'Enter the title to your HowTo Schema', 'uael' ),
						),
						array(
							'steps_item_title' => __( 'Step 2 : Enter the HowTo Schema description and add a relevant image', 'uael' ),
							'steps_item_desc'  => __( 'Enter the HowTo Description with a relevant image to your description.', 'uael' ),
						),
						array(
							'steps_item_title' => __( 'Step 3 : Configure the Advanced settings. ie Total Time, Estimated Cost, Materials, Tools', 'uael' ),
							'steps_item_desc'  => __( 'Enter Total Time, Estimated Cost, Tools & Materials', 'uael' ),
						),
						array(
							'steps_item_title' => __( 'Step 4 : Enter the Steps for your HowTo Schema ', 'uael' ),
							'steps_item_desc'  => __( 'Steps for your HowTo Schema instructions. It can be a single step (text, document or video) or an ordered list of steps (itemList) of HowTo Step.', 'uael' ),
						),
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo Box style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_box_style_controls() {

		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => __( 'Box', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'overall_align',
				array(
					'label'     => __( 'Overall Alignment', 'uael' ),
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Register HowTo spacing Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_spacing_controls() {
		$this->start_controls_section(
			'section_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'heading_spacing',
				array(
					'label'      => __( 'Heading bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-title-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

			$this->add_responsive_control(
				'desc_spacing',
				array(
					'label'      => __( 'Description bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

			$this->add_responsive_control(
				'image_spacing',
				array(
					'label'      => __( 'Image bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

			$this->add_responsive_control(
				'spacing_between_sections',
				array(
					'label'      => __( 'Spacing between sections', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-supply, {{WRAPPER}} .uael-howto-tools, {{WRAPPER}} .uael-howto-details' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

			$this->add_control(
				'section_spacing_common',
				array(
					'label'      => __( 'Tools & Materials', 'uael' ),
					'type'       => Controls_Manager::HEADING,
					'separator'  => 'before',
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_tools',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'show_supply',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				'title_spacing',
				array(
					'label'      => __( 'Section title bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 15,
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_tools',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'show_supply',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				'items_spacing',
				array(
					'label'      => __( 'Spacing between items', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 30,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-content .uael-howto-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'show_tools',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'show_supply',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'section_spacing_steps',
				array(
					'label'     => __( 'Steps', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'step_title_spacing',
				array(
					'label'      => __( 'Title bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-steps-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 15,
					),
				)
			);

			$this->add_responsive_control(
				'step_content_title_spacing',
				array(
					'label'      => __( 'Step title bottom spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-steps-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 10,
					),
				)
			);

			$this->add_responsive_control(
				'step_items_spacing',
				array(
					'label'      => __( 'Spacing between steps', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 30,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-steps-wrapper .uael-howto-step-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

			$this->add_responsive_control(
				'steps_image_spacing',
				array(
					'label'      => __( 'Image spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
						'em' => array(
							'max'  => 5,
							'step' => 0.1,
						),
					),
					'size_units' => array( 'px', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}}.uael-howto-image-align-bottom .uael-howto-step-image-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.uael-howto-image-align-left .uael-howto-step-image-wrap,
						.rtl {{WRAPPER}}.uael-howto-image-align-right .uael-howto-step-image-wrap' => 'margin-right: {{SIZE}}{{UNIT}};margin-left:0;',
						'{{WRAPPER}}.uael-howto-image-align-right .uael-howto-step-image-wrap,
						.rtl {{WRAPPER}}.uael-howto-image-align-left .uael-howto-step-image-wrap' => 'margin-left: {{SIZE}}{{UNIT}};margin-right:0;',
					),
					'default'    => array(
						'size' => 20,
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo heading style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_heading_style_controls() {
		$this->start_controls_section(
			'section_heading_style',
			array(
				'label' => __( 'Heading', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'heading_tag',
				array(
					'label'   => __( 'Select Tag', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'h1'   => __( 'H1', 'uael' ),
						'h2'   => __( 'H2', 'uael' ),
						'h3'   => __( 'H3', 'uael' ),
						'h4'   => __( 'H4', 'uael' ),
						'h5'   => __( 'H5', 'uael' ),
						'h6'   => __( 'H6', 'uael' ),
						'div'  => __( 'div', 'uael' ),
						'span' => __( 'span', 'uael' ),
						'p'    => __( 'p', 'uael' ),
					),
					'default' => 'h3',
				)
			);

			$this->add_control(
				'heading_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-title-text' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'heading_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .uael-howto-title-text',
				)
			);

			$this->add_responsive_control(
				'heading_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-wrapper .uael-howto-title' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo description style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_desc_style_controls() {
		$this->start_controls_section(
			'section_desc_style',
			array(
				'label' => __( 'Description', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-description' => 'color: {{VALUE}};',
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
					'selector' => '{{WRAPPER}} .uael-howto-description',
				)
			);

			$this->add_responsive_control(
				'desc_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-wrapper .uael-howto-description' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo image style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_image_style_controls() {

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'image_width',
				array(
					'label'      => __( 'Width', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 500,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'    => array(
						'size' => 30,
						'unit' => '%',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-image img' => 'width: {{SIZE}}{{UNIT}}; min-width:{{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_control(
				'border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'image_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-image' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Register HowTo Time & Cost style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_time_cost_style_controls() {

		$this->start_controls_section(
			'section_time_cost_style',
			array(
				'label'      => __( 'Time & Cost', 'uael' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_cost',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'show_time',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);
			$this->add_control(
				'time_cost_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-time-needed, {{WRAPPER}} .uael-howto-estimated-cost' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'time_cost_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-howto-time-needed, {{WRAPPER}} .uael-howto-estimated-cost',
				)
			);

			$this->add_responsive_control(
				'time_cost_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
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
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-wrapper .uael-howto-details' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo content style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_content_style_controls() {
		$this->start_controls_section(
			'common_content_style',
			array(
				'label'      => __( 'Tools & Materials', 'uael' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_tools',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'show_supply',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

			$this->add_control(
				'inline_supply_tools',
				array(
					'label'       => __( 'Inline Sections', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'Yes', 'uael' ),
					'label_off'   => __( 'No', 'uael' ),
					'default'     => 'true',
					'label_block' => false,
				)
			);

			$this->add_control(
				'common_title_heading',
				array(
					'label' => __( 'Title', 'uael' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'common_title_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-heading' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'common_title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'selector' => '{{WRAPPER}} .uael-howto-heading',
				)
			);

			$this->add_control(
				'common_content_heading',
				array(
					'label'     => __( 'Content', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'common_content_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-content' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'common_content_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-howto-content',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register HowTo steps content style Controls.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_steps_content_controls() {

		$this->start_controls_section(
			'steps_content_style',
			array(
				'label' => __( 'Steps', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'steps_title_heading',
				array(
					'label' => __( 'Section Title', 'uael' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'steps_title_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-steps-text' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'steps_title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'selector' => '{{WRAPPER}} .uael-howto-steps-text',
				)
			);

			$this->add_control(
				'steps_content_title_heading',
				array(
					'label'     => __( 'Step Title', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'steps_content_title_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-steps-title, {{WRAPPER}} .uael-howto-steps-title a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'steps_content_title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'selector' => '{{WRAPPER}} .uael-howto-steps-title, {{WRAPPER}} .uael-howto-steps-title a',
				)
			);

			$this->add_control(
				'steps_section_content_heading',
				array(
					'label'     => __( 'Step Description', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'steps_section_content_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-howto-steps-desc' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'steps_section_content_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-howto-steps-desc',
				)
			);

			$this->add_control(
				'steps_section_image_heading',
				array(
					'label'     => __( 'Step Image', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'step_image_position',
				array(
					'label'        => __( 'Image Position', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'default'      => 'right',
					'options'      => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-h-align-left',
						),
						'bottom' => array(
							'title' => __( 'Bottom', 'uael' ),
							'icon'  => 'eicon-v-align-bottom',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'prefix_class' => 'uael-howto-image-align-',
					'render_type'  => 'template',
					'toggle'       => false,
				)
			);

			$this->add_responsive_control(
				'step_image_width',
				array(
					'label'      => __( 'Width', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 500,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'size' => 30,
						'unit' => '%',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-step-image-wrap' => 'width: {{SIZE}}{{UNIT}}; min-width:{{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_control(
				'step_image_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-howto-step-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/how-to-schema/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render HowTo output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function render() {

		global $wp_embed;
		$settings = $this->get_settings_for_display();

		$supply_container      = array();
		$supply_container_wrap = array();

		$tool_container      = array();
		$tool_container_wrap = array();

		$steps_container      = array();
		$steps_container_wrap = array();

		$empty_items_array = array();
		$inline_class      = '';

		if ( isset( $settings['supply'] ) && is_array( $settings['supply'] ) ) {

			foreach ( $settings['supply'] as $supply ) {

				$supply_container = array(

					'@type' => 'HowToSupply',
					'name'  => $supply['supply_item_text'],
				);
				array_push( $supply_container_wrap, $supply_container );
			}
		}

		if ( isset( $settings['tool'] ) && is_array( $settings['tool'] ) ) {

			foreach ( $settings['tool'] as $tool ) {

				$tool_container = array(

					'@type' => 'HowToTool',
					'name'  => $tool['tool_item_text'],
				);
				array_push( $tool_container_wrap, $tool_container );
			}
		}

		if ( isset( $settings['steps'] ) && is_array( $settings['steps'] ) ) {
			foreach ( $settings['steps'] as $step ) {

				$steps_container = array(
					'@type' => 'HowToStep',
					'url'   => ! empty( $step['steps_item_url']['url'] ) ? $step['steps_item_url']['url'] : get_permalink(),
					'name'  => $step['steps_item_title'],
					'text'  => $step['steps_item_desc'],
					'image' => $step['steps_item_image']['url'],
				);
				array_push( $steps_container_wrap, $steps_container );
			}
		}

		if ( ! empty( $supply_container ) && is_array( $supply_container ) ) {

			$supply = wp_json_encode( $supply_container_wrap );
		}
		if ( ! empty( $tool_container ) && is_array( $tool_container ) ) {

			$tool = wp_json_encode( $tool_container_wrap );
		}
		if ( ! empty( $steps_container_wrap ) && is_array( $steps_container_wrap ) ) {

			$steps = wp_json_encode( $steps_container_wrap );
		}

		$years   = ( '' !== $settings['total_time_years'] ) ? $settings['total_time_years'] : '0';
		$months  = ( '' !== $settings['total_time_months'] ) ? $settings['total_time_months'] : '0';
		$days    = ( '' !== $settings['total_time_days'] ) ? $settings['total_time_days'] : '0';
		$hours   = ( '' !== $settings['total_time_hours'] ) ? $settings['total_time_hours'] : '0';
		$minutes = ( '' !== $settings['time_needed'] ) ? $settings['time_needed'] : '0';

		$total_time = array(
			// translators: %s for time duration.
			'year'   => ! empty( $years ) ? sprintf( _n( '%s year', '%s years', $years, 'uael' ), number_format_i18n( $years ) ) : '',
			// translators: %s for time duration.
			'month'  => ! empty( $months ) ? sprintf( _n( '%s month', '%s months', $months, 'uael' ), number_format_i18n( $months ) ) : '',
			// translators: %s for time duration.
			'day'    => ! empty( $days ) ? sprintf( _n( '%s day', '%s days', $days, 'uael' ), number_format_i18n( $days ) ) : '',
			// translators: %s for time duration.
			'hour'   => ! empty( $hours ) ? sprintf( _n( '%s hour', '%s hours', $hours, 'uael' ), number_format_i18n( $hours ) ) : '',
			// translators: %s for time duration.
			'minute' => ! empty( $minutes ) ? sprintf( _n( '%s minute', '%s minutes', $minutes, 'uael' ), number_format_i18n( $minutes ) ) : '',
		);

		foreach ( $total_time as $time_key => $duration ) {
			if ( empty( $duration ) ) {
				unset( $total_time[ $time_key ] );
			}
		}

		if ( ! empty( $total_time ) ) {
			$time      = implode( ', ', $total_time );
			$time_text = $time;
		}

		$y                    = ( 525600 * $years );
		$m                    = ( 43200 * $months );
		$d                    = ( 1440 * $days );
		$h                    = ( 60 * $hours );
		$calculate_total_time = $y + $m + $d + $h + $minutes;

		$heading_tag = UAEL_Helper::validate_html_tag( $settings['heading_tag'] )

		?>

		<div class="uael-howto-wrapper">
			<div class="uael-howto-title">
				<?php echo '<' . esc_attr( $heading_tag ) . ' class="uael-howto-title-text">' . wp_kses_post( $settings['title'] ) . '</' . esc_attr( $heading_tag ) . '>'; ?>
			</div>
			<div class="uael-howto-description">
				<span class="uael-howto-desc-text"><?php echo wp_kses_post( $settings['description'] ); ?></span>
			</div>
			<?php if ( ! empty( $settings['image']['url'] ) ) { ?>
				<div class="uael-howto-image">
					<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) ); ?>
				</div>
			<?php } ?>
			<?php if ( 'yes' === $settings['show_time'] || 'yes' === $settings['show_cost'] ) { ?>
				<div class="uael-howto-details">
					<?php if ( 'yes' === $settings['show_time'] ) { ?>
						<div class="uael-howto-time-needed"><span><span class="uael-howto-time-title"><?php echo wp_kses_post( $settings['time_text'] ) . ' '; ?></span><?php echo esc_html( $time_text ); ?></span></div>
					<?php } ?>
					<?php if ( 'yes' === $settings['show_cost'] ) { ?>
						<div class="uael-howto-estimated-cost">
							<span><span class="uael-howto-cost-title"><?php echo wp_kses_post( $settings['cost_text'] ) . ' '; ?></span><?php echo esc_html( $settings['estimated_cost'] ) . ' ' . esc_html( $settings['cost_iso_code'] ); ?></span>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<?php
			if ( 'yes' === $settings['inline_supply_tools'] ) {
				$inline_class = 'uael-howto-section-inline';
			}
			?>
			<?php if ( 'yes' === $settings['show_tools'] || 'yes' === $settings['show_supply'] ) { ?>
				<div class="uael-howto-supply-tools <?php echo esc_attr( $inline_class ); ?>">
					<?php if ( 'yes' === $settings['show_tools'] ) { ?>
						<div class="uael-howto-tools">
							<h4 class="uael-howto-tool-text uael-howto-heading"><?php echo wp_kses_post( $settings['tools_text'] ); ?></h4>
							<div class="uael-howto-tool-wrapper uael-howto-content">
								<?php foreach ( $settings['tool'] as $index => $item ) { ?>
									<div class="uael-howto-tool-item uael-howto-item uael-tool-<?php echo esc_attr( $index ); ?>">
										<span><?php echo wp_kses_post( $item['tool_item_text'] ); ?></span>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
					<?php if ( 'yes' === $settings['show_supply'] ) { ?>
						<div class="uael-howto-supply">
							<h4 class="uael-howto-supply-text uael-howto-heading"><?php echo wp_kses_post( $settings['supply_text'] ); ?></h4>
							<div class="uael-howto-supply-wrapper uael-howto-content">
								<?php foreach ( $settings['supply'] as $index => $item ) { ?>
									<div class="uael-howto-supply-item uael-howto-item uael-supply-<?php echo esc_attr( $index ); ?>">
										<span><?php echo wp_kses_post( $item['supply_item_text'] ); ?></span>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="uael-howto-steps">
				<h4 class="uael-howto-steps-text"><?php echo wp_kses_post( $settings['steps_text'] ); ?></h4>
				<div class="uael-howto-steps-wrapper">
					<?php foreach ( $settings['steps'] as $index => $item ) { ?>
						<?php
						$is_image = '';
						if ( ! empty( $item['steps_item_image']['url'] ) ) {
							$is_image = 'uael-step-has-image';
						}
						?>
						<div class="uael-howto-step-item uael-step-<?php echo esc_attr( $index ) . ' ' . esc_attr( $is_image ); ?>">

							<div class="uael-howto-step-item-wrap">
								<div class="uael-howto-steps-title">
									<?php
									if ( ! empty( $item['steps_item_url']['url'] ) ) {

										$title = $item['steps_item_title'];

										$this->add_link_attributes( 'step_url_' . $index, $item['steps_item_url'] );

										$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'step_url_' . $index ), $title );

										echo $title; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									} else {
										?>
										<span><?php echo wp_kses_post( $item['steps_item_title'] ); ?></span>
									<?php } ?>
								</div>
								<div class="uael-howto-steps-desc"><span><?php echo wp_kses_post( $item['steps_item_desc'] ); ?></span></div>
							</div>
							<?php if ( ! empty( $item['steps_item_image']['url'] ) ) { ?>
								<div class="uael-howto-step-image-wrap">
									<img class="uael-howto-step-image" src="<?php echo esc_url( $item['steps_item_image']['url'] ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $item['steps_item_image'] ) ); ?>" title="<?php echo esc_attr( Control_Media::get_image_title( $item['steps_item_image'] ) ); ?>" />
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php // @codingStandardsIgnoreStart. ?>
			<script type="application/ld+json">
				{
				"@context": "http://schema.org",
				"@type": "HowTo",
				"name": "<?php echo ! empty( $settings['title'] ) ? $settings['title'] : ''; ?>",
				"description": "<?php echo ! empty( $settings['description'] ) ? esc_html( $settings['description'] ) : ''; ?>",
				<?php if ( ! empty( $settings['image']['url'] ) ) { ?>
					"image": {
						"@type": "ImageObject",
						"url": "<?php echo ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : ''; ?>",
						"height": "406",
						"width": "305"
					},
				<?php } ?>
				<?php if ( ! empty( $settings['estimated_cost'] ) && 'yes' === $settings['show_cost'] ) { ?>
					"estimatedCost": {
						"@type": "MonetaryAmount",
						"currency": "<?php echo ! empty( $settings['cost_iso_code'] ) ? $settings['cost_iso_code'] : ''; ?>",
						"value": "<?php echo ! empty( $settings['estimated_cost'] ) ? $settings['estimated_cost'] : ''; ?>"
					},
				<?php } ?>
				<?php if ( ! empty( $settings['supply'][0] ) && 'yes' === $settings['show_supply'] ) { ?>
				"supply":
					<?php
					if ( isset( $supply ) && ! empty( $supply ) ) {
						echo $supply;
					}
					?>
					,
				<?php } ?>
				<?php if ( ! empty( $settings['tool'][0] ) && 'yes' === $settings['show_tools'] ) { ?>
				"tool":
					<?php
					if ( isset( $tool ) && ! empty( $tool ) ) {
						echo $tool;
					}
					?>
					,
				<?php } ?>
				<?php if ( ! empty( $settings['steps'][0] ) ) { ?>
				"step":
					<?php
					if ( isset( $steps ) && ! empty( $steps ) ) {
						echo $steps;
					}
					?>
				<?php } ?>

				<?php if ( ! empty( $calculate_total_time ) && 'yes' === $settings['show_time'] ) { ?>
					, "totalTime":
					<?php if ( isset( $calculate_total_time ) ) { ?>
						"PT<?php echo $calculate_total_time; ?>M"
					<?php } ?>
				<?php } ?>


			}
			</script>
			<?php // @codingStandardsIgnoreEnd. ?>
		</div>

		<?php
	}

	/**
	 *  Render Image HTML.
	 *
	 *  @param string $settings settings object instance.
	 *  @since 1.23.0
	 */
	public function render_image( $settings ) {

		$image_id   = $settings['image']['id'];
		$image_size = $settings['image_size_size'];
		$class      = '';

		if ( 'custom' === $image_size ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $instance );
		} else {
			$image_src = wp_get_attachment_image_src( $image_id, $image_size );
			$image_src = $image_src[0];
		}

		if ( '' === $image_id ) {
			if ( isset( $item['image']['url'] ) ) {
				$image_src = $settings['image']['url'];
				$class     = 'uael-howto-img';
			}
		}

		return sprintf( '<img class="%s" src="%s" alt="%s" />', $class, $image_src, $settings['title'] );
	}

	/**
	 * Render HowTo widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.23.0
	 * @access protected
	 */
	protected function content_template() {}
}
