<?php
/**
 * Template Name: Sitemap
 *
 * @package Betheme
 * @author Muffin Group
 * @link https://muffingroup.com
 */

get_header();
?>

<div id="Content">
	<div class="content_wrapper clearfix">
		<div class="sections_group">
			<div class="section">
				<div class="section_wrapper clearfix">

					<?php
						if (have_posts()) {
							the_post();
						}
					?>

					<div class="one column">
						<ul class="list">
							<?php wp_list_pages('title_li='); ?>
						</ul>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer();
