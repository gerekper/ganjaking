<?php
/**
 * The template for displaying content in the template-portfolio.php template
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! function_exists('mfn_content_portfolio') ){
	function mfn_content_portfolio( $query = false, $style = false ){

		global $wp_query;
		$output = '';

		$translate['readmore'] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-readmore', 'Read more' ) : __( 'Read more', 'betheme' );
		$translate['client'] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-client', 'Client' ) : __( 'Client', 'betheme' );
		$translate['date'] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-date', 'Date' ) : __( 'Date', 'betheme' );
		$translate['website'] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-website', 'Website' ) : __( 'Website', 'betheme' );
		$translate['view'] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-view', 'View website' ) : __( 'View website', 'betheme' );

		// query

		if( ! $query ){
			$query = $wp_query;
		}

		// style

		if( ! $style ){
			if( $_GET && key_exists('mfn-p', $_GET) ){
				$style = esc_html( $_GET['mfn-p'] ); // demo
			} else {
				$style = mfn_opts_get( 'portfolio-layout', 'grid' );
			}
		}

		// list meta

		$list_meta = mfn_opts_get( 'portfolio-meta' );

		if ( $query->have_posts() ){
			while ( $query->have_posts() ){

				$query->the_post();

				$item_class = array();
				$categories = '';

				$terms = get_the_terms( get_the_ID(), 'portfolio-types' );
				if( is_array( $terms ) ){
					foreach( $terms as $term ){
						$item_class[] = 'category-'. $term->slug;
						$categories .= '<a href="'. site_url() .'/portfolio-types/'. $term->slug .'">'. $term->name .'</a>, ';
					}
					$categories = substr( $categories , 0, -2 );
				}

				$item_class[] = get_post_meta( get_the_ID(), 'mfn-post-size', true );
				$item_class[] = has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail';
				$item_class = implode(' ', $item_class);

				$external = mfn_opts_get( 'portfolio-external' );
				$ext_link = get_post_meta( get_the_ID(), 'mfn-post-link', true );
				$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );

				// item backgrounds

				// style: list

				if( $item_bg_image = get_post_meta( get_the_ID(), 'mfn-post-bg', true ) ){
					$item_bg_image = 'background-image:url('. esc_url($item_bg_image) .');';
				}

				// style: masonry hover

				$item_bg_class = 'bg-'. mfn_brightness( mfn_opts_get( 'background-imageframe-link', '#2991d6' ), 169 );

				if( $item_bg_color = get_post_meta( get_the_ID(), 'mfn-post-bg-hover', true ) ){

					$item_bg_class = 'bg-'. mfn_brightness( $item_bg_color, 169 );
					$item_bg_color = 'background-color:'. mfn_hex2rgba( $item_bg_color, 0.9 ) .';';

				}

				// image link

				if( in_array( $external, array('disable','popup') ) ){

					// disable details & link popup
					$link_before_escaped 	= '<a class="link" href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto">';

				} elseif( $external && $ext_link ){

					// link to project website
					$link_before_escaped 	= '<a class="link" href="'. esc_url($ext_link) .'" target="'. esc_attr($external) .'">';

				} else {

					// link to project details
					$link_before_escaped 	= '<a class="link" href="'. esc_url(get_permalink()) .'">';

				}

				// output -----

				$output .= '<li class="portfolio-item isotope-item '. esc_attr($item_class) .'">';

					if( $style == 'exposure' ){

						// style: Exposure

						$output .= $link_before_escaped;

							// photo

							$output .= '<div class="image-wrapper scale-with-grid">';
								$output .= get_the_post_thumbnail( get_the_ID(), 'full', array( 'class'=>'scale-with-grid', 'itemprop'=>'image' ) );
								$output .= '<div class="mask"></div>';
							$output .= '</div>';

							// title

							$output .= '<div class="desc-inner">';
								$output .= '<div class="section_wrapper">';
									$output .= '<div class="desc-wrapper-inner">';

										$output .= '<div class="line"></div>';
										$output .= '<h2 class="entry-title" itemprop="headline">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h2>';

										$output .= '<div class="desc-wrappper">';
											$output .= get_the_excerpt();
										$output .= '</div>';

									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';

						$output .= '</a>';

						// details

						$output .= '<div class="details-wrapper">';
							$output .= '<div class="section_wrapper">';
								$output .= '<div class="details-wrapper-inner">';

									if( $link = get_post_meta( get_the_ID(), 'mfn-post-link', true ) ){
										$output .= '<div class="column one-fourth website">';
											$output .= '<h5 class="label">'. esc_html($translate['website']) .'</h5>';
											$output .= '<h5><a target="_blank" href="'. esc_url($link) .'"><i class="icon-forward"></i>'. esc_html($translate['view']) .'</a></h5>';
										$output .= '</div>';
									}

									if( $client = get_post_meta( get_the_ID(), 'mfn-post-client', true ) ){
										$output .= '<div class="column one-fourth client">';
											$output .= '<h5 class="label">'. esc_html($translate['client']) .'</h5>';
											$output .= '<h5>'. esc_html($client) .'</h5>';
										$output .= '</div>';
									}

									if( isset( $list_meta['date'] ) ){
										$output .= '<div class="column one-fourth date">';
											$output .= '<h5 class="label">'. esc_html($translate['date']) .'</h5>';
											$output .= '<h5>'. esc_html(get_the_date()) .'</a></h5>';
										$output .= '</div>';
									}

								$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';

					} elseif( $style == 'masonry-minimal' ){

						// style: Masonry Minimal

							$output .= '<div class="image_frame scale-with-grid">';
								$output .= '<div class="image_wrapper">';
									$output .= mfn_post_thumbnail( get_the_ID(), 'portfolio', 'masonry-minimal' );
								$output .= '</div>';
							$output .= '</div>';


					} elseif( $style == 'masonry-hover' ){

						// style: Masonry Hover

						$output .= '<div class="masonry-hover-wrapper">';

							// desc

							$output .= '<div class="hover-desc '. esc_attr($item_bg_class) .'" style="'. esc_attr($item_bg_color) .'">';

								$output .= '<div class="desc-inner">';

									$output .= '<h3 class="entry-title" itemprop="headline">'. $link_before_escaped . wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h3>';
									$output .= '<div class="desc-wrappper">';
										$output .= get_the_excerpt();
									$output .= '</div>';

								$output .= '</div>';

								if( $external != 'disable' ){
									$output .= '<div class="links-wrappper clearfix">';

										if( ! in_array( $external, array('_self','_blank') ) ){
											$output .= '<a class="zoom" href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto"><i class="icon-search"></i></a>';
										}
										if( $ext_link ){
											$output .= '<a class="external" target="_blank" href="'. esc_url($ext_link) .'" ><i class="icon-forward"></i></a>';
										}
										if( ! $external ){
											$output .= $link_before_escaped. '<i class="icon-link"></i></a>';
										}

									$output .= '</div>';
								}

							$output .= '</div>';

							// photo

							$output .= '<div class="image-wrapper scale-with-grid">';
								$output .= $link_before_escaped;
									$output .= get_the_post_thumbnail( get_the_ID(), 'full', array( 'class'=>'scale-with-grid', 'itemprop'=>'image' ) );
								$output .= '</a>';
							$output .= '</div>';

						$output .= '</div>';

					} else {

						// style: default

						$output .= '<div class="portfolio-item-fw-bg" style="'. esc_attr($item_bg_color) . esc_attr($item_bg_image) .'">';

							$output .= '<div class="portfolio-item-fill"></div>';

							// style: List | Section Wrapper

							if( $style == 'list' ){
								$output .= '<div class="section_wrapper">';
							}

								// style: list | desc

								$output .= '<div class="list_style_header">';
									$output .= '<h3 class="entry-title" itemprop="headline">'. $link_before_escaped . wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h3>';
									$output .= '<div class="links_wrapper">';
										$output .= '<a href="#" class="button button_js portfolio_prev_js"><span class="button_icon"><i class="icon-up-open"></i></span></a>';
										$output .= '<a href="#" class="button button_js portfolio_next_js"><span class="button_icon"><i class="icon-down-open"></i></span></a>';
										$output .= '<a href="'. esc_url(get_permalink()) .'" class="button button_left button_theme button_js"><span class="button_icon"><i class="icon-link"></i></span><span class="button_label">'. esc_html($translate['readmore']) .'</span></a>';
									$output .= '</div>';
								$output .= '</div>';

								// style: default | photo

								$output .= '<div class="image_frame scale-with-grid">';
									$output .= '<div class="image_wrapper">';
										$output .= mfn_post_thumbnail( get_the_ID(), 'portfolio', $style );
									$output .= '</div>';
								$output .= '</div>';

								// style: default | desc

								$output .= '<div class="desc">';

									$output .= '<div class="title_wrapper">';
										$output .= '<h5 class="entry-title" itemprop="headline">'. $link_before_escaped . wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h5>';
										$output .= '<div class="button-love">'. mfn_love() .'</div>';
									$output .= '</div>';

									$output .= '<div class="details-wrapper">';
										$output .= '<dl>';

											if( $client = get_post_meta( get_the_ID(), 'mfn-post-client', true ) ){
												$output .= '<dt>'. esc_html($translate['client']) .'</dt>';
												$output .= '<dd>'. esc_html($client) .'</dd>';
											}

											if( isset( $list_meta['date'] ) ){
												$output .= '<dt>'. esc_html($translate['date']) .'</dt>';
												$output .= '<dd>'. esc_html(get_the_date()) .'</dd>';
											}

											if( $link = get_post_meta( get_the_ID(), 'mfn-post-link', true ) ){
												$output .= '<dt>'. esc_html($translate['website']) .'</dt>';
												$output .= '<dd><a target="_blank" href="'. esc_url($link) .'"><i class="icon-forward"></i>'. esc_html($translate['view']) .'</a></dd>';
											}

										$output .= '</dl>';
									$output .= '</div>';

									$output .= '<div class="desc-wrapper">';
										$output .= get_the_excerpt();
									$output .= '</div>';

								$output .= '</div>';

							// style: List | end: Section Wrapper

							if( $style == 'list' ){
								$output .= '</div>';
							}

						$output .= '</div>';

					}

				$output .= '</li>';

			}
		}

		return $output;
	}
}
