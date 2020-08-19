<div class="ac-segments -admin" data-initial="<?= esc_attr( $this->current_segment_id ); ?>">
	<div class="ac-segments__create">
		<span class="cpac_icons-segment"></span>
		<button class="button button-primary">
			<?php _e( 'Save Filters', 'codepress-admin-columns' ); ?>
		</button>
	</div>
	<div class="ac-segments__list -personal">
		<div class="ac-segments__list__items"></div>
	</div>
	<div class="ac-segments__list -global">
		<div class="ac-segments__list__label">Public
			<span class="ac-segments__list__label__icon dashicons dashicons-info" data-ac-tip="<?= __( 'Available to all users', 'codepress-admin-columns' ); ?>"></span>
		</div>
		<div class="ac-segments__list__items"></div>
	</div>
	<div class="ac-segments__instructions" rel="pointer-segments">
		<?php _e( 'Instructions', 'codepress-admin-columns' ); ?>
		<div id="ac-segments-instructions" style="display:none;">
			<h3><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></h3>
			<p>
				<?php _e( 'Save a set of custom smart filters for later use.', 'codepress-admin-columns' ); ?>
			</p>
			<p>
				<?php _e( 'This can be useful to group your WordPress content based on different criteria.', 'codepress-admin-columns' ); ?>&nbsp;<?php _e( 'Click on a segment to load the filtered list.', 'codepress-admin-columns' ); ?>
			</p>
		</div>
	</div>

</div>
<div class="ac-modal" id="ac-modal-create-segment">
	<div class="ac-modal__dialog -create-segment">
		<form id="frm_create_segment">
			<div class="ac-modal__dialog__header">
				<?php _e( 'Save Filters', 'codepress-admin-columns' ); ?>
				<button class="ac-modal__dialog__close">
					<span class="dashicons dashicons-no"></span>
				</button>
			</div>
			<div class="ac-modal__dialog__content">
				<label for="inp_segment_name" class="screen-reader-text"><?= __( 'Name', 'codepress-admin-columns' ); ?></label>
				<input type="text" name="name" id="inp_segment_name" required autocomplete="off" placeholder="<?= __( 'Name', 'codepress-admin-columns' ); ?>">

				<?php if ( $this->user_can_manage_segments ) : ?>
					<br><label><input type="checkbox" name="global"/> <?= __( 'Make available to all users', 'codepress-admin-columns' ); ?></label>
				<?php endif; ?>

				<div class="ac-modal__error">
				</div>
			</div>
			<div class="ac-modal__dialog__footer">
				<div class="ac-modal__loading">
					<span class="dashicons dashicons-update"></span>
				</div>
				<button class="button button" data-dismiss="modal"><?php _e( 'Cancel', 'codepress-admin-columns' ); ?></button>
				<button type="submit" class="button button-primary"><?php _e( 'Save', 'codepress-admin-columns' ); ?></button>
			</div>
		</form>
	</div>
</div>