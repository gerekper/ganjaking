<?php
/**
 * Shortcodes
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Column One Sixth
 * [one_sixth] [/one_sixth]
 */

if (! function_exists('sc_one_sixth')) {
	function sc_one_sixth($attr, $content = null)
	{
		$output  = '<div class="column one-sixth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column One Fifth
 * [one_fifth] [/one_fifth]
 */

if (! function_exists('sc_one_fifth')) {
	function sc_one_fifth($attr, $content = null)
	{
		$output  = '<div class="column one-fifth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column One Fourth
 * [one_fourth] [/one_fourth]
 */

if (! function_exists('sc_one_fourth')) {
	function sc_one_fourth($attr, $content = null)
	{
		$output  = '<div class="column one-fourth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column One Third
 * [one_third] [/one_third]
 */

if (! function_exists('sc_one_third')) {
	function sc_one_third($attr, $content = null)
	{
		$output  = '<div class="column one-third">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Two Fifth [two_fifth] [/two_fifth]
 */

if (! function_exists('sc_two_fifth')) {
	function sc_two_fifth($attr, $content = null)
	{
		$output  = '<div class="column two-fifth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column One Second [one_second] [/one_second]
 */

if (! function_exists('sc_one_second')) {
	function sc_one_second($attr, $content = null)
	{
		$output  = '<div class="column one-second">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Two Fifth [three_fifth] [/three_fifth]
 */

if (! function_exists('sc_three_fifth')) {
	function sc_three_fifth($attr, $content = null)
	{
		$output  = '<div class="column three-fifth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Two Third [two_third] [/two_third]
 */

if (! function_exists('sc_two_third')) {
	function sc_two_third($attr, $content = null)
	{
		$output  = '<div class="column two-third">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Three Fourth [three_fourth] [/three_fourth]
 */

if (! function_exists('sc_three_fourth')) {
	function sc_three_fourth($attr, $content = null)
	{
		$output  = '<div class="column three-fourth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Two Fifth [four_fifth] [/four_fifth]
 */

if (! function_exists('sc_four_fifth')) {
	function sc_four_fifth($attr, $content = null)
	{
		$output  = '<div class="column four-fifth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column Two Fifth [five_sixth] [/five_sixth]
 */

if (! function_exists('sc_five_sixth')) {
	function sc_five_sixth($attr, $content = null)
	{
		$output  = '<div class="column five-sixth">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Column One [one] [/one]
 */

if (! function_exists('sc_one')) {
	function sc_one($attr, $content = null)
	{
		$output  = '<div class="column one">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Code [code] [/code]
 */

if (! function_exists('sc_code')) {
	function sc_code($attr, $content = null)
	{
		$output  = '<pre>';
			$output .= do_shortcode(htmlspecialchars($content));
		$output .= '</pre>'."\n";

		return $output;
	}
}

/**
 * Article Box [article_box] [/article_box]
 */

if (! function_exists('sc_article_box')) {
	function sc_article_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 	=> '',
			'slogan' 	=> '',
			'title' 	=> '',
			'link' 		=> '',
			'target' 	=> '',
			'animate' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="article_box">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<div class="photo_wrapper">';
						$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. mfn_get_attachment_data($image, 'width') .'" height="'. mfn_get_attachment_data($image, 'height') .'"/>';
					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';

						if ($slogan) {
							$output .= '<p>'. wp_kses($slogan, mfn_allowed_html()) .'</p>';
						}
						if ($title) {
							$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
						}

						$output .= '<i class="icon-right-open themecolor"></i>';

					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			if ($animate) {
				$output .= '</div>'."\n";
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Helper [helper] [/helper]
 */

if (! function_exists('sc_helper')) {
	function sc_helper($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 		=> '',
			'title_tag' => 'h4',

			'title1' 		=> '',
			'content1' 	=> '',
			'link1' 		=> '',
			'target1' 	=> '',
			'class1' 		=> '',

			'title2' 		=> '',
			'content2' 	=> '',
			'link2' 		=> '',
			'target2' 	=> '',
			'class2' 		=> '',

		), $attr));

		// target

		if ($target1) {
			$target_1_escaped = 'target="_blank"';
		} else {
			$target_1_escaped = false;
		}

		if ($target2) {
			$target_2_escaped = 'target="_blank"';
		} else {
			$target_2_escaped = false;
		}

		// output -----

		$output = '<div class="helper">';

			$output .= '<div class="helper_header">';

				if ($title) {
					$output .= '<'. esc_attr($title_tag) .' class="title">'. wp_kses($title, mfn_allowed_html()) .'</'. esc_attr($title_tag) .'>';
				}

				$output .= '<div class="links">';

					if ($title1) {
						if ($link1) {
							// This variable has been safely escaped above in this function
							$output .= '<a class="link link-1 '. esc_attr($class1) .'" href="'. esc_url($link1) .'" '. $target_1_escaped .'>'. wp_kses($title1, mfn_allowed_html()) .'</a>';
						} else {
							$output .= '<a class="link link-1 toggle" href="#" data-rel="1">'. wp_kses($title1, mfn_allowed_html()) .'</a>';
						}
					}

					if ($title2) {
						if ($link2) {
							// This variable has been safely escaped above in this function
							$output .= '<a class="link link-2 '. esc_attr($class2) .'" href="'. esc_url($link2) .'" '. $target_2_escaped .'>'. wp_kses($title2, mfn_allowed_html()) .'</a>';
						} else {
							$output .= '<a class="link link-2 toggle" href="#" data-rel="2">'. wp_kses($title2, mfn_allowed_html()) .'</a>';
						}
					}

				$output .= '</div>';

			$output .= '</div>';

			$output .= '<div class="helper_content">';

				if (! $link1) {
					$output .= '<div class="item item-1">'. do_shortcode($content1) .'</div>';
				}

				if (! $link2) {
					$output .= '<div class="item item-2">'. do_shortcode($content2) .'</div>';
				}

			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Before After [before_after] [/before_after]
 */

if (! function_exists('sc_before_after')) {
	function sc_before_after($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image_before'	=> '',
			'image_after' 	=> '',
			'classes' 			=> '',
		), $attr));

		// image | visual composer fix

		$image_before = mfn_vc_image($image_before);
		$image_after = mfn_vc_image($image_after);

		// output -----

		$output = '<div class="before_after twentytwenty-container">';

			$output .= '<img class="scale-with-grid" src="'. esc_url($image_before) .'" alt="'. esc_attr(mfn_get_attachment_data($image_before, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image_before, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image_before, 'height')) .'"/>';
			$output .= '<img class="scale-with-grid" src="'. esc_url($image_after) .'" alt="'. esc_attr(mfn_get_attachment_data($image_after, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image_after, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image_after, 'height')) .'"/>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Flat Box [flat_box] [/flat_box]
 */

if (! function_exists('sc_flat_box')) {
	function sc_flat_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 			=> '',
			'title' 			=> '',
			'icon' 				=> 'icon-lamp',
			'icon_image' 	=> '',
			'background' 	=> '',
			'link' 				=> '',
			'target' 			=> '',
			'animate' 		=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);
		$icon_image = mfn_vc_image($icon_image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// background

		if ($background) {
			$background_escaped = 'style="background-color:'. esc_attr($background) .'"';
		} else {
			$background_escaped = false;
		}

		// output -----

		$output = '<div class="flat_box">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<div class="photo_wrapper">';

						// This variable has been safely escaped above in this function
						$output .= '<div class="icon themebg" '. $background_escaped .'>';

							if ($icon_image) {
								$output .= '<img class="scale-with-grid" src="'. esc_url($icon_image) .'" alt="'. esc_attr(mfn_get_attachment_data($icon_image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($icon_image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($icon_image, 'height')) .'"/>';
							} else {
								$output .= '<i class="'. esc_attr($icon) .'"></i>';
							}

						$output .= '</div>';

						$output .= '<img class="photo scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';

					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';

						if ($title) {
							$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
						}

						if ($content) {
							$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
						}

					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Flat Box [feature_box] [/feature_box]
 */

if (! function_exists('sc_feature_box')) {
	function sc_feature_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 		=> '',
			'title' 		=> '',
			'background'=> '',
			'link' 			=> '',
			'target' 		=> '',
			'animate' 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// background

		if ($background) {
			$background_escaped = 'style="background-color:'. esc_attr($background) .'"';
		} else {
			$background_escaped = false;
		}

		// output -----

		$output = '<div class="feature_box">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				// This variable has been safely escaped above in this function
				$output .= '<div class="feature_box_wrapper" '. $background_escaped .'>';

					$output .= '<div class="photo_wrapper">';

						if ($link) {
							// This variable has been safely escaped above in this function
							$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
						}

						$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'" />';

						if ($link) {
							$output .= '</a>';
						}

					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';

						if ($title) {
							$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
						}

						if ($content) {
							$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
						}

					$output .= '</div>';

				$output .= '</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Photo Box [photo_box] [/photo_box]
 */

if (! function_exists('sc_photo_box')) {
	function sc_photo_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 		=> '',
			'title' 		=> '',
			'align' 		=> '',
			'link' 			=> '',
			'target' 		=> '',
			'greyscale' => '',
			'animate' 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// class

		$class = '';

		if ($align) {
			$class .= ' pb_'. $align;
		}
		if ($greyscale) {
			$class .= ' greyscale';
		}
		if (! $content) {
			$class .= ' without-desc';
		}

		// output -----

		$output = '<div class="photo_box '. esc_attr($class) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($title) {
					$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
				}

				if ($image) {
					$output .= '<div class="image_frame">';
					$output .= '<div class="image_wrapper">';

					if ($link) {
						// This variable has been safely escaped above in this function
						$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
					}

					$output .= '<div class="mask"></div>';
					$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';

					if ($link) {
						$output .= '</a>';
					}

					$output .= '</div>';
					$output .= '</div>';
				}

				if ($content) {
					$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Zoom Box [zoom_box] [/zoom_box]
 */

if (! function_exists('sc_zoom_box')) {
	function sc_zoom_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 				=> '',
			'bg_color' 			=> '#000',
			'content_image' => '',
			'link' 					=> '',
			'target' 				=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);
		$content_image = mfn_vc_image($content_image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="zoom_box">';

			if ($link) {
				// This variable has been safely escaped above in this function
				$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
			}

				$output .= '<div class="photo">';
					$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
				$output .= '</div>';

				$output .= '<div class="desc" style="background-color:'. esc_attr(mfn_hex2rgba($bg_color, 0.8)) .';">';
					$output .= '<div class="desc_wrap">';

					if ($content_image) {
						$output .= '<div class="desc_img">';
							$output .= '<img class="scale-with-grid" src="'. esc_url($content_image) .'" alt="'. esc_attr(mfn_get_attachment_data($content_image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($content_image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($content_image, 'height')) .'"/>';
						$output .= '</div>';
					}

					if ($content) {
						$output .= '<div class="desc_txt">';
							$output .= do_shortcode($content);
						$output .= '</div>';
					}

					$output .= '</div>';
				$output .= '</div>';

			if ($link) {
				$output .= '</a>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Sliding Box [sliding_box] [/sliding_box]
 */

if (! function_exists('sc_sliding_box')) {
	function sc_sliding_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 	=> '',
			'title' 	=> '',
			'link' 		=> '',
			'target' 	=> '',
			'animate' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="sliding_box">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<div class="photo_wrapper">';
						$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';
						if ($title) {
							$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
						}
					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Story Box [story_box] [/story_box]
 */

if (! function_exists('sc_story_box')) {
	function sc_story_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 	=> '',
			'style' 	=> '',
			'title' 	=> '',
			'link' 		=> '',
			'target' 	=> '',
			'animate' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="story_box '. esc_attr($style) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<div class="photo_wrapper">';
						$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';

						if ($title) {
							$output .= '<h3 class="themecolor">'. wp_kses($title, mfn_allowed_html()) .'</h3>';
							$output .= '<hr class="hr_color">';
						}

					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

				$output .= '<div class="desc">'. do_shortcode($content) .'</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Trailer Box [trailer_box]
 */

if (! function_exists('sc_trailer_box')) {
	function sc_trailer_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' => '',
			'orientation' => '',
			'slogan' => '',
			'title' => '',

			'link' => '',
			'target' => '',

			'style' => '', // [default], plain
			'animate' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// class

		$class = '';
		if ($style) {
			$class .= $style;
		}

		if ($orientation) {
			$class .= ' '. $orientation;
		}

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="trailer_box '. esc_attr(trim($class)) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';

					$output .= '<div class="desc">';

						if ($slogan) {
							$output .= '<div class="subtitle">'. wp_kses($slogan, mfn_allowed_html()) .'</div>';
						}
						if ($title) {
							$output .= '<h2>'. wp_kses($title, mfn_allowed_html()) .'</h2>';
						}

						$output .= '<div class="line"></div>';

					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Promo Box [promo_box] [/promo_box]
 */

if (! function_exists('sc_promo_box')) {
	function sc_promo_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 		=> '',
			'title' 		=> '',
			'btn_text' 	=> '',
			'btn_link' 	=> '',
			'position' 	=> 'left',
			'border' 		=> '',
			'target' 		=> '',
			'animate' 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// border

		if ($border) {
			$border = 'has_border';
		} else {
			$border = 'no_border';
		}

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="promo_box '. esc_attr($border) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				$output .= '<div class="promo_box_wrapper promo_box_'. esc_attr($position) .'">';

					$output .= '<div class="photo_wrapper">';
						if ($image) {
							$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
						}
					$output .= '</div>';

					$output .= '<div class="desc_wrapper">';

						if ($title) {
							$output .= '<h2>'. wp_kses($title, mfn_allowed_html()) .'</h2>';
						}

						if ($content) {
							$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
						}

						if ($btn_link) {
							// This variable has been safely escaped above in this function
							$output .= '<a href="'. esc_url($btn_link) .'" class="button button_left button_theme button_js" '. $target_escaped .'>';
								$output .= '<span class="button_icon">';
									$output .= '<i class="icon-layout"></i>';
								$output .= '</span>';
								$output .= '<span class="button_label">'. esc_html($btn_text) .'</span>';
							$output .= '</a>';
						}

					$output .= '</div>';

				$output .= '</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Share Box [share_box]
 */

if (! function_exists('sc_share_box')) {
	function sc_share_box($attr, $content = null)
	{
		$output = mfn_share('item');

		return $output;
	}
}

/**
 * How It Works [how_it_works] [/how_it_works]
 */

if (! function_exists('sc_how_it_works')) {
	function sc_how_it_works($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image' 	=> '',
			'number' 	=> '',
			'title' 	=> '',

			'border' 	=> '',
			'style' 	=> '',

			'link' 		=> '',
			'target' 	=> '',

			'animate' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// class

		$class = '';

		// border

		if ($border) {
			$class .= ' has_border';
		} else {
			$class .= ' no_border';
		}

		// style

		if ($style) {
			$class .= ' '. $style;
		}

		// image

		if (! $image) {
			$class .= ' no-img';
		}

		// output -----

		$output = '<div class="how_it_works '. esc_attr($class) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<div class="image">';
						if ($image) {
							$output .= '<img src="'. esc_url($image) .'" class="scale-with-grid" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'">';
						}
						if ($number) {
							$output .= '<span class="number">'. esc_html($number) .'</span>';
						}
					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

				if ($title) {
					$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
				}

				$output .= '<div class="desc">'. do_shortcode($content) .'</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Blog [blog]
 */

if (! function_exists('sc_blog')) {
	function sc_blog($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count'					=> 2,
			'style'					=> 'classic', //  classic, grid, masonry, masonry tiles, photo, photo2, timeline
			'columns'				=> 3,
			'title_tag'			=> 'h2',
			'images'				=> '',

			'category'			=> '',
			'category_multi'=> '',
			'orderby'				=> 'date',
			'order'					=> 'DESC',

			'exclude_id'		=> '',
			'filters'				=> '',
			'excerpt'				=> true,
			'more'					=> '',

			'pagination'		=> '',
			'load_more'			=> '',

			'greyscale'			=> '',
			'margin'				=> '',

			'events'				=> '',
		), $attr));

		// translate

		$translate['filter'] = mfn_opts_get('translate') ? mfn_opts_get('translate-filter', 'Filter by') : __('Filter by', 'betheme');
		$translate['tags'] = mfn_opts_get('translate') ? mfn_opts_get('translate-tags', 'Tags') : __('Tags', 'betheme');
		$translate['authors'] = mfn_opts_get('translate') ? mfn_opts_get('translate-authors', 'Authors') : __('Authors', 'betheme');
		$translate['all'] = mfn_opts_get('translate') ? mfn_opts_get('translate-item-all', 'All') : __('All', 'betheme');
		$translate['categories'] = mfn_opts_get('translate') ? mfn_opts_get('translate-categories', 'Categories') : __('Categories', 'betheme');

		// query args

		$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
		$args = array(
			'posts_per_page'			=> intval($count, 10),
			'paged' 							=> $paged,
			'orderby'							=> $orderby,
			'order'								=> $order,
			'post_status'					=> 'publish',
			'ignore_sticky_posts'	=> false,
		);

		// private

		if (is_user_logged_in()) {
			$args['post_status'] = array( 'publish', 'private' );
		}

		// Include events | The events calendar

		if ($events) {
			$args['post_type'] = array( 'post', 'tribe_events' );
		}

		// categories

		if ($category_multi) {
			$args['category_name'] = trim($category_multi);
		} elseif ($category) {
			$args['category_name'] = $category;
		}

		// exclude posts

		if ($exclude_id) {
			$exclude_id = str_replace(' ', '', $exclude_id);
			$args['post__not_in'] = explode(',', $exclude_id);
		}

		$query_blog = new WP_Query($args);

		// classes

		$classes = $style;

		if ($greyscale) {
			$classes .= ' greyscale';
		}
		if ($margin) {
			$classes .= ' margin';
		}
		if (! $more) {
			$classes .= ' hide-more';
		}

		if ($filters || in_array($style, array( 'masonry', 'masonry tiles' ))) {
			$classes .= ' isotope';
		}

		// title tag

		$title_tag = intval(str_replace('h', '', trim($title_tag)), 10);

		// output -----

		$output = '<div class="column_filters">';

			// output | Filters

			if ($filters && (! $category) && (! $category_multi)) {

				$filters_class = '';
				if ($filters != 1) {
					$filters_class .= ' only '. $filters;
				}

				$output .= '<div id="Filters" class="isotope-filters '. esc_attr($filters_class) .'" data-parent="column_filters">';

					$output .= '<ul class="filters_buttons">';

						$output .= '<li class="label">'. esc_html($translate['filter']) .'</li>';
						$output .= '<li class="categories"><a class="open" href="#"><i class="icon-docs"></i>'. esc_html($translate['categories']) .'<i class="icon-down-dir"></i></a></li>';
						$output .= '<li class="tags"><a class="open" href="#"><i class="icon-tag"></i>'. esc_html($translate['tags']) .'<i class="icon-down-dir"></i></a></li>';
						$output .= '<li class="authors"><a class="open" href="#"><i class="icon-user"></i>'. esc_html($translate['authors']) .'<i class="icon-down-dir"></i></a></li>';

					$output .= '</ul>';

					$output .= '<div class="filters_wrapper">';

						// categories

						$output .= '<ul class="categories">';

							$output .= '<li class="reset current-cat"><a class="all" data-rel="*" href="#">'. esc_html($translate['all']) .'</a></li>';

							if ($categories = get_categories()) {
								foreach ($categories as $category) {
									$output .= '<li class="'. esc_attr($category->slug) .'"><a data-rel=".category-'. esc_attr($category->slug) .'" href="'. esc_url(get_term_link($category)) .'">'. esc_html($category->name) .'</a></li>';
								}
							}

							$output .= '<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>';

						$output .= '</ul>';

						// tags

						$output .= '<ul class="tags">';

							$output .= '<li class="reset current-cat"><a class="all" data-rel="*" href="#">'. esc_html($translate['all']) .'</a></li>';

							if ($tags = get_tags()) {
								foreach ($tags as $tag) {
									$output .= '<li class="'. esc_attr($tag->slug) .'"><a data-rel=".tag-'. esc_attr($tag->slug) .'" href="'. esc_url(get_tag_link($tag)) .'">'. esc_html($tag->name) .'</a></li>';
								}
							}

							$output .= '<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>';

						$output .= '</ul>';

						// authors

						$output .= '<ul class="authors">';

							$output .= '<li class="reset current-cat"><a class="all" data-rel="*" href="#">'. esc_html($translate['all']) .'</a></li>';
							$authors = mfn_get_authors();

							if (is_array($authors)) {
								foreach ($authors as $auth) {
									$output .= '<li class="'. esc_attr(mfn_slug($auth->data->user_login)) .'"><a data-rel=".author-'. mfn_slug($auth->data->user_login) .'" href="'. get_author_posts_url($auth->ID) .'">'. $auth->data->display_name .'</a></li>';
								}
							}
							$output .= '<li class="close"><a href="#"><i class="icon-cancel"></i></a></li>';

						$output .= '</ul>';

					$output .= '</div>';

				$output .= '</div>'."\n";
			}

			// output | Main Content

			$output .= '<div class="blog_wrapper isotope_wrapper clearfix">';

				$output .= '<div class="posts_group lm_wrapper col-'. esc_attr($columns) .' '. esc_attr($classes) .'">';

					// blog query attributes

					$attr = array(
						'excerpt' => $excerpt,
						'featured_image' => false,
						'filters' => $filters,
						'title_tag' => $title_tag,
					);

					if ($load_more) {
						$attr['featured_image'] = 'no_slider';	// no slider if load more
					}
					if ($images) {
						$attr['featured_image'] = 'image';	// images only option
					}

					$output .= mfn_content_post($query_blog, $style, $attr);

				$output .= '</div>';

				if ($pagination || $load_more) {
					$output .= mfn_pagination($query_blog, $load_more);
				}

			$output .= '</div>'."\n";

		$output .= '</div>'."\n";

		wp_reset_postdata();

		return $output;
	}
}

/**
 * Blog Slider [blog_slider]
 */

if (! function_exists('sc_blog_slider')) {
	function sc_blog_slider($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'				=> '',
			'count'				=> 5,

			'category'		=> '',
			'category_multi'	=> '',
			'orderby'			=> 'date',
			'order'				=> 'DESC',

			'more'				=> '',
			'style'				=> '',
			'navigation'	=> '',
		), $attr));

		// translate

		$translate['readmore'] = mfn_opts_get('translate') ? mfn_opts_get('translate-readmore', 'Read more') : __('Read more', 'betheme');

		// classes

		$classes = '';
		if (! $more) {
			$classes .= ' hide-more';
		}
		if ($style) {
			$classes .= ' '. $style;
		}
		if ($navigation) {
			$classes .= ' '. $navigation;
		}

		// query args

		$args = array(
			'posts_per_page'			=> intval($count, 10),
			'orderby' 						=> $orderby,
			'order'								=> $order,
			'no_found_rows'				=> 1,
			'post_status'					=> 'publish',
			'ignore_sticky_posts'	=> 0,
		);

		// private

		if (is_user_logged_in()) {
			$args['post_status'] = array( 'publish', 'private' );
		}

		// categories

		if ($category_multi) {
			$args['category_name'] = trim($category_multi);
		} elseif ($category) {
			$args['category_name'] = $category;
		}

		$query_blog = new WP_Query($args);

		// output -----

		$output = '<div class="blog_slider clearfix '. esc_attr($classes) .'" data-count="'. intval($query_blog->post_count, 10) .'">';

			$output .= '<div class="blog_slider_header">';
				if ($title) {
					$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
				}
			$output .= '</div>';

			$output .= '<ul class="blog_slider_ul">';

				while ($query_blog->have_posts()) {
					$query_blog->the_post();

					$output .= '<li class="'. implode(' ', get_post_class()) .'">';
						$output .= '<div class="item_wrapper">';

							if (get_post_format() == 'quote') {
								$output .= '<blockquote>';
									$output .= '<a href="'. esc_url(get_permalink()) .'">';
										$output .= wp_kses(get_the_title(), mfn_allowed_html());
									$output .= '</a>';
								$output .= '</blockquote>';
							} else {
								$output .= '<div class="image_frame scale-with-grid">';
									$output .= '<div class="image_wrapper">';
										$output .= '<a href="'. esc_url(get_permalink()) .'">';
											$output .= get_the_post_thumbnail(get_the_ID(), 'blog-portfolio', array( 'class' => 'scale-with-grid' ));
										$output .= '</a>';
									$output .= '</div>';
								$output .= '</div>';
							}

							$output .= '<div class="date_label">'. esc_html(get_the_date()) .'</div>';

							$output .= '<div class="desc">';
								if (get_post_format() != 'quote') {
									$output .= '<h4><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h4>';
								}
								$output .= '<hr class="hr_color" />';
								$output .= '<a href="'. esc_url(get_permalink()) .'" class="button button_left button_js"><span class="button_icon"><i class="icon-layout"></i></span><span class="button_label">'. esc_html($translate['readmore']) .'</span></a>';
							$output .= '</div>';

						$output .= '</div>';
					$output .= '</li>';
				}

			$output .= '</ul>';

			$output .= '<div class="slider_pager slider_pagination"></div>';

		$output .= '</div>'."\n";

		wp_reset_postdata();

		return $output;
	}
}

/**
 * Blog News [blog_news]
 */

if (! function_exists('sc_blog_news')) {
	function sc_blog_news($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'				=> '',
			'count'				=> 5,
			'style'				=> '',

			'category'		=> '',
			'category_multi'	=> '',
			'orderby'			=> 'date',
			'order'				=> 'DESC',

			'excerpt'			=> '',
			'link'				=> '',
			'link_title'	=> '',
		), $attr));

		// query args

		$args = array(
			'posts_per_page'	=> intval($count, 10),
			'orderby' 				=> $orderby,
			'order'						=> $order,
			'no_found_rows'		=> 1,
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 0,
		);

		// private

		if (is_user_logged_in()) {
			$args['post_status'] = array( 'publish', 'private' );
		}

		// categories

		if ($category_multi) {
			$args['category_name'] = trim($category_multi);
		} elseif ($category) {
			$args['category_name'] = $category;
		}

		// featured first

		if ($style == 'featured') {
			$first = true;
		} else {
			$first = false;
		}

		$query_blog = new WP_Query($args);

		// output -----

		$output = '<div class="Latest_news '. esc_attr($style) .'">';

			if ($title) {
				$output .= '<h3 class="title">'. $title .'</h3>';
			}

			$output .= '<ul class="ul-first">';

				while ($query_blog->have_posts()) {
					$query_blog->the_post();

					$output .= '<li class="'. esc_attr(implode(' ', get_post_class())) .'">';

						$output .= '<div class="photo">';
							$output .= '<a href="'. esc_url(get_permalink()) .'">';
								$output .= get_the_post_thumbnail(get_the_ID(), 'blog-portfolio', array( 'class' => 'scale-with-grid' ));
							$output .= '</a>';
						$output .= '</div>';

						$output .= '<div class="desc">';

							if ($first) {
								$output .= '<h4><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h4>';
							} else {
								$output .= '<h5><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h5>';
							}

							$output .= '<div class="desc_footer">';
								$output .= '<span class="date"><i class="icon-clock"></i> '. esc_html(get_the_date()) .'</span>';
								if (comments_open()) {
									$output .= '<i class="icon-comment-empty-fa"></i> <a href="'. esc_url(get_comments_link()) .'" class="post-comments">'. intval(get_comments_number(), 10) .'</a>';
								}
								$output .= '<div class="button-love">'. mfn_love() .'</div>';
							$output .= '</div>';

							if ($excerpt == '1' || ($first && $excerpt == 'featured')) {
								$output .= '<div class="post-excerpt">'. get_the_excerpt() .'</div>';
							}

						$output .= '</div>';

					$output .= '</li>';

					if ($first) {
						$output .= '</ul>';
						$output .= '<ul class="ul-second">';

						$first = false;
					}
				}

				wp_reset_postdata();

			$output .= '</ul>';

			if ($link) {
				$output .= '<a class="button button_js" href="'. esc_url($link) .'"><span class="button_icon"><i class="icon-layout"></i></span><span class="button_label">'. esc_html($link_title) .'</span></a>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Blog Teaser [blog_teaser]
 */

if (! function_exists('sc_blog_teaser')) {
	function sc_blog_teaser($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'					=> '',
			'title_tag'			=> 'h3',

			'category'			=> '',
			'category_multi'=> '',
			'orderby'				=> 'date',
			'order'					=> 'DESC',

			'margin'				=> '',
		), $attr));

		// query args

		$args = array(
			'posts_per_page'			=> 3,
			'orderby' 						=> $orderby,
			'order'								=> $order,
			'no_found_rows'				=> 1,
			'post_status'					=> 'publish',
			'ignore_sticky_posts'	=> 0,
		);

		// translate

		$translate['published'] = mfn_opts_get('translate') ? mfn_opts_get('translate-published', 'Published by') : __('Published by', 'betheme');
		$translate['at'] = mfn_opts_get('translate') ? mfn_opts_get('translate-at', 'at') : __('at', 'betheme');

		// title tag

		$title_tag = intval(str_replace('h', '', trim($title_tag)), 10);

		// class

		$class = '';
		if (! $margin) {
			$class .= 'margin-no';
		}

		// private

		if (is_user_logged_in()) {
			$args['post_status'] = array( 'publish', 'private' );
		}

		// categories

		if ($category_multi) {
			$args['category_name'] = trim($category_multi);
		} elseif ($category) {
			$args['category_name'] = $category;
		}

		$query_blog = new WP_Query($args);

		// output -----

		$output = '<div class="blog-teaser '. esc_attr($class) .'">';

			if ($title) {
				$output .= '<h3 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h3>';
			}

			$output .= '<ul class="teaser-wrapper">';

				$first = true;

				while ($query_blog->have_posts()) {
					$query_blog->the_post();

					$output .= '<li class="'. esc_attr(implode(' ', get_post_class())) .'">';

						$output .= '<div class="photo-wrapper scale-with-grid">';
							$output .= get_the_post_thumbnail(get_the_ID(), 'blog-portfolio', array( 'class' => 'scale-with-grid' ));
						$output .= '</div>';

						$output .= '<div class="desc-wrapper">';
							$output .= '<div class="desc">';

								if (mfn_opts_get('blog-meta')) {
									$output .= '<div class="post-meta clearfix">';

										$output .= '<span class="author">';
											$output .= '<span class="label">'. esc_html($translate['published']) .' </span>';
											$output .= '<i class="icon-user"></i> ';
											$output .= '<a href="'. esc_url(get_author_posts_url(get_the_author_meta('ID'))) .'">'. esc_html(get_the_author_meta('display_name')) .'</a>';
										$output .= '</span> ';

										$output .= '<span class="date">';
											$output .= '<span class="label">'. esc_html($translate['at']) .' </span>';
											$output .= '<i class="icon-clock"></i> ';
											$output .= '<span class="post-date">'. esc_html(get_the_date()) .'</span>';
										$output .= '</span>';

										// .post-comments | Style == Masonry Tiles
										if (comments_open() && mfn_opts_get('blog-comments')) {
											$output .= '<span class="comments">';
												$output .= '<i class="icon-comment-empty-fa"></i> <a href="'. esc_url(get_comments_link()) .'" class="post-comments">'. esc_html(get_comments_number()) .'</a>';
											$output .= '</span>';
										}

									$output .= '</div>';
								}

								$output .= '<div class="post-title">';
									$output .= '<h'. esc_attr($title_tag) .'><a href="'. esc_url(get_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h'. esc_attr($title_tag) .'>';
								$output .= '</div>';

							$output .= '</div>';
						$output .= '</div>';

					$output .= '</li>';

					if ($first) {
						$title_tag ++;
						$first = false;
					}
				}
				wp_reset_postdata();

			$output .= '</ul>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Shop Slider [shop_slider]
 */

if (! function_exists('sc_shop_slider')) {
	function sc_shop_slider($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'			=> '',
			'count'			=> 5,
			'show'			=> '',
			'category'	=> '',
			'orderby' 	=> 'date',
			'order' 		=> 'DESC',
		), $attr));

		// query args

		$args = array(
			'post_type' 			=> 'product',
			'posts_per_page' 	=> intval($count, 10),
			'paged' 					=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts'	=> 1,
		);

		// show

		if ($show == 'featured') {

			// featured ------------------------------
			$args['post__in'] =  array_merge(array( 0 ), wc_get_featured_product_ids());
		} elseif ($show == 'onsale') {

			// onsale --------------------------------
			$args['post__in'] =  array_merge(array( 0 ), wc_get_product_ids_on_sale());
		} elseif ($show == 'best-selling') {

			// best-selling --------------------------
			$args['meta_key'] = 'total_sales';
			$args['orderby'] 	= 'meta_value_num';
		}

		// category

		if ($category) {
			$args['product_cat'] = $category;
		}

		$query_shop = new WP_Query();
		$query_shop->query($args);

		// output -----

		$output = '<div class="shop_slider" data-count="'. esc_attr($query_shop->post_count) .'">';

			$output .= '<div class="blog_slider_header">';
				if ($title) {
					$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
				}
			$output .= '</div>';

			$output .= '<ul class="shop_slider_ul">';
				while ($query_shop->have_posts()) {
					$query_shop->the_post();
					global $product;

					$output .= '<li class="'. esc_attr(implode(' ', get_post_class())) .'">';
						$output .= '<div class="item_wrapper">';

							if (mfn_opts_get('shop-images') == 'secondary') {
								$output .= '<div class="hover_box hover_box_product" ontouchstart="this.classList.toggle(\'hover\');">';

									$output .= '<a href="'. esc_url(get_the_permalink()) .'">';
										$output .= '<div class="hover_box_wrapper">';

											$output .= get_the_post_thumbnail(null, 'shop_catalog', array('class'=>'visible_photo scale-with-grid' ));

											if ($attachment_ids = $product->get_gallery_attachment_ids()) {
												$secondary_image_id = $attachment_ids['0'];
												$output .= wp_get_attachment_image($secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'hidden_photo scale-with-grid' ));
											}

										$output .= '</div>';
									$output .= '</a>';

									if ($product->is_on_sale()) {
										$output .= '<span class="onsale"><i class="icon-star"></i></span>';
									}

								$output .= '</div>';
							} else {
								$output .= '<div class="image_frame scale-with-grid product-loop-thumb">';

									$output .= '<div class="image_wrapper">';

										$output .= '<a href="'. esc_url(get_the_permalink()) .'">';
											$output .= '<div class="mask"></div>';
											$output .= get_the_post_thumbnail(null, 'shop_catalog', array( 'class' => 'scale-with-grid' ));
										$output .= '</a>';

										$output .= '<div class="image_links">';
											$output .= '<a class="link" href="'. esc_url(get_the_permalink()) .'"><i class="icon-link"></i></a>';
										$output .= '</div>';

									$output .= '</div>';

									if ($product->is_on_sale()) {
										$output .= '<span class="onsale"><i class="icon-star"></i></span>';
									}

								$output .= '</div>';
							}

							$output .= '<div class="desc">';

								$output .= '<h4><a href="'. esc_url(get_the_permalink()) .'">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</a></h4>';

								if ($price_html = $product->get_price_html()) {
									$output .= '<span class="price">'. $price_html .'</span>';
								}

							$output .= '</div>';

						$output .= '</div>';
					$output .= '</li>';
				}

			$output .= '</ul>';

			$output .= '<div class="slider_pager slider_pagination"></div>';

		$output .= '</div>'."\n";

		wp_reset_postdata();

		return $output;
	}
}

/**
 * Contact Box [contact_box]
 */

if (! function_exists('sc_contact_box')) {
	function sc_contact_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'				=> '',
			'address' 		=> '',
			'telephone'		=> '',
			'telephone_2'	=> '',
			'fax'					=> '',
			'email' 			=> '',
			'www' 				=> '',
			'image' 			=> '',
			'animate' 		=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// background

		if ($image) {
			$background_escaped = 'style="background-image:url('. esc_url($image) .');"';
		} else {
			$background_escaped = false;
		}

		// output -----

		$output = '';

		if ($animate) {
			$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
		}

			// This variable has been safely escaped above in this function
			$output .= '<div class="get_in_touch" '. $background_escaped .'>';

				if ($title) {
					$output .= '<h3>'. wp_kses($title, mfn_allowed_html()) .'</h3>';
				}

				$output .= '<div class="get_in_touch_wrapper">';
					$output .= '<ul>';

						if ($address) {
							$output .= '<li class="address">';
								$output .= '<span class="icon"><i class="icon-location"></i></span>';
								$output .= '<span class="address_wrapper">'. wp_kses_post($address) .'</span>';
							$output .= '</li>';
						}

						if ($telephone) {
							$output .= '<li class="phone phone-1">';
								$output .= '<span class="icon"><i class="icon-phone"></i></span>';
								$output .= '<p><a href="tel:'. esc_attr(str_replace(' ', '', $telephone)) .'">'. esc_html($telephone) .'</a></p>';
							$output .= '</li>';
						}

						if ($telephone_2) {
							$output .= '<li class="phone phone-2">';
								$output .= '<span class="icon"><i class="icon-phone"></i></span>';
								$output .= '<p><a href="tel:'. esc_attr(str_replace(' ', '', $telephone_2)) .'">'. esc_html($telephone_2) .'</a></p>';
							$output .= '</li>';
						}

						if ($fax) {
							$output .= '<li class="phone fax">';
								$output .= '<span class="icon"><i class="icon-print"></i></span>';
								$output .= '<p><a href="fax:'. esc_attr(str_replace(' ', '', $fax)) .'">'. esc_html($fax) .'</a></p>';
							$output .= '</li>';
						}

						if ($email) {
							$output .= '<li class="mail">';
								$output .= '<span class="icon"><i class="icon-mail"></i></span>';
								$output .= '<p><a href="mailto:'. esc_attr($email) .'">'. esc_html($email) .'</a></p>';
							$output .= '</li>';
						}

						if ($www) {
							if (strpos($www, 'http') === 0) {
								$url = $www;
								$www = str_replace('http://', '', $www);
								$www = str_replace('https://', '', $www);
							} else {
								$url = 'http'. mfn_ssl() .'://'. $www;
							}

							$output .= '<li class="www">';
								$output .= '<span class="icon"><i class="icon-link"></i></span>';
								$output .= '<p><a target="_blank" href="'. esc_url($url) .'">'. esc_html($www) .'</a></p>';
							$output .= '</li>';
						}

					$output .= '</ul>';
				$output .= '</div>';

			$output .= '</div>'."\n";

		if ($animate) {
			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Popup [popup][/popup]
 */

if (! function_exists('sc_popup')) {
	function sc_popup($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'			=> '',
			'padding'		=> '',
			'button' 		=> '',
			'uid'			 => 'popup-'. uniqid(),
		), $attr));

		// padding

		if ($padding) {
			$style_escaped = 'style="padding:'. intval($padding, 10) .'px;"';
		} else {
			$style_escaped = false;
		}

		// output -----

		$output = '';

		if ($button) {
			$output .= '<a href="#'. esc_attr($uid) .'" rel="prettyphoto" class="popup-link button button_js"><span class="button_label">'. wp_kses($title, mfn_allowed_html()) .'</span></a>';
		} else {
			$output .= '<a href="#'. esc_attr($uid) .'" rel="prettyphoto" class="popup-link">'. wp_kses($title, mfn_allowed_html()) .'</a>';
		}

		$output .= '<div id="'. esc_attr($uid) .'" class="popup-content">';

			// This variable has been safely escaped above in this function
			$output .= '<div class="popup-inner" '. $style_escaped .'>'. do_shortcode($content) .'</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Info Box [info_box]
 */

if (! function_exists('sc_info_box')) {
	function sc_info_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'			=> '',
			'tabs'			=> '',
			'image' 		=> '',
			'animate' 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// background

		if ($image) {
			$background_escaped = 'style="background-image:url('. esc_url($image) .');"';
		} else {
			$background_escaped = false;
		}

		// output -----

		$output = '';

		if ($animate) {
			$output .= '<div class="animate" data-anim-type="'. $animate .'">';
		}

			// This variable has been safely escaped above in this function
			$output .= '<div class="infobox" '. $background_escaped .'>';

				if ($title) {
					$output .= '<h3>'. wp_kses($title, mfn_allowed_html()) .'</h3>';
				}

				$output .= '<div class="infobox_wrapper">';

					// Elementor repeater item

					if( is_array( $tabs ) ){
						$output .= '<ul>';
							foreach( $tabs as $tab ){
								$output .= '<li>'. nl2br($tab['content']) .'</li>';
							}
						$output .= '</ul>';
					}

					$output .= do_shortcode($content);

				$output .= '</div>';

			$output .= '</div>'."\n";

		if ($animate) {
			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Opening hours [opening_hours]
 */

if (! function_exists('sc_opening_hours')) {
	function sc_opening_hours($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'			=> '',
			'tabs'			=> '',
			'image' 		=> '',
			'animate' 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// background

		if ($image) {
			$background_escaped = 'style="background-image:url('. esc_url($image) .');"';
		} else {
			$background_escaped = false;
		}

		// output -----

		$output = '';

		if ($animate) {
			$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
		}

			// This variable has been safely escaped above in this function
			$output .= '<div class="opening_hours" '. $background_escaped .'>';

				if ($title) {
					$output .= '<h3>'. wp_kses($title, mfn_allowed_html()) .'</h3>';
				}

				$output .= '<div class="opening_hours_wrapper">';

					// Elementor repeater item

					if( is_array( $tabs ) ){
						$output .= '<ul>';
							foreach( $tabs as $tab ){
								$output .= '<li><label>'. $tab['days'] .'</label><span>'. $tab['hours'] .'</span></li>';
							}
						$output .= '</ul>';
					}

					$output .= do_shortcode($content);

				$output .= '</div>';

			$output .= '</div>'."\n";

		if ($animate) {
			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Divider [divider]
 */

if (! function_exists('sc_divider')) {
	function sc_divider($attr, $content = null)
	{
		extract(shortcode_atts(array(
		'height'      => 0,
		'style'       => '',	// default, dots, zigzag
		'line'        => '',	// default, narrow, wide, 0 = no_line
		'color'  			=> '',
		'themecolor'  => '',
	), $attr));

		// classes

		$class = '';

		if ($themecolor) {
			$class .= ' hr_color';
			$color = false; // theme color overwrites
		}

		// color

		if( $color ){
			if( 'zigzag' == $style ){
				$color = 'color:'. $color;
			} else {
				$color = 'background-color:'. $color;
			}
		}

		switch ($style) {

			case 'dots':

			// This variable has been safely escaped above in this function
				$output = '<div class="hr_dots" style="margin:0 auto '. intval($height, 10) .'px"><span style="'. esc_attr($color) .'"></span><span style="'. esc_attr($color) .'"></span><span style="'. esc_attr($color) .'"></span></div>'."\n";
				break;

			case 'zigzag':

			// This variable has been safely escaped above in this function
				$output = '<div class="hr_zigzag" style="margin:0 auto '. intval($height, 10) .'px"><i class="icon-down-open" style="'. esc_attr($color) .'"></i><i class="icon-down-open" style="'. esc_attr($color) .'"></i><i class="icon-down-open" style="'. esc_attr($color) .'"></i></div>'."\n";
				break;

			default:

				if ($line == 'narrow') {

			  // This variable has been safely escaped above in this function
					$output = '<hr class="hr_narrow '. esc_attr($class) .'" style="margin:0 auto '. intval($height, 10) .'px;'. esc_attr($color) .'"/>'."\n";

				} elseif ($line == 'wide') {

			  // This variable has been safely escaped above in this function
					$output = '<div class="hr_wide '. esc_attr($class) .'" style="margin:0 auto '. intval($height, 10) .'px"><hr style="'. esc_attr($color) .'"/></div>'."\n";

				} elseif ($line) {

			  // This variable has been safely escaped above in this function
					$output = '<hr class="'. esc_attr($class) .'" style="margin:0 auto '. intval($height, 10) .'px;'. esc_attr($color) .'"/>'."\n";

				} else {

			  // This variable has been safely escaped above in this function
					$output = '<hr class="no_line" style="margin:0 auto '. intval($height, 10) .'px"/>'."\n";

				}

		}

		return $output;
	}
}

/**
 * Fancy Divider [fancy_divider]
 */

if (! function_exists('sc_fancy_divider')) {
	function sc_fancy_divider($attr, $content = null)
	{
		extract(shortcode_atts(array(
		'style' 		    => 'stamp',
		'color_top'     => '',
		'color_bottom' 	=> '',
	), $attr));

		// output -----

		$output = '<div class="fancy-divider">';

		switch ($style) {

			case 'circle up':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_top) .';">';
					$output .= '<path d="M0 100 C50 0 50 0 100 100 Z" style="fill: '. esc_attr($color_bottom) .'; stroke: '. esc_attr($color_bottom) .';"/>';
				$output .= '</svg>';
				break;

			case 'circle down':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_bottom) .';">';
					$output .= '<path d="M0 0 C50 100 50 100 100 0 Z" style="fill: '. esc_attr($color_top) .'; stroke: '. esc_attr($color_top) .';"/>';
				$output .= '</svg>';
				break;

			case 'curve up':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_top) .';">';
					$output .= '<path d="M0 100 C 20 0 50 0 100 100 Z" style="fill: '. esc_attr($color_bottom) .'; stroke: '. esc_attr($color_bottom) .';"/>';
				$output .= '</svg>';
				break;

			case 'curve down':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_bottom) .';">';
					$output .= '<path d="M0 0 C 50 100 80 100 100 0 Z" style="fill: '. esc_attr($color_top) .'; stroke: '. esc_attr($color_top) .';"/>';
				$output .= '</svg>';
				break;

			case 'triangle up':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_top) .';">';
					$output .= '<path d="M0 100 L50 2 L100 100 Z" style="fill: '. esc_attr($color_bottom) .'; stroke: '. esc_attr($color_bottom) .';"/>';
				$output .= '</svg>';
				break;

			case 'triangle down':

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 100" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_bottom) .';">';
					$output .= '<path d="M0 0 L50 100 L100 0 Z" style="fill: '. esc_attr($color_top) .'; stroke: '. esc_attr($color_top) .';"/>';
				$output .= '</svg>';
				break;

			default:

				$output .= '<svg preserveAspectRatio="none" viewBox="0 0 100 102" height="100" width="100%" version="1.1" xmlns="https://www.w3.org/2000/svg" style="background: '. esc_attr($color_bottom) .';">';
					$output .= '<path d="M0 0 Q 2.5 40 5 0 Q 7.5 40 10 0Q 12.5 40 15 0Q 17.5 40 20 0Q 22.5 40 25 0Q 27.5 40 30 0Q 32.5 40 35 0Q 37.5 40 40 0Q 42.5 40 45 0Q 47.5 40 50 0 Q 52.5 40 55 0Q 57.5 40 60 0Q 62.5 40 65 0Q 67.5 40 70 0Q 72.5 40 75 0Q 77.5 40 80 0Q 82.5 40 85 0Q 87.5 40 90 0Q 92.5 40 95 0Q 97.5 40 100 0 Z" style="fill: '. esc_attr($color_top) .'; stroke: '. esc_attr($color_top) .';"/>';
				$output .= '</svg>';

		}

		$output .= '</div>';

		return $output;
	}
}

/**
 * Google Font [google_font]
 */

if (! function_exists('sc_google_font')) {
	function sc_google_font($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'font' 				=> '',

			'size' 				=> '25',
			'weight'			=> '400',

			'italic'			=> '',
			'letter_spacing' 	=> '',
			'subset' 			=> '',

			'color'				=> '',
			'inline' 			=> '',
		), $attr));

		// style

		$style_escaped = array();
		$style_escaped[] = "font-family:'". esc_attr($font) ."',Arial,Tahoma,sans-serif;";
		$style_escaped[] = "font-size:". intval($size, 10) ."px;";
		$style_escaped[] = "line-height:". intval($size, 10) ."px;";
		$style_escaped[] = "font-weight:". esc_attr($weight) .";";
		$style_escaped[] = "letter-spacing:". intval($letter_spacing, 10) ."px;";

		if ($color) {
			$style_escaped[] = "color:". esc_attr($color) .";";
		}

		// italic

		if ($italic) {
			$style_escaped[] = "font-style:italic;";
			$weight = $weight .'italic';
		}

		$style_escaped = implode('', $style_escaped);

		// subset

		if ($subset) {
			$subset	= '&amp;subset='. str_replace(' ', '', $subset);
		} else {
			$subset = false;
		}

		// class

		$class = '';
		if ($inline) {
			$class .= ' inline';
		}

		// enqueue_style

		$font_slug	= str_replace(' ', '+', $font);
		wp_enqueue_style(esc_attr($font_slug), 'https://fonts.googleapis.com/css?family='. esc_attr($font_slug) .':'. esc_attr($weight) . esc_attr($subset));

		// output -----

		// This variable has been safely escaped above in this function
		$output = '<div class="google_font'. esc_attr($class).'" style="'. $style_escaped .'">';
			$output .= do_shortcode($content);
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Sidebar Widget [sidebar_widget]
 */

if (! function_exists('sc_sidebar_widget')) {
	function sc_sidebar_widget($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'sidebar'  => '',
		), $attr));

		// output -----

		$output = '';

		if (($sidebar !== '') && ($sidebar !== false)) {
			$sidebars = mfn_opts_get('sidebars');

			if (is_array($sidebars)) {
				$sidebar = $sidebars[ esc_attr($sidebar) ];

				ob_start();
				dynamic_sidebar($sidebar);
				$output = ob_get_clean();
			}
		}

		return $output;
	}
}

/**
 * Pricing Item [pricing_item] [/pricing_item]
 */

if (! function_exists('sc_pricing_item')) {
	function sc_pricing_item($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image'			   => '',
			'title'			   => '',
			'currency'     => '',
			'currency_pos' => '',
			'price'        => '',
			'period'       => '',
			'subtitle'     => '',
			'link_title'   => '',
			'link'         => '',
			'target'       => '',
			'icon'         => '',
			'featured'     => '',
			'style'        => 'box',
			'animate'      => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// classes

		$classes = '';

		if ($currency_pos) {
			$classes .= ' cp-'. $currency_pos;
		}
		if ($featured) {
			$classes .= ' pricing-box-featured';
		}
		if ($style) {
			$classes .= ' pricing-box-'. $style;
		}

		// output -----

		$output = '<div class="pricing-box '. esc_attr($classes) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				// header

				$output .= '<div class="plan-header">';

					if ($image) {
						$output .= '<div class="image">';
							$output .= '<img src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
						$output .= '</div>';
					}

					if ($title) {
						$output .= '<h2>'. wp_kses($title, mfn_allowed_html()) .'</h2>';
					}

					if ($price || ($price === '0')) {
						$output .= '<div class="price">';

							if ($currency_pos != 'right') {
								$output .= '<sup class="currency">'. esc_html($currency) .'</sup>';
							}

							$output .= '<span>'. esc_html($price) .'</span>';

							if ($currency_pos == 'right') {
								$output .= '<sup class="currency">'. esc_html($currency) .'</sup>';
							}

							$output .= '<sup class="period">'. esc_html($period) .'</sup>';

						$output .= '</div>';

						$output .= '<hr class="hr_color" />';
					}

					if ($subtitle) {
						$output .= '<p class="subtitle"><big>'. wp_kses($subtitle, mfn_allowed_html()) .'</big></p>';
					}

				$output .= '</div>';

				// content

				if ($content) {
					$output .= '<div class="plan-inside">';
						$output .= do_shortcode($content);
					$output .= '</div>';
				}

				// link

				if ($link) {
					if ($icon) {
						$button_class = 'button_left';
					} else {
						$button_class = false;
					}

					$output .= '<div class="plan-footer">';

						// This variable has been safely escaped above in this function
						$output .= '<a href="'. esc_url($link) .'" class="button button_theme button_js '. esc_attr($button_class) .'" '. $target_escaped .'>';

							if ($icon) {
								$output .= '<span class="button_icon"><i class="'. esc_attr($icon) .'"></i></span>';
							}

							$output .= '<span class="button_label">'. esc_html($link_title) .'</span>';

						$output .= '</a>';

					$output .= '</div>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Call to Action [call_to_action] [/call_to_action]
 */

if (! function_exists('sc_call_to_action')) {
	function sc_call_to_action($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 		=> '',
			'icon' 			=> '',
			'link' 			=> '',
			'button_title'	=> '',
			'class' 		=> '',
			'target' 		=> '',
			'animate' 	=> '',
		), $attr));

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// FIX | prettyphoto

		if (strpos($class, 'prettyphoto') !== false) {
			$class 	= str_replace('prettyphoto', '', $class);
			$rel_escaped 	= 'rel="prettyphoto"';
		} else {
			$rel_escaped 	= false;
		}

		// output -----

		$output = '<div class="call_to_action">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				$output .= '<div class="call_to_action_wrapper">';

					$output .= '<div class="call_left">';
						$output .= '<h3>'. wp_kses($title, mfn_allowed_html()) .'</h3>';
					$output .= '</div>';

					$output .= '<div class="call_center">';

						if ($button_title) {

							if ($link) {
								// This variable has been safely escaped above in this function
								$output .= '<a href="'. esc_url($link) .'" class="button button_js '. esc_attr($class) .'" '. $rel_escaped .' '. $target_escaped .'>';
							}

							if ($icon) {
								if( is_array($icon) ){
									// svg
									$output .= '<span class="button_icon"><i class="'. esc_attr($icon) .'"></i></span>';
								} else {
									// icon
									$output .= '<span class="button_icon"><i class="'. esc_attr($icon) .'"></i></span>';
								}
							}

							$output .= '<span class="button_label">'. esc_html($button_title) .'</span>';

							if ($link) {
								$output .= '</a>';
							}

						} else {

							if ($link) {
								// This variable has been safely escaped above in this function
								$output .= '<a href="'. esc_url($link) .'" class="'. esc_attr($class) .'" '. $rel_escaped .' '. $target_escaped .'>';
							}

							if( is_array($icon) ){
								// svg
								$output .= '<span class="icon_wrapper"><img src="'. esc_attr($icon['url']) .'" width="30" alt="" /></span>';
							} else {
								// icon
								$output .= '<span class="icon_wrapper"><i class="'. esc_attr($icon) .'"></i></span>';
							}

							if ($link) {
								$output .= '</a>';
							}
						}

					$output .= '</div>';

					$output .= '<div class="call_right">';
						$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
					$output .= '</div>';

				$output .= '</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Chart [chart]
 */

if (! function_exists('sc_chart')) {
	function sc_chart($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' => '',
			'percent' => '',
			'label' => '',
			'icon' => '',
			'image' => '',
			'line_width' => '',
			'color' => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// color

		if( ! $color ){
			$color = mfn_opts_get('color-counter', '#2991D6');
		}

		// line width

		if ($line_width) {
			$line_width_escaped = 'data-line-width="'. intval($line_width, 10) .'"';
		} else {
			$line_width_escaped = false;
		}

		$output = '<div class="chart_box">';

			// This variable has been safely escaped above in this function
			$output .= '<div class="chart" data-percent="'. intval($percent, 10) .'" data-bar-color="'. esc_attr($color) .'" '. $line_width_escaped .'>';

				if ($image) {
					$output .= '<div class="image"><img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/></div>';
				} elseif ($icon) {
					$output .= '<div class="icon"><i class="'. esc_attr($icon) .'"></i></div>';
				} else {
					$output .= '<div class="num">'. esc_html($label) .'</div>';
				}

			$output .= '</div>';

			$output .= '<p><big>'. esc_html($title) .'</big></p>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Countdown [countdown]
 */

if (! function_exists('sc_countdown')) {
	function sc_countdown($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'date'			=> '12/30/2020 12:00:00',
			'timezone'	=> '0',
			'show'			=> '',
		), $attr));

		// translate

		$translate['days'] = mfn_opts_get('translate') ? mfn_opts_get('translate-days', 'days') : __('days', 'betheme');
		$translate['hours'] = mfn_opts_get('translate') ? mfn_opts_get('translate-hours', 'hours') : __('hours', 'betheme');
		$translate['minutes'] = mfn_opts_get('translate') ? mfn_opts_get('translate-minutes', 'minutes') : __('minutes', 'betheme');
		$translate['seconds']	= mfn_opts_get('translate') ? mfn_opts_get('translate-seconds', 'seconds') : __('seconds', 'betheme');

		// number of columns to show

		switch ($show) {
			case 'dhm':
				$hide = 1;
				$columns = 'one-third';
				break;
			case 'dh':
				$hide = 2;
				$columns = 'one-second';
				break;
			case 'd':
				$hide = 3;
				$columns = 'one';
				break;
			default:
				$hide = 0;
				$columns = 'one-fourth';
		}

		$output = '<div class="downcount clearfix" data-date="'. esc_attr($date) .'" data-offset="'. esc_attr($timezone) .'">';

			$output .= '<div class="column column_quick_fact '. esc_attr($columns) .'">';
				$output .= '<div class="quick_fact">';
					$output .= '<div data-anim-type="zoomIn" class="animate zoomIn">';
						$output .= '<div class="number-wrapper">';
							$output .= '<div class="number days">00</div>';
						$output .= '</div>';
						$output .= '<h3 class="title">'. esc_html($translate['days']) .'</h3>';
						$output .= '<hr class="hr_narrow">';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

			if (3 > $hide) {
				$output .= '<div class="column column_quick_fact '. esc_attr($columns) .'">';
					$output .= '<div class="quick_fact">';
						$output .= '<div data-anim-type="zoomIn" class="animate zoomIn">';
							$output .= '<div class="number-wrapper">';
								$output .= '<div class="number hours">00</div>';
							$output .= '</div>';
							$output .= '<h3 class="title">'. esc_html($translate['hours']) .'</h3>';
							$output .= '<hr class="hr_narrow">';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}

			if (2 > $hide) {
				$output .= '<div class="column column_quick_fact '. esc_attr($columns) .'">';
					$output .= '<div class="quick_fact">';
						$output .= '<div data-anim-type="zoomIn" class="animate zoomIn">';
							$output .= '<div class="number-wrapper">';
								$output .= '<div class="number minutes">00</div>';
							$output .= '</div>';
							$output .= '<h3 class="title">'. esc_html($translate['minutes']) .'</h3>';
							$output .= '<hr class="hr_narrow">';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}

			if (1 > $hide) {
				$output .= '<div class="column column_quick_fact '. esc_attr($columns) .'">';
					$output .= '<div class="quick_fact">';
						$output .= '<div data-anim-type="zoomIn" class="animate zoomIn">';
							$output .= '<div class="number-wrapper">';
								$output .= '<div class="number seconds">00</div>';
							$output .= '</div>';
							$output .= '<h3 class="title">'. esc_html($translate['seconds']) .'</h3>';
							$output .= '<hr class="hr_narrow">';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Counter [counter]
 */

if (! function_exists('sc_counter')) {
	function sc_counter($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon' 			=> '',
			'color' 		=> '',
			'image' 		=> '',
			'number' 		=> '',
			'prefix' 		=> '',
			'label' 		=> '',
			'title' 		=> '',
			'type'	 		=> 'vertical',
			'animate'	 	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// color

		if ($color) {
			$style_escaped = 'style="color:'. esc_attr($color) .';"';
		} else {
			$style_escaped = false;
		}

		// animate math

		$animate_math = mfn_opts_get('math-animations-disable') ? false : 'animate-math';

		// output -----

		$output = '<div class="counter counter_'. esc_attr($type) .' '. esc_attr($animate_math) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				$output .= '<div class="icon_wrapper">';
					if ($image) {
						$output .= '<img src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					} elseif ($icon) {
						// This variable has been safely escaped above in this function
						$output .= '<i class="'. esc_attr($icon) .'" '. $style_escaped .'></i>';
					}
				$output .= '</div>';

				$output .= '<div class="desc_wrapper">';

					if ($number) {
						$output .= '<div class="number-wrapper">';

							if ($prefix) {
								$output .= '<span class="label prefix">'. esc_html($prefix) .'</span>';
							}

							$output .= '<span class="number" data-to="'. intval($number, 10) .'">'. intval($number, 10) .'</span>';

							if ($label) {
								$output .= '<span class="label postfix">'. esc_html($label) .'</span>';
							}

						$output .= '</div>';
					}

					if ($title) {
						$output .= '<p class="title">'. wp_kses($title, mfn_allowed_html()) .'</p>';
					}

				$output .= '</div>';

			if ($animate) {
				$output .= '</div>'."\n";
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Counter inline [counter_inline]
 */

if (! function_exists('sc_counter_inline')) {
	function sc_counter_inline($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'value' => '',
		), $attr));

		if( ! $value ){
			return false;
		}

		$output = '<span class="counter-inline animate-math"><span class="number" data-to="'. intval($value, 10) .'">'. intval($value, 10) .'</span></span>';

		return $output;
	}
}

/**
 * Icon [icon]
 */

if (! function_exists('sc_icon')) {
	function sc_icon($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'type' => '',
			'color' => '',
		), $attr));

		$class = $type;

		if( 'themecolor' == $color ){
			$class .= ' themecolor';
			$color = false;
		} elseif( $color ){
			$color = 'color:'. $color;
		}

		$output = '<i class="'. esc_attr($class) .'" style="'. esc_attr($color) .'"></i>';

		return $output;
	}
}

/**
 * Icon Block [icon_block]
 */

if (! function_exists('sc_icon_block')) {
	function sc_icon_block($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon'	=> '',
			'align'	=> '',
			'color'	=> '',
			'size'	=> 25,
		), $attr));

		// classes

		$class = '';

		if ($align) {
			$class .= ' icon_'. $align;
		}

		if ($color) {
			$color = 'color:'. $color .';';
		} else {
			$class .= ' themecolor';
		}

		// output -----

		$output = '<span class="single_icon '. esc_attr($class) .'">';
			$output .= '<i style="font-size:'. intval($size, 10) .'px;line-height:'. intval($size, 10) .'px;'. esc_attr($color) .'" class="'. esc_attr($icon) .'"></i>';
		$output .= '</span>'."\n";

		return $output;
	}
}

/**
 * Image [image]
 */

if (! function_exists('sc_image')) {
	function sc_image($attr, $content = null)
	{
		extract(shortcode_atts(array(

			'src'      => '',
			'size'     => '',
			'width'    => '',
			'height'   => '',

			// options
			'align'    => 'none',
			'stretch'  => '',
			'border'   => '',
			'margin'   => '',
			'margin_top'     => '',	// alias for: margin
			'margin_bottom'	 => '',

			// link
			'link_image' => '',
			'link'       => '',
			'target'     => '',
			'hover'      => '',

			// description
			'alt'      => '',
			'caption'  => '',

			// advanced
			'greyscale'  => '',
			'animate'    => '',
			'classes'    => '',

		), $attr));

		// margin

		if ($margin_top) {
			// alias for: margin
			$margin = $margin_top;
		}

		if ($margin || $margin_bottom) {
			$margin_tmp = '';

			if ($margin) {
				$margin_tmp .= 'margin-top:'. intval($margin, 10) .'px;';
			}
			if ($margin_bottom) {
				$margin_tmp .= 'margin-bottom:'. intval($margin_bottom, 10) .'px;';
			}

			$style_escaped = 'style="'. esc_attr($margin_tmp) .'"';
		} else {
			$style_escaped = false;
		}

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// double link

		if ($link & $link_image) {
			$double_link = 'double';
		} else {
			$double_link = false;
		}

		// DIV image_frame | classes

		$class_div 	= '';

		// align

		if ($align) {
			$class_div .= ' align'. $align;
		}

		// stretch

		if ($stretch == 'ultrawide') {
			$class_div .= ' stretch-ultrawide';
		} elseif ($stretch) {
			$class_div .= ' stretch';
		}

		// border

		if ($border) {
			$class_div .= ' has_border';
		} else {
			$class_div .= ' no_border';
		}

		// greyscale

		if ($greyscale) {
			$class_div .= ' greyscale';
		}

		// hover

		if ($hover) {
			$class_div .= ' hover-disable';
		}

		// width x height, alt

		if (! $alt) {
			$alt = mfn_get_attachment_data($src, 'alt');
		}

		$title = mfn_get_attachment_data($src, 'title');

		// image size

		if ($size) {
			if ($img_id = mfn_get_attachment_id_url($src)) {
				$img_src = wp_get_attachment_image_src($img_id, $size);

				$src 	= $img_src[0];
				$width 	= $img_src[1];
				$height = $img_src[2];
			}
		}

		if (! $width) {
			$width 	= mfn_get_attachment_data($src, 'width');
		}
		if (! $height) {
			$height = mfn_get_attachment_data($src, 'height');
		}

		if ($width) {
			$width_escaped = 'width="'. esc_attr($width) .'"';
		} else {
			$width_escaped = false;
		}

		if ($height) {
			$height_escaped = 'height="'. esc_attr($height) .'"';
		} else {
			$height_escaped = false;
		}

		// prettyPhoto -----

		$link_all = $link;

		if ($link_all) {

			$rel_escaped = false;

			if( false !== strpos( $classes, 'scroll' ) ){
				$rel_escaped = 'class="scroll"';
			}

		} else {

			$link_all        = $link_image;
			$rel_escaped     = 'rel="prettyphoto"';
			$target_escaped  = false;

		}

		// image output -----

		// This variable has been safely escaped above in this function
		$image_output_escapes = '<img class="scale-with-grid" src="'. esc_url($src) .'" alt="'. esc_attr($alt) .'" title="'. esc_attr($title) .'" '. $width_escaped .' '. $height_escaped .'/>';

		// output -----

		$output = '';

		if ($animate) {
			$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
		}

			if ($link || $link_image) {

				// This variable has been safely escaped above in this function
				$output .= '<div class="image_frame image_item scale-with-grid'. esc_attr($class_div) .'" '. $style_escaped .'>';

					$output .= '<div class="image_wrapper">';

						// This variable has been safely escaped above in this function
						$output .= '<a href="'. esc_url($link_all) .'" '. $rel_escaped .' '. $target_escaped .'>';
							$output .= '<div class="mask"></div>';
							// This variable has been safely escaped above in this function
							$output .= $image_output_escapes;
						$output .= '</a>';

						$output .= '<div class="image_links '. esc_attr($double_link) .'">';
							if ($link_image) {
								$output .= '<a href="'. esc_url($link_image) .'" class="zoom" rel="prettyphoto"><i class="icon-search"></i></a>';
							}
							if ($link) {
								// This variable has been safely escaped above in this function
								$output .= '<a href="'. esc_url($link) .'" class="link" '. $target_escaped .'><i class="icon-link"></i></a>';
							}
						$output .= '</div>';

					$output .= '</div>';

					if ($caption) {
						$output .= '<p class="wp-caption-text">'. wp_kses($caption, mfn_allowed_html('caption')) .'</p>';
					}

				$output .= '</div>'."\n";
			} else {

				// This variable has been safely escaped above in this function
				$output .= '<div class="image_frame image_item no_link scale-with-grid'. esc_attr($class_div) .'" '. $style_escaped .'>';

					$output .= '<div class="image_wrapper">';
						// This variable has been safely escaped above in this function
						$output .= $image_output_escapes;
					$output .= '</div>';

					if ($caption) {
						$output .= '<p class="wp-caption-text">'. wp_kses($caption, mfn_allowed_html('caption')) .'</p>';
					}

				$output .= '</div>'."\n";
			}

		if ($animate) {
			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Hover Box [hover_box]
 */

if (! function_exists('sc_hover_box')) {
	function sc_hover_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image'        => '',
			'image_hover'  => '',
			'link'         => '',
			'target'       => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);
		$image_hover = mfn_vc_image($image_hover);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="hover_box">';

			if ($link) {
				// This variable has been safely escaped above in this function
				$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
			}

				$output .= '<div class="hover_box_wrapper">';
					$output .= '<img class="visible_photo scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					$output .= '<img class="hidden_photo scale-with-grid" src="'. esc_url($image_hover) .'" alt="'. esc_attr(mfn_get_attachment_data($image_hover, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image_hover, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image_hover, 'height')) .'"/>';
				$output .= '</div>';

			if ($link) {
				$output .= '</a>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Hover Color [hover_color]
 */

if (! function_exists('sc_hover_color')) {
	function sc_hover_color($attr, $content = null)
	{
		extract(shortcode_atts(array(

			'align'        => '',
			'background'   => '',
			'background_hover' => '',
			'border'       => '',
			'border_hover' => '',
			'border_width' => '',
			'padding'      => '',

			'link'         => '',
			'class'        => '',
			'target'       => '',

			'style'        => '',

		), $attr));

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// padding

		if ($padding) {
			$padding = 'padding:'. $padding .';';
		}

		// FIX | prettyphoto

		if (strpos($class, 'prettyphoto') !== false) {
			$class = str_replace('prettyphoto', '', $class);
			$rel_escaped = 'rel="prettyphoto"';
		} else {
			$rel_escaped = false;
		}

		// output -----

		$output = '<div class="hover_color align_'. esc_attr($align) .'" style="background-color:'. esc_attr($background_hover) .';border-color:'. esc_attr($border_hover) .';'. esc_attr($style) .'" ontouchstart="this.classList.toggle(\'hover\');">';
			$output .= '<div class="hover_color_bg" style="background-color:'. esc_attr($background) .';border-color:'. esc_attr($border) .';border-width:'. esc_attr($border_width) .';">';

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. $link .'" class="'. esc_attr($class) .'" '. $rel_escaped .' '. $target_escaped .'>';
				}

					$output .= '<div class="hover_color_wrapper" style="'. esc_attr($padding) .'">';
						$output .= do_shortcode($content);
					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			$output .= '</div>'."\n";
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Quick Fact [quick_fact]
 */

if (! function_exists('sc_quick_fact')) {
	function sc_quick_fact($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'heading'  => '',
			'title'    => '',
			'number'   => '',
			'prefix'   => '',
			'label'    => '',
			'align'    => 'center',
			'animate'  => '',
		), $attr));

		// animate math

		$animate_math = mfn_opts_get('math-animations-disable') ? false : 'animate-math';

		// output -----

		$output = '<div class="quick_fact align_'. esc_attr($align) .' '. esc_attr($animate_math) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($heading) {
					$output .= '<h4 class="title">'. wp_kses($heading, mfn_allowed_html()) .'</h4>';
				}

				if ($number) {
					$output .= '<div class="number-wrapper">';

						if ($prefix) {
							$output .= '<span class="label prefix">'. esc_html($prefix) .'</span>';
						}

						$output .= '<span class="number" data-to="'. esc_attr($number) .'">'. esc_html($number) .'</span>';

						if ($label) {
							$output .= '<span class="label postfix">'. esc_html($label) .'</span>';
						}

					$output .= '</div>';
				}

				if ($title) {
					$output .= '<h3 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h3>';
				}

				$output .= '<hr class="hr_narrow" />';

				$output .= '<div class="desc">'. do_shortcode($content) .'</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Button [button]
 */

if (! function_exists('sc_button')) {
	function sc_button($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' => 'Button',
			'link' => '',
			'target' => '',
			'align' => '',

			'icon' => '',
			'icon_position' => 'left',

			'color' => '',
			'font_color' => '',

			'large' => '', // deprecated since Be 12.4
			'size' => 2,
			'full_width' => '',

			'class' => '',
			'rel' => '',
			'download' => '',
			'onclick' => '',
		), $attr));

		// target

		if( 'lightbox' === $target ) {
			$target_escaped = false;
			$rel = 'prettyphoto '. $rel; // do not change order
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// download

		if ($download) {
			$download_escaped = 'download="'. $download .'"';
		} else {
			$download_escaped = false;
		}

		// onclick

		if ($onclick) {
			$onclick_escaped = 'onclick="'. $onclick .'"';
		} else {
			$onclick_escaped = false;
		}

		// icon_position

		if ($icon_position != 'left') {
			$icon_position = 'right';
		}

		// FIX | prettyphoto

		if (strpos($class, 'prettyphoto') !== false) {
			$class 	= str_replace('prettyphoto', '', $class);
			$rel = 'prettyphoto '. $rel; // do not change order
		}

		// class

		if ($icon) {
			$class .= ' button_'. $icon_position;
		}
		if ($full_width) {
			$class .= ' button_full_width';
		}
		if ($large) {
			$class .= ' button_large';
		} else {
			if ($size) {
				$class .= ' button_size_'. $size;
			}
		}

		// custom color

		$style = '';
		$style_icon	= '';

		if ($color) {
			if (strpos($color, '#') === 0) {
				if (mfn_opts_get('button-style') == 'stroke') {

					// Stroke | Border
					$style .= 'border-color:'. $color .'!important;';
					$class .= ' button_stroke_custom';

				} else {

					// Default | Background
					$style .= 'background-color:'. $color .'!important;';

				}
			} else {
				$class .= ' button_'. $color;
			}
		} else {
			if( 'dark' == mfn_brightness(mfn_opts_get('background-button','#f7f7f7')) ){
				$class .= ' button_dark';
			}
		}

		if ($font_color) {
			$style .= 'color:'. $font_color .';';
			$style_icon = 'color:'. $font_color .'!important;';
		}

		if ($style) {
			$style_escaped = ' style="'. esc_attr($style) .'"';
		} else {
			$style_escaped = false;
		}

		if ($style_icon) {
			$style_icon_escaped = ' style="'. $style_icon .'"';
		} else {
			$style_icon_escaped = false;
		}

		// rel (do not move up)

		if ($rel) {
			$rel_escaped = 'rel="'. esc_attr($rel) .'"';
		} else {
			$rel_escaped = false;
		}

		// link attributes

		// This variable has been safely escaped above in this function
		$attributes_escaped = $style_escaped .' '. $target_escaped .' '. $rel_escaped .' '. $download_escaped .' '. $onclick_escaped;

		// output -----

		$output = '';

		if ($align) {
			$output .= '<div class="button_align align_'. esc_attr($align) .'">';
		}

			// This variable has been safely escaped above in this function
			$output .= '<a class="button '. esc_attr($class) .' button_js" href="'. esc_url($link) .'"'. $attributes_escaped .'>';

				if ($icon) {
					// This variable has been safely escaped above in this function
					$output .= '<span class="button_icon"><i class="'. esc_attr($icon) .'" '. $style_icon_escaped .'></i></span>';
				}
				if ($title) {
					$output .= '<span class="button_label">'. wp_kses($title, mfn_allowed_html('button')) .'</span>';
				}

			$output .= '</a>';

		if ($align) {
			$output .= '</div>';
		}

		$output .= "\n";

		return $output;
	}
}

/**
 * Icon Bar [icon_bar]
 */

if (! function_exists('sc_icon_bar')) {
	function sc_icon_bar($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon' 			=> 'icon-lamp',
			'link' 			=> '',
			'target' 		=> '',
			'size' 			=> '',
			'social' 		=> '',
		), $attr));

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// class

		$class = '';
		if ($social) {
			$class .= ' icon_bar_'. $social;
		}
		if ($size) {
			$class .= ' icon_bar_'. $size;
		}

		// This variable has been safely escaped above in this function
		$output = '<a href="'. esc_url($link) .'" class="icon_bar '. esc_attr($class) .'" '. $target_escaped .'>';
			$output .= '<span class="t"><i class="'. esc_attr($icon) .'"></i></span>';
			$output .= '<span class="b"><i class="'. esc_attr($icon) .'"></i></span>';
		$output .= '</a>'."\n";

		return $output;
	}
}

/**
 * Alert [alert] [/alert]
 */

if (! function_exists('sc_alert')) {
	function sc_alert($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'style' => 'warning',
		), $attr));

		// style

		switch ($style) {
			case 'error':
				$icon = 'icon-alert';
				break;
			case 'info':
				$icon = 'icon-help';
				break;
			case 'success':
				$icon = 'icon-check';
				break;
			default:
				$icon = 'icon-lamp';
				break;
		}

		// output -----

		$output = '<div class="alert alert_'. esc_attr($style) .'">';

			$output .= '<div class="alert_icon">';
				$output .= '<i class="'. esc_attr($icon) .'"></i>';
			$output .= '</div>';

			$output .= '<div class="alert_wrapper">';
				$output .= do_shortcode($content);
			$output .= '</div>';

			$output .= '<a href="#" class="close"><i class="icon-cancel"></i></a>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Idea [idea] [/idea]
 */

if (! function_exists('sc_idea')) {
	function sc_idea($attr, $content = null)
	{
		$output = '<div class="idea_box">';
			$output .= '<div class="icon"><i class="icon-lamp"></i></div>';
			$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Dropcap [dropcap] [/dropcap]
 */

if (! function_exists('sc_dropcap')) {
	function sc_dropcap($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'font'         => '',
			'size'         => 1, // 1-3, or custom size in px
			'background'   => '',
			'color'        => '',
			'circle'       => '',
			'transparent'  => '',
		), $attr));

		$class = '';
		$style = '';

		// font family

		if ($font) {
			$style .= "font-family:'". $font ."',Arial,Tahoma,sans-serif;";
			$font_slug = str_replace(' ', '+', $font);
			wp_enqueue_style($font_slug, 'https://fonts.googleapis.com/css?family='. esc_attr($font_slug) .':400');
		}

		// circle

		if ($circle) {
			$class = ' dropcap_circle';
		}

		// transparent

		if ($transparent) {
			$class = ' transparent';
		}

		// background

		if ($background) {
			$style .= 'background-color:'. $background .';';
		}

		// color

		if ($color) {
			$style .= ' color:'. $color .';';
		}

		// size

		$size = intval($size, 10);
		$sizeH = $size + 15;

		if ($size > 3) {
			if ($transparent) {
				$style .= ' font-size:'. $size .'px;height:'. $size .'px;line-height:'. $size .'px;width:'. $size .'px;';
			} else {
				$style .= ' font-size:'. $size .'px;height:'. $sizeH .'px;line-height:'. $sizeH .'px;width:'. $sizeH .'px;';
			}
		} else {
			$class .= ' size-'. $size;
		}

		if ($style) {
			$style_escaped = 'style="'. esc_attr($style) .'"';
		} else {
			$style_escaped = false;
		}

		// output -----

		// This variable has been safely escaped above in this function
		$output  = '<span class="dropcap'. esc_attr($class) .'" '. $style_escaped .'>';
			$output .= do_shortcode($content);
		$output .= '</span>'."\n";

		return $output;
	}
}

/**
 * Highlight [highlight] [/highlight]
 */

if (! function_exists('sc_highlight')) {
	function sc_highlight($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'background' => '',
			'color' 		 => '',
		), $attr));

		// style

		$style = '';
		if ($background) {
			$style .= 'background-color:'. $background .';';
		}
		if ($color) {
			$style .= 'color:'. $color .';';
		}

		if ($style) {
			$style_escaped = 'style="'. esc_attr($style) .'"';
		} else {
			$style_escaped = false;
		}

		// output -----

		// This variable has been safely escaped above in this function
		$output  = '<span class="highlight" '. $style_escaped .'>';
			$output .= do_shortcode($content);
		$output .= '</span>'."\n";

		return $output;
	}
}

/**
 * Tooltip [tooltip] [/tooltip]
 */

if (! function_exists('sc_tooltip')) {
	function sc_tooltip($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'hint' => '',
		), $attr));

		// output -----

		$output = '';

		if ($hint) {
			$output .= '<span class="tooltip tooltip-txt" data-tooltip="'. esc_attr($hint) .'">';
				$output .= do_shortcode($content);
			$output .= '</span>'."\n";
		}

		return $output;
	}
}

/**
 * Tooltip [tooltip_image] [/tooltip_image]
 */

if (! function_exists('sc_tooltip_image')) {
	function sc_tooltip_image($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'hint' 			=> '',
			'image' 		=> '',
		), $attr));

		// output -----

		$output = '';

		if ($hint || $image) {
			$output .= '<span class="tooltip tooltip-img">';

				$output .= '<span class="tooltip-content">';

					if ($image) {
						$output .= '<img src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					}

					if ($hint) {
						$output .= wp_kses($hint, mfn_allowed_html('caption'));
					}

				$output .= '</span>';

				$output .= do_shortcode($content);

			$output .= '</span>'."\n";
		}

		return $output;
	}
}

/**
 * Content Link [content_link]
 */

if (! function_exists('sc_content_link')) {
	function sc_content_link($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'    => '',
			'icon'     => '',
			'link'     => '',
			'target'   => '',
			'class'    => '',
			'download' => '',
		), $attr));

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// download

		if ($download) {
			$download_escaped = 'download="'. esc_attr($download) .'"';
		} else {
			$download_escaped = false;
		}

		// output -----

		// This variable has been safely escaped above in this function
		$output = '<a class="content_link '. esc_attr($class) .'" href="'. esc_url($link) .'" '. $target_escaped .' '. $download_escaped .'>';
			if ($icon) {
				$output .= '<span class="icon"><i class="'. esc_attr($icon) .'"></i></span>';
			}
			if ($title) {
				$output .= '<span class="title">'. wp_kses($title, mfn_allowed_html()) .'</span>';
			}
		$output .= '</a>';

		return $output;
	}
}

/**
 * Fancy Link [fancy_link]
 */

if (! function_exists('sc_fancy_link')) {
	function sc_fancy_link($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'    => '',
			'link'     => '',
			'target'   => '',
			'style'    => '1',	// 1-8
			'class'    => '',
			'download' => '',
		), $attr));

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// download

		if ($download) {
			$download_escaped = 'download="'. esc_attr($download) .'"';
		} else {
			$download_escaped = false;
		}

		// output -----

		// This variable has been safely escaped above in this function
		$output = '<a class="mfn-link mfn-link-'. intval($style, 10) .' '. esc_attr($class) .'" href="'. esc_url($link) .'" data-hover="'. esc_html($title) .'" ontouchstart="this.classList.toggle(\'hover\');" '. $target_escaped .' '. $download_escaped .'>';
			$output .= '<span data-hover="'. esc_html($title) .'">'. esc_html($title) .'</span>';
		$output .= '</a>';

		return $output;
	}
}

/**
 * Blockquote [blockquote] [/blockquote]
 */

if (! function_exists('sc_blockquote')) {
	function sc_blockquote($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'author' => '',
			'link'   => '',
			'target' => 0,
		), $attr));

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="blockquote">';

			$output .= '<blockquote>'. do_shortcode($content) .'</blockquote>';

			if ($author) {
				$output .= '<p class="author">';

					$output .= '<i class="icon-user"></i>';

					if ($link) {
						// This variable has been safely escaped above in this function
						$output .= '<a href="'. esc_attr($link) .'" '. $target_escaped .'>'. esc_html($author) .'</a>';
					} else {
						$output .= '<span>'. esc_html($author) .'</span>';
					}

				$output .= '</p>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Clients [clients]
 */

if (! function_exists('sc_clients')) {
	function sc_clients($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'in_row'     => 6,
			'category'   => '',
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
			'style'      => '',
			'greyscale'  => '',
		), $attr));

		// class

		$class = '';

		if ($greyscale) {
			$class .= ' greyscale';
		}
		if ($style) {
			$class .= ' clients_tiles';
		}

		if (! intval($in_row, 10)) {
			$in_row = 6;
		}

		// query args

		$args = array(
			'post_type'      => 'client',
			'posts_per_page' => -1,
			'orderby'        => $orderby,
			'order'          => $order,
		);

		if ($category) {
			$args['client-types'] = $category;
		}

		$clients_query = new WP_Query();
		$clients_query->query($args);

		// output -----

		$output  = '<ul class="clients clearfix '. esc_attr($class) .'">';

			if ($clients_query->have_posts()) {
				$i = 1;
				$width = round((100 / $in_row), 3);

				while ($clients_query->have_posts()) {
					$clients_query->the_post();

					$output .= '<li style="width:'. esc_attr($width) .'%">';
						$output .= '<div class="client_wrapper">';

							$link = get_post_meta(get_the_ID(), 'mfn-post-link', true);

							if ($link) {
								$output .= '<a target="_blank" href="'. esc_url($link) .'" title="'. the_title(false, false, false) .'">';
							}

								$output .= '<div class="gs-wrapper">';
									$output .= get_the_post_thumbnail(null, 'clients-slider', array( 'class'=>'scale-with-grid' ));
								$output .= '</div>';

							if ($link) {
								$output .= '</a>';
							}

						$output .= '</div>';
					$output .= '</li>';

					$i++;
				}
			}

			wp_reset_query();

		$output .= '</ul>'."\n";

		return $output;
	}
}

/**
 * Clients slider [clients_slider]
 */

if (! function_exists('sc_clients_slider')) {
	function sc_clients_slider($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'    => '',
			'category' => '',
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
		), $attr));

		// query args

		$args = array(
			'post_type'      => 'client',
			'posts_per_page' => -1,
			'orderby'        => $orderby,
			'order'          => $order,
		);

		if ($category) {
			$args['client-types'] = $category;
		}

		$clients_query = new WP_Query();
		$clients_query->query($args);

		// output -----

		$output = '';

		if ($clients_query->have_posts()) {
			$output  = '<div class="clients_slider">';

				$output .= '<div class="clients_slider_header">';
					if ($title) {
						$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
					}
				$output .= '</div>';

				$output .= '<ul class="clients clients_slider_ul">';
					while ($clients_query->have_posts()) {
						$clients_query->the_post();

						$output .= '<li>';
							$output .= '<div class="client_wrapper">';

								$link = get_post_meta(get_the_ID(), 'mfn-post-link', true);

								if ($link) {
									$output .= '<a target="_blank" href="'. esc_url($link) .'" title="'. the_title(false, false, false) .'">';
								}

									$output .= get_the_post_thumbnail(null, 'clients-slider', array( 'class' => 'scale-with-grid' ));

								if ($link) {
									$output .= '</a>';
								}

							$output .= '</div>';
						$output .= '</li>';
					}
				$output .= '</ul>';

			$output .= '</div>'."\n";
		}

		wp_reset_query();

		return $output;
	}
}

/**
 * Fancy Heading [fancy_heading] [/fancy_heading]
 */

if (! function_exists('sc_fancy_heading')) {
	function sc_fancy_heading($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'    => '',
			'h1'       => '',
			'icon'     => '',
			'slogan'   => '',
			'style'    => 'icon',	// icon, line, arrows
			'animate'  => '',
		), $attr));

		// output -----

		$output = '<div class="fancy_heading fancy_heading_'. esc_attr($style) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($style == 'icon' && $icon) {
					$output .= '<span class="icon_top"><i class="'. esc_attr($icon) .'"></i></span>';
				}

				if ($style == 'line' && $slogan) {
					$output .= '<span class="slogan">'. wp_kses($slogan, mfn_allowed_html()) .'</span>';
				}

				if ($style =='arrows') {
					$title = '<i class="icon-right-dir"></i>'. wp_kses($title, mfn_allowed_html()) .'<i class="icon-left-dir"></i>';
				}

				if ($title) {
					if ($h1) {
						$output .= '<h1 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h1>';
					} else {
						$output .= '<h2 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h2>';
					}
				}
				if ($content) {
					$output .= '<div class="inside">'. do_shortcode($content) .'</div>';
				}

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Heading [heading] [/heading]
 */

if (! function_exists('sc_heading')) {
	function sc_heading($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'tag'    => 'h2',
			'align'  => 'left',
			'color'  => '',
			'style'  => '', // [none], lines
			'color2' => '',
		), $attr));

		$before = $after = '';

		// inline_css

		$inline_css = '';

		if ($color) {
			$inline_css .= 'color:'. $color .';';
		}

		if ($inline_css) {
			$inline_css_escaped = 'style="'. esc_attr($inline_css) .'"';
		} else {
			$inline_css_escaped = false;
		}

		// inline_css_line

		$inline_css_line = '';

		if ($color2) {
			$inline_css_line .= 'background:'. $color2 .';';
		}

		if ($inline_css_line) {
			$inline_css_line_escaped = 'style="'. esc_attr($inline_css_line) .'"';
		} else {
			$inline_css_line_escaped = false;
		}

		// style

		if ($style == 'lines') {
			// This variable has been safely escaped above in this function
			$before	= '<span class="line line_l" '. $inline_css_line_escaped .'></span>';
			$after	= '<span class="line line_r" '. $inline_css_line_escaped .'></span>';
		}

		if ($style) {
			$style = 'heading_'. $style;
		}

		// output -----

		$output = '<div class="mfn_heading '. esc_attr($style) .' align_'. esc_attr($align).'">';

			// This variable has been safely escaped above in this function
			$output .= '<'. esc_attr($tag) .' class="title" '. $inline_css_escaped .'>';

				$output .= $before;

					$output .= do_shortcode($content);

				$output .= $after;

			$output .= '</'. esc_attr($tag) .'>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Icon Box [icon_box] [/icon_box]
 */

