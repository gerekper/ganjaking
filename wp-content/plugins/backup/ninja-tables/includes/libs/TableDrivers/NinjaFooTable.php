<?php namespace NinjaTable\TableDrivers;

use NinjaTables\Classes\ArrayHelper;

class NinjaFooTable
{
    public static $version = NINJA_TABLES_VERSION;

    /**
     * Table specfic css prerender status.
     *
     * @var array
     */
    public static $tableCssStatuses = [];

    public static function run($tableArray)
    {
        global $ninja_table_instances;
        global $ninja_table_current_rendering_table;
        $tableInstance = 'ninja_table_instance_' . count($ninja_table_instances);
        $ninja_table_instances[] = $tableInstance;

        $tableArray['uniqueID'] = 'ninja_table_unique_id_'.rand().'_'.$tableArray['table_id'];

        $ninja_table_current_rendering_table = $tableArray;

        static::enqueuePublicCss();

        if (!ArrayHelper::get($tableArray, 'settings.table_color_type')) {
            if (ArrayHelper::get($tableArray, 'settings.table_color') == 'ninja_table_custom_color') {
                $tableArray['settings']['table_color_type'] = 'custom_color';
            } else {
                $tableArray['settings']['table_color_type'] = 'pre_defined_color';
            }
        }

        $tableArray['table_instance_name'] = $tableInstance;
        $table_provider = ninja_table_get_data_provider($tableArray['table_id']);
        $tableArray['provider'] = $table_provider;
        do_action('ninja_rendering_table_' . $table_provider, $tableArray);
        self::enqueue_assets();
        self::render($tableArray);
    }

    private static function enqueue_assets()
    {
        wp_enqueue_script('footable',
            NINJA_TABLES_PUBLIC_DIR_URL . "libs/footable/js/footable.min.js",
            array('jquery'), '3.1.5', true
        );

        wp_enqueue_script('footable_init',
            NINJA_TABLES_DIR_URL . "assets/js/ninja-tables-footable." . NINJA_TABLES_ASSET_VERSION . ".js",
            array('footable'), self::$version, true
        );

        $localizeData = array(
            'ajax_url'      => admin_url('admin-ajax.php'),
            'tables'        => array(),
            'ninja_version' => NINJA_TABLES_VERSION,
            'i18n'          => array(
                'search_in'  => __('Search in', 'ninja-tables'),
                'search'     => __('Search', 'ninja-tables'),
                'empty_text' => __('No Result Found', 'ninja-tables'),
            )
        );

        if(defined('NINJAPROPLUGIN_VERSION')) {
            $localizeData['pro_version'] = NINJAPROPLUGIN_VERSION;
        }

        wp_localize_script('footable_init', 'ninja_footables', $localizeData);
    }

    /**
     * Set the table header colors.
     *
     * @param array $tableArray
     *
     * @param string $extra_css
     *
     * @return void
     */
    private static function addCustomColorCSS($tableArray, $extra_css = '')
    {
        $css = self::generateCustomColorCSS($tableArray, $extra_css);
        if ($css) {
            add_action('ninja_tables_after_table_print', function () use ($css) {
                echo $css;
            });
        }
    }

