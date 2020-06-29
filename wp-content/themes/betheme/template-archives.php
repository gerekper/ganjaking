<?php
/**
 * Template Name: Archives
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

					<div class="one-fourth column">
						<h4><?php esc_html_e('Available Pages', 'betheme'); ?></h4>
						<ul class="list">
							<?php wp_list_pages('title_li=&depth=-1'); ?>
						</ul>
					</div>

					<div class="one-fourth column">
						<h4><?php esc_html_e('The 20 latest posts', 'betheme'); ?></h4>
						<ul class="list">
							<?php
								$args = array(
									'post_type' => array('post'),
									'posts_per_page' => 20
								);
								$posts_query = new WP_Query($args);
								while ($posts_query->have_posts()) :
									$posts_query->the_post();
							?>
								<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></li>
							<?php
								endwhile;
								wp_reset_query();
							?>
						</ul>
					</div>

					<div class="one-fourth column">
						<h4><?php esc_html_e('Archives by Subject', 'betheme'); ?></h4>
						<ul class="list">
						<?php
							$args =  array(
								'orderby' => 'name',
								'show_count' => 0,
								'hide_empty' => 0,
								'title_li' => '',
								'taxonomy' => 'category'
							);
							wp_list_categories($args);
							?>
						</ul>
					</div>

					<div class="one-fourth column">
						<h4><?php esc_html_e('Archives by Month', 'betheme'); ?></h4>
						<ul class="list">
							<?php wp_get_archives('type=monthly'); ?>
						</ul>
					</div>

				</div>
			</div>

		</div>

	</div>
</div>

<?php get_footer();
