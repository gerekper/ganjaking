<?php

namespace ACP\Editing\Service\Post;

use AC\Helper\Select\Option;
use AC\Request;
use AC\Type\ToggleOptions;
use ACP\Editing\Service;
use ACP\Editing\View\Toggle;

class Sticky implements Service {

	private $stickies;

	public function get_view( $context ) {
		$options = new ToggleOptions(
			new Option( 'no', 'No' ),
			new Option( 'yes', 'Yes' )
		);

		return new Toggle( $options );
	}

	public function get_value( $id ) {
		return $this->is_sticky( $id )
			? 'yes'
			: 'no';
	}

	public function update( Request $request ) {
		$id = (int) $request->get( 'id' );

		if ( 'yes' === $request->get( 'value' ) ) {
			stick_post( $id );
		} else {
			unstick_post( $id );
		}

		wp_update_post( [ 'ID' => $id ] );

		return true;
	}

	private function is_sticky( $post_id ) {
		if ( null === $this->stickies ) {
			$this->stickies = get_option( 'sticky_posts' );
		}

		return in_array( $post_id, (array) $this->stickies );
	}

}