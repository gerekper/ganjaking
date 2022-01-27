<?php

	/*
	Template name: Custom-page Template
	*/

	get_header();

	// use this action to add any content before custom page container element
	do_action( 'agro_page_header_action' );

	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	endif;

	get_footer();

?>
