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

			<?php porto_render_rich_snippets(); ?>

			<div class="faq-content">
				<?php the_content(); ?>
			</div>

		<?php endif; ?>

	</div>

<?php get_footer(); ?>
