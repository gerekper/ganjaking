<?php
/**
 * The main template file.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

get_header();

// class

$blog_classes	= array();
$section_class = array();
$current_id = get_queried_object_id();

// class | layout

if ($_GET && key_exists('mfn-b', $_GET)) {
	$blog_layout = esc_html($_GET['mfn-b']); // demo
} else {
	$blog_layout = mfn_opts_get('blog-layout', 'classic');
}
$blog_classes[] = $blog_layout;

// layout | masonry tiles

if ($blog_layout == 'masonry tiles') {
	$blog_layout = 'masonry';
}

// class | columns

if ($_GET && key_exists('mfn-bc', $_GET)) {
	$blog_classes[] = 'col-'. esc_html($_GET['mfn-bc']); // demo
} else {
	$blog_classes[] = 'col-'. mfn_opts_get('blog-columns', 3);
}

// full width

if ($_GET && key_exists('mfn-bfw', $_GET)) {
	$section_class[] = 'full-width'; // demo
}
if (mfn_opts_get('blog-full-width') && ($blog_layout == 'masonry')) {
	$section_class[] = 'full-width';
}

$section_class = implode(' ', $section_class);

// isotope

if ($_GET && key_exists('mfn-iso', $_GET)) {
	$isotope = true;
} elseif (mfn_opts_get('blog-isotope')) {
	$isotope = true;
} else {
	$isotope = false;
}

if ($isotope || ($blog_layout == 'masonry')) {
	$blog_classes[] = 'isotope';
}

// load more

$load_more = mfn_opts_get('blog-load-more');

// translate

$translate['filter'] = mfn_opts_get('translate') ? mfn_opts_get('translate-filter', 'Filter by') : __('Filter by', 'betheme');
$translate['tags'] = mfn_opts_get('translate') ? mfn_opts_get('translate-tags', 'Tags') : __('Tags', 'betheme');
$translate['authors'] = mfn_opts_get('translate') ? mfn_opts_get('translate-authors', 'Authors') : __('Authors', 'betheme');
$translate['all'] = mfn_opts_get('translate') ? mfn_opts_get('translate-all', 'Show all') : __('Show all', 'betheme');
$translate['categories'] = mfn_opts_get('translate') ? mfn_opts_get('translate-categories', 'Categories') : __('Categories', 'betheme');
$translate['item-all'] = mfn_opts_get('translate') ? mfn_opts_get('translate-item-all', 'All') : __('All', 'betheme');
?>

<div id="Content">
	<div class="content_wrapper clearfix">

		<div class="sections_group">

			<div class="extra_content">
				<?php
					if (get_option('page_for_posts') || mfn_opts_get('blog-page')) {

						if (category_description()) {

							echo '<div class="section the_content category_description">';
								echo '<div class="section_wrapper">';
									echo '<div class="the_content_wrapper">';
										echo category_description();
									echo '</div>';
								echo '</div>';
							echo '</div>';

						} else {

							$mfn_builder = new Mfn_Builder_Front(mfn_ID(), true);
							$mfn_builder->show();

						}

					}
				?>
			</div>

			<?php if (($filters = mfn_opts_get('blog-filters')) && (is_home() || is_category() || is_tag() || is_author())): ?>

				<div class="section section-filters">
					<div class="section_wrapper clearfix">

						<?php
							$filters_class = '';

							if ($isotope) {
								$filters_class .= ' isotope-filters';
							}

							if ($filters != 1) {
								$filters_class .= ' only '. $filters;
							}
						?>

						<!-- #Filters -->
						<div id="Filters" class="column one <?php echo esc_attr($filters_class); ?>">

							<ul class="filters_buttons">
								<li class="label"><?php echo esc_html($translate['filter']); ?></li>
								<li class="categories"><a class="open" href="#"><i class="icon-docs"></i><?php echo esc_html($translate['categories']); ?><i class="icon-down-dir"></i></a></li>
								<li class="tags"><a class="open" href="#"><i class="icon-tag"></i><?php echo esc_html($translate['tags']); ?><i class="icon-down-dir"></i></a></li>
								<li class="authors"><a class="open" href="#"><i class="icon-user"></i><?php echo esc_html($translate['authors']); ?><i class="icon-down-dir"></i></a></li>
								<li class="reset"><a class="close" data-rel="*" href="<?php echo esc_url(get_permalink(mfn_ID())); ?>"><i class="icon-cancel"></i><?php echo esc_html($translate['all']); ?></a></li>
							</ul>

							<div class="filters_wrapper">

								<ul class="categories">
									<?php

										$class = false;
										if( ! ( is_category() || is_tag() || is_author() ) ){
											$class = 'current-cat';
										}

										echo '<li class="reset-inner '. $class .'"><a data-rel="*" href="'. esc_url(get_permalink(mfn_ID())) .'">'. esc_html($translate['item-all']) .'</a></li>';

										if ($categories = get_categories()) {

											$exclude = mfn_get_excluded_categories();

											foreach ($categories as $category) {

												$class = false;

												if ($exclude && in_array($category->slug, $exclude)) {
													continue;
												}
												if( is_category() && $current_id == $category->term_id ){
													$class = 'current-cat';
												}

												echo '<li class="'. esc_attr($class) .'"><a data-rel=".category-'. esc_attr($category->slug) .'" href="'. esc_url(get_term_link($category)) .'">'. esc_html($category->name) .'</a></li>';

											}
										}

									?>
									<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>
								</ul>

								<ul class="tags">
									<?php

										echo '<li class="reset-inner"><a data-rel="*" href="'. esc_url(get_permalink(mfn_ID())) .'">'. esc_html($translate['item-all']) .'</a></li>';

										if ($tags = get_tags()) {
											foreach ($tags as $tag) {

												$class = false;

												if( is_tag() && $current_id == $tag->term_id ){
													$class = 'current-cat';
												}

												echo '<li class="'. esc_attr($class) .'"><a data-rel=".tag-'. esc_attr($tag->slug) .'" href="'. esc_url(get_tag_link($tag)) .'">'. esc_html($tag->name) .'</a></li>';

											}
										}

									?>
									<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>
								</ul>

								<ul class="authors">
									<?php

										echo '<li class="reset-inner"><a data-rel="*" href="'. esc_url(get_permalink(mfn_ID())) .'">'. esc_html($translate['item-all']) .'</a></li>';

										$authors = mfn_get_authors();
										if (is_array($authors)) {
											foreach ($authors as $auth) {

												$class = false;

												if( is_author() && $current_id == $auth->ID ){
													$class = 'current-cat';
												}

												echo '<li class="'. esc_attr($class) .'"><a data-rel=".author-'. esc_attr(mfn_slug($auth->data->user_login)) .'" href="'. esc_url(get_author_posts_url($auth->ID)) .'">'. esc_html($auth->data->display_name) .'</a></li>';

											}
										}

									?>
									<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>
								</ul>
							</div>

						</div>

					</div>
				</div>

			<?php endif; ?>

			<div class="section <?php echo esc_attr($section_class); ?>">
				<div class="section_wrapper clearfix">

					<div class="column one column_blog">
						<div class="blog_wrapper isotope_wrapper">

							<div class="posts_group lm_wrapper <?php echo esc_attr(implode(' ', $blog_classes)); ?>">
								<?php

									$attr = array(
										'featured_image' => false,
										'filters' => $filters,
									);

									if ($load_more) {
										$attr['featured_image'] = 'no_slider';
									}
									if (mfn_opts_get('blog-images')) {
										$attr['featured_image'] = 'image';
									}

									echo mfn_content_post(false, false, $attr);
								?>
							</div>

							<?php
								if (function_exists('mfn_pagination')):

									echo mfn_pagination(false, $load_more);

								else:
									?>
										<div class="nav-next"><?php next_posts_link(__('&larr; Older Entries', 'betheme')) ?></div>
										<div class="nav-previous"><?php previous_posts_link(__('Newer Entries &rarr;', 'betheme')) ?></div>
									<?php
								endif;
							?>

						</div>
					</div>

				</div>
			</div>


		</div>

		<?php get_sidebar(); ?>

	</div>
</div>

<?php get_footer();
