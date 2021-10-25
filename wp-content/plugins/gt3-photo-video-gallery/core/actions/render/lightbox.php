<?php

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

add_filter('gt3pg_render_image_output_lightbox', function($return, $id, $atts, $img_alt, $media_class, $media_url, $attachment){
	$external_link = get_post_meta($id, 'gt3_external_link_url', true);
	$return        = gt3_el::Create('a')
	                       ->addAttrs(array( 'href' => wp_get_attachment_image_url($id, $atts['size']) ))
	                       ->addClasses(array( 'gt3pg_swipebox', $media_class ))
	                       ->addData('description', $attachment->post_content)
	                       ->addContent($return)
	;
	if(strlen($external_link)) {
		$return->addAttrs(array( 'href' => esc_url($external_link), 'target' => '_blank' ));
		$return->addClass('external-link');
	}

	return $return;
}, 10, 7);

add_filter('gt3pg_before_render_link', function($link, $atts){
	return in_array($atts['link'], array(
		'lightbox',
	)) ? $atts['link'] : $link;

}, 10, 2);

add_filter('gt3pg_after_render_gallery_lightbox', function($output, $atts, $gallery_json, $instance, $selector){
	$gallery_parts = array(
		'header'   => gt3_el::Create()->addClass('gt3pg_slide_header')
		                    ->addContent('20', '<div class="free-space"></div>')
		                    ->addContent('40', '<div class="gt3pg_close_wrap"><div class="gt3pg_close"></div> </div>'),
		'slides'   => '<div class="gt3pg_slides"></div>',
		'footer'   => gt3_el::Create()->addClass('gt3pg_slide_footer')
		                    ->addContent('10', '<div class="gt3pg_title_wrap">
                                        <div class="gt3pg_title gt3pg_clip"></div>
                                        <div class="gt3pg_description gt3pg_clip"></div>
                                    </div>')
		                    ->addContent('20', '<div class="free-space"></div>')
		                    ->addContent('40', '<div class="gt3pg_caption_wrap">
                                        <div class="gt3pg_caption_current"></div>
                                        <div class="gt3pg_caption_delimiter"></div>
                                        <div class="gt3pg_caption_all"></div>
                                    </div>'),
		'controls' => gt3_el::Create()->addClass('gt3pg_controls')
		                    ->addContent('10', '<div class="gt3pg_prev_wrap"><div class="gt3pg_prev"></div></div>')
		                    ->addContent('20', '<div class="gt3pg_next_wrap"><div class="gt3pg_next"></div></div>')
	);
	$footer        = '';
	$gallery_parts = apply_filters('gt3pg_render_lightbox_gallery_parts', $gallery_parts, $atts);
	$class         = apply_filters('gt3pg_before_render_lightbox_class_wrap', array(
		'gt3pg_gallery_wrap'  => 'gt3pg_gallery_wrap',
		'gt3pg_wrap_controls' => 'gt3pg_wrap_controls',
		'gt3_gallety_type'    => 'gt3_gallery_type_lightbox',
		'gt3pg_version_lite'  => 'gt3pg_version_lite'
	), $atts);

	$footer = gt3_el::Create()->addClasses($class)->addAttr('id', 'popup_'.$selector);
	if(is_array($gallery_parts) && count($gallery_parts)) {
		$footer->addContent(implode(PHP_EOL, $gallery_parts));
	}
	$footer = apply_filters('gt3pg_before_render_lightbox_wrap', $footer, $atts);

	$options    = array(
		'index'     => 'link'.$instance,
		'container' => '"#popup_'.$selector.'"',
		'event'     => 'event',
		'instance'  => $instance,
	);
	$options    = apply_filters('gt3pg_render_lightbox_options', $options, $atts);
	$pr_options = array();
	foreach($options as $k => $v) {
		$pr_options[] .= $k.': '.$v;
	}
	$options = implode(', '.PHP_EOL, $pr_options);

	$footer .= '<script>
						var '.$selector.' = null;
					document.addEventListener("DOMContentLoaded", function(event) {
						jQuery(function($) {
							$(\'#'.$selector.'\').on("click", ".gt3pg_element",function(event) {
								if (this.querySelector(".external-link")) return event;
								event.preventDefault();
								var target = event.target || event.srcElement,
													link'.$instance.' = $(this).index(),
													links'.$instance.' = ['.implode(",", $gallery_json).'],
													options'.$instance.' = { '.$options.' };
									'.$selector.'	=		blueimp.Gallery(links'.$instance.', options'.$instance.');
							});
						});
					});
				</script>';
	if(!isset($GLOBALS['gt3pg_footer'])) {
		$GLOBALS['gt3pg_footer'] = '';
	}
	$GLOBALS['gt3pg_footer'] .= $footer;

	return $output;
}, 10, 5);

