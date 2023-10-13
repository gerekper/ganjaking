<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

use NinjaTables\Framework\Support\Arr;
use NinjaTables\Framework\Support\Sanitizer;

class TableHandler
{
    public function ninjaTableJsConfig($config, $filter)
    {
        if ( ! empty($config['shortCodeData']['get_filter'])) {
            $filter_var = $config['shortCodeData']['get_filter'];
            if (isset($_GET[$filter_var])) {
                $filter = Sanitizer::sanitizeTextField($_GET[$filter_var]);
            }
            if (isset($_GET['column'])) {
                $config['settings']['filter_column'] = explode(',', $_GET['column']);
            }
        }
        $filter                   = apply_filters('ninja_parse_placeholder', $filter);
        $config['default_filter'] = $filter;


        if (isset($config['shortCodeData']['stackable']) && $config['shortCodeData']['stackable']) {
            $config['settings']['stackable']      = $config['shortCodeData']['stackable'];
            $devices                              = $config['shortCodeData']['stack_devices'];
            $devices                              = explode(',', $devices);
            $config['settings']['stacks_devices'] = $devices;
        }

        return $config;
    }

    public function ninjaTableColumnAttributes($formatted_column, $originalColumn)
    {
        if (isset($formatted_column['title'])) {
            $formatted_column['title'] = do_shortcode($formatted_column['title']);
        }

        if (isset($originalColumn['conditions'])) {
            $conditions = $originalColumn['conditions'];

            foreach ($conditions as $conditionIndex => $condition) {
                $conditions[$conditionIndex]['conditionalValue']  = apply_filters('ninja_parse_placeholder',
                    $conditions[$conditionIndex]['conditionalValue']);
                $conditions[$conditionIndex]['conditionalValue2'] = apply_filters('ninja_parse_placeholder',
                    $conditions[$conditionIndex]['conditionalValue2']);
            }
            $formatted_column['conditions'] = $conditions;
        }

        if (isset($originalColumn['transformed_value']) && $originalColumn['transformed_value']) {
            $originalColumn['transformed_value'] = apply_filters('ninja_parse_placeholder',
                $originalColumn['transformed_value']);
        }

        if (isset($originalColumn['transformed_value'])) {
            $formatted_column['transformed_value'] = $originalColumn['transformed_value'];
        }

        return $formatted_column;
    }

    public function ninjaTablesShortcodeDefaults($defaults)
    {
        wp_register_script('ninja-tables-pro', NINJAPROPLUGIN_URL . 'assets/js/ninja-tables-pro.js', array('footable'),
            NINJAPROPLUGIN_VERSION, true);
        $defaults['per_page']            = null;
        $defaults['search']              = null;
        $defaults['sorting']             = null;
        $defaults['hide_header']         = null;
        $defaults['logged_in_only']      = null;
        $defaults['columns']             = 'all';
        $defaults['get_filter']          = '';
        $defaults['filter_column']       = '';
        $defaults['skip']                = 0;
        $defaults['limit']               = 0;
        $defaults['disable_edit']        = 'no';
        $defaults['hide_default_filter'] = '';
        $defaults['filter_selects']      = '';
        $defaults['stackable']           = '';
        $defaults['stack_devices']       = 'xs,sm';
        $defaults['post_tax']            = '';
        $defaults['post_tax_field']      = 'slug';
        $defaults['sf_filter']           = '';
        $defaults['sf_column']           = '';
        $defaults['sf_match']            = 'equal';

        return $defaults;
    }

    public function ninjaTablesRenderingTableSettings($settings, $shortCodeData)
    {
        if (isset($shortCodeData['per_page']) && $shortCodeData['per_page'] !== null) {
            $settings['perPage'] = intval($shortCodeData['per_page']);
        }

        if (isset($shortCodeData['search']) && $shortCodeData['search'] !== null) {
            $settings['enable_search'] = (bool)$shortCodeData['search'];
        }
        if (isset($shortCodeData['sorting']) && $shortCodeData['sorting'] !== null) {
            $settings['column_sorting'] = (bool)$shortCodeData['sorting'];
        }
        if (isset($shortCodeData['hide_header']) && $shortCodeData['hide_header'] !== null) {
            $settings['hide_header_row'] = (bool)$shortCodeData['hide_header'];
        }

        if (isset($shortCodeData['logged_in_only']) && $shortCodeData['logged_in_only'] !== null && $shortCodeData['logged_in_only']) {
            if ( ! is_user_logged_in()) {
                return array();
            }
        }

        if (isset($shortCodeData['skip']) && $shortCodeData['skip']) {
            $settings['skip_rows'] = $shortCodeData['skip'];
        }
        if (isset($shortCodeData['limit']) && $shortCodeData['limit']) {
            $settings['limit_rows'] = $shortCodeData['limit'];
        }

        if (isset($shortCodeData['filter_column']) && $shortCodeData['filter_column']) {
            $filterColumns             = explode(',', $shortCodeData['filter_column']);
            $settings['filter_column'] = $filterColumns;
        }

        if (isset($shortCodeData['columns']) && ! empty($shortCodeData['columns']) && $shortCodeData['columns'] != 'all') {
            $columns = explode(',', $shortCodeData['columns']);
            if ($columns) {
                $columns                  = array_flip($columns);
                $settings['columns_only'] = $columns;
            }
        }

        return $settings;
    }

