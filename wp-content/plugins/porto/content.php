<?php
global $porto_settings;
?>

<article <?php post_class(); ?>>
	<div class="page-content">
		<?php
			porto_render_rich_snippets();
			the_content();
		?>
	</div>
</article>

