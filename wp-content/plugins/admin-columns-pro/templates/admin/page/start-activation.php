<?php

use AC\Type\Url;
use ACP\RequestParser;
use ACP\Type\License;

/**
 * @var License\Key        $license_key
 * @var ACP\Entity\License $license
 * @var string             $next_url
 * @var bool               $allow_skip
 * @var bool               $is_remote_local
 */
$license_key = $this->license_key;
$license = $this->license;
$next_url = $this->next_url;
$allow_skip = $this->allow_skip;
$is_remote_local = $this->is_remote_local;

?>
<div class="ac-section-group -start">

	<section class="ac-article">
		<h2 class="ac-article__title"><?php _e( 'Welcome to Admin Columns Pro', 'codepress-admin-columns' ); ?></h2>

		<div class="ac-article__body">
			<p>
				<?= esc_html( __( 'Enter your Admin Columns Pro License Key below.', 'codepress-admin-columns' ) ); ?>
				<?= esc_html( __( 'Your key unlocks access to automatic updates, the add-on installer, support, and the column editor.', 'codepress-admin-columns' ) ); ?>
				<?= sprintf( __( 'You can find your key on the %s on the Admin Columns Pro site.', 'codepress-admin-columns' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( ( new Url\Site( Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS ) )->get_url() ), __( 'My Account page', 'codepress-admin-columns' ) ) ); ?>
			</p>
			<form class="acp-setup-form -activation" method="post">

				<?php if ( ! $license ) : ?>

					<?php wp_nonce_field( RequestParser::NONCE_ACTION, '_acnonce' ); ?>
					<input type="hidden" name="action" value="<?= RequestParser::ACTION_ACTIVATE; ?>">
					<label for="ac-license-key" class="screen-reader-text"><?= esc_html( __( 'License Key', 'codepress-admin-columns' ) ); ?></label>
					<div class="ac-input-group">
						<input id="ac-license-key" class="ac-input" name="license" placeholder="<?= esc_attr( __( 'License Key', 'codepress-admin-columns' ) ); ?>" value="<?= $license_key ? $license_key->get_value() : ''; ?>">
					</div>
					<?php if ( $this->is_remote_local ) : ?>
						<p>
							<em>
								<?= __( 'We noticed your are using a local or development server.', 'codepress-admin-columns' ); ?>
								<?= __( 'Your development site won’t count towards your site limit when activating your license.', 'codepress-admin-columns' ); ?>
							</em>
						</p>
					<?php endif; ?>

					<div class="acp-setup-form__footer">
						<button class="ac-btn-primary" type="submit">
							<?= esc_html( __( 'Next', 'codepress-admin-columns' ) ); ?>
						</button>
						<?php if ( $allow_skip ) : ?>
							<a href="<?= $next_url; ?>"><?php _e( 'Skip step', 'codepress-admin-columns' ); ?></a>
						<?php endif; ?>
					</div>

				<?php else : ?>

					<p class="acp-activation-status">
						<span class="dashicons dashicons-yes"></span> <?php _e( 'License active', 'codepress-admin-columns' ); ?>
					</p>
					<div class="acp-setup-form__footer">
						<a class="ac-btn-primary" href="<?= $next_url; ?>"><?php _e( 'Next', 'codepress-admin-columns' ); ?></a>
					</div>

				<?php endif; ?>

			</form>
		</div>
	</section>

</div>