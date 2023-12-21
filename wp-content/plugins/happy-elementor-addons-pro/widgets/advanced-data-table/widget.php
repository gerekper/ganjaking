<?php
/**
 * Advanced Data Table
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

defined('ABSPATH') || die();

class Advanced_Data_Table extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Advanced Data Table', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-data-table';
	}

	public function get_keywords() {
		return ['data', 'table', 'google', 'spreadsheet', 'advanced', 'row', 'column', 'tabular'];
	}

	// Whether the reload preview is required or not.
    public function is_reload_preview_required() {
        return true;
    }

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__table_content_controls();
		$this->__settings_content_controls();
	}

	protected function __table_content_controls() {

		$this->start_controls_section(
			'_section_table',
			[
				'label' => __( 'Data Table', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'table_type',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Table Type', 'happy-addons-pro' ),
				'default' => 'google_sheet',
				'options' => [
					'google_sheet' => __( 'Google Sheet', 'happy-addons-pro' ),
					'table_press' => __( 'TablePress', 'happy-addons-pro' ),
					'database' => __( 'Database', 'happy-addons-pro' ),
					'import' => __( 'Import', 'happy-addons-pro' ),
				]
			]
		);

		$this->add_control(
			'import_table_data',
			[
				'label' => __( 'Add Data', 'happy-addons-pro' ),
				'description' => __( 'Paste Data in CSV format. First Row will be Column Labels', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 10,
				'label_block' => true,
				'condition' => [
					'table_type' => 'import'
				],
			]
		);

		$this->add_control(
			'database_tables_list',
			[
				'label' => __( 'Tables', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'condition' => [
					'table_type' => 'database'
				],
				'options' => ['' => __( 'Select Database Table', 'happy-addons-pro' ) ] + \hapro_db_tables_list(),
			]
		);

		$this->add_control(
			'table_press_list',
			[
				'label' => __( 'Select Table', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'condition' => [
					'table_type' => 'table_press'
				],
				'options' => ['' => __( 'Select Table', 'happy-addons-pro' ) ] + \hapro_get_table_press_list(),
			]
		);

        $this->add_control(
            'credentials',
            [
                'label' => __('Credentials from', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' =>  [
                    'global' => __('Global', 'happy-addons-pro'),
                    'custom' => __('Custom', 'happy-addons-pro'),
                ],
				'condition' => [
					'table_type' => 'google_sheet'
				]
            ]
        );

        $this->add_control(
            'credentials_set_notice',
            [
                'raw' => '<strong>' . esc_html__('Note!', 'happy-addons-pro') . '</strong> ' . esc_html__('Please set credentials in Happy Addons Dashboard - ', 'happy-addons-pro') . '<a style="border-bottom-color: inherit;" href="'. esc_url(admin_url('admin.php?page=happy-addons#credentials')) . '" target="_blank" >'. esc_html__('Credentials', 'happy-addons-pro') .'</a>',
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'render_type' => 'ui',
                'condition' => [
					'table_type' => 'google_sheet',
                    'credentials' => 'global',
                ],
            ]
        );

		$this->add_control(
			'api_key',
			[
				'label' => __( 'Google API Key', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => sprintf('<a href="https://console.developers.google.com/" target="_blank">%s</a>', __( 'Get API Key', 'happy-addons-pro' ) ),
				'condition' => [
					'table_type' => 'google_sheet',
					'credentials' => 'custom',
				]
			]
		);

		$this->add_control(
			'sheet_id',
			[
				'label' => __( 'Google Sheet ID', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Add Google Sheets ID.', 'happy-addons-pro' ),
				'condition' => [
					'table_type' => 'google_sheet',
					'credentials' => 'custom',
				]
			]
		);

		$this->add_control(
			'sheet_range',
			[
				'label' => __( 'Google Sheets Range', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Add Google Sheets Range. Ex: A1:D5', 'happy-addons-pro' ),
				'condition' => [
					'table_type' => 'google_sheet',
					'credentials' => 'custom',
				]
			]
		);

		$this->add_responsive_control(
			'head_align',
			[
				'label' => __( 'Head Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__head-column-cell' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'row_align',
			[
				'label' => __( 'Row Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body-row-cell' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'remove_cash',
			[
				'label' => __( 'Remove Cache', 'happy-addons-pro' ),
				'description' => __( "Don't forget to off when you have done editing Google Sheet.", 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'table_type' => 'google_sheet'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_table_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_search',
			[
				'label' => __( 'Enable Search', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __( 'Enable Pagination', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
				'prefix_class' => 'ha-enable-pagination-'
			]
		);

		$this->add_control(
			'show_entries',
			[
				'label' => __( 'Enable Entries', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'ha-show-entries-',
				'condition' => [
					'show_pagination' => 'yes'
				]
			]
		);

		$this->add_control(
			'export_table',
			[
				'label' => __( 'Enable Export Table', 'happy-addons-pro' ),
				'description' => __( 'Export as CSV file', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'export_table_text',
			[
				'label' => __( 'Export Table', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => __( 'Export Table', 'happy-addons-pro' ),
				'condition' => [
					'export_table' => 'yes'
				]
			]
		);

		$this->add_control(
			'allow_visitors_export_table',
			[
				'label' => __( 'Allow Visitors to Export Table', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'ON', 'happy-addons-pro' ),
				'label_off' => __( 'OFF', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'export_table' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'table_width',
			[
				'label' => __( 'Table Width for Horizontal Scroll', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'desktop_default' => [
					'unit' => 'px',
					'size' => ''
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => '768'
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => '767'
				],
				'selectors' => [
					'{{WRAPPER}} .dataTables_wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'enable_nowrap',
			[
				'label' => __( 'Disable Word Break', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'ha-adt-x-scroll-',
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__table_head_style_controls();
		$this->__table_row_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_table_common',
			[
				'label' => __( 'Common', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'search_heading',
			[
				'label' => __( 'Search', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'search_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_search' => ''
				],
				'raw' => __( 'Search is not Enable on <strong>Settings</strong>.', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'search_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'search_border',
				'selector' => '{{WRAPPER}} .dataTables_filter input',
			]
		);

		$this->add_control(
			'search_label_color',
			[
				'label' => __( 'Label Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'search_input_background_color',
			[
				'label' => __( 'Input Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter input' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_heading',
			[
				'label' => __( 'Pagination', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'pagination_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_pagination' => ''
				],
				'raw' => __( 'Pagination is not Enable on <strong>Settings</strong>.', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'pagination_top_spacing',
			[
				'label' => __( 'Top Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate ' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dataTables_info ' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __( 'Space between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button.previous' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dataTables_paginate span a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_border',
				'selector' => '{{WRAPPER}} .dataTables_paginate a',
			]
		);

		$this->add_control(
			'show_entries_color',
			[
				'label' => __( 'Show Entries Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_length' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_entries_Counter',
			[
				'label' => __( 'Data Counter Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_info' => 'color: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_pagination' );

        $this->start_controls_tab(
            '_tab_arrow_normal',
            [
                'label' => __( 'Normal', 'happy-addons-pro' ),
            ]
		);

		$this->add_control(
			'pagination_normal_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_normal_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate a' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
            '_tab_active',
            [
                'label' => __( 'Active', 'happy-addons-pro' ),
            ]
		);

		$this->add_control(
			'pagination_active_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate span .paginate_button.current' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate span .paginate_button.current' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_disabled',
            [
                'label' => __( 'Disabled', 'happy-addons-pro' ),
            ]
		);

		$this->add_control(
			'pagination_disabled_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button.disabled' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_disabled_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button.disabled' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
            '_tab_hover',
            [
                'label' => __( 'Hover', 'happy-addons-pro' ),
            ]
		);

		$this->add_control(
			'pagination_hover_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button:not(.disabled):hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button:not(.disabled):hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'pagination_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .dataTables_paginate .paginate_button:not(.disabled):hover' => 'border-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __table_head_style_controls() {

		$this->start_controls_section(
			'_section_table_head',
			[
				'label' => __( 'Table Head', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'table_head_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__head .ha-advanced-table__head-column-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'head_border',
				'selector' => '{{WRAPPER}} .ha-advanced-table__head .ha-advanced-table__head-column-cell',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'head_typography',
				'selector' => '{{WRAPPER}} .ha-advanced-table__head .ha-advanced-table__head-column-cell',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'head_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__head .ha-advanced-table__head-column' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'head_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__head .ha-advanced-table__head-column-cell' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'column_short_icon_color',
			[
				'label' => __( 'Sorting Icon Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__head-column-cell.sorting_asc:before' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .ha-advanced-table__head-column-cell.sorting_desc:after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .ha-advanced-table__head-column-cell.sorting:before' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .ha-advanced-table__head-column-cell.sorting:after' => 'border-top-color: {{VALUE}}'
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_row_style_controls() {

		$this->start_controls_section(
			'_section_table_row',
			[
				'label' => __( 'Table Row', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'table_row_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'row_border',
				'selector' => '{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row-cell',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'row_typography',
				'selector' => '{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row-cell',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->start_controls_tabs( '_tabs_rows' );
		$this->start_controls_tab(
			'_tab_head_row',
			[
				'label' => __( 'Normal', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'row_background_color_even',
			[
				'label' => __( 'Background Color (Even)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(even)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_background_color_odd',
			[
				'label' => __( 'Background Color (Odd)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(odd)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_color_even',
			[
				'label' => __( 'Color (Even) ', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(even)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_color_odd',
			[
				'label' => __( 'Color (Odd)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(odd)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_row',
			[
				'label' => __( 'Hover', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'row_hover_background_color_even',
			[
				'label' => __( 'Background Color (Even)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(even):hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_hover_background_color_odd',
			[
				'label' => __( 'Background Color (Odd)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(odd):hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_hover_color_even',
			[
				'label' => __( 'Color (Even)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(even):hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'row_hover_color_odd',
			[
				'label' => __( 'Color (Odd)', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-advanced-table__body .ha-advanced-table__body-row:nth-child(odd):hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['table_type'] == 'google_sheet' ) {
			$this->google_sheet_render( $this->get_id() );
		} elseif ( $settings['table_type'] == 'database' ) {
			$this->database_table_render();
		} elseif ( $settings['table_type'] == 'table_press' ) {
			$this->tablepress_render();
		} elseif ( $settings['table_type'] == 'import' ) {
			$this->import_table_render();
		}

	}

	protected function attributes() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'data-table', 'class', 'ha-advanced-table' );
		$this->add_render_attribute( 'data-table', 'data-widget-id', $this->get_id() );

		if ( $settings['show_search'] == 'yes' ) {
			$this->add_render_attribute( 'data-table', 'data-search', 'true' );
		} else $this->add_render_attribute( 'data-table', 'data-search', 'false' );

		if ( $settings['show_pagination'] == 'yes' ) {
			$this->add_render_attribute( 'data-table', 'data-paging', 'true' );
		} else $this->add_render_attribute( 'data-table', 'data-paging', 'false' );

		if ( ! empty( $settings['table_width']['size'] ) ) {
			$this->add_render_attribute( 'data-table', 'data-scroll-x', 'true' );
		}

		if ( $settings['export_table'] == 'yes' ) {
			$this->add_render_attribute( 'data-table', 'data-export-table-text', esc_attr( $settings['export_table_text'] ) );
			if ( $settings['allow_visitors_export_table'] == 'yes' ) {
				$this->add_render_attribute( 'data-table', 'data-export-table', 'true' );
			} else {
				is_admin() == true ? $this->add_render_attribute( 'data-table', 'data-export-table', 'true' ) : null;
			}
		}
	}

	protected function google_sheet_render($id) {
		$settings = $this->get_settings_for_display();

		if( 'global' == $settings['credentials'] && is_array( ha_get_credentials('advanced_data_table') ) ){
			$credentials = ha_get_credentials('advanced_data_table');
			$api_key = esc_html( $credentials['api_key'] );
			$sheet_id = esc_html( $credentials['sheet_id'] );
			$range = $credentials['sheet_range'] ? str_replace(':', '%3A', esc_html( trim( $credentials['sheet_range'] ) ) ) : '';
		}else {
			$api_key = esc_html( $settings['api_key'] );
			$sheet_id = esc_html( $settings['sheet_id'] );
			$range = $settings['sheet_range'] ? str_replace(':', '%3A', esc_html( trim( $settings['sheet_range'] ) ) ) : '';
		}

		$error_message = [];

		$base_url = 'https://sheets.googleapis.com/v4/spreadsheets/';
		$parameters = '?dateTimeRenderOption=FORMATTED_STRING&majorDimension=ROWS&valueRenderOption=FORMATTED_VALUE&key=';
		$url = $base_url . $sheet_id .'/values/'. $range . $parameters . $api_key;

		// error handling for editor fields.
		if ( empty( $api_key ) ) {
			$error_message[] = __( 'Add API key', 'happy-addons-pro' );
		} elseif ( empty( $sheet_id ) ) {
			$error_message[] = __( 'Add Google Sheets ID', 'happy-addons-pro' );
		} elseif ( empty( $range ) ) {
			$error_message[] = __( 'Add Sheets Range', 'happy-addons-pro' );
		}
		if ( ! empty( $error_message ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', $error_message[0] );
		}

		$transient_key = $id . '_data_table_cash';
		$table_data = get_transient( $transient_key );

		if ( false === $table_data ) {
			$data = wp_remote_get( $url );
			$table_data = json_decode( wp_remote_retrieve_body( $data ), true );
			set_transient( $transient_key, $table_data, 0 );
		}
		if ( $settings['remove_cash'] == 'yes' ) {
			delete_transient( $transient_key );
		}

		// error handling for google sheet
		if ( empty( $table_data ) ) {
			$error_message['sheet_empty'] = __( 'Google Sheet is Empty', 'happy-addons-pro' );
			return printf( '<div class="ha-data-table-error">%s</div>', $error_message['sheet_empty'] );
		} elseif ( ! empty( $table_data ) && ! empty( $table_data['error'] ) ) {
			$error_message['sheet_error'] = $table_data['error']['message'];

			if ( ! empty( $error_message['sheet_error'] ) ) {
				return printf( '<div class="ha-data-table-error">%s</div>', $error_message['sheet_error'] );
			}
		}

		// echo "<pre>";
		// print_r($table_data);
		// echo "<pre>";
		$table_columns = $table_data['values'][0];
		$table_rows = array_splice($table_data['values'], 1, count( $table_data['values'] ) );

		$this->attributes();
		?>

		<table <?php echo $this->get_render_attribute_string( 'data-table' ); ?>>

			<thead class="ha-advanced-table__head">
				<tr class="ha-advanced-table__head-column">
					<?php foreach ( $table_columns as $column ) : ?>
						<th class="ha-advanced-table__head-column-cell"><?php echo esc_html( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody class="ha-advanced-table__body">
				 <?php
				 for( $i = 0; $i < count( $table_rows ); $i++ ) :
					if ( count( $table_columns ) > count( $table_rows[$i] ) ) {
						$diference = count( $table_columns ) - count( $table_rows[$i] );

						for( $j = 0; $j < $diference; $j++ ) {
							array_push( $table_rows[$i], null );
						}
					}
					?>
					<tr class="ha-advanced-table__body-row">
						<?php
						foreach ( $table_rows[$i] as $row ) :
						$cell = $row == null ? '' : $row;
							?>
							<td class="ha-advanced-table__body-row-cell"><?php echo esc_html( $cell ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endfor; ?>

			</tbody>

		</table>

		<?php
	}

	protected function database_table_render() {
		global $wpdb;

		$settings = $this->get_settings_for_display();

		$table_name = $settings["database_tables_list"];
		if ( empty( $table_name ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', __( 'Select Table', 'happy-addons-pro' ) );
		}
		$table = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

		if ( is_wp_error( $table ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', $table->get_error_message() );
		}

		$this->attributes();
		?>

		<table <?php echo $this->get_render_attribute_string( 'data-table' ); ?>>
			<thead class="ha-advanced-table__head">
				<tr class="ha-advanced-table__head-column">
					<?php foreach ( array_keys($table[0]) as $key => $column ) : ?>
						<th class="ha-advanced-table__head-column-cell"><?php echo esc_html( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody class="ha-advanced-table__body">
				<?php for( $i = 0; $i < count( $table ); $i++ ) : ?>
					<tr class="ha-advanced-table__body-row">
						<?php foreach ( $table[$i] as $row ) : ?>
							<td class="ha-advanced-table__body-row-cell"><?php echo esc_html( $row ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>

		<?php
	}

	protected function tablepress_render() {
		$settings = $this->get_settings_for_display();

		if ( ! hapro_is_table_press_activated() ) {
			return printf( '<div class="ha-data-table-error">%s</div>', __( 'Install TablePress', 'happy-addons-pro' ) );
		}
		if ( empty( hapro_get_table_press_list() ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', __( 'Create Table', 'happy-addons-pro' ) );
		}
		if ( empty( $settings['table_press_list'] ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', __( 'Select Table', 'happy-addons-pro' ) );
		}

		$tables = [];
		$tables_option = get_option( 'tablepress_tables', '{}' );
        $tables_opt = json_decode( $tables_option, true );
		$tables = $tables_opt['table_post'];
        $table_id = $tables[$settings['table_press_list']];
        $table_data = get_post_field( 'post_content', $table_id );
		$tables = json_decode( $table_data, true );

		$this->attributes();
		?>

		<table <?php echo $this->get_render_attribute_string( 'data-table' ); ?>>
			<thead class="ha-advanced-table__head">
				<tr class="ha-advanced-table__head-column">
					<?php foreach ( $tables[0] as $key => $column ) : ?>
						<th class="ha-advanced-table__head-column-cell"><?php echo wp_kses_post( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody class="ha-advanced-table__body">
				<?php for( $i = 0; $i < count( $tables );  ) : ++$i; ?>
					<tr class="ha-advanced-table__body-row">
						<?php foreach ( $tables[$i] as $row ) : ?>
							<td class="ha-advanced-table__body-row-cell"><?php echo wp_kses_post( $row ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>

		<?php
	}

	protected function import_table_render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['import_table_data'] ) ) {
			return printf( '<div class="ha-data-table-error">%s</div>', __( 'Paste Data in CSV format.', 'happy-addons-pro' ) );
		}

		$table_data = explode( "\n", $settings['import_table_data'] );
		$columns = explode( ',', $table_data[0] );
		$table_rows = array_splice( $table_data, 1, count( $table_data ) );

		$this->attributes();
		?>

		<table <?php echo $this->get_render_attribute_string( 'data-table' ); ?>>
			<thead class="ha-advanced-table__head">
				<tr class="ha-advanced-table__head-column">
					<?php foreach ( $columns as $key => $column ) : ?>
						<th class="ha-advanced-table__head-column-cell"><?php echo wp_kses_post( $column ); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody class="ha-advanced-table__body">
				<?php for( $i = 0; $i < count( $table_rows ); $i++ ) :
					$rows = explode( ',', $table_rows[$i]);
					?>
					<tr class="ha-advanced-table__body-row">
						<?php foreach ( $rows as $row ) : ?>
							<td class="ha-advanced-table__body-row-cell"><?php echo wp_kses_post( $row ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>

		<?php
	}

}
