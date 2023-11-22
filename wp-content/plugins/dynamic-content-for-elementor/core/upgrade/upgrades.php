<?php

namespace DynamicContentForElementor\Core\Upgrade;

use DynamicContentForElementor\Extensions;
use Elementor\Core\Upgrade\Updater;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Upgrades
{
    public static function _v_2_10_1_fix_filter_php_error($updater)
    {
        $option = get_option('dce_tokens_filters_whitelist');
        if (\is_array($option)) {
            $option = \implode("\n", \array_keys($option));
            update_option('dce_tokens_filters_whitelist', $option);
        }
        return \false;
    }
    public static function _v_2_10_0_merge_used_filters($updater)
    {
        $option = get_option('dce_tokens_filters_whitelist');
        $wl = [];
        if (\is_string($option)) {
            $list = \explode("\n", $option);
            foreach ($list as $f) {
                $wl[\trim($f)] = \true;
            }
        }
        $used_filters = get_option('dce_tokens_used_filters', []);
        update_option('dce_tokens_filters_whitelist', $wl + $used_filters);
        return \false;
    }
    /**
     * @param Updater $updater
     * @return boolean
     */
    public static function _v_2_8_0_conditional_validation_join_lines($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_conditional_validation_join_lines'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /**
     * @param Updater $updater
     * @return boolean
     */
    public static function _v_2_6_1_views_change_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['dce_views_order_by_no_nulls' => 'no']]];
        return self::_update_widget_settings('dce-views', $updater, $changes);
    }
    /**
     * @param Updater $updater
     * @return boolean
     */
    public static function _v_2_5_0_dynamic_charts_background_repeater($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_charts_background_repeater'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamic-charts', $updater, $changes);
    }
    /**
     * Set old default value for Dynamic Google Maps in 'acfmap'
     */
    public static function _v_2_5_0_dynamic_google_maps_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['map_data_type' => 'acfmap']]];
        return self::_update_widget_settings('dyncontel-acf-google-maps', $updater, $changes);
    }
    /**
     * @param Updater $updater
     * @return boolean
     */
    public static function _v_2_5_0_disable_tokens_filters_whitelist($updater)
    {
        update_option('dce_tokens_filters_whitelist_status', 'disable');
        update_option('dce_active_tokens', ['form', 'system', 'date', 'author', 'user', 'post', 'term', 'option', 'wp_query', 'query', 'comment', 'acf', 'product', 'expr']);
        return \false;
    }
    /**
     * No new features by default, save current status.
     *
     * @param Updater $updater
     * @return boolean
     */
    public static function _v_2_5_0_no_new_features_default($updater)
    {
        $option = \json_decode(get_option('dce_features_status_option', '[]'), \true);
        $disable_new_features = \defined('DCE_DISABLE_NEW_FEATURES') && DCE_DISABLE_NEW_FEATURES;
        $default = \DynamicContentForElementor\Core\Upgrade\UpgradesData::FEATURES_STATUS_2_4_4;
        if ($disable_new_features) {
            $default = \array_map(function () {
                return 'inactive';
            }, $default);
        }
        $status = $option + $default;
        update_option('dce_features_status_option', wp_json_encode($status));
        return \false;
    }
    public static function _v_2_3_1_remote_content_cache_age($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_remote_content_cache_age'], 'control_ids' => []]];
        return self::_update_widget_settings('dyncontel-remotecontent', $updater, $changes);
    }
    public static function _v_2_3_1_rename_remote_content_headers($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_rename_widget_settings'], 'control_ids' => ['authorization_header' => 'headers']]];
        return self::_update_widget_settings('dyncontel-remotecontent', $updater, $changes);
    }
    /**
     * Remove HTML Tag from Text Editor with Tokens and move it on text setting
     */
    public static function _v_2_3_1_remove_tag_text_editor_tokens($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_tokens_remove_tag'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-tokens', $updater, $changes);
    }
    /**
     * Remove base64 encoding from option 'dce_license_domain'
     */
    public static function _v_2_3_0_remove_base64($updater)
    {
        $dce_license_domain = get_option('dce_license_domain');
        if ($dce_license_domain) {
            update_option('dce_license_domain', \base64_decode($dce_license_domain));
        }
        return \false;
    }
    public static function _v_2_3_0_wysiwyg_upgrade($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_wysiwyg_upgrade'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /** Remove controls from repeater in add to favorites */
    public static function _v_2_2_0_flatten_add_to_favorites_list($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_flatten_add_to_favorites_list'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-add-to-favorites', $updater, $changes);
    }
    /**
     * Smooth Transition should be inactive by default on 2.2.0. So we need to
     * save the database.
     */
    public static function _v_2_2_0_smooth_transition_default_inactive()
    {
        $option = \json_decode(get_option('dce_features_status_option', '[]'), \true);
        if (!isset($option['gst_smooth_transition'])) {
            $option['gst_smooth_transition'] = 'active';
            update_option('dce_features_status_option', wp_json_encode($option));
        }
    }
    public static function _flatten_add_to_favorites_list($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $controls = ['dce_favorite_key', 'dce_favorite_title_add', 'dce_favorite_icon_add', 'dce_favorite_title_remove', 'dce_favorite_icon_remove'];
        if (isset($element['settings']['dce_favorite_list']) && !empty($element['settings']['dce_favorite_list'])) {
            $old = $element['settings']['dce_favorite_list'][0] ?? [];
            foreach ($controls as $control) {
                if (isset($old[$control])) {
                    $element['settings'][$control] = $old[$control];
                    $args['do_update'] = \true;
                }
            }
        }
        return $element;
    }
    public static function get_new_extensions_status()
    {
        $extensions_old = \json_decode(get_option('dce_excluded_extensions', '[]'), \true);
        $extensions_old += \json_decode(get_option('dce_excluded_dynamic_tags', '[]'), \true);
        $extensions_new = [];
        foreach ($extensions_old as $old_name => $is_excluded) {
            if (!isset(\DynamicContentForElementor\Core\Upgrade\UpgradesData::EXTENSION_TRANSLATION[$old_name])) {
                continue;
            }
            $new_name = \DynamicContentForElementor\Core\Upgrade\UpgradesData::EXTENSION_TRANSLATION[$old_name];
            $extensions_new[$new_name] = $is_excluded ? 'inactive' : 'active';
        }
        return $extensions_new;
    }
    public static function get_new_widgets_status()
    {
        $widgets_old = \json_decode(get_option('dce_excluded_widgets', '[]'), \true);
        $widgets_new = [];
        foreach ($widgets_old as $old_name => $is_excluded) {
            if (!isset(\DynamicContentForElementor\Core\Upgrade\UpgradesData::WIDGET_TRANSLATION[$old_name])) {
                continue;
            }
            $new_name = \DynamicContentForElementor\Core\Upgrade\UpgradesData::WIDGET_TRANSLATION[$old_name];
            $widgets_new[$new_name] = $is_excluded ? 'inactive' : 'active';
        }
        // all widgets that are not set in the option were active by default (including legacy):
        $all_widgets = \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'widget']);
        $all_widgets = \array_map(function ($w) {
            return 'active';
        }, $all_widgets);
        return $widgets_new + $all_widgets;
    }
    public static function get_new_settings_status()
    {
        $settings_old = \json_decode(get_option('dce_excluded_page_settings', '[]'), \true);
        $settings_old += \json_decode(get_option('dce_excluded_global_settings', '[]'), \true);
        $settings_new = [];
        foreach ($settings_old as $old_name => $_) {
            if (!isset(\DynamicContentForElementor\Core\Upgrade\UpgradesData::SETTING_TRANSLATION[$old_name])) {
                continue;
            }
            $new_name = \DynamicContentForElementor\Core\Upgrade\UpgradesData::SETTING_TRANSLATION[$old_name];
            $settings_new[$new_name] = 'inactive';
        }
        return $settings_new;
    }
    /**
     * Merge all feature status options into one.
     */
    public static function _v_2_1_0_merge_feature_status_options()
    {
        $ext = self::get_new_extensions_status();
        $wdg = self::get_new_widgets_status();
        $set = self::get_new_settings_status();
        $features_manager = \DynamicContentForElementor\Plugin::instance()->features;
        $features_manager->db_update_features_status($ext + $wdg + $set);
    }
    /**
     * Move Dynamic Tags option from extensions option
     */
    public static function _v_2_0_0_move_dynamic_tags_option()
    {
        $excluded_extensions = \json_decode(get_option('dce_excluded_extensions'), \true);
        $excluded_dynamic_tags = [];
        if (isset($excluded_extensions['DCE_Extension_Template']) && \true === $excluded_extensions['DCE_Extension_Template']) {
            $excluded_dynamic_tags['DCE_Extension_Template'] = \true;
        }
        if (isset($excluded_extensions['DCE_Extension_Token']) && \true === $excluded_extensions['DCE_Extension_Token']) {
            $excluded_dynamic_tags['DCE_Extension_Token'] = \true;
        }
        if (!empty($excluded_dynamic_tags)) {
            update_option('dce_excluded_dynamic_tags', wp_json_encode($excluded_dynamic_tags));
        }
    }
    /**
     * Remove old options not used from v 1.14.0
     */
    public static function _v_2_0_0_remove_old_options()
    {
        delete_option('dce_template_disable');
        delete_option('WP-DCE-1_excluded_extensions');
        delete_option('WP-DCE-1_excluded_widgets');
        delete_option('WP-DCE-1_excluded_documents');
        delete_option('WP-DCE-1_excluded_globals');
        delete_option('WP-DCE-1_active_widgets');
        delete_option('WP-DCE-1_active_extensions');
        delete_option('WP-DCE-1_active_documents');
        delete_option('WP-DCE-1_active_globals');
        delete_option('WP-DCE-1_license_activated');
        delete_option('WP-DCE-1_license_domain');
        delete_option('WP-DCE-1_license_key');
        delete_option('WP-DCE-1_license_expiration');
        return \false;
    }
    /**
     * Move Custom Meta Fields tab on Dynamic Posts - Custom Meta Items
     */
    public static function _v_2_0_0_dynamic_posts_move_custom_meta_fields($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_move_custom_meta_fields'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _dynamic_posts_move_custom_meta_fields($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items']) && isset($element['settings']['custommeta_items'])) {
            $custommeta_items = $element['settings']['custommeta_items'];
            $custommeta_items_align = $element['settings']['custommeta_items_align'] ?? '';
            $count_custommeta = \count($custommeta_items);
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            $metaitems_found = \false;
            foreach ($old_items as $item) {
                if ('item_custommeta' === ($item['item_id'] ?? '')) {
                    if ($metaitems_found) {
                        continue;
                    }
                    $metaitems_found = \true;
                    foreach ($custommeta_items as $custommeta_item) {
                        $new_item = $custommeta_item;
                        if (empty($custommeta_item['item_align'])) {
                            $new_item['item_align'] = $custommeta_items_align;
                        } else {
                            $new_item['item_align'] = $custommeta_item['item_align'];
                        }
                        $new_item['item_id'] = 'item_custommeta';
                        $new_items[] = $new_item;
                    }
                } else {
                    $new_items[] = $item;
                }
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    /**
     * Change Open Link in New Window on Dynamic Posts Items
     */
    public static function _v_2_0_0_dynamic_posts_link_new_window($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_link_new_window'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _dynamic_posts_link_new_window($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items'])) {
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            foreach ($old_items as $item) {
                if (empty($item['open_target_blank'])) {
                    $item['open_target_blank'] = 'yes';
                }
                $new_items[] = $item;
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _tokens_remove_tag($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['dce_html_tag'])) {
            $tag = $element['settings']['dce_html_tag'];
            unset($element['settings']['dce_html_tag']);
            $element['settings']['text_w_tokens'] = '<' . $tag . '>' . $element['settings']['text_w_tokens'] . '<\\/' . $tag . '>';
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _rename_tooltip_control($element, $args)
    {
        if (!(($element['settings']['enable_tooltip'] ?? '') === 'yes')) {
            return $element;
        }
        $args['do_update'] = \true;
        $changes = ['enable_tooltip' => 'dce_enable_tooltip', 'tooltip_content' => 'dce_tooltip_content', 'tooltip_arrow' => 'dce_tooltip_arrow', 'tooltip_follow_cursor' => 'dce_tooltip_follow_cursor', 'tooltip_max_width' => 'dce_tooltip_max_width', 'tooltip_touch' => 'dce_tooltip_touch', 'tooltip_background_color' => 'dce_tooltip_background_color', 'tooltip_color' => 'dce_tooltip_color'];
        foreach ($changes as $old => $new) {
            if (isset($element['settings'][$old])) {
                $element['settings'][$new] = $element['settings'][$old];
                unset($element['settings'][$old]);
            }
        }
        return $element;
    }
    /** Rename because of conflict with other plugins */
    public static function _v_1_15_3_rename_tooltip_control($updater)
    {
        global $wpdb;
        $post_ids = $updater->query_col('SELECT `post_id`
					FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = "_elementor_data"
					AND `meta_value` LIKE \'%"enable_tooltip":"yes"%\';');
        if (empty($post_ids)) {
            return \false;
        }
        foreach ($post_ids as $post_id) {
            $do_update = \false;
            $document = \Elementor\Plugin::instance()->documents->get($post_id);
            if (!$document) {
                continue;
            }
            $data = $document->get_elements_data();
            if (empty($data)) {
                continue;
            }
            $args = ['do_update' => &$do_update];
            $callback = ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_rename_tooltip_control'];
            $data = \Elementor\Plugin::instance()->db->iterate_data($data, $callback, $args);
            if (!$do_update) {
                continue;
            }
            // We need the `wp_slash` in order to avoid the unslashing during the `update_metadata`
            $json_value = wp_slash(wp_json_encode($data));
            update_metadata('post', $post_id, '_elementor_data', $json_value);
        }
        return $updater->should_run_again($post_ids);
    }
    /**
     * Change _id to item_id on Dynamic Posts v2 Items and remove hidden items
     */
    public static function _v_1_15_0_dynamic_posts_v2_item_id($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_v2_items'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    /**
     * Set a new option called 'dce_template'
     */
    public static function _v_1_14_4_template_system_old_default()
    {
        $template_disable = get_option('dce_template_disable');
        if (!$template_disable) {
            update_option('dce_template', 'active');
        } else {
            // We used 0 before, change it to 'inactive'.
            update_option('dce_template', 'inactive');
        }
        return \false;
    }
    /**
     * split ACF Repeater widget in old and new version
     */
    public static function _v_1_14_0_split_acf_repeater($updater)
    {
        if (get_option('dce_acfrepeater_newversion', 'yes') !== 'yes') {
            return \false;
        }
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_change_widget_name'], 'data' => 'dce-acf-repeater-v2']];
        return self::_update_widget_settings('dyncontel-acf-repeater', $updater, $changes);
    }
    /**
     * We want to change setting "Results per page/Number of Posts" default values for Dynamic Posts v1 and v2
     */
    public static function _v_1_14_0_dynamic_posts_results_per_page_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['num_posts' => '-1']]];
        return self::_update_widget_settings('dyncontel-acfposts', $updater, $changes);
    }
    public static function _v_1_14_0_dynamic_posts_v2_results_per_page_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['num_posts' => '-1']]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _v_1_14_0_update_excluded_extensions_option($updater)
    {
        // Update on v2.1.0: this is the upgrade from
        // WP-DCE-1_excluded_extensions to dce_excluded_extension. It did the
        // right thing before, but the old functions have been removed, and so
        // now we do nothing. Thus resetting all extensions to active.
    }
    /**
     * Remove WP-DCE-1 from all options and set new options with "dce_" prefix
     */
    public static function _v_1_14_0_update_options($updater)
    {
        $excluded_widgets = \json_decode(get_option('WP-DCE-1_excluded_widgets'), \true);
        if ($excluded_widgets) {
            update_option('dce_excluded_widgets', wp_json_encode($excluded_widgets));
        }
        $excluded_documents = \json_decode(get_option('WP-DCE-1_excluded_documents'), \true);
        if ($excluded_documents) {
            update_option('dce_excluded_page_settings', wp_json_encode($excluded_documents));
        }
        $excluded_globals = \json_decode(get_option('WP-DCE-1_excluded_globals'), \true);
        // Set Option for Frontend Navigator
        if (isset($excluded_globals['DCE_Frontend_Navigator_Enable_Visitor'])) {
            update_option(DCE_FRONTEND_NAVIGATOR_OPTION, 'active-visitors');
        } elseif (!isset($excluded_globals['DCE_Frontend_Navigator'])) {
            update_option(DCE_FRONTEND_NAVIGATOR_OPTION, 'active');
        } else {
            update_option(DCE_FRONTEND_NAVIGATOR_OPTION, 'inactive');
        }
        if ($excluded_globals) {
            update_option('dce_excluded_global_settings', wp_json_encode($excluded_globals));
        }
        $license_activated = get_option('WP-DCE-1_license_activated');
        if ($license_activated) {
            update_option('dce_license_activated', $license_activated);
        }
        $license_domain = get_option('WP-DCE-1_license_domain');
        if ($license_domain) {
            update_option('dce_license_domain', $license_domain);
        }
        $license_key = get_option('WP-DCE-1_license_key');
        if ($license_key) {
            update_option('dce_license_key', $license_key);
        }
        $license_expiration = get_option('WP-DCE-1_license_expiration');
        if ($license_expiration) {
            update_option('dce_license_expiration', $license_expiration);
        }
    }
    public static function _v_1_12_4_remove_option_api_array($updater)
    {
        $dce_apis = get_option('WP-DCE-1_apis', []);
        if (isset($dce_apis['dce_api_gmaps'])) {
            update_option('dce_google_maps_api', $dce_apis['dce_api_gmaps']);
        }
        if (!empty($dce_apis['dce_api_gmaps_acf'])) {
            update_option('dce_google_maps_api_acf', 'yes');
        }
    }
    public static function _v_1_12_4_dce_tokens_html_tag_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['dce_html_tag' => 'span']]];
        return self::_update_widget_settings('dce-tokens', $updater, $changes);
    }
    /**
     * We want to change setting default values for the converters of the form
     * pdf action and pdf widget.
     */
    public static function _v_1_10_0_pdf_button_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['dce_pdf_button_converter' => 'dompdf']]];
        return self::_update_widget_settings('dce_pdf_button', $updater, $changes);
    }
    public static function _v_1_10_0_form_pdf_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_save_old_default_conv_form_pdf'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /** Form pdf SVG has now a repeater for multiple pages. */
    public static function _v_1_10_0_form_pdf_svg_repeater($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_pdf_form_new_svg_repeater'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /**
     * This is so we can distinguish old installations from new ones, old
     * installations use the old acfrepeater version.
     */
    public static function _v_1_11_0_acfrepeater_version_olddefault()
    {
        $version = get_option('dce_acfrepeater_newversion');
        if (!$version) {
            update_option('dce_acfrepeater_newversion', 'no');
        } else {
            // We used 1 before, change it to 'yes'.
            update_option('dce_acfrepeater_newversion', 'yes');
        }
        return \false;
    }
    public static function _remote_content_cache_age($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $ages_breakpoints = [60 * 3 => '1m', 60 * 10 => '5m', 60 * 30 => '15m', 60 * 60 * 3 => '1h', 60 * 60 * 9 => '6h', 60 * 60 * 18 => '12h', \PHP_INT_MAX => '24h'];
        if (($element['settings']['data_cache'] ?? '') === 'yes') {
            $args['do_update'] = \true;
            $age = $element['settings']['data_cache_maxage'] ?? 86400;
            if (!$age) {
                $element['settings']['data_cache'] = 'no';
            } else {
                foreach ($ages_breakpoints as $bp => $ref_age) {
                    if ($age < $bp) {
                        $element['settings']['cache_age'] = $ref_age;
                        break;
                    }
                }
            }
        }
        return $element;
    }
    /**
     * @param array<mixed> $element
     * @param array<mixed> $args
     * @return array<mixed>
     */
    public static function _conditional_validation_join_lines($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['dce_conditional_validations']) && \is_array($element['settings']['dce_conditional_validations'])) {
            foreach ($element['settings']['dce_conditional_validations'] as &$validation) {
                $old = $validation['expression'];
                $validation['expression'] = \preg_replace('/\\s/', ' ', $validation['expression']);
                if ($old !== $validation['expression']) {
                    $args['do_update'] = \true;
                }
            }
        }
        return $element;
    }
    public static function _wysiwyg_upgrade($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['form_fields']) && \is_array($element['settings']['form_fields'])) {
            $fields = $element['settings']['form_fields'];
            foreach ($fields as $i => $field) {
                if (($field['field_type'] ?? '') === 'textarea' && ($field['field_wysiwyg'] ?? '') === 'true') {
                    $element['settings']['form_fields'][$i]['field_type'] = 'dce_wysiwyg';
                    unset($element['settings']['form_fields'][$i]['field_wysiwyg']);
                    $args['do_update'] = \true;
                }
            }
        }
        return $element;
    }
    public static function _change_widget_name($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $element['widgetType'] = $args['data'];
        $args['do_update'] = \true;
        return $element;
    }
    public static function _dynamic_posts_v2_items($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items'])) {
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            foreach ($old_items as $item) {
                if (!isset($item['show_item']) || $item['show_item'] === 'check') {
                    $item['item_id'] = $item['_id'];
                    unset($item['_id']);
                    $new_items[] = $item;
                }
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    /**
     * @param array<string,mixed> $element
     * @param array<string,mixed> $args
     * @return array<string,mixed>
     */
    public static function _dynamic_charts_background_repeater($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $code = $element['settings']['background_data'] ?? '#E52600';
        $repeater = [['_id' => wp_unique_id(), 'color' => $code]];
        $element['settings']['background_data'] = $repeater;
        $args['do_update'] = \true;
        return $element;
    }
    public static function _pdf_form_new_svg_repeater($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['dce_form_pdf_svg_code'])) {
            $code = $element['settings']['dce_form_pdf_svg_code'];
            unset($element['settings']['dce_form_pdf_svg_code']);
            $repeater = [['_id' => wp_unique_id(), 'text' => $code]];
            $element['settings']['dce_form_pdf_svg_code_repeater'] = $repeater;
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _save_old_default_conv_form_pdf($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        // if the pdf action was registered in the form:
        if (isset($element['settings']['submit_actions']) && \in_array('dce_form_pdf', $element['settings']['submit_actions'])) {
            if (empty($element['settings']['dce_form_pdf_converter'])) {
                $element['settings']['dce_form_pdf_converter'] = 'dompdf';
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
    /**
     * $changes is an array of arrays in the following format:
     * [
     *   'control_ids' => array of control ids
     *   'callback' => user callback to manipulate the control_ids
     * ]
     *
     * @param       $widget_id
     * @param       $updater
     * @param array $changes
     *
     * @return bool
     */
    public static function _update_widget_settings($widget_id, $updater, $changes)
    {
        global $wpdb;
        $post_ids = $updater->query_col('SELECT `post_id`
					FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = "_elementor_data"
					AND `meta_value` LIKE \'%"widgetType":"' . $widget_id . '"%\';');
        if (empty($post_ids)) {
            return \false;
        }
        foreach ($post_ids as $post_id) {
            $do_update = \false;
            $document = \Elementor\Plugin::instance()->documents->get($post_id);
            if (!$document) {
                continue;
            }
            $data = $document->get_elements_data();
            if (empty($data)) {
                continue;
            }
            // loop thru callbacks & array
            foreach ($changes as $change) {
                $args = ['do_update' => &$do_update, 'widget_id' => $widget_id];
                if (isset($change['control_ids'])) {
                    $args['control_ids'] = $change['control_ids'];
                }
                if (isset($change['data'])) {
                    $args['data'] = $change['data'];
                }
                if (isset($change['prefix'])) {
                    $args['prefix'] = $change['prefix'];
                    $args['new_id'] = $change['new_id'];
                }
                $data = \Elementor\Plugin::instance()->db->iterate_data($data, $change['callback'], $args);
                if (!$do_update) {
                    continue;
                }
                // We need the `wp_slash` in order to avoid the unslashing during the `update_metadata`
                $json_value = wp_slash(wp_json_encode($data));
                update_metadata('post', $post_id, '_elementor_data', $json_value);
            }
        }
        // End foreach().
        return $updater->should_run_again($post_ids);
    }
    /**
     * @param $element
     * @param $args
     *
     * @return mixed
     */
    public static function _rename_widget_settings($element, $args)
    {
        $widget_id = $args['widget_id'];
        $changes = $args['control_ids'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        foreach ($changes as $old => $new) {
            if (!empty($element['settings'][$old]) && !isset($element['settings'][$new])) {
                $element['settings'][$new] = $element['settings'][$old];
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
    /**
     * Useful when we want to change a setting default value: Finds all the
     * instances where the setting value is unset and set it to the old
     * default value.
     */
    public static function _widget_settings_save_old_default($element, $args)
    {
        $widget_id = $args['widget_id'];
        $changes = $args['control_ids'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        foreach ($changes as $setting_name => $old_default) {
            if (empty($element['settings'][$setting_name])) {
                $element['settings'][$setting_name] = $old_default;
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
}