if (! function_exists('sc_icon_box')) {
	function sc_icon_box($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title'      => '',
			'title_tag'  => 'h4',

			'icon'       => '',
			'image'      => '',
			'icon_position'  => 'top',
			'border'     => '',

			'link'       => '',
			'target'     => '',
			'class'      => '',

			'animate'    => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// border

		if ($border) {
			$border = 'has_border';
		} else {
			$border = 'no_border';
		}

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// FIX | prettyphoto

		if (strpos($class, 'prettyphoto') !== false) {
			$class 	= str_replace('prettyphoto', '', $class);
			$rel_escaped 	= 'rel="prettyphoto"';
		} else {
			$rel_escaped 	= false;
		}

		// output -----

		$output = '';

		if ($animate) {
			$output .= '<div class="animate" data-anim-type="'. $animate .'">';
		}

			$output .= '<div class="icon_box icon_position_'. esc_attr($icon_position) .' '. esc_attr($border) .'">';

				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a class="'. esc_attr($class) .'" href="'. esc_url($link) .'" '. $target_escaped .' '. $rel_escaped .'>';
				}

					if ($image) {
						$output .= '<div class="image_wrapper">';
							$output .= '<img src="'. esc_url($image) .'" class="scale-with-grid" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
						$output .= '</div>';
					} else {
						$output .= '<div class="icon_wrapper">';
							$output .= '<div class="icon">';
								$output .= '<i class="'. esc_attr($icon) .'"></i>';
							$output .= '</div>';
						$output .= '</div>';
					}

					$output .= '<div class="desc_wrapper">';

						if ($title) {
							$output .= '<'. esc_attr($title_tag) .' class="title">'. wp_kses($title, mfn_allowed_html()) .'</'. esc_attr($title_tag) .'>';
						}
						if ($content) {
							$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
						}

					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}

			$output .= '</div>'."\n";

		if ($animate) {
			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Our Team [our_team]
 */

if (! function_exists('sc_our_team')) {
	function sc_our_team($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'heading'  => '',
			'image'    => '',
			'title'    => '',
			'subtitle' => '',

			'phone'    => '',
			'email'    => '',

			'facebook' => '',
			'twitter'  => '',
			'linkedin' => '',
			'vcard'    => '',

			'blockquote' => '',
			'style'      => 'vertical',

			'link'     => '',
			'target'   => '',

			'animate'  => '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="team team_'. esc_attr($style) .'">';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}

				if ($heading) {
					$output .= '<h4 class="title">'. wp_kses($heading, mfn_allowed_html()) .'</h4>';
				}

				$output .= '<div class="image_frame photo no_link scale-with-grid">';
					$output .= '<div class="image_wrapper">';

						if ($link) {
							// This variable has been safely escaped above in this function
							$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
						}
							$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
						if ($link) {
							$output .= '</a>';
						}

					$output .= '</div>';
				$output .= '</div>';

				$output .= '<div class="desc_wrapper">';

					if ($title) {
						$output .= '<h4>';
							if ($link) {
								// This variable has been safely escaped above in this function
								$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
							}
								$output .= wp_kses($title, mfn_allowed_html());
							if ($link) {
								$output .= '</a>';
							}
						$output .= '</h4>';
					}

					if ($subtitle) {
						$output .= '<p class="subtitle">'. wp_kses($subtitle, mfn_allowed_html()) .'</p>';
					}
					if ($phone) {
						$output .= '<p class="phone"><i class="icon-phone"></i> <a href="tel:'. esc_attr($phone) .'">'. esc_html($phone) .'</a></p>';
					}
					$output .= '<hr class="hr_color" />';

					$output .= '<div class="desc">'. do_shortcode($content) .'</div>';

					if ($email || $phone || $facebook || $twitter || $linkedin) {
						$output .= '<div class="links">';
							if ($email) {
								$output .= '<a href="mailto:'. esc_attr($email) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-mail"></i></span><span class="b"><i class="icon-mail"></i></span></a>';
							}
							if ($facebook) {
								$output .= '<a target="_blank" href="'. esc_url($facebook) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-facebook"></i></span><span class="b"><i class="icon-facebook"></i></span></a>';
							}
							if ($twitter) {
								$output .= '<a target="_blank" href="'. esc_url($twitter) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-twitter"></i></span><span class="b"><i class="icon-twitter"></i></span></a>';
							}
							if ($linkedin) {
								$output .= '<a target="_blank" href="'. esc_url($linkedin) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-linkedin"></i></span><span class="b"><i class="icon-linkedin"></i></span></a>';
							}
							if ($vcard) {
								$output .= '<a href="'. esc_url($vcard) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-vcard"></i></span><span class="b"><i class="icon-vcard"></i></span></a>';
							}
						$output .= '</div>';
					}

					if ($blockquote) {
						$output .= '<blockquote>'. wp_kses($blockquote, mfn_allowed_html('desc')) .'</blockquote>';
					}

				$output .= '</div>';

			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Our Team List [our_team_list]
 */

if (! function_exists('sc_our_team_list')) {
	function sc_our_team_list($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'image'			=> '',
			'title'			=> '',
			'subtitle'	=> '',
			'blockquote'=> '',
			'email'			=> '',
			'phone' 		=> '',
			'facebook' 	=> '',
			'twitter'		=> '',
			'linkedin'	=> '',
			'vcard'			=> '',
			'link' 			=> '',
			'target' 		=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ( 'lightbox' === $target ) {
			$target_escaped = 'rel="prettyphoto"';
		} elseif ( $target ) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="team team_list clearfix">';

			$output .= '<div class="column one-fourth">';
				$output .= '<div class="image_frame no_link scale-with-grid">';
					$output .= '<div class="image_wrapper">';

					if ($link) {
						// This variable has been safely escaped above in this function
						$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
					}
						$output .= '<img class="scale-with-grid" src="'. esc_url($image) .'" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
					if ($link) {
						$output .= '</a>';
					}

					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="column one-second">';
				$output .= '<div class="desc_wrapper">';

					if ($title) {
						$output .= '<h4>';
						if ($link) {
							// This variable has been safely escaped above in this function
							$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
						}
							$output .= wp_kses($title, mfn_allowed_html());
						if ($link) {
							$output .= '</a>';
						}
						$output .= '</h4>';
					}

					if ($subtitle) {
						$output .= '<p class="subtitle">'. wp_kses($subtitle, mfn_allowed_html()) .'</p>';
					}
					if ($phone) {
						$output .= '<p class="phone"><i class="icon-phone"></i> <a href="tel:'. esc_attr($phone) .'">'. esc_html($phone) .'</a></p>';
					}
					$output .= '<hr class="hr_color" />';

					$output .= '<div class="desc">'. do_shortcode($content) .'</div>';

				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="column one-fourth">';
				$output .= '<div class="bq_wrapper">';

					if ($blockquote) {
						$output .= '<blockquote>'. wp_kses($blockquote, mfn_allowed_html('desc')) .'</blockquote>';
					}

					if ($email || $phone || $facebook || $twitter || $linkedin) {
						$output .= '<div class="links">';
						if ($email) {
							$output .= '<a href="mailto:'. esc_attr($email) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-mail"></i></span><span class="b"><i class="icon-mail"></i></span></a>';
						}
						if ($facebook) {
							$output .= '<a target="_blank" href="'. esc_url($facebook) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-facebook"></i></span><span class="b"><i class="icon-facebook"></i></span></a>';
						}
						if ($twitter) {
							$output .= '<a target="_blank" href="'. esc_url($twitter) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-twitter"></i></span><span class="b"><i class="icon-twitter"></i></span></a>';
						}
						if ($linkedin) {
							$output .= '<a target="_blank" href="'. esc_url($linkedin) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-linkedin"></i></span><span class="b"><i class="icon-linkedin"></i></span></a>';
						}
						if ($vcard) {
							$output .= '<a href="'. esc_url($vcard) .'" class="icon_bar icon_bar_small"><span class="t"><i class="icon-vcard"></i></span><span class="b"><i class="icon-vcard"></i></span></a>';
						}
						$output .= '</div>';
					}

				$output .= '</div>';
			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Portfolio [portfolio]
 */

if (! function_exists('sc_portfolio')) {
	function sc_portfolio($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count' 				=> 2,
			'category' 			=> '',
			'category_multi'=> '',
			'exclude_id'		=> '',
			'orderby' 			=> 'date',
			'order' 				=> 'DESC',
			'style'					=> 'list',
			'columns'				=> 3,
			'greyscale'			=> '',
			'filters'				=> '',
			'pagination'		=> '',
			'load_more'			=> '',
			'related'				=> '',
		), $attr));

		// translate

		$translate['all'] = mfn_opts_get('translate') ? mfn_opts_get('translate-item-all', 'All') : __('All', 'betheme');

		// class

		$class = '';
		if ($greyscale) {
			$class .= ' greyscale';
		}

		// query args

		$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
		$args = array(
			'post_type' 			=> 'portfolio',
			'posts_per_page' 	=> intval($count, 10),
			'paged' 					=> $paged,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' => 1,
		);

		// categories

		if ($category_multi = trim($category_multi)) {

			$category_multi = mfn_wpml_term_slug($category_multi, 'portfolio-types', 1);
			$args['portfolio-types'] = $category_multi;

			$category_multi_array = explode(',', str_replace(' ', '', $category_multi));

		} elseif ($category) {

			$category = mfn_wpml_term_slug($category, 'portfolio-types');
			$args['portfolio-types'] = $category;

		}

		// exclude posts

		if ($exclude_id) {
			$exclude_id = str_replace(' ', '', $exclude_id);
			$args['post__not_in'] = explode(',', $exclude_id);
		}

		// related | exclude current

		if ($related) {
			$args['post__not_in'] = array( get_the_ID() );
		}

		// query

		$query_portfolio = new WP_Query($args);

		// output -----

		$output = '<div class="column_filters">';

			// output | filters

			if ($filters && ! $category) {
				$output .= '<div id="Filters" class="isotope-filters filters4portfolio" data-parent="column_filters">';
					$output .= '<div class="filters_wrapper">';
						$output .= '<ul class="categories">';

							$output .= '<li class="reset current-cat"><a class="all" data-rel="*" href="#">'. esc_html($translate['all']) .'</a></li>';
							if ($portfolio_categories = get_terms('portfolio-types')) {
								foreach ($portfolio_categories as $category) {
									if ($category_multi) {
										if (in_array($category->slug, $category_multi_array)) {
											$output .= '<li class="'. esc_attr($category->slug) .'"><a data-rel=".category-'. esc_attr($category->slug) .'" href="'. esc_url(get_term_link($category)) .'">'. esc_html($category->name) .'</a></li>';
										}
									} else {
										$output .= '<li class="'. esc_attr($category->slug) .'"><a data-rel=".category-'. esc_attr($category->slug) .'" href="'. esc_url(get_term_link($category)) .'">'. esc_html($category->name) .'</a></li>';
									}
								}
							}

						$output .= '</ul>';
					$output .= '</div>';
				$output .= '</div>'."\n";
			}

			// output | main

			$output .= '<div class="portfolio_wrapper isotope_wrapper '. esc_attr($class) .'">';

				$output .= '<ul class="portfolio_group lm_wrapper isotope col-'. intval($columns, 10) .' '. esc_attr($style) .'">';
					$output .= mfn_content_portfolio($query_portfolio, $style);
				$output .= '</ul>';

				if ($pagination) {
					$output .= mfn_pagination($query_portfolio, $load_more);
				}

			$output .= '</div>'."\n";

		$output .= '</div>'."\n";

		wp_reset_postdata();

		return $output;
	}
}

/**
 * Portfolio Grid [portfolio_grid]
 */

if (! function_exists('sc_portfolio_grid')) {
	function sc_portfolio_grid($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count'						=> '4',
			'category' 				=> '',
			'category_multi' 	=> '',
			'orderby' 				=> 'date',
			'order' 					=> 'DESC',
			'greyscale' 			=> '',
		), $attr));

		// class

		$class = '';
		if ($greyscale) {
			$class .= ' greyscale';
		}

		// query args

		$args = array(
			'post_type' 			=> 'portfolio',
			'posts_per_page' 	=> intval($count, 10),
			'paged' 					=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' => 1,
		);

		// categories

		if ($category_multi = trim($category_multi)) {
			$category_multi = mfn_wpml_term_slug($category_multi, 'portfolio-types', 1);
			$args['portfolio-types'] = $category_multi;
		} elseif ($category) {
			$category = mfn_wpml_term_slug($category, 'portfolio-types');
			$args['portfolio-types'] = $category;
		}

		// query

		$query = new WP_Query();
		$query->query($args);
		$post_count = $query->post_count;

		// output -----

		$output = '';

		if ($query->have_posts()) {
			$output  = '<ul class="portfolio_grid '. esc_attr($class) .'">';
				while ($query->have_posts()) {

					$query->the_post();

					$output .= '<li>';
						$output .= '<div class="image_frame scale-with-grid">';
							$output .= '<div class="image_wrapper">';
								$output .= mfn_post_thumbnail(get_the_ID(), 'portfolio');
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</li>';

				}
			$output .= '</ul>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Portfolio Photo [portfolio_photo]
 */

if (! function_exists('sc_portfolio_photo')) {
	function sc_portfolio_photo($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count' 					=> '5',
			'category' 				=> '',
			'category_multi'	=> '',
			'orderby' 				=> 'date',
			'order' 					=> 'DESC',
			'target' 					=> '',
			'greyscale' 			=> '',
			'margin' 					=> '',
		), $attr));

		// translate

		$translate['readmore'] = mfn_opts_get('translate') ? mfn_opts_get('translate-readmore', 'Read more') : __('Read more', 'betheme');

		// class

		$class = '';
		if ($greyscale) {
			$class .= ' greyscale';
		}
		if ($margin) {
			$class .= ' margin';
		}

		// query args

		$args = array(
			'post_type' 			=> 'portfolio',
			'posts_per_page' 	=> intval($count, 10),
			'paged' 					=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' => 1,
		);

		// categories

		if ($category_multi = trim($category_multi)) {
			$category_multi = mfn_wpml_term_slug($category_multi, 'portfolio-types', 1);
			$args['portfolio-types'] = $category_multi;
		} elseif ($category) {
			$category = mfn_wpml_term_slug($category, 'portfolio-types');
			$args['portfolio-types'] = $category;
		}

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// query

		$query = new WP_Query();
		$query->query($args);

		// output -----

		$output = '';

		if ($query->have_posts()) {
			$output  = '<div class="portfolio-photo '. esc_attr($class) .'">';
				while ($query->have_posts()) {

					$query->the_post();

					// external link to project page

					if (get_post_meta(get_the_ID(), 'mfn-post-link', true)) {
						$link = get_post_meta(get_the_ID(), 'mfn-post-link', true);
					} else {
						$link = get_permalink();
					}

					// portfolio categories

					$terms = get_the_terms(get_the_ID(), 'portfolio-types');
					$categories = array();
					if (is_array($terms)) {
						foreach ($terms as $term) {
							$categories[] = $term->name;
						}
					}
					$categories = implode(', ', $categories);

					// image
					$large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');

					$output .= '<div class="portfolio-item">';

						// This variable has been safely escaped above in this function
						$output .= '<a class="portfolio-item-bg" href="'. esc_url($link) .'" '. $target_escaped .'>';
							$output .= get_the_post_thumbnail(get_the_ID(), 'full');
							$output .= '<div class="mask"></div>';
						$output .= '</a>';

						// This variable has been safely escaped above in this function
						$output .= '<a class="portfolio-details" href="'. esc_url($link) .'" '. $target_escaped .'>';

							$output .= '<div class="details">';
								$output .= '<h3 class="title">'. get_the_title() .'</h3>';
								if ($categories) {
									$output .= '<div class="categories">'. esc_html($categories) .'</div>';
								}
							$output .= '</div>';

							$output .= '<span class="more"><h4>'. esc_html($translate['readmore']) .'</h4></span>';

						$output .= '</a>';

					$output .= '</div>';

				}
			$output .= '</div>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Portfolio Slider [portfolio_slider]
 */

if (! function_exists('sc_portfolio_slider')) {
	function sc_portfolio_slider($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count' 				=> '5',

			'category' 			=> '',
			'category_multi'=> '',
			'orderby' 			=> 'date',
			'order' 				=> 'DESC',

			'arrows'				=> '',			// [default], hover, always
			'size'					=> 'small',	// small, medium, large
			'scroll'				=> 'page',	// page, slide
		), $attr));

		// image

		$sizes = array(
			'small'		=> 380,
			'medium'	=> 480,
			'large'		=> 638,
		);

		// slider scroll type

		$scrolls = array(
			'page'		=> 5,
			'slide'		=> 1,
		);

		// class

		$class = '';
		if ($arrows) {
			$class .= ' arrows arrows_' .$arrows;
		}

		// query args

		$args = array(
			'post_type' 			=> 'portfolio',
			'posts_per_page' 	=> intval($count, 10),
			'paged' 					=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' => 1,
		);

		// categories

		if ($category_multi = trim($category_multi)) {
			$category_multi = mfn_wpml_term_slug($category_multi, 'portfolio-types', 1);
			$args['portfolio-types'] = $category_multi;
		} elseif ($category) {
			$category = mfn_wpml_term_slug($category, 'portfolio-types');
			$args['portfolio-types'] = $category;
		}

		// query

		$query = new WP_Query();
		$query->query($args);

		// output ------

		$output = '';

		if ($query->have_posts()) {
			$output  = '<div class="portfolio_slider'. esc_attr($class) .'" data-size="'. esc_attr($sizes[ $size ]) .'" data-scroll="'. esc_attr($scrolls[ $scroll ]) .'">';
				$output .= '<ul class="portfolio_slider_ul">';
					while ($query->have_posts()) {

						$query->the_post();

						$output .= '<li>';
							$output .= '<div class="image_frame scale-with-grid">';
								$output .= '<div class="image_wrapper">';
									$output .= mfn_post_thumbnail(get_the_ID(), 'portfolio');
								$output .= '</div>';
							$output .= '</div>';
						$output .= '</li>';

					}
				$output .= '</ul>';
			$output .= '</div>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Slider [slider]
 */

if (! function_exists('sc_slider')) {
	function sc_slider($attr, $content = null)
	{
		extract(shortcode_atts(array(

			'category' 		=> '',
			'orderby' 		=> 'date',
			'order' 			=> 'DESC',

			'style' 			=> '',	// [default], img-text, flat, carousel
			'navigation'	=> '',	// [default], hide-arrows, hide-dots, hide

		), $attr));

		// query args

		$args = array(
			'post_type' 			=> 'slide',
			'posts_per_page' 	=> -1,
			'paged' 					=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' 	=> 1,
		);

		if ($category) {
			$args['slide-types'] = $category;
		}

		// query

		$query = new WP_Query();
		$query->query($args);
		$post_count = $query->post_count;

		// class

		$class = $style;

		if ($class == 'description') {
			$class .= ' flat';
		}

		if ($navigation) {
			if ($navigation == 'hide') {
				$navigation = 'hide-arrows hide-dots';
			}
			$class .= ' '. $navigation;
		}

		// output -----

		$output = '';
		if ($query->have_posts()) {
			$output .= '<div class="content_slider '. esc_attr($class) .'">';
				$output .= '<ul class="content_slider_ul">';

					$i = 0;

					while ($query->have_posts()) {

						$query->the_post();
						$i++;

						$output .= '<li class="content_slider_li_'. esc_attr($i) .'">';

							$link = get_post_meta(get_the_ID(), 'mfn-post-link', true);

							// target

							$target = get_post_meta(get_the_ID(), 'mfn-post-target', true);

							if ( 'lightbox' === $target ) {
								$target_escaped = 'rel="prettyphoto"';
							} elseif ( $target ) {
								$target_escaped = 'target="_blank"';
							} else {
								$target_escaped = false;
							}

							// echo

							if ($link) {
								// This variable has been safely escaped above in this function
								$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
							}

								$output .= get_the_post_thumbnail(null, 'slider-content', array('class'=>'scale-with-grid' ));

								if ($style == 'carousel') {
									$output .= '<p class="title">'. esc_html(get_the_title(get_the_ID())) .'</p>';
								}

								if ($style == 'description') {
									$output .= '<h3 class="title">'. get_the_title(get_the_ID()) .'</h3>';
									if ($desc = get_post_meta(get_the_ID(), 'mfn-post-desc', true)) {
										$output .= '<div class="desc">'. do_shortcode($desc) .'</div>';
									}
								}

							if ($link) {
								$output .= '</a>';
							}

						$output .= '</li>';
					}

				$output .= '</ul>';

				$output .= '<div class="slider_pager slider_pagination"></div>';

			$output .= '</div>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Slider Plugin [slider_plugin]
 */

if (! function_exists('sc_slider_plugin')) {
	function sc_slider_plugin($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'rev' 		=> '',
			'layer' 	=> '',
		), $attr));

		// output -----

		$output = '';

		if ($rev) {
			// Revolution Slider

			$output .= '<div class="mfn-main-slider mfn-rev-slider">';
				$output .= do_shortcode('[rev_slider '. esc_attr($rev) .']');
			$output .= '</div>';

		} elseif ($layer) {
			// Layer Slider

			$output .= '<div class="mfn-main-slider mfn-layer-slider">';
				$output .= do_shortcode('[layerslider id="'. esc_attr($layer) .'"]');
			$output .= '</div>';
		}

		return $output;
	}
}

/**
 * Offer Slider Full [offer]
 */

if (! function_exists('sc_offer')) {
	function sc_offer($attr = false, $content = null)
	{
		extract(shortcode_atts(array(
			'category' 	=> '',
			'align' 	=> 'left',
		), $attr));

		// query args

		$args = array(
			'post_type'				=> 'offer',
			'posts_per_page'	=> -1,
			'orderby'					=> 'menu_order',
			'order'						=> 'ASC',
			'ignore_sticky_posts'	=> 1,
		);

		if ($category) {
			$args['offer-types'] = $category;
		}

		$offer_query = new WP_Query();
		$offer_query->query($args);

		// output -----

		$output = '';
		if ($offer_query->have_posts()) {
			$output .= '<div class="offer">';

				$output .= '<ul class="offer_ul">';

					while ($offer_query->have_posts()) {
						$offer_query->the_post();
						$output .= '<li class="offer_li">';

							// link

							if ($link = get_post_meta(get_the_ID(), 'mfn-post-link', true)) {
								$class = 'has-link';
							} else {
								$class = 'no-link';
							}

							// target

							if (get_post_meta(get_the_ID(), 'mfn-post-target', true)) {
								$target_escaped = 'target="_blank"';
							} else {
								$target_escaped = false;
							}

							$output .= '<div class="image_wrapper">';
								$output .= get_the_post_thumbnail(get_the_ID(), 'full', array('class'=>'scale-with-grid' ));
							$output .= '</div>';

							$output .= '<div class="desc_wrapper align_'. esc_attr($align) .' '. esc_attr($class) .'">';

								$output .= '<div class="title">';
									$output .= '<h3>'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h3>';
									if ($link) {
										// This variable has been safely escaped above in this function
										$output .= '<a href="'. esc_url($link) .'" class="button button_js" '. $target_escaped .'>';
											$output .= '<span class="button_icon"><i class="icon-layout"></i></span>';
											$output .= '<span class="button_label">'. esc_html(get_post_meta(get_the_ID(), 'mfn-post-link_title', true)) .'</span>';
										$output .= '</a>';
									}
								$output .= '</div>';

								$output .= '<div class="desc">';
									$output .= apply_filters('the_content', get_the_content());
								$output .= '</div>';

							$output .= '</div>';

						$output .= '</li>';
					}

				$output .= '</ul>';

				// pagination
				$output .= '<div class="slider_pagination"><span class="current">1</span> / <span class="count">'. intval($offer_query->post_count, 10) .'</span></div>';

			$output .= '</div>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Offer Slider Thumb [offer_thumb]
 */

if (! function_exists('sc_offer_thumb')) {
	function sc_offer_thumb($attr = false, $content = null)
	{
		extract(shortcode_atts(array(
			'category' 	=> '',
			'style' 		=> '',
			'align' 		=> 'left',
		), $attr));

		// query args

		$args = array(
			'post_type'				=> 'offer',
			'posts_per_page'	=> -1,
			'orderby'					=> 'menu_order',
			'order'						=> 'ASC',
			'ignore_sticky_posts'	=> 1,
		);

		if ($category) {
			$args['offer-types'] = $category;
		}

		$offer_query = new WP_Query();
		$offer_query->query($args);

		// output -----

		$output = '';
		if ($offer_query->have_posts()) {
			$output .= '<div class="offer_thumb '. esc_attr($style) .'">';

				$output .= '<ul class="offer_thumb_ul">';
					$i = 1;

					while ($offer_query->have_posts()) {
						$offer_query->the_post();

						$output .= '<li class="offer_thumb_li id_'. esc_attr($i) .'">';

							// the ID
							$id = get_the_ID();

							// link
							if ($link = get_post_meta($id, 'mfn-post-link', true)) {
								$class = 'has-link';
							} else {
								$class = 'no-link';
							}

							// target
							if (get_post_meta($id, 'mfn-post-target', true)) {
								$target_escaped = 'target="_blank"';
							} else {
								$target_escaped = false;
							}

							$output .= '<div class="image_wrapper">';
								$output .= get_the_post_thumbnail($id, 'full', array( 'class' => 'scale-with-grid' ));
							$output .= '</div>';

							$output .= '<div class="desc_wrapper align_'. esc_attr($align) .' '. esc_attr($class) .'">';

								if (trim(get_the_title()) || $link) {
									$output .= '<div class="title">';
										$output .= '<h3>'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h3>';
										if ($link) {
											// This variable has been safely escaped above in this function
											$output .= '<a href="'. esc_url($link) .'" class="button button_js" '. $target_escaped .'>';
												$output .= '<span class="button_icon"><i class="icon-layout"></i></span>';
												$output .= '<span class="button_label">'. esc_html(get_post_meta(get_the_ID(), 'mfn-post-link_title', true)) .'</span>';
											$output .= '</a>';
										}
									$output .= '</div>';
								}

								$output .= '<div class="desc">';
									$output .=  apply_filters('the_content', get_the_content());
								$output .= '</div>';

							$output .= '</div>';

							$output .= '<div class="thumbnail" style="display:none">';

								if ($thumbnail = get_post_meta($id, 'mfn-post-thumbnail', true)) {
									$output .= '<img src="'. esc_url($thumbnail) .'" class="scale-with-grid" alt="thumbnail" />';
								} elseif (get_the_post_thumbnail($id)) {
									$output .= get_the_post_thumbnail($id, 'testimonials', array( 'class' => 'scale-with-grid' ));
								}

							$output .= '</div>';

						$output .= '</li>';

						$i++;
					}

				$output .= '</ul>';

				// pagination
				$output .= '<div class="slider_pagination"></div>';

			$output .= '</div>'."\n";
		}
		wp_reset_query();

		return $output;
	}
}

/**
 * Map Basic [map_basic]
 */

if (! function_exists('sc_map_basic')) {
	function sc_map_basic($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'iframe' 	=> '',

			'address' => '',
			'zoom' 		=> 13,
			'height' 	=> 300,
		), $attr));

		// output -----

		$output = '';

		if ($iframe) {
			// iframe

			$output = wp_kses($iframe, array(
				'iframe' => array(
					'src' => array(),
					'width' => array(),
					'height' => array(),
					'frameborder' => array(),
					'style' => array(),
					'allowfullscreen' => array(),
				),
			));

		} elseif ($address) {
			// embed

			$address = str_replace(array( ' ' ,',' ), '+', trim($address));
			$api_key = trim(mfn_opts_get('google-maps-api-key'));
			$src = 'https://www.google.com/maps/embed/v1/place?key='. $api_key .'&q='. $address .'&zoom='. $zoom ;

			$output = '<iframe class="embed" width="100%" height="'. esc_attr($height) .'" frameborder="0" style="border:0" src="'. esc_url($src) .'" allowfullscreen></iframe>';
		}

		return $output;
	}
}

/**
 * Map [map] [/map]
 */

if (! function_exists('sc_map')) {
	function sc_map($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'lat' 		=> '',
			'lng' 		=> '',
			'zoom' 		=> 13,
			'height' 	=> 300,

			'type' 			=> 'ROADMAP',
			'controls' 	=> '',
			'draggable' => '',
			'border' 		=> '',

			'icon' 		=> '',
			'color'		=> '',
			'styles'	=> '',
			'latlng' 	=> '',

			'title'			=> '',
			'telephone'	=> '',
			'email' 		=> '',
			'www' 			=> '',
			'style' 		=> 'box',

			'uid' 		=> uniqid(),
		), $attr));

		// image | visual composer fix

		$icon = mfn_vc_image($icon);

		// border

		if ($border) {
			$class = 'has_border';
		} else {
			$class = 'no_border';
		}

		// controls

		$zoomControl = $mapTypeControl = $streetViewControl = 'false';
		if (! $controls) {
			$zoomControl = 'true';
		}
		if (strpos($controls, 'zoom') !== false) {
			$zoomControl = 'true';
		}
		if (strpos($controls, 'mapType') !== false) {
			$mapTypeControl = 'true';
		}
		if (strpos($controls, 'streetView') !== false) {
			$streetViewControl = 'true';
		}

		if ($api_key = trim(mfn_opts_get('google-maps-api-key'))) {
			$api_key = '?key='. $api_key;
		}

		// enqueue

		wp_enqueue_script('google-maps', 'https://maps.google.com/maps/api/js'. esc_attr($api_key), false, null, true);

		// output -----

		// output: JS

		$inline_script = 'function mfn_google_maps_'. esc_attr($uid) .'(){';

		  $inline_script .= 'var latlng = new google.maps.LatLng('. esc_attr($lat) .','. esc_attr($lng) .');';

		  // draggable

		  if ($draggable == 'disable') {
		    $inline_script .= 'var draggable = false;';
		  } elseif ($draggable == 'disable-mobile') {
		    $inline_script .= 'var draggable = jQuery(document).width() > 767 ? true : false;';
		  } else {
		    $inline_script .= 'var draggable = true;';
		  }

		  // 1 click color adjustment

		  if ($color && ! $styles) {

		    if ( 'light' == mfn_brightness($color) ) {

		      $styles = '[{featureType:"all",elementType:"labels",stylers:[{visibility:"on"}]},{featureType:"administrative",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"'. esc_attr($color) .'"},{visibility:"simplified"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{visibility:"on"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"50"}]},{featureType:"road",elementType:"labels.text",stylers:[{visibility:"on"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"-60"}]},{featureType:"road",elementType:"labels.text.stroke",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"50"}]},{featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"simplified"},{color:"'. esc_attr($color) .'"},{lightness:"50"}]},{featureType:"transit.station",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"-10"}]}]';

		    } else {

		      $styles = '[{featureType:"all",elementType:"labels",stylers:[{visibility:"on"}]},{featureType:"administrative",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"'. esc_attr($color) .'"},{visibility:"simplified"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{visibility:"on"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"30"},{saturation:"-10"}]},{featureType:"road",elementType:"labels.text",stylers:[{visibility:"on"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"80"}]},{featureType:"road",elementType:"labels.text.stroke",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"0"}]},{featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"simplified"},{color:"'. esc_attr($color) .'"},{lightness:"50"}]},{featureType:"transit.station",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"'. esc_attr($color) .'"},{lightness:"-20"}]}]';

		    }
		  }

		  $inline_script .= 'var myOptions = {';
		    $inline_script .= 'zoom:'. intval($zoom, 10) .',';
		    $inline_script .= 'center:latlng,';
		    $inline_script .= 'mapTypeId:google.maps.MapTypeId.'. esc_attr($type) .',';
		    if ($styles) {
		      $inline_script .= 'styles:'. $styles .',';
		    }
		    $inline_script .= 'draggable:draggable,';
		    $inline_script .= 'zoomControl:'. esc_attr($zoomControl) .',';
		    $inline_script .= 'mapTypeControl:'. esc_attr($mapTypeControl) .',';
		    $inline_script .= 'streetViewControl:'. esc_attr($streetViewControl) .',';
		    $inline_script .= 'scrollwheel:false';
		  $inline_script .= '};';

		  $inline_script .= 'var map = new google.maps.Map(document.getElementById("google-map-area-'. esc_attr($uid) .'"), myOptions);';

		  $inline_script .= 'var marker = new google.maps.Marker({';
		    $inline_script .= 'position:latlng,';
		    if ($icon) {
		      $inline_script .= 'icon:"'. esc_url($icon) .'",';
		    }
		    $inline_script .= 'map:map';
		  $inline_script .= '});';

		  // additional markers

		  if ($latlng) {

		    // remove white spaces

		    $latlng = str_replace(' ', '', $latlng);

		    // explode array

		    $latlng = explode(';', $latlng);

		    foreach ($latlng as $k=>$v) {
		      $markerID = $k + 1;
		      $markerID = 'marker'. $markerID;

		      // custom marker icon

		      $vEx = explode(',', $v);
		      if (isset($vEx[2])) {
		        $customIcon = $vEx[2];
		      } else {
		        $customIcon = $icon;
		      }

		      $inline_script .= 'var '. esc_attr($markerID) .' = new google.maps.Marker({';
		        $inline_script .= 'position : new google.maps.LatLng('. esc_attr($vEx[0]) .','. esc_attr($vEx[1]) .'),';
		        if ($customIcon) {
		          $inline_script .= 'icon: "'. esc_url($customIcon) .'",';
		        }
		        $inline_script .= 'map : map';
		      $inline_script .= '});';
		    }
		  }

		$inline_script .= '}';

		$inline_script .= 'jQuery(document).ready(function(){';
		  $inline_script .= 'mfn_google_maps_'. esc_attr($uid) .'();';
		$inline_script .= '});';

		wp_add_inline_script('google-maps', $inline_script);

		// output: HTML

		$output = '<div class="google-map-wrapper '. esc_attr($class) .'">';

			if ($title || $content) {
				$output .= '<div class="google-map-contact-wrapper style-'. esc_attr($style) .'">';
					$output .= '<div class="get_in_touch">';

						if ($title) {
							$output .= '<h3>'. wp_kses($title, mfn_allowed_html()) .'</h3>';
						}

						$output .= '<div class="get_in_touch_wrapper">';
							$output .= '<ul>';
								if ($content) {
									$output .= '<li class="address">';
										$output .= '<span class="icon"><i class="icon-location"></i></span>';
										$output .= '<span class="address_wrapper">'. do_shortcode($content) .'</span>';
									$output .= '</li>';
								}
								if ($telephone) {
									$output .= '<li class="phone">';
										$output .= '<span class="icon"><i class="icon-phone"></i></span>';
										$output .= '<p><a href="tel:'. esc_attr(str_replace(' ', '', $telephone)) .'">'. esc_html($telephone) .'</a></p>';
									$output .= '</li>';
								}
								if ($email) {
									$output .= '<li class="mail">';
										$output .= '<span class="icon"><i class="icon-mail"></i></span>';
										$output .= '<p><a href="mailto:'. esc_attr($email) .'">'. esc_html($email) .'</a></p>';
									$output .= '</li>';
								}
								if ($www) {
									$output .= '<li class="www">';
										$output .= '<span class="icon"><i class="icon-link"></i></span>';
										$output .= '<p><a target="_blank" href="http'. mfn_ssl() .'://'. esc_attr($www) .'">'. esc_html($www) .'</a></p>';
									$output .= '</li>';
								}
							$output .= '</ul>';
						$output .= '</div>';

					$output .= '</div>';
				$output .= '</div>';
			}

			$output .= '<div class="google-map" id="google-map-area-'. esc_attr($uid) .'" style="width:100%; height:'. intval($height, 10) .'px;">&nbsp;</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Tabs [tabs]
 */

