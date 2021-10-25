<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Blog $widget */

$settings = array(
	'blog_post_listing_content_module' => 'yes',
	'meta_author' => '',
	'meta_comments' => '',
	'meta_categories' => '',
	'meta_date' => '',
	//'share' => '',
	'items_per_line' => '1',
	'spacing_beetween_items' => '30',
	'pagination_en' => '',
	'packery_en' => '',
	'blog_filter' => '',
	'static_info_block' => '',
	'title' => esc_html__('Title', 'gt3_themes_core'),
	'sub_title' => esc_html__('Subtitle', 'gt3_themes_core'),
	'content' => esc_html__('Content', 'gt3_themes_core'),
	'btn_block' => '',
	'btn_title' => esc_html__('Button Title', 'gt3_themes_core'),
	'btn_link'         => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'enable_icon' => 'yes',
	//'element_icon' => '',
	'grid_gap'      => 0,
	'post_btn_link' => '',
	'post_boxed_content' => '',
	'packery_items_per_line' => 'theme_packery',
	'post_btn_link_title' => esc_html__('Read More', 'gt3_themes_core'),
	'meta_position' => 'after_title',
	'items_type_line1_type' => 'type1',
	'thumbs_size' => 'default',
	'post_media_content' => 'yes',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$blog_masonry = $blog_masonry_item = '';

if ((bool)$settings['packery_en']) {
	$settings['items_per_line'] = '';
	$settings['spacing_beetween_items'] = '0';
}

if($settings['items_per_line'] !== '1') {
	$blog_masonry      = 'isotope_blog_items';
	$blog_masonry_item = 'element';
}

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_module_blog',
	'items'.$settings['items_per_line'],
	(bool)$settings['packery_en'] ? 'packery_wrapper' : '',
	'items_'.$settings['items_type_line1_type'],
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


$query_args['paged'] = $paged;

$widget->packery_grids = wp_parse_args(apply_filters( 'gt3/core/render/blog/packery_grid', $widget->packery_grids ),array(
	'lap'  => 4,
	'grid' => 4,
	'elem' => array(
	)
));
$dataSettings = array();

if ((bool)$settings['packery_en']) {
	if ($settings['packery_items_per_line'] !== 'theme_packery'){
		$widget->packery_grids = array(
			'lap'  => intval($settings['packery_items_per_line']),
			'grid' => intval($settings['packery_items_per_line']),
			'elem' => array(
			)
		);
	}
	$dataSettings = array(
		'packery'      => true,
		'packery_grid' => $widget->packery_grids,
		'gap_value'    => esc_attr( intval( $settings['grid_gap'] ) ),
		'gap_unit'     => esc_attr( substr( $settings['grid_gap'], - 1 ) == '%' ? '%' : 'px' ),
	);
} else {
	$dataSettings = array(
		'packery' => false,
	);
}
$widget->print_data_settings($dataSettings);

if(empty($settings['btn_link']['url'])) {
	$settings['btn_link']['url'] = '#';
}
$widget->add_render_attribute('btn_link', 'class', 'static_info_link');
$widget->add_render_attribute('btn_link', 'href', esc_url($settings['btn_link']['url']));

if($settings['btn_link']['is_external']) {
	$widget->add_render_attribute('btn_link', 'target', '_blank');
}

if(!empty($settings['btn_link']['nofollow'])) {
	$widget->add_render_attribute('btn_link', 'rel', 'nofollow');
}

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php if ((bool)$settings['blog_filter']) { ?>
			<div class="isotope-filter container">
				<?php
				echo '<a href="#" class="active" data-filter="*">'.esc_html__('Show All', 'gt3_themes_core').'</a>';
				foreach($widget->get_taxonomy($query_raw['taxonomy']) as $cat_slug) {
					echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'">'.esc_html($cat_slug['name']).'</a>';
				}
				?>
			</div>
		<?php }	?>
		<?php
		if($query->have_posts()) {

			if($settings['items_per_line'] !== '1') {
				echo '<div class="spacing_beetween_items_'.$settings['spacing_beetween_items'].' '.esc_attr($blog_masonry).' isotope">';
			}
			$widget->render_index = 1;

			$settings['post_btn_link_title'] = (!empty($settings['post_btn_link_title']) ? esc_html__($settings['post_btn_link_title']) : esc_html__('Read More', 'gt3_themes_core'));

			while($query->have_posts()) {
				if ((bool)$settings['static_info_block'] && $widget->render_index === 1) {
					$widget->render_index++;
					?>
					<div class="static_info_text_block isotope-item blog_post_preview isotope_item loading element">
						<div class="item_wrapper">
							<div class="item">
								<?php echo( ! empty( $settings['title'] ) ? '<div class="title">'.esc_html( $settings['title'] ).'</div>' : '' ); ?>
								<?php echo( ! empty( $settings['sub_title'] ) ? '<div class="sub_title">'.esc_html( $settings['sub_title'] ).'</div>' : '' ); ?>
								<?php echo( ! empty( $settings['content'] ) ? '<div class="content">'.wp_kses_post( $settings['content'] ).'</div>' : '' ); ?>

								<?php if ( (bool) $settings['btn_block'] ) {
									if ( (bool) $settings['enable_icon'] ) {
										$btn_icon = '<span class="static_info_icon"><i class="fa fa-angle-right"></i></span>';
									} else {
										$btn_icon = '';
									}
									echo '<a '.$widget->get_render_attribute_string( 'btn_link' ).'>'.esc_html( $settings['btn_title'] ).$btn_icon.'</a>';
								}; ?>
							</div>
						</div>
					</div>
				<?php }

				$query->the_post();

				$comments_num = get_comments_number(get_the_ID());

				if($comments_num == 1) {
					$comments_text = esc_html__('comment', 'gt3_themes_core');
				} else {
					$comments_text = esc_html__('comments', 'gt3_themes_core');
				}

				$post_date = $post_author = $post_category_compile = $post_comments = '';

				// Categories
				$item_class  = array();
				$categories = get_the_category();

				if (!empty($categories)) {
					foreach($categories as $category) {
						$item_class[] = $category->slug;
					}
				}
				$item_class = implode(' ',$item_class);
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

				$icon_post_user = '';
				if(!empty($settings['meta_author'])) {
					if ((bool)$settings['packery_en']) {
						$icon_post_user = gt3_svg_icons_name('user');
					}
					$post_author = apply_filters( 'gt3/core/render/blog/post_author', '<span class="post_author"><a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">' . $icon_post_user . esc_html(get_the_author_meta('display_name')).'</a></span>' );
				}

				$packary_comments_text = ' <span class="post_comments_text">'.$comments_text.'</span>';

				if ((bool)$settings['packery_en']) {
					$packary_comments_text = '';
				}

				$icon_post_comments = '';
				if(!empty($settings['meta_comments']) && (int)get_comments_number(get_the_ID()) != 0) {
					if ((bool)$settings['packery_en']) {
						$icon_post_comments = gt3_svg_icons_name('chat');
					}

					$post_comments = apply_filters( 'gt3/core/render/blog/post_comments', '<span class="post_comments"><a href="'.esc_url(get_comments_link()).'" title="'.esc_html(get_comments_number(get_the_ID())).' '.$comments_text.'">'.$icon_post_comments.esc_html(get_comments_number(get_the_ID())).''.$packary_comments_text.'</a></span>' );
				}

				// Post meta
				if ((bool)$settings['packery_en']) {
					$post_meta = $post_date;
					$packery_foot_info = '<div class="packery_foot_info">'. $post_author.$post_comments.'</div>';
					$packery_cats = $post_category_compile;
				} else {
					$meta = apply_filters('gt3/core/render/blog/listing_meta_order', array(
						'date'     => $post_date,
						'author'   => $post_author,
						'category' => $post_category_compile,
						'comments' => $post_comments,
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
					$packery_foot_info = '';
					$packery_cats = '';
				}

				$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

				$pf = get_post_format();
				if(empty($pf)) {
					$pf = "standard";
				}

				ob_start();
				if(has_excerpt(get_the_ID()) && trim(get_the_excerpt())) {
					the_excerpt();
				} else {
					the_content();
				}
				$post_excerpt = ob_get_clean();

				$width  = '1170';
				if($settings['items_per_line'] == '1') {
					$height = wp_kses_post( apply_filters( 'gt3/core/render/blog/media_height', '725' ) );
				} else {
					$height = wp_kses_post( apply_filters( 'gt3/core/render/blog/media_height', '950' ) );
				}

				if ((bool)$settings['post_boxed_content'] && $settings['packery_en'] == '') {
					$height = wp_kses_post( apply_filters( 'gt3/core/render/blog/media_height_boxed', '950' ) );
				}

				if (isset($settings['thumbs_size']) && $settings['thumbs_size'] !== 'default') {
					switch($settings['thumbs_size']){
						case 'large':
							$width = '1024';
							break;
						case 'medium_large':
							$width = '768';
							break;
						case 'medium':
							$width = '300';
							break;
					}
					$height = '';
					$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $settings['thumbs_size']);
				}

				$pf_media = gt3_get_pf_type_output($pf, $width, $height, $featured_image, $settings['items_per_line']);

				$pf = $pf_media['pf'];

				if ((bool)$featured_image[0] && $pf !== 'standard') {
					$symbol_count = apply_filters( 'gt3/core/render/blog/symbol_count_default_image', '380' );
				}else{
					$symbol_count = apply_filters( 'gt3/core/render/blog/symbol_count_default', '380' );
				}

				if($settings['items_per_line'] == '3' || $settings['items_per_line'] == '4') {
					$symbol_count = $symbol_count/3;
				}

				if (isset($settings['symbol_count_descrt']['size']) && strlen($settings['symbol_count_descrt']['size'])) {
					$symbol_count = $settings['symbol_count_descrt']['size'];
				}

				if ( (bool) $settings['packery_en'] ) {
					$symbol_count = apply_filters( 'gt3/core/render/blog/symbol_count', '80' );
					if ( key_exists( $widget->render_index, $widget->packery_grids['elem'] ) ) {
						$symbol_count = apply_filters( 'gt3/core/render/blog/symbol_count_wide', '165' );
					}
				}

				if (strlen($featured_image[0]) > 0 && (bool)$settings['packery_en']) {
					$thumb_id = get_post_thumbnail_id(get_the_ID());
					$thumb_width = 670;
					$ratio = 1;
					$render_index = $widget->render_index;
					$packery_grids = $widget->packery_grids;
					if (!empty($packery_grids['elem'][$render_index]['w'])) {
						$ratio = $packery_grids['elem'][$render_index]['w'];
					}
					$featured_image_url = aq_resize($featured_image[0], $thumb_width*$ratio, $thumb_width, true, true, true);
					$featured_bg = 'style="background-image: url('.esc_url($featured_image_url).');"';

				} else {
					$featured_bg = '';
				}

				if($settings['blog_post_listing_content_module'] == 'yes' || (bool)$settings['packery_en']) {
					$post_excerpt              = preg_replace('~\[[^\]]+\]~', '', $post_excerpt);
					$post_excerpt_without_tags = strip_tags($post_excerpt);
					$post_descr                = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");
				} else {
					$post_descr = $post_excerpt;
				}
				$post_title = get_the_title();

				$post_boxed_content = '';
				if ((bool)$settings['post_boxed_content'] && $settings['packery_en'] == '') {
					$post_boxed_content = 'has_post_boxed_content';
				}
				if ($settings['items_type_line1_type'] == 'type2') {
					$post_boxed_content = '';
				}
				$key = 'key'.$widget->render_index;
				$widget->add_render_attribute( $key, 'class', array(
					'blog_post_preview',
					'isotope_item',
					'isotope-item',
					'loading',
					$blog_masonry_item,
					'format-'.$pf,
					$item_class,
					'packery_blog_item_'.$widget->render_index,
					$post_boxed_content,
					is_sticky() ? 'gt3_sticky_post' : ''
				) );

				echo '<div '.$widget->get_render_attribute_string($key).'>
                        <div class="item_wrapper" '.$featured_bg.'>
                            <div class="blog_content">';
				if ((bool)$settings['packery_en']) {
					echo '<div class="packery_content_wrap">';
					$packery_content_wrap_end = '</div>';
				} else {
					$packery_content_wrap_end = '';
				}

				$packery_pf_icon = '';
				if ((bool)$settings['packery_en']) {
					if ($pf == 'gallery' || $pf == 'audio' || $pf == 'video' || $pf == 'standard-image' || $pf == 'standard') {
					} else if ($pf == 'link' || $pf == 'quote') {
						echo ''.$pf_media['content'];
						if ($pf == 'link') {
							$packery_pf_icon = '<i class="blog_post_media__icon blog_post_media__icon--link"></i>';
						} else if ($pf == 'quote') {
							$packery_pf_icon = '<i class="blog_post_media__icon blog_post_media__icon--quote"></i>';
						}
						$packery_cats = '';
					}
					$post_btn_link = '';
				} else {
					if ($pf_media['content']){
						if ($settings['items_type_line1_type'] == 'type2') {
							echo '<div class="gt3_post_media_block">';
						}
						/*
						if($pf == 'gallery' || $pf == 'audio' || $pf == 'link' || $pf == 'video' || $pf == 'quote') {
							echo ''.$pf_media['content'];
						} else {
							echo '<a href="'.esc_url(get_permalink()).'">'.$pf_media['content'].'</a>';
						}
						*/
						if ($settings['post_media_content'] == 'yes') {
							echo $pf_media['content'];
						}

						if ($settings['items_type_line1_type'] == 'type2') {
							echo '</div>';
						}
					}
				}

				if(!empty($settings['post_btn_link'])) {
					$post_btn_link = '<div class="gt3_module_button_list"><a href="'. esc_url(get_permalink()) .'">'. $settings['post_btn_link_title'] .'</a></div>';
				} else {
					$post_btn_link = '<div class="gt3_module_button_empty"></div>';
				}

				if ((bool)$settings['packery_en'] && ($pf == 'link' || $pf == 'quote')) {
				} else {

					if ($settings['items_type_line1_type'] == 'type2') {
						echo '<div class="gt3_post_content_block">';
					}

					$listing_cats = ($settings['packery_en'] == '' && $post_category_compile) ? '<div class="gt3_page_title_cats">'.$post_category_compile.'</div>' : '';
					echo wp_kses_post( apply_filters( 'gt3/core/render/blog/listing_cats', $listing_cats ) );

					$pf_icon = is_sticky() ? '<i class="fa fa-thumb-tack"></i>' : '';
					$listing_title = strlen( $post_title ) > 0 ? '<h2 class="blogpost_title">'.$pf_icon.'<a href="'.esc_url( get_permalink() ).'">'.esc_html( $post_title ).'</a></h2>' : '';

					if (isset($settings['meta_position']) && $settings['meta_position'] == 'after_title') {
						echo wp_kses_post( apply_filters( 'gt3/core/render/blog/listing_title', $listing_title ) );
					}

					$listing_meta = (strlen( $post_meta )) ? '<div class="listing_meta">'.$post_meta.'</div>' : '';
					echo apply_filters( 'gt3/core/render/blog/listing_meta', $listing_meta );

					if (isset($settings['meta_position']) && $settings['meta_position'] == 'before_title') {
						echo wp_kses_post( apply_filters( 'gt3/core/render/blog/listing_title', $listing_title ) );
					}

					$listing_descr = strlen( $post_descr ) > 0 ? '<div class="blog_item_description">'.$post_descr.'</div>' : '';
					echo wp_kses_post( apply_filters( 'gt3/core/render/blog/listing_descr', $listing_descr ) );

					$listing_btn = '<div class="clear post_clear"></div>'.$post_btn_link.'<div class="clear"></div>';
					echo apply_filters( 'gt3/core/render/blog/listing_btn', $listing_btn, $settings);

					if ($settings['items_type_line1_type'] == 'type2') {
						echo '</div>';
					}
				}

				echo ''.$packery_content_wrap_end . $packery_cats . $packery_foot_info . $packery_pf_icon;
				echo '</div>
						</div>
					</div>
					';
				$widget->render_index++;
			} //endwhile
			wp_reset_postdata();

			if($settings['items_per_line'] !== '1') {
				echo '</div>';
			}
			if ((bool)$settings['pagination_en']) {
				echo gt3_get_theme_pagination(5, "", $query->max_num_pages, $paged);
			}
		}
		?>
	</div>
<?php
