<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php global $post, $woocommerce; ?>
<form name="warranty_form" id="warranty_form" method="POST" action="" enctype="multipart/form-data" >
	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	$request_data = warranty_request_data();
	?>
	<div class="wfb-field-div">
		<label for="first_name" class="wfb-field-label"><?php esc_html_e( 'Name', 'wc_warranty' ); ?></label>
		<input type="text" name="first_name" id="first_name" placeholder="First" value="<?php echo esc_attr( $defaults['first_name'] ); ?>" style="width:20%; margin-right: 10px;" />
		<input type="text" name="last_name" id="last_name" placeholder="Last" value="<?php echo esc_attr( $defaults['last_name'] ); ?>" style="width:20%;" />
	</div>

	<div class="wfb-field-div">
		<label for="email" class="wfb-field-label"><?php esc_html_e( 'Email Address', 'wc_warranty' ); ?></label>
		<input type="email" name="email" id="email" value="<?php echo esc_attr( $defaults['email'] ); ?>" />
	</div>

	<div class="wfb-field-div">
		<label for="order_id" class="wfb-field-label"><?php esc_html_e( 'Order Number', 'wc_warranty' ); ?></label>
		<input type="text" name="order_id" id="order_id" required value="<?php echo esc_attr( $order_id ); ?>" />
	</div>

	<?php if ( empty( $items ) ) { ?>
		<div class="wfb-field-div">
			<label for="product_name" class="wfb-field-label"><?php esc_html_e( 'Product', 'wc_warranty' ); ?></label>
			<input type="text" name="product_name" id="product_name" value="" />
		</div>
	<?php } else { ?>
		<div class="wfb-field-div">
			<label for="item_idx" class="wfb-field-label"><?php esc_html_e( 'Product', 'wc_warranty' ); ?></label>
			<select name="item_idx" id="item_idx">
				<?php
				foreach ( $items as $item_idx => $item ) {
					$product = $item->get_product();
					?>
					<?php // translators: %1$s: Item name, %2$d: Item quantity. ?>
					<option value="<?php echo esc_attr( $item_idx ); ?>"><?php printf( esc_html__( '%1$s x %2$d', 'wc_warranty' ), esc_html( $item->get_name() ), esc_html( $item->get_quantity() ) ); ?></option>
					<?php
				}
				?>
			</select>
		</div>
	<?php } ?>
	<?php WooCommerce_Warranty::render_warranty_form(); ?>
	<p>
		<input type="hidden" name="return" value="<?php echo esc_attr( $post->ID ); ?>" />
		<input type="hidden" name="warranty_key" value="<?php echo esc_attr( $warranty_key ); ?>" />
		<input type="hidden" name="req" value="new_return" />
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Submit', 'wc_warranty' ); ?>" class="button">
		<?php wp_nonce_field( 'wc_warranty_new_return_nonce', 'wc_new_return_nonce' ); ?>
	</p>

</form>
<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {
		$( 'body' ).addClass( 'woocommerce-page woocommerce' );
		$( '#warranty_form' ).submit( function() {
			var is_error = false;
			var fields = [];

			$( '#warranty_form' )
				.find( 'input[type=text], input[type=file], textarea, select' )
				.each( function() {
					if ( $( this ).hasClass( 'wfb-field' ) && $( this )
						.data( 'required' ) && !$( this ).val().trim() ) {
						is_error = true;

						var id = $( this ).attr( 'id' ) + '-div';
						var $label = $( '#' + id + ' label' ).clone();
						$label.find( 'span.required' ).remove();
						fields.push( $label.text().trim() );
					}
				} );

			if ( is_error ) {
				var msg = "<?php esc_html_e( 'Please complete the required fields and try submitting again. The following fields are incomplete:', 'wc_warranty' ); ?>\n";

				for ( var i in fields ) {
					msg += '\n\t-' + fields[i];
				}

				alert( msg );
				return false;
			}
		} );
	} );
</script>
