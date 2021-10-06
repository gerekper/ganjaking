<?php

namespace ACP\Editing\Service\User;

use AC;
use ACP\Editing;
use ACP\Editing\Service;
use ACP\Editing\View;

class LanguageRemote extends Service\BasicStorage implements Editing\RemoteOptions {

	public function __construct() {
		parent::__construct( new Editing\Storage\User\Meta( 'locale' ) );
	}

	public function get_view( $context ) {
		return new View\RemoteSelect();
	}

	public function get_remote_options( $id = null ) {
		$translations = ( new AC\Helper\User() )->get_translations_remote();

		$options = [
			'' => _x( 'Site Default', 'default site language' ),
		];

		foreach ( get_available_languages() as $language ) {
			if ( isset( $translations[ $language ] ) ) {
				$options[ $language ] = $translations[ $language ]['native_name'];
			}
		}

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}