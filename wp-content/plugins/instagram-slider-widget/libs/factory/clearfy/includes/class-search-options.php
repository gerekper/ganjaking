<?php

namespace WBCR\Factory_Clearfy_228;

/**
 * Class Search options
 *
 * Allows you to collect all the options from the plugin pages, is a registry of options.
 *
 * @author Alex Kovlaev <alex.kovalevv@gmail.com>, https://github.com/alexkovalevv
 *
 * @since 2.2.0
 */
class Search_Options {

	private static $_all_options;

	/**
	 * Registers page options in the options registry
	 *
	 * This will allow the user to search all the plugin options.
	 *
	 * @param array $options
	 * @param string $page_url
	 * @param string $page_id
	 * @since 2.2.0
	 *
	 */
	public static function register_options($options, $page_url, $page_id)
	{
		if( empty($options) || !is_array($options) ) {
			return;
		}

		$extracted_options = static::recursive_extraxt_options($options);

		if( !empty($extracted_options) ) {
			foreach((array)$extracted_options as $option) {
				if( 'div' === $option['type'] || 'html' === $option['type'] || !isset($option['title']) ) {
					continue;
				}

				$formated_option['title'] = $option['title'];

				/*if( isset($option['hint']) ) {
					$formated_option['hint'] = $option['hint'];
				}*/

				$formated_option['page_url'] = $page_url . '#factory-control-' . $option['name'];
				$formated_option['page_id'] = $page_id;

				static::$_all_options[] = $formated_option;
			}
		}
	}

	/**
	 * Extracted options from a nested array
	 * @param array $options
	 * @return array
	 * @since 2.2.0
	 */
	protected static function recursive_extraxt_options($options)
	{
		$extracted_options = [];

		foreach($options as $option) {
			if( isset($option['items']) ) {
				$extracted_options = array_merge($extracted_options, static::recursive_extraxt_options($option['items']));
			} else {
				$extracted_options[] = $option;
			}
		}

		return $extracted_options;
	}

	/**
	 * Get all plugin options
	 * @return mixed
	 * @since 2.2.0
	 */
	public static function get_all_options()
	{
		return static::$_all_options;
	}
}