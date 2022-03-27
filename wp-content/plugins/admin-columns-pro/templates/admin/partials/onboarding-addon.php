<?php
/* @var \AC\Integration $integration */

$integration = $this->integration;
$checked = $this->checked;
$integration_is_active = is_plugin_active( $integration->get_basename() );

$classes = [ 'ac-block-checkbox' ];
if ( $integration_is_active  ) {
	$classes[] = '-installed';
}
?>
<div class="<?= implode( ' ', $classes ) ?>">
	<?php if ( ! $integration_is_active ): ?>
		<input type="checkbox" name="integration" <?php checked( $checked ) ?> data-addon="<?= esc_attr( $integration->get_slug() ); ?>" id="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__input" value="<?= esc_attr( $integration->get_slug() ); ?>"/>
	<?php endif; ?>
	<label for="ac-addon-<?= esc_attr( $integration->get_slug() ); ?>" class="ac-block-checkbox__info">
		<strong class="ac-block-checkbox__label"><?= $integration->get_title(); ?></strong>

		<?= $integration->get_description() ?>

		<div class="ac-block-checkbox__indicator">
			<span class="ac-block-checkbox__indicator__input-select"></span>
			<span class="ac-block-checkbox__indicator__icon dashicons dashicons-yes"></span>
		</div>
		<div class="ac-block-checkbox__error"></div>
		<span class="spinner"></span>
	</label>

</div>