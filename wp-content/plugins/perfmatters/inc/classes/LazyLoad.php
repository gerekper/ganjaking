<?php

namespace Perfmatters;

class LazyLoad
{
	//initialize lazyload iframe functions
	public static function init_iframes()
	{
		add_action('wp', array('Perfmatters\LazyLoad', 'queue_iframes'));
	}

	//queue iframe functions
	public static function queue_iframes()
	{
		//check filters
		if(empty(apply_filters('perfmatters_lazyload', !empty(Config::$options['lazyload']['lazy_loading_iframes'])))) {
			return;
		}

		//skip woocommerce
		if(Utilities::is_woocommerce()) {
			return;
		}

		//check post meta
		if(Utilities::get_post_meta('perfmatters_exclude_lazy_loading')) {
			return;
		}

		add_action('perfmatters_output_buffer_template_redirect', array('Perfmatters\LazyLoad', 'iframes_buffer'));
	}

	//process iframe buffer
	public static function iframes_buffer($html) {

		$clean_html = Utilities::clean_html($html);

		$html = self::lazyload_iframes($html, $clean_html);
		$html = self::lazyload_videos($html, $clean_html);

		return $html;
	}

	//lazy load iframes
	public static function lazyload_iframes($html, $buffer) {

		//match all iframes
		preg_match_all('#<iframe(\s.+)>.*</iframe>#iUs', $buffer, $iframes, PREG_SET_ORDER);

		if(!empty($iframes)) {

			//remove any duplicates
			$iframes = array_unique($iframes, SORT_REGULAR);

			foreach($iframes as $iframe) {

				//get iframe attributes array
				$iframe_atts = Utilities::get_atts_array($iframe[1]);

				//dont check excluded if forced attribute was found
				if(!self::lazyload_excluded($iframe[1], self::lazyload_forced_atts())) {

					//skip if exluded attribute was found
					if(self::lazyload_excluded($iframe[1], self::lazyload_excluded_atts())) {
						continue;
					}

					//skip if no-lazy class is found
					if(!empty($iframe_atts['class']) && strpos($iframe_atts['class'], 'no-lazy') !== false) {
						continue;
					}
				}

				//skip if no src is found
				if(empty($iframe_atts['src'])) {
					continue;
				}

				//try rendering youtube preview placeholder if we need to
				if(!empty(Config::$options['lazyload']['youtube_preview_thumbnails'])) {
					$iframe['src'] = trim($iframe_atts['src']);
					$iframe_lazyload = self::lazyload_youtube_iframe($iframe);
				}
						
				//default iframe placeholder
				if(empty($iframe_lazyload)) {

					$iframe_atts['class'] = !empty($iframe_atts['class']) ? $iframe_atts['class'] . ' ' . 'perfmatters-lazy' : 'perfmatters-lazy';

					//migrate src
					$iframe_atts['data-src'] = $iframe_atts['src'];
					unset($iframe_atts['src']);

					//replace iframe attributes string
					$iframe_lazyload = str_replace($iframe[1], ' ' . Utilities::get_atts_string($iframe_atts), $iframe[0]);
					
					//add noscript original iframe
					if(apply_filters('perfmatters_lazyload_noscript', true)) {
						$iframe_lazyload.= '<noscript>' . $iframe[0] . '</noscript>';
					}
				}

				//replace iframe with placeholder
				$html = str_replace($iframe[0], $iframe_lazyload, $html);

				unset($iframe_lazyload);
			}
		}

		return $html;
	}

