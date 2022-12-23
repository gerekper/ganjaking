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
			<select
				class="wc-photography-collections-select"
				id="collections"
				name="collections[]"
				multiple="multiple"
				data-placeholder="<?php _e( 'Search for a collection&hellip;', 'woocommerce-photography' ); ?>"
				style="width: 300px">
				<?php foreach ( $collections as $collection_id => $collection_name ) : ?>
					<option value="<?php echo $collection_id; ?>" selected="selected"><?php echo $collection_name; ?></option>
				<?php endforeach; ?>
			</select>
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
