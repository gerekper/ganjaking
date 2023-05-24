<?php
/**
 * Image thumbnail template
 * Sets post image based on featured image
 * or custom field
 *
 * @since 1.0.0
 * @version 2.2.1
 */

$thumblink = '';

// Show video embed if available
if ( $show_embed ) {
	$content = apply_filters( 'the_content', get_the_content() );
	$video = false;

	// Only get video from the content if a playlist isn't present.
	if ( false === strpos( $content, 'wp-playlist-script' ) ) {
		$video = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
	}

	if ( is_array( $video ) && isset( $video[0] ) ) {
		if ( preg_match("/wp-video-shortcode/", $video[0]) ) {
			$thumblink = '<div class="post-img">' . $video[0] . '</div>';
		}
		else {
			$thumblink = '<div class="post-img"><div class="embed-wrap">' . $video[0] . '</div></div>';
		}
	}

}

// Show post thumbnail if available
if ( $show_thumbnail && '' === $thumblink ) {

	// Post format icons
	$format_icon = $caption = '';
	if ( ( 'video' == get_post_format() || 'gallery' == get_post_format() ) && $post_format_icon && $show_thumbnail ) {
		$format_icon = '<div class="' . get_post_format() . '-overlay"></div>';
	}

	if ( 'featured' == $img_source && has_post_thumbnail() ) {
		$imgwidth = ( '' == $imgwidth ) ? 800 : $imgwidth;
		$imgheight = ( '' == $imgheight ) ? 600 : $imgheight;
		$img_anchor = '';
		if ( 'none' == $imglink ) {
			$img_anchor = $enable_schema ? get_the_post_thumbnail( get_the_id(), array( floatval( $imgwidth ), floatval( $imgheight ), 'bfi_thumb' => $bfi, 'crop' => $imgcrop, 'quality' => floatval($imgquality), 'grayscale' => $imggrayscale ), array( 'itemprop' => 'url' ) ) : get_the_post_thumbnail( get_the_id(), array( floatval($imgwidth), floatval($imgheight), 'bfi_thumb' => $bfi, 'crop' => $imgcrop, 'quality' => floatval($imgquality), 'grayscale' => $imggrayscale ) );
		} else {
			$img_anchor = sprintf( '<a href="%1$s" title="%2$s"%3$s>%4$s%5$s</a>',
				'media' == $imglink ? wp_get_attachment_url( get_post_thumbnail_id( get_the_id() ), 'full' ) : esc_url( get_permalink() ),
				wp_strip_all_tags( $title ),
				( ( 'media' == $imglink ) && $imglightbox ) ? ' data-elementor-open-lightbox="yes"' : '',
				$enable_schema ? get_the_post_thumbnail( get_the_id(), array( floatval( $imgwidth ), floatval( $imgheight ), 'bfi_thumb' => $bfi, 'crop' => $imgcrop, 'quality' => floatval($imgquality), 'grayscale' => $imggrayscale ), array( 'itemprop' => 'url' ) ) : get_the_post_thumbnail( get_the_id(), array( floatval($imgwidth), floatval($imgheight), 'bfi_thumb' => $bfi, 'crop' => $imgcrop, 'quality' => floatval($imgquality), 'grayscale' => $imggrayscale ) ),
				$format_icon
			);
		}
		$thumblink = sprintf( apply_filters( 'wppm_grid_thumbnail_s1', '<div%1$s class="post-img%2$s">%3$s%4$s</div>' ),
			$enable_schema ? ' itemprop="image" itemscope itemtype="' . $protocol . '://schema.org/ImageObject"' : '',
			$enable_captions && get_the_post_thumbnail_caption() ? ' has-caption' : '',
			$img_anchor,
			$enable_captions && get_the_post_thumbnail_caption() ? '<p class="wp-caption-text">' . get_the_post_thumbnail_caption() . '</p>' : ''
		);
	} // featured == $img_source

	if ( 'meta_box' == $img_source || 'custom_field' == $img_source ) {
		$img_url = '';
		if ( 'meta_box' == $img_source ) {
			$meta_box_arr = get_post_meta( $post_id, $img_meta_box, true );
			if ( isset( $meta_box_arr ) && is_array( $meta_box_arr ) && isset( $meta_box_arr[ $img_cust_field_key ] ) && '' !==  $meta_box_arr[ $img_cust_field_key ] ) {
				if ( filter_var( $meta_box_arr[ $img_cust_field_key ], FILTER_VALIDATE_URL ) ) {
					$img_url = $meta_box_arr[ $img_cust_field_key ];
				} else {
					$img_url = wp_get_attachment_url( intval( $meta_box_arr[ $img_cust_field_key ] ) );
				}
			}
		} elseif ( 'custom_field' == $img_source ) {
			$cust_field_img = get_post_meta( $post_id, $img_cust_field_key, true );
			if ( isset( $cust_field_img ) && '' !== $cust_field_img ) {
				if ( filter_var( $cust_field_img, FILTER_VALIDATE_URL ) ) {
					$img_url = $cust_field_img;
				} else {
					$img_url = wp_get_attachment_url( intval( $cust_field_img ) );
				}
			}
		}

		// BFI image resize
		if ( $bfi && $img_url ) {
			$img_url = bfi_thumb ( $img_url, array( 'width' => intval( $imgwidth ), 'height' => intval( $imgheight ), 'crop' => $imgcrop, 'quality' => intval( $imgquality ) ) );
		}

		if ( $img_url ) {
			if ( 'none' == $imglink ) {
				$img_anchor = sprintf( '<img src="%1$s" alt="%2$s"%3$s />',
					esc_url( $img_url ),
					wp_strip_all_tags( $title ),
					$enable_schema ? ' itemprop="url"' : ''
				);
			} else {
				$img_anchor = sprintf( '<a href="%1$s" title="%2$s"%3$s>%4$s%5$s</a>',
					'media' == $imglink ? esc_url( $img_url ) : esc_url( get_permalink() ),
					wp_strip_all_tags( $title ),
					( ( 'media' == $imglink ) && $imglightbox ) ? ' data-elementor-open-lightbox="yes"' : '',
					sprintf( '<img src="%1$s" alt="%2$s"%3$s />',
						esc_url( $img_url ),
						wp_strip_all_tags( $title ),
						$enable_schema ? ' itemprop="url"' : ''
					),
					$format_icon
				);
			}
			$thumblink = sprintf( apply_filters( 'wppm_grid_thumbnail_s1', '<div%1$s class="post-img">%2$s</div>' ),
				$enable_schema ? ' itemprop="image" itemscope itemtype="' . $protocol . '://schema.org/ImageObject"' : '',
				$img_anchor
			);
		} // $img_url

	} // meta_box == $img_source

} // show_thumbnail