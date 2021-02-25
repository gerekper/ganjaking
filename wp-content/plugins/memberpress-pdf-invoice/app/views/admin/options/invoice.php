<style>
  .mb-5{
    margin-bottom: 5px;
  }
</style>

<h3 class="mb-5">
<?php esc_html_e( 'PDF Invoice Settings', 'memberpress-pdf-invoice' ); ?>
<?php
MeprAppHelper::info_tooltip(
  'mepr-pdf-invoice-settings',
  esc_html__( 'PDF Invoice Settings', 'memberpress-pdf-invoice' ),
  esc_html__( 'Enter more of your business\'s details and invoice preferences. Used to generate PDF invoices.', 'memberpress-pdf-invoice' )
);
?>
</h3>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_invoice_template' ); ?>"><?php esc_html_e( 'Template', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <select id="var-mepr-pdf-invoice-template" name="<?php echo $mepr_options->attr_slug( 'biz_invoice_template' ); ?>">
          <?php foreach ( MePdfInvoicesHelper::get_templates() as $key => $var ) : ?>
            <option value="<?php echo $key; ?>" <?php selected( $mepr_options->attr( 'biz_invoice_template' ), $key ); ?>><?php echo $var; ?></option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <!-- <tr valign="top">
      <th scope="row">
        <label for="<?php //echo $mepr_options->attr_slug( 'biz_invoice_paper' ); ?>"><?php //esc_html_e( 'Paper', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <select name="<?php //echo $mepr_options->attr_slug( 'biz_invoice_paper_size' ); ?>" id="<?php //echo $mepr_options->attr_slug( 'biz_invoice_paper_size' ); ?>">
          <?php
          //foreach ( MePdfInvoicesHelper::get_paper_sizes() as $key => $var ) : ?>
            <option value="<?php //echo $key; ?>" <?php //selected( $mepr_options->attr( 'biz_invoice_paper_size' ), $key ); ?>><?php //echo $var; ?></option>
          <?php //endforeach; ?>
        </select>
        <select name="<?php //echo $mepr_options->attr_slug( 'biz_invoice_paper_orientation' ); ?>" id="<?php //echo $mepr_options->attr_slug( 'biz_invoice_paper_orientation' ); ?>">
          <?php
          //foreach ( MePdfInvoicesHelper::get_paper_orientation() as $key => $var ) : ?>
            <option value="<?php //echo $key; ?>" <?php //selected( $mepr_options->attr( 'biz_invoice_paper_orientation' ), $key ); ?>><?php //echo $var; ?></option>
          <?php //endforeach; ?>
        </select>
      </td>
    </tr> -->
    <!-- <tr valign="top">
      <th scope="row">
        <label for="<?php //echo $mepr_options->attr_slug( 'biz_invoice_font' ); ?>"><?php //_e( 'Font', 'memberpress-pdf-invoice' ); ?></label>
        <?php
          // MeprAppHelper::info_tooltip(
          //   'mepr-pdf-invoice-settings',
          //   esc_html__( 'PDF Fonts', 'memberpress-pdf-invoice' ),
          //   sprintf('%s <br/><br/> %s <br/><br/> %s', esc_attr__( 'System Fonts will use PDF standard fonts (Helvetica, Times and Courier) depending on your OS.' ), esc_attr__( 'DejaVu Sans is a unicode font that supports most Western/European languages.', 'memberpress-pdf-invoice' ), esc_attr__( 'If you want support for other languages like CJK, you can add custom fonts here.', 'memberpress-pdf-invoice' ))
          // );
        ?>
      </th>
      <td>
        <select name="<?php //echo $mepr_options->attr_slug( 'biz_invoice_font' ); ?>" id="<?php //echo $mepr_options->attr_slug( 'biz_invoice_font' ); ?>">
          <?php
          //foreach ( MePdfInvoicesHelper::get_fonts() as $var => $key ) : ?>
            <option value="<?php // echo $var; ?>" <?php // selected( $mepr_options->attr( 'biz_invoice_font' ), $var ); ?>><?php // echo $key; ?></option>
          <?php //endforeach; ?>
        </select>
      </td>
    </tr>     -->
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_phone' ); ?>"><?php _e( 'Phone', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <input type="text" class="regular-text" name="<?php echo $mepr_options->attr_slug( 'biz_phone' ); ?>" value="<?php echo $mepr_options->attr( 'biz_phone' ); ?>" />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_email' ); ?>"><?php _e( 'Email', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <input type="text" class="regular-text" name="<?php echo $mepr_options->attr_slug( 'biz_email' ); ?>" value="<?php echo $mepr_options->attr( 'biz_email' ); ?>" />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_logo' ); ?>"><?php _e( 'Logo', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <?php
        $url = wp_get_attachment_url( $mepr_options->attr( 'biz_logo' ) );
        if ( ! empty( $url ) ) {
          printf( '<img id="mepr-biz-logo" src="%s" />', esc_url( $url ) );
          printf( '<input type="hidden" name="%s_remove" value="0" />', $mepr_options->attr_slug( 'biz_logo' ) );
          printf( '<a class="button-secondary" id="mepr-biz-logo-remove" href="#0" title="%s">%s</a>', esc_attr__( 'Remove Logo', 'memberpress-pdf-invoice' ), esc_attr__( '&times; Remove', 'memberpress-pdf-invoice' ) );
        } else{
          printf( '<input type="file" accept="image/*" name="%s" />', $mepr_options->attr_slug( 'biz_logo' ) );
        }
        ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_invoice_format' ); ?>"><?php _e( 'Invoice No. Format', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="mb-5">
          <input type="text" class="regular-text" name="<?php echo $mepr_options->attr_slug( 'biz_invoice_format' ); ?>" value="<?php echo $mepr_options->attr( 'biz_invoice_format' ); ?>" id="mepr-invoice-no">
        </div>
        <div>
          <select id="var-mepr-invoice-no">
            <?php foreach ( MePdfInvoicesHelper::get_invoice_vars() as $var ) : ?>
              <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
            <?php endforeach; ?>
          </select>
          <a href="#" class="button mepr-insert-email-var" data-variable-id="var-mepr-invoice-no" data-textarea-id="mepr-invoice-no"><?php _e( 'Insert &uarr;', 'memberpress-pdf-invoice' ); ?></a>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'inv_starting_number' ); ?>"><?php _e( 'Next Invoice No.', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="mb-5">
          <input type="text" class="regular-text" name="<?php echo $mepr_options->attr_slug( 'inv_starting_number' ); ?>" value="<?php echo $mepr_options->attr( 'inv_starting_number' ); ?>" id="mepr-invoice-no"><br/>
          <?php if(empty($mepr_options->attr( 'inv_starting_number' ))) : ?>
            <small><?php printf(_x('NOTICE: You already have %1$d existing completed transactions. We recommend starting at a number higher than %1$d.', 'ui', 'memberpress', 'memberpress-pdf-invoice'), MePdfInvoiceNumber::completed_refunded_transactions() ) ?></small>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_address_format' ); ?>"><?php _e( 'Business Address Format', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="regular-text mb-5">
        <?php echo MePdfInvoicesHelper::render_editor( $mepr_options->attr( 'biz_address_format' ), 'mepr-biz-address-format', $mepr_options->attr_slug( 'biz_address_format' ) ); ?>
        </div>
        <div>
          <select id="var-mepr-biz-address-format">
            <?php foreach ( MePdfInvoicesHelper::get_invoice_vars() as $var ) : ?>
              <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
            <?php endforeach; ?>
          </select>

          <a href="#" class="button mepr-insert-email-var" data-variable-id="var-mepr-biz-address-format" data-textarea-id="mepr-biz-address-format"><?php esc_html_e( 'Insert &uarr;', 'memberpress-pdf-invoice' ); ?></a>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_cus_address_format' ); ?>"><?php esc_html_e( 'Customer Address Format', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="regular-text mb-5">
        <?php echo MePdfInvoicesHelper::render_editor( $mepr_options->attr( 'biz_cus_address_format' ), 'mepr-cus-address-format', $mepr_options->attr_slug( 'biz_cus_address_format' ) ); ?>
        </div>
        <div>
          <select id="var-mepr-cus-address-format">
            <?php foreach ( MePdfInvoicesHelper::get_invoice_vars() as $var ) : ?>
              <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
            <?php endforeach; ?>
          </select>
          <a href="#" class="button mepr-insert-email-var" data-variable-id="var-mepr-cus-address-format" data-textarea-id="mepr-cus-address-format"><?php esc_html_e( 'Insert &uarr;', 'memberpress-pdf-invoice' ); ?></a>
        </div>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_invoice_notes' ); ?>"><?php _e( 'Notes', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="regular-text mb-5">
        <?php echo MePdfInvoicesHelper::render_editor( $mepr_options->attr( 'biz_invoice_notes' ), 'mepr-invoice-notes', $mepr_options->attr_slug( 'biz_invoice_notes' ) ); ?>
        </div>
        <div>
          <select id="var-mepr-invoice-notes">
            <?php foreach ( MePdfInvoicesHelper::get_invoice_vars() as $var ) : ?>
              <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
            <?php endforeach; ?>
          </select>
          <a href="#" class="button mepr-insert-email-var" data-variable-id="var-mepr-invoice-notes" data-textarea-id="mepr-invoice-notes"><?php esc_html_e( 'Insert &uarr;', 'memberpress-pdf-invoice' ); ?></a>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_invoice_footnotes' ); ?>"><?php _e( 'Footnotes', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <div class="regular-text mb-5">
        <?php echo MePdfInvoicesHelper::render_editor( $mepr_options->attr( 'biz_invoice_footnotes' ), 'mepr-invoice-footnotes', $mepr_options->attr_slug( 'biz_invoice_footnotes' ) ); ?>
        </div>
        <div>
          <select id="var-mepr-invoice-footnotes">
            <?php foreach ( MePdfInvoicesHelper::get_invoice_vars() as $var ) : ?>
              <option value="{$<?php echo $var; ?>}">{$<?php echo $var; ?>}</option>
            <?php endforeach; ?>
          </select>
          <a href="#" class="button mepr-insert-email-var" data-variable-id="var-mepr-invoice-footnotes" data-textarea-id="mepr-invoice-footnotes"><?php esc_html_e( 'Insert &uarr;', 'memberpress-pdf-invoice' ); ?></a>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $mepr_options->attr_slug( 'biz_invoice_color' ); ?>"><?php _e( 'Color', 'memberpress-pdf-invoice' ); ?></label>
      </th>
      <td>
        <input type="text" class="regular-text mepr-color-picker" name="<?php echo $mepr_options->attr_slug( 'biz_invoice_color' ); ?>" value="<?php echo $mepr_options->attr( 'biz_invoice_color' ); ?>" />
      </td>
    </tr>
  </tbody>
</table>
