<?php
/**
 * UAEL FAQ's.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\FAQ\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Controls_Manager\DIVIDER;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class FAQ.
 */
class FAQ extends Common_Widget {

	/**
	 * FAQ class var.
	 *
	 * @var $settings array.
	 */
	public $settings = array();

	/**
	 * Retrieve FAQ Widget name.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'FAQ' );
	}

	/**
	 * Retrieve FAQ title.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'FAQ' );
	}

	/**
	 * Retrieve FAQ Widget icon.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'FAQ' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'FAQ' );
	}

	/**
	 * Retrieve the list of scripts the FAQ widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_presets_control( 'FAQ', $this );
		// content tab.
		$this->register_content();
		$this->register_layout();
		$this->register_icon_content();
		$this->register_helpful_information();
		// Style tab.
		$this->register_accordion();
		$this->register_question_style();
		$this->register_answer_style();
		$this->register_icon_style();
	}


	/**
	 * Render content type list.
	 *
	 * @since 1.22.0
	 * @return array Array of content type
	 * @access public
	 */
	public function get_content_type() {
		$content_type = array(
			'content'              => __( 'Content', 'uael' ),
			'saved_rows'           => __( 'Saved Section', 'uael' ),
			'saved_container'      => __( 'Saved Container', 'uael' ),
			'saved_page_templates' => __( 'Saved Page', 'uael' ),
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$content_type['saved_modules'] = __( 'Saved Widget', 'uael' );
		}

		return $content_type;
	}

