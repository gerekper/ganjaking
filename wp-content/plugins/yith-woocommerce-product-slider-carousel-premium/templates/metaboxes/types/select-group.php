<?php
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
$is_multiple = isset( $multiple ) && $multiple;
$multiple    = ( $is_multiple ) ? ' multiple' : '';
$deps_html   = '';
if ( function_exists( 'yith_field_deps_data' ) ) {
	$deps_html = yith_field_deps_data( $args );
} else {
	if ( isset( $deps ) ) {
		$deps_ids    = $deps['ids'];
		$deps_values = $deps['values'];
		$deps_html   = "data-field='$id' data-dep='{$deps_ids}' data-value='$deps_values'";
	}
}

?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo $deps_html; //phpcs:ignore WordPress.Security.EscapeOutput ?> >

	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

	<div class="select_wrapper">
		<select <?php echo esc_attr( $multiple ); ?> id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>
						<?php
						if ( $is_multiple ) {
							echo '[]';}
						?>
		"
						<?php
						if ( isset( $std ) ) :
							?>
			data-std="<?php echo ( esc_attr( $is_multiple ) ) ? esc_attr( implode( ' ,', esc_attr( $std ) ) ) : esc_attr( $std ); ?>"<?php endif ?>>
			<option value="" <?php selected( '', $value ); ?>><?php esc_html_e( 'None', 'yith-woocommerce-product-slider-carousel' ); ?></option>
		<?php foreach ( $options as $group_name => $it ) : ?>
		<optgroup label="<?php echo esc_attr( $group_name ); ?>">
			<?php foreach ( $options[ $group_name ] as $key ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"
											<?php
											if ( $is_multiple ) :
												selected( true, in_array( $key, $value, true ) );
else :
	selected( $key, $value );
endif;
?>
><?php echo esc_html( $key ); ?></option>
				<?php endforeach; ?>
			</optgroup>
			<?php endforeach; ?>
		</select>
	</div>

	<span class="desc inline"><?php echo esc_html( $desc ); ?></span>
</div>
