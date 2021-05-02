<?php

use AC\Admin\Tooltip;
use AC\View;

?>
<div class="ac-segments -admin">
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
		<div class="ac-segments__list__label"><?= __( 'Public', 'codepress-admin-columns' ); ?>
		</div>
		<div class="ac-segments__list__items"></div>
	</div>

	<?php
	$content = ( new View() )->set_template( 'table/tooltip-saved-filters' )->render();

	$tooltip = new Tooltip( 'filtered_segments', [
		'content'    => $content,
		'link_label' => __( 'Instructions', 'codepress-admin-columns' ),
		'title'      => __( 'Instructions', 'codepress-admin-columns' ),
		'position'   => 'right_bottom',
	] );

	?>
	<div class="ac-segments__instructions">
		<?php
		echo $tooltip->get_label();
		echo $tooltip->get_instructions();
		?>
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
					<br><label><input type="checkbox" name="global"/><?= __( 'Make available to all users', 'codepress-admin-columns' ); ?></label>
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