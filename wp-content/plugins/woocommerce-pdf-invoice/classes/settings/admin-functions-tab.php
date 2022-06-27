<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
  
// include DomPDF autoloader
require_once ( WP_PLUGIN_DIR . "/woocommerce-pdf-invoice/lib/dompdf/autoload.inc.php" );

// reference the Dompdf namespace
use Dompdf\Dompdf;

$current_user = wp_get_current_user();

?>

<h3 class="dompdf-config"><?php _e("Send test email with PDF attachment" , 'woocommerce-pdf-invoice' ); ?></h3>
                    
<form method="post" action="" >
<table class="dompgf-debugging-table">
	<tr>
    	<th colspan="2"><?php _e("Enter email address" , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="email" name="pdfemailtest-emailaddress" placeholder="Email Address"/></td>
      <td>
          <?php wp_nonce_field('pdf_test_nonce_action','pdf_test_nonce'); ?>
          <input type="hidden" name="pdfemailtest" value="1" />
          <input type="submit" class="dompgf-debugging-submit" value="<?php _e("Send test email with PDF Attachment" , 'woocommerce-pdf-invoice' ); ?>" />
      </td>
	</tr>
</table>
</form>

<?php 
$pdf_past_orders_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_pdf_past_orders', 'administrator' );
if( in_array( $pdf_past_orders_allowed_user_role, $current_user->roles ) ) { 
?>
<h3 class="dompdf-config"><?php _e("Create Invoices For Past Orders" , 'woocommerce-pdf-invoice' ); ?></h3>
<p><?php _e("This option will create invoices for any orders that are complete and don't have an invoice number.<br />The process runs in the background in batches to avoid any server timeouts." , 'woocommerce-pdf-invoice' ); ?></p>
<form method="post" action="" name="pdf_past_orders">
<table class="dompgf-debugging-table">
  <tr>
      <th colspan="2"><?php _e("Type 'confirm' to create invoices for past orders." , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="text" name="pdf_past_orders-confirmation" placeholder="Type 'confirm'"/></td>
      <td>
          <?php wp_nonce_field('pdf_past_orders_nonce_action','pdf_past_orders_nonce'); ?>
          <input type="hidden" name="pdf_past_orders" value="1" />
          <input type="submit" class="dompgf-debugging-submit" value="<?php _e("Create Invoices For Past Orders" , 'woocommerce-pdf-invoice' ); ?>" />
      </td>
  </tr>
</table>
</form>
<?php } ?>

<?php 
$pdf_past_orders_email_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_pdf_past_orders_email', 'administrator' );
if( in_array( $pdf_past_orders_email_allowed_user_role, $current_user->roles ) ) { 
?>
<h3 class="dompdf-config"><?php _e("Create And Email Invoices For Past Orders" , 'woocommerce-pdf-invoice' ); ?></h3>
<p><?php _e("This option will create invoices for any orders that are complete and don't have an invoice number.<br />The invoice will be emailed to the customer. The process runs in the background in batches to avoid any server timeouts.<br /><strong>Warning : sending large numbers of emails in short periods can cause deliverability issues.</strong>" , 'woocommerce-pdf-invoice' ); ?></p>
<form method="post" action="" name="pdf_past_orders_email">
<table class="dompgf-debugging-table">
  <tr>
      <th colspan="2"><?php _e("Type 'confirm' to create invoices for past orders." , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="text" name="pdf_past_orders_email-confirmation" placeholder="Type 'confirm'"/></td>
      <td>
          <?php wp_nonce_field('pdf_past_orders_email_nonce_action','pdf_past_orders_email_nonce'); ?>
          <input type="hidden" name="pdf_past_orders_email" value="1" />
          <input type="submit" class="dompgf-debugging-submit" value="<?php _e("Create And Email Invoices For Past Orders" , 'woocommerce-pdf-invoice' ); ?>" />
      </td>
  </tr>
</table>
</form>
<?php } ?>

<?php 
$pdf_delete_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_pdf_delete', 'administrator' );
if( in_array( $pdf_delete_allowed_user_role, $current_user->roles ) ) { 
?>
<h3 class="dompdf-config"><?php _e("Delete Invoice Information" , 'woocommerce-pdf-invoice' ); ?></h3>
<p><?php _e("This is an unrecoverable option, use with caution." , 'woocommerce-pdf-invoice' ); ?></p>
<p><?php _e('You can delete the invoice information store in each order.<br /><strong>The information can only be recovered using a backup of your database. USE WITH CAUTION!</strong>' , 'woocommerce-pdf-invoice' ); ?></p>
<form method="post" action="" name="pdfdelete">
<table class="dompgf-debugging-table">
  <tr>
      <th colspan="2"><?php _e("Type 'confirm' to confirm you understand that this will delete all of the invoice information stored in each order." , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="text" name="pdfdelete-confirmation" placeholder="Type 'confirm'"/></td>
      <td>
          <?php wp_nonce_field('pdf_delete_nonce_action','pdf_delete_nonce'); ?>
          <input type="hidden" name="pdfdelete" value="1" />
          <input type="submit" class="dompgf-debugging-submit" value="<?php _e("Delete invoice information from orders and reset invoice numbers" , 'woocommerce-pdf-invoice' ); ?>" />
      </td>
  </tr>
</table>
</form>
<?php } ?>

<?php 
$pdf_fix_dates_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_pdf_fix_dates', 'administrator' );
if( in_array( $pdf_fix_dates_allowed_user_role, $current_user->roles ) ) { 
?>
<h3 class="dompdf-config"><?php _e("Fix Invoice dates" , 'woocommerce-pdf-invoice' ); ?></h3>
<p><?php _e("<strong>This is an unrecoverable option, use with caution.</strong> The process runs in the background in batches to avoid any server timeouts." , 'woocommerce-pdf-invoice' ); ?></p>
<p><?php _e('You can update the invoice date and date format using this option.<br /><strong>This change can only be undone using a backup of your database. USE WITH CAUTION!</strong>' , 'woocommerce-pdf-invoice' ); ?></p>
<form method="post" action="" name="pdffixdates">
<table class="dompgf-debugging-table">
  <tr>
      <th colspan="2"><?php _e("Type 'confirm' to confirm you understand that this will change correct the date and date format for ALL invoices, based on the current PDF Invoice date settings." , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="text" name="pdffix-dates-confirmation" placeholder="Type 'confirm'"/></td>
      <td>
          <?php wp_nonce_field('pdf_fix_dates_nonce_action','pdf_fix_dates_nonce'); ?>
          <input type="hidden" name="pdffixdates" value="1" />
          <input type="submit" class="dompgf-debugging-submit" value="<?php _e("Fix Invoice Dates" , 'woocommerce-pdf-invoice' ); ?>" />
      </td>
  </tr>
</table>
</form>
<?php } ?>
