<?php
/**
 * Theme functions
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Allowed HTML elements for wp_kses
 */

if (! function_exists('mfn_allowed_html')) {
	function mfn_allowed_html($type = false)
	{
		switch ($type) {

			case 'button':

				$allowed_html = array(
					'i' => array(
						'class' => array(),
					),
					'span' => array(),
					'strong' => array(),
				);
				break;

			case 'caption':

				$allowed_html = array(
					'a' => array(
						'href' => array(),
						'target' => array(),
					),
					'b' => array(),
					'br' => array(),
					'em' => array(),
					'span' => array(),
					'strong' => array(),
					'u' => array(),
				);
				break;

			case 'desc':

				$allowed_html = array(
					'a' => array(
						'href' => array(),
						'target' => array(),
					),
					'b' => array(),
					'br' => array(),
					'em' => array(),
					'i' => array(
						'class'  => array(),
					),
					'li' => array(),
					'span' => array(),
					'strong' => array(),
					'u' => array(),
					'ul' => array(),
				);
				break;

			default:

				$allowed_html = array(
					'b' => array(),
					'br' => array(),
					'em' => array(),
					'i' => array(
						'class'  => array(),
					),
					'span' => array(
						'id' => array(),
						'class' => array(),
						'style' => array(),
					),
					'strong' => array(),
					'u' => array(),
				);

		}

		return $allowed_html;
	}
}

/**
 * Image Size | Add
 * TIP: add_image_size ( string $name, int $width, int $height, bool|array $crop = false )
 */

if (! function_exists('mfn_add_image_size')) {
	function mfn_add_image_size()
	{
		// featured image in wp-admin
		set_post_thumbnail_size(260, 146, false);

		// thumbnails in wp-admin lists
		add_image_size('50x50', 50, 50, false);

		// clients
		add_image_size('clients-slider', 150, 75, false);

		// slider (builder items)
		add_image_size('slider-content', 1630, 860, true);

		// testimonials
		add_image_size('testimonials', 85, 85, true);

		// sticky navigation * widget: recent posts
		add_image_size('blog-navi', 80, 80, true);

		// portfolio | style: masonry flat
		add_image_size('portfolio-mf', 1280, 1000, true);
		add_image_size('portfolio-mf-w', 1280, 500, true);	/* Wide */
		add_image_size('portfolio-mf-t', 768, 1200, true);	/* Tall	*/

		// portfolio | style: list
		add_image_size('portfolio-list', 1920, 750, true);

		// blog & portfolio: dynamic sizes
		$archivesW = mfn_opts_get('featured-blog-portfolio-width', 960);
		$archivesH = mfn_opts_get('featured-blog-portfolio-height', 750);
		$archivesC = mfn_opts_get('featured-blog-portfolio-crop', 'crop');
		$archivesC = ($archivesC == 'resize') ? false : true;

		add_image_size('blog-portfolio', $archivesW, $archivesH, $archivesC);

		$singleW = mfn_opts_get('featured-single-width', 1200);
		$singleH = mfn_opts_get('featured-single-height', 480);
		$singleC = mfn_opts_get('featured-single-crop', 'crop');
		$singleC = ($singleC == 'resize') ? false : true;

		add_image_size('blog-single', $singleW, $singleH, $singleC);
	}
}
add_action('after_setup_theme', 'mfn_add_image_size', 11);

/**
 * Image size | Get size dimensions
 */

if (! function_exists('mfn_get_image_sizes')) {
	function mfn_get_image_sizes($size, $string = false)
	{
		$sizes = array();

		$sizes['width'] = get_option("{$size}_size_w");
		$sizes['height'] = get_option("{$size}_size_h");
		$sizes['crop'] = (bool) get_option("{$size}_crop");

		if ($string) {
			$crop = $sizes['crop'] ? ', crop' : '';
			return 'max width: '. esc_attr($sizes['width']) .', max height: '. esc_attr($sizes['height']) . esc_attr($crop);
		}

		return $sizes;
	}
}

/**
 * Excerpt | Lenght
 */

if (! function_exists('mfn_excerpt_length')) {
	function mfn_excerpt_length($length)
	{
		return esc_attr(mfn_opts_get('excerpt-length', 26));
	}
}
add_filter('excerpt_length', 'mfn_excerpt_length', 999);

/**
 * Excerpt | Wrap [...] into <span>
 */

if (! function_exists('mfn_trim_excerpt')) {
	function mfn_trim_excerpt($text)
	{
		return '<span class="excerpt-hellip"> […]</span>';
	}
}
add_filter('excerpt_more', 'mfn_trim_excerpt');

/**
 * Excerpt | for Pages
 */

if (! function_exists('mfn_add_excerpts_to_pages')) {
	function mfn_add_excerpts_to_pages()
	{
		add_post_type_support('page', 'excerpt');
	}
}
add_action('init', 'mfn_add_excerpts_to_pages');

/**
 * Slug | Generate
 */

if (! function_exists('mfn_slug')) {
	function mfn_slug($string = false)
	{
		return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
	}
}

/**
 * Blog Page | Order
 */

if (! function_exists('mfn_blog_order')) {
	function mfn_blog_order($query)
	{
		if ($query->is_main_query()) {
			if (is_home() || is_category() || is_tag()) {
				$orderby = mfn_opts_get('blog-orderby', 'date');
				$order = mfn_opts_get('blog-order', 'DESC');

				if ($orderby == 'date' && $order == 'DESC') {
					return true;
				}

				$query->set('orderby', $orderby);
				$query->set('order', $order);
			}
		}

		return $query;
	}
}
add_action('pre_get_posts', 'mfn_blog_order');

/**
 * Blog Page | Exclude category
 */

if (! function_exists('mfn_get_excluded_categories')) {
	function mfn_get_excluded_categories()
	{
		$categories = array();

		if ($exclude = mfn_opts_get('exclude-category')) {
			$exclude = str_replace(' ', '', $exclude);
			$exclude = explode(',', $exclude);

			if (is_array($exclude)) {
				$categories = $exclude;
			}
		}

		return $categories;
	}
}

if (! function_exists('mfn_exclude_category')) {
	function mfn_exclude_category($query)
	{
		if (is_home() && $query->is_main_query()) {
			$exclude_ids = array();

			if ($exclude = mfn_get_excluded_categories()) {
				foreach ($exclude as $slug) {
					$category = get_category_by_slug($slug);
					$exclude_ids[] = $category->term_id * -1;
				}
			}

			$exclude_ids = implode(',', $exclude_ids);

			$query->set('cat', $exclude_ids);
		}

		return $query;
	}
}
add_filter('pre_get_posts', 'mfn_exclude_category');

/**
 * SSL | Compatibility
 */

if (! function_exists('mfn_ssl')) {
	function mfn_ssl($echo = false)
	{
		$ssl = '';

		if (is_ssl()) {
			$ssl = 's';
		}

		if ($echo) {
			echo esc_attr($ssl);
		}

		return $ssl;
	}
}

/**
 * SSL | Attachments
 */

if (! function_exists('mfn_ssl_attachments')) {
	function mfn_ssl_attachments($url)
	{
		if (is_ssl()) {
			return str_replace('http://', 'https://', $url);
		}
		return $url;
	}
}
add_filter('wp_get_attachment_url', 'mfn_ssl_attachments');

/**
 * White Label | Admin Body Class
 */

if (! function_exists('mfn_white_label_class')) {
	function mfn_white_label_class($classes)
	{
		if (WHITE_LABEL) {
			$classes .= ' white-label ';
		}
		return $classes;
	}
}
add_filter('admin_body_class', 'mfn_white_label_class');

