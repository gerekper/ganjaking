<?php 
/*
Widget Name: Table
Description: Content of table.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Data_Table extends Widget_Base {
	
	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-table';
	}

    public function get_title() {
        return esc_html__('Table', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-table theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "table";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		$this->start_controls_section( 'section_table',
			[
				'label' => esc_html__( 'Table', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control( 'table_selection',
			[
				'label' => wp_kses_post( "Content Table <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "table-elementor-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'  => esc_html__( 'Custom', 'theplus' ),
					'csv_file' => esc_html__( 'CSV File', 'theplus' ),
					'google_sheet' => esc_html__( 'Google Sheet', 'theplus' ),
				],
			]
		);
		
		$this->add_control( 'api_key',
			[
				'label' => esc_html__( 'API Key', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);
		$this->add_control( 'sheet_id',
			[
				'label' => esc_html__( 'Sheet ID', 'theplus' ),
				'type'  => Controls_Manager::TEXT,
				'dynamic' => ['active' => true,],
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);
		$this->add_control( 'table_range',
			[
				'label' => esc_html__( 'Table Range', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true
				],
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);
		$this->add_control('google_sheet_doc',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "comparison-data-table-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Import Data From Google Sheets <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);
		$this->add_control( 'TimeFrq',
			[
				'label' => esc_html__( 'Refresh Time','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '86400',
				'options' => [
					'3600' => esc_html__( '1 hour','theplus' ),	
					'7200' => esc_html__( '2 hour','theplus' ),
					'21600' => esc_html__( '6 hour','theplus' ),
					'86400' => esc_html__( '1 day','theplus' ),
					'604800' => esc_html__( '1 Week','theplus' ),
				],
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);

		$this->add_control( 'delete_transient',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<span>Delete All Transient </span><a class="tp-table-delete-transient" id="tp-table-delete-transient"> Delete </a>',
				'content_classes' => 'tp-table-delete-transient-btn',
				'label_block' => true,
				'condition' => [
					'table_selection' => 'google_sheet',
				],
			]
		);

		$this->add_control('file',
			[
				'label' => wp_kses_post( "Enter a CSV File URL <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "import-data-from-csv-in-elementor-table/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::URL,
				'show_external' => false,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => THEPLUS_ASSETS_URL . 'images/table.csv',
				],
				'condition'     => [
					'table_selection' => 'csv_file',
				],
			]
		);
		$this->add_control('how_it_works',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "comparison-data-table-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> Learn how to create Comparison Table <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_table_header',
			[
				'label' => esc_html__( 'Table Header', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'     => [
					'table_selection' => 'custom',
				],
			]
		);
		// Repeater object created.
		$repeater = new \Elementor\Repeater();

		// Content Type Row/Col.
		$repeater->add_control(
			'header_content_type',
			[
				'label'   => esc_html__( 'Action', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cell',
				'options' => [
					'row'  => esc_html__( 'Start New Row', 'theplus' ),
					'cell' => esc_html__( 'Cell Content', 'theplus' ),
				],
			]
		);
		
		// Table TH heading Row/Cell Note.
		$repeater->add_control(
			'add_head_cell_row_description',
			[
				'label'     => '',
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Your new row have been initiated. Add content of cells by selecting <b>"Cell Content"</b> in your next repeater tab.', 'theplus' ) ),
				'condition' => [
					'header_content_type' => 'row',
				],
			]
		);
		$repeater->start_controls_tabs( 'items_repeater' );

		// Start control Table content.
		$repeater->start_controls_tab(
			'tab_head_content',
			[
				'label'     => esc_html__( 'CONTENT', 'theplus' ),
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);
		$repeater->add_control(
			'heading_text',
			[
				'label'     => esc_html__( 'Text', 'theplus' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic' => ['active'   => true,],
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);
		$repeater->add_control(
			'heading_show_tooltips',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Enable', 'theplus' ),
				'label_off'    => esc_html__( 'Disable', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'heading_show_tooltips_on',
			[
				'label' => esc_html__( 'Tooltip On', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'box',
				'options' => [
					'box'  => esc_html__( 'Box', 'theplus' ),
					'icon'  => esc_html__( 'Icon', 'theplus' ),
				],
				'condition' => [
					'heading_show_tooltips' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'heading_tooltip_content',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'heading_show_tooltips' => 'yes',
				],
			]				
		);
		$repeater->end_controls_tab();

		// Start control content.
		$repeater->start_controls_tab(
			'tab_head_icon',
			[
				'label'     => esc_html__( 'ICON / IMAGE', 'theplus' ),
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);
		$repeater->add_control(
			'header_content_icon_image',
			[
				'label'   => esc_html__( 'Select', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'icons_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => ['active'   => true,],
				'condition' => [					
					'header_content_icon_image' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'icons_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [					
					'header_content_icon_image' => 'image',
				],
			]
		);
		$repeater->add_control(
			'icon_font_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [					
					'header_content_icon_image' => 'icon',
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-bank',
				'condition' => [			
					'header_content_icon_image' => 'icon',
					'icon_font_style' => 'font_awesome',
				],
			]
		);
		$repeater->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [				
					'header_content_icon_image' => 'icon',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$repeater->end_controls_tab();
		// Start control content.
		$repeater->start_controls_tab(
			'tab_head_advance',
			[
				'label'     => esc_html__( 'ADVANCE', 'theplus' ),
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);
		$repeater->add_control(
			'heading_col_span',
			[
				'label'     => esc_html__( 'Column Span', 'theplus' ),
				'title'     => esc_html__( 'Number of columns for this column span COLSPAN.', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);

		// Column row Span.
		$repeater->add_control(
			'heading_row_span',
			[
				'label'     => esc_html__( 'Row Span', 'theplus' ),
				'title'     => esc_html__( 'Number of rows for this column span ROWSPAN. Note : Put Row Span first and Column Span second in list.', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'separator' => 'below',
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);

		// Column row Span.
		$repeater->add_control(
			'heading_row_width',
			[
				'label'      => esc_html__( 'Column Width', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'separator'  => 'below',
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.plus-table-col' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'header_content_type' => 'cell',
				],
			]
		);

		// Single Header Text Color.
		$repeater->add_control(
			'single_heading_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table-row {{CURRENT_ITEM}} .plus-table__text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);

		// Single Header Background Color.
		$repeater->add_control(
			'single_heading_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'header_content_type' => 'cell',
				],
			]
		);

		$repeater->add_responsive_control('cell_align_head_indi',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'header_content_type' => 'cell',
				],
				'selectors' => [
					'{{WRAPPER}} th{{CURRENT_ITEM}} span' => 'justify-content:{{VALUE}};',
				],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tab();
		
		$this->add_control(
			'table_headings',
			[
				'type'        => Controls_Manager::REPEATER,
				'show_label'  => true,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{ header_content_type }}: {{{ heading_text }}}',
				'default'     => [
					[
						'header_content_type' => 'row',
					],
					[
						'header_content_type' => 'cell',
						'heading_text'        => esc_html__( 'ID', 'theplus' ),
					],
					[
						'header_content_type' => 'cell',
						'heading_text'        => esc_html__( 'Title 1', 'theplus' ),
					],
					[
						'header_content_type' => 'cell',
						'heading_text'        => esc_html__( 'Title 2', 'theplus' ),
					],
				],
			]
		);		
		$this->end_controls_section();
		/*Table Header*/
		
		/*Table Content*/		
		$this->start_controls_section(
			'section_table_content',
			[
				'label'     => esc_html__( 'Table Body', 'theplus' ),
				'condition'     => [
					'table_selection' => 'custom',
				],
			]
		);

		$repeater_row_col = new \Elementor\Repeater();

		// Content Type Row/Col.
		$repeater_row_col->add_control(
			'content_type',
			[
				'label'   => esc_html__( 'Action', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cell',
				'options' => [
					'row'  => esc_html__( 'Start New Row', 'theplus' ),
					'cell' => esc_html__( 'Cell Content', 'theplus' ),
				],
			]
		);

		// Table heading border Row/Cell Note.
		$repeater_row_col->add_control(
			'add_body_cell_row_description',
			[
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Your new row have been initiated. Add content of cells by selecting <b>"Cell Content"</b> in your next repeater tab.', 'theplus' ) ),
				'condition' => [
					'content_type' => 'row',
				],
			]
		);

		// Start control tab.
		$repeater_row_col->start_controls_tabs( 'items_repeater' );

		$repeater_row_col->start_controls_tab(
			'tab_content',
			[
				'label'     => esc_html__( 'Content', 'theplus' ),
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);

		// Single Column text.
		$repeater_row_col->add_control(
			'cell_text',
			[
				'label'     => esc_html__( 'Text', 'theplus' ),
				'type'      => Controls_Manager::TEXTAREA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);

		// Single Column LINK.
		$repeater_row_col->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'theplus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#',
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => '',
				],
				'condition'   => [
					'content_type' => 'cell',
				],
			]
		);
		$repeater_row_col->add_control(
			'cell_display_button',
			[
				'label' => wp_kses_post( "Button <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "insert-button-inside-elementor-table/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition'   => [
					'content_type' => 'cell',
				],
				'separator' => 'before',
			]
		);
		$repeater_row_col->add_control(
			'cell_button_style', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Button Style', 'theplus'),
				'default' => 'style-8',
				'options' => [						
					'style-8' => esc_html__('Style 1', 'theplus'),							
				],
				'condition' => [
					'content_type' => 'cell',
					'cell_display_button' => 'yes',
				],
			]
		);
		$repeater_row_col->add_control(
			'cell_button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click here', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_type' => 'cell',
					'cell_display_button' => 'yes',
				],
			]
		);
		$repeater_row_col->add_control(
			'cell_button_link',
			[
				'label' => esc_html__( 'URL/Link', 'theplus' ),
				'type' => Controls_Manager::URL,						
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_type' => 'cell',
					'cell_display_button' => 'yes',
				],
			]
		);
		$repeater_row_col->add_control(
			'button_custom_attributes',
			[
				'label'     => __( 'Add Custom Attributes', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'content_type' => 'cell',
					'cell_display_button' => 'yes',
				],
			]
		);

		$repeater_row_col->add_control(
			'custom_attributes',
			[
				'label' => __( 'Custom Attributes', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'theplus' ),				
				'condition' => [
					'content_type' => 'cell',
					'cell_display_button' => 'yes',
					'button_custom_attributes' => 'yes'
				]
			]
		);
		$repeater_row_col->add_control(
			'body_show_tooltips',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Enable', 'theplus' ),
				'label_off'    => esc_html__( 'Disable', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$repeater_row_col->add_control(
			'body_show_tooltips_on',
			[
				'label' => esc_html__( 'Tooltip On', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'box',
				'options' => [
					'box'  => esc_html__( 'Box', 'theplus' ),
					'icon'  => esc_html__( 'Icon', 'theplus' ),
				],
				'condition' => [
					'body_show_tooltips' => 'yes',
				],
			]
		);
		$repeater_row_col->add_control(
			'body_tooltip_content',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'body_show_tooltips' => 'yes',
				],
			]				
		);
		$repeater_row_col->end_controls_tab();

		// Start Icon/Image Tab.
		$repeater_row_col->start_controls_tab(
			'tab_media',
			[
				'label'     => esc_html__( 'ICON / IMAGE', 'theplus' ),
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);

		// Type Icon/Image.
		$repeater_row_col->add_control(
			'cell_content_icon_image',
			[
				'label'   => esc_html__( 'Select', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
				],
			]
		);
				
		$repeater_row_col->add_control(
			'icon_font_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'content_type'            => 'cell',
					'cell_content_icon_image' => 'icon',
				],
			]
		);
		
		$repeater_row_col->add_control(
			'cell_icon',
			[
				'label'       => esc_html__( 'Icon', 'theplus' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => false,
				'default'     => '',
				'condition'   => [
					'content_type'            => 'cell',
					'icon_font_style'            => 'font_awesome',
					'cell_content_icon_image' => 'icon',
				],
			]
		);
		$repeater_row_col->add_control(
			'cell_icons_mind',
			[
				'label' => esc_html__( 'Icon Minds', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [				
					'content_type'            => 'cell',
					'cell_content_icon_image' => 'icon',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$repeater_row_col->add_control(
			'cell_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [				
					'content_type'            => 'cell',
					'cell_content_icon_image' => 'icon',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-row td.plus-table-col{{CURRENT_ITEM}} .plus-table__text i' => 'color: {{VALUE}};',
				],
			]
		);
		// Image.
		$repeater_row_col->add_control(
			'image',
			[
				'label' => wp_kses_post( "Choose Image <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "insert-images-in-table-content-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'content_type' => 'cell',
					'cell_content_icon_image' => 'image',
				],
			]
		);
		$repeater_row_col->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'content_type'            => 'cell',
					'cell_content_icon_image' => 'image',
				],
			]
		);

		$repeater_row_col->end_controls_tab();

		$repeater_row_col->start_controls_tab(
			'tab_advance_cells',
			[
				'label'     => esc_html__( 'Advance', 'theplus' ),
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);
		$repeater_row_col->add_responsive_control(
			'cell_align',
			[
				'label'     => esc_html__( 'Text Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'content_type' => 'cell',
				],
				'selectors' => [
					'{{WRAPPER}} td{{CURRENT_ITEM}} .plus-table__text,{{WRAPPER}} td{{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		
		$repeater_row_col->add_control(
			'cell_span',
			[
				'label'     => esc_html__( 'Column Span', 'theplus' ),
				'title'     => esc_html__( 'Number of columns for this column span.', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);
			
		$repeater_row_col->add_control(
			'cell_row_span',
			[
				'label'     => esc_html__( 'Row Span', 'theplus' ),
				'title'     => esc_html__( 'Number of rows for this column span.', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'separator' => 'below',
				'condition' => [
					'content_type' => 'cell',
				],
			]
		);

		$repeater_row_col->add_control(
			'table_th_td',
			[
				'label'       => esc_html__( 'Mark this cell as a Table Heading?', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'td' => esc_html__( 'No', 'theplus' ),
					'th' => esc_html__( 'Yes', 'theplus' ),
				],
				'default'     => 'td',
				'condition'   => [
					'content_type' => 'cell',
				],
				'label_block' => true,
			]
		);

		$repeater_row_col->end_controls_tab();

		$repeater_row_col->end_controls_tabs();

		// Repeater default values.
		$this->add_control(
			'table_content',
			[
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'content_type' => 'row',
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Sample #1', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 1, Content 1', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 1, Content 2', 'theplus' ),
					],
					[
						'content_type' => 'row',
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Sample #2', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 2, Content 1', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 2, Content 2', 'theplus' ),
					],
					[
						'content_type' => 'row',
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Sample #3', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 3, Content 1', 'theplus' ),
					],
					[
						'content_type' => 'cell',
						'cell_text'    => esc_html__( 'Row 3, Content 2', 'theplus' ),
					],
				],
				'fields'      => $repeater_row_col->get_controls(),
				'title_field' => '{{ content_type }}: {{{ cell_text }}}',
			]
		);

		$this->end_controls_section();
		/*Table Content*/
		
		/*Table Extra Option*/
		$this->start_controls_section(
			'section_advance_settings',
			[
				'label' => esc_html__( 'Extra Settings', 'theplus' ),
			]
		);
		$this->add_control('scrollbar',
			[
				'label' => wp_kses_post( "Table Vertical Scrollbar <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-vertical-scrollbar-to-elementor-table/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_responsive_control( 'height',
			[
				'label' => esc_html__( 'Height (px)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1024,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper' => 'height: {{SIZE}}{{UNIT}};overflow-y: scroll;',
				],
				'condition' => [
					'scrollbar' => 'yes',
				],
			]
		);
		$this->add_control(
			'searchable',
			[
				'label' => wp_kses_post( "Table Searchable <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-a-search-in-elementor-table/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'separator'	=> 'before',
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'searchable_label',
			[
				'label' => esc_html__( 'Search Field Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', 'theplus' ),
				'dynamic' => [ 
					'active' => true 
				],
				'condition' => [
					'searchable' => 'yes',
				],
			]
		);
		$this->add_control(
			'sortable',
			[
				'label' => wp_kses_post( "Table Sortable <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "enable-sorting-in-elementor-tables/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'theplus' ),
				'label_off'    => esc_html__( 'Off', 'theplus' ),					
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'	=> 'before',
			]
		);

		
		$this->add_control(
			'show_entries',
			[
				'label' => wp_kses_post( "Entry Filter Dropdown <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "limit-the-number-of-rows-in-elementor-table/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'description'  => esc_html__( 'Controls the number of entries in a table.', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'theplus' ),
				'label_off'    => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'	=> 'before',
			]
		);
		$this->add_control(
			'mobile_responsive_table',
			[
				'label' => wp_kses_post( "Mobile Responsive <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "make-data-tables-mobile-responsive-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Swipe Responsive', 'theplus' ),
					'one-by-one' => esc_html__( 'One by One Responsive', 'theplus' ),
				],
				'separator'	=> 'before',
			]
		);

		$this->end_controls_section();
		/*Table Extra Option*/
		
		/*Table Header Style*/
		$this->start_controls_section(
			'section_header_style',
			[
				'label' => esc_html__( 'Table Header', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'cell_align_head_normal',
			[
				'label'     => esc_html__( 'Text Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition'   => [
					'table_selection' => 'custom',
				],
			]
		);
		$this->add_responsive_control(
			'cell_align_head',
			[
				'label'     => esc_html__( 'Text Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition'   => [
						'table_selection' => 'csv_file',
				],
				'selectors' => [
					'{{WRAPPER}} th .plus-table__text,{{WRAPPER}} th' => 'text-align: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_typography',
				'label'    => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} th.plus-table-col',
			]
		);

		$this->add_responsive_control(
			'cell_padding_head',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '15',
					'bottom'   => '15',
					'left'     => '15',
					'right'    => '15',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} th.plus-table-col' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_header_colors_row' );

		$this->start_controls_tab( 'tab_header_colors_row', 
			[ 
				'label' => esc_html__( 'Normal', 'theplus' ) 
			] 
		);

		$this->add_control(
			'header_cell_color_row',
			[
				'label'     => esc_html__( 'Row Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_TEXT
                ],
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row th .plus-table__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} th' => 'color: {{VALUE}};',
					'{{WRAPPER}} tbody .plus-table-row th' => 'color: {{VALUE}};',							
				],
			]
		);


		$this->add_control(
			'header_cell_background_row',
			[
				'label'     => esc_html__( 'Row Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} tbody .plus-table-row th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_border_styling',
			[
				'label'        => esc_html__( 'Apply Border To', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'CELL', 'theplus' ),
				'label_off'    => esc_html__( 'ROW', 'theplus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'plus-border-',
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'row_border_head',
				'label'          => esc_html__( 'Row Border', 'theplus' ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'selector'       => '{{WRAPPER}} thead tr.plus-table-row, {{WRAPPER}} tbody .plus-table-row th',
				'condition'      => [
					'header_border_styling!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'cell_border_head',
				'label'          => esc_html__( 'Cell Border', 'theplus' ),
				'selector'       => '{{WRAPPER}} th.plus-table-col',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'condition'      => [
					'header_border_styling' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_header_hover_colors_row', 
			[ 
				'label' => esc_html__( 'Hover', 'theplus' ) 
			] 
		);

		$this->add_control(
			'header_cell_hover_color_row',
			[
				'label'     => esc_html__( 'Row Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row:hover .plus-table__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} tbody .plus-table-row:hover th .plus-table__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-table-row:hover th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_cell_hover_background_row',
			[
				'label'     => esc_html__( 'Row Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row:hover > th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .plus-table tbody .plus-table-row:hover > th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_cell_hover_color',
			[
				'label'     => esc_html__( 'Cell Hover Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead th.plus-table-col:hover .plus-table__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} tbody .plus-table-row th.plus-table-col:hover .plus-table__text' => 'color: {{VALUE}};',
					'{{WRAPPER}} tr.plus-table-row th.plus-table-col:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_cell_hover_background',
			[
				'label'     => esc_html__( 'Cell Hover Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} thead .plus-table-row th.plus-table-col:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .plus-table tbody .plus-table-row:hover >  th.plus-table-col:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/*Table Header Style*/
		
		/*Table Header Mobile Style*/
		$this->start_controls_section(
			'section_table_mobile_res_style',
			[
				'label' => esc_html__( 'Header Mobile Responsive Style', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'      => [
					'mobile_responsive_table' => 'one-by-one',
				],
			]
		);
		
		$this->add_control(
			'mob_cell_align_head',
			[
				'label'     => esc_html__( 'Text Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row' => 'text-align: {{VALUE}};width: 100%;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'mob_header_typography',
				'label'    => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row',
			]
		);
		$this->add_responsive_control(
			'mob_cell_padding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '15',
					'bottom'   => '15',
					'left'     => '15',
					'right'    => '15',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row,{{WRAPPER}} .plus-table-mob-res .plus-table-mob-wrap span.plus-table__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'mob_cell_head_width',
			[
				'label' => esc_html__( 'Heading Cell Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 120,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res .plus-table-mob-wrap span.plus-table-mob-row' => '-webkit-flex-basis: {{SIZE}}{{UNIT}};-ms-flex-preferred-size: {{SIZE}}{{UNIT}};flex-basis: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->start_controls_tabs( 'tabs_mob_head_colors_row' );

		
		$this->start_controls_tab( 'tab_mob_head_colors_row', 
			[ 
				'label' => esc_html__( 'Normal', 'theplus' )
			]
		);

		$this->add_control(
			'mob_head_cell_color_row',
			[
				'label'     => esc_html__( 'Heading Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_TEXT
                ],
				'selectors' => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mob_head_cell_background_row',
			[
				'label'     => esc_html__( 'Heading Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row' => 'background-color: {{VALUE}};',		
				],
			]
		);
		$this->add_responsive_control(
			'mob_cell_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'devices' => [ 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody tr td.plus-table-col' => 'border-bottom-width: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .plus-table-mob-wrap span.plus-table-mob-row' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody  tr.plus-table-row' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'mob_cell_border_color',
			[
				'label'     => esc_html__( 'Cell Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'devices' => [ 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody tr td.plus-table-col' => 'border-bottom-color: {{VALUE}} !important;',				
					'{{WRAPPER}} .plus-table-mob-wrap span.plus-table-mob-row' => 'border-right-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'mob_row_border_color',
			[
				'label'     => esc_html__( 'Row Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'devices' => [ 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody  tr.plus-table-row' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'mob_row_space',
			[
				'label' => esc_html__( 'Row Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'mobile_default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'devices' => [ 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody  tr.plus-table-row' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody  tr.plus-table-row:last-child' => 'margin-bottom: 0px;',
				],
			]
		);
		$this->add_responsive_control(
			'mob_row_border_radius',
			[
				'label' => esc_html__( 'Row Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'devices' => [ 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table.plus-table-mob-res tbody  tr.plus-table-row' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
			
		$this->start_controls_tab( 'tab_mob_head_hover_colors_row', 
			[
				'label' => esc_html__( 'Hover', 'theplus' ) 
			]
		);

		$this->add_control(
			'mob_head_cell_hover_color_row',
			[
				'label'     => esc_html__( 'Heading Hover Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mob_head_cell_hover_background_row',
			[
				'label'     => esc_html__( 'Heading Hover Background', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table-mob-res span.plus-table-mob-row:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Table Header Mobile Style*/
		
		/*Table Body Style*/
		$this->start_controls_section(
			'section_table_body_style',
			[
				'label' => esc_html__( 'Table Body', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'cell_align',
			[
				'label'     => esc_html__( 'Text Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} td .plus-table__text,{{WRAPPER}} td' => 'text-align: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'cell_valign',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'theplus' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'middle',
				'options'   => [
					'top'    => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'theplus' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-row .plus-table-col' => 'vertical-align: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cell_typography',
				'label'    => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} td .plus-table__text-inner,{{WRAPPER}} td .plus-align-icon--left,{{WRAPPER}} td .plus-align-icon--right,
				{{WRAPPER}} td',
			]
		);

		$this->add_responsive_control(
			'cell_padding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '15',
					'bottom'   => '15',
					'left'     => '15',
					'right'    => '15',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} td.plus-table-col' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_cell_colors' );

		$this->start_controls_tab( 'tab_cell_colors', [ 'label' => esc_html__( 'Normal', 'theplus' ) ] );

		$this->add_control(
			'cell_color',
			[
				'label' => esc_html__( 'Row Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_TEXT
                ],
				'selectors' => [
					'{{WRAPPER}} tbody td.plus-table-col .plus-table__text,{{WRAPPER}} tbody td.plus-table-col' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'striped_effect_feature',
			[
				'label'        => esc_html__( 'Stripped Effect', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'YES', 'theplus' ),
				'label_off'    => esc_html__( 'NO', 'theplus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'striped_effect_odd',
			[
				'label'     => esc_html__( 'Stripe Rows Color 1', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eaeaea',
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(odd)' => 'background: {{VALUE}};',
				],
				'condition' => [
					'striped_effect_feature' => 'yes',
				],
			]
		);

		$this->add_control(
			'striped_effect_even',
			[
				'label'     => esc_html__( 'Stripe Rows Color 2', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} tbody tr:nth-child(even)' => 'background: {{VALUE}};',
				],
				'condition' => [
					'striped_effect_feature' => 'yes',
				],
			]
		);

		$this->add_control(
			'cell_background',
			[
				'label'     => esc_html__( 'Row Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody .plus-table-row' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'striped_effect_feature!' => 'yes',
				],
			]
		);

		$this->add_control(
			'body_border_styling',
			[
				'label'        => esc_html__( 'Apply Border To', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'CELL', 'theplus' ),
				'label_off'    => esc_html__( 'ROW', 'theplus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'row_border',
				'label'          => esc_html__( 'Border', 'theplus' ),
				'selector'       => '{{WRAPPER}} tbody .plus-table-row',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'condition'      => [
					'body_border_styling!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'cell_border_body',
				'label'          => esc_html__( 'Cell Border', 'theplus' ),
				'selector'       => '{{WRAPPER}} td.plus-table-col',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'condition'      => [
					'body_border_styling' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_cell_hover_colors',
			[ 
				'label' => esc_html__( 'Hover', 'theplus' ) 
			]
		);
		$this->add_control(
			'row_hover_color',
			[
				'label'     => esc_html__( 'Row Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody .plus-table-row:hover td.plus-table-col .plus-table__text,
					{{WRAPPER}} tbody .plus-table-row:hover td.plus-table-col' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'row_hover_background',
			[
				'label'     => esc_html__( 'Row Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} tbody .plus-table-row:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} tbody .plus-table-row:hover > .plus-table-col:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cell_hover_color',
			[
				'label'     => esc_html__( 'Cell Hover Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table tbody td.plus-table-col:hover .plus-table__text,
					{{WRAPPER}} .plus-table tbody td.plus-table-col:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cell_hover_background',
			[
				'label'     => esc_html__( 'Cell Hover Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-table tbody .plus-table-row:hover > td.plus-table-col:hover' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tbody_button_heading',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Button Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '15',
							'right' => '30',
							'bottom' => '15',
							'left' => '30',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-col .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_responsive_control(
			'button_width',
			[
				'label' => esc_html__( 'Button Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 2,
					],
				],
				//'devices' => [ 'tablet', 'mobile' ],
				'tablet_default' => [
					'size' => 120,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 120,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-table-col .pt_plus_button .button-link-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .plus-table-col .pt_plus_button .button-link-wrap',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		
		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',					
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'separator' => 'after',	
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_control(
			'button_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_responsive_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
		'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'separator' => 'after',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_control(
			'btn_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover' => 'color: {{VALUE}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'separator' => 'after',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-color: {{VALUE}};',
				],
				'separator' => 'after',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Table Body Style*/
		
		$this->start_controls_section(
			'section_icon_image_style',
			[
				'label'     => esc_html__( 'Icon / Image Options', 'theplus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
			'icon_styling_heading',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type'  => Controls_Manager::HEADING,
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
			'all_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-align-icon--left i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-align-icon--right i' => 'color: {{VALUE}};',					
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'all_icon_size',
			[
				'label'     => esc_html__( 'Icon Size', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 30,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					// Item.
					'{{WRAPPER}} .plus-align-icon--left i' => 'font-size: {{SIZE}}px;    vertical-align: middle;',
					'{{WRAPPER}} .plus-align-icon--right i' => 'font-size: {{SIZE}}px;vertical-align: middle;',					
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
			'all_icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Before', 'theplus' ),
					'right' => esc_html__( 'After', 'theplus' ),
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'all_icon_indent',
			[
				'label'     => esc_html__( 'Icon Spacing', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-align-icon--left'  => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .plus-align-icon--right' => 'margin-left: {{SIZE}}px;',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
			'image_styling_heading',
			[
				'label'     => esc_html__( 'Image', 'theplus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'all_image_size',
			[
				'label'      => esc_html__( 'Image Size', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 30,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-col-img--left'  => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-col-img--right' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_control(
			'all_image_align',
			[
				'label'   => esc_html__( 'Image Position', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Before', 'theplus' ),
					'right' => esc_html__( 'After', 'theplus' ),
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'all_image_indent',
			[
				'label'     => esc_html__( 'Image Spacing', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					// Item.
					'{{WRAPPER}} .plus-col-img--left'  => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .plus-col-img--right' => 'margin-left: {{SIZE}}px;',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->add_responsive_control(
			'all_image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-col-img--left'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .plus-col-img--right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'     => [
					'table_selection!' => 'csv_file',
				],
			]
		);

		$this->end_controls_section();
		/* Icon/Image Style*/
		
		/* Search Style*/		
		$this->start_controls_section(
			'section_search_style',
			[
				'label' => esc_html__( 'Search Bar / Show Entries', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Label Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-advance-heading label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_color',
			[
				'label'     => esc_html__( 'Input Value Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .plus-advance-heading label, {{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input',
			]
		);
		$this->add_control(
			'label_bg_color',
			[
				'label'     => esc_html__( 'Input Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'input_padding',
			[
				'label'      => esc_html__( 'Input Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '10',
					'bottom'   => '10',
					'left'     => '10',
					'right'    => '10',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'input_border',
				'label'          => esc_html__( 'Input Border', 'theplus' ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
					],
					'color'  => [
						'default' => '#bbb',
					],
				],
				'selector'       => '{{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input',
			]
		);
		$this->add_responsive_control(
			'input_border_radius',
			[
				'label'      => esc_html__( 'Input Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '2',
					'bottom'   => '2',
					'left'     => '2',
					'right'    => '2',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-advance-heading select, {{WRAPPER}} .plus-advance-heading input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'search_input_size',
			[
				'label'     => esc_html__( 'Search Bar Width', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 200,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 400,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-advance-heading .plus-tbl-search-wrapper input' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'entry_page_input_size',
			[
				'label'     => esc_html__( 'Show Entries Width', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 200,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 400,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .plus-advance-heading .plus-tbl-entry-wrapper select' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'bottom_spacing',
			[
				'label'     => esc_html__( 'Bottom Space', 'theplus' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
					'unit' => 'px',
				],
				'selectors' => [
					// Item.
					'{{WRAPPER}} .plus-advance-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
		/* Search Style*/
		
		/* Scroll Bar Option start*/
		$this->start_controls_section('ScrollBarTab',
			[
				'label' => esc_html__( 'Scroll Bar', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'scrollbar' => 'yes',
				],
			]
		);
		$this->add_control( 'ContentScroll',
			[
				'label' => esc_html__( 'Content Scrolling Bar', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->start_controls_tabs( 'scrollC_style' );
		$this->start_controls_tab( 'scrollC_Bar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'ScrollBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar',
			]
		);
		$this->add_responsive_control('ScrollWidth',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('scrollC_Tmb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'ThumbBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control('ThumbBrs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ThumbBsw',
				'selector' => '{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-thumb',		
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('scrollC_Trk',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'TrackBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control( 'TrackBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'TrackBsw',
				'selector' => '{{WRAPPER}} .plus-table-wrapper::-webkit-scrollbar-track',		
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*tooltip option start*/
		$this->start_controls_section(
            'section_tooltip_option_styling',
            [
                'label' => esc_html__('Tooltip Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			[
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_common_option',
			]
		);
		$this->add_group_control(
			\Theplus_Tooltips_Option_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Tooltip Style', 'theplus' ),
				'name'           => 'tooltip_common_style',
			]
		);
		$this->add_control(
			'tt_on_icon',
			[
				'label' => esc_html__( 'Tooltip Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-info-circle',
					'library' => 'solid',
				],
			]
		);
		$this->add_control(
            'tt_on_icon_margin_left',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Offset', 'theplus'),
				'range' => [
					'' => [
						'min'	=> 1,
						'max'	=> 50,
						'step' => 1,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'render_type' => 'ui',
				'selectors'  => [
					'{{WRAPPER}} .tp-tooltip-on-icon' => 'margin-left: {{SIZE}}px;',
				],
            ]
        );
		$this->add_control(
			'tt_on_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-tooltip-on-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-tooltip-on-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
            'tt_on_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'range' => [
					'' => [
						'min'	=> 1,
						'max'	=> 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors'  => [
					'{{WRAPPER}} .tp-tooltip-on-icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .tp-tooltip-on-icon svg' => 'width: {{SIZE}}px;height: {{SIZE}}px;',
				],
            ]
        );
		$this->end_controls_section();
		/*tooltip option end*/
		
		/* table background option*/
		$this->start_controls_section(
            'section_table_option_styling',
            [
                'label' => esc_html__('Table Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'tos_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],				
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper .dataTables_wrapper,{{wrapper}} .plus-table-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'tos_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],				
				'selectors' => [
					'{{WRAPPER}} .plus-table-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tos_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-table-wrapper',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tos_border_check',
			[
				'label' => esc_html__( 'Display Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tos_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-table-wrapper',
				'condition' => [
					'tos_border_check' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tos_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-table-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'tos_border_check' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tos_box_shadow',
				'selector' => '{{WRAPPER}} .plus-table-wrapper .plus-table',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'table_overflow',
			[
				'label' => esc_html__( 'Overflow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'selectors'  => [
					'{{WRAPPER}} .plus-table-wrapper .plus-table' => 'overflow:visible;',
				],
			]
		);
		$this->end_controls_section();
		/*table background option*/
		$this->start_controls_section(
            'section_animation_styling',
            [
                'label' => esc_html__('On Scroll View Animation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'animation_effects',
			[
				'label'   => esc_html__( 'In Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_animation_options(),				
			]
		);
		$this->add_control(
            'animation_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_duration_default',
            [
				'label'   => esc_html__( 'Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animate_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_duration_default' => 'yes',
				],
            ]
        );
		$this->add_control(
			'animation_out_effects',
			[
				'label'   => esc_html__( 'Out Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_out_animation_options(),
				'separator' => 'before',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Out Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_out_duration_default',
            [
				'label'   => esc_html__( 'Out Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
					'animation_out_duration_default' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
	}
	
	 protected function render() {
        $settings = $this->get_settings_for_display();
		$widget_id   = $this->get_id();
		$is_editor_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
		
		$mResponsive = !empty($settings["mobile_responsive_table"]) ? $settings["mobile_responsive_table"] : 'default';
		$sortable = !empty($settings["sortable"]) ? $settings["sortable"] : '';
		$showEntries = !empty($settings['show_entries']) ? $settings['show_entries'] : '';
		$searchable = !empty($settings['searchable']) ? $settings['searchable'] : '';
		$searchableLabel = !empty($settings['searchable_label']) ? $settings['searchable_label'] : '';
		$tableSelection = !empty($settings['table_selection']) ? $settings['table_selection'] : '';

		$cell_align_head_desktop = !empty($settings["cell_align_head_normal"]) ? $settings["cell_align_head_normal"] : '';
		$cell_align_head_tablet = !empty($settings["cell_align_head_normal_tablet"]) ? $settings["cell_align_head_normal_tablet"] : '';
		$cell_align_head_mobile = !empty($settings["cell_align_head_normal_mobile"]) ? $settings["cell_align_head_normal_mobile"] : '';
		
		$tmdefaultclass = '';
		if(!empty($mResponsive) && $mResponsive == 'default' ){
			$tmdefaultclass = ' tp-table-mobresswipe';
		}

		/*--On Scroll View Animation ---*/
			
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
	
		ob_start();
		
		//Table Wrapper
		$this->add_render_attribute( 'plus_table_wrapper', 'class', 'plus-table-wrapper '.esc_attr($animated_class). ' '.$tmdefaultclass );

		if( !empty($animation_attr) ){
			$this->add_render_attribute( 'plus_table_wrapper', $animation_attr );
		}

		$this->add_render_attribute( 'plus_table_wrapper', 'itemtype', 'http://schema.org/Table' );
		
		$this->add_render_attribute( 'plus_table_id', 'id', 'plus-table-id-' . $widget_id );
		$this->add_render_attribute( 'plus_table_id', 'class', 'plus-table' );
		$this->add_render_attribute( 'plus_table_id', 'class', 'plus-text-break' );
		$this->add_render_attribute( 'plus_table_id', 'class', 'plus-column-rules' );
		

		if(!empty($mResponsive) && $mResponsive == 'one-by-one' ){
			$this->add_render_attribute( 'plus_table_id', 'class', 'plus-table-mob-res' );
		}
		
		// <Tr> (Row).
		$this->add_render_attribute( 'plus_table_row', 'class', 'plus-table-row' );
		
		// Text span.
		$this->add_render_attribute( 'plus_table__text', 'class', 'plus-table__text' );

		// Table Sortable.
		if ( !empty($sortable) && 'yes' === $sortable ) {
			$this->add_render_attribute( 'plus_table_id', 'data-sort-table', $sortable );
		} else {
			$this->add_render_attribute( 'plus_table_id', 'data-sort-table', 'no' );
		}
		
		// Table Show entries.
		if ( !empty($showEntries) && 'yes' == $showEntries ) {
			$this->add_render_attribute( 'plus_table_id', 'data-show-entry', $showEntries );
		} else {
			$this->add_render_attribute( 'plus_table_id', 'data-show-entry', 'no' );
		}
		
		//Table Searchable
		if ( !empty($searchable) &&  'yes' == $searchable ) {
			$this->add_render_attribute( 'plus_table_id', 'data-searchable', $searchable );
			$this->add_render_attribute( 'plus_table_id', 'data-searchable-label', $searchableLabel );				
		} else {
			$this->add_render_attribute( 'plus_table_id', 'data-searchable', 'no' );
		}
		
		//Table CSV File
		if(!empty($tableSelection) && $tableSelection == 'csv_file'){
			if( !empty($settings['file']['url']) ){
				$ext = pathinfo($settings['file']['url'], PATHINFO_EXTENSION);
				if($ext!='csv'){
					echo '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only CSV File Extension.",'theplus').'</h3>';
					return false;
				}
			}

			if($settings['file']['url']){
				echo '<div itemscope '.$this->get_render_attribute_string( 'plus_table_wrapper' ).'>';
				echo '<table '.$this->get_render_attribute_string( 'plus_table_id' ).'>';
					echo theplus_fetch_csv(esc_url($settings['file']['url']),$settings['sortable']);
				echo '</table></div>';
			} else {
				echo '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! You didn\'t enter any table data or CSV file",'theplus').'</h3>';
			}
			
		} else if(!empty($tableSelection) && $tableSelection == 'google_sheet'){
			echo '<div itemscope '.$this->get_render_attribute_string( 'plus_table_wrapper' ).'>';
				echo '<table '.$this->get_render_attribute_string( 'plus_table_id' ).'>';
					echo $this->tp_google_sheet();
				echo '</table>';	
			echo '</div>';
		} else { ?>
		<div itemscope <?php echo $this->get_render_attribute_string( 'plus_table_wrapper' ); ?>>

			<table <?php echo $this->get_render_attribute_string( 'plus_table_id' ); ?>>
				<?php
				$first_row_th    = true;
				$cell_col_count = 0;
				$counter_row      = 1;					
				$inline_count   = 0;
				$row_count_tb    = count( (array)$settings['table_headings'] );
				$data_entry_col     = 0;
				$header_text    = array();					

				if ( $row_count_tb > 1 ) {
					?>
				<thead>
					<?php
					if ( $settings['table_headings'] ) {
						$headi=0;
						foreach ( $settings['table_headings'] as $index => $head ) {
							
							// Header text prepview editing.
							$repeater_heading_text = $this->get_repeater_setting_key( 'heading_text', 'table_headings', $inline_count );
							$this->add_render_attribute( $repeater_heading_text, 'class', 'plus-table__text-inner' );
							$this->add_inline_editing_attributes( $repeater_heading_text );
							
							// TH.
							if ( true === $first_row_th ) {
								$this->add_render_attribute( 'current_' . $head['_id'], 'data-sort', $cell_col_count );
							}
							
							$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'sort-this' );
							$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'elementor-repeater-item-' . $head['_id'] );
							$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'plus-table-col' );								
							
							if ( 1 < $head['heading_col_span'] ) {
								$this->add_render_attribute( 'current_' . $head['_id'], 'colspan', $head['heading_col_span'] );
							}
							if ( 1 < $head['heading_row_span'] ) {
								$this->add_render_attribute( 'current_' . $head['_id'], 'rowspan', $head['heading_row_span'] );
							}
							
							// Sort Icon.
							if ( 'yes' === $settings['sortable'] && true === $first_row_th ) {
								$this->add_render_attribute( 'icon_sort_' . $head['_id'], 'class', 'plus-sort-icon' );
							}
							
							if (!empty($head['icons_image']['url']) ) {
								$icons_image=$head['icons_image']['id'];
								$img = wp_get_attachment_image_src($icons_image,$head['icons_image_thumbnail_size']);
								$icons_image_Src = $img[0];
								$this->add_render_attribute( 'plus_head_col_img' . $head['_id'], 'src', $icons_image_Src );
								$this->add_render_attribute( 'plus_head_col_img' . $head['_id'], 'class', 'plus-col-img--' . $settings['all_image_align'] );
								$this->add_render_attribute( 'plus_head_col_img' . $head['_id'], 'title', get_the_title( $head['icons_image']['id'] ) );
								$this->add_render_attribute( 'plus_head_col_img' . $head['_id'], 'alt', get_the_title( $head['icons_image']['id'] ) );
							}
							
							// ICON.								
							if('icon' === $head['header_content_icon_image'] && $head["icon_font_style"]=='font_awesome'){
								$this->add_render_attribute( 'plus_heading_icon' . $head['_id'], 'class', $head['icon_fontawesome'] );
							}else if('icon' === $head['header_content_icon_image'] && $head["icon_font_style"]=='icon_mind'){
								$this->add_render_attribute( 'plus_heading_icon' . $head['_id'], 'class', $head['icons_mind'] );
							}
							$this->add_render_attribute( 'plus_heading_icon_align' . $head['_id'], 'class', 'plus-align-icon--' . $settings['all_icon_align'] );
							
							//tooltip
							$_tooltip='_tooltip_'.$headi;
							if( $head['heading_show_tooltips'] == 'yes' ) {
								
								$this->add_render_attribute( $_tooltip, 'data-tippy', '', true );

								$tooltip_content=$head['heading_tooltip_content'];
								$this->add_render_attribute( $_tooltip, 'title', $tooltip_content, true );
								
								$plus_tooltip_position=($settings["tooltip_common_option_plus_tooltip_position"]!='') ? $settings["tooltip_common_option_plus_tooltip_position"] : 'top';
								$this->add_render_attribute( $_tooltip, 'data-tippy-placement', $plus_tooltip_position, true );
								
								$tooltip_interactive =($settings["tooltip_common_option_plus_tooltip_interactive"]=='' || $settings["tooltip_common_option_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
								$this->add_render_attribute( $_tooltip, 'data-tippy-interactive', $tooltip_interactive, true );
								
								$plus_tooltip_theme=($settings["tooltip_common_option_plus_tooltip_theme"]!='') ? $settings["tooltip_common_option_plus_tooltip_theme"] : 'dark';
								$this->add_render_attribute( $_tooltip, 'data-tippy-theme', $plus_tooltip_theme, true );
								
								
								$tooltip_arrow =($settings["tooltip_common_option_plus_tooltip_arrow"]!='none' || $settings["tooltip_common_option_plus_tooltip_arrow"]=='') ? 'true' : 'false';
								$this->add_render_attribute( $_tooltip, 'data-tippy-arrow', $tooltip_arrow , true );
								
								$plus_tooltip_arrow=($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
								$this->add_render_attribute( $_tooltip, 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
								
								$plus_tooltip_animation=($settings["tooltip_common_option_plus_tooltip_animation"]!='') ? $settings["tooltip_common_option_plus_tooltip_animation"] : 'shift-toward';
								$this->add_render_attribute( $_tooltip, 'data-tippy-animation', $plus_tooltip_animation, true );
								
								$plus_tooltip_x_offset=($settings["tooltip_common_option_plus_tooltip_x_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_x_offset"] : 0;
								$plus_tooltip_y_offset=($settings["tooltip_common_option_plus_tooltip_y_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_y_offset"] : 0;
								$this->add_render_attribute( $_tooltip, 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
								
								$tooltip_duration_in =($settings["tooltip_common_option_plus_tooltip_duration_in"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_in"] : 250;
								$tooltip_duration_out =($settings["tooltip_common_option_plus_tooltip_duration_out"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_out"] : 200;
								$tooltip_trigger =($settings["tooltip_common_option_plus_tooltip_triggger"]!='') ? $settings["tooltip_common_option_plus_tooltip_triggger"] : 'mouseenter';
								$tooltip_arrowtype =($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
							}

							$uniqid=uniqid("tooltip");
					
							$show_tooltips_on = $head['heading_show_tooltips_on'];
							$toolbox=$toolicon=$tt_on_icon='';
							if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
								$toolbox = $this->get_render_attribute_string( 'current_' . $head['_id'] );
								$toolicon = 'id="'.esc_attr($uniqid).'" class="plus-icon-list-item elementor-repeater-item-'.esc_attr($head['_id']).'" data-local="true" '.$this->get_render_attribute_string( $_tooltip ).'';
								
								ob_start();
								\Elementor\Icons_Manager::render_icon( $settings['tt_on_icon'] , [ 'aria-hidden' => 'true' ]);
								$tt_on_icon = ob_get_contents();
								ob_end_clean();
								
							}else{									
								$toolbox = 'id="'.esc_attr($uniqid).'"'.$this->get_render_attribute_string( 'current_' . $head['_id'] ).' data-local="true" '.$this->get_render_attribute_string( $_tooltip ).'';
							}
					
							if ( 'cell' === $head['header_content_type'] ) {
								?>
								<th <?php echo $toolbox; ?> scope="col">
									<span class="sort-style">
									<span <?php echo $this->get_render_attribute_string( 'plus_table__text' ); ?>>
										<?php if ( 'icon' === $head['header_content_icon_image'] ) { ?>												
												<?php if ( 'left' === $settings['all_icon_align'] ) { ?>
											<span <?php echo $this->get_render_attribute_string( 'plus_heading_icon_align' . $head['_id'] ); ?>>
												<i <?php echo $this->get_render_attribute_string( 'plus_heading_icon' . $head['_id'] ); ?>></i>
											</span>
										<?php } ?>											
										<?php } else { ?>
												<?php if ( !empty($head['icons_image']['url']) ) { ?>
													<?php if ( 'left' == $settings['all_image_align'] ) { ?>
													<img <?php echo $this->get_render_attribute_string( 'plus_head_col_img' . $head['_id'] ); ?>>
												<?php } ?>
												<?php } ?>
										<?php } ?>
										<span <?php echo $this->get_render_attribute_string( $repeater_heading_text ); ?>><?php echo $head['heading_text']; ?></span>
										<?php if ( 'icon' === $head['header_content_icon_image'] ) { ?>												
												<?php if ( 'right' === $settings['all_icon_align'] ) { ?>
											<span <?php echo $this->get_render_attribute_string( 'plus_heading_icon_align' . $head['_id'] ); ?>>
												<i <?php echo $this->get_render_attribute_string( 'plus_heading_icon' . $head['_id'] ); ?>></i>
											</span>
										<?php } ?>											
										<?php } else { ?>
												<?php if ( !empty($head['icons_image']['url']) ) { ?>
													<?php if ( 'right' == $settings['all_image_align'] ) { ?>
													<img <?php echo $this->get_render_attribute_string( 'plus_head_col_img' . $head['_id'] ); ?>>
												<?php } ?>
												<?php } ?>
										<?php } 
										if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
											echo '<span class="tp-tooltip-on-icon" '.$toolicon.'>'.$tt_on_icon.'</span>';
										}											
										?>
									</span> 
									
									<?php
									if ( 'yes' === $settings['sortable'] && true === $first_row_th ) { ?>
										<span <?php echo $this->get_render_attribute_string( 'icon_sort_' . $head['_id'] ); ?>></span>
									<?php } ?>
									</span>
								</th>
								<?php
								$inline_tippy_js='';
								if($head['heading_show_tooltips'] == 'yes'){
									$inline_tippy_js ='jQuery( document ).ready(function() {
									"use strict";
										if(typeof tippy === "function"){
											tippy( "#'.esc_attr($uniqid).'" , {
												arrowType : "'.esc_attr($tooltip_arrowtype).'",
												duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
												trigger : "'.esc_attr($tooltip_trigger).'",
												appendTo: document.querySelector("#'.esc_attr($uniqid).'")
											});
										}
									});';
									echo wp_print_inline_script_tag($inline_tippy_js);
								}
						
								$header_text[ $cell_col_count ]['heading_text'] = $head['heading_text'];
								$header_text[ $cell_col_count ]['icon_image'] = $head['header_content_icon_image'];
								$header_text[ $cell_col_count ]['plus_heading_icon_align'] = 'plus_heading_icon_align' .$head['_id'];
								$header_text[ $cell_col_count ]['plus_heading_icon'] = 'plus_heading_icon' . $head['_id'];
								$header_text[ $cell_col_count ]['icons_image_url'] = !empty($head['icons_image']['url']) ? $head['icons_image']['url'] : '';
								$header_text[ $cell_col_count ]['plus_head_col_img'] = 'plus_head_col_img' . $head['_id'];
								$cell_col_count++;
							} else {
								if ( $counter_row > 1 && $counter_row < $row_count_tb ) {
									// Break into new row.
									?>
									</tr><tr <?php echo $this->get_render_attribute_string( 'plus_table_row' ); ?>>
									<?php
									$first_row_th = false;
								} elseif ( 1 === $counter_row && false === $this->table_first_row() ) {
									?>
									<tr <?php echo $this->get_render_attribute_string( 'plus_table_row' ); ?>>
													<?php
								}
								$cell_col_count = 0;
							}
							$headi++;
							$counter_row++;
							$inline_count++;
						}
					}
					?>
				</thead>
				<?php } ?>
				<tbody>
					<!-- ROWS -->
					<?php
					$cell_counter_c    = 0;
					$counter           = 1;						
					$cell_inline_count = 0;
					$row_count         = count( (array)$settings['table_content'] );						
					$attr_id='cell';
					$ij=0;
					
					if ( $settings['table_content'] ) {
						$rowi=0;
						foreach ( $settings['table_content'] as $index => $row ) {
							// Cell text inline classes.
							$ij++;
							
							$repeater_cell_text = $this->get_repeater_setting_key( 'cell_text', 'table_content', $cell_inline_count );
							$this->add_render_attribute( $repeater_cell_text, 'class', 'plus-table__text-inner' );
							$this->add_inline_editing_attributes( $repeater_cell_text );
							$this->add_render_attribute( 'plus_cell_icon_align' . $row['_id'], 'class', 'plus-align-icon--' . $settings['all_icon_align'] );
							
							$button='';
							if(!empty($row["cell_display_button"]) && $row["cell_display_button"]=='yes'){
								$link_key = 'link_' . $ij;
								if ( ! empty( $row['cell_button_link']['url'] ) ) {
									$this->add_render_attribute( $link_key, 'href', $row['cell_button_link']['url'] );
									if ( $row['cell_button_link']['is_external'] ) {
										$this->add_render_attribute( $link_key, 'target', '_blank' );
									}
									if ( $row['cell_button_link']['nofollow'] ) {
										$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
									}
								}
								$this->add_render_attribute( $link_key, 'class', 'button-link-wrap' );
								$this->add_render_attribute( $link_key, 'role', 'button' );
								
								/*button attributes start*/
								$button_custom_attributes=$row["button_custom_attributes"];
								$custom_attributes=$row["custom_attributes"];
																	
								$cst_att='';
								if((!empty($button_custom_attributes) && $button_custom_attributes=='yes') && !empty($custom_attributes)){
									$cst_att = $custom_attributes;
								}
								/*button attributes end*/
								
								$button_style = $row['cell_button_style'];
								$button_text = $row['cell_button_text'];
								$btn_uid=uniqid('btn');
								$data_class= $btn_uid;
								$data_class .=' button-'.$button_style.' ';
								$button .='<div class="pt_plus_button '.esc_attr($data_class).'">';						
										$button .='<a '.$this->get_render_attribute_string( $link_key ).' '.$cst_att.' >';
										$button .= esc_html($button_text);
										$button .='</a>';
								$button .='</div>';
							}
							
							if('icon' === $row['cell_content_icon_image'] && $row["icon_font_style"]=='font_awesome'){
								$this->add_render_attribute( 'plus_cell_icon'  . $row['_id'], 'class', $row['cell_icon'] );
							}else if('icon' === $row['cell_content_icon_image'] && $row["icon_font_style"]=='icon_mind'){
								$this->add_render_attribute( 'plus_cell_icon'  . $row['_id'], 'class', $row['cell_icons_mind'] );								
							}

							$this->add_render_attribute( 'plus_table_col' . $row['_id'], 'class', 'plus-table-col' );
							$this->add_render_attribute( 'plus_table_col' . $row['_id'], 'class', 'elementor-repeater-item-' . $row['_id'] );
							
							if ( 1 < $row['cell_span'] ) {
								$this->add_render_attribute( 'plus_table_col' . $row['_id'], 'colspan', $row['cell_span'] );
							}
							if ( 1 < $row['cell_row_span'] ) {
								$this->add_render_attribute( 'plus_table_col' . $row['_id'], 'rowspan', $row['cell_row_span'] );
							}
							
							if ( !empty($row['image']['url']) ) {
								$image=$row['image']['id'];
								$img = wp_get_attachment_image_src($image,$row['image_thumbnail_size']);
								$image_Src = $img[0];
							
								$this->add_render_attribute( 'plus_col_img' . $row['_id'], 'src', $image_Src );
								$this->add_render_attribute( 'plus_col_img' . $row['_id'], 'class', 'plus-col-img--' . $settings['all_image_align'] );
								$this->add_render_attribute( 'plus_col_img' . $row['_id'], 'title', get_the_title( $row['image']['id'] ) );
								$this->add_render_attribute( 'plus_col_img' . $row['_id'], 'alt', get_the_title( $row['image']['id']) );
							}
							
							if ( ! empty( $row['link']['url'] ) ) {
								$this->add_render_attribute( 'col-link-' . $row['_id'], 'href', $row['link']['url'] );
								if ( $row['link']['is_external'] ) {
									$this->add_render_attribute( 'col-link-' . $row['_id'], 'target', '_blank' );
								}
								if ( $row['link']['nofollow'] ) {
									$this->add_render_attribute( 'col-link-' . $row['_id'], 'rel', 'nofollow' );
								}
								$this->add_render_attribute( 'col-link-' . $row['_id'], 'class', 'tb-col-link' );
							}

							if ( 'cell' === $row['content_type'] ) {
								// Fetch corresponding header cell text.
								if ( isset( $header_text[ $cell_counter_c ]['heading_text'] ) && $header_text[ $cell_counter_c ]['heading_text'] ) {
									$this->add_render_attribute( 'plus_table_col' . $row['_id'], 'data-title', $header_text[ $cell_counter_c ]['heading_text'] );
								}
								
								//tooltip
								$_tooltip='_tooltip_'.$rowi;
								if( isset($row['body_show_tooltips']) && $row['body_show_tooltips'] == 'yes' ) {
									
									$this->add_render_attribute( $_tooltip, 'data-tippy', '', true );

									$tooltip_content=$row['body_tooltip_content'];
									$this->add_render_attribute( $_tooltip, 'title', $tooltip_content, true );
									
									$plus_tooltip_position=($settings["tooltip_common_option_plus_tooltip_position"]!='') ? $settings["tooltip_common_option_plus_tooltip_position"] : 'top';
									$this->add_render_attribute( $_tooltip, 'data-tippy-placement', $plus_tooltip_position, true );
									
									$tooltip_interactive =($settings["tooltip_common_option_plus_tooltip_interactive"]=='' || $settings["tooltip_common_option_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
									$this->add_render_attribute( $_tooltip, 'data-tippy-interactive', $tooltip_interactive, true );
									
									$plus_tooltip_theme=($settings["tooltip_common_option_plus_tooltip_theme"]!='') ? $settings["tooltip_common_option_plus_tooltip_theme"] : 'dark';
									$this->add_render_attribute( $_tooltip, 'data-tippy-theme', $plus_tooltip_theme, true );
									
									
									$tooltip_arrow =($settings["tooltip_common_option_plus_tooltip_arrow"]!='none' || $settings["tooltip_common_option_plus_tooltip_arrow"]=='') ? 'true' : 'false';
									$this->add_render_attribute( $_tooltip, 'data-tippy-arrow', $tooltip_arrow , true );
									
									$plus_tooltip_arrow=($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
									$this->add_render_attribute( $_tooltip, 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
									
									$plus_tooltip_animation=($settings["tooltip_common_option_plus_tooltip_animation"]!='') ? $settings["tooltip_common_option_plus_tooltip_animation"] : 'shift-toward';
									$this->add_render_attribute( $_tooltip, 'data-tippy-animation', $plus_tooltip_animation, true );
									
									$plus_tooltip_x_offset=($settings["tooltip_common_option_plus_tooltip_x_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_x_offset"] : 0;
									$plus_tooltip_y_offset=($settings["tooltip_common_option_plus_tooltip_y_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_y_offset"] : 0;
									$this->add_render_attribute( $_tooltip, 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
									
									$tooltip_duration_in =($settings["tooltip_common_option_plus_tooltip_duration_in"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_in"] : 250;
									$tooltip_duration_out =($settings["tooltip_common_option_plus_tooltip_duration_out"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_out"] : 200;
									$tooltip_trigger =($settings["tooltip_common_option_plus_tooltip_triggger"]!='') ? $settings["tooltip_common_option_plus_tooltip_triggger"] : 'mouseenter';
									$tooltip_arrowtype =($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
								}

								$uniqid=uniqid("tooltip");
						
								$show_tooltips_on = !empty($row['body_show_tooltips_on']) ? $row['body_show_tooltips_on'] : 'box';
								$toolbox=$toolicon=$tt_on_icon='';
								if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
									$toolbox = $this->get_render_attribute_string( 'plus_table_col' . $row['_id'] );
									$toolicon = 'id="'.esc_attr($uniqid).'" '.$this->get_render_attribute_string( 'plus_table_col' . $row['_id'] ).' data-local="true" '.$this->get_render_attribute_string( $_tooltip ).'';
									
									ob_start();
									\Elementor\Icons_Manager::render_icon( $settings['tt_on_icon'] , [ 'aria-hidden' => 'true' ]);
									$tt_on_icon = ob_get_contents();
									ob_end_clean();
									
								}else{										
									$toolbox = 'id="'.esc_attr($uniqid).'"'.$this->get_render_attribute_string( 'plus_table_col' . $row['_id'] ) .$this->get_render_attribute_string( $_tooltip );
								}								
							
								?>
								<<?php echo esc_attr($row['table_th_td']); ?> <?php echo $toolbox; ?>>
									<?php if ( ! empty( $row['link']['url'] ) ) { ?>
									<a <?php echo $this->get_render_attribute_string( 'col-link-' . $row['_id'] ); ?>>
									<?php } ?>
										<?php if(!empty($settings["mobile_responsive_table"]) && $settings["mobile_responsive_table"]=='one-by-one'){ ?>
											<div class="plus-table-mob-wrap">
											<span class="plus-table-mob-row">
												<?php if ( 'icon' === $header_text[ $cell_counter_c ]['icon_image'] ) { ?>												
														<?php if ( 'left' === $settings['all_icon_align'] ) { ?>
													<span <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_heading_icon_align'] ); ?>>
														<i <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_heading_icon'] ); ?>></i>
													</span>
												<?php } ?>											
												<?php } else { ?>
														<?php if ( $header_text[ $cell_counter_c ]['icons_image_url'] ) { ?>
															<?php if ( 'left' == $settings['all_image_align'] ) { ?>
															<img <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_head_col_img'] ); ?>>
														<?php } ?>
														<?php } ?>
												<?php } ?>
												<?php if ( isset( $header_text[ $cell_counter_c ]['heading_text'] ) && $header_text[ $cell_counter_c ]['heading_text'] ) {														
													echo '<span class="mob-heading-text">'.$header_text[ $cell_counter_c ]['heading_text'].'</span>';
												}
												?>
												<?php if ( 'icon' === $header_text[ $cell_counter_c ]['icon_image'] ) { ?>												
													<?php if ( 'right' === $settings['all_icon_align'] ) { ?>
													<span <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_heading_icon_align'] ); ?>>
														<i <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_heading_icon'] ); ?>></i>
													</span>
												<?php } ?>											
												<?php } else { ?>
														<?php if ( $header_text[ $cell_counter_c ]['icons_image_url'] ) { ?>
															<?php if ( 'right' == $settings['all_image_align'] ) { ?>
															<img <?php echo $this->get_render_attribute_string( $header_text[ $cell_counter_c ]['plus_head_col_img'] ); ?>>
														<?php } ?>
														<?php } ?>
												<?php } ?>
											</span> 
										<?php } ?>
											<span <?php echo $this->get_render_attribute_string( 'plus_table__text' ); ?>>
												<?php if ( 'icon' === $row['cell_content_icon_image'] ) { ?>
													
														<?php if ( 'left' === $settings['all_icon_align'] ) { ?>
													<span <?php echo $this->get_render_attribute_string( 'plus_cell_icon_align' . $row['_id'] ); ?>>
														<i <?php echo $this->get_render_attribute_string( 'plus_cell_icon' . $row['_id'] ); ?>></i>
													</span>
													<?php } ?>
													
												<?php } else { ?>
													<?php if ( !empty($row['image']) && !empty($row['image']['url']) ) { ?>
														<?php if ( 'left' === $settings['all_image_align'] ) { ?>
														<img <?php echo $this->get_render_attribute_string( 'plus_col_img' . $row['_id'] ); ?>>
													<?php } ?>
													<?php } ?>
												<?php } ?>
												<?php if(!empty($row['cell_text'])){ ?>
													<span <?php echo $this->get_render_attribute_string( $repeater_cell_text ); ?>><?php echo $row['cell_text']; ?></span>
												<?php } ?>
												<?php if ( 'icon' === $row['cell_content_icon_image'] ) { ?>
													
														<?php if ( 'right' === $settings['all_icon_align'] ) { ?>
													<span <?php echo $this->get_render_attribute_string( 'plus_cell_icon_align' . $row['_id'] ); ?>>
														<i <?php echo $this->get_render_attribute_string( 'plus_cell_icon' . $row['_id'] ); ?>></i>
													</span>
													<?php } ?>
													
												<?php } else { ?>
													<?php if ( !empty($row['image']['url']) ) { ?>
														<?php if ( 'right' === $settings['all_image_align'] ) { ?>
														<img <?php echo $this->get_render_attribute_string( 'plus_col_img' . $row['_id'] ); ?>>
													<?php } ?>
													<?php } ?>
												<?php } ?>
												<?php echo $button; ?>
											</span>
										<?php if(!empty($settings["mobile_responsive_table"]) && $settings["mobile_responsive_table"]=='one-by-one'){ ?>
											</div>
										<?php } 
										if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
											echo '<span class="tp-tooltip-on-icon" '.$toolicon.'>'.$tt_on_icon.'</span>';
										}											
										?>
									<?php if ( ! empty( $row['link']['url'] ) ) { ?>
									</a>
									<?php } ?>
								</<?php echo $row['table_th_td']; ?>>
									<?php
									// Increment to next cell.
									$cell_counter_c++;
									
									$body_inline_tippy_js='';
									if(isset($row['body_show_tooltips']) && $row['body_show_tooltips'] == 'yes'){
										$body_inline_tippy_js ='jQuery( document ).ready(function() {
										"use strict";
											if(typeof tippy === "function"){
												tippy( "#'.esc_attr($uniqid).'" , {
													arrowType : "'.esc_attr($tooltip_arrowtype).'",
													duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
													trigger : "'.esc_attr($tooltip_trigger).'",
													appendTo: document.querySelector("#'.esc_attr($uniqid).'")
												});
											}
										});';
										echo wp_print_inline_script_tag($body_inline_tippy_js);
									}
							} else {
								if ( $counter > 1 && $counter < $row_count ) {
									// Break into new row.
									++$data_entry_col;
									?>
									</tr><tr data-entry="<?php echo esc_attr($data_entry_col); ?>" <?php echo $this->get_render_attribute_string( 'plus_table_row' ); ?>>
								<?php
								} elseif ( 1 === $counter && false === $this->table_first_row() ) {
									$data_entry_col = 1;
									?>
									<tr data-entry="<?php echo esc_attr($data_entry_col); ?>" <?php echo $this->get_render_attribute_string( 'plus_table_row' ); ?>>
								<?php
								}
								$cell_counter_c = 0;
							}
							$rowi++;
							$counter++;
							$cell_inline_count++;
						}
					}
					?>
				</tbody>
			</table>

		</div>
		<?php } 

		$html = ob_get_clean();
		
		echo $html;
		
		$css_rule = '<style>';
		if(!empty($cell_align_head_desktop)){
			$css_rule .='#plus-table-id-' .esc_attr($widget_id).' th,#plus-table-id-' .esc_attr($widget_id).' th .plus-table__text{ ';			
				if($cell_align_head_desktop == 'left'){
					$css_rule .='margin:0 auto;text-align:left;margin-left:0;';
				}
				if($cell_align_head_desktop == 'center'){
					$css_rule .='margin:0 auto;text-align:center;';
				}
				if($cell_align_head_desktop == 'right'){
					$css_rule .='margin:0 auto;text-align:right;margin-right:0;';
				}
			$css_rule .='}';
		}

		if(!empty($cell_align_head_tablet)){
			$css_rule .='@media (max-width:1024px){#plus-table-id-' .esc_attr($widget_id).' th,#plus-table-id-' .esc_attr($widget_id).' th .plus-table__text{';
			
				if($cell_align_head_tablet=='left'){
					$css_rule .='margin:0 auto;text-align:left;margin-left:0;';
				}
				if($cell_align_head_tablet=='center'){
					$css_rule .='margin:0 auto;text-align:center;';
				}
				if($cell_align_head_tablet=='right'){
					$css_rule .='margin:0 auto;text-align:right;margin-right:0;';
				}			
			$css_rule .='}}';
		}

		if(!empty($cell_align_head_mobile)){
			$css_rule .='@media (max-width:767px){#plus-table-id-' .esc_attr($widget_id).' th,#plus-table-id-' .esc_attr($widget_id).' th .plus-table__text{';
			
				if($cell_align_head_mobile=='left'){
					$css_rule .='margin:0 auto;text-align:left;margin-left:0;';
				}
				if($cell_align_head_mobile=='center'){
					$css_rule .='margin:0 auto;text-align:center;';
				}
				if($cell_align_head_mobile=='right'){
					$css_rule .='margin:0 auto;text-align:right;margin-right:0;';
				}
			$css_rule .='}}';
		}

		$css_rule .='</style>';

		echo $css_rule;
	}

	/**
	 * Function to identify if it is a table first row or not.
	 *
	 * If yes returns false no returns true.
	 *
	 * @access protected
	 */
	protected function table_first_row() {

		$settings = $this->get_settings_for_display();

		if ( 'row' === $settings['table_content'][0]['content_type'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Function to It is used for Retrieve data for google sheet
	 *
	 * @version 5.2.5
	 */
	protected function tp_google_sheet() {
		$WidgetID = $this->get_id();
		$settings = $this->get_settings();

		$api_key = !empty($settings["api_key"]) ? $settings["api_key"] : '';
		$sheet_id = !empty($settings["sheet_id"]) ? $settings["sheet_id"] : '';
		$table_range = !empty($settings["table_range"]) ? $settings["table_range"] : '';
		$RefreshTime = !empty($settings['TimeFrq']) ? $settings['TimeFrq'] : '3600';

		$TimeFrq = array('TimeFrq' => $RefreshTime );

		$output = '';

		$ErrorTitle = esc_html__('Data Not Found!', 'theplus');
		$ErrorMassage = esc_html__('Google Sheet Data Not Found', 'theplus');
		if( empty($api_key) || empty($sheet_id) || empty($table_range) ){
			$output = theplus_get_widgetError($ErrorTitle, $ErrorMassage);

			return $output;
		} 

		$API = "https://sheets.googleapis.com/v4/spreadsheets/{$sheet_id}/values/{$table_range}?key={$api_key}";

		$Data=[];
		$BGetAPI = get_transient("tp-gs-table-url-$WidgetID");
	    $BGetTime = get_transient("tp-gs-table-time-$WidgetID");
		if( $BGetAPI != $API || $BGetTime != $TimeFrq ){
			$Data = $this->tp_table_api($API);

			set_transient("tp-gs-table-url-$WidgetID", $API, $TimeFrq);
			set_transient("tp-gs-table-time-$WidgetID", $TimeFrq, $TimeFrq);
			set_transient("tp-gs-table-Data-$WidgetID", $Data, $TimeFrq);
	    }else{
	        $Data = get_transient("tp-gs-table-Data-$WidgetID");
	    }

		if( is_wp_error($Data) ){
			$output = theplus_get_widgetError($ErrorTitle, $ErrorMassage);

			return $output;
		}

		$SheetData = isset($Data['values']) ? $Data['values'] : [];

		if( empty($SheetData) ){
			$output = theplus_get_widgetError($ErrorTitle, $ErrorMassage);

			return $output;
		}

		$output = '';
		$output .= '<thead><tr class="plus-table-row">';
		foreach ( $SheetData[0] as $key => $th ){
			$output .= '<th class="sort-this plus-table-col">';
				// $output .= $th;
				// if ( $sortable === 'yes') {
					$output .= '<span class="plus-sort-icon">'.$th.'</span>';
				// }
			$output .= '</th>';
		}

		$output .= '</tr></thead><tbody>';
		array_shift( $SheetData );

		foreach ( $SheetData as $rows ) {
			$output .= '<tr class="plus-table-row">';
			foreach ( $rows as $col ) {
				$output .= '<td class="plus-table-col">' . htmlentities($col). '</td>';
			}
			$output .= '</tr>';
		}

		$output .= '</tbody>';

		return $output;
	}

	/**
	 * Function to It is use for call api
	 *
	 * If yes returns Array Data
	 *
	 * @version 5.2.5
	 */
	protected function tp_table_api($API){
		$settings = $this->get_settings_for_display();
		$Final=[];

		$URL = wp_remote_get($API);
		$StatusCode = wp_remote_retrieve_response_code($URL);
		$GetDataOne = wp_remote_retrieve_body($URL);
		$Statuscode = array( "HTTP_CODE" => $StatusCode );

		$Response = json_decode($GetDataOne, true);
		if( is_array($Statuscode) && is_array($Response) ){
			$Final = array_merge($Statuscode, $Response);
		}
		
		return $Final;
    }

    protected function content_template() {
	
    }
}