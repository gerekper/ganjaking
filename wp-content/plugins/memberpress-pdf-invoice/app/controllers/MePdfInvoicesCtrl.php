<?php
if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' );
}

class MePdfInvoicesCtrl extends MeprBaseCtrl {

  protected $invoice = array();
  protected $txn;

  public function __construct() {
     parent::__construct();
  }

  /**
   * Load hooks.
   *
   * @return void
   */
  public function load_hooks() {
    add_action( 'mepr_account_payments_table_header', array( $this, 'table_header' ) );
    add_action( 'mepr_account_payments_table_row', array( $this, 'table_row' ) );
    add_action( 'wp_ajax_mepr_download_invoice', array( $this, 'ajax_download_invoice' ) );

    add_action( 'mepr_display_info_options', array( $this, 'admin_options_invoice_fields' ) );
    add_filter( 'mepr_view_paths', array( $this, 'add_view_path' ) );

    add_filter( 'mepr-validate-options', array( $this, 'validate_options' ) );
    add_action( 'mepr-process-options', array( $this, 'process_options' ) );
    add_filter( 'mepr-options-dynamic-attrs', array( $this, 'add_dynamic_attrs' ) );
    add_action( 'admin_enqueue_scripts', 'MePdfInvoicesCtrl::enqueue_scripts' );

    add_filter( 'mepr_transaction_email_params', array( $this, 'more_invoice_params' ), 10, 2 );
    add_filter( 'mepr-pdf-invoice-data', array( $this, 'test_invoice_data' ), 10, 2 );
    add_filter( 'mepr-wp-mail-headers', array( $this, 'add_email_headers' ), 10, 5 );
    add_filter( 'mepr_email_send_attachments', array( $this, 'add_email_attachments' ), 10, 4 );
    add_filter( 'mepr_email_sent', array( $this, 'remove_receipt_pdf_invoice' ), 10, 3 );
  }

  /**
   * Enqueues styles and scripts
   *
   * @param  mixed $hook
   *
   * @return void
   */
  public static function enqueue_scripts( $hook ) {
    if ( $hook == 'memberpress_page_memberpress-options' ) {
      // Add the color picker css file
      wp_enqueue_style( 'wp-color-picker' );

      wp_enqueue_style( 'mpdf-invoice-css', MPDFINVOICE_URL . 'css/invoice.css', array( 'mp-options' ), MPDFINVOICE_VERSION );
      wp_enqueue_script( 'mpdf-invoice-js', MPDFINVOICE_URL . 'js/invoice.js', array( 'jquery', 'wp-color-picker' ), MPDFINVOICE_VERSION );
    }
  }

  /**
   * Outputs Download column header to Account>Payment page
   *
   * @return void
   */
  public function table_header() {
    ?><th><?php _ex( 'Download', 'ui', 'memberpress-pdf-invoice' ); ?></th>
    <?php
  }

  /**
   * Outputs Download column row to Account>Payment page
   *
   * @param  mixed $payment
   *
   * @return void
   */
  public function table_row( $payment ) {
    ?>
    <td data-label="<?php echo esc_html_x( 'Download', 'ui', 'memberpress-pdf-invoice' ); ?>">
      <a href="<?php
      echo MeprUtils::admin_url(
        'admin-ajax.php',
        array( 'download_invoice', 'mepr_invoices_nonce' ),
        array(
          'action' => 'mepr_download_invoice',
          'txn'    => $payment->id,
        )
      );
      ?>" target="_blank"><?php echo esc_html_x( 'PDF', 'ui', 'memberpress-pdf-invoice' ); ?></a>
    </td>
    <?php
  }

  /**
   * Adds Invoice Setting fields to MemberPress Settings page.
   *
   * @return void
   */
  public function admin_options_invoice_fields() {
    $mepr_options = MeprOptions::fetch();
    MeprView::render( '/admin/options/invoice', get_defined_vars() );
  }

  /**
   * Dynamic attributes for admin invoice settings
   *
   * @param  mixed $attrs
   *
   * @return array
   */
  public function add_dynamic_attrs( $attrs ) {
    $attrs = array_merge( $attrs, MePdfInvoicesHelper::get_dynamic_attrs() );
    return $attrs;
  }

