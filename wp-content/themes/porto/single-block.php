<?php get_header(); ?>

<?php
global $porto_settings;

wp_reset_postdata();
?>

	<div id="content" role="main">

		<?php
		if ( have_posts() ) :
			the_post();
			?>

			<?php the_content(); ?>

		<?php endif; ?>

	</div>

<?php get_footer(); ?>
