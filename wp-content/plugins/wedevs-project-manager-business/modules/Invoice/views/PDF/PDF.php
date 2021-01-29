<!DOCTYPE html>
<html>
<head>
	<title>Invoice</title>
	<link rel='stylesheet' id='common-css'  href="<?php echo PM_PRO_INVOICE_URL; ?>/views/assets/css/invoice.css" type='text/css' media='all' />
</head>
<body>
<div class="cpmi-single-invoice pm-pro-invoice-pdf-wrap"><!----> 
	<div class="pm-pro-invoice-child-wrap">
		<div class="cpmi-address-content">
			<div class="cpmi-front-end-wrap">
				<!-- <div class="cpmi-satus">
					<div class="cpmi-ribbon-green cpmi-unpaid">
				        Unpaid        
				    </div>
				</div>  -->
				<div class="pm-pro-address-wrap">
					<table class="cpmi-title-tb">
						<tbody>
							<tr>
								<td class="pm-pro-address-td">
									<table class="pm-pro-address-table">
										<tbody>
											<tr>
												<th class="cpmi-invoice-head cpmi-invoice-head-top">Invoice #:</th> 
												<td class="cpmi-invoice-head cpmi-invoice-head-top">
													<?php echo $invoice['id']; ?></td>
											</tr> 

											<tr>
												<th class="cpmi-invoice-head cpmi-odd">Invoice Date:</th> 
												<td class="cpmi-invoice-head cpmi-odd"><?php echo $invoice['start_at']['date']; ?></td>
											</tr> 

											<tr>
												<th class="cpmi-invoice-head cpmi-odd">Due Date:</th> 
												<td class="cpmi-invoice-head cpmi-odd"><?php echo $invoice['due_date']['date']; ?></td>
											</tr> 

											<tr>
												<th class="cpmi-invoice-head cpmi-odd">Amount Due:</th> 
												<td class="cpmi-invoice-head cpmi-odd"><span style="font-family: 'Hind Siliguri';"><?php echo $currency_symbol; ?></span><?php echo pm_pro_invoice_get_total_due( $invoice ); ?></td>
						                    </tr>
						                </tbody>
						            </table>
						        </td> 
						        <td>
						        	<div class="pm-pro-invoice-status">
						        		<?php
						        		
						        		if ( $invoice['status'] == '0' ) {
						        			$class = 'pm-pro-invoice-status-unpaid';
						        			$label = 'Unpaid';
						        		} else if ( $invoice['status'] == '1' ) {
						        			$class = 'pm-pro-invoice-status-paid';
						        			$label = 'Paid';
						        		} else if ( $invoice['status'] == '2' ) {
						        			$class = 'pm-pro-invoice-status-partial';
						        			$label = 'Partial';
						        		}

						        		?>
							        	<h2 class="pm-pro-invoice-title">INVOICE</h2>
							        	<div class="<?php echo $class; ?>">
											   <div class="ribbon"><span><?php echo $label; ?></span></div>
										</div>
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
													<h4>From</h4>
												</td>
											</tr> 
											<tr>
												<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['organization'] ) ? '' : $invoice_settings['organization']; ?>                                    
						                        </td>
						                    </tr> 
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['address_line_1'] ) ? '' : $invoice_settings['address_line_1']; ?>  
						                        </td>
						                    </tr> 
						                    <tr>
						                    	<td class="cpmi-address-td">
													<?php echo empty( $invoice_settings['address_line_2'] ) ? '' : $invoice_settings['address_line_2']; ?>                                     
						                        </td>
						                    </tr> 
						                    <tr>
						                    	<td class="cpmi-address-td">
			                                        <?php echo empty( $invoice_settings['sate_province'] ) ? '' : $invoice_settings['sate_province'] . ','; ?>
			                                        <?php echo empty( $invoice_settings['city'] ) ? '' : $invoice_settings['city'] . ','; ?>
			                                        <?php echo empty( $invoice_settings['zip_code'] ) ? '' : $invoice_settings['zip_code'] . ','; ?>                                    
												</td>
											</tr> 
											<tr>
												<td class="cpmi-address-td">
													<?php $country_code = empty( $invoice_settings['country_code'] ) ? '' : $invoice_settings['country_code']; ?>
													<?php echo empty( $countries[$country_code] ) ? '' : $countries[$country_code]; ?>
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
						        					<h4>To</h4>
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
							<thead>
								<tr>
									<th class="cpmi-odd cpmi-first">Task</th>
									<th class="cpmi-odd cpmi-first">Entry Notes</th>
									<th class="cpmi-odd cpmi-first">Rate</th>
									<th class="cpmi-odd cpmi-first">Hours</th>
									<th class="cpmi-odd cpmi-last">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								
								foreach ( $invoice['entryTasks'] as $key => $entry_task ) {
									
									?>
									<tr>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['task'] ) ? $entry_task['task'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['description'] ) ? $entry_task['description'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['amount'] ) ? $entry_task['amount'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $entry_task['hour'] ) ? $entry_task['hour'] : ''; ?>
										</td>
										<td class="cpmi-last cpmi-even">
											<span><?php echo $currency_symbol; ?></span><?php echo pm_pro_task_line_total( $entry_task ); ?>
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
							
							<thead>
								<tr>
									<th class="cpmi-odd cpmi-first">Item</th>
									<th class="cpmi-odd cpmi-first">Description</th>
									<th class="cpmi-odd cpmi-first">Unit Cost</th>
									<th class="cpmi-odd cpmi-first">Qty</th>
									<th class="cpmi-odd cpmi-last">Price</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach ( $invoice['entryNames'] as $key => $name ) {
									?>
									<tr>
										<td class="cpmi-first cpmi-even">
											<?php echo isset($name['task']) ? $name['task'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset($name['description']) ? $name['description'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $name['amount'] ) ? $name['amount'] : ''; ?>
										</td>
										<td class="cpmi-first cpmi-even">
											<?php echo isset( $name['quantity'] ) ? $name['quantity'] : ''; ?>
										</td>
										<td class="cpmi-last cpmi-even">
											<span><?php echo $currency_symbol; ?></span><?php echo pm_pro_name_line_total( $name ); ?>
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
		    <h4>Invoice Payment History</h4>
		    <?php $payments = empty( $invoice['payments']['data'] ) ? [] : $invoice['payments']['data']; ?>
		    <table class="cpmi-partial-list widefat">
		        <thead>
		            <tr>
		                <th>Date</th>
		                <th>Payment Method</th>
		                <th>Note</th>
		                <th class="cpmi-amount">Amount</th>
		            </tr>
		        </thead>
		        <tbody>
		        	<?php
		        	foreach ( $payments as $key => $payment ) {
		        		?>
		        		 <tr class="even">
			                <td><?php echo $payment['date']['date']; ?></td>
			                <td><?php echo $payment['gateway']; ?></td>
			                <td><?php echo empty( $payment['notes'] ) ? '' : $payment['notes']; ?></td>
			                <td class="cpmi-amount">
			                	<span><?php echo $currency_symbol; ?></span><?php echo $payment['amount']; ?>
			                </td>
			            </tr>
		        		<?php
		        	}
		           	?>
		           	<tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance">Subtotal</span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo $currency_symbol; ?></span><?php echo pm_pro_calculate_sub_total( $invoice['entryTasks'], $invoice['entryNames'] ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance">Discount</span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo $currency_symbol; ?></span><?php echo $invoice['discount'];  ?>
		                </td>
		            </tr>
		           	<tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance">Tax(%)</span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo $currency_symbol; ?></span><?php echo pm_pro_calculate_total_tax( $invoice['entryTasks'], $invoice['entryNames'] );  ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td">
		                	<span class="cpmi-partial-balance">Total Amount</span>
		                </td>
		                <td class="cpmi-amount cmpi-custom-td">
		                	<span><?php echo $currency_symbol; ?></span><?php echo pm_pro_invoice_get_invoice_total( $invoice['entryTasks'], $invoice['entryNames'], $invoice['discount'] ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td"><span class="cpmi-partial-balance">Total Paid</span></td>
		                <td class="cpmi-amount cmpi-custom-td"><span><?php echo $currency_symbol; ?></span><?php echo pm_pro_invoice_get_total_paid( $payments ); ?>
		                </td>
		            </tr>
		            <tr>
		                <td></td>
		                <td></td>
		                <td class="cmpi-custom-td cpmi-last-td"><span class="cpmi-partial-balance">Due</span></td>
		                <td class="cpmi-amount cmpi-custom-td cpmi-last-td"><span><?php echo $currency_symbol; ?></span><?php echo pm_pro_invoice_get_total_due( $invoice ); ?>
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</div>

		<table>
                <tr>
                    <th>
                        Terms & Conditions
                    </th>
                    <th>
                        Notes
                    </th>

                </tr>
                
                <tr>
                    <td>
                        <?php echo $invoice['terms'] ?>
                    </td>
                    <td><?php echo $invoice['client_notes'] ?></td>
                </tr>
            </table>
	</div>
</div>


</body>
</html>