    /**
     * Generate custom css for the table.
     *
     * @param array $tableArray
     * @param string $extra_css
     * @return mixed
     */
    public static function generateCustomColorCSS($tableArray, $extra_css = '')
    {
        $tableId = intval($tableArray['table_id']);
        $cellStyles = array();
        $tableProvider = ninja_table_get_data_provider($tableId);
        if ($tableProvider == 'default' && get_option('_ninja_tables_settings_migration')) {
            ob_start();
            $cellStyles = ninja_tables_DbTable()
                ->select(array('id', 'settings'))
                ->where('table_id', $tableId)
                ->whereNotNull('settings')
                ->get();
            $maybeError = ob_get_clean();
        }

        $css_prefix = '#footable_' . $tableId;
        $hasStackable = false;
        if (ArrayHelper::get($tableArray, 'settings.stackable') == 'yes') {
            $hasStackable = true;
            $stackPrefix = '#footable_' . $tableId . ' .footable-details';
        }

        $customColumnCss = '';
        if (defined('NINJATABLESPRO')) {
            $columns = ArrayHelper::get($tableArray, 'columns');
            foreach ($columns as $index => $column) {
                $bgColor = ArrayHelper::get($column, 'background_color');
                $textColor = ArrayHelper::get($column, 'text_color');
                if ($bgColor || $textColor) {
                    if ($bgColor && $textColor) {
                        $customColumnCss .= $css_prefix . ' thead tr th.ninja_column_' . $index . ',' . $css_prefix . ' tbody tr td.ninja_column_' . $index . '{ background-color: ' . $bgColor . '; color: ' . $textColor . '; }';
                    } else if ($bgColor) {
                        $customColumnCss .= $css_prefix . ' thead tr th.ninja_column_' . $index . ',' . $css_prefix . ' tbody tr td.ninja_column_' . $index . '{ background-color: ' . $bgColor . '; }';
                    } else if ($textColor) {
                        $customColumnCss .= $css_prefix . ' thead tr th.ninja_column_' . $index . ',' . $css_prefix . ' tbody tr td.ninja_column_' . $index . '{ color: ' . $textColor . '; }';
                    }
                }
            }
        }

        $colors = false;
        $custom_css = str_replace('NT_ID', $tableId, get_post_meta($tableId, '_ninja_tables_custom_css', true));

        if (ArrayHelper::get($tableArray, 'settings.table_color_type') == 'custom_color'
            && defined('NINJATABLESPRO')
        ) {
            $colorSettings = $tableArray['settings'];
            $colors = array(
                'table_color_primary'   => ArrayHelper::get($colorSettings, 'table_color_primary'),
                'table_color_secondary' => ArrayHelper::get($colorSettings, 'table_color_secondary'),
                'table_color_border'    => ArrayHelper::get($colorSettings, 'table_color_border'),

                'table_color_primary_hover'   => ArrayHelper::get($colorSettings, 'table_color_primary_hover'),
                'table_color_secondary_hover' => ArrayHelper::get($colorSettings, 'table_color_secondary_hover'),
                'table_color_border_hover'    => ArrayHelper::get($colorSettings, 'table_color_border_hover'),

                'table_search_color_primary'   => ArrayHelper::get($colorSettings, 'table_search_color_primary'),
                'table_search_color_secondary' => ArrayHelper::get($colorSettings, 'table_search_color_secondary'),
                'table_search_color_border'    => ArrayHelper::get($colorSettings, 'table_search_color_border'),

                'table_header_color_primary'   => ArrayHelper::get($colorSettings, 'table_header_color_primary'),
                'table_color_header_secondary' => ArrayHelper::get($colorSettings, 'table_color_header_secondary'),
                'table_color_header_border'    => ArrayHelper::get($colorSettings, 'table_color_header_border'),

                'alternate_color_status' => ArrayHelper::get($colorSettings, 'alternate_color_status'),

                'table_alt_color_primary'   => ArrayHelper::get($colorSettings, 'table_alt_color_primary'),
                'table_alt_color_secondary' => ArrayHelper::get($colorSettings, 'table_alt_color_secondary'),
                'table_alt_color_hover'     => ArrayHelper::get($colorSettings, 'table_alt_color_hover'),

                'table_alt_2_color_primary'   => ArrayHelper::get($colorSettings, 'table_alt_2_color_primary'),
                'table_alt_2_color_secondary' => ArrayHelper::get($colorSettings, 'table_alt_2_color_secondary'),
                'table_alt_2_color_hover'     => ArrayHelper::get($colorSettings, 'table_alt_2_color_hover'),

                'table_footer_bg'     => ArrayHelper::get($colorSettings, 'table_footer_bg'),
                'table_footer_active' => ArrayHelper::get($colorSettings, 'table_footer_active'),
                'table_footer_border' => ArrayHelper::get($colorSettings, 'table_footer_border'),
            );
        }

        $custom_css .= $extra_css . $customColumnCss;

        if (!$colors && !$custom_css && !$cellStyles) {
            return;
        }
        ob_start();
        include 'views/ninja_footable_css.php';
        return ob_get_clean();
    }

