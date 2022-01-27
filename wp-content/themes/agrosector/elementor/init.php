<?php

add_filter('gt3/core/builder_support', function($supports){
	$supports[] = 'elementor';

	return $supports;
});

add_filter('gt3/elementor/widgets/register', function($widgets){
	$widgets = array(
		'Accordion',
		'Blog',
		'Button',
		'CustomMeta',
		'Counter',
		'Divider',
		'EmptySpace',
		'Flipbox',
		'GoogleMap',
		'InfoList',
		'PieChart',
		'Portfolio',
		'PortfolioCarousel',
		'PriceBox',
		'ProcessBar',
		'Project',
		'Sharing',
		'Tabs',
		'Team',
		'Testimonials',
	);
	if (class_exists('RevSlider')) {
		$widgets[] = 'RevolutionSlider';
	}
	if (class_exists('WooCommerce')) {
		$widgets[] = 'ShopList';
	}
	return $widgets;
});

add_action('elementor/element/gt3-core-blog/general_section/before_section_end', function($element,$args){
	/* @var \Elementor\Widget_Base $element */
	$element->update_control('packery_en',array(
		'condition'=>array(
			'show'=>'newer'
		)
	));
	$element->update_control('static_info_block',array(
		'condition'=>array(
			'show'=>'newer'
		)
	));
},20,2);

// Meta
add_filter( 'gt3/core/render/blog/listing_meta', function ($compile) {
	return '<div class="listing_meta_wrap">'.$compile.'</div>';
});

// Media height
add_filter( 'gt3/core/render/blog/media_height', function () {
	return '700';
});

// Post comments
add_filter( 'gt3/core/render/blog/post_comments', function () {

	$comments_text = get_comments_number( get_the_ID() ) == 1 ? esc_html__( 'comment', 'agrosector' ) : esc_html__( 'comments', 'agrosector' );

	$icon_post_comments = '<span class="post_comments_icon"></span>';

	return '<span class="post_comments"><a href="' . esc_url(get_comments_link()) . '" title="' . esc_attr(get_comments_number(get_the_ID())) . ' ' . $comments_text . '">' . esc_html(get_comments_number(get_the_ID())) . $icon_post_comments . '</a></span>';

});

// Post author
add_filter( 'gt3/core/render/blog/post_author', function () {
	return '<span class="post_author">' . esc_html__('by', 'agrosector') . ' <a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author_meta('display_name')) . '</a></span>';
});

// Post bottom Area
add_filter( 'gt3/core/render/blog/listing_btn', function ($listing_btn, $settings) {

	$show_likes = gt3_option('blog_post_likes');
	$show_share = gt3_option('blog_post_share');
	if ($show_share == '1'){
		$show_share = ! empty( $settings['share'] ) ? '1' : false;
	}

	$all_likes = gt3pb_get_option("likes");

	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

	$btn_compile = '<div class="clear post_clear"></div><div class="gt3_post_footer">';

	if(!empty($settings['post_btn_link'])) {
		$btn_compile .= '<div class="gt3_module_button_list"><a href="'. esc_url(get_permalink()) .'">'. $settings['post_btn_link_title'] .'</a></div>';
	}

	if ($show_share == "1" || $show_likes == "1") {
		$btn_compile .= '<div class="blog_post_info">';

		if ($show_share == "1") {
			$btn_compile .= '
			<div class="post_share_block">
				<a href="'. esc_js("javascript:void(0)") .'"><span class="sharing_title">'. esc_html__('Share', 'agrosector') .'</span></a>
				<div class="post_share_wrap">
					<ul>
						<li class="post_share-facebook"><a target="_blank" href="'.esc_url('//www.facebook.com/share.php?u='. get_permalink()).'"><span class="fa fa-facebook"></span></a></li>
						<li class="post_share-twitter"><a target="_blank" href="'.esc_url('//twitter.com/intent/tweet?text='. get_the_title() .'&amp;url='. get_permalink()).'"><span class="fa fa-twitter"></span></a></li>';
						if (strlen($featured_image[0]) > 0) {
							$btn_compile .= '<li class="post_share-pinterest"><a target="_blank" href="'. esc_url('//pinterest.com/pin/create/button/?url='. get_permalink() .'&media='. $featured_image[0]) .'"><span class="fa fa-pinterest"></span></a></li>';
						}
						$btn_compile .= '<li class="post_share-linkedin"><a target="_blank" href="'. esc_url('//www.linkedin.com/shareArticle?mini=true&url='.get_permalink().'&title='.esc_attr(get_the_title()).'&source='.get_bloginfo("name")) .'"><span class="fa fa-linkedin"></span></a></li>';
						/* Email Link */
						ob_start();
						the_title('','',true);
						$email_title = ob_get_clean();
						ob_start();
						the_permalink();
						$email_permalink = ob_get_clean();
						$email_link = 'mailto:?subject='. $email_title . '&body='. $email_permalink;
						$btn_compile .= '<li class="post_share-mail"><a target="_blank" href="' . $email_link . '"><span class="fa fa-envelope"></span></a></li>';
					$btn_compile .= '</ul>
				</div>
			</div>';
		}

		if ($show_likes == "1") {
			$btn_compile .= '<div class="likes_block post_likes_add '. (isset($_COOKIE['like_post'.get_the_ID()]) ? "already_liked" : "") .'" data-postid="'. esc_attr(get_the_ID()).'" data-modify="like_post">
				<span class="fa fa-heart-o icon"></span>
				<span class="like_count">'.((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()]>0) ? $all_likes[get_the_ID()] : 0).'</span>
			</div>';
		}

		$btn_compile .= '</div>';
	}

	$btn_compile .= '<div class="clear"></div></div>';

	return $btn_compile;

}, 10, 2);

if (class_exists('\gt3_photo_video_galery_pro')) {
	gt3_photo_video_galery_pro::instance()->actions();
	if (class_exists('\gt3pg_pro_plugin_updater')) {
		gt3pg_pro_plugin_updater::instance()->status = 'valid';
	}
}

if (class_exists('\GT3\PhotoVideoGalleryPro\Autoload')) {
	\GT3\PhotoVideoGalleryPro\Autoload::instance()->Init();
}
