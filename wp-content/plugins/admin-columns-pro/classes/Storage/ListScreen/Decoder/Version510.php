<?php

namespace ACP\Storage\ListScreen\Decoder;

use AC\ListScreenTypes;
use ACP\Exception\UndecodableListScreenException;
use ACP\Storage\ListScreen\Decoder;
use DateTime;

final class Version510 implements Decoder {

	/**
	 * @var ListScreenTypes
	 */
	private $list_screen_types;

	public function __construct( ListScreenTypes $list_screen_types ) {
		$this->list_screen_types = $list_screen_types;
	}

	public function decode( array $encoded_list_screen ) {
		if ( ! $this->can_decode( $encoded_list_screen ) ) {
			throw new UndecodableListScreenException( $encoded_list_screen );
		}

		$list_screen = $this->list_screen_types->get_list_screen_by_key( $encoded_list_screen['type'] );

		if ( null === $list_screen ) {
			throw new UndecodableListScreenException( $encoded_list_screen );
		}

		$title = $encoded_list_screen['title'];

		if ( ! $title ) {
			$title = ucfirst( $list_screen->get_label() );
		}

		$list_screen->set_layout_id( $encoded_list_screen['id'] )
		            ->set_settings( $encoded_list_screen['columns'] )
		            ->set_preferences( $encoded_list_screen['settings'] )
		            ->set_title( $title )
		            ->set_updated( DateTime::createFromFormat( 'U', (int) $encoded_list_screen['updated'] ) );

		return $list_screen;
	}

	public function can_decode( array $encoded_list_screen ) {
		if ( ! isset( $encoded_list_screen['type'], $encoded_list_screen['version'] ) ) {
			return false;
		}

		$list_screen = $this->list_screen_types->get_list_screen_by_key( $encoded_list_screen['type'] );

		if ( null === $list_screen ) {
			return false;
		}

		if ( ! version_compare( $encoded_list_screen['version'], '5.1.0', '>=' ) ) {
			return false;
		}

		return true;
	}

}