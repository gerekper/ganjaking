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
    $get_args = shortcode_atts(
        array(
            'text' => ''
        ),
        $atts
    );
    do_action( 'betterdocs_before_shortcode_load' );
    if ($get_args['text']) {
        $reactions_text = $get_args['text'];
    } else {
        $reactions_text = get_theme_mod('betterdocs_post_reactions_text', esc_html__('What are your Feelings', 'betterdocs-pro'));
    }
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
    $nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
	$get_args = shortcode_atts(
		array(
			'column' => '',
			'nested_subcategory' => '',
			'terms' => '',
            'terms_orderby' => BetterDocs_DB::get_settings('alphabetically_order_term'),
			'terms_order' => '',
			'kb_slug' => '',
			'multiple_knowledge_base' => false,
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);

    $nested_subcategory = ($nested_subcategory == 1 && $get_args['nested_subcategory'] == '') || ($get_args['nested_subcategory'] == true && $get_args['nested_subcategory'] != "false");
	$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);

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
			$term_slug = $term->slug;
			$count = $term->count;
			$get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
			$term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
			if ($term_count > 0) {
				// set active category class in single page	
				$wrap_class = 'docs-single-cat-wrap';

				$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);

				echo '<a href="' . esc_url($term_permalink) . '" class="' . esc_attr($wrap_class) . '" id="cat-id-'.$term_id.'">';
				$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);
				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}
				echo '<div class="title-count">';
				echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';
				$cat_desc = get_theme_mod('betterdocs_doc_page_cat_desc');
				if ($cat_desc == true) {
					echo '<p class="cat-description">' . $term->description . '</p>';
				}
				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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
 * List view
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_list_view', 'betterdocs_list_view');
function betterdocs_list_view($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
    ob_start();
    $post_count = BetterDocs_DB::get_settings('post_count');
    $count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
    $count_text = BetterDocs_DB::get_settings('count_text');
    $nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
    $get_args = shortcode_atts(
        array(
			'kb_description' => false,
            'nested_subcategory' => '',
            'terms' => '',
            'kb_slug' => '',
            'terms_orderby' => '',
			'terms_order' => '',
            'multiple_knowledge_base' => false,
            'disable_customizer_style' => false,
            'title_tag' => 'h2'
        ),
        $atts
    );
    $nested_subcategory = ($nested_subcategory == 1 && $get_args['nested_subcategory'] == '') || ($get_args['nested_subcategory'] == true && $get_args['nested_subcategory'] != "false");
    $taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'] ,$get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);

    if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
        $class = ['betterdocs-categories-wrap betterdocs-category-box betterdocs-category-box-pro pro-layout-3 layout-flex betterdocs-list-view ash-bg'];

        if ($get_args['disable_customizer_style'] == false) {
            $class[] = 'single-kb';
        }

        echo '<div class="' . implode(' ', $class) . '">';

        // display category grid by order
        foreach ($taxonomy_objects as $term) {
            $term_id = $term->term_id;
            $term_slug = $term->slug;
            $count = $term->count;
            $get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
            $term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
            if ($term_count > 0) {
                $term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);

                echo '<a href="' . esc_url($term_permalink) . '" class="docs-single-cat-wrap" id="cat-id-'.$term_id.'">';
                $cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);
                if ($cat_icon_id) {
                    echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
                } else {
                    echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
                }
                echo '<div class="title-count">';
                echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';
                $cat_desc = get_theme_mod('betterdocs_doc_page_cat_desc');
                if ($cat_desc == true || $get_args['kb_description'] == true) {
                    echo '<p class="cat-description">' . $term->description . '</p>';
                }
                if ($post_count == 1) {
                    if ($term->count == 1) {
                        echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
                    } else {
                        echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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
 * List view
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_multiple_kb_tab_grid', 'betterdocs_multiple_kb_tab_grid');
function betterdocs_multiple_kb_tab_grid($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
    ob_start();
    $posts_number        = BetterDocs_DB::get_settings('posts_number');
    $nested_subcategory  = BetterDocs_DB::get_settings('nested_subcategory') == 'off' ? false : true;
    $exploremore_btn     = BetterDocs_DB::get_settings('exploremore_btn');
    $exploremore_btn_txt = BetterDocs_DB::get_settings('exploremore_btn_txt');
    $get_args = shortcode_atts(
        array(
            'column' => '',
            'terms' => '',
            'terms_orderby' => '',
            'terms_order' => '',
            'disable_customizer_style' => false,
            'title_tag' => 'h2',
            'orderby' => BetterDocs_DB::get_settings('alphabetically_order_post'),
            'order' => BetterDocs_DB::get_settings('docs_order'),
            'posts_per_grid' => '',
            'post_counter' => true,
            'icon' => true
        ),
        $atts
    );

    $terms_object = array(
        'taxonomy' => 'knowledge_base',
        'hide_empty' => true,
        'parent' => 0
    );

	$alphabetically_order_term = BetterDocs_DB::get_settings('alphabetically_order_term');
    if ( $alphabetically_order_term != 1 ) {
        $terms_object['meta_key'] = 'kb_order';
        $terms_object['orderby'] = 'meta_value_num';
        $terms_object['order'] = 'ASC';
    } else {
        $terms_object['orderby'] = 'name';
    }

    if ($get_args['terms']) {
        $terms_object['include'] = explode(',', $get_args['terms']);
        $terms_object['orderby'] = 'include';
    }

    $taxonomy_objects = get_terms(apply_filters('betterdocs_kb_terms_object', $terms_object));

    if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
        $class = ['betterdocs-categories-wrap betterdocs-tab-grid ash-bg'];

        if ($get_args['disable_customizer_style'] == false) {
            $class[] = 'multiple-kb';
        }

        echo '<div class="' . implode(' ', $class) . '">';
        ?>
        <div class="betterdocs-tab-list tabs-nav">
            <?php
            foreach ($taxonomy_objects as $kb) {
                if ($kb->count > 0) {
                    echo '<a href="" class="icon-wrap" data-toggle-target=".'.$kb->slug .'">'.$kb->name .'</a>';
                }
            }
            ?>
        </div>
        <div class="tabs-content">
            <?php
            foreach ($taxonomy_objects as $kb) {
                if ($kb->count > 0) {
                    echo '<div class="betterdocs-tab-content '.$kb->slug.'">';
                    echo '<div class="betterdocs-tab-categories">';
                    $category_objects = BetterDocs_Helper::taxonomy_object(true, $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $kb->slug, $nested_subcategory);
                    if ($category_objects && !is_wp_error($category_objects)) {
                        // display category grid by order
                        foreach ($category_objects as $term) {
                            $term_id = $term->term_id;
                            $term_slug = $term->slug;
                            $count = $term->count;
                            $get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
                            $term_count = apply_filters('betterdocs_postcount', $get_term_count, true, $term_id, $term_slug, $count, $nested_subcategory, $kb->slug);

                            if ($term_count > 0) {
                                $cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);

                                if ($cat_icon_id) {
                                    $cat_icon_url = wp_get_attachment_image_url($cat_icon_id, 'thumbnail');
                                    $cat_icon = '<img class="docs-cat-icon" src="' . $cat_icon_url . '" alt="">';
                                } else {
                                    $cat_icon = '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
                                }
                                $term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);
                                echo '<div class="docs-single-cat-wrap" id="cat-id-'.$term_id.'">
                                    <div class="docs-cat-title-inner">
                                        <div class="docs-cat-title">' . $cat_icon . '<a href="' . esc_url($term_permalink) . '"><'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-heading">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'></a></div>
                                    </div>
                                    <div class="docs-item-container">';
                                        if (isset($get_args['posts_per_grid']) && $get_args['posts_per_grid'] == true && is_numeric($get_args['posts_per_grid'])) {
                                            $posts_per_grid = $get_args['posts_per_grid'];
                                        } else {
                                            $posts_per_grid = $posts_number;
                                        }

                                        $list_args = BetterDocs_Helper::list_query_arg('docs', true, $term_slug, $posts_per_grid, $get_args['orderby'], $get_args['order'], $kb->slug);
                                        $args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);
                                        $post_query = new WP_Query($args);
                                        if ($post_query->have_posts()) :
                                            echo '<ul>';
                                            while ($post_query->have_posts()) : $post_query->the_post();
                                                $attr = ['href="' . get_the_permalink() . '"'];
                                                echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' . esc_html(get_the_title()) . '</a></li>';
                                            endwhile;
                                            echo '</ul>';
                                        endif;
                                        wp_reset_query();

                                        // Sub category query
                                        if ($nested_subcategory == true) {
                                            nested_category_list(
                                                $term_id,
                                                true,
                                                '',
                                                'docs',
												$get_args['orderby'],
												$get_args['order'],
                                                $get_args['terms_orderby'],
                                                $get_args['terms_order'],
                                                '',
                                                $kb->slug
                                            );
                                        }

                                        // Read More Button
                                        if ($get_args['posts_per_grid'] == '-1' || $posts_number == '-1') {
                                            echo '';
                                        } else if ($exploremore_btn == 1 && !is_singular('docs') && BetterDocs_Helper::get_tax() != 'doc_category' && !is_tax('doc_tag')) {
                                            echo '<a class="docs-cat-link-btn" href="' . $term_permalink . '">' . esc_html($exploremore_btn_txt) . '</a>';
                                        }
                                    echo '</div>
                                </div>';
                            }
                        }
                    }
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <?php
        echo '</div>';

    endif;
    return ob_get_clean();
}

/**
 * Popular articles
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_popular_articles', 'betterdocs_popular_articles');
function betterdocs_popular_articles($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
    ob_start();
    $get_args = shortcode_atts(
        array(
            'post_per_page' => '',
            'title' => esc_html__('Popular Docs', 'betterdocs-pro'),
            'title_tag' => 'h2',
            'multiple_knowledge_base' => false,
            'disable_customizer_style' => false,
        ),
        $atts
    );

    $class = ['betterdocs-categories-wrap betterdocs-popular-list'];

    if ($get_args['disable_customizer_style'] == false && $get_args['multiple_knowledge_base'] == true) {
        $class[] = 'multiple-kb';
    } elseif ($get_args['disable_customizer_style'] == false && $get_args['multiple_knowledge_base'] == false) {
        $class[] = 'single-kb';
    }

    echo '<div class="' . implode(' ', $class) . '">';
    echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="popular-title">' . $get_args['title'] . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';
    $args = array(
        'post_type' => 'docs',
        'posts_per_page' => !empty( $get_args['post_per_page'] ) ? $get_args['post_per_page'] : 10,
        'meta_key' => '_betterdocs_meta_views',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );
    $args = apply_filters('betterdocs_articles_args', $args);
    $post_query = new WP_Query($args);
    if ($post_query->have_posts()) :
        echo '<ul>';
        while ($post_query->have_posts()) : $post_query->the_post();
            $icon = '<svg viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0)">
                <path d="M13.15 5.40903H4.84447C4.4615 5.40903 4.15234 5.73849 4.15234 6.14662C4.15234 6.55476 4.4615 6.88422 4.84447 6.88422H13.15C13.533 6.88422 13.8422 6.55476 13.8422 6.14662C13.8422 5.73849 13.533 5.40903 13.15 5.40903Z"/>
                <path d="M13.15 8.85112H4.84447C4.4615 8.85112 4.15234 9.18058 4.15234 9.58872C4.15234 9.99685 4.4615 10.3263 4.84447 10.3263H13.15C13.533 10.3263 13.8422 9.99685 13.8422 9.58872C13.8422 9.18058 13.533 8.85112 13.15 8.85112Z"/>
                <path d="M13.15 12.2933H4.84447C4.4615 12.2933 4.15234 12.6227 4.15234 13.0309C4.15234 13.439 4.4615 13.7685 4.84447 13.7685H13.15C13.533 13.7685 13.8422 13.439 13.8422 13.0309C13.8422 12.6227 13.533 12.2933 13.15 12.2933Z"/>
                <path d="M10.3815 15.7354H4.84447C4.4615 15.7354 4.15234 16.0648 4.15234 16.473C4.15234 16.8811 4.4615 17.2106 4.84447 17.2106H10.3815C10.7645 17.2106 11.0736 16.8811 11.0736 16.473C11.0736 16.0648 10.7645 15.7354 10.3815 15.7354Z"/>
                <path d="M15.9236 0H9.00231H2.07639C0.927455 0 0 0.988377 0 2.21279V19.7872C0 21.0116 0.927455 22 2.07639 22H9.00231H15.9282C17.0772 22 18.0046 21.0116 18.0046 19.7872V2.21279C18 0.988377 17.0725 0 15.9236 0ZM16.6157 19.7872C16.6157 20.1954 16.3066 20.5248 15.9236 20.5248H9.00231H2.07639C1.69341 20.5248 1.38426 20.1954 1.38426 19.7872V2.21279C1.38426 1.80465 1.69341 1.47519 2.07639 1.47519H6.9213H9.00231H11.0833H15.9282C16.3112 1.47519 16.6204 1.80465 16.6204 2.21279V19.7872H16.6157Z"/>
                </g>
                </svg>';
            echo '<li>'.$icon.'<a href="' . get_the_permalink() . '">' . esc_html(get_the_title()) . '</a></li>';
        endwhile;
        echo '</ul>';
    endif;
    wp_reset_query();
    echo '</div>';
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

    $alphabetically_order_term = BetterDocs_DB::get_settings('alphabetically_order_term');
    if ( $alphabetically_order_term != 1 ) {
        $terms_object['meta_key'] = 'kb_order';
        $terms_object['orderby'] = 'meta_value_num';
        $terms_object['order'] = 'ASC';
    } else {
        $terms_object['orderby'] = 'name';
    }

	if ($get_args['terms']) {
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

	$taxonomy_objects = get_terms(apply_filters('betterdocs_kb_terms_object', $terms_object));
	
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
				echo '<a href="' . get_term_link($term->slug, 'knowledge_base') . '" class="' . esc_attr($wrap_class) . '" id="mkb-id-'.$term_id.'">';
				$cat_icon_id = get_term_meta($term_id, 'knowledge_base_image-id', true);

				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="'.$term->name.'">';
				}
			
				echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';
				$mkb_desc = get_theme_mod('betterdocs_mkb_desc');

				if ($mkb_desc == true) {
					echo '<p class="cat-description">' . $term->description . '</p>';
				}

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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

    $alphabetically_order_term = BetterDocs_DB::get_settings('alphabetically_order_term');
    if ( $alphabetically_order_term != 1 ) {
        $terms_object['meta_key'] = 'kb_order';
        $terms_object['orderby'] = 'meta_value_num';
        $terms_object['order'] = 'ASC';
    } else {
        $terms_object['orderby'] = 'name';
    }

	if ($get_args['terms']) {
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

    $taxonomy_objects = get_terms(apply_filters('betterdocs_kb_terms_object', $terms_object));

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
				echo '<a href="' . get_term_link($term->slug, 'knowledge_base') . '" class="' . esc_attr($wrap_class) . '" id="mkb-id-'.$term_id.'">';
				$cat_icon_id = get_term_meta($term_id, 'knowledge_base_image-id', true);

				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="'.$term->name.'">';
				}

				echo '<div class="title-count">';
				echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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
 * Multiple KB List
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_multiple_kb_list', 'betterdocs_multiple_kb_list');
function betterdocs_multiple_kb_list($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
    ob_start();
    $post_count = BetterDocs_DB::get_settings('post_count');
    $count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
    $count_text = BetterDocs_DB::get_settings('count_text');
    $nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
    $get_args = shortcode_atts(
        array(
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

	$alphabetically_order_term = BetterDocs_DB::get_settings('alphabetically_order_term');
    if ( $alphabetically_order_term != 1 ) {
        $terms_object['meta_key'] = 'kb_order';
        $terms_object['orderby'] = 'meta_value_num';
        $terms_object['order'] = 'ASC';
    } else {
        $terms_object['orderby'] = 'name';
    }

    if ($get_args['terms']) {
        $terms_object['include'] = explode(',', $get_args['terms']);
        $terms_object['orderby'] = 'include';
    }

    $taxonomy_objects = get_terms(apply_filters('betterdocs_kb_terms_object', $terms_object));

    if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
        $class = ['betterdocs-categories-wrap betterdocs-category-box betterdocs-category-box-pro pro-layout-3 layout-flex betterdocs-list-view ash-bg'];

        if ($get_args['disable_customizer_style'] == false) {
            $class[] = 'multiple-kb';
        }

        echo '<div class="' . implode(' ', $class) . '">';

        // display category grid by order
        foreach ($taxonomy_objects as $term) {
            $term_id = $term->term_id;

            if ($term->count != 0) {
                $term_permalink = BetterDocs_Helper::term_permalink('knowledge_base', $term->slug);

                echo '<a href="' . esc_url($term_permalink) . '" class="docs-single-cat-wrap" id="mkb-id-'.$term_id.'">';
                $cat_icon_id = get_term_meta($term_id, 'knowledge_base_image-id', true);
                if ($cat_icon_id) {
                    echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
                } else {
                    echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
                }
                echo '<div class="title-count">';
                echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';
                $cat_desc = get_theme_mod('betterdocs_mkb_desc');
                if ($cat_desc == true) {
                    echo '<p class="cat-description">' . $term->description . '</p>';
                }
                if ($post_count == 1) {
                    if ($term->count == 1) {
                        echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
                    } else {
                        echo wp_sprintf('<span>%s %s</span>', $term->count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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
            'terms_orderby' => '',
            'terms_order' => '',
			'multiple_knowledge_base' => false,
			'disable_customizer_style' => false,
            'title_tag' => 'h2'
		),
		$atts
	);
    $nested_subcategory = ($nested_subcategory == 1 && $get_args['nested_subcategory'] == '') || ($get_args['nested_subcategory'] == true && $get_args['nested_subcategory'] != "false");
	$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);
	
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
			$term_id = $term->term_id;
			$term_slug = $term->slug;
			$count = $term->count;
			$get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
			$term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
			if ('0' == ($term->count && $term->parent)) {
				$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);
				echo '<a href="' . esc_url($term_permalink) . '" class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-box" id="cat-id-'.$term_id.'">';
				echo '<div class="docs-cat-list-2-box-content">';
				$term_id = $term->term_id;
				$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);
				if ($cat_icon_id) {
					echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
				} else {
					echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
				}

				echo '<div class="title-count">';
				echo '<'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'>';

				if ($post_count == 1) {
					if ($term->count == 1) {
						echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs-pro'));
					} else {
						echo wp_sprintf('<span>%s %s</span>', $term_count, ($count_text) ? $count_text : __('articles', 'betterdocs-pro'));
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
			echo '<div id = "cat-id-'.$term_id.'"class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-items">
				<div class="docs-cat-title-wrap">
					<a href="' . esc_url($term_permalink) . '"><'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. Betterdocs_Pro::validate_html_tag($get_args['title_tag']) .'></a>
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
					echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' . esc_html(get_the_title()) . '</a></li>';
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
                    $get_args['terms_orderby'],
                    $get_args['terms_order'],
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
	<script id="hs-loader">
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