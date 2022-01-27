<?php
if ( !post_password_required() ) {
	get_header();
	the_post();

	$layout = gt3_option('blog_single_sidebar_layout');
	$sidebar = gt3_option('blog_single_sidebar_def');
	if (class_exists( 'RWMB_Loader' )) {
		$mb_layout = rwmb_meta('mb_page_sidebar_layout');
		if (!empty($mb_layout) && $mb_layout != 'default') {
			$layout = $mb_layout;
			$sidebar = rwmb_meta('mb_page_sidebar_def');
		}
	}
	$column = 12;
	if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
		$column = 9;
	}else{
		$sidebar = '';
	}
	$row_class = ' sidebar_'.$layout;

	$show_likes = gt3_option('blog_post_likes');
	$show_share = gt3_option('blog_post_share');

	$all_likes = gt3pb_get_option("likes");

	$comments_num = '' . get_comments_number(get_the_ID()) . '';

	if ($comments_num == 1) {
		$comments_text = '' . esc_html__('comment', 'agrosector') . '';
	} else {
		$comments_text = '' . esc_html__('comments', 'agrosector') . '';
	}

	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');

	$pf = get_post_format();
	if (empty($pf)) $pf = "standard";

	$width = '1170';
	$height = '700';

	$pf_media = gt3_get_pf_type_output($pf, $width, $height, $featured_image);
	$pf = $pf_media['pf'];

	$post_title = get_the_title();

	?>

	<div class="container">
        <div class="row<?php echo esc_attr($row_class); ?>">
            <div class="content-container span<?php echo (int)esc_attr($column); ?>">
                <section id='main_content'>
					<div class="blog_post_preview format-<?php echo (($pf)); ?>">
						<div <?php post_class("single_meta"); ?>>
							<div class="item_wrapper">
								<div class="blog_content">
									<?php

										if (strlen($post_title) > 0) {
											$pf_icon = '';

											$page_title_conditional = ((gt3_option('page_title_conditional') == '1' || gt3_option('page_title_conditional') == true)) ? 'yes' : 'no' ;
											if (class_exists( 'RWMB_Loader' ) && gt3_get_queried_object_id() !== 0) {
												$mb_page_title_conditional = rwmb_meta('mb_page_title_conditional');
									            if ($mb_page_title_conditional == 'yes') {
									                $page_title_conditional = 'yes';
									            }elseif($mb_page_title_conditional == 'no'){
									                $page_title_conditional = 'no';
									            }
											}

											$blog_title_conditional = ((gt3_option('blog_title_conditional') == '1' || gt3_option('blog_title_conditional') == true)) ? 'yes' : 'no';

									        if (is_singular('post') && $page_title_conditional == 'yes' && $blog_title_conditional == 'no') {
									            $page_title_conditional = 'no';
									        }

											if ( $page_title_conditional != 'yes' || (bool)$pf_media['content'] ) {
												echo '<h3 class="blogpost_title">' . $pf_icon . esc_html($post_title) . '</h3>';
											}

										}

										if ($pf == 'quote' || $pf == 'audio' || $pf == 'link') {
											echo (($pf_media['content']));
										}

										the_content();
										wp_link_pages(array('before' => '<div class="page-link"><span class="pagger_info_text">' . esc_html__('Pages', 'agrosector') . ': </span>', 'after' => '</div>'));
									?>

									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<?php if (gt3_option('post_comments') == "1"): ?>
						<div class="row">
							<div class="span12">
								<?php comments_template(); ?>
							</div>
						</div>
					<?php endif; ?>
				</section>
			</div>
			<?php
			if ($layout == 'left' || $layout == 'right') {
				echo '<div class="sidebar-container span'.(12 - (int)esc_attr($column)).'">';
				if (is_active_sidebar( $sidebar )) {
					echo "<aside class='sidebar'>";
					dynamic_sidebar( $sidebar );
					echo "</aside>";
				}
				echo "</div>";
			}
			?>
		</div>

	</div>

	<?php
		get_footer();
} else {
	get_header();
	?>
	<div class="pp_block">
		<div class="container_vertical_wrapper">
			<div class="container a-center pp_container">
				<h1><?php echo esc_html__('Password Protected', 'agrosector'); ?></h1>
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<?php
	get_footer();
} ?>