    private static function render($tableArray)
    {
        extract($tableArray);
        if (!count($columns)) {
            return;
        }

        $renderType = ArrayHelper::get($settings, 'render_type', 'ajax_table');

        $formatted_columns = array();
        $sortingType = ArrayHelper::get($settings, 'sorting_type', 'by_created_at');

        $globalSorting = (bool)ArrayHelper::get($settings, 'column_sorting', false);

        $customCss = array();

        foreach ($columns as $index => $column) {
            $columnType = self::getColumnType($column);
            $cssColumnName = 'ninja_column_' . $index;
            $columnClasses = array($cssColumnName);
            $columnClasses[] = 'ninja_clmn_nm_' . $column['key'];
            if (isset($column['classes'])) {
                $userClasses = explode(' ', $column['classes']);
                $columnClasses = array_unique(array_merge($columnClasses, $userClasses));
            }
            $customCss[$cssColumnName] = array();
            if ($columnWidth = ArrayHelper::get($column, 'width')) {
                $customCss[$cssColumnName]['width'] = $columnWidth . ArrayHelper::get($column, 'maxWidthUnit', 'px');
            }

            $columnTitle = $column['name'];
            if (ArrayHelper::get($column, 'enable_html_content') == 'true') {
                if ($columnContent = ArrayHelper::get($column, 'header_html_content')) {
                    $columnTitle = do_shortcode($columnContent);
                }
            }

            $formatted_column = array(
                'name'        => $column['key'],
                'key'         => $column['key'],
                'title'       => $columnTitle,
                'breakpoints' => $column['breakpoints'],
                'type'        => $columnType,
                'visible'     => ($column['breakpoints'] == 'hidden') ? false : true,
                'classes'     => $columnClasses,
                'filterable'  => (isset($column['unfilterable']) && $column['unfilterable'] == 'yes') ? false : true,
                'sortable'    => (isset($column['unsortable']) && $column['unsortable'] == 'yes') ? false : $globalSorting,
            );

            // We will remove it after few versions
            if (defined('NINJAPROPLUGIN_VERSION') && isset($column['transformed_value'])) {
                $formatted_column['transformed_value'] = $column['transformed_value'];
            }

            if ($columnType == 'date') {
                wp_enqueue_script(
                    'moment',
                    NINJA_TABLES_DIR_URL . "public/libs/moment/moment.min.js",
                    [],
                    '2.22.0',
                    true
                );
                $formatted_column['formatString'] = $column['dateFormat'] ?: 'MM/DD/YYYY';
                $formatted_column['showTime'] = isset($column['showTime']) && $column['showTime'] === 'yes';
                $formatted_column['firstDayOfWeek'] = isset($column['firstDayOfWeek']) && $column['firstDayOfWeek'] ? $column['firstDayOfWeek'] : 0;
            }
            if ($sortingType == 'by_column' && $column['key'] == ArrayHelper::get($settings, 'sorting_column')) {
                $formatted_column['sorted'] = true;
                $formatted_column['direction'] = ArrayHelper::get($settings, 'sorting_column_by');
            }

            if ($columnType == 'numeric') {
                $formatted_column['thousandSeparator'] = isset($column['thousandSeparator'])
                    ? $column['thousandSeparator'] : ',';
                $formatted_column['decimalSeparator'] = isset($column['decimalSeparator'])
                    ? $column['decimalSeparator'] : '.';
            }

            if ($columnType == 'image') {
                $linkType = ArrayHelper::get($column, 'link_type');
                if ($linkType == 'image_light_box' || $linkType == 'iframe_ligtbox') {
                    $settings['load_lightbox'] = true;
                    if ($linkType == 'iframe_ligtbox') {
                        $settings['iframe_lightbox'] = true;
                    }
                }
            }

            if ($tableArray['provider'] == 'wp_woo' && ArrayHelper::get($column, 'image_permalink_type') == 'lightbox') {
                $settings['load_lightbox'] = true;
            };

            $formatted_columns[] = apply_filters(
                'ninja_table_column_attributes', $formatted_column, $column, $table_id, $tableArray
            );
        }

        if (ArrayHelper::get($settings, 'show_all')) {
            $pagingSettings = false;
        } else {
            $pagingSettings = ArrayHelper::get($settings, 'perPage', 20);
        }

        $enableSearch = ArrayHelper::get($settings, 'enable_search', false);

        $default_sorting = false;
        if ($sortingType == 'manual_sort') {
            $default_sorting = 'manual_sort';
        } elseif (isset($settings['default_sorting'])) {
            $default_sorting = $settings['default_sorting'];
        }

        $configSettings = array(
            'filtering'             => $enableSearch,
            'togglePosition'        => ArrayHelper::get($settings, 'togglePosition', 'first'),
            'paging'                => $pagingSettings,
            'pager'                 => !!ArrayHelper::get($settings, 'show_pager'),
            'page_sizes'            => explode(',', ArrayHelper::get($settings, 'paze_sizes', '10,20,50,100')),
            'sorting'               => true,
            'default_sorting'       => $default_sorting,
            'defualt_filter'        => isset($default_filter) ? $default_filter : false,
            'defualt_filter_column' => ArrayHelper::get($settings, 'filter_column'),
            'expandFirst'           => (isset($settings['expand_type']) && $settings['expand_type'] == 'expandFirst')
                ? true : false,
            'expandAll'             => (isset($settings['expand_type']) && $settings['expand_type'] == 'expandAll') ? true
                : false,
            'i18n'                  => array(
                'search_in'      => (isset($settings['search_in_text']))
                    ? sanitize_text_field($settings['search_in_text']) : __('Search in', 'ninja-tables'),
                'search'         => (isset($settings['search_placeholder']))
                    ? sanitize_text_field($settings['search_placeholder']) : __('Search', 'ninja-tables'),
                'no_result_text' => (isset($settings['no_result_text']))
                    ? sanitize_text_field($settings['no_result_text']) : __('No Result Found', 'ninja-tables'),
            ),
            'shouldNotCache'        => isset($settings['shouldNotCache']) ? $settings['shouldNotCache'] : false,
            'skip_rows'             => ArrayHelper::get($settings, 'skip_rows', 0),
            'limit_rows'            => ArrayHelper::get($settings, 'limit_rows', 0),
            'use_parent_width'      => ArrayHelper::get($settings, 'use_parent_width', false),
            'info'                  => ArrayHelper::get($tableArray, 'shortCodeData.info', ''),
            'enable_html_cache'     => ArrayHelper::get($settings, 'enable_html_cache'),
            'html_caching_minutes'  => ArrayHelper::get($settings, 'html_caching_minutes')
        );

        $settings['info'] = ArrayHelper::get($tableArray, 'shortCodeData.info', '');
        $table_classes = self::getTableCssClass($settings);

        $tableHasColor = '';

        $configSettings['extra_css_class'] = '';
        if ((ArrayHelper::get($settings, 'table_color_type') == 'pre_defined_color'
            && ArrayHelper::get($settings, 'table_color')
            && ArrayHelper::get($settings, 'table_color') != 'ninja_no_color_table')
        ) {
            $tableHasColor = 'colored_table';
            $configSettings['extra_css_class'] = 'inverted';
        }
        if (ArrayHelper::get($settings, 'table_color_type') == 'custom_color') {
            $tableHasColor = 'colored_table';
            $table_classes .= ' ninja_custom_color';
            $configSettings['extra_css_class'] = 'inverted';
        }

        $table_classes .= ' '.$configSettings['extra_css_class'];

        if ($pagingPosition = ArrayHelper::get($settings, 'pagination_position')) {
            $table_classes .= ' footable-paging-' . $pagingPosition;
        } else {
            $table_classes .= ' footable-paging-right';
        }

        if (isset($settings['hide_all_borders']) && $settings['hide_all_borders']) {
            $table_classes .= ' hide_all_borders';
        }

        if (isset($settings['hide_header_row']) && $settings['hide_header_row']) {
            $table_classes .= ' ninjatable_hide_header_row';
        }

        $isStackable = ArrayHelper::get($settings, 'stackable', 'no');
        $isStackable = $isStackable == 'yes';

        if ($isStackable && count(ArrayHelper::get($settings, 'stacks_devices', []))) {
            $stackDevices = ArrayHelper::get($settings, 'stacks_devices', array());
            $configSettings['stack_config'] = array(
                'stackable'      => $isStackable,
                'stacks_devices' => $stackDevices
            );
            $stackApearances = ArrayHelper::get($settings, 'stacks_appearances', array());
            if (is_array($stackApearances) && $stackApearances) {
                $extraStackClasses = implode(' ', $stackApearances);
                $table_classes .= ' ' . $extraStackClasses;
            }
        }

        if (!$enableSearch) {
            $table_classes .= ' ninja_table_search_disabled';
        }

        if (defined('NINJATABLESPRO')) {
            $table_classes .= ' ninja_table_pro';
            if (ArrayHelper::get($settings, 'hide_on_empty')) {
                $configSettings['hide_on_empty'] = true;
            }

            if (ArrayHelper::get($settings, 'paginate_to_top')) {
                $configSettings['paginate_to_top'] = true;
            }

            $configSettings['disable_sticky_on_mobile'] = ArrayHelper::get($settings, 'disable_sticky_on_mobile');
        }

        $advancedFilterSettings = get_post_meta($table_id, '_ninja_custom_filter_styling', true);
        $advancedFilters = get_post_meta($table_id, '_ninja_table_custom_filters', true);
        if ($advancedFilterSettings && $advancedFilters) {
            $defaultStyling = array(
                'filter_display_type' => 'inline',
                'filter_columns'      => 'columns_2',
                'filter_column_label' => 'new_line'
            );
            $advancedFilterSettings = wp_parse_args($advancedFilterSettings, $defaultStyling);
            if ($advancedFilterSettings['filter_display_type'] == 'inline') {
                $table_classes .= ' ninja_table_afd_inline';
            } else {
                $table_classes .= ' ninja_table_afd_' . $advancedFilterSettings['filter_display_type'];
                $table_classes .= ' ninja_table_afcs_' . $advancedFilterSettings['filter_columns'];
                $table_classes .= ' ninja_table_afcl_' . $advancedFilterSettings['filter_column_label'];
            }
            $table_classes .= ' ninja_table_has_custom_filter';
        } else if ($configSettings['defualt_filter']) {
            $table_classes .= ' ninja_has_filter';
        }

        $configSettings['has_formula'] = ArrayHelper::get($settings, 'formula_support', 'no');

	    $tableCaption = get_post_meta($table_id, '_ninja_table_caption', true);

        $table_vars = array(
            'table_id'         => $table_id,
            'title'            => $table->post_title,
            'caption'          => $tableCaption,
            'columns'          => $formatted_columns,
            'original_columns' => $columns,
            'settings'         => $configSettings,
            'render_type'      => $renderType,
            'custom_css'       => $customCss,
            'instance_name'    => $table_instance_name,
            'table_version'    => NINJA_TABLES_VERSION,
            'provider'         => $tableArray['provider'],
            'uniqueID'         => $uniqueID
        );

        $table_vars = apply_filters('ninja_table_rendering_table_vars', $table_vars, $table_id, $tableArray);

        if ($tableArray['provider'] == 'wp_woo') {
            $table_vars['wc_ajax_url'] = add_query_arg( array(
                'wc-ajax' => 'add_to_cart',
                'ninja_table' => $tableArray['table_id']
            ), home_url( '/', 'relative')  );
        }

        if ($renderType == 'ajax_table') {
            $totalSizeQuery = ninja_tables_DbTable()->where('table_id', $table_id);
            $totalSizeQuery = apply_filters('ninja_tables_total_size_query', $totalSizeQuery, $table_vars);
            $totalSize = $totalSizeQuery->count();
            $perChunk = ninjaTablePerChunk($table_id);
            if ($totalSize > $perChunk) {
                $table_vars['chunks'] = ceil($totalSize / $perChunk) - 1;
            }
        }


        $table_vars['init_config'] = self::getNinjaTableConfig($table_vars);

        self::addInlineVars(json_encode($table_vars, true), $table_id, $table_instance_name);
        $foo_table_attributes = self::getFootableAtrributes($table_vars);

        // We have to check if these css already rendered
        if (!isset(static::$tableCssStatuses[$tableArray['table_id']])) {
            $columnContentCss = static::getColumnsCss($tableArray['table_id'], $columns);

            static::addCustomColorCSS($tableArray, $columnContentCss);
        }

        do_action('ninja_table_before_render_table_source', $table, $table_vars, $tableArray);
        include 'views/ninja_foo_table.php';
    }