	//prep youtube iframe for lazy loading
	public static function lazyload_youtube_iframe($iframe) {

		if(!$iframe) {
			return false;
		}

		//attempt to get the id based on url
		$result = preg_match('#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)?([\w-]{11})#iU', $iframe['src'], $matches);

		//return false if there is no usable id
		if(!$result || $matches[1] === 'videoseries') {
			return false;
		}

		$youtube_id = $matches[1];

		//parse iframe src url
		$query = wp_parse_url(htmlspecialchars_decode($iframe['src']), PHP_URL_QUERY);

		//clean up the url
		$parsed_url = wp_parse_url($iframe['src'], -1);
		$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '//';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$youtube_url = $scheme . $host . $path;

		//thumbnail resolutions
		$resolutions = array(
			'default'       => array(
				'width'  => 120,
				'height' => 90,
			),
			'mqdefault'     => array(
				'width'  => 320,
				'height' => 180,
			),
			'hqdefault'     => array(
				'width'  => 480,
				'height' => 360,
			),
			'sddefault'     => array(
				'width'  => 640,
				'height' => 480,
			),
			'maxresdefault' => array(
				'width'  => 1280,
				'height' => 720,
			)
		);

		//filter set resolution
		$resolution = apply_filters('perfmatters_lazyload_youtube_thumbnail_resolution', 'hqdefault');

		//finished youtube lazy output
		$youtube_lazyload = '<div class="perfmatters-lazy-youtube" data-src="' . esc_attr($youtube_url) . '" data-id="' . esc_attr($youtube_id) . '" data-query="' . esc_attr($query) . '" onclick="perfmattersLazyLoadYouTube(this);">';
			$youtube_lazyload.= '<div>';
				$youtube_lazyload.= '<img src="https://i.ytimg.com/vi/' . esc_attr($youtube_id) .'/' . $resolution . '.jpg" alt="YouTube ' . __('video', 'perfmatters') . '" width="' . $resolutions[$resolution]['width'] . '" height="' . $resolutions[$resolution]['height'] . '" data-pin-nopin="true" nopin="nopin">';
				$youtube_lazyload.= '<div class="play"></div>';
			$youtube_lazyload.= '</div>';
		$youtube_lazyload.= '</div>';

		//noscript tag
		if(apply_filters('perfmatters_lazyload_noscript', true)) {
			$youtube_lazyload.= '<noscript>' . $iframe[0] . '</noscript>';
		}

		return $youtube_lazyload;
	}

	//lazy load videos
	public static function lazyload_videos($html, $buffer) {

		//match all videos
		preg_match_all('#<video(\s.+)>.*</video>#iUs', $buffer, $videos, PREG_SET_ORDER);

		if(!empty($videos)) {

			//remove any duplicates
			$videos = array_unique($videos, SORT_REGULAR);

			foreach($videos as $video) {

				//get video attributes array
				$video_atts = Utilities::get_atts_array($video[1]);

				//dont check excluded if forced attribute was found
				if(!self::lazyload_excluded($video[1], self::lazyload_forced_atts())) {

					//skip if exluded attribute was found
					if(self::lazyload_excluded($video[1], self::lazyload_excluded_atts())) {
						continue;
					}

					//skip if no-lazy class is found
					if(!empty($video_atts['class']) && strpos($video_atts['class'], 'no-lazy') !== false) {
						continue;
					}
				}

				//skip if no src is found
				if(empty($video_atts['src'])) {
					continue;
				}

				//add lazyload class
				$video_atts['class'] = !empty($video_atts['class']) ? $video_atts['class'] . ' ' . 'perfmatters-lazy' : 'perfmatters-lazy';

				//migrate src
				$video_atts['data-src'] = $video_atts['src'];
				unset($video_atts['src']);

				//replace video attributes string
				$video_lazyload  = str_replace($video[1], ' ' . Utilities::get_atts_string($video_atts), $video[0]);

				//add noscript original video
				if(apply_filters('perfmatters_lazyload_noscript', true)) {
					$video_lazyload .= '<noscript>' . $video[0] . '</noscript>';
				}

				//replace video with placeholder
				$html = str_replace($video[0], $video_lazyload, $html);

				unset($video_lazyload);
			}
		}

		return $html;
	}

	//initialize lazy loading
	public static function init_images()
	{
		add_action('wp', array('Perfmatters\LazyLoad', 'queue_images'));
	}

	//queue image functions
	public static function queue_images()
	{
		//check filters
		if(empty(apply_filters('perfmatters_lazyload', !empty(Config::$options['lazyload']['lazy_loading']) || !empty(Config::$options['lazyload']['lazy_loading_iframes']) || !empty(Config::$options['lazyload']['css_background_images'])))) {
			return;
		}

		//skip woocommerce
		if(Utilities::is_woocommerce()) {
			return;
		}

		//check post meta
		if(Utilities::get_post_meta('perfmatters_exclude_lazy_loading')) {
			return;
		}

		//disable wp native lazy loading
		add_filter('wp_lazy_loading_enabled', '__return_false');

		//actions + filters
		add_action('perfmatters_output_buffer_template_redirect', array('Perfmatters\LazyLoad', 'lazyload_buffer'));
		add_action('wp_enqueue_scripts', array('Perfmatters\LazyLoad', 'enqueue_scripts'));
		add_filter('script_loader_tag', array('Perfmatters\LazyLoad', 'async_script'), 10, 2);
		add_action('wp_head', array('Perfmatters\LazyLoad', 'lazyload_css'), PHP_INT_MAX);

		//images only
		if(!empty(Config::$options['lazyload']['lazy_loading'])) {
			add_filter('wp_get_attachment_image_attributes', function($attr) {
				unset($attr['loading']);
			  	return $attr;
			});
		}
	}

