<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Text Plugin Admin View
 *
 * @package    YITH
 * @author     Emanuela Castorina <emanuela.castorina@yithemes.it>
 * @since      1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

extract( $field );
$index = 0;

$value = maybe_unserialize( $value );
if ( isset( $value['list'] ) ) :
	foreach ( $value['list'] as $element ) :
		$index ++;
		$current_name = $name . '[list][' . $index . ']';
		$current_id   = $id . '[list][' . $index . ']';
		$hide_remove  = 1 == $index ? 'hide-remove' : 1;
		$points       = ( isset( $element['points'] ) ) ? $element['points'] : '';
		$number       = ( isset( $element['number'] ) ) ? $element['number'] : '';
		$repeat       = ( isset( $element['repeat'] ) ) ? $element['repeat'] : 0;
		$multiple     = isset( $multiple ) ? $multiple : 1;
		$show_repeat  = isset( $show_repeat ) ? $show_repeat : 1;
		?>
		<div id="<?php echo esc_attr( $id ); ?>-container" data-index="<?php echo esc_attr( $index ); ?>"
			 class="yit_options rm_option rm_input rm_text extrapoint-options">
			<div class="option">

				<input type="number" name="<?php echo esc_attr( $current_name ); ?>[number]" step="1" min="1"
					   id="<?php echo esc_attr( $current_id ); ?>-number"
					   value="<?php echo esc_attr( $number ); ?>"/>
				<span><?php echo esc_html( $label ); ?></span>

				<input type="number" name="<?php echo esc_attr( $current_name ); ?>[points]" step="1" min="1"
					   id="<?php echo esc_attr( $current_id ); ?>-points"
					   value="<?php echo esc_attr( $points ); ?>"/>
				<span><?php esc_html_e( 'Points', 'yith-woocommerce-points-and-rewards' ); ?></span>

				<?php if ( $show_repeat ) : ?>
					<input type="checkbox" name="<?php echo esc_attr( $current_name ); ?>[repeat]" value="1"
						   id="<?php echo esc_attr( $current_id ); ?>-repeat" <?php checked( $repeat, 1, 1 ); ?>>
					<small><?php esc_html_e( 'repeat', 'yith-woocommerce-points-and-rewards' ); ?></small>
				<?php endif ?>
				<?php if ( $multiple ) : ?>

						<span class="ywpar-add-row"></span>
						<span class="ywpar-remove-row <?php echo esc_attr( $hide_remove ); ?>"></span>

				<?php endif ?>
			</div>

		</div>
		<div class="clear"></div>

		<?php
	endforeach;
endif;
?>
