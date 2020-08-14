<DatiPagamento>
    <CondizioniPagamento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $invoice_details['payment_info']['conditions'], 'CondizioniPagamento',$document )?></CondizioniPagamento>
    <DettaglioPagamento>
        <ModalitaPagamento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $invoice_details['payment_info']['mode'], 'ModalitaPagamento',$document )?></ModalitaPagamento>
        <ImportoPagamento><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $invoice_details['payment_info']['total_amount'], 'ImportoPagamento',$document )?></ImportoPagamento>
    </DettaglioPagamento>
</DatiPagamento>