<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php _e( 'Use this screen to upload your photographs. Before uploading, set any of the following parameters to apply them to each uploaded photograph.', 'woocommerce-photography' ); ?></p>

	<div id="wc-photography-uploader">
		<div id="wc-photography-uploader-error"></div>
		<div id="wc-photography-uploader-upload-ui" class="hide-if-no-js">

			<table class="form-table">
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc-photography-batch-sku"><?php _e( 'SKU Pattern', 'woocommerce-photography' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'SKU Pattern', 'woocommerce-photography' ); ?></span></legend>
							<input class="input-text regular-input" type="text" name="sku" id="wc-photography-batch-sku">
							<span class="description"><?php echo sprintf( __( 'Specify a pattern to ensure your photos have a unique SKU. E.g. %swc-%s', 'woocommerce-photography' ), '<code>', '</code>' ); ?></span>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc-photography-batch-price"><?php _e( 'Price', 'woocommerce-photography' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Price', 'woocommerce-photography' ); ?></span></legend>
							<input class="wc_input_price input-text regular-input" type="text" name="price" id="wc-photography-batch-price" placeholder="0">
							<span class="description"><?php echo _e( 'Set a global price that will be set for each photo uploaded.', 'woocommerce-photography' ); ?></span>
						</fieldset>
					</td>
				</tr>
				<tr class="collection-form-field" valign="top">
					<th scope="row" class="titledesc">
						<label for="wc-photography-batch-collection"><?php _e( 'Collections', 'woocommerce-photography' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Collections', 'woocommerce-photography' ); ?></span></legend>
							<?php if ( version_compare( WC_VERSION, '3.0', '<' ) ): ?>

								<input type="hidden" id="wc-photography-batch-collection" class="wc-photography-collections-select" name="collections" style="width: 300px;" />
							<?php else: ?>
								<select
									class="wc-photography-collections-select"
									id="wc-photography-batch-collection"
									name="collections[]"
									multiple="multiple"
									data-placeholder="<?php _e('Search for a collection&hellip;', 'woocommerce'); ?>"
									style="width: 300px">
								</select>
							<?php endif; ?>

							<span class="description"><?php echo _e( 'Specify which collection(s) these photos belong to.', 'woocommerce-photography' ); ?></span>
							<div class="photography-add-collection">
								<a href="#"><?php _e( '+ Add Collection', 'woocommerce-photography' ); ?></a>
								<div class="fields">
									<input type="text" class="input-text regular-input new-collection" />
									<button type="submit" class="button"><?php _e( 'Add New Collection', 'woocommerce-photography' ); ?></button>
								</div>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php do_action( 'wc_photography_batch_upload_fields' ); ?>

			<div id="wc-photography-drag-drop-area">
				<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php _e( 'Drop images here', 'woocommerce-photography' ); ?></p>
					<p><?php _ex( 'or', 'Uploader: Drop images here - or - Select Images', 'woocommerce-photography' ); ?></p>
					<p class="drag-drop-buttons">
						<input id="wc-photography-uploader-browse-button" type="button" value="<?php esc_attr_e( 'Select Images', 'woocommerce-photography' ); ?>" class="button" />
					</p>
				</div>
			</div>

			<?php do_action( 'wc_photography_batch_upload_fields_after' ); ?>

			<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', 'woocommerce-photography' ), esc_html( size_format( $max_upload_size ) ) ); ?></p>
		</div>

		<div id="wc-photography-html-upload-ui" class="hide-if-js">
			<p><?php _e( 'You can\'t send images because your browser is too old or do not have JavaScript enabled!', 'woocommerce-photography' ); ?></p>
		</div>
	</div>

	<div id="wc-photography-image-edit" class="meta-box-sortables" style="display: none;">

		<p class="submit"><button type="button" class="button button-primary"><?php _e( 'Save Changes', 'woocommerce-photography' ); ?></button></p>

		<div class="postbox">
			<div class="wc-metaboxes-wrapper">
				<p class="toolbar">
					<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce-photography' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce-photography' ); ?></a>
					<strong><?php _e( 'Photographs', 'woocommerce-photography' ); ?></strong>
				</p>

				<div class="wc-metaboxes">
				</div>
			</div>
		</div>

		<p class="submit"><button type="button" class="button button-primary"><?php _e( 'Save Changes', 'woocommerce-photography' ); ?></button></p>
	</div>

	<script type="text/template" id="wc-photography-image-template">
		<div id="photography-<%- id %>" class="wc-metabox closed" data-index="<%- index %>" data-id="<%- id %>">
			<h3>
				<img src="<%- thumbnail %>" alt="" class="thumbnail" />
				<button type="button" class="remove button"><?php _e( 'Remove', 'woocommerce-photography' ); ?></button>
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-photography' ); ?>"></div>
				<strong class="image-name"><%- index %>. <%- sku %></strong>
			</h3>
			<div class="wc-metabox-content">
				<div class="fields">
					<?php do_action( 'wc_photography_batch_upload_edit_fields' ); ?>
					<p class="form-field first">
						<label for="photography-<%- id %>-sku"><?php _e( 'SKU', 'woocommerce-photography' ); ?></label>
						<input type="text" id="photography-<%- id %>-sku" class="short sku-field" name="photography[<%- id %>][sku]" value="<%- sku %>" />
					</p>
					<p class="form-field last">
						<label for="photography-<%- id %>-price"><?php _e( 'Price', 'woocommerce-photography' ); ?></label>
						<input type="text" id="photography-<%- id %>-price" class="wc_input_price price-field input-text regular-input" name="photography[<%- id %>][price]" value="<%- price %>" />
					</p>
					<div class="collection-form-field">
						<p class="form-field full">
							<label for="photography-<%- id %>-collections"><?php _e( 'Collections', 'woocommerce-photography' ); ?></label>
							<?php if ( version_compare( WC_VERSION, '3.0', '<' ) ): ?>
								<input type="hidden" id="photography-<%- id %>-collections" class="wc-photography-collections-select" name="photography[<%- id %>][collections]" style="width: 300px;" value="<%- collections_ids %>" data-selected='[<%
								var collectionsSize = _.size( collections ),
									current = 0;
								_.each( collections, function( collection_name, collection_id ) {
									current++;
									%>{"id": "<%- collection_id %>", "text": "<%- collection_name %>"}<% if ( current !== collectionsSize ){ %>,<% }
								}); %>]' />
							<?php else: ?>
								<select
									class="wc-photography-collections-select"
									id="photography-<%- id %>-collections"
									name="photography[<%- id %>][collections][]"
									multiple="multiple"
									data-placeholder="<?php _e('Search for a collection&hellip;', 'woocommerce'); ?>"
									style="width: 300px">
                                                                        <%
										var collectionsSize = _.size( collections ),
											current = 0;
										_.each( collections, function( collection_name, collection_id ) {
										current++;
										%><option value="<%- collection_id %>" selected="selected"><%- collection_name %></option><% }); %>
								</select>
							<?php endif; ?>
						</p>
						<p class="form-field full photography-add-collection">
							<a href="#"><?php _e( '+ Add Collection', 'woocommerce-photography' ); ?></a>
							<span class="fields">
								<input type="text" class="input-text regular-input new-collection" />
								<button type="submit" class="button"><?php _e( 'Add New Collection', 'woocommerce-photography' ); ?></button>
							</span>
						</p>
					</div>
					<p class="form-field full">
						<label for="photography-<%- id %>-caption"><?php _e( 'Caption', 'woocommerce-photography' ); ?></label>
						<textarea id="photography-<%- id %>-caption" rows="4" cols="50" class="caption-field" name="photography[<%- id %>][caption]"></textarea>
					</p>
					<?php do_action( 'wc_photography_batch_upload_after_edit_fields' ); ?>
				</div>
			</div>
		</div>
	</script>
</div>