/**
 * Get Real Post ID
 */

if (! function_exists('mfn_ID')) {
	function mfn_ID()
	{
		global $post;

		// 404

		if( is_404() || is_search() ){
			return false;
		}

		// Shop

		if( function_exists('is_woocommerce') && is_woocommerce() ){
			return wc_get_page_id('shop');
		}

		// Taxonomy

		if( is_tax() ){
			return mfn_opts_get('portfolio-page');
		}

		// Archive

		if( in_array(get_post_type(), array( 'post', 'tribe_events' )) && ! is_singular() ){
			return mfn_get_blog_ID();
		}

		return get_the_ID();

	}
}

/**
 * Get blog page ID
 */

if (! function_exists('mfn_get_blog_ID')) {
	function mfn_get_blog_ID(){

		$id = get_option('page_for_posts');

		if( ! $id ){
			$id = mfn_opts_get('blog-page');
		}

		return $id;
	}
}

/**
 * Get Layout ID
 */

if (! function_exists('mfn_layout_ID')) {
	function mfn_layout_ID()
	{
		$layoutID = false;

		if (mfn_ID()) {

			if (is_single() && get_post_type() == 'post') {

				// Theme Options | Single Post
				$layoutID = mfn_opts_get('blog-single-layout');

			} elseif (is_single() && get_post_type() == 'portfolio') {

				if (get_post_meta(mfn_ID(), 'mfn-post-custom-layout', true)) {

					// Page Options | Single Portfolio
					$layoutID = get_post_meta(mfn_ID(), 'mfn-post-custom-layout', true);

				} else {

					// Theme Options | Single Portfolio
					$layoutID = mfn_opts_get('portfolio-single-layout');

				}

			} else {

				// Page Options | Page
				$layoutID = get_post_meta(mfn_ID(), 'mfn-post-custom-layout', true);

			}

		}

		return $layoutID;
	}
}

/**
 * Slider | Isset
 */

if (! function_exists('mfn_slider_isset')) {
	function mfn_slider_isset($id = false)
	{
		$slider = false;

		// global slider shortcode

		if (is_page() && mfn_opts_get('slider-shortcode')) {
			return 'global';
		}

		if ($id || is_home() || is_category() || is_tax() || get_post_type() == 'page' || (get_post_type(mfn_ID()) == 'portfolio' && get_post_meta(mfn_ID(), 'mfn-post-slider-header', true))) {

			if (! $id) {
				$id = mfn_ID();
			} // do NOT move it before IF

			if (get_post_meta($id, 'mfn-post-slider', true)) {

				// Revolution Slider
				$slider = 'rev';

			} elseif (get_post_meta($id, 'mfn-post-slider-layer', true)) {

				// Layer Slider
				$slider = 'layer';

			} elseif (get_post_meta($id, 'mfn-post-slider-shortcode', true)) {

				// Custom Slider
				$slider = 'custom';

			}
		}

		return $slider;
	}
}

/**
 * Slider | Get
 */

if (! function_exists('mfn_slider')) {
	function mfn_slider($id = false)
	{
		$slider = false;
		$slider_type = mfn_slider_isset($id);

		if (! $id) {
			$id = mfn_ID();
		} // do NOT move it before IF

		switch ($slider_type) {

			case 'global':
				$slider = '<div class="mfn-main-slider" id="mfn-global-slider">';
					$slider .= do_shortcode(mfn_opts_get('slider-shortcode'));
				$slider .= '</div>';
				break;

			case 'rev':
				$slider = '<div class="mfn-main-slider mfn-rev-slider">';
					$slider .= do_shortcode('[rev_slider '. get_post_meta($id, 'mfn-post-slider', true) .']');
				$slider .= '</div>';
				break;

			case 'layer':
				$slider = '<div class="mfn-main-slider mfn-layer-slider">';
					$slider .= do_shortcode('[layerslider id="'. get_post_meta($id, 'mfn-post-slider-layer', true) .'"]');
				$slider .= '</div>';
				break;

			case 'custom':
				$slider = '<div class="mfn-main-slider" id="mfn-custom-slider">';
					$slider .= do_shortcode(get_post_meta($id, 'mfn-post-slider-shortcode', true));
				$slider .= '</div>';
				break;

		}

		return $slider;
	}
}

/**
 * Share
 */

if (! function_exists('mfn_share')) {
	function mfn_share($container = false)
	{
		$type = false;
		$class = false;

		if (! mfn_opts_get('share')) {
			return false;
		}

		$style = mfn_opts_get('share-style', 'classic');

		// type

		if (($container == 'header') && ($style == 'classic')) {
			$type = 'classic';
		}

		if ($container == 'intro') {
			if ($style == 'simple') {
				$type = 'simple';
			} else {
				$type = 'classic';
			}
		}

		if (($container == 'footer') && ($style == 'simple')) {
			$type = 'simple';
		}

		if ($container == 'item') {
			$type = $style;
			$class = 'share_item';
		}

		// output

		$output = '';

		if ($type == 'simple') {

			// simple

			$translate['share'] = mfn_opts_get('translate') ? mfn_opts_get('translate-share', 'Share') : __('Share', 'betheme');

			$output .= '<div class="share-simple-wrapper '. esc_attr($class) .'">';

				$output .= '<span class="share-label">'. esc_html($translate['share']) .'</span>';

				$output .= '<div class="icons">';
					$output .= '<a target="_blank" class="facebook" href="https://www.facebook.com/sharer/sharer.php?u='. urlencode(esc_url(get_permalink())) .'"><i class="icon-facebook"></i></a>';
					$output .= '<a target="_blank" class="twitter" href="https://twitter.com/intent/tweet?text='. urlencode( esc_attr(wp_get_document_title()) .'. '. esc_url(get_permalink()) ) .'"><i class="icon-twitter"></i></a>';
					$output .= '<a target="_blank" class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url='. urlencode(esc_url(get_permalink())) .'"><i class="icon-linkedin"></i></a>';
					$output .= '<a target="_blank" class="pinterest" href="https://pinterest.com/pin/find/?url='. urlencode(esc_url(get_permalink())) .'"><i class="icon-pinterest"></i></a>';
				$output .= '</div>';

				if ($container != 'item') {
					$output .= '<div class="button-love">'. mfn_love() .'</div>';
				}

			$output .= '</div>';

		} elseif ($type == 'classic') {

			// classic

			wp_enqueue_script('share-this', 'https://ws.sharethis.com/button/buttons.js', false, null, true);
			$share_this_inline = 'stLight.options({publisher:"1390eb48-c3c3-409a-903a-ca202d50de91",doNotHash:false,doNotCopy:false,hashAddressBar:false});';
			wp_add_inline_script('share-this', $share_this_inline);

			$output .= '<div class="share_wrapper '. esc_attr($class) .'">';

				$output .= '<span class="st_facebook_vcount"></span>';
				$output .= '<span class="st_twitter_vcount"></span>';
				$output .= '<span class="st_pinterest_vcount"></span>';

			$output .= '</div>';
		}

		return $output;
	}
}

/**
 * WP Mobile Detect | Quick FIX: parallax on mobile
 */

if (! function_exists('mfn_is_mobile')) {
	function mfn_is_mobile()
	{
		$mobile = wp_is_mobile();

		if (mfn_opts_get('responsive-parallax')) {
			$mobile = false;
		}

		return $mobile;
	}
}

/**
 * User OS
 */

if (! function_exists('mfn_user_os')) {
	function mfn_user_os()
	{
		$os = false;
		$user_agent = $_SERVER['HTTP_USER_AGENT']; // context is safe and necessary

		if (strpos($user_agent, 'iPad;') || strpos($user_agent, 'iPhone;')) {
			$os = ' ios';
		}

		return $os;
	}
}

