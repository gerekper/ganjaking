<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogSimple $widget */

$settings = array(
	'content_cut' => '',
	'symbol_count' => '',
	'post_featured_image' => '',
	'pagination_en' => '',
	'post_btn_link' => '',
	'post_btn_link_title' => esc_html__('Read More', 'gt3_themes_core'),

	'nav'           => 'none',
	'autoplay'      => true,
	'autoplay_time' => 4000,
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$blog_masonry = $blog_masonry_item = '';


$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_module_blog_simple',
));
if (isset($settings['arrows_bg_color']) && !empty($settings['arrows_bg_color']) && $settings['arrows_bg_color'] != 'transparent') {
	$widget->add_render_attribute('wrapper', 'class','has_arrows_bg_color');
}

$data = array(
	'fade'          => false,
	'autoplay'      => (bool) $settings['autoplay'],
	'items_per_line' => 1,
	'autoplaySpeed' => intval($settings['autoplay_time']),
	'dots'          => ($settings['nav'] === 'dots') ? true : false,
	'arrows'        => ($settings['nav'] === 'arrows') ? true : false,
	'centerMode'    => false,
	'posts_per_column' => 3,
	'l10n'          => array(
		'prev' => esc_html__('Prev', 'gt3_themes_core'),
		'next' => esc_html__('Next', 'gt3_themes_core'),
	),
);

//$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data));

if ( ! empty( $settings['carousel'] ) ) {
    $widget->add_script_depends('slick');
    $widget->add_style_depends('slick');
    $widget->add_render_attribute( 'wrapper', 'class', 'gt3_carousel-elementor' );
}