    public function ninjaTableRenderingTableVars($table_vars, $table_id, $tableArray)
    {
        if (isset($tableArray['shortCodeData']['disable_edit']) && $tableArray['shortCodeData']['disable_edit'] == "no") {
            $dataProvider = ninja_table_get_data_provider($table_id);
            if ($dataProvider == 'default') {
                $editor                = new TableEditorHandler();
                $table_vars['editing'] = $editor->getEditingVars($table_id);
            }
        }

        $table_vars['settings']['filter_selects'] = array();
        $defaultSelects                           = $tableArray['shortCodeData']['filter_selects'];
        if (isset($_GET['filter_selects']) && $_GET['filter_selects']) {
            $defaultSelects = Sanitizer::sanitizeTextField($_GET['filter_selects']);
        }
        if ($defaultSelects) {
            $defaultSelects   = explode('|', $defaultSelects);
            $formattedSelects = array();
            foreach ($defaultSelects as $defaultSelect) {
                $selectPair = explode('=', $defaultSelect);
                if (count($selectPair) == 2) {
                    $formattedSelects[] = array(
                        'target' => $selectPair[0],
                        'value'  => $selectPair[1]
                    );
                }
            }
            $table_vars['settings']['filter_selects'] = $formattedSelects;
        }

        if (isset($tableArray['shortCodeData']['hide_default_filter']) && $tableArray['shortCodeData']['hide_default_filter'] == "yes") {
            $table_vars['settings']['hide_default_filter'] = 'yes';
        }

        if (Arr::get($tableArray, 'settings.sticky_header') == 'yes') {
            wp_enqueue_script('jquery.stickytableheaders',
                NINJAPROPLUGIN_URL . 'assets/libs/stickyheaders/jquery.stickytableheaders.min.js', array('jquery'),
                '0.1.24', true);
            $table_vars['settings']['sticky_header']        = true;
            $table_vars['settings']['sticky_header_offset'] = Arr::get($tableArray, 'settings.sticky_header_offset');
        }

        return $table_vars;
    }

    public function loadFormulaParser()
    {
        wp_enqueue_script('formula-parser',
            NINJAPROPLUGIN_URL . "assets/libs/formula/formula-parser.min.js",
            array('jquery'), '3.0.1', true
        );
    }

    public function ninjaTablesLoadLightbox()
    {
        wp_enqueue_script('lity', NINJAPROPLUGIN_URL . 'assets/libs/lity/lity.min.js', array('jquery'), '2.3.1', true);
        wp_enqueue_style('lity', NINJAPROPLUGIN_URL . 'assets/libs/lity/lity.min.css', array(), '2.3.1');
    }

    public function ninjaTableGetPublicData($data)
    {
        global $ninja_table_current_rendering_table;
        if ( ! $ninja_table_current_rendering_table || ! isset($ninja_table_current_rendering_table['shortCodeData']['sf_column']) || ! $ninja_table_current_rendering_table['shortCodeData']['sf_column']) {
            return $data;
        }
        $sFilter = $ninja_table_current_rendering_table['shortCodeData']['sf_filter'];
        $sColumn = $ninja_table_current_rendering_table['shortCodeData']['sf_column'];
        $sfMatch = $ninja_table_current_rendering_table['shortCodeData']['sf_match'];
        if ( ! $sFilter || ! $sColumn) {
            return $data;
        }
        $sFilter = apply_filters('ninja_parse_placeholder', $sFilter);
        $newData = array_filter($data, function ($array) use ($sFilter, $sColumn, $sfMatch) {

            if ( ! isset($array[$sColumn])) {
                return false;
            }

            switch ($sfMatch) {
                case 'equal':
                    return ($array[$sColumn] == $sFilter);
                case 'contains':
                    return (strpos($array[$sColumn], $sFilter) !== false);
                case 'lt':
                    return ($array[$sColumn] < $sFilter);
                case 'gt':
                    return ($array[$sColumn] > $sFilter);
                case 'startswith':
                    return (strpos($array[$sColumn], $sFilter) === 0);
            }

            return false;
        });

        return $newData;
    }

    public function ninjaTablesItemAttributes($attributes)
    {
        $values = json_decode($attributes['value'], true);

        if (isset($values['created_at']) && ! $values['created_at']) {
            $values['created_at'] = date('Y-m-d h:i A', current_time('timestamp'));
            $attributes['value']  = json_encode($values, JSON_UNESCAPED_UNICODE);
        }

        if (isset($values['updated_at'])) {
            $values['updated_at'] = date('Y-m-d h:i A', current_time('timestamp'));
            $attributes['value']  = json_encode($values, JSON_UNESCAPED_UNICODE);
        }

        return $attributes;
    }

    public function ownDataFilter($query, $tableId)
    {
        // Check if the table actually has fronend editing
        $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);
        if ($settings && Arr::get($settings, 'allow_frontend') == 'yes' && Arr::get($settings,
                'own_data_only') == 'yes') {
            if ( ! current_user_can('administrator')) {
                $currentUserId = get_current_user_id();
                if ( ! $currentUserId) {
                    $currentUserId = -1;
                }
                $query->where('owner_id', $currentUserId);
            }
        }

        return $query;
    }

    public function ownDataTotalFilter($query, $tableVars)
    {
        if (isset($tableVars['editing']) && $tableVars['editing']['own_data_only'] == 'yes' && ! current_user_can('administrator')) {
            $currentUserId = get_current_user_id();
            if ( ! $currentUserId) {
                $currentUserId = -1;
            }
            $query->where('owner_id', $currentUserId);
        }

        return $query;
    }

    public function addOriginalColumn($formatted_column, $column, $table_id)
    {
        $formatted_column['original'] = $column;

        return $formatted_column;
    }
}
