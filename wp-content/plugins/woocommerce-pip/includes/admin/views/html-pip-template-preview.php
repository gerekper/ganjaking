<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">

	<?php $document = wc_pip()->get_document( 'invoice', array( 'order_id' => 0 ) ); ?>
	<?php $type     = $document->type; ?>
	<?php $order    = new \WC_Order(); ?>

	<title><?php echo apply_filters( 'wc_pip_document_title', sprintf( esc_html( '%1$s - %2$s %3$s' ), get_bloginfo( 'name' ), $document->name, $document->get_invoice_number() ), $type, $document, $order ); ?></title>

	<?php

	/** This action is documented in templates/pip/head.php */
	do_action( 'wc_pip_head', $type, $document, $order );

	?>

	<style type="text/css">
		.facsimile-ribbon {
			background: #0073AA;
			letter-spacing: 1px;
			line-height: 50px;
			color: #FFFFFF;
			opacity: .86;
			position: fixed;
			text-align: center;
			width: 200px;
			z-index: 9999;
			top: 25px;
			right: -50px;
			left: auto;
			transform: rotate(45deg);
			-webkit-transform: rotate(45deg);
		}
		.demo_store {
			display: none !important;
		}
		body > div.container:after {
			display: none;
		}
	</style>
</head>
<body id="woocoomerce-pip" class="woocommerce-pip invoice" <?php echo is_rtl() ? 'style="direction: rtl;"' : ''; ?>>

	<div class="facsimile-ribbon"><?php echo esc_html_x( 'Sample Invoice', 'Facsimile label. Try to keep this below 16 characters length.', 'woocommerce-pip' ); ?></div>

	<div id="order-123" class="container">

		<header>
			<?php

			/** This action is documented in templates/pip/content/order-table-before.php */
			do_action( 'wc_pip_before_header', $type, 'print', $document, $order );

			?>
			<div class="document-header <?php echo sanitize_html_class( $type ); ?>-header">

				<div class="head company-information">
					<?php

					$logo          = $document->get_company_logo();
					$title         = ! empty( $logo ) ? $document->get_company_logo() : $document->get_company_name();
					$subtitle      = $document->get_company_extra_info();
					$vat_number    = $document->get_company_vat_number();
					$align_title   = sanitize_html_class( get_option( 'wc_pip_company_title_align', 'left' ) );
					$address       = $document->get_company_address();
					$align_address = sanitize_html_class( get_option( 'wc_pip_company_address_align', '' ) );

					?>
					<div class="company-title <?php echo empty( $align_title ) ? 'left' : $align_title; ?>">

						<h1 class="title <?php echo 'right' === $align_title ? 'align-right' : ''; ?>"><?php echo $document->get_company_link( $title ); ?></h1>

						<?php if ( ! empty( $subtitle ) ) : ?>
							<h5 class="company-subtitle align-<?php echo $align_title; ?>"><?php echo $subtitle; ?></h5>
						<?php endif; ?>

						<?php if ( ! empty( $vat_number ) && is_string( $vat_number ) ) : ?>
							<h6 class="company-vat-number align-<?php echo $align_title; ?>"><?php
								/* translators: Placeholder: %s - VAT number */
								printf( esc_html__( 'VAT Number: %s', 'woocommerce-pip' ), $vat_number ); ?></h6>
						<?php endif; ?>

					</div>

					<address class="company-address <?php echo empty( $align_address ) ? 'right' : $align_address; ?> <?php echo ! empty( $logo ) ? 'has-logo' : ''; ?>">
						<p><?php echo $address; ?></p>
					</address>

					<div class="clear"></div>
				</div>
				<?php

				$invoice_number = 123;

				if ( 'yes' === get_option( 'wc_pip_use_order_number', 'yes' ) ) {

					$leading_zeros = (int) get_option( 'wc_pip_invoice_number_leading_zeros', '0' );

					if ( $leading_zeros > 0 ) {
						$invoice_number = str_pad( (string) $invoice_number, $leading_zeros + 2, '0', STR_PAD_LEFT );
					}
				}

				/* translators: Placeholder: %s - invoice number */
				echo '<h3 class="order-info">' . sprintf(  esc_html__( 'Invoice: %s', 'woocommerce-pip' ), wc_pip_parse_merge_tags( get_option( 'wc_pip_invoice_number_prefix', '' ) . $invoice_number . get_option( 'wc_pip_invoice_number_suffix', '' ), $type ) ) . '</h3>';

				/* translators: Placeholder: %s - order date */
				printf( '<h5 class="order-date">' . esc_html__( 'Order Date: %s', 'woocommerce-pip' ) . '</h5>', date_i18n( wc_date_format(), time() ) );

				// avoid to print the invoice number twice after we have manually hardcoded one right above
				if ( method_exists( $document, 'document_header' ) && has_action( 'wc_pip_header', array( $document, 'document_header' ) ) ) {

					remove_action( 'wc_pip_header', array( $document, 'document_header' ), 1 );
				}

				/** This action is documented in templates/pip/content/order-table-before.php */
				do_action( 'wc_pip_header', $type, 'print', $document, $order );

				?>
				<div class="customer-addresses">

					<?php if ( $document->show_billing_address() ) : ?>

						<div class="column customer-address billing-address left">
							<h3><?php esc_html_e( 'Billing Address', 'woocommerce-pip' ); ?></h3>
							<address class="customer-addresss">
								<?php
									/** This filter is documented in templates/pip/content/order-table-before.php */
									echo apply_filters( 'wc_pip_billing_address',
										'John Doe <br>
											548 Market St #70640 <br>
											San Francisco, CA <br>
											94104-5401 <br>
											United States',
										$type,
										$order
									);
								?>
							</address>
						</div>

					<?php endif; ?>

					<?php if ( $document->show_shipping_address() ) : ?>

						<div class="column customer-address shipping-address left">
							<h3><?php esc_html_e( 'Shipping Address', 'woocommerce-pip' ); ?></h3>
							<address class="customer-address">
								<?php
									/** This filter is documented in templates/pip/content/order-table-before.php */
									echo apply_filters( 'wc_pip_shipping_address',
										'John Doe <br>
										548 Market St #70640 <br>
										San Francisco, CA <br>
										94104-5401 <br>
										United States',
										$type,
										$order
									);
								?>
							</address>
						</div>

					<?php endif; ?>

					<?php if ( $document->show_shipping_method() ) : ?>

						<div class="column shipping-method left">
							<h3><?php esc_html_e( 'Shipping Method', 'woocommerce-pip' ); ?></h3>
							<em class="shipping-method"><?php
								/* This filter is documented in includes/abstract-wc-pip-document.php */
								echo apply_filters( 'wc_pip_document_shipping_method', esc_html( 'Free shipping', 'woocommerce-pip' ) . '<br>' . wc_price( 0 ), $type, $order ); ?>
							</em>
						</div>

					<?php endif; ?>

					<div class="clear"></div>
				</div>
				<?php

				/** This action is documented in templates/pip/content/order-table-before.php */
				do_action( 'wc_pip_after_customer_addresses', $type, 'print', $document, $order );

				?>
				<div class="document-heading <?php echo sanitize_html_class( $type ); ?>-heading">
					<?php echo $document->get_header(); ?>
				</div>
			</div>

			<?php

			/** This action is documented in templates/pip/content/order-table-before.php */
			do_action( 'wc_pip_after_header', $type, 'print', $document, $order );

			?>

		</header>

		<main class="document-body <?php echo sanitize_html_class( $type ); ?>-body">

			<?php

			/** This action is documented in templates/pip/content/order-table-before.php */
			do_action( 'wc_pip_before_body', $type, 'print', $document, $order );

			?>

			<table class="order-table invoice-order-table">

				<thead class="order-table-head">
					<tr>
						<?php $column_widths = $document->get_column_widths(); ?>

						<?php foreach( $document->get_table_headers() as $column_id => $title ): ?>
							<th class="<?php echo sanitize_html_class( $column_id ); ?>" style="width: <?php echo esc_attr( $column_widths[ $column_id ] ); ?>%;"><?php echo esc_html( $title ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>

				<?php

				$chosen_fields = get_option( 'wc_pip_invoice_show_optional_fields', [ 'sku' ] );
				$show_sku      = 'sku' === $chosen_fields || ( is_array( $chosen_fields ) && in_array( 'sku', $chosen_fields, true ) );
				$colspan       = $show_sku ? '3' : '2';

				?>

				<tfoot class="order-table-footer">
					<tr>
						<td class="cart_subtotal" colspan="<?php echo $colspan ?>">
							<strong class="order-cart_subtotal"><?php esc_html_e( 'Subtotal:', 'woocommerce-pip' ); ?></strong>
						</td>
						<td class="value">
							<span class="amount"><?php echo wc_price( 55.90 ); ?></span>
						</td>
					</tr>
					<tr>
						<td class="discount" colspan="<?php echo $colspan ?>">
							<strong class="order-discount"><?php esc_html_e( 'Discount:', 'wooocommerce-pip' ) ?></strong>
						</td>
						<td class="value">
							<span class="amount"><?php echo wc_price( -1.00 ); ?></span>
						</td>
					</tr>
					<tr>
						<td class="shipping_method" colspan="<?php echo $colspan ?>">
							<strong class="order-shipping_method"><?php esc_html_e( 'Shipping:', 'woocommerce-pip' ) ?></strong>
						</td>
						<td class="value">
							<span><?php esc_html_e( 'Free Shipping', 'woocommerce-pip' ); ?></span>
						</td>
					</tr>
					<tr>
						<td class="us-al-state-tax-1" colspan="<?php echo $colspan ?>">
							<strong class="order-us-al-state-tax-1"><?php esc_html_e( 'State Tax:', 'woocommerce-pip' ); ?></strong>
						</td>
						<td class="value">
							<span class="amount"><?php echo wc_price( 2.24 ); ?></span>
						</td>
					</tr>
					<tr>
						<td class="payment_method" colspan="<?php echo $colspan ?>">
							<strong class="order-payment_method"><?php esc_html_e( 'Payment Method:', 'woocommerce-pip' ); ?></strong>
						</td>
						<td class="value">
							<span>Paypal</span>
						</td>
					</tr>
					<tr>
						<td class="refund_0" colspan="<?php echo $colspan ?>">
							<strong class="order-refund_0"><?php esc_html_e( 'Refund:', 'woocommerce-pip' ); ?></strong>
						</td>
						<td class="value">
							<span class="amount"><?php echo wc_price( -18.72 ); ?></span>
						</td>
					</tr>
					<tr>
						<td class="order_total" colspan="<?php echo $colspan ?>">
							<strong class="order-order_total"><?php esc_html_e( 'Total:', 'woocommerce-pip' ); ?></strong>
						</td>
						<td class="value">
							<del><?php echo wc_price( 57.14 ); ?></del> <ins><span class="amount"><?php echo wc_price( 38.42 ); ?></span></ins>
						</td>
					</tr>
				</tfoot>

				<tbody class="order-table-body">
					<tr class="row table-item odd">
						<?php if ( $show_sku ) : ?>
							<td class="sku">SV1000232</td>
						<?php endif; ?>
						<td class="product">
							<span class="product product-simple"><a href="https://www.skyverge.com/shop/" target="_blank">Woo Album</a></span>
						</td>
						<td class="quantity">1</td>
						<td class="price"><?php echo wc_price( 7.90 ); ?></td>
						<td class="id"><span data-item-id="0"></span></td>
					</tr>
					<tr class="row table-item even">
						<?php if ( $show_sku ) : ?>
							<td class="sku">SV1001232</td>
						<?php endif; ?>
						<td class="product">
							<span class="product product-simple"><a href="https://www.skyverge.com/shop/" target="_blank">Woo Belt</a></span>
						</td>
						<td class="quantity">1</td>
						<td class="price"><?php echo wc_price( 15.00 ); ?></td>
						<td class="id"><span data-item-id="0"></span></td>
					</tr>
					<tr class="row table-item odd">
						<?php if ( $show_sku ) : ?>
							<td class="sku">SV1001321</td>
						<?php endif; ?>
						<td class="product">
							<span class="product product-variation"><a href="https://www.skyverge.com/shop/" target="_blank">Woo Tee Shirt</a></span>
							<dl class="variation">
								<dt class="variation-pa_color"><?php esc_html_e( 'Color:', 'woocommerce-pip' ); ?></dt>
								<dd class="variation-pa_color"><p><?php esc_html_e( 'Blue', 'woocommerce-pip' ); ?></p></dd>
								<dt class="variation-size"><?php esc_html_e( 'Size:', 'woocommerce-pip' ); ?></dt>
								<dd class="variation-size"><p><?php echo esc_html_x( 'M', 'Sample Variation Medium Size', 'woocommerce-pip' ); ?></p></dd>
							</dl>
						</td>
						<td class="quantity">
							<span class="quantity"><del>2</del></span>
							<span class="refund-quantity">1</span>
						</td>
						<td class="price">
							<del><span class="price"><span class="amount"><?php echo wc_price( 36.00 ); ?></span></span></del>
							<span class="refund-price"><span class="amount"><?php echo wc_price( 18.00 ); ?></span></span>
						</td>
						<td class="id"><span data-item-id="0"></span></td>
					</tr>
				</tbody>

			</table>

			<?php

			/** This action is documented in templates/pip/content/order-table-after.php */
			do_action( 'wc_pip_after_body', $type, 'print', $document, $order );

			?>

			<?php if ( $document->show_coupons_used() ) : ?>

				<?php $coupons = array( date_i18n( 'Y', time() ) . 'ONEDOLLAR' );
				/* translators: Placeholder: %1$s - opening <strong> tag, %2$s - coupons count (used in order), %3$s - closing </strong> tag - %4$s - coupons list */
				printf( '<br><div class="coupons-used">' . _n( '%1$sCoupon used:%3$s %4$s', '%1$sCoupons used (%2$s):%3$s %4$s', count( $coupons ), 'woocommerce-pip' ) . '</div><br>', '<strong>', count( $coupons ), '</strong>', '<span class="coupon">' . implode( '</span>, <span class="coupon">', $coupons ) . '</span>' ); ?>

			<?php endif; ?>

			<?php if ( $document->show_customer_details() ) : ?>

				<h3><?php esc_html_e( 'Customer details', 'woocommerce-pip' ); ?></h3>
				<ul class="customer-details">
					<li class="customer-email"><strong><?php esc_html_e( 'Email:', 'woocommerce-pip' ); ?></strong> <a href="mailto:john@skyverge.com">john@skyverge.com</a></li>
					<li class="customer-phone"><strong><?php esc_html_e( 'Telephone:' , 'woocommerce-pip' ); ?></strong> <a href="tel: +1 123 456 789">+1 123 456 789</a></li>
				</ul>

			<?php endif; ?>

			<?php if ( $document->show_customer_note() ) : ?>

				<div class="customer-note">
					<blockquote>
						<?php

						/* This filter is documented in includes/abstract-wc-pip-document.php */
						echo apply_filters( 'wc_pip_document_customer_note', esc_html_x( 'Please include a printed copy of your catalog, thank you.', 'Customer note sample for the live template preview.', 'woocommerce-pip' ), 0, $type );

						?>
					</blockquote>
				</div>

			<?php endif; ?>

			<?php

			/** This action is documented in templates/pip/content/order-table-after.php */
			do_action( 'wc_pip_order_details_after_customer_details', $type, 'print', $document, $order );

			?>
		</main>

		<br>

		<footer class="document-footer <?php echo $type; ?>-footer">

			<?php

			/** This action is documented in templates/pip/content/order-table-after.php */
			do_action( 'wc_pip_before_footer', $type, 'print', $document, $order );

			?>

			<div class="terms-and-conditions">
				<?php echo $document->get_return_policy(); ?>
			</div>

			<hr>

			<div class="document-colophon <?php echo $type; ?>-colophon">
				<?php echo $document->get_footer(); ?>
			</div>

			<?php

			/** This action is documented in templates/pip/content/order-table-after.php */
			do_action( 'wc_pip_after_footer', $type, 'print', $document, $order );

			?>
		</footer>

	</div>
</body>
</html>
<?php
