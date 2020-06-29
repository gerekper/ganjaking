<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	Class WC_pdf_template_editor {

	    public function __construct() {
			
			global $wpdb,$woocommerce;

			// TODO Add duplicate links to PDF Template post type
			// if ( ! function_exists( 'duplicate_post_plugin_activation' ) ) {
			//	include_once 'class-pdf-template-duplicator.php';
			// }

			// Create pdf_invoice_templates custom post type
			add_action( 'init', array( 'WC_pdf_template_editor', 'create_posttype' ) );


			// Disable block editor and visual editor for PDF Invoice template pages
			add_filter('use_block_editor_for_post_type', array( 'WC_pdf_template_editor', 'disable_gutenberg') );
			add_filter('user_can_richedit', array( 'WC_pdf_template_editor', 'disable_richeditor') );

			// Create metaboxes for PDF Invoice template page editor
			add_action( 'add_meta_boxes', array( 'WC_pdf_template_editor', 'default_invoice_layout_meta_box' ), 10, 2 );

			// Contextual help
			// PHP Deprecated:  contextual_help is <strong>deprecated</strong> since version 3.3.0! Use get_current_screen()->add_help_tab(), get_current_screen()->remove_help_tab()
			// add_action( 'contextual_help', array( 'WC_pdf_template_editor', 'codex_add_help_text' ), 10, 3 );

		}

		// Create PDF Invoice Template post type
		public static function create_posttype() {

			$labels = array(
				'name'               => _x( 'PDF Invoice Templates', 'post type general name', 'woocommerce-pdf-invoice' ),
				'singular_name'      => _x( 'PDF Invoice Template', 'post type singular name', 'woocommerce-pdf-invoice' ),
				'menu_name'          => _x( 'PDF Invoice Templates', 'admin menu', 'woocommerce-pdf-invoice' ),
				'name_admin_bar'     => _x( 'PDF Invoice Template', 'add new on admin bar', 'woocommerce-pdf-invoice' ),
				'add_new'            => _x( 'Add New', 'template', 'woocommerce-pdf-invoice' ),
				'add_new_item'       => __( 'Add New Template', 'woocommerce-pdf-invoice' ),
				'new_item'           => __( 'New Template', 'woocommerce-pdf-invoice' ),
				'edit_item'          => __( 'Edit Template', 'woocommerce-pdf-invoice' ),
				'view_item'          => __( 'View Template', 'woocommerce-pdf-invoice' ),
				'all_items'          => __( 'All Templates', 'woocommerce-pdf-invoice' ),
				'search_items'       => __( 'Search Templates', 'woocommerce-pdf-invoice' ),
				'parent_item_colon'  => __( 'Parent Templates:', 'woocommerce-pdf-invoice' ),
				'not_found'          => __( 'No templates found.', 'woocommerce-pdf-invoice' ),
				'not_found_in_trash' => __( 'No templates found in Trash.', 'woocommerce-pdf-invoice' )
			);

			$args = array(
				'labels'             => $labels,
		                'description'        => __( 'Description.', 'woocommerce-pdf-invoice' ),
				'public'             => false,
				'show_ui' 			 => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'pdfi_templates' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => true,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'revisions' )
			);

			register_post_type( 'pdfi_templates', $args );

		}


		/**
		 * Disable block editor for PDF Invoice template pages
		 * This is important to prevent any HTML from being corrupted.
		 */
		public static function disable_gutenberg( $current_status, $post_type = NULL ) {
		    // Use your post type key instead of 'product'
		    if ($post_type === 'pdfi_templates') return false;
		    return $current_status;
		}

		/**
		 * Disable visual editor for PDF Invoice template pages
		 * This is important to prevent any HTML from being corrupted.
		 */
		public static function disable_richeditor( $default ){
			if( get_post_type() === 'pdfi_templates') {
				return false;
			}
			return $default;
		}

		/**
		 * Show default template HTML meta box at the very bottom so that users can easily revert if necessary 
		 */	
		public static function default_invoice_layout_meta_box( $post_type, $post ) {
			add_meta_box( 'woocommerce-invoice-meta', __('Default template tags', 'woocommerce-pdf-invoice'), array( 'WC_pdf_template_editor','default_invoice_layout_meta_box_contents' ), 'pdfi_templates', 'advanced', 'low');
		}

		/**
		 * Show default template HTML meta box contents
		 */	
		public static function default_invoice_layout_meta_box_contents( $post ) {
			global $woocommerce;

			// Set $order_id and $order to NULL to prevent notices.
			$order_id = NULL;
			$order    = NULL;
			$field 	  = NULL;

			$default_template_tags = array(
				'PDFFONTFAMILY'        => array(
				    'tag'         => __( '[[PDFFONTFAMILY]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '\"DejaVu Sans\", \"DejaVu Sans Mono\", \"DejaVu\", sans-serif, monospace',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_default_font_family', '\"DejaVu Sans\", \"DejaVu Sans Mono\", \"DejaVu\", sans-serif, monospace', $order_id );",
				    'translate'   => 'no'
				),
				'TEXTDIRECTION'        => array(
				    'tag'         => __( '[[TEXTDIRECTION]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFLOGO'        => array(
				    'tag'         => __( '[[PDFLOGO]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFCOMPANYNAME'        => array(
				    'tag'         => __( '[[PDFCOMPANYNAME]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => 'apply_filters( "woocommerce_pdf_invoice_company_field", $text, $order_id, $field );',
				    'translate'   => 'no'
				),
				'PDFCOMPANYDETAILS'        => array(
				    'tag'         => __( '[[PDFCOMPANYDETAILS]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_company_field', $text, $order_id, $field );",
				    'translate'   => 'no'
				),
				'PDFINVOICENUMHEADING'        => array(
				    'tag'         => __( '[[PDFINVOICENUMHEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Invoice No.',
				    'filter'   	  => "apply_filters( 'pdf_template_invoice_number_text', esc_html__( 'Invoice No.', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFINVOICENUM'        => array(
				    'tag'         => __( '[[PDFINVOICENUM]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFORDERENUMHEADING'        => array(
				    'tag'         => __( '[[PDFORDERENUMHEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Order No.',
				    'filter'   	  => "apply_filters( 'pdf_template_order_number_text', esc_html__( 'Order No.', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFORDERENUM'        => array(
				    'tag'         => __( '[[PDFORDERENUM]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICEDATEHEADING'        => array(
				    'tag'         => __( '[[PDFINVOICEDATEHEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Invoice Date',
				    'filter'   	  => "apply_filters( 'pdf_template_invoice_date_text', esc_html__( 'Invoice Date', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFINVOICEDATE'        => array(
				    'tag'         => __( '[[PDFINVOICEDATE]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFORDERDATEHEADING'        => array(
				    'tag'         => __( '[[PDFORDERDATEHEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Order Date',
				    'filter'   	  => "apply_filters( 'pdf_template_order_date_text', esc_html__( 'Order Date', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFORDERDATE'        => array(
				    'tag'         => __( '[[PDFORDERDATE]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_PAYMETHOD_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_PAYMETHOD_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Payment Method',
				    'filter'   	  => "apply_filters( 'pdf_template_payment_method_text', __( 'Payment Method', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFINVOICEPAYMENTMETHOD'        => array(
				    'tag'         => __( '[[PDFINVOICEPAYMENTMETHOD]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_SHIPMETHOD_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_SHIPMETHOD_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Shipping Method',
				    'filter'   	  => "apply_filters( 'pdf_template_shipping_method_text', __( 'Shipping Method', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFSHIPPINGMETHOD'        => array(
				    'tag'         => __( '[[PDFSHIPPINGMETHOD]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_BILLINGDETAILS_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_BILLINGDETAILS_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Billing Details',
				    'filter'   	  => "apply_filters( 'pdf_template_billing_details_text', esc_html__('Billing Details', 'woocommerce-pdf-invoice'), $order );",
				    'translate'   => 'Yes'
				),
				'PDFBILLINGADDRESS'        => array(
				    'tag'         => __( '[[PDFBILLINGADDRESS]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFBILLINGTEL'        => array(
				    'tag'         => __( '[[PDFBILLINGTEL]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFBILLINGEMAIL'        => array(
				    'tag'         => __( '[[PDFBILLINGEMAIL]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFBILLINGVATNUMBER'        => array(
				    'tag'         => __( '[[PDFBILLINGVATNUMBER]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_SHIPPINGDETAILS_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Shipping Details',
				    'filter'   	  => "apply_filters( 'pdf_template_shipping_details_text', esc_html__('Shipping Details', 'woocommerce-pdf-invoice'), $order );",
				    'translate'   => 'Yes'
				),
				'PDFSHIPPINGADDRESS'        => array(
				    'tag'         => __( '[[PDFSHIPPINGADDRESS]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_REGISTEREDNAME_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_REGISTEREDNAME_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Registered Name',
				    'filter'   	  => "apply_filters( 'pdf_template_registered_name_text', esc_html__( 'Registered Name ', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFREGISTEREDNAME'        => array(
				    'tag'         => __( '[[PDFREGISTEREDNAME]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFINVOICE_REGISTEREDOFFICE_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_REGISTEREDOFFICE_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Registered Office',
				    'filter'   	  => "apply_filters( 'pdf_template_registered_office_text', esc_html__( 'Registered Office ', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFREGISTEREDADDRESS'        => array(
				    'tag'         => __( '[[PDFREGISTEREDADDRESS]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => 'apply_filters( "woocommerce_pdf_invoice_company_field", $text, $order_id, $field );',
				    'translate'   => 'no'
				),
				'PDFINVOICE_COMPANYNUMBER_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_COMPANYNUMBER_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Company Number',
				    'filter'   	  => "apply_filters( 'pdf_template_company_number_text', __( 'Company Number ', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFCOMPANYNUMBER'        => array(
				    'tag'         => __( '[[PDFCOMPANYNUMBER]] ', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => 'apply_filters( "woocommerce_pdf_invoice_company_field", $text, $order_id, $field );',
				    'translate'   => 'no'
				),
				'PDFINVOICE_VATNUMBER_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_VATNUMBER_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'VAT Number',
				    'filter'   	  => "apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number ', 'woocommerce-pdf-invoice' ), $order );",
				    'translate'   => 'Yes'
				),
				'PDFTAXNUMBER'        => array(
				    'tag'         => __( '[[PDFTAXNUMBER]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => 'apply_filters( "woocommerce_pdf_invoice_company_field", $text, $order_id, $field );',
				    'translate'   => 'no'
				),
				'PDFINVOICE_ORDERDETAILS_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_ORDERDETAILS_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Order Details',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_order_details_heading', esc_html__('Order Details', 'woocommerce-pdf-invoice'), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_QTY_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_QTY_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Qty',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_qty_heading', esc_html__( 'Qty', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'no'
				),
				'PDFINVOICE_PRODUCT_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_PRODUCT_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Product',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_product_heading', esc_html__( 'Product', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_PRICEEX_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_PRICEEX_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Price Ex.',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_priceex_heading', esc_html__( 'Price Ex.', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_TOTALEX_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_TOTALEX_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Total Ex.',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_totalex_heading', esc_html__( 'Total Ex.', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_TAX_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_TAX_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Tax',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_tax_heading', esc_html__( 'Tax', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_PRICEINC_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_PRICEINC_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Price Inc',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_priceinc_heading', esc_html__( 'Price Inc', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'PDFINVOICE_TOTALINC_HEADING'        => array(
				    'tag'         => __( '[[PDFINVOICE_TOTALINC_HEADING]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => 'Total Inc',
				    'filter'   	  => "apply_filters( 'woocommerce_pdf_invoice_totalinc_heading', esc_html__( 'Total Inc', 'woocommerce-pdf-invoice' ), $order )",
				    'translate'   => 'Yes'
				),
				'ORDERINFO'        => array(
				    'tag'         => __( '[[ORDERINFO]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFBARCODES'        => array(
				    'tag'         => __( '[[PDFBARCODES]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFORDERNOTES'        => array(
				    'tag'         => __( '[[PDFORDERNOTES]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				),
				'PDFORDERTOTALS'        => array(
				    'tag'         => __( '[[PDFORDERTOTALS]]', 'woocommerce-pdf-invoice' ),
				    'description' => __( '', 'woocommerce-pdf-invoice' ),
				    'default'     => '',
				    'filter'   	  => '',
				    'translate'   => 'no'
				)
			);		
?>
			<div class="invoice_meta_group">
				<table width="100%" style=" margin:0; border:0; padding:0;">
					<tr style="background:#333;">
						<th width="20%" style="font-size:12px; color:#FFF; padding: 10px 0;"><?php _e('Tag', 'woocommerce-pdf-invoice') ?></th>
						<th width="30%" style="font-size:12px; color:#FFF; padding: 10px 0;"><?php _e('Description', 'woocommerce-pdf-invoice') ?></th>
						<th width="20%" style="font-size:12px; color:#FFF; padding: 10px 0;"><?php _e('Default', 'woocommerce-pdf-invoice') ?></th>
						<th width="20%" style="font-size:12px; color:#FFF; padding: 10px 0;"><?php _e('Filter', 'woocommerce-pdf-invoice') ?></th>
						<th width="10%" style="font-size:12px; color:#FFF; padding: 10px 0;"><?php _e('Translatable?', 'woocommerce-pdf-invoice') ?></th>
					</tr>
<?php 
					foreach( $default_template_tags AS $default_template_tag ) { 
						$i = 0;
						$i++;
						$bk = '#FFF';
						if ($i % 2 == 0) {
  							$bk = '#DADADA';
						}
?>
						<tr style="background:<?php echo $bk;?>;">
							<td style="padding: 5px 0;"><?php echo $default_template_tag['tag']; ?></td>
							<td style="padding: 5px 0;"><?php echo $default_template_tag['description']; ?></td>
							<td style="padding: 5px 0;"><?php echo $default_template_tag['default']; ?></td>
							<td style="padding: 5px 0;"><?php echo $default_template_tag['filter']; ?></td>
							<td style="padding: 5px 0;"><?php echo $default_template_tag['translate']; ?></td>
						</tr>
					<?php } ?>
				</table>
				<div class="clear"></div>
			</div><?php
			
		}

		public static function codex_add_help_text( $contextual_help, $screen_id, $screen ) {
		  // $contextual_help .= var_dump( $screen ); // use this to help determine $screen->id
		  if ( 'pdfi_templates' == $screen->id ) {
		    $contextual_help =
		      '<p>' . __('Things to remember when adding or editing a book:', 'pdf_invoice_templates') . '</p>' .
		      '<ul>' .
		      '<li>' . __('Specify the correct genre such as Mystery, or Historic.', 'pdf_invoice_templates') . '</li>' .
		      '<li>' . __('Specify the correct writer of the book.  Remember that the Author module refers to you, the author of this book review.', 'pdf_invoice_templates') . '</li>' .
		      '</ul>' .
		      '<p>' . __('If you want to schedule the book review to be published in the future:', 'pdf_invoice_templates') . '</p>' .
		      '<ul>' .
		      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.', 'pdf_invoice_templates') . '</li>' .
		      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.', 'pdf_invoice_templates') . '</li>' .
		      '</ul>' .
		      '<p><strong>' . __('For more information:', 'pdf_invoice_templates') . '</strong></p>' .
		      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>', 'pdf_invoice_templates') . '</p>' .
		      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'pdf_invoice_templates') . '</p>' ;
		  } elseif ( 'edit-pdfi_templates' == $screen->id ) {
		    $contextual_help =
		      '<p>' . __('This is the help screen displaying the table of books blah blah blah.', 'pdf_invoice_templates') . '</p>' ;
		  }
		  return $contextual_help;
		}
		

	}

	// $GLOBALS['WC_pdf_template_editor'] = new WC_pdf_template_editor();