<?php

namespace WPMailSMTP\Pro\Providers\Gmail\Api;

/**
 * Site ID class.
 *
 * @since 3.11.0
 */
class SiteId {

	/**
	 * Site ID option name.
	 *
	 * @since 3.11.0
	 */
	const SITE_ID_OPTION = 'wp_mail_smtp_gmail_one_click_setup_site_id';

	/**
	 * Get the site ID.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public function get() {

		$site_id = get_option( self::SITE_ID_OPTION );

		if ( $site_id ) {
			return $site_id;
		}

		$site_id = $this->create();

		update_option( self::SITE_ID_OPTION, $site_id );

		return $site_id;
	}

	/**
	 * Create a unique site ID.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	private function create() {

		$site_id = sprintf(
			'%s%s%s',
			defined( 'AUTH_KEY' ) ? AUTH_KEY : '',
			defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '',
			defined( 'LOGGED_IN_KEY' ) ? LOGGED_IN_KEY : ''
		);

		/**
		 * Strips any characters except those specifically allowed.
		 *
		 * `^` negates the matching (matches any character that is NOT listed in the set).
		 * `a-z` matches any lowercase characters between a-z.
		 * `A-Z` matches any uppercase characters between A-Z.
		 * `0-9` matches any digits.
		 */
		$site_id = preg_replace( '/[^a-zA-Z0-9]/', '', $site_id );

		return strlen( $site_id ) > 30 ? substr( $site_id, 0, 30 ) : $site_id;
	}
}
