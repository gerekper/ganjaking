<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div id="ywcdd_quantity_table_container">
	<?php

	if ( $quantity_table_id ) {

		$product        = wc_get_product( $product_id );
		$carrier_id     = get_post_meta( $quantity_table_id, 'ywcdd_table_select_carrier', true );
		$quantity_price = get_post_meta( $quantity_table_id, 'ywcdd_qty_product_table', true );

		$need_table_days   = get_post_meta( $quantity_table_id, 'ywcdd_table_need_days', true );
		$query_args        = array(
			'meta_query' => array(
				array(
					'key'     => '_ywcdd_type_checkout',
					'value'   => 'no',
					'compare' => '='
				)
			),
			'fields'     => 'ids'
		);
		$processing_method = YITH_Delivery_Date_Processing_Method()->get_processing_method( $query_args );
		$processing_method = isset( $processing_method[0] ) ? $processing_method[0] : false;

		$need_day = get_post_meta( $processing_method, '_ywcdd_minworkday', true );

		if ( apply_filters( 'ywcdd_choose_max_processing_day', true ) ) {
			$need_table_days = max( $need_day, $need_table_days );
		} else {
			$need_table_days += $need_day;
		}

		$first_processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_method, array( 'min_working_day' => $need_table_days ) );


		$delivery_dates = YITH_Delivery_Date_Manager()->get_all_delivery_dates( $carrier_id, array(
			'from_date' => $first_processing_date,
			'max_range' => 4
		) );


		?>
        <style>

            #ywcdd_quantity_table_wrap #ywcdd_quantity_table tbody td:hover, #ywcdd_quantity_table_wrap #ywcdd_quantity_table tbody td.ywcdd_day_selected{
                background-color:<?php echo get_option('ywcdd_table_customization_selected_quantity_color', '#c8b4c4' );?>;
            }

            #ywcdd_quantity_table_wrap #ywcdd_quantity_table thead th.ywcdd_day:hover, #ywcdd_quantity_table_wrap #ywcdd_quantity_table thead th.ywcdd_day_selected{
                background-color:<?php echo get_option('ywcdd_table_customization_selected_day_color', '#a46497' );?>;
                border: 4px solid <?php echo get_option('ywcdd_table_customization_selected_day_color', '#a46497' );?>;
            }

            .quantity {
                display: none !important;
            }

            .single_add_to_cart_button {
                float: right;
                margin-right: 10px;
            }
        </style>
        <div id="ywcdd_quantity_table_wrap">
            <table id="ywcdd_quantity_table">
                <thead>
                <tr>
                    <th class="ywcdd_empty_th">&nbsp;</th>
					<?php for ( $i = 0; $i < 4; $i ++ ) {
						if ( $product->is_type( 'variable' ) ) {
							$class_disabled = 'ywcdd_disable_all_day';
						} else {
							$col_disabled = yith_delivery_date_column_is_disabled( $quantity_price, $i );

							$class_disabled = $col_disabled ? 'ywcdd_disable_all_day' : '';
						}

						$date = new WC_DateTime( date( 'Y-m-d', $delivery_dates[ $i ] ), new DateTimeZone( 'UTC' ) );

						$last_shipping_date = YITH_Delivery_Date_Manager()->get_last_shipping_date( $delivery_dates[ $i ], $processing_method, $carrier_id,
							array(
								'processing_min_working_day' => $need_table_days
							)
						);
						$last_shipping_date = date( 'Y-m-d', $last_shipping_date );
						$month              = wc_format_datetime( $date, 'F' );
						$day                = wc_format_datetime( $date, 'd' );
						$day_of_week        = wc_format_datetime( $date, 'D' );

						$formatted_date = sprintf( '<span class="ywcdd_formatted_date"><span class="week_of_day">%s</span><span class="day">%s</span><span class="month">%s</span></span>', $day_of_week, $day, $month );

						?>
                        <th class="ywcdd_day day_<?php echo( $i + 1 ); ?> <?php echo $class_disabled; ?>"
                            data-day_column="<?php echo( $i + 1 ); ?> ">
                            <div data-delivery_date="<?php echo date( 'Y-m-d', $delivery_dates[ $i ] ); ?>"
                                 data-last_shipping_date="<?php echo $last_shipping_date; ?>"><?php echo $formatted_date; ?></div>
                        </th>
						<?php
					}
					?>
                </tr>
                <tr>
                    <th class="ywcdd_quantity"><?php _e( 'Quantity', 'yith-woocommerce-delivery-date' ); ?></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ( $quantity_price as $index => $row ):
					$quantity = $row['quantity'];
					$row_days = $row['days'];
					$manage_stock = $product->get_manage_stock();

					$enough_in_stock = ( ! $manage_stock && $product->is_in_stock() ) || ( $manage_stock && $product->get_stock_quantity() >= $quantity ) || ( $manage_stock && $product->backorders_allowed() );

					$original_max_price = false;
					if ( $product->is_type( 'variable' ) ) {

						/**
						 * @var WC_Product_Variable $product
						 */
						$original_min_price = floatval( $product->get_variation_price( 'min', true ) );
						$original_max_price = floatval( $product->get_variation_price( 'max', true ) );


					} else {
						$original_min_price = floatval( $product->get_price() );
					}
					?>
                    <tr>
                        <td class="ywcdd_quantity" data-qty="<?php echo $quantity; ?>">
                            <span><?php echo $quantity; ?></span>
                        </td>
						<?php foreach ( $row_days as $n_day => $day ):

							if ( 'yes' == $day['enabled'] && $enough_in_stock ) {
								$col_disabled  = '';
								$val           = $day['value'];
								$val           = empty( $val ) ? 0 : $val;
								$new_max_price = false;
								switch ( $day['type'] ) {

									case 'discount':
										$new_price = ( $original_min_price - $val );

										if ( $original_max_price ) {
											$new_max_price = ( $original_max_price - $val );
										}
										break;
									case 'markup':
										$new_price = ( $original_min_price + $val );
										if ( $original_max_price ) {
											$new_max_price = ( $original_max_price + $val );
										}
										break;

									case 'discount_perc' :
										$new_price = ( $original_min_price - ( $original_min_price * $val / 100 ) );
										if ( $original_max_price ) {
											$new_max_price = ( $original_max_price - ( $original_max_price * $val / 100 ) );
										}
										break;
									case 'markup_perc':
										$new_price = ( $original_min_price + ( $original_min_price * $val / 100 ) );
										if ( $original_max_price ) {
											$new_max_price = ( $original_max_price + ( $original_max_price * $val / 100 ) );
										}
										break;
								}


								$new_price     = floatval( $new_price ) > 0 ? floatval( $new_price ) : 0;
								$new_max_price = $new_max_price && floatval( $new_max_price ) > 0 ? floatval( $new_max_price ) : false;

								if ( $original_max_price ) {
									$col_disabled = 'ywcdd_disable_day';
								}
								if ( $new_max_price && $new_price < $new_max_price ) {

									$original_min_price_disp = wc_get_price_to_display( $product, array(
										'price' => $original_min_price,
										'qty'   => $quantity
									) );

									$original_max_price_disp = wc_get_price_to_display( $product, array(
										'price' => $original_max_price,
										'qty'   => $quantity
									) );

									$new_price_dp     = wc_get_price_to_display( $product, array(
										'price' => $new_price,
										'qty'   => $quantity
									) );
									$new_max_price_dp = wc_get_price_to_display( $product, array(
										'price' => $new_max_price,
										'qty'   => $quantity
									) );

									$price_from = wc_format_price_range( $original_min_price_disp, $original_max_price_disp );
									$price_to   = wc_format_price_range( $new_price_dp, $new_max_price_dp );


									if ( $new_price !== $original_min_price ) {
										$price_formatted = wc_format_sale_price( $price_from, $price_to ) . $product->get_price_suffix();
									} else {
										$price_formatted = $price_to . $product->get_price_suffix();
									}
								} else {
									$price_from = wc_get_price_to_display( $product, array(
										'price' => $original_min_price,
										'qty'   => $quantity
									) );

									$price_to = wc_get_price_to_display( $product, array(
										'price' => $new_price,
										'qty'   => $quantity
									) );

									if ( $new_price < $original_min_price ) {
										$price_formatted = wc_format_sale_price( $price_from, $price_to ) . $product->get_price_suffix();
									} else {
										$price_formatted = wc_price( $price_to ) . $product->get_price_suffix();
									}

								}

								$price_formatted = apply_filters( 'yith_delivery_date_quantity_price_html', $price_formatted, $new_price, $new_max_price, $original_min_price, $original_max_price, $quantity );
							} else {
								$col_disabled    = 'ywcdd_disable_day';
								$price_formatted = '-';
								$new_price       = '';

							}
							?>
                            <td class="ywcdd_day day_<?php echo $n_day + 1; ?> <?php echo $col_disabled; ?>"
                                data-price="<?php echo $new_price; ?>" data-day_column="<?php echo $n_day + 1; ?>">
                                <span><?php echo $price_formatted; ?></span>
                            </td>
						<?php endforeach; ?>
                    </tr>
				<?php
				endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td colspan="4">
                        <table>
                            <tr>
                                <td id="ywcdd_delivery_recap_date">
                                    <p><?php echo __( 'Delivery', 'yith-woocommerce-delivery-date' ); ?></p>
                                    <p class="ywcdd_recap_value"></p>
                                </td>
                                <td id="ywcdd_delivery_recap_quantity">
                                    <p><?php echo __( 'Quantity', 'yith-woocommerce-delivery-date' ); ?></p>
                                    <p class="ywcdd_recap_value"></p>
                                </td>
                                <td id="ywcdd_delivery_recap_total">
                                    <p><?php echo __( 'Total price', 'yith-woocommerce-delivery-date' ); ?></p>
                                    <p class="ywcdd_recap_value"></p>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
                </tfoot>
            </table>

        </div>
        <input type="hidden" id="ywcdd_date_selected" name="ywcdd_date_selected">
        <input type="hidden" id="ywcdd_new_price" name="ywcdd_new_price">
        <input type="hidden" id="ywcdd_last_shipping_date" name="ywcdd_last_shipping_date">
        <input type="hidden" name="ywcdd_processing_method_id" value="<?php echo $processing_method; ?>">
        <input type="hidden" name="ywcdd_carrier_id" value="<?php echo $carrier_id; ?>">
		<?php

	}
	?>

</div>