    /**
     * Generate column specific custom css.
     *
     * @param $tableId
     * @param $columns
     * @return string
     */
    public static function getColumnsCss($tableId, $columns)
    {
        $columnContentCss = '';

        foreach ( $columns as $index => $column) {
            if ($contentAlign = ArrayHelper::get($column, 'contentAlign')) {
                $columnContentCss .= '#footable_' . $tableId . ' td.ninja_column_' . $index
                    . ' { text-align: ' . $contentAlign . '; }';
            }

            if ($textAlign = ArrayHelper::get($column, 'textAlign')) {
                $columnContentCss .= '#footable_' . $tableId . ' th.ninja_column_' . $index
                    . ' { text-align: ' . $textAlign . '; }';
            }
        }

        return $columnContentCss;
    }

    public static function getTableHTML($table, $table_vars)
    {
        if ($table_vars['render_type'] == 'ajax_table') {
            return;
        }
        if ($table_vars['render_type'] == 'legacy_table') {
            self::generateLegacyTableHTML($table, $table_vars);
            return;
        }
    }

    private static function generateLegacyTableHTML($table, $table_vars)
    {
        $tableId = $table->ID;

        $limitRows = ArrayHelper::get($table_vars, 'settings.limit_rows', false);
        $skipRows = ArrayHelper::get($table_vars, 'settings.skip_rows', false);
        $tableColumns = $table_vars['columns'];
        $ownOnly = false;

        if (ArrayHelper::get($table_vars, 'editing.own_data_only') == 'yes') {
            $ownOnly = true;
        }

        $isHtmlCacheEnabled = ArrayHelper::get($table_vars, 'settings.enable_html_cache', true) == 'yes' &&
            ArrayHelper::get($table_vars, 'settings.shouldNotCache', true) != 'yes';

        if (!$ownOnly && $isHtmlCacheEnabled) {
            $cachedTableData = self::getTableCachedHTML($tableId, $table_vars);
            if ($cachedTableData) {
                echo $cachedTableData;
                return;
            }
        }

        $formatted_data = ninjaTablesGetTablesDataByID(
            $tableId,
            $tableColumns,
            $table_vars['settings']['default_sorting'],
            false,
            $limitRows,
            $skipRows,
            $ownOnly
        );

        $formatted_data = apply_filters('ninja_tables_get_public_data', $formatted_data, $table->ID);

        $tableHtml = self::loadView('public/views/table_inner_html', array(
            'table_columns' => $tableColumns,
            'table_rows'    => $formatted_data
        ));
        if ($isHtmlCacheEnabled) {
            update_post_meta($tableId, '__last_ninja_table_last_cached_time', time());
        }
        update_post_meta($tableId, '__ninja_cached_table_html', $tableHtml);
        echo $tableHtml;
        return;
    }

