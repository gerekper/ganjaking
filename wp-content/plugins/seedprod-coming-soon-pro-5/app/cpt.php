<?php

/**
 * Reguster seedprod custom post type.
 *
 * @return void
 */
function seedprod_pro_post_type() {

	$args = array(
		'supports'           => array( 'title', 'editor', 'revisions' ),
		'public'             => false,
		'capability_type'    => 'page',
		'show_ui'            => false,
		'publicly_queryable' => true,
		'can_export'         => false,
	);
	register_post_type( 'seedprod', $args );

}
$sedprod_pt = post_type_exists( 'seedprod' );
if ( false === $sedprod_pt ) {
	add_action( 'init', 'seedprod_pro_post_type', 0 );
}
