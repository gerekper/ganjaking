<?php
/**
 * The template for displaying content in the index.php template
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! function_exists( 'mfn_content_post' ) ){
	function mfn_content_post( $query = false, $style = false, $attr = false ){

		global $wp_query;
		$output = '';

		$translate['published'] = mfn_opts_get('translate') ? mfn_opts_get('translate-published','Published by') : __('Published by','betheme');
		$translate['at'] = mfn_opts_get('translate') ? mfn_opts_get('translate-at','at') : __('at','betheme');
		$translate['categories'] = mfn_opts_get('translate') ? mfn_opts_get('translate-categories','Categories') : __('Categories','betheme');
		$translate['like'] = mfn_opts_get('translate') ? mfn_opts_get('translate-like','Do you like it?') : __('Do you like it?','betheme');
		$translate['readmore'] = mfn_opts_get('translate') ? mfn_opts_get('translate-readmore','Read more') : __('Read more','betheme');

		extract( shortcode_atts( array(
			'excerpt' => true,
			'featured_image' => false,
			'filters' => false,
			'title_tag' => false,
		), $attr ) );

		if( ! $query ){
			$query = $wp_query;
		}

		if( ! $style ){
			if( $_GET && key_exists('mfn-b', $_GET) ){
				$style = esc_html( $_GET['mfn-b'] ); // demo
			} else {
				$style = mfn_opts_get( 'blog-layout', 'classic' );
			}
		}

		if ( $query->have_posts() ){
			while ( $query->have_posts() ){
				$query->the_post();

				// classes

				$post_class =  array('post-item','isotope-item','clearfix');

				if( ! mfn_post_thumbnail( get_the_ID() ) ){
					$post_class[] = 'no-img';
				}
				if( post_password_required() ){
					$post_class[] = 'no-img';
				}

				if( in_array( $filters, array( 1, 'only-authors' ) ) ){
					$post_class[] = 'author-'. mfn_slug( get_the_author_meta( 'user_login' ) );
				}

				$post_class = implode(' ', get_post_class( $post_class ));

				// background color | style: Masonry Tiles

				$bg_color = get_post_meta( get_the_ID(), 'mfn-post-bg', true );

				if( $bg_color && 'masonry tiles' == $style ){
					$bg_color = 'background-color:'. $bg_color .';';
				} else {
					$bg_color = false;
				}

				// output -----

				$output .= '<div class="'. esc_attr($post_class) .'" style="'. esc_attr($bg_color) .'">';

					// icon | style: Masonry Tiles

					if( 'masonry tiles' == $style ){

						if( get_post_format() == 'video' ){

							$output .=  '<i class="post-format-icon icon-play"></i>';

						} elseif( get_post_format() == 'quote' ){

							$output .=  '<i class="post-format-icon icon-quote"></i>';

						} elseif( get_post_format() == 'link' ){

							$output .=  '<i class="post-format-icon icon-link"></i>';

						} elseif( get_post_format() == 'audio' ){	// for future use

							$output .=  '<i class="post-format-icon icon-music-line"></i>';

						} else {

							$rev_slider = get_post_meta( get_the_ID(), 'mfn-post-slider', true );
							$lay_slider = get_post_meta( get_the_ID(), 'mfn-post-slider-layer', true );

							if( $rev_slider || $lay_slider ){
								$output .=  '<i class="post-format-icon icon-code"></i>';
							}

						}

					}

					// date | style: Timeline

					$output .= '<div class="date_label">'. esc_html(get_the_date()) .'</div>';

					// photo

					if( ! post_password_required() ){

						if( 'masonry tiles' == $style ){

							// photo | style: Masonry Tiles

							$output .= '<div class="post-photo-wrapper scale-with-grid">';
								$output .= '<div class="image_wrapper_tiles">';
									$output .= get_the_post_thumbnail( get_the_ID(), 'full', array( 'class'=>'scale-with-grid', 'itemprop'=>'image' ) );
								$output .= '</div>';
							$output .= '</div>';

						} else {

							// photo | style: default

							// post image

							$post_format = mfn_post_thumbnail_type( get_the_ID() );

							if( 'photo2' == $style ){
								$featured_image = 'image';

								$output .= '<div class="button-love">'. mfn_love() .'</div>';
							}

							if( 'image' == $featured_image ){
								$post_format = 'images_only';
							}

							$output .= '<div class="image_frame post-photo-wrapper scale-with-grid '. esc_attr($post_format) .'">';
								$output .= '<div class="image_wrapper">';
									$output .= mfn_post_thumbnail( get_the_ID(), 'blog', $style, $featured_image );
								$output .= '</div>';
							$output .= '</div>';

						}

					}

					// desc

					$bg_color = get_post_meta( get_the_ID(), 'mfn-post-bg', true );
					$item_bg_class = 'bg-'. mfn_brightness( $bg_color );

					if( $bg_color &&  'photo2' == $style ){
						$bg_color = 'background-color:'. $bg_color .';';
					} else {
						$bg_color = false;
					}

					$output .= '<div class="post-desc-wrapper '. $item_bg_class .'" style="'. esc_attr($bg_color) .'">';
						$output .= '<div class="post-desc">';

							// head

							$output .= '<div class="post-head">';

								// meta

								$show_meta = false;
								$list_meta = mfn_opts_get( 'blog-meta' );

								if( is_array( $list_meta ) ){
									if( isset( $list_meta['author'] ) || isset( $list_meta['date'] ) || isset( $list_meta['categories'] ) ){
										$show_meta = true;
									}
								}

								if( $show_meta ){

									$output .= '<div class="post-meta clearfix">';

										$output .= '<div class="author-date">';

											if( isset( $list_meta['author'] ) ){
												$output .= '<span class="vcard author post-author">';
													$output .= '<span class="label">'. esc_html($translate['published']) .' </span>';
													$output .= '<i class="icon-user"></i> ';
													$output .= '<span class="fn"><a href="'. esc_url(get_author_posts_url(get_the_author_meta('ID'))) .'">'. esc_html(get_the_author_meta('display_name')) .'</a></span>';
												$output .= '</span> ';
											}

											if( isset( $list_meta['date'] ) ){
												$output .= '<span class="date">';
													if( isset( $list_meta['author'] ) ){
														$output .= '<span class="label">'. esc_html($translate['at']) .' </span>';
													}
													$output .= '<i class="icon-clock"></i> ';
													$output .= '<span class="post-date updated">'. esc_html(get_the_date()) .'</span>';
												$output .= '</span>';
											}

											// .post-comments | style: Masonry Tiles

											if( 'masonry tiles' == $style && comments_open() && mfn_opts_get( 'blog-comments' ) ){
												$output .= '<div class="post-links">';
													$output .= '<i class="icon-comment-empty-fa"></i> <a href="'. esc_url(get_comments_link()) .'" class="post-comments">'. esc_html(get_comments_number()) .'</a>';
												$output .= '</div>';
											}

										$output .= '</div>';

										if( isset( $list_meta['categories'] ) ){
											$output .= '<div class="category">';
												$output .= '<span class="cat-btn">'. esc_html($translate['categories']) .' <i class="icon-down-dir"></i></span>';
												$output .= '<div class="cat-wrapper">'. get_the_category_list() .'</div>';
											$output .= '</div>';
										}

									$output .= '</div>';

								}

								// .post-footer | style: Photo

								if( 'photo' == $style ){
									$output .= '<div class="post-footer">';

										$output .= '<div class="button-love"><span class="love-text">'. $translate['like'] .'</span>'. mfn_love() .'</div>';
										$output .= '<div class="post-links">';
											if( comments_open() && mfn_opts_get('blog-comments') ){
												$output .= '<i class="icon-comment-empty-fa"></i> <a href="'. esc_url(get_comments_link()) .'" class="post-comments">'. esc_html(get_comments_number()) .'</a>';
											}
											$output .= '<i class="icon-doc-text"></i> <a href="'. esc_url(get_permalink()) .'" class="post-more">'. esc_html($translate['readmore']) .'</a>';
										$output .= '</div>';

									$output .= '</div>';
								}

							$output .= '</div>';

							// title

							$output .= '<div class="post-title">';

								if( get_post_format() == 'quote' ){

									// quote

									$output .= '<blockquote><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></blockquote>';

								} elseif( get_post_format() == 'link' ){

									// link

									$link = get_post_meta(get_the_ID(), 'mfn-post-link', true);

									$output .= '<i class="icon-link"></i>';
									$output .= '<div class="link-wrapper">';
										$output .= '<h4>'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h4>';
										$output .= '<a target="_blank" href="'. esc_url($link) .'">'. esc_html($link) .'</a>';
									$output .= '</div>';

								} else {

									// default

									if( ! $title_tag ){
										$title_tag = mfn_opts_get('blog-title-tag', 2);
									}
									$output .= '<h'. esc_attr($title_tag) .' class="entry-title" itemprop="headline"><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h'. esc_attr($title_tag) .'>';

								}

							$output .= '</div>';

							// content

							if( $excerpt ){
								$output .= '<div class="post-excerpt">'. get_the_excerpt() .'</div>';
							}

							// .post-footer | style NOT: Photo, Masonry Tiles

							if( ! in_array( $style, array('photo','photo2','masonry tiles') ) ){
								$output .= '<div class="post-footer">';

									$output .= '<div class="button-love"><span class="love-text">'. esc_html($translate['like']) .'</span>'. mfn_love() .'</div>';
									$output .= '<div class="post-links">';
										if( comments_open() && mfn_opts_get( 'blog-comments' ) ){
											$output .= '<i class="icon-comment-empty-fa"></i> <a href="'. esc_url(get_comments_link()) .'" class="post-comments">'. esc_html(get_comments_number()) .'</a>';
										}
										$output .= '<i class="icon-doc-text"></i> <a href="'. esc_url(get_permalink()) .'" class="post-more">'. esc_html($translate['readmore']) .'</a>';
									$output .= '</div>';

								$output .= '</div>';
							}

							// .post-footer | style: Photo 2

							if( 'photo2' == $style ){
								if( isset( $list_meta['author'] ) || isset( $list_meta['date'] ) ){
									$output .= '<div class="post-footer">';

										if( isset( $list_meta['author'] ) ){
											$output .= '<span class="vcard author post-author">';
												global $user;
												$output .= get_avatar(get_the_author_meta('email'), '24', false, get_the_author_meta('display_name', $user['ID']));
												$output .= '<span class="fn"><a href="'. esc_url(get_author_posts_url(get_the_author_meta('ID'))) .'">'. esc_html(get_the_author_meta('display_name')) .'</a></span>';
											$output .= '</span> ';
										}

										if( isset( $list_meta['date'] ) ){
											$output .= '<span class="date">';
												$output .= '<i class="icon-clock"></i> ';
												$output .= '<span class="post-date updated">'. esc_html(get_the_date()) .'</span>';
											$output .= '</span>';
										}

									$output .= '</div>';
								}
							}


						$output .= '</div>';
					$output .= '</div>';

				$output .= '</div>';

			}
		}

		return $output;
	}
}
