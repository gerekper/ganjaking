<?php
/**
 * Globally-accessible functions
 *
 * @link           https://authlab.io
 * @since          1.0.0
 *
 * @package        wp_table_data_press
 * @subpackage     wp_table_data_press/includes
 *
 * @param        $tableId
 * @param string $scope
 *
 * @return array
 */
if (!function_exists('ninja_table_get_table_columns')) {
    function ninja_table_get_table_columns($tableId, $scope = 'public')
    {
        $tableColumns = get_post_meta($tableId, '_ninja_table_columns', true);
        if (!$tableColumns || !is_array($tableColumns)) {
            $tableColumns = array();
        }
        return apply_filters('ninja_get_table_columns_' . $scope, $tableColumns, $tableId);
    }
}

if (!function_exists('ninja_table_get_table_settings')) {
    function ninja_table_get_table_settings($tableId, $scope = 'public')
    {
        $tableSettings = get_post_meta($tableId, '_ninja_table_settings', true);
        if (!$tableSettings) {
            $tableSettings = getDefaultNinjaTableSettings();
        } else {
            if (empty($tableSettings['css_classes'])) {
                $tableSettings['css_classes'] = array();
            }

            if (empty($tableSettings['stacks_devices'])) {
                $tableSettings['stacks_devices'] = array();
            }

            if (empty($tableSettings['stacks_appearances'])) {
                $tableSettings['stacks_appearances'] = array();
            }
        }

        return apply_filters('ninja_get_table_settings_' . $scope, $tableSettings, $tableId);
    }
}


if (!function_exists('getDefaultNinjaTableSettings')) {
    function getDefaultNinjaTableSettings()
    {
        $renderType = defined('NINJATABLESPRO') ? 'legacy_table' : 'ajax_table';
        $settings = get_option('_ninja_table_default_appearance_settings');
        $defaults = array(
            "perPage" => 20,
            "show_all" => false,
            "library" => 'footable',
            "css_lib" => 'semantic_ui',
            "enable_ajax" => false,
            "css_classes" => array(),
            "enable_search" => true,
            "column_sorting" => true,
            "default_sorting" => 'old_first',
            "sorting_type" => "by_created_at",
            "table_color" => 'ninja_no_color_table',
            "render_type" => $renderType,
            "table_color_type" => 'pre_defined_color',
            "expand_type" => 'default',
            'stackable'   => 'no',
            'stacks_devices' => array(),
            'stacks_appearances' => array()
        );
        if(!$settings) {
            $defaults['css_classes'] = array(
                'selectable',
                'striped',
                'vertical_centered'
            );
        }
        if(!$settings) {
            $settings = array();
        }
        $settings = wp_parse_args($settings,$defaults);

        return apply_filters('get_default_ninja_table_settings', $settings);
    }
}

if (!function_exists('ninja_table_admin_role')) {
    function ninja_table_admin_role()
    {
        if (current_user_can('administrator')) {
            return 'administrator';
        }
        $roles = apply_filters('ninja_table_admin_role', array('administrator'));
        if (is_string($roles)) {
            $roles = array($roles);
        }
        foreach ($roles as $role) {
            if (current_user_can($role)) {
                return $role;
            }
        }
        return false;
    }
}

if (!function_exists('ninja_tables_db_table_name')) {
    function ninja_tables_db_table_name()
    {
        return 'ninja_table_items';
    }
}

if (!function_exists('ninja_tables_DbTable')) {
    function ninja_tables_DbTable()
    {
        return ninjaDB(ninja_tables_db_table_name());
    }
}

if (!function_exists('ninja_table_renameDuplicateValues')) {
    function ninja_table_renameDuplicateValues($values)
    {
        $result = array();

        $scale = array_count_values(array_unique($values));

        foreach ($values as $item) {
            if ($scale[$item] == 1) {
                $result[] = $item;
            } else {
                $result[] = $item . '-' . $scale[$item];
            }

            $scale[$item]++;
        }

        return $result;
    }
}