	//lazy load buffer
	public static function lazyload_buffer($html) {

		$buffer = Utilities::clean_html($html);

		//replace image tags
		if(!empty(Config::$options['lazyload']['lazy_loading'])) {
			$html = self::lazyload_parent_exclusions($html, $buffer);
			$html = self::lazyload_pictures($html, $buffer);
			$html = self::lazyload_background_images($html, $buffer);
			$html = self::lazyload_images($html, $buffer);
		}

		//replace css background elements
		if(!empty(Config::$options['lazyload']['css_background_images'])) {
			$html = self::lazyload_css_background_images($html, $buffer);
		}
		
		return $html;
	}

	//enqueue lazy load script
	public static function enqueue_scripts() {
		wp_register_script('perfmatters-lazy-load', plugins_url('perfmatters/js/lazyload.min.js'), array(), PERFMATTERS_VERSION, true);
		wp_enqueue_script('perfmatters-lazy-load');
		wp_add_inline_script('perfmatters-lazy-load', self::inline_js(), 'before');
	}

	//add async tag to enqueued script
	public static function async_script($tag, $handle) {
	    if($handle !== 'perfmatters-lazy-load') {
	    	return $tag;
	    }
	    return str_replace(' src', ' async src', $tag);
	}

	//print lazy load styles
	public static function lazyload_css() {

		//print noscript styles
		if(apply_filters('perfmatters_lazyload_noscript', true)) {
			echo '<noscript><style>.perfmatters-lazy[data-src]{display:none !important;}</style></noscript>';
		}

		$styles = '';

		//youtube thumbnails
		if(!empty(Config::$options['lazyload']['lazy_loading_iframes']) && !empty(Config::$options['lazyload']['youtube_preview_thumbnails'])) {
			$styles.= '.perfmatters-lazy-youtube{position:relative;width:100%;max-width:100%;height:0;padding-bottom:56.23%;overflow:hidden}.perfmatters-lazy-youtube img{position:absolute;top:0;right:0;bottom:0;left:0;display:block;width:100%;max-width:100%;height:auto;margin:auto;border:none;cursor:pointer;transition:.5s all;-webkit-transition:.5s all;-moz-transition:.5s all}.perfmatters-lazy-youtube img:hover{-webkit-filter:brightness(75%)}.perfmatters-lazy-youtube .play{position:absolute;top:50%;left:50%;right:auto;width:68px;height:48px;margin-left:-34px;margin-top:-24px;background:url('.plugins_url('perfmatters/img/youtube.svg').') no-repeat;background-position:center;background-size:cover;pointer-events:none}.perfmatters-lazy-youtube iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:99}';
			if(current_theme_supports('responsive-embeds')) {
				$styles.= '.wp-has-aspect-ratio .wp-block-embed__wrapper{position:relative;}.wp-has-aspect-ratio .perfmatters-lazy-youtube{position:absolute;top:0;right:0;bottom:0;left:0;width:100%;height:100%;padding-bottom:0}';
			}
		}
 
		//fade in effect
		if(!empty(Config::$options['lazyload']['fade_in'])) {
			$styles.= '.perfmatters-lazy.pmloaded,.perfmatters-lazy.pmloaded>img,.perfmatters-lazy>img.pmloaded,.perfmatters-lazy[data-ll-status=entered]{animation:' . apply_filters('perfmatters_fade_in_speed', 500) . 'ms pmFadeIn}@keyframes pmFadeIn{0%{opacity:0}100%{opacity:1}}';
		}

		//css background images
		if(!empty(Config::$options['lazyload']['css_background_images'])) {
			$styles.='body .perfmatters-lazy-css-bg:not([data-ll-status=entered]),body .perfmatters-lazy-css-bg:not([data-ll-status=entered]) *,body .perfmatters-lazy-css-bg:not([data-ll-status=entered])::before,body .perfmatters-lazy-css-bg:not([data-ll-status=entered])::after{background-image:none!important;will-change:transform;transition:opacity 0.025s ease-in,transform 0.025s ease-in!important;}';
		}
		
		//print styles
		if(!empty($styles)) {
			echo '<style>' . $styles . '</style>';
		}
	}

