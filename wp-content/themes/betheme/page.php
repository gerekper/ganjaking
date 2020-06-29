<?php
/**
 * The template for displaying all pages.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

get_header();
?>

<div id="Content">
	<div class="content_wrapper clearfix">

		<div class="sections_group">

			<div class="entry-content" itemprop="mainContentOfPage">

				<?php
					while (have_posts()) {

						the_post();

						$mfn_builder = new Mfn_Builder_Front(get_the_ID());
						$mfn_builder->show();

					}
				?>

				<div class="section section-page-footer">
					<div class="section_wrapper clearfix">

						<div class="column one page-pager">
							<?php
								wp_link_pages(array(
									'before' => '<div class="pager-single">',
									'after' => '</div>',
									'link_before' => '<span>',
									'link_after' => '</span>',
									'next_or_number' => 'number'
								));
							?>
						</div>

					</div>
				</div>

			</div>

			<?php if (mfn_opts_get('page-comments')): ?>
				<div class="section section-page-comments">
					<div class="section_wrapper clearfix">

						<div class="column one comments">
							<?php comments_template('', true); ?>
						</div>

					</div>
				</div>
			<?php endif; ?>

		</div>

		<?php get_sidebar(); ?>

	</div>
</div>

<?php get_footer();