if (!function_exists('ninja_table_is_in_production_mood')) {
    function ninja_table_is_in_production_mood()
    {
        return apply_filters('ninja_table_is_in_production_mood', false);
    }
}


function ninjaTablesGetTablesDataByID($tableId, $tableColumns = [], $defaultSorting = false, $disableCache = false, $limit = false, $skip = false, $ownOnly = false)
{
    $providerName = ninja_table_get_data_provider($tableId);
    $providerName = in_array($providerName, array('csv', 'google-csv')) ? 'csv' : $providerName;

    $data = apply_filters(
        'ninja_tables_fetching_table_rows_' . $providerName,
        array(),
        $tableId,
        $defaultSorting,
        $limit,
        $skip,
        $ownOnly
    );

    return $data;
}

function ninjaTablesClearTableDataCache($tableId)
{
    update_post_meta($tableId, '_ninja_table_cache_object', false);
    update_post_meta($tableId, '_ninja_table_cache_html', false);
    update_post_meta($tableId, '_external_cached_data', false);
    update_post_meta($tableId, '_last_external_cached_time', false);
    update_post_meta($tableId, '__ninja_cached_table_html', false);
}

function ninjaTablesAllowedHtmlTags($tags)
{
    $tags['a']['download'] = true;
    $tags['iframe'] = array(
        'src' => true,
        'srcdoc' => true,
        'width' => true,
        'height' => true,
        'scrolling' => true,
        'frameborder' => true,
        'allow' => true,
        'style' => true,
        'allowfullscreen' => true,
        'name' => true
    );

    return $tags;
}

/**
 * Determine if the table's data has been migrated for manual sorting.
 *
 * @param  int $tableId
 * @return bool
 */
function ninjaTablesDataMigratedForManualSort($tableId)
{
    // The post meta table would have a flag that the data of
    // the table is migrated to use for the manual sorting.
    $postMetaKey = '_ninja_tables_data_migrated_for_manual_sort';

    return !!get_post_meta($tableId, $postMetaKey, true);
}

/**
 * Determine if the user wants to disable the caching for the table.
 *
 * @param  int $tableId
 * @return bool
 */
function ninja_tables_shouldNotCache($tableId)
{
    $tableSettings = ninja_table_get_table_settings($tableId, 'public');
    return (
        isset($tableSettings['shouldNotCache']) && $tableSettings['shouldNotCache'] == 'yes'
    ) ? true : false;
}

/**
 * Get the ninja table icon url.
 *
 * @return string
 */
function ninja_table_get_icon_url()
{
    return 'data:image/svg+xml;base64,'
        . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 321.98 249.25"><defs><style>.cls-1{fill:#fff;}.cls-2,.cls-3{fill:none;stroke-miterlimit:10;stroke-width:7px;}.cls-2{stroke:#9fa3a8;}.cls-3{stroke:#38444f;}</style></defs><title>Asset 7</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M312.48,249.25H9.5a9.51,9.51,0,0,1-9.5-9.5V9.5A9.51,9.51,0,0,1,9.5,0h303A9.51,9.51,0,0,1,322,9.5V239.75A9.51,9.51,0,0,1,312.48,249.25ZM9.5,7A2.53,2.53,0,0,0,7,9.5V239.75a2.53,2.53,0,0,0,2.5,2.5h303a2.53,2.53,0,0,0,2.5-2.5V9.5a2.53,2.53,0,0,0-2.5-2.5Z"/><rect class="cls-1" x="74.99" y="44.37" width="8.75" height="202.71"/><path class="cls-2" d="M129.37,234.08"/><path class="cls-2" d="M129.37,44.37"/><path class="cls-3" d="M189.37,234.08"/><path class="cls-3" d="M189.37,44.37"/><path class="cls-3" d="M249.37,234.08"/><path class="cls-3" d="M249.37,44.37"/><path class="cls-1" d="M6.16.51H315.82a6,6,0,0,1,6,6V50.32a.63.63,0,0,1-.63.63H.79a.63.63,0,0,1-.63-.63V6.51A6,6,0,0,1,6.16.51Z"/><rect class="cls-1" x="4.88" y="142.84" width="312.61" height="15.1"/><rect class="cls-1" x="22.47" y="89.99" width="28.27" height="16.97"/><rect class="cls-1" x="111.61" y="89.99" width="165.67" height="16.97"/><rect class="cls-1" x="22.47" y="189.99" width="28.27" height="16.97"/><rect class="cls-1" x="111.61" y="189.99" width="165.67" height="16.97"/></g></g></svg>');
}

