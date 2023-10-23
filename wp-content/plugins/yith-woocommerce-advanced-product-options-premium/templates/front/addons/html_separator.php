<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var array  $settings
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

//Settings configuration.
extract($settings );

if ( 'empty_space' === $separator_style ) {
	$css = 'height: ' . $separator_size . 'px';
} else {
	$css = 'width: ' . $separator_width . '%; border-width: ' . $separator_size . 'px; border-color: ' . ( ! is_array( $separator_color ) ? $separator_color : '' ) . ';';
}

?>

<div class="yith-wapo-separator <?php echo esc_attr( $separator_style ); ?>" style="<?php echo esc_attr( $css ); ?>"></div>
