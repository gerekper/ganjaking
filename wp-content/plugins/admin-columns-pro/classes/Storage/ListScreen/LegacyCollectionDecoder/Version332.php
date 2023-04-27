<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenFactoryInterface;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;

final class Version332 implements LegacyCollectionDecoder {

	private $list_screen_factory;

	public function __construct( ListScreenFactoryInterface $list_screen_factory ) {
		$this->list_screen_factory = $list_screen_factory;
	}

	public function decode( array $data ): ListScreenCollection {
		$list_screens = new ListScreenCollection();

		foreach ( $data as $list_key => $columns ) {
			if ( ! $this->list_screen_factory->can_create( (string) $list_key ) ) {
				continue;
			}

			$settings = [
				'title'   => __( 'Original', 'codepress-admin-columns' ),
				'list_id' => sanitize_key( substr( md5( serialize( $columns ) . $list_key ), 0, 16 ) ),
				'columns' => $columns,
			];

			$list_screens->add( $this->list_screen_factory->create( (string) $list_key, $settings ) );
		}

		return $list_screens;
	}

	public function can_decode( array $data ): bool {
		foreach ( $data as $columns ) {
			if ( ! is_array( $columns ) ) {
				return false;
			}

			foreach ( $columns as $column_settings ) {
				if ( ! is_array( $column_settings ) || ! array_key_exists( 'column-name', $column_settings ) ) {
					return false;
				}
			}
		}

		return true;
	}

}