/**
 * User Agent | For: Prallax - Safari detect & future use
 */

if (! function_exists('mfn_user_agent')) {
	function mfn_user_agent()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT']; // context is safe and necessary

		if (stripos($user_agent, 'Chrome/') !== false) {
			$user_agent = 'chrome';
		} elseif ((stripos($user_agent, 'Safari/') !== false) && (stripos($user_agent, 'Mobile/') !== false)) {
			$user_agent = 'safari mobile';
		} elseif (stripos($user_agent, 'Safari/') !== false) {
			$user_agent = 'safari';
		} else {

			// for future use
			$user_agent = false;
		}

		return $user_agent;
	}
}

/**
 * Paralllax | Plugin
 */

if (! function_exists('mfn_parallax_plugin')) {
	function mfn_parallax_plugin()
	{
		$parallax = mfn_opts_get('parallax');

		if ($parallax == 'translate3d no-safari') {
			if (mfn_user_agent() == 'safari') {
				$parallax = 'enllax';
			} else {
				$parallax = 'translate3d';
			}
		}

		return $parallax;
	}
}

/**
 * Paralllax | Code - Section & wrapper background
 */

if (! function_exists('mfn_parallax_data')) {
	function mfn_parallax_data()
	{
		$parallax = mfn_parallax_plugin();

		if ($parallax == 'translate3d') {
			$parallax = 'data-parallax="3d"';
		} elseif ($parallax == 'stellar') {
			$parallax = 'data-stellar-background-ratio="0.5"';
		} else {
			$parallax = 'data-enllax-ratio="-0.3"';
		}

		return $parallax;
	}
}

/**
 * Pagination for Blog and Portfolio
 */

if (! function_exists('mfn_pagination')) {
	function mfn_pagination($query = false, $load_more = false)
	{
		global $wp_query;
		$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);

		// default $wp_query

		if (! $query) {
			$query = $wp_query;
		}

		$translate['prev'] = mfn_opts_get('translate') ? mfn_opts_get('translate-prev', '&lsaquo; Prev page') : __('Prev page', 'betheme');
		$translate['next'] = mfn_opts_get('translate') ? mfn_opts_get('translate-next', 'Next page &rsaquo;') : __('Next page', 'betheme');
		$translate['load-more'] = mfn_opts_get('translate') ? mfn_opts_get('translate-load-more', 'Load more') : __('Load more', 'betheme');

		$query->query_vars['paged'] > 1 ? $current = $query->query_vars['paged'] : $current = 1;

		if (empty($paged)) {
			$paged = 1;
		}
		$prev = $paged - 1;
		$next = $paged + 1;

		$end_size = 1;
		$mid_size = 2;
		$show_all = mfn_opts_get('pagination-show-all');
		$dots = false;

		if (! $total = $query->max_num_pages) {
			$total = 1;
		}

		$output = '';

		if ($total > 1) {

			if ($load_more) {

				// load more

				if ($paged < $total) {
					$output .= '<div class="column one pager_wrapper pager_lm">';
						$output .= '<a class="pager_load_more button button_js" href="'. esc_url(get_pagenum_link($next) ).'">';
							$output .= '<span class="button_icon"><i class="icon-layout"></i></span>';
							$output .= '<span class="button_label">'. esc_html($translate['load-more']) .'</span>';
						$output .= '</a>';
					$output .= '</div>';
				}

			} else {

				// default

				$output .= '<div class="column one pager_wrapper">';
					$output .= '<div class="pager">';

						if ($paged >1) {
							$output .= '<a class="prev_page" href="'. esc_url(get_pagenum_link($prev)) .'"><i class="icon-left-open"></i>'. esc_html($translate['prev']) .'</a>';
						}

						$output .= '<div class="pages">';
						for ($i=1; $i <= $total; $i++) {
							if ($i == $current) {
								$output .= '<a href="'. esc_url(get_pagenum_link($i)) .'" class="page active">'. esc_html($i) .'</a>';
								$dots = true;
							} else {
								if ($show_all || ($i <= $end_size || ($current && $i >= $current - $mid_size && $i <= $current + $mid_size) || $i > $total - $end_size)) {
									$output .= '<a href="'. esc_url(get_pagenum_link($i)) .'" class="page">'. esc_html($i) .'</a>';
									$dots = true;
								} elseif ($dots && ! $show_all) {
									$output .= '<span class="page">...</span>';
									$dots = false;
								}
							}
						}
						$output .= '</div>';

						if ($paged < $total) {
							$output .= '<a class="next_page" href="'. esc_url(get_pagenum_link($next)) .'">'. esc_html($translate['next']) .'<i class="icon-right-open"></i></a>';
						}

					$output .= '</div>';
				$output .= '</div>'."\n";

			}

		}
		return $output;
	}
}

/**
 * Current page URL
 */

if (! function_exists('mfn_current_URL')) {
	function mfn_current_URL()
	{
		$env = $_SERVER; // context is safe and necessary

		$pageURL = 'http';
		if (is_ssl()) {
			$pageURL .= 's';
		}

		$pageURL .= '://';

		if( in_array( $env['SERVER_PORT'], array(80, 443) ) ){
			$pageURL .= $env['SERVER_NAME'].$env['REQUEST_URI'];
		} else {
			$pageURL .= $env['SERVER_NAME'] .':'. $env['SERVER_PORT'].$env['REQUEST_URI'];
		}

		return $pageURL;
	}
}

/**
 * Subheader | Page Title
 */

if (! function_exists('mfn_page_title')) {
	function mfn_page_title($echo = false)
	{
		if (is_home()) {

			// blog
			$title = get_the_title(mfn_ID());

		} elseif (function_exists('tribe_is_month') && (tribe_is_event_query() || tribe_is_month() || tribe_is_event() || tribe_is_day() || tribe_is_venue())) {

			// The Events Calendar
			$title = tribe_get_events_title();

		} elseif (is_tag()) {

			// blog: tag
			$title = single_tag_title('', false);

		} elseif (is_category()) {

			// blog: category
			$title = single_cat_title('', false);

		} elseif (is_author()) {

			// blog: author
			$title = get_the_author();

		} elseif (is_day()) {

			// blog: day
			$title = get_the_time('d');

		} elseif (is_month()) {

			// blog: month
			$title = get_the_time('F');

		} elseif (is_year()) {

			// blog: year
			$title = get_the_time('Y');

		} elseif (is_single() || is_page()) {

			// blog: single
			$title = get_the_title(mfn_ID());

		} elseif (get_post_taxonomies()) {

			// taxonomy
			$title = single_cat_title('', false);

		} else {

			// default
			$title = get_the_title(mfn_ID());

		}

		if ($echo) {
			echo wp_kses($title, mfn_allowed_html());
		}

		return $title;
	}
}

/**
 * Breadcrumbs
 */