	//inline lazy load js
	private static function inline_js() {

		$threshold = apply_filters('perfmatters_lazyload_threshold', !empty(Config::$options['lazyload']['threshold']) ? Config::$options['lazyload']['threshold'] : '0px');
		if(ctype_digit($threshold)) {
			$threshold.= 'px';
		}

		//declare lazy load options
		$output = 'window.lazyLoadOptions={elements_selector:"img[data-src],.perfmatters-lazy,.perfmatters-lazy-css-bg",thresholds:"' . $threshold . ' 0px",class_loading:"pmloading",class_loaded:"pmloaded",callback_loaded:function(element){if(element.tagName==="IFRAME"){if(element.classList.contains("pmloaded")){if(typeof window.jQuery!="undefined"){if(jQuery.fn.fitVids){jQuery(element).parent().fitVids()}}}}}};';

		//lazy loader initialized
		$output.= 'window.addEventListener("LazyLoad::Initialized",function(e){var lazyLoadInstance=e.detail.instance;';

			//dom monitoring
			if(!empty(Config::$options['lazyload']['lazy_loading_dom_monitoring'])) { 
				$output.= 'var target=document.querySelector("body");var observer=new MutationObserver(function(mutations){lazyLoadInstance.update()});var config={childList:!0,subtree:!0};observer.observe(target,config);';
			}

		$output.= '});';

		//youtube thumbnails
		if(!empty(Config::$options['lazyload']['lazy_loading_iframes']) && !empty(Config::$options['lazyload']['youtube_preview_thumbnails'])) {
			$autoplay = apply_filters('perfmatters_lazyload_youtube_autoplay', true);
			$output.= 'function perfmattersLazyLoadYouTube(e){var t=document.createElement("iframe"),r="ID?";r+=0===e.dataset.query.length?"":e.dataset.query+"&"' . ($autoplay ? ',r+="autoplay=1"' : '') . ',t.setAttribute("src",r.replace("ID",e.dataset.src)),t.setAttribute("frameborder","0"),t.setAttribute("allowfullscreen","1"),t.setAttribute("allow","accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"),e.replaceChild(t,e.firstChild)}';
		}
		return $output;
	}

	//lazy load img tags
	private static function lazyload_images($html, $buffer) {

		//match all img tags
		preg_match_all('#<img([^>]+?)\/?>#is', $buffer, $images, PREG_SET_ORDER);

		if(!empty($images)) {

			$lazy_image_count = 0;
			$exclude_leading_images = apply_filters('perfmatters_exclude_leading_images', Config::$options['lazyload']['exclude_leading_images'] ?? 0);

			//remove any duplicate images
			$images = array_unique($images, SORT_REGULAR);

			//loop through images
	        foreach($images as $image) {

	        	$lazy_image_count++;

	        	if($lazy_image_count <= $exclude_leading_images) {
	        		continue;
	        	}

	        	//prepare lazy load image
	            $lazy_image = self::lazyload_image($image);

	            //replace image in html
	            $html = str_replace($image[0], $lazy_image, $html);
	        }
		}
			
		return $html;
	}

	//lazy load picture tags for webp
	private static function lazyload_pictures($html, $buffer) {

		//match all picture tags
		preg_match_all('#<picture(.*)?>(.*)<\/picture>#isU', $buffer, $pictures, PREG_SET_ORDER);

		if(!empty($pictures)) {

			foreach($pictures as $picture) {

				//get picture tag attributes
				$picture_atts = Utilities::get_atts_array($picture[1]);

				//dont check excluded if forced attribute was found
				if(!self::lazyload_excluded($picture[1], self::lazyload_forced_atts())) {

					//skip if no-lazy class is found
					if((!empty($picture_atts['class']) && strpos($picture_atts['class'], 'no-lazy') !== false) || self::lazyload_excluded($picture[0], self::lazyload_excluded_atts())) {

						//mark image for exclusion later
						preg_match('#<img([^>]+?)\/?>#is', $picture[0], $image);
						if(!empty($image)) {
							$image_atts = Utilities::get_atts_array($image[1]);
							$image_atts['class'] = (!empty($image_atts['class']) ? $image_atts['class'] . ' ' : '') . 'no-lazy';
							$new_image = sprintf('<img %1$s />', Utilities::get_atts_string($image_atts));                
			                $html = str_replace($image[0], $new_image, $html);
						}
						continue;
					}
				}

				//match all source tags inside the picture
				preg_match_all('#<source(\s.+)>#isU', $picture[2], $sources, PREG_SET_ORDER);

				if(!empty($sources)) {

					//remove any duplicate sources
					$sources = array_unique($sources, SORT_REGULAR);

		            foreach($sources as $source) {

		            	//skip if exluded attribute was found
						if(self::lazyload_excluded($source[1], self::lazyload_excluded_atts())) {
							continue;
						}

						//migrate srcet
		                $new_source = preg_replace('/([\s"\'])srcset/i', '${1}data-srcset', $source[0]);

		                //migrate sizes
		                $new_source = preg_replace('/([\s"\'])sizes/i', '${1}data-sizes', $new_source);

		                //replace source in html	                
		                $html = str_replace($source[0], $new_source, $html);
		            }
				}
				else {
					continue;
				}
			}
		}

		return $html;
	}

