<?php
/**
 * "Welcome" view.
 *
 * @var string $variant
 * @var array  $items
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

$variant = $variant ?? 'list';

$classes = array(
	'yith-wcbk-welcome__items',
	"yith-wcbk-welcome__items--{$variant}",
);
$classes = implode( ' ', $classes );

$loop = 1;
?>
<ul class="<?php echo esc_attr( $classes ); ?>">

	<?php foreach ( $items as $item ) : ?>
		<?php
		$item_url         = $item['url'] ?? '';
		$item_title       = $item['title'] ?? '';
		$item_description = $item['description'] ?? '';
		$item_cta         = $item['cta'] ?? '';
		$item_classes     = array( 'yith-wcbk-welcome__item' );
		if ( ! $item_url ) {
			$item_classes[] = 'yith-wcbk-welcome__item--no-link';
		}

		$item_classes = implode( ' ', $item_classes );
		?>
		<li class="<?php echo esc_attr( $item_classes ); ?>">
			<a
					class="yith-wcbk-welcome__item__wrap"
					target="_blank"
				<?php if ( $item_url ) : ?>
					href="<?php echo esc_url( $item_url ); ?>"
				<?php endif; ?>
			>
				<?php if ( 'steps' === $variant ) : ?>
					<div class="yith-wcbk-welcome__item__step">
						<?php echo esc_html( $loop ); ?>
					</div>
				<?php endif; ?>
				<div class="yith-wcbk-welcome__item__content">
					<div class="yith-wcbk-welcome__item__title">
						<?php echo wp_kses_post( $item_title ); ?>
					</div>
					<div class="yith-wcbk-welcome__item__description">
						<?php echo wp_kses_post( $item_description ); ?>
					</div>
					<?php if ( $item_cta ) : ?>
						<div class="yith-wcbk-welcome__item__cta">
							<?php echo esc_html( $item_cta ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $item_url && ! $item_cta ) : ?>
					<i class="yith-wcbk-welcome__item__arrow yith-icon yith-icon-arrow-right-alt"></i>
				<?php endif; ?>
			</a>
		</li>

		<?php $loop ++; ?>
	<?php endforeach; ?>
</ul>
