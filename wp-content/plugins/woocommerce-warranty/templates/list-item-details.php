<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<tr id="inline-edit-<?php echo esc_attr( $request['ID'] ); ?>" class="inline-edit-row inline-edit-row-post inline-edit-product quick-edit-row quick-edit-row-post inline-edit-product" style="display: none">
	<td colspan="<?php echo esc_attr( $this->num_columns ); ?>" class="colspanchange">
		<div class="warranty-update-message warranty-updated hidden"><p></p></div>

		<fieldset class="inline-edit-col">
			<div class="warranty-comments" style="float: right;">
				<h4><?php esc_html_e( 'Admin Notes', 'wc_warranty' ); ?></h4>

				<ul class="admin-notes">
					<?php
					require WooCommerce_Warranty::$base_path . 'templates/list-item-notes.php';
					?>
				</ul>
				<div class="add-note">
					<h4><?php esc_html_e( 'Add Note', 'wc_warranty' ); ?></h4>

					<p>
						<textarea rows="3" cols="35" class="input-text" id="admin_note_<?php echo esc_attr( $request['ID'] ); ?>" name="" type="text"></textarea>
					</p>

					<p>
						<a class="add_note button" data-request="<?php echo esc_attr( $request['ID'] ); ?>" href="#"><?php esc_html_e( 'Add', 'wc_warranty' ); ?></a>
					</p>
				</div>
			</div>

			<div class="inline-edit-col">
				<div class="codes_form closeable">
					<?php if ( ! empty( $this->inputs ) ) : ?>
						<h4><?php esc_html_e( 'Additional Details', 'wc_warranty' ); ?></h4>
						<?php
						foreach ( $this->inputs as $input ) {
							$key        = $input->key;
							$input_type = $input->type;
							$field      = $this->form['fields'][ $input->key ];

							if ( 'paragraph' === $input_type ) {
								continue;
							}

							$value = get_post_meta( $request['ID'], '_field_' . $key, true );

							if ( is_array( $value ) ) {
								$value = implode( ',<br/>', $value );
							}

							if ( 'file' === $input_type && ! empty( $value ) ) {
								$value = WooCommerce_Warranty::get_uploaded_file_anchor_tag( $value, 'customer' );
							}


							if ( empty( $value ) && ! empty( $item['reason'] ) && ! $this->row_reason_injected ) {
								$value                     = $item['reason'];
								$this->row_reason_injected = true;
							}

							if ( ! $value ) {
								$value = '-';
							}

							?>
							<p>
								<strong><?php echo esc_html( $field['name'] ); ?></strong>
								<br/>
								<?php echo wp_kses_post( $value ); ?>
							</p>
							<?php
						}
						?>
					<?php endif; ?>

					<h4><?php esc_html_e( 'Return shipping details', 'wc_warranty' ); ?></h4>
					<?php

					$shipping_label_id = get_post_meta( $request['ID'], '_warranty_shipping_label', true );

					if ( $shipping_label_id ) {
						$lnk = wp_get_attachment_url( $shipping_label_id );
						echo '<a href="' . esc_url( $lnk ) . '"><strong>Download the Shipping Label</strong></a>';
					} else {
						?>
						<input name="shipping_label_image" id="shipping_label_<?php echo esc_attr( $request['ID'] ); ?>" class="shipping-label-url short-text" type="text" value="" />
						<input name="shipping_label_image_id" id="shipping_label_id_<?php echo esc_attr( $request['ID'] ); ?>" type="hidden" value="" />
						<input name="shipping_label_image_file" id="shipping_label_image_file_<?php echo esc_attr( $request['ID'] ); ?>" data-request_id="<?php echo esc_attr( $request['ID'] ); ?>" data-security="<?php echo esc_attr( wp_create_nonce( 'shipping_label_image_file_upload' ) ); ?>" type="file" style="display: none;">
						<input class="rma-upload-button button" type="button" data-id="<?php echo esc_attr( $request['ID'] ); ?>" data-uploader_title="<?php esc_html_e( 'Set Shipping Label', 'wc_warranty' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Set Shipping Label', 'wc_warranty' ); ?>" value="<?php esc_attr_e( 'Select Shipping Label', 'wc_warranty' ); ?>" />
						<?php
					} // End final If Checking the attachment :)
					?>
				</div>
			</div>

			<div class="inline-edit-col warranty-tracking">
				<h4><?php esc_html_e( 'Return tracking details', 'wc_warranty' ); ?></h4>

				<?php
				// if tracking code is being requested, notify the admin
				$class = 'hidden';
				if ( $request['request_tracking_code'] == 'y' && empty( $request['tracking_code'] ) ) :
					$class = '';
				endif;
				?>
				<div class="codes_form closeable">
					<div class="wc-tracking-requested wc-updated <?php echo esc_attr( $class ); ?>"><p><?php esc_html_e( 'Tracking information requested from customer', 'wc_warranty' ); ?></p></div>

					<?php
					// Tracking code hasnt been requested yet
					if ( $request['request_tracking_code'] != 'y' ) :
						?>
						<div class="request-tracking-div">
							<label>
								<input type="checkbox" name="request_tracking" value="1" />
								<strong><?php esc_html_e( 'Request tracking code from the Customer', 'wc_warranty' ); ?></strong>
							</label>
						</div>
						<?php
					else : // tracking code requested
						// if tracking code is not empty, it has already been provided
						if ( ! empty( $request['tracking_code'] ) ) {
							echo '<strong>' . esc_html__( 'Customer Provided Tracking', 'wc_warranty' ) . ':</strong>&nbsp;';

							if ( ! empty( $request['tracking_provider'] ) ) {
								$all_providers = array();

								foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
									foreach ( $providers as $provider => $format ) {
										$all_providers[ sanitize_title( $provider ) ] = $format;
									}
								}

								$provider      = esc_html( $request['tracking_provider'] );
								$tracking_code = esc_html( $request['tracking_code'] );
								$link          = $all_providers[ $provider ];
								$link          = str_replace( '%1$s', $tracking_code, $link );
								$link          = str_replace( '%2$s', '', $link );
								printf( '%s via %s (<a href="%s" target="_blank">' . esc_html__( 'Track Shipment', 'wc_warranty' ) . '</a>)', esc_html( $tracking_code ), esc_html( $provider ), esc_url( $link ) );
							} else {
								echo esc_html( $request['tracking_code'] );
							}
						}
					endif;
					?>
				</div>

				<div class="codes_form closeable">
					<div class="wc-tracking-saved wc-updated hidden"><p><?php esc_html_e( 'Shipping/Tracking data saved', 'wc_warranty' ); ?></p></div>
					<?php
					if ( ! empty( $request['return_tracking_provider'] ) ) :
						?>
						<p>
							<label for="return_tracking_provider_<?php echo esc_attr( $request['ID'] ); ?>"><strong><?php esc_html_e( 'Shipping Provider', 'wc_warranty' ); ?></strong></label>
							<select class="return_tracking_provider" name="return_tracking_provider" id="return_tracking_provider_<?php echo esc_attr( $request['ID'] ); ?>">
								<?php
								foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
									echo '<optgroup label="' . esc_attr( $provider_group ) . '">';
									foreach ( $providers as $provider => $url ) {
										$selected = ( sanitize_title( $provider ) === $request['return_tracking_provider'] ) ? 'selected' : '';
										echo '<option value="' . esc_attr( sanitize_title( $provider ) ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $provider ) . '</option>';
									}
									echo '</optgroup>';
								}
								?>
							</select>
						</p>
						<p>
							<label for="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>"><strong><?php esc_html_e( 'Tracking details ', 'wc_warranty' ); ?></strong></label>
							<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>" value="<?php echo esc_attr( $request['return_tracking_code'] ); ?>" placeholder="<?php esc_attr_e( 'Enter the shipment tracking number', 'wc_warranty' ); ?>" />
							<span class="description"><?php esc_html_e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
						</p>
					<?php else : ?>
						<p>
							<label for="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>"><strong><?php esc_html_e( 'Tracking details ', 'wc_warranty' ); ?></strong></label>
							<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>" value="<?php echo esc_attr( $request['return_tracking_code'] ); ?>" placeholder="Enter the shipment tracking number " />
							<span class="description"><?php esc_html_e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</fieldset>

		<div class="submit inline-edit-save">
			<div class="alignright">
				<a class="button-primary" target="_blank" href="<?php echo esc_url( wp_nonce_url( 'admin-post.php?action=warranty_print&request=' . $request['ID'], 'warranty_print' ) ); ?>"><?php esc_html_e( 'Print', 'wc_warranty' ); ?></a>
				<input type="button" class="button-primary rma-update" data-id="<?php echo esc_attr( $request['ID'] ); ?>" data-security="<?php echo esc_attr( $update_nonce ); ?>" value="<?php esc_attr_e( 'Update', 'wc_warranty' ); ?>" />
			</div>
			<input type="button" class="button close_tr" value="<?php esc_attr_e( 'Close', 'wc_warranty' ); ?>" />
		</div>
	</td>
</tr>
