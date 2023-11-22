<?php
// namespace ElementPack\Includes\Shortcodes;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Add a shortcode.
 *
 * @param array $data New shortcode data.
 * @param boolean $replace Replace existing shortcode or not.
 * @since  5.4.2
 */
function ep_add_shortcode($data, $replace = true) {
    return Element_Pack_Shortcodes::add($data, $replace);
}

/**
 * Remove a shortcode.
 *
 * @param string $id Shortcode ID to remove.
 * @since  5.4.2
 */
function ep_remove_shortcode($id) {
    return Element_Pack_Shortcodes::remove($id);
}

/**
 * Get all shortcodes.
 *
 * @return array The collection of available shortcodes.
 * @since  5.4.2
 */
function ep_get_all_shortcodes() {
    return Element_Pack_Shortcodes::get_all();
}

/**
 * Get specific shortcode by ID.
 *
 * @param string $id The ID (without prefix) of shortcode.
 * @return array|boolean   Shortcode data if found, False otherwise.
 * @since  5.4.2
 */
function ep_get_shortcode($id) {
    return Element_Pack_Shortcodes::get($id);
}

/**
 * Get shortcode default settings.
 *
 * @param string $id Shortcode ID.
 * @return array      Array with default settings.
 * @since 5.4.0
 */
function ep_get_shortcode_defaults($id) {

    $shortcode = ep_get_shortcode($id);
    $defaults  = array();

    if (!isset($shortcode['atts'])) {
        return $defaults;
    }

    foreach ($shortcode['atts'] as $key => $props) {
        $defaults[$key] = isset($props['default']) ? $props['default'] : '';
    }

    return $defaults;
}

/**
 * Parse shortcode attribute values.
 *
 * @param string $id Shortcode ID.
 * @param array $atts Input values.
 * @param array $extra Additional attributes.
 * @return array         Parsed values.
 * @since  5.4.0
 */
function ep_parse_shortcode_atts($id, $atts, $extra = array()) {

    return shortcode_atts(
        array_merge(ep_get_shortcode_defaults($id), $extra),
        $atts,
        $id
    );
}

/**
 * Custom do_shortcode function for nested shortcodes
 *
 * @param string $content Shortcode content.
 * @param string $pre First shortcode letter.
 * @return string          Formatted content.
 * @since  5.0.4
 */
function ep_do_nested_shortcodes_alt($content, $pre) {

    if (strpos($content, '[_') !== false) {
        $content = preg_replace('@(\[_*)_(' . $pre . '|/)@', '$1$2', $content);
    }

    return do_shortcode($content);
}

/**
 * Remove underscores from nested shortcodes.
 *
 * @param string $content String with nested shortcodes.
 * @param string $shortcode Shortcode tag name (without prefix).
 * @return string            Parsed string.
 * @since  5.0.4
 */
function ep_do_nested_shortcodes($content, $shortcode) {

    if (get_option('ep_option_do_nested_shortcodes_alt')) {
        return ep_do_nested_shortcodes_alt($content, substr($shortcode, 0, 1));
    }

    $prefix = ep_get_shortcode_prefix();

    if (strpos($content, '[_' . $prefix . $shortcode) !== false) {

        $content = str_replace(
            array('[_' . $prefix . $shortcode, '[_/' . $prefix . $shortcode),
            array('[' . $prefix . $shortcode, '[/' . $prefix . $shortcode),
            $content
        );

        return do_shortcode($content);
    }

    return do_shortcode(wptexturize($content));
}

/**
 * Get shortcode prefix.
 *
 * @return string Shortcode prefix.
 * @since  5.4.2
 */
function ep_get_shortcode_prefix() {
    return 'ep_';
}

/**
 * Do shortcodes in attributes.
 *
 * Replace braces with square brackets: {shortcode} => [shortcode], applies do_shortcode() filter.
 *
 * @param string $value Attribute value with shortcodes.
 * @return string        Parsed string.
 * @since  5.4.2
 */
function ep_do_attribute($value) {

    $value = str_replace(array('{', '}'), array('[', ']'), $value);
    $value = do_shortcode($value);

    return $value;
}

// new Shortcode_Helper();