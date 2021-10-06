<?php

use AC\View;
use ACP\RequestParser;

$form_buttons = new View( [
	'is_license_defined' => $this->is_license_defined,
] );
$form_buttons->set_template( 'admin/partials/license-form-buttons' );

/**
 * @var \ACP\Entity\License $license
 */
$license = $this->license;
?>

<form id="licence_activation" action="" method="post">
	<?php wp_nonce_field( RequestParser::NONCE_ACTION, '_acnonce' ); ?>

	<?php if ( $license ) : ?>

		<?php
		$is_expired = $license->is_expired();

		// Give auto renewal 2 extra days before marked as expired
		if ( $is_expired && $license->is_auto_renewal() && $license->get_expiry_date()->get_expired_seconds() < ( 2 * DAY_IN_SECONDS ) ) {
			$is_expired = false;
		}

		?>
		<ul class="license-info-list">
			<?php if ( $is_expired ) : ?>
				<li><span class="red dashicons dashicons-no-alt"></span> <?php _e( 'Automatic updates are disabled.', 'codepress-admin-columns' ); ?></li>
				<li><span class="red dashicons dashicons-no-alt"></span> <?php printf( __( 'License has expired on %s', 'codepress-admin-columns' ), '<strong>' . ac_format_date( 'F j, Y', $license->get_expiry_date()->get_value()->getTimestamp() ) . '</strong>' ); ?></li>
			<?php elseif ( $license->is_cancelled() ) : ?>
				<li><span class="red dashicons dashicons-no-alt"></span> <?php _e( 'Automatic updates are disabled.', 'codepress-admin-columns' ); ?></li>
				<li><span class="red dashicons dashicons-no-alt"></span> <?php _e( 'Your subscription is cancelled.', 'codepress-admin-columns' ); ?></li>
			<?php else : ?>
				<li><span class="green dashicons dashicons-yes"></span> <?php _e( 'Automatic updates are enabled.', 'codepress-admin-columns' ); ?></li>

				<?php if ( ! $license->is_lifetime() && ! $this->license->is_auto_renewal() && $this->license->get_expiry_date()->exists() ) : ?>
					<li><span class="green dashicons dashicons-yes"></span> <?php printf( __( 'License is valid until %s', 'codepress-admin-columns' ), '<strong>' . ac_format_date( 'F j, Y', $license->get_expiry_date()->get_value()->getTimestamp() ) . '</strong>' ); ?></li>
				<?php endif; ?>

			<?php endif; ?>
			<?php if ( $this->is_license_defined ): ?>
				<li><span class="green dashicons dashicons-yes"></span> <?php _e( 'License key is defined in code.', 'codepress-admin-columns' ); ?></li>
			<?php endif; ?>
		</ul>
		<?= $form_buttons; ?>

	<?php elseif ( $this->is_license_defined && $this->license_key ) : ?>

		<input type="hidden" name="license" value="<?= $this->license_key->get_value(); ?>">
		<button type="submit" class="button" name="action" value="<?= RequestParser::ACTION_ACTIVATE; ?>"><?php _e( 'Activate license', 'codepress-admin-columns' ); ?></button>
		<ul class="license-info-list">
			<li>
				<span class="info dashicons dashicons-info-outline orange"></span>
				<?php _e( 'License key is defined in code but not yet activated.', 'codepress-admin-columns' ); ?>
			</li>
		</ul>

	<?php else : ?>

		<input type="text" value="<?= $this->license_key ? $this->license_key->get_value() : null; ?>" name="license" size="40" placeholder="<?php echo esc_attr( __( 'Enter your license code', 'codepress-admin-columns' ) ); ?>">
		<button type="submit" class="button" name="action" value="<?= RequestParser::ACTION_ACTIVATE; ?>"><?php _e( 'Activate', 'codepress-admin-columns' ); ?></button>
		<p class="description">
			<?php echo ac_helper()->icon->dashicon( [ 'icon' => 'info-outline', 'class' => 'orange' ] ); ?>
			<?php _e( 'Enter your license key to receive automatic updates.', 'codepress-admin-columns' ); ?>
			<?php printf( __( 'You can find your license key on your %s.', 'codepress-admin-columns' ), sprintf( '<a href="%s" target="_blank">%s</a>', $this->my_account_link, __( 'account page', 'codepress-admin-columns' ) ) ); ?>
		</p>
	<?php endif; ?>

</form>