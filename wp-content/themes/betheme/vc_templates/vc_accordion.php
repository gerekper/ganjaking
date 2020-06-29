<?php

$title = $interval = $el_class = $collapsible = $active_tab = '';

extract(shortcode_atts(array(
	'title' => '',
	'interval' => 0,
	'el_class' => '',
	'collapsible'	=> 'no',
	'active_tab' => '1'
), $atts));

$el_class = $this->getExtraClass($el_class);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'accordion wpb_content_element ' . $el_class . ' not-column-inherit', $this->settings['base'], $atts);

$class = '';
if ($collapsible == 'yes') {
	$class .= ' toggle';
}

// output -----

echo '<div class="'. esc_attr($css_class) .'">';

	if ($title) {
		echo '<h4 class="title">'. esc_html($title) .'</h4>';
	}

	echo '<div class="mfn-acc accordion_wrapper'. esc_attr($class) .'" data-active-tab="'. esc_attr($active_tab) .'">';
		echo wpb_js_remove_wpautop($content);
	echo '</div>';

echo '</div>';
