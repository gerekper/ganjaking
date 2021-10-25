<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Divider $widget */


$settings = array(
	'typed_text'     => array(
		array(
			'string' => esc_html__('Type out sentences line 1', 'gt3_themes_core'),
		),
		array(
			'string' => esc_html__('Type out sentences line 2', 'gt3_themes_core'),
		),
		array(
			'string' => esc_html__('Type out sentences line 3', 'gt3_themes_core'),
		),
	),
	'prefix_text'    => '',
	'suffix_text'    => '',
	'typeSpeed'      => 40,
	'startDelay'     => 0,
	'backSpeed'      => 10,
	'smartBackspace' => 'yes',
	'backDelay'      => 700,
	'loop'           => 'yes',
	'loopCount'      => 0,
	'showCursor'     => 'yes',
	'cursorChar'     => '|',
	'fadeOut'        => 1,
);

$settings = wp_parse_args($this->get_settings(), $settings);

$strings = array();
if(is_array($settings['typed_text']) && count($settings['typed_text'])) {
	foreach($settings['typed_text'] as $typed) {
		$strings[] = str_replace(PHP_EOL, '<br/>', $typed['string']);
	}
}

$options = array(
	'id'      => '#typed_'.$this->get_id(),
	'strings' => $strings,

	'typeSpeed'      => intval($settings['typeSpeed']),
	'startDelay'     => intval($settings['startDelay']),
	'backSpeed'      => intval($settings['backSpeed']),
	'smartBackspace' => (bool) $settings['smartBackspace'],
	'backDelay'      => intval($settings['backDelay']),
	'loop'           => (bool) $settings['loop'],
	'loopCount'      => intval($settings['loopCount']),
	'showCursor'     => (bool) $settings['showCursor'],
	'cursorChar'     => $settings['cursorChar'],
	'fadeOut'        => (bool) $settings['fadeOut'],
	'fadeOutDelay'   => intval($settings['backDelay']),
);

$this->add_render_attribute('wrapper', 'class', 'gt3_typed_widget');

?>
	<div <?php $this->print_render_attribute_string('wrapper') ?>>
		<?php
		if(!empty($settings['prefix_text'])) {
			?>
			<span class="typing-effect-prefix"><?php echo esc_html($settings['prefix_text']); ?></span>
		<?php
		}
		?>
		<span class="typing-effect-strings" id="typed_<?=$this->get_id();?>"></span>
		<?php
		if(!empty($settings['suffix_text'])) {
			?>
			<span class="typing-effect-suffix"><?php echo esc_html($settings['suffix_text']); ?></span>
		<?php
		}
		?>
	</div>
<?php

$widget->print_data_settings($options);
