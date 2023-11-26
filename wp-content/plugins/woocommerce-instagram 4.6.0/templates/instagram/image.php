<?php
/**
 * Instagram Image
 *
 * @package WC_Instagram/Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $image ) ) :
	return;
endif;
?>
<li <?php wc_instagram_image_class(); ?>>
	<a href="<?php echo esc_url( $image['permalink'] ); ?> " target="_blank">
		<img src="<?php echo esc_url( $image['media_url'] ); ?>" alt="<?php echo esc_attr( ( isset( $image['caption'] ) ? $image['caption'] : '' ) ); ?>" />
	</a>
</li>