	//lazy load background images
	private static function lazyload_background_images($html, $buffer) {

		//match all elements with inline styles
		//preg_match_all('#<(?<tag>div|figure|section|span|li)(\s+[^>]+[\'"\s]?style\s*=\s*[\'"].*?background-image.*?[\'"][^>]*)>#is', $buffer, $elements, PREG_SET_ORDER); //alternate to possibly filter some out that don't have background images???
		preg_match_all('#<(?<tag>div|figure|section|span|li|a)(\s+[^>]*[\'"\s]?style\s*=\s*[\'"].*?[\'"][^>]*)>#is', $buffer, $elements, PREG_SET_ORDER);

		if(!empty($elements)) {

			foreach($elements as $element) {

				//get element tag attributes
				$element_atts = Utilities::get_atts_array($element[2]);

				//dont check excluded if forced attribute was found
				if(!self::lazyload_excluded($element[2], self::lazyload_forced_atts())) {

					//skip if no-lazy class is found
					if(!empty($element_atts['class']) && strpos($element_atts['class'], 'no-lazy') !== false) {
						continue;
					}

					//skip if exluded attribute was found
					if(self::lazyload_excluded($element[2], self::lazyload_excluded_atts())) {
						continue;
					}
				}

				//skip if no style attribute
				if(!isset($element_atts['style'])) {
					continue;
				}

				//match background-image in style string
				preg_match('#(([^;\s])*background(-(image|url))?)\s*:\s*(\s*url\s*\((?<url>[^)]+)\))\s*;?#is', $element_atts['style'], $url);

				if(!empty($url)) {

					$url['url'] = trim($url['url'], '\'" ');

					//add lazyload class
					$element_atts['class'] = !empty($element_atts['class']) ? $element_atts['class'] . ' ' . 'perfmatters-lazy' : 'perfmatters-lazy';

					//remove background image url from inline style attribute
					$element_atts['style'] = str_replace($url[0], '', $element_atts['style']);

					//migrate src
					$element_atts['data-bg'] = esc_url(trim(strip_tags(html_entity_decode($url['url'], ENT_QUOTES|ENT_HTML5)), '\'" '));

					if(!empty($url[2])) {
						$element_atts['data-bg-var'] = $url[1];
					}

					//build lazy element
					$lazy_element = sprintf('<' . $element['tag'] . ' %1$s >', Utilities::get_atts_string($element_atts));

					//replace element with placeholder
					$html = str_replace($element[0], $lazy_element, $html);

					unset($lazy_element);
				}
				else {
					continue;
				}
			}
		}

		return $html;
	}

	//prep img tag for lazy loading
	private static function lazyload_image($image) {

		//if there are no attributes, return original match
		if(empty($image[1])) {
			return $image[0];
		}

		//get image attributes array
		$image_atts = Utilities::get_atts_array($image[1]);

		//get new attributes
		if(empty($image_atts['src']) || (!self::lazyload_excluded($image[1], self::lazyload_forced_atts()) && ((!empty($image_atts['class']) && strpos($image_atts['class'], 'no-lazy') !== false) || self::lazyload_excluded($image[1], self::lazyload_excluded_atts()) || !empty($image_atts['fetchpriority'])))) {
			return $image[0];
		}
		else {

			//add lazyload class
			$image_atts['class'] = !empty($image_atts['class']) ? $image_atts['class'] . ' ' . 'perfmatters-lazy' : 'perfmatters-lazy';

			//migrate src
			$image_atts['data-src'] = $image_atts['src'];

			//add placeholder src
			$width = !empty($image_atts['width']) ? $image_atts['width'] : 0;
			$height = !empty($image_atts['height']) ? $image_atts['height'] : 0;
			$image_atts['src'] = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='" . $width . "'%20height='" . $height . "'%20viewBox='0%200%20" . $width . "%20" . $height . "'%3E%3C/svg%3E";

			//migrate srcset
			if(!empty($image_atts['srcset'])) {
				$image_atts['data-srcset'] = $image_atts['srcset'];
				unset($image_atts['srcset']);
			}
			
			//migrate sizes
			if(!empty($image_atts['sizes'])) {
				$image_atts['data-sizes'] = $image_atts['sizes'];
				unset($image_atts['sizes']);
			}

			//unset existing loading attribute
			if(isset($image_atts['loading'])) {
				unset($image_atts['loading']);
			}
		}

		//replace attributes
		$output = sprintf('<img %1$s />', Utilities::get_atts_string($image_atts));

		//original noscript image
		if(apply_filters('perfmatters_lazyload_noscript', true)) {
			$output.= "<noscript>" . $image[0] . "</noscript>";
		}
		
		return $output;
	}