if (!function_exists('ninja_tables_is_valid_url')) {
    define('URL_FORMAT',
        '/^(https?):\/\/' .                                         // protocol
        '(([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+' .         // username
        '(:([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+)?' .      // password
        '@)?(?#' .                                                  // auth requires @
        ')((([a-z0-9]\.|[a-z0-9][a-z0-9-]*[a-z0-9]\.)*' .                      // domain segments AND
        '[a-z][a-z0-9-]*[a-z0-9]' .                                 // top level domain  OR
        '|((\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])\.){3}' .
        '(\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])' .                 // IP address
        ')(:\d+)?' .                                                // port
        ')(((\/+([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)*' . // path
        '(\?([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)' .      // query string
        '?)?)?' .                                                   // path and query string optional
        '(#([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)?' .      // fragment
        '$/i');
    function ninja_tables_is_valid_url($url)
    {
        return preg_match(URL_FORMAT, $url);
    }
}

if (!function_exists('ninja_tables_sanitize_array')) {
    function ninja_tables_sanitize_array(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = ninja_tables_sanitize_array($value);
            } else {
                $array[$key] = wp_kses_post($value);
            }
        }

        return $array;
    }
}


function ninjaTableGetExternalCachedData($tableId)
{
    $tableSettings = get_post_meta($tableId, '_ninja_table_settings', true);
    if (!isset($tableSettings['caching_interval']) && $tableSettings['caching_interval']) {
        return false;
    }
    $intervalMinutes = intval($tableSettings['caching_interval']);
    if (!$intervalMinutes) {
        return false;
    }
    $interval = $intervalMinutes * 60;
    $lastCachedTime = intval(get_post_meta($tableId, '_last_external_cached_time', true));

    if ((time() - $lastCachedTime) < $interval) {
        return get_post_meta($tableId, '_external_cached_data', true);
    }
    return false;
}

function ninjaTableSetExternalCacheData($tableId, $data)
{
    $tableSettings = get_post_meta($tableId, '_ninja_table_settings', true);
    if (!isset($tableSettings['caching_interval']) && $tableSettings['caching_interval']) {
        return false;
    }

    update_post_meta($tableId, '_last_external_cached_time', time());
    update_post_meta($tableId, '_external_cached_data', $data);
}

