<?php

namespace WPForms\SmartTags\SmartTag;

/**
 * Class PageUrl.
 *
 * @since 1.6.7
 */
class PageUrl extends SmartTag {

	/**
	 * Get smart tag value.
	 *
	 * @since 1.6.7
	 *
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	public function get_value( $form_data, $fields = [], $entry_id = '' ) {

		global $wp;

		return empty( $_POST['page_url'] ) ? home_url( add_query_arg( $_GET, $wp->request ) ) : esc_url_raw( wp_unslash( $_POST['page_url'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
}
