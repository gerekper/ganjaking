<?php
/**
 * Order API Resources HTML for meta box.
 *
 * @package WooCommerce API Manager/Admin/Meta boxes
 */

defined( 'ABSPATH' ) || exit;

$expires = '';

if ( ! empty( $resource ) ) {
	if ( WCAM()->get_wc_subs_exist() && ! empty( $resource->sub_id ) ) {
		$expires = ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $resource->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $resource->sub_id, 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' );
	} else {
		if ( WC_AM_API_RESOURCE_DATA_STORE()->is_access_expired( $resource->access_expires ) ) {
			$expires = __( 'Expired', 'woocommerce-api-manager' );
		} else {
			$expires = $resource->access_expires == 0 ? _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) : esc_attr( WC_AM_FORMAT()->unix_timestamp_to_date( $resource->access_expires ) );
		}
	}

	$version = WC_AM_PRODUCT_DATA_STORE()->get_meta( $resource->product_id, '_api_new_version' );
	?>

	<style>
        img.ui-datepicker-trigger {
            position: relative;
            top: 0.5em;
        }

        .activation-resources-help-tip .woocommerce-help-tip {
            display: inline;
            margin: 1px !important;
        }
	</style>

	<div class="wc-metaboxes">
		<div class="wc-metabox closed">
			<h3 class="fixed">
				<div style="padding: 1em; border-radius: 1em;" <?php if ( $i % 2 == 0 )
					echo ' class="alternate"' ?>>
					<span class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'woocommerce-api-manager' ); ?>"></span>
					<strong><?php printf( __( 'Product ID: %s | Product Title: %s | Activations: %s out of %s | Current Version: %s | Expires: %s', 'woocommerce-api-manager' ), $resource->product_id, $resource->product_title, $resource->activations_total, $resource->activations_purchased_total, esc_attr( ! empty( $version ) ? $version : '' ), esc_html( $expires ) ); ?></strong>
				</div>
			</h3>
			<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
				<tbody>
				<tr>
					<td>
						<label for="poak<?php echo $i; ?>"><?php esc_html_e( 'Product Order API Key:', 'woocommerce-api-manager' ); ?></label>
						<input type="text" class="short am_expand_text_box" id="poak<?php echo $i; ?>" name="product_order_api_key[<?php echo $i; ?>]"
						       value="<?php esc_attr_e( $resource->product_order_api_key ); ?>" readonly/>
					</td>
					<td>
						<label><span style="display: inline"><?php esc_html_e( 'Activation Limit Total:', 'woocommerce-api-manager' ); ?></span>
							<span class="activation-resources-help-tip">
                                <?php echo wc_help_tip( __( 'A value less than the current Activation Limit Total is not valid.', 'woocommerce-api-manager' ) ) ?>
                            </span>
						</label>
						<div id="activations-purchased-total-div<?php echo $i; ?>">
							<input type="number" id="activations_purchased_total[<?php echo $i; ?>]"
							       class="short"
							       name="activations_purchased_total[<?php echo $i; ?>]" step="1" min="<?php echo $resource->activations_purchased_total ?>"
							       value="<?php esc_attr_e( $resource->activations_purchased_total ) ?>"
							       placeholder="<?php esc_html_e( '1', 'woocommerce-api-manager' ); ?>"/>
						</div>
					</td>
					<input type="hidden" id="current_activations_purchased_total[<?php echo $i; ?>]" name="current_activations_purchased_total[<?php echo $i; ?>]"
					       value="<?php echo $resource->activations_purchased_total ?>">
					<td>
						<label><?php esc_html_e( 'Current Version:', 'woocommerce-api-manager' ); ?></label>
						<input type="text" class="short" name="version[<?php echo $i; ?>]"
						       value="<?php echo esc_attr( ! empty( $version ) ? $version : '' ) ?>"
						       placeholder="<?php esc_html_e( 'Required', 'woocommerce-api-manager' ); ?>" readonly/>
					</td>
				</tr>
				<tr>
					<td>
						<label><?php esc_html_e( 'Resource Title:', 'woocommerce-api-manager' ); ?></label>
						<input type="text" class="am_tooltip short am_expand_text_box" name="product_title[<?php echo $i; ?>]"
						       value="<?php esc_attr_e( $resource->product_title ) ?>"
						       placeholder="<?php esc_html_e( 'Required', 'woocommerce-api-manager' ); ?>" readonly/>
					</td>
					<td>
						<label><?php esc_html_e( 'Product ID:', 'woocommerce-api-manager' ); ?></label>

						<div style="display: inline-block; vertical-align: middle;">
							<input type="text" class="short" name="product_id[<?php echo $i; ?>]"
							       value="<?php esc_attr_e( $resource->product_id ) ?>"
							       placeholder="<?php esc_html_e( 'Required', 'woocommerce-api-manager' ); ?>" readonly/>
						</div>
						<div style="display: inline-block; vertical-align: middle;">
                            <span style="text-decoration: none;">
                            <?php echo '<a href="' . esc_url( admin_url() . 'post.php?post=' . WC_AM_PRODUCT_DATA_STORE()->get_parent_product_id( $resource->product_id ) . '&action=edit' ) . '" title="' . WC_AM_API_RESOURCE_DATA_STORE()->get_title_by_api_resource_id( $resource->api_resource_id ) . '" target="_blank">' ?>
                        </span>
							<span style="text-decoration: none;" class="dashicons dashicons-admin-links"></span></a>
						</div>
					</td>
					<td>
						<label><?php esc_html_e( 'Access Expires:', 'woocommerce-api-manager' ); ?>
							<?php
							if ( empty( $resource->sub_id ) && ! empty( $resource->access_expires ) ) {
								?><span class="activation-resources-help-tip"><?php
								echo wc_help_tip( __( 'A date in the future can be chosen to extend the Access Expires value.', 'woocommerce-api-manager' ) );
								?></span><?php
							}
							?>
						</label>
						<input type="text" class="short" id="wc_am_access_expires_api_resources_<?php echo $i; ?>" name="access_expires[<?php echo $i; ?>]"
						       value="<?php esc_html_e( $expires ) ?>"
						       placeholder="<?php esc_html_e( 'Required', 'woocommerce-api-manager' ); ?>" readonly/>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>