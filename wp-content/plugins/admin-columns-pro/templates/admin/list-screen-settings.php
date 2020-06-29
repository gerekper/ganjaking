<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AC\Form\Element\Select; ?>
<section class="ac-setbox ac-ls-settings ac-section -closable" data-section="ls-settings">
	<header class="ac-section__header">
		<div class="ac-setbox__header__title"><?= __( 'Settings', 'codepress-admin-columns' ); ?>
			<small>(<?= __( 'optional', 'codepress-admin-columns' ); ?>)</small>
		</div>
	</header>
	<form class="ac-setbox__fields" method="post">

		<div class="ac-setbox__row">
			<div class="ac-setbox__row__th">
				<label><?= __( 'Conditionals', 'codepress-admin-columns' ); ?></label>
				<small><?= __( 'Make this column set available only for specific users or roles.', 'codepress-admin-columns' ); ?></small>
			</div>
			<div class="ac-setbox__row__fields">
				<div class="ac-setbox__row__fields__inner">
					<fieldset>
						<div class="row roles">
							<label for="layout-roles-<?php echo $this->id; ?>" class="screen-reader-text">
								<?php _e( 'Roles', 'codepress-admin-columns' ); ?>
								<span>(<?php _e( 'optional', 'codepress-admin-columns' ); ?>)</span>
							</label>

							<?php echo $this->select_roles; ?>

						</div>
						<div class="row users">
							<label for="layout-users-<?php echo $this->id; ?>" class="screen-reader-text">
								<?php _e( 'Users' ); ?>
								<span>(<?php _e( 'optional', 'codepress-admin-columns' ); ?>)</span>
							</label>

							<?php echo $this->select_users; ?>
						</div>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="ac-setbox__row" id="hide-on-screen">
			<div class="ac-setbox__row__th">
				<label><?= __( 'Hide on screen', 'codepress-admin-columns' ); ?></label>
				<small><?= __( 'Select items to hide from the list table screen.', 'codepress-admin-columns' ); ?></small>
			</div>
			<div class="ac-setbox__row__fields">
				<div class="ac-setbox__row__fields__inner">
					<div class="checkbox-labels checkbox-labels vertical">

						<?= $this->hide_on_screen; ?>

					</div>
				</div>
			</div>
		</div>

		<div class="ac-setbox__row">
			<div class="ac-setbox__row__th">
				<label><?= __( 'Preferences', 'codepress-admin-columns' ); ?></label>
				<small><?= __( 'Set default settings that users will see when they visit the list table.', 'codepress-admin-columns' ); ?></small>
			</div>
			<div class="ac-setbox__row__fields -has-subsettings">
				<?php
				$pref_hs = isset( $this->preferences['horizontal_scrolling'] ) ? $this->preferences['horizontal_scrolling'] : 'off';
				?>
				<div class="ac-setbox__row -sub -horizontal-scrolling">
					<div class="ac-setbox__row__th">
						<label><?= __( 'Horizontal Scrolling', 'codepress-admin-columns' ); ?></label>
						<?php echo $this->tooltip_hs->get_label(); ?>
						<?php echo $this->tooltip_hs->get_instructions(); ?>
					</div>
					<div class="ac-setbox__row__fields">
						<div class="ac-setbox__row__fields__inner">
							<div class="radio-labels radio-labels">
								<label class="ac-setting-input_filter"><input name="settings[horizontal_scrolling]" type="radio" value="on" <?php checked( $pref_hs, 'on' ); ?>><?= __( 'Yes' ); ?></label>
								<label class="ac-setting-input_filter"><input name="settings[horizontal_scrolling]" type="radio" value="off" <?php checked( $pref_hs, 'off' ); ?>><?= __( 'No' ); ?></label>
							</div>
						</div>
					</div>
				</div>
				<?php
				$pref_sorting = isset( $this->preferences['sorting'] ) ? $this->preferences['sorting'] : 0;
				$pref_order = isset( $this->preferences['sorting_order'] ) ? $this->preferences['sorting_order'] : 'asc';
				?>
				<div class="ac-setbox__row -sub -sorting" data-setting="sorting-preference">
					<div class="ac-setbox__row__th">
						<label><?= __( 'Sorting', 'codepress-admin-columns' ); ?></label>
					</div>
					<div class="ac-setbox__row__fields">
						<div class="ac-setbox__row__fields__inner">
							<div class="radio-labels radio-labels">
								<?php
								$select = new Select( 'settings[sorting]', [
									0 => __( 'Default' ),
								] );
								echo $select->set_attribute( 'data-sorting', $pref_sorting );

								$select = new Select( 'settings[sorting_order]', [
									'asc'  => __( 'Ascending' ),
									'desc' => __( 'Descending' ),
								] );
								echo $select->set_class( 'sorting_order' )->set_value( $pref_order );

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>
</section>