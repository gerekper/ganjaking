<?php
/**
 * Product Data - Options Pricing
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template variables.
 *
 * @var array $fields An array with the fields' data.
 */
?>
</div>
<div class="store-credit-pricing-options options_group show_if_store_credit">
	<?php
	foreach ( $fields as $key => $field ) :
		wc_store_credit_meta_box_field( $field );
	endforeach;
	?>
