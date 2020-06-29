<?php
/**
 * The template for displaying content in the single-portfolio.php template
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

// prev & next post

mfn_post_navigation_sort();

$single_post_nav = array(
	'hide-header'	=> false,
	'hide-sticky'	=> false,
	'in-same-term' => false,
);

$opts_single_post_nav = mfn_opts_get('prev-next-nav');

if (is_array($opts_single_post_nav)) {
	if (isset($opts_single_post_nav['hide-header'])) {
		$single_post_nav['hide-header'] = true;
	}
	if (isset($opts_single_post_nav['hide-sticky'])) {
		$single_post_nav['hide-sticky'] = true;
	}
	if (isset($opts_single_post_nav['in-same-term'])) {
		$single_post_nav['in-same-term'] = true;
	}
}

$post_prev = get_adjacent_post($single_post_nav['in-same-term'], '', true, 'portfolio-types');
$post_next = get_adjacent_post($single_post_nav['in-same-term'], '', false, 'portfolio-types');
$portfolio_page_id = mfn_opts_get('portfolio-page');

// categories

$aCategories = array();

$terms = get_the_terms(get_the_ID(), 'portfolio-types');
if (is_array($terms)) {
	foreach ($terms as $term) {
		$aCategories[]	= $term->term_id;
	}
}

// post classes

$classes = array();

if (get_post_meta(get_the_ID(), 'mfn-post-slider-header', true)) {
	$classes[] = 'no-img';
}
if (! mfn_opts_get('portfolio-single-title')) {
	$classes[] = 'no-title';
}

if (mfn_opts_get('share') == 'hide-mobile') {
	$classes[] = 'no-share-mobile';
} elseif (! mfn_opts_get('share')) {
	$classes[] = 'no-share';
}

if (mfn_opts_get('share-style')) {
	$classes[] = 'share-'. mfn_opts_get('share-style');
}

// translate

$translate['published'] = mfn_opts_get('translate') ? mfn_opts_get('translate-published', 'Published by') : __('Published by', 'betheme');
$translate['at'] = mfn_opts_get('translate') ? mfn_opts_get('translate-at', 'at') : __('at', 'betheme');
$translate['categories'] = mfn_opts_get('translate') ? mfn_opts_get('translate-categories', 'Categories') : __('Categories', 'betheme');
$translate['all'] = mfn_opts_get('translate') ? mfn_opts_get('translate-all', 'Show all') : __('Show all', 'betheme');
$translate['related'] = mfn_opts_get('translate') ? mfn_opts_get('translate-related', 'Related posts') : __('Related posts', 'betheme');
$translate['readmore'] = mfn_opts_get('translate') ? mfn_opts_get('translate-readmore', 'Read more') : __('Read more', 'betheme');
$translate['client'] = mfn_opts_get('translate') ? mfn_opts_get('translate-client', 'Client') : __('Client', 'betheme');
$translate['date'] = mfn_opts_get('translate') ? mfn_opts_get('translate-date', 'Date') : __('Date', 'betheme');
$translate['website'] = mfn_opts_get('translate') ? mfn_opts_get('translate-website', 'Website') : __('Website', 'betheme');
$translate['view'] = mfn_opts_get('translate') ? mfn_opts_get('translate-view', 'View website') : __('View website', 'betheme');
$translate['task'] = mfn_opts_get('translate') ? mfn_opts_get('translate-task', 'Task') : __('Task', 'betheme');
?>

<div id="portfolio-item-<?php the_ID(); ?>" <?php post_class($classes); ?>>

	<?php
		// single post navigation | sticky

		if (! $single_post_nav['hide-sticky']) {
			echo mfn_post_navigation_sticky($post_prev, 'prev', 'icon-left-open-big');
			echo mfn_post_navigation_sticky($post_next, 'next', 'icon-right-open-big');
		}
	?>

	<?php if (get_post_meta(get_the_ID(), 'mfn-post-template', true) != 'intro'): ?>

		<div class="section section-post-header">
			<div class="section_wrapper clearfix">

				<?php
					// single post navigation | header

					if (! $single_post_nav['hide-header']) {
						echo mfn_post_navigation_header($post_prev, $post_next, mfn_wpml_ID($portfolio_page_id), $translate);
					}
				?>

				<div class="column one post-header">

					<?php if (mfn_opts_get('share-style') != 'simple'): ?>
						<div class="button-love"><?php echo mfn_love() ?></div>
					<?php endif; ?>

					<div class="title_wrapper">

						<?php
							if (mfn_opts_get('portfolio-single-title')) {
								$h = mfn_opts_get('title-heading', 1);
								echo '<h'. esc_attr($h) .' class="entry-title" itemprop="headline">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h'. esc_attr($h) .'>';
							}
						?>

						<?php
							$show_meta = false;
							$single_meta = mfn_opts_get('portfolio-meta');

							if (is_array($single_meta)) {
								if (isset($single_meta['author']) || isset($single_meta['date']) || isset($single_meta['categories'])) {
									$show_meta = true;
								}
							}
						?>

						<?php if ($show_meta): ?>
							<div class="post-meta clearfix">

								<div class="author-date">

									<?php if (isset($single_meta['author'])): ?>
										<span class="author">
											<?php echo esc_html($translate['published']); ?> <i class="icon-user"></i>
											<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author_meta('display_name'); ?></a>
										</span>
									<?php endif; ?>

									<?php if (isset($single_meta['date'])): ?>
										<span class="date">
											<?php echo esc_html($translate['at']); ?> <i class="icon-clock"></i>
											<time class="entry-date" datetime="<?php the_date('c'); ?>" itemprop="datePublished" pubdate><?php echo esc_html(get_the_date()); ?></time>
										</span>
									<?php endif; ?>

								</div>

								<?php if (isset($single_meta['categories'])): ?>
									<div class="category">
										<span class="cat-btn"><?php echo esc_html($translate['categories']); ?> <i class="icon-down-dir"></i></span>
										<div class="cat-wrapper">
											<ul>
												<?php
													if (is_array($terms)) {
														foreach ($terms as $term) {
															echo '<li><a href="'. esc_url(get_term_link($term)) .'">'. esc_html($term->name) .'</a></li>';
														}
													}
												?>
											</ul>
										</div>
									</div>
								<?php endif; ?>

							</div>
						<?php endif; ?>

					</div>

				</div>

				<div class="column one single-photo-wrapper <?php echo mfn_post_thumbnail_type(get_the_ID()); ?>">

					<?php echo mfn_share('header'); ?>

					<?php if (! get_post_meta(get_the_ID(), 'mfn-post-slider-header', true)): ?>
					<div class="image_frame scale-with-grid">

						<div class="image_wrapper">
							<?php echo mfn_post_thumbnail(get_the_ID()); ?>
						</div>

						<?php
							if ($caption = get_post(get_post_thumbnail_id())->post_excerpt) {
								echo '<p class="wp-caption-text '. esc_attr(mfn_opts_get('featured-image-caption')) .'">'. wp_kses($caption, mfn_allowed_html('caption')) .'</p>';
							}
						?>

					</div>
					<?php endif; ?>

				</div>

				<div class="column one project-description">
					<ul>
						<?php

							// client
							if ($client = get_post_meta(get_the_ID(), 'mfn-post-client', true)) {
								echo '<li class="one-third"><span class="label">'. esc_html($translate['client']) .'</span>'. esc_html($client) .'</li>';
							}

							// date
							if (isset($single_meta['date'])) {
								echo '<li class="one-third"><span class="label">'. esc_html($translate['date']) .'</span>'. esc_html(get_the_date()) .'</li>';
							}

							// website
							if ($link = get_post_meta(get_the_ID(), 'mfn-post-link', true)) {
								echo '<li class="one-third"><span class="label">'. esc_html($translate['website']) .'</span><a target="_blank" href="'. esc_url($link) .'"><i class="icon-forward"></i>'. esc_html($translate['view']) .'</a></li>';
							}

							// task
							if ($task = get_post_meta(get_the_ID(), 'mfn-post-task', true)) {
								echo '<li><span class="label">'. esc_html($translate['task']) .'</span>'. esc_html($task) .'</li>';
							}
						?>
					</ul>
				</div>

			</div>
		</div>

	<?php endif; ?>

	<div class="entry-content" itemprop="mainContentOfPage">
		<?php
			$mfn_builder = new Mfn_Builder_Front(get_the_ID());
			$mfn_builder->show();
		?>
	</div>

	<div class="section section-post-footer">
		<div class="section_wrapper clearfix">

			<div class="column one post-pager">
				<?php
					// list of pages
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

	<?php if (mfn_opts_get('share') && (mfn_opts_get('share-style') == 'simple')): ?>
		<div class="section section-post-intro-share">
			<div class="section_wrapper clearfix">
				<div class="column one">

					<?php echo mfn_share('footer'); ?>

				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="section section-post-related">
		<div class="section_wrapper clearfix">

			<?php
				if (mfn_opts_get('portfolio-related') && $aCategories) {

					$related_count = intval(mfn_opts_get('portfolio-related'));
					$related_cols	= 'col-'. absint(mfn_opts_get('portfolio-related-columns', 3));
					$related_style = mfn_opts_get('related-style');

					$args = array(
						'post_type' => 'portfolio',
						'tax_query' => array(
							array(
								'taxonomy' => 'portfolio-types',
								'field' => 'term_id',
								'terms' => $aCategories
							),
						),
						'post__not_in' => array( get_the_ID() ),
						'posts_per_page' => $related_count,
						'order' => mfn_opts_get( 'portfolio-order', 'DESC' ),
						'orderby' => mfn_opts_get( 'portfolio-orderby', 'date' ),
						'post_status' => 'publish',
						'no_found_rows' => true,
						'ignore_sticky_posts'	=> true,
					);

					$query_related_posts = new WP_Query($args);
					if ($query_related_posts->have_posts()) {

						echo '<div class="section-related-adjustment '. esc_attr($related_style) .'">';

							echo '<h4>'. esc_html($translate['related']) .'</h4>';

							echo '<div class="section-related-ul '. esc_attr($related_cols) .'">';

								while ($query_related_posts->have_posts()) {
									$query_related_posts->the_post();

									echo '<div class="column post-related '. esc_attr(implode(' ', get_post_class())).'">';

										echo '<div class="image_frame scale-with-grid">';

											echo '<div class="image_wrapper">';
												echo mfn_post_thumbnail(get_the_ID(), 'portfolio');
											echo '</div>';

											if (has_post_thumbnail() && $caption = get_post(get_post_thumbnail_id())->post_excerpt) {
												echo '<p class="wp-caption-text '. esc_attr(mfn_opts_get('featured-image-caption')) .'">'. wp_kses($caption, mfn_allowed_html('caption')) .'</p>';
											}

										echo '</div>';

										echo '<div class="date_label">'. esc_html(get_the_date()) .'</div>';

										echo '<div class="desc">';
											echo '<h4><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h4>';
											echo '<hr class="hr_color" />';
											echo '<a href="'. esc_url(get_permalink()) .'" class="button button_left button_js"><span class="button_icon"><i class="icon-layout"></i></span><span class="button_label">'. esc_html($translate['readmore']) .'</span></a>';
										echo '</div>';

									echo '</div>';
								}

							echo '</div>';

						echo '</div>';
					}
					wp_reset_postdata();
				}
			?>

		</div>
	</div>

</div>
