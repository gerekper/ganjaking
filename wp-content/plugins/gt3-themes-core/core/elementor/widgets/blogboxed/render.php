<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogBoxed $widget */

$settings = array(
	'content_cut' => 'yes',
	'meta_author' => 'yes',
	'meta_comments' => '',
	'meta_categories' => '',
	'meta_date' => 'yes',
	'border_box' => 'yes',
	'post_content' => '',
	'items_per_line' => '3',
	'items_per_line_type2' => '2',
	'module_type' => 'type1',
	'image_position' => 'right',
	'meta_position' => 'before_title',
	'post_featured_bg' => '',
	'post_btn_link' => '',
	'post_btn_link_title' => esc_html__('Read More', 'gt3_themes_core'),
	'image_optimization' => ''
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$items_perline = $settings['items_per_line'];
$post_content_var = $settings['post_content'];

if ($settings['module_type'] == 'type2') {
	$items_perline = $settings['items_per_line_type2'];
	$post_content_var = 'yes';
}

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_module_blogboxed',
	'items'.$items_perline,
	(bool)$post_content_var ? 'post_content_front_visible' : '',
	'module_'.$settings['module_type'],
	$settings['module_type'] == 'type2' ? 'image_position_'.$settings['image_position'] : '',
));

global $paged;
if(empty($paged)) {
	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_raw = $settings['query'];
$query_args['paged'] = $paged;
$query   = new WP_Query($query_args);


$settings['post_btn_link_title'] = (!empty($settings['post_btn_link_title']) ? esc_html__($settings['post_btn_link_title']) : esc_html__('Read More', 'gt3_themes_core'));

$symbol_count = $settings['symbol_count']['size'];

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
		if($query->have_posts()) {

			echo '<div class="blogboxed_grid">';

			while($query->have_posts()) {

				$post_date = $post_category_compile = $post_comments = $border_class = $featured_bg = $post_btn_link = '';

				$query->the_post();

				$comments_num = get_comments_number(get_the_ID());

				if($comments_num == 1) {
					$comments_text = esc_html__('comment', 'gt3_themes_core');
				} else {
					$comments_text = esc_html__('comments', 'gt3_themes_core');
				}

				// Categories
				$item_class  = '';
				$categories = get_the_category();
				if (!empty($categories)) {
					foreach($categories as $category) {
						$item_class .= $category->slug;
					}
				}
				if($settings['meta_categories'] != '' && !empty($categories)) {
					$post_category_compile = '<span class="post_category">';
					$post_category_compile .= get_the_category_list(' ','');
					$post_category_compile .= '</span>';
				}else{
					$post_category_compile = '';
				}

				if(!empty($settings['meta_date'])) {
					$post_date = '<span class="post_date">'.esc_html(get_the_time(get_option('date_format'))).'</span>';
				}

				if(!empty($settings['meta_comments']) && (int)get_comments_number(get_the_ID()) != 0) {
					$post_comments = '<span class="post_comments"><a href="'.esc_url(get_comments_link()).'" title="'.esc_html(get_comments_number(get_the_ID())).' '.$comments_text.'">'.esc_html(get_comments_number(get_the_ID())).' '.$comments_text.'</a></span>';
				}

				// Post meta
				$post_meta = $post_date.$post_category_compile.$post_comments;

				$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

				if (!$featured_image) $featured_image = array(
					0 => '',
				);

				ob_start();

				if(has_excerpt()) {
					the_excerpt();
				} else {
					the_content();
				}
				$post_excerpt = ob_get_clean();

				if($settings['content_cut'] == 'yes') {
					$post_excerpt              = preg_replace('~\[[^\]]+\]~', '', $post_excerpt);
					$post_excerpt_without_tags = strip_tags($post_excerpt);
					$post_descr                = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");
				} else {
					$post_descr = $post_excerpt;
				}

				$post_title = get_the_title();

				if(!empty($settings['post_btn_link'])) {
					$post_btn_link = '<div class="gt3_module_button_list"><a href="'. esc_url(get_permalink()) .'">'. $settings['post_btn_link_title'] .'</a></div>';
				}

				if (strlen( $post_descr ) > 0) {
					$center_mode = '';
				} else {
					$center_mode = 'gt3_center_mode';
				}

				echo '<div class="boxed_block_item">
					<div class="item_wrapper ' . esc_attr($center_mode) . '">';
						if((bool)$settings['post_featured_bg'] && strlen($featured_image[0]) > 0 || $settings['border_box'] == '') {
							$border_class = 'without_bordered';
						}

						if((bool)$settings['post_featured_bg'] && strlen($featured_image[0]) > 0) {
							$thumb_id = get_post_thumbnail_id(get_the_ID());
							$ratio = 1;

							if ($items_perline == "4") {
								$thumb_width = 480;
							} else if ($items_perline == "3") {
								$thumb_width = 640;
							} else if ($items_perline == "2") {
								$thumb_width = 960;
								$ratio = 0.6;
							} else {
								$thumb_width = 1170;
								$ratio = 0.3;
							}

							if ($settings['module_type'] == 'type2') {
								$thumb_width = 700;
								$ratio = 1;
							}

							$image_optimization_width = $settings['image_optimization_width']['size'];
							if((bool)$settings['image_optimization'] && !empty($image_optimization_width)) {
								$thumb_width = $image_optimization_width;
							}

							$featured_image_url = aq_resize($featured_image[0], $thumb_width, $thumb_width*$ratio, true, true, true);
							$featured_bg = 'style="background-image: url('.esc_url($featured_image_url).');"';

							$item_img_class = 'has_img_block';

							$item_img_wrap_start = $item_img_wrap_end = $item_img_wrap_link = '';
							if($settings['module_type'] == 'type2') {
								$item_img_wrap_start = '<div class="blogboxed_img_wrapper">';
								$item_img_wrap_end = '</div>';
								$item_img_wrap_link = '<a href="'.esc_url( get_permalink() ).'" title="'.esc_html( $post_title ).'">'.esc_html( $post_title ).'</a>';
							}

							echo $item_img_wrap_start.'<div class="blogboxed_img_block" '.$featured_bg.'></div>'.$item_img_wrap_link.$item_img_wrap_end;
						} else {
							$item_img_class = 'without_img_block';
						}

						echo'<div class="blogboxed_content '.esc_attr($border_class).' '.esc_attr($item_img_class).'">';

							echo wp_kses_post( apply_filters( 'gt3/core/render/blogboxed/block_wrap_start', '' ) );

							$listing_title = strlen( $post_title ) > 0 ? '<h2 class="blog_post_title"><a href="'.esc_url( get_permalink() ).'">'.esc_html( $post_title ).'</a></h2>' : '';

							if (isset($settings['meta_position']) && $settings['meta_position'] == 'after_title') {
								echo $listing_title;
							}

							$listing_meta = (strlen( $post_meta )) ? '<div class="listing_meta">'.$post_meta.'</div>' : '';
							echo $listing_meta;

							if (isset($settings['meta_position']) && $settings['meta_position'] == 'before_title') {
								echo $listing_title;
							}

							echo '<div class="blogboxed_info_box">';

							if(!empty($settings['meta_author'])) {
								echo '<div class="blogboxed_author"><a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">' . esc_html(get_the_author_meta('display_name')).'</a></div>';
							}

							$listing_descr = strlen( $post_descr ) > 0 ? '<div class="blogboxed_description">'.$post_descr.'</div>' : '';
							echo $listing_descr;

							$post_btn_link = apply_filters( 'gt3/core/render/blogboxed/post_links_block', $post_btn_link);
							echo '<div class="clear"></div>'.$post_btn_link.'<div class="clear"></div>';
							echo '</div>';

							echo wp_kses_post( apply_filters( 'gt3/core/render/blogboxed/block_wrap_end', '' ) );

						echo '</div>
					</div>
				</div>';
			} //endwhile
			wp_reset_postdata();

			echo '</div>';
		}
		?>
	</div>
<?php
