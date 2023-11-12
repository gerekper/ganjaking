<?php
/**
 * UAEL Table of Contents.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\TableOfContents\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Table of contents.
 */
class Table_Of_Contents extends Common_Widget {

	/**
	 * Table Of Contents class var.
	 *
	 * @var $settings array.
	 */
	public $settings = array();

	/**
	 * Retrieve Table Of Contents Widget name.
	 *
	 * @since 1.19.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Table_of_Contents' );
	}

	/**
	 * Retrieve Table Widget title.
	 *
	 * @since 1.19.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Table_of_Contents' );
	}

	/**
	 * Retrieve Table Widget icon.
	 *
	 * @since 1.19.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Table_of_Contents' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.19.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Table_of_Contents' );
	}

	/**
	 * Retrieve the list of scripts the toc widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.19.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-table-of-contents' );

	}

	/**
	 * Register controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_table_of_contents_controls();
		$this->register_heading_to_display_controls();
		$this->register_exclude_content_controls();
		$this->register_hide_button_controls();
		$this->register_scroll_heading_controls();
		$this->register_helpful_information();

		$this->register_general_controls();
		$this->register_heading_controls();
		$this->register_separator_controls();
		$this->register_contents_controls();

	}

	/**
	 * Registers all controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_table_of_contents_controls() {

		$this->start_controls_section(
			'section_heading_fields',
			array(
				'label' => __( 'Title', 'uael' ),
			)
		);

		$this->add_control(
			'heading_title',
			array(
				'label'       => __( 'Enter Title Text', 'uael' ),
				'default'     => __( 'Table of Contents', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

	}
	/**
	 * Registers all controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_heading_to_display_controls() {
		$this->start_controls_section(
			'section_contents_fields',
			array(
				'label' => __( 'Content', 'uael' ),
			)
		);

		$this->add_control(
			'heading_select',
			array(
				'label'       => __( 'Select heading tags to display', 'uael' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => array(
					'h1' => __( 'H1', 'uael' ),
					'h2' => __( 'H2', 'uael' ),
					'h3' => __( 'H3', 'uael' ),
					'h4' => __( 'H4', 'uael' ),
					'h5' => __( 'H5', 'uael' ),
					'h6' => __( 'H6', 'uael' ),
				),
				'default'     => array( 'h1', 'h2', 'h3' ),
				'label_block' => true,

			)
		);

		$this->add_control(
			'bullet_icon',
			array(
				'label'       => __( 'List Icon Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'unordered_list',
				'label_block' => false,
				'options'     => array(
					'none'           => __( 'None', 'uael' ),
					'unordered_list' => __( 'Bullets', 'uael' ),
					'ordered_list'   => __( 'Numbers', 'uael' ),
				),
			)
		);

		$this->add_control(
			'heading_separator_style',
			array(
				'label'        => __( 'Separator', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'heading_title!' => '',
				),
			)
		);

		$this->end_controls_section();

	}


	/**
	 * Register Advanced Heading Separator Controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_exclude_content_controls() {

		$this->start_controls_section(
			'exclude_headings',
			array(
				'label' => __( 'Exclude Headings', 'uael' ),
			)
		);

			$this->add_control(
				'exclude_headings_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Add the CSS class <b>uae-toc-hide-heading</b> to the heading you want to exclude from the table.', 'uael' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'exclude_doc_link',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Learn More » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/exclude-specific-headings-from-table/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);
		}

		$this->end_controls_section();

	}

	/**
	 * Registers all controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_hide_button_controls() {

		$this->start_controls_section(
			'hide_button',
			array(
				'label'     => __( 'Collapsible', 'uael' ),
				'condition' => array(
					'heading_title!' => '',
				),
			)
		);
		$this->add_control(
			'collapsible',
			array(
				'label'        => __( 'Make Content Collapsible', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'auto_collapsible',
			array(
				'label'        => __( 'Keep Collapsed Initially', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'collapsible' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'toc_icon_size',
			array(
				'label'              => __( 'Icon Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'em', 'rem' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'default'            => array(
					'size' => 20,
					'unit' => 'px',
				),
				'condition'          => array(
					'collapsible' => 'yes',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-toc-switch .uael-icon:before' => 'font-size: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; text-align: center;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'toc_switch_icon_color',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-toc-header .uael-toc-switch .uael-icon:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'collapsible' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_scroll_heading_controls() {

		$this->start_controls_section(
			'scroll',
			array(
				'label' => __( 'Scroll', 'uael' ),
			)
		);

		$this->add_control(
			'scroll_to_top',
			array(
				'label'        => __( 'Scroll to Top', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'description'  => __( 'This will add a Scroll to Top arrow at the bottom of page.', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_responsive_control(
			'scroll_to_top_offset',
			array(
				'label'              => __( 'Scroll to Top Offset', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'max' => 200,
					),
				),
				'condition'          => array(
					'scroll_to_top' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'scroll_to_top_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array(
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'  => array(
					'scroll_to_top' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-scroll-top-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; font-size: calc( {{SIZE}}px / 2 );',
				),
			)
		);

		$this->add_control(
			'scroll_to_top_color',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-scroll-top-icon, {{WRAPPER}} a.uael-scroll-top-icon:hover, {{WRAPPER}} a.uael-scroll-top-icon:focus' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'scroll_to_top' => 'yes',
				),
			)
		);

		$this->add_control(
			'scroll_to_top_bgcolor',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-scroll-top-icon' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'scroll_to_top' => 'yes',
				),
			)
		);

		$this->add_control(
			'scroll_toc',
			array(
				'label'        => __( 'Smooth Scroll', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'YES', 'uael' ),
				'label_off'    => __( 'NO', 'uael' ),
				'description'  => __( 'Smooth scroll upto destination.', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'scroll_time',
			array(
				'label'      => __( 'Scroll Animation Delay (ms)', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'max' => 2000,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 500,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'scroll_toc',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'scroll_to_top',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),

			)
		);

		$this->add_responsive_control(
			'scroll_offset',
			array(
				'label'              => __( 'Smooth Scroll Offset (px)', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'max' => 100,
					),
				),
				'condition'          => array(
					'scroll_toc' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}
	/**
	 * Helpful Information.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/introducing-table-of-contents-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';
		$help_link_2 = UAEL_DOMAIN . 'docs/exclude-specific-headings-from-table/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to exclude specific headings Table of Contents? » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}


	/**
	 * Registers general style controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_general_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Container', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'toc_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '40',
						'bottom' => '40',
						'left'   => '40',
						'right'  => '40',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-toc-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Registers heading style controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_heading_controls() {

		$this->start_controls_section(
			'content_heading',
			array(
				'label' => __( 'Title', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'heading_text_align',
			array(
				'label'              => __( 'Alignment', 'uael' ),
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
				'selectors'          => array(
					'{{WRAPPER}} .uael-toc-heading' => 'text-align: {{VALUE}};',
				),
				'prefix_class'       => 'uael%s-heading-align-',
				'frontend_available' => true,
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
					'{{WRAPPER}} .uael-toc-heading, {{WRAPPER}} .uael-toc-switch .uael-icon' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .uael-toc-heading, {{WRAPPER}} .uael-toc-heading a',
			)
		);

		$this->add_responsive_control(
			'heading_bottom_space',
			array(
				'label'              => __( 'Title Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-toc-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-toc-auto-collapse .uael-toc-header,
					{{WRAPPER}} .uael-toc-hidden .uael-toc-header' => 'margin-bottom: 0px;',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Advanced Heading Image/Icon Controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_separator_controls() {

		$this->start_controls_section(
			'section_separator_line_style',
			array(
				'label'      => __( 'Separator', 'uael' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'heading_separator_style',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'heading_title',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_line_style',
			array(
				'label'       => __( 'Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'label_block' => false,
				'options'     => array(
					'solid'  => __( 'Solid', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'double' => __( 'Double', 'uael' ),
				),
				'condition'   => array(
					'heading_separator_style' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-separator' => 'border-top-style: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'heading_line_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'condition' => array(
					'heading_separator_style' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-separator' => 'border-top-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'heading_line_thickness',
			array(
				'label'      => __( 'Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 1,
					'unit' => 'px',
				),
				'condition'  => array(
					'heading_separator_style' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_bottom_space',
			array(
				'label'              => __( 'Separator Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-separator-parent' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Registers all controls.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function register_contents_controls() {

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'content_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} .uael-toc-content-wrapper, {{WRAPPER}} .uael-toc-empty-note',
				)
			);

			$this->add_responsive_control(
				'content_between_space',
				array(
					'label'              => __( 'Spacing Between Content', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'            => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-toc-list li' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-toc-content-wrapper #toc-li-0' => 'margin-top: 0px;',
					),
					'frontend_available' => true,
				)
			);

			$this->start_controls_tabs( 'content_tabs_style' );

				$this->start_controls_tab(
					'content_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'content_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_SECONDARY,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-toc-content-wrapper a, {{WRAPPER}} .uael-toc-list li, {{WRAPPER}} .uael-toc-empty-note' => 'color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'content_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'content_hover_color',
						array(
							'label'     => __( 'Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-toc-content-wrapper a:hover' => 'color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();
				$this->start_controls_tab(
					'content_active',
					array(
						'label' => __( 'Active', 'uael' ),
					)
				);

					$this->add_control(
						'content_active_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-toc-content-wrapper a.uael-toc-active-heading' => 'color: {{VALUE}}',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Display Separator.
	 *
	 * @since 1.19.0
	 * @access public
	 * @param object $settings for settings.
	 */
	public function render_separator( $settings ) {
		if ( 'yes' === $settings['heading_separator_style'] && '' !== $settings['heading_title'] ) {
			?>
			<div class="uael-separator-parent">
				<div class="uael-separator"></div>
			</div>
			<?php
		}
	}

