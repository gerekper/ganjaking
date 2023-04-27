<?php
declare( strict_types=1 );

namespace ACP\Storage\ListScreen\Decoder;

use AC\ListScreen;
use AC\ListScreenFactoryInterface;
use ACP\Exception\UndecodableListScreenException;
use ACP\Storage\ListScreen\Decoder;
use DateTime;

final class Version510 implements Decoder {

	private $list_screen_factory;

	public function __construct( ListScreenFactoryInterface $list_screen_factory ) {
		$this->list_screen_factory = $list_screen_factory;
	}

	public function decode( array $encoded_list_screen ): ListScreen {
		if ( ! $this->can_decode( $encoded_list_screen ) ) {
			throw new UndecodableListScreenException( $encoded_list_screen );
		}

		$list_key = (string) ( $encoded_list_screen['type'] ?? '' );

		if ( ! $this->list_screen_factory->can_create( $list_key ) ) {
			throw new UndecodableListScreenException( $encoded_list_screen );
		}

		$settings = [
			'list_id'     => $encoded_list_screen['id'],
			'columns'     => $encoded_list_screen['columns'] ?? [],
			'preferences' => $encoded_list_screen['settings'] ?? [],
			'title'       => $encoded_list_screen['title'] ?? '',
			'date'        => DateTime::createFromFormat( 'U', (string) $encoded_list_screen['updated'] ),
		];

		return $this->list_screen_factory->create( $list_key, $settings );
	}

	public function can_decode( array $encoded_list_screen ): bool {
		if ( ! isset( $encoded_list_screen['type'], $encoded_list_screen['version'] ) ) {
			return false;
		}

		if ( ! $this->list_screen_factory->can_create( (string) $encoded_list_screen['type'] ) ) {
			return false;
		}

		if ( ! version_compare( $encoded_list_screen['version'], '5.1.0', '>=' ) ) {
			return false;
		}

		return true;
	}

}