<?php

// Register Custom Post Type
function seedprod_pro_post_type() {

	$args = array( 
		'supports' => array( 'title', 'editor', 'revisions' ), 
		'public' => false, 
		'capability_type' => 'page',
		'show_ui' => false,
		'publicly_queryable' => true,
	);
	register_post_type( 'seedprod', $args );

}
$sedprod_pt = post_type_exists('seedprod');
if($sedprod_pt === false){
	add_action( 'init', 'seedprod_pro_post_type', 0 );
}
