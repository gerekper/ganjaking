<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-slide-tab">
	<div class="ls-slide-tab">
		<span class="ls-slide-counter"></span>
		<span class="ls-slide-hidden dashicons dashicons-hidden"></span>
		<span class="ls-slide-actions dashicons dashicons-arrow-down-alt2"></span>
		<div class="ls-slide-preview">
			<span><?php _e('No Preview', 'LayerSlider') ?></span>
		</div>
		<div class="ls-slide-name">
			<input type="text" placeholder="<?php _e('Type slide name here', 'LayerSlider') ?>">
		</div>
		<ul class="ls-slide-actions-sheet ls-hidden">
			<li class="ls-slide-duplicate">
				<span>
					<i class="dashicons dashicons-admin-page"></i>
					<?php _e('Duplicate', 'LayerSlider') ?>
				</span>
			</li>
			<li class="ls-slide-visibility">
				<span>
					<i class="dashicons dashicons-hidden"></i>
					<?php _e('Hide', 'LayerSlider') ?>
				</span>
				<span>
					<i class="dashicons dashicons-visibility"></i>
					<?php _e('Unhide', 'LayerSlider') ?>
				</span>
			</li>
			<li class="ls-slide-remove">
				<span>
					<i class="dashicons dashicons-trash"></i>
					<?php _e('Remove', 'LayerSlider') ?>
				</span>
			</li>
		</ul>
	</div>
</script>