<?php
global $post;

$settings = get_post_meta($post->ID, '_wccc_settings', true);
if (!$settings) {
	$settings = array('location' => 'single-product', 'hook' => 'woocommerce_template_single_excerpt');
}

$locations = apply_filters('wc_conditional_content_get_locations', array());

if (!isset($settings['type'])) {
	$settings['type'] = 'single';
}

?>

<h4><?php _e('Show Content', 'wc_conditional_content'); ?></h4>

<label for="wccc_settings_type"><?php _e('Type:', 'wc_conditional_content'); ?></label><br />
<select name="wccc_settings_type" id="wccc_settings_type">
	<option value="single" <?php selected($settings['type'], 'single'); ?>><?php _e('Once', 'wc_conditional_content'); ?></option>
	<option value="loop" <?php selected($settings['type'], 'loop'); ?>><?php _e('In Loop', 'wc_conditional_content'); ?></option>
</select>
<p class="description"><?php _e('Is this content displayed in a loop?.', 'wc_conditional_content'); ?></p>

<label for="wccc_settings_location"><?php _e('Location:', 'wc_conditional_content'); ?></label><br />
<select name="wccc_settings_location" id="wccc_settings_location">

	<?php foreach ($locations as $location => $data) : ?>
		<optgroup label="<?php echo $data['title']; ?>">
			<?php foreach ($data['hooks'] as $hook_id => $hook) : ?>
				<option <?php selected($location . ':' . $hook_id, $settings['location'] . ':' . $settings['hook']) ?> value="<?php echo $location . ':' . $hook_id ?>"><?php echo $hook['title']; ?></option>
			<?php endforeach; ?>
		</optgroup>
	<?php endforeach; ?>

	<optgroup label="<?php _e('Custom', 'wc_conditional_content'); ?>">
		<option <?php selected('custom', $settings['hook']); ?> value="custom:custom"><?php _e('Custom Action', 'wc_conditional_content'); ?></option>
	</optgroup>
</select>
<p class="description"><?php _e('Choose where you would like this content to be displayed.', 'wc_conditional_content'); ?></p>




<div class="wccc-settings-custom">

	<label for="wccc_settings_location_custom_hook"><?php _e('Action Hook Name', 'wc_conditional_content'); ?></label>
	<br /><input type="text" name="wccc_settings_location_custom_hook" value="<?php echo esc_attr($settings['hook'] == 'custom' ? $settings['custom_hook'] : ''); ?>" />
	<br /><label for="wccc_settings_location_custom_priority"><?php _e('Priority', 'wc_conditional_content'); ?></label>
	<br /><input type="text" name="wccc_settings_location_custom_priority" value="<?php echo esc_attr($settings['hook'] == 'custom' ? $settings['custom_priority'] : ''); ?>" />

	<p class="description"><?php printf(_e('Enter the name and priority of an action where this content should be output.  See the <a href="%s">woocommerce hooks and filter reference</a> for a full list of all template actions and filters.', 'wc_conditional_content'), 'http://docs.woothemes.com/document/hooks/'); ?></p>


</div>
