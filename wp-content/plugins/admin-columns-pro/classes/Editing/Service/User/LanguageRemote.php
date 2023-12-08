<?php

namespace ACP\Editing\Service\User;

use AC;
use AC\Helper\Select\Options;
use ACP\Editing;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\View;

class LanguageRemote extends BasicStorage implements Editing\RemoteOptions {

	public function __construct() {
		parent::__construct( new Editing\Storage\User\Meta( 'locale' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\RemoteSelect();
	}

	public function get_remote_options( int $id = null ): Options {
		$translations = ( new AC\Helper\User() )->get_translations_remote();

		$options = [
			'' => _x( 'Site Default', 'default site language' ),
		];

		foreach ( get_available_languages() as $language ) {
			if ( isset( $translations[ $language ] ) ) {
				$options[ $language ] = $translations[ $language ]['native_name'];
			}
		}

		return Options::create_from_array( $options );
	}

}