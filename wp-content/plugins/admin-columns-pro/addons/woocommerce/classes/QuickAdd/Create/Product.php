<?php

namespace ACA\WC\QuickAdd\Create;

use ACP;
use WP_User;

class Product implements ACP\QuickAdd\Model\Create {

	public function create() {
		$post_id = wp_insert_post( [
			'post_title'  => __( '(no title)' ),
			'post_status' => 'draft',
			'post_type'   => 'product',
		] );

		wp_set_object_terms( $post_id, 'simple', 'product_type' );

		$product = wc_get_product( $post_id );
		$product->save();

		return $post_id;
	}

	public function has_permission( WP_User $user ) {
		return user_can( $user, get_post_type_object( 'product' )->cap->create_posts );
	}

}