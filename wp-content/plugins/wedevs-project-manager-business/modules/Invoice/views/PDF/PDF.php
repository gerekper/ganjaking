<!DOCTYPE html>
<html>
<head>
	<title><?php esc_html_e( 'Invoice', 'pm-pro' ); ?></title>
	<link rel='stylesheet' id='common-css'  href="<?php echo PM_PRO_INVOICE_URL; ?>/views/assets/css/invoice.css" type='text/css' media='all' />
</head>
<body>
<div class="cpmi-single-invoice pm-pro-invoice-pdf-wrap"><!---->
	<div class="pm-pro-invoice-child-wrap">
		<div class="cpmi-address-content">
			<div class="cpmi-front-end-wrap">
				<div class="pm-header-area">
					<?php if ( ! empty( pm_pro_get_logo() ) && ! empty( pm_pro_get_logo()['url'] ) ) : ?>
						<img src="<?php echo esc_url( pm_pro_get_logo()['url'] ); ?>" width="100" />
					<?php endif; ?>
					<h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
				</div>

				<div class="pm-pro-address-wrap">
					<table class="cpmi-title-tb">
						<tbody>
							<tr>
								<td class="pm-pro-address-td">
									<table class="pm-pro-address-table">
										<tbody>
											<tr>
												<td class="cpmi-invoice-head cpmi-invoice-head-top">
													<?php esc_html_e( 'Invoice', 'pm-pro' ); ?> #:
												</td>
												<td class="cpmi-invoice-head cpmi-invoice-head-top">
													<?php echo esc_html( $invoice['id'] ); ?>
												</td>
											</tr>
											<tr>
												<td class="cpmi-invoice-head cpmi-odd"><?php esc_html_e( 'Invoice Date:', 'pm-pro' ); ?></td>
												<td class="cpmi-invoice-head cpmi-odd"><?php echo esc_html( $invoice['start_at']['date'] ); ?></td>
											</tr>
											<tr>
												<td class="cpmi-invoice-head cpmi-odd"><?php esc_html_e( 'Due Date:', 'pm-pro' ); ?></td>
												<td class="cpmi-invoice-head cpmi-odd"><?php echo esc_html( $invoice['due_date']['date'] ); ?></td>
											</tr>
											<tr>
												<td class="cpmi-invoice-head cpmi-odd"><?php esc_html_e( 'Amount Due:', 'pm-pro' ); ?></td>
												<td class="cpmi-invoice-head cpmi-odd">
													<span>
														<?php echo esc_html( $currency_symbol ); ?>
													</span>
													<?php echo pm_pro_invoice_get_total_due( $invoice ); ?>
												</td>
						                    </tr>
						                </tbody>
						            </table>
						        </td>
						        <td>
						        	<div class="pm-pro-invoice-status">
						        		<?php
										$class = '';
										$label = '';
										$style = '';
						        		if ( $invoice['status'] == '0' ) {
						        			$class = 'pm-pro-invoice-status-unpaid';
											$style = 'margin-left: 4%; border: 2px solid #cc0000; padding: 5px 20px; color: #cc0000; text-align: center; margin-right: 4%;';
						        			$label = __( 'Unpaid', 'pm-pro' );
						        		} else if ( $invoice['status'] == '1' ) {
						        			$class = 'pm-pro-invoice-status-paid';
											$style = 'margin-left: 4%; border: 2px solid #009801; padding: 5px 20px; color: #009801; text-align: center; margin-right: 4%;';
						        			$label = __( 'Paid', 'pm-pro' );
						        		} else if ( $invoice['status'] == '2' ) {
						        			$class = 'pm-pro-invoice-status-partial';
											$style = 'margin-left: 4%; border: 2px solid #000099; padding: 5px 20px; color: #000099; text-align: center; margin-right: 4%;';
						        			$label = __( 'Partial', 'pm-pro' );
						        		}
						        		?>

							        	<span class="pm-pro-invoice-title" style="font-size: 24px; padding-left: 30px;">
											&nbsp; &nbsp; &nbsp; &nbsp;
											<?php esc_html_e( 'INVOICE', 'pm-pro' ); ?>
										</span>
										<span class="<?php echo $class; ?>">
											<span class="ribbon" style="<?php echo $style; ?>">
												&nbsp; &nbsp; &nbsp;
												<span><?php echo $label; ?></span>
												&nbsp; &nbsp; &nbsp;
											</span>
										</span>
									</div>
						        </td>
						    </tr>
						</tbody>
					</table>

					<table class="cpmi-frm-to-top">
						<tbody>
							<tr>
								<td class="cpmi-invoice-from">
									<table>
										<tbody>
											<tr>
												<td>
													<span style="font-size: 22px;">
														<?php esc_html_e( 'From', 'pm-pro' ); ?>
													</span>
												</td>
											</tr>
											<tr>
												<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['organization'] ) ? '' : esc_html( $invoice_settings['organization'] ); ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['address_line_1'] ) ? '' : esc_html( $invoice_settings['address_line_1'] ); ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['address_line_2'] ) ? '' : esc_html( $invoice_settings['address_line_2'] ); ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
			                                        <?php echo empty( $invoice_settings['sate_province'] ) ? '' : esc_html( $invoice_settings['sate_province'] ) . ','; ?>
			                                        <?php echo empty( $invoice_settings['city'] ) ? '' : esc_html( $invoice_settings['city'] ) . ','; ?>
			                                        <?php echo empty( $invoice_settings['zip_code'] ) ? '' : esc_html( $invoice_settings['zip_code'] ) . ','; ?>
												</td>
											</tr>
											<tr>
												<td class="cpmi-address-td">
													<?php $country_code = empty( $invoice_settings['country_code'] ) ? '' : $invoice_settings['country_code']; ?>
													<?php echo empty( $countries[$country_code] ) ? '' : esc_html( $countries[$country_code] ); ?>
						                        </td>
						                    </tr>
						                </tbody>
						            </table>
						        </td>
						        <td class="cpmi-invoice-to">
						        	<table class="pm-pro-invoice-to">
						        		<tbody>
						        			<tr>
						        				<td>
						        					<span style="font-size: 22px;">
														<?php esc_html_e( 'To', 'pm-pro' ); ?>
													</span>
						        				</td>
						        			</tr>
											<tr>
												<td class="cpmi-address-td">
													<?php echo empty( $client_address['organization'] ) ? '' : $client_address['organization']; ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $client_address['address_line_1'] ) ? '' : $client_address['address_line_1']; ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $client_address['address_line_2'] ) ? '' : $client_address['address_line_2']; ?>
						                        </td>
						                    </tr>
						                    <tr>
						                    	<td class="cpmi-address-td">
			                                        <?php echo empty( $client_address['sate_province'] ) ? '' : $client_address['sate_province'] . ','; ?>
			                                        <?php echo empty( $client_address['city'] ) ? '' : $client_address['city'] . ','; ?>
			                                        <?php echo empty( $client_address['zip_code'] ) ? '' : $client_address['zip_code'] . ','; ?>
												</td>
											</tr>
											<tr>
												<td class="cpmi-address-td">
													<?php $country_code = empty( $client_address['country_code'] ) ? '' : $client_address['country_code']; ?>
													<?php echo empty( $countries[$country_code] ) ? '' : $countries[$country_code]; ?>
						                        </td>
						                    </tr>
					                    </tbody>
					                </table>
					            </td>
					        </tr>
					    </tbody>
					</table>
				</div>
				<div class="pm-pro-invoice-clearfix"></div>
				<div class="cpmi-front-end-table-wrap">
					<?php if ($invoice['entryTasks'][0]['task'] !== '' ) { ?>
						<table class="widefat cpm-invoice-items">
							<tbody>
								<tr class="even">
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Task', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Entry Notes', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Rate', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Hours', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-last"><?php esc_html_e( 'Total', 'pm-pro' ); ?></td>
								</tr>
								<?php
								foreach ( $invoice['entryTasks'] as $key => $entry_task ) {
									?>
									<tr>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['task'] ) ? esc_html( $entry_task['task'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['description'] ) ? esc_html( $entry_task['description'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['amount'] ) ? esc_html( $entry_task['amount'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['hour'] ) ? esc_html( $entry_task['hour'] ) : ''; ?>
										</td>
										<td class="cpmi-last cpmi-even">
											<span><?php echo esc_html( $currency_symbol ); ?></span>
											<?php echo pm_pro_task_line_total( $entry_task ); ?>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					<?php } ?>
					<?php if ($invoice['entryNames'][0]['task'] !== '' ) { ?>
						<table class="widefat cpm-invoice-items">
							<tbody>
								<tr class="even">
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Item', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Description', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Unit Cost', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-first"><?php esc_html_e( 'Qty', 'pm-pro' ); ?></td>
									<td class="cpmi-odd cpmi-last"><?php esc_html_e( 'Price', 'pm-pro' ); ?></td>
								</tr>
								<?php
								foreach ( $invoice['entryNames'] as $key => $name ) {
									?>
									<tr>
										<td class="cpmi-first cpmi-even">
											<?php echo isset($name['task']) ? esc_html( $name['task'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset($name['description']) ? esc_html( $name['description'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $name['amount'] ) ? esc_html( $name['amount'] ) : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $name['quantity'] ) ? esc_html( $name['quantity'] ) : ''; ?>
										</td>
										<td class="cpmi-last cpmi-even">
											<span><?php echo esc_html( $currency_symbol ); ?></span>
											<?php echo pm_pro_name_line_total( $name ); ?>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="cpm-invoice-list wrap">
		    <div style="margin-top: 20px; margin-bottom: 20px;">
				<span style="font-size: 22px;"><?php esc_html_e( 'Invoice Payment History', 'pm-pro' ); ?></span>
			</div>

		    <?php $payments = empty( $invoice['payments']['data'] ) ? [] : $invoice['payments']['data']; ?>
		    <table class="cpmi-partial-list widefat">
		        <tbody>
		            <tr class="even">
		                <td><?php esc_html_e( 'Date', 'pm-pro' ); ?></td>
		                <td><?php esc_html_e( 'Payment Method', 'pm-pro' ); ?></td>
		                <td><?php esc_html_e( 'Note', 'pm-pro' ); ?></td>
		                <td class="cpmi-amount"><?php esc_html_e( 'Amount', 'pm-pro' ); ?></td>
		            </tr>
		        	<?php
		        	foreach ( $payments as $key => $payment ) {
		        		?>
		        		 <tr class="even">
			                <td><?php echo esc_html( $payment['date']['date'] ); ?></td>
			                <td><?php echo esc_html( $payment['gateway'] ); ?></td>
			                <td><?php echo empty( $payment['notes'] ) ? '' : esc_html( $payment['notes'] ); ?></td>
			                <td class="cpmi-amount">
			                	<span><?php echo esc_html( $currency_symbol ); ?></span>
								<?php echo esc_html( $payment['amount'] ); ?>
			                </td>
			            </tr>
		        		<?php
		        	}
		           	?>
		           	<tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance"><?php esc_html_e( 'Subtotal', 'pm-pro' ); ?></span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo esc_html( $currency_symbol ); ?></span><?php echo pm_pro_calculate_sub_total( $invoice['entryTasks'], $invoice['entryNames'] ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance"><?php esc_html_e( 'Discount', 'pm-pro' ); ?></span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo esc_html( $currency_symbol ); ?></span><?php echo $invoice['discount'];  ?>
		                </td>
		            </tr>
		           	<tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance"><?php esc_html_e( 'Tax', 'pm-pro' ); ?>(%)</span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo esc_html( $currency_symbol ); ?></span><?php echo pm_pro_calculate_total_tax( $invoice['entryTasks'], $invoice['entryNames'] );  ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance"><?php esc_html_e( 'Total Amount', 'pm-pro' ); ?></span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo esc_html( $currency_symbol ); ?></span>
							<?php echo pm_pro_invoice_get_invoice_total( $invoice['entryTasks'], $invoice['entryNames'], $invoice['discount'] ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td"><span class="cpmi-partial-balance"><?php esc_html_e( 'Total Paid', 'pm-pro' ); ?></span></td>
		                <td class="cpmi-amount cmpi-custom-td">
							<span><?php echo esc_html( $currency_symbol ); ?></span>
							<?php echo pm_pro_invoice_get_total_paid( $payments ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td cpmi-last-td"><span class="cpmi-partial-balance"><?php esc_html_e( 'Due', 'pm-pro' ); ?></span></td>
		                <td class="cpmi-amount cmpi-custom-td cpmi-last-td">
							<span><?php echo esc_html( $currency_symbol ); ?></span>
							<?php echo pm_pro_invoice_get_total_due( $invoice ); ?>
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</div>

		<table>
                <tr>
                    <td><?php esc_html_e( 'Terms & Conditions', 'pm-pro' ); ?></td>
                    <td><?php esc_html_e( 'Notes', 'pm-pro' ); ?></td>
                </tr>
                <tr>
                    <td><?php echo esc_html( $invoice['terms'] ); ?></td>
                    <td><?php echo esc_html( $invoice['client_notes'] ); ?></td>
                </tr>
		</table>
	</div>
</div>
</body>
</html>