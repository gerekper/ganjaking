<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PieChart $widget */

$settings = array(
	'widget_title' => '',
	'graph_value' => '50',
	'label_value' => '',
	'chart_units' => '',
	'graph_size' => '125',
	'graph_thickness' => '5',
	'line_cap' => 'square',
	'circle_border_color' => '#f7f7f7',
	'circle_arc_color_type' => 'yes',
	'circle_arc_bg' => '#5a81b7',
	'circle_arc_gradient1' => '#5a81b7',
	'circle_arc_gradient2' => '#66afc0',
	'static_label_enable' => false,
	'static_label_value' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_pie_chart_wrapper',
	$settings['static_label_enable'] ? 'has_static_label' : '',
));

$graph_value = ((int)$settings['graph_value']['size'])/100;

if (!empty($settings['label_value'])) {
	$label_value = $settings['label_value'];
} else {
	$label_value = $settings['graph_value']['size'];
}

?>
	<div <?php echo ''.$widget->get_render_attribute_string('wrapper') ?>>
		<div
			  class="gt3_elementor_pie_chart"
			  data-value="<?php echo ''.$graph_value; ?>"
			  data-label-value="<?php echo (int)$label_value; ?>"
			  data-units="<?php echo esc_attr($settings['chart_units']); ?>"
			  data-size="<?php echo (int)($settings['graph_size']['size']); ?>"
			  data-thickness="<?php echo (int)($settings['graph_thickness']['size']); ?>"
			  data-line-cap="<?php echo esc_attr($settings['line_cap']); ?>"
			  data-fill="{
			  	<?php if ($settings['circle_arc_color_type'] == 'yes') { ?>
					&quot;gradient&quot;: [&quot;<?php echo esc_attr($settings['circle_arc_gradient1']); ?>&quot;, &quot;<?php echo esc_attr($settings['circle_arc_gradient2']); ?>&quot;]
				<?php } else { ?>
					&quot;color&quot;: &quot;<?php echo esc_attr($settings['circle_arc_bg']); ?>&quot;
				<?php } ?>
			  }"
			  data-empty-fill="<?php echo esc_attr($settings['circle_border_color']); ?>"
			>
			<strong class="element_typography"><?php echo (int)$label_value . esc_attr($settings['chart_units']) ?></strong>
			<?php
				if (!empty($settings['static_label_value'])) {
					echo '<div class="static_label_text element_typography">'.esc_attr($settings['static_label_value']).'</div>';
				}
			?>
		</div>
		<?php
			if (strlen($settings['widget_title']) > 0 && !empty($settings['widget_title'])) {
				echo '<div class="gt3_elementor_pie_chart_text">' . esc_html($settings['widget_title']) . '</div>';
			}
		?>
  </div>
<?php





