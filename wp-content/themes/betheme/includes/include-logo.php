<?php
/**
 * Logo
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

// logo wrapper allowed html5

$logo_allowed_html = array(
	'a' => array(
		'data-height' => array(),
		'data-padding' => array(),
		'href' => array(),
		'id' => array(),
		'title' => array(),
	),
	'h1' => array(),
	'span' => array(
		'data-height' => array(),
		'data-padding' => array(),
		'id' => array(),
	),
);

// class for text logo

if ($logo_text = mfn_opts_get('logo-text')) {
	$logo_class = ' text-logo';
} else {
	$logo_class = false;
}

echo '<div class="logo'. esc_attr($logo_class) .'">';

	// options

	$logo_height = mfn_opts_get('logo-height', 60);
	$logo_padding = mfn_opts_get('logo-vertical-padding', 15);

	$logo_options = mfn_opts_get('logo-link', false);

	$logo_before = '';
	$logo_after	= '';

	// link

	if (isset($logo_options['link'])) {
		$logo_before = '<a id="logo" href="'. esc_url(get_home_url()) .'" title="'. esc_attr(get_bloginfo('name')) .'" data-height="'. intval($logo_height, 10) .'" data-padding="'. intval($logo_padding, 10) .'">';
		$logo_after = '</a>';
	} else {
		$logo_before = '<span id="logo" data-height="'. intval($logo_height, 10) .'" data-padding="'. intval($logo_padding, 10) .'">';
		$logo_after = '</span>';
	}

	// H1

	if (is_front_page()) {
		if (is_array($logo_options) && isset($logo_options['h1-home'])) {
			$logo_before = '<h1>'. $logo_before;
			$logo_after .= '</h1>';
		}
	} else {
		if (is_array($logo_options) && isset($logo_options['h1-all'])) {
			$logo_before = '<h1>'. $logo_before;
			$logo_after .= '</h1>';
		}
	}

	// source

	$logo = array(
		'default'	=> array(
			'main' => '',
			'sticky' => '',
			'mobile' => '',
			'mobile-sticky' => '',
		),
		'retina' => array(
			'main' => '',
			'sticky' => '',
			'mobile' => '',
			'mobile-sticky' => '',
		),
	);

	if ($layoutID = mfn_layout_ID()) {

		// custom layout | layout options

		$logo['default']['main'] = get_post_meta($layoutID, 'mfn-post-logo-img', true);
		$logo['default']['sticky'] = get_post_meta($layoutID, 'mfn-post-sticky-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-sticky-logo-img', true) : $logo['default']['main'];
		$logo['default']['mobile'] = get_post_meta($layoutID, 'mfn-post-responsive-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-responsive-logo-img', true) : $logo['default']['main'];
		$logo['default']['mobile-sticky'] = get_post_meta($layoutID, 'mfn-post-responsive-sticky-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-responsive-sticky-logo-img', true) : $logo['default']['main'];

		$logo['retina']['main'] = get_post_meta($layoutID, 'mfn-post-retina-logo-img', true);
		$logo['retina']['sticky'] = get_post_meta($layoutID, 'mfn-post-sticky-retina-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-sticky-retina-logo-img', true) : $logo['retina']['main'];
		$logo['retina']['mobile'] = get_post_meta($layoutID, 'mfn-post-responsive-retina-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-responsive-retina-logo-img', true) : $logo['retina']['main'];
		$logo['retina']['mobile-sticky'] = get_post_meta($layoutID, 'mfn-post-responsive-sticky-retina-logo-img', true) ? get_post_meta($layoutID, 'mfn-post-responsive-sticky-retina-logo-img', true) : $logo['retina']['main'];

	} else {

		// default | theme options

		$logo['default']['main'] = mfn_opts_get('logo-img', get_theme_file_uri('/images/logo/logo.png'));
		$logo['default']['sticky'] = mfn_opts_get('sticky-logo-img') ? mfn_opts_get('sticky-logo-img') : $logo['default']['main'];
		$logo['default']['mobile'] = mfn_opts_get('responsive-logo-img') ? mfn_opts_get('responsive-logo-img') : $logo['default']['main'];
		$logo['default']['mobile-sticky'] = mfn_opts_get('responsive-sticky-logo-img') ? mfn_opts_get('responsive-sticky-logo-img') : $logo['default']['main'];

		$logo['retina']['main'] = mfn_opts_get('retina-logo-img');
		$logo['retina']['sticky'] = mfn_opts_get('sticky-retina-logo-img') ? mfn_opts_get('sticky-retina-logo-img') : $logo['retina']['main'];
		$logo['retina']['mobile'] = mfn_opts_get('responsive-retina-logo-img') ? mfn_opts_get('responsive-retina-logo-img') : $logo['retina']['main'];
		$logo['retina']['mobile-sticky'] = mfn_opts_get('responsive-sticky-retina-logo-img') ? mfn_opts_get('responsive-sticky-retina-logo-img') : $logo['retina']['main'];
	}

	// SVG width

	if ($width = mfn_opts_get('logo-width')) {
		$svg = ' svg';
		$width_escaped = 'width="'. esc_attr($width) .'"';
	} else {
		$svg = false;
		$width_escaped = false;
	}

	// output -----

	if( mfn_opts_get('logo-img') || $logo_text || ( ! has_custom_logo() ) ){

		echo wp_kses($logo_before, $logo_allowed_html);

		if ($logo_text) {

			echo esc_html($logo_text);

		} else {

			foreach ($logo['default'] as $logo_key => $logo_src) {
				// This variable has been safely escaped above
				echo '<img class="logo-'. esc_attr($logo_key) .' scale-with-grid'. esc_attr($svg) .'" src="'. esc_url($logo_src) .'" data-retina="'. esc_url($logo['retina'][$logo_key]) .'" data-height="'. esc_attr(mfn_get_attachment_data($logo_src, 'height')) .'" alt="'. esc_attr(mfn_get_attachment_data($logo_src, 'alt')) .'" data-no-retina '. $width_escaped .'/>';
			}

		}

		echo wp_kses($logo_after, $logo_allowed_html);

	} else {

		the_custom_logo();

	}

echo '</div>';
