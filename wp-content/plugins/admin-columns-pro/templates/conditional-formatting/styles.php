<style>

	<?php foreach( $this->styles as $name => $style ): ?>
	<?php
		$class = 'acp-cf-style-'.$name;
 	?>
	.<?= $class ?>,
	.acp-cf-conditions__row .-style button.<?= $class ?>,
	table tr td .<?= $class ?> {
	<?php if ( ! empty( $style['background'] ) ) : ?> background: <?= esc_html( $style['background'] ) ?>;
	<?php endif;?> <?php if ( ! empty( $style['background_color'] ) ) : ?> background-color: <?= esc_html( $style['background_color'] ) ?>;
	<?php endif;?> color: <?= esc_html( $style['color'] ?? '' ) ?>;
	<?php if ( ! empty( $style['background_color'] ) ) : ?> border-color: <?= esc_html( $style['background_color'] ) ?>;
	<?php endif; ?>
	}

	<?php if ( ! empty( $style['background_color'] ) || ! empty( $style['background'] ) ) : ?>
	table tr td .<?= $class ?> {
		padding: 2px 4px;
		-webkit-box-decoration-break: clone;
		box-decoration-break: clone;
		border-radius: 2px;
	}

	<?php endif; ?>


	table tr td .<?= $class ?> > *,
	table tr td .<?= $class ?> span.dashicons {
		color: <?= esc_html( $style['color']??'' ) ?>;
	}

	.<?= $class ?> a,
	.<?= $class ?> a:hover,
	.<?= $class ?> a:focus {
		text-decoration: underline;
	}

	<?php endforeach;?>
</style>