<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="ls-layer-item-template">
	<li>
		<span class="ls-sublayer-sortable-handle dashicons dashicons-menu"></span>
		<span class="ls-sublayer-controls">
			<span class="ls-icon-eye dashicons dashicons-visibility" data-help="<?php _e('Toggle layer visibility.', 'LayerSlider') ?>"></span>
			<span class="ls-icon-lock dashicons dashicons-lock disabled" data-help="<?php _e('Prevent layer dragging in the editor.', 'LayerSlider') ?>"></span>
		</span>
		<div class="ls-sublayer-thumb"></div>
		<input type="text" name="subtitle" class="ls-sublayer-title" value="<?php echo sprintf(__('Layer #%d', 'LayerSlider'), '1') ?>">
		<a href="#" title="<?php _e('Duplicate this layer', 'LayerSlider') ?>" class="dashicons dashicons-admin-page duplicate"></a>
		<a href="#" title="<?php _e('Remove this layer', 'LayerSlider') ?>" class="dashicons dashicons-trash remove"></a>
	</li>
</script>
