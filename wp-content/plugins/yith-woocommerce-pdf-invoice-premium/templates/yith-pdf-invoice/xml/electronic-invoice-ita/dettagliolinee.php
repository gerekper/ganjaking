<?php

$i=1;

foreach ( $invoice_details['order_items'] as $item_id => $item ):

    if( $item instanceof WC_Order_Item_Product ){

        $tax_rate_amount = $item->get_subtotal_tax();

    }else{

        $tax_rate_amount = $item->get_total_tax();

    }

    if( abs($tax_rate_amount) > 0 ){

        $order = isset( $invoice_details['main_order'] ) ? $invoice_details['main_order'] : $document->order;

        $tax_class = $item->get_tax_class() == 'inherit' ? '' : $item->get_tax_class();

        $tax_rates = WC_Tax::find_rates(
            array(
                'country'   =>  $order->get_billing_country(), //Use the main order in case of refund
                'state'     =>  $order->get_billing_state(),
                'city'      =>  $order->get_billing_city(),
                'postcode'  =>  $order->get_billing_postcode(),
                'tax_class' =>  $tax_class
            )
        );


        foreach ( $tax_rates as $tax_rate ){

            $tax_percentage = number_format( $tax_rate['rate'], 2, '.', '');

        }

    }else{

        $tax_percentage = '0.00';

    }

    ?>

    <?php $quantity = YITH_Electronic_Invoice()->get_item_quantity( $item, $document, true ); ?>
    <?php $price =  YITH_Electronic_Invoice()->get_order_item_price( $item ,true); ?>
    <?php $discount = ( $item instanceof WC_Order_Item_Product) ? YITH_Electronic_Invoice()->get_discount_increment( $item ) : 0;  ?>
    <?php $total_price = number_format( abs($item['total']) , 2, '.', '') ?>
    <?php $item_name = YITH_Electronic_Invoice()->get_order_item_name( $item ) ?>

    <DettaglioLinee>
        <NumeroLinea><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $i, 'NumeroLinea',$document )?></NumeroLinea>
        <Descrizione><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $item_name , 'Descrizione',$document );  ?></Descrizione>
        <Quantita><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $quantity , 'Quantita',$document ); ?></Quantita>
        <PrezzoUnitario><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $price , 'PrezzoUnitario',$document ); ?></PrezzoUnitario>
        <?php if( $discount > 0 ): ?>
            <ScontoMaggiorazione>
                <Tipo>SC</Tipo>
                <Importo><?php echo YITH_Electronic_Invoice()->get_discount_increment( $item, true, $quantity ); ?></Importo>
            </ScontoMaggiorazione>
        <?php endif; ?>


        <PrezzoTotale><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $total_price, 'PrezzoTotale',$document ); ?></PrezzoTotale>
        <AliquotaIVA><?php echo apply_filters( 'ywpi_electronic_invoice_field_value', $tax_percentage , 'AliquotaIVA',$document ); ?></AliquotaIVA>
        <?php if( $tax_percentage == '0.00' ): ?>
            <Natura>N4</Natura>
        <?php endif; ?>
    </DettaglioLinee>
    <?php $i++ ;?>
<?php endforeach; ?>