    private static function getTableCachedHTML($tableId, $table_vars)
    {
        $lastCachedTime = intval(get_post_meta($tableId, '__last_ninja_table_last_cached_time', true));
        $cacheValidationMinutes = floatval(ArrayHelper::get($table_vars, 'settings.html_caching_minutes', true));

        if (time() >= $lastCachedTime + ($cacheValidationMinutes * 60)) {
            return false;
        }
        // Get the cached data now
        $cachedTableHtml = get_post_meta($tableId, '__ninja_cached_table_html', true);

        if (strpos($cachedTableHtml, 'ninja_tobody_rendering_done')) {
            return $cachedTableHtml . '<!--ninja_cached_data-->';
        }
        return false;
    }

    private static function loadView($file, $data)
    {
        $file = NINJA_TABLES_DIR_PATH . $file . '.php';
        ob_start();
        extract($data);
        include $file;

        return ob_get_clean();
    }

    private static function getTableCssClass($settings)
    {

        $tableCassClasses = array(
            self::getTableClassByLib($settings['css_lib']),
            ArrayHelper::get($settings, 'extra_css_class', '')
        );

        if (ArrayHelper::get($settings, 'load_lightbox')) {
            $tableCassClasses[] = 'nt_has_lightbox';
            if (ArrayHelper::get($settings, 'iframe_lightbox')) {
                $tableCassClasses[] = 'nt_has_iframe_lightbox';
            }
            do_action('ninja_tables_load_lightbox', $settings);
        } else if (in_array('nt_has_lightbox', $tableCassClasses)) {
            do_action('ninja_tables_load_lightbox', $settings);
        }

        if (ArrayHelper::get($settings, 'info')) {
            $tableCassClasses[] = 'ninja_has_count_format';
        }

        if (
            ArrayHelper::get($settings, 'table_color_type') == 'pre_defined_color'
            && ArrayHelper::get($settings, 'table_color') != 'ninja_no_color_table'
        ) {
            $tableCassClasses[] = ArrayHelper::get($settings, 'table_color');
        }

        if ($searchBarPosition = ArrayHelper::get($settings, 'search_position')) {
            $tableCassClasses[] = 'ninja_search_' . $searchBarPosition;
        }

        if (ArrayHelper::get($settings, 'hide_responsive_labels')) {
            $tableCassClasses[] = 'nt_hide_breakpoint_labels';
        }

        if (ArrayHelper::get($settings, 'nt_search_full_width')) {
            $tableCassClasses[] = 'nt_search_full_width';
        }

        $tableCassClasses[] = 'nt_type_'.ArrayHelper::get($settings, 'render_type');

        $definedClasses = ArrayHelper::get($settings, 'css_classes', array());
        $classArray = array_merge($tableCassClasses, $definedClasses);
        $uniqueCssArray = array_unique($classArray);

        return implode(' ', $uniqueCssArray);
    }

