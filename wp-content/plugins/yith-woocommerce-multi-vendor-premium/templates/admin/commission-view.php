<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @var YITH_Commission $commission
 */

//string added @version 1.14.1 - ALL FILE
$order               = $commission->get_order();
$user                = $commission->get_user();
$vendor              = $commission->get_vendor();
$product             = $commission->get_product();
$item                = $commission->get_item();
$item_id             = $commission->line_item_id;
$tax_data            = empty( $legacy_order ) && wc_tax_enabled() ? maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' ) : false;
$order_taxes         = $order instanceof WC_order ? $order->get_taxes() : array();
$back_url = isset( $back_url ) ? $back_url : remove_query_arg( 'view' );
$line_items_shipping = $order instanceof WC_order ? $order->get_items( 'shipping' ) : array();
$currency            = $order instanceof WC_order ? array( 'currency' => yith_wcmv_get_order_currency( $order ) ) : get_woocommerce_currency();

?>

<div class="wrap">
	<h2>
		<?php _e( 'View Commission', 'yith-woocommerce-product-vendors' ) ?>
		<a href="<?php echo apply_filters( 'yith_wcmv_back_to_commissions_list_url', esc_url( $back_url ) ) ?>" class="add-new-h2"><?php _e( 'Back', 'yith-woocommerce-product-vendors' ) ?></a>
	</h2>

	<?php YITH_Commissions()->admin_notice(); ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">

					<?php if ( $vendor->is_super_user() ) : ?>
					<form id="woocommerce-order-actions" class="postbox" method="GET">
						<h3 class="hndle ui-sortable-handle"><span><?php _e( 'Commission Actions', 'yith-woocommerce-product-vendors' ) ?></span></h3>
						<div class="inside">
							<ul class="order_actions submitbox">

								<li class="wide" id="actions">
									<select name="new_status">
										<option value=""><?php _e( 'Actions', 'yith-woocommerce-product-vendors' ) ?></option>
										<?php foreach ( YITH_Commissions()->get_status() as $status => $display ) : if ( ! YITH_Commissions()->is_status_changing_permitted( $status, $commission->status ) ) continue; ?>
											<option value="<?php echo $status ?>"><?php printf( __( 'Change to %s', 'yith-woocommerce-product-vendors' ), $display ) ?></option>
										<?php endforeach; ?>
									</select>

									<input type="hidden" name="action" value="yith_commission_table_actions" />
									<input type="hidden" name="view" value="<?php echo $commission->id ?>" />
									<button type="submit" class="button wc-reload" title="<?php _e( 'Apply', 'yith-woocommerce-product-vendors' ) ?>"><span><?php _e( 'Apply', 'yith-woocommerce-product-vendors' ) ?></span></button>
								</li>

							</ul>
						</div>
					</form>
					<?php endif; ?>

					<div id="woocommerce-order-notes" class="postbox">
						<h3 class="hndle ui-sortable-handle"><span><?php _e( 'Commission notes', 'yith-woocommerce-product-vendors' ) ?></span></h3>
						<div class="inside">
							<ul class="order_notes">

								<?php if ( $notes = $commission->get_notes() ) : ?>
									<?php foreach ( $notes as $note ) : ?>
										<li rel="<?php echo $note->ID ?>" class="note">
											<div class="note_content">
												<p><?php echo $note->description ?></p>
											</div>
											<p class="meta">
												<abbr class="exact-date" title="<?php echo $note->note_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'yith-woocommerce-product-vendors' ), date_i18n( wc_date_format(), strtotime( $note->note_date ) ), date_i18n( wc_time_format(), strtotime( $note->note_date ) ) ); ?></abbr>
											</p>
										</li>
									<?php endforeach; ?>
								<?php else : ?>
									<li><?php _e( 'There are no notes yet.', 'yith-woocommerce-product-vendors' ) ?></li>
								<?php endif; ?>

							</ul>
						</div>
					</div>

				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">

							<style type="text/css">
								#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
							</style>

							<div class="panel-wrap woocommerce">
								<div id="order_data" class="yith-commission panel">
									<h2 class="commission-details-<?php echo $commission->type ?>">
                                        <?php $commission_title_label = 'product' == $commission->type ? _x( 'Commission', '[admin] part of commission details', 'yith-woocommerce-product-vendors' ) : _x( 'Shipping fee', '[admin] part of shipping fee details', 'yith-woocommerce-product-vendors' ); ?>
                                        <?php printf( '%s #%s %s', $commission_title_label, $commission->id, _x( 'details', '[admin] part of commission details', 'yith-woocommerce-product-vendors' ) ); ?>
                                    </h2>
                                    <p class="order_number">
										<?php
										$user_info = $commission->get_user();

										if ( ! empty( $user_info ) ) {

											$current_user_can = apply_filters( 'yith_wcmv_commission_details_current_user_can_edit_users', ( current_user_can( 'edit_users' ) || get_current_user_id() == $user_info->ID ) );

                                            $username = $current_user_can ? '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">' : '';

                                            if ( $user_info->first_name || $user_info->last_name ) {
                                                $username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
                                            }
                                            else {
                                                $username .= esc_html( ucfirst( $user_info->display_name ) );
                                            }

                                            if ( $current_user_can ) {
                                                $username .= '</a>';
                                            }
                                        }
										else {
											$billing_first_name = $billing_last_name = '';
										    if( $order instanceof WC_Order ){
											    $billing_first_name = yit_get_prop( $order, 'billing_first_name' );
											    $billing_last_name  = yit_get_prop( $order, 'billing_last_name' );
                                            }

											if ( $billing_first_name || $billing_last_name ) {
												$username = trim( $billing_first_name . ' ' . $billing_last_name );
											}
				 							else {
												$username = __( 'Guest', 'yith-woocommerce-product-vendors' );
											}
                                        }

										$order_id     = $commission->order_id;
										$order_number = $order instanceof WC_Order ? $order->get_order_number() : $order_id;
										$order_number = '<strong>#' . esc_attr( $order_number ) . '</strong>';
										$order_uri    = sprintf( '<a href="%s">%s</a>', apply_filters( 'yith_wcmv_commission_get_order_uri', 'post.php?post=' . absint( $order_id ) . '&action=edit', $order_id, $order ), $order_number );
										$order_info   = $vendor->is_super_user() ? $order_uri : $order_number;

                                        if( $vendor->is_super_user() ){
                                            $order_info = $order_uri;
                                        }

                                        else if( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && $vendor->has_limited_access() && wp_get_post_parent_id( $order_id )&& in_array( $order_id, $vendor->get_orders() ) ){
                                            $order_info = $order_uri;
                                        }

                                        else {
                                            $order_info = $order_number;
                                        }

                                        $order_status = $order instanceof WC_Order ? yith_wcmv_get_order_status( $order, 'display' ) : __( 'Order Deleted', 'yith-woocommerce-product-vendors' );

                                        if( ! $order instanceof WC_Order ){
                                            $order_info = strip_tags( $order_info );
                                        }

										printf( _x( 'credited to %s &#8212; from order %s &#8212; order status: %s', 'Commission credited to [user]', 'yith-woocommerce-product-vendors' ), $username, $order_info, $order_status );
										?>
									</p>

									<div class="order_data_column_container">
										<div class="order_data_column">

											<h4><?php _e( 'General details', 'yith-woocommerce-product-vendors' ) ?></h4>
											<div class="address">
												<p>
													<strong><?php _e( 'Status', 'yith-woocommerce-product-vendors' ) ?>:</strong>
													<?php echo $commission->get_status('display') ?>
												</p>
												<p>
													<strong><?php _e( 'Date', 'yith-woocommerce-product-vendors' ) ?>:</strong>
													<?php echo $commission->get_date('display') ?>
												</p>
												<p>
													<strong><?php _e( 'Last update', 'yith-woocommerce-product-vendors' ) ?>:</strong>
													<?php
													$date = ! empty( $commission->last_edit ) && strpos( $commission->last_edit, '0000-00-00' ) ? $commission->last_edit : $commission->get_date();
													$t_time = date_i18n( __( 'Y/m/d g:i:s A', 'yith-woocommerce-product-vendors' ), mysql2date( 'U', $date ) );
													$h_time = sprintf( __( '%s ago', 'yith-woocommerce-product-vendors' ), human_time_diff( mysql2date( 'U', $date ) ) );

													echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
													?>
												</p>
											</div>

										</div>
										<div class="order_data_column">

											<h4><?php _e( 'User details', 'yith-woocommerce-product-vendors' ) ?></h4>
											<div class="address">
												<p>
                                                    <?php
                                                    if( ! empty( $user ) ) {
                                                        printf( '<strong>%1$s:</strong>',  __( 'Email', 'yith-woocommerce-product-vendors' ) );
                                                        printf( '<a href="mailto:%1$s">%1$s</a>', $user->user_email );
                                                    } else {
                                                        echo '<em>' . __( 'User deleted', 'yith-woocommerce-product-vendors' ) . '</em>';
                                                    }
                                                    ?>
												</p>
												<p>
													<strong><?php _e( 'Vendor', 'yith-woocommerce-product-vendors' ) ?>:</strong>
													<?php
                                                    if( $vendor->is_valid() ) {
                                                        $vendor_url  = get_edit_term_link( $vendor->id, $vendor->taxonomy );
													    echo ! empty( $vendor_url ) ? "<a href='{$vendor_url}' target='_blank'>{$vendor->name}</a>" : $vendor->name;
                                                    } else {
                                                        echo '<em>' . __( 'Vendor deleted', 'yith-woocommerce-product-vendors' ) . '</em>';
                                                    }
													?>
												</p>
												<p>
													<strong><?php _e( 'PayPal', 'yith-woocommerce-product-vendors' ) ?>:</strong>
                                                    <?php if( ! empty( $vendor->paypal_email ) ) : ?>
                                                        <a href="mailto:<?php echo $vendor->paypal_email ?>"><?php echo $vendor->paypal_email ?></a>
                                                    <?php else: ?>
                                                        <?php _e( 'Email address not set ', 'yith-woocommerce-product-vendors' ); ?>
                                                    <?php endif; ?>
												</p>
											</div>

										</div>
                                        <?php if( ! empty( $user ) ) : ?>
                                            <div class="order_data_column">
                                                <h4><?php _e( 'Billing information', 'yith-woocommerce-product-vendors' ) ?></h4>
                                                <div class="address">
                                                    <p>
                                                        <?php

                                                        // Formatted Addresses
                                                        $formatted = ( YITH_Vendors()->is_wc_2_7_or_greather && $order instanceof WC_Order ) ? $order->get_formatted_billing_address() :
                                                            WC()->countries->get_formatted_address( array(
                                                                'first_name'    => $user->first_name,
                                                                'last_name'     => $user->last_name,
                                                                'company'       => $user->billing_company,
                                                                'address_1'     => get_user_meta( $user->ID, 'billing_address_1', true ),
                                                                'address_2'     => get_user_meta( $user->ID, 'billing_address_2', true ),
                                                                'city'          => get_user_meta( $user->ID, 'billing_city', true ),
                                                                'state'         => get_user_meta( $user->ID, 'billing_state', true ),
                                                                'postcode'      => get_user_meta( $user->ID, 'billing_postcode', true ),
                                                                'country'       => get_user_meta( $user->ID, 'billing_country', true ),
                                                            )
                                                        );

                                                        echo wp_kses( $formatted, array( 'br' => array() ) )
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="order_data_column">
                                                <h4><?php _e( 'Shipping information', 'yith-woocommerce-product-vendors' ) ?></h4>
                                                <div class="address">
                                                    <p>
                                                        <?php

                                                        // Formatted Addresses
                                                        $formatted = ( YITH_Vendors()->is_wc_2_7_or_greather && $order instanceof WC_Order ) ? $order->get_formatted_shipping_address() :
                                                            WC()->countries->get_formatted_address( array(
                                                                'first_name'    => get_user_meta( $user->ID, 'shipping_first_name', true ),
                                                                'last_name'     => get_user_meta( $user->ID, 'shipping_last_name', true ),
                                                                'company'       => get_user_meta( $user->ID, 'shipping_company', true ),
                                                                'address_1'     => get_user_meta( $user->ID, 'shipping_address_1', true ),
                                                                'address_2'     => get_user_meta( $user->ID, 'shipping_address_2', true ),
                                                                'city'          => get_user_meta( $user->ID, 'shipping_city', true ),
                                                                'state'         => get_user_meta( $user->ID, 'shipping_state', true ),
                                                                'postcode'      => get_user_meta( $user->ID, 'shipping_postcode', true ),
                                                                'country'       => get_user_meta( $user->ID, 'shipping_country', true ),
                                                            )
                                                        );

                                                        echo wp_kses( $formatted, array( 'br' => array() ) )
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
									</div>

									<div class="clear"></div>

								</div>
							</div>

						</div>
					</div>
                    <div id="woocommerce-order-items" class="postbox">
                        <h3 class="hndle ui-sortable-handle"><span><?php _e( 'Item data', 'yith-woocommerce-product-vendors' ) ?></span></h3>
                        <div class="inside">

                            <div class="woocommerce_order_items_wrapper wc-order-items-editable">
                                <table cellpadding="0" cellspacing="0" class="woocommerce_order_items">

                                    <?php if ( 'product' == $commission->type ) : ?>
                                        <thead>
                                        <tr>
                                            <th class="item sortable" colspan="2"><?php _e( 'Item', 'yith-woocommerce-product-vendors' ) ?></th>
                                            <?php do_action( 'yith_wcmv_admin_order_item_headers', $order, $item, $item_id ); ?>
                                            <th class="item_cost sortable"><?php _e( 'Cost', 'yith-woocommerce-product-vendors' ) ?></th>
                                            <th class="quantity sortable"><?php _e( 'Qty', 'yith-woocommerce-product-vendors' ) ?></th>
                                            <th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'yith-woocommerce-product-vendors' ); ?></th>
                                            <?php
                                            if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
                                                foreach ( $order_taxes as $tax_id => $tax_item ) :
                                                    $tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
                                                    $tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'yith-woocommerce-product-vendors' );
                                                    $column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'yith-woocommerce-product-vendors' );
                                                    $column_tip     = $tax_item['name'] . ' (' . $tax_class_name . ')';
                                                    ?>
                                                    <th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
                                                        <?php echo esc_attr( $column_label ); ?>
                                                        <input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
                                                        <a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
                                                    </th>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </tr>
                                        </thead>

                                        <tbody id="order_line_items">
                                        <tr class="item Zero Rate" data-order_item_id="<?php echo $item_id ?>">

											<td class="thumb">
                                                <?php $product_id = $product_uri = ''; ?>
												<?php if ( $product ) : ?>
                                                    <?php $product_id = yit_get_prop( $product, 'id' ); ?>
                                                    <?php $product_uri = apply_filters( 'yith_wcmv_commission_get_product_uri', admin_url( 'post.php?post=' . absint( $product_id ) . '&action=edit' ), $product_id ); ?>
													<a href="<?php echo esc_url( $product_uri ); ?>" class="tips" data-tip="<?php

                                                    echo '<strong>' . __( 'Product ID:', 'yith-woocommerce-product-vendors' ) . '</strong> ' . absint( $item['product_id'] );

                                                    if ( $item['variation_id'] && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
                                                        echo '<br/><strong>' . __( 'Variation ID:', 'yith-woocommerce-product-vendors' ) . '</strong> ' . absint( $item['variation_id'] );
                                                    } elseif ( $item['variation_id'] ) {
                                                        echo '<br/><strong>' . __( 'Variation ID:', 'yith-woocommerce-product-vendors' ) . '</strong> ' . absint( $item['variation_id'] ) . ' (' . __( 'No longer exists', 'yith-woocommerce-product-vendors' ) . ')';
                                                    }

                                                    if ( $product && $product->get_sku() ) {
                                                        echo '<br/><strong>' . __( 'Product SKU:', 'yith-woocommerce-product-vendors' ).'</strong> ' . esc_html( $product->get_sku() );
                                                    }

                                                    $variation_data = $product->get_attributes();

                                                    if ( $product && 'variable' == $product->get_type() && isset( $variation_data ) ) {
                                                        echo '<br/>' . wc_get_formatted_variation( $variation_data, true );
                                                    }

                                                    ?>"><?php echo $product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a>
                                                <?php else : ?>
                                                    <?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
                                                <?php endif; ?>
                                            </td>

                                            <td class="name">

                                                <?php echo ( $product && $product->get_sku() ) ? esc_html( $product->get_sku() ) . ' &ndash; ' : ''; ?>

												<?php if ( $product ) : ?>
													<a target="_blank" href="<?php echo esc_url( $product_uri ); ?>">
														<?php echo esc_html( $item['name'] ); ?>
													</a>
												<?php else : ?>
													<?php echo esc_html( $item['name'] ); ?>
												<?php endif; ?>
                                                <div class="view">
                                                    <?php
                                                    global $wpdb;
                                                    $metadata = false;

                                                    if( YITH_Vendors()->is_wc_2_7_or_greather ){
                                                        $metadata = ! empty( $item ) ? $item->get_meta_data() : null;
                                                    }

                                                    else {
                                                        $metadata = $order->has_meta( $item_id );
                                                    }

                                                    if ( $metadata ) {
                                                        echo '<table cellspacing="0" class="display_meta">';
                                                        foreach ( $metadata as $single_meta ) {

                                                            $meta = yith_wcmv_get_meta_field( $single_meta );

                                                            // Skip hidden core fields
                                                            if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
                                                                '_qty',
                                                                '_tax_class',
                                                                '_product_id',
                                                                '_variation_id',
                                                                '_line_subtotal',
                                                                '_line_subtotal_tax',
                                                                '_line_total',
                                                                '_line_tax',
                                                                '_commission_id'
                                                            ) ) )
                                                            ) {
                                                                continue;
                                                            }

                                                            // Skip serialised meta
                                                            if ( is_serialized( $meta['meta_value'] ) ) {
                                                                continue;
                                                            }

                                                            // Get attribute data
                                                            if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
                                                                $term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
                                                                $meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
                                                                $meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
                                                            }
                                                            else {
                                                                $meta['meta_key'] = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta['meta_key'], $product ), $meta['meta_key'], $product );
                                                            }

                                                            if( is_string( $meta['meta_value'] ) ){
	                                                            echo '<tr><th>' . wp_kses_post( rawurldecode( $meta['meta_key'] ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta['meta_value'] ) ) ) ) . '</td></tr>';
                                                            }
                                                        }
                                                        echo '</table>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>

                                            <?php do_action( 'yith_wcmv_admin_order_item_values', $product, $item, absint( $item_id ) ); ?>

                                            <td class="item_cost" width="1%">
                                                <div class="view">
                                                    <?php
                                                    if ( isset( $item['line_total'] ) ) {
                                                        if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
                                                            echo '<del>' . wc_price( $order->get_item_subtotal( $item, false, true ), $currency ) . '</del> ';
                                                        }
                                                        echo wc_price( $order->get_item_total( $item, false, true ), $currency );
                                                    }
                                                    ?>
                                                </div>
                                            </td>

                                            <td class="quantity" width="1%">
                                                <div class="view">
                                                    <?php
                                                    echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : '';
                                                    $refunded_qty = $order instanceof WC_Order ? $order->get_qty_refunded_for_item( $item_id ) : 0;
                                                    if ( $refunded_qty ) {
                                                        echo '<small class="refunded">-' . $refunded_qty . '</small>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>

                                            <td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( isset( $item['line_total'] ) ? $item['line_total'] : '' ); ?>">
                                                <div class="view">
                                                    <?php
                                                    if ( isset( $item['line_total'] ) ) {
                                                        echo wc_price( $item['line_total'], $currency );
                                                    }

                                                    if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] !== $item['line_total'] ) {
                                                        echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $item['line_subtotal'] - $item['line_total'], '' ), $currency ) . '</span>';
                                                    }

                                                    $refunded = $order instanceof WC_Order ? $order->get_total_refunded_for_item( $item_id ): 0;

                                                    if ( $refunded ) {
                                                        echo '<small class="refunded">' . wc_price( $refunded, $currency ) . '</small>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>

                                            <?php
                                            if ( ! empty( $tax_data ) ) {
                                                foreach ( $order_taxes as $tax_item ) {
                                                    $tax_item_id       = $tax_item['rate_id'];
                                                    $tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
                                                    $tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
                                                    ?>
                                                    <td class="line_tax" width="1%">
                                                        <div class="view">
                                                            <?php
                                                            if ( '' != $tax_item_total ) {
                                                                echo wc_price( wc_round_tax_total( $tax_item_total ), $currency );
                                                            } else {
                                                                echo '&ndash;';
                                                            }

                                                            if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] !== $item['line_total'] ) {
                                                                echo '<span class="wc-order-item-discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), $currency ) . '</span>';
                                                            }

                                                            if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
                                                                echo '<small class="refunded">' . wc_price( $refunded, $currency ) . '</small>';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="edit" style="display: none;">
                                                            <div class="split-input">
                                                                <div class="input">
                                                                    <label><?php esc_attr_e( 'Pre-discount:', 'yith-woocommerce-product-vendors' ); ?></label>
                                                                    <input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" class="line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                                                                </div>
                                                                <div class="input">
                                                                    <label><?php esc_attr_e( 'Total:', 'yith-woocommerce-product-vendors' ); ?></label>
                                                                    <input type="text" name="line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" class="line_tax wc_input_price" data-total_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="refund" style="display: none;">
                                                            <input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                                                        </div>
                                                    </td>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tr>
                                        </tbody>
                                    <?php endif; ?>

                                    <tbody id="order_refunds">
                                    <?php foreach ( $commission->get_refunds() as $refund_id => $amount ) : $refund = new WC_Order_Refund( $refund_id ) ?>
                                        <tr class="refund Zero Rate">
                                            <td class="thumb">
                                                <div></div>
                                            </td>

                                            <td class="name">
                                                <?php  $date_created = yit_get_prop( $refund, 'date_created' );  ?>
                                                <?php if( class_exists( 'WC_DateTime' ) && $date_created instanceof WC_DateTime ){ $date_created = $date_created->getTimestamp(); }?>
                                                <?php echo esc_attr__( 'Refund', 'yith-woocommerce-product-vendors' ) . ' #' . absint( yit_get_prop( $refund, 'id' ) ) . ' - ' . esc_attr( date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $date_created ) );  ?>
                                                <?php $refund_reason = $refund->get_reason(); ?>
                                                <?php if ( $refund_reason ) : ?>
                                                    <p class="description">
                                                        <?php echo esc_html( $refund_reason ); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </td>

                                            <td class="quantity" width="1%">&nbsp;</td>

                                            <td class="line_cost" width="1%">
                                                <div class="view">
                                                    <?php echo wc_price( $amount ) ?>
                                                </div>
                                            </td>

                                            <td class="line_tax" width="1%"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>

                                </table>
                            </div>

                            <div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">

                                <?php

                                $coupons = $order instanceof WC_Order ? $order->get_items( array( 'coupon' ) ) : array();

                                if ( $coupons ) {
                                    ?>
                                    <div class="wc-used-coupons">
                                        <ul class="wc_coupon_list"><?php
                                            echo '<li><strong>' . __( 'Coupon(s) Used', 'yith-woocommerce-product-vendors' ) . '</strong></li>';
                                            foreach ( $coupons as $item_id => $item ) {
                                                global $wpdb;
                                                $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

                                                $link_before = $link_after = '';
                                                if ( current_user_can( 'manage_woocommerce' ) ) {
                                                    $link = $post_id ? esc_url( add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ) : add_query_arg( array( 's' => $item['name'], 'post_status' => 'all', 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) );
                                                    $link_before = '<a href="' . esc_url( $link ) . '" class="tips" data-tip="' . esc_attr( wc_price( $item['discount_amount'], $currency ) ) . '">';
                                                    $link_after = '</a>';
                                                }

                                                printf( '<li class="code">%s<span>' . esc_html( $item['name'] ). '</span>%s</li>', $link_before, $link_after );
                                            }
                                            ?></ul>
                                    </div>
                                    <?php
                                }
                                ?>

                                <table class="wc-order-totals">
                                    <tbody>
                                    <?php if( 'product' == $commission->type ) : ?>
                                        <tr>
                                            <td class="label"><?php _e( 'Rate', 'yith-woocommerce-product-vendors' ) ?>:</td>
                                            <td class="total"><?php echo $commission->get_rate( 'display' ) ?></td>
                                            <td width="1%"></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <?php $commission_label = '';

                                        if( 'product' == $commission->type ){
                                            $commission_label = _x( 'Commission', 'Label for single commission page', 'yith-woocommerce-product-vendors' );
                                        }

                                        elseif( 'shipping' == $commission->type  ) {
	                                        $commission_label = _x( 'Shipping cost', 'Label for single commission page', 'yith-woocommerce-product-vendors' );
                                            $shipping_method = isset( $line_items_shipping[ $commission->line_item_id ] ) ? $line_items_shipping[ $commission->line_item_id ] : null;
                                            if( ! empty( $shipping_method ) ){
                                                /** @var $shipping_method WC_Order_Item_Shipping */
                                                $commission_label = sprintf( '%s - %s', $commission_label, $shipping_method->get_name() );
                                            }
                                        } ?>
                                        <td class="label"><?php echo $commission_label; ?>:</td>
                                        <td class="total">
                                            <?php
                                            $total = $commission->get_amount();
                                            $total = wc_price( $total, $currency );
                                            echo str_replace( array( '<span class="amount">', '</span>' ), '', $total ) ?>
                                        </td>
                                        <td width="1%"></td>
                                    </tr>

                                    <?php if ( ! empty( (float) $commission->get_amount_refunded( 'edit' ) ) ) : ?>
                                        <tr>
                                            <td class="label refunded-total"><?php printf( '%s:', __( 'Refunded', 'yith-woocommerce-product-vendors' ) ) ?></td>
                                            <td class="total refunded-total"><?php echo $commission->get_refund_amount( 'display', $currency ) ?></td>
                                            <td width="1%"></td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <td class="label"><?php printf( '%s:', __( 'Total', 'yith-woocommerce-product-vendors' ) ); ?></td>
                                        <td class="total"><?php echo $commission->get_amount_to_pay( 'display', $currency ) ?></td>
                                        <td width="1%"></td>
                                    </tr>

                                    </tbody>
                                </table>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>

		</div>

		<br class="clear">
	</div>

</div>