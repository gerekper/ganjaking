<?php

namespace ACP;

use AC\Registrable;

class Localize implements Registrable {

	const TEXTDOMAIN = 'codepress-admin-columns';

	/**
	 * @var string
	 */
	private $plugin_dir;

	public function __construct( $plugin_dir ) {
		$this->plugin_dir = $plugin_dir;
	}

	public function register() {
		add_action( 'init', [ $this, 'localize' ] );
	}

	public function localize() {
		// prevent the loading of existing translations within the 'wp-content/languages' folder.
		unload_textdomain( self::TEXTDOMAIN );

		$local = $this->get_local();

		$this->load_textdomain( $this->plugin_dir . 'admin-columns/languages', $local );
		$this->load_textdomain( $this->plugin_dir . 'languages', $local );
	}

	/**
	 * @return string
	 */
	private function get_local() {
		$local = function_exists( 'determine_locale' )
			? determine_locale()
			: get_user_locale();

		return (string) apply_filters( 'plugin_locale', $local, self::TEXTDOMAIN );
	}

	/**
	 * Do no use `load_plugin_textdomain()` because it could prevent
	 * pro languages from loading when core translation files are found.
	 *
	 * @param string $language_dir
	 * @param string $local
	 */
	private function load_textdomain( $language_dir, $local ) {
		$mofile = sprintf(
			'%s/%s-%s.mo',
			$language_dir,
			self::TEXTDOMAIN,
			$local
		);

		load_textdomain( self::TEXTDOMAIN, $mofile );
	}

}