<?php
/**
 * Social Icons
 *
 * @package Betheme
 * @author Muffin group
 * @link http://muffingroup.com
 */

$attr = array();
$social_attr = mfn_opts_get('social-attr');

if (is_array($social_attr)) {
	if (isset($social_attr['blank'])) {
		$attr[] = 'target="_blank"';
	}
	if (isset($social_attr['nofollow'])) {
		$attr[] = 'rel="nofollow"';
	}
}

$attr = implode(' ', $attr);

echo '<ul class="social">';

	if (mfn_opts_get('social-skype')) {
		echo '<li class="skype"><a '. $attr .' href="'. esc_attr(mfn_opts_get('social-skype')) .'" title="Skype"><i class="icon-skype"></i></a></li>';
	}
	if (mfn_opts_get('social-whatsapp')) {
		echo '<li class="whatsapp"><a '. $attr .' href="'. esc_attr(mfn_opts_get('social-whatsapp')) .'" title="WhatsApp"><i class="icon-whatsapp"></i></a></li>';
	}
	if (mfn_opts_get('social-facebook')) {
		echo '<li class="facebook"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-facebook')) .'" title="Facebook"><i class="icon-facebook"></i></a></li>';
	}
	if (mfn_opts_get('social-twitter')) {
		echo '<li class="twitter"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-twitter')) .'" title="Twitter"><i class="icon-twitter"></i></a></li>';
	}
	if (mfn_opts_get('social-vimeo')) {
		echo '<li class="vimeo"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-vimeo')) .'" title="Vimeo"><i class="icon-vimeo"></i></a></li>';
	}
	if (mfn_opts_get('social-youtube')) {
		echo '<li class="youtube"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-youtube')) .'" title="YouTube"><i class="icon-play"></i></a></li>';
	}
	if (mfn_opts_get('social-flickr')) {
		echo '<li class="flickr"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-flickr')) .'" title="Flickr"><i class="icon-flickr"></i></a></li>';
	}
	if (mfn_opts_get('social-linkedin')) {
		echo '<li class="linkedin"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-linkedin')) .'" title="LinkedIn"><i class="icon-linkedin"></i></a></li>';
	}
	if (mfn_opts_get('social-pinterest')) {
		echo '<li class="pinterest"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-pinterest')) .'" title="Pinterest"><i class="icon-pinterest"></i></a></li>';
	}
	if (mfn_opts_get('social-dribbble')) {
		echo '<li class="dribbble"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-dribbble')) .'" title="Dribbble"><i class="icon-dribbble"></i></a></li>';
	}
	if (mfn_opts_get('social-instagram')) {
		echo '<li class="instagram"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-instagram')) .'" title="Instagram"><i class="icon-instagram"></i></a></li>';
	}
	if (mfn_opts_get('social-snapchat')) {
		echo '<li class="snapchat"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-snapchat')) .'" title="Snapchat"><i class="icon-snapchat"></i></a></li>';
	}
	if (mfn_opts_get('social-behance')) {
		echo '<li class="behance"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-behance')) .'" title="Behance"><i class="icon-behance"></i></a></li>';
	}
	if (mfn_opts_get('social-tumblr')) {
		echo '<li class="tumblr"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-tumblr')) .'" title="Tumblr"><i class="icon-tumblr"></i></a></li>';
	}
	if (mfn_opts_get('social-tripadvisor')) {
		echo '<li class="tripadvisor"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-tripadvisor')) .'" title="TripAdvisor"><i class="icon-tripadvisor"></i></a></li>';
	}
	if (mfn_opts_get('social-vkontakte')) {
		echo '<li class="vkontakte"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-vkontakte')) .'" title="VKontakte"><i class="icon-vkontakte"></i></a></li>';
	}
	if (mfn_opts_get('social-viadeo')) {
		echo '<li class="viadeo"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-viadeo')) .'" title="Viadeo"><i class="icon-viadeo"></i></a></li>';
	}
	if (mfn_opts_get('social-xing')) {
		echo '<li class="xing"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-xing')) .'" title="Xing"><i class="icon-xing"></i></a></li>';
	}
	if (mfn_opts_get('social-rss')) {
		echo '<li class="rss"><a '. $attr .' href="'. esc_url(get_bloginfo('rss2_url')) .'" title="RSS"><i class="icon-rss"></i></a></li>';
	}

	if (mfn_opts_get('social-custom-icon') &&  mfn_opts_get('social-custom-link')) {
		$title = mfn_opts_get('social-custom-title');
		echo '<li class="custom"><a '. $attr .' href="'. esc_url(mfn_opts_get('social-custom-link')) .'" title="'. esc_attr($title) .'"><i class="'. esc_attr(mfn_opts_get('social-custom-icon')) .'"></i></a></li>';
	}

echo '</ul>';
