<?php

namespace WPForms\Lite\Admin\Pages;

/**
 * Addons page for Lite.
 *
 * @since 1.6.7
 */
class Addons {

	/**
	 * Page slug.
	 *
	 * @since 1.6.7
	 *
	 * @type string
	 */
	const SLUG = 'addons';

	/**
	 * Determine if current class is allowed to load.
	 *
	 * @since 1.6.7
	 *
	 * @return bool
	 */
	public function allow_load() {

		return wpforms_is_admin_page( self::SLUG );
	}

	/**
	 * Init.
	 *
	 * @since 1.6.7
	 */
	public function init() {

		if ( ! $this->allow_load() ) {
			return;
		}

		// Define hooks.
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.6.7
	 */
	public function hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
		add_action( 'wpforms_admin_page', [ $this, 'output' ] );
	}

	/**
	 * Add appropriate scripts to the Addons page.
	 *
	 * @since 1.6.7
	 */
	public function enqueues() {

		// JavaScript.
		wp_enqueue_script(
			'jquery-matchheight',
			WPFORMS_PLUGIN_URL . 'assets/js/jquery.matchHeight-min.js',
			[ 'jquery' ],
			'0.7.0'
		);

		wp_enqueue_script(
			'listjs',
			WPFORMS_PLUGIN_URL . 'assets/js/list.min.js',
			[ 'jquery' ],
			'1.5.0'
		);
	}

	/**
	 * Render the Addons page.
	 *
	 * @since 1.6.7
	 */
	public function output() {

		$addons = wpforms()->get( 'addons' )->get_all();

		if ( empty( $addons ) ) {
			return;
		}

		echo wpforms_render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'admin/addons',
			[
				'upgrade_link' => wpforms_admin_upgrade_link( 'addons' ),
				'addons'       => $addons,
			],
			true
		);
	}
}
