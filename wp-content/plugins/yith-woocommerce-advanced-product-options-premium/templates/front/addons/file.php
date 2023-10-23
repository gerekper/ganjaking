<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var int    $x
 * @var string $setting_hide_images
 * @var string $required_message
 * @var array  $settings
 * @var string $image_replacement
 * @var string $option_description
 * @var string $option_image
 * @var string $price
 * @var string $price_method
 * @var string $price_sale
 * @var string $price_type
 * @var string $currency
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

//Settings configuration.
extract($settings );

$hide_options_prices = apply_filters( 'yith_wapo_hide_option_prices', $hide_option_prices, $addon );
$show_in_a_grid      = wc_string_to_bool( $show_in_a_grid );
$options_width_css   = $show_in_a_grid && 1 == $options_per_row ? 'width: ' . $options_width . '%' : 'width: 100%';

$hide_option_images  = wc_string_to_bool( $hide_option_images );
$hide_option_label   = wc_string_to_bool( $hide_option_label );
$hide_option_prices  = wc_string_to_bool( $hide_option_prices );
$hide_product_prices = wc_string_to_bool( $hide_product_prices );

$image_replacement = $addon->get_image_replacement( $addon, $x );

// Options configuration.
$required       = $addon->get_option( 'required', $x, 'no', false ) === 'yes';
$allow_multiple = $addon->get_option( 'multiupload', $x, 'no', false ) === 'yes';
$max_multiple   = $addon->get_option( 'multiupload_max', $x, '', false );
$allow_multiple = wc_string_to_bool( $allow_multiple );

$upload_string  = get_option( 'yith_wapo_uploads_text_to_show', __( 'Drop files to upload or', 'yith-woocommerce-product-add-ons' ) );

?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
     class="yith-wapo-option <?php echo $allow_multiple ? 'allow-multiple' : '';?>"
     data-option-id="<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
	 <?php
	    if ( '' !== $max_multiple && $max_multiple > 0 ) {
			?>
			data-max-multiple="<?php echo esc_attr( $max_multiple ); ?>"
     <?php
	    }
	 ?>
>

	<div class="file-input label <?php echo wp_kses_post( ! empty( $addon_image_position ) ? 'position-' . $addon_image_position : '' ); ?>">
		<div class="file-input-container">
			<div class="option-container">
				<!-- ABOVE / LEFT IMAGE -->
				<?php
				if ( 'above' === $addon_options_images_position || 'left' === $addon_options_images_position ) {
					//TODO: use wc_get_template() function.
					include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
				?>
				<label for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">
					<!-- LABEL -->
					<?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?>

					<!-- PRICE -->
					<?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x, $currency ) ) : ''; ?>
				</label>
				<!-- UNDER / RIGHT IMAGE -->
				<?php
				if ( 'under' === $addon_options_images_position || 'right' === $addon_options_images_position ) {
					//TODO: use wc_get_template() function.
					include YITH_WAPO_DIR . '/templates/front/option-image.php';
				}
				?>
			</div>
			<div class="file-container">
				<!-- INPUT -->
				<input type="hidden"
				       id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
				       class="option yith-wapo-option-value upload-parent"
				       value=""
				       data-price="<?php echo esc_attr( $price ); ?>"
					<?php
					if ( $price > 0 ) {
						?>
						data-price-sale="<?php echo esc_attr( $price_sale ); ?>"
						<?php
					}
					?>
					   data-price-type="<?php echo esc_attr( $price_type ); ?>"
					   data-price-method="<?php echo esc_attr( $price_method ); ?>"
					   data-first-free-enabled="<?php echo esc_attr( $first_options_selected ); ?>"
					   data-first-free-options="<?php echo esc_attr( $first_free_options ); ?>"
					   data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
					<?php echo $required ? 'required' : ''; ?>>

				<input id="yith-wapo-file-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>" type="file" class="file" <?php echo $allow_multiple ? 'multiple' : ''; ?>>

				<div class="yith-wapo-ajax-uploader" style="<?php echo esc_attr( $options_width_css ); ?>;">
					<div class="yith-wapo-uploaded-file" style="display: none;">
					</div>
					<div class="yith-wapo-ajax-uploader-container">
						<?php echo '<span>' . wp_kses_post( $upload_string ) . '</span>'; ?>
						<?php if ( get_option( 'yith_wapo_uploads_link_to_show' ) === 'text' ) : ?>
							&nbsp;<a class="link"><?php echo esc_html__( 'browse', 'yith-woocommerce-product-add-ons' ); ?></a>
						<?php else : ?>
							<span class="button"><?php echo strtoupper( esc_html__( 'upload', 'yith-woocommerce-product-add-ons' ) ); ?></span>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- TOOLTIP -->
	<?php if ( $addon->get_option( 'tooltip', $x ) !== '' ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>">
			<span><?php echo wp_kses_post( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description"><?php echo wp_kses_post( $option_description ); ?></p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if ( 'yes' === $sell_individually ) : ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