global $paged;
if(empty($paged)) {
	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_raw = $settings['query'];
$query_args['paged'] = $paged;
$query   = new WP_Query($query_args);


$query_args['paged'] = $paged;


?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
		if($query->have_posts()) {
			$widget->render_index = 1;

			$settings['post_btn_link_title'] = (!empty($settings['post_btn_link_title']) ? esc_html__($settings['post_btn_link_title']) : esc_html__('Read More', 'gt3_themes_core'));

			if (! empty( $settings['carousel'] ) && ! empty( $settings['posts_per_column'] )) {
				echo '<div class="gt3_blog_simple_item">';
			}

			while($query->have_posts()) {

				$query->the_post();

				$comments_num = get_comments_number(get_the_ID());

				$comments_text = $comments_num == 1 ? esc_html__( 'comment', 'gt3_themes_core' ) : esc_html__( 'comments', 'gt3_themes_core' );
				$permalink = get_permalink();

				$post_author = $post_category_compile = $post_comments = '';

				$categories = get_the_category();
				if(!empty($categories)) {
					$post_category_compile = '<span class="post_category">';
					$post_category_compile .= get_the_category_list(' ','');
					$post_category_compile .= '</span>';
				}

				$post_date = '<span class="post_date"><a href="'. esc_url($permalink) .'">'.esc_html(get_the_time(get_option('date_format'))).'</a></span>';

				$icon_post_user = '';
				$post_author = apply_filters( 'gt3/core/render/blogsimple/post_author', '<span class="post_author"><a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">' . $icon_post_user . esc_html(get_the_author_meta('display_name')).'</a></span>' );

				$packary_comments_text = ' <span class="post_comments_text">'.$comments_text.'</span>';
				$icon_post_comments = '';
				if((int)get_comments_number(get_the_ID()) != 0) {
					$post_comments = apply_filters( 'gt3/core/render/blogsimple/post_comments', '<span class="post_comments"><a href="'.esc_url(get_comments_link()).'" title="'.esc_html(get_comments_number(get_the_ID())).' '.$comments_text.'">'.$icon_post_comments.esc_html(get_comments_number(get_the_ID())).''.$packary_comments_text.'</a></span>' );
				}

				// Post meta
				$meta = apply_filters('gt3/core/render/blogsimple/listing_meta_order', array(
					'date'     => $post_date,
				), array(
					'date'     => $post_date,
					'author'   => $post_author,
					'category' => $post_category_compile,
					'comments' => $post_comments,
				));
				if (!is_array($meta)) {
					$meta = array();
				}
				$post_meta = join('',$meta);

				$featured_image = '';
				if ($settings['post_featured_image'] == 'yes') {
					$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
					if (!empty($featured_image[0])) {
						$featured_image_url = aq_resize($featured_image[0], 160, 160, true, true, true);
					}
				}


				ob_start();
				if(has_excerpt(get_the_ID()) && trim(get_the_excerpt())) {
					the_excerpt();
				} else {
					the_content();
				}
				$post_excerpt = ob_get_clean();

				$width  = '1170';

				$symbol_count = $settings['symbol_count'];
				if (isset($settings['symbol_count']['size']) && strlen($settings['symbol_count']['size'])) {
					$symbol_count = $settings['symbol_count']['size'];
				}

				$post_excerpt              = preg_replace('~\[[^\]]+\]~', '', $post_excerpt);
				$post_excerpt_without_tags = strip_tags($post_excerpt);

				if($settings['content_cut'] == 'yes') {
					$post_descr                = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");
				} else {
					$post_descr = $post_excerpt_without_tags;
				}

				$post_title = get_the_title();

				$key = 'key'.$widget->render_index;
				$widget->add_render_attribute( $key, 'class', array(
					'blog_post_preview',
                    is_sticky() ? 'gt3_sticky_post' : ''
				) );

				echo '<div '.$widget->get_render_attribute_string($key).'>
                        <div class="item_wrapper">
                            <div class="blog_content">';

								if(!empty($settings['post_btn_link'])) {
                                    $post_btn_link = '<div class="gt3_module_button_list"><a href="'. esc_url($permalink) .'">'. $settings['post_btn_link_title'] .'</a></div>';
                                } else {
                                    $post_btn_link = '<div class="gt3_module_button_empty"></div>';
                                }
                                	echo '<div class="gt3_blogsimple_header">';

	                                	$featured_image = !empty($featured_image_url) ? '<div class="gt3_blogsimple_featured_image"><a href="'. esc_url($permalink) .'"><img src="'.$featured_image_url.'" ></a></div>' : '';
	                                	echo wp_kses_post( apply_filters( 'gt3/core/render/blogsimple/featured_image', $featured_image ) );

	                                	$listing_meta = (strlen( $post_meta )) ? '<div class="listing_meta">'.$post_meta.'</div>' : '';
		                                echo wp_kses_post( apply_filters( 'gt3/core/render/blogsimple/listing_meta', $listing_meta ) );

	                                echo '</div>'; // end gt3_blogsimple_header

									$pf_icon = is_sticky() ? '<i class="fa fa-thumb-tack"></i>' : '';
                                    $listing_title = strlen( $post_title ) > 0 ? '<h3 class="blogpost_title">'.$pf_icon.'<a href="'.esc_url( $permalink ).'">'.esc_html( $post_title ).'</a></h3>' : '';
                                    echo wp_kses_post( apply_filters( 'gt3/core/render/blogsimple/listing_title', $listing_title ) );

                                    $listing_btn = apply_filters( 'gt3/core/render/blogsimple/listing_btn', $post_btn_link.'<div class="clear"></div>', $settings);

	                                $listing_descr = strlen( $post_descr ) > 0 ? '<div class="blog_item_description">'.$post_descr.$listing_btn.'</div>' : $listing_btn;

	                                echo wp_kses_post( apply_filters( 'gt3/core/render/blogsimple/listing_descr', $listing_descr ) );

	                                echo '<div class="clear"></div>';

                            echo '</div>
						</div>
					</div>
					';


				$endpost = ((int)$settings['query']['posts_per_page']*$paged < $query->found_posts ? (int)$settings['query']['posts_per_page']*$paged : $query->found_posts);

				if ((int)$settings['query']['posts_per_page'] === -1) {
					$endpost = $query->found_posts;
				}

				if (! empty( $settings['carousel'] ) && ! empty( $settings['posts_per_column'] )) {
					if (($widget->render_index % (int)$settings['posts_per_column']) === 0) {
						if ((int)$widget->render_index !== (int)$endpost) {
							echo '</div>';
							echo '<div class="gt3_blog_simple_item">';
						}

					}
				}

				$widget->render_index++;
			} //endwhile
			if (! empty( $settings['carousel'] ) && ! empty( $settings['posts_per_column'] )) {
				echo '</div>';
			}
			wp_reset_postdata();

			if ((bool)$settings['pagination_en'] && !(bool)$settings['carousel']) {
				echo gt3_get_theme_pagination(5, "", $query->max_num_pages, $paged);
			}
		}
		?>
	</div>
<?php
$widget->print_data_settings($data);

