<?php
/**
 * Responsive | Side Slide
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

// responsive | mobile | options

$menu_pos = 'right';
if (in_array(mfn_opts_get('responsive-header-minimal'), array( 'ml-ll','ml-lc','ml-lr' ))) {
	$menu_pos = 'left';
}

if( is_rtl() || isset($_GET['mfn-rtl']) ){
	$menu_pos = 'left';
	if (in_array(mfn_opts_get('responsive-header-minimal'), array( 'ml-ll','ml-lc','ml-lr' ))) {
		$menu_pos = 'right';
	}
}

$side_class = $menu_pos;

// background color | brightness

$side_class .= ' '. mfn_brightness(mfn_opts_get('background-side-menu', '#191919'));

// side slide | hide

$ss_hide = mfn_opts_get('responsive-side-slide');
if (is_array($ss_hide)) {
	if (isset($ss_hide['button'])) {
		$side_class .= ' hide-button';
	}
	if (isset($ss_hide['icons'])) {
		$side_class .= ' hide-icons';
	}
	if (isset($ss_hide['social'])) {
		$side_class .= ' hide-social';
	}
}

echo '<div id="Side_slide" class="'. esc_attr($side_class) .'" data-width="'. esc_attr(mfn_opts_get('responsive-side-slide-width', 250)) .'">';

// close button

echo '<div class="close-wrapper">';
	echo '<a href="#" class="close"><i class="icon-cancel-fine"></i></a>';
echo '</div>';

// extras

echo '<div class="extras">';

	// action button

	if ($action_link = mfn_opts_get('header-action-link')) {
		$action_options = mfn_opts_get('header-action-target');

		if (isset($action_options['target'])) {
			$action_target = 'target="_blank"';
		} else {
			$action_target = false;
		}

		if (isset($action_options['scroll'])) {
			$action_class = ' scroll';
		} else {
			$action_class = false;
		}

		echo '<a href="'. esc_url($action_link) .'" class="action_button'. esc_attr($action_class) .'" '. wp_kses_data($action_target) .'>'. wp_kses(mfn_opts_get('header-action-title'), mfn_allowed_html('button')) .'</a>';
	}

	// icons

	echo '<div class="extras-wrapper">';

		// WooCommerce cart

		global $woocommerce;
		$show_cart = trim(mfn_opts_get('shop-cart'));

		if ($woocommerce && $show_cart) {
			echo '<a class="icon cart" id="slide-cart" href="'. esc_url(wc_get_cart_url()) .'"><i class="'. esc_attr($show_cart) .'"></i><span>'. esc_html($woocommerce->cart->cart_contents_count) .'</span></a>';
		}

		// search

		if (mfn_opts_get('header-search')) {
			echo '<a class="icon search" href="#"><i class="icon-search-fine"></i></a>';
		}

		// languages menu

		if (has_nav_menu('lang-menu')) {

			// custom languages menu
			echo '<a class="lang-active" href="#">'. esc_html(mfn_get_menu_name('lang-menu')) .'<i class="icon-down-open-mini"></i></a>';

		} elseif (function_exists('icl_get_languages')) {

			// WPML | custom languages menu

			$lang_args = '';
			$lang_options = mfn_opts_get('header-wpml-options');
			$wmpl_flags = mfn_opts_get('header-wpml');

			if (isset($lang_options['link-to-home'])) {
				$lang_args .= 'skip_missing=0';
			} else {
				$lang_args .= 'skip_missing=1';
			}
			$languages = icl_get_languages($lang_args);

			if (is_array($languages) && $wmpl_flags != 'hide') {
				$active_lang = false;
				foreach ($languages as $lang_k=>$lang) {
					if ($lang['active']) {
						$active_lang = $lang;
					}
				}

				if ($active_lang) {

					echo '<a class="lang-active" href="#">';
						if ($wmpl_flags == 'dropdown-name') {
							echo esc_html($active_lang['native_name']);
						} elseif ($wmpl_flags == 'horizontal-code') {
							echo esc_html(strtoupper($active_lang['language_code']));
						} else {
							echo '<img src="'. esc_url($active_lang['country_flag_url']) .'" alt="'. esc_attr($active_lang['translated_name']) .'" width="18" height="12"/>';
						}
						if (count($languages) > 1) {
							echo '<i class="icon-down-open-mini"></i>';
						}
					echo '</a>';
				}
			}
		}

	echo '</div>';

echo '</div>';

// Search | wrapper

if (mfn_opts_get('header-search')) {
	echo '<div class="search-wrapper">';
		echo '<form id="side-form" method="get" action="'. esc_url(home_url('/')) .'">';

			if (mfn_opts_get('header-search') == 'shop') {
				echo '<input type="hidden" name="post_type" value="product" />';
			}

			$translate['search-placeholder'] = mfn_opts_get('translate') ? mfn_opts_get('translate-search-placeholder', 'Enter your search') : __('Enter your search', 'betheme');
			echo '<input type="text" class="field" name="s" placeholder="'. esc_attr($translate['search-placeholder']) .'" />';
			echo '<input type="submit" class="display-none" value="" />';

			do_action('wpml_add_language_form_field');

			echo '<a class="submit" href="#"><i class="icon-search-fine"></i></a>';

		echo '</form>';
	echo '</div>';
}

// languages menu | wrapper

echo '<div class="lang-wrapper">';

	// languages menu
	if (has_nav_menu('lang-menu')) {

		// custom languages menu
		mfn_wp_lang_menu();

	} elseif (function_exists('icl_get_languages')) {

		// WPML | custom languages menu

		if (count($languages) > 1) {

			echo '<ul class="wpml-lang">';
				foreach ($languages as $lang) {
					echo '<li><a href="'. esc_url($lang['url']) .'" class="'. ($lang['active'] ? 'active' : false)  .'">';
						if ($wmpl_flags == 'dropdown-name') {
							echo esc_html($lang['native_name']);
						} elseif ($wmpl_flags == 'horizontal-code') {
							echo esc_html(strtoupper($lang['language_code']));
						} else {
							echo '<img src="'. esc_url($lang['country_flag_url']) .'" alt="'. esc_attr($lang['translated_name']) .'" width="18" height="12"/>';
						}
					echo '</a></li>';
				}
			echo '</ul>';

		} else {

			$translate['wpml-no'] = mfn_opts_get('translate') ? mfn_opts_get('translate-wpml-no', 'No translations available for this page') : __('No translations available for this page', 'betheme');
			echo '<ul class="wpml-no"><li><a href="#">'. esc_html($translate['wpml-no']) .'</a></li></ul>';

		}
	}

echo '</div>';

// main menu | jQuery content - DO NOT DELETE

echo '<div class="menu_wrapper"></div>';

// social

$action_bar = mfn_opts_get('action-bar');
if( isset($action_bar['side-slide']) ){
	get_template_part('includes/include', 'slogan');
}

if (has_nav_menu('social-menu')) {
	mfn_wp_social_menu();
} else {
	get_template_part('includes/include', 'social');
}

echo '</div>';

// #body_overlay

echo '<div id="body_overlay"></div>';
