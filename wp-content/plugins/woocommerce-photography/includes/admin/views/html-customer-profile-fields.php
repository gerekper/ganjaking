<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<table class="form-table">
	<tr class="form-field collection-form-field">
		<th scope="row">
			<label for="collections"><?php _e( 'Collections', 'woocommerce-photography' ); ?></label>
		</th>
		<td>
			<?php if ( version_compare( WC_VERSION, '3.0', '<' ) ) : ?>
				<input type="hidden" id="collections" class="wc-photography-collections-select" name="collections" style="width: 25em;" value="<?php echo implode( ',', array_keys( $collections ) ); ?>" data-selected='[<?php
					$total   = count( $collections );
					$current = 0;
				foreach ( $collections as $collection_id => $collection_name ) {
						$current++;

						echo '{"id": "' . $collection_id . '", "text": "' . esc_attr( $collection_name ) . '"}';
						echo ( $total !== $current ) ? ',' : '';
				}
				?>]' />
			<?php else : ?>
				<select
					class="wc-photography-collections-select"
					id="collections"
					name="collections[]"
					multiple="multiple"
					data-placeholder="<?php _e( 'Search for a collection&hellip;', 'woocommerce' ); ?>"
					style="width: 300px">
			<?php
			foreach ( $collections as $collection_id => $collection_name ) {
				?>
					<option value="<?php echo $collection_id; ?>" selected="selected"><?php echo $collection_name; ?></option>
			<?php
			}
			?>
				</select>
			<?php endif; ?>
			<div class="photography-add-collection">
				<a href="#"><?php _e( '+ Add Collection', 'woocommerce-photography' ); ?></a>
				<div class="fields">
					<input type="text" class="input-text regular-input new-collection" />
					<button type="submit" class="button"><?php _e( 'Add New Collection', 'woocommerce-photography' ); ?></button>
				</div>
			</div>
		</td>
	</tr>
</table>
