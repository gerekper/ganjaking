<?php

/**
 * Docs Reactions Shortcode
 * *
 * @since      1.0.2
 * 
 */

add_shortcode('betterdocs_article_reactions', 'betterdocs_article_reactions');
function betterdocs_article_reactions($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	$reactions_text = get_theme_mod('betterdocs_post_reactions_text', esc_html__('What are your Feelings', 'betterdocs-pro'));
	?>
	<div class="betterdocs-article-reactions">
		<div class="betterdocs-article-reactions-heading">
			<?php 
			if ($reactions_text) {
				echo '<h5>' . esc_html($reactions_text) . '</h5>';
			} 
			?>
		</div>
		<ul class="betterdocs-article-reaction-links">
			<li>
				<a class="betterdocs-feelings" data-feelings="happy" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<path class="st0" d="M10,0.1c-5.4,0-9.9,4.4-9.9,9.8c0,5.4,4.4,9.9,9.8,9.9c5.4,0,9.9-4.4,9.9-9.8C19.9,4.5,15.4,0.1,10,0.1z
					M13.3,6.4c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.1,12.5,6.4,13.3,6.4z M6.7,6.4
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C5.2,7.1,5.9,6.4,6.7,6.4z M10,16.1c-2.6,0-4.9-1.6-5.8-4
					l1.2-0.4c0.7,1.9,2.5,3.2,4.6,3.2s3.9-1.3,4.6-3.2l1.2,0.4C14.9,14.5,12.6,16.1,10,16.1z" />
						<path class="st1" d="M-6.6-119.7c-7.1,0-12.9,5.8-12.9,12.9s5.8,12.9,12.9,12.9s12.9-5.8,12.9-12.9S0.6-119.7-6.6-119.7z
					M-2.3-111.4c1.1,0,2,0.9,2,2c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-4.3-110.5-3.4-111.4-2.3-111.4z M-10.9-111.4c1.1,0,2,0.9,2,2
					c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-12.9-110.5-12-111.4-10.9-111.4z M-6.6-98.7c-3.4,0-6.4-2.1-7.6-5.3l1.6-0.6
					c0.9,2.5,3.3,4.2,6,4.2s5.1-1.7,6-4.2L1-104C-0.1-100.8-3.2-98.7-6.6-98.7z" />
					</svg>
				</a>
			</li>
			<li>
				<a class="betterdocs-feelings" data-feelings="normal" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<path class="st0" d="M10,0.2c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.2,10,0.2z M6.7,6.5
				c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5C5.9,9.5,5.2,8.9,5.2,8C5.2,7.2,5.9,6.5,6.7,6.5z M14.2,14.3H5.9
				c-0.3,0-0.6-0.3-0.6-0.6c0-0.3,0.3-0.6,0.6-0.6h8.3c0.3,0,0.6,0.3,0.6,0.6C14.8,14,14.5,14.3,14.2,14.3z M13.3,9.5
				c-0.8,0-1.5-0.7-1.5-1.5c0-0.8,0.7-1.5,1.5-1.5c0.8,0,1.5,0.7,1.5,1.5C14.8,8.9,14.1,9.5,13.3,9.5z" />
					</svg>
				</a>
			</li>
			<li>
				<a class="betterdocs-feelings" data-feelings="sad" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<circle class="st0" cx="27.5" cy="0.6" r="1.9" />
						<circle class="st0" cx="36" cy="0.6" r="1.9" />
						<path class="st1" d="M10,0.3c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.3,10,0.3z M13.3,6.6
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.3,12.4,6.6,13.3,6.6z M6.7,6.6c0.8,0,1.5,0.7,1.5,1.5
					c0,0.8-0.7,1.5-1.5,1.5C5.9,9.6,5.2,9,5.2,8.1C5.2,7.3,5.9,6.6,6.7,6.6z M14.1,15L14.1,15c-0.2,0-0.4-0.1-0.5-0.2
					c-0.9-1-2.2-1.7-3.7-1.7s-2.8,0.6-3.7,1.7C6.2,14.9,6,15,5.9,15h0c-0.6,0-0.8-0.6-0.5-1.1c1.1-1.3,2.8-2.1,4.6-2.1
					c1.8,0,3.5,0.8,4.6,2.1C15,14.3,14.7,15,14.1,15z" />
					</svg>
				</a>
			</li>
		</ul>
	</div> <!-- Social Share end-->
<?php }


