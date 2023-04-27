<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenFactoryInterface;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;

final class Version384 implements LegacyCollectionDecoder {

	private $list_screen_factory;

	public function __construct( ListScreenFactoryInterface $list_screen_factory ) {
		$this->list_screen_factory = $list_screen_factory;
	}

	public function decode( array $data ): ListScreenCollection {
		$list_screens = new ListScreenCollection();

		foreach ( $data as $list_key => $encoded_list_screens ) {

			foreach ( $encoded_list_screens as $encoded_list_screen ) {
				if ( ! $this->list_screen_factory->can_create( (string) $list_key ) ) {
					continue;
				}

				$settings = [
					'list_id' => sanitize_key( substr( md5( serialize( $encoded_list_screen ) . $list_key ), 0, 16 ) ),
					'columns' => $encoded_list_screen['columns'] ?? [],
				];

				$layout = $encoded_list_screen['layout'] ?? null;

				if ( $layout ) {
					if ( ! empty( $layout['name'] ) ) {
						$settings['title'] = (string) $layout['name'];
					}
					if ( ! empty( $layout['users'] ) && is_array( $layout['users'] ) ) {
						$settings['preferences']['users'] = array_map( 'intval', $layout['users'] );
					}
					if ( ! empty( $layout['roles'] ) && is_array( $layout['roles'] ) ) {
						$settings['preferences']['roles'] = array_map( 'strval', $layout['roles'] );
					}
				}

				$list_screens->add( $this->list_screen_factory->create( (string) $key, $settings ) );
			}
		}

		return $list_screens;
	}

	public function can_decode( array $data ): bool {
		foreach ( $data as $list_screens_data ) {
			if ( ! is_array( $list_screens_data ) ) {
				return false;
			}

			foreach ( $list_screens_data as $list_screen_data ) {
				if ( ! is_array( $list_screen_data ) || ! array_key_exists( 'columns', $list_screen_data ) ) {
					return false;
				}
			}
		}

		return true;
	}

}