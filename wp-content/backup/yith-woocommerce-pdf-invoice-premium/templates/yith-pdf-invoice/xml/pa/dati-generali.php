<?php $causale = apply_filters( 'ywpi_electronic_invoice_field_value', '', 'Causale', $document ); ?>
<?php $riferimento_numero_linea = apply_filters( 'ywpi_electronic_invoice_field_value', 1, 'RiferimentoNumeroLinea', $document ); ?>
<?php $order_total = $document->order->get_total() ?>
<DatiGenerali>
    <DatiGeneraliDocumento>
        <TipoDocumento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value','TD01','TipoDocumento',$document ) ?></TipoDocumento>
        <Divisa><?php echo apply_filters( 'ywpi_electronic_invoice_field_value',$document->order->get_currency(),'Divisa',$document ) ?></Divisa>
        <Data><?php echo apply_filters( 'ywpi_electronic_invoice_field_value',$document->get_formatted_document_date() ,'Data', $document ) ?></Data>
        <Numero><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $document->formatted_number, 'Numero',$document )?></Numero> <!-- numero fattura -->
        <?php if( $causale != '' ): ?>
            <Causale><?php echo $causale; ?></Causale>
        <?php endif; ?>
        <ImportoTotaleDocumento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $order_total, 'ImportoTotaleDocumento',$document )?></ImportoTotaleDocumento>
    </DatiGeneraliDocumento>
    <DatiOrdineAcquisto>
        <IdDocumento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', get_post_meta( $document->order->get_id(),'_ywpi_invoice_number',true ), 'IdDocumento', $document )?></IdDocumento>
        <?php if( $document->is_pa_customer() ): ?>
            <CodiceCUP><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'CodiceCUP',$document )?></CodiceCUP>
            <CodiceCIG><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', '', 'CodiceCIG',$document )?></CodiceCIG>
        <?php endif; ?>
    </DatiOrdineAcquisto>

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

    <?php if( $document->is_pa_customer() ): ?>
        <DatiContratto>
            <RiferimentoNumeroLinea></RiferimentoNumeroLinea>
            <IdDocumento></IdDocumento>
            <Data></Data>
            <NumItem></NumItem>
            <CodiceCUP></CodiceCUP>
            <CodiceCIG></CodiceCIG>
        </DatiContratto>
        <DatiConvenzione>
            <RiferimentoNumeroLinea></RiferimentoNumeroLinea>
            <IdDocumento></IdDocumento>
            <NumItem></NumItem>
            <CodiceCUP></CodiceCUP>
            <CodiceCIG></CodiceCIG>
        </DatiConvenzione>
        <DatiRicezione>
            <RiferimentoNumeroLinea></RiferimentoNumeroLinea>
            <IdDocumento></IdDocumento>
            <NumItem></NumItem>
            <CodiceCUP></CodiceCUP>
            <CodiceCIG></CodiceCIG>
        </DatiRicezione>
    <?php endif; ?>
</DatiGenerali>