if (! function_exists('mfn_breadcrumbs')) {
	function mfn_breadcrumbs($class = false)
	{
		global $post;

		$breadcrumbs = array();
		$separator = ' <span><i class="icon-right-open"></i></span>';

		// translate

		$translate['home'] = mfn_opts_get('translate') ? mfn_opts_get('translate-home', 'Home') : __('Home', 'betheme');

		// plugin: bbPress

		if (function_exists('is_bbpress') && is_bbpress()) {
			bbp_breadcrumb(array(
				'before' => '<ul class="breadcrumbs">',
				'after' => '</ul>',
				'sep' => '<i class="icon-right-open"></i>',
				'crumb_before' => '<li>',
				'crumb_after' => '</li>',
				'home_text' => esc_html($translate['home']),
			));
			return true; // exit
		}

		// home prefix

		$breadcrumbs[] = '<a href="'. esc_attr(home_url()) .'">'. esc_html($translate['home']) .'</a>';

		// blog

		if (get_post_type() == 'post') {

			$blogID = false;

			if (get_option('page_for_posts')) {
				$blogID = get_option('page_for_posts');	// Setings / Reading
			}

			if ($blogID) {
				$blog_post = get_post($blogID);

				// blog Page has parent

				if ($blog_post && $blog_post->post_parent) {

					$parent_id  = $blog_post->post_parent;
					$parents = array();

					while ($parent_id) {
						$page = get_page($parent_id);
						$parents[] = '<a href="'. get_permalink($page->ID) .'">'. wp_kses(get_the_title($page->ID), mfn_allowed_html()) .'</a>';
						$parent_id  = $page->post_parent;
					}

					$parents = array_reverse($parents);
					$breadcrumbs = array_merge_recursive($breadcrumbs, $parents);
				}

				$breadcrumbs[] = '<a href="'. esc_url(get_permalink($blogID)) .'">'. wp_kses(get_the_title($blogID), mfn_allowed_html()) .'</a>';
			}
		}

		if (is_front_page() || is_home()) {

			// do nothing

		} elseif (function_exists('tribe_is_month') && (tribe_is_event_query() || tribe_is_month() || tribe_is_event() || tribe_is_day() || tribe_is_venue())) {

			// plugin: Events Calendar

			if (function_exists('tribe_get_events_link')) {
				$breadcrumbs[] = '<a href="'. esc_url(tribe_get_events_link()) .'">'. esc_html(tribe_get_events_title()) .'</a>';
			}

		} elseif (is_tag()) {

			// blog: tag

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(single_tag_title('', false)) . '</a>';

		} elseif (is_category()) {

			// blog: category

			$cat = get_term_by('name', single_cat_title('', false), 'category');
			if ($cat && $cat->parent) {
				$breadcrumbs[] = get_category_parents($cat->parent, true, $separator);
			}

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(single_cat_title('', false)) .'</a>';

		} elseif (is_author()) {

			// blog: author

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(get_the_author()) .'</a>';

		} elseif (is_day()) {

			// blog: day

			$breadcrumbs[] = '<a href="'. esc_url(get_year_link(get_the_time('Y'))) . '">'. esc_html(get_the_time('Y')) .'</a>';
			$breadcrumbs[] = '<a href="'. esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) .'">'. esc_html(get_the_time('F')) .'</a>';
			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(get_the_time('d')) .'</a>';

		} elseif (is_month()) {

			// blog: month

			$breadcrumbs[] = '<a href="'. esc_url(get_year_link(get_the_time('Y'))) .'">' . esc_html(get_the_time('Y')) . '</a>';
			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(get_the_time('F')) .'</a>';

		} elseif (is_year()) {

			// blog: year

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(get_the_time('Y')) .'</a>';

		} elseif (is_single() && ! is_attachment()) {

			// single

			if (get_post_type() != 'post') {

				// portfolio

				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				$portfolio_page_id = mfn_wpml_ID(mfn_opts_get('portfolio-page'));

				// portfolio page

				if ($slug['slug'] == mfn_opts_get('portfolio-slug', 'portfolio-item') && $portfolio_page_id) {
					$breadcrumbs[] = '<a href="'. esc_url(get_page_link($portfolio_page_id)) .'">'. esc_html(get_the_title($portfolio_page_id)) .'</a>';
				}

				// category

				if ($portfolio_page_id) {
					$terms = get_the_terms(get_the_ID(), 'portfolio-types');
					if (! empty($terms) && ! is_wp_error($terms)) {
						$breadcrumbs[] = get_term_parents_list($terms[0], 'portfolio-types', array('separator' => $separator ));
					}
				}

				// single

				$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. get_the_title().'</a>';

			} else {

				// blog single

				$cat = get_the_category();
				if (! empty($cat)) {
					$breadcrumbs[] = get_category_parents($cat[0], true, $separator);
				}

				$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. get_the_title() .'</a>';
			}

		} elseif (! is_page() && get_post_taxonomies()) {

			// taxonomy portfolio

			$post_type = get_post_type_object(get_post_type());
			if ($post_type->name == 'portfolio' && $portfolio_page_id = mfn_wpml_ID(mfn_opts_get('portfolio-page'))) {
				$breadcrumbs[] = '<a href="'. esc_url(get_page_link($portfolio_page_id)) .'">'. esc_html(get_the_title($portfolio_page_id)) .'</a>';
			}

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. esc_html(single_cat_title('', false)) .'</a>';

		} elseif (is_page() && $post->post_parent) {

			// page with parent

			$parent_id = $post->post_parent;
			$parents = array();

			while ($parent_id) {
				$page = get_page($parent_id);
				$parents[] = '<a href="'. esc_url(get_permalink($page->ID)) .'">'. wp_kses(get_the_title($page->ID), mfn_allowed_html()) .'</a>';
				$parent_id = $page->post_parent;
			}
			$parents = array_reverse($parents);
			$breadcrumbs = array_merge_recursive($breadcrumbs, $parents);

			$breadcrumbs[] = '<a href="' . esc_url(mfn_current_URL()) . '">'. wp_kses(get_the_title(mfn_ID()), mfn_allowed_html()) .'</a>';

		} else {

			// default

			$breadcrumbs[] = '<a href="'. esc_url(mfn_current_URL()) .'">'. wp_kses(get_the_title(mfn_ID()), mfn_allowed_html()) .'</a>';

		}

		// output -----

		echo '<ul class="breadcrumbs '. esc_attr($class) .'">';

			$count = count($breadcrumbs);
			$i = 1;

			foreach ($breadcrumbs as $bk => $bc) {

				if (strpos($bc, $separator)) {

					// category parent

					echo '<li>'. $bc .'</li>';

				} else {

					if ($i == $count) {
						$separator = '';
					}
					echo '<li>'. $bc . $separator .'</li>';

				}

				$i++;
			}

		echo '</ul>';
	}
}

/**
 * Hex 2 rgba
 */

if (! function_exists('mfn_hex2rgba')) {
	function mfn_hex2rgba($hex, $alpha = 1, $echo = false)
	{
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3) {
			$r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
		} else {
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}

		$rgba = 'rgba('. $r.','. $g .','. $b .','. $alpha .')';

		if ($echo) {
			echo esc_attr($rgba);
			return true;
		}

		return $rgba;
	}
}

/**
 * Is dark color
 */

if (! function_exists('mfn_brightness')) {
	function mfn_brightness($hex, $tolerance = 169, $oposite_color = false)
	{
		if( ! $hex ){
			return false;
		}

		$hex = str_replace("#", "", $hex);

		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));

		$brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

		if ($brightness > $tolerance) {
			$brightness = 'light';
		} else {
			$brightness = 'dark';
		}

		if ($oposite_color) {
			if ($brightness == 'light') {
				$brightness = 'black';
			} else {
				$brightness = 'white';
			}
		}

		return $brightness;
	}
}

/**
 * jPlayer HTML
 */

