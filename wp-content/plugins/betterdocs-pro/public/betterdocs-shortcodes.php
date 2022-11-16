<?php

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
            'disable_customizer_style' => 'false',
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

        if ($get_args['disable_customizer_style'] == 'false') {
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
                    $category_objects = BetterDocs_Helper::taxonomy_object(true, '', $get_args['terms_order'], $get_args['terms_orderby'], $kb->slug, $nested_subcategory);
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
                                                echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' .  wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a></li>';
                                            endwhile;
                                            echo '</ul>';
                                        endif;
                                        wp_reset_query();

                                        // Sub category query
                                        if ($nested_subcategory == true) {
                                            nested_category_list(
                                                $term_id,
                                                true,
                                                array(''),
                                                'docs',
												$get_args['orderby'],
												$get_args['order'],
                                                $get_args['terms_orderby'],
                                                $get_args['terms_order'],
                                                '',
                                                $kb->slug,
												$posts_per_grid
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
            echo '<li>'.$icon.'<a href="' . get_the_permalink() . '">' .  wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a></li>';
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
            'disable_customizer_style' => 'false',
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

        if ($get_args['disable_customizer_style'] == 'false') {
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
			if ($term_count > 0) {
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
			$count = $term->count;
			$get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
			$term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
			if( $term_count > 0 ) {
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
						echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' . wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a></li>';
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
		}
		echo '</div>';
	endif;
	return ob_get_clean();
}

add_shortcode('betterdocs_category_grid_list', 'betterdocs_category_grid_list');
function betterdocs_category_grid_list($atts,  $content = null) {
	do_action( 'betterdocs_before_shortcode_load' );

	ob_start();

	$get_args = shortcode_atts(
		array(
			'posts_per_grid' 	 	=> 5,
			'nested_subcategory' 	=> BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? 'true' : 'false',
			'multiple_kb'    	 	=> BetterDocs_DB::get_settings('multiple_kb') != 'off' ? 'true' : 'false',
			'terms_order'		 	=> BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'ASC' : BetterDocs_DB::get_settings('terms_order'),
			'terms_orderby'		 	=> BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'name' : BetterDocs_DB::get_settings('terms_orderby'),
			'orderby'		 		=> BetterDocs_DB::get_settings('alphabetically_order_post'),
			'order'		 			=> BetterDocs_DB::get_settings('docs_order'),
			'explore_more_text'	 	=> BetterDocs_DB::get_settings('exploremore_btn_txt') ? BetterDocs_DB::get_settings('exploremore_btn_txt') : __('Explore More', 'betterdocs-pro'),
			'show_count_icon'	 	=> 'true',
			'show_term_image'	 	=> 'true',
			'show_term_description' => 'true',
			'term_title_tag'	 	=> 'h2'
		), $atts 
	);
	
	$nested_subcategory    = $get_args['nested_subcategory'] === 'true' ? true : false;
	$multiple_kb		   = $get_args['multiple_kb'] === 'true' ? true : false;
	$terms_order		   = (string) $get_args['terms_order'];
	$terms_orderby		   = (string) $get_args['terms_orderby'];
	$docs_orderby		   = (string) $get_args['orderby'];
	$docs_order			   = (string) $get_args['order'];
	$explore_more		   = (string) $get_args['explore_more_text'];
	$docs_per_category	   = (int) $get_args['posts_per_grid'];
	$show_count_icon	   = $get_args['show_count_icon'] === 'true' ? true : false;
	$show_term_image	   = $get_args['show_term_image'] === 'true' ? true : false;
	$show_term_description = $get_args['show_term_description'] === 'true' ? true : false;
	$term_title_tag		   = (string) $get_args['term_title_tag'];
	$current_kb_slug	   = class_exists('BetterDocs_Multiple_Kb') ? BetterDocs_Multiple_Kb::kb_slug() : '';

	$terms_objects  = class_exists('BetterDocs_Helper') && ! is_wp_error( BetterDocs_Helper::taxonomy_object( $multiple_kb, '', $terms_order, $terms_orderby, $current_kb_slug, $nested_subcategory ) ) ? BetterDocs_Helper::taxonomy_object( $multiple_kb, '', $terms_order, $terms_orderby, $current_kb_slug, $nested_subcategory ) : '';

	foreach( $terms_objects as $term_object ) {
		if( $term_object->count > 0 ) {
			$term_id 		  = isset( $term_object->term_id ) ? $term_object->term_id : '';
			$term_title 	  = isset( $term_object->name ) ? $term_object->name : '';
			$term_slug		  = isset( $term_object->slug ) ? $term_object->slug : '';
			$term_description = isset( $term_object->description ) && $show_term_description ? $term_object->description : '';
			$term_count	      = isset( $term_object->count ) ? $term_object->count : '';
			$term_doc_count	  = betterdocs_get_postcount( $term_count, $term_id, $nested_subcategory );
			$term_doc_count	  = apply_filters('betterdocs_postcount', $term_doc_count, $multiple_kb, $term_id, $term_slug, $term_count, $nested_subcategory);

			if( $term_doc_count == 0 ) {
				continue;
			}

			$term_permalink   = BetterDocs_Helper::term_permalink('doc_category', $term_slug);
			$term_icon_id 	  = get_term_meta( $term_id, 'doc_category_thumb-id', true ) ?  get_term_meta( $term_id, 'doc_category_thumb-id', true ) : '';
			$term_icon_url    = $term_icon_id ? wp_get_attachment_image( $term_icon_id, 'betterdocs-category-thumb' ) : '<img src="' . BETTERDOCS_PRO_URL. 'admin/assets/img/cat-grid-2.png">';
			$term_icon_url    = $show_term_image ? $term_icon_url : '';
			$term_count_icon  = $show_count_icon ? '<span class="betterdocs-term-count">'.$term_doc_count.'</span>' : '';
			$list_args 		  = BetterDocs_Helper::list_query_arg('docs', $multiple_kb, $term_slug, $docs_per_category, $docs_orderby, $docs_order);
			$args 			  = apply_filters('betterdocs_articles_args', $list_args, $term_id);
			$doc_query		  = new WP_Query( $args );
			$doc_list         = '';

			while( $doc_query->have_posts() ) {
				$doc_query->the_post();
				$doc_list .= '<li id="'.esc_attr( get_the_ID() ).'"><a class="betterdocs-article" href="'.get_the_permalink().'"><p>'.wp_kses( get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML ).'</p><svg class="doc-list-arrow" width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.85848 5.53176L1.51833 0.191706C1.39482 0.068097 1.22994 0 1.05414 0C0.878334 0 0.713458 0.068097 0.589947 0.191706L0.196681 0.584873C-0.059219 0.841066 -0.059219 1.25745 0.196681 1.51326L4.68094 5.99751L0.191706 10.4867C0.0681946 10.6104 0 10.7751 0 10.9508C0 11.1267 0.0681946 11.2915 0.191706 11.4152L0.584971 11.8083C0.70858 11.9319 0.873359 12 1.04916 12C1.22497 12 1.38984 11.9319 1.51335 11.8083L6.85848 6.46336C6.98229 6.33936 7.05028 6.1738 7.04989 5.9978C7.05028 5.82112 6.98229 5.65566 6.85848 5.53176Z" fill="#15063F"/></svg>';
			}

			$doc_list .='<li class="betterdocs-explore-more"><a class="betterdocs-term-explore-more" href="'.$term_permalink.'"><p>'.$explore_more.'</p><svg class="doc-explore-more" width="15" height="12" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.1938 5.43957L8.98464 0.230318C8.83594 0.0816199 8.63775 0 8.42643 0C8.21488 0 8.01681 0.0817371 7.86811 0.230318L7.39516 0.703385C7.24658 0.851849 7.16473 1.05015 7.16473 1.26159C7.16473 1.47291 7.24658 1.6779 7.39516 1.82636L10.4341 4.87198H0.779259C0.343953 4.87198 0 5.21277 0 5.64819V6.31698C0 6.7524 0.343953 7.12755 0.779259 7.12755H10.4686L7.39528 10.1902C7.2467 10.3389 7.16484 10.5318 7.16484 10.7432C7.16484 10.9544 7.2467 11.1501 7.39528 11.2987L7.86823 11.7703C8.01693 11.919 8.215 12 8.42655 12C8.63787 12 8.83606 11.9179 8.98476 11.7692L14.1939 6.56008C14.3429 6.41091 14.4249 6.21179 14.4243 6.00012C14.4248 5.78774 14.3429 5.5885 14.1938 5.43957Z" fill="#523BE9"/></svg></a></li>';

			echo	'<div class="betterdocs-category-grid-layout-6">';
			echo		'<div class="betterdocs-term-img">';
			echo			$term_icon_url;
			echo		'</div>';

			echo		'<div class="betterdocs-term-info">';
			echo			'<div class="betterdocs-term-title-count">';
			echo				'<'.$term_title_tag.' class="betterdocs-term-title">'.$term_title.'</'.$term_title_tag.'>';
			echo				$term_count_icon;
			echo			'</div>';
			echo			'<p class="betterdocs-term-description">'.$term_description.'</p>';
			echo            '<ul class="betterdocs-doc-list">';
			echo            $doc_list;
			echo			'</ul>';
			echo		'</div>';
			echo	'</div>';
		}
	}

	return ob_get_clean();
}

add_shortcode('betterdocs_sidebar_list', 'betterdocs_sidebar_list');
function betterdocs_sidebar_list($atts,  $content = null) {
	do_action( 'betterdocs_before_shortcode_load' );

	ob_start();

	$get_args = shortcode_atts(
		array(
			'nested_subcategory' => BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? 'true' : 'false',
			'multiple_knowledge_base' => BetterDocs_DB::get_settings('multiple_kb') != 'off' ? 'true' : 'false',
			'terms_order'		 => BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'ASC' : BetterDocs_DB::get_settings('terms_order'),
			'terms_orderby'		 => BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'name' : BetterDocs_DB::get_settings('terms_orderby'),
			'orderby'		     => BetterDocs_DB::get_settings('alphabetically_order_post'),
			'order'		 		 =>	BetterDocs_DB::get_settings('docs_order'),
			'terms_title_tag'	 => 'h2'
		), $atts 
	);

	$ancestors  		= array();
	$nested_subcategory = $get_args['nested_subcategory'] === 'true' ? true : false;
	$multiple_kb		= $get_args['multiple_knowledge_base'] === 'true' ? true : false;
	$terms_order		= (string) $get_args['terms_order'];
	$terms_orderby		= (string) $get_args['terms_orderby'];
	$docs_orderby		= (string) $get_args['orderby'];
	$docs_order			= (string) $get_args['order'];
	$terms_title_tag    = (string) $get_args['terms_title_tag'];
	$terms_objects 		= class_exists('BetterDocs_Helper') && ! is_wp_error( BetterDocs_Helper::taxonomy_object( $multiple_kb, '', $terms_order, $terms_orderby, '', $nested_subcategory ) ) ? BetterDocs_Helper::taxonomy_object( $multiple_kb, '', $terms_order, $terms_orderby, '', $nested_subcategory ) : '';
	$term_list     		= wp_get_post_terms( get_the_ID(), 'doc_category', array( "fields" => "all" ) );

	if ( is_single() && ! empty( $term_list ) ) {
		$category_id = array_column($term_list, 'term_id');
		$cat_index   = isset( $category_id[0] ) ? $category_id[0] : '';
		$ancestors   = get_ancestors( $cat_index, 'doc_category' );
	} 

	$current_doc_id	  =  get_the_ID();
	$current_doc_term = isset( get_the_terms( $current_doc_id, 'doc_category')[0]->term_id ) ? get_the_terms( get_the_ID(), 'doc_category')[0]->term_id : '';

	echo '<ul class="betterdocs-sidebar-layout-6">';

	foreach( $terms_objects as $term_object ) {	
		$term_title 	  = isset( $term_object->name ) ? $term_object->name : '';
		$term_slug        = isset( $term_object->slug ) ? $term_object->slug : '';
		$term_count       = isset( $term_object->count ) ? $term_object->count : '';
		$term_doc_count	  = betterdocs_get_postcount( $term_count, $term_object->term_id, $nested_subcategory );
		$term_doc_count	  = apply_filters('betterdocs_postcount', $term_doc_count, $multiple_kb, $term_object->term_id, $term_slug, $term_count, $nested_subcategory);

		if( $term_doc_count > 0 ) {
			$current_category = is_single() && ( $current_doc_term == $term_object->term_id || ( $nested_subcategory == 1 && in_array( $term_object->term_id, $ancestors ) ) ) ? ' current-term' : ( BetterDocs_Helper::get_tax() == 'doc_category' && $current_doc_term == $term_object->term_id ? ' current-term' : '' );
			$current_doc_list = is_single() && ( $current_doc_term == $term_object->term_id || ( $nested_subcategory == 1 && in_array( $term_object->term_id, $ancestors ) ) ) ? ' current-doc-list' : ( BetterDocs_Helper::get_tax() == 'doc_category' && $current_doc_term == $term_object->term_id ? ' current-doc-list' : '' );
			$list_args 		  = BetterDocs_Helper::list_query_arg('docs', $multiple_kb, $term_slug, '-1', $docs_orderby, $docs_order);
			$args 	   		  = apply_filters('betterdocs_articles_args', $list_args, $term_object->term_id);
			$post_query 	  = new WP_Query( $args );

			$term_doc_list	= '';

			if ($post_query->have_posts()) {
				while ( $post_query->have_posts() ) {
					$post_query->the_post();
					$current_active_doc = $current_doc_id === get_the_ID() && BetterDocs_Helper::get_tax() != 'doc_category' ? 'active-doc' : '';
					$term_doc_list .= '<li><a class="'.$current_active_doc.'" href="'.esc_url( get_the_permalink() ).'">' .  wp_kses( get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML ). '</a></li>';
				}
				wp_reset_query();
			}

			echo 	'<li>';
			echo       '<div class="betterdocs-sidebar-category-title-count'.$current_category.'">';
			echo 			'<a href="#"><'.$terms_title_tag.' class="betterdocs-sidebar-term-heading">'.esc_attr( $term_title ).'</'.$terms_title_tag.'></a>';
			echo			'<span class="betterdocs-sidebar-term-count">'.$term_doc_count.'</span>';
			echo        '</div>';
			echo 		'<div class="doc-list'.$current_doc_list.'">';
			echo 			'<ul>';
			echo 			 	$term_doc_list;
			echo 			'</ul>';
			echo			$nested_subcategory ? sidebar_nested_category_list($term_object->term_id, $multiple_kb, $current_doc_term, 'docs', 'name', 'asc', $terms_orderby, $terms_order, $current_doc_id) : '';
			echo 		'</div>';
			echo    '</li>';
		}
	}

	echo  '</ul>';

	return ob_get_clean();
}

function sidebar_nested_category_list( $term_id, $multiple_kb, $category_id, $post_type, $docs_orderby, $docs_order, $terms_orderby, $terms_order, $page_cat, $kb_slug='', $nested_posts_num = -1) {
	$sub_categories = BetterDocs_Helper::child_taxonomy_terms($term_id, $multiple_kb, $terms_orderby, $terms_order, $kb_slug);
	if ($sub_categories) {
		foreach ($sub_categories as $sub_category) {
			if ( is_single() && $sub_category->term_id === $category_id ) {
				$subcat_class = 'nested-docs-sub-cat current-sub-cat';
			} else {
				$subcat_class = 'nested-docs-sub-cat';
			}

			echo '<span class="nested-docs-sub-cat-title">
				' . BetterDocs_Helper::arrow_right_svg() . '
				' . BetterDocs_Helper::arrow_down_svg() . '
				<a href="#">' . $sub_category->name . '</a></span>';

			echo '<ul class="' . esc_attr($subcat_class) . '">';
			$sub_args = BetterDocs_Helper::list_query_arg($post_type, $multiple_kb, $sub_category->slug, $nested_posts_num, $docs_orderby, $docs_order, $kb_slug);
			$sub_args = apply_filters('betterdocs_articles_args', $sub_args, $sub_category->term_id);
			$sub_post_query = new WP_Query($sub_args);

			if ($sub_post_query->have_posts()) {
				while ( $sub_post_query->have_posts() ) {
					$sub_post_query->the_post();
					$sub_attr = ['href="' . get_the_permalink() . '"'];
					if ($page_cat === get_the_ID() && BetterDocs_Helper::get_tax() != 'doc_category') {
						$sub_attr[] = 'class="active-doc"';
					}
					echo '<li class="doc-sub-list">' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $sub_attr) . '>' . get_the_title() . '</a></li>';
				}
			}
			wp_reset_query();
			sidebar_nested_category_list($sub_category->term_id, $multiple_kb, $category_id, $post_type, $docs_orderby, $docs_order, $terms_orderby, $terms_order, $page_cat, $kb_slug);
			echo '</ul>';
		}
	}
}

add_action('wp_ajax_nopriv_load_more_terms', 'betterdocs_load_terms');
add_action('wp_ajax_load_more_terms', 'betterdocs_load_terms');

function betterdocs_load_terms() {
	if ( isset( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'show-more-catergories' ) ) {
		die( 'Cheating&huh?' );
	}
	$tax_page           = isset( $_GET['tax_page'] ) ? $_GET['tax_page'] : '';
	$current_items 		= isset( $_GET['current_terms'] ) ? $_GET['current_terms'] : $_GET['current_terms'];
	$current_term_id	= isset( $_GET['current_term_id'] ) ? $_GET['current_term_id'] : '';
	$kb_slug			= isset( $_GET['kb_slug'] ) ? $_GET['kb_slug'] : '';
	$no_of_terms   		= 4;
	$multiple_kb		= BetterDocs_DB::get_settings('multiple_kb') != 'off' ? true : false;
	$terms_order		= BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'ASC' : BetterDocs_DB::get_settings('terms_order');
	$terms_orderby		= BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'name' : BetterDocs_DB::get_settings('terms_orderby');
	$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? true : false;
	$terms         		= BetterDocs_Helper::get_doc_terms( $multiple_kb, $terms_order, $terms_orderby, $tax_page, $current_term_id, $nested_subcategory, $kb_slug );
	$terms				= array_slice( $terms, $current_items, $no_of_terms );
	$other_terms_markup = related_categories( $terms, $nested_subcategory, $multiple_kb );

	wp_send_json_success( $other_terms_markup );

	die();
}

add_shortcode( 'betterdocs_related_categories','betterdocs_related_categories' );
function betterdocs_related_categories($atts,  $content = null) {
	do_action( 'betterdocs_before_shortcode_load' );

	ob_start();

	$get_args = shortcode_atts(
		array(
			'terms_order'		 => BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'ASC' : BetterDocs_DB::get_settings('terms_order'),
			'terms_orderby'		 => BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'name' : BetterDocs_DB::get_settings('terms_orderby'),
			'multiple_kb'		 => BetterDocs_DB::get_settings('multiple_kb') != 'off' ? 'true' : 'false',
			'nested_subcategory' => BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? 'true' : 'false',
			'heading'			 => __('Other Categories', 'betterdocs-pro'),
			'load_more_text'	 => __('Load More','betterdocs-pro'),
			'terms_title_tag'	 => 'h2'
		), $atts 
	);

	$nested_subcategory = $get_args['nested_subcategory'] === 'true' ? true : false;
	$terms_order		= (string) $get_args['terms_order'];
	$terms_orderby		= (string) $get_args['terms_orderby'];
	$terms_title_tag    = (string) $get_args['terms_title_tag'];
	$heading 			= (string) $get_args['heading'];
	$load_more_text		= (string) $get_args['load_more_text'];
	$multiple_kb		= $get_args['multiple_kb'] === 'true' ? true : false;
	
	$current_term_id	 = get_queried_object() != null ? get_queried_object()->term_id : '';
	$terms_objects       = class_exists('BetterDocs_Helper') && ! is_wp_error( BetterDocs_Helper::get_doc_terms( $multiple_kb, $terms_order, $terms_orderby, BetterDocs_Helper::get_tax(), $current_term_id, $nested_subcategory ) ) ? array_slice( BetterDocs_Helper::get_doc_terms(  $multiple_kb, $terms_order, $terms_orderby, BetterDocs_Helper::get_tax(), $current_term_id, $nested_subcategory ), 0, 4 ) : '';
	$terms_objects_count = count( BetterDocs_Helper::get_doc_terms( $multiple_kb, $terms_order, $terms_orderby, BetterDocs_Helper::get_tax(), $current_term_id, $nested_subcategory ) );
	$other_terms_markup  = related_categories( $terms_objects, $nested_subcategory, $multiple_kb, $terms_title_tag );

	$loader = '<svg class="betterdocs-load-more-loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: 0; background: none; shape-rendering: auto;" width="25px" height="25px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(78,50)"><g transform="rotate(0)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="1"><animateTransform attributeName="transform" type="scale" begin="-0.8663366336633663s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.8663366336633663s"></animate></circle></g></g><g transform="translate(69.79898987322333,69.79898987322332)"><g transform="rotate(45)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.875"><animateTransform attributeName="transform" type="scale" begin="-0.7425742574257426s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.7425742574257426s"></animate></circle></g></g><g transform="translate(50,78)"><g transform="rotate(90)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.75"><animateTransform attributeName="transform" type="scale" begin="-0.6188118811881188s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.6188118811881188s"></animate></circle></g></g><g transform="translate(30.201010126776673,69.79898987322333)"><g transform="rotate(135)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.625"><animateTransform attributeName="transform" type="scale" begin="-0.49504950495049505s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.49504950495049505s"></animate></circle></g></g><g transform="translate(22,50)"><g transform="rotate(180)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.5"><animateTransform attributeName="transform" type="scale" begin="-0.3712871287128713s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.3712871287128713s"></animate></circle></g></g><g transform="translate(30.201010126776666,30.201010126776673)"><g transform="rotate(225)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.375"><animateTransform attributeName="transform" type="scale" begin="-0.24752475247524752s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.24752475247524752s"></animate></circle></g></g><g transform="translate(49.99999999999999,22)"><g transform="rotate(270)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.25"><animateTransform attributeName="transform" type="scale" begin="-0.12376237623762376s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="-0.12376237623762376s"></animate></circle></g></g><g transform="translate(69.79898987322332,30.201010126776666)"><g transform="rotate(315)"><circle cx="0" cy="0" r="6" fill="#ccbaff" fill-opacity="0.125"><animateTransform attributeName="transform" type="scale" begin="0s" values="1.5 1.5;1 1" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="0.9900990099009901s" repeatCount="indefinite" values="1;0" begin="0s"></animate></circle></g></g></svg>';
	echo empty( $terms_objects ) ? '<h2 class="betterdocs-related-doc-head">'.__('No Other Categories', 'betterdocs-pro').'</h2>' : '<h2 class="betterdocs-related-doc-head">'.$heading.'</h2>';
	echo '<div class="betterdocs-related-doc-row">';
	echo $other_terms_markup;	   
	echo '</div>';
	echo '<div class="betterdocs-show-more-terms">';
	echo ! empty( $terms_objects ) && $terms_objects_count > 4 ? '<button class="betterdocs-load-more-button" type="button"><p class="load-more-text">'.$load_more_text.'</p>'.$loader.'</button>' : '';
	echo '</div>';

	return ob_get_clean();
}

/**
 * This function creates related categories markup.
 * 
 * @param array $terms_objects
 * @param bool $nested_subcategory
 * @param bool $multiple_kb
 * 
 * @return string
 */
function related_categories( $terms_objects, $nested_subcategory, $multiple_kb, $terms_title_tag = 'h2' ) {
	$term_list = '';

	foreach( $terms_objects as $term_object ) {
			$term_title 	  			= isset( $term_object->name ) ? $term_object->name : '';
			$term_slug        			= isset( $term_object->slug ) ? $term_object->slug : '';
			$term_permalink   			= BetterDocs_Helper::term_permalink('doc_category', $term_slug);
			$current_category_icon_id 	= get_term_meta( $term_object->term_id, 'doc_category_thumb-id', true ) ?  get_term_meta( $term_object->term_id, 'doc_category_thumb-id', true ) : '';
			$current_category_icon_url  = $current_category_icon_id ? wp_get_attachment_image( $current_category_icon_id, array('340', '282') ) : '<img src="' . BETTERDOCS_PRO_URL. 'admin/assets/img/cat-grid-3.png">';
			$term_description 			= $term_object->description != '' ? '<p class="betterdocs-related-term-desc">'.wp_trim_words( $term_object->description, 10 ).'</p>' : '';
			$term_count       			= isset( $term_object->count ) ? $term_object->count : '';
			$kb_slug         			= $multiple_kb && ! empty( get_term_meta( $term_object->term_id,'doc_category_knowledge_base', true )[0] ) ? get_term_meta( $term_object->term_id,'doc_category_knowledge_base', true )[0] : '';
            $term_doc_count	  			= betterdocs_get_postcount( $term_count, $term_object->term_id, $nested_subcategory );
            $term_doc_count	  			= apply_filters('betterdocs_postcount', $term_doc_count, $multiple_kb, $term_object->term_id, $term_slug, $term_count, $nested_subcategory,	$kb_slug ); 
			$term_list 		 		   .= '<div class="betterdocs-related-category">';
			$term_list				   .= '<div class="betterdocs-related-category-wrap">';
			$term_list				   .= '<div class="betterdocs-related-category-img-wrap">';
			$term_list		 		   .= $current_category_icon_url;
			$term_list				   .= '</div>';
			$term_list				   .= '<div class="betterdocs-related-category-info-wrap">';
			$term_list		 		   .= '<a href="'.$term_permalink.'" class="betterdocs-related-info">';
			$term_list       		   .= '<'.$terms_title_tag.' class="betterdocs-related-term-text">'.$term_title.'</'.$terms_title_tag.'>';
			$term_list		 		   .= '<span class="betterdocs-related-term-count">'.$term_doc_count.'</span>';
			$term_list		 		   .= '</a>';
			$term_list		 		   .= $term_description;
			$term_list				   .= '</div>';
			$term_list				   .= '</div>';
			$term_list		 		   .= '</div>';
	}

	return $term_list;
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