  /**
   * Hooks to MemberPress validation function
   *
   * @param  mixed $errors
   *
   * @return array
   */
  public function validate_options( $errors ) {
    // Validate Logo
    if ( isset( $_FILES['mepr_biz_logo'] ) && ! empty( $_FILES['mepr_biz_logo']['name'] ) ) {
      $filetype = wp_check_filetype( basename( $_FILES['mepr_biz_logo']['name'] ), null );
      if ( ! in_array( $filetype['type'], array( 'image/jpeg', 'image/gif', 'image/png' ) ) ) {
        $errors[] = esc_html__( 'Business Logo must have a valid image extension. Valid extensions are JPG, PNG, and GIF', 'memberpress-pdf-invoice' );
      }
    }

    // Validate Business Email
    if ( isset( $_POST['mepr_biz_email'] ) && !empty( $_POST['mepr_biz_email'] ) && false == is_email( $_POST['mepr_biz_email'] ) ) {
      $errors[] = esc_html__( 'Invalid Business Email Format', 'memberpress-pdf-invoice' );
    }

    return $errors;
  }

  /**
   * Process invoice settings values
   *
   * @param  mixed $params
   *
   * @return void
   */
  public function process_options( $params ) {
    $mepr_options = MeprOptions::fetch();

    if ( isset( $_FILES['mepr_biz_logo'] ) && ! empty( $_FILES['mepr_biz_logo']['name'] ) ) {
      $upload = wp_upload_bits( $_FILES['mepr_biz_logo']['name'], null, @file_get_contents( $_FILES['mepr_biz_logo']['tmp_name'] ) );

      if ( false === $upload['error'] ) {

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype( basename( $upload['file'] ), null );
        $tempDir  = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
          'guid'           => $tempDir['url'] . '/' . basename( $upload['file'] ),
          'post_mime_type' => $filetype['type'],
          'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
          'post_content'   => '',
          'post_status'    => 'inherit',
        );
        $attach_id  = wp_insert_attachment( $attachment, $upload['file'] );
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        $_POST['mepr_biz_logo'] = $attach_id;
      }
    } elseif ( isset( $_POST['mepr_biz_logo_remove'] ) && '1' == $_POST['mepr_biz_logo_remove'] ) {
      wp_delete_attachment( $mepr_options->attr( 'biz_logo' ) );
      $_POST['mepr_biz_logo'] = '';
    }
  }

  /**
   * Download process begins
   *
   * @return void
   */
  public function ajax_download_invoice() {
    $current_user = MeprUtils::get_currentuserinfo();

    // Exit if any of the checks fail.
    $this->do_security_checks( $current_user );

    $mepr_options = MeprOptions::fetch();
    $prd          = $this->txn->product();

    // Prepare the data
    $invoice = (object) $this->collect_invoice_data( $this->txn, $mepr_options, $prd, $current_user );

    $mpdf = new MePdfMPDF();
    $mpdf->render( $invoice, $this->txn );

    wp_die();
  }


  /**
   * Security checks
   *
   * @return array
   */
  public function do_security_checks() {

    check_ajax_referer( 'download_invoice', 'mepr_invoices_nonce' );

    if ( ! MeprUtils::is_user_logged_in() ) {
      MeprUtils::exit_with_status( 403, esc_html__( 'Forbidden', 'memberpress-pdf-invoice' ) );
    }

    $current_user = MeprUtils::get_currentuserinfo();

    if ( ! isset( $_REQUEST['txn'] ) ) {
      MeprUtils::exit_with_status( 400, esc_html__( 'No transaction specified', 'memberpress-pdf-invoice' ) );
    }

    if ( ! MeprUpdateCtrl::is_activated() ) {
      MeprUtils::exit_with_status( 403, esc_html__( 'A licensing error has occurred, please contact site administrator.', 'memberpress-pdf-invoice' ) );
    }

    $txn = new MeprTransaction( $_REQUEST['txn'] );
    if ( $txn->id <= 0 ) {
      MeprUtils::exit_with_status( 400, esc_html__( 'Invalid Transaction', 'memberpress-pdf-invoice' ) );
    }

    if ( ! MeprUtils::is_mepr_admin() && $txn->user_id != $current_user->ID ) {
      MeprUtils::exit_with_status( 403, esc_html__( 'Forbidden Transaction', 'memberpress-pdf-invoice' ) );
    }

    $this->txn = $txn;
  }

  /**
   * Gets all invoice data
   *
   * @param  mixed $txn
   * @param  mixed $mepr_options
   * @param  mixed $bill_to
   * @param  mixed $prd
   * @param  mixed $company
   *
   * @return void
   */
  public function collect_invoice_data( $txn, $mepr_options, $prd, $current_user ) {
    $created_ts = strtotime( $txn->created_at );
    $blog_name  = get_option( 'blogname' );

    $this->invoice['template']        = $mepr_options->attr( 'biz_invoice_template' );
    $this->invoice['locale']          = get_locale();
    $this->invoice['invoice_number']  = $this->replace_variables( 'biz_invoice_no', $txn );
    $this->invoice['company']         = $this->replace_variables( 'biz_address_format', $txn );
    $this->invoice['bill_to']         = $this->replace_variables( 'biz_cus_address_format', $txn );
    $this->invoice['logo']            = $mepr_options->attr( 'biz_logo' );
    $this->invoice['notes']           = $this->replace_variables( 'biz_invoice_notes', $txn );
    $this->invoice['footnotes']       = $this->replace_variables( 'biz_invoice_footnotes', $txn );
    $this->invoice['tax_rate']        = $txn->tax_rate;
    $this->invoice['tax_description'] = $txn->tax_desc;
    $this->invoice['paid_at']         = $created_ts;
    $this->invoice['invoice_date']    = $created_ts;
    $this->invoice['color']           = $mepr_options->attr( 'biz_invoice_color' );

    if ( $sub = $txn->subscription() ) {
      if ( $sub->trial && $sub->txn_count < 1 ) {
        $desc = esc_html__( 'Initial Payment', 'memberpress-pdf-invoice' );
        // Must do this *after* apply tax so we don't screw up the invoice
        $txn->subscription_id = $sub->id;
      } elseif ( $sub->txn_count >= 1 ) {
        $desc = esc_html__( 'Subscription Payment', 'memberpress-pdf-invoice' );
      } else {
        $desc = esc_html__( 'Initial Payment', 'memberpress-pdf-invoice' );
      }
    } else {
      $desc = esc_html__( 'Payment', 'memberpress-pdf-invoice' );
    }

    if ( $coupon = $txn->coupon() ) {
      $amount     = $prd->price;
      $cpn_id     = $coupon->ID;
      $cpn_desc   = sprintf( esc_html__( "Coupon Code '%s'", 'memberpress-pdf-invoice' ), $coupon->post_title );
      $cpn_amount = MeprUtils::format_float( (float) $amount - (float) $txn->amount );
    } else {
      $amount     = $txn->amount;
      $cpn_id     = 0;
      $cpn_desc   = '';
      $cpn_amount = 0.00;
    }

    $this->invoice['items'] = array(
      array(
        'description' => $prd->post_title . '&nbsp;&ndash;&nbsp;' . $desc,
        'quantity'    => 1,
        'amount'      => $amount,
      ),
    );

    $this->invoice['coupon'] = array(
      'id'     => $cpn_id,
      'desc'   => $cpn_desc,
      'amount' => $cpn_amount,
    );

    $this->invoice['tax'] = array(
      'percent' => $txn->tax_rate,
      'type'    => $txn->tax_desc,
      'amount'  => $txn->tax_amount,
    );

    $this->invoice['show_quantity'] = MeprHooks::apply_filters( 'mepr-invoice-show-quantity', false, $txn );

    $quantities = array();
    foreach ( $this->invoice['items'] as $item ) {
      $quantities[] = $item['amount'];
    }

    $this->invoice['subtotal'] = (float) array_sum( $quantities ) - (float) $this->invoice['coupon']['amount'];
    $this->invoice['total']    = $this->invoice['subtotal'] + $this->invoice['tax']['amount'];

    return MeprHooks::apply_filters( 'mepr-pdf-invoice-data', $this->invoice, $txn );
  }

  /**
   * Utility function to replace variables
   *
   * @param  mixed $text
   * @param  mixed $values
   *
   * @return mixed
   */
  public function replace_variables( $name, $txn ) {
    $mepr_options = MeprOptions::fetch();
    $text         = $mepr_options->get_attr( $name );
    $params       = MePdfInvoicesHelper::get_invoice_params( $txn );

    return MeprUtils::replace_vals( $text, $params );
  }

  /**
   * Gets view template content
   *
   * @param  mixed $invoice
   *
   * @return mixed
   */
  public static function get_html_content( $invoice ) {
    $mepr_options  = MeprOptions::fetch();
    $template_name = isset( $invoice->template ) && ! empty( $invoice->template ) ? $invoice->template : $mepr_options->attr( 'invoice_template' );

    $template_name = MeprHooks::apply_filters( 'mpdf_invoices_template_name', $template_name );

    // Does template exists? If not default to simple
    if ( false === MeprView::file( '/account/invoice/' . $template_name ) ) {
      $template_name = 'simple';
    }

    return MeprView::get_string( '/account/invoice/' . $template_name, get_defined_vars() );
  }

  /**
   * Adds these params to TransactionsHelper params array.
   *
   * @param  mixed $params
   * @param  mixed $txn
   *
   * @return void
   */
  public function more_invoice_params( $params, $txn ) {
    $usr          = $txn->user();
    $mepr_options = MeprOptions::fetch();
    $created_ts   = strtotime( $txn->created_at );

    $params['user_address_single'] = str_replace( '<br/>', ', ', preg_replace( '/^(<br\s*\/?>)*|(<br\s*\/?>)*$/i', '', $usr->formatted_address() ) );
    $params['invoice_num']         = $txn->id;
    $params['biz_phone']           = $mepr_options->attr( 'biz_phone' );
    $params['biz_email']           = $mepr_options->attr( 'biz_email' );
    $params['trans_date']          = date_i18n( get_option( 'date_format' ), $created_ts );
    $params['biz_country']         = MePdfInvoicesHelper::get_formatted_country( $mepr_options->attr( 'biz_country' ) );
    $params['site_domain']         = home_url();
    $params['pdf_txn']             = $txn->id;

    return $params;
  }

  /**
   * test_email_params
   *
   * @param  mixed $params
   * @param  mixed $txn
   * @return void
   */
  public function test_invoice_data($invoice, $txn){
    if($txn->id == 0){

      $mepr_options = MeprOptions::fetch();

      $invoice['bill_to']      = '<br/>' .
                                      __('John Doe', 'memberpress', 'memberpress-pdf-invoice') .'<br/>' .
                                      __('111 Cool Avenue', 'memberpress', 'memberpress-pdf-invoice') .'<br/>' .
                                      __('New York, NY 10005', 'memberpress', 'memberpress-pdf-invoice') . '<br/>' .
                                      __('United States', 'memberpress', 'memberpress-pdf-invoice') . '<br/>';
      $invoice['items']             = array(
        array(
          'description' => esc_html__( 'Bronze Edition', 'memberpress-pdf-invoice' ) . '&nbsp;&ndash;&nbsp;' . esc_html__( 'Initial Payment', 'memberpress-pdf-invoice' ),
          'quantity'    => 1,
          'amount'      => sprintf( '%s'.MeprUtils::format_float(15.15), stripslashes( $mepr_options->currency_symbol ) ),
        ),
      );

      $invoice['coupon'] = array(
        'id'      => 0,
        'desc'    => '',
        'amount'  => 0
      );

      $invoice['tax']               = array(
          'percent' => 10,
          'type'    => '',
          'amount'  => MeprUtils::format_float(0.15)
      );
      $invoice['paid_at'] = time();
      $invoice['tax_rate'] = 10;
      $invoice['subtotal'] = MeprUtils::format_float(15.15);
      $invoice['total'] = MeprUtils::format_float(15.30);
    }

    return $invoice;
  }


  /**
   *  Attach Invoice PDF file to email message
   *
   * @param  mixed $attachments
   * @param  mixed $class
   * @param  mixed $body
   * @param  mixed $values
   * @return string
   */
  public function add_email_attachments($attachments, $class, $body, $values){

    if ($class instanceof MeprUserReceiptEmail && isset($values['pdf_txn'])) {
      $txn_id = $values['pdf_txn'];
      $txn = new MeprTransaction($txn_id);

      if(!$txn->id || empty($values['pdf_txn'])){
        return $attachments;
      }
    }
    elseif($class instanceof MeprUserReceiptEmail && 'johndoe' == $values['user_login']){
      // We're sending test email
      $txn = new MeprTransaction();
    }
    else{
      return $attachments;
    }

    $file = $this->create_receipt_pdf($txn);
    if($file){
      $attachments[] = $file;
    }

    return $attachments;
  }


  /**
   * create_receipt_pdf
   *
   * @param  mixed $txn
   * @return void
   */
  public function create_receipt_pdf($txn){
    $mepr_options = MeprOptions::fetch();
    $prd = $txn->product();
    $current_user = get_current_user();
    $invoice = (object) $this->collect_invoice_data( $txn, $mepr_options, $prd, $current_user );

    // Create and Save PDF in the Uploads directory
    $mpdf = new MePdfMPDF();
    $path = $mpdf->save( $invoice, $txn );
    return $path;
  }

  public function remove_receipt_pdf_invoice($class, $values, $attachments){
    if(!$class instanceof MeprUserReceiptEmail && !$class instanceof MeprBaseEmail){
      return;
    }

    if($attachments){
      foreach ($attachments as $attachment) {
        unlink($attachment);
      }
    }
  }

  public function add_email_headers($headers, $recipients,  $subject, $message, $attachments){
    if($attachments){
      $separator = md5(time());
      $eol = PHP_EOL;
      $headers = "MIME-Version: 1.0".$eol;
      // $headers .= "Content-Type: application/pdf".$eol; // see below
      // $headers .= "Content-Transfer-Encoding: 7bit".$eol;
    }
    return $headers;
  }

  /**
   * Add plugin path to memberpress view path
   *
   * @param  mixed $paths MemberPress paths
   *
   * @return mixed
   */
  function add_view_path( $paths ) {
    array_splice( $paths, 1, 0, MPDFINVOICE_PATH . 'app/views' );
    return $paths;
  }

} //End class
