<?php
/**
 * PDF Invoice Template
 *
 * Dati riepilogo XML.
 *
 * @author  YITH
 * @package YITH\PDFInvoice\Templates
 */

$taxes = array();

$order_items = $invoice_details['order_items'];

$bundle_exists = false;

if ( wc_tax_enabled() ) {
	foreach ( $order_items as $item_id => $item ) :

		if ( $item instanceof WC_Order_Item_Product ) {

			if ( $item->get_product() instanceof WC_Product_Bundle ) {

				$bundle_exists = true;
			}
		}

		$tax = $item->get_taxes(); //phpcs:ignore

		$tax_rate_amount = $item->get_total_tax();

		$tax_percentage = '0.00';

		if ( abs( $tax_rate_amount ) > 0 || $item instanceof WC_Order_Item_Product ) {

			$order = isset( $invoice_details['main_order'] ) ? $invoice_details['main_order'] : $document->order; //phpcs:ignore

			$tax_class = strval( $item->get_tax_class() ) === 'inherit' ? '' : $item->get_tax_class();

			$tax_rates = WC_Tax::find_rates(
				array(
					'country'   => $order->get_billing_country(),
					'state'     => $order->get_billing_state(),
					'city'      => $order->get_billing_city(),
					'postcode'  => $order->get_billing_postcode(),
					'tax_class' => $tax_class,
				)
			);


			foreach ( $tax_rates as $tax_rate ) {

				$tax_percentage = number_format( $tax_rate['rate'], 2, '.', '' );

			}
		}

		$new_total = isset( $taxes[ $tax_percentage ]['total'] ) ? $taxes[ $tax_percentage ]['total'] + $item['total'] : $item['total'];

		$new_tax_total = isset( $taxes[ $tax_percentage ]['total_tax'] ) ? $taxes[ $tax_percentage ]['total_tax'] + $tax_rate_amount : $tax_rate_amount;

		$taxes[ $tax_percentage ] = array(
			'total'     => $new_total,
			'total_tax' => $new_tax_total,
		);


	endforeach;
}


$taxes = apply_filters( 'ywpi_invoce_taxes', $taxes, $bundle_exists );

?>

<?php foreach ( $taxes as $key => $tax ) : //phpcs:ignore ?>

	<?php $tax_percentage = number_format( (float) $key, 2, '.', '' ); ?>

	<?php $total = number_format( abs( $tax['total'] ), 2, '.', '' ); ?>

	<?php $total_tax = number_format( abs( $tax['total_tax'] ), 2, '.', '' ); ?>

	<DatiRiepilogo>
		<AliquotaIVA><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', $tax_percentage, 'AliquotaIVA', $document ) ); ?></AliquotaIVA>
		<?php if ( '0.00' == $tax_percentage ) :  //phpcs:ignore ?>
			<Natura><?php echo wp_kses_post( $invoice_details['natura'] ); ?></Natura>
		<?php endif; ?>
		<ImponibileImporto><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', $total, 'ImponibileImporto', $document ) ); ?></ImponibileImporto>
		<Imposta><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', $total_tax, 'Imposta', $document ) ); ?></Imposta>
		<EsigibilitaIVA><?php echo wp_kses_post( $invoice_details['chargeability_vat'] ); ?></EsigibilitaIVA>
	</DatiRiepilogo>
<?php endforeach; ?>
