<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Stripes $widget */

$settings = array(
	'item_align'	=> 'left',
	'stripes_divider'	=> 'yes',
	'stripes_enable_active_state'	=> 'yes',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$css_class = array(
	'gt3-stripes-list',
	!empty($settings['item_align']) ? 'text-align-'.esc_attr($settings['item_align']) : '',
	!empty($settings['stripes_divider']) ? 'stripes_divider' : '',
	!empty($settings['stripes_enable_active_state']) ? 'stripes_enable_active_state' : '',
	(count($settings['items']) > 1) ? 'gt3-some-stripes' : '',
);

$widget->add_render_attribute('wrapper', 'class', $css_class);
$widget->add_render_attribute('wrapper', 'data-count', count($settings['items']));

if(is_array($settings['items']) && count($settings['items'])) {
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php foreach($settings['items'] as $index => $item) { ?>
        <div class="gt3-stripe-item">
			<div class="gt3-stripe-content">
				<?php
					/* Title */
					if (!empty($item['title'])) {
						echo '<div class="gt3-stripe-title"><h4>'.esc_html( $item['title'] ).'</h4></div>';
					}
					if (!empty($item['content']) || !empty($item['link']['url'])) {
						echo '<div class="gt3-stripe-info">';
					}
					/* Description */
					if (!empty($item['content'])) {
						echo '<div class="gt3-stripe-text">'. $item['content'] .'</div>';
					}
					/* Link */
					if (!empty($item['link']['url'])) {
						$repeater_key_link = $widget->get_repeater_key('link', 'items', $index);
						$widget->add_render_attribute($repeater_key_link, 'href', $item['link']['url']);

						$widget->add_render_attribute($repeater_key_link, 'class', 'gt3-stripe-more');

						if ($item['link']['is_external']) {
							$widget->add_render_attribute($repeater_key_link, 'target', '_blank');
						}

						if (!empty($item['link']['nofollow'])) {
							$widget->add_render_attribute($repeater_key_link, 'rel', 'nofollow');
						}
						echo '<a ' . $widget->get_render_attribute_string($repeater_key_link) . '>' . esc_html__('View  more', 'gt3_themes_core') . '</a>';
					}
					if (!empty($item['content']) || !empty($item['link']['url'])) {
						echo '</div>';
					}
				?>
			</div>
		</div>
		<?php
			/* Image Bg */
			if(!empty($item['image']['id'])) {
				$repeater_key_img = $widget->get_repeater_key('image', 'items', $index);

				$src = Utils::get_placeholder_image_src();
				if(isset($item['image']['id']) && (bool) $item['image']['id']) {
					$image = wp_get_attachment_image_src($item['image']['id'], 'single-post-thumbnail');
					if($image) {
						$src = $image[0];
					}
				}
				$widget->add_render_attribute($repeater_key_img, 'style', array(
					sprintf('background-image: url(%s);', esc_url($src))
				));
				echo '<div class="gt3-stripe-bg"' . $widget->get_render_attribute_string($repeater_key_img) . '></div>';
			}
		?>
		<?php } ?>
	</div>
<?php
}
