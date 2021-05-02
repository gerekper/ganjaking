<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenTypes;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use DateTime;

final class Version332 implements LegacyCollectionDecoder {

	/**
	 * @var ListScreenTypes
	 */
	private $types;

	public function __construct( ListScreenTypes $types ) {
		$this->types = $types;
	}

	public function decode( array $data ) {
		$list_screens = new ListScreenCollection();

		foreach ( $data as $key => $columns ) {
			$list_screen = $this->types->get_list_screen_by_key( $key );

			if ( null === $list_screen ) {
				continue;
			}

			$id = sanitize_key( substr( md5( serialize( $columns ) . $key ), 0, 16 ) );

			$list_screen
				->set_layout_id( $id )
				->set_settings( $columns )
				->set_title( __( 'Original', 'codepress-admin-columns' ) )
				->set_updated( new DateTime() );

			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function can_decode( array $data ) {
		foreach ( $data as $key => $columns ) {
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