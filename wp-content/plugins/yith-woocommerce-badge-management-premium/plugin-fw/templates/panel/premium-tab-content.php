<?php
/**
 * The template for displaying the content of the premium tab.
 *
 * @var array  $features             List of premium features
 * @var array  $testimonials         List of testimonials
 * @var array  $pricing              Pricing details.
 * @var string $landing_page_url     The premium landing page URL.
 * @var string $free_vs_premium_url  The free vs premium URL.
 * @var bool   $show_free_vs_premium Show free VS premium link flag.
 * @package YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;

list ( $price_html, $discount_percentage ) = yith_plugin_fw_extract( $pricing, 'price_html', 'discount_percentage' );

$default_feature     = array(
	'title'       => '',
	'description' => '',
);
$default_testimonial = array(
	'name'    => '',
	'avatar'  => '',
	'message' => '',
);
?>

<div class="main-content">
	<div class="features">
		<?php foreach ( $features as $feature ) : ?>
			<?php
			$feature = wp_parse_args( $feature, $default_feature );
			?>
			<div class="feature">
				<?php if ( ! ! $feature['title'] ) : ?>
					<div class="feature__title">
						<?php echo wp_kses_post( $feature['title'] ); ?>

						<?php
						yith_plugin_fw_get_component(
							array(
								'class' => 'feature__premium-tag',
								'type'  => 'tag',
								'label' => _x( 'PREMIUM', 'Panel option tag', 'yith-plugin-fw' ),
								'color' => 'premium',
							),
							true
						);
						?>
					</div>
				<?php endif; ?>
				<?php if ( ! ! $feature['description'] ) : ?>
					<div class="feature__description"><?php echo wp_kses_post( $feature['description'] ); ?></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<div>
			<?php echo esc_html_x( '...and so much more!', 'Premium Tab', 'yith-plugin-fw' ); ?>
			<?php if ( $show_free_vs_premium ) : ?>
				<a href="<?php echo esc_url( $free_vs_premium_url ); ?>" target="_blank">
					<?php echo esc_html_x( 'Check the free vs premium features >', 'Premium Tab', 'yith-plugin-fw' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $landing_page_url ); ?>" target="_blank">
					<?php echo esc_html_x( 'Check the premium features >', 'Premium Tab', 'yith-plugin-fw' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="landing">
		<div class="landing__container">

			<div class="landing__head">
				<?php
				if ( $discount_percentage ) {
					echo sprintf(
					// translators: %s is the discount percentage.
						esc_html_x( 'Upgrade now to get a %s%% off', 'Premium Tab', 'yith-plugin-fw' ),
						absint( $discount_percentage )
					);
				} else {
					echo esc_html_x( 'Upgrade now to unlock premium features', 'Premium Tab', 'yith-plugin-fw' );
				}
				?>
			</div>

			<?php if ( $price_html ) : ?>
				<div class="landing__pricing">
					<?php echo wp_kses_post( $price_html ); ?>
				</div>
			<?php endif; ?>

			<div class="landing__advantages">
				<div class="landing__advantage"><?php echo esc_html_x( 'Advanced features', 'Premium Tab', 'yith-plugin-fw' ); ?></div>
				<div class="landing__advantage"><?php echo esc_html_x( 'Regular updates', 'Premium Tab', 'yith-plugin-fw' ); ?></div>
				<div class="landing__advantage"><?php echo esc_html_x( 'Technical support', 'Premium Tab', 'yith-plugin-fw' ); ?></div>
				<div class="landing__advantage"><?php echo esc_html_x( '100% Money-back guarantee', 'Premium Tab', 'yith-plugin-fw' ); ?></div>
			</div>

			<a href="<?php echo esc_url( $landing_page_url ); ?>" target="_blank" class="landing__cta">
				<?php echo esc_html_x( 'Get the premium version', 'Premium Tab', 'yith-plugin-fw' ); ?>
			</a>

			<div class="landing__testimonials">
				<?php foreach ( $testimonials as $testimonial ) : ?>
					<?php
					$testimonial = wp_parse_args( $testimonial, $default_testimonial );
					if ( ! $testimonial['name'] || ! $testimonial['message'] ) {
						continue;
					}
					?>
					<div class="testimonial">
						<div class="testimonial__message">
							<svg class="testimonial__message__mark" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 379.51">
								<path
									d="M299.73 345.54c81.25-22.55 134.13-69.68 147.28-151.7 3.58-22.31-1.42-5.46-16.55 5.86-49.4 36.97-146.53 23.88-160.01-60.55C243.33-10.34 430.24-36.22 485.56 46.34c12.87 19.19 21.39 41.59 24.46 66.19 13.33 106.99-41.5 202.28-137.82 247.04-17.82 8.28-36.6 14.76-56.81 19.52-10.12 2.04-17.47-3.46-20.86-12.78-2.87-7.95-3.85-16.72 5.2-20.77zm-267.78 0c81.25-22.55 134.14-69.68 147.28-151.7 3.58-22.31-1.42-5.46-16.55 5.86-49.4 36.97-146.53 23.88-160-60.55-27.14-149.49 159.78-175.37 215.1-92.81 12.87 19.19 21.39 41.59 24.46 66.19 13.33 106.99-41.5 202.28-137.82 247.04-17.82 8.28-36.59 14.76-56.81 19.52-10.12 2.04-17.47-3.46-20.86-12.78-2.87-7.95-3.85-16.72 5.2-20.77z"/>
							</svg>
							<?php echo wp_kses_post( wpautop( $testimonial['message'] ) ); ?>
						</div>
						<div class="testimonial__details">
							<div class="testimonial__name-rating">
								<div class="testimonial__name">
									<strong><?php echo esc_html( $testimonial['name'] ); ?></strong> - <?php echo esc_html_x( 'Verified Customer', 'Premium Tab', 'yith-plugin-fw' ); ?>
								</div>
								<div class="testimonial__rating">
									<?php for ( $i = 0; $i < 5; $i ++ ) : ?>
										<svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
											<path clip-rule="evenodd" fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"></path>
										</svg>
									<?php endfor; ?>
								</div>
							</div>
							<?php if ( ! ! $testimonial['avatar'] ) : ?>
								<img class="testimonial__avatar" src="<?php echo esc_attr( $testimonial['avatar'] ); ?>" alt="<?php echo esc_attr( $testimonial['name'] ); ?>"/>
							<?php endif; ?>
						</div>
					</div>

				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
