<?php

namespace DynamicContentForElementor;

use ElementorPro\Modules\Forms\Module as Forms_Module;
trait Elementor
{
    /**
     * Get Current Post ID
     *
     * @return int
     */
    public static function get_current_post_id()
    {
        if (isset(\Elementor\Plugin::instance()->documents)) {
            return \Elementor\Plugin::instance()->documents->get_current()->get_main_id();
        }
        return get_the_ID();
    }
    /**
     * Fetch an the elementor element.
     *
     * Fetch $element_id inside $post_id. Side effect: It also switches post
     * to $queried_id for the element dynamic settings.
     *
     * @copyright Elementor
     * @license GPLv3
     */
    public static function get_elementor_element_from_post_data($post_id, $element_id, $queried_id)
    {
        $elementor = \Elementor\Plugin::$instance;
        $elementor->db->switch_to_post($queried_id);
        $document = $elementor->documents->get($post_id);
        $element = null;
        $template_id = null;
        if ($document) {
            $element = self::find_element_recursive($document->get_elements_data(), $element_id);
        }
        if ($element === \false) {
            return \false;
        }
        if (!empty($element['templateID'])) {
            $template = $elementor->documents->get($element['templateID']);
            if (!$template) {
                return \false;
            }
            $template_id = $template->get_id();
            $element = $template->get_elements_data()[0];
        }
        $widget = $elementor->elements_manager->create_element_instance($element);
        $element['settings'] = $widget->get_settings_for_display();
        $element['settings']['id'] = $element_id;
        return $element;
    }
    public static $documents = [];
    /**
     * Get All Templates
     *
     * @param boolean $default
     * @return array<int|string,string>
     */
    public static function get_all_templates(bool $default = \false)
    {
        if ($default) {
            $templates[0] = __('Default', 'dynamic-content-for-elementor');
            $templates[1] = __('None', 'dynamic-content-for-elementor');
        } else {
            $templates[0] = __('None', 'dynamic-content-for-elementor');
        }
        $get_templates = self::get_templates();
        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {
                $templates[$template['template_id']] = $template['title'] . ' (' . $template['type'] . ')';
            }
        }
        return $templates;
    }
    public static function get_templates()
    {
        return \Elementor\Plugin::instance()->templates_manager->get_source('local')->get_items();
    }
    public static function get_element_by_id_with_post_id($element_id, $post_id)
    {
        $elementor_data = self::get_elementor_data($post_id);
        if ($elementor_data) {
            if ($element_id) {
                $element = self::array_find_deep_value($elementor_data, $element_id, 'id');
                if (isset($element['id'])) {
                    return $element;
                }
            }
        }
    }
    public static function get_element_by_id($element_id, $post_id = null)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id && isset($_GET['post'])) {
                $post_id = \intval($_GET['post']);
            }
            if (!$post_id && isset($_POST['post_id'])) {
                $post_id = \intval($_POST['post_id']);
            }
        }
        if ($post_id) {
            $element = self::get_element_by_id_with_post_id($element_id, $post_id);
            if ($element) {
                return $element;
            }
        }
        $ext_post_id = self::get_post_id_by_element_id($element_id);
        if ($ext_post_id) {
            $element = self::get_element_by_id_with_post_id($element_id, $ext_post_id);
            return $element;
        }
    }
    public static function get_post_id_by_element_data($data, $post_id = 0)
    {
        $element_id = \false;
        if (isset($data['id'])) {
            $element_id = $data['id'];
        } else {
            if (isset($data[0]['id'])) {
                $element_id = $data[0]['id'];
            } else {
                if (isset($data[0][0]['id'])) {
                    $element_id = $data[0][0]['id'];
                }
            }
        }
        if ($element_id) {
            if (is_singular() && !$post_id) {
                $post_id = get_the_id();
            }
            return self::get_post_id_by_element_id($element_id, $post_id);
        }
        return \false;
    }
    public static function get_post_id_by_element_id($element_id, $post_id = 0)
    {
        $ext_post_id = \false;
        if (isset(self::$documents[$element_id])) {
            $ext_post_id = self::$documents[$element_id];
        } else {
            // find element settings (because it may not be on post, but in a template)
            global $wpdb;
            $table = $wpdb->prefix . 'postmeta';
            $query = $wpdb->prepare("SELECT post_id FROM {$table} WHERE meta_key LIKE %s AND meta_value LIKE %s", '_elementor_data', '%"id":"' . $wpdb->esc_like($element_id) . '",%');
            if ($post_id) {
                $query .= $wpdb->prepare(' AND post_id = %d', $post_id);
            } else {
                $query .= " AND post_id IN (\n\t\t\t\t\tSELECT id FROM {$wpdb->prefix}posts\n\t\t\t\t\tWHERE post_status LIKE 'publish'\n\t\t\t\t)";
            }
            $results = $wpdb->get_results($query);
            if (!empty($results)) {
                $result = \reset($results);
                $ext_post_id = \reset($result);
                self::$documents[$element_id] = $ext_post_id;
            }
        }
        return $ext_post_id;
    }
    public static function get_elementor_element_by_id($element_id, $post_id = null)
    {
        if (!$post_id) {
            if ($element_id) {
                $post_id = self::get_post_id_by_element_id($element_id);
            }
            if (!$post_id) {
                $post_id = get_the_ID();
                if (!$post_id && isset($_GET['post'])) {
                    $post_id = \intval($_GET['post']);
                }
                if (!$post_id && isset($_POST['post_id'])) {
                    $post_id = \intval($_POST['post_id']);
                }
            }
        }
        if ($post_id) {
            $document = \Elementor\Plugin::$instance->documents->get($post_id);
            if ($document) {
                $element_raw = \DynamicContentForElementor\Helper::find_element_recursive($document->get_elements_data(), $element_id);
                if ($element_raw) {
                    $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance($element_raw);
                    return $element;
                } else {
                    return \false;
                }
            }
        }
        return \false;
    }
    public static function get_elementor_element_current()
    {
        $element = \DynamicContentForElementor\Elements::$elementor_current;
        if ($element) {
            return $element;
        }
        return \false;
    }
    public static function get_elementor_element_settings_by_id($element_id = null, $post_id = null)
    {
        $element = self::get_elementor_element_by_id($element_id, $post_id);
        if ($element) {
            $settings = $element->get_settings_for_display();
            return $settings;
        }
        return \false;
    }
    public static function get_settings_by_id($element_id = null, $post_id = null)
    {
        $element = self::get_element_by_id($element_id, $post_id);
        if ($element && !empty($element['settings'])) {
            return $element['settings'];
        }
        return \false;
    }
    public static function get_elementor_data($post_id)
    {
        if ($post_id && \is_numeric($post_id)) {
            $elementor_data = get_post_meta($post_id, '_elementor_data', \true);
            if ($elementor_data) {
                $post_meta = \json_decode($elementor_data, \true);
                return $post_meta;
            }
        }
        return \false;
    }
    public static function set_all_settings_by_id($element_id = null, $settings = array(), $post_id = null)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id) {
                $post_id = \intval($_GET['post']);
            }
        }
        $post_meta = self::get_settings_by_id(null, $post_id);
        if ($element_id) {
            $keys_array = self::array_find_deep($post_meta, $element_id);
            $tmp_key = \array_search('id', $keys_array);
            if ($tmp_key !== \false) {
                $keys_array[$tmp_key] = 'settings';
            }
            $post_meta = \DynamicContentForElementor\Helper::set_array_value_by_keys($post_meta, $keys_array, $settings);
            \array_walk_recursive($post_meta, function ($v, $k) {
                $v = self::escape_json_string($v);
            });
        }
        $post_meta_prepared = wp_json_encode($post_meta);
        $post_meta_prepared = wp_slash($post_meta_prepared);
        update_metadata('post', $post_id, '_elementor_data', $post_meta_prepared);
    }
    public static function set_settings_by_id($element_id, $key, $value = null, $post_id = null)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
            if (!$post_id) {
                $post_id = \intval($_GET['post']);
            }
        }
        $post_meta = self::get_elementor_data($post_id);
        $keys_array = self::array_find_deep($post_meta, $element_id);
        if (!empty($keys_array)) {
            $tmp_key = \array_search('id', $keys_array);
            if ($tmp_key !== \false) {
                \array_pop($keys_array);
                $keys_array[] = 'settings';
            }
            $keys_array[] = $key;
            $post_meta = \DynamicContentForElementor\Helper::set_array_value_by_keys($post_meta, $keys_array, $value);
            \array_walk_recursive($post_meta, function ($v, $k) {
                $v = self::escape_json_string($v);
            });
            $post_meta_prepared = wp_json_encode($post_meta);
            $post_meta_prepared = wp_slash($post_meta_prepared);
            update_metadata('post', $post_id, '_elementor_data', $post_meta_prepared);
        }
        return $post_id;
    }
    public static function set_dynamic_tag($editor_data)
    {
        if (\is_array($editor_data)) {
            foreach ($editor_data as $key => $avalue) {
                $editor_data[$key] = self::set_dynamic_tag($avalue);
            }
            if (isset($editor_data['elType'])) {
                foreach ($editor_data['settings'] as $skey => $avalue) {
                    $editor_data['settings'][\Elementor\Core\DynamicTags\Manager::DYNAMIC_SETTING_KEY][$skey] = 'token';
                }
            }
        }
        return $editor_data;
    }
    public static function recursive_array_search($needle, $haystack, $currentKey = '')
    {
        foreach ($haystack as $key => $value) {
            if (\is_array($value)) {
                $nextKey = self::recursive_array_search($needle, $value, \is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]');
                if ($nextKey) {
                    return $nextKey;
                }
            } elseif ($value == $needle) {
                return \is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]';
            }
        }
        return \false;
    }
    /**
     * @param array<mixed> $elements
     * @param string $element_id
     *
     * @return array<string,mixed>|false
     */
    public static function find_element_recursive(array $elements, string $element_id)
    {
        foreach ($elements as $element) {
            if ($element_id === $element['id']) {
                return $element;
            }
            if (!empty($element['elements'])) {
                $element = self::find_element_recursive($element['elements'], $element_id);
                if ($element) {
                    return $element;
                }
            }
        }
        return \false;
    }
    public static function array_find_deep($array, $search, $keys = array())
    {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                if (\is_array($value)) {
                    $sub = self::array_find_deep($value, $search, \array_merge($keys, array($key)));
                    if (\count($sub)) {
                        return $sub;
                    }
                } elseif ($value === $search) {
                    return \array_merge($keys, array($key));
                }
            }
        }
        return [];
    }
    public static function array_find_deep_value($array, $value, $key)
    {
        if (\is_array($array)) {
            foreach ($array as $akey => $avalue) {
                if (\is_array($avalue)) {
                    if (isset($avalue[$key]) && $value == $avalue[$key]) {
                        return $avalue;
                    }
                    $sub = self::array_find_deep_value($avalue, $value, $key);
                    if (!empty($sub)) {
                        return $sub;
                    }
                }
            }
        }
        return \false;
    }
    public static function get_the_id($datasource = \false, $fromparent = \false)
    {
        $id_page = get_the_ID();
        if ($datasource) {
            $id_page = $datasource;
        }
        if ($id_page && $fromparent) {
            $the_parent = wp_get_post_parent_id($id_page);
            if ($the_parent != 0) {
                $id_page = $the_parent;
            }
        }
        if (!$id_page) {
            global $wp;
            $current_url = home_url(add_query_arg(array(), $wp->request));
            $id_page = url_to_postid($current_url);
        }
        // Myself
        $type_page = get_post_type($id_page);
        $id_page = self::get_rev_ID($id_page, $type_page);
        // Demo
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            global $product;
            global $post;
            // BACKUP
            $original_post = $post;
            $original_product = $product;
            $demoPage = get_post_meta(get_the_ID(), 'demo_id', \true);
            // using get_the_id to retrieve Template ID
            if ($demoPage) {
                $id_page = $demoPage;
                $product = self::wooc_data($id_page);
                $post = get_post($id_page);
            }
            // RESET
            $post = $original_post;
            if ($type_page != 'product') {
                $product = $original_product;
            }
        }
        return $id_page;
    }
    public static function get_template_id_by_html($content = '')
    {
        $tmp = \explode('elementor elementor-', $content, 2);
        if (\count($tmp) > 1) {
            $tmp = \str_replace('"', ' ', \end($tmp));
            $tmp = \explode(' ', $tmp, 2);
            if (\count($tmp) > 1) {
                $tmp = \reset($tmp);
                return \intval($tmp);
            }
        }
        return \false;
    }
    public static function get_theme_builder_template_id($location = \false)
    {
        $template_id = 0;
        if (\DynamicContentForElementor\Helper::is_elementorpro_active()) {
            // check with Elementor Pro Theme Builder
            if (!$location) {
                if (is_singular()) {
                    $location = 'single';
                } else {
                    $location = 'archive';
                }
            }
            $document = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location($location);
            if (!empty($document)) {
                $document = \reset($document);
                $template_id = $document->get_main_id();
            }
        }
        return $template_id;
    }
    public static function user_can_elementor()
    {
        if (is_user_logged_in()) {
            if (is_super_admin()) {
                return \true;
            }
            if (is_singular()) {
                if (\Elementor\User::is_current_user_can_edit_post_type(get_post_type())) {
                    return \true;
                }
            } else {
                return \Elementor\User::is_current_user_can_edit_post_type('elementor_library');
            }
        }
        return \false;
    }
    public static function get_icon($icon, $attributes = [], $tag = 'i')
    {
        \ob_start();
        \Elementor\Icons_Manager::render_icon($icon, $attributes, $tag);
        $icon_html = \ob_get_clean();
        return $icon_html;
    }
    public static function get_elementor_elements($type = '')
    {
        global $wpdb;
        $sql_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}postmeta\n\t\t\tWHERE meta_key LIKE %s\n\t\t\tAND meta_value LIKE %s\n\t\t\tAND post_id IN (\n\t\t\t\tSELECT id FROM {$wpdb->prefix}posts\n\t\t\t\tWHERE post_status LIKE 'publish'\n\t\t\t)", '_elementor_data', '%"widgetType":"' . $wpdb->esc_like($type) . '"%');
        $results = $wpdb->get_results($sql_query);
        if (!\count($results)) {
            return \false;
        }
        $elements = array();
        foreach ($results as $result) {
            $post_id = $result->post_id;
            $elementor_data = $result->meta_value;
            $elements_tmp = self::get_elements_from_elementor_data($elementor_data, 'form');
            if (!empty($elements_tmp)) {
                foreach ($elements_tmp as $key => $value) {
                    $elements[$post_id][$key] = $value;
                }
            }
        }
        return $elements;
    }
    public static function get_elements_from_elementor_data($elementor_data, $type = '')
    {
        $elements = array();
        if (\is_string($elementor_data)) {
            $elementor_data = \json_decode($elementor_data);
        }
        if (!empty($elementor_data)) {
            foreach ($elementor_data as $element) {
                if ($type && $element->widgetType == $type) {
                    $elements[$element->id] = $element->settings;
                }
                if (!empty($element->elements)) {
                    $elements_tmp = self::get_elements_from_elementor_data($element->elements, $type);
                    if (!empty($elements_tmp)) {
                        foreach ($elements_tmp as $key => $value) {
                            $elements[$key] = $value;
                        }
                    }
                }
            }
        }
        return $elements;
    }
    // remove the elementor Template main wrappers (added by "print_elements_with_wrapper")
    public static function template_unwrap($html = '')
    {
        $pos = \strpos($html, 'elementor-section-wrap');
        if ($pos !== \false) {
            list($tmp, $html) = \explode('elementor-section-wrap', $html, 2);
            $tmp = \explode('<', $tmp);
            \array_pop($tmp);
            \array_pop($tmp);
            \array_pop($tmp);
            $pre = \implode('<', $tmp);
            list($tmp, $html) = \explode('>', $html, 2);
            $html = $pre . $html;
            for ($i = 0; $i < 3; $i++) {
                $pos = \strrpos($html, '</div>');
                if ($pos !== \false) {
                    $html = \substr_replace($html, '', $pos, \strlen('</div>'));
                }
            }
        }
        return $html;
    }
    public static function validate_html_tag($tag)
    {
        $allowed_tags = self::ALLOWED_HTML_WRAPPER_TAGS;
        return \in_array(\strtolower($tag), $allowed_tags, \true) ? $tag : 'div';
    }
    /**
     * Validate Post Types
     *
     * @param string|array|void $post_type
     * @return mixed
     */
    public static function validate_post_type($post_type)
    {
        $allowed_post_types = \DynamicContentForElementor\Helper::get_public_post_types();
        if (\is_string($post_type) && \array_key_exists($post_type, $allowed_post_types)) {
            return $post_type;
        } else {
            if (\is_array($post_type)) {
                $post_type = \array_filter($post_type, function ($type) use($allowed_post_types) {
                    return \array_key_exists($type, $allowed_post_types);
                });
                return $post_type;
            }
        }
        return '';
    }
    public static function get_active_devices_list()
    {
        return \Elementor\Plugin::$instance->breakpoints->get_active_devices_list(['reverse' => \true]);
    }
}
