<?php

if ( is_singular() ) {
	if ( 'porto_builder' == get_post_type() ) {
		global $post;
		$post_backup = $post;
		wp_reset_postdata();
		get_template_part( 'share' );
		setup_postdata( $post_backup );
	} else {
		get_template_part( 'share' );
	}
}