if (! function_exists('mfn_jplayer_html')) {
	function mfn_jplayer_html($video_m4v, $poster = false)
	{
		$player_id = mt_rand(0, 999);

		$output = '<div id="jp_container_'. esc_attr($player_id) .'" class="jp-video mfn-jcontainer">';
		$output .= '<div class="jp-type-single">';
		$output .= '<div id="jquery_jplayer_'. esc_attr($player_id) .'" class="jp-jplayer mfn-jplayer" data-m4v="'. esc_url($video_m4v) .'" data-img="'. esc_url($poster) .'" data-swf="'. get_theme_file_uri('/assets/jplayer') .'"></div>';
		$output .= '<div class="jp-gui">';
		$output .= '<div class="jp-video-play">';
		$output .= '<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>';
		$output .= '</div>';
		$output .= '<div class="jp-interface">';
		$output .= '<div class="jp-progress">';
		$output .= '<div class="jp-seek-bar">';
		$output .= '<div class="jp-play-bar"></div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="jp-current-time"></div>';
		$output .= '<div class="jp-duration"></div>';
		$output .= '<div class="jp-controls-holder">';
		$output .= '<ul class="jp-controls">';
		$output .= '<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>';
		$output .= '</ul>';
		$output .= '<div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div>';
		$output .= '<ul class="jp-toggles">';
		$output .= '<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>';
		$output .= '<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>';
		$output .= '</ul>';
		$output .= '</div>';
		$output .= '<div class="jp-title"><ul><li>jPlayer Video Title</li></ul></div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="jp-no-solution"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a></div>';
		$output .= '</div>';
		$output .= '</div>'."\n";

		return $output;
	}
}

/**
 * jPlayer
 */

if (! function_exists('mfn_jplayer')) {
	function mfn_jplayer($postID, $sizeH = 'full')
	{
		// masonry square video fix

		if ($sizeH == 'blog-masonry') {
			$sizeH = 'blog-square';
		}

		$video_m4v	= get_post_meta($postID, 'mfn-post-video-mp4', true);
		$poster	= wp_get_attachment_image_src(get_post_thumbnail_id($postID), $sizeH);
		$poster	= $poster[0];

		return mfn_jplayer_html($video_m4v, $poster);
	}
}

/**
 * Post Format
 */

if (! function_exists('mfn_post_format')) {
	function mfn_post_format($postID)
	{
		if (get_post_type($postID) == 'portfolio' && is_single($postID)) {

			// portfolio

			if (get_post_meta(get_the_ID(), 'mfn-post-video', true)) {

				// video: embed
				$format = 'video';

			} elseif (get_post_meta(get_the_ID(), 'mfn-post-video-mp4', true)) {

				// video: HTML5
				$format = 'video';

			} else {

				// image
				$format = false;

			}

		} else {

			// blog
			$format = get_post_format($postID);

		}

		return $format;
	}
}

/**
 * Attachment | GET attachment ID by URL
 */

if (! function_exists('mfn_get_attachment_id_url')) {
	function mfn_get_attachment_id_url($image_url)
	{
		global $wpdb;

		$image_url = esc_url($image_url);
		$attachment = $wpdb->get_col($wpdb->prepare( "SELECT `ID` FROM $wpdb->posts WHERE `guid` = %s", $image_url ));

		if (isset($attachment[0])) {
			return $attachment[0];
		}

		// QUICK FIX https
		$image_url = str_replace('https://', 'http://', $image_url);
		$attachment = $wpdb->get_col($wpdb->prepare( "SELECT `ID` FROM $wpdb->posts WHERE `guid` = %s", $image_url ));

		if (isset($attachment[0])) {
			return $attachment[0];
		}
	}
}

/**
 * Attachment | GET attachment data
 */

if (! function_exists('mfn_get_attachment_data')) {
	function mfn_get_attachment_data($image, $data, $with_key = false)
	{
		$size = $return = false;

		if (! is_numeric($image)) {
			$image = mfn_get_attachment_id_url($image);
		}

		// WPML workaround
		$image = apply_filters('wpml_object_id', $image, 'attachment', true);
		$meta = wp_prepare_attachment_for_js($image);

		if (is_array($meta) && isset($meta[ $data ])) {

			$return = $meta[ $data ];

			// if looking for alt and it isn't specified use image title

			if ($data == 'alt' && ! $return) {
				$return = $meta[ 'title' ];
			}

		}

		if ($return && $with_key) {
			$return = esc_attr($data). '="'. esc_attr($return) .'"';
		}

		return $return;
	}
}

/**
 * Post Thumbnail | GET post thumbnail type
 */

if (! function_exists('mfn_post_thumbnail_type')) {
	function mfn_post_thumbnail_type($postID)
	{
		$type = 'image';
		$post_format = mfn_post_format($postID);

		if ($post_format == 'image') {
			$type = 'image';
		} elseif ($post_format == 'video' && get_post_meta($postID, 'mfn-post-video', true)) {
			$type = 'video embed';
		} elseif ($post_format == 'video' && get_post_meta($postID, 'mfn-post-video-mp4', true)) {
			$type = 'video html5';
		} elseif (get_post_meta($postID, 'mfn-post-slider', true) || get_post_meta($postID, 'mfn-post-slider-layer', true)) {
			$type = 'slider';
		}

		return $type;
	}
}

/**
 * Post Thumbnail | GET post thumbnail
 */

