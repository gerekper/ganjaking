<?php get_header(); ?>

<?php
global $porto_settings;
?>
	<div id="content" role="main">
		<?php /* The loop */ ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article <?php post_class(); ?>>
				<div class="page-content">
					<?php
					the_content();
					?>
				</div>
			</article>

		<?php endwhile; ?>

	</div>

<?php get_footer(); ?>
