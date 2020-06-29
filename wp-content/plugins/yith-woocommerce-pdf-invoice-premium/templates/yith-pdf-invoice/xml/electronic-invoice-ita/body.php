<FatturaElettronicaBody>
    <?php
    wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/datigenerali.php',
        array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
        '',
        YITH_YWPI_TEMPLATE_DIR  );

    ?>

    <DatiBeniServizi>

        <?php
        wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/dettagliolinee.php',
            array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
        '',
        YITH_YWPI_TEMPLATE_DIR  );

        wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/datiriepilogo.php',
            array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
            '',
            YITH_YWPI_TEMPLATE_DIR  );
        ?>

    </DatiBeniServizi>

    <?php
    wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/datipagamento.php',
        array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
        '',
        YITH_YWPI_TEMPLATE_DIR  );
    ?>


</FatturaElettronicaBody>