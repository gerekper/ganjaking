<?php

add_action( 'wp_ajax_porto_blog-like', 'porto_ajax_blog_like' );
add_action( 'wp_ajax_nopriv_porto_blog-like', 'porto_ajax_blog_like' );

function porto_ajax_blog_like() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	if ( isset( $_POST['blog_id'] ) ) {

		$blog_id    = (int) $_POST['blog_id'];
		$like_count = get_post_meta( $blog_id, 'like_count', true );

		if ( ! isset( $_COOKIE[ 'porto_like_' . $blog_id ] ) || 0 == (int) $like_count ) {

			$like_count++;
			setcookie( 'porto_like_' . $blog_id, $blog_id, time() + 360 * 24 * 60 * 60, '/' );
			update_post_meta( $blog_id, 'like_count', $like_count );
		}

		echo '<span class="like-text">' . esc_html__( 'Liked', 'porto' ) . ': </span><span class="blog-liked linked text-color-secondary" title="' . esc_attr__( 'Already Liked', 'porto' ) . '" data-tooltip><i class="fas fa-heart"></i><span class="font-weight-semibold">' . ( (int) $like_count ) . '</span></span>';
	}
	// phpcs: enable
	exit;
}


function porto_blog_like() {

	global $post;
	$blog_id    = (int) $post->ID;
	$like_count = get_post_meta( $blog_id, 'like_count', true );

	if ( $like_count && isset( $_COOKIE[ 'porto_like_' . $blog_id ] ) ) {
		$output = '<span class="like-text">' . esc_html__( 'Liked', 'porto' ) . ': </span><span class="blog-liked linked text-color-secondary" title="' . esc_attr__( 'Already liked', 'porto' ) . '" data-tooltip><i class="fas fa-heart"></i><span class="font-weight-semibold">' . ( (int) $like_count ) . '</span></span>';
	} else {
		$output = '<span class="like-text">' . esc_html__( 'Like', 'porto' ) . ': </span><span class="blog-like cur-pointer font-weight-semibold text-color-secondary" title="' . esc_attr__( 'Like', 'porto' ) . '" data-tooltip data-id="' . $blog_id . '"><i class="fas fa-heart"></i> ' . ( $like_count ? (int) $like_count : '0' ) . '</span>';
	}
	return $output;
}