    private static function getTableClassByLib($lib = 'bootstrap3')
    {
        switch ($lib) {
            case 'bootstrap3':
            case 'bootstrap4':
                return 'table';
            case 'semantic_ui':
                return 'ui table';
            default:
                return '';
        }
    }

    private static function addInlineVars($vars, $table_id, $table_instance_name)
    {
        add_action('wp_footer', function () use ($vars, $table_id, $table_instance_name) {
            ?>
            <script type="text/javascript">
                window['<?php echo $table_instance_name;?>'] = <?php echo $vars; ?>
            </script>
            <?php
        });
    }

    public static function getColumnType($column)
    {
        $type = (isset($column['data_type'])) ? $column['data_type'] : 'text';
        $acceptedTypes = array(
            'text',
            'number',
            'date',
            'html',
            'image'
        );
        if (in_array($type, $acceptedTypes)) {
            if ($type == 'number') {
                return 'numeric';
            }
            return $type;
        }

        return 'text';
    }

    private static function getFootableAtrributes($tableVars)
    {
	    $tableID = $tableVars['table_id'];

        $atts = array(
            'data-footable_id'  => $tableID,
            'data-filter-delay' => 500
        );

        if ($tableVars['title'] && !$tableVars['caption']) {
            $atts['aria-label'] = $tableVars['title'];
        }

        $atts = apply_filters('ninja_table_attributes', $atts, $tableID);

        $atts_string = '';
        if ($atts) {
            foreach ($atts as $att_name => $att) {
                $atts_string .= $att_name . '="' . $att . '" ';
            }
        }
        return (string)$atts_string;
    }

