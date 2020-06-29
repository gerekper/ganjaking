<?php

namespace ACP\Editing;

/**
 * @since 4.0
 */
class Helper {

	/**
	 * Format options for posts selection
	 * Results are formatted as an array of post types, the key being the post type name, the value
	 * being an array with two keys: label (the post type label) and options, an array of options (posts)
	 * for this post type, with the post IDs as keys and the post titles as values
	 *
	 * @param array $args Additional query arguments for WP_Query
	 *
	 * @return array List of options, grouped by post type
	 * @deprecated 4.5
	 * @since      1.0
	 * @uses       WP_Query
	 */
	public function get_posts_list( $args ) {
		_deprecated_function( __METHOD__, '4.4' );

		return [];
	}

	/**
	 * Format list of options for users selection
	 * Results are formatted as an array of roles, the key being the role name, the value
	 * being an array with two keys: label (the role label) and options, an array of options (users)
	 * for this role, with the user IDs as keys and the user display names as values
	 *
	 * @param array $args User query args
	 *
	 * @return array Grouped users by role
	 * @deprecated 4.5
	 * @since      1.0
	 * @uses       WP_User_Query
	 */
	public function get_users_list( $args ) {
		_deprecated_function( __METHOD__, '4.4' );

		return [];
	}

	/**
	 * Format list of options for term selection
	 *
	 * @param array $args get_term args
	 *
	 * @return array Formatted Taxonomies
	 * @since      4.0
	 * @deprecated 4.5
	 */
	public function get_terms_list( $args ) {
		_deprecated_function( __METHOD__, '4.4' );

		return [];
	}

	/**
	 * Format list of options for comment selection
	 *
	 * @param array $args Comment query args
	 *
	 * @return array Formatted Comments
	 * @deprecated 4.5
	 * @since      4.0
	 * @uses       WP_User_Query
	 */
	public function get_comments_list( $args ) {
		_deprecated_function( __METHOD__, '4.4' );

		return [];
	}

}