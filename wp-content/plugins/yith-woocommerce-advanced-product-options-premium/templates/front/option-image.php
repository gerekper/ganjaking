<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Addon $addon
 * @var int $x
 * @var string $option_image
 * @var string $hide_option_images
 * @var string $addon_options_images_position
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! empty( $addon_image_position ) ) : ?>
<label class="image-container" for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">
	<div class="image">
		<img src="<?php echo esc_attr( $option_image ); ?>" style="<?php echo $images_height_style ?? '' ?>">
	</div>
</label>

<?php endif; ?>