if (! function_exists('mfn_post_thumbnail')) {
	function mfn_post_thumbnail($postID, $type = false, $style = false, $featured_image = false)
	{
		$output = '';

		// image size -----

		if ($type == 'portfolio') {

			// portfolio

			if ($style == 'list') {

				// portfolio: list

				$sizeH = 'portfolio-list';

			} elseif ($style == 'masonry-flat') {

				// portfolio: masonry flat

				$size = get_post_meta($postID, 'mfn-post-size', true);
				if ($size == 'wide') {
					$sizeH = 'portfolio-mf-w';
				} elseif ($size == 'tall') {
					$sizeH = 'portfolio-mf-t';
				} else {
					$sizeH = 'portfolio-mf';
				}

			} elseif ($style == 'masonry-minimal') {

				// portfolio: masonry minimal

				$sizeH = 'full';

			} else {

				// portfolio: default

				$sizeH = 'blog-portfolio';

			}

		} elseif( 'blog' == $type && in_array($style, array('photo', 'photo2')) ){

			// blog: photo

			$sizeH = 'blog-single';
			$sizeV = 'blog-single';

		} elseif( 'related' == $type ){

			// related posts

			$sizeH = 'blog-portfolio';

		} elseif (is_single($postID)) {

			// blog & portfolio: single

			$sizeH = 'blog-single';
			$sizeV = 'full';

		} else {

			// default

			$sizeH = 'blog-portfolio';
			$sizeV = 'full';

		}

		// link wrap -----

		$large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($postID), 'large');

		if (is_single($postID)) {

			// single

			$link_before = '<a href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto">';
				$link_before .= '<div class="mask"></div>';

				$link_after = '</a>';
			$link_after .= '<div class="image_links">';
				$link_after .= '<a href="'. esc_url($large_image_url[0]) .'" class="zoom" rel="prettyphoto"><i class="icon-search"></i></a>';
			$link_after .= '</div>';

			// single: post

			if (get_post_type() == 'post') {

				// blog: single - disable image zoom

				if (! mfn_opts_get('blog-single-zoom')) {
					$link_before = '';
					$link_after = '';
				}

				// blog single: structured data

				if (mfn_opts_get('mfn-seo-schema-type')) {
					$link_before .= '<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';

						$image_url = wp_get_attachment_image_src(get_post_thumbnail_id($postID), 'full');

						$link_after_schema = '<meta itemprop="url" content="'. esc_url($image_url[0]) .'"/>';
						$link_after_schema .= '<meta itemprop="width" content="'. esc_attr(mfn_get_attachment_data($image_url[0], 'width')) .'"/>';
						$link_after_schema .= '<meta itemprop="height" content="'. esc_attr(mfn_get_attachment_data($image_url[0], 'height')) .'"/>';

					$link_after_schema .= '</div>';

					$link_after = $link_after_schema . $link_after;
				}
			}

		} elseif ($type == 'portfolio') {

			// portfolio

			$external = mfn_opts_get('portfolio-external');

			// external link to project page

			if ($image_links = (get_post_meta(get_the_ID(), 'mfn-post-link', true))) {
				$image_links_class = 'triple';
			} elseif (! in_array($external, array( '_self', '_blank' ))) {
				$image_links_class = 'double';
			} else {
				$image_links_class = 'single';
			}

			// image link

			if ($external == 'popup') {

				// popup

				$link_before = '<a href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto">';
				$link_title = '<a href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto">';

			} elseif ($external == 'disable') {

				// disable details

				$link_before = '<a href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto[portfolio]">';
				$link_title = '<a href="'. esc_url($large_image_url[0]) .'" rel="prettyphoto">';

			} elseif ($external && $image_links) {

				// link to project website

				$image_links_class = 'double';
				$link_before = '<a href="'. esc_url($image_links) .'" target="'. esc_attr($external) .'">';
				$link_title = '<a href="'. esc_url($image_links) .'" target="'. esc_attr($external ).'">';

			} else {

				// link to project details

				$link_before = '<a href="'. esc_url(get_permalink()) .'">';
				$link_title = '<a href="'. esc_url(get_permalink()) .'">';

			}

			$link_before .= '<div class="mask"></div>';

			$link_after = '</a>';

			// hover

			if (mfn_opts_get('portfolio-hover-title')) {

				// hover: title

				$link_after .= '<div class="image_links hover-title">';
					$link_after .= $link_title . wp_kses(get_the_title(), mfn_allowed_html()) .'</a>';
				$link_after .= '</div>';

			} elseif ($external != 'disable') {

				// hover: icons

				$link_after .= '<div class="image_links '. esc_attr($image_links_class) .'">';
					if (! in_array($external, array( '_self', '_blank' ))) {
						$link_after .= '<a href="'. esc_url($large_image_url[0]) .'" class="zoom" rel="prettyphoto"><i class="icon-search"></i></a>';
					}
					if ($image_links) {
						$link_after .= '<a target="_blank" href="'. esc_url($image_links) .'" class="external"><i class="icon-forward"></i></a>';
					}
					$link_after .= '<a href="'. esc_url(get_permalink()) .'" class="link"><i class="icon-link"></i></a>';
				$link_after .= '</div>';
			}
		} else {

			// blog

			$link_before = '<a href="'. esc_url(get_permalink()) .'">';
				$link_before .= '<div class="mask"></div>';

			$link_after = '</a>';
			$link_after .= '<div class="image_links double">';
				$link_after .= '<a href="'. esc_url($large_image_url[0]) .'" class="zoom" rel="prettyphoto"><i class="icon-search"></i></a>';
				$link_after .= '<a href="'. esc_url(get_permalink()) .'" class="link"><i class="icon-link"></i></a>';
			$link_after .= '</div>';
		}

		// post format -----

		$post_format = mfn_post_format($postID);

		// featured image: available types

		// no slider if load more

		if ( 'no_slider' == $featured_image ) {
			$type = 'portfolio';
		}

		// images only option

		if ( 'image' == $featured_image ) {
			if (! in_array($post_format, array( 'quote', 'link', 'image' ))) {
				$post_format = 'image-only';
			}
		}

		switch ($post_format) {

			case 'quote':
			case 'link':

				// quote - Quote - without image

				return false;
				break;

			case 'image':

				// image - Vertical Image

				if (has_post_thumbnail()) {
					$output .= $link_before;
						$output .= get_the_post_thumbnail($postID, $sizeV, array( 'class'=>'scale-with-grid' ));
					$output .= $link_after;
				}
				break;

			case 'video':

				// video - Video

				if ($video = get_post_meta($postID, 'mfn-post-video', true)) {
					if (is_numeric($video)) {
						// Vimeo
						$output .= '<iframe class="scale-with-grid" src="https://player.vimeo.com/video/'. esc_attr($video) .'" allowFullScreen></iframe>'."\n";
					} else {
						// YouTube
						$output .= '<iframe class="scale-with-grid" src="https://www.youtube.com/embed/'. esc_attr($video) .'?wmode=opaque&rel=0" allowfullscreen></iframe>'."\n";
					}
				} elseif (get_post_meta($postID, 'mfn-post-video-mp4', true)) {
					$output .= mfn_jplayer($postID);
				}
				break;

			case 'image-only':

				// images only option

				if (has_post_thumbnail()) {
					$output .= $link_before;
						$output .= get_the_post_thumbnail($postID, $sizeH, array( 'class'=>'scale-with-grid' ));
					$output .= $link_after;
				}
				break;

			default:

				// standard - Text, Horizontal Image, Slider

				$rev_slider = get_post_meta($postID, 'mfn-post-slider', true);
				$lay_slider = get_post_meta($postID, 'mfn-post-slider-layer', true);

				if (('portfolio' != $type) && ($rev_slider || $lay_slider)) {
					if ($rev_slider) {
						// Revolution Slider
						$output .= do_shortcode('[rev_slider '. $rev_slider .']');
					} elseif ($lay_slider) {
						// Layer Slider
						$output .= do_shortcode('[layerslider id="'. $lay_slider .'"]');
					}
				} elseif (has_post_thumbnail()) {

					// Image
					$output .= $link_before;
						$output .= get_the_post_thumbnail($postID, $sizeH, array( 'class'=>'scale-with-grid' ));
					$output .= $link_after;
				}
		}

		return $output;
	}
}

/**
 * Single Post Navigation | SET query order
 */

// previous

if (! function_exists('mfn_filter_previous_post_sort')) {
	function mfn_filter_previous_post_sort($sort)
	{
		if (mfn_get_portfolio_order() == 'ASC') {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}
		$sort = "ORDER BY p.". esc_sql(mfn_get_portfolio_orderby()) ." ". $order ." LIMIT 1";
		return $sort;
	}
}

if (! function_exists('mfn_filter_previous_post_where')) {
	function mfn_filter_previous_post_where($where)
	{
		global $post, $wpdb;

		$orderby = mfn_get_portfolio_orderby();
		$where = preg_replace("/(.*)p.post_type/", "AND p.post_type", $where);

		if (mfn_get_portfolio_order() == 'ASC') {
			$where_pre = $wpdb->prepare("WHERE p.". esc_sql($orderby) ." < %s", $post->$orderby);
		} else {
			$where_pre = $wpdb->prepare("WHERE p.". esc_sql($orderby) ." > %s", $post->$orderby);
		}

		$where = $where_pre.' '.$where;
		return $where;
	}
}

// next

if (! function_exists('mfn_filter_next_post_sort')) {
	function mfn_filter_next_post_sort($sort)
	{
		$sort = "ORDER BY p.". esc_sql(mfn_get_portfolio_orderby()) ." ". esc_sql(mfn_get_portfolio_order()) ." LIMIT 1";
		return $sort;
	}
}

if (! function_exists('mfn_filter_next_post_where')) {
	function mfn_filter_next_post_where($where)
	{
		global $post, $wpdb;

		$orderby = mfn_get_portfolio_orderby();
		$where = preg_replace("/(.*)p.post_type/", "AND p.post_type", $where);

		if (mfn_get_portfolio_order() == 'ASC') {
			$where_pre = $wpdb->prepare("WHERE p.". esc_sql($orderby) ." > %s", $post->$orderby);
		} else {
			$where_pre = $wpdb->prepare("WHERE p.". esc_sql($orderby) ." < %s", $post->$orderby);
		}

		$where = $where_pre.' '.$where;
		return $where;
	}
}

// helpers

