<?php
/**
 * The template for displaying PDF Invoice
 * Override by copying it to yourtheme/memberpress/account/invoice/simple.php.
 */
$color = isset($invoice->color) && !empty($invoice->color) ? $invoice->color : '#eee';
?>
<!DOCTYPE html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style>
    @page{margin:15px 25px}a{color:#5d6975;text-decoration:underline}body{position:relative;margin:0 auto;color:#001028;background:#fff;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;font-size:12px;font-weight:400;}header{padding:10px 0 10px;margin-bottom:30px}#logo{margin:0 auto 10px}#logo img,.img-responsive{max-width:100%;height:auto!important}h1{color:#fff;font-size:1.2em;padding:.3em 0;font-weight:400;text-align:center;margin:0 0 20px 0;background:<?php echo $color ?>}#project{vertical-align:top}#project span{color:#5d6975;text-align:right;width:52px;margin-right:10px;display:inline-block;font-size:.8em;vertical-align: text-top;}#company{text-align:right;vertical-align:top}#company div,#project div{white-space:nowrap}#company p,#project p{margin:0}table{width:100%;border-collapse:collapse;border-spacing:0;margin-bottom:20px}table#content tr:nth-child(2n-1) td{background:#f5f5f5}table#content tr:last-child(2n-1) td{background:#fff}table td,table th{text-align:left}table th{padding:5px 20px;border-bottom:1px solid #c1ced9;white-space:nowrap}.notice h4,table .notice h3,table th{color:#5d6975;font-weight:400}table .desc,table .service{text-align:left}table td{padding:10px 25px}table td.desc,table td.service{vertical-align:top}table td.grand{border-top:1px solid #5d6975;font-weight:700}#notices .notice{color:#5d6975;font-size:1.2em}footer{color:#5d6975;width:100%;position:absolute;bottom:0;left:0;right:0;border-top:1px solid #c1ced9;padding:0px 0 8px;text-align:center;} footer p{margin:0}
  </style>
</head>

<body>

  <header class="clearfix">
    <div id="logo" style="width:7em">
      <?php if(is_numeric($invoice->logo)){ ?>
      <img src="<?php echo get_attached_file( $invoice->logo ); ?>">
      <?php } ?>
    </div>

    <?php if(absint($invoice->credit_number) > 0) : ?>
      <?php printf( '<h1>%s: %s | %s: %s</h1>', esc_html__( 'CREDIT NOTE NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->credit_number ), esc_html__( 'ORIG. INVOICE NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->invoice_number ) ); ?>
    <?php else: ?>
      <?php printf( '<h1>%s: %s</h1>', esc_html__( 'INVOICE NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->invoice_number ) ); ?>
    <?php endif; ?>
    <table>
      <tr>
        <td id="project">
          <div><?php echo wpautop( $invoice->bill_to ); ?></div>
        </td>
        <td id="company">
          <div><?php echo wpautop( $invoice->company ); ?></div>
        </td>
      </tr>
    </table>

    <div >
    </div>

    <div class="clearfix">
    </div>

  </header>
  <main>

    <table id="content">
      <thead>
        <tr>
          <th><?php esc_html_e( 'DESCRIPTION', 'memberpress-pdf-invoice' ); ?></th>
          <?php
          if ( $invoice->show_quantity ) :
            '<th class="quantity">QUANTITY</th>';
          endif;
          ?>

          <th><?php esc_html_e( 'AMOUNT', 'memberpress-pdf-invoice' ); ?></th>
        </tr>
      </thead>
      <tbody>
      <?php

      foreach ( $invoice->items as $item ) {
        ?>
        <tr>
          <td><?php echo $item['description']; ?></td>
          <?php
          if ( $invoice->show_quantity ) :
            '<td>' . $item['quantity'] . '</td>';
          endif;
          ?>

          <td class="unit"><?php echo $item['amount']; ?></td>
        </tr>
        <?php
      }
      ?>

      <?php if ( isset( $invoice->coupon ) && ! empty( $invoice->coupon ) && $invoice->coupon['id'] != 0 ) : ?>
    <tr>
      <td><?php echo $invoice->coupon['desc']; ?></td>
        <?php if ( $invoice->show_quantity ) : ?>
      <td>&nbsp;</td>
      <?php endif; ?>
      <td class="mp-currency-cell">-<?php echo MeprAppHelper::format_currency( $invoice->coupon['amount'], true, false ); ?></td>
    </tr>
    <?php endif; ?>

    <?php if ( $invoice->tax['amount'] > 0.00 ) : ?>
        <tr>
          <td><?php esc_html_e( 'SUBTOTAL', 'memberpress-pdf-invoice' ); ?></td>
      <?php if ( $invoice->show_quantity ) : ?>
      <td>&nbsp;</td>
      <?php endif; ?>
          <td class="total"><?php echo MeprAppHelper::format_currency( $invoice->subtotal, true, false ); ?></td>
        </tr>
        <tr>
          <td><?php echo MeprUtils::format_tax_percent_for_display( $invoice->tax['percent'] ) . '% ' . $invoice->tax['type']; ?></td>
          <?php if ( $invoice->show_quantity ) : ?>
      <td>&nbsp;</td>
      <?php endif; ?>
          <td class="total"><?php echo MeprAppHelper::format_currency( $invoice->tax['amount'], true, false ); ?></td>
        </tr>
      <?php endif; ?>
        <tr>
          <td class="grand total"><?php esc_html_e( 'GRAND TOTAL', 'memberpress-pdf-invoice' ); ?></td>
          <td class="grand total"><?php echo MeprAppHelper::format_currency( $invoice->total, true, false ); ?></td>
        </tr>
      </tbody>
    </table>

    <table>
      <tr>
        <td>
          <div class="notice"><?php echo wpautop( $invoice->notes ); ?></div>
        </td>
        <td>
          <img class="img-responsivel" style="width:150px; float:right" src="<?php echo esc_url($invoice->paid_logo_url); ?>" alt="paid-stamp">
        </td>
      </tr>
    </table>

  </main>
  <footer>
    <?php echo wpautop( $invoice->footnotes ); ?>
  </footer>
</body>

</html>