$mfn_tabs_array = false;
$mfn_tabs_count = 0;

if (! function_exists('sc_tabs')) {
	function sc_tabs($attr, $content = null)
	{
		global $mfn_tabs_array, $mfn_tabs_count;

		extract(shortcode_atts(array(
			'title'		=> '',
			'tabs'		=> '',
			'type'		=> '',
			'padding'	=> '',
			'uid'			=> '',
		), $attr));

		do_shortcode( $content );

		// content builder

		if ($tabs) {
			$mfn_tabs_array = $tabs;
		}

		// uid

		if (! $uid) {
			$uid = 'tab-'. uniqid();
		}

		// padding

		if( is_numeric( $padding ) ){
			$padding .= 'px';
		}

		if ($padding || $padding === '0') {
			$style_escaped = 'style="padding:'. esc_attr($padding) .'"';
		} else {
			$style_escaped = false;
		}

		// output -----

		$output = '';

		if (is_array($mfn_tabs_array)) {

			if ($title) {
				$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
			}
			$output .= '<div class="jq-tabs tabs_wrapper tabs_'. esc_attr($type) .'">';

				$output .= '<ul>';

					$i = 1;
					$output_tabs = '';

					foreach ($mfn_tabs_array as $tab) {

						// check if content contains [tab]|[tabs]
						$pattern = '/\[tab(\]|s| )/';
						if (preg_match($pattern, $tab['content'])) {
							$tab['content'] = __('Tab shortcode does not work inside another tab.', 'betheme');
						}

						$output .= '<li><a href="#'. esc_attr($uid) .'-'. esc_attr($i) .'">'. wp_kses($tab['title'], mfn_allowed_html()) .'</a></li>';

						// This variable has been safely escaped above in this function
						$output_tabs .= '<div id="'. esc_attr($uid) .'-'. esc_attr($i) .'" '. $style_escaped .'>'. do_shortcode($tab['content']) .'</div>';

						$i++;
					}

				$output .= '</ul>';

				// titles

				$output .= $output_tabs;

			$output .= '</div>';

			$mfn_tabs_array = false;
			$mfn_tabs_count = 0;
		}

		return $output;
	}
}

