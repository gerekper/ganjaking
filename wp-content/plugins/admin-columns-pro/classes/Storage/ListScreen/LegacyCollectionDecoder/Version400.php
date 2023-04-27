<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenFactoryInterface;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use DateTime;

final class Version400 implements LegacyCollectionDecoder {

	private $list_screen_factory;

	public function __construct( ListScreenFactoryInterface $list_screen_factory ) {
		$this->list_screen_factory = $list_screen_factory;
	}

	public function decode( array $data ): ListScreenCollection {
		$list_screens = new ListScreenCollection();

		foreach ( $data['list_screens'] as $encoded_list_screen ) {
			$list_key = (string) ( $encoded_list_screen['type'] ?? '' );

			if ( ! $this->list_screen_factory->can_create( $list_key ) ) {
				continue;
			}

			$settings = [
				'list_id'     => $encoded_list_screen['id'],
				'columns'     => $encoded_list_screen['columns'] ?? [],
				'preferences' => $encoded_list_screen['settings'] ?? [],
				'title'       => $encoded_list_screen['title'] ?? '',
				'date'        => DateTime::createFromFormat( 'U', (string) $encoded_list_screen['date_modified'] ),
			];

			$list_screens->add( $this->list_screen_factory->create( $list_key, $settings ) );
		}

		return $list_screens;
	}

	public function can_decode( array $data ): bool {
		if ( ! isset( $data['version'], $data['list_screens'] ) ) {
			return false;
		}

		return true;
	}

}