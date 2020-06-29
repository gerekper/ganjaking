<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_title   = __( 'Create add-ons', 'woocommerce-product-addons' );
$button_title = __( 'Publish', 'woocommerce-product-addons' );

if ( isset( $_POST ) && ! empty( $_POST['save_addon'] ) || ! empty( $_GET['edit'] ) ) {
	$page_title   = __( 'Edit Add-on', 'woocommerce-product-addons' );
	$button_title = __( 'Update', 'woocommerce-product-addons' );
}
?>
<div class="wrap woocommerce">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br/></div>

	<h2><?php echo esc_html( $page_title ); ?></h2>

	<div><?php esc_html_e( 'Set up add-ons that apply to all products or specific product categories.', 'woocommerce-product-addons' ); ?></div><br />

	<form method="POST" action="">
		<table class="form-table global-addons-form meta-box-sortables">
			<tr>
				<th>
					<label for="addon-reference"><?php esc_html_e( 'Name', 'woocommerce-product-addons' ); ?></label>
				</th>
				<td>
					<input type="text" name="addon-reference" id="addon-reference" style="width:50%;" value="<?php echo esc_attr( $reference ); ?>" />
					<p class="description"><?php esc_html_e( 'This name is for your reference only and will not be visible to customers.', 'woocommerce-product-addons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="addon-priority"><?php esc_html_e( 'Priority', 'woocommerce-product-addons' ); ?></label>
				</th>
				<td>
					<input type="text" name="addon-priority" id="addon-priority" style="width:50%;" value="<?php echo esc_attr( $priority ); ?>" />
					<p class="description"><?php esc_html_e( 'This determines the order when there are multiple add-ons. Add-ons for individual products are set to order 10.', 'woocommerce-product-addons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="addon-objects"><?php esc_html_e( 'Product Categories', 'woocommerce-product-addons' ); ?></label>
				</th>
				<td>
					<select id="addon-objects" name="addon-objects[]" multiple="multiple" style="width:50%;" data-placeholder="<?php esc_attr_e( 'Choose categories&hellip;', 'woocommerce-product-addons' ); ?>" class="wc-enhanced-select wc-pao-enhanced-select">
						<option value="all" <?php selected( in_array( 'all', $objects ), true ); ?>><?php esc_html_e( 'All Products', 'woocommerce-product-addons' ); ?></option>
						<optgroup label="<?php esc_attr_e( 'Product categories', 'woocommerce-product-addons' ); ?>">
							<?php
							$terms = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );

							foreach ( $terms as $term ) {
								echo '<option value="' . $term->term_id . '" ' . selected( in_array( $term->term_id, $objects ), true, false ) . '>' . $term->name . '</option>';
							}
							?>
						</optgroup>
						<?php do_action( 'woocommerce_product_addons_global_edit_objects', $objects ); ?>
					</select>
					<p class="description"><?php esc_html_e( 'Select which categories this add-on should apply to. Create add-ons for a single product when editing that product.', 'woocommerce-product-addons' ); ?></p>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<hr />
				</td>
			</tr>

			<tr>
				<td id="poststuff" class="postbox" colspan="2">
					<?php
					$exists = false;
					include( dirname( __FILE__ ) . '/html-addon-panel.php' );
					?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="hidden" name="edit_id" value="<?php echo ( ! empty( $edit_id ) ? esc_attr( $edit_id ) : '' ); ?>" />
			<input type="hidden" name="save_addon" value="true" />
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( $button_title ); ?>">
		</p>
	</form>
</div>

<script type="text/javascript">
	jQuery( function( $ ) {
		$( '.wc-enhanced-select' ).on( 'select2:select', function( e ) {
			var selectedID = e.params.data.id,
				values     = $( '.wc-enhanced-select' ).val(),
				all        = 'all',
				allIndex   = values.indexOf( all );

			if ( all === selectedID ) {
				values = [ all ];
			} else if ( 0 === allIndex ) {
				values.splice( allIndex, 1 );
			}

			$( '.wc-enhanced-select' ).val( values ).trigger( 'change.select2' );
		} ); 
	} );
</script>
