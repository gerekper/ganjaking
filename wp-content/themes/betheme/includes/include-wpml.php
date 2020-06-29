<?php
/**
 * WPML Custom Switcher
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (has_nav_menu('lang-menu')) {

	// custom languages menu

	echo '<div class="wpml-languages custom">';
		echo '<a class="active" href="#">'. esc_html(mfn_get_menu_name('lang-menu')) .'<i class="icon-down-open-mini"></i></a>';
		mfn_wp_lang_menu();
	echo '</div>';

} elseif (function_exists('icl_get_languages')) {

	// WPML - Custom Languages Menu

	$lang_args = '';
	$lang_options = mfn_opts_get('header-wpml-options');
	$wmpl_flags = mfn_opts_get('header-wpml');

	if (isset($lang_options['link-to-home'])) {
		$lang_args .= 'skip_missing=0';
	} else {
		$lang_args .= 'skip_missing=1';
	}
	$languages = icl_get_languages($lang_args);

	if (($wmpl_flags != 'hide') && $languages && is_array($languages)) {
		if (! $wmpl_flags || $wmpl_flags == 'dropdown-name') {

			// dropdown

			$active_lang = false;
			foreach ($languages as $lang_k=>$lang) {
				if ($lang['active']) {
					$active_lang = $lang;
					unset($languages[$lang_k]);
				}
			}

			// disabled

			if (count($languages)) {
				$lang_status = 'enabled';
			} else {
				$lang_status = 'disabled';
			}

			if ($active_lang) {

				$translate['wpml-no'] = mfn_opts_get('translate') ? mfn_opts_get('translate-wpml-no', 'No translations available for this page') : __('No translations available for this page', 'betheme');

				echo '<div class="wpml-languages '. esc_attr($lang_status) .'">';

					echo '<a class="active tooltip" ontouchstart="this.classList.toggle(\'hover\');" data-tooltip="'. esc_html($translate['wpml-no']) .'">';

						if ($wmpl_flags == "dropdown-name") {
							echo esc_html($active_lang['native_name']);
						} else {
							echo '<img src="'. esc_url($active_lang['country_flag_url']) .'" alt="'. esc_attr($active_lang['translated_name']) .'" width="18" height="12"/>';
						}

						if (count($languages)) {
							echo '<i class="icon-down-open-mini"></i>';
						}

					echo '</a>';

					if (count($languages)) {
						echo '<ul class="wpml-lang-dropdown">';
							foreach ($languages as $lang) {
								if ($wmpl_flags == 'dropdown-name') {
									echo '<li><a href="'. esc_url($lang['url']) .'">'. esc_html($lang['native_name']) .'</a></li>';
								} else {
									echo '<li><a href="'. esc_url($lang['url']) .'"><img src="'. esc_url($lang['country_flag_url']) .'" alt="'. esc_attr($lang['translated_name']) .'" width="18" height="12"/></a></li>';
								}
							}
						echo '</ul>';
					}

				echo '</div>';
			}
		} else {

			// horizontal

			echo '<div class="wpml-languages horizontal">';
				echo '<ul>';
					foreach ($languages as $lang) {
						if ($lang['active']) {
							$lang_class = 'lang-active';
						} else {
							$lang_class = false;
						}

						if ($wmpl_flags == 'horizontal-code') {
							echo '<li class="'. esc_attr($lang_class) .'"><a href="'. esc_url($lang['url']) .'">'. esc_html(strtoupper($lang['language_code'])) .'</a></li>';
						} else {
							echo '<li class="'. esc_attr($lang_class) .'"><a href="'. esc_url($lang['url']) .'"><img src="'. esc_url($lang['country_flag_url']) .'" alt="'. esc_attr($lang['translated_name']) .'" width="18" height="12"/></a></li>';
						}
					}
				echo '</ul>';
			echo '</div>';
		}
	}
}
