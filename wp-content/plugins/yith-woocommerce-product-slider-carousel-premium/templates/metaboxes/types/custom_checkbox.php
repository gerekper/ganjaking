<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


extract( $args ); //phpcs:ignore WordPress.PHP.DontExtract

$custom_attr_string = '';
if ( isset( $custom_attributes ) ) {

	$custom_attr_string = '';

	foreach ( $custom_attributes as $key => $attr_value ) {
		$custom_attr_string .= $key . '=' . $attr_value;
	}
}
?>
<div id="<?php echo esc_attr( $id ); ?>-container"
					<?php
					if ( isset( $deps ) ) :
						?>
	data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<p>
		<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1"
											<?php
											if ( isset( $std ) ) :
												?>
			data-std="<?php echo esc_attr( $std ); ?>"
													<?php
endif;
											checked( $value, 1 );
											?>
												<?php echo esc_html( $custom_attr_string ); ?> />
		<span class="desc inline"><?php echo esc_html( $desc ); ?></span>
	</p>
</div>
