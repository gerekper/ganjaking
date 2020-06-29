<?php

namespace ACP\Storage\ListScreen\LegacyCollectionDecoder;

use AC\ListScreenCollection;
use AC\ListScreenTypes;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use DateTime;

final class Version384 implements LegacyCollectionDecoder {

	/**
	 * @var ListScreenTypes
	 */
	private $types;

	public function __construct( ListScreenTypes $types ) {
		$this->types = $types;
	}

	/**
	 * @inheritDoc
	 */
	public function decode( array $data ) {
		$list_screens = new ListScreenCollection();

		foreach ( $data as $key => $encoded_list_screens ) {

			foreach ( $encoded_list_screens as $encoded_list_screen ) {

				$list_screen = $this->types->get_list_screen_by_key( $key );

				if ( null === $list_screen ) {
					continue;
				}

				$id = sanitize_key( substr( md5( serialize( $encoded_list_screen ) . $key ), 0, 16 ) );

				$list_screen
					->set_layout_id( $id )
					->set_settings( $encoded_list_screen['columns'] )
					->set_title( ucfirst( $list_screen->get_label() ) )
					->set_updated( new DateTime() );

				if ( isset( $encoded_list_screen['layout'] ) ) {

					$layout = $encoded_list_screen['layout'];

					if ( ! empty( $layout['name'] ) ) {
						$list_screen->set_title( $layout['name'] );
					}

					$settings = [];

					if ( ! empty( $layout['users'] ) && is_array( $layout['users'] ) ) {
						$settings['users'] = array_map( 'intval', $layout['users'] );
					}
					if ( ! empty( $layout['roles'] ) && is_array( $layout['roles'] ) ) {
						$settings['roles'] = array_map( 'strval', $layout['roles'] );
					}

					$list_screen->set_preferences( $settings );
				}

				$list_screens->add( $list_screen );
			}
		}

		return $list_screens;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function can_decode( array $data ) {
		foreach ( $data as $key => $list_screens_data ) {
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