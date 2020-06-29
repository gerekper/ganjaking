<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-revisions-options">
	<div id="ls-revisions-modal-window">
		<h1 class="kmw-modal-title"><?php _e('Revisions Preferences', 'LayerSlider') ?></h1>
		<form method="post">
			<?php wp_nonce_field('ls-save-revisions-options'); ?>
			<input type="hidden" name="ls-revisions-options" value="1">
			<table>
				<tr>
					<td><input type="checkbox" name="ls-revisions-enabled" class="hero" data-warning="<?php _e('Disabling Slider Revisions will also remove all revisions saved so far. Are you sure you want to continue?', 'LayerSlider') ?>" <?php echo LS_Revisions::$enabled ? 'checked' : '' ?>></td>
					<td><?php _e('Enable Revisions', 'LayerSlider') ?></td>
				</tr>
			</table>


			<div>
				<h2 class="ls-revisions-h2"><?php _e('Update Frequency', 'LayerSlider') ?></h2>
				<?php echo sprintf(__('Limit the total number of revisions per slider to %s.', 'LayerSlider'), '<input type="number" name="ls-revisions-limit" min="2" max="500" value="'.LS_Revisions::$limit.'">' ) ?> <br>
				<?php echo sprintf(__('Wait at least %s minutes between edits before adding a new revision.', 'LayerSlider'), '<input type="number" name="ls-revisions-interval" min="0" max="500" value="'.LS_Revisions::$interval.'">') ?>
			</div>

			<div class="ls-notification-info">
				<i class="dashicons dashicons-info"></i>
				<?php _e('Slider Revisions also stores the undo/redo controls. There is no reason using very frequent saves since you will be able to undo the changes in-between.', 'LayerSlider') ?>
			</div>

			<div class="ls-center">
				<button class="button button-primary button-hero"><?php _e('Update Revisions Preferences', 'LayerSlider') ?></button>
			</div>
		</form>
	</div>
</script>