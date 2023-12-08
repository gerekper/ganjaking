<?php

namespace ACA\EC\Editing\Service\Event;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Editing\View;
use ACP\Helper\Select\Post\PaginatedFactory;

class Organizer implements ACP\Editing\Service, ACP\Editing\PaginatedOptions {

	const META_KEY = '_EventOrganizerID';

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\AjaxSelect() )
			->set_multiple( true )
			->set_clear_button( true );
	}

	public function get_value( $id ) {
		$values = [];
		$ids = get_post_meta( $id, self::META_KEY, false );

		if ( ! $ids ) {
			return $values;
		}

		foreach ( array_filter( $ids ) as $_id ) {
			$values[ $_id ] = html_entity_decode( get_the_title( $_id ) );
		}

		return $values;
	}

	public function update( int $id, $data ): void {
		delete_post_meta( $id, self::META_KEY );

		if ( empty( $data ) ) {
			$data = [];
		}

		foreach ( $data as $value ) {
			add_post_meta( $id, self::META_KEY, $value );
		}
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'paged'     => $page,
			's'         => $search,
			'post_type' => 'tribe_organizer',
		] );
	}

}