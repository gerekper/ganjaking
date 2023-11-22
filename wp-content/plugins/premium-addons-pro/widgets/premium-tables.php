<?php
/**
 * Class: Premium_Tables
 * Name: Table
 * Slug: premium-tables-addon
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Tables
 */
class Premium_Tables extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-tables-addon';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Table', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-table';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'sheet', 'data', 'advanced', 'dynamic', 'comparison', 'csv', 'google' );
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'table-sorter',
			'lottie-js',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Get Repeater Controls
	 *
	 * @since 0.0.1
	 * @access protected
	 *
	 * @param object $repeater repeater object.
	 * @param array  $condition controls condition.
	 */
	protected function get_repeater_controls( $repeater, $condition = array() ) {

		$repeater->add_control(
			'premium_table_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'Text',
				'condition'   => array_merge( $condition, array() ),
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text' => 'color: {{VALUE}} !important;',
				),
				'condition' => array_merge( $condition, array() ),
			)
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cell_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th{{CURRENT_ITEM}}.premium-table-cell .premium-table-text, {{WRAPPER}} .premium-table .premium-table-row td{{CURRENT_ITEM}}.premium-table-cell .premium-table-text',
			)
		);

		$repeater->add_control(
			'premium_table_icon_selector',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'font-awesome-icon',
				'separator'   => 'before',
				'label_block' => true,
				'options'     => array(
					'font-awesome-icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'custom-image'      => __( 'Custom Image', 'premium-addons-pro' ),
					'animation'         => __( 'Lottie Animation', 'premium-addons-pro' ),
				),
				'condition'   => array_merge( $condition, array() ),
			)
		);

		$repeater->add_control(
			'premium_table_cell_icon',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'condition'   => array_merge(
					$condition,
					array(
						'premium_table_icon_selector' => 'font-awesome-icon',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_table_cell_icon_img',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'condition' => array_merge(
					$condition,
					array(
						'premium_table_icon_selector' => 'custom-image',
					)
				),
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_table_icon_selector' => 'animation',
				),
			)
		);

		$repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_table_icon_selector' => 'animation',
				),
			)
		);

		$repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_table_icon_selector' => 'animation',
				),
			)
		);

		$repeater->add_control(
			'premium_table_cell_icon_align',
			array(
				'label'       => __( 'Icon Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'before',
				'options'     => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'before' => __( 'Before', 'premium-addons-pro' ),
					'after'  => __( 'After', 'premium-addons-pro' ),
				),
				'condition'   => array_merge( $condition, array() ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array_merge( $condition, array() ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 150,
					),
					'em' => array(
						'min' => 1,
						'max' => 15,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text img, {{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text svg'    => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$repeater->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text i' => 'color: {{VALUE}} !important',
				),
				'condition' => array_merge(
					$condition,
					array(
						'premium_table_icon_selector' => 'font-awesome-icon',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_table_cell_icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array_merge( $condition, array() ),
				'selectors' => array(
					'{{WRAPPER}}.premium-table-dir-rtl {{CURRENT_ITEM}} .premium-table-text .premium-table-cell-icon-after, {{WRAPPER}}.premium-table-dir-ltr {{CURRENT_ITEM}} .premium-table-text .premium-table-cell-icon-before'   => 'margin-right: {{SIZE}}px',
					'{{WRAPPER}}.premium-table-dir-ltr {{CURRENT_ITEM}} .premium-table-text .premium-table-cell-icon-after, {{WRAPPER}}.premium-table-dir-rtl {{CURRENT_ITEM}} .premium-table-text .premium-table-cell-icon-before'   => 'margin-left: {{SIZE}}px',
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text.premium-table-cell-top .premium-table-cell-icon-top'    => 'margin-bottom: {{SIZE}}px',
				),
				'separator' => 'below',
			)
		);

		$repeater->add_control(
			'premium_table_cell_row_span',
			array(
				'label'     => __( 'Row Span', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'title'     => __( 'Enter the number of rows for the cell', 'premium-addons-pro' ),
				'separator' => 'before',
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'condition' => array_merge( $condition, array() ),
			)
		);

		$repeater->add_control(
			'premium_table_cell_span',
			array(
				'label'     => __( 'Column Span', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'title'     => __( 'Enter the number of columns for the cell', 'premium-addons-pro' ),
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'condition' => array_merge( $condition, array() ),
			)
		);

		$repeater->add_responsive_control(
			'premium_table_cell_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'separator'            => 'before',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'justify-content: flex-start; text-align: left',
					'center' => 'justify-content: center; text-align: center',
					'right'  => 'justify-content: flex-end; text-align: right',
				),
				'default'              => 'left',
				'selectors'            => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-text' => '{{VALUE}};',
				),
				'condition'            => array_merge(
					$condition,
					array(
						'premium_table_cell_icon_align!' => 'top',
					)
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_table_cell_top_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-table-cell-top' => 'align-items: {{VALUE}};',
				),
				'condition' => array_merge(
					$condition,
					array(
						'premium_table_cell_icon_align' => 'top',
					)
				),
			)
		);

	}

	/**
	 * Register Table controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_table_data_section',
			array(
				'label' => __( 'Data', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_data_type',
			array(
				'label'   => __( 'Data Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'custom' => __( 'Custom', 'premium-addons-pro' ),
					'csv'    => 'CSV' . __( ' File', 'premium-addons-pro' ),
				),
				'default' => 'custom',
			)
		);

		$this->add_control(
			'premium_table_csv_type',
			array(
				'label'     => __( 'File Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'file' => __( 'Upload FIle', 'premium-addons-pro' ),
					'url'  => __( 'Remote File', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_table_data_type' => 'csv',
				),
				'default'   => 'file',
			)
		);

		$this->add_control(
			'premium_table_separator',
			array(
				'label'       => __( 'Data Separator', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Separator between cells data', 'premium-addons-pro' ),
				'label_block' => true,
				'default'     => ',',
				'condition'   => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->add_control(
			'premium_table_csv',
			array(
				'label'      => __( 'Upload CSV File', 'premium-addons-pro' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array( 'active' => true ),
				'media_type' => array(),
				'condition'  => array(
					'premium_table_data_type' => 'csv',
					'premium_table_csv_type'  => 'file',
				),
			)
		);

		$this->add_control(
			'premium_table_csv_url',
			array(
				'label'       => __( 'File URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_table_data_type' => 'csv',
					'premium_table_csv_type'  => 'url',
				),
			)
		);

		$this->add_control(
			'premium_table_csv_first_row',
			array(
				'label'       => __( 'Render First Row As', 'premium-addons-pro' ),
				'description' => __( 'Choose whether to render the first row as table head or not', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'head' => __( 'Header', 'premium-addons-pro' ),
					'body' => __( 'Body', 'premium-addons-pro' ),
				),
				'default'     => 'head',
				'condition'   => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->add_control(
			'reload',
			array(
				'label'     => __( 'Refresh Cached Data Once Every', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'minutes' => __( 'Five Minutes', 'premium-addons-pro' ),
					'hour'    => __( 'Hour', 'premium-addons-pro' ),
					'day'     => __( 'Day', 'premium-addons-pro' ),
					'week'    => __( 'Week', 'premium-addons-pro' ),
					'month'   => __( 'Month', 'premium-addons-pro' ),
					'year'    => __( 'Year', 'premium-addons-pro' ),
				),
				'default'   => 'hour',
				'condition' => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_head_section',
			array(
				'label'     => __( 'Header', 'premium-addons-pro' ),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$head_repeater = new Repeater();

		$this->get_repeater_controls( $head_repeater, array() );

		$head_repeater->add_control(
			'head_link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$head_repeater->add_control(
			'head_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'head_link_switcher' => 'yes',
				),
			)
		);

		$head_repeater->add_control(
			'head_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'after',
				'condition'   => array(
					'head_link_switcher'  => 'yes',
					'head_link_selection' => 'url',
				),
			)
		);

		$head_repeater->add_control(
			'head_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'head_link_switcher'  => 'yes',
					'head_link_selection' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_table_head_repeater',
			array(
				'label'         => __( 'Cell', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $head_repeater->get_controls(),
				'default'       => array(
					array(
						'premium_table_text' => __( 'First Head', 'premium-addons-pro' ),
					),
					array(
						'premium_table_text' => __( 'Second Head', 'premium-addons-pro' ),
					),
					array(
						'premium_table_text' => __( 'Third Head', 'premium-addons-pro' ),
					),
				),
				'title_field'   => '{{{ premium_table_text }}}',
				'prevent_empty' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_body_section',
			array(
				'label'     => __( 'Body', 'premium-addons-pro' ),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$body_repeater = new Repeater();

		$body_repeater->add_control(
			'premium_table_elem_type',
			array(
				'label'   => __( 'Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'cell' => __( 'Cell', 'premium-addons-pro' ),
					'row'  => __( 'Row', 'premium-addons-pro' ),
				),
				'default' => 'cell',
			)
		);

		$body_repeater->add_control(
			'premium_table_cell_type',
			array(
				'label'     => __( 'Cell Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'td' => __( 'Body', 'premium-addons-pro' ),
					'th' => __( 'Head', 'premium-addons-pro' ),
				),
				'default'   => 'td',
				'condition' => array(
					'premium_table_elem_type' => 'cell',
				),
			)
		);

		$this->get_repeater_controls( $body_repeater, array( 'premium_table_elem_type' => 'cell' ) );

		$body_repeater->add_control(
			'premium_table_link_switcher',
			array(
				'label'     => __( 'Link', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_table_elem_type' => 'cell',
				),
			)
		);

		$body_repeater->add_control(
			'premium_table_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_table_link_switcher' => 'yes',
					'premium_table_elem_type'     => 'cell',
				),
			)
		);

		$body_repeater->add_control(
			'premium_table_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'after',
				'condition'   => array(
					'premium_table_elem_type'      => 'cell',
					'premium_table_link_switcher'  => 'yes',
					'premium_table_link_selection' => 'url',
				),
			)
		);

		$body_repeater->add_control(
			'premium_table_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_table_elem_type'      => 'cell',
					'premium_table_link_switcher'  => 'yes',
					'premium_table_link_selection' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_table_body_repeater',
			array(
				'label'         => __( 'Rows', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'default'       => array(
					array(
						'premium_table_elem_type' => 'row',
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #1', 'premium-addons-pro' ),
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #2', 'premium-addons-pro' ),
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #3', 'premium-addons-pro' ),
					),
					array(
						'premium_table_elem_type' => 'row',
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #1', 'premium-addons-pro' ),
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #2', 'premium-addons-pro' ),
					),
					array(
						'premium_table_elem_type' => 'cell',
						'premium_table_text'      => __( 'Column #3', 'premium-addons-pro' ),
					),
				),
				'fields'        => $body_repeater->get_controls(),
				'title_field'   => '{{ premium_table_elem_type }}: {{{ premium_table_text }}}',
				'prevent_empty' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_display',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_layout',
			array(
				'label'     => __( 'Table Layout', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'auto'  => __( 'Auto', 'premium-addons-pro' ),
					'fixed' => __( 'Fixed', 'premium-addons-pro' ),
				),
				'default'   => 'auto',
				'selectors' => array(
					'{{WRAPPER}} .premium-table' => 'table-layout: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 700,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-widget-container' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_table_blur',
			array(
				'label'       => __( 'Blur On Hover', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => sprintf( '<span style="font-weight: bold">%s</span>', __( 'You will need to set rows text hover color from style tab', 'premium-addons-pro' ) ),
			)
		);

		$this->add_control(
			'premium_table_responsive',
			array(
				'label'              => __( 'Responsive', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enables scroll on mobile.', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'premium_table_align',
			array(
				'label'     => __( 'Table Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_advanced',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_sort',
			array(
				'label'              => __( 'Sortable', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enables sorting with respect to the table heads.', 'premium-addons-pro' ),
				'frontend_available' => true,
				'condition'          => array(
					'premium_table_data_type!' => 'csv',
				),
			)
		);

		$this->add_control(
			'us_numbers',
			array(
				'label'              => __( 'US Numbers Format', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enable this if you are using US formatted numbers. For instance, 1,234,567.89', 'premium-addons-pro' ),
				'frontend_available' => true,
				'condition'          => array(
					'premium_table_sort'       => 'yes',
					'premium_table_data_type!' => 'csv',
				),
			)
		);

		$this->add_control(
			'premium_table_sort_mob',
			array(
				'label'              => __( 'Sort on Mobile', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'premium_table_sort' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_table_search',
			array(
				'label'              => __( 'Search', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enables searching through the table using rows\' first cell keyword.', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_table_search_placeholder',
			array(
				'label'     => __( 'Placeholder', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Live Search...', 'premium-addons-pro' ),
				'condition' => array(
					'premium_table_search' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_table_records',
			array(
				'label'              => __( 'Show Records', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Shows a dropdown to control number of records', 'premium-addons-pro' ),
				'condition'          => array(
					'premium_table_data_type' => 'custom',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_table_records_label',
			array(
				'label'     => __( 'Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Show Records:', 'premium-addons-pro' ),
				'condition' => array(
					'premium_table_records' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_search_align',
			array(
				'label'     => __( 'Search Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'right',
				'selectors' => array(
					'{{WRAPPER}} .premium-table-search-wrap' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_search'   => 'yes',
					'premium_table_records!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_records_align',
			array(
				'label'     => __( 'Show Records Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'right',
				'selectors' => array(
					'{{WRAPPER}} .premium-table-records-wrap' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_records' => 'yes',
					'premium_table_search!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'       => __( 'Pagination', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Please note that pagination works only on frontend', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'rows_per_page',
			array(
				'label'     => esc_html__( 'Rows Per Page', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 5,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'     => __( 'Pagination Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination-list'  => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_table_search_dir',
			array(
				'label'        => __( 'Direction', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-table-dir-',
				'options'      => array(
					'ltr' => array(
						'title' => __( 'LTR', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-right',
					),
					'rtl' => array(
						'title' => __( 'RTL', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-left',
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-table-wrap' => 'direction: {{VALUE}};',
				),
				'default'      => 'ltr',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/table-widget-tutorial' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/importing-table-widget-data-from-csv-file/' => __( 'How to import data from CSV file »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-add-links-to-csv-file-in-premium-elementor-table-widget/' => __( 'How to add links/images to CSV file »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/add-google-sheets-to-elementor-table-widget-tutorial' => __( 'How to import data from Google Spreadsheet »', 'premium-addons-pro' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_head_style',
			array(
				'label' => __( 'Head', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_table_head_style_tabs' );

		$this->start_controls_tab(
			'premium_table_odd_head_odd_style',
			array(
				'label' => __( 'Odd', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_odd_head_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_odd_head_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_odd_head_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_table_odd_head_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_table_odd_head_text_shadow',
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text',
			)
		);

		$this->add_control(
			'premium_table_odd_head_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_odd_head_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_head_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd)',
			)
		);

		$this->add_control(
			'premium_table_odd_head_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_head_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd):hover',
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'premium_table_odd_head_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'left',
				'selectors_dictionary' => array(
					'left'   => 'justify-content: flex-start; text-align: left',
					'center' => 'justify-content: center; text-align: center',
					'right'  => 'justify-content: flex-end; text-align: right',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(odd) .premium-table-text' => '{{VALUE}};',
				),
				'condition'            => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_table_head_even_style',
			array(
				'label' => __( 'Even', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_even_head_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_even_head_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_even_head_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_table_even_head_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_table_even_head_text_shadow',
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text',
			)
		);

		$this->add_control(
			'premium_table_even_head_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_even_head_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_head_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even)',
			)
		);

		$this->add_control(
			'premium_table_even_head_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_head_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even):hover',
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'premium_table_even_head_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'left',
				'selectors_dictionary' => array(
					'left'   => 'justify-content: flex-start; text-align: left',
					'center' => 'justify-content: center; text-align: center',
					'right'  => 'justify-content: flex-end; text-align: right',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell:nth-child(even) .premium-table-text' => '{{VALUE}};',
				),
				'condition'            => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_table_head_rows_border',
				'selector'  => '{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_table_head_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table .premium-table-row th.premium-table-cell .premium-table-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_row_style',
			array(
				'label' => __( 'Rows', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_table_row_style_tabs' );

		$this->start_controls_tab(
			'premium_table_odd_row_odd_style',
			array(
				'label' => __( 'Odd', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_odd_row_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell .premium-table-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-table-blur tbody:hover tr:nth-of-type(odd) .premium-table-text' => 'text-shadow: 0 0 3px {{VALUE}};',

				),
			)
		);

		$this->add_control(
			'premium_table_odd_row_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell .premium-table-text:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-table-blur tbody:hover tr:nth-of-type(odd):hover .premium-table-text' => 'text-shadow: none !important; color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'premium_table_row_row_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell .premium-table-text i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_table_odd_row_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell .premium-table-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_table_odd_row_text_shadow',
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell .premium-table-text',
			)
		);

		$this->add_control(
			'premium_table_odd_row_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_odd_row_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_row_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell',
			)
		);

		$this->add_control(
			'premium_table_odd_row_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_row_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(odd) .premium-table-cell:hover',
			)
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_table_row_even_style',
			array(
				'label' => __( 'Even', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_even_row_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell .premium-table-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-table-blur tbody:hover tr:nth-of-type(even) .premium-table-text' => 'text-shadow: 0 0 3px {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_even_row_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell .premium-table-text:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-table-blur tbody:hover tr:nth-of-type(even):hover .premium-table-text' => 'text-shadow: none !important; color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'premium_table_row_even_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell .premium-table-text i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_table_data_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_table_even_row_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell .premium-table-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_table_even_row_text_shadow',
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell .premium-table-text',
			)
		);

		$this->add_control(
			'premium_table_even_row_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_even_row_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_row_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell',
			)
		);

		$this->add_control(
			'premium_table_even_row_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_row_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table tbody tr:nth-of-type(even) .premium-table-cell:hover',
			)
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_table_row_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-table .premium-table-row td.premium-table-cell',
			)
		);

		$this->add_responsive_control(
			'premium_table_odd_row_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table .premium-table-row td.premium-table-cell .premium-table-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_col_style',
			array(
				'label' => __( 'Columns', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_table_col_style_tabs' );

		$this->start_controls_tab(
			'premium_table_odd_col_odd_style',
			array(
				'label' => __( 'Odd', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_odd_col_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_odd_col_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_col_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row .premium-table-cell:nth-child(odd)',
			)
		);

		$this->add_control(
			'premium_table_odd_col_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_odd_col_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row .premium-table-cell:nth-child(odd):hover',
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'premium_table_odd_col_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'left',
				'selectors_dictionary' => array(
					'left'   => 'justify-content: flex-start; text-align: left',
					'center' => 'justify-content: center; text-align: center',
					'right'  => 'justify-content: flex-end; text-align: right',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-table .premium-table-row .premium-table-cell:nth-child(odd) .premium-table-text' => '{{VALUE}};',
				),
				'condition'            => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_table_col_even_style',
			array(
				'label' => __( 'Even', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_table_even_col_background_popover',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_table_even_col_background_heading',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_col_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row .premium-table-cell:nth-child(even)',
			)
		);

		$this->add_control(
			'premium_table_even_col_hover_background_heading',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_even_col_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table .premium-table-row .premium-table-cell:nth-child(even):hover',
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'premium_table_even_col_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'left',
				'selectors_dictionary' => array(
					'left'   => 'justify-content: flex-start; text-align: left',
					'center' => 'justify-content: center; text-align: center',
					'right'  => 'justify-content: flex-end; text-align: right',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-table .premium-table-row td.premium-table-cell:nth-child(even) .premium-table-text' => '{{VALUE}};',
				),
				'condition'            => array(
					'premium_table_data_type' => 'csv',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_sort_style',
			array(
				'label'     => __( 'Sort', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_table_sort'       => 'yes',
					'premium_table_data_type!' => 'csv',
				),
			)
		);

		$this->add_control(
			'premium_table_sort_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_sort_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table thead .premium-table-cell:hover .premium-table-sort-icon:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_table_sort_background',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_table_sort_border',
				'selector' => '{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before',
			)
		);

		$this->add_control(
			'premium_table_sort_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_table_sort_box_shadow',
				'selector' => '{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before',
			)
		);

		$this->add_responsive_control(
			'premium_table_sort_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table thead .premium-table-cell .premium-table-sort-icon:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_search_style',
			array(
				'label'     => __( 'Search', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_table_search' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_search_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-search-field, {{WRAPPER}} .premium-table-filter-records .premium-table-search-wrap' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_table_search_color',
			array(
				'label'     => __( 'Input Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table-search-field' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_search_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table-search-field',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_table_search_border',
				'selector' => '{{WRAPPER}} .premium-table-search-field',
			)
		);

		$this->add_control(
			'premium_table_search_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-search-field' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_table_container_search_shadow',
				'selector' => '{{WRAPPER}} .premium-table-search-field',
			)
		);

		$this->add_responsive_control(
			'premium_table_search_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-search-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_search_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-search-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_records_style',
			array(
				'label'     => __( 'Records', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_table_records' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_records_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-records-box' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_table_filters_color',
			array(
				'label'     => __( 'Options Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-table-records-box' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_records_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table-records-box',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_table_records_border',
				'selector' => '{{WRAPPER}} .premium-table-records-box',
			)
		);

		$this->add_control(
			'premium_table_records_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-records-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_table_records_shadow',
				'selector' => '{{WRAPPER}} .premium-table-records-box',
			)
		);

		$this->add_responsive_control(
			'premium_table_records_margin',
			array(
				'label'      => __( 'Select Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-records-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_records_box_margin',
			array(
				'label'      => __( 'Box Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-records-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_table_records_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-records-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_style',
			array(
				'label'     => __( 'Pagination', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .premium-table-pagination ul li > .page-numbers',
			)
		);

		$this->add_responsive_control(
			'pagination_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => __( 'Numbers Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'pagination_style_tabs' );

		$this->start_controls_tab(
			'pagination_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .premium-table-pagination ul li .page-numbers',
			)
		);

		$this->add_control(
			'pagination_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pagination_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_hover_border',
				'selector' => '{{WRAPPER}} .premium-table-pagination ul li .page-numbers:hover',
			)
		);

		$this->add_control(
			'pagination_hover_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-pagination ul li .page-numbers:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_active',
			array(
				'label' => __( 'Active', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li a.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-table-pagination ul li a.current' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_active_border',
				'selector' => '{{WRAPPER}} .premium-table-pagination ul li a.current',
			)
		);

		$this->add_control(
			'pagination_active_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table-pagination ul li a.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_table_style',
			array(
				'label' => __( 'Table', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_table_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-table',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_table_border',
				'selector' => '{{WRAPPER}} .premium-table',
			)
		);

		$this->add_control(
			'premium_table_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-table' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_table_box_shadow',
				'selector' => '{{WRAPPER}} .premium-table',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Table head output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render_table_head() {

		$settings = $this->get_settings_for_display();

		$head = '';

		$this->add_render_attribute( 'table_head', 'class', 'premium-table-head' );

		$this->add_render_attribute( 'table_row', 'class', 'premium-table-row' );

		?>

		<thead <?php echo wp_kses_post( $this->get_render_attribute_string( 'table_head' ) ); ?>>

			<tr <?php echo wp_kses_post( $this->get_render_attribute_string( 'table_row' ) ); ?>>

				<?php

				if ( 'custom' === $settings['premium_table_data_type'] ) {

					foreach ( $settings['premium_table_head_repeater'] as $index => $head_cell ) {

						$html_tag = 'span';

						$head_cell_text = $this->get_repeater_setting_key( 'premium_table_text', 'premium_table_head_repeater', $index );

						if ( 'yes' === $head_cell['head_link_switcher'] ) {
							$html_tag = 'a';
							if ( 'url' === $head_cell['head_link_selection'] ) {
								$this->add_link_attributes( 'head-text-' . $index, $head_cell['head_link'] );
							} else {
								$this->add_render_attribute( 'head-text-' . $index, 'href', get_permalink( $head_cell['head_existing_link'] ) );
							}
						}

						$this->add_render_attribute(
							'head-cell-' . $index,
							'class',
							array(
								'premium-table-cell',
								'elementor-repeater-item-' . $head_cell['_id'],
							)
						);

						$this->add_render_attribute( 'head-text-' . $index, 'class', 'premium-table-text' );

						if ( 'top' === $head_cell['premium_table_cell_icon_align'] ) {
							$this->add_render_attribute( 'head-text-' . $index, 'class', 'premium-table-cell-top' );
						}

						if ( $head_cell['premium_table_cell_span'] > 1 ) {
							$this->add_render_attribute( 'head-cell-' . $index, 'colspan', $head_cell['premium_table_cell_span'] );
						}
						if ( $head_cell['premium_table_cell_row_span'] > 1 ) {
							$this->add_render_attribute( 'head-cell-' . $index, 'rowspan', $head_cell['premium_table_cell_row_span'] );
						}

						$head .= '<th ' . $this->get_render_attribute_string( 'head-cell-' . $index ) . '>';
						$head .= '<' . $html_tag . ' ' . $this->get_render_attribute_string( 'head-text-' . $index ) . '>';

						if ( ! empty( $head_cell['premium_table_cell_icon'] ) || ! empty( $head_cell['premium_table_cell_icon_img']['url'] ) || ! empty( $head_cell['lottie_url'] ) ) {

							$head_icon_type = $head_cell['premium_table_icon_selector'];

							$this->add_render_attribute( 'cell-icon-' . $index, 'class', 'premium-table-cell-icon-' . $head_cell['premium_table_cell_icon_align'] );
							$this->add_render_attribute( 'head-text-' . $index, 'class', 'premium-table-text' );

							$head .= '<span ' . $this->get_render_attribute_string( 'cell-icon-' . $index ) . '>';
							if ( 'font-awesome-icon' === $head_icon_type ) {
								$head .= '<i class="' . esc_attr( $head_cell['premium_table_cell_icon'] ) . '"></i>';
							} elseif ( 'custom-image' === $head_icon_type ) {
								$head .= '<img src="' . esc_attr( $head_cell['premium_table_cell_icon_img']['url'] ) . '">';
							} else {
								$this->add_render_attribute(
									'head_lottie_' . $index,
									array(
										'class'            => array(
											'premium-table-head-lottie',
											'premium-lottie-animation',
										),
										'data-lottie-url'  => $head_cell['lottie_url'],
										'data-lottie-loop' => $head_cell['lottie_loop'],
										'data-lottie-reverse' => $head_cell['lottie_reverse'],
									)
								);
								$head .= '<div ' . $this->get_render_attribute_string( 'head_lottie_' . $index ) . '></div>';
							}

							$head .= '</span>';
						}

						$head .= $head_cell['premium_table_text'];
						if ( 'yes' === $settings['premium_table_sort'] ) {
							$head .= '<span class="premium-table-sort-icon premium-icon-sort fa fa-sort"></span>';
							$head .= '<span class="premium-table-sort-icon premium-icon-sort-up fa fa-sort-up"></span>';
							$head .= '<span class="premium-table-sort-icon premium-icon-sort-down fa fa-sort-down"></span>';
						}
						$head .= '</' . $html_tag . '>';
						$head .= '</th>';

					}

					echo wp_kses_post( $head );

				}

				?>

			</tr>

		</thead>

		<?php
	}

	/**
	 * Is First Elem Row
	 *
	 * Check if the first cell type is set to row.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function is_first_elem_row() {

		$settings = $this->get_settings();

		if ( 'row' === $settings['premium_table_body_repeater'][0]['premium_table_elem_type'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Render Table Body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render_table_body() {

		$settings = $this->get_settings_for_display();

		$body = '';

		$counter = 1;

		$cell_counter = 0;

		$row_count = count( $settings['premium_table_body_repeater'] );

		$this->add_render_attribute( 'table_body', 'class', 'premium-table-body' );

		$this->add_render_attribute( 'table_row', 'class', 'premium-table-row' );

		?>
		<tbody <?php echo wp_kses_post( $this->get_render_attribute_string( 'table_body' ) ); ?>>
			<?php if ( $this->is_first_elem_row() ) { ?>
				<tr <?php echo wp_kses_post( $this->get_render_attribute_string( 'table_row' ) ); ?>
			<?php } ?>
			<?php
			foreach ( $settings['premium_table_body_repeater'] as $index => $elem ) {

				$html_tag = 'span';

				$body_cell_text = $this->get_repeater_setting_key( 'premium_table_text', 'premium_table_body_repeater', $index );

				if ( 'yes' === $elem['premium_table_link_switcher'] ) {
					$html_tag = 'a';
					if ( 'url' === $elem['premium_table_link_selection'] ) {
						$this->add_link_attributes( 'body-cell-text-' . $counter, $elem['premium_table_link'] );
					} else {
						$this->add_render_attribute( 'body-cell-text-' . $counter, 'href', get_permalink( $elem['premium_table_existing_link'] ) );
					}
				}

				if ( 'cell' === $elem['premium_table_elem_type'] ) {
					$this->add_render_attribute( 'body-cell-' . $counter, 'class', 'premium-table-cell' );
					$this->add_render_attribute( 'body-cell-' . $counter, 'class', 'elementor-repeater-item-' . $elem['_id'] );

					$this->add_render_attribute( 'body-cell-text-' . $counter, 'class', 'premium-table-text' );

					if ( 'top' === $elem['premium_table_cell_icon_align'] ) {
						$this->add_render_attribute( 'body-cell-text-' . $counter, 'class', 'premium-table-cell-top' );
					}

					if ( $elem['premium_table_cell_span'] > 1 ) {
						$this->add_render_attribute( 'body-cell-' . $counter, 'colspan', $elem['premium_table_cell_span'] );
					}
					if ( $elem['premium_table_cell_row_span'] > 1 ) {
						$this->add_render_attribute( 'body-cell-' . $counter, 'rowspan', $elem['premium_table_cell_row_span'] );
					}

					$body .= '<' . $elem['premium_table_cell_type'] . ' ' . $this->get_render_attribute_string( 'body-cell-' . $counter ) . '>';
					$body .= '<' . $html_tag . ' ' . $this->get_render_attribute_string( 'body-cell-text-' . $counter ) . '>';

					if ( ! empty( $elem['premium_table_cell_icon'] ) || ! empty( $elem['premium_table_cell_icon_img']['url'] ) || ! empty( $elem['lottie_url'] ) ) {

						$body_icon_type = $elem['premium_table_icon_selector'];

						$this->add_render_attribute( 'cell-icon-' . $counter, 'class', 'premium-table-cell-icon-' . $elem['premium_table_cell_icon_align'] );

						$body .= '<span ' . $this->get_render_attribute_string( 'cell-icon-' . $counter ) . '>';

						if ( 'font-awesome-icon' === $body_icon_type ) {
							$body .= '<i class="' . esc_attr( $elem['premium_table_cell_icon'] ) . '"></i>';
						} elseif ( 'custom-image' === $body_icon_type ) {
							$body .= '<img src="' . esc_attr( $elem['premium_table_cell_icon_img']['url'] ) . '">';
						} else {

							$this->add_render_attribute(
								'body_lottie_' . $index,
								array(
									'class'               => array(
										'premium-table-body-lottie',
										'premium-lottie-animation',
									),
									'data-lottie-url'     => $elem['lottie_url'],
									'data-lottie-loop'    => $elem['lottie_loop'],
									'data-lottie-reverse' => $elem['lottie_reverse'],
								)
							);

							$body .= '<div ' . $this->get_render_attribute_string( 'body_lottie_' . $index ) . '></div>';
						}

						$body .= '</span>';
					}
					$body .= $elem['premium_table_text'];
					// $body .= '</span>';
					$body .= '</' . $html_tag . '>';
					$body .= '</' . $elem['premium_table_cell_type'] . '>';
				} else {

					$this->add_render_attribute( 'body-row-' . $counter, 'class', 'premium-table-row' );
					$this->add_render_attribute( 'body-row-' . $counter, 'class', 'elementor-repeater-item-' . $elem['_id'] );

					if ( $counter > 1 && $counter < $row_count ) {
						$body .= '</tr><tr ' . $this->get_render_attribute_string( 'body-row-' . $counter ) . '>';

					} elseif ( 1 === $counter && false === $this->is_first_elem_row() ) {
						$body .= '<tr ' . $this->get_render_attribute_string( 'body-row-' . $counter ) . '>';
					}

					$cell_counter = 0;
				}

					$counter++;
			}

				echo wp_kses_post( $body );
			?>
			</tr>
		</tbody>

		<?php
	}

	/**
	 * Render Table output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'table_wrap', 'class', 'premium-table-wrap' );
		if ( 'yes' === $settings['premium_table_responsive'] ) {
			$this->add_render_attribute( 'table_wrap', 'class', 'premium-table-responsive' );
		}

		if ( 'yes' === $settings['premium_table_records'] && 'yes' === $settings['premium_table_search'] ) {
			$this->add_render_attribute( 'table_wrap', 'class', 'premium-table-filter-records' );
		}

		$this->add_render_attribute( 'table', 'class', 'premium-table' );

		if ( 'yes' === $settings['premium_table_search'] ) {
			$this->add_render_attribute( 'table', 'class', 'premium-table-search' );
		}

		if ( 'yes' === $settings['premium_table_blur'] ) {
			$this->add_render_attribute( 'table', 'class', 'premium-table-blur' );
		}

		if ( 'yes' === $settings['premium_table_sort'] ) {
			$this->add_render_attribute( 'table', 'class', 'premium-table-sort' );
		}

		$table_settings = array(
			'sort'       => ( 'yes' === $settings['premium_table_sort'] ) ? true : false,
			'usNumbers'  => ( 'yes' === $settings['us_numbers'] ) ? true : false,
			'sortMob'    => ( 'yes' === $settings['premium_table_sort_mob'] ) ? true : false,
			'search'     => ( 'yes' === $settings['premium_table_search'] ) ? true : false,
			'records'    => ( 'yes' === $settings['premium_table_records'] ) ? true : false,
			'dataType'   => $settings['premium_table_data_type'],
			'csvFile'    => ( 'file' === $settings['premium_table_csv_type'] ) ? $settings['premium_table_csv']['url'] : $settings['premium_table_csv_url'],
			'firstRow'   => $settings['premium_table_csv_first_row'],
			'separator'  => $settings['premium_table_separator'],
			'pagination' => $settings['pagination'],
			'rows'       => intval( $settings['rows_per_page'] ),
		);

		if ( 'csv' === $settings['premium_table_data_type'] ) {
			$table_settings['id']     = $this->get_id();
			$table_settings['reload'] = $settings['reload'];
		}

		$this->add_render_attribute( 'table', 'data-settings', wp_json_encode( $table_settings ) );

		if ( 'yes' === $settings['premium_table_search'] ) {
			$this->add_render_attribute( 'search', 'class', 'premium-table-search-field' );
			$this->add_render_attribute( 'search', 'type', 'text' );
			$this->add_render_attribute( 'search', 'placeholder', $settings['premium_table_search_placeholder'] );
		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'table_wrap' ) ); ?>>
			<?php if ( 'yes' === $settings['premium_table_search'] || 'yes' === $settings['premium_table_records'] ) : ?>
				<div class="premium-table-filter">
					<?php if ( 'yes' === $settings['premium_table_search'] ) : ?>
						<div class="premium-table-search-wrap">
							<input <?php echo wp_kses_post( $this->get_render_attribute_string( 'search' ) ); ?>>
						</div>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['premium_table_records'] && 'custom' === $settings['premium_table_data_type'] ) : ?>
						<div class="premium-table-records-wrap">
							<label class="premium-table-label-records">
								<?php echo wp_kses_post( $settings['premium_table_records_label'] ); ?>
								</label>
							<select class="premium-table-records-box">
								<?php
									$rows = 0;
								foreach ( $settings['premium_table_body_repeater'] as $element ) {
									if ( 'row' === $element['premium_table_elem_type'] ) {
										$rows++;
										if ( 1 === $rows ) {
											?>
											<option value="1" selected="selected"><?php echo esc_html( __( 'All', 'premium-addons-pro' ) ); ?></option>
										<?php } else { ?>
											<option value="<?php echo esc_attr( $rows ); ?>"><?php echo wp_kses_post( $rows - 1 ); ?></option>
											<?php
										}
									}
								}
								?>
							</select>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<table <?php echo wp_kses_post( $this->get_render_attribute_string( 'table' ) ); ?> >

			<?php
			if ( ! empty( $settings['premium_table_head_repeater'] ) ) :
				$this->render_table_head();
				endif;

			if ( ! empty( $settings['premium_table_body_repeater'] ) ) :
				$this->render_table_body();
				endif;
			?>

			</table>
		</div>
		<?php if ( 'yes' === $settings['pagination'] ) { ?>
			<div class="premium-table-pagination">
				<ul class="premium-table-pagination-list page-numbers">
					<li><a href="#" class="page-numbers prev" data-page="0">&laquo;</a></li>
					<li><a href="#" class="page-numbers next" data-page="last">&raquo;</a></li>
				</ul>
			</div>

			<?php
		}

	}

	/**
	 * Render Table widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			view.addRenderAttribute('table_wrap', 'class', 'premium-table-wrap');

			if( 'yes' == settings.premium_table_responsive ){
				view.addRenderAttribute('table_wrap', 'class', 'premium-table-responsive');
			}

			if( 'yes' === settings.premium_table_records && 'yes' === settings.premium_table_search ) {
				view.addRenderAttribute('table_wrap', 'class', 'premium-table-filter-records');
			}

			view.addRenderAttribute('table', 'class', 'premium-table');

			if(  'yes' === settings.premium_table_search ){
				view.addRenderAttribute('table', 'class', 'premium-table-search');
			}

			if(  'yes' === settings.premium_table_blur){
				view.addRenderAttribute('table', 'class', 'premium-table-blur');
			}

			if( 'yes' === settings.premium_table_sort ){
				view.addRenderAttribute('table', 'class', 'premium-table-sort');
			}

			var tableSettings = {};

			tableSettings.sort = 'yes' === settings.premium_table_sort ? true : false;
			tableSettings.usNumbers = 'yes' === settings.us_numbers ? true : false;
			tableSettings.sortMob = 'yes' === settings.premium_table_sort_mob ? true : false;
			tableSettings.search  = 'yes' === settings.premium_table_search ? true : false;
			tableSettings.records = 'yes' === settings.premium_table_records ? true : false;
			tableSettings.dataType  = settings.premium_table_data_type;
			tableSettings.csvFile   = 'file' === settings.premium_table_csv_type ? settings.premium_table_csv.url : settings.premium_table_csv_url;
			tableSettings.firstRow  = settings.premium_table_csv_first_row;
			tableSettings.separator = settings.premium_table_separator;
			tableSettings.pagination = settings.pagination;
			tableSettings.rows = settings.rows_per_page;


			if( 'csv' === settings.premium_table_data_type ) {
				tableSettings.id = view.getID();
				tableSettings.reload = settings.reload;
			}

			view.addRenderAttribute('table', 'data-settings', JSON.stringify(tableSettings));

			if( 'yes' === settings.premium_table_search ) {
				view.addRenderAttribute('search', 'class', 'premium-table-search-field' );
				view.addRenderAttribute('search', 'type', 'text' );
				view.addRenderAttribute('search', 'placeholder', settings.premium_table_search_placeholder );
			}

			function renderTableHead() {

				var head = '';

				view.addRenderAttribute('table_head', 'class', 'premium-table-head');

				view.addRenderAttribute('table_row', 'class', 'premium-table-row');

			#>

			<thead {{{ view.getRenderAttributeString('table_head') }}}>

				<tr {{{ view.getRenderAttributeString('table_row') }}}>

				<#

				if( 'custom' == settings.premium_table_data_type ) {

					_.each( settings.premium_table_head_repeater, function( headCell, index ) {

						var htmlTag  = 'span',
							headCellText = view.getRepeaterSettingKey('premium_table_text', 'premium_table_head_repeater', index);

						if( 'yes' == headCell.head_link_switcher ) {
							htmlTag = 'a';
							if( 'url' === headCell.head_link_selection ) {
								view.addRenderAttribute( 'head-text-' + index, 'href', headCell.head_link.url );
							} else {
								view.addRenderAttribute( 'head-text-' + index, 'href', headCell.head_existing_link );
							}
						}

						view.addRenderAttribute('head-cell-' + index , 'class', 'premium-table-cell');
						view.addRenderAttribute('head-cell-' + index , 'class', 'elementor-repeater-item-' + headCell._id );

						view.addRenderAttribute('head-text-' + index, 'class', 'premium-table-text');

						if( 'top' === headCell.premium_table_cell_icon_align ) {
							view.addRenderAttribute('head-text-' + index, 'class', 'premium-table-cell-top');
						}

						view.addRenderAttribute(headCellText, 'class', 'premium-table-inner');
						view.addInlineEditingAttributes(headCellText, 'basic');

						if( headCell.premium_table_cell_span > 1 ){
							view.addRenderAttribute('head-cell-' + index, 'colspan', headCell.premium_table_cell_span);
						}
						if( headCell.premium_table_cell_row_span > 1 ){
							view.addRenderAttribute('head-cell-' + index, 'rowspan', headCell.premium_table_cell_row_span);
						}
						#>
						<th {{{ view.getRenderAttributeString( 'head-cell-' + index ) }}}>
						<{{{htmlTag}}} {{{ view.getRenderAttributeString( 'head-text-' + index ) }}}>
						<# if( '' != headCell.premium_table_cell_icon || '' != headCell.premium_table_cell_icon_img.url || '' != headCell.lottie_url ){

							var headIconType = headCell.premium_table_icon_selector;

							view.addRenderAttribute('cell-icon-' + index , 'class', 'premium-table-cell-icon-' + headCell.premium_table_cell_icon_align);
							view.addRenderAttribute('head-text-' + index, 'class', 'premium-table-text');

						#>
							<span {{{ view.getRenderAttributeString( 'cell-icon-' + index ) }}}>
								<# if( headIconType === 'font-awesome-icon' ){ #>
									<i class="{{ headCell.premium_table_cell_icon }}"></i>
								<# } else if( headIconType === 'custom-image' ) { #>
									<img src="{{ headCell.premium_table_cell_icon_img.url }}">
								<# } else {
									view.addRenderAttribute( 'head_lottie_' + index, 'class', [ 'premium-table-head-lottie', 'premium-lottie-animation' ] );

									view.addRenderAttribute( 'head_lottie_' + index, 'data-lottie-url', headCell.lottie_url );
									view.addRenderAttribute( 'head_lottie_' + index, 'data-lottie-loop', headCell.lottie_loop );
									view.addRenderAttribute( 'head_lottie_' + index, 'data-lottie-reverse', headCell.lottie_reverse );
								#>
									<div {{{ view.getRenderAttributeString( 'head_lottie_' + index ) }}}></div>
								<# } #>
							</span>
						<# } #>

						<span {{{ view.getRenderAttributeString( headCellText ) }}}>{{{ headCell.premium_table_text }}}</span>
						<# if ( 'yes' === settings.premium_table_sort ) { #>
							<span class="premium-table-sort-icon premium-icon-sort fa fa-sort"></span>
							<span class="premium-table-sort-icon premium-icon-sort-up fa fa-sort-up"></span>
							<span class="premium-table-sort-icon premium-icon-sort-down fa fa-sort-down"></span>
						<# } #>
						</{{{htmlTag}}}>
						</th>

					<#
					} )
				}

				#>

				</tr>

			</thead>

			<# }

			function isFirstElemRow() {

				if ( 'row' === settings.premium_table_body_repeater[0].premium_table_elem_type )
					return false;

				return true;

			}

			function renderTableBody(){

				var counter 		= 1,
				cellCounter         = 0;
				rowCount            = settings.premium_table_body_repeater.length;

				view.addRenderAttribute('table_body', 'class', 'premium-table-body');

				view.addRenderAttribute('table_row', 'class', 'premium-table-row');

				#>

				<tbody {{{ view.getRenderAttributeString('table_body') }}}>
				<# if( isFirstElemRow() ) { #>
					<tr {{{ view.getRenderAttributeString('table_row') }}}>
				<# } #>
				<#

					_.each( settings.premium_table_body_repeater, function( bodyCell, index ) {

						var htmlTag = 'span',
							bodyCellText = view.getRepeaterSettingKey('premium_table_text', 'premium_table_body_repeater', index);


						if( 'yes' == bodyCell.premium_table_link_switcher ) {
							htmlTag = 'a';
							if( 'url' === bodyCell.premium_table_link_selection ) {
								view.addRenderAttribute('body-cell-text-' + counter, 'href', bodyCell.premium_table_link.url);
							} else {
								view.addRenderAttribute('body-cell-text-' + counter, 'href', bodyCell.premium_table_existing_link);
							}
						}

						if( 'cell' == bodyCell.premium_table_elem_type ) {

							view.addRenderAttribute('body-cell-' + counter , 'class', 'premium-table-cell');
							view.addRenderAttribute('body-cell-' + counter , 'class', 'elementor-repeater-item-' + bodyCell._id);

							view.addRenderAttribute('body-cell-text-' + counter, 'class', 'premium-table-text');

							if( 'top' == bodyCell.premium_table_cell_icon_align ){
								view.addRenderAttribute('body-cell-text-' + counter, 'class', 'premium-table-cell-top');
							}

							view.addRenderAttribute(bodyCellText, 'class', 'premium-table-inner');
							view.addInlineEditingAttributes(bodyCellText, 'basic');

							if( bodyCell.premium_table_cell_span > 1 ){
								view.addRenderAttribute('body-cell-' + counter, 'colspan', bodyCell.premium_table_cell_span);
							}
							if( bodyCell.premium_table_cell_row_span > 1 ){
								view.addRenderAttribute('body-cell-' + counter, 'rowspan', bodyCell.premium_table_cell_row_span);
							}
							#>
							<{{{bodyCell.premium_table_cell_type}}} {{{ view.getRenderAttributeString('body-cell-' + counter) }}}>
								<{{{htmlTag}}} {{{ view.getRenderAttributeString('body-cell-text-' + counter) }}} >

									<# if( '' != bodyCell.premium_table_cell_icon || '' != bodyCell.premium_table_cell_icon_img.url || '' != bodyCell.lottie_url ) {

										var bodyIconType = bodyCell.premium_table_icon_selector;

										view.addRenderAttribute('cell-icon-' + counter , 'class', 'premium-table-cell-icon-' + bodyCell.premium_table_cell_icon_align);

										#>
										<span {{{ view.getRenderAttributeString('cell-icon-' + counter) }}}>
											<# if( bodyIconType === 'font-awesome-icon' ){ #>
												<i class="{{ bodyCell.premium_table_cell_icon }}"></i>
											<# } else if( 'custom-image' === bodyIconType ) { #>
												<img src="{{ bodyCell.premium_table_cell_icon_img.url }}">
											<# } else{
												view.addRenderAttribute( 'body_lottie_' + index, 'class', [ 'premium-table-body-lottie', 'premium-lottie-animation' ] );

												view.addRenderAttribute( 'body_lottie_' + index, 'data-lottie-url', bodyCell.lottie_url );
												view.addRenderAttribute( 'body_lottie_' + index, 'data-lottie-loop', bodyCell.lottie_loop );
												view.addRenderAttribute( 'body_lottie_' + index, 'data-lottie-reverse', bodyCell.lottie_reverse );

											#>
												<div {{{ view.getRenderAttributeString( 'body_lottie_' + index ) }}}></div>
											<# } #>
										</span>
									<# } #>

										<span {{{ view.getRenderAttributeString(bodyCellText) }}}> {{{bodyCell.premium_table_text}}}</span>
									</span>
								</{{{htmlTag}}}>
							</{{{bodyCell.premium_table_cell_type}}}>

						<# } else {

							view.addRenderAttribute( 'body-row-' + counter, 'class', 'premium-table-row' );
							view.addRenderAttribute( 'body-row-' + counter, 'class', 'elementor-repeater-item-' + bodyCell._id );

							if ( counter > 1 && counter < rowCount ) { #>
								</tr><tr {{{ view.getRenderAttributeString( 'body-row-' + counter ) }}}>
							<# } else if ( counter === 1 && false === isFirstElemRow() ) { #>
								<tr {{{ view.getRenderAttributeString( 'body-row-' + counter ) }}}>
							<# }

							cellCounter = 0;

							}

							counter++;

					})

				#>

			<# }

		#>

		<div {{{ view.getRenderAttributeString('table_wrap') }}}>
			<div class="premium-table-filter">
			<# if( 'yes' == settings.premium_table_search ) { #>
				<div class="premium-table-search-wrap">
					<input {{{ view.getRenderAttributeString('search') }}}>
				</div>
			<# } #>
			<# if( 'yes' == settings.premium_table_records && 'custom' === settings.premium_table_data_type ) { #>
				<div class="premium-table-records-wrap">
					<label class="premium-table-label-records">{{{ settings.premium_table_records_label }}}</label>
					<select class="premium-table-records-box">
						<#
							var rows = 0;
							_.each( settings.premium_table_body_repeater, function( element, index ) {
								if( 'row' === element.premium_table_elem_type ) {
								rows++;
								if( 1 === rows ) { #>
									<option value="1" selected="selected"><?php echo esc_html( __( 'All', 'premium-addons-pro' ) ); ?></option>
								<# } else { #>
									<option value="{{rows}}">{{{ rows - 1 }}}</option>
								<# }
								}
							} )
						#>

					</select>
				</div>
			<# } #>
			</div>
			<table {{{ view.getRenderAttributeString('table') }}}>

			<#  if( '' != settings.premium_table_head_repeater )
					renderTableHead();
				if( '' != settings.premium_table_body_repeater )
					renderTableBody();
			#>

			</table>
		</div>
			<# if ( settings.pagination === 'yes' ) { #>
				<div class="premium-table-pagination">
					<ul class="premium-table-pagination-list page-numbers">
						<li><a href="#" class="page-numbers prev" data-page="0">&laquo;</a></li>
						<li><a href="#" class="page-numbers next" data-page="last">&raquo;</a></li>
					</ul>
				</div>
			<# } #>

		<?php
	}

}