/**
 * _Tab [tab] _private
 */

if (! function_exists('sc_tab')) {
	function sc_tab($attr, $content = null)
	{
		global $mfn_tabs_array, $mfn_tabs_count;

		extract(shortcode_atts(array(
			'title' => 'Tab title',
		), $attr));

		if (! is_array($mfn_tabs_array)) {
			$mfn_tabs_array = array();
		}

		$mfn_tabs_array[] = array(
			'title' 	=> $title,
			'content' => do_shortcode($content)
		);

		$mfn_tabs_count++;

		return true;
	}
}

/**
 * Accordion [accordion][accordion_item]...[/accordion]
 */

if (! function_exists('sc_accordion')) {
	function sc_accordion($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 	=> '',
			'tabs' 		=> '',
			'open1st' => '',
			'openall' => '',
			'openAll' => '',
			'style' 	=> 'accordion',
		), $attr));

		// class

		$class = '';

		if ($open1st) {
			$class .= ' open1st';
		}

		if ($openall || $openAll) {
			$class .= ' openAll';
		}

		if ($style == 'toggle') {
			$class .= ' toggle';
		}

		// output ------

		$output = '<div class="accordion">';

			if ($title) {
				$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
			}

			$output .= '<div class="mfn-acc accordion_wrapper '. esc_attr($class) .'">';

				if (is_array($tabs)) {
					// content builder
					foreach ($tabs as $tab) {
						$output .= '<div class="question">';
							$output .= '<div class="title"><i class="icon-plus acc-icon-plus"></i><i class="icon-minus acc-icon-minus"></i>'. wp_kses($tab['title'], mfn_allowed_html()) .'</div>';
							$output .= '<div class="answer">';
								$output .= do_shortcode($tab['content']);
							$output .= '</div>';
						$output .= '</div>'."\n";
					}
				} else {
					// shortcode
					$output .= do_shortcode($content);
				}

			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Accordion Item [accordion_item][/accordion_item]
 */

