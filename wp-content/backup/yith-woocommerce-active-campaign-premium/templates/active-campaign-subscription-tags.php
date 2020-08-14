<?php
/**
 * Subscription checkbox template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly
?>
<fieldset>
	<legend><?php echo esc_html( $show_tags_label ); ?></legend>
	<?php
	foreach ( $selected_show_tags as $tag_id => $tag_label ) {
		?>
		<input type="checkbox" name="yith_wcac_subscribe_tags[<?php echo esc_attr( $tag_id ); ?>]" value="yes"/> <?php echo esc_html( $tag_label ); ?>
		<br/>
		<?php
	}
	?>
</fieldset>
