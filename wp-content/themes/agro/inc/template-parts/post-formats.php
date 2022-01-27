<?php

if (is_admin()) {
    return false;
}

/*************************************************
##  FORMATS_CONTENT
*************************************************/

if (! function_exists('agro_post')) {
    function agro_post()
    {
        $index_type = agro_settings('blog_index_type', 'grid' );
        $post_column = agro_settings('post_column', 'col-md-12' );
        $post_alignment = agro_settings('post_alignment', 'text-left' );
        $post_attr = '';
        $post_attr .= 'masonry' == $index_type ? ' masonry-item' : '';
        $post_attr .=  ' '.$post_column;
        $post_attr .= ' '.$post_alignment;
        //add sticky class to post if post sticked
        $sticky = (is_sticky()) ? ' -has-sticky ' : '';

        ob_start();
        post_class(esc_attr('nt-blog-item'.$sticky.$post_attr));

        echo'<div id="post-'.get_the_ID().'" '.ob_get_clean().' >

			<div class="nt-blog-item-inner">';

        agro_post_format();

        //post content wrapper div
        echo '<div class="nt-blog-info">';

        // post categories
        if ( ! is_single() ) {
            agro_post_categories();
        }

        // post title
        if ('0' != agro_settings('post_title_onoff', '1' )) {
            the_title(sprintf('<h3 class="nt-blog-info-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h3>');
        }

        if (!is_search()) {

            // post meta function contains author - date - comments
            agro_post_meta();
        }

        if ('0' != agro_settings('post_excerpt_onoff', '1' )) {
        // post content function for loop excerpt.
        $excerptsz = agro_settings('excerptsz', '80' );
        echo '<div class="nt-blog-info-excerpt">'. wp_trim_words( get_the_excerpt(), $excerptsz) .'</div>';
        }


        // post read-more button.
        if ('0' != agro_settings( 'post_button_onoff', '1' )) {
            $read_more = agro_settings( 'post_button_title' );
            $read_more = $read_more ? $read_more : esc_html__('Read More', 'agro');
            echo '<div class="nt-blog-info-link">
				<a href="'.esc_url(get_permalink()).'" class="nt-btn-theme nt-post-button custom-btn custom-btn--medium custom-btn--style-1">'.esc_html( $read_more ).'</a>
			</div>';
        }

        // this function must be using for wp linkable pages, don't delete!
        agro_wp_link_pages();

        // end post content wrapper div
        echo '</div>
		</div>
	</div>';
    }
}

if (! function_exists('agro_post_two')) {
    function agro_post_two()
    {
        $index_type = agro_settings('blog_index_type', 'grid' );
        $post_column = agro_settings('post_column', '6' );
        $post_alignment = agro_settings('post_alignment', 'text-left' );
        $post_attr = '';
        $post_attr .= 'masonry' == $index_type ? ' masonry-item' : '';
        $post_attr .= 'masonry' == $index_type || 'grid' == $index_type ? ' '.$post_column : '';
        $post_attr .= ' '.$post_alignment;
        //add sticky class to post if post sticked
        $sticky = (is_sticky()) ? ' -has-sticky ' : '';

        ob_start();
        post_class(esc_attr('posts posts--style-1 nt-blog-item'.$sticky.$post_attr));
        echo'<div id="post-'.get_the_ID().'" '.ob_get_clean().' >';

            echo '<div class="__item __item--preview aos-init aos-animate" data-aos="flip-up" data-aos-delay="100" data-aos-offset="0">';

                echo '<figure class="__image">';
                    the_post_thumbnail('agro-820-hard');
                echo '</figure>';

                echo '<div class="__content">';
                    if ( '0' != agro_settings('post_category_onoff', '1' ) ) {
                        echo '<p class="__category">';
                            the_category(' / ');
                        echo '</p>';
                    }

                    if ('0' != agro_settings('post_title_onoff', '1' )) {
                        the_title(sprintf('<h3 class="__title h5"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h3>');
                    }

                    if ('0' != agro_settings('post_excerpt_onoff', '1' )) {
                        $excerptsz = agro_settings('excerptsz', '80' );
                        echo '<p>'. wp_trim_words( get_the_excerpt(), $excerptsz) .'</p>';
                    }

                    agro_wp_link_pages();

                    if ('0' != agro_settings( 'post_button_onoff', '1' )) {
                        $read_more = agro_settings( 'post_button_title' );
                        $read_more = $read_more ? $read_more : esc_html__('Read More', 'agro');
                        echo '<a href="get_permalink()" class="custom-btn custom-btn--medium custom-btn--style-1">'.esc_html( $read_more ).'</a>';
                    }
                echo '</div>';

                echo '<span class="__date-post"><strong>'.get_the_date('j').'</strong>'.get_the_date('M').'</span>';

            echo '</div>';
        echo '</div>';

    }
}

if (! function_exists('agro_post_three')) {
    function agro_post_three()
    {
        $index_type = agro_settings('blog_index_type', 'grid' );
        $post_column = agro_settings('post_column', '6' );
        $post_alignment = agro_settings('post_alignment', 'text-left' );
        $post_attr = '';
        $post_attr .= 'masonry' == $index_type ? ' masonry-item' : '';
        $post_attr .= 'masonry' == $index_type || 'grid' == $index_type ? ' '.$post_column : '';
        $post_attr .= ' '.$post_alignment;
        //add sticky class to post if post sticked
        $sticky = (is_sticky()) ? ' -has-sticky ' : '';

        ob_start();
        post_class(esc_attr('posts posts--style-2 nt-blog-item'.$sticky.$post_attr));
        echo'<div id="post-'.get_the_ID().'" '.ob_get_clean().' >';
            echo '<div class="__item __item--preview aos-init aos-animate" data-aos="flip-up" data-aos-delay="100" data-aos-offset="0">';
                echo '<figure class="__image">';
                    the_post_thumbnail('agro-820-hard');

                    echo '<span class="__overlay"></span>';

                    echo '<div class="__content">';

                        echo '<span class="__date-post"><strong>'.get_the_date('j').'</strong>'.get_the_date('M').'</span>';

                        if ( '0' != agro_settings('post_category_onoff', '1' )) {
                            echo '<p class="__category">';
                                the_category(' / ');
                            echo '</p>';
                        }

                        if ('0' != agro_settings('post_title_onoff', '1' )) {
                            the_title(sprintf('<h3 class="__title h5"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h3>');
                        }

                    echo '</div>';
                echo '</figure>';
            echo '</div>';
        echo '</div>';

    }
}


/*************************************************
##  POST FORMAT
*************************************************/


if (! function_exists('agro_post_format')) {
    function agro_post_format()
    {

        // post format
        $format = get_post_format() ? : 'standard';

        // post format video or audio embed
        if ('video' == $format || 'audio' == $format) {
            $content = rwmb_meta('agro_embed_content');

            // Only get video from the content if a playlist isn't present.
            if (false === strpos($content, 'wp-playlist-script')) {
                $embed = get_media_embedded_in_content($content, array( 'video', 'object', 'embed', 'iframe', 'audio'  ));
            }

            // If not a single post, highlight the video file.
            if (! empty($embed)) {
                foreach ($embed as $embed_html) {
                    echo '<div class="nt-blog-media embed-responsive embed-responsive-16by9">'. wp_kses($embed_html, agro_allowed_html()) .'</div>';
                }
            }

            // post format gallery
        } elseif ('gallery' == $format) {
            $images = rwmb_meta('agro_post_gallery', array( 'size' => 'full', 'type' => 'image_advanced' ));

            if ($images) {
                wp_enqueue_style('owl-carousel');
                wp_enqueue_script('owl-carousel');

                echo '<div class="nt-post-gallery-type owl-carousel">';

                foreach ($images as $image) {
                    echo '<div class="nt-blog-media post-gallery-item">
							<a href="'.esc_url(get_permalink()).'">
								<img src="'.esc_url($image['full_url']).'" alt="'.esc_attr($image['alt']).'">
							</a>
						</div>';
                }

                echo '</div>';
            }

            // standart post
        } else {
            if (has_post_thumbnail()) {
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'agro-820-hard');

                echo '<div class="nt-blog-media">
					<a class="nt-blog-media-link" href="'.esc_url(get_permalink()).'">';
						the_post_thumbnail('agro-820-hard');
					echo '</a>
				</div>';
            }
        } // end post format
    }
}


/*************************************************
##  POST/CPT META
*************************************************/


if (! function_exists('agro_post_meta')) {
    function agro_post_meta()
    {
        $archive_year  = get_the_time('Y');
        $archive_month = get_the_time('m');
        $archive_day   = get_the_time('d');
        $author_id 	   = get_the_author_meta('ID');
        $author_link   = get_author_posts_url($author_id);

        if ('0' != agro_settings('post_meta_onoff', '1')) {
            ?>

			<!-- Post Category, Author, Comments -->
			<ul class="nt-blog-info-meta">

				<?php

                if (is_sticky()) {
                    echo '<div class="nt-sticky-label">'.esc_html__('Sticky', 'agro').'</div>';
                }

            // post author
            if ('0' != agro_settings('post_author_onoff', '1')) {
                ?>

					<li class="nt-blog-info-meta-item">
						<a class="nt-blog-info-meta-link post-author" href="<?php echo esc_url($author_link); ?>">
                            <i class="fa fa-user"></i>
                            <?php the_author(); ?>
                        </a>
					</li>

				<?php
            }

            // post comments
            if ('0' != agro_settings('post_comments_onoff', '1')) {
                ?>

					<li class="nt-blog-info-meta-item">
						<a class="nt-blog-info-meta-link post-comment" href="<?php esc_url( the_permalink() ); ?>#respond">
                            <i class="fa fa-comments"></i>
                            <?php comments_number(); ?>
                        </a>
					</li>

				<?php
            }

            // post date
            if ('0' != agro_settings('post_date_onoff', '1')) {
                ?>

					<li class="nt-blog-info-meta-item">
						<a class="nt-blog-info-meta-link post-date" href="<?php echo esc_url(get_day_link($archive_year, $archive_month, $archive_day)); ?>">
                            <i class="fa fa-calendar"></i>
                            <?php the_time(get_option('date_format')); ?>
                        </a>
					</li>

                <?php
            } ?>

			</ul>

	<?php
        }
    }
}

/*************************************************
##  SINLGE POST/CPT TAGS
*************************************************/

if (! function_exists('agro_post_categories')) {
    function agro_post_categories()
    {
        if ('0' != agro_settings('post_category_onoff','1') && has_category()) {
    ?>

	<!-- Post Categories -->
	<h5 class="nt-blog-info-category"><?php the_category(' '); ?></h5>

<?php
        }
    }
}

/*************************************************
##  SINLGE POST/CPT TAGS
*************************************************/

if (! function_exists('agro_single_post_tags')) {
    function agro_single_post_tags()
    {
        if ('0' != agro_settings('single_postmeta_tags_onoff','1')) {
            if (has_tag()) {
                ?>

				<!-- Post Tags -->
				<div class="nt-post-tags">

					<ul class="nt-tags-list">

						<?php

                            $tags = get_the_tags(get_the_ID());

                            foreach ($tags as $tag) {
                                echo '<li class="nt-tags-list-item">
            									<a class="nt-tags-list-link uppercase '. esc_attr($tag->name) .'" href="'.esc_url(get_tag_link($tag->term_id)).'">'. esc_html($tag->name) .'</a>
            								</li>';
                            }

                        ?>

					</ul>

				</div>
				<!-- Post Tags End -->
			<?php
            }
        }
    }
}


/*************************************************
## SINGLE POST AUTHOR BOX FUNCTION
*************************************************/

if (! function_exists('agro_single_post_author_box')) {
    function agro_single_post_author_box()
    {
        global $post;

        if ('0' != agro_settings('single_post_author_box_onoff','1')) {

            // Get author's display name
            $display_name = get_the_author_meta('display_name', $post->post_author);
            // If display name is not available then use nickname as display name
            $display_name = empty($display_name) ? get_the_author_meta('nickname', $post->post_author) : $display_name ;

            // Get author's biographical information or description
            $user_description = get_the_author_meta('user_description', $post->post_author);

            // Get author's website URL
            $user_website = get_the_author_meta('url', $post->post_author);

            // Get link to the author archive page
            $user_posts = get_author_posts_url(get_the_author_meta('ID', $post->post_author));

            // Get the rest of the author links. These are stored in the
            // wp_usermeta table by the key assigned in wpse_user_contactmethods()
            $author_facebook = get_the_author_meta('facebook', $post->post_author);
            $author_twitter  = get_the_author_meta('twitter', $post->post_author);
            $author_linkedin = get_the_author_meta('linkedin', $post->post_author);
            $author_youtube  = get_the_author_meta('youtube', $post->post_author);

            if ('' != $user_description) {
                ?>

				<div class="container-author-box mb-60">

					<h3 class="nt-inner-title"><?php echo esc_html_e('About The Author', 'agro'); ?></h3>

					<div class="row">

						<div class="col-md-2">
							<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
								<?php if (function_exists('get_avatar')) {
                                    echo get_avatar(get_the_author_meta('email'), '100');
                                } ?>
							</a>
						</div>

						<div class="col-md-10">

							<h5 class="nt-single-post-related-time"><a class="u-color-dark u-text-capitalize" href="<?php echo esc_url($user_posts); ?>"><?php echo esc_html($display_name); ?></a></h5>

							<p><?php echo esc_html($user_description); ?></p>

							<div class="nt-author-social -color-mixed-default -hover-mixed-outline -corner-circle -size-medium">

								<ul class="nt-author-social-inner">

									<?php if ('' != $author_facebook) { ?>
										<li class="nt-author-social-item"><a class="nt-author-social-link -icon-facebook" href="<?php echo esc_url($author_facebook); ?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
									<?php } ?>

									<?php if ('' != $author_twitter ) {?>
										<li class="nt-author-social-item"><a class="nt-author-social-link -icon-twitter" href="<?php echo esc_url($author_twitter); ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
									<?php } ?>

									<?php if ('' != $author_linkedin) { ?>
										<li class="nt-author-social-item"><a class="nt-author-social-link -icon-linkedin" href="<?php echo esc_url($author_linkedin); ?>" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
									<?php } ?>

									<?php if ('' != $author_youtube) { ?>
										<li class="nt-author-social-item"><a class="nt-author-social-link -icon-youtube" href="<?php echo esc_url($author_youtube); ?>" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
									<?php } ?>

								</ul>

							</div>

						</div>

					</div>
				</div>

			<?php
            }
        }
    }
}

/*************************************************
## SINGLE POST RELATED POSTS
*************************************************/

if (! function_exists('agro_single_post_related')) {
    function agro_single_post_related()
    {
        if ('0' != agro_settings('single_related_onoff', '0')) {
            global $post;
            $tags = wp_get_post_tags($post->ID);

            if ($tags) {


                $related_title = agro_settings('related_title');
                $related_title = $related_title != '' ? $related_title : '';
        ?>

			<div class="nt-single-post-related">

				<h3 class="nt-inner-title"><?php echo esc_html($related_title); ?></h3>

				<div class="row">

        			<?php

                        $tag_ids = array();

                        foreach ($tags as $individual_tag) {
                            $tag_ids[] = $individual_tag->term_id;
                        }
                        $related_perpage = agro_settings('related_perpage');
                        $related_perpage = $related_perpage != '' ? $related_perpage : 4;
                        $args=array(
                            'tag__in' => $tag_ids,
                            'post__not_in' => array($post->ID),
                            'posts_per_page'=>$related_perpage,
                        );

                        $like_query = new wp_query($args);

                        while ($like_query->have_posts()) {
                            $like_query->the_post(); ?>
        						<div class="col-md-3 col-sm-3">
        							<div class="nt-single-post-related-item">
        								<?php if (has_post_thumbnail()) { ?>
        									<div class="nt-single-post-related-image">
        										<a href="<?php esc_url( the_permalink() ); ?>"><?php the_post_thumbnail(array(200,200)); ?></a>
        									</div>
        								<?php } ?>
        								<h5 class="nt-single-post-related-title"><a class="u-color-dark"  href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h5>
        								<span class="nt-single-post-related-time"><?php the_time('F j, Y'); ?></span>
        							</div>
        						</div>

        					<?php
                        }

                        wp_reset_postdata();

                    ?>

				</div>
			</div>

		<?php
            }
        }
    }
}
