<?php
/**
 * UAEL Table.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Table\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Table.
 */
class Table extends Common_Widget {

	/**
	 * Retrieve Table Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Table' );
	}

	/**
	 * Retrieve Table Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Table' );
	}

	/**
	 * Retrieve Table Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Table' );
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
		return parent::get_widget_keywords( 'Table' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-datatable', 'uael-table' );
	}

	/**
	 * Register General Content controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_header_content_controls();
		$this->register_body_content_controls();
		$this->register_adv_content_controls();

		$this->register_header_style_controls();
		$this->register_body_style_controls();
		$this->register_icon_image_controls();
		$this->register_search_controls();
		$this->register_helpful_information();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_header_content_controls() {

		$condition = array();

		// Table header settings.
		$this->start_controls_section(
			'section_table_header',
			array(
				'label' => __( 'Table Header', 'uael' ),
			)
		);

			$this->add_control(
				'source',
				array(
					'label'   => __( 'Source', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'manual',
					'options' => array(
						'manual' => __( 'Manual', 'uael' ),
						'file'   => __( 'CSV File', 'uael' ),
					),
				)
			);

			$this->add_control(
				'file',
				array(
					'label'     => __( 'Upload a CSV File', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'source' => 'file',
					),
				)
			);
		if ( parent::is_internal_links() ) {
			$this->add_control(
				'file_help_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( 'Note: Facing issue with %1$sCSV importer?%2$s Please read %3$sthis%2$s article for troubleshooting steps.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/create-table-by-uploading-csv/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>', '<a href=' . UAEL_DOMAIN . 'docs/facing-issues-with-csv-import/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'source' => 'file',
					),
				)
			);
		}

			// Repeater object created.
			$repeater = new Repeater();

			// Content Type Row/Col.
			$repeater->add_control(
				'header_content_type',
				array(
					'label'   => __( 'Action', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'cell',
					'options' => array(
						'row'  => __( 'Start New Row', 'uael' ),
						'cell' => __( 'Add New Cell', 'uael' ),
					),
				)
			);

			// Table heading border Row/Cell Note.
			$repeater->add_control(
				'add_head_cell_row_description',
				array(
					'label'     => '',
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'You have started a new row. Please add new cells in your row by clicking <b>Add Item</b> button below.', 'uael' ) ),
					'condition' => array(
						'header_content_type' => 'row',
					),
				)
			);

			// Start control tab.
			$repeater->start_controls_tabs( 'items_repeater' );

				// Start control content tab.
				$repeater->start_controls_tab(
					'tab_head_content',
					array(
						'label'     => __( 'CONTENT', 'uael' ),
						'condition' => array(
							'header_content_type' => 'cell',
						),
					)
				);

						// table heading text.
						$repeater->add_control(
							'heading_text',
							array(
								'label'     => __( 'Text', 'uael' ),
								'type'      => Controls_Manager::TEXT,
								'dynamic'   => array(
									'active' => true,
								),
								'condition' => array(
									'header_content_type' => 'cell',
								),
							)
						);

				$repeater->end_controls_tab();

				// Start control content tab.
				$repeater->start_controls_tab(
					'tab_head_icon',
					array(
						'label'     => __( 'ICON / IMAGE', 'uael' ),
						'condition' => array(
							'header_content_type' => 'cell',
						),
					)
				);

					// Content Type Icon/Image.
					$repeater->add_control(
						'header_content_icon_image',
						array(
							'label'   => __( 'Select', 'uael' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'icon',
							'options' => array(
								'icon'  => __( 'Icon', 'uael' ),
								'image' => __( 'Image', 'uael' ),
							),
						)
					);

		if ( UAEL_Helper::is_elementor_updated() ) {

			// Single select icon.
			$repeater->add_control(
				'new_heading_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'heading_icon',
					'condition'        => array(
						'header_content_type'       => 'cell',
						'header_content_icon_image' => 'icon',
					),
					'render_type'      => 'template',
				)
			);

		} else {
			// Single select icon.
			$repeater->add_control(
				'heading_icon',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'condition' => array(
						'header_content_type'       => 'cell',
						'header_content_icon_image' => 'icon',
					),
				)
			);

		}

					// Single Add Image.
					$repeater->add_control(
						'head_image',
						array(
							'label'     => __( 'Choose Image', 'uael' ),
							'type'      => Controls_Manager::MEDIA,
							'dynamic'   => array(
								'active' => true,
							),
							'condition' => array(
								'header_content_type' => 'cell',
								'header_content_icon_image' => 'image',
							),
						)
					);

					$repeater->end_controls_tab();

					// Start control content tab.
					$repeater->start_controls_tab(
						'tab_head_advance',
						array(
							'label'     => __( 'ADVANCE', 'uael' ),
							'condition' => array(
								'header_content_type' => 'cell',
							),
						)
					);

					// Table header column span.
					$repeater->add_control(
						'heading_col_span',
						array(
							'label'     => __( 'Column Span', 'uael' ),
							'title'     => __( 'How many columns should this column span across.', 'uael' ),
							'type'      => Controls_Manager::NUMBER,
							'default'   => 1,
							'min'       => 1,
							'max'       => 20,
							'step'      => 1,
							'condition' => array(
								'header_content_type' => 'cell',
							),
						)
					);

					// Cell row Span.
					$repeater->add_control(
						'heading_row_span',
						array(
							'label'     => __( 'Row Span', 'uael' ),
							'title'     => __( 'How many rows should this column span across.', 'uael' ),
							'type'      => Controls_Manager::NUMBER,
							'default'   => 1,
							'min'       => 1,
							'max'       => 25,
							'step'      => 1,
							'separator' => 'below',
							'condition' => array(
								'header_content_type' => 'cell',
							),
						)
					);

					// Cell row Span.
					$repeater->add_control(
						'heading_row_width',
						array(
							'label'      => __( 'Column Width', 'uael' ),
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
							'separator'  => 'below',
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}.uael-table-col' => 'width: {{SIZE}}{{UNIT}}',
							),
							'condition'  => array(
								'header_content_type' => 'cell',
							),
						)
					);

					// Single Header Text Color.
					$repeater->add_control(
						'single_heading_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-table-row {{CURRENT_ITEM}} .uael-table__text, {{WRAPPER}} tbody .uael-table-head{{CURRENT_ITEM}} .uael-table__text' => 'color: {{VALUE}};',
								'{{WRAPPER}} .uael-table-row {{CURRENT_ITEM}} .uael-table__text svg' => 'fill: {{VALUE}};',
							),
							'condition' => array(
								'header_content_type' => 'cell',
							),
						)
					);

					// Single Header Background Color.
					$repeater->add_control(
						'single_heading_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} thead .uael-table-row {{CURRENT_ITEM}},{{WRAPPER}} .uael-table-row .uael-table-head{{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'header_content_type' => 'cell',
							),
						)
					);

					$repeater->add_control(
						'show_head_id_class',
						array(
							'label'        => __( 'Additional Settings', 'uael' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => __( 'Show', 'uael' ),
							'label_off'    => __( 'Hide', 'uael' ),
							'return_value' => 'yes',
							'default'      => 'no',
						)
					);

					$repeater->add_control(
						'table_head_cell_id',
						array(
							'label'          => __( 'CSS ID', 'uael' ),
							'type'           => Controls_Manager::TEXT,
							'dynamic'        => array(
								'active' => true,
							),
							'title'          => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'uael' ),
							'style_transfer' => false,
							'render_type'    => 'template',
							'condition'      => array(
								'show_head_id_class' => 'yes',
							),
						)
					);

					$repeater->add_control(
						'table_head_cell_class',
						array(
							'label'          => __( 'CSS Classes', 'uael' ),
							'type'           => Controls_Manager::TEXT,
							'dynamic'        => array(
								'active' => true,
							),
							'title'          => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'uael' ),
							'style_transfer' => false,
							'render_type'    => 'template',
							'condition'      => array(
								'show_head_id_class' => 'yes',
							),
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tab();

			// Repeater set default values.
			$this->add_control(
				'table_headings',
				array(
					'type'        => Controls_Manager::REPEATER,
					'show_label'  => true,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ header_content_type }}: {{ heading_text }}',
					'default'     => array(
						array(
							'header_content_type' => 'row',
						),
						array(
							'header_content_type' => 'cell',
							'heading_text'        => __( 'Sample ID', 'uael' ),
						),
						array(
							'header_content_type' => 'cell',
							'heading_text'        => __( 'Heading 1', 'uael' ),
						),
						array(
							'header_content_type' => 'cell',
							'heading_text'        => __( 'Heading 2', 'uael' ),
						),
					),
					'condition'   => array(
						'source' => 'manual',
					),
				)
			);

			$this->add_control(
				'table_responsive',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => __( 'Responsive Support', 'uael' ),
					'description' => __( 'Note: Advance settings will not work if Responsive Support is Enabled.', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => __( 'On', 'uael' ),
					'label_off'   => __( 'Off', 'uael' ),
					'default'     => 'no',
				)
			);

			// Sticky Heading.
			$this->add_control(
				'sticky_table_heading',
				array(
					'label'        => __( 'Sticky Header', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'ON', 'uael' ),
					'label_off'    => __( 'OFF', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'prefix_class' => 'uael-header-sticky-',
				)
			);

			// Sticky Heading Note.
			$this->add_control(
				'sticky_table_heading_description',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf( '<p style="font-size: 11px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Note: Sticky Header will not work if "Responsive Support" option is enabled.', 'uael' ) ),
					'condition' => array(
						'table_responsive' => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_body_content_controls() {

		// Table content.
		$this->start_controls_section(
			'section_table_content',
			array(
				'label'     => __( 'Table Content', 'uael' ),
				'condition' => array(
					'source' => 'manual',
				),
			)
		);

		// Repeater obj for content.
		$repeater_content = new Repeater();

		// Content Type Row/Col.
		$repeater_content->add_control(
			'content_type',
			array(
				'label'   => __( 'Action', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cell',
				'options' => array(
					'row'  => __( 'Start New Row', 'uael' ),
					'cell' => __( 'Add New Cell', 'uael' ),
				),
			)
		);

		// Table heading border Row/Cell Note.
		$repeater_content->add_control(
			'add_body_cell_row_description',
			array(
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'You have started a new row. Please add new cells in your row by clicking <b>Add Item</b> button below.', 'uael' ) ),
				'condition' => array(
					'content_type' => 'row',
				),
			)
		);

		// Start control tab.
		$repeater_content->start_controls_tabs( 'items_repeater' );

			// Start control content tab.
			$repeater_content->start_controls_tab(
				'tab_content',
				array(
					'label'     => __( 'Content', 'uael' ),
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

				// Single Cell text.
				$repeater_content->add_control(
					'cell_text',
					array(
						'label'     => __( 'Text', 'uael' ),
						'type'      => Controls_Manager::TEXTAREA,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'content_type' => 'cell',
						),
					)
				);

				// Single Cell LINK.
				$repeater_content->add_control(
					'link',
					array(
						'label'       => __( 'Link', 'uael' ),
						'type'        => Controls_Manager::URL,
						'placeholder' => '#',
						'dynamic'     => array(
							'active' => true,
						),
						'default'     => array(
							'url' => '',
						),
						'condition'   => array(
							'content_type' => 'cell',
						),
					)
				);

			// End Content control tab.
			$repeater_content->end_controls_tab();

			// Start Media Tab.
			$repeater_content->start_controls_tab(
				'tab_media',
				array(
					'label'     => __( 'ICON / IMAGE', 'uael' ),
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

				// Content Type Icon/Image.
				$repeater_content->add_control(
					'cell_content_icon_image',
					array(
						'label'   => __( 'Select', 'uael' ),
						'type'    => Controls_Manager::SELECT,
						'default' => 'icon',
						'options' => array(
							'icon'  => __( 'Icon', 'uael' ),
							'image' => __( 'Image', 'uael' ),
						),
					)
				);

		if ( UAEL_Helper::is_elementor_updated() ) {

			// Single Cell Icon.
			$repeater_content->add_control(
				'new_cell_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'cell_icon',
					'condition'        => array(
						'content_type'            => 'cell',
						'cell_content_icon_image' => 'icon',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			// Single Cell Icon.
			$repeater_content->add_control(
				'cell_icon',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'condition' => array(
						'content_type'            => 'cell',
						'cell_content_icon_image' => 'icon',
					),
				)
			);
		}

				// Single Add Image.
				$repeater_content->add_control(
					'image',
					array(
						'label'     => __( 'Choose Image', 'uael' ),
						'type'      => Controls_Manager::MEDIA,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'content_type'            => 'cell',
							'cell_content_icon_image' => 'image',
						),
					)
				);

			// End Media control tab.
			$repeater_content->end_controls_tab();

			// Start Media Tab.
			$repeater_content->start_controls_tab(
				'tab_advance_cells',
				array(
					'label'     => __( 'Advance', 'uael' ),
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

			// Cell Column Span.
			$repeater_content->add_control(
				'cell_span',
				array(
					'label'     => __( 'Column Span', 'uael' ),
					'title'     => __( 'How many columns should this column span across.', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 1,
					'min'       => 1,
					'max'       => 20,
					'step'      => 1,
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

			// Cell row Span.
			$repeater_content->add_control(
				'cell_row_span',
				array(
					'label'     => __( 'Row Span', 'uael' ),
					'title'     => __( 'How many rows should this column span across.', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 1,
					'min'       => 1,
					'max'       => 25,
					'step'      => 1,
					'separator' => 'below',
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

			// Cell Column Span.
			$repeater_content->add_control(
				'table_th_td',
				array(
					'label'       => __( 'Convert this Cell into Table Heading?', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => array(
						'td' => __( 'No', 'uael' ),
						'th' => __( 'Yes', 'uael' ),
					),
					'default'     => 'td',
					'condition'   => array(
						'content_type' => 'cell',
					),
					'label_block' => true,
				)
			);

			// Single Cell Color.
			$repeater_content->add_control(
				'single_cell_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} table[data-responsive="horizontal"] .uael-table-row {{CURRENT_ITEM}} .uael-table__text,{{WRAPPER}} table[data-responsive="no"] .uael-table-row {{CURRENT_ITEM}} span.uael-table__text,{{WRAPPER}} table[data-responsive="yes"] .uael-table-row {{CURRENT_ITEM}} div.uael-table-head + span.uael-table__text' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-table-row {{CURRENT_ITEM}} .uael-table__text svg' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

			// Single Cell Background Color.
			$repeater_content->add_control(
				'single_cell_background_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} tbody .uael-table-row {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'content_type' => 'cell',
					),
				)
			);

		$repeater_content->add_control(
			'show_content_id_class',
			array(
				'label'        => __( 'Additional Settings', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$repeater_content->add_control(
			'table_content_cell_id',
			array(
				'label'          => __( 'CSS ID', 'uael' ),
				'type'           => Controls_Manager::TEXT,
				'dynamic'        => array(
					'active' => true,
				),
				'title'          => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'uael' ),
				'style_transfer' => false,
				'render_type'    => 'template',
				'condition'      => array(
					'show_content_id_class' => 'yes',
				),
			)
		);

		$repeater_content->add_control(
			'table_content_cell_class',
			array(
				'label'          => __( 'CSS Classes', 'uael' ),
				'type'           => Controls_Manager::TEXT,
				'dynamic'        => array(
					'active' => true,
				),
				'title'          => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'uael' ),
				'style_transfer' => false,
				'render_type'    => 'template',
				'condition'      => array(
					'show_content_id_class' => 'yes',
				),
			)
		);

			// End Media control tab.
			$repeater_content->end_controls_tab();

		// End control tab.
		$repeater_content->end_controls_tabs();

		// Repeater set default values.
		$this->add_control(
			'table_content',
			array(
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'content_type' => 'row',
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Sample #1', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 1, Content 1', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 1, Content 2', 'uael' ),
					),
					array(
						'content_type' => 'row',
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Sample #2', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 2, Content 1', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 2, Content 2', 'uael' ),
					),
					array(
						'content_type' => 'row',
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Sample #3', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 3, Content 1', 'uael' ),
					),
					array(
						'content_type' => 'cell',
						'cell_text'    => __( 'Row 3, Content 2', 'uael' ),
					),
				),
				'fields'      => $repeater_content->get_controls(),
				'title_field' => '{{ content_type }}: {{ cell_text }}',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_adv_content_controls() {

		// Column style starts.
		$this->start_controls_section(
			'section_advance_settings',
			array(
				'label'     => __( 'Advance Settings', 'uael' ),
				'condition' => array(
					'table_responsive!' => 'yes',
				),
			)
		);

			// Sortable Table Switcher.
			$this->add_control(
				'sortable',
				array(
					'label'        => __( 'Sortable Table', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'YES', 'uael' ),
					'label_off'    => __( 'NO', 'uael' ),
					'description'  => __( 'Sort table entries on the click of table headings.', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'sortable_dropdown',
				array(
					'label'        => __( 'Sortable Dropdown', 'uael' ),
					'description'  => __( 'This will show dropdown menu to sort the table by columns', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'show',
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'show',
					'condition'    => array(
						'sortable' => 'yes',
					),
				)
			);

			// Searchable Table Switcher.
			$this->add_control(
				'searchable',
				array(
					'label'        => __( 'Searchable Table', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'description'  => __( 'Search/filter table entries easily.', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			// Sort text.
			$this->add_control(
				'search_text',
				array(
					'label'     => __( 'Search Label', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'default'   => __( 'Search:', 'uael' ),
					'condition' => array(
						'searchable' => 'yes',
					),
				)
			);

			$this->add_control(
				'show_entries',
				array(
					'label'        => __( 'Show Entries Dropdown', 'uael' ),
					'description'  => __( 'Controls the number of entries in a table.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_header_style_controls() {

		// Header heading style.
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => __( 'Table Header', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Header typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} th.uael-table-col,{{WRAPPER}} tr.uael-table-row div.responsive-header-text span.uael-table__text-inners',
			)
		);

		// Header padding.
		$this->add_responsive_control(
			'cell_padding_head',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '15',
					'bottom'   => '15',
					'left'     => '15',
					'right'    => '15',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} th.uael-table-col, {{WRAPPER}} tbody .uael-table-col .uael-table-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Header text alignment.
		$this->add_responsive_control(
			'cell_align_head',
			array(
				'label'     => __( 'Text Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
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
					'{{WRAPPER}} th .uael-table__text,{{WRAPPER}} tbody .uael-table-col .uael-table-head .uael-table__text' => 'text-align: {{VALUE}};width: 100%;',
				),
			)
		);

		// Header tabs starts here.
		$this->start_controls_tabs( 'tabs_header_colors_row' );

			// Header Default tab starts.
			$this->start_controls_tab( 'tab_header_colors_row', array( 'label' => __( 'Default', 'uael' ) ) );

				// Header row color default.
				$this->add_control(
					'header_cell_color_row',
					array(
						'label'     => __( 'Row Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
						'selectors' => array(
							'{{WRAPPER}} thead .uael-table-row th .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} thead .uael-table-row th .uael-table__text svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} th' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row th' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-head .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-head .uael-table__text svg' => 'fill: {{VALUE}};',
						),
					)
				);

				// Header row background color default.
				$this->add_control(
					'header_cell_background_row',
					array(
						'label'     => __( 'Row Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} thead .uael-table-row th' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row th, {{WRAPPER}} tbody .uael-table-col .uael-table-head' => 'background-color: {{VALUE}};',
						),
					)
				);

				// Advanced Setting for header Switcher.
				$this->add_control(
					'header_border_styling',
					array(
						'label'        => __( 'Apply Border To', 'uael' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'CELL', 'uael' ),
						'label_off'    => __( 'ROW', 'uael' ),
						'return_value' => 'yes',
						'default'      => 'yes',
						'prefix_class' => 'uael-border-',
					)
				);

				// Table heading border Row/Cell Note.
				$this->add_control(
					'head_border_note',
					array(
						'type' => Controls_Manager::RAW_HTML,
						'raw'  => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Note: By default, the border will be applied to cells. You can change it to row by using the above setting.', 'uael' ) ),
					)
				);

				// Header row border.
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'           => 'row_border_head',
						'label'          => __( 'Row Border', 'uael' ),
						'fields_options' => array(
							'border' => array(
								'default' => 'solid',
							),
							'width'  => array(
								'default' => array(
									'top'      => '1',
									'right'    => '1',
									'bottom'   => '1',
									'left'     => '1',
									'isLinked' => true,
								),
							),
							'color'  => array(
								'default' => '#bbb',
							),
						),
						'selector'       => '{{WRAPPER}} thead tr.uael-table-row, {{WRAPPER}} tbody .uael-table-row th',
						'condition'      => array(
							'header_border_styling!' => 'yes',
						),
					)
				);

				// Header Cell border.
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'           => 'cell_border_head',
						'label'          => __( 'Cell Border', 'uael' ),
						'selector'       => '{{WRAPPER}} th.uael-table-col, {{WRAPPER}} tbody .uael-table-row th, {{WRAPPER}} tbody .uael-table-row .uael-table-head, {{WRAPPER}} tr.uael-table-row div.responsive-header-text, {{WRAPPER}}.elementor-widget-uael-table .uael-table-wrapper table[data-responsive="yes"] tbody tr.uael-table-row div.responsive-header-text',
						'fields_options' => array(
							'border' => array(
								'default' => 'solid',
							),
							'width'  => array(
								'default' => array(
									'top'      => '1',
									'right'    => '1',
									'bottom'   => '1',
									'left'     => '1',
									'isLinked' => true,
								),
							),
							'color'  => array(
								'default' => '#bbb',
							),
						),
						'condition'      => array(
							'header_border_styling' => 'yes',
						),
					)
				);

			$this->end_controls_tab();

			// Tab header hover.
			$this->start_controls_tab( 'tab_header_hover_colors_row', array( 'label' => __( 'Hover', 'uael' ) ) );

				// Header text row color hover.
				$this->add_control(
					'header_cell_hover_color_row',
					array(
						'label'     => __( 'Row Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} thead .uael-table-row:hover .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} thead .uael-table-row:hover .uael-table__text svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover th .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover th .uael-table__text svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} .uael-table-row:hover th' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover .uael-table-head .uael-table__text' => 'color: {{VALUE}};',
						),
					)
				);

				// Header row background color hover.
				$this->add_control(
					'header_cell_hover_background_row',
					array(
						'label'     => __( 'Row Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} thead .uael-table-row:hover > th' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .uael-table tbody .uael-table-row:hover > th' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover .uael-table-head'  => 'background-color: {{VALUE}};',
						),
					)
				);

				// Header cell hover text color.
				$this->add_control(
					'header_cell_hover_color',
					array(
						'label'     => __( 'Cell Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} thead th.uael-table-col:hover .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row th.uael-table-col:hover .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tr.uael-table-row th.uael-table-col:hover' => 'color: {{VALUE}};',
							'{{WRAPPER}} thead th.uael-table-col:hover .uael-table__text svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row th.uael-table-col:hover .uael-table__text svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row .uael-table-head:hover .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row .uael-table-head:hover .uael-table__text svg' => 'fill: {{VALUE}};',
						),
					)
				);

				// Header cell hover background color.
				$this->add_control(
					'header_cell_hover_background',
					array(
						'label'     => __( 'Cell Hover Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} thead .uael-table-row th.uael-table-col:hover' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .uael-table tbody .uael-table-row:hover >  th.uael-table-col:hover' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-col .uael-table-head:hover' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row .uael-table-head:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_body_style_controls() {

		// Rows style tab heading.
		$this->start_controls_section(
			'section_table_body_style',
			array(
				'label' => __( 'Table Body', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Cell Typograghy.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cell_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '(desktop){{WRAPPER}} td div:not(.responsive-header-text) .uael-table__text-inner, {{WRAPPER}} td div + .uael-table__text-inner,{{WRAPPER}} tbody .uael-table__text:not(.uael-tbody-head-text),{{WRAPPER}} td .uael-align-icon--left,{{WRAPPER}} td .uael-align-icon--right',
			)
		);

		// Cell padding.

		$this->add_responsive_control(
			'cell_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '15',
					'bottom'   => '15',
					'left'     => '15',
					'right'    => '15',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} tbody td.uael-table-col' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cell_padding_note',
			array(
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Note: Padding will not work on responsive devices if Responsive Support is Enabled.', 'uael' ) ),
				'condition' => array(
					'table_responsive' => 'yes',
				),
			)
		);

		// Cell text alignment.
		$this->add_responsive_control(
			'cell_align',
			array(
				'label'     => __( 'Text Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => '',
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
					'{{WRAPPER}} td .uael-table__text' => 'text-align: {{VALUE}};    width: 100%;',
				),
			)
		);

		// Cell text alignment.
		$this->add_responsive_control(
			'cell_valign',
			array(
				'label'     => __( 'Vertical Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'middle',
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'uael' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-table-row .uael-table-col' => 'vertical-align: {{VALUE}};',
				),
			)
		);

		// Tab control starts.
		$this->start_controls_tabs( 'tabs_cell_colors' );

			// Tab Default starts.
			$this->start_controls_tab( 'tab_cell_colors', array( 'label' => __( 'Default', 'uael' ) ) );

				// Cell Color Default.
				$this->add_control(
					'cell_color',
					array(
						'label'     => __( 'Row Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
						'selectors' => array(
							'{{WRAPPER}} tbody td.uael-table-col .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody td.uael-table-col .uael-table__text svg' => 'fill: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'striped_effect_feature',
					array(
						'label'        => __( 'Striped Effect', 'uael' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'YES', 'uael' ),
						'label_off'    => __( 'NO', 'uael' ),
						'return_value' => 'yes',
						'default'      => 'yes',
					)
				);

				// Striped effect (Odd Rows).
				$this->add_control(
					'striped_effect_odd',
					array(
						'label'     => __( 'Striped Odd Rows Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#eaeaea',
						'selectors' => array(
							'{{WRAPPER}} tbody tr:nth-child(odd)' => 'background: {{VALUE}};',
						),
						'condition' => array(
							'striped_effect_feature' => 'yes',
						),
					)
				);

				// Striped effect (Even Rows).
				$this->add_control(
					'striped_effect_even',
					array(
						'label'     => __( 'Striped Even Rows Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#FFFFFF',
						'selectors' => array(
							'{{WRAPPER}} tbody tr:nth-child(even)' => 'background: {{VALUE}};',
						),
						'condition' => array(
							'striped_effect_feature' => 'yes',
						),
					)
				);

				$this->add_control(
					'striped_note',
					array(
						'type'      => Controls_Manager::RAW_HTML,
						'raw'       => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Note: Striped effect will not work on responsive devices.', 'uael' ) ),
						'condition' => array(
							'table_responsive'       => 'yes',
							'striped_effect_feature' => 'yes',
						),
					)
				);

				// Cell background color default.
				$this->add_control(
					'cell_background',
					array(
						'label'     => __( 'Row Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} tbody .uael-table-row' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'striped_effect_feature!' => 'yes',
						),
					)
				);

				// Advanced Setting for header Switcher.
				$this->add_control(
					'body_border_styling',
					array(
						'label'        => __( 'Apply Border To', 'uael' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'CELL', 'uael' ),
						'label_off'    => __( 'ROW', 'uael' ),
						'return_value' => 'yes',
						'default'      => 'yes',
					)
				);

				// Table body border Row/Cell Note.
				$this->add_control(
					'body_border_note',
					array(
						'type' => Controls_Manager::RAW_HTML,
						'raw'  => sprintf( '<p style="font-size: 12px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Note: By default, the border will be applied to cells. You can change it to row by using the above setting.', 'uael' ) ),
					)
				);

				// Body Row border.
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'           => 'row_border',
						'label'          => __( 'Border', 'uael' ),
						'selector'       => '{{WRAPPER}} tbody .uael-table-row,{{WRAPPER}} tbody .uael-table-row .uael-table-head:first-child',
						'fields_options' => array(
							'border' => array(
								'default' => 'solid',
							),
							'width'  => array(
								'default' => array(
									'top'      => '1',
									'right'    => '1',
									'bottom'   => '1',
									'left'     => '1',
									'isLinked' => true,
								),
							),
							'color'  => array(
								'default' => '#bbb',
							),
						),
						'condition'      => array(
							'body_border_styling!' => 'yes',
						),
					)
				);

				// Body Cell border.
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'           => 'cell_border_body',
						'label'          => __( 'Cell Border', 'uael' ),
						'selector'       => '{{WRAPPER}} td.uael-table-col',
						'fields_options' => array(
							'border' => array(
								'default' => 'solid',
							),
							'width'  => array(
								'default' => array(
									'top'      => '1',
									'right'    => '1',
									'bottom'   => '1',
									'left'     => '1',
									'isLinked' => true,
								),
							),
							'color'  => array(
								'default' => '#bbb',
							),
						),
						'condition'      => array(
							'body_border_styling' => 'yes',
						),
					)
				);

			// Default tab ends here.
			$this->end_controls_tab();

			// Hover tab starts here.
			$this->start_controls_tab( 'tab_cell_hover_colors', array( 'label' => __( 'Hover', 'uael' ) ) );

				// Row hover text color.
				$this->add_control(
					'row_hover_color',
					array(
						'label'     => __( 'Row Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} tbody .uael-table-row:hover td.uael-table-col .uael-table__text:not(.uael-tbody-head-text)' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover td.uael-table-col .uael-table__text svg' => 'fill: {{VALUE}};',
						),
					)
				);

				// Row hover background color.
				$this->add_control(
					'row_hover_background',
					array(
						'label'     => __( 'Row Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} tbody .uael-table-row:hover' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row:hover > .uael-table-col:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

				// Cell color hover.
				$this->add_control(
					'cell_hover_color',
					array(
						'label'     => __( 'Cell Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} table[data-responsive="horizontal"] tbody td.uael-table-col:hover .uael-table__text' => 'color: {{VALUE}};',
							'{{WRAPPER}} tbody .uael-table-row td.uael-table-col:hover .uael-table__text:not(.uael-tbody-head-text)' => 'color: {{VALUE}}',

							'{{WRAPPER}} .uael-table tbody td.uael-table-col:hover .uael-table__text svg' => 'fill: {{VALUE}};',
						),
					)
				);

				// Cell background color hover.
				$this->add_control(
					'cell_hover_background',
					array(
						'label'     => __( 'Cell Hover Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-table tbody .uael-table-row:hover > td.uael-table-col:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

		// Tab control ends.
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_icon_image_controls() {

		// Icon/Image Styling.
		$this->start_controls_section(
			'section_icon_image_style',
			array(
				'label'     => __( 'Icon / Image', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'source' => 'manual',
				),
			)
		);

		// Icon - styling heading.
		$this->add_control(
			'icon_styling_heading',
			array(
				'label' => __( 'Icon', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		// All icon color.
		$this->add_control(
			'all_icon_color',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-align-icon--left i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-align-icon--right i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-align-icon--left svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .uael-align-icon--right svg' => 'fill: {{VALUE}};',
				),
			)
		);

		// All icon size.
		$this->add_responsive_control(
			'all_icon_size',
			array(
				'label'     => __( 'Scale', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					// Item.
					'{{WRAPPER}} .uael-align-icon--left i' => 'font-size: {{SIZE}}px; vertical-align: middle;',
					'{{WRAPPER}} .uael-align-icon--right i' => 'font-size: {{SIZE}}px; vertical-align: middle;',
					'{{WRAPPER}} .uael-align-icon--left svg' => 'height: {{SIZE}}px; width: {{SIZE}}px; vertical-align: middle;',
					'{{WRAPPER}} .uael-align-icon--right svg' => 'height: {{SIZE}}px; width: {{SIZE}}px; vertical-align: middle;',
				),
			)
		);

		// All Icon Position.
		$this->add_control(
			'all_icon_align',
			array(
				'label'   => __( 'Icon Position', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Before', 'uael' ),
					'right' => __( 'After', 'uael' ),
				),
			)
		);

		// All Icon Spacing.
		$this->add_responsive_control(
			'all_icon_indent',
			array(
				'label'     => __( 'Icon Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					// Item.
					'{{WRAPPER}} .uael-align-icon--left'  => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .uael-align-icon--right' => 'margin-left: {{SIZE}}px;',
				),
			)
		);

		// Image - Styling heading.
		$this->add_control(
			'image_styling_heading',
			array(
				'label'     => __( 'Image', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// All Image Size.
		$this->add_responsive_control(
			'all_image_size',
			array(
				'label'      => __( 'Scale', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 30,
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					),
				),
				'selectors'  => array(
					// Item.
					'{{WRAPPER}} .uael-col-img--left'  => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-col-img--right' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// All Image Position.
		$this->add_control(
			'all_image_align',
			array(
				'label'   => __( 'Image Position', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Before', 'uael' ),
					'right' => __( 'After', 'uael' ),
				),
			)
		);

		// All Image Size.
		$this->add_responsive_control(
			'all_image_indent',
			array(
				'label'     => __( 'Image Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					// Item.
					'{{WRAPPER}} .uael-col-img--left'  => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .uael-col-img--right' => 'margin-left: {{SIZE}}px;',
				),
			)
		);

		// All image border radius.
		$this->add_responsive_control(
			'all_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-col-img--left'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-col-img--right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_search_controls() {

		// Icon / Image Styling.
		$this->start_controls_section(
			'section_search_style',
			array(
				'label'     => __( 'Search / Show Entries', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'table_responsive!' => 'yes',
				),
			)
		);

			// All icon color.
			$this->add_control(
				'label_color',
				array(
					'label'     => __( 'Label Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-advance-heading label' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'input_color',
				array(
					'label'     => __( 'Input Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'label_bg_color',
				array(
					'label'     => __( 'Input Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'input_border',
					'label'          => __( 'Input Border', 'uael' ),
					'fields_options' => array(
						'border' => array(
							'default' => 'solid',
						),
						'width'  => array(
							'default' => array(
								'top'      => '1',
								'right'    => '1',
								'bottom'   => '1',
								'left'     => '1',
								'isLinked' => true,
							),
						),
						'color'  => array(
							'default' => '#bbb',
						),
					),
					'selector'       => '{{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'label_typography',
					'label'    => __( 'Typography', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-advance-heading label, {{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input',
				)
			);

			// Cell padding.
			$this->add_responsive_control(
				'input_padding',
				array(
					'label'      => __( 'Input Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'default'    => array(
						'top'      => '10',
						'bottom'   => '10',
						'left'     => '10',
						'right'    => '10',
						'unit'     => 'px',
						'isLinked' => false,
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			// All icon size.
			$this->add_control(
				'input_size',
				array(
					'label'     => __( 'Input Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 200,
					),
					'range'     => array(
						'px' => array(
							'min'  => 1,
							'max'  => 400,
							'step' => 1,
						),
					),
					'selectors' => array(
						// Item.
						'{{WRAPPER}} .uael-advance-heading select, {{WRAPPER}} .uael-advance-heading input' => 'width: {{SIZE}}{{UNIT}}',
					),
				)
			);

			// All icon size.
			$this->add_control(
				'bottom_spacing',
				array(
					'label'     => __( 'Bottom Space', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors' => array(
						// Item.
						'{{WRAPPER}} .uael-advance-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					),
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
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/table-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_0',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=wwu4ZzXrhGc&index=15&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to add rows & columns? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-rows-and-columns-to-the-table/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to add table header? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-table-header-with-table-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to add content in table cell? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-table-content-with-table-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Table styling » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-style-the-table/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_6',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Row / Column span » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-merge-columns-and-rows-in-table/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_9',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Custom Column Width » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-custom-width-to-table-columns/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_10',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Create table by uploading CSV » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/create-table-by-uploading-csv/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_11',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Facing issues with CSV import? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/facing-issues-with-csv-import/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_7',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Sortable / Searchable table » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-sortable-and-searchable-table-how-to-show-entries-dropdown/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_8',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Control on table entries display count » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-sortable-and-searchable-table-how-to-show-entries-dropdown/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Function to identify if it is a first row or not.
	 *
	 * If yes returns false no returns true.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function is_invalid_first_row() {

		$settings = $this->get_settings_for_display();

		if ( 'row' === $settings['table_content'][0]['content_type'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Function to get table HTML from csv file.
	 *
	 * Parse CSV to Table
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function parse_csv() {

		$settings = $this->get_settings_for_display();

		if ( 'file' !== $settings['source'] ) {
			return array(
				'html' => '',
				'rows' => '',
			);
		}
		$response = wp_remote_get(
			$settings['file']['url'],
			array(
				'sslverify' => false,
			)
		);

		if (
			'' === $settings['file']['url'] ||
			is_wp_error( $response ) ||
			200 !== $response['response']['code'] ||
			'.csv' !== substr( $settings['file']['url'], -4 )
		) {
			return array(
				/* translators: 1: <p> 2: </p> */
				'html' => sprintf( __( '%1$sPlease provide a valid CSV file.%2$s', 'uael' ), '<p>', '</p>' ),
				'rows' => '',
			);
		}

		$rows       = array();
		$rows_count = array();
		$upload_dir = wp_upload_dir();
		$file_url   = str_replace( $upload_dir['baseurl'], '', $settings['file']['url'] );

		$file = $upload_dir['basedir'] . $file_url;

		// Attempt to change permissions if not readable.
		if ( ! is_readable( $file ) ) {
			chmod( $file, 0744 ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.chmod_chmod
		}

		// Check if file is writable, then open it in 'read only' mode.
		if ( is_readable( $file ) ) {

			$_file = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

			if ( ! $_file ) {
				return array(
					'html' => __( "File could not be opened. Check the file's permissions to make sure it's readable by your server.", 'uael' ),
					'rows' => '',
				);
			}

			// To sum this part up, all it really does is go row by row.
			// Column by column, saving all the data.
			$file_data = array();

			// Get first row in CSV, which is of course the headers.
			$header = fgetcsv( $_file );

			// @codingStandardsIgnoreStart
			while ( $row = fgetcsv( $_file ) ) {

				foreach ( $header as $i => $key ) {
					$file_data[ $i ] = $row[ $i ];
				}

				$data[] = $file_data;
			}
			// @codingStandardsIgnoreEnd

			fclose( $_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		} else {
			return array(
				'html' => __( "File could not be opened. Check the file's permissions to make sure it's readable by your server.", 'uael' ),
				'rows' => '',
			);
		}

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$rows[ $key ]       = $value;
				$rows_count[ $key ] = count( $value );
			}
		}

		$return['rows'] = $rows_count;

		$heading_count = 0;

		ob_start();
		?>
		<table <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_id' ) ); ?>>
			<thead>
				<?php
				$first_row_h    = true;
				$counter_h      = 1;
				$cell_counter_h = 0;
				$inline_count   = 0;
				$header_text    = array();
				$data_entry     = 0;

				if ( $header ) {
					?>
					<tr <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
					<?php
					foreach ( $header as $hkey => $head ) {

						$repeater_heading_text = $this->get_repeater_setting_key( 'heading_text', 'table_headings', $inline_count );
						$this->add_render_attribute( $repeater_heading_text, 'class', 'uael-table__text-inner' );

						// TH.
						if ( true === $first_row_h ) {
							$this->add_render_attribute( 'current_' . $hkey, 'data-sort', $cell_counter_h );
						}
						$this->add_render_attribute( 'current_' . $hkey, 'class', 'sort-this' );
						$this->add_render_attribute( 'current_' . $hkey, 'class', 'elementor-repeater-item-' . $hkey );
						$this->add_render_attribute( 'current_' . $hkey, 'class', 'uael-table-col' );
						$this->add_render_attribute( 'current_' . $hkey, 'class', 'uael-table-head-cell-text' );
						// Sort Icon.
						if ( 'yes' === $settings['sortable'] && true === $first_row_h ) {
							$this->add_render_attribute( 'icon_sort_' . $hkey, 'class', 'uael-sort-icon' );
						}

						?>
							<th <?php echo wp_kses_post( $this->get_render_attribute_string( 'current_' . esc_attr( $hkey ) ) ); ?> scope="col">
								<span class="sort-style">
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table__text' ) ); ?>>

									<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_heading_text ) ); ?>><?php echo wp_kses_post( $head ); ?></span>
								</span>
								<?php if ( 'yes' === $settings['sortable'] && true === $first_row_h ) { ?>
									<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_sort_' . esc_attr( $hkey ) ) ); ?>></span>
								<?php } ?>
								</span>
							</th>
							<?php
							$header_text[ $cell_counter_h ] = $head;
							$cell_counter_h++;

							$counter_h++;
							$inline_count++;
					}
					?>
					</tr>
					<?php
				}
				?>
			</thead>
			<tbody>
				<!-- ROWS -->
				<?php
				$counter           = 1;
				$cell_counter      = 0;
				$cell_inline_count = 0;

				foreach ( $rows as $row_key => $row ) {
					?>
				<tr data-entry="<?php echo esc_attr( $row_key ) + 1; ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
					<?php
					foreach ( $row as $bkey => $col ) {

						// Cell text inline classes.
						$repeater_cell_text = $this->get_repeater_setting_key( 'cell_text', 'table_content', $cell_inline_count );
						$this->add_render_attribute( $repeater_cell_text, 'class', 'uael-table__text-inner' );

						$this->add_render_attribute( 'uael_table_col' . $bkey, 'class', 'uael-table-col' );
						$this->add_render_attribute( 'uael_table_col' . $bkey, 'class', 'uael-table-body-cell-text' );
						$this->add_render_attribute( 'uael_table_col' . $bkey, 'class', 'elementor-repeater-item-' . $bkey );

						// Fetch corresponding header cell text.
						if ( isset( $header_text[ $cell_counter ] ) && $header_text[ $cell_counter ] ) {
							$this->add_render_attribute( 'uael_table_col' . $bkey, 'data-title', $header_text[ $cell_counter ] );
						}
						?>
						<td <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_col' . esc_attr( $bkey ) ) ); ?>>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table__text' ) ); ?>>

								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_cell_text ) ); ?>><?php echo wp_kses_post( $col ); ?></span>
							</span>
						</td>
						<?php
						// Increment to next cell.
						$cell_counter++;

						$counter++;
						$cell_inline_count++;
					}
					?>
				</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
		$html           = ob_get_clean();
		$return['html'] = $html;
		return $return;
	}

	/**
	 * Display Table Row icons HTML.
	 *
	 * @since 1.16.1
	 * @access public
	 * @param object $row for row settings.
	 */
	public function render_row_icon( $row ) {
		?>

		<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
			<?php
			$body_icon_migrated = isset( $row['__fa4_migrated']['new_cell_icon'] );
			$body_icon_is_new   = empty( $row['cell_icon'] );
			?>
			<?php if ( isset( $row['cell_icon'] ) || isset( $row['new_cell_icon'] ) ) { ?>
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_cell_icon_align' . esc_attr( $row['_id'] ) ) ); ?>>
					<?php
					if ( $body_icon_migrated || $body_icon_is_new ) {
						\Elementor\Icons_Manager::render_icon( $row['new_cell_icon'], array( 'aria-hidden' => 'true' ) );
					} else {
						?>
						<i class="<?php echo esc_attr( $row['cell_icon'] ); ?>"></i>
					<?php } ?>
				</span>
			<?php } ?>
		<?php } elseif ( isset( $row['cell_icon'] ) ) { ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_cell_icon_align' . esc_attr( $row['_id'] ) ) ); ?>>
				<i class="<?php echo esc_attr( $row['cell_icon'] ); ?>"></i>
			</span>
			<?php

		}
	}

	/**
	 * Display Table heading icons HTML.
	 *
	 * @since 1.16.1
	 * @access public
	 * @param object $head for head settings.
	 */
	public function render_heading_icon( $head ) {
		?>

		<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
			<?php
			$head_icon_migrated = isset( $head['__fa4_migrated']['new_heading_icon'] );
			$head_icon_is_new   = empty( $head['heading_icon'] );
			?>
			<?php if ( isset( $head['heading_icon'] ) || isset( $head['new_heading_icon'] ) ) { ?>
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_heading_icon_align' . esc_attr( $head['_id'] ) ) ); ?>>

					<?php
					if ( $head_icon_migrated || $head_icon_is_new ) {
						\Elementor\Icons_Manager::render_icon( $head['new_heading_icon'], array( 'aria-hidden' => 'true' ) );
					} else {
						?>
						<i class="<?php echo esc_attr( $head['heading_icon'] ); ?>"></i>
					<?php } ?>

				</span>
			<?php } ?>
		<?php } elseif ( isset( $head['heading_icon'] ) ) { ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_heading_icon_align' . esc_attr( $head['_id'] ) ) ); ?>>
				<i class="<?php echo esc_attr( $head['heading_icon'] ); ?>"></i>
			</span>
			<?php
		}
	}

	/**
	 * Render Woo Product Grid output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings  = $this->get_settings_for_display();
		$node_id   = $this->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();
		ob_start();
		include UAEL_MODULES_DIR . 'table/widgets/template.php';
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
