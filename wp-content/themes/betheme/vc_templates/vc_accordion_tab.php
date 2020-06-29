<?php

$title = '';

extract(shortcode_atts(array(
	'title' => __("Section", "js_composer")
), $atts));

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion_section group', $this->settings ['base'], $atts);

// output -----

echo '<div class="question '. esc_attr($css_class) .'">';
	echo '<div class="title"><i class="icon-plus acc-icon-plus"></i><i class="icon-minus acc-icon-minus"></i>'. esc_html($title) .'</div>';
	echo '<div class="answer">';
		if($content == '' || $content == ' '){
			echo esc_html__("Empty section. Edit page to add content here.", "js_composer");
		} else {
			echo wpb_js_remove_wpautop($content);
		}
	echo '</div>';
echo '</div>';
