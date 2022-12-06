<?php

namespace ACA\WC\Editing\Comment;

use ACP;
use ACP\Editing\Service;
use ACP\Editing\View;
use RuntimeException;
use WC_Comments;

class Rating implements ACP\Editing\Service\Editability, Service {

	public function is_editable( int $id ): bool {
		return 'product' === get_post_type( get_comment( $id )->comment_post_ID );
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Item is not a rating.', 'codepress-admin-columns' );
	}

	public function get_view( string $context ): ?View {
		$options = [
			'' => __( 'None', 'codepress-admin-columns' ),
		];

		for ( $i = 1; $i < 6; $i++ ) {
			$options[ $i ] = $i;
		}

		return ( new ACP\Editing\View\Select( $options ) )->set_clear_button( true );
	}

	public function get_value( $id ) {
		return get_metadata( 'comment', $id, 'rating', true );
	}

	public function update( int $id, $data ): void {
		$data = $data->get_value();

		if ( $data > 5 ) {
			throw new RuntimeException( __( 'Maximum rating is %s', 'codepress-admin-columns' ) );
		}

		$comment = get_comment( $id );
		$product = wc_get_product( $comment->comment_post_ID );

		// Update average rating for product
		$rating = WC_Comments::get_average_rating_for_product( $product );

		update_comment_meta( $id, 'rating', $rating );
	}

}