if (! function_exists('sc_accordion_item')) {
	function sc_accordion_item($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 	=> '',
		), $attr));

		$output = '<div class="question">';

			$output .= '<div class="title">';
				$output .= '<i class="icon-plus acc-icon-plus"></i>';
				$output .= '<i class="icon-minus acc-icon-minus"></i>';
				$output .= wp_kses($title, mfn_allowed_html());
			$output .= '</div>';

			$output .= '<div class="answer">';
				$output .= do_shortcode($content);
			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * FAQ [faq][faq_item]../[/faq]
 */

if (! function_exists('sc_faq')) {
	function sc_faq($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 	=> '',
			'tabs' 		=> '',
			'open1st' => '',
			'openall' => '',
			'openAll' => '',
			'style' 	=> '',
		), $attr));

		// class

		$class = '';

		if ($open1st) {
			$class .= ' open1st';
		}

		if ($openall || $openAll) {
			$class .= ' openAll';
		}

		if ($style == 'toggle') {
			$class .= ' toggle';
		}

		// output -----

		$output = '<div class="faq">';

			if ($title) {
				$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
			}
			$output .= '<div class="mfn-acc faq_wrapper '. esc_attr($class) .'">';

				if (is_array($tabs)) {

					// content builder
					$i = 0;

					foreach ($tabs as $tab) {
						$i++;

						$output .= '<div class="question">';

							$output .= '<div class="title">';
								$output .= '<span class="num">'. esc_html($i) .'</span>';
								$output .= '<i class="icon-plus acc-icon-plus"></i>';
								$output .= '<i class="icon-minus acc-icon-minus"></i>';
								$output .= wp_kses($tab['title'], mfn_allowed_html());
							$output .= '</div>';

							$output .= '<div class="answer">';
								$output .= do_shortcode($tab['content']);
							$output .= '</div>';

						$output .= '</div>'."\n";
					}

				} else {

					// shortcode
					$output .= do_shortcode($content);

				}

			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * FAQ Item [faq_item][/faq_item]
 */

