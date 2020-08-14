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
 * @since      1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
extract( $field );

?>
<div id="<?php echo esc_attr( $id ); ?>-container" class="yit_options rm_option rm_input rm_text">
	<div class="option">
		<?php if ( $show_datepicker ) : ?>
		<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" class="panel-datepicker"/>
		<?php endif; ?>
		<button id="<?php echo esc_attr( $id ); ?>_btn" class="button button-primary"><?php echo esc_html( $label ); ?></button>
	</div>
	<div class="clear"></div>
</div>
