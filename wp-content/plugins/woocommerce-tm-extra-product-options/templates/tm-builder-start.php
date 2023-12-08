<?php
/**
 * The template for displaying the start of the builder mode options
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-builder-start.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $field_id ) ) :

	$field_id = (string) $field_id;
	?>
<li id="<?php echo esc_attr( $field_id ); ?>" class="tm-extra-product-options-field tc-row tc-cell tcwidth tcwidth-100">
	<?php
endif;
