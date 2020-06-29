<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Popup
if( ! empty( $slides['properties']['attrs']['type'] ) && $slides['properties']['attrs']['type'] === 'popup' ) {
	$slides['properties']['props']['width']  = ! empty( $slides['properties']['props']['popupWidth'] ) ? $slides['properties']['props']['popupWidth'] : 640;
	$slides['properties']['props']['height'] = ! empty( $slides['properties']['props']['popupHeight']) ? $slides['properties']['props']['popupHeight'] : 360;
}

// Get slider style
$sliderStyleAttr[] = 'width:'.layerslider_check_unit($slides['properties']['props']['width']).';';

if( ( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'fullsize' ) && ( empty($slides['properties']['attrs']['fullSizeMode']) || $slides['properties']['attrs']['fullSizeMode'] !== 'fitheight' ) ) {
	$sliderStyleAttr[] = 'height:100vh;';
} else {
	$sliderStyleAttr[] = 'height:'.layerslider_check_unit($slides['properties']['props']['height']).';';
}

if(!empty($slides['properties']['props']['maxwidth'])) {
	$sliderStyleAttr[] = 'max-width:'.layerslider_check_unit($slides['properties']['props']['maxwidth']).';';
}

$sliderStyleAttr[] = 'margin:0 auto;';
if(isset($slides['properties']['props']['sliderStyle'])) {
	$sliderStyleAttr[] = $slides['properties']['props']['sliderStyle'];
}

// Gutenberg Margin Options
if( ! empty( $embed['marginTop'] ) ) { $sliderStyleAttr[] = 'margin-top: '.layerslider_check_unit( $embed['marginTop'] ).';'; }
if( ! empty( $embed['marginRight'] ) ) { $sliderStyleAttr[] = 'margin-right: '.layerslider_check_unit( $embed['marginRight'] ).';'; }
if( ! empty( $embed['marginBottom'] ) ) { $sliderStyleAttr[] = 'margin-bottom: '.layerslider_check_unit( $embed['marginBottom'] ).';'; }
if( ! empty( $embed['marginLeft'] ) ) { $sliderStyleAttr[] = 'margin-left: '.layerslider_check_unit( $embed['marginLeft'] ).';'; }

// Before slider content hook
if(has_action('layerslider_before_slider_content')) {
	do_action('layerslider_before_slider_content');
}

// Wrap Popups
if( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'popup' ) {
	$lsContainer[] = '<div class="ls-popup">';
}

$customClasses = '';
if( ! empty( $slides['properties']['props']['sliderclass'] ) ) {
	$customClasses = ' '.$slides['properties']['props']['sliderclass'];
}

if( ! empty( $embed['className'] ) ) {
	$customClasses .= ' '.$embed['className'];
}

$useSrcset = true;
if( isset($slides['properties']['attrs']['useSrcset']) && $slides['properties']['attrs']['useSrcset'] === false ) {
	$useSrcset = false;
}

$enhancedLazyLoad = false;
if( ! empty( $slides['properties']['props']['enhancedLazyLoad'] ) ) {
	$enhancedLazyLoad = true;
}

// Start of slider container
$lsContainer[] = '<div id="'.$sliderID.'" class="ls-wp-container fitvidsignore'.$customClasses.'" style="'.implode('', $sliderStyleAttr).'">';

