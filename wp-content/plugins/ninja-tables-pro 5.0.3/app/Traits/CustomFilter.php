<?php

namespace NinjaTablesPro\App\Traits;

trait CustomFilter
{
    private $meta_key = '_ninja_table_custom_filters';

    public function getCustomFilters($tableId, $tableVars)
    {
        $filters = get_post_meta($tableId, $this->meta_key, true);
        if ( ! $filters) {
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

                    if (isset($filters[$filterIndex]['showTime']) && isset($formattedColumns[$filter['columns'][0]]['showTime'])) {
                        $filters[$filterIndex]['showTime'] = $formattedColumns[$filter['columns'][0]]['showTime'];
                    }

                    if (isset($filters[$filterIndex]['firstDayOfWeek']) && isset($formattedColumns[$filter['columns'][0]]['firstDayOfWeek'])) {
                        $filters[$filterIndex]['firstDayOfWeek'] = $formattedColumns[$filter['columns'][0]]['firstDayOfWeek'];
                    }
                }
                wp_enqueue_script('pikaday', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.min.js',
                    array('jquery'), NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_script('pikaday.jquery', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.jquery.js',
                    array('pikaday'), NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_style('pickaday.css', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/css/pikaday.css', array(),
                    NINJAPROPLUGIN_VERSION);
            } elseif ($filter['type'] == 'select' && isset($filter['is_multi_select']) && $filter['is_multi_select'] == 'yes') {
                // We have multi select Here
                wp_enqueue_script('jquery.sumoselect',
                    NINJAPROPLUGIN_URL . 'assets/libs/sumoselect/jquery.sumoselect.js', array('jquery'),
                    NINJAPROPLUGIN_VERSION, true);
                wp_enqueue_style('ninja_sumoselect', NINJAPROPLUGIN_URL . 'assets/css/sumoselect.css', array(),
                    NINJAPROPLUGIN_VERSION);
            }
        }

        return $filters;
    }

    private function get($array, $key, $default = '')
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
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

    private function normalizeFilter($filter)
    {
        $options          = $filter['options'];
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
}
