<?php

namespace ACA\EC\Editing\Service\Event;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\EC\Editing;
use ACP;
use ACP\Editing\View;

class HideFromUpcoming implements ACP\Editing\Service {

	private const META_KEY = '_EventHideFromUpcoming';

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( '' ),
				new Option( 'yes' )
			)
		);
	}

	public function get_value( $id ) {
		return get_post_meta( $id, self::META_KEY, true );
	}

	public function update( int $id, $data ): void {
		if ( $data ) {
			update_post_meta( $id, self::META_KEY, $data );
		} else {
			delete_post_meta( $id, self::META_KEY );
		}
	}

}