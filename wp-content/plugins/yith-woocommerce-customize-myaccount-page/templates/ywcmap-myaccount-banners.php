<?php
/**
 * MY ACCOUNT TEMPLATE AVATAR FORM
 *
 * @since 2.2.0
 * @package YITH WooCommerce Customize My Account Page
 * @var array $banners
 */

defined( 'YITH_WCMAP' ) || exit;

?>

<div class="yith-wcmap-banners-wrapper">
	<?php foreach ( $banners as $banner_key => $banner ) : ?>
		<?php $class = urldecode( $banner_key ); ?>
		<div class="yith-wcmap-banner banner-<?php echo esc_attr( $class ); ?>">
			<?php if ( $banner['link'] ) : ?>
			<a href="<?php echo esc_url( $banner['link'] ); ?>">
				<?php endif; ?>

				<?php if ( 'empty' !== $banner['icon_type'] || $banner['counter'] ) : ?>
					<div class="banner-icon-counter">

					<?php if ( 'default' === $banner['icon_type'] ) : ?>
						<i class="fa fa-<?php echo esc_attr( $banner['icon'] ); ?>"></i>
					<?php elseif ( 'custom' === $banner['icon_type'] ) : ?>
						<img src="<?php echo esc_url( $banner['custom_icon'] ); ?>" alt=""
									width="<?php echo intval( $banner['custom_icon_width'] ); ?>" height="auto">
					<?php endif; ?>

					<?php if ( false !== $banner['counter'] ) : ?>
						<span class="banner-counter"><?php echo intval( $banner['counter'] ); ?></span>
					<?php endif; ?>

					</div>
				<?php endif; ?>
				<h3 class="banner-title"><?php echo esc_html( $banner['name'] ); ?></h3>
				<?php if ( ! empty( $banner['text'] ) ) : ?>
					<p class="banner-text"><?php echo wp_kses_post( do_shortcode( $banner['text'] ) ); ?></p>
				<?php endif; ?>

				<?php if ( $banner['link'] ) : ?>
			</a>
		<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
