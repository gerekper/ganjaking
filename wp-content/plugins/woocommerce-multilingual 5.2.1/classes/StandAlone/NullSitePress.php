<?php

namespace WCML\StandAlone;

use WPML\Core\ISitePress;
use WPML_WP_API;

class NullSitePress implements ISitePress {

	/** @var WPML_WP_API */
	private $wp_api;

	/**
	 * @param bool   $refresh
	 * @param bool   $major_first
	 * @param string $order_by
	 * @return array
	 */
	public function get_active_languages( $refresh = false, $major_first = false, $order_by = 'english_name' ) {
		$code = $this->get_current_language();
		return [
			$code => [
				'id'             => 1,
				'code'           => $code,
				'major'          => 1,
				'active'         => 1,
				'default_locale' => get_locale(),
				'encode_url'     => 0,
				'tag'            => $code,
				'english_name'   => $code,
				'native_name'    => $code,
				'display_name'   => $code,
			],
		];
	}

	/** @return bool|mixed|null|string */
	public function get_admin_language() {
		return $this->get_current_language();
	}

	/** @return string */
	public function get_current_language() {
		return preg_replace( '/_.+/', '', get_locale() );
	}

	/**
	 * @param null|string $code
	 * @param bool|string $cookie_lang
	 */
	public function switch_lang( $code = null, $cookie_lang = false ) {

	}

	/** @return string|false */
	public function get_default_language() {
		return $this->get_current_language();
	}

	/**
	 * @param int         $trid
	 * @param string|null $el_type Use comment, post, page, {custom post time name}, nav_menu, nav_menu_item, category, post_tag, etc. (prefixed with 'post_', 'tax_', or nothing for 'comment').
	 * @param bool|null   $skip_empty
	 * @param bool|null   $all_statuses
	 * @param bool|null   $skip_cache
	 * @param bool|null   $skip_recursions
	 * @param bool|null   $skipPrivilegeChecking
	 *
	 * @return array<string,\stdClass>
	 */
	public function get_element_translations(
		$trid,
		$el_type = 'post_post',
		$skip_empty = false,
		$all_statuses = false,
		$skip_cache = false,
		$skip_recursions = false,
		$skipPrivilegeChecking = false
	 ) {
		 return [];
	}

	/**
	 * @param string $code
	 * @return string
	 */
	public function get_flag_url( $code ) {
		return '';
	}

	/**
	 * Find language of document based on given permalink
	 *
	 * @param string $url Local url in permalink form.
	 * @return string language code
	 */
	public function get_language_from_url( $url ) {
		return $this->get_current_language();
	}

	/**
	 * Filter to add language field to WordPress search form
	 *
	 * @param string $form HTML code of search for before filtering.
	 *
	 * @return string HTML code of search form
	 */
	public function get_search_form_filter( $form ) {
		return $form;
	}

	/**
	 * @param string     $key
	 * @param mixed|bool $default
	 * @return bool|mixed
	 * @since 3.1
	 */
	public function get_setting( $key, $default = false ) {
		return $default;
	}

	/** @return array */
	public function get_settings() {
		return [];
	}

	/** @return \WPML_WP_API */
	public function get_wp_api() {
		$this->wp_api = $this->wp_api ? $this->wp_api : new WPML_WP_API();
		return $this->wp_api;
	}

	/**
	 * @param string|bool|null $lang
	 * @return bool
	 */
	public function is_rtl( $lang = false ) {
		return is_rtl();
	}

	/**
	 * @param int    $element_id   Use term_taxonomy_id for taxonomies, post_id for posts.
	 * @param string $element_type Use comment, post, page, {custom post time name}, nav_menu, nav_menu_item, category,
	 *                             post_tag, etc. (prefixed with 'post_', 'tax_', or nothing for 'comment').
	 *
	 * @return null|string
	 */
	public function get_language_for_element( $element_id, $element_type ) {
		return $this->get_current_language();
	}
}