if (! function_exists('mfn_get_portfolio_order')) {
	function mfn_get_portfolio_order()
	{
		return mfn_opts_get('portfolio-order', 'DESC');
	}
}

if (! function_exists('mfn_get_portfolio_orderby')) {
	function mfn_get_portfolio_orderby()
	{
		$orderby = mfn_opts_get('portfolio-orderby', 'date');
		switch ($orderby) {
			case 'title':
				$orderby = 'post_title';
				break;
			case 'menu_order':
				$orderby = 'menu_order';
				break;
			default:
				$orderby = 'post_date';
		}
		return $orderby;
	}
}

// filters

if (! function_exists('mfn_post_navigation_sort')) {
	function mfn_post_navigation_sort()
	{
		add_filter('get_previous_post_sort', 'mfn_filter_previous_post_sort');
		add_filter('get_previous_post_where', 'mfn_filter_previous_post_where');
		add_filter('get_next_post_sort', 'mfn_filter_next_post_sort');
		add_filter('get_next_post_where', 'mfn_filter_next_post_where');
	}
}

/**
 * Single Post Navigation | GET header navigation
 */

if (! function_exists('mfn_post_navigation_header')) {
	function mfn_post_navigation_header($post_prev, $post_next, $post_home, $translate = array())
	{
		$style = mfn_opts_get('prev-next-style');

		$output = '<div class="column one post-nav '. esc_attr($style) .'">';

		if ($style == 'minimal') {

				// minimal

			if ($post_prev) {
				$output .= '<a class="prev" href="'. esc_url(get_permalink($post_prev)) .'"><i class="icon icon-left-open-big"></i></a>';
			}
			if ($post_next) {
				$output .= '<a class="next" href="'. esc_url(get_permalink($post_next)) .'"><i class="icon icon-right-open-big"></i></a>';
			}
			if ($post_home) {
				$output .= '<a class="home" href="'. esc_url(get_permalink($post_home)) .'"><svg class="icon" width="22" height="22" xmlns="https://www.w3.org/2000/svg"><path d="M7,2v5H2V2H7 M9,0H0v9h9V0L9,0z"/><path d="M20,2v5h-5V2H20 M22,0h-9v9h9V0L22,0z"/><path d="M7,15v5H2v-5H7 M9,13H0v9h9V13L9,13z"/><path d="M20,15v5h-5v-5H20 M22,13h-9v9h9V13L22,13z"/></svg></a>';
			}
		} else {

				// default

			$output .= '<ul class="next-prev-nav">';
			if ($post_prev) {
				$output .= '<li class="prev"><a class="button button_js" href="'. esc_url(get_permalink($post_prev)) .'"><span class="button_icon"><i class="icon-left-open"></i></span></a></li>';
			}
			if ($post_next) {
				$output .= '<li class="next"><a class="button button_js" href="'. esc_url(get_permalink($post_next)) .'"><span class="button_icon"><i class="icon-right-open"></i></span></a></li>';
			}
			$output .= '</ul>';

			if ($post_home) {
				$output .= '<a class="list-nav" href="'. esc_url(get_permalink($post_home)) .'"><i class="icon-layout"></i>'. esc_html($translate['all']) .'</a>';
			}
		}

		$output .= '</div>';

		return $output;
	}
}

/**
 * Single Post Navigation | GET sticky navigation
 */

if (! function_exists('mfn_post_navigation_sticky')) {
	function mfn_post_navigation_sticky($post, $next_prev, $icon)
	{
		$output = '';

		if (is_object($post)) {

			// move this DOM element with JS

			$style = mfn_opts_get('prev-next-sticky-style', 'default');

			$output .= '<a class="fixed-nav fixed-nav-'. esc_attr($next_prev) .' format-'. esc_attr(get_post_format($post)) .' style-'. esc_attr($style) .'" href="'. esc_url(get_permalink($post)) .'">';

				$output .= '<span class="arrow"><i class="'. esc_attr($icon) .'"></i></span>';

				$output .= '<div class="photo">';
					$output .= get_the_post_thumbnail($post->ID, 'blog-navi');
				$output .= '</div>';

				$output .= '<div class="desc">';
					$output .= '<h6>'. wp_kses(get_the_title($post), array()) .'</h6>';
					$output .= '<span class="date"><i class="icon-clock"></i>'. esc_html(get_the_date(get_option('date_format'), $post->ID)) .'</span>';
				$output .= '</div>';

			$output .= '</a>';
		}

		return $output;
	}
}

/**
 * Search | SET add custom fields to search query
 */