	/**
	 * Registers controls for FAQ question and answer.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_content() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'uael' ),
			)
		);
				$repeater = new Repeater();

				$repeater->add_control(
					'question',
					array(
						'label'       => __( 'Title', 'uael' ),
						'type'        => Controls_Manager::TEXT,
						'label_block' => true,
						'default'     => __( 'What is FAQ?', 'uael' ),
						'dynamic'     => array(
							'active' => true,
						),
					)
				);

				$repeater->add_control(
					'faq_content_type',
					array(
						'label'   => __( 'Content Type', 'uael' ),
						'type'    => Controls_Manager::SELECT,
						'default' => 'content',
						'options' => $this->get_content_type(),
					)
				);

			$repeater->add_control(
				'ct_saved_rows',
				array(
					'label'     => __( 'Select Section', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'section' ),
					'default'   => '-1',
					'condition' => array(
						'faq_content_type' => 'saved_rows',
					),
				)
			);

			$repeater->add_control(
				'ct_saved_container',
				array(
					'label'     => __( 'Select Container', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'container' ),
					'default'   => '-1',
					'condition' => array(
						'faq_content_type' => 'saved_container',
					),
				)
			);

			$repeater->add_control(
				'ct_saved_modules',
				array(
					'label'     => __( 'Select Widget', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'widget' ),
					'default'   => '-1',
					'condition' => array(
						'faq_content_type' => 'saved_modules',
					),
				)
			);

			$repeater->add_control(
				'ct_page_templates',
				array(
					'label'     => __( 'Select Page', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'page' ),
					'default'   => '-1',
					'condition' => array(
						'faq_content_type' => 'saved_page_templates',
					),
				)
			);

				$repeater->add_control(
					'answer',
					array(
						'label'      => __( 'Content', 'uael' ),
						'type'       => Controls_Manager::WYSIWYG,
						'default'    => __( 'Accordion Content', 'uael' ),
						'show_label' => true,
						'dynamic'    => array(
							'active' => true,
						),
						'default'    => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),

						'condition'  => array(
							'faq_content_type' => 'content',
						),
					)
				);

				$this->add_control(
					'tabs',
					array(
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default'     => array(
							array(
								'question' => __( 'Impedit egestas aliquet?', 'uael' ),
								'answer'   => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
							),
							array(
								'question' => __( 'Sapien class quo temporibus?', 'uael' ),
								'answer'   => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
							),
							array(
								'question' => __( 'Elementum voluptate sodales?', 'uael' ),
								'answer'   => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
							),
						),
						'title_field' => '{{ question }}',
					)
				);

				$this->add_control(
					'hr',
					array(
						'type' => Controls_Manager::DIVIDER,
					)
				);

				$this->add_control(
					'schema_support',
					array(
						'label'       => __( 'Enable Schema Support', 'uael' ),
						'description' => __( 'Note: Schema will not work if the dynamic content is used in FAQ\'s', 'uael' ),
						'type'        => Controls_Manager::SWITCHER,
						'label_on'    => __( 'Yes', 'uael' ),
						'label_off'   => __( 'No', 'uael' ),
						'default'     => 'no',
					)
				);

				$this->end_controls_section();
	}


	/**
	 * Registers controls for icon.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_icon_content() {
			$this->start_controls_section(
				'uael_icon_content',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'condition' => array(
						'faq_layout!' => 'grid',
					),
				)
			);

			$this->add_control(
				'selected_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'separator'        => 'before',
					'fa4compatibility' => 'icon',
					'default'          => array(
						'value'   => 'fas fa-angle-right',
						'library' => 'fa-solid',
					),
					'style_transfer'   => true,
				)
			);

			$this->add_control(
				'selected_active_icon',
				array(
					'label'          => __( 'Active Icon', 'uael' ),
					'type'           => Controls_Manager::ICONS,
					'default'        => array(
						'value'   => 'fas fa-angle-up',
						'library' => 'fa-solid',
					),
					'condition'      => array(
						'selected_icon[value]!' => '',
					),
					'style_transfer' => true,
				)
			);

			$this->add_control(
				'icon_align',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => __( 'Start', 'uael' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right' => array(
							'title' => __( 'End', 'uael' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default'      => is_rtl() ? 'right' : 'left',
					'toggle'       => false,
					'label_block'  => false,
					'render_type'  => 'template',
					'prefix_class' => 'align-at-',
				)
			);

			$this->end_controls_section();
	}

	/**
	 * Registers controls for layout option for grid.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_layout() {
			$this->start_controls_section(
				'section_layout',
				array(
					'label' => __( 'Layout', 'uael' ),
				)
			);
			$this->add_control(
				'faq_layout',
				array(
					'label'   => __( 'Layout', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'accordion' => __( 'Accordion', 'uael' ),
						'grid'      => __( 'Grid', 'uael' ),
					),
					'default' => 'accordion',
				)
			);

			$this->add_control(
				'enable_toggle_layout',
				array(
					'label'     => __( 'Toggle', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => __( 'Enable', 'uael' ),
					'label_off' => __( 'Disable', 'uael' ),
					'default'   => 'Disable',
					'condition' => array(
						'faq_layout' => 'accordion',

					),
				)
			);

			$this->add_control(
				'faq_layout_style',
				array(
					'label'        => __( 'Enable Box Style', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'no', 'uael' ),
					'default'      => 'yes',
					'condition'    => array(
						'faq_layout' => 'accordion',
					),
					'prefix_class' => 'uael-faq-box-layout-',
				)
			);

			$this->add_responsive_control(
				'row_gap',
				array(
					'label'     => __( 'Rows Gap', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 10,
					),
					'condition' => array(
						'faq_layout_style' => 'yes',
						'faq_layout'       => 'accordion',

					),
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-container > .uael-faq-accordion:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_responsive_control(
				'columns',
				array(
					'label'           => __( 'Columns', 'uael' ),
					'type'            => Controls_Manager::SELECT,
					'desktop_default' => 2,
					'tablet_default'  => 2,
					'mobile_default'  => 1,
					'options'         => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class'    => 'elementor-grid%s-',
					'condition'       => array(
						'faq_layout' => 'grid',
					),
					'render_type'     => 'template',
				)
			);

			$this->add_responsive_control(
				'grid_column_gap',
				array(
					'label'     => __( 'Columns Gap', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 10,
					),
					'condition' => array(
						'faq_layout' => 'grid',
						'columns!'   => '1',
					),
					'selectors' => array(
						'{{WRAPPER}}:not(.elementor-grid-0) .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}}.elementor-grid-0 .uael-faq-accordion' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2)',
						'{{WRAPPER}}.elementor-grid-0 .elementor-grid' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
					),

				)
			);

			$this->add_responsive_control(
				'grid_row_gap',
				array(
					'label'     => __( 'Rows Gap', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 10,
					),
					'condition' => array(
						'faq_layout' => 'grid',
					),
					'selectors' => array(
						'{{WRAPPER}}:not(.elementor-grid-0) .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}}.elementor-grid-0 .uael-faq-accordion' => 'margin-bottom: {{SIZE}}{{UNIT}}',
						'(tablet) {{WRAPPER}}.elementor-grid-tablet-0 .elementor-share-btn' => 'margin-bottom: {{SIZE}}{{UNIT}}',
						'(mobile) {{WRAPPER}}.elementor-grid-mobile-0 .elementor-share-btn' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_responsive_control(
				'uael_grid_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'eicon-text-align-right',
						),
						'justify' => array(
							'title' => __( 'Justified', 'uael' ),
							'icon'  => 'eicon-text-align-justify',
						),
					),
					'default'   => '',
					'condition' => array(
						'faq_layout' => 'grid',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-accordion.elementor-grid-item' => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'enable_seperator',
				array(
					'label'     => __( 'Enable Separator', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => __( 'Yes', 'uael' ),
					'label_off' => __( 'No', 'uael' ),
					'default'   => 'no',
					'separator' => 'before',
				)
			);

			$this->end_controls_section();
	}

	/**
	 * Registers controls for styling options of border.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_accordion() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Box', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'faq_border_style',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'double' => __( 'Double', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-box-layout-yes .uael-faq-container .uael-faq-accordion' => 'border-style: {{VALUE}}; ',
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion.elementor-grid-item' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-container:last-child' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-container.uael-faq-container:last-child' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-container.uael-faq-layout-grid:last-child' => 'border-bottom-style: none ;',
				),
				'condition'   => array(
					'faq_layout_style!' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_border_width',
			array(
				'label'     => __( 'Width', 'uael' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default'   => array(
					'top'      => 1,
					'right'    => 1,
					'bottom'   => 1,
					'left'     => 1,
					'isLinked' => true,
				),
				'selectors' => array(

					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion' => 'border-width:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px {{LEFT}}{{UNIT}} ;',
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion.elementor-grid-item' => 'border-width:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',
					'{{WRAPPER}} .uael-faq-container:last-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',
					'{{WRAPPER}}.uael-faq-layout-grid .uael-faq-container:last-child' => 'border-bottom: 0px;',
				),
				'condition' => array(
					'faq_border_style!' => 'none',
					'faq_layout_style!' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_border_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-container:last-child' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content' => 'border-top-color: {{VALUE}};',
				),
				'default'   => '#D4D4D4',
				'condition' => array(
					'faq_border_style!' => 'none',
					'faq_layout_style!' => 'yes',
				),
			)
		);

		$this->add_control(
			'faq_box_border_style',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'double' => __( 'Double', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-faq-wrapper .uael-faq-container .uael-faq-accordion' => 'border-style: {{VALUE}};',
				),
				'condition'   => array(
					'faq_layout_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_box_border_width',
			array(
				'label'     => __( 'Width', 'uael' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default'   => array(
					'top'      => 1,
					'right'    => 1,
					'bottom'   => 1,
					'left'     => 1,
					'isLinked' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-wrapper .uael-faq-container .uael-faq-accordion' => 'border-width:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',

				),
				'condition' => array(
					'faq_box_border_style!' => 'none',
					'faq_layout_style'      => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_box_border_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-wrapper .uael-faq-container .uael-faq-accordion' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion .uael-accordion-content' => 'border-top-color: {{VALUE}};',
				),
				'default'   => '#D4D4D4',
				'condition' => array(
					'faq_box_border_style!' => 'none',
					'faq_layout_style'      => 'yes',
				),
			)
		);

		$this->add_control(
			'uael_border_radius',
			array(
				'label'     => __( 'Border radius', 'uael' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default'   => array(
					'top'      => 1,
					'right'    => 1,
					'bottom'   => 1,
					'left'     => 1,
					'isLinked' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'faq_box_border_style!' => 'none',
					'faq_layout_style'      => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'box_layout_shadow',
				'label'     => __( 'Box Shadow', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-faq-accordion',
				'condition' => array(
					'faq_border_style!' => 'none',
					'faq_layout_style'  => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'box_normal_layout_shadow',
				'label'     => __( 'Box Shadow', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-faq-wrapper',
				'condition' => array(
					'faq_border_style!' => 'none',
					'faq_layout_style!' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_separator_heading',
			array(
				'label'     => __( 'Separator', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'enable_seperator' => 'yes',
				),
			)
		);

			$this->add_control(
				'faq_separator_style',
				array(
					'label'       => __( 'Style', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'solid',
					'label_block' => false,
					'options'     => array(
						'none'   => __( 'None', 'uael' ),
						'solid'  => __( 'Solid', 'uael' ),
						'dashed' => __( 'Dashed', 'uael' ),
						'dotted' => __( 'Dotted', 'uael' ),
						'double' => __( 'Double', 'uael' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .uael-faq-container .uael-faq-accordion .uael-accordion-content' => 'border-top-style: {{VALUE}};',
					),
					'condition'   => array(
						'enable_seperator' => 'yes',
					),
				)
			);

		$this->add_control(
			'uael_separator_width',
			array(
				'label'     => __( 'Width', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion .uael-accordion-content' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'enable_seperator'     => 'yes',
					'faq_separator_style!' => 'none',

				),
			)
		);

		$this->add_control(
			'uael_separator_border_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-container .uael-faq-accordion .uael-accordion-content' => 'border-top-color: {{VALUE}};',
				),
				'default'   => '#D4D4D4',
				'condition' => array(
					'enable_seperator'     => 'yes',
					'faq_separator_style!' => 'none',
				),

			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls for Title/Question styling.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_question_style() {
		$this->start_controls_section(
			'uael_title_style',
			array(
				'label' => __( 'Title', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => __( 'HTML Tag', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => __( 'H1', 'uael' ),
					'h2'   => __( 'H2', 'uael' ),
					'h3'   => __( 'H3', 'uael' ),
					'h4'   => __( 'H4', 'uael' ),
					'h5'   => __( 'H5', 'uael' ),
					'h6'   => __( 'H6', 'uael' ),
					'div'  => __( 'div', 'uael' ),
					'p'    => __( 'p', 'uael' ),
					'span' => __( 'span', 'uael' ),
				),
				'default' => 'span',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .uael-faq-accordion .uael-accordion-title .uael-question-span, {{WRAPPER}} .uael-faq-accordion .uael-accordion-title .uael-accordion-icon',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),

			)
		);

		$this->start_controls_tabs( 'uael_title_colors' );

		$this->start_controls_tab(
			'uael_colors_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

			$this->add_control(
				'uael_title_background',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'uael_active_title_background',
				array(
					'label'     => __( 'Active Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title.uael-title-active' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'faq_layout' => 'accordion',
					),
				)
			);

			$this->add_control(
				'uael_title_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title .uael-question-span,
						{{WRAPPER}}  .uael-accordion-icon-closed, {{WRAPPER}} span.uael-accordion-icon-opened' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-accordion-icon-closed, {{WRAPPER}} span.uael-accordion-icon-opened' => 'fill: {{VALUE}};',
					),
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),

				)
			);

			$this->add_control(
				'uael_title_active_color',
				array(
					'label'     => __( 'Active Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title.uael-title-active .uael-question-span,
						{{WRAPPER}} span.uael-accordion-icon-opened' => 'color: {{VALUE}};',
					),
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'condition' => array(
						'faq_layout' => 'accordion',
					),
				)
			);

		$this->end_controls_tab();
				$this->start_controls_tab(
					'icon_colors_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

		$this->add_control(
			'uael_title_background_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'uael_active_title_hover_background',
			array(
				'label'     => __( 'Active Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title.uael-title-active:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'faq_layout' => 'accordion',
				),
			)
		);

		$this->add_control(
			'uael_title_hover_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title .uael-question-span:hover,
					{{WRAPPER}}  .uael-accordion-icon-closed:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-accordion-icon-closed:hover' => 'fill: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),

			)
		);

		$this->add_control(
			'uael_title_active_hover_color',
			array(
				'label'     => __( 'Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title.uael-title-active:hover .uael-question-span,
					{{WRAPPER}} span.uael-accordion-icon-opened:hover' => 'color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'faq_layout' => 'accordion',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'isLinked' => true,
				),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls for Content/Answer styling.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_answer_style() {
		$this->start_controls_section(
			'uael_content_style',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .uael-faq-accordion .uael-accordion-content',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->start_controls_tabs( 'uael_content_colors' );

		$this->start_controls_tab(
			'uael_content_colors_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'uael_content_background',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-faq-accordion.elementor-grid-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'uael_content_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content' => 'color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),

			)
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'uael_content_colors_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'uael_content_hover_background',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'uael_content_hover_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content:hover' => 'color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),

			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'uael_content_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-faq-accordion .uael-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls for Icon Styling.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_icon_style() {
		$this->start_controls_section(
			'uael_icon_style',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'faq_layout!' => 'grid' ),
			)
		);

		$this->add_responsive_control(
			'title_icon_size',
			array(
				'label'     => __( 'Icon Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 16,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-faq-wrapper .uael-accordion-title .uael-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-faq-wrapper .uael-accordion-title .uael-accordion-icon svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'
				{{WRAPPER}}  .uael-accordion-icon-closed' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-accordion-icon-closed' => 'fill: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),

			)
		);

		$this->add_control(
			'uael_icon_active_color',
			array(
				'label'     => __( 'Active Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} span.uael-accordion-icon-opened'  => 'color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
			)
		);

		$this->add_responsive_control(
			'uael_icon_space',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-accordion-icon.uael-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-accordion-icon.uael-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Register FAQ docs link.
	 *
	 * @since 1.22.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/faq/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
	 * Render button widget classes names.
	 *
	 * @since 1.22.0
	 * @param array $key The settings array.
	 * @return string Concatenated string of classes
	 * @access public
	 */
	public function get_modal_content( $key ) {
		$dynamic_settings = $this->get_settings_for_display();
		$content_type     = $key['faq_content_type'];
		$output           = '';
		switch ( $content_type ) {
			case 'content':
				$output = '<span>' . $key['answer'] . '</span>';
				break;
			case 'saved_rows':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( apply_filters( 'wpml_object_id', $key['ct_saved_rows'], 'page' ) );
				break;
			case 'saved_container':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( apply_filters( 'wpml_object_id', $key['ct_saved_container'], 'page' ) );
				break;
			case 'saved_modules':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $key['ct_saved_modules'] );
				break;
			case 'saved_page_templates':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $key['ct_page_templates'] );
				break;
			default:
				return;
		}
		return $output;
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.22.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings               = $this->get_settings_for_display();
		$is_editor              = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$id_int                 = substr( $this->get_id_int(), 0, 3 );
		$content_schema_warning = 0;
		$count                  = 1;
		foreach ( $settings['tabs'] as $key ) {
			if ( 'content' !== $key['faq_content_type'] ) {
				$content_schema_warning = 1;
			}
		}

		if ( ( 1 === $content_schema_warning ) && ( true === $is_editor ) && ( 'yes' === $settings['schema_support'] ) ) {
			?><span>
				<?php
				echo '<div class="elementor-alert elementor-alert-warning uael-warning">';
				echo esc_attr_e( 'The FAQ Schema is not supported in the case of Saved Section / Saved Page.', 'uael' );
				echo '</div>';
				?>
			</span>
			<?php
		}

		$this->add_render_attribute( 'uael-faq-container', 'class', 'uael-faq-container uael-faq-layout-' . $settings['faq_layout'] );

		if ( 'grid' === $settings['faq_layout'] ) {
			$this->add_render_attribute( 'uael-faq-container', 'class', 'elementor-grid' );
		} elseif ( 'accordion' === $settings['faq_layout'] ) {
			if ( 'yes' === $settings['enable_toggle_layout'] ) {
				$this->add_render_attribute( 'uael-faq-container', 'data-layout', 'toggle' );
			} else {
				$this->add_render_attribute( 'uael-faq-container', 'data-layout', 'accordion' );
			}
		}
		?>

			<div id='uael-faq-wrapper-<?php echo esc_attr( $id_int ); ?>' class="uael-faq-wrapper">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-faq-container' ) ); ?> >
					<?php

					foreach ( $settings['tabs'] as $key ) {
						if ( ( '' === $key['question'] || '' === $key['answer'] ) && 'yes' === $settings['schema_support'] && ( true === $is_editor ) ) {
							?>
							<span>
								<?php
								echo '<div class="elementor-alert elementor-alert-warning uael-warning">';
								echo esc_attr_e( 'Please fill out the empty fields in content', 'uael' );
								echo '</div>';
								?>
							</span>
							<?php
						}
						if ( 'grid' === $settings['faq_layout'] ) {
							$this->add_render_attribute(
								'uael_faq_accordion_' . $key['_id'],
								array(
									'id'    => 'uael-accordion-' . $key['_id'],
									'class' => array( 'uael-faq-accordion', 'elementor-grid-item' ),
								)
							);
						} else {
							$this->add_render_attribute(
								'uael_faq_accordion_' . $key['_id'],
								array(
									'id'    => 'uael-accordion-' . $key['_id'],
									'class' => 'uael-faq-accordion',
								)
							);
						}

						if ( ! ( '' === $key['question'] || '' === $key['answer'] ) ) {
							$heading_size_tag = UAEL_Helper::validate_html_tag( $settings['heading_tag'] );
							?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_faq_accordion_' . $key['_id'] ) ); ?> role="tablist">
								<div class= "uael-accordion-title" aria-expanded="false" role="tab">
									<span class="uael-accordion-icon uael-accordion-icon-<?php echo esc_attr( $settings['icon_align'] ); ?>">
										<span class="uael-accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
										<span class="uael-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
									</span>
									<<?php echo esc_html( $heading_size_tag ); ?> class="uael-question-<?php echo esc_attr( $key['_id'] ); ?> uael-question-span" tabindex="0" id="uael-faq-<?php echo esc_attr( $count ); ?>"><?php echo wp_kses_post( $key['question'] ); ?></<?php echo esc_html( $heading_size_tag ); ?>>
								</div>
								<div class="uael-accordion-content" role="tabpanel">
									<span>
									<?php
									echo $this->get_modal_content( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
									</span>
								</div>
							</div>
							<?php
						} else {
							$content_schema_warning = 1;
						}
						$count++;
					}
					?>
				</div>
			</div>
			<?php
	}
}
