<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Addon $addon
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
$options_width_css   = $show_in_a_grid ? 'width: ' . ( $options_width ) . '%; min-width: ' . ( $options_width ) . '%;' : 'width: auto';

$hide_option_images  = wc_string_to_bool( $hide_option_images );
$hide_option_label   = wc_string_to_bool( $hide_option_label );
$hide_option_prices  = wc_string_to_bool( $hide_option_prices );
$hide_product_prices = wc_string_to_bool( $hide_product_prices );

// Image replacement.
$image_replacement = $addon->get_image_replacement( $addon, $x );

// Options configuration.
$addon_label = $addon->get_option( 'label', $x ); // The label of the add-on.

$required = $addon->get_option( 'required', $x, 'no', false ) === 'yes';
$checked  = $addon->get_option( 'default', $x, 'no', false ) === 'yes';
$selected = $checked ? 'selected' : '';

$style_images_position      = get_option( 'yith_wapo_style_images_position', 'above' );
$style_images_equal_height  = get_option( 'yith_wapo_style_images_equal_height', 'no' );
$style_images_height        = get_option( 'yith_wapo_style_images_height' );
$style_label_position       = get_option( 'yith_wapo_style_label_position', 'inside' );
$style_description_position = get_option( 'yith_wapo_style_description_position', 'outside' );

// Individual style options.
$images_position      = '';
$images_height_style  = '';
$label_position_style = '';
$label_padding_style        = '';
$label_content_align_style  = '';
$description_position_style = '';

//Images position
if ( 'default' !== $addon_options_images_position ) {
	$images_position = (string) $addon_options_images_position;
} else {
	$images_position = (string) $style_images_position;
}

//Label content alignment.
$label_content_align_style = $label_content_align;

//Force image equal heights.
if ( $image_equal_height === 'yes' ) {
	$images_height_style = 'height: ' . $images_height . 'px';
} else {
	if ( 'yes' === $style_images_equal_height ) {
		$images_height_style = 'height: ' . $style_images_height . 'px';
	}
}

//Label position.
if ( 'default' !== $label_position ) {
	$label_position_style = $label_position;
} else {
	$label_position_style = $style_label_position;
}

// Description position.
if ( 'default' !== $description_position ) {
	$description_position_style = $description_position;
} else {
	$description_position_style = $style_description_position;
}

// Label padding.
$label_padding_dim = $label_padding['dimensions'];
$label_padding_style = 'padding: ' . $label_padding_dim['top'] . 'px ' . $label_padding_dim['right'] . 'px ' . $label_padding_dim['bottom'] . 'px ' . $label_padding_dim['left'] . 'px;';

$label_price_html  = '<div class="label_price">';
$label_price_html .= ! $hide_option_label ? '<label>' . $addon_label . '</label>' : '';
$label_price_html .= $required ? ' <span class="required">*</span>' : '';
$label_price_html .= ! $hide_option_prices ? ' ' . $addon->get_option_price_html( $x, $currency ) : '';
$label_price_html .= '</div>';

$description_html = '' !== $option_description ? '<p class="description">' . wp_kses_post( $option_description ) . '</p>' : '';

?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
	class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?> <?php echo esc_attr( $selected ); ?>"
	data-replace-image="<?php echo esc_attr( $image_replacement ); ?>">

	<!-- INPUT -->
	<input type="checkbox"
		id="yith-wapo-<?php echo esc_attr( $addon->id . '-' . $x ); ?>"
		class="yith-proteo-standard-checkbox yith-wapo-option-value"
		name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
		value="<?php echo esc_attr( $addon->get_option( 'label', $x ) ); ?>"
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
		<?php echo $required ? 'required' : ''; ?>
		<?php echo $checked ? 'checked="checked"' : ''; ?>
		style="display: none;">

	<div class="label_container <?php echo esc_attr( $images_position ); ?> <?php echo esc_attr( $label_position_style ); ?> <?php echo 'content-align-' . wp_kses_post( $label_content_align_style )?>">



		<div class="label <?php echo wp_kses_post( ! empty( $addon_image_position ) ? 'position-' . $addon_image_position : '' ); ?>" for="yith-wapo-<?php echo esc_attr( $addon->id . '-' . $x ); ?>">

            <?php if ( 'outside' === $label_position_style && 'under' === $images_position ) : ?>
                <?php echo wp_kses_post( $label_price_html ); ?>
            <?php endif; ?>

			<div class="label-container-display" style="<?php echo esc_attr( $label_padding_style ); ?>">
				<?php
				if ( 'above' === $images_position || 'left' === $images_position ) {
					//TODO: use wc_get_template() function.
					include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
				?>

				<?php if ( 'inside' === $label_position_style && 'under' === $images_position ) : ?>
					<?php echo wp_kses_post( $label_price_html ); ?>
				<?php endif; ?>

				<div class="inside">

					<?php if ( 'inside' === $label_position_style && 'under' !== $images_position ) : ?>
						<?php echo wp_kses_post( $label_price_html ); ?>
					<?php endif; ?>

					<?php if ( 'inside' === $description_position_style ) : ?>
						<?php echo wp_kses_post( $description_html ); ?>
					<?php endif; ?>

				</div>

				<?php
				if ( 'under' === $images_position || 'right' === $images_position ) {
					//TODO: use wc_get_template() function.
					include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
				?>

			</div>
            <div class="outside">

                <?php if ( 'outside' === $label_position_style && 'under' !== $images_position ) : ?>
                    <?php echo wp_kses_post( $label_price_html ); ?>
                <?php endif; ?>

                <?php if ( 'outside' === $description_position_style ) : ?>
                    <?php echo wp_kses_post( $description_html ); ?>
                <?php endif; ?>

            </div>

			<!-- TOOLTIP -->
			<?php if ( $addon->get_option( 'tooltip', $x ) !== '' ) : ?>
				<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>">
			<span><?php echo esc_html( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
			<?php endif; ?>
		</div>

	</div>

	<!-- Sold individually -->
	<?php if ( 'yes' === $sell_individually ) : ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
