<?php

add_shortcode('pafe_gallery', 'pafe_gallery_shortcode');

function pafe_gallery_shortcode( $attr ) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	$title_source = $title_key = $caption_source = $caption_key = $thumbnail_custom_size = $thumbnail_first = '';

	
	if (!empty($attr['title_source'])) {
		$title_source = $attr['title_source'];
	}
	if (!empty($attr['title_key'])) {
		$title_key = $attr['title_key'];
	}
	if (!empty($attr['caption_source'])) {
		$caption_source = $attr['caption_source'];
	}
	if (!empty($attr['caption_key'])) {
		$caption_key = $attr['caption_key'];
	}
	if (!empty($attr['thumbnail_custom_size'])) {
		$thumbnail_custom_size = $attr['thumbnail_custom_size'];
	}
	if (!empty($attr['thumbnail_first'])) {
		$thumbnail_first = $attr['thumbnail_first'];
	}
	
	/**
	 * Filters the default gallery shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default gallery template.
	 *
	 * @since 2.5.0
	 * @since 4.2.0 The `$instance` parameter was added.
	 *
	 * @see gallery_shortcode()
	 *
	 * @param string $output   The gallery output. Default empty.
	 * @param array  $attr     Attributes of the gallery shortcode.
	 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
	 */
	//$output = apply_filters( 'post_gallery', '', $attr, $instance );
	$output = '';
	if ( $output != '' ) {
		return $output;
	}

	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );

	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	} else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		}
		return $output;
	}

	$itemtag = tag_escape( $atts['itemtag'] );
	$captiontag = tag_escape( $atts['captiontag'] );
	$icontag = tag_escape( $atts['icontag'] );
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		$itemtag = 'dl';
	}
	if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		$captiontag = 'dd';
	}
	if ( ! isset( $valid_tags[ $icontag ] ) ) {
		$icontag = 'dt';
	}

	$columns = intval( $atts['columns'] );
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = '';

	/**
	 * Filters whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */

	$size_class = sanitize_html_class( $atts['size'] );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}' data-pafe-lightbox-gallery-inner ";

	if( !empty($attr['masonry']) ) {
		if( $attr['masonry'] == 'yes' ) {
			$gallery_div .= " data-pafe-masonry";
		}
	}

	if( !empty($attr['light_skin']) ) {
		if( $attr['light_skin'] == 'yes' ) {
			$gallery_div .= " data-pafe-lightbox-gallery-light-skin";
		}
	} else {
		$gallery_div .= " data-pafe-lightbox-gallery-dark-skin";
	}

	if( !empty($attr['background_color']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-background-color='" . $attr['background_color'] . "'";
	} else {
		if( !empty($attr['light_skin']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-background-color='#ffffff'";
		} else {
			$gallery_div .= " data-pafe-lightbox-gallery-background-color='#000000'";
		}
	}

	if( !empty($attr['background_opacity']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-background-opacity='" . $attr['background_opacity'] . "'";
	} else {
		$gallery_div .= " data-pafe-lightbox-gallery-background-opacity=1";
	}

	if( isset($attr['facebook']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-facebook='" . $attr['facebook'] . "'";
	} else {
		if (!empty($attr['facebook'])) {
			$gallery_div .= " data-pafe-lightbox-gallery-facebook='yes'";
		}
	}

	if( isset($attr['tweeter']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-tweeter='" . $attr['tweeter'] . "'";
	} else {
		if (!empty($attr['tweeter'])) {
			$gallery_div .= " data-pafe-lightbox-gallery-tweeter='yes'";
		}
	}

	if( isset($attr['pinterest']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-pinterest='" . $attr['pinterest'] . "'";
	} else {
		if (!empty($attr['pinterest'])) {
			$gallery_div .= " data-pafe-lightbox-gallery-pinterest='yes'";
		}
	}

	if( isset($attr['download_image']) ) {
		$gallery_div .= " data-pafe-lightbox-gallery-download-image='" . $attr['download_image'] . "'";
	} else {
		if (!empty($attr['download_image'])) {
			$gallery_div .= " data-pafe-lightbox-gallery-download-image='yes'";
		}
	}

	if( !empty($thumbnail_first) ) {
		$gallery_div .= " data-pafe-thumbnail-first";
	}

	$gallery_div .= ">";

	/**
	 * Filters the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default CSS styles and opening HTML div container
	 *                              for the gallery shortcode output.
	 */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$i++;

		$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
		if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
			$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
		} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
			$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
		} else {
			$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
		}
		$image_output = strip_tags($image_output,'<img>');

		if( !empty($thumbnail_custom_size) ) {
			if( $thumbnail_custom_size == 'yes' ) {
				$image_output = "<div class='pafe-lightbox-gallery__item-inner' style='background-image: url(" . wp_get_attachment_url($id) . ");'></div>";
			}
		}

		if ($i > 1 && !empty($thumbnail_first)) {
			$image_output = "<div class='pafe-lightbox-gallery__item-inner' style='background-image: url(" . wp_get_attachment_url($id) . ");'></div>";
		}

		$image_meta  = wp_get_attachment_metadata( $id );

		$image = get_post($id);
		$image_title = '';
		$image_caption = '';

		if( $title_source == 'attachment' ) {
			$image_title = $image->post_title;
		}
		if( $title_source == 'custom_field' ) {
			$image_title = get_post_meta( $id, $title_key, true );
		}
		if( $title_source == 'acf_field' ) {
			if (function_exists('get_field')) {
				$image_title = get_field($title_key,$id);
			}
		}

		if( $caption_source == 'attachment' ) {
			$image_caption = $image->post_excerpt;
		}
		if( $caption_source == 'description' ) {
			$image_caption = $image->post_content;
		}
		if( $caption_source == 'custom_field' ) {
			$image_caption = get_post_meta( $id, $caption_key, true );
		}
		if( $caption_source == 'acf_field' ) {
			if (function_exists('get_field')) {
				$image_caption = get_field($caption_key,$id);
			}
		}

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}

		if ($i > 1 && !empty($thumbnail_first)) {
			$output .= "<div class='pafe-pswp'><a class='gallery-item' style='display:none' data-href='" . wp_get_attachment_url($id) . "' data-med='" . wp_get_attachment_url($id) . "' data-width='" . $image_meta['width'] . "' data-height='" . $image_meta['height'] . "' data-med-size='" . $image_meta['width'] . "x" . $image_meta['height'] . "'>";
		} else {
			$output .= "<div class='pafe-pswp'><a class='gallery-item' data-href='" . wp_get_attachment_url($id) . "' data-med='" . wp_get_attachment_url($id) . "' data-width='" . $image_meta['width'] . "' data-height='" . $image_meta['height'] . "' data-med-size='" . $image_meta['width'] . "x" . $image_meta['height'] . "'>";
		}

		$output .= "$image_output";

		if( $title_source != 'none' || $caption_source != 'none' ) {
			$output .= '<div class="pafe-lightbox__text">';
			if( $title_source != 'none' ) {
				$output .= '<div class="pafe-lightbox__title"><strong>' . $image_title . '</strong></div>';
			}
			if( $caption_source != 'none' ) {
				$output .= '<div class="pafe-lightbox__caption">' . $image_caption . '</div>';
			}
			$output .= '</div>';
		}

		$output .= "</a></div>";

	}

	$output .= "</div>\n";

	return $output;
}
?>