	//lazy load css background images
	private static function lazyload_css_background_images($html, $buffer) {

		if(!empty(Config::$options['lazyload']['css_background_selectors'])) {

			//match all selectors
			preg_match_all('#<(?>div|section)(\s[^>]*?(' . implode('|', Config::$options['lazyload']['css_background_selectors']) . ').*?)>#i', $buffer, $selectors, PREG_SET_ORDER);

			if(!empty($selectors)) {

				foreach($selectors as $selector) {

					$selector_atts = Utilities::get_atts_array($selector[1]);

					$selector_atts['class'] = !empty($selector_atts['class']) ? $selector_atts['class'] . ' ' . 'perfmatters-lazy-css-bg' : 'perfmatters-lazy-css-bg';

					//replace video attributes string
					$selector_lazyload  = str_replace($selector[1], ' ' . Utilities::get_atts_string($selector_atts), $selector[0]);

					//replace video with placeholder
					$html = str_replace($selector[0], $selector_lazyload, $html);

					unset($selector_lazyload);
				}
			}
		}

		return $html;
	}

	//mark images inside parent exclusions as no-lazy
	private static function lazyload_parent_exclusions($html, $buffer) {

		if(!empty(Config::$options['lazyload']['lazy_loading_parent_exclusions'])) {

			//match all selectors
			preg_match_all('#<(div|section|figure)(\s[^>]*?(' . implode('|', Config::$options['lazyload']['lazy_loading_parent_exclusions']) . ').*?)>.*?<img.*?<\/\g1>#is', $buffer, $selectors, PREG_SET_ORDER);

			if(!empty($selectors)) {

				foreach($selectors as $selector) {

					//match all img tags
					preg_match_all('#<img([^>]+?)\/?>#is', $selector[0], $images, PREG_SET_ORDER);

					if(!empty($images)) {

						//remove any duplicate images
						$images = array_unique($images, SORT_REGULAR);

						//loop through images
				        foreach($images as $image) {

				        	$image_atts = Utilities::get_atts_array($image[1]);

				        	$image_atts['class'] = !empty($image_atts['class']) ? $image_atts['class'] . ' ' . 'no-lazy' : 'no-lazy';

				            //replace video attributes string
							$new_image = str_replace($image[1], ' ' . Utilities::get_atts_string($image_atts), $image[0]);

							//replace video with placeholder
							$html = str_replace($image[0], $new_image, $html);

							unset($new_image);
				        }
					}
				}
			}
		}

		return $html;
	}

	//get forced attributes
	private static function lazyload_forced_atts() {
		return apply_filters('perfmatters_lazyload_forced_attributes', array());
	}

	//get excluded attributes
	private static function lazyload_excluded_atts() {

		//base exclusions
		$attributes = array(
			'data-perfmatters-preload',
			'gform_ajax_frame',
			';base64',
			'skip-lazy'
		); 

		//get exclusions added from settings
		if(!empty(Config::$options['lazyload']['lazy_loading_exclusions']) && is_array(Config::$options['lazyload']['lazy_loading_exclusions'])) {
			$attributes = array_unique(array_merge($attributes, Config::$options['lazyload']['lazy_loading_exclusions']));
		}

	    return apply_filters('perfmatters_lazyload_excluded_attributes', $attributes);
	}

	//check for excluded attributes in attributes string
	private static function lazyload_excluded($string, $excluded) {
	    if(!is_array($excluded)) {
	        (array) $excluded;
	    }

	    if(empty($excluded)) {
	        return false;
	    }

	    foreach($excluded as $exclude) {
	        if(strpos($string, $exclude) !== false) {
	            return true;
	        }
	    }

	    return false;
	}
}