<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Helper
 *
 * @class RightPress_Help
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Help
{

    /**
     * Include template
     *
     * @access public
     * @param string $template
     * @param string $plugin_path
     * @param string $plugin_name
     * @param array $args
     * @param string $custom_path
     * @return void
     */
    public static function include_template($template, $plugin_path, $plugin_name, $args = array(), $custom_path = null)
    {
        if ($args && is_array($args)) {
            extract($args);
        }

        // Get template path
        $template_path = self::get_template_path($template, $plugin_path, $plugin_name, $custom_path);

        // Check if template exists
	    if (!file_exists($template_path)) {

            // Add admin debug notice
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_path), get_bloginfo('version'));
            return;
	    }

        // Include template
        include $template_path;
    }

    /**
     * Include extension template
     *
     * @access public
     * @param string $extension_key
     * @param string $template
     * @param string $plugin_path
     * @param string $plugin_name
     * @param array $args
     * @return void
     */
    public static function include_extension_template($extension_key, $template, $plugin_path, $plugin_name, $args = array())
    {
        $custom_path = 'extensions/' . $extension_key . '/';
        RightPress_Help::include_template($template, $plugin_path, $plugin_name, $args, $custom_path);
    }

    /**
     * Select correct template (allow overrides in theme folder)
     *
     * @access public
     * @param string $template
     * @param string $plugin_path
     * @param string $plugin_name
     * @param string $custom_path
     * @return string
     */
    public static function get_template_path($template, $plugin_path, $plugin_name, $custom_path = null)
    {
        $template = rtrim($template, '.php') . '.php';

        // Check if this template exists in current theme
        if (!($template_path = locate_template(array($plugin_name . '/' . $custom_path . $template)))) {
            $template_path = $plugin_path . $custom_path . 'templates/' . $template;
        }

        return $template_path;
    }

    /**
     * Check WooCommerce version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wc_version_gte($version)
    {
        if (defined('WC_VERSION') && WC_VERSION) {
            return version_compare(WC_VERSION, $version, '>=');
        }
        else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
            return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
        }
        else {
            return false;
        }
    }

    /**
     * Check WordPress version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wp_version_gte($version)
    {
        $wp_version = get_bloginfo('version');

        // Treat release candidate strings
        $wp_version = preg_replace('/-RC.+/i', '', $wp_version);

        if ($wp_version) {
            return version_compare($wp_version, $version, '>=');
        }

        return false;
    }

    /**
     * Check PHP version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function php_version_gte($version)
    {
        return version_compare(PHP_VERSION, $version, '>=');
    }

    /**
     * Check if string contains phrase that starts with a given string
     *
     * @access public
     * @param string $string
     * @param string $phrase
     * @return bool
     */
    public static function string_contains_phrase($string, $phrase)
    {
        return preg_match('/.*(^|\s|#|[^A-Za-z0-9])' . preg_quote($phrase) . '.*/i', $string) === 1 ? true : false;
    }

    /**
     * Get list of roles assigned to current user
     *
     * @access public
     * @return array
     */
    public static function current_user_roles()
    {
        return is_user_logged_in() ? RightPress_Help::user_roles(get_current_user_id()) : array();
    }

    /**
     * Get list of roles assigned to specific user
     *
     * @access public
     * @param int $user_id
     * @return array
     */
    public static function user_roles($user_id)
    {

        // Get user
        $user = get_userdata($user_id);

        // Get user roles
        return $user ? (array) $user->roles : array();
    }

    /**
     * Get list of capabilities assigned to current user
     *
     * @access public
     * @return array
     */
    public static function current_user_capabilities()
    {

        $capabilities = is_user_logged_in() ? RightPress_Help::user_capabilities(get_current_user_id()) : array();
        return apply_filters('rightpress_current_user_capabilities', $capabilities);
    }

    /**
     * Get list of capabilities assigned to specific user
     *
     * @access public
     * @param int $user_id
     * @return array
     */
    public static function user_capabilities($user_id)
    {
        // Groups plugin active?
        if (class_exists('Groups_User') && class_exists('Groups_Wordpress')) {
            $groups_user = new Groups_User($user_id);

            if ($groups_user) {
                return $groups_user->capabilities_deep;
            }
            else {
                return array();
            }
        }

        // Get regular WP capabilities
        else {

            // Get user data
            $user = get_userdata($user_id);
            $all_user_capabilities = $user->allcaps;
            $user_capabilities = array();

            if (is_array($all_user_capabilities)) {
                foreach ($all_user_capabilities as $capability => $status) {
                    if ($status) {
                        $user_capabilities[] = $capability;
                    }
                }
            }

            return $user_capabilities;
        }
    }

    /**
     * Get optimized lowercase locale with dash as a separator
     *
     * @access public
     * @param string $method
     *    - single - return first part of the locale only
     *    - double - return both parts of the locale only
     *    - mixed - return first part if both locales match and both parts if they differ
     * @return string
     */
    public static function get_optimized_locale($method = 'single')
    {
        // Split WordPress locale
        $parts = explode('_', get_locale());

        // Expected result?
        if (is_array($parts) && count($parts) == 2 && $parts[1] != 'US') {
            $first = strtolower($parts[0]);
            $second = strtolower($parts[1]);

            // Single, double or mixed?
            if ($method == 'single') {
                return $first;
            }
            else if ($method == 'double') {
                return $first . '-' . $second;
            }
            else if ($method == 'mixed') {
                return $first == $second ? $first : $first . '-' . $second;
            }
        }

        // Fallback
        return $method == 'double' ? 'en_en' : 'en';
    }

    /**
     * Add WooCommerce notice
     *
     * @access public
     * @param string $message
     * @param string $notice_type
     * @return void
     */
    public static function wc_add_notice($message, $notice_type = 'success')
    {
        wc_add_notice($message, $notice_type);
    }

    /**
     * Get array of term ids - parent term id and all children ids
     *
     * WC31: As of WC 3.4 taxonomies/terms are still used throughout WooCommerce
     *
     * @access public
     * @param int $id
     * @param string $taxonomy
     * @return array
     */
    public static function get_term_with_children($id, $taxonomy)
    {
        $term_ids = array();

        // Check if term exists
        if (!get_term_by('id', $id, $taxonomy)) {
            return $term_ids;
        }

        // Store parent
        $term_ids[] = (int) $id;

        // Get and store children
        $children = get_term_children($id, $taxonomy);
        $term_ids = array_merge($term_ids, $children);
        $term_ids = array_unique($term_ids);

        return $term_ids;
    }

    /**
     * Check if post exists
     *
     * @access public
     * @param int $post_id
     * @return bool
     */
    public static function post_exists($post_id)
    {
        return get_post_status($post_id) !== false;
    }

    /**
     * Check post type
     *
     * @access public
     * @param mixed $post
     * @param string $type
     * @return bool
     */
    public static function post_type_is($post, $type)
    {
        return get_post_type($post) === $type;
    }

    /**
     * Check post status
     *
     * @access public
     * @param mixed $post
     * @param string $status
     * @return bool
     */
    public static function post_status_is($post, $status)
    {
        $post_id = is_object($post) ? $post->ID : $post;
        return get_post_status($post_id) === $status;
    }

    /**
     * Check if post is existant and not in trash
     *
     * @access public
     * @param int $post_id
     * @return bool
     */
    public static function post_is_active($post_id)
    {
        return self::post_exists($post_id) && !self::post_is_trashed($post_id);
    }

    /**
     * Check if post is trashed
     *
     * @access public
     * @param int $post_id
     * @return bool
     */
    public static function post_is_trashed($post_id)
    {
        return self::post_status_is($post_id, 'trash');
    }

    /**
     * Maybe strip dash and number from the end of term slug
     *
     * @access public
     * @param string $slug
     * @return string
     */
    public static function clean_term_slug($slug)
    {
        return preg_replace('/-\d+/', '', $slug);
    }

    /**
     * Unwrap array elements from get_post_meta moves all [0] elements one level higher
     *
     * @access public
     * @param array $input
     * @return array
     */
    public static function unwrap_post_meta($input)
    {
        $output = array();

        foreach ((array) $input as $key => $value) {

            if (is_array($value) && count($value) === 1) {
                $output[$key] = $value[0];
            }
            else {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Cast value to specified data type
     * Accepts types: int, bool, float, string, array, object, unset
     * Casts null and empty string to null for int and float (instead of 0) to differentiate between empty value and a zero set by user
     *
     * @access public
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    public static function cast_to($type = 'string', $value = '')
    {
        if ($type === 'int') {
            return ($value !== '' && $value !== null) ? (int) $value : null;
        }
        else if ($type === 'bool') {
            return (bool) $value;
        }
        else if ($type === 'float') {
            return ($value !== '' && $value !== null) ? (float) $value : null;
        }
        else if ($type === 'string') {
            return (string) $value;
        }
        else if ($type === 'array') {
            return (array) $value;
        }
        else if ($type === 'object') {
            return (object) $value;
        }
        else if ($type === 'unset') {
            return null;
        }
        else {
            return $value;
        }
    }

    /**
     * Get empty value by data type
     * Accepts types: int, bool, float, string, array, object, unset
     * Uses null instead of 0 for int and float to indicate that value is indeed empty and not a zero set by user
     *
     * @access public
     * @param string $type
     * @return mixed
     */
    public static function get_empty_value_by_type($type = 'string')
    {
        if ($type === 'int') {
            return null;
        }
        else if ($type === 'bool') {
            return false;
        }
        else if ($type === 'float') {
            return null;
        }
        else if ($type === 'string') {
            return '';
        }
        else if ($type === 'array') {
            return array();
        }
        else if ($type === 'object') {
            return new stdClass();
        }
        else if ($type === 'unset') {
            return null;
        }
        else {
            return '';
        }
    }

    /**
     * Insert element to array before specific key
     *
     * @access public
     * @param array $array
     * @param string $search
     * @param array $insert
     * @return array
     */
    public static function insert_to_array_before_key($array, $search, $insert)
    {
        return RightPress_Help::insert_to_array_before_or_after_key($array, $search, $insert, 'before');
    }

    /**
     * Insert element to array after specific key
     *
     * @access public
     * @param array $array
     * @param string $search
     * @param array $insert
     * @return array
     */
    public static function insert_to_array_after_key($array, $search, $insert)
    {
        return RightPress_Help::insert_to_array_before_or_after_key($array, $search, $insert, 'after');
    }

    /**
     * Insert element to array before or after specific key
     *
     * @access public
     * @param array $array
     * @param string $search
     * @param array $insert
     * @param string $where
     * @return array
     */
    public static function insert_to_array_before_or_after_key($array, $search, $insert, $where)
    {
        // Get position of the seach key
        if (isset($array[$search])) {
            $position = array_search($search, array_keys($array)) + (($where === 'before') ? 0 : 1);
        }
        else {
            $position = ($where === 'before') ? 0 : count($array);
        }

        // Extract array parts before and after proposed position
        $before = array_slice($array, 0, $position, true);
        $after = array_slice($array, $position, null, true);

        // Merge arrays and return
        return array_merge($before, $insert, $after);
    }

    /**
     * Sort array so that it starts from a specified key
     *
     * Sort order between elements before and after specified element are not affected
     *
     * @access public
     * @param array $array
     * @param string|int $key
     * @return array
     */
    public static function sort_array_to_start_from_key($array, $key)
    {
        // Get position of given key in array
        $position = isset($array[$key]) ? array_search($key, array_keys($array), true) : 0;

        // Extract array parts before and after this position
        $before = array_slice($array, 0, $position, true);
        $after = array_slice($array, $position, null, true);

        // Merge arrays in reverse order so that the final array starts with requested key
        return array_merge($after, $before);
    }

    /**
     * Shorten text
     *
     * @access public
     * @param string $text
     * @param int $max_chars
     * @return string
     */
    public static function shorten_text($text, $max_chars)
    {
        if (mb_strlen($text) > ($max_chars + 3)) {
            return mb_substr($text, 0, $max_chars) . '...';
        }

        return $text;
    }

    /**
     * Check if post meta key exists for a given post
     *
     * @access public
     * @param int $post_id
     * @param string $meta_key
     * @return bool
     */
    public static function post_meta_key_exists($post_id, $meta_key)
    {
        return metadata_exists('post', $post_id, $meta_key);
    }

    /**
     * Check if order item meta key exists for a given order item
     *
     * @access public
     * @param int $order_item_id
     * @param string $meta_key
     * @return bool
     */
    public static function order_item_meta_key_exists($order_item_id, $meta_key)
    {
        $order_item = RightPress_Help::wc_get_order_item($order_item_id);
        return $order_item ? $order_item->meta_exists($meta_key) : false;
    }

    /**
     * Check if user meta key exists for a given user
     *
     * @access public
     * @param int $user_id
     * @param string $meta_key
     * @return bool
     */
    public static function user_meta_key_exists($user_id, $meta_key)
    {
        return metadata_exists('user', $user_id, $meta_key);
    }

    /**
     * Check if meta key exists for item of a given context
     *
     * Supported meta contexts: post, order_item, user
     *
     * @access public
     * @param string $meta_key
     * @param mixed $meta_contexts
     * @param int $item_id
     * @return bool
     */
    public static function meta_key_exists($meta_key, $meta_contexts = null, $item_id = null)
    {
        return self::get_meta($meta_key, $meta_contexts, $item_id, true);
    }

    /**
     * Get meta row by meta key
     *
     * Supported meta contexts: post, order_item, user
     *
     * @access public
     * @param string $meta_key
     * @param mixed $meta_contexts
     * @param int $item_id
     * @return bool
     */
    public static function get_meta_row($meta_key, $meta_contexts = null, $item_id = null)
    {
        return self::get_meta($meta_key, $meta_contexts, $item_id, false, true);
    }

    /**
     * Get meta value by meta key
     *
     * Supported meta contexts: post, order_item, user
     *
     * @access public
     * @param string $meta_key
     * @param mixed $meta_contexts
     * @param int $item_id
     * @param bool $count_only
     * @param bool $get_row
     * @return mixed
     */
    public static function get_meta($meta_key, $meta_contexts = null, $item_id = null, $count_only = false, $get_row = false)
    {
        global $wpdb;

        $meta_contexts = (array) $meta_contexts;

        // Get all contexts if left empty
        if (empty($meta_contexts)) {
            $meta_contexts = array('post', 'order_item', 'user');
        }

        // Check all meta contexts
        foreach ($meta_contexts as $meta_context) {

            // Get meta table name
            $table = _get_meta_table($meta_context);

            // Set up item constraint
            $item_constraint = $item_id !== null ? 'AND ' . $meta_context . '_id = ' . absint($item_id) : '';

            // Prepare fields to get
            if ($get_row) {
                $fields = '*';
            }
            else if ($count_only) {
                $fields = 'COUNT(*)';
            }
            else {
                $fields = 'meta_value';
            }

            // Prepare query
            $sql = $wpdb->prepare("SELECT $fields FROM $table WHERE meta_key = %s $item_constraint", $meta_key);

            // Run query
            if ($get_row) {
                $result = $wpdb->get_row($sql, ARRAY_A);
            }
            else {
                $result = $wpdb->get_var($sql);
            }

            // Check result
            if ($result) {
                return $count_only ? true : $result;
            }
        }

        return false;
    }

    /**
     * Delete meta row by meta key
     *
     * Supported meta contexts: post, order_item, user
     *
     * @access public
     * @param string $meta_key
     * @param mixed $meta_contexts
     * @param int $item_id
     * @return bool
     */
    public static function delete_meta($meta_key, $meta_contexts = null, $item_id = null)
    {
        $meta_contexts = (array) $meta_contexts;

        // Get all contexts if left empty
        if (empty($meta_contexts)) {
            $meta_contexts = array('post', 'order_item', 'user');
        }

        // Check all meta contexts
        foreach ($meta_contexts as $meta_context) {

            // Delete meta row(s)
            delete_metadata($meta_context, $item_id, $meta_key, '', !$item_id);
        }
    }

    /**
     * Get hash - either random or based on provided data
     *
     * @access public
     * @param bool $long
     * @param mixed $data
     * @return string
     */
    public static function get_hash($long = false, $data = null)
    {
        // Get data to hash
        $data = $data !== null ? json_encode($data) : (rand() . time() . rand());

        // Generate hash
        $hash = md5($data);

        // Shorten hash if needed and return
        return $long ? $hash : substr($hash, 0, 8);
    }

    /**
     * Get array value by key or return false if not set
     *
     * Unserializes value if serialized
     * Searches first level only
     *
     * @access public
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public static function array_value_or_false($array, $key)
    {
        return isset($array[$key]) ? maybe_unserialize($array[$key]) : false;
    }

    /**
     * Get file content type (mime type) depending on PHP version
     *
     * @access public
     * @param string $file_path
     * @return string
     */
    public static function get_file_content_type($file_path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $file_path);
    }

    /**
     * Print link to post edit page
     *
     * @access public
     * @param int $id
     * @param string $title
     * @param string $pre
     * @param string $post
     * @param int $max_chars
     * @return void
     */
    public static function print_link_to_post($id, $title = '', $pre = '', $post = '', $max_chars = null)
    {
        echo self::get_link_to_post_html($id, $title, $pre, $post, $max_chars);
    }

    /**
     * Format link to post edit page
     *
     * @access public
     * @param int $id
     * @param string $title
     * @param string $pre
     * @param string $post
     * @param int $max_chars
     * @return string
     */
    public static function get_link_to_post_html($id, $title = '', $pre = '', $post = '', $max_chars = null)
    {
        // Get title to display
        $link_title = '';
        $title_to_display = !empty($title) ? $title : '#' . $id;

        // Maybe shorten title
        if ($max_chars !== null && mb_strlen($title_to_display) > ($max_chars + 3)) {
            $link_title = $title_to_display;
            $title_to_display = RightPress_Help::shorten_text($title_to_display, $max_chars);
        }

        // Make link and return
        return $pre . ' <a href="post.php?post=' . $id . '&action=edit" title="' . $link_title . '">' . $title_to_display . '</a> ' . $post;
    }

    /**
     * Print frontend link to post
     *
     * @access public
     * @param int $id
     * @param string $title
     * @param string $pre
     * @param string $post
     * @return void
     */
    public static function print_frontend_link_to_post($id, $title = '', $pre = '', $post = '')
    {
        echo self::get_frontend_link_to_post_html($id, $title, $pre, $post);
    }

    /**
     * Format frontend link to post
     *
     * @access public
     * @param int $id
     * @param string $title
     * @param string $pre
     * @param string $post
     * @return void
     */
    public static function get_frontend_link_to_post_html($id, $title = '', $pre = '', $post = '')
    {
        $title_to_display = !empty($title) ? $title : '#' . $id;
        $html = $pre . ' <a href="' . get_permalink($id) . '">' . $title_to_display . '</a> ' . $post;
        return $html;
    }

    /**
     * Check if value is date with correct format
     *
     * @access public
     * @param string $value
     * @param string $format
     * @return bool
     */
    public static function is_date($value, $format)
    {
        // Initialize DateTime object
        $datetime = DateTime::createFromFormat($format, $value, self::get_time_zone());

        // Check if dates correspond
        return ($datetime && $datetime->format($format) === $value);
    }

    /**
     * Check if value is hexadecimal color code
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function is_hexadecimal_color_code($value)
    {
        // Check if string starts with hash symbol
        if (RightPress_Help::string_begins_with_substring($value, '#')) {

            // Strip hash symbol
            $value = substr($value, 1);

            // Check if remaining value is hexadecimal with either 3 or 6 characters
            if (ctype_xdigit($value) && (strlen($value) === 6 || strlen($value) === 3)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Convert date/time string from one format to another
     *
     * @access public
     * @param string $value
     * @param sting $from_format
     * @param string $to_format
     * @return string
     */
    public static function convert_date_from_format_to_format($value, $from_format, $to_format)
    {

        // Get datetime object from format
        $datetime = RightPress_Help::date_create_from_format($from_format, $value);

        // Format date to new format
        return $datetime->format($to_format);
    }

    /**
     * Get date time object from date format and value
     *
     * Does not accept timestamps as there are issues with timezones then,
     * use get_datetime_object() when timestamp is available
     *
     * @access public
     * @param string $format
     * @param string $value
     * @return object
     */
    public static function date_create_from_format($format, $value)
    {
        $timezone_string = RightPress_Help::get_time_zone_string();
        $timezone = new DateTimeZone($timezone_string);
        return DateTime::createFromFormat($format, $value, $timezone);
    }

    /**
     * Make readable date/time from timestamp (yyyy-mm-dd hh:mm:ss)
     *
     * @access public
     * @param int $timestamp
     * @return string
     */
    public static function get_iso_datetime($timestamp = null)
    {
        $timestamp = ($timestamp === null ? time() : $timestamp);
        return RightPress_Help::get_adjusted_datetime($timestamp, RightPress_Help::get_iso_datetime_format(true));
    }

    /**
     * Get timestamp from date string with current time
     *
     * @access public
     * @param string $date
     * @param bool $set_time
     * @return int
     */
    public static function get_timestamp_from_date_string($date, $set_time = false)
    {
        // Get date object from date operation (e.g. +4 weeks, next monday)
        $dt = RightPress_Help::get_datetime_object($date, false);

        // Maybe set date (required for some operations that set time to 00:00:00)
        if ($set_time) {
            $t = RightPress_Help::get_datetime_object();
            $dt->setTime($t->format('H'), $t->format('i'), $t->format('s'));
        }

        // Format and return timestamp
        return $dt->format('U');
    }

    /**
     * Get timezone-adjusted formatted date/time string
     *
     * @access public
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    public static function get_adjusted_datetime($timestamp = null, $format = null)
    {
        // Get timestamp
        $timestamp = ($timestamp !== null ? $timestamp : time());

        // Get datetime object
        $date_time = self::get_datetime_object($timestamp);

        // Get datetime as string in ISO format
        $date_time_iso = $date_time->format(RightPress_Help::get_iso_datetime_format(true));

        // Hack to make date_i18n() work with our time zone
        $date_time_utc = new DateTime($date_time_iso);
        $time_zone_utc = new DateTimeZone('UTC');
        $date_time_utc->setTimezone($time_zone_utc);

        // Get format
        $format = ($format !== null ? $format : (get_option('date_format') . ' ' . get_option('time_format')));

        // Format and return
        return date_i18n($format, $date_time_utc->format('U'));
    }

    /**
     * Get ISO date format
     *
     * @access public
     * @return string
     */
    public static function get_iso_date_format()
    {

        return 'Y-m-d';
    }

    /**
     * Get ISO time format
     *
     * @access public
     * @param bool $include_seconds
     * @return string
     */
    public static function get_iso_time_format($include_seconds = false)
    {

        return 'H:i' . ($include_seconds ? ':s' : '');
    }

    /**
     * Get ISO datetime format
     *
     * @access public
     * @param bool $include_seconds
     * @return string
     */
    public static function get_iso_datetime_format($include_seconds = false)
    {

        return 'Y-m-d H:i' . ($include_seconds ? ':s' : '');
    }

    /**
     * Get usable datetime object with correct time zone from timestamp
     *
     * @access public
     * @param mixed $date
     * @param bool $date_is_timestamp
     * @return object
     */
    public static function get_datetime_object($date = null, $date_is_timestamp = true)
    {
        if ($date !== null && $date_is_timestamp) {
            $date = '@' . $date;
        }

        // Get datetime object
        $date_time = new DateTime();

        // Set timestamp if passed in
        if ($date_is_timestamp && $date !== null) {
            $date_time->modify($date);
        }

        // Set correct time zone
        $time_zone = self::get_time_zone();
        $date_time->setTimezone($time_zone);

        // Set date if passed in
        if (!$date_is_timestamp && $date !== null) {
            $date_time->modify($date);
        }

        return $date_time;
    }

    /**
     * Get timezone object
     *
     * @access public
     * @return object
     */
    public static function get_time_zone()
    {
        return new DateTimeZone(self::get_time_zone_string());
    }

    /**
     * Get timezone string
     *
     * @access public
     * @return string
     */
    public static function get_time_zone_string()
    {
        // Timezone string
        if ($time_zone = get_option('timezone_string')) {
            return $time_zone;
        }

        // Offset
        if ($utc_offset = get_option('gmt_offset')) {

            // Offsets supported
            if (RightPress_Help::php_version_gte('5.5.10')) {
                return ($utc_offset < 0 ? '-' : '+') . gmdate('Hi', floor(abs($utc_offset) * 3600));
            }
            // Offsets not supported
            else {

                $utc_offset = $utc_offset * 3600;
                $dst = date('I');

                // Try to get timezone name from offset
                if ($time_zone = timezone_name_from_abbr('', $utc_offset)) {
                    return $time_zone;
                }

                // Try to guess timezone by looking at a list of all timezones
                foreach (timezone_abbreviations_list() as $abbreviation) {
                    foreach ($abbreviation as $city) {
                        if ($city['dst'] == $dst && $city['offset'] == $utc_offset && isset($city['timezone_id'])) {
                            return $city['timezone_id'];
                        }
                    }
                }
            }
        }

        return 'UTC';
    }

    /**
     * Check if this is a demo of the plugin
     *
     * @access public
     * @return bool
     */
    public static function is_demo()
    {
        return (strpos(self::get_request_url(), 'demo.rightpress.net') !== false);
    }

    /**
     * Get full URL of current request
     *
     * @access public
     * @return string
     */
    public static function get_request_url()
    {
        return 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Check if value is empty but not zero, that is - empty string,
     * null, boolean false or empty array
     *
     * @access public
     * @param mixed $value
     * @return bool
     */
    public static function is_empty($value)
    {
        return ($value === '' || $value === null || $value === false || ((is_array($value) || is_object($value)) && count($value) === 0));
    }

    /**
     * Check if value represents field that is checked
     *
     * @access public
     * @param array|string $value
     * @return bool
     */
    public static function is_checked($value)
    {
        if (gettype($value) === 'array') {

            // All elements need to match for this to be valid
            foreach ($value as $single_value) {
                if (!$single_value) {
                    return false;
                }
            }

            // If we reached this point, each array element is checked
            return true;
        }
        else if ($value) {
            return true;
        }

        return false;
    }

    /**
     * Check if value equals string
     *
     * Designed to work with meta data conditions
     *
     * @access public
     * @param array|string $value
     * @param string $string
     * @return bool
     */
    public static function equals($value, $string)
    {
        if (gettype($value) === 'array') {

            // All elements need to match for this to be valid
            foreach ($value as $single_value) {
                if ($single_value !== $string) {
                    return false;
                }
            }

            // If we reached this point, each array element matches string
            return true;
        }
        else {
            return ($value === $string);
        }

        return false;
    }

    /**
     * Check if value contains string
     *
     * Designed to work with meta data conditions
     *
     * @access public
     * @param array|string $value
     * @param string $string
     * @return bool
     */
    public static function contains($value, $string)
    {
        if (gettype($value) === 'array') {
            return in_array($string, $value, true);
        }
        else {
            return (strpos($value, $string) !== false);
        }

        return false;
    }

    /**
     * Check if value begins with string
     *
     * Designed to work with meta data conditions
     *
     * @access public
     * @param array|string $value
     * @param string $string
     * @return bool
     */
    public static function begins_with($value, $string)
    {
        if (gettype($value) === 'array') {
            $first = array_shift($value);
            return $first === $string;
        }
        else {
            return RightPress_Help::string_begins_with_substring($value, $string);
        }

        return false;
    }

    /**
     * Check if value ends with string
     *
     * Designed to work with meta data conditions
     *
     * @access public
     * @param array|string $value
     * @param string $string
     * @return bool
     */
    public static function ends_with($value, $string)
    {
        if (gettype($value) === 'array') {
            $last = array_pop($value);
            return $last === $string;
        }
        else {
            return RightPress_Help::string_ends_with_substring($value, $string);
        }

        return false;
    }

    /**
     * Check if value is more than number
     *
     * @access public
     * @param mixed $value
     * @param string $number
     * @return bool
     */
    public static function more_than($value, $number)
    {
        if (gettype($value) === 'array') {

            // All elements need to match for this to be valid
            foreach ($value as $single_value) {
                if ($single_value <= $number) {
                    return false;
                }
            }

            // If we reached this point, each array element is bigger than number
            return true;
        }
        else {
            return ($value > $number);
        }

        return false;
    }

    /**
     * Check if value is less than number
     *
     * @access public
     * @param mixed $value
     * @param string $number
     * @return bool
     */
    public static function less_than($value, $number)
    {
        if (gettype($value) === 'array') {

            // All elements need to match for this to be valid
            foreach ($value as $single_value) {
                if ($single_value >= $number) {
                    return false;
                }
            }

            // If we reached this point, each array element is smaller than number
            return true;
        }
        else {
            return ($value < $number);
        }

        return false;
    }

    /**
     * Check if current page is backend user edit page
     *
     * @access public
     * @return bool
     */
    public static function is_wp_backend_user_edit_page()
    {
        return defined('IS_PROFILE_PAGE');
    }

    /**
     * Check if current page is backend new user page
     *
     * @access public
     * @return bool
     */
    public static function is_wp_backend_new_user_page()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        return (is_object($screen) && $screen->base === 'user' && $screen->action === 'add');
    }

    /**
     * Check if current page is frontend user registration page
     *
     * @access public
     * @return bool
     */
    public static function is_wp_frontend_user_registration_page()
    {
        global $pagenow;
        return ($pagenow === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'register');
    }

    /**
     * Filter associative array by an array of allowed keys
     *
     * @access public
     * @param array $input
     * @param array $allowed
     * @return array
     */
    public static function filter_by_keys($input, $allowed)
    {
        return array_intersect_key($input, array_flip($allowed));
    }

    /**
     * Filter associative array by an array of allowed keys and set default
     * values if they do not exist
     *
     * @access public
     * @param array $input
     * @param array $allowed
     * @return array
     */
    public static function filter_by_keys_with_defaults($input, $allowed)
    {
        return array_merge($allowed, self::filter_by_keys($input, array_keys($allowed)));
    }

    /**
     * Support for currency switcher extensions
     *
     * This is only supposed to be used for fees or fixed discounts set in config
     * of our own extensions - do not apply this to any prices set in WooCommerce
     * as currency switcher extensions are already converting those prices
     *
     * @access public
     * @param float $amount
     * @param array $plugins
     * @param string $to_currency
     * @param string $from_currency
     * @return float
     */
    public static function get_amount_in_currency($amount, $plugins = null, $to_currency = null, $from_currency = null)
    {

        // Iterate over list of supported plugins
        foreach (array('aelia', 'realmag777', 'wpml') as $plugin) {

            // Check if this plugin needs to be used
            if (!is_array($plugins) || in_array($plugin, $plugins, true)) {

                // Convert amount
                $method = 'get_amount_in_currency_' . $plugin;
                $amount = RightPress_Help::$method($amount, $to_currency, $from_currency);
            }
        }

        // Return possibly converted amount
        return $amount;
    }

    /**
     * Support for Aelia currency switcher extension
     *
     * This is only supposed to be used for fees or fixed discounts set in config
     * of our own extensions - do not apply this to any prices set in WooCommerce
     * as currency switcher extensions are already converting those prices
     *
     * @access public
     * @param float $amount
     * @param string $to_currency
     * @param string $from_currency
     * @return float
     */
    public static function get_amount_in_currency_aelia($amount, $to_currency = null, $from_currency = null)
    {

        // Get from currency
        $from_currency = $from_currency ?: get_option('woocommerce_currency');

        // Get to currency
        $to_currency = $to_currency ?: get_woocommerce_currency();

        // Currency Switcher for WooCommerce by Aelia
        // https://aelia.co/shop/currency-switcher-woocommerce/
        $amount = apply_filters('wc_aelia_cs_convert', $amount, $from_currency, $to_currency);

        // Return possibly converted amount
        return (float) $amount;
    }

    /**
     * Support for RealMag777 currency switcher extension
     *
     * This is only supposed to be used for fees or fixed discounts set in config
     * of our own extensions - do not apply this to any prices set in WooCommerce
     * as currency switcher extensions are already converting those prices
     *
     * @access public
     * @param float $amount
     * @param string $to_currency
     * @param string $from_currency
     * @return float
     */
    public static function get_amount_in_currency_realmag777($amount, $to_currency = null, $from_currency = null)
    {

        // WooCommerce Currency Switcher by RealMag777
        // https://wordpress.org/plugins/woocommerce-currency-switcher/
        // https://codecanyon.net/item/woocommerce-currency-switcher/8085217
        $amount = apply_filters('woocs_exchange_value', $amount);

        // Return possibly converted amount
        return (float) $amount;
    }

    /**
     * Support for WPML currency switcher extension
     *
     * This is only supposed to be used for fees or fixed discounts set in config
     * of our own extensions - do not apply this to any prices set in WooCommerce
     * as currency switcher extensions are already converting those prices
     *
     * @access public
     * @param float $amount
     * @param string $to_currency
     * @param string $from_currency
     * @return float
     */
    public static function get_amount_in_currency_wpml($amount, $to_currency = null, $from_currency = null)
    {

        // WPML Currency Switcher
        $amount = apply_filters('wcml_raw_price_amount', $amount, $to_currency);

        // Return possibly converted amount
        return (float) $amount;
    }

    /**
     * Get order total in base currency
     *
     * Support for Aelia and RealMag777 currency switcher extensions
     *
     * @access public
     * @param mixed $order
     * @return float|bool
     */
    public static function get_wc_order_total_in_base_currency($order)
    {
        // Load order object
        if (!is_a($order, 'WC_Order')) {
            $order = wc_get_order($order);
        }

        // RealMag777 currency switcher support
        if ($order_base_currency = RightPress_WC::order_get_meta($order, '_woocs_order_base_currency', true)) {
            if ($order_base_currency !== $order->get_currency()) {
                if ($currency_rate = RightPress_WC::order_get_meta($order, '_woocs_order_rate', true)) {
                    return (float) ($order->get_total() / $currency_rate);
                }
            }
        }

        // Aelia currency switcher support
        if ($order_total = RightPress_WC::order_get_meta($order, '_order_total_base_currency', true)) {
            return (float) $order_total;
        }

        // Currency was not changed for this order (or unsupported currency
        // switcher extension was used)
        return false;
    }

    /**
     * Check what kind of WordPress request this is
     *
     * Adapted from WooCommerce since they have set their method to private
     *
     * @access public
     * @param string $type
     * @return bool
     */
    public static function is_request($type)
    {
        $type = (string) $type;

        switch ($type)
        {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return ((!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON'));
            default:
                RightPress_Help::doing_it_wrong(__METHOD__, "Request type $type is not defined.", '1007');
                exit;
        }
    }

    /**
     * Attempt to get WooCommerce Product id
     *
     * @access public
     * @param mixed $product_id
     * @return void
     */
    public static function get_wc_product_id($product_id = null)
    {
        // Already set
        if ($product_id !== null) {
            return (int) $product_id;
        }

        global $post;

        // Post of type product
        // WC31: used only in Custom Fields, need to solve there
        if ($post && is_object($post) && isset($post->post_type) && $post->post_type === 'product') {
            return (int) $post->ID;
        }

        // Add to cart via GET
        if (isset($_GET['add-to-cart']) && is_numeric($_GET['add-to-cart']) && wc_get_product($_GET['add-to-cart'])) {
            return (int) $_GET['add-to-cart'];
        }

        // Add to cart via POST - version 1
        if (isset($_POST['action']) && $_POST['action'] === 'woocommerce_add_to_cart' && isset($_POST['product_id']) && is_numeric($_POST['product_id']) && wc_get_product($_POST['product_id'])) {
            return (int) $_POST['product_id'];
        }

        // Add to cart via POST - version 2
        if (isset($_POST['add-to-cart']) && is_numeric($_POST['add-to-cart']) && wc_get_product($_POST['add-to-cart'])) {
            return (int) $_POST['add-to-cart'];
        }

        // Failed figuring out product id
        return null;
    }

    /**
     * Attempt to get WooCommerce Order id
     *
     * @access public
     * @param mixed $order_id
     * @return void
     */
    public static function get_wc_order_id($order_id = null)
    {
        // Already set
        if ($order_id !== null) {
            return $order_id;
        }

        global $post;

        // Post of type shop order
        // WC31: used only in Custom Fields, need to solve there
        if ($post && is_object($post) && isset($post->post_type) && $post->post_type === 'shop_order') {
            return $post->ID;
        }

        // View Order query var
        if (get_query_var('view-order')) {
            return get_query_var('view-order');
        }

        // Order Received query var
        if (get_query_var('order-received')) {
            return get_query_var('order-received');
        }

        // Order items ajax request
        if (is_admin() && is_ajax() && isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('woocommerce_load_order_items', 'woocommerce_save_order_items', 'woocommerce_calc_line_taxes', 'woocommerce_get_order_details'), true) && !empty($_REQUEST['order_id'])) {
            return $_REQUEST['order_id'];
        }

        // Failed figuring out order id
        return null;
    }

    /**
     * Check if user owns WooCommerce Order
     *
     * @access public
     * @param int $user_id
     * @param int $order_id
     * @return bool
     */
    public static function user_owns_wc_order($user_id, $order_id)
    {
        // Load order object
        $order = wc_get_order($order_id);

        // Check if order was loaded
        if ($order) {

            // Get user id from order
            $order_user_id = $order->get_customer_id();

            // Check if order belongs to user
            if (!empty($order_user_id) && (int) $user_id === (int) $order_user_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user owns WooCommerce Order Item
     *
     * @access public
     * @param int $user_id
     * @param int $order_item_id
     * @return bool
     */
    public static function user_owns_wc_order_item($user_id, $order_item_id)
    {
        // Get order id
        $order_id = RightPress_Help::get_wc_order_id_from_order_item_id($order_item_id);

        // Check if user owns order
        return $order_id ? RightPress_Help::user_owns_wc_order($user_id, $order_id) : false;
    }

    /**
     * Get WooCommerce Order id from Order Item id
     *
     * @access public
     * @param int $order_item_id
     * @return int
     */
    public static function get_wc_order_id_from_order_item_id($order_item_id)
    {
        $order_item = RightPress_Help::wc_get_order_item($order_item_id);
        return $order_item ? $order_item->get_order_id() : false;
    }

    /**
     * Get WooCommerce coupon id from code
     *
     * @access public
     * @param string $code
     * @return int
     */
    public static function get_wc_coupon_id_from_code($code)
    {
        return wc_get_coupon_id_by_code($code);
    }

    /**
     * Ensure string is unique by appending suffix to it
     *
     * @access public
     * @param string $string
     * @param mixed $compare
     * @param string $suffix
     * @return string
     */
    public static function ensure_unique_string($string, $compare, $suffix = '_1')
    {
        // Iterate till we come up with unique string
        while (true) {

            // Check if string is unique
            if (is_array($compare)) {
                $is_unique = !in_array($string, $compare, true);
            }
            else {
                $is_unique = $string !== $compare;
            }

            // Either break and return or append suffix and try again
            if ($is_unique) {
                break;
            }
            else {
                $string .= $suffix;
            }
        }

        return $string;
    }

    /**
     * Get WooCommerce shipping class id by product id
     *
     * @access public
     * @param int $product_id
     * @param int $variation_id
     * @return mixed
     */
    public static function get_wc_product_shipping_class_id($product_id, $variation_id = null)
    {
        foreach (array($variation_id, $product_id) as $id) {

            if ($id !== null) {

                if ($product = wc_get_product($id)) {

                    if (!$product->is_virtual()) {

                        if ($class_id = $product->get_shipping_class_id()) {
                            return $class_id;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get WooCommerce product attribute ids
     *
     * @access public
     * @param int $product_id
     * @param array $selected
     * @return array
     */
    public static function get_wc_product_attribute_ids($product_id, $selected = array())
    {
        $attribute_ids = array();

        // Get product
        $product = wc_get_product($product_id);

        // Product is variation
        if ($product->is_type('variation')) {

            // Get variation attributes if none were provided
            if (empty($selected)) {

                $selected = $product->get_variation_attributes();
            }

            // Switch to parent variable product
            $product_id = $product->get_parent_id();
            $product    = wc_get_product($product_id);
        }

        // Unable to load product
        if (!is_a($product, 'WC_Product')) {
            return $attribute_ids;
        }

        // Get product attributes
        $attributes = $product->get_attributes();

        // Iterate over product attributes
        foreach ($attributes as $attribute) {

            // Only taxonomy-based attributes are supported
            if (!$attribute->is_taxonomy()) {
                continue;
            }

            // Attribute is not used for variations
            if (!in_array($product->get_type(), array('variable', 'variation'), true) || !$attribute->get_variation()) {

                // Add attribute terms
                foreach ($attribute->get_terms() as $term) {
                    $attribute_ids[] = $term->term_id;
                }
            }
            // Attribute is used for variations
            else {

                // Iterate over attribute terms
                foreach ($attribute->get_terms() as $term) {

                    // Check if this term has been selected
                    foreach ($selected as $attribute_key => $selected_term_slug) {
                        if (RightPress_Help::string_ends_with_substring($attribute_key, $term->taxonomy) && ($selected_term_slug === $term->slug || $selected_term_slug === '')) {
                            $attribute_ids[] = $term->term_id;
                        }
                    }
                }
            }
        }

        return $attribute_ids;
    }

    /**
     * Get WooCommerce product tag ids
     *
     * @access public
     * @param int $product_id
     * @return array
     */
    public static function get_wc_product_tag_ids($product_id)
    {
        if ($product = wc_get_product($product_id)) {
            return $product->get_tag_ids();
        }

        return array();
    }

    /**
     * Check if string begins with a given string
     *
     * @access public
     * @param string $string
     * @param string $substring
     * @return bool
     */
    public static function string_begins_with_substring($string, $substring)
    {
        return $substring === '' || strpos($string, $substring) === 0;
    }

    /**
     * Check if string ends with a given string
     *
     * @access public
     * @param string $string
     * @param string $substring
     * @return bool
     */
    public static function string_ends_with_substring($string, $substring)
    {
        return $substring === '' || (($temp = strlen($string) - strlen($substring)) >= 0 && strpos($string, $substring, $temp) !== false);
    }

    /**
     * Replace comma in numeric value with dot but revert to original string if is_numeric() fails on a new value
     *
     * @access public
     * @param mixed $value
     * @return string
     */
    public static function fix_decimal_separator($value)
    {
        // Replace comma with dot
        $new_value = str_replace(',', '.', (string) $value);

        // Check if result is numeric value
        if (is_numeric($new_value)) {
            return $new_value;
        }
        else {
            return $value;
        }
    }

    /**
     * Get WooCommerce cart
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @return object|bool
     */
    public static function get_wc_cart()
    {

        // Check if cart is loaded
        if (isset(WC()->cart) && is_a(WC()->cart, 'WC_Cart')) {

            return WC()->cart;
        }
        else {

            return false;
        }
    }

    /**
     * Get WooCommerce cart items
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @return array
     */
    public static function get_wc_cart_items()
    {

        // Get cart
        if ($cart = RightPress_Help::get_wc_cart()) {

            // Get cart items
            return $cart->get_cart();
        }

        // Unable to get cart
        return array();
    }

    /**
     * Get WooCommerce cart subtotal
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param bool $include_tax
     * @return float
     */
    public static function get_wc_cart_subtotal($include_tax = true)
    {

        // Get cart
        if ($cart = RightPress_Help::get_wc_cart()) {

            // Get subtotal
            return $include_tax ? $cart->subtotal : $cart->subtotal_ex_tax;
        }

        // Unable to get cart
        return 0.0;
    }

    /**
     * Calculate our own cart subtotal since in some cases it may not be available yet
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * Note: looks like line_subtotal and line_subtotal_tax are not always set
     * when this is called, however calculating by adding $product->get_price()
     * is not a good idea. Need to monitor if it affects anything from the users
     * perspective.
     *
     * @access public
     * @param bool $include_tax
     * @return float
     */
    public static function calculate_subtotal($include_tax = false)
    {

        $subtotal = 0.0;

        // Iterate over cart items
        foreach (RightPress_Help::get_wc_cart_items() as $cart_item) {

            if (isset($cart_item['line_subtotal'])) {

                // Add line subtotal
                $subtotal += $cart_item['line_subtotal'];

                // Add line subtotal tax
                if (isset($cart_item['line_subtotal_tax']) && $include_tax) {
                    $subtotal += $cart_item['line_subtotal_tax'];
                }
            }
        }

        return $subtotal;
    }

    /**
     * Get WooCommerce cart contents weight
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return float
     */
    public static function get_wc_cart_contents_weight($cart_items = null)
    {
        $weight = 0.0;

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Iterate over cart items
        if (is_array($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $cart_item) {
                $weight += (float) $cart_item['data']->get_weight() * $cart_item['quantity'];
            }
        }

        // Return cart weight
        return $weight;
    }

    /**
     * Get WooCommerce cart applied coupon ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @return array
     */
    public static function get_wc_cart_applied_coupon_ids()
    {

        // Get applied coupon ids
        $applied_coupon_ids = array();

        // Get cart
        if ($cart = RightPress_Help::get_wc_cart()) {

            // Iterate over applied coupons
            foreach ($cart->get_applied_coupons() as $applied_coupon) {
                $coupon_id = RightPress_Help::get_wc_coupon_id_from_code($applied_coupon);

                if (!in_array($coupon_id, $applied_coupon_ids)) {
                    $applied_coupon_ids[] = $coupon_id;
                }
            }
        }

        return $applied_coupon_ids;
    }

    /**
     * Get sum of WooCommerce cart item quantities
     *
     * @access public
     * @param array $cart_items
     * @param array $params
     * @return int
     */
    public static function get_wc_cart_sum_of_item_quantities($cart_items = null, $params = array())
    {
        $sum = 0;

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Check if we have any items
        if (is_array($cart_items) && !empty($cart_items)) {

            // Optionally filter cart items
            if (!empty($params)) {
                $cart_items = RightPress_Help::wc_filter_cart_items($cart_items, $params);
            }

            // Add all quantities
            foreach ($cart_items as $cart_item) {
                $sum += $cart_item['quantity'];
            }
        }

        // Return sum of quantities
        return $sum;
    }

    /**
     * Get sum of WooCommerce cart item subtotals
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $params
     * @param bool $include_tax
     * @return float
     */
    public static function get_wc_cart_sum_of_item_subtotals($params = array(), $include_tax = true)
    {
        $sum = 0.0;

        // Include all cart items
        if (empty($params)) {
            $sum = RightPress_Help::get_wc_cart_subtotal($include_tax);
        }
        // Filter cart items
        else {

            // Get cart items
            $cart_items = RightPress_Help::get_wc_cart_items();

            // Filter cart items
            $cart_items = RightPress_Help::wc_filter_cart_items($cart_items, $params);

            // Iterate over cart items
            foreach ($cart_items as $cart_item) {

                if (isset($cart_item['line_subtotal'])) {

                    // Add subtotal
                    $sum += $cart_item['line_subtotal'];

                    // Add subtotal tax
                    if ($include_tax && isset($cart_item['line_subtotal_tax'])) {
                        $sum += $cart_item['line_subtotal_tax'];
                    }
                }
            }
        }

        return $sum;
    }

    /**
     * Get sum of WooCommerce order item quantities
     *
     * @access public
     * @param array $order_items
     * @param array $params
     * @return int
     */
    public static function get_wc_order_sum_of_item_quantities($order_items, $params = array())
    {
        $sum = 0;

        // Check if we have any items
        if (is_array($order_items) && !empty($order_items)) {

            // Optionally filter order items
            if (!empty($params)) {
                $order_items = RightPress_Help::wc_filter_order_items($order_items, $params);
            }

            // Add all quantities
            foreach ($order_items as $order_item) {
                $sum += $order_item->get_quantity();
            }
        }

        // Return sum of quantities
        return $sum;
    }

    /**
     * Get sum of WooCommerce order item values
     *
     * @access public
     * @param array $order_items
     * @param array $params
     * @param bool $include_tax
     * @return float
     */
    public static function get_wc_order_sum_of_item_values($order_items, $params = array(), $include_tax = true)
    {
        $sum = 0.0;

        // Check if we have any items
        if (is_array($order_items) && !empty($order_items)) {

            // Optionally filter order items
            if (!empty($params)) {
                $order_items = RightPress_Help::wc_filter_order_items($order_items, $params);
            }

            // Add all values
            foreach ($order_items as $order_item) {

                // Add subtotal
                $sum += $order_item->get_subtotal();

                // Add subtotal tax
                if ($include_tax) {
                    $sum += $order_item->get_subtotal_tax();
                }
            }
        }

        return $sum;
    }

    /**
     * Filter WooCommerce cart items
     *
     * @access public
     * @param array $cart_items
     * @param array $params
     * @return array
     */
    public static function wc_filter_cart_items($cart_items, $params)
    {
        $items = array();

        // Prepare items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            $items[$cart_item_key] = array(
                'product_id'    => !empty($cart_item['product_id']) ? (string) $cart_item['product_id'] : null,
                'variation_id'  => RightPress_Help::get_wc_variation_id_from_cart_item($cart_item),
                'attribute_ids' => RightPress_Help::get_wc_product_attribute_ids_from_cart_item($cart_item),
            );
        }

        // Filter items
        $items = RightPress_Help::wc_filter_items($items, $params);

        // Filter cart items by remaining items
        $cart_items = array_intersect_key($cart_items, $items);

        // Return filtered cart items array
        return $cart_items;
    }

    /**
     * Filter WooCommerce order items
     *
     * @access public
     * @param array $order_items
     * @param array $params
     * @return array
     */
    public static function wc_filter_order_items($order_items, $params)
    {
        $items = array();

        // Prepare items
        foreach ($order_items as $order_item_key => $order_item) {

            // Get product id
            $product_id     = $order_item->get_product_id();
            $variation_id   = $order_item->get_variation_id();

            // Get attributes
            $attribute_ids = array();

            if (!empty($product_id) && !empty($variation_id)) {

                // Get selected variation attributes
                $selected = array();

                // Load variation
                $variation = wc_get_product($variation_id);

                // Get variation attributes
                $attributes = $variation->get_variation_attributes();

                // Get selected attributes
                foreach ($attributes as $attribute_key => $attribute_value) {

                    // Value is set
                    if ($attribute_value !== '') {
                        $selected[$attribute_key] = $attribute_value;
                    }
                    // Search for value
                    else {
                        $selected[$attribute_key] = $order_item->get_meta(str_replace('attribute_', '', $attribute_key), true, 'edit');
                    }
                }

                // Get attribute ids
                $attribute_ids = RightPress_Help::get_wc_product_attribute_ids($product_id, $selected);
            }

            // Fill items array
            $items[$order_item_key] = array(
                'product_id'    => !empty($product_id) ? (string) $product_id : null,
                'variation_id'  => !empty($variation_id) ? (string) $variation_id : null,
                'attribute_ids' => $attribute_ids,
            );
        }

        // Filter items
        $items = RightPress_Help::wc_filter_items($items, $params);

        // Filter order items by remaining items
        $order_items = array_intersect_key($order_items, $items);

        // Return filtered order items array
        return $order_items;
    }

    /**
     * Filter WooCommerce items
     *
     * Accepts array of arrays with the following structure
     * - key
     *   - product_id
     *   - variation_id
     *   - attribute_ids
     *
     * Params accepts one or more of the following properties:
     * - products               array of product ids
     * - product_variations     array of product variation ids
     * - product_categories     array of product category ids
     * - product_attributes     array or product attribute ids
     * - product_tags           array of product tag ids
     *
     * @access public
     * @param array $items
     * @param array $params
     * @return array
     */
    public static function wc_filter_items($items, $params)
    {
        $filtered = array();

        // Iterate over items
        foreach ($items as $item_key => $item) {

            // Products
            if (!empty($params['products'])) {
                if (empty($item['product_id']) || !in_array($item['product_id'], $params['products'], true)) {
                    continue;
                }
            }

            // Product variations
            if (!empty($params['product_variations'])) {
                if (empty($item['variation_id']) || !in_array((string) $item['variation_id'], $params['product_variations'], true)) {
                    continue;
                }
            }

            // Product categories
            if (!empty($params['product_categories'])) {

                // No product id
                if (empty($item['product_id'])) {
                    continue;
                }

                // Get item category ids
                $item_category_ids = RightPress_Help::get_wc_product_category_ids_from_product_ids(array($item['product_id']));
                $item_category_ids = array_map('strval', $item_category_ids);

                // Get condition category ids with children
                $condition_category_ids = array();

                foreach ($params['product_categories'] as $category_id) {
                    $condition_category_ids = array_merge($condition_category_ids, RightPress_Help::get_term_with_children($category_id, 'product_cat'));
                }

                $condition_category_ids = array_map('strval', $condition_category_ids);

                // Get matching category ids
                $matching_category_ids = array_intersect($item_category_ids, $condition_category_ids);

                // Check if at least one category id is matching
                if (empty($matching_category_ids)) {
                    continue;
                }
            }

            // Product attributes
            if (!empty($params['product_attributes'])) {

                // Get item attribute ids
                $item_attribute_ids = array_map('strval', $item['attribute_ids']);

                // Get matching attribute ids
                $matching_attribute_ids = array_intersect($item_attribute_ids, $params['product_attributes']);

                // Check if at least one attribute id is matching
                if (empty($matching_attribute_ids)) {
                    continue;
                }
            }

            // Product tags
            if (!empty($params['product_tags'])) {

                // No product id
                if (empty($item['product_id'])) {
                    continue;
                }

                // Get item tag ids
                $item_tag_ids = RightPress_Help::get_wc_product_tag_ids_from_product_ids(array($item['product_id']));
                $item_tag_ids = array_map('strval', $item_tag_ids);

                // Get matching tag ids
                $matching_tag_ids = array_intersect($item_tag_ids, $params['product_tags']);

                // Check if at least one tag id is matching
                if (empty($matching_tag_ids)) {
                    continue;
                }
            }

            // Shipping classes
            if (!empty($params['shipping_classes'])) {

                // Get item shipping class id
                $shipping_class_id = (string) RightPress_Help::get_wc_product_shipping_class_id($item['product_id'], $item['variation_id']);

                // Check if shipping class id is in list
                if (!in_array($shipping_class_id, $params['shipping_classes'], true)) {
                    continue;
                }
            }

            // If we ended up here, item matches all criteria
            $filtered[$item_key] = $item;
        }

        return $filtered;
    }

    /**
     * Get WooCommerce cart item count
     *
     * @access public
     * @param array $cart_items
     * @return int
     */
    public static function get_wc_cart_item_count($cart_items = null)
    {
        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Count cart items
        return count($cart_items);
    }

    /**
     * Get WooCommerce cart item by cart item key
     *
     * @access public
     * @param string $cart_item_key
     * @return array|bool
     */
    public static function get_wc_cart_item_by_key($cart_item_key)
    {
        // Get cart items
        $cart_items = RightPress_Help::get_wc_cart_items();

        // Check for cart item and return
        return isset($cart_items[$cart_item_key]) ? $cart_items[$cart_item_key] : false;
    }

    /**
     * Get WooCommerce cart product ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_ids($cart_items = null)
    {
        $products = array();

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Iterate over items and pick product ids
        if (is_array($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $cart_item) {

                // Reference product
                $product = $cart_item['data'];

                // Get product id
                $product_id = RightPress_Help::get_wc_product_absolute_id($product);

                // Add product id to array if it's not there yet
                if (!in_array($product_id, $products)) {
                    $products[] = $product_id;
                }
            }
        }

        // Return list of products
        return $products;
    }

    /**
     * Get WooCommerce cart product variation ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_variation_ids($cart_items = null)
    {
        $product_variations = array();

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Iterate over items and pick product ids
        if (is_array($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $cart_item) {

                // Get variation id from cart item
                $variation_id = RightPress_Help::get_wc_variation_id_from_cart_item($cart_item);

                // Add to array if it does not exist there yet
                if ($variation_id && !in_array($variation_id, $product_variations)) {
                    $product_variations[] = $variation_id;
                }
            }
        }

        // Return list of variations
        return $product_variations;
    }

    /**
     * Get WooCommerce cart product category ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_category_ids($cart_items = null)
    {
        // Get cart product ids
        $product_ids = RightPress_Help::get_wc_cart_product_ids($cart_items);

        // Get product category ids from product ids
        return RightPress_Help::get_wc_product_category_ids_from_product_ids($product_ids);
    }

    /**
     * Get WooCommerce cart product attribute ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_attribute_ids($cart_items = null)
    {
        $attributes = array();

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Iterate over cart items
        if (is_array($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $cart_item) {

                // Get attribute ids for current cart item
                if ($attribute_ids = RightPress_Help::get_wc_product_attribute_ids_from_cart_item($cart_item)) {
                    $attributes = array_merge($attributes, $attribute_ids);
                }
            }
        }

        // Return unique attributes
        return array_unique($attributes);
    }

    /**
     * Get WooCommerce cart product tag ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_tag_ids($cart_items = null)
    {
        // Get cart item product ids
        $product_ids = RightPress_Help::get_wc_cart_product_ids($cart_items);

        // Get product tag ids from product ids
        return RightPress_Help::get_wc_product_tag_ids_from_product_ids($product_ids);
    }

    /**
     * Get WooCommerce cart product shipping class ids
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function get_wc_cart_product_shipping_class_ids($cart_items = null)
    {

        $shipping_class_ids = array();

        // Get cart items
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Iterate over cart items
        if (is_array($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $cart_item) {

                // Get product id from cart item
                $product_id = RightPress_Help::get_wc_product_absolute_id($cart_item['data']);

                // Get variation id from cart item
                $variation_id = RightPress_Help::get_wc_variation_id_from_cart_item($cart_item);

                // Get shipping class if for current cart item
                if ($shipping_class_id = RightPress_Help::get_wc_product_shipping_class_id($product_id, $variation_id)) {

                    // Cast to string
                    $shipping_class_id = (string) $shipping_class_id;

                    // Add to array if it does not exist there yet
                    if (!in_array($shipping_class_id, $shipping_class_ids, true)) {
                        $shipping_class_ids[] = $shipping_class_id;
                    }
                }
            }
        }

        return $shipping_class_ids;
    }

    /**
     * Get WooCommerce order product ids
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_wc_order_product_ids($order_id)
    {
        $product_ids = array();

        // Load order object
        $order = wc_get_order($order_id);

        // Check if order was loaded
        if (!$order) {
            return $product_ids;
        }

        // Get order items
        $order_items = $order->get_items();

        // Iterate over order items and get product ids
        foreach ($order_items as $order_item) {

            $product_id = $order_item->get_product_id();

            if (!empty($product_id) && !in_array($product_id, $product_ids, true)) {
                $product_ids[] = $product_id;
            }
        }

        // Return product ids
        return $product_ids;
    }

    /**
     * Get WooCommerce order product variation ids
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_wc_order_product_variation_ids($order_id)
    {
        $variation_ids = array();

        // Load order object
        if ($order = wc_get_order($order_id)) {

            // Iterate over order items
            foreach ($order->get_items() as $order_item) {

                // Get variation id
                if ($variation_id = $order_item->get_variation_id()) {
                    $variation_ids[] = $variation_id;
                }
            }
        }

        // Return variation ids
        return array_unique($variation_ids);
    }

    /**
     * Get WooCommerce order product category ids
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_wc_order_product_category_ids($order_id)
    {
        // Get order product ids
        $product_ids = RightPress_Help::get_wc_order_product_ids($order_id);

        // Get product category ids from product ids
        return RightPress_Help::get_wc_product_category_ids_from_product_ids($product_ids);
    }

    /**
     * Get WooCommerce order product attribute ids
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_wc_order_product_attribute_ids($order_id)
    {
        $attribute_ids = array();

        // Load order object
        $order = wc_get_order($order_id);

        // Check if order was loaded
        if (!$order) {
            return $attribute_ids;
        }

        // Get order items
        $order_items = $order->get_items();

        // Iterate over order items and get attribute ids
        foreach ($order_items as $order_item) {

            // Get product id
            if ($product_id = $order_item->get_product_id()) {

                // Get selected attributes
                $selected = array();

                // Iterate over order item meta
                foreach ($order_item->get_meta_data() as $meta) {
                    if (RightPress_Help::string_begins_with_substring($meta->key, 'pa_')) {
                        $selected[$meta->key] = $meta->value;
                    }
                }

                // Get attribute ids
                if ($current_ids = RightPress_Help::get_wc_product_attribute_ids($product_id, $selected)) {
                    $attribute_ids = array_merge($attribute_ids, $current_ids);
                }
            }
        }

        // Return attribute ids
        return array_unique($attribute_ids);
    }

    /**
     * Get WooCommerce order product tag ids
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_wc_order_product_tag_ids($order_id)
    {
        // Get order product ids
        $product_ids = RightPress_Help::get_wc_order_product_ids($order_id);

        // Get product tag ids from product ids
        return RightPress_Help::get_wc_product_tag_ids_from_product_ids($product_ids);
    }

    /**
     * Get WooCommerce product category ids from product ids
     *
     * @access public
     * @param array $product_ids
     * @return array
     */
    public static function get_wc_product_category_ids_from_product_ids($product_ids)
    {
        $category_ids = array();

        // Iterate over product ids
        foreach ($product_ids as $product_id) {

            // Get product
            if ($product = wc_get_product($product_id)) {

                // Get category ids for current product
                if ($current_category_ids = $product->get_category_ids()) {

                    // Merge with main array
                    $category_ids = array_merge($category_ids, $current_category_ids);
                }
            }
        }

        return $category_ids;
    }

    /**
     * Get WooCommerce product tag ids from product ids
     *
     * @access public
     * @param array $product_ids
     * @return array
     */
    public static function get_wc_product_tag_ids_from_product_ids($product_ids)
    {
        $tag_ids = array();

        // Iterate over product ids
        foreach($product_ids as $product_id) {

            // Get tag ids
            if ($current_ids = RightPress_Help::get_wc_product_tag_ids($product_id)) {
                $tag_ids = array_merge($tag_ids, $current_ids);
            }
        }

        return array_unique($tag_ids);
    }

    /**
     * Get WooCommerce last user paid order id
     *
     * Searches by both $customer_id and $billing_email separately if both are provided
     * to get orders that were made by the same customer while not being logged in
     *
     * @access public
     * @param int $customer_id
     * @param string $billing_email
     * @return mixed
     */
    public static function get_wc_last_user_paid_order_id($customer_id = null, $billing_email = null)
    {
        $order_ids = array();

        // At least one argument must be set
        if (!isset($customer_id) && !isset($billing_email)) {
            return false;
        }

        // Build query
        $config = array(
            'status'    => RightPress_Help::get_wc_order_is_paid_statuses(),
            'return'    => 'ids',
            'limit'     => 1,
        );

        // Get last paid order id by customer id
        if (isset($customer_id)) {

            // Search orders
            $current_order_ids = wc_get_orders(array_merge($config, array('customer' => $customer_id)));

            // Add to main array
            if (is_array($current_order_ids)) {
                $order_ids = array_merge($order_ids, $current_order_ids);
            }
        }

        // Get last paid order id by billing email
        if (isset($billing_email)) {

            // Search orders
            $current_order_ids = wc_get_orders(array_merge($config, array('billing_email' => $billing_email)));

            // Add to main array
            if (is_array($current_order_ids)) {
                $order_ids = array_merge($order_ids, $current_order_ids);
            }
        }

        // Return last paid order id or false if no paid orders were found
        return !empty($order_ids) ? max($order_ids) : false;
    }

    /**
     * Get WooCommerce order
     *
     * @access public
     * @param int $order_id
     * @return object
     */
    public static function wc_get_order($order_id)
    {
        return wc_get_order($order_id);
    }

    /**
     * Get WooCommerce product
     *
     * @access public
     * @param int $product_id
     * @return object
     */
    public static function wc_get_product($product_id)
    {
        return wc_get_product($product_id);
    }

    /**
     * Get WooCommerce customer
     *
     * @access public
     * @param int $customer_id
     * @return mixed
     */
    public static function wc_get_customer($customer_id)
    {
        $customer = new WC_Customer($customer_id);
        return $customer->get_id() ? $customer : false;
    }

    /**
     * Get WooCommerce order item
     *
     * @access public
     * @param int $order_item_id
     * @return mixed
     */
    public static function wc_get_order_item($order_item_id)
    {
        try {
            $order_item = new WC_Order_Item_Product($order_item_id);
            return $order_item->get_id() ? $order_item : false;
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Adds empty option to select field options
     *
     * @access pubic
     * @param array $options
     * @return array
     */
    public static function add_empty_field_option($options = array())
    {
        return array('' => '') + $options;
    }

    /**
     * Get WooCommerce tax class list
     *
     * @access public
     * @param array $prepend
     * @param array $append
     * @return array
     */
    public static function get_wc_tax_class_list($prepend = array(), $append = array())
    {
        $tax_classes = array();

        // Check if tax calculation is enabled on this store
        if (wc_tax_enabled()) {

            // Add Standard class
            $tax_classes['standard'] = __('Standard Rate', 'woocommerce');

            // Iterate over tax class names
            foreach (WC_Tax::get_tax_classes() as $tax_class_name) {

                // Add tax class to list
                $tax_classes[sanitize_title($tax_class_name)] = esc_html($tax_class_name);
            }
        }

        // Add custom tax classes and return
        return array_merge($prepend, $tax_classes, $append);
    }

    /**
     * Get hour list in one day (from 00:00 to 23:00)
     *
     * @access public
     * @return array
     */
    public static function get_hour_list()
    {
        $list = array();

        $format = wc_time_format();
        $dt = new DateTime;

        for ($i = 0; $i < 24; $i++) {
            $dt->setTime($i, 0, 0);
            $list[$i] = $dt->format($format);
        }

        return $list;
    }

    /**
     * Merge arrays recursively combining child arrays that have numeric keys (array_merge_recursive does not support this)
     *
     * @access public
     * @param array $all_vars
     * @param array $var
     * @return array
     */
    public static function array_merge_recursive_for_indexed_lists($all_vars, $var)
    {
        foreach ($var as $key => $value) {

            // Key does not exist in main array yet
            if (!isset($all_vars[$key])) {
                $all_vars[$key] = $value;
            }
            // Value is array
            else if (is_array($value)) {
                $all_vars[$key] = RightPress_Help::array_merge_recursive_for_indexed_lists($all_vars[$key], $value);
            }
            // Finite numerically indexed list of values
            else if (is_int($key)) {
                $all_vars[] = $value;
            }
        }

        return $all_vars;
    }

    /**
     * Check if array has more than one dimension
     *
     * @access public
     * @param array $array
     * @return bool
     */
    public static function array_is_multidimensional($array)
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    /**
     * Stringify array keys
     *
     * Legacy method - must not be used as this hack no longer works in PHP 7.2
     *
     * @access public
     * @param array $array
     * @return array
     */
    public static function stringify_array_keys($array)
    {
        $result = new stdClass();

        foreach ($array as $key => $value) {
            $string_key = (string) $key;
            $result->$string_key = $value;
        }

        return (array) $result;
    }

    /**
     * Get weekdays
     *
     * Numeric indexes - 0 stands for Sunday
     * Sorted by "Week starts on" setting
     *
     * @access public
     * @param bool $localize
     * @return array
     */
    public static function get_weekdays($localize = true)
    {
        // Get localized names
        if ($localize) {

            global $wp_locale;

            $weekdays = array();

            for ($day_index = 0; $day_index <= 6; $day_index++) {
                $weekdays[$day_index] = $wp_locale->get_weekday($day_index);
            }
        }
        // Get English names
        else {
            $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        }

        // Get weekday sort order
        $sort_list = array_keys($weekdays);

        if ($first_day = RightPress_Help::get_start_of_week()) {
            $sort_list = array_merge(array_slice($sort_list, $first_day), array_slice($sort_list, 0, $first_day));
        }

        // Return reordered weekdays
        return array_replace(array_flip($sort_list), $weekdays);
    }

    /**
     * Get start of week
     *
     * Represents PHP date('w') where 0 is Sunday and 6 is Saturday
     *
     * @access public
     * @return int
     */
    public static function get_start_of_week()
    {
        return intval(get_option('start_of_week', 0));
    }

    /**
     * Get literal start of week
     *
     * Returns "sunday" to "saturday"
     *
     * @access public
     * @return string
     */
    public static function get_literal_start_of_week()
    {

        $weekdays = RightPress_Help::get_weekday_names();

        $start_of_week = RightPress_Help::get_start_of_week();

        return $weekdays[$start_of_week];
    }

    /**
     * Get list of weekday names starting from Sunday
     *
     * @access public
     * @return array
     */
    public static function get_weekday_names()
    {

        return array(
            0 => 'sunday',
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
        );
    }

    /**
     * Get WooCommerce order ids
     *
     * Optional parameters:
     *   date           object          Date object
     *   customer_id    int             Customer id
     *   billing_email  int             Billing email
     *   status         string|array    Order status(es)
     *
     * @access public
     * @param array $params
     * @return array
     */
    public static function get_wc_order_ids($params = array())
    {
        // Start building query
        $config = array(
            'return'    => 'ids',
            'limit'     => -1,
        );

        // Date
        if (!empty($params['date']) && is_object($params['date'])) {
            $config['date_created'] = '>=' . $params['date']->format('U');
        }

        // Customer id
        if (!empty($params['customer_id'])) {
            $config['customer_id'] = $params['customer_id'];
        }

        // Billing email
        if (!empty($params['billing_email'])) {
            $config['billing_email'] = $params['billing_email'];
        }

        // Order statuses
        if (!empty($params['status'])) {
            $config['status'] = array_map(function($status) {
                return RightPress_Help::clean_wc_status($status);
            }, (array) $params['status']);
        }

        // Get order ids
        $order_ids = wc_get_orders($config);

        // Return order ids
        return is_array($order_ids) ? $order_ids : array();
    }

    /**
     * Clean WooCommerce order status
     *
     * @access public
     * @param string $status
     * @return string
     */
    public static function clean_wc_status($status)
    {

        return RightPress_Help::unprefix_string($status, 'wc-');
    }

    /**
     * Get WooCommerce paid order statuses
     *
     * @access public
     * @param bool $include_prefix
     * @return array
     */
    public static function get_wc_order_is_paid_statuses($include_prefix = false)
    {
        $statuses = wc_get_is_paid_statuses();
        return $include_prefix ? preg_filter('/^/', 'wc-', $statuses) : $statuses;
    }

    /**
     * Get min float value
     *
     * @access public
     * @return float
     */
    public static function get_min_float_value()
    {
        $test_value = (float) 1;

        for ($i = 0; $i < 50; $i++) {

            // Get current value
            $current_value = (float) ('0.' . str_repeat('0', $i) . '1');

            // Check current value
            if ((float) (string) ($current_value + 1) > 1) {
                $value = $current_value;
            }
            else {
                break;
            }
        }

        return $value;
    }

    /**
     * Get WooCommerce product quantity step
     *
     * @access public
     * @param object $product
     * @return int|float
     */
    public static function get_wc_product_quantity_step($product)
    {
        return apply_filters('woocommerce_quantity_input_step', 1, $product);
    }

    /**
     * Check if WooCommerce product uses decimal quantities
     *
     * @access public
     * @param object $product
     * @param float|int $step
     * @return bool
     */
    public static function wc_product_uses_decimal_quantities($product, $step = null)
    {
        if ($step === null) {
            $step = RightPress_Help::get_wc_product_quantity_step($product);
        }

        return floor($step) != $step;
    }

    /**
     * Get WooCommerce product attribute ids from cart item
     *
     * @access public
     * @param array $cart_item
     * @return array
     */
    public static function get_wc_product_attribute_ids_from_cart_item($cart_item)
    {
        // Get selected variable product attributes
        $selected = (!empty($cart_item['variation'])) ? $cart_item['variation'] : array();

        // Get product attribute ids
        return RightPress_Help::get_wc_product_attribute_ids($cart_item['product_id'], $selected);
    }

    /**
     * Encode ajax response data to json
     *
     * Used for multiselect options
     *
     * @access public
     * @param mixed $data
     * @return string
     */
    public static function json_encode_multiselect_options($data)
    {
        if (RightPress_Help::php_version_gte('5.4')) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        else {
            return json_encode($data);
        }
    }

    /**
     * Extract WooCommerce variable product attributes from array
     * that may contain other data
     *
     * @access public
     * @param array $data
     * @return array
     */
    public static function extract_wc_product_attributes_from_array($data)
    {
        $attributes = array();

        foreach ($data as $key => $value) {
            if (RightPress_Help::string_contains_phrase($key, 'attribute_pa_')) {
                $attributes[str_replace('attribute_', '', $key)] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Attempt to get WooCommerce product variation id from a set of product attributes
     *
     * @access public
     * @param mixed $product
     * @param array $attributes
     * @return int|null|false
     */
    public static function get_wc_variation_id_from_attributes($product, $attributes)
    {
        // Load product object
        if (!is_a($product, 'WC_Product')) {
            $product = wc_get_product($product);
        }

        // Product unknown
        if (!is_a($product, 'WC_Product')) {
            return false;
        }

        // Product is not variable
        if ($product->get_type() !== 'variable') {
            return null;
        }

        // Prefix attributes
        $prefixed_attributes = array();

        foreach ($attributes as $key => $value) {
            $prefixed_attributes['attribute_' . $key] = $value;
        }

        // Find matching variation
        $data_store = WC_Data_Store::load('product');
        $variation_id = $data_store->find_matching_product_variation($product, $prefixed_attributes);

        return $variation_id ? (int) $variation_id : null;
    }

    /**
     * Inject or enqueue stylesheet depending on wether or not it's too late
     * to print them in the head section
     *
     * @access public
     * @param string $handle
     * @param string $url
     * @param string $version
     * @return void
     */
    public static function enqueue_or_inject_stylesheet($handle, $url, $version)
    {
        // Enqueue in a regular fashion
        if (!did_action('wp_print_styles')) {
            wp_enqueue_style($handle, $url, array(), $version);
        }
        // Inject via Javascript
        else {
            RightPress_Help::inject_stylesheet($handle, $url, $version);
        }
    }

    /**
     * Inject stylesheet into head section from within body
     *
     * @access public
     * @param string $handle
     * @param string $url
     * @param string $version
     * @return void
     */
    public static function inject_stylesheet($handle, $url, $version = null)
    {
        // Append version
        if ($version !== null) {
            $url .= '?ver=' . $version;
        }

        $script_id = $handle . '-injector';

        $script = "jQuery('<link>').appendTo('head').attr({type: 'text/css', rel: 'stylesheet', id: '{$handle}'}).attr('href', '{$url}');";
        echo '<script type="text/javascript" style="display: none;" id="' . $script_id . '"> if (!document.getElementById("' . $handle . '")) { ' . $script . ' } jQuery("#' . $script_id . '").remove(); </script>';
    }

    /**
     * Get posted ajax request data
     *
     * Throws exceptions on error
     *
     * @access public
     * @return array
     */
    public static function get_parsed_ajax_request_data()
    {

        // Check if any data was posted
        if (empty($_POST['data'])) {
            throw new Exception('No data received.');
        }

        // Parse request data
        parse_str(urldecode($_POST['data']), $parsed);

        // Return parsed request data
        return $parsed;
    }

    /**
     * Get product page Ajax request data
     *
     * Used in places where we load our own data via Ajax on product pages
     *
     * Throws exceptions on error
     *
     * @access public
     * @param array $custom_keys
     * @return array
     */
    public static function get_product_page_ajax_request_data($custom_keys = array())
    {

        $data = array();

        // Get parsed ajax request data
        $parsed = RightPress_Help::get_parsed_ajax_request_data();

        // Get quantity
        $data['quantity'] = !empty($parsed['quantity']) ? (int) $parsed['quantity'] : 1;

        // Get product id
        foreach (array('product_id', 'add-to-cart', 'rightpress_reference_product_id') as $key) {
            if (isset($parsed[$key]) && is_numeric($parsed[$key])) {
                $data['product_id'] = (int) $parsed[$key];
                break;
            }
        }

        // Check if product id is defined
        if (empty($data['product_id'])) {
            throw new Exception('Product is not defined.');
        }

        // Variation id is set
        if (isset($parsed['variation_id']) && is_numeric($parsed['variation_id'])) {
            $data['variation_id'] = (int) $parsed['variation_id'];
        }
        // Attempt to get variation id from attributes
        else {
            $attributes = RightPress_Help::extract_wc_product_attributes_from_array($parsed);
            $data['variation_id'] = RightPress_Help::get_wc_variation_id_from_attributes($data['product_id'], $attributes);
        }

        // Load variation if it's variable product
        if ($variation = wc_get_product($data['variation_id'])) {

            // Iterate over variation attributes
            foreach ($variation->get_attributes() as $key => $value) {

                // Prepend key
                $key = 'attribute_' . $key;

                // Get value passed with this request and set to data array
                if (isset($parsed[$key])) {
                    $data['variation_attributes'][$key] = $parsed[$key];
                }
            }
        }
        else {
            $data['variation_attributes'] = array();
        }

        // Extract custom keys
        foreach ($custom_keys as $custom_key) {
            $data[$custom_key] = isset($parsed[$custom_key]) ? $parsed[$custom_key] : null;
        }

        // Return request data
        return $data;
    }

    /**
     * Developers doing something wrong
     *
     * @access public
     * @param string $function
     * @param string $message
     * @param string $version
     * @return void
     */
    public static function doing_it_wrong($function, $message, $version)
    {
        $message .= ' Backtrace: ' . wp_debug_backtrace_summary();
        do_action('doing_it_wrong_run', $function, $message, $version);
        error_log(sprintf(__('%1$s was called incorrectly. %2$s. Since version %3$s.'), $function, $message, $version));
    }

    /**
     * Stable asort
     *
     * Adapted from https://github.com/vanderlee/PHP-stable-sort-functions/
     *
     * @access public
     * @param array $array
     * @param string $sort_flags
     * @return array
     */
    public static function stable_asort(&$array, $sort_flags = SORT_REGULAR)
    {
        $index = 0;

        foreach ($array as &$item) {
            $item = array($index++, $item);
        }

        $result = uasort($array, function($a, $b) use($sort_flags) {

            if ($a[1] == $b[1]) {
                return $a[0] - $b[0];
            }

            $set = array(-1 => $a[1], 1 => $b[1]);
            asort($set, $sort_flags);
            reset($set);
            return key($set);
        });

        foreach ($array as &$item) {
            $item = $item[1];
        }

        return $result;
    }

    /**
     * Stable uasort
     *
     * Adapted from https://github.com/vanderlee/PHP-stable-sort-functions/
     *
     * @access public
     * @param array $array
     * @param callable $value_compare_func
     * @param array $params
     * @return array
     */
    public static function stable_uasort(&$array, $value_compare_func, $params = array())
    {
        $index = 0;

        foreach ($array as &$item) {
            $item = array($index++, $item);
        }

        $result = @uasort($array, function($a, $b) use($value_compare_func, $params) {
            $result = call_user_func($value_compare_func, $a[1], $b[1], $params);
            return $result == 0 ? $a[0] - $b[0] : $result;
        });

        foreach ($array as &$item) {
            $item = $item[1];
        }

        return $result;
    }

    /**
     * Get WooCommerce main product id (i.e. parent id in case of product variation)
     *
     * @access public
     * @param object|int $product
     * @return int
     */
    public static function get_wc_product_absolute_id($product)
    {
        // Load product object
        if (!is_a($product, 'WC_Product')) {
            $product = wc_get_product($product);
        }

        // Return appropriate id
        return $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
    }

    /**
     * Normalize string decimal
     *
     * Safely changes '12.345,67' to '12345.67'
     *
     * @access public
     * @param string $number
     * @return string
     */
    public static function normalize_string_decimal($number)
    {
        return preg_replace('/\.(?=.*\.)/', '', (str_replace(',', '.', $number)));
    }

    /**
     * Get WooCommerce product variation id from cart item
     *
     * @access public
     * @param array $cart_item
     * @return int|null
     */
    public static function get_wc_variation_id_from_cart_item($cart_item)
    {
        return (!empty($cart_item['variation_id']) && $cart_item['variation_id'] != $cart_item['product_id']) ? $cart_item['variation_id'] : null;
    }

    /**
     * Get WooCommerce smallest price decimal
     *
     * @access public
     * @return float
     */
    public static function get_wc_smallest_price_decimal()
    {
        return (1 / pow(10, wc_get_price_decimals()));
    }

    /**
     * Check if value is whole number
     *
     * For floats this will only check till precision 0.00001 due to float comparison issues
     *
     * @access public
     * @param mixed $value
     * @return float
     */
    public static function is_whole_number($value)
    {

        // Value must be numeric
        if (is_numeric($value)) {

            // Value is integer
            if (is_int($value)) {

                // All integers are whole numbers
                return true;
            }

            // Value is float
            if (is_float($value)) {

                // It must not have a decimal part or it must be smaller than defined precision
                return (abs($value - round($value)) < 0.00001);
            }

            // Value is string
            if (is_string($value)) {

                // It must not have a non-zero decimal part
                return (floor($value) == $value);
            }
        }

        // Value did not pass validation
        return false;
    }

    /**
     * Sanitize numeric value
     *
     * @access public
     * @param mixed $value
     * @return mixed
     */
    public static function sanitize_numeric_value($value)
    {
        // Fix comma as decimal separator
        $sanitized = RightPress_Help::fix_decimal_separator($value);

        // Check if resulting value appears to be numeric
        return is_numeric($sanitized) ? $sanitized : false;
    }

    /**
     * Filter out bundle cart items
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public static function filter_out_bundle_cart_items($cart_items)
    {

        // Remove bundle items
        if (is_array($cart_items)) {
            foreach ($cart_items as $cart_item_key => $cart_item) {
                if (RightPress_Help::cart_item_is_bundle($cart_item)) {
                    unset($cart_items[$cart_item_key]);
                    break;
                }
            }
        }

        // Return filtered items
        return $cart_items;
    }

    /**
     * Cart item is product bundle
     *
     * @access public
     * @param array $cart_item
     * @return bool
     */
    public static function cart_item_is_bundle($cart_item)
    {

        // Flags to use
        // Note: developers need to return array('bundled_by') to reverse the operation
        // for the "official" Product Bundles plugin, issues #359 and #437
        $flags = apply_filters('rightpress_bundle_cart_item_filter_flags', array('bundled_items'));

        // Check if cart item is product bundle
        foreach ($flags as $flag) {
            if (isset($cart_item[$flag])) {
                return true;
            }
        }

        // Not bundle
        return false;
    }

    /**
     * WooCommerce product has childern (i.e. is of type that can have children)
     *
     * @access public
     * @param object|int $product
     * @return bool
     */
    public static function wc_product_has_children($product)
    {

        // Load product
        if (!is_a($product, 'WC_Product')) {
            $product = wc_get_product($product);
        }

        // Check if product has children
        return ($product->is_type('variable') || $product->is_type('grouped') || $product->get_children());
    }

    /**
     * Add early action
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @param int $priority_modifier
     * @return void
     */
    public static function add_early_action($hook, $callback, $accepted_args = 1, $priority_modifier = 0)
    {

        add_action($hook, $callback, RightPress_Help::get_early_hook_priority($hook, $callback, $priority_modifier), $accepted_args);
    }

    /**
     * Add late action
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @param int $priority_modifier
     * @return void
     */
    public static function add_late_action($hook, $callback, $accepted_args = 1, $priority_modifier = 0)
    {

        add_action($hook, $callback, RightPress_Help::get_late_hook_priority($hook, $callback, $priority_modifier), $accepted_args);
    }

    /**
     * Add early filter
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @param int $priority_modifier
     * @return void
     */
    public static function add_early_filter($hook, $callback, $accepted_args = 1, $priority_modifier = 0)
    {

        add_filter($hook, $callback, RightPress_Help::get_early_hook_priority($hook, $callback, $priority_modifier), $accepted_args);
    }

    /**
     * Add late filter
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @param int $priority_modifier
     * @return void
     */
    public static function add_late_filter($hook, $callback, $accepted_args = 1, $priority_modifier = 0)
    {

        add_filter($hook, $callback, RightPress_Help::get_late_hook_priority($hook, $callback, $priority_modifier), $accepted_args);
    }

    /**
     * Get early hook priority
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $priority_modifier
     * @return int
     */
    public static function get_early_hook_priority($hook, $callback, $priority_modifier = 0)
    {

        return (int) apply_filters('rightpress_early_hook_priority', (RightPress_Help::get_php_int_min() + $priority_modifier), $hook, $callback, $priority_modifier);
    }

    /**
     * Get late hook priority
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $priority_modifier
     * @return int
     */
    public static function get_late_hook_priority($hook, $callback, $priority_modifier = 0)
    {

        return (int) apply_filters('rightpress_late_hook_priority', (PHP_INT_MAX + $priority_modifier), $hook, $callback, $priority_modifier);
    }

    /**
     * Check if WordPress user exists by id
     *
     * @access public
     * @param int $user_id
     * @return bool
     */
    public static function wp_user_exists($user_id)
    {

        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user_id));

        return $count == 1;
    }

    /**
     * Format html list from values array
     *
     * @access public
     * @param array $values
     * @param bool $ordered
     * @param string $class
     * @return string
     */
    public static function array_to_html_list($values, $ordered = false, $class = null)
    {

        $html = '';

        if (!empty($values)) {

            // Format class attribute
            $class = $class !== null ? ('class="' . $class . '"') : '';

            // Open list
            $html .= $ordered ? "<ol $class>" : "<ul $class>";

            // Iterate over values
            foreach ($values as $value) {

                // Append value
                $html .= '<li>' . $value . '</li>';
            }

            // Close list
            $html .= $ordered ? '</ol>' : '</ul>';
        }

        return $html;
    }

    /**
     * Check if WooCommerce order has product
     *
     * @access public
     * @param WC_Order|int $order
     * @param WC_Product|int $product
     * @return bool
     */
    public static function wc_order_has_product($order, $product)
    {

        // Load order object
        if (!is_a($order, 'WC_Order')) {
            $order = wc_get_order($order);
        }

        // Invalid order
        if (!is_a($order, 'WC_Order')) {
            return false;
        }

        // Get product id
        $product_id = is_a($product, 'WC_Product') ? $product->get_id() : $product;

        // Iterate over order items
        foreach ($order->get_items() as $order_item) {

            // Order has product
            if ($product_id === $order_item->get_variation_id() || $product_id === $order_item->get_product_id()) {
                return true;
            }
        }

        // Order does not have product
        return false;
    }

    /**
     * Check if cart contains product
     *
     * @access public
     * @param WC_Product|int $product
     * @return bool
     */
    public static function wc_cart_contains_product($product)
    {

        // Get product id
        $product_id = is_a($product, 'WC_Product') ? $product->get_id() : $product;

        // Get cart items
        $cart_items = RightPress_Help::get_wc_cart_items();

        // Iterate over cart items
        foreach ($cart_items as $cart_item) {

            // Product found in cart
            if ($cart_item['variation_id'] === $product_id || $cart_item['product_id'] == $product_id) {
                return true;
            }
        }

        // Product not found in cart
        return false;
    }

    /**
     * Get min integer available on the system
     *
     * @access public
     * @return int
     */
    public static function get_php_int_min()
    {

        return RightPress_Help::php_version_gte('7.0') ? PHP_INT_MIN : (-PHP_INT_MAX + 10);
    }

    /**
     * Add prefix to string or list of strings
     *
     * @access public
     * @param string|array $value
     * @param string $prefix
     * @return string|array
     */
    public static function prefix($value, $prefix)
    {

        return preg_filter('/^/', $prefix, $value);
    }

    /**
     * Remove prefix from string or list of strings
     *
     * @access public
     * @param string|array $value
     * @param string $prefix
     * @return string|array
     */
    public static function unprefix($value, $prefix)
    {

        // Check if value is array
        $is_array = is_array($value);

        // Convert string value to array
        $value = (array) $value;

        // Iterate over value elements
        foreach ($value as $index => $string) {

            // Check if string contains prefix
            if (substr($string, 0, strlen($prefix)) === $prefix) {

                // Unprefix string
                $value[$index] = substr($string, strlen($prefix));
            }
        }

        // Maybe extract string value
        if (!$is_array) {
            $value = array_pop($value);
        }

        return $value;
    }

    /**
     * Remove prefix from string
     *
     * Legacy alias for unprefix()
     *
     * @access public
     * @param string $string
     * @param string $prefix
     * @return string
     */
    public static function unprefix_string($string, $prefix)
    {

        return RightPress_Help::unprefix($string, $prefix);
    }

    /**
     * Get WooCommerce session in a safe way
     *
     * @access public
     * @return object|false
     */
    public static function get_wc_session()
    {

        if (is_callable('WC') && isset(WC()->session) && is_object(WC()->session)) {
            return WC()->session;
        }

        return false;
    }





}
