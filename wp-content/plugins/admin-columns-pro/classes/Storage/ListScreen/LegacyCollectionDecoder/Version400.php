<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenTypes;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use DateTime;

final class Version400 implements LegacyCollectionDecoder {

	/**
	 * @var ListScreenTypes
	 */
	private $types;

	public function __construct( ListScreenTypes $types ) {
		$this->types = $types;
	}

	public function decode( array $data ) {
		$list_screens = new ListScreenCollection();

		foreach ( $data['list_screens'] as $encoded_list_screen ) {
			$list_screen = $this->types->get_list_screen_by_key( $encoded_list_screen['type'] );

			if ( null === $list_screen ) {
				continue;
			}

			$title = $encoded_list_screen['title'];

			if ( ! $title ) {
				$title = ucfirst( $list_screen->get_label() );
			}

			$list_screen
				->set_layout_id( $encoded_list_screen['id'] )
				->set_settings( $encoded_list_screen['columns'] )
				->set_preferences( $encoded_list_screen['settings'] )
				->set_title( $title )
				->set_updated( DateTime::createFromFormat( 'U', (int) $encoded_list_screen['date_modified'] ) );

			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	public function can_decode( array $data ) {
		if ( ! isset( $data['version'], $data['list_screens'] ) ) {
			return false;
		}

		return true;
	}

}