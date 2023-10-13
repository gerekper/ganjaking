<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

use NinjaTables\Framework\Support\Arr;
use NinjaTablesPro\App\Traits\CustomFilter;

class CustomFilterHandler
{
    use CustomFilter;

    public function ninjaTableRenderingTableVars($tableVars, $tableId)
    {
        $tableVars = $this->addAdvancedFilter($tableVars, $tableId);

        return $this->addButtonVars($tableVars, $tableId);
    }

    public function addAdvancedFilter($tableVars, $tableId)
    {
        $customFilters = $this->getCustomFilters($tableId, $tableVars);
        if ( ! $customFilters) {
            return $tableVars;
        }
        wp_enqueue_script('ninja-tables-pro', NINJAPROPLUGIN_URL . 'assets/js/ninja-tables-pro.js', array('footable'),
            NINJAPROPLUGIN_VERSION, true);
        $table_instance_name = $tableVars['instance_name'] . '_custom_filter';
        $customFilters       = array_reverse($customFilters);

        foreach ($customFilters as $filterIndex => $filter) {
            if (in_array($filter['type'], ['select', 'radio', 'checkbox'])) {
                $filterOptions    = $filter['options'];
                $formattedOptions = [];
                foreach ($filterOptions as $filterOption) {
                    $filterOption['value'] = apply_filters('ninja_parse_placeholder', $filterOption['value']);
                    $formattedOptions[]    = $filterOption;
                }
                $customFilters[$filterIndex]['options'] = $formattedOptions;
            }
        }

        $this->addFilterJS($table_instance_name, $customFilters, $tableVars);
        $tableVars['custom_filter_key'] = $table_instance_name;

        return $tableVars;
    }

    public function addButtonVars($tableVars, $tableId)
    {
        $tableButtons = get_post_meta($tableId, '_ninja_custom_table_buttons', true);
        if ( ! $tableButtons) {
            $tableButtons = array();
        }
        foreach ($tableButtons as $button) {
            if ( ! empty($button['status']) && $button['status'] == 'yes') {
                wp_enqueue_script('ninja-tables-pro', NINJAPROPLUGIN_URL . 'assets/js/ninja-tables-pro.js',
                    array('footable'), NINJAPROPLUGIN_VERSION, true);
                $tableVars['table_buttons'] = $tableButtons;

                return $tableVars;
            }
        }

        return $tableVars;
    }

    private function addFilterJS($filter_key, $filters, $tableVars)
    {
        $tableId = $tableVars['table_id'];

        $preSelects = Arr::get($tableVars, 'settings.filter_selects', array());

        if ($preSelects) {
            foreach ($preSelects as $index => $preSelect) {
                $preSelects[$index]['value'] = apply_filters('ninja_parse_placeholder', $preSelects[$index]['value']);
            }
        }

        global $ninja_table_current_rendering_table;
        ob_start();
        $filterStyling = get_post_meta($tableId, '_ninja_custom_filter_styling', true);
        $progressive   = false;
        if ($filterStyling && isset($filterStyling['progressive']) && $filterStyling['progressive'] == 'yes') {
            $progressive = true;
        }
        ?>
        jQuery(document).ready(function ($) {
        var filters = <?php echo json_encode($filters); ?>;
        var filterKey = "<?php echo $filter_key; ?>";
        var tableId = <?php echo $tableId; ?>;
        var progressive = <?php if ($progressive) { ?>true<?php } else { ?>false<?php } ?>;
        var preSelects = <?php echo json_encode($preSelects); ?>;
        var uniqueId = "<?php echo $ninja_table_current_rendering_table['uniqueID']; ?>";
        var FilterOptions = ninjaTableGetCustomFilter(filters, filterKey, tableId, preSelects, progressive, uniqueId);
        FooTable[filterKey] = FooTable.Filtering.extend(FilterOptions);
        });

        <?php
        $js = ob_get_clean();
        wp_add_inline_script('footable', $js);
    }
}
