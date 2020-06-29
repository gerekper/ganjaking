<?php

$invoice_details = YITH_Electronic_Invoice()->get_invoice_details( $document );

echo '<?xml version="1.0" encoding="UTF-8" ?>
         <p:FatturaElettronica versione="FPR12" xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
             xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" 
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
             xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 
             http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">';


wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/header.php',
    array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
    '',
    YITH_YWPI_TEMPLATE_DIR  );

wc_get_template( 'yith-pdf-invoice/xml/'. $type .'/body.php',
    array( 'document' => $document, 'type' => $type, 'invoice_details'  =>  $invoice_details),
    '',
    YITH_YWPI_TEMPLATE_DIR  );


echo '</p:FatturaElettronica>';