/**
 * Category box layout 3
 * *
 * @since      1.0.2
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_category_box_2', 'betterdocs_category_box_2');
function betterdocs_category_box_2($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$post_count = BetterDocs_DB::get_settings('post_count');
	$count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
	$count_text = BetterDocs_DB::get_settings('count_text');
	$get_args = shortcode_atts(
		array(
			'column' => '',
			'nested_subcategory' => '',
			'terms' => '',
			'kb_slug' => '',
			'multiple_knowledge_base' => false,
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);

	$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['kb_slug']);

	if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
		$class = ['betterdocs-categories-wrap betterdocs-category-box betterdocs-category-box-pro pro-layout-3 ash-bg layout-flex'];
		if (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
			$class[] = 'docs-col-' . $get_args['column'];
		} else {
			$class[] = 'docs-col-' . $column_number;
		}

		if ($get_args['disable_customizer_style'] == false) {
			$class[] = 'single-kb';
		}

		echo '<div class="' . implode(' ', $class) . '">';

		// display category grid by order
		foreach ($taxonomy_objects as $term) {
			$term_id = $term->term_id;
			if ($term->count != 0) {
				// set active category class in single page	
				$wrap_class = 'docs-single-cat-wrap';

				$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);

				echo '<a href="' . esc_url($term_permalink) . '" class="' . esc_attr($wrap_class) . '">';
				$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);
				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}
				echo '<div class="title-count">';
				echo '<'.$get_args['title_tag'].' class="docs-cat-title">' . $term->name . '</'.$get_args['title_tag'].'>';
				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs'));
					}
				}
				echo '</div>
				</a>';
			}
		}
		echo '</div>';

	endif;
	return ob_get_clean();
}

/**
 * Manage multiple docs
 * *
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_multiple_kb', 'betterdocs_multiple_kb');
function betterdocs_multiple_kb($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$post_count = BetterDocs_DB::get_settings('post_count');
	$count_text = BetterDocs_DB::get_settings('count_text');
	$count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
	$get_args = shortcode_atts(
		array(
			'column' => '',
			'terms' => '',
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);

	$terms_object = array(
		'taxonomy' => 'knowledge_base',
		'hide_empty' => true,
		'parent' => 0
	);

	if ($get_args['terms']) {
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

	$taxonomy_objects = get_terms($terms_object);

	if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
		$class = ['betterdocs-categories-wrap betterdocs-category-box layout-2 ash-bg'];
		$class[] = 'layout-flex';

		if (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
			$class[] = 'docs-col-' . $get_args['column'];
		} else {
			$class[] = 'docs-col-' . $column_number;
		}
		if ($get_args['disable_customizer_style'] == false) {
			$class[] = 'multiple-kb';
		}
		echo '<div class="' . implode(' ', $class) . '">';
		// display category grid by order
		foreach ($taxonomy_objects as $term) {
			$term_id = $term->term_id;

			if ($term->count != 0) {
				$wrap_class = 'docs-single-cat-wrap';
				echo '<a href="' . get_term_link($term->slug, 'knowledge_base') . '" class="' . esc_attr($wrap_class) . '">';
				$cat_icon_id = get_term_meta($term_id, 'knowledge_base_image-id', true);

				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}

				echo '<'.$get_args['title_tag'].' class="docs-cat-title">' . $term->name . '</'.$get_args['title_tag'].'>';
				$mkb_desc = get_theme_mod('betterdocs_mkb_desc');

				if ($mkb_desc == true) {
					echo '<p class="cat-description">' . $term->description . '</p>';
				}

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs'));
					}
				}
				echo '</a>';
			}
		}
		echo '</div>';
	endif;
	return ob_get_clean();
}

/**
 * multiple kb layout 2
 * *
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_multiple_kb_2', 'betterdocs_multiple_kb_2');
function betterdocs_multiple_kb_2($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$post_count = BetterDocs_DB::get_settings('post_count');
	$count_text = BetterDocs_DB::get_settings('count_text');
	$count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
	$get_args = shortcode_atts(
		array(
			'column' => '',
			'terms' => '',
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);

	$terms_object = array(
		'taxonomy' => 'knowledge_base',
		'hide_empty' => true,
		'parent' => 0
	);

	if ($get_args['terms']) {
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

	$taxonomy_objects = get_terms($terms_object);

	if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
		$class = ['betterdocs-categories-wrap betterdocs-category-box betterdocs-category-box-pro pro-layout-3 ash-bg layout-flex'];
		$class[] = 'layout-flex';

		if (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
			$class[] = 'docs-col-' . $get_args['column'];
		} else {
			$class[] = 'docs-col-' . $column_number;
		}

		if ($get_args['disable_customizer_style'] == false) {
			$class[] = 'multiple-kb';
		}

		echo '<div class="' . implode(' ', $class) . '">';
		foreach ($taxonomy_objects as $term) {
			$term_id = $term->term_id;

			if ($term->count != 0) {
				$wrap_class = 'docs-single-cat-wrap';
				echo '<a href="' . get_term_link($term->slug, 'knowledge_base') . '" class="' . esc_attr($wrap_class) . '">';
				$cat_icon_id = get_term_meta($term_id, 'knowledge_base_image-id', true);

				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}

				echo '<div class="title-count">';
				echo '<'.$get_args['title_tag'].' class="docs-cat-title">' . $term->name . '</'.$get_args['title_tag'].'>';

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs'));
					}
				}

				echo '</div>';
				echo '</a>';
			}
		}
		echo '</div>';
	endif;
	return ob_get_clean();
}


/**
 * Get the category grid with docs list.
 * *
 * @since      1.0.2
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_category_grid_2', 'betterdocs_category_grid_2');
function betterdocs_category_grid_2($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$posts_number = BetterDocs_DB::get_settings('posts_number');
	$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
	$exploremore_btn = BetterDocs_DB::get_settings('exploremore_btn');
	$exploremore_btn_txt = BetterDocs_DB::get_settings('exploremore_btn_txt');
	$post_count = BetterDocs_DB::get_settings('post_count');
	$count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
	$count_text = BetterDocs_DB::get_settings('count_text');
	$get_args = shortcode_atts(
		array(
			'sidebar_list' => false,
            'orderby' => BetterDocs_DB::get_settings('alphabetically_order_post'),
            'order' => BetterDocs_DB::get_settings('docs_order'),
			'count' => true,
			'icon' => true,
			'masonry' => '',
			'column' => '',
			'posts' => '',
			'nested_subcategory' => '',
			'terms' => '',
            'kb_slug' => '',
			'multiple_knowledge_base' => false,
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);

	$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['kb_slug']);

	if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
		$class = ['betterdocs-categories-wrap category-grid pro-layout-4 white-bg'];

		if (!is_singular('docs') && !is_tax('doc_category') && !is_tax('doc_tag')) {
			$class[] = 'layout-flex';

			if (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
				$class[] = 'docs-col-' . $get_args['column'];
			} else {
				$class[] = 'docs-col-' . $column_number;
			}
		}
		if ($get_args['disable_customizer_style'] == false) {
			$class[] = 'single-kb';
		}

		echo '<div class="' . implode(' ', $class) . '">';

		// get single page category id
		if (is_single()) {
			$term_list = wp_get_post_terms(get_the_ID(), 'doc_category', array("fields" => "all"));
			$category_id = array_column($term_list, 'term_id');
			$page_cat = get_the_ID();
		} else {
			$category_id = '';
			$page_cat = '';
		}

		$taxonomy_first_row = array_slice($taxonomy_objects, 0, $column_number);
		$taxonomy_all = array_slice($taxonomy_objects, $column_number);

		echo '<div class="betterdocs-categories-wrap wrap-top-row layout-flex">';

		foreach ($taxonomy_first_row as $term) {
			if ('0' == ($term->count && $term->parent)) {
				$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);
				echo '<a href="' . esc_url($term_permalink) . '" class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-box">';
				echo '<div class="docs-cat-list-2-box-content">';
				$term_id = $term->term_id;
				$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);

				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}

				echo '<div class="title-count">';
				echo '<'.$get_args['title_tag'].' class="docs-cat-title">' . $term->name . '</'.$get_args['title_tag'].'>';

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs'));
					}
				}
				echo '</div>';
				echo '</div>';
				echo '</a>';
			}
		}
		echo '</div>';

		// display category grid by order
		foreach ($taxonomy_all as $term) {
			$term_id = $term->term_id;
			$term_slug = $term->slug;
			$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term_slug);
			echo '<div class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-items">
				<div class="docs-cat-title-wrap">
					<a href="' . esc_url($term_permalink) . '"><'.$get_args['title_tag'].' class="docs-cat-title">' . $term->name . '</'.$get_args['title_tag'].'></a>
				</div>
				<div class="docs-item-container">';

			if (isset($get_args['posts_per_grid']) && $get_args['posts_per_grid'] == true && is_numeric($get_args['posts_per_grid'])) {
				$posts_per_grid = $get_args['posts_per_grid'];
			} else {
				$posts_per_grid = $posts_number;
			}

			$list_args = BetterDocs_Helper::list_query_arg('docs', $get_args['multiple_knowledge_base'], $term_slug, $posts_per_grid, $get_args['orderby'], $get_args['order'], $get_args['kb_slug']);

			$args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);

			$post_query = new WP_Query($args);
			if ($post_query->have_posts()) :

				echo '<ul>';
				while ($post_query->have_posts()) : $post_query->the_post();
					$attr = ['href="' . get_the_permalink() . '"'];
					if ($page_cat === get_the_ID()) {
						$attr[] = 'class="active"';
					}
					echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' . get_the_title() . '</a></li>';
				endwhile;

				echo '</ul>';

			endif;
			wp_reset_query();

			// Sub category query
			if (
				($nested_subcategory == 1 || $get_args['nested_subcategory'] == true) 
				&& $get_args['nested_subcategory'] != "false"
				&& function_exists('nested_category_list')
			) {
				nested_category_list(
					$term_id, 
					$get_args['multiple_knowledge_base'], 
					$category_id,
                    'docs',
                    $get_args['orderby'],
                    $get_args['order'],
					$page_cat,
                    $get_args['kb_slug']
				);
			}

			if ($exploremore_btn == 1 && !is_singular('docs') && !is_tax('doc_category') && !is_tax('doc_tag')) {
				echo '<a class="docs-cat-link-btn" href="' . $term_permalink . '">' . esc_html($exploremore_btn_txt) . ' </a>';
			}
			echo '</div>
			</div>';
		}
		echo '</div>';
	endif;
	return ob_get_clean();
}

/* Describe what the code snippet does so you can remember later on */
//add_action('wp_head', 'betterdocs_helpshelf_integration');
function betterdocs_helpshelf_integration()
{
	?>
	<script type="text/javascript" id="hs-loader">
		window.helpShelfSettings = {
			"siteKey": "GQ2xBVCe",
			"userId": "",
			"userEmail": "",
			"userFullName": "",
			"userAttributes": {
				// Add custom user attributes here
			},
			"companyAttributes": {
				// Add custom company attributes here
			}
		};

		(function() {
			var po = document.createElement("script");
			po.type = "text/javascript";
			po.async = true;
			po.src = "https://s3.amazonaws.com/helpshelf-production/gen/loader/GQ2xBVCe.min.js";
			po.onload = po.onreadystatechange = function() {
				var rs = this.readyState;
				if (rs && rs != "complete" && rs != "loaded") return;
				HelpShelfLoader = new HelpShelfLoaderClass();
				HelpShelfLoader.identify(window.helpShelfSettings);
			};
			var s = document.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(po, s);
		})();
	</script>
<?php
};
?>