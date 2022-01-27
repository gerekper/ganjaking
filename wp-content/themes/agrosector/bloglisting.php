<?php
	$show_likes = gt3_option('blog_post_likes');
	$show_share = gt3_option('blog_post_share');
	$all_likes = gt3pb_get_option("likes");

    $comments_text = get_comments_number(get_the_ID()) == 1 ? esc_html__( 'comment', 'agrosector' ) : esc_html__( 'comments', 'agrosector' );

	$post_date = $post_author = $post_category_compile = $post_comments = '';

	$class = is_sticky() ? ' gt3_sticky' : '';
	// Categories
	if (get_the_category()) $categories = get_the_category();
	if (!empty($categories)) {
		$post_categ = '';
		foreach ($categories as $category) {
			$post_categ .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->cat_name) . '</a>' . '';
		}
		$post_category_compile .= '<span class="post_category">' . trim($post_categ, ', ') . '</span>';
	}else{
		$post_category_compile = '';
	}

	$post = get_post();

	$post_date = '<span class="post_date">' . esc_html(get_the_time(get_option( 'date_format' ))) . '</span>';

	$post_author = '<span class="post_author">' . esc_html__('by', 'agrosector') . ' <a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author_meta('display_name')) . '</a></span>';

	$icon_post_comments = '<span class="post_comments_icon"></span>';

    if ((int)get_comments_number(get_the_ID()) != 0) {
	    $post_comments = '<span class="post_comments"><a href="' . esc_url(get_comments_link()) . '" title="' . esc_attr(get_comments_number(get_the_ID())) . ' ' . $comments_text . '">' . esc_html(get_comments_number(get_the_ID())) . $icon_post_comments . '</a></span>';
    }

	// Post meta
	$post_meta =  $post_date . $post_author . $post_category_compile . $post_comments;

	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

	$pf = get_post_format();
	if (empty($pf)) $pf = "standard";

	ob_start();
	if (has_excerpt()) {
		$post_excerpt = the_excerpt();
	} else {
		$post_excerpt = the_content();
	}
	$post_excerpt = ob_get_clean();

	$width = '1170';
	$height = '700';

	$pf_media = gt3_get_pf_type_output($pf, $width, $height, $featured_image);

	$pf = $pf_media['pf'];

	$symbol_count = '400';

	if (gt3_option('blog_post_listing_content') == "1") {
		$post_excerpt = preg_replace( '~\[[^\]]+\]~', '', $post_excerpt);
		$post_excerpt_without_tags = strip_tags($post_excerpt);
		$post_descr = wpautop( gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...") );
	} else {
		$post_descr = $post_excerpt;
	}

	$post_title = get_the_title();

?>
	<div class="blog_post_preview format-<?php echo esc_attr($pf).esc_attr($class);?>">
		<div class="item_wrapper">
			<div class="blog_content">
			<?php

				if ($pf == 'gallery' || $pf == 'video') {
					echo ''.$pf_media['content'];
				} elseif ($pf == 'standard-image') {
					echo '<a href="'.esc_url( get_permalink() ).'">'.$pf_media['content'].'</a>';
				}

				if ( strlen($post_meta) ) {
					echo '<div class="listing_meta_wrap"><div class="listing_meta">' . $post_meta . '</div></div>';
				}

				if ($pf == 'link' || $pf == 'quote') {
					echo ''.$pf_media['content'];
				} elseif (strlen($post_title) > 0) {
					$pf_icon = '';
					if ( is_sticky() ) {
						$pf_icon = '<i class="fa fa-thumb-tack"></i>';
					}
					echo '<h2 class="blogpost_title">' . $pf_icon . '<a href="' . esc_url(get_permalink()) . '">' . esc_html($post_title) . '</a></h2>';
				}

				if ($pf == 'audio') {
					echo ''.$pf_media['content'];
				}

				echo (strlen($post_descr) ? $post_descr : '') . '<div class="clear post_clear"></div><div class="gt3_post_footer"><div class="gt3_module_button_list"><a href="'. esc_url(get_permalink()) .'">'. esc_html__('Read More', 'agrosector') .'</a></div>';
				?>
				<?php if ($show_share == "1" || $show_likes == "1") { ?>
					<div class="blog_post_info">
						<?php
							if ($show_share == "1") { ?>
								<!-- post share block -->
								<div class="post_share_block">
									<a href="<?php echo esc_js("javascript:void(0)"); ?>"><span class="sharing_title"><?php echo esc_html__('Share', 'agrosector'); ?></span></a>
									<div class="post_share_wrap">
										<ul>
											<?php
											echo '<li class="post_share-facebook"><a target="_blank" href="'.esc_url('https://www.facebook.com/share.php?u='. get_permalink()).'"><span class="fa fa-facebook"></span></a></li>';
											echo '<li class="post_share-twitter"><a target="_blank" href="'.esc_url('https://twitter.com/intent/tweet?text='. get_the_title() .'&amp;url='. get_permalink()).'"><span class="fa fa-twitter"></span></a></li>';
											if (strlen($featured_image[0]) > 0) {
												echo '<li class="post_share-pinterest"><a target="_blank" href="'. esc_url('https://pinterest.com/pin/create/button/?url='. get_permalink() .'&media='. $featured_image[0]) .'"><span class="fa fa-pinterest"></span></a></li>';
											}
											echo '<li class="post_share-linkedin"><a target="_blank" href="'. esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.get_permalink().'&title='.esc_attr(get_the_title()).'&source='.get_bloginfo("name")) .'"><span class="fa fa-linkedin"></span></a></li>';
											/* Email Link */
											ob_start();
											the_title('','',true);
											$email_title = ob_get_clean();
											ob_start();
											the_permalink();
											$email_permalink = ob_get_clean();
											$email_link = 'mailto:?subject='. $email_title . '&body='. $email_permalink;
											echo '<li class="post_share-mail"><a target="_blank" href="' . $email_link . '"><span class="fa fa-envelope"></span></a></li>';
											?>
										</ul>
									</div>
								</div>
								<!-- //post share block -->
						<?php }
							if ($show_likes == "1") {
								echo '<div class="likes_block post_likes_add '. (isset($_COOKIE['like_post'.get_the_ID()]) ? "already_liked" : "") .'" data-postid="'. esc_attr(get_the_ID()).'" data-modify="like_post">
									<span class="fa fa-heart-o icon"></span>
									<span class="like_count">'.((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()]>0) ? $all_likes[get_the_ID()] : 0).'</span>
								</div>';
							}
						?>
					</div>
				<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>