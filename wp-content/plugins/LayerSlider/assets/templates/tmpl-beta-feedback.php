<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<?php if(strpos(LS_PLUGIN_VERSION, 'b') !== false) : ?>
<div class="ls-version-number">
	<?php echo sprintf(__('Using beta version (%s)', 'LayerSlider'), LS_PLUGIN_VERSION) ?>
	<a href="mailto:support@kreaturamedia.com?subject=LayerSlider WP (v<?php echo LS_PLUGIN_VERSION ?>) Feedback"><?php _e('Send feedback', 'LayerSlider') ?></a>
</div>
<?php endif; ?>
