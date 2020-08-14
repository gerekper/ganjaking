<DatiGenerali>
    <DatiGeneraliDocumento>
        <TipoDocumento><?php echo $invoice_details['document_type'] ?></TipoDocumento>
        <Divisa><?php echo $invoice_details['order_currency'] ?></Divisa>
        <Data><?php echo $invoice_details['document_date'] ?></Data>
        <Numero><?php echo $invoice_details['document_number']?></Numero> <!-- numero fattura -->
        <ImportoTotaleDocumento><?php echo $invoice_details['document_order_total']?></ImportoTotaleDocumento>
        <?php if( $invoice_details['reason'] != '' ): ?>
            <Causale><?php echo $invoice_details['reason'] ?></Causale>
        <?php endif; ?>
    </DatiGeneraliDocumento>
    <DatiOrdineAcquisto>
        <IdDocumento><?php echo $invoice_details['document_id'] ?></IdDocumento>
    </DatiOrdineAcquisto>

    <?php if( $invoice_details['is_refund'] ): ?>
    <!-- Only per refund -->
        <DatiFattureCollegate>
            <IdDocumento><?php echo $invoice_details['refund_document_id']; ?></IdDocumento>
            <Data><?php echo $invoice_details['refund_document_date'] ?></Data>
        </DatiFattureCollegate>
    <?php endif; ?>

    <?php if( YITH_Electronic_Invoice()->include_tracking_info == 'yes' ): ?>
        <DatiTrasporto>
            <DatiAnagraficiVettore>
                <IdFiscaleIVA>
                    <IdPaese><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'IdPaese',$document )?></IdPaese>
                    <IdCodice><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'IdCodice',$document )?></IdCodice>
                </IdFiscaleIVA>
                <Anagrafica>
                    <Denominazione><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'Denominazione',$document )?></Denominazione>
                </Anagrafica>
            </DatiAnagraficiVettore>
            <DataOraConsegna><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'DataOraConsegna',$document )?></DataOraConsegna>
        </DatiTrasporto>
    <?php endif; ?>
</DatiGenerali>