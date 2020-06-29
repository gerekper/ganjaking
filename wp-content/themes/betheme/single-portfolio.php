<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

get_header();

$class = '';
if (get_post_meta(get_the_ID(), 'mfn-post-template', true) == 'builder') {
	$class .= 'no-padding';
}
?>

<div id="Content" class="<?php echo esc_attr($class); ?>">
	<div class="content_wrapper clearfix">

		<div class="sections_group">

			<?php

				if (get_post_meta(get_the_ID(), 'mfn-post-template', true) == 'builder') {

					// template: builder -----

					// prev & next post navigation

					mfn_post_navigation_sort();

					$single_post_nav = array(
						'hide-sticky'	=> false,
						'in-same-term'	=> false,
					);

					$opts_single_post_nav = mfn_opts_get('prev-next-nav');
					if (isset($opts_single_post_nav['hide-sticky'])) {
						$single_post_nav['hide-sticky'] = true;
					}

					// single post navigation | sticky

					if (! $single_post_nav['hide-sticky']) {
						if (isset($opts_single_post_nav['in-same-term'])) {
							$single_post_nav['in-same-term'] = true;
						}

						$post_prev = get_adjacent_post($single_post_nav['in-same-term'], '', true, 'portfolio-types');
						$post_next = get_adjacent_post($single_post_nav['in-same-term'], '', false, 'portfolio-types');

						echo mfn_post_navigation_sticky($post_prev, 'prev', 'icon-left-open-big');
						echo mfn_post_navigation_sticky($post_next, 'next', 'icon-right-open-big');
					}

					while (have_posts()) {

						the_post();
						
						$mfn_builder = new Mfn_Builder_Front(get_the_ID());
						$mfn_builder->show();

					}

				} else {

					// template: default

					while (have_posts()) {
						the_post();
						get_template_part('includes/content', 'single-portfolio');
					}

					if (mfn_opts_get('portfolio-comments')) {
						echo '<div class="section section-page-comments">';
							echo '<div class="section_wrapper clearfix">';
								echo '<div class="column one comments">';
								comments_template('', true);
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
				}

			?>

		</div>

		<?php get_sidebar(); ?>

	</div>
</div>

<?php get_footer();
