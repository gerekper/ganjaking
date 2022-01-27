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

	$comments_num = get_comments_number(get_the_ID());

	$comments_text = $comments_num == 1 ? esc_html__( 'comment', 'agrosector' ) : esc_html__( 'comments', 'agrosector' );

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
										if (strlen($post_title) > 0) {
											$pf_icon = '';
											if (is_sticky()) {
												$pf_icon = '<i class="fa fa-thumb-tack"></i>';
											}
										}
										$icon_post_comments = '<span class="post_comments_icon"></span>';

										if ($page_title_conditional == 'no') {
										?>
											<div class="post_block_info">
												<div class="listing_meta_wrap">
													<div class="listing_meta">
														<span class="post_date"><?php echo esc_html(get_the_time(get_option( 'date_format' ))); ?></span>
														<span class="post_author"><?php echo esc_html__('by', 'agrosector') ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo esc_html(get_the_author_meta('display_name')); ?></a></span>
														<span class="post_category"><?php the_category(' '); ?></span>

                                                        <?php if((int)get_comments_number(get_the_ID()) != 0 ): ?>
														<span class="post_comments"><a href="<?php echo esc_url(get_comments_link()); ?>" title="<?php echo esc_attr(get_comments_number(get_the_ID())) . ' ' . $comments_text ?>"><?php echo esc_html(get_comments_number(get_the_ID())); ?><?php echo (($icon_post_comments)); ?></a></span>
                                                        <?php endif; ?>
													</div>
												</div>
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
											</div>
											<h1 class="blogpost_title_content"><?php echo (($pf_icon)) . esc_html($post_title); ?></h1>
										<?php
										}

										echo (($pf_media['content']));

										the_content();
										wp_link_pages(array(
											'before' => '<div class="page-link"><span class="pagger_info_text">' . esc_html__('Pages', 'agrosector') . ': </span>',
											'after' => '</div>',
											'pagelink'         => '<span class="page-number">%</span>',
										));
									?>
									<div class="dn"><?php posts_nav_link(); ?></div>
									<div class="clear post_clear"></div>

									<?php
										ob_start();
										the_tags("", ' ', '');
										$post_tags = ob_get_clean();
									?>
									<?php if (strlen($post_tags) > 0 || $show_share == "1" || $show_likes == "1") { ?>
									<div class="post_block_info">
										<div class="single_post_tags">
											<?php
											if (strlen($post_tags) > 0) {
											?>
												<div class="tagcloud">
													<?php echo (($post_tags)); ?>
												</div>
											<?php } ?>
										</div>
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
									</div>
									<?php } ?>

                                    <hr>

									<?php if(gt3_option('author_box') && get_the_author_meta('user_description')) { ?>
										<div class="gt3_author_box">
											<div class="gt3_author_box__avatar">
												<?php
												$user = get_the_author_meta('ID');
												echo get_avatar($user,200);
												?>
											</div>
											<h3 class="gt3_author_box__name"><?php echo esc_html( get_the_author_meta( 'display_name' ) );?></h3>
											<div class="gt3_author_box__desc"><?php echo get_the_author_meta('user_description');?></div>
										</div>
									<?php } ?>

									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<?php
					$show_post_featured = gt3_option("related_posts");
					if ( $show_post_featured == "1" ) :
						// Related Posts
						//for use in the loop, list 5 post titles related to first tag on current post
						$compile_related = '';
						$orig_post = $post;
						global $post;
						$tags = wp_get_post_tags($post->ID);

						if ($tags) :
							$tag_ids = array();
							foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;

							$postsArgs = array(
								'tag__in' => $tag_ids,
								'post__not_in' => array($post->ID),
								'posts_per_page' => (($layout == "none") ? "3" : "2"), // Number of posts to display.
								'ignore_sticky_posts' => 1,
								'orderby' => 'date',
								'order' => 'DESC',
								'post_type' => 'post',
								'post_status' => 'publish'
							);

							$gt3_wp_query_posts = new WP_Query();
							$gt3_wp_query_posts->query($postsArgs);
							while ($gt3_wp_query_posts->have_posts()) : $gt3_wp_query_posts->the_post();
								$gt3_theme_featured_image_latest = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()));

								$post_date = $post_category_compile = '';

								// Categories
								$categories = get_the_category();
								if ( !empty($categories) ) {
									$post_categ            = '';
									$post_category_compile = '<span class="post_category">';
									foreach ( $categories as $category ) {
										$post_categ = $post_categ . '<a href="' . esc_url(get_category_link( $category->term_id )) . '">' . esc_html($category->cat_name) . '</a>' . '';
									}
									$post_category_compile .= ' ' . trim( $post_categ, ', ' ) . '</span>';
								} else {
									$post_category_compile = '';
								}

								$post_date = '<span class="post_date">' . esc_html( get_the_time( get_option( 'date_format' ) ) ) . '</span>';

								// Post meta
								$post_meta = $post_date . $post_category_compile;

								$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' );

								$pf = 'standard';
								if ((bool)$featured_image[0]) {
									$pf = "standard-image";
								}

								if (has_excerpt()) {
									$post_excerpt = get_the_excerpt();
								} else {
									$post_excerpt = get_the_content();
								}

								$width  = '500';
								$height = '390';

								$symbol_count = 145;


								$post_excerpt = preg_replace( '~\[[^\]]+\]~', '', $post_excerpt);
								$post_excerpt_without_tags = strip_tags($post_excerpt);
								$post_descr = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");

								$compile_related .= '
								<div class="blog_post_preview format-' . esc_attr( $pf ) . '">
		                        	<div class="item_wrapper">
		                            	<div class="blog_content">'
		                            		.( (bool)$featured_image[0] ? '<a href="'.esc_url(get_permalink()).'"><div class="blog_post_media"><img src="'.esc_url(aq_resize($featured_image[0], $width, $height, true, true, true)) . '" alt="'.esc_attr(get_the_title()).'" /></div></a>' : '' )
		                            		.(strlen( $post_meta) ? '<div class="listing_meta">' . $post_meta . '</div>' : '').
		                            		'<h2 class="blogpost_title"><a href="' . esc_url( get_permalink() ) . '">'.get_the_title().'</a></h2>';

                                            $compile_related .= '<p>'.(strlen( $post_descr ) ? $post_descr : '' ).'</p><div class="clear"></div>
                                            <div class="gt3_module_button_list">
                                                <a href="'.esc_url( get_permalink() ).'">'. esc_html__('Read More', 'agrosector') .'</a>
                                            </div>';

											$compile_related .= '
											<div class="clear"></div>
										</div>
									</div>
								</div>';
							endwhile;
						endif;
						wp_reset_postdata();

						if ($compile_related != '') {
							echo '
							<div class="gt3_module_title"><h2>' . esc_html__('Related Posts', 'agrosector') . '</h2></div>
							<div class="gt3_module_related_posts gt3_module_featured_posts items'.(($layout == "none") ? "3" : "2").'">
								<div class="clear"></div>
								<div class="spacing_beetween_items_30">
									' . $compile_related . '
								</div>
								<div class="clear"></div>
							</div>
							';
						}

					endif;

					if (gt3_option('post_comments') == "1") {
						comments_template();
					}
				?>
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
	// prev next links
	$bottom_prev_next = gt3_option( "bottom_prev_next" );
	$prev_post = get_previous_post();
	$next_post = get_next_post();
	if ($bottom_prev_next && ($prev_post || $next_post)) { ?>
	<div class="single_prev_next_posts">
		<div class="container">
			<?php
			if (!empty($prev_post)) {
				previous_post_link('<div class="fleft">%link</div>', '<span class="gt3_post_navi" data-title="' . esc_attr($prev_post->post_title) . '">' . esc_html__('Prev', 'agrosector') . '</span>');
			}
			echo '<a href="'. esc_js("javascript:history.back()") .'" class="port_back2grid"><span class="port_back2grid_box1"></span><span class="port_back2grid_box2"></span><span class="port_back2grid_box3"></span><span class="port_back2grid_box4"></span></a>';
			if (!empty($next_post)) {
				next_post_link('<div class="fright">%link</div>', '<span class="gt3_post_navi" data-title="' . esc_attr($next_post->post_title) . '">' . esc_html__('Next', 'agrosector') . '</span>');
			}
			?>
		</div>
	</div>
	<?php }
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