<?php
/**
 * Product Data - Store Credit
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variables.
 *
 * @var array $fields
 */
?>
<div id="store_credit_product_data" class="panel woocommerce_options_panel">
	<div class="options_group">
		<?php
		foreach ( $fields as $key => $field ) :
			wc_store_credit_meta_box_field( $field );
		endforeach;
		?>
	</div>
</div>