    public static function getFormattedColumn($column, $index, $settings, $globalSorting, $sortingType)
    {
        $columnType = self::getColumnType($column);
        $cssColumnName = 'ninja_column_' . $index;
        $columnClasses = array($cssColumnName);
        if (isset($column['classes'])) {
            $userClasses = explode(' ', $column['classes']);
            $columnClasses = array_unique(array_merge($columnClasses, $userClasses));
        }
        $customCss[$cssColumnName] = array();
        if ($columnWidth = ArrayHelper::get($column, 'width')) {
            $customCss[$cssColumnName]['width'] = $columnWidth . 'px';
        }
        if ($textAlign = ArrayHelper::get($column, 'textAlign')) {
            $customCss[$cssColumnName]['textAlign'] = $textAlign;
        }
        $columnTitle = $column['name'];
        if (ArrayHelper::get($column, 'enable_html_content') == 'true') {
            if ($columnContent = ArrayHelper::get($column, 'header_html_content')) {
                $columnTitle = do_shortcode($columnContent);
            }
        }
        $formatted_column = array(
            'name'        => $column['key'],
            'key'         => $column['key'],
            'title'       => $columnTitle,
            'breakpoints' => $column['breakpoints'],
            'type'        => $columnType,
            'sortable'    => $globalSorting,
            'visible'     => ($column['breakpoints'] == 'hidden') ? false : true,
            'classes'     => $columnClasses,
            'filterable'  => (isset($column['unfilterable']) && $column['unfilterable'] == 'yes') ? false : true,
            'column'      => $column
        );
        if ($columnType == 'date') {
            wp_enqueue_script(
                'moment',
                NINJA_TABLES_DIR_URL . "public/libs/moment/moment.min.js",
                [],
                '2.22.0',
                true
            );
            $formatted_column['formatString'] = $column['dateFormat'] ?: 'MM/DD/YYYY';
        }
        if ($sortingType == 'by_column' && $column['key'] == $settings['sorting_column']) {
            $formatted_column['sorted'] = true;
            $formatted_column['direction'] = $settings['sorting_column_by'];
        }
        return $formatted_column;
    }

