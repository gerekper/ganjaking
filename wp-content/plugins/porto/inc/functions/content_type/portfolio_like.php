<?php

add_action( 'wp_ajax_porto_portfolio-like', 'porto_ajax_portfolio_like' );
add_action( 'wp_ajax_nopriv_porto_portfolio-like', 'porto_ajax_portfolio_like' );

function porto_ajax_portfolio_like() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	if ( isset( $_POST['portfolio_id'] ) ) {
		$portfolio_id = (int) $_POST['portfolio_id'];
		$like_count   = get_post_meta( $portfolio_id, 'like_count', true );
		if ( ! isset( $_COOKIE[ 'porto_like_' . $portfolio_id ] ) || 0 == (int) $like_count ) {
			$like_count++;
			setcookie( 'porto_like_' . $portfolio_id, $portfolio_id, time() * 20, '/' );
			update_post_meta( $portfolio_id, 'like_count', $like_count );
		}
		echo '<span class="portfolio-liked linked" title="' . esc_attr__( 'Already Liked', 'porto' ) . '" data-bs-tooltip><i class="fas fa-heart"></i>' . $like_count . '</span>';
	}

	// phpcs: enable
	exit;
}

function porto_portfolio_like() {
	global $post;

	$portfolio_id = (int) $post->ID;
	$like_count   = get_post_meta( $portfolio_id, 'like_count', true );

	if ( $like_count && isset( $_COOKIE[ 'porto_like_' . $portfolio_id ] ) ) {
		$output = '<span class="portfolio-liked linked" title="' . esc_attr__( 'Already liked', 'porto' ) . '" data-bs-tooltip><i class="fas fa-heart"></i>' . $like_count . '</span>';
	} else {
		$output = '<span class="portfolio-like" title="' . esc_attr__( 'Like', 'porto' ) . '" data-bs-tooltip data-id="' . $portfolio_id . '"><i class="fas fa-heart"></i>' . ( $like_count ? $like_count : '0' ) . '</span>';
	}

	return $output;
}