if (! function_exists('sc_faq_item')) {
	function sc_faq_item($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' 	=> '',
			'number' 	=> '1',
		), $attr));

		// output

		$output = '<div class="question">';

			$output .= '<div class="title">';
				$output .= '<span class="num">'. esc_html($number) .'</span>';
				$output .= '<i class="icon-plus acc-icon-plus"></i>';
				$output .= '<i class="icon-minus acc-icon-minus"></i>';
				$output .= wp_kses($title, mfn_allowed_html());
			$output .= '</div>';

			$output .= '<div class="answer">';
				$output .= do_shortcode($content);
			$output .= '</div>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Progress Icons [progress_icons]
 */

if (! function_exists('sc_progress_icons')) {
	function sc_progress_icons($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon' 				=> 'icon-lamp',
			'image' 			=> '',
			'count' 			=> 5,
			'active' 			=> 0,
			'background' 	=> '',
		), $attr));

		// output -----

		$output = '<div class="progress_icons" data-active="'. esc_attr($active) .'" data-color="'. esc_attr($background) .'">';
			for ($i = 1; $i <= $count; $i++) {
				if ($image) {
					$output .= '<span class="progress_icon progress_image"><img src="'. esc_url($image) .'" alt="progress image"/></span>';
				} else {
					$output .= '<span class="progress_icon"><i class="'. esc_attr($icon) .'"></i></span>';
				}
			}
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Progress Bars [progress_bars][bar][/progress_bars]
 */

