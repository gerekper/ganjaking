<?php

namespace WPMailSMTP\Pro\Emails\Control;

use WPMailSMTP\Pro\Emails\Control\Admin\SettingsTab;

/**
 * Class Control.
 *
 * @since 1.5.0
 */
class Control {

	/**
	 * Control constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize the Control functionality.
	 *
	 * @since 1.5.0
	 */
	public function init() {

		// Add a new Email Controls tab under General.
		add_filter(
			'wp_mail_smtp_admin_get_pages',
			function ( $pages ) {

				$misc = $pages['misc'];
				unset( $pages['misc'] );

				$pages['control'] = new SettingsTab();
				$pages['misc']    = $misc;

				return $pages;
			},
			1
		);

		// Filter admin area options save process.
		add_filter( 'wp_mail_smtp_options_set', [ $this, 'filter_options_set' ] );

		new Switcher();
	}

	/**
	 * Sanitize admin area options.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options The options array.
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) {

		if ( isset( $options['control'] ) ) {
			foreach ( $options['control'] as $key => $value ) {
				$options['control'][ $key ] = (bool) $value;
			}
		} else {
			$controls = $this->get_controls( true );

			// All emails are on by default (not disabled).
			foreach ( $controls as $control ) {
				$options['control'][ $control ] = false;
			}
		}

		return $options;
	}

	/**
	 * Get the list of all available emails that we can manage.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $keys_only Whether to return the list of emails keys only (no sections/descriptions).
	 *
	 * @return array
	 */
	public function get_controls( $keys_only = false ) {

		$data = SettingsTab::get_controls();

		if ( $keys_only === true ) {
			// Create an array of arrays per each section of all the keys.
			$update_data = array_map(
				function ( $leaf ) {
					return array_keys( $leaf );
				},
				array_column( $data, 'emails' )
			);

			// Unpack to flatten it the array.
			$data = array_merge( ...$update_data );
		}

		return apply_filters( 'wp_mail_smtp_pro_emails_control_get_controls', $data, $keys_only );
	}
}
