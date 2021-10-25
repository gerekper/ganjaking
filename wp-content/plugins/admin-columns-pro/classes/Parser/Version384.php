<?php

namespace ACP\Parser;

use AC\ListScreenCollection;
use AC\ListScreenTypes;
use DateTime;

class Version384 implements Decode {

	const VERSION = '3.8.4';

	/**
	 * @param array $data
	 *
	 * @return ListScreenCollection
	 */
	public function decode( array $data ) {
		$list_screens = new ListScreenCollection();

		foreach ( $data as $key => $list_screens_data ) {

			foreach ( $list_screens_data as $data ) {

				$list_screen = ListScreenTypes::instance()->get_list_screen_by_key( $key );

				if ( null === $list_screen ) {
					continue;
				}

				$id = sanitize_key( substr( md5( serialize( $data ) . $key ), 0, 16 ) );

				$list_screen
					->set_layout_id( $id )
					->set_settings( $data['columns'] )
					->set_title( ucfirst( $list_screen->get_label() ) )
					->set_updated( new DateTime() );

				if ( isset( $data['layout'] ) ) {

					$layout = $data['layout'];

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

				$list_screens->push( $list_screen );
			}
		}

		return $list_screens;
	}

}