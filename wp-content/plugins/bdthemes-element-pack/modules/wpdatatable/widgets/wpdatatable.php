<?php
namespace ElementPack\Modules\Wpdatatable\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class wpdatatable extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-wpdatatable';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'wpDataTable', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-wpdatatable';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'wp', 'data', 'table' ];
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/p_FRLsEVNjQ';
	// }

	protected function get_table_list() {
        if(shortcode_exists("wpdatatable")){
            global $wpdb;

			$query          = "SELECT id, title FROM {$wpdb->prefix}wpdatatables ORDER BY id";
			$allTables      = $wpdb->get_results($query, ARRAY_A);
			$returnTables   = [];
			$returnTables[] = __('Choose a table', 'wpdatatables');

            foreach ($allTables as $table) {
                $returnTables[$table['id']] = $table['title'];
            }

            return $returnTables;
        }
    }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);


		$slider_list = $this->get_table_list();

		$this->add_control(
			'table_id',
			[
				'label'       => esc_html__( 'Select Table', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $slider_list,
				'rendar_type' => 'template',
				'default'     => 0,
			]
		);

		
		$this->end_controls_section();

	}

	public function render() {
		$settings = $this->get_settings_for_display();
		if ($settings['table_id']) {
			echo do_shortcode($this->wp_data_table($settings['table_id']));
		} else {
			echo '<div class="bdt-alert bdt-alert-warning">'.__('Please select a table from setting!', 'bdthemes-element-pack').'</div>';
		}
	}


	function wp_data_table($id, $atts = null, $content = null) {
	    global $wdtVar1, $wdtVar2, $wdtVar3, $wdtExportFileName;

	    extract(shortcode_atts(array(
	        'var1' => '%%no_val%%',
	        'var2' => '%%no_val%%',
	        'var3' => '%%no_val%%',
	        'export_file_name' => '%%no_val%%',
	        'table_view' => 'regular'
	    ), $atts));

	    /**
	     * Protection
	     * @var int $id
	     */
	    if (!$id) {
	        return false;
	    }

	    $tableData = \WDTConfigController::loadTableFromDB($id);
	    if (empty($tableData->content)) {
	        return __('wpDataTable with provided ID not found!', 'wpdatatables');
	    }

	    do_action('wpdatatables_before_render_table', $id);

	    /** @var mixed $var1 */
	    $wdtVar1 = $var1 !== '%%no_val%%' ? $var1 : $tableData->var1;
	    /** @var mixed $var2 */
	    $wdtVar2 = $var2 !== '%%no_val%%' ? $var2 : $tableData->var2;
	    /** @var mixed $var3 */
	    $wdtVar3 = $var3 !== '%%no_val%%' ? $var3 : $tableData->var3;

	    /** @var mixed $export_file_name */
	    $wdtExportFileName = $export_file_name !== '%%no_val%%' ? $export_file_name : '';

	    /** @var mixed $table_view */
	    if ($table_view == 'excel') {
	        /** @var WPExcelDataTable $wpDataTable */
	        $wpDataTable = new \WPExcelDataTable();
	    } else {
	        /** @var WPDataTable $wpDataTable */
	        $wpDataTable = new \WPDataTable();
	    }

	    $wpDataTable->setWpId($id);

	    $columnDataPrepared = $wpDataTable->prepareColumnData($tableData);

	    try {
	        $wpDataTable->fillFromData($tableData, $columnDataPrepared);
			$wpDataTable = apply_filters('wpdatatables_filter_initial_table_construct', $wpDataTable);
			$output      = '';
			$output     .= $wpDataTable->generateTable();
	    } catch (Exception $e) {
	        $output 	 = \WDTTools::wdtShowError($e->getMessage());
	    }
    	$output 		 = apply_filters('wpdatatables_filter_rendered_table', $output, $id);

	    return $output;
	}
}