if (! function_exists('mfn_search')) {
	function mfn_search($search_query)
	{
		global $wpdb;

		if (is_admin()) {
			return false;
		}

		if (is_search() && $search_query->is_main_query() && $search_query->is_search()) {

			$keyword = get_search_query();

			if (! $keyword) {
				return false;
			}

			// WooCommerce uses default search Query

			if (function_exists('is_woocommerce') && is_woocommerce()) {
				return false;
			}

			$keyword = '%'. $wpdb->esc_like($keyword) .'%';

			// post title

			$post_ids_title = $wpdb->get_col($wpdb->prepare("
				SELECT DISTINCT `ID` FROM {$wpdb->posts}
				WHERE `post_title` LIKE %s
			", $keyword));

			// post conatnt

			$post_ids_content = $wpdb->get_col($wpdb->prepare("
				SELECT DISTINCT `ID` FROM {$wpdb->posts}
				WHERE `post_content` LIKE %s
			", $keyword));

			// custom fields

			$post_ids_meta = $wpdb->get_col($wpdb->prepare("
				SELECT DISTINCT `post_id` FROM {$wpdb->postmeta}
				WHERE `meta_key` = 'mfn-page-items-seo'
				AND `meta_value` LIKE %s
			", $keyword));

			$post_ids = array_merge($post_ids_title, $post_ids_content, $post_ids_meta);

			if (! count($post_ids)) {
				return false;
			}

			$search_query->set('s', false);
			$search_query->set('post__in', $post_ids);
			$search_query->set('orderby', 'post__in');
		}
	}
}
add_action('pre_get_posts', 'mfn_search');

/**
 * Posts per page & pagination fix
 */

if (! function_exists('mfn_option_posts_per_page')) {
	function mfn_option_posts_per_page($value)
	{
		if (is_tax('portfolio-types')) {
			$posts_per_page = mfn_opts_get('portfolio-posts', 6, true);
		} else {
			$posts_per_page = mfn_opts_get('blog-posts', 5, true);
		}
		return $posts_per_page;
	}
}

if (! function_exists('mfn_posts_per_page')) {
	function mfn_posts_per_page()
	{
		add_filter('option_posts_per_page', 'mfn_option_posts_per_page');
	}
}
add_action('init', 'mfn_posts_per_page', 0);

/**
 *	Comments number with text
 */

if (! function_exists('mfn_comments_number')) {
	function mfn_comments_number()
	{
		$translate['comment'] = mfn_opts_get('translate') ? mfn_opts_get('translate-comment', 'comment') : __('comment', 'betheme');
		$translate['comments'] = mfn_opts_get('translate') ? mfn_opts_get('translate-comments', 'comments') : __('comments', 'betheme');
		$translate['comments-off'] = mfn_opts_get('translate') ? mfn_opts_get('translate-comments-off', 'comments off') : __('comments off', 'betheme');

		$num_comments = get_comments_number(); // get_comments_number returns only a numeric value

		if (comments_open()) {
			if ($num_comments != 1) {
				$comments = '<a href="'. esc_url(get_comments_link()) .'">'. esc_html($num_comments).'</a> '. esc_html($translate['comments']);
			} else {
				$comments = '<a href="'. esc_url(get_comments_link()) .'">1</a> '. esc_html($translate['comment']);
			}
		} else {
			$comments = $translate['comments-off'];
		}
		return $comments;
	}
}

/**
 *	Menu title in selected location
 */

if (! function_exists('mfn_get_menu_name')) {
	function mfn_get_menu_name($location)
	{
		if (! has_nav_menu($location)) {
			return false;
		}

		$menus = get_nav_menu_locations();
		$menu_title = wp_get_nav_menu_object($menus[$location])->name;

		return $menu_title;
	}
}

/**
 *	GET | WordPress Authors
 */

if (! function_exists('mfn_get_authors')) {
	function mfn_get_authors()
	{
		$authors = get_users();

		if (is_array($authors)) {
			foreach ($authors as $ka => $author) {
				$remove = true;
				if (in_array('contributor', $author->roles)) {
					$remove = false;
				}
				if (in_array('author', $author->roles)) {
					$remove = false;
				}
				if (in_array('editor', $author->roles)) {
					$remove = false;
				}
				if (in_array('administrator', $author->roles)) {
					$remove = false;
				}
				if ($remove) {
					unset($authors[$ka]);
				}
			}
		}

		return $authors;
	}
}

/**
 * GET Categories
 * Categories for posts or specified taxonomy
 */

if (! function_exists('mfn_get_categories')) {
	function mfn_get_categories($category)
	{
		$categories = get_categories(array(
			'taxonomy' => $category,
		));

		$array = array(
			'' => esc_html__('All', 'mfn-opts'),
		);

		foreach ($categories as $cat) {
			if (is_object($cat)) {
				$array[$cat->slug] = $cat->name;
			}
		}

		return $array;
	}
}

/**
 *	Under Construction
 */

if (! function_exists('mfn_under_construction')) {
	function mfn_under_construction()
	{
		$php_self = $_SERVER['PHP_SELF']; // context is safe and necessary

		if (mfn_opts_get('construction')) {

			if (isset($_POST['_wpcf7'])) {
				// contact form 7 compatibility
			} else {
				if (! is_user_logged_in() && ! is_admin()
				&& basename($php_self) != 'wp-login.php'
				&& basename($php_self) != 'wp-cron.php'
				&& basename($php_self) != 'xmlrpc.php') {
					get_template_part('under-construction');
					exit();
				}
			}

		}
	}
}
add_action('init', 'mfn_under_construction', 30);

/**
 *	Set Max Content Width
 */

if (! isset($content_width)) {
	$content_width = 1220;
}

/**
 *	WPML | Date Format
 */

if (! function_exists('mfn_wpml_date_format')) {
	function mfn_wpml_date_format($format)
	{
		if (function_exists('icl_translate')) {
			$format = icl_translate('Formats', $format, $format);
		}
		return $format;
	}
}
add_filter('option_date_format', 'mfn_wpml_date_format');

/**
 *	WPML | ID
 *	@param type string – 'post', 'page', 'post_tag' or 'category'
 */

if (! function_exists('mfn_wpml_ID')) {
	function mfn_wpml_ID($id, $type = 'page')
	{
		if (function_exists('icl_object_id')) {
			return icl_object_id($id, $type, true);
		} else {
			return $id;
		}
	}
}

/**
 *	WPML | Term slug
 */

if (! function_exists('mfn_wpml_term_slug')) {
	function mfn_wpml_term_slug($slug, $type, $multi = false)
	{
		if (function_exists('icl_object_id')) {
			if ($multi) {

				// multiple categories

				$slugs = explode(',', $slug);

				if (is_array($slugs)) {
					foreach ($slugs as $slug_k => $slug) {
						$slug = trim($slug);

						$term = get_term_by('slug', $slug, $type);
						$term = apply_filters('wpml_object_id', $term->term_id, $type, true);
						$slug = get_term_by('term_id', $term, $type)->slug;

						$slugs[$slug_k] = $slug;
					}
				}

				$slug = implode(',', $slugs);
			} else {

				// single category

				$term = get_term_by('slug', $slug, $type);
				$term = apply_filters('wpml_object_id', $term->term_id, $type, true);
				$slug = get_term_by('term_id', $term, $type)->slug;
			}
		}

		return $slug;
	}
}

/**
 *	Schema | Auto Get Schema Type By Post Type
 */

if (! function_exists('mfn_tag_schema')) {
	function mfn_tag_schema()
	{
		$schema = 'https://schema.org/';

		// Is Woocommerce product
		if (function_exists('is_product') && is_product()) {
			$type = false;
		} elseif (is_single() && get_post_type() == 'post') {

			// Single post
			$type = "Article";
		} elseif (is_author()) {

			// Author page
			$type = 'ProfilePage';
		} elseif (is_search()) {

			// Search results
			$type = 'SearchResultsPage';
		} else {

			// Default
			$type = 'WebPage';
		}

		if (mfn_opts_get('mfn-seo-schema-type') && $type) {
			echo ' itemscope itemtype="'. esc_url($schema) . esc_attr($type) .'"';
		}

		return true;
	}
}

/**
 * Bundled plugins
 */

if (! function_exists('mfn_bundled_plugins')) {
	function mfn_bundled_plugins(){

		if (! mfn_opts_get('plugin-rev')) {
	  	if (function_exists('set_revslider_as_theme')) {
	  		set_revslider_as_theme();
	  	}
	  }

	  if (! mfn_opts_get('plugin-visual')) {
	  	function mfn_vc_set_as_theme(){
	  		vc_set_as_theme();
	  	}
			add_action('vc_before_init', 'mfn_vc_set_as_theme');
	  }

	}
}
mfn_bundled_plugins();

/**
 *	Registration | Is hosted
 */

function mfn_is_hosted()
{
	return defined('ENVATO_HOSTED_KEY') ? true : false;
}

/**
 *	Registration | Is registered
 */

 function mfn_is_registered()
 {
 	return true;
 	
 	if (mfn_is_hosted()) {
 		return mfn_is_hosted();
 	}

 	if (mfn_get_purchase_code()) {
 		return true;
 	}

 	return false;
 }

/**
 *	Registration | Get purchase code
 */

function mfn_get_purchase_code()
{
 	if (mfn_is_hosted()) {
 		return SUBSCRIPTION_CODE;
 	}

 	$code = get_site_option('envato_purchase_code_7758048');

 	if( ! $code ){
 		// BeTheme < 21.0.8 backward compatibility
 		$code = get_site_option('betheme_purchase_code');
 		if( $code ){
 			update_site_option('envato_purchase_code_7758048', $code);
 			delete_site_option('betheme_purchase_code');
 			delete_site_option('betheme_registered');
 		}
 	}

	return $code;
}

/**
 *	Registration | Get purchase code with asterisk
 */

function mfn_get_purchase_code_hidden()
{
	$code = mfn_get_purchase_code();

	if ($code) {
		$code = substr($code, 0, 13);
		$code = $code .'-****-****-************';
	}

	return $code;
}

/**
 *	Registration | Get ish
 */

function mfn_get_ish()
{
	if (! defined('ENVATO_HOSTED_KEY')) {
		return false;
	}

	return substr(ENVATO_HOSTED_KEY, 0, 16);
}

/**
 * Theme support
 */

add_editor_style(array('css/editor-style.css','https://fonts.googleapis.com/css?family=Roboto'));

add_theme_support('automatic-feed-links');
add_theme_support('custom-logo', array('width'=> 145, 'height' => 35, 'flex-height' => true, 'flex-width' => true));
add_theme_support('editor-styles');
add_theme_support('post-formats', array('image', 'video', 'quote', 'link'));
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