// Add slides
if(!empty($slider['slides']) && is_array($slider['slides'])) {
	foreach($slider['slides'] as $slidekey => $slide) {

		// Skip this slide?
		if(!empty($slide['props']['skip'])) { continue; }

		// Get slide attributes
		$slideId = !empty($slide['props']['id']) ? ' id="'.$slide['props']['id'].'"' : '';
		$slideAttrs = !empty($slide['attrs']) ? ls_array_to_attr($slide['attrs']) : '';

		if( ! empty( $slide['props']['customProperties'] ) && is_array( $slide['props']['customProperties'] ) ) {
			$slideAttrs .= ls_array_to_attr( $slide['props']['customProperties'] );
		}

		$postContent = false;


		// Check for the origami plugin
		if( ! empty( $slide['attrs']['transitionorigami'] ) ) {
			$lsPlugins[] = 'origami';
		}

		// Post content
		//if( !isset($slide['props']['post_content']) || $slide['props']['post_content']) {
			$queryArgs = array(
				'post_status' => 'publish',
				'limit' => 1,
				'posts_per_page' => 1,
				'suppress_filters' => false
			);


			if(isset($slide['props']['post_offset'])) {
				if($slide['props']['post_offset'] == -1) {
					$slide['props']['post_offset'] = $slidekey;
				}

				$queryArgs['offset'] = $slide['props']['post_offset'];
			}

			if(!empty($slides['properties']['props']['post_type'])) {
				$queryArgs['post_type'] = $slides['properties']['props']['post_type']; }

			if(!empty($slides['properties']['props']['post_orderby'])) {
				$queryArgs['orderby'] = $slides['properties']['props']['post_orderby']; }

			if(!empty($slides['properties']['props']['post_order'])) {
				$queryArgs['order'] = $slides['properties']['props']['post_order']; }

			if(!empty($slides['properties']['props']['post_categories'][0])) {
				$queryArgs['category__in'] = $slides['properties']['props']['post_categories']; }

			if(!empty($slides['properties']['props']['post_tags'][0])) {
				$queryArgs['tag__in'] = $slides['properties']['props']['post_tags']; }

			if(!empty($slides['properties']['props']['post_taxonomy']) && !empty($slides['properties']['props']['post_tax_terms'])) {
				$queryArgs['tax_query'][] = array(
					'taxonomy' => $slides['properties']['props']['post_taxonomy'],
					'field' => 'id',
					'terms' => $slides['properties']['props']['post_tax_terms']
				);
			}

			$postContent = LS_Posts::find($queryArgs);
		//}

		// Start of slide
		$slideAttrs = !empty($slideAttrs) ? 'data-ls="'.$slideAttrs.'"' : '';
		$lsMarkup[] = '<div class="ls-slide"'.$slideId.' '.$slideAttrs.'>';

		// Add slide background
		if( ! empty($slide['props']['background'])) {
			$lsBG = '';
			$alt = '';

			if( ! empty($slide['props']['backgroundId'])) {
				$lsBG = wp_get_attachment_image($slide['props']['backgroundId'], 'full', false, array('class' => 'ls-bg'));

			} elseif($slide['props']['background'] == '[image-url]') {
				$src = $postContent->getWithFormat($slide['props']['background']);

				if(is_object($postContent->post)) {
					$attchID = get_post_thumbnail_id($postContent->post->ID);
					$lsBG = wp_get_attachment_image($attchID, 'full', false, array('class' => 'ls-bg'));
				}
			} else {
				$src = do_shortcode($slide['props']['background']);
				$alt = 'Slide background';
			}

			if( ! empty( $lsBG ) ) {

				if( ! $useSrcset ) {
					$lsBG = preg_replace('/srcset="[^\"]*"/', '', $lsBG);
					$lsBG = preg_replace('/sizes="[^\"]*"/', '', $lsBG);
				}

				if( $enhancedLazyLoad ) {
					$lsBG = str_replace(' src="', ' data-src="', $lsBG);
					$lsBG = str_replace(' srcset="', ' data-srcset="', $lsBG);
				}

				$lsMarkup[] = $lsBG;
			} elseif( ! empty( $src ) ) {
				$lsMarkup[] = '<img src="'.$src.'" class="ls-bg" alt="'.$alt.'" />';
			}
		}

		// Add slide thumbnail
		if(!isset($slides['properties']['attrs']['thumbnailNavigation']) || $slides['properties']['attrs']['thumbnailNavigation'] != 'disabled') {
			if(!empty($slide['props']['thumbnail'])) {

				$lsTN = '';
				if( ! empty($slide['props']['thumbnailId']) ) {
					$lsTN = wp_get_attachment_image($slide['props']['thumbnailId'], 'full', false, array('class' => 'ls-tn'));
				}

				if( ! empty( $lsTN ) && ! $useSrcset ) {
					$lsTN = preg_replace('/srcset="[^\"]*"/', '', $lsTN);
					$lsTN = preg_replace('/sizes="[^\"]*"/', '', $lsTN);
				}

				if( ! empty( $lsTN ) && $enhancedLazyLoad ) {
					$lsTN = str_replace(' src="', ' data-src="', $lsTN);
					$lsTN = str_replace(' srcset="', ' data-srcset="', $lsTN);
				}

				$lsMarkup[] = ! empty( $lsTN ) ? $lsTN : '<img src="'.$slide['props']['thumbnail'].'" class="ls-tn" alt="Slide thumbnail" />';
			}
		}

		// Add layers
		if(!empty($slide['layers']) && is_array($slide['layers'])) {
			foreach($slide['layers'] as $layerkey => $layer) {

				// Skip this slide?
				if(!empty($layer['props']['skip'])) { continue; }

				unset($layerAttributes);
				unset($innerAttributes);
				$layerAttributes = array('style' => '', 'class' => 'ls-l');
				$innerAttributes = array('style' => '', 'class' => '');

				if( empty( $layer['props']['url'] ) ) {
					$innerAttributes =& $layerAttributes;
				}

				if( empty( $layer['props']['styles'] ) ) {
					$layer['props']['styles'] = array();
				}

				// WPML support
				if( has_filter( 'wpml_translate_single_string' ) ) {

					// Check 'createdWith' property to decide which WPML implementation
					// should we use. This property was added in v6.5.5 along with the
					// new WPML implementation, so no version comparison required.
					if( ! empty( $slides['properties']['attrs']['createdWith'] ) ) {
						$string_name = "slider-{$id}-layer-{$layer['props']['uuid']}-html";
						$layer['props']['html'] = apply_filters( 'wpml_translate_single_string', $layer['props']['html'], 'LayerSlider Sliders', $string_name );

					// Old implementation
					} else {
						$string_name = '<'.$layer['props']['type'].':'.substr(sha1($layer['props']['html']), 0, 10).'> layer on slide #'.($slidekey+1).' in slider #'.$id.'';
						$layer['props']['html'] = apply_filters( 'wpml_translate_single_string', $layer['props']['html'], 'LayerSlider WP', $string_name);
					}

					// Fallback WPML support for older sliders.
					if( ! empty( $layer['props']['url'] ) ) {

						// Don't try to modify the URL if it's auto-generated
						if( empty( $layer['props']['linkId'] ) && $layer['props']['url'] !== '[post-url]' ) {

							// Don't try to modify the URL if it starts with a hash tag.
							$firstChar = function_exists('mb_substr') ? mb_substr( $layer['props']['url'], 0, 1 ) : substr( $layer['props']['url'], 0, 1 );
							if( $firstChar !== '#' ) {

								// Carry over the 'lang' URI param if it's set and the URL is non-relative, non-external
								if( ! empty( $_GET['lang'] ) && ( strpos($layer['props']['url'], 'http') !== 0 || strpos( $layer['props']['url'], $_SERVER['SERVER_NAME'] ) !== false ) ) {
									if(strpos($layer['props']['url'], '?') !== false) { $layer['props']['url'] .= '&amp;lang=' . ICL_LANGUAGE_CODE; }
										else { $layer['props']['url'] .= '?lang=' . ICL_LANGUAGE_CODE; }
								}
							}
						}
					}
				}

				// Get layer type
				$layer['props']['media'] = !empty($layer['props']['media']) ? $layer['props']['media'] : '';

				if( ! empty( $layer['props']['media'] ) ) {
					switch( $layer['props']['media'] ) {
						case 'img':
							$layer['props']['type'] = 'img';
							break;

						case 'button':
						case 'icon':
							$layer['props']['type'] = 'span';
							break;

						case 'html':
						case 'media':
							$layer['props']['type'] = 'div';
							break;

						case 'post':
							$layer['props']['type'] = 'div';
							break;
					}
				}

				// v6.6.7: Ensure default value for the 'type' key if it's
				// somehow missing.
				if( empty( $layer['props']['type'] ) ) {
					$layer['props']['type'] = 'div';
				}

				// Post layer
				if(!empty($layer['props']['media']) && $layer['props']['media'] == 'post') {
					$layer['props']['post_text_length'] = !empty($layer['props']['post_text_length']) ? $layer['props']['post_text_length'] : 0;
					$layer['props']['html'] = $postContent->getWithFormat($layer['props']['html'], $layer['props']['post_text_length']);
					$layer['props']['html'] = do_shortcode($layer['props']['html']);
				}

				// Skip image layer without src
				if($layer['props']['type'] == 'img' && empty($layer['props']['image'])) { continue; }

				// Create layer
				$first = substr($layer['props']['html'], 0, 1);
				$last = substr($layer['props']['html'], strlen($layer['props']['html'])-1, 1);

				// Image layer
				$layerIMG = false;
				if($layer['props']['type'] == 'img') {
					if( ! empty($layer['props']['imageId'])) {
						$layerIMG = wp_get_attachment_image( (int)$layer['props']['imageId'], 'full', false, array('class' => 'ls-l'));

					} elseif($layer['props']['image'] == '[image-url]') {

						if(is_object($postContent->post)) {
							$attchID = get_post_thumbnail_id($postContent->post->ID);
							$layerIMG = wp_get_attachment_image($attchID, 'full', false, array('class' => 'ls-l'));
						} else {
							$innerAttributes['src'] = $postContent->getWithFormat($layer['props']['image']);
						}

					} else {
						$innerAttributes['src'] = $layer['props']['image'];

						if(!empty($layer['props']['alt'])) {
						$innerAttributes['alt'] = $layer['props']['alt']; }
							else { 	$innerAttributes['alt'] = ''; }
					}
				}

				if($layer['props']['media'] == 'post' && ($first == '<' && $last == '>')) {
					$type = $layer['props']['html'];
				} else {

					if( ! empty( $layerIMG ) && ! $useSrcset ) {
						$layerIMG = preg_replace('/srcset="[^\"]*"/', '', $layerIMG);
						$layerIMG = preg_replace('/sizes="[^\"]*"/', '', $layerIMG);
					}

					if( ! empty( $layerIMG ) && $enhancedLazyLoad ) {
						$layerIMG = str_replace(' src="', ' data-src="', $layerIMG);
						$layerIMG = str_replace(' srcset="', ' data-srcset="', $layerIMG);
					}

					$type = ! empty($layerIMG) ? $layerIMG : '<'.$layer['props']['type'].'>';
				}


				// Linked layer
				if( ! empty( $layer['props']['url'] ) ) {

					// Create <a> element
					$el = LayerSlider\PHPQuery\phpQuery::newDocumentHTML('<a>')->children();

					// Auto-generated URL
					if( ! empty( $layer['props']['linkId'] ) ) {

						// Smart Links
						if( '#' === substr( $layer['props']['linkId'], 0, 1 ) ) {
							$layer['props']['url'] = $layer['props']['linkId'];

						// Dynamic Layer
						} elseif( '[post-url]' === $layer['props']['linkId'] ) {
							$layer['props']['url'] = $postContent->getWithFormat('[post-url]');

						// Attachment
						} elseif( ! empty( $layer['props']['linkType'] ) && $layer['props']['linkType'] === 'attachment' ) {
							$layer['props']['url'] = wp_get_attachment_url( $layer['props']['linkId'] );

						// Page / Post
						} else {
							$layer['props']['url'] = get_permalink( $layer['props']['linkId'] );
						}
					}


					if( $layer['props']['url'] === '[post-url]' ) {
						$layer['props']['url'] = $postContent->getWithFormat('[post-url]');
					}

					$layerAttributes['href'] = ! empty( $layer['props']['url'] ) ? $layer['props']['url'] : '#';

					if(!empty($layer['props']['target'])) {
						$layerAttributes['target'] =  $layer['props']['target'];
					}

					$inner = $el->append($type)->children();

				} else {
					$el = $inner = LayerSlider\PHPQuery\phpQuery::newDocumentHTML($type)->children();
				}

				// HTML attributes
				$layerAttributes['class'] = 'ls-l';

				if(!empty($layer['props']['id'])) { $innerAttributes['id'] = $layer['props']['id']; }
				if(!empty($layer['props']['class'])) { $innerAttributes['class'] .= ' '.$layer['props']['class']; }
				if(!empty($layer['props']['url'])) {
					if(!empty($layer['props']['rel'])) {
						$layerAttributes['rel'] = $layer['props']['rel']; }
					if(!empty($layer['props']['title'])) {
						$layerAttributes['title'] = $layer['props']['title']; }
				} else {
					if(!empty($layer['props']['title'])) {
						$innerAttributes['title'] = $layer['props']['title']; }
				}


				if( ! empty( $layer['props']['posterId'] ) ) {

					$poster = wp_get_attachment_image_src( $layer['props']['posterId'], 'full', false );
					$poster = ! empty( $poster[0] ) ? $poster[0]: '';

					$layer['attrs']['poster'] = $poster;
				}


				if(isset($layer['attrs']) && isset($layer['props']['transition'])) { $layerAttributes['data-ls'] = ls_array_to_attr($layer['attrs']); }
					elseif(isset($layer['attrs'])) { $layerAttributes['style'] .= ls_array_to_attr($layer['attrs']); }

				if(!empty($layer['props']['style'])) {
					if(substr($layer['props']['style'], -1) != ';') { $layer['props']['style'] .= ';'; }
					$innerAttributes['style'] .= preg_replace('/\s\s+/', ' ', $layer['props']['style']);
				}

				if( ! empty( $layer['props']['layerBackground']) ) {

					if( ! empty( $layer['props']['layerBackgroundId'] ) ) {

						$layerBG = wp_get_attachment_image_src( $layer['props']['layerBackgroundId'], 'full', false );
						$layerBG = ! empty( $layerBG[0] ) ? $layerBG[0]: '';

					} elseif( $layer['props']['layerBackground'] === '[image-url]' ) {
						$layerBG = $postContent->getWithFormat( $layer['props']['layerBackground'] );

					} else {
						$layerBG = do_shortcode( $slide['props']['layerBackground'] );
					}

					$layer['props']['styles']['background-image'] = 'url("'.$layerBG.'")';
				}


				$innerAttributes['style'] .= ls_array_to_attr($layer['props']['styles'], 'css');

				// Text / HTML layer
				if($layer['props']['media'] != 'post' || ($first != '<' && $last != '>')) {
					$inner->html(do_shortcode(__(stripslashes($layer['props']['html']))));
				}

				// Rewrite Youtube/Vimeo iframe src to data-src
				$video = $inner->find('iframe[src*="youtube-nocookie.com"], iframe[src*="youtube.com"], iframe[src*="youtu.be"], iframe[src*="player.vimeo"]');
				if( $video->length ) {
					$video->attr('data-src', $video->attr('src') );
					$video->removeAttr('src');
				}

				// Device dependent responsive classes
				if( ! empty($layer['props']['hide_on_desktop']) ) {
					$layerAttributes['class'] .=  ' ls-hide-desktop';
				}

				if( ! empty($layer['props']['hide_on_tablet']) ) {
					$layerAttributes['class'] .= ' ls-hide-tablet';
				}

				if( ! empty($layer['props']['hide_on_phone']) ) {
					$layerAttributes['class'] .= ' ls-hide-phone';
				}

				$el->attr( $layerAttributes );
				$inner->attr( $innerAttributes );

				if( ! empty( $layer['props']['outerAttributes'] ) ) {
					foreach( $layer['props']['outerAttributes'] as $key => $val ) {
						if( $key === 'class' ) {
							$el->addClass( $val );
						} else {
							$el->attr( $key, $val );
						}
					}
				}

				if( ! empty( $layer['props']['innerAttributes'] ) ) {
					foreach( $layer['props']['innerAttributes'] as $key => $val ) {
						if( $key === 'class' ) {
							$inner->addClass( $val );
						} else {
							$inner->attr( $key, $val );
						}
					}
				}

				$lsMarkup[] = $el;
				LayerSlider\PHPQuery\phpQuery::unloadDocuments();
			}
		}

		// Link this slide
		if( ! empty( $slide['props']['linkUrl'] ) ) {

			if( ! empty( $slide['props']['linkTarget'] ) ) {
				$target = ' target="'.$slide['props']['linkTarget'].'"';
			} else {
				$target = '';
			}

			if( ! empty( $slide['props']['linkId'] ) ) {

				// Smart Links
				if( '#' === substr( $slide['props']['linkId'], 0, 1 ) ) {
					$slide['props']['linkUrl'] = $slide['props']['linkId'];

				// Dynamic Layer
				} elseif( '[post-url]' === $slide['props']['linkId'] ) {
					$slide['props']['linkUrl'] = $postContent->getWithFormat('[post-url]');

				// Attachment
				} elseif( ! empty( $slide['props']['linkType'] ) && $slide['props']['linkType'] === 'attachment' ) {
					$slide['props']['linkUrl'] = wp_get_attachment_url( $slide['props']['linkId'] );

				// Page / Post
				} else {
					$slide['props']['linkUrl'] = get_permalink( $slide['props']['linkId'] );
				}
			}


			if( $slide['props']['linkUrl'] === '[post-url]' ) {
				$slide['props']['linkUrl'] = $postContent->getWithFormat('[post-url]');
			}

			// Fallback WPML support for older sliders
			if( has_filter( 'wpml_translate_single_string' ) ) {

				// Don't try to modify the URL if it's auto-generated
				if( empty( $slide['props']['linkId'] ) && $slide['props']['linkUrl'] !== '[post-url]' ) {

					// Carry over the 'lang' URI param if it's set and the URL is non-relative, non-external
					if( ! empty( $_GET['lang'] ) && ( strpos($slide['props']['linkUrl'], 'http') !== 0 || strpos( $slide['props']['linkUrl'], $_SERVER['SERVER_NAME'] ) !== false ) ) {
						if(strpos($slide['props']['linkUrl'], '?') !== false) { $slide['props']['linkUrl'] .= '&amp;lang=' . ICL_LANGUAGE_CODE; }
							else { $slide['props']['linkUrl'] .= '?lang=' . ICL_LANGUAGE_CODE; }
					}
				}
			}


			$linkClass = 'ls-link';
			if( empty( $slide['props']['linkPosition'] ) || $slide['props']['linkPosition'] === 'over' ) {
				$linkClass .= ' ls-link-on-top';
			}

			$slide['props']['linkUrl'] = ! empty( $slide['props']['linkUrl'] ) ? $slide['props']['linkUrl'] : '#';

			$lsMarkup[] = '<a href="'.$slide['props']['linkUrl'].'"'.$target.' class="'.$linkClass.'"></a>';
		}

		// End of slide
		$lsMarkup[] = '</div>';
	}
}

// End of slider container
$lsMarkup[] = '</div>';

// End of Popup wrapper
if( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'popup' ) {
	$lsMarkup[] = '</div>';
}

// After slider content hook
if(has_action('layerslider_after_slider_content')) {
	do_action('layerslider_after_slider_content');
}