	/**
	 * Render Woo Product Grid output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.19.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$head_data   = $settings['heading_select'];
		$hideshow    = $settings['collapsible'];
		$displayicon = '';

		$head_data = implode( ',', $head_data );

		$this->add_inline_editing_attributes( 'heading_title', 'basic' );

		$this->add_render_attribute(
			'parent-wrapper',
			array(
				'class'         => 'uael-toc-main-wrapper',
				'data-headings' => $head_data,
			)
		);
		$scroll_time_size          = isset( $settings['scroll_time']['size'] ) ? $settings['scroll_time']['size'] : '';
		$scroll_offset_mobile_size = isset( $settings['scroll_offset_mobile']['size'] ) ? $settings['scroll_offset_mobile']['size'] : '';
		$scroll_offset_tablet_size = isset( $settings['scroll_offset_tablet']['size'] ) ? $settings['scroll_offset_tablet']['size'] : '';
		$scroll_offset_size        = isset( $settings['scroll_offset']['size'] ) ? $settings['scroll_offset']['size'] : '';

		$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll', $scroll_time_size );

		if ( '' !== $scroll_offset_mobile_size ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-offset-mobile', $scroll_offset_mobile_size );
		}

		if ( '' !== $scroll_offset_tablet_size ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-offset-tablet', $scroll_offset_tablet_size );
		}

		if ( '' !== $scroll_offset_size ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-offset', $scroll_offset_size );
		}

		$scroll_to_top_offset_size   = isset( $settings['scroll_to_top_offset']['size'] ) ? $settings['scroll_to_top_offset']['size'] : '';
		$scroll_to_top_offset_mobile = isset( $settings['scroll_to_top_offset_mobile']['size'] ) ? $settings['scroll_to_top_offset_mobile']['size'] : '';
		$scroll_to_top_offset_tablet = isset( $settings['scroll_to_top_offset_tablet']['size'] ) ? $settings['scroll_to_top_offset_tablet']['size'] : '';

		if ( '' !== $scroll_to_top_offset_mobile ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-to-top-offset-mobile', $scroll_to_top_offset_mobile );
		}

		if ( '' !== $scroll_to_top_offset_tablet ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-to-top-offset-tablet', $scroll_to_top_offset_tablet );
		}

		if ( '' !== $scroll_to_top_offset_size ) {
			$this->add_render_attribute( 'list-parent-wrapper', 'data-scroll-to-top-offset', $scroll_to_top_offset_size );
		}

		$this->add_render_attribute( 'hide-show-wrapper', 'data-hideshow', $hideshow );

		if ( 'yes' === $settings['collapsible'] ) {
			$this->add_render_attribute( 'hide-show-wrapper', 'data-is-collapsible', 'yes' );

			if ( 'yes' === $settings['auto_collapsible'] ) {
				$this->add_render_attribute( 'parent-wrapper', 'class', 'uael-toc-auto-collapse' );
			} else {
				$this->add_render_attribute( 'parent-wrapper', 'class', 'content-show' );
			}
		}
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'parent-wrapper' ) ); ?> >
			<div class="uael-toc-wrapper">
				<div class="uael-toc-header">
					<span class="uael-toc-heading elementor-inline-editing" data-elementor-setting-key="heading_title" data-elementor-inline-editing-toolbar="basic" ><?php echo wp_kses_post( $this->get_settings_for_display( 'heading_title' ) ); ?></span>
					<?php if ( 'yes' === $settings['collapsible'] ) { ?>
						<div class="uael-toc-switch" <?php echo wp_kses_post( $this->get_render_attribute_string( 'hide-show-wrapper' ) ); ?>>
							<span class="uael-icon fa"></span>
						</div>
					<?php } ?>
				</div>
				<?php $this->render_separator( $settings ); ?>
				<div class="uael-toc-toggle-content">
					<div class="uael-toc-content-wrapper">
						<?php
						if ( 'unordered_list' === $settings['bullet_icon'] ) {
							?>

							<ul data-toc-headings="headings" class="uael-toc-list uael-toc-list-disc" <?php echo wp_kses_post( $this->get_render_attribute_string( 'list-parent-wrapper' ) ); ?> ></ul>
						<?php } elseif ( 'ordered_list' === $settings['bullet_icon'] ) { ?>

							<ol data-toc-headings="headings" class="uael-toc-list" <?php echo wp_kses_post( $this->get_render_attribute_string( 'list-parent-wrapper' ) ); ?> ></ol>

						<?php } else { ?>

							<ul data-toc-headings="headings" class="uael-toc-list uael-toc-list-none" <?php echo wp_kses_post( $this->get_render_attribute_string( 'list-parent-wrapper' ) ); ?> ></ul>
						<?php } ?>
					</div>
				</div>
				<div class="uael-toc-empty-note">
					<span><?php echo esc_html__( 'Add a header to begin generating the table of contents', 'uael' ); ?></span>
				</div>
			</div>
			<?php if ( 'yes' === $settings['scroll_to_top'] ) { ?>
				<a id="uael-scroll-top" class="uael-scroll-top-icon">
					<span class="screen-reader-text">Scroll to Top</span>
				</a>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render TOC widgets output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		function render_separator() {
			if ( 'yes' === settings.heading_separator_style && '' !== settings.heading_title ) {
			#>
				<div class="uael-separator-parent">
					<div class="uael-separator"></div>
				</div>
			<#
			}
		}
		var head_data = settings.heading_select;

		view.addRenderAttribute( 'parent-wrapper', {
			'class': 'uael-toc-main-wrapper',
			'data-headings': head_data.join()
		});

		view.addRenderAttribute( 'list-parent-wrapper', 'data-scroll', settings.scroll_time.size );

		if ( '' !== settings.scroll_offset_mobile.size ) {
			view.addRenderAttribute( 'list-parent-wrapper', 'data-scroll-offset-mobile', settings.scroll_offset_mobile.size );
		}

		if ( '' !== settings.scroll_offset_tablet.size ) {
			view.addRenderAttribute( 'list-parent-wrapper', 'data-scroll-offset-tablet', settings.scroll_offset_tablet.size );
		}

		if ( '' !== settings.scroll_offset.size ) {
			view.addRenderAttribute( 'list-parent-wrapper', 'data-scroll-offset', settings.scroll_offset.size );
		}

		view.addRenderAttribute( 'hide-show-wrapper', 'data-hideshow', 'settings.collapsible' );

		if ( 'yes' === settings.collapsible ) {
			view.addRenderAttribute( 'hide-show-wrapper', 'data-is-collapsible', 'yes' );

			if ( 'yes' === settings.auto_collapsible ) {
				view.addRenderAttribute( 'parent-wrapper', 'class', 'uael-toc-auto-collapse' );
			} else {
				view.addRenderAttribute( 'parent-wrapper', 'class', 'content-show' );
			}
		}

		#>

		<div {{{ view.getRenderAttributeString( 'parent-wrapper' ) }}} > <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
			<div class="uael-toc-wrapper">
				<div class="uael-toc-header">
					<span class="uael-toc-heading elementor-inline-editing" data-elementor-setting-key="heading_title" data-elementor-inline-editing-toolbar="basic" >{{ settings.heading_title }}</span>
					<# if ( 'yes' === settings.collapsible ) { #>
						<div class="uael-toc-switch" {{{ view.getRenderAttributeString( 'hide-show-wrapper' ) }}} > <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
							<span class="uael-icon fa"></span>
						</div>
					<# } #>
				</div>

				<# render_separator(); #>

				<div class="uael-toc-toggle-content">
					<div class="uael-toc-content-wrapper">
						<# if ( 'unordered_list' === settings.bullet_icon ) { #>
							<ul data-toc-headings="headings" class="uael-toc-list uael-toc-list-disc" {{{ view.getRenderAttributeString( 'list-parent-wrapper' ) }}} ></ul> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# } else if ( 'ordered_list' === settings.bullet_icon ) { #>

							<ol data-toc-headings="headings" class="uael-toc-list"{{{ view.getRenderAttributeString( 'list-parent-wrapper' ) }}} ></ol> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>

						<# } else { #>
							<ul data-toc-headings="headings" class="uael-toc-list uael-toc-list-none" {{{ view.getRenderAttributeString( 'list-parent-wrapper' ) }}} ></ul> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# } #>
					</div>
				</div>

				<div class="uael-toc-empty-note">
					<span><?php echo esc_html__( 'Add a header to begin generating the table of contents', 'uael' ); ?></span>
				</div>

			</div>
			<# if ( 'yes' === settings.scroll_to_top ) { #>
				<a id="uael-scroll-top" class="uael-scroll-top-icon">
					<span class="screen-reader-text">Scroll to Top</span>
				</a>
			<# } #>
		</div>

		<?php
	}
}