if (! function_exists('sc_progress_bars')) {
	function sc_progress_bars($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' => '',
			'tabs' => '',
		), $attr));

		// output

		$output = '<div class="progress_bars">';

			if ($title) {
				$output .= '<h4 class="title">'. wp_kses($title, mfn_allowed_html()) .'</h4>';
			}

			$output .= '<ul class="bars_list">';

				if( is_array( $tabs ) ) {

					// array

					foreach( $tabs as $tab ){
						$output .= sc_bar( $tab );
					}

				} else {

					// shortcode

					$output .= do_shortcode($content);

				}

			$output .= '</ul>';

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * _Bar [bar]
 */

if (! function_exists('sc_bar')) {
	function sc_bar($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'title' => '',
			'value' => 0,
			'size' => '',
			'color' => '',
		), $attr));

		// size

		if ($size) {
			$size_escaped = 'style="height:'. intval($size, 10) .'px"';
		} else {
			$size_escaped = false;
		}

		// color

		if( $color ){
			$color_escaped = 'background-color:'. esc_attr($color);
		} else {
			$color_escaped = false;
		}

		// output -----

		$output  = '<li>';

			$output .= '<h6>';
				$output .= wp_kses($title, mfn_allowed_html());
				$output .= '<span class="label">'. intval($value, 10) .'<em>%</em></span>';
			$output .= '</h6>';

			// This variable has been safely escaped above in this function
			$output .= '<div class="bar" '. $size_escaped .'>';
				$output .= '<span class="progress" style="width:'. intval($value,10) .'%;'. $color_escaped .'"></span>';
			$output .= '</div>';

		$output .= '</li>'."\n";

		return $output;
	}
}

/**
 * Timeline [timeline] [/timeline]
 */

if (! function_exists('sc_timeline')) {
	function sc_timeline($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'count' => '',
			'tabs' => '',
		), $attr));

		// output -----

		$output  = '<ul class="timeline_items">';

			if (is_array($tabs)) {

				// content builder
				foreach ($tabs as $tab) {
					$output .= '<li>';
						$output .= '<h3>'. wp_kses($tab['title'], mfn_allowed_html('caption')) .'</h3>';
						if ($tab['content']) {
							$output .= '<div class="desc">';
								$output .= do_shortcode($tab['content']);
							$output .= '</div>';
						}
					$output .= '</li>';
				}

			} else {

				// shortcode
				$output .= do_shortcode($content);

			}

		$output .= '</ul>'."\n";

		return $output;
	}
}

/**
 * Testimonials [testimonials]
 */

if (! function_exists('sc_testimonials')) {
	function sc_testimonials($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'category'		=> '',
			'orderby' 		=> 'menu_order',
			'order' 			=> 'ASC',
			'style' 			=> '',
			'hide_photos' => '',
		), $attr));

		// query args

		$args = array(
			'post_type' 			=> 'testimonial',
			'posts_per_page' 	=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' => 1,
		);

		if ($category) {
			$args['testimonial-types'] = $category;
		}

		// query

		$query_tm = new WP_Query();
		$query_tm->query($args);

		// class

		$class = $style;

		if ($hide_photos) {
			$class .= ' hide-photos';
		}

		// output -----

		$output = '';

		if ($query_tm->have_posts()) {
			$output .= '<div class="testimonials_slider '. esc_attr($class) .'">';

				// photos | pagination (style !== single-photo)
				if ($style != 'single-photo' && ! $hide_photos) {
					$output .= '<div class="slider_pager slider_images"></div>';
				}

					// testimonials | contant
					$output .= '<ul class="testimonials_slider_ul">';

						while ($query_tm->have_posts()) {
							$query_tm->the_post();

							$output .= '<li>';

								$output .= '<div class="single-photo-img">';
									if (has_post_thumbnail()) {
										$output .= get_the_post_thumbnail(null, 'testimonials', array('class'=>'scale-with-grid'));
									} else {
										$output .= '<img class="scale-with-grid" src="'. esc_url(get_theme_file_uri('/images/testimonials-placeholder.png')). '" alt="'. esc_attr(get_post_meta(get_the_ID(), 'mfn-post-author', true)) .'" />';
									}
								$output .= '</div>';

								$output .= '<div class="bq_wrapper">';
									$output .= '<blockquote>'. get_the_content() .'</blockquote>';
								$output .= '</div>';

								$output .= '<div class="hr_dots"><span></span><span></span><span></span></div>';

								$output .= '<div class="author">';
									$output .= '<h5>';
										if ($link = get_post_meta(get_the_ID(), 'mfn-post-link', true)) {
											$output .= '<a target="_blank" href="'. esc_url($link) .'">';
										}
											$output .= esc_html(get_post_meta(get_the_ID(), 'mfn-post-author', true));
										if ($link) {
											$output .= '</a>';
										}
									$output .= '</h5>';
									$output .= '<span class="company">'. get_post_meta(get_the_ID(), 'mfn-post-company', true) .'</span>';
								$output .= '</div>';

							$output .= '</li>';
						}

						wp_reset_query();

					$output .= '</ul>';

				// photos | pagination (style == single-photo)
				if ($style == 'single-photo') {
					$output .= '<div class="slider_pager slider_pagination"></div>';
				}

			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Testimonials List [testimonials_list]
 */

if (! function_exists('sc_testimonials_list')) {
	function sc_testimonials_list($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'category' 	=> '',
			'orderby' 	=> 'menu_order',
			'order' 		=> 'ASC',
			'style' 		=> '',	// [default], quote
		), $attr));

		// query args

		$args = array(
			'post_type' 			=> 'testimonial',
			'posts_per_page' 	=> -1,
			'orderby' 				=> $orderby,
			'order' 					=> $order,
			'ignore_sticky_posts' =>1,
		);

		if ($category) {
			$args['testimonial-types'] = $category;
		}

		// query

		$query_tm = new WP_Query();
		$query_tm->query($args);

		// class

		if ($style) {
			$class = 'style_'. $style;
		} else {
			$class = '';
		}

		// output -----

		$output = '';

		if ($query_tm->have_posts()) {
			$output .= '<div class="testimonials_list '. esc_attr($class) .'">';

				while ($query_tm->have_posts()) {
					$query_tm->the_post();

					// classes
					$class = '';
					if (! has_post_thumbnail()) {
						$class .= 'no-img';
					}

					$output .= '<div class="item '. esc_attr($class) .'">';

						if (has_post_thumbnail()) {
							$output .= '<div class="photo">';
								$output .= '<div class="image_frame no_link scale-with-grid has_border">';
									$output .= '<div class="image_wrapper">';
										$output .= get_the_post_thumbnail(null, 'full', array('class'=>'scale-with-grid' ));
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						}

						$output .= '<div class="desc">';

							if ($style == 'quote') {
								$output .= '<div class="blockquote clearfix">';
									$output .= '<blockquote>'. get_the_content() .'</blockquote>';
								$output .= '</div>';
								$output .= '<hr class="hr_color" />';
							}

							$output .= '<h4>';
								if ($link = get_post_meta(get_the_ID(), 'mfn-post-link', true)) {
									$output .= '<a target="_blank" href="'. esc_url($link) .'">';
								}
									$output .= get_post_meta(get_the_ID(), 'mfn-post-author', true);
								if ($link) {
									$output .= '</a>';
								}
							$output .= '</h4>';

							$output .= '<p class="subtitle">'. esc_html(get_post_meta(get_the_ID(), 'mfn-post-company', true)) .'</p>';

							if ($style != 'quote') {
								$output .= '<hr class="hr_color" />';
								$output .= '<div class="blockquote">';
									$output .= '<blockquote>'. get_the_content() .'</blockquote>';
								$output .= '</div>';
							}

						$output .= '</div>';

					$output .= '</div>'."\n";
				}
				wp_reset_query();

			$output .= '</div>'."\n";
		}

		return $output;
	}
}