    public static function getNinjaTableConfig($tableConfig)
    {

        $tableId = $tableConfig['table_id'];
        // Prepare Table Init Configuration
        $tableSettings = $tableConfig['settings'];
        $initConfig = array(
            "toggleColumn"   => ArrayHelper::get($tableSettings, 'togglePosition'),
            "cascade"        => true,
            "useParentWidth" => !!ArrayHelper::get($tableSettings, 'use_parent_width'),
            "columns"        => ArrayHelper::get($tableConfig, 'columns'),
            "expandFirst"    => ArrayHelper::get($tableSettings, 'expandFirst'),
            "expandAll"      => ArrayHelper::get($tableSettings, 'expandAll'),
            'empty'          => ArrayHelper::get($tableSettings, 'i18n.no_result_text'),
            "sorting"        => array(
                'enabled' => !!ArrayHelper::get($tableSettings, 'sorting')
            )
        );

        if (ArrayHelper::get($tableConfig, 'render_type') !== 'legacy_table') {

            $rowRequestUrlParams = array(
                'action'          => 'wp_ajax_ninja_tables_public_action',
                'table_id'        => $tableId,
                'target_action'   => 'get-all-data',
                'default_sorting' => ArrayHelper::get($tableSettings, 'default_sorting'),
                'skip_rows'       => ArrayHelper::get($tableSettings, 'skip_rows'),
                'limit_rows'      => ArrayHelper::get($tableSettings, 'limit_rows')
            );

            if (ArrayHelper::get($tableConfig, 'editing.check_editing') == 'yes' && ArrayHelper::get($tableConfig, 'editing.own_data_only') == 'yes') {
                $rowRequestUrlParams['own_only'] = 'yes';
            }


            $chucks = ArrayHelper::get($tableConfig, 'chunks', 0);
            if ($chucks > 0) {
                $rowRequestUrlParams['chunk_number'] = 0;
            }
            $initConfig['data_request_url'] = add_query_arg($rowRequestUrlParams, admin_url('admin-ajax.php'));

        }

        $enabledSearch = !!ArrayHelper::get($tableSettings, 'filtering');
        $defaultFilter = ArrayHelper::get($tableSettings, 'defualt_filter');

        if ($enabledSearch || $defaultFilter) {
            $enabledSearch = true;
        }

        $initConfig['filtering'] = array(
            "enabled"       => $enabledSearch,
            "delay"         => 1,
            "dropdownTitle" => ArrayHelper::get($tableSettings, 'i18n.search_in'),
            "placeholder"   => ArrayHelper::get($tableSettings, 'i18n.search'),
            "connectors"    => false,
            "ignoreCase"    => true
        );

        if ($defaultFilter) {
            if ($defaultFilter == "'0'") {
                $defaultFilter = "0";
            }
            $filterColumns = ArrayHelper::get($tableSettings, 'defualt_filter_column');
            $validColumns = array();
            if ($filterColumns && count($filterColumns)) {
                $columns = $tableConfig['columns'];
                foreach ($columns as $column) {
                    $columnName = ArrayHelper::get($column, 'name');
                    if (in_array($columnName, $filterColumns)) {
                        $validColumns[] = $columnName;
                    }
                }
            }
            $initConfig['filtering']['filters'] = array(
                array(
                    "name"    => "ninja_table_default_filter",
                    "hidden"  => ArrayHelper::get($tableSettings, 'hide_default_filter') == 'yes',
                    "query"   => $defaultFilter,
                    "columns" => $validColumns
                )
            );
        }

        $pageSize = ArrayHelper::get($tableSettings, 'paging');


        $initConfig['paging'] = array(
            "enabled"     => !!$pageSize,
            "position"    => "right",
            "size"        => $pageSize,
            "container"   => "#footable_parent_" . $tableId . " .paging-ui-container",
            "countFormat" => ArrayHelper::get($tableSettings, 'info', ' ')
        );

        $config = apply_filters('ninja_tables_js_init_config', $initConfig, $tableConfig, $tableId);
        return apply_filters('ninja_tables_js_init_config_' . $tableId, $config, $tableConfig, $tableId);
    }

    /**
     * Enqueue main public css.
     */
    public static function enqueuePublicCss()
    {
        $styleSrc = NINJA_TABLES_DIR_URL . "assets/css/ninjatables-public.css";

        if (is_rtl()) {
            $styleSrc = NINJA_TABLES_DIR_URL . "assets/css/ninjatables-public-rtl.css";
        }

        wp_enqueue_style(
            'footable_styles',
            $styleSrc,
            array(),
            self::$version,
            'all'
        );

        ninjaTablePreloadFont();
    }
}
