<?php

use AC\Integrations;

/* @var Integrations $active */
/* @var Integrations $recommended */
/* @var Integrations $available */
/* @var string $next_url */
/* @var AC\Integration $integration */

$active = $this->active;
$recommended = $this->recommended;
$available = $this->available;
$next_url = $this->next_url;

$active_titles = [];

if ( $active->exists() ) {
	foreach ( $active->all() as $item ) {
		$active_titles[] = sprintf( '<strong>%s %s</strong>', $item->get_title(), __( 'add-on', 'codepress-admin-columns' ) );
	}
}
?>
<div class="ac-section-group -start" data-ac-setup="addons">

	<section class="ac-article">
		<h1 class="ac-article__title"><?php _e( 'Add-ons', 'codepress-admin-coluns' ); ?></h1>
		<div class="ac-article__body">
			<p>
				<?= esc_html( __( 'Our integration addons guarantees the best possible integration between Admin Columns Pro and you favorite plugins.', 'codepress-admin-columns' ) ); ?>
			</p>
			<?php if ( $active->exists() ) : ?>
				<p>
					<?= sprintf( __( 'You already have installed: %s.', 'codepress-admin-columns' ), ac_helper()->string->enumeration_list( $active_titles, 'and' ) ); ?>
				</p>
			<?php endif; ?>

			<form class="acp-setup-form -addons" method="post">
				<?php wp_nonce_field( 'ac-ajax', '_nonce' ); ?>
				<input type="hidden" name="network_wide" value="<?= esc_attr( $this->network_wide ); ?>">

				<?php if ( $recommended->exists() ) : ?>
					<p>
						<strong><?= esc_html( __( 'We recommend to install the following integration add-ons:', 'codepress-admin-columns' ) ); ?></strong>
					</p>
					<?php foreach ( $recommended as $integration ): ?>
						<div class="ac-block-checkbox">
							<input type="checkbox" name="integration" checked data-addon="<?= esc_attr( $integration->get_slug() ); ?>" id="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__input" value="<?= esc_attr( $integration->get_slug() ); ?>"/>
							<label for="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__info">
								<strong class="ac-block-checkbox__label"><?= $integration->get_title(); ?></strong>
								<?= $integration->get_description() ?>
								<a href="<?= esc_url( $integration->get_link() ); ?>" target="_blank" class="ac-block-checkbox__more"><?= __( 'More details', 'codepress-admin-columns' ); ?></a>
								<div class="ac-block-checkbox__status">
									<span class="ac-block-checkbox__status__icon dashicons dashicons-yes"></span>
									<span class="ac-block-checkbox__status__text" data-status-message></span>
								</div>
							</label>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( $available->exists() ) : ?>
					<div class="available-integrations">
						<input class="available-integrations__toggle" type="checkbox" id="toggle-available-integrations">
						<label class="available-integrations__label" for="toggle-available-integrations">
							<?php _e( 'Show all integration add-ons', 'codepress-admin-columns' ); ?>
						</label>
						<div class="available-integrations__list" id="available-integrations">
							<?php foreach ( $available as $integration ): ?>
								<div class="ac-block-checkbox">
									<input type="checkbox" name="integration" data-addon="<?= esc_attr( $integration->get_slug() ); ?>" id="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__input" value="<?= esc_attr( $integration->get_slug() ); ?>"/>
									<label for="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__info">
										<strong class="ac-block-checkbox__label"><?= $integration->get_title(); ?></strong>
										<?= $integration->get_description() ?>
										<a href="<?= esc_url( $integration->get_link() ); ?>" target="_blank" class="ac-block-checkbox__more"><?= __( 'More details', 'codepress-admin-columns' ); ?></a>
										<div class="ac-block-checkbox__status">
											<span class="ac-block-checkbox__status__icon dashicons dashicons-yes"></span>
											<span class="ac-block-checkbox__status__text" data-status-message></span>
										</div>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="acp-setup-form__footer">
					<button class="ac-btn-primary" type="submit" data-loading="<?= esc_attr( __( 'Loading', 'codepress-admin-columns' ) ); ?>..."><?= esc_html( __( 'Next', 'codepress-admin-columns' ) ); ?></button>
					<a href="<?= $next_url; ?>" data-next-url><?php _e( 'Skip step', 'codepress-admin-columns' ); ?></a>
				</div>

			</form>

		</div>
	</section>

</div>