/**
 * Vimeo [video]
 */

if (! function_exists('sc_video')) {
	function sc_video($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'video' 			=> '',
			'parameters' 	=> '',
			'mp4' 				=> '',
			'ogv'	 				=> '',
			'placeholder' => '',
			'html5_parameters' => '',
			'width' 			=> '700',
			'height' 			=> '400',
		), $attr));

		// parameters

		if ($parameters) {
			$parameters = '&'. $parameters;
		}

		// HTML5 parameters

		$html5_default = array(
			'autoplay'	=> 'autoplay="1"',
			'controls'	=> 'controls="1"',
			'loop'			=> 'loop="1"',
			'muted'			=> 'muted="1"',
		);

		if ($html5_parameters) {
			$html5_parameters = explode(';', $html5_parameters);
			if (! $html5_parameters[0]) {
				$html5_default['autoplay'] = false;
			}
			if (! $html5_parameters[1]) {
				$html5_default['controls'] = false;
			}
			if (! $html5_parameters[2]) {
				$html5_default['loop'] = false;
			}
			if (! $html5_parameters[3]) {
				$html5_default['muted'] = false;
			}
		}

		// no need to escape, no user data
		$html5_escaped = implode(' ', $html5_default);

		// class

		$class = ($video) ? 'iframe' : '' ;

		if ($width && $height) {
			$class .= ' has-wh';
		} else {
			$class .= ' auto-wh';
		}

		$output  = '<div class="content_video '. esc_attr($class) .'">';

			if ($video) {

				// Embed
				if (is_numeric($video)) {
					// Vimeo
					$output .= '<iframe class="scale-with-grid" width="'. esc_attr($width) .'" height="'. esc_attr($height) .'" src="http'. mfn_ssl() .'://player.vimeo.com/video/'. esc_attr($video) .'?wmode=opaque'. esc_attr($parameters) .'" allowFullScreen></iframe>'."\n";
				} else {
					// YouTube
					$output .= '<iframe class="scale-with-grid" width="'. esc_attr($width) .'" height="'. esc_attr($height) .'" src="http'. mfn_ssl() .'://www.youtube.com/embed/'. esc_attr($video) .'?wmode=opaque'. esc_attr($parameters) .'" allowfullscreen></iframe>'."\n";
				}

			} elseif ($mp4) {

				// HTML5
				$output .= '<div class="section_video">';

					$output .= '<div class="mask"></div>';
					$poster = ($placeholder) ? $placeholder : false;

					// This variable has been safely escaped above in this function
					$output .= '<video poster="'. esc_url($poster) .'" '. $html5_escaped .' style="max-width:100%;">';

						$output .= '<source type="video/mp4" src="'. esc_url($mp4) .'" />';
						if ($ogv) {
							$output .= '<source type="video/ogg" src="'. esc_url($ogv) .'" />';
						}

					$output .= '</video>';

				$output .= '</div>';
			}

		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * _Item [item]
 * [feature_list][item][/feature_list]
 */

if (! function_exists('sc_item')) {
	function sc_item($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon'		=> 'icon-picture',
			'title'		=> '',
			'link'		=> '',
			'target'	=> '',
			'animate'	=> '',
		), $attr));

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// elementor icon

		if( is_array($icon) ){
			$icon = $icon['value'];
		}

		// output -----

		$output  = '<li>';

			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}
				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					$output .= '<span class="icon">';
						$output .= '<i class="'. esc_attr($icon) .'"></i>';
					$output .= '</span>';
					$output .= '<p>'. wp_kses($title, mfn_allowed_html()) .'</p>';

				if ($link) {
					$output .= '</a>';
				}
			if ($animate) {
				$output .= '</div>';
			}

		$output .= '</li>'."\n";

		return $output;
	}
}

/**
 * Feature List [feature_list]				[feature_list][item][/feature_list]
 */

if (! function_exists('sc_feature_list')) {
	function sc_feature_list($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'tabs' => '',
			'columns'	=> 4,
		), $attr));

		// output -----

		$output = '<div class="feature_list" data-col="'. esc_attr($columns) .'">';
			$output .= '<ul>';

			if( is_array( $tabs ) ) {

				// array

				foreach( $tabs as $tab ){
					$output .= sc_item( $tab );
				}

			} else {

				// shortcode

				$output .= do_shortcode($content);

			}

			$output .= '</ul>';
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * List [list][/list]
 */

if (! function_exists('sc_list')) {
	function sc_list($attr, $content = null)
	{
		extract(shortcode_atts(array(
			'icon'		=> 'icon-picture',
			'image'		=> '',
			'title'		=> '',
			'link'		=> '',
			'target'	=> '',
			'style'		=> 1,
			'animate'	=> '',
		), $attr));

		// image | visual composer fix

		$image = mfn_vc_image($image);

		// target

		if ($target) {
			$target_escaped = 'target="_blank"';
		} else {
			$target_escaped = false;
		}

		// output -----

		$output = '<div class="list_item lists_'. esc_attr($style) .' clearfix">';
			if ($animate) {
				$output .= '<div class="animate" data-anim-type="'. esc_attr($animate) .'">';
			}
				if ($link) {
					// This variable has been safely escaped above in this function
					$output .= '<a href="'. esc_url($link) .'" '. $target_escaped .'>';
				}

					if ($style == 4) {
						$output .= '<div class="circle">'. wp_kses($title, mfn_allowed_html()) .'</div>';
					} elseif ($image) {
						$output .= '<div class="list_left list_image">';
							$output .= '<img src="'. esc_url($image) .'" class="scale-with-grid" alt="'. esc_attr(mfn_get_attachment_data($image, 'alt')) .'" width="'. esc_attr(mfn_get_attachment_data($image, 'width')) .'" height="'. esc_attr(mfn_get_attachment_data($image, 'height')) .'"/>';
						$output .= '</div>';
					} else {
						$output .= '<div class="list_left list_icon">';
							$output .= '<i class="'. esc_attr($icon) .'"></i>';
						$output .= '</div>';
					}

					$output .= '<div class="list_right">';
						if ($title && $style != 4) {
							$output .= '<h4>'. wp_kses($title, mfn_allowed_html()) .'</h4>';
						}
						$output .= '<div class="desc">'. do_shortcode($content) .'</div>';
					$output .= '</div>';

				if ($link) {
					$output .= '</a>';
				}
			if ($animate) {
				$output .= '</div>';
			}
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * Gallery [gallery]
 */

if (! function_exists('sc_gallery')) {
	function sc_gallery($attr)
	{
		$post = get_post();

		static $instance = 0;
		$instance++;

		if (! empty($attr['ids'])) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if (empty($attr['orderby'])) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if (isset($attr['orderby'])) {
			$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
			if (! $attr['orderby']) {
				unset($attr['orderby']);
			}
		}

		$html5 = current_theme_supports('html5', 'gallery');
		$atts = shortcode_atts(array(
			'order'				=> 'ASC',
			'orderby'    	=> 'menu_order ID',
			'id'         	=> $post  ? $post->ID : 0,
			'itemtag'    	=> $html5 ? 'figure'     : 'dl',
			'icontag'    	=> $html5 ? 'div'        : 'dt',
			'captiontag' 	=> $html5 ? 'figcaption' : 'dd',
			'columns'    	=> 3,
			'size'       	=> 'thumbnail',
			'include'    	=> '',
			'exclude'    	=> '',
			'link'       	=> '',

		// mfn custom ---------------------------
			'style'			=> '',	// [default], flat, fancy, masonry
			'greyscale'		=> '',

		), $attr, 'gallery');


		// Muffin | Custom Classes -----------------
		$class = $atts['link'];
		if ($atts['style']) {
			$class .= ' '. $atts['style'];
		}
		if ($atts['greyscale']) {
			$class .= ' greyscale';
		}


		$id = intval($atts['id'], 10);
		if ('RAND' == $atts['order']) {
			$atts['orderby'] = 'none';
		}

		if (! empty($atts['include'])) {
			$_attachments = get_posts(array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ));

			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif (! empty($atts['exclude'])) {
			$attachments = get_children(array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ));
		} else {
			$attachments = get_children(array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ));
		}

		if (empty($attachments)) {
			return '';
		}

		if (is_feed()) {
			$output = "\n";
			foreach ($attachments as $att_id => $attachment) {
				$output .= wp_get_attachment_link($att_id, $atts['size'], true) . "\n";
			}
			return $output;
		}

		$itemtag = tag_escape($atts['itemtag']);
		$captiontag = tag_escape($atts['captiontag']);
		$icontag = tag_escape($atts['icontag']);
		$valid_tags = wp_kses_allowed_html('post');
		if (! isset($valid_tags[ $itemtag ])) {
			$itemtag = 'dl';
		}
		if (! isset($valid_tags[ $captiontag ])) {
			$captiontag = 'dd';
		}
		if (! isset($valid_tags[ $icontag ])) {
			$icontag = 'dt';
		}

		$columns = intval($atts['columns'], 10);

		$itemwidth = $columns > 0 ? (ceil(100/$columns*100)/100 - 0.01) : 100;

		$float = is_rtl() ? 'right' : 'left';

		$selector = "sc_gallery-{$instance}";

		$gallery_style = '';

		if (apply_filters('use_default_gallery_style', ! $html5)) {
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
				/* see sc_gallery() in functions/theme-shortcodes.php */
			</style>\n\t\t";
		}

		$size_class = sanitize_html_class($atts['size']);
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} {$class}'>";

		$output = apply_filters('gallery_style', $gallery_style . $gallery_div);

		$i = 0;
		foreach ($attachments as $id => $attachment) {
			if (! empty($atts['link']) && 'file' === $atts['link']) {
				$image_output = wp_get_attachment_link($id, $atts['size'], false, false);
			} elseif (! empty($atts['link']) && 'none' === $atts['link']) {
				$image_output = wp_get_attachment_image($id, $atts['size'], false);
			} else {
				$image_output = wp_get_attachment_link($id, $atts['size'], true, false);
			}
			$image_meta  = wp_get_attachment_metadata($id);

			$orientation = '';
			if (isset($image_meta['height'], $image_meta['width'])) {
				$orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
			}
			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
				<{$icontag} class='gallery-icon {$orientation}'>
					$image_output
				</{$icontag}>";
			if ($captiontag && trim($attachment->post_excerpt)) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption'>
					" . wptexturize($attachment->post_excerpt) . "
					</{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			if (! $html5 && $columns > 0 && ++$i % $columns == 0) {
				$output .= '<br style="clear: both" />';
			}
		}

		if (! $html5 && $columns > 0 && $i % $columns !== 0) {
			$output .= "
				<br style='clear: both' />";
		}

		$output .= "
			</div>\n";

		return $output;
	}
}

/**
 * Raw [raw][/raw]
 * WordPress 4.8 | Text Widget - autop
 */

if (! function_exists('sc_raw')) {
	function sc_raw($content)
	{
		$new_content = '';
		$pattern_full = '{(\[raw\].*?\[/raw\])}is';
		$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
		$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

		foreach ($pieces as $piece) {
			if (preg_match($pattern_contents, $piece, $matches)) {
				$new_content .= $matches[1];
			} else {
				$new_content .= wptexturize(wpautop($piece));
			}
		}

		return $new_content;
	}
}

if (! function_exists('mfn_widget_text_content')) {
	function mfn_widget_text_content(){
		add_filter('widget_text_content', 'sc_raw', 99);
	}
}

/**
 * Shortcodes
 */

if (! function_exists('mfn_shortcodes')) {
	function mfn_shortcodes(){

		// columns

		add_shortcode('one', 'sc_one');
		add_shortcode('one_second', 'sc_one_second');
		add_shortcode('one_third', 'sc_one_third');
		add_shortcode('two_third', 'sc_two_third');

		add_shortcode('one_fourth', 'sc_one_fourth');
		add_shortcode('two_fourth', 'sc_one_second');
		add_shortcode('three_fourth', 'sc_three_fourth');

		add_shortcode('one_fifth', 'sc_one_fifth');
		add_shortcode('two_fifth', 'sc_two_fifth');
		add_shortcode('three_fifth', 'sc_three_fifth');
		add_shortcode('four_fifth', 'sc_four_fifth');

		add_shortcode('one_sixth', 'sc_one_sixth');
		add_shortcode('two_sixth', 'sc_one_third');
		add_shortcode('three_sixth', 'sc_one_second');
		add_shortcode('four_sixth', 'sc_two_third');
		add_shortcode('five_sixth', 'sc_five_sixth');

		// Content, inline shortcodes

		add_shortcode('alert', 'sc_alert');
		add_shortcode('blockquote', 'sc_blockquote');
		add_shortcode('button', 'sc_button');
		add_shortcode('code', 'sc_code');
		add_shortcode('content_link', 'sc_content_link');
		add_shortcode('counter_inline', 'sc_counter_inline');
		add_shortcode('divider', 'sc_divider');
		add_shortcode('dropcap', 'sc_dropcap');
		add_shortcode('fancy_link', 'sc_fancy_link');
		add_shortcode('google_font', 'sc_google_font');
		add_shortcode('heading', 'sc_heading');
		add_shortcode('highlight', 'sc_highlight');
		add_shortcode('hr', 'sc_divider'); // do NOT change, alias for [divider] shortcode
		add_shortcode('icon', 'sc_icon');
		add_shortcode('icon_bar', 'sc_icon_bar');
		add_shortcode('icon_block', 'sc_icon_block');
		add_shortcode('idea', 'sc_idea');
		add_shortcode('image', 'sc_image');
		add_shortcode('popup', 'sc_popup');
		add_shortcode('progress_icons', 'sc_progress_icons');
		add_shortcode('share_box', 'sc_share_box');
		add_shortcode('tooltip', 'sc_tooltip');
		add_shortcode('tooltip_image', 'sc_tooltip_image');
		add_shortcode('video_embed', 'sc_video'); // WordPress has default [video] shortcode

		// builder

		add_shortcode('accordion', 'sc_accordion');
		add_shortcode('accordion_item', 'sc_accordion_item');
		add_shortcode('article_box', 'sc_article_box');
		add_shortcode('before_after', 'sc_before_after');
		add_shortcode('blog', 'sc_blog');
		add_shortcode('blog_news', 'sc_blog_news');
		add_shortcode('blog_slider', 'sc_blog_slider');
		add_shortcode('blog_teaser', 'sc_blog_teaser');
		add_shortcode('call_to_action', 'sc_call_to_action');
		add_shortcode('chart', 'sc_chart');
		add_shortcode('clients', 'sc_clients');
		add_shortcode('clients_slider', 'sc_clients_slider');
		add_shortcode('contact_box', 'sc_contact_box');
		add_shortcode('countdown', 'sc_countdown');
		add_shortcode('counter', 'sc_counter');
		add_shortcode('fancy_divider', 'sc_fancy_divider');
		add_shortcode('fancy_heading', 'sc_fancy_heading');
		add_shortcode('faq', 'sc_faq');
		add_shortcode('faq_item', 'sc_faq_item');
		add_shortcode('feature_box', 'sc_feature_box');
		add_shortcode('feature_list', 'sc_feature_list');
		add_shortcode('flat_box', 'sc_flat_box');
		add_shortcode('helper', 'sc_helper');
		add_shortcode('hover_box', 'sc_hover_box');
		add_shortcode('hover_color', 'sc_hover_color');
		add_shortcode('how_it_works', 'sc_how_it_works');
		add_shortcode('icon_box', 'sc_icon_box');
		add_shortcode('info_box', 'sc_info_box');
		add_shortcode('list', 'sc_list');
		add_shortcode('map_basic', 'sc_map_basic');
		add_shortcode('map', 'sc_map');
		add_shortcode('offer', 'sc_offer');
		add_shortcode('offer_thumb', 'sc_offer_thumb');
		add_shortcode('opening_hours', 'sc_opening_hours');
		add_shortcode('our_team', 'sc_our_team');
		add_shortcode('our_team_list', 'sc_our_team_list');
		add_shortcode('photo_box', 'sc_photo_box');
		add_shortcode('portfolio', 'sc_portfolio');
		add_shortcode('portfolio_grid', 'sc_portfolio_grid');
		add_shortcode('portfolio_photo', 'sc_portfolio_photo');
		add_shortcode('portfolio_slider', 'sc_portfolio_slider');
		add_shortcode('pricing_item', 'sc_pricing_item');
		add_shortcode('progress_bars', 'sc_progress_bars');
		add_shortcode('promo_box', 'sc_promo_box');
		add_shortcode('quick_fact', 'sc_quick_fact');
		add_shortcode('shop_slider', 'sc_shop_slider');
		add_shortcode('slider', 'sc_slider');
		add_shortcode('sliding_box', 'sc_sliding_box');
		add_shortcode('story_box', 'sc_story_box');
		add_shortcode('tabs', 'sc_tabs');
		add_shortcode('tab', 'sc_tab');
		add_shortcode('testimonials', 'sc_testimonials');
		add_shortcode('testimonials_list', 'sc_testimonials_list');
		add_shortcode('trailer_box', 'sc_trailer_box');
		add_shortcode('zoom_box', 'sc_zoom_box');

		// private

		add_shortcode('bar', 'sc_bar');
		add_shortcode('item', 'sc_item');

		// gallery

		if (! mfn_opts_get('sc-gallery-disable')) {
			remove_shortcode('gallery');
			add_shortcode('gallery', 'sc_gallery');
		}

	}
}

add_action('init', 'mfn_widget_text_content');
add_action('init', 'mfn_shortcodes');
