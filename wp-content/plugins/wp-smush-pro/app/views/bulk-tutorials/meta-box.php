<?php
/**
 * Tutorials under Bulk Smush meta box.
 *
 * @since 2.7.1
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$tutorials = $this->get_tutorials_data();

// Desktop.
$row_number = 1;
?>
<div class="wp-smush-tutorials-section sui-hidden-xs" data-active="<?php echo esc_attr( $row_number ); ?>">

	<div id="wp-smush-carousel-desktop" class="wp-smush-tutorials-slider" aria-live="polite">

		<ul class="wp-smush-slider-wrapper">

			<li tabindex="0" id="wp-smush-tutorials-group-<?php echo esc_attr( $row_number ); ?>-desktop" data-slide="<?php echo esc_attr( $row_number ); ?>" aria-hidden="false">

				<?php foreach ( $tutorials as $index => $tutorial ) : ?>

					<?php if ( 0 !== $index && 0 === $index % 2 ) : ?>
						<?php $row_number++; ?>
						</li>
						<li tabindex="-1" id="wp-smush-tutorials-group-<?php echo esc_attr( $row_number ); ?>-desktop" class="sui-hidden" data-slide="<?php echo esc_attr( $row_number ); ?>" aria-hidden="true">
					<?php endif; ?>

					<div class="wp-smush-tutorial">

						<div class="wp-smush-tutorial-header">

							<a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank" class="wp-smush-tutorial-image" aria-hidden="true">
								<img
									src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full'] ); ?>"
									srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full'] ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full_2x'] ); ?> 2x"
									alt="<?php esc_html_e( 'Article image', 'wp-smushit' ); ?>"
									aria-hidden="true"
								/>
							</a>

							<div class="wp-smush-tutorial-header-right">

								<h4 class="wp-smush-tutorial-title">
									<a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank"><?php echo esc_html( $tutorial['title'] ); ?></a>
								</h4>

								<p class="wp-smush-tutorial-time">
									<i class="sui-icon-clock sui-sm" aria-hidden="true"></i>
									<?php /* translators: reading time in minutes */ ?>
									<span class="wp-smush-reading-time"><?php printf( esc_html__( '%d min read', 'wp-smushit' ), esc_html( $tutorial['read_time'] ) ); ?></span>
								</p>

							</div>

						</div>

						<div class="wp-smush-tutorial-body">

							<p class="sui-description" style="margin-bottom: 10px;"><?php echo esc_html( $tutorial['content'] ); ?></p>

							<p class="sui-description"><a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank" class="wp-smush-read-more-link"><?php esc_html_e( 'Read article', 'wp-smushit' ); ?></a></p>

						</div>

					</div>

				<?php endforeach; ?>

			</li>

		</ul>

	</div>

	<fieldset class="wp-smush-tutorials-slider-buttons" aria-label="<?php esc_html_e( 'Tutorials Navigation', 'wp-smushit' ); ?>" aria-controls="wp-smush-carousel-desktop">

		<button class="sui-button-icon wp-smush-tutorials-button wp-smush-slider-button-prev" data-direction="prev" aria-disabled="true" disabled>
			<i class="sui-icon-chevron-left sui-sm" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Previous', 'wp-smushit' ); ?></span>
		</button>

		<button class="sui-button-icon wp-smush-tutorials-button wp-smush-slider-button-next" data-direction="next" <?php echo 3 <= count( $tutorials ) ? '' : 'aria-disabled="true" disabled'; ?>>
			<i class="sui-icon-chevron-right sui-sm" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Next', 'wp-smushit' ); ?></span>
		</button>

	</fieldset>

</div>

<?php // Mobile. ?>
<?php $row_number_mobile = 1; ?>
<div class="wp-smush-tutorials-section sui-hidden-lg sui-hidden-md sui-hidden-sm" data-active="<?php echo esc_attr( $row_number_mobile ); ?>">

	<div id="wp-smush-carousel-mobile" class="wp-smush-tutorials-slider" aria-live="polite">

		<ul class="wp-smush-slider-wrapper">

			<?php foreach ( $tutorials as $tutorial ) : ?>

				<li
					tabindex="<?php echo 1 !== $row_number_mobile ? '-1' : '0'; ?>"
					id="wp-smush-tutorials-group-<?php echo esc_attr( $row_number_mobile ); ?>-mobile"
					data-slide="<?php echo esc_attr( $row_number_mobile ); ?>"
					aria-hidden="<?php echo 1 !== $row_number_mobile ? 'true' : 'false'; ?>"
					<?php echo 1 !== $row_number_mobile ? 'class="sui-hidden"' : ''; ?>
				>

					<div class="wp-smush-tutorial">

						<div class="wp-smush-tutorial-header">

							<a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank" class="wp-smush-tutorial-image" aria-hidden="true">
								<img
									src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full'] ); ?>"
									srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full'] ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/tutorials/' . $tutorial['thumbnail_full_2x'] ); ?> 2x"
									alt="<?php esc_html_e( 'Article image', 'wp-smushit' ); ?>"
									aria-hidden="true"
								/>
							</a>

							<div class="wp-smush-tutorial-header-right">

								<h4 class="wp-smush-tutorial-title">
									<a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank"><?php echo esc_html( $tutorial['title'] ); ?></a>
								</h4>

								<p class="wp-smush-tutorial-time">
									<span class="sui-icon-clock sui-sm" aria-hidden="true"></span>
									<?php /* translators: reading time in minutes */ ?>
									<span class="wp-smush-reading-time"><?php printf( esc_html__( '%d min read', 'wp-smushit' ), esc_html( $tutorial['read_time'] ) ); ?></span>
								</p>

							</div>

						</div>

						<div class="wp-smush-tutorial-body">

							<p class="sui-description" style="margin-bottom: 10px;"><?php echo esc_html( $tutorial['content'] ); ?></p>

							<p class="sui-description"><a href="<?php echo esc_url( $tutorial['url'] ); ?>" target="_blank" class="wp-smush-read-more-link"><?php esc_html_e( 'Read article', 'wp-smushit' ); ?></a></p>

						</div>

					</div>

				</li>

				<?php $row_number_mobile++; ?>
			<?php endforeach; ?>

		</ul>

	</div>

	<fieldset class="wp-smush-tutorials-slider-buttons" aria-label="<?php esc_html_e( 'Tutorials Navigation', 'wp-smushit' ); ?>" aria-controls="wp-smush-carousel-mobile">

		<button class="sui-button-icon wp-smush-tutorials-button wp-smush-slider-button-prev" data-direction="prev" aria-disabled="true" disabled>
			<i class="sui-icon-chevron-left sui-sm" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Previous', 'wp-smushit' ); ?></span>
		</button>

		<button class="sui-button-icon wp-smush-tutorials-button wp-smush-slider-button-next" data-direction="next" <?php echo 2 <= count( $tutorials ) ? '' : 'aria-disabled="true" disabled'; ?>>
			<i class="sui-icon-chevron-right sui-sm" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Next', 'wp-smushit' ); ?></span>
		</button>

	</fieldset>

</div>