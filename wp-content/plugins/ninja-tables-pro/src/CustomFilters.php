<?php

namespace NinjaTablesPro;

use NinjaTables\Classes\ArrayHelper;

class CustomFilters
{
    private $meta_key = '_ninja_table_custom_filters';

    public function getCustomFilters($tableId, $tableVars)
    {
        $filters = get_post_meta($tableId, $this->meta_key, true);
        if (!$filters) {
            $filters = array();
        }

        $columns = $this->get($tableVars, 'columns', array());

        $formattedColumns = array();
        foreach ($columns as $column) {
            $formattedColumns[$column['name']] = $column;
        }

        foreach ($filters as $filterIndex => $filter) {
            if ($filter['type'] == 'date_picker' || $filter['type'] == 'date_range') {
                if (isset($filter['columns'][0])) {
                    if (isset($formattedColumns[$filter['columns'][0]]['formatString'])) {
                        $filters[$filterIndex]['dateFormat'] = $formattedColumns[$filter['columns'][0]]['formatString'];
                    } else {
                        $filters[$filterIndex]['dateFormat'] = 'MM/DD/YYYY';
                    }

                    $filters[$filterIndex]['showTime'] = $formattedColumns[$filter['columns'][0]]['showTime'];
                    $filters[$filterIndex]['firstDayOfWeek'] = $formattedColumns[$filter['columns'][0]]['firstDayOfWeek'];
                }
                wp_enqueue_script('pikaday', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.min.js', array('jquery'), NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_script('pikaday.jquery', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.jquery.js', array('pikaday'), NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_style('pickaday.css', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/css/pikaday.css', array(), NINJAPROPLUGIN_VERSION);
            }
            else if($filter['type'] == 'select' && isset($filter['is_multi_select']) && $filter['is_multi_select'] == 'yes') {
                // We have multi select Here
                wp_enqueue_script('jquery.sumoselect', NINJAPROPLUGIN_URL.'assets/libs/sumoselect/jquery.sumoselect.js',  array('jquery'), NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_style('ninja_sumoselect', NINJAPROPLUGIN_URL.'assets/sumoselect.css',  array(), NINJAPROPLUGIN_VERSION);
            }
        }
        return $filters;
    }

    public function updateFilters($tableId, $filters)
    {
        // Format the filters First
        $formattedFilters = array();
        foreach ($filters as $index => $filter) {
            $formattedFilters['ninja_filter_' . $index] = $this->normalizeFilter($filter);
        }
        return update_post_meta($tableId, $this->meta_key, $formattedFilters);
    }

    public function addAdvancedFilter($tableVars, $tableId)
    {
        $customFilters = $this->getCustomFilters($tableId, $tableVars);
        if (!$customFilters) {
            return $tableVars;
        }
        wp_enqueue_script('ninja-tables-pro', NINJAPROPLUGIN_URL.'assets/ninja-tables-pro.js',  array('footable'), NINJAPROPLUGIN_VERSION, true);
        $table_instance_name = $tableVars['instance_name'] . '_custom_filter';
        $customFilters = array_reverse($customFilters);

        foreach ($customFilters as $filterIndex => $filter) {
            if(in_array($filter['type'], ['select', 'radio'])) {
                $filterOptions = $filter['options'];
                $formattedOptions = [];
                foreach ($filterOptions as $filterOption) {
                    $filterOption['value'] = apply_filters('ninja_parse_placeholder', $filterOption['value']);
                    $formattedOptions[] =$filterOption;
                }
                $customFilters[$filterIndex]['options'] = $formattedOptions;
            }
        }

        $this->addFilterJS($table_instance_name, $customFilters, $tableVars);
        $tableVars['custom_filter_key'] = $table_instance_name;
        return $tableVars;
    }

    public function addButtonVars($tableVars, $tableId) {
        $tableButtons = get_post_meta($tableId, '_ninja_custom_table_buttons', true);
        if (!$tableButtons) {
            $tableButtons = array();
        }
        foreach ($tableButtons as $button) {
            if (!empty($button['status']) && $button['status'] == 'yes') {
                wp_enqueue_script('ninja-tables-pro', NINJAPROPLUGIN_URL.'assets/ninja-tables-pro.js',  array('footable'), NINJAPROPLUGIN_VERSION, true);
                $tableVars['table_buttons'] = $tableButtons;
                return $tableVars;
            }
        }
        return $tableVars;
    }

    private function addFilterJS($filter_key, $filters, $tableVars)
    {
        $tableId = $tableVars['table_id'];

        $preSelects = ArrayHelper::get($tableVars, 'settings.filter_selects', array());

        if($preSelects) {
            foreach ($preSelects as $index => $preSelect) {
                $preSelects[$index]['value'] = apply_filters('ninja_parse_placeholder', $preSelects[$index]['value']);
            }
        }

        global $ninja_table_current_rendering_table;
        ob_start();
        $filterStyling = get_post_meta($tableId, '_ninja_custom_filter_styling', true);
        $progressive = false;
        if($filterStyling && isset($filterStyling['progressive']) && $filterStyling['progressive'] == 'yes') {
            $progressive = true;
        }
        ?>
            jQuery(document).ready(function ($) {
                var filters = <?php echo json_encode($filters); ?>;
                var filterKey = "<?php echo $filter_key; ?>";
                var tableId = <?php echo $tableId; ?>;
                var progressive = <?php if($progressive) { ?>true<?php } else {   ?>false<?php } ?>;
                var preSelects = <?php echo json_encode($preSelects); ?>;
                var uniqueId = "<?php echo $ninja_table_current_rendering_table['uniqueID']; ?>";
                var FilterOptions = ninjaTableGetCustomFilter(filters, filterKey, tableId, preSelects, progressive, uniqueId);
                FooTable[filterKey] = FooTable.Filtering.extend(FilterOptions);
            });

        <?php
        $js = ob_get_clean();
        wp_add_inline_script('footable', $js);
    }

    public function checkPermission()
    {
        if (!ninja_table_admin_role()) {
            if (wp_doing_ajax()) {
                wp_send_json_error(array(
                    'message' => 'Permission error'
                ), 400);
            } else {
                return false;
            }
        }
        return true;
    }

    private function normalizeFilter($filter)
    {
        $options = $filter['options'];
        $formattedOptions = array();
        foreach ($options as $option) {
            $formattedOptions[] = array(
                'label' => wp_unslash($option['label']),
                'value' => wp_unslash($option['value'])
            );
        }
        $filter['options'] = $formattedOptions;
        return $filter;
    }

    private function get($array, $key, $default = '')
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }
}

add_action('ninja_table_rendering_table_vars', function ($tableVars, $tableId) {
    $custom_filter = new CustomFilters();
    $tableVars = $custom_filter->addAdvancedFilter($tableVars, $tableId);
    $tableVars = $custom_filter->addButtonVars($tableVars, $tableId);
    return $tableVars;
}, 100, 2);

add_action('wp_ajax_ninjatable_get_custom_table_filters', function () {
	ninjaTablesValidateNonce();
    $tableId = intval($_REQUEST['table_id']);
    $custom_filter = new CustomFilters();
    $custom_filter->checkPermission();
    $table_filters = $custom_filter->getCustomFilters($tableId, array());
    $formattedFilters = array();
    foreach ($table_filters as $key => $table_filter) {
        $table_filter['name'] = $key;
        $formattedFilters[] = $table_filter;
    }

    $filterStyling = get_post_meta($tableId, '_ninja_custom_filter_styling', true);
    if (!$filterStyling) {
        $filterStyling = array();
    }
    $defaultStyling = array(
        'filter_display_type' => 'inline',
        'filter_columns' => 'columns_2',
        'filter_column_label' => 'new_line',
        'progressive' => 'no'
    );

    $filterStyling = wp_parse_args($filterStyling, $defaultStyling);

    wp_send_json_success(array(
        'table_filters' => $formattedFilters,
        'filter_styling' => $filterStyling
    ));
});

add_action('wp_ajax_ninjatable_update_custom_table_filters', function () {
	ninjaTablesValidateNonce();
    $tableId = intval($_REQUEST['table_id']);
    $custom_filter = new CustomFilters();
    $custom_filter->checkPermission();
    $filters = wp_unslash(ArrayHelper::get($_REQUEST, 'ninja_filters', []));
    $custom_filter->updateFilters($tableId, $filters);

    if (isset($_REQUEST['filter_styling'])) {
        $filterAppearance = wp_unslash($_REQUEST['filter_styling']);
        update_post_meta($tableId, '_ninja_custom_filter_styling', $filterAppearance);
    }

    if(isset($_REQUEST['table_buttons'])) {
        $tableButtons = wp_unslash($_REQUEST['table_buttons']);
        update_post_meta($tableId, '_ninja_custom_table_buttons', $tableButtons);
    }

    wp_send_json_success(array(
        'message' => __('Filters successfully updated', 'ninja_table_pro')
    ));
});

add_action('ninja_tables_after_table_print', function ($table) {
	global $ninja_table_after_print;

	if (in_array($table->ID, $ninja_table_after_print)) {
		return;
	} else {
		$ninja_table_after_print[] = $table->ID;
    }

    $customJS = get_post_meta($table->ID, '_ninja_tables_custom_js', true);

    if ($customJS) {
        add_action('wp_footer', function () use ($customJS, $table) {
           ?>
                <script type="text/javascript">
                    jQuery(document).on('ninja_table_ready_init_table_id_<?php echo $table->ID; ?>',function (e, params) {
                        var $table = params.$table;
                        var $ = jQuery;
                        var tableConfig = params.tableConfig;

                        if (window.ninjaTableAfterPrint) {
                            if (window.ninjaTableAfterPrint.indexOf(tableConfig.table_id) != -1) {
                                return;
                            } else {
                                window.ninjaTableAfterPrint.push(tableConfig.table_id);
                            }
                        } else {
                            window.ninjaTableAfterPrint = [tableConfig.table_id];
                        }

                        try {
                            <?php echo $customJS; ?>
                        } catch (e) {
                            console.warn('Error in custom JS of Ninja Table ID: '+tableConfig.table_id);
                            console.error(e);
                        }
                    });
                </script>
            <?php
        }, 999);
    }
});