if (!function_exists('getNinjaFluentFormMenuIcon')) {
    function getNinjaFluentFormMenuIcon()
    {
        $icon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><defs><style>.cls-1{fill:#fff;}</style></defs><title>dashboard_icon</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M15.57,0H4.43A4.43,4.43,0,0,0,0,4.43V15.57A4.43,4.43,0,0,0,4.43,20H15.57A4.43,4.43,0,0,0,20,15.57V4.43A4.43,4.43,0,0,0,15.57,0ZM12.82,14a2.36,2.36,0,0,1-1.66.68H6.5A2.31,2.31,0,0,1,7.18,13a2.36,2.36,0,0,1,1.66-.68l4.66,0A2.34,2.34,0,0,1,12.82,14Zm3.3-3.46a2.36,2.36,0,0,1-1.66.68H3.21a2.25,2.25,0,0,1,.68-1.64,2.36,2.36,0,0,1,1.66-.68H16.79A2.25,2.25,0,0,1,16.12,10.53Zm0-3.73a2.36,2.36,0,0,1-1.66.68H3.21a2.25,2.25,0,0,1,.68-1.64,2.36,2.36,0,0,1,1.66-.68H16.79A2.25,2.25,0,0,1,16.12,6.81Z"/></g></g></svg>');

        return apply_filters('fluent_form_menu_icon', $icon);
    }
}


if (!function_exists('ninjaTablesGetPostStatuses')) {
    function ninjaTablesGetPostStatuses()
    {
        return [
            ['key' => 'publish', 'label' => 'Publish'],
            ['key' => 'pending', 'label' => 'Pending'],
            ['key' => 'draft', 'label' => 'Draft'],
            ['key' => 'auto-draft', 'label' => 'Auto Draft'],
            ['key' => 'future', 'label' => 'Future'],
            ['key' => 'private', 'label' => 'Private'],
            ['key' => 'inherit', 'label' => 'Inherit'],
            ['key' => 'trash', 'label' => 'Trash'],
            ['key' => 'any', 'label' => 'Any'],
        ];
    }
}

if (!function_exists('ninja_table_get_data_provider')) {
    function ninja_table_get_data_provider($tableId)
    {
        $provider = get_post_meta($tableId, '_ninja_tables_data_provider', true);
        if (!$provider) {
            $provider = 'default';
        }
        return $provider;
    }
}

if (!function_exists('ninja_table_format_header')) {
    function ninja_table_format_header($header)
    {
        $acceptedChars = array(
            'a','b','c','d','e','f','g','h','i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
            'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );

        $data = array();
        $column_counter = 1;
        foreach ($header as $item) {
            $string = trim(strip_tags($item));
            $string = strtolower($string);
            $chars = str_split($string);
            $key = '';
            foreach ($chars as $char) {
                if(in_array($char, $acceptedChars)) {
                    $key .= $char;
                }
            }
            $key = sanitize_title($key, 'ninja_column_' . $column_counter, 'display');
            $counter = 1;
            while (isset($data[$key])) {
                $key .= '_' . $counter;
                $counter++;
            }
            $data[$key] = $item;
            $column_counter++;
        }
        return $data;
    }
}

if(!function_exists('ninja_table_url_slug')) {
    function ninja_table_url_slug($str, $options = array())
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '_',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = array(
            // Latin
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ő' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ű' => 'U',
            'Ý' => 'Y',
            'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ő' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ű' => 'u',
            'ý' => 'y',
            'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A',
            'Β' => 'B',
            'Γ' => 'G',
            'Δ' => 'D',
            'Ε' => 'E',
            'Ζ' => 'Z',
            'Η' => 'H',
            'Θ' => '8',
            'Ι' => 'I',
            'Κ' => 'K',
            'Λ' => 'L',
            'Μ' => 'M',
            'Ν' => 'N',
            'Ξ' => '3',
            'Ο' => 'O',
            'Π' => 'P',
            'Ρ' => 'R',
            'Σ' => 'S',
            'Τ' => 'T',
            'Υ' => 'Y',
            'Φ' => 'F',
            'Χ' => 'X',
            'Ψ' => 'PS',
            'Ω' => 'W',
            'Ά' => 'A',
            'Έ' => 'E',
            'Ί' => 'I',
            'Ό' => 'O',
            'Ύ' => 'Y',
            'Ή' => 'H',
            'Ώ' => 'W',
            'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a',
            'β' => 'b',
            'γ' => 'g',
            'δ' => 'd',
            'ε' => 'e',
            'ζ' => 'z',
            'η' => 'h',
            'θ' => '8',
            'ι' => 'i',
            'κ' => 'k',
            'λ' => 'l',
            'μ' => 'm',
            'ν' => 'n',
            'ξ' => '3',
            'ο' => 'o',
            'π' => 'p',
            'ρ' => 'r',
            'σ' => 's',
            'τ' => 't',
            'υ' => 'y',
            'φ' => 'f',
            'χ' => 'x',
            'ψ' => 'ps',
            'ω' => 'w',
            'ά' => 'a',
            'έ' => 'e',
            'ί' => 'i',
            'ό' => 'o',
            'ύ' => 'y',
            'ή' => 'h',
            'ώ' => 'w',
            'ς' => 's',
            'ϊ' => 'i',
            'ΰ' => 'y',
            'ϋ' => 'y',
            'ΐ' => 'i',
            // Turkish
            'Ş' => 'S',
            'İ' => 'I',
            'Ç' => 'C',
            'Ü' => 'U',
            'Ö' => 'O',
            'Ğ' => 'G',
            'ş' => 's',
            'ı' => 'i',
            'ç' => 'c',
            'ü' => 'u',
            'ö' => 'o',
            'ğ' => 'g',
            // Russian
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'Yo',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Sh',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye',
            'І' => 'I',
            'Ї' => 'Yi',
            'Ґ' => 'G',
            'є' => 'ye',
            'і' => 'i',
            'ї' => 'yi',
            'ґ' => 'g',
            // Czech
            'Č' => 'C',
            'Ď' => 'D',
            'Ě' => 'E',
            'Ň' => 'N',
            'Ř' => 'R',
            'Š' => 'S',
            'Ť' => 'T',
            'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c',
            'ď' => 'd',
            'ě' => 'e',
            'ň' => 'n',
            'ř' => 'r',
            'š' => 's',
            'ť' => 't',
            'ů' => 'u',
            'ž' => 'z',
            // Polish
            'Ą' => 'A',
            'Ć' => 'C',
            'Ę' => 'e',
            'Ł' => 'L',
            'Ń' => 'N',
            'Ó' => 'o',
            'Ś' => 'S',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a',
            'ć' => 'c',
            'ę' => 'e',
            'ł' => 'l',
            'ń' => 'n',
            'ó' => 'o',
            'ś' => 's',
            'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A',
            'Č' => 'C',
            'Ē' => 'E',
            'Ģ' => 'G',
            'Ī' => 'i',
            'Ķ' => 'k',
            'Ļ' => 'L',
            'Ņ' => 'N',
            'Š' => 'S',
            'Ū' => 'u',
            'Ž' => 'Z',
            'ā' => 'a',
            'č' => 'c',
            'ē' => 'e',
            'ģ' => 'g',
            'ī' => 'i',
            'ķ' => 'k',
            'ļ' => 'l',
            'ņ' => 'n',
            'š' => 's',
            'ū' => 'u',
            'ž' => 'z',
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }
}


function ninjaTableInsertDataToTable($tableId, $values, $header)
{
    $header = array_keys($header);
    $time = current_time('mysql');
    $headerCount = count($header);
    $timeStamp = time();
    $userId = get_current_user_id();
    $datas = [];

    foreach ($values as $index => $item) {
        if ($headerCount == count($item)) {
            $itemTemp = array_combine($header, $item);
        } else {
            // The item can have less/more entry than the header has.
            // We have to ensure that the header and values match.
            $itemTemp = array_combine(
                $header,
                // We'll get the appropriate values by merging Array1 & Array2
                array_merge(
                // Array1 = Only the entries that the header has.
                    array_intersect_key($item, array_fill_keys(array_values($header), null)),
                    // Array2 = The remaining header entries will be blank.
                    array_fill_keys(array_diff(array_values($header), array_keys($item)), null)
                )
            );
        }

        $data = array(
            'table_id' => $tableId,
            'attribute' => 'value',
            'owner_id' => $userId,
            'value' => json_encode($itemTemp, JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s', $timeStamp + $index),
            'updated_at' => $time
        );

        if(isset($item['position']) && defined('NINJAPROPLUGIN_VERSION')) {
            $data['position'] = $item['position'];
        }

        $datas[] = $data;
    }

    // We are gonna batch insert by small chunk so that we can avoid PHP
    // memory issue or MYSQL max_allowed_packet issue for large data set.
    global $wpdb;
    $tableName = $wpdb->prefix . ninja_tables_db_table_name();
    foreach (array_chunk($datas, 3000) as $chunk) {
        ninjtaTableBatchInsert($tableName, $chunk);
    }
}

function ninjaTablePerChunk($table_id = false) {
    return apply_filters('ninja_table_per_chunk', 3000, $table_id);
}

function ninja_table_clear_all_cache()
{
    $tables = ninjaDB()->table('posts')
                ->select('ID')
                ->where('post_type', 'ninja-table')
                ->get();
    foreach ($tables as $table) {
        ninjaTablesClearTableDataCache($table->ID);
    }
    return true;
}

/**
 * Batch insert data using raw SQL query.
 *
 * @param  string $table
 * @param  array $rows
 * @return bool|int
 */
function ninjtaTableBatchInsert($table, $rows) {
    global $wpdb;

    // Extract column list from first row of data
    $columns = array_keys($rows[0]);
    asort($columns);
    $columnList = '`' . implode('`, `', $columns) . '`';
    // Start building SQL, initialise data and placeholder arrays
    $sql = "INSERT INTO `$table` ($columnList) VALUES\n";
    $placeholders = array();
    $data = array();
    // Build placeholders for each row, and add values to data array
    foreach ($rows as $row) {
        ksort($row);
        $rowPlaceholders = array();
        foreach ($row as $key => $value) {
            $data[] = $value;
            $rowPlaceholders[] = is_numeric($value) ? '%d' : '%s';
        }
        $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
    }
    // Stitch all rows together
    $sql .= implode(",\n", $placeholders);
    // Run the query.  Returns number of affected rows.
    return $wpdb->query($wpdb->prepare($sql, $data));
}

/**
 * Normalize every item, i.e. make string "true" to boolean true
 *
 * @param  array $data
 * @return array
 */
function ninjaTableNormalize($data = []) {
    foreach ($data as $key => $item) {
        if ($item == 'false') {
            $item = false;
        }

        if ($item == 'true') {
            $item = true;
        }

        if (is_array($item)) {
            $item = array_map('sanitize_text_field', $item);
        } else {
            $item = sanitize_text_field($item);
        }

        $data[$key] = $item;
    }

    return $data;
}

/**
 * Parse the given html content get the table IDs from the matched shortcodes.
 *
 * @param  string $content
 * @return array
 */
function ninjaTablesGetShortCodeIds($content) {
    $tag = 'ninja_tables';

    if (false === strpos($content, '[')) {
        return [];
    }

    preg_match_all('/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return [];
    }

    $ids = [];

    foreach ($matches as $shortcode) {
        if ($tag === $shortcode[2]) {
            // Replace braces with empty string.
            $parsedCode = str_replace(['[', ']', '&#91;', '&#93;'], '', $shortcode[0]);

            $result = shortcode_parse_atts($parsedCode);

            if (!empty($result['id'])) {
                $ids[$result['id']] = $result['id'];
            }
        }
    }

    return $ids;
}

/**
 * Preloads frontend custom font.
 */
function ninjaTablePreloadFont () {
    add_action('wp_head', function () {
        $preloadFontUrl = NINJA_TABLES_DIR_URL . "assets/fonts/ninja-tables.woff2?" . NINJA_TABLES_PRELOAD_FONT_VERSION;
        ?>
        <link rel="preload" as="font" href="<?php echo $preloadFontUrl ?>" type="font/woff2" crossorigin="anonymous">
        <?php
    }, 99);
}

/**
 * Prints admin styles
 */
function ninjaTablesAdminPrintStyles() {
    add_action('admin_print_styles', function () {
        ?>
        <style>
            #adminmenu #toplevel_page_ninja_tables li.ninja_tables_help:before {
                background: #b4b9be;
                content: "";
                display: block;
                height: 1px;
                margin: 5px auto 0;
                width: calc(100% - 24px);
                opacity: .4;
            }
        </style>
        <?php
    });
}
