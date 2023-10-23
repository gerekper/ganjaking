<?php
/**
 * PDF Invoice Template
 *
 * Dati Generali XML.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Templates
 */

?>
<DatiGenerali>
	<DatiGeneraliDocumento>
		<TipoDocumento><?php echo wp_kses_post( $invoice_details['document_type'] ); ?></TipoDocumento>
		<Divisa><?php echo wp_kses_post( $invoice_details['order_currency'] ); ?></Divisa>
		<Data><?php echo wp_kses_post( $invoice_details['document_date'] ); ?></Data>
		<Numero><?php echo wp_kses_post( $invoice_details['document_number'] ); ?></Numero> <!-- numero fattura -->
		<ImportoTotaleDocumento><?php echo wp_kses_post( $invoice_details['document_order_total'] ); ?></ImportoTotaleDocumento>
		<?php if ( '' !== strval( $invoice_details['reason'] ) ) : ?>
			<Causale><?php echo wp_kses_post( $invoice_details['reason'] ); ?></Causale>
		<?php endif; ?>
	</DatiGeneraliDocumento>
	<DatiOrdineAcquisto>
		<IdDocumento><?php echo wp_kses_post( $invoice_details['order_number'] ); ?></IdDocumento>
		<Data><?php echo wp_kses_post( $invoice_details['order_date'] ); ?></Data>
	</DatiOrdineAcquisto>

	<?php if ( $invoice_details['is_refund'] ) : ?>
	<!-- Only per refund -->
		<DatiFattureCollegate>
			<IdDocumento><?php echo wp_kses_post( $invoice_details['refund_document_id'] ); ?></IdDocumento>
			<Data><?php echo wp_kses_post( $invoice_details['refund_document_date'] ); ?></Data>
		</DatiFattureCollegate>
	<?php endif; ?>

	<?php if ( strval( YITH_Electronic_Invoice()->include_tracking_info ) === 'yes' ) : ?>
		<DatiTrasporto>
			<DatiAnagraficiVettore>
				<IdFiscaleIVA>
					<IdPaese><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', '', 'IdPaese', $document ) ); ?></IdPaese>
					<IdCodice><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', '', 'IdCodice', $document ) ); ?></IdCodice>
				</IdFiscaleIVA>
				<Anagrafica>
					<Denominazione><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', '', 'Denominazione', $document ) ); ?></Denominazione>
				</Anagrafica>
			</DatiAnagraficiVettore>
			<DataOraConsegna><?php echo wp_kses_post( apply_filters( 'ywpi_electronic_invoice_field_value', '', 'DataOraConsegna', $document ) ); ?></DataOraConsegna>
		</DatiTrasporto>
	<?php endif; ?>
</DatiGenerali>
