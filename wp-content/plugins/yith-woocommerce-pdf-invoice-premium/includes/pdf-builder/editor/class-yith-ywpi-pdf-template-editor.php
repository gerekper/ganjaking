<?php
/**
 * Class to manage the PDF template Editor
 *
 * @class   YITH_YWPI_PDF_Template_Editor
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWPI_PDF_Template_Editor' ) ) {
	/**
	 * Class YITH_YWPI_PDF_Template_Editor
	 */
	class YITH_YWPI_PDF_Template_Editor {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWPI_PDF_Template_Editor
		 */
		protected static $instance;

		/**
		 * Preview Products
		 *
		 * @var array
		 */
		public $preview_products = array();

		/**
		 * List of internal method to call Gutenberg Blocks related the documents
		 *
		 * @var array
		 */
		protected $render_functions = array(
			'core/column'               => 'render_columns_block',
			'core/columns'              => 'render_columns_block',
			'yith/ywpi-products-table'  => 'render_product_table',
			'yith/ywpi-products-totals' => 'render_product_totals',
			'yith/ywpi-date'            => 'render_date',
			'yith/ywpi-order-number'    => 'render_order_number',
			'yith/ywpi-order-amount'    => 'render_order_amount',
			'yith/ywpi-document-number' => 'render_document_number',
			'yith/ywpi-customer-info'   => 'render_customer_info',
			'yith/ywpi-shipping-info'   => 'render_shipping_info',
		);

		/**
		 * List of internal method to render graphic Gutenberg Blocks
		 *
		 * @var array
		 */
		protected $render_graphic_blocks = array(
			'core/image'     => 'render_image_block',
			'core/separator' => 'render_separator_block',
		);

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_YWPI_PDF_Template_Editor
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
		}

		/**
		 * Render template
		 *
		 * @param   YITH_YWPI_PDF_Template $pdf_template      PDF template object.
		 * @param   string                 $content           Content of template.
		 * @param   YITH_Document|int      $document          The document to show or the path of the file to be shown.
		 * @param   int                    $order_id          Order id.
		 * @param   array                  $preview_products  Preview products for the preview.
		 *
		 * @return string
		 */
		public function render_template( $pdf_template, $content, $document, $order_id, $preview_products = array() ) {
			$this->preview_products = $preview_products;

			$blocks  = parse_blocks( $content );
			$output  = $this->get_main_style( $pdf_template );
			$output .= $this->render_blocks( $blocks, $document, $order_id );

			return $output;
		}

		/**
		 * Return the css rules included inside the pdf template assets
		 *
		 * @param   YITH_YWPI_PDF_Template $pdf_template  PDF template object.
		 *
		 * @return string
		 */
		public function get_main_style( $pdf_template ) {
			$style                  = '<style>';
			$template               = $pdf_template->get_template_parent();
			$custom_background      = $pdf_template->get_custom_background();
			$large_margin_templates = array(
				'leaf',
				'stripes',
				'sexy',
				'elegant-blue',
			);

			$lateral_margin = apply_filters(
				'yith_ywpi_template_pdf_lateral_margin',
				in_array( $template, $large_margin_templates, true ) ? 5 : 3,
				$template
			);

			$footer_margin_bottom = apply_filters(
				'yith_ywpi_template_pdf_footer_margin_bottom',
				in_array( $template, $large_margin_templates, true ) ? 13 : 6,
				$template
			);

			$bg         = 'default' !== $template ? YITH_YWPI_ASSETS_URL . '/images/pdf-builder/bg/' . $template . '.svg' : '';
			$background = isset( $custom_background['url'] ) ? $custom_background['url'] : $bg;
			$background = apply_filters( 'yith_ywpi_template_pdf_background_image', $background, $template );

			$style .= '
			@page{
				background: url(' . $background . ') no-repeat 0 0;
			  	background-image-resize: 6;
			  	margin:10mm ' . $lateral_margin . 'mm;
			  	margin-bottom: 14mm;
			  	margin-footer: ' . $footer_margin_bottom . 'mm;
			}';

			ob_start();
			include YITH_YWPI_ASSETS_DIR . '/css/pdf-builder/ywpi-template-pdf.css';
			$style .= apply_filters( 'yith_ywpi_template_style', ob_get_contents(), $template, $this );
			ob_end_clean();
			$style .= '</style>';

			return $style;
		}

		/**
		 * Render the blocks inside the template
		 *
		 * @param   array             $blocks    List of blocks.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_blocks( $blocks, $document, $order_id ) {
			$output = '';

			foreach ( $blocks as $block ) {
				if ( isset( $this->render_functions[ $block['blockName'] ] ) && method_exists( $this, $this->render_functions[ $block['blockName'] ] ) ) {
					$callback = $this->render_functions[ $block['blockName'] ];
					$output  .= $this->$callback( $block, $document, $order_id );
				} elseif ( isset( $this->render_graphic_blocks[ $block['blockName'] ] ) && method_exists( $this, $this->render_graphic_blocks[ $block['blockName'] ] ) ) {
					$callback = $this->render_graphic_blocks[ $block['blockName'] ];
					$output  .= $this->$callback( $block );
				} else {
					$output .= render_block( $block );
				}
			}

			return $output;
		}

		/**
		 * Render the columns and column blocks
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_columns_block( array $block, $document, $order_id ) {
			$inner_blocks = $block['innerBlocks'];
			$is_single    = 'core/column' === $block['blockName'];
			$first_tag    = $block['innerContent'][0];

			if ( $inner_blocks && ! $is_single ) {
				$inner_blocks_class = sprintf( 'columns-%d', count( $inner_blocks ) );
				$output             = str_replace(
					'wp-block-columns',
					'wp-block-columns ' . $inner_blocks_class,
					$first_tag
				);
				$output            .= '<sethtmlpagefooter name="footer" value="on" page="ALL" />';
			} elseif ( strpos( $first_tag, 'style="' ) !== false ) {
				$width  = isset( $block['attrs']['width'] ) ? 'style="width:' . $block['attrs']['width'] . ';' : '';
				$output = ( '' !== $width ) ? str_replace( 'style="', $width, $first_tag ) : $first_tag;
			} else {
				$width  = isset( $block['attrs']['width'] ) ? 'style="width:' . $block['attrs']['width'] . '"' : '';
				$output = ( '' !== $width ) ? str_replace( '>', $width . '>', $first_tag ) : $first_tag;
			}

			$output .= $this->render_blocks( $inner_blocks, $document, $order_id );
			$output .= end( $block['innerContent'] );

			return $output;
		}

		/**
		 * Render separator block
		 *
		 * @param   array $block  Block.
		 *
		 * @return string
		 */
		public function render_separator_block( $block ) {
			$output  = render_block( $block );
			$output  = str_replace( 'background-color', 'border-color', $output );
			$output  = str_replace( 'hr', 'div', $output );
			$output  = str_replace( '/>', '>', $output );
			$output .= '</div>';

			return $output;
		}

		/**
		 * Render the image block
		 *
		 * @param   array $block  Block.
		 *
		 * @return string
		 */
		public function render_image_block( $block ) {
			$rendered_block = render_block( $block );

			if ( apply_filters( 'yith_ywpi_get_images_via_path', false ) ) {
				$path           = get_attached_file( $block['attrs']['id'] );
				$rendered_block = preg_replace( '/src=[\'\"](.*?)[\'\"]/', 'src="' . $path . '"', $rendered_block );
			}

			return $rendered_block;
		}

		/**
		 * Render the block with the customer info
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_customer_info( $block, $document, $order_id ) {
			$rendered_block = render_block( $block );
			$order          = $this->get_order( $document, $order_id, $block );
			$customer       = $this->get_customer( $order );

			foreach ( $this->get_customer_info_placeholders() as $placeholder ) {
				$order_meta     = is_object( $order ) ? $order->get_meta( '_' . $placeholder ) : $placeholder;
				$value          = $customer[ $placeholder ] ?? $order_meta;
				$rendered_block = str_replace( '{{' . $placeholder . '}}', $value, $rendered_block );
				$rendered_block = str_replace( '<br><br>', '<br>', $rendered_block );
			}

			return $rendered_block;
		}

		/**
		 * Render the block with the shipping info
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_shipping_info( $block, $document, $order_id ) {
			$rendered_block = render_block( $block );
			$order          = $this->get_order( $document, $order_id, $block );
			$shipping       = $this->get_shipping( $order );

			foreach ( $this->get_shipping_info_placeholders() as $placeholder ) {
				$value          = isset( $shipping[ $placeholder ] ) ? $shipping[ $placeholder ] : '';
				$rendered_block = str_replace( '{{' . $placeholder . '}}', $value, $rendered_block );
				$rendered_block = str_replace( '<br><br>', '<br>', $rendered_block );
			}

			return $rendered_block;
		}

		/**
		 * Return the placeholders for customer info
		 *
		 * @return array
		 */
		public function get_customer_info_placeholders() {
			$fields_billing = WC()->countries->get_address_fields();

			return apply_filters( 'yith_ywpi_template_editor_customer_info_placeholders', array_keys( $fields_billing ) );
		}

		/**
		 * Return the placeholders for shipping info
		 *
		 * @return array
		 */
		public function get_shipping_info_placeholders() {
			$fields_shipping = WC()->countries->get_address_fields( '', 'shipping_' );

			return apply_filters( 'yith_ywpi_template_editor_shipping_info_placeholders', array_keys( $fields_shipping ) );
		}

		/**
		 * Return the product table rendered
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_product_table( $block, $document, $order_id ) {
			$rendered_block     = render_block( $block );
			$output             = '';
			$default_attributes = array(
				'invoiceType'        => 1,
				'thumbnails'         => 1,
				'productName'        => 1,
				'productSku'         => 1,
				'productDescription' => 0,
				'productWeight'      => 0,
				'productDimensions'  => 0,
				'quantity'           => 1,
				'regularPrice'       => 0,
				'salePrice'          => 1,
				'price'              => 1,
				'discountPercentage' => 0,
				'tax'                => 1,
				'percentageTax'      => 0,
				'productSubtotal'    => 1,
				'productTotal'       => 1,
				'positiveAmount'     => 1,
				'refundDescription'  => 1,
				'shippingItems'      => 1,
				'feeItems'           => 1,
			);

			$attr     = wp_parse_args( $block['attrs'], $default_attributes );
			$tr_class = '';

			if ( ! $document ) {
				$products = $this->get_preview_products();
				$size     = count( $products );
				$i        = 0;

				foreach ( $products as $product ) {
					$tr_class = ++$i === $size ? 'class="last"' : '';
					$output  .= "<tr {$tr_class}>";

					$product_content = $this->get_product_content( $product, $attr );
					$output         .= $this->get_product_content_row( $product_content, $attr );

					$output .= '</tr>';
				}

				if ( isset( $attr['feeItems'] ) && $attr['feeItems'] ) {
					$fees = $this->get_fee_preview_content();

					foreach ( $fees as $fee ) {
						$output         .= "<tr {$tr_class}>";
						$product_content = $this->get_product_content( $fee, $attr, 'fee' );
						$output         .= $this->get_product_content_row( $product_content, $attr );
						$output         .= '</tr>';
					}
				}

				if ( isset( $attr['shippingItems'] ) && $attr['shippingItems'] ) {
					$shippings = $this->get_shipping_preview_content();

					foreach ( $shippings as $shipping ) {
						$output         .= "<tr {$tr_class}>";
						$product_content = $this->get_product_content( $shipping, $attr, 'shipping' );
						$output         .= $this->get_product_content_row( $product_content, $attr );
						$output         .= '</tr>';
					}
				}
			} else {
				ob_start();
				wc_get_template(
					'yith-pdf-invoice/pdf-builder/product-table.php',
					array(
						'resource' => $document,
						'attr'     => $attr,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				$output .= ob_get_contents();

				ob_end_clean();
			}

			$rendered_block = str_replace( '##table_content', $output, $rendered_block );

			return apply_filters( 'ywpi_pdf_builder_editor_render_product_table', $rendered_block, $block, $document, $order_id, $this, $attr );
		}

		/**
		 * Render product totals
		 *
		 * @param   array             $block     Gutenberg block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_product_totals( $block, $document, $order_id ) {
			$rendered_block     = render_block( $block );
			$default_attributes = array(
				'invoiceType'               => 1,
				'positiveAmount'            => 1,
				'brokenDownTaxes'           => 1,
				'showTax'                   => 1,
				'subtotalInclusiveDiscount' => 1,
				'showDiscount'              => 1,
				'showSubtotal'              => 1,
			);

			$attr   = wp_parse_args( $block['attrs'], $default_attributes );
			$output = '';

			/**
			 * APPLY_FILTERS: yith_ywpi_pdf_builder_subtotal_label
			 *
			 * This filter allow to replace the label subtotal inside a document
			 *
			 * @param   YITH_Document  $document  Document.
			 *
			 * @return string
			 */
			$label_subtotal = apply_filters( 'yith_ywpi_pdf_builder_subtotal_label', _x( 'Subtotal', 'subtotal label on pdf document', 'yith-woocommerce-pdf-invoice' ), $document );

			if ( ! $order_id ) {
				$total_items = 0;
				$total_tax   = 0;
				$products    = $this->get_preview_products();
				$fees        = $this->get_fee_preview_content();
				$shipping    = $this->get_shipping_preview_content();

				foreach ( array_merge( $products, $shipping, $fees ) as $product ) {
					$total_items += $product['line_total'];
					$total_tax   += $product['line_total_tax'];
				}

				if ( $attr['invoiceType'] ) {
					$show_discount          = ! isset( $attr['showDiscount'] ) || ( isset( $attr['showDiscount'] ) && $attr['showDiscount'] );
					$subtotal_with_discount = $show_discount && ( ! isset( $attr['subtotalInclusiveDiscount'] ) || ( isset( $attr['subtotalInclusiveDiscount'] ) && $attr['subtotalInclusiveDiscount'] ) );

					if ( $show_discount ) {
						$discount_sbt_label = $subtotal_with_discount ? _x( 'Discount incl.', 'discount included inside subtotal', 'yith-woocommerce-pdf-invoice' ) : _x( 'Discount excl.', 'discount exluded inside subtotal', 'yith-woocommerce-pdf-invoice' );
						$label_subtotal     = sprintf( '%s <small>(%s)</small>', $label_subtotal, $discount_sbt_label );
					}

					$split_tax = ! isset( $attr['brokenDownTaxes'] ) || ( isset( $attr['brokenDownTaxes'] ) && $attr['brokenDownTaxes'] );
					$output   .= '<tr class="subtotal-row"><td class="subtotal-label" >' . $label_subtotal . '</td><td class="subtotal number">' . wc_price( $total_items ) . '</td></tr>';

					if ( ! isset( $attr['showDiscount'] ) || ( isset( $attr['showDiscount'] ) && $attr['showDiscount'] ) ) {
						$output .= '<tr class="subtotal-row"><td class="subtotal-label" >' . _x( 'Discount', 'discount label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td class="subtotal number">' . wc_price( 0 ) . '</td></tr>';
					}

					if ( $split_tax ) {
						$output .= '<tr class="subtotal-row"><td class="subtotal-label">' . _x( 'Taxes', 'taxes label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td class="subtotal number">' . wc_price( $total_tax ) . '</td></tr>';
					}

					$output .= '<tr class="subtotal-row"><td class="subtotal-label">' . _x( 'Total Taxes', 'total label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td class="subtotal number">' . wc_price( $total_tax ) . '</td></tr>';
					$output .= '<tr class="total-row"><td class="total-label">' . _x( 'Total', 'total label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td style="text-align: right">' . wc_price( $total_items + $total_tax ) . '</td></tr>';
				} else {
					$sign = $attr['positiveAmount'] ? 1 : - 1;

					if ( $attr['showSubtotal'] ) {
						$output .= '<tr class="subtotal-row"><td class="subtotal-label" >' . $label_subtotal . '</td><td class="subtotal number">' . wc_price( $sign * $total_items ) . '</td></tr>';
					}

					if ( $attr['showTax'] ) {
						$output .= '<tr class="subtotal-row"><td class="subtotal-label">' . _x( 'Taxes', 'taxes label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td class="subtotal number">' . wc_price( $sign * $total_tax ) . '</td></tr>';
					}

					$output .= '<tr class="total-row"><td class="total-label">' . _x( 'Refunded amount', 'total label inside product totals', 'yith-woocommerce-pdf-invoice' ) . '</td><td style="text-align: right">' . wc_price( $sign * ( $total_items + $total_tax ) ) . '</td></tr>';
				}
			} else {
				ob_start();
				wc_get_template(
					'yith-pdf-invoice/pdf-builder/product-totals.php',
					array(
						'resource'       => $document,
						'attr'           => $attr,
						'label_subtotal' => $label_subtotal,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);
				$output .= ob_get_contents();

				ob_end_clean();
			}

			$rendered_block = str_replace( '##table_totals', $output, $rendered_block );

			return apply_filters( 'yith_ywpi_pdf_builder_editor_render_product_totals', $rendered_block, $block, $document, $order_id, $this );
		}

		/**
		 * Render the quote number
		 *
		 * @param   array             $block    Block.
		 * @param   YITH_Document|int $document The document to show or the path of the file to be shown.
		 *
		 * @return string
		 */
		public function render_document_number( $block, $document ) {
			$output = render_block( $block );

			if ( ! $document ) {
				$resource_number = 586;
			} else {
				$resource_number = $document->get_formatted_document_number();
			}

			$output = str_replace( '{{document_number}}', $resource_number, $output );

			return $output;
		}

		/**
		 * Render the order number
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_order_number( $block, $document, $order_id ) {
			$output = render_block( $block );

			if ( ! $order_id ) {
				$order_number = 601;
			} else {
				$order        = $this->get_order( $document, $order_id, $block );
				$order_number = apply_filters( 'yith_ywpi_order_number', $order->get_order_number(), $order );
			}

			$invoice = ywpi_get_invoice( $order_number, 'invoice' );

			if ( ! is_null( $invoice ) ) {
				$invoice_number = $invoice->get_formatted_document_number();
			}

			$output = str_replace( '{{order_number}}', $order_number, $output );
			$output = str_replace( '{{invoice_number}}', $invoice_number, $output );

			return $output;
		}

		/**
		 * Render the document date
		 *
		 * @param   array             $block     Block.
		 * @param   YITH_Document|int $document  The document to show or the path of the file to be shown.
		 * @param   int               $order_id  Order id.
		 *
		 * @return string
		 */
		public function render_date( $block, $document, $order_id ) {
			$output = render_block( $block );

			if ( ! $document ) {
				$output = str_replace( '{{current_date}}', date_i18n( wc_date_format(), time() ), $output );
				$output = str_replace(
					'{{order_created}}',
					date_i18n( wc_date_format(), time() - DAY_IN_SECONDS ),
					$output
				);
				$output = str_replace(
					'{{order_completed}}',
					date_i18n( wc_date_format(), time() + DAY_IN_SECONDS ),
					$output
				);
				$output = str_replace(
					'{{invoice_created}}',
					date_i18n( wc_date_format(), time() + DAY_IN_SECONDS ),
					$output
				);
			} else {
				$attr      = $block['attrs'];
				$date_type = isset( $attr['dateType'] ) ? $attr['dateType'] : 'current';
				$order     = $this->get_order( $document, $order_id, $block );

				switch ( $date_type ) {
					case 'order_created':
						$order_date = wc_format_datetime( $order->get_date_created() );
						$output     = str_replace( '{{order_created}}', $order_date, $output );
						break;

					case 'order_completed':
						$order_date = wc_format_datetime( $order->get_date_completed() );
						$output     = str_replace( '{{order_completed}}', $order_date, $output );
						break;

					case 'invoice_created':
						try {
							$document_date = new WC_DateTime( $document->date );
						} catch ( Exception $e ) {
							$document_date = '';
						}

						$document_date = wc_format_datetime( $document_date );

						$output = str_replace( '{{invoice_created}}', $document_date, $output );
						break;

					default:
						$output = str_replace( '{{current_date}}', date_i18n( wc_date_format(), time() ), $output );
				}
			}
			return $output;
		}

		/**
		 * Render the order amount
		 *
		 * @param   array             $block    Block.
		 * @param   YITH_Document|int $document The document to show or the path of the file to be shown.
		 *
		 * @return string
		 */
		public function render_order_amount( $block, $document ) {
			$output = render_block( $block );
			$total  = 0;

			if ( ! $document ) {
				$products = $this->get_preview_products();

				foreach ( $products as $product ) {
					$total += $product['line_total'] + $product['line_total_tax'];
				}

				$output = str_replace( '{{refunded_amount}}', wc_price( $total ), $output );
				$output = str_replace( '{{order_amount}}', wc_price( $total ), $output );
			} else {
				$invoice_details = new YITH_Invoice_Details( $document );
				$order_amount    = $invoice_details->get_order_currency_new( $document->order->get_total() );

				if ( $document instanceof YITH_Credit_Note ) {
					// order amount of parent.
					$main_order_id = get_post_field( 'post_parent', $document->order->get_id() );
					$main_order    = wc_get_order( $main_order_id );
					$output        = str_replace( '{{order_amount}}', wc_price( $main_order->get_total() ), $output );

					// amount refunded.
					$negative_value  = get_option( 'ywpi_credit_note_positive_values_builder', 'no' ) === 'yes' ? -1 : 1;
					$refund_total    = $document->order->get_total() * $negative_value;
					$refunded_amount = $invoice_details->get_order_currency_new( $refund_total );
					$output          = str_replace( '{{refunded_amount}}', $refunded_amount, $output );
				} else {
					$output = str_replace( '{{order_amount}}', $order_amount, $output );
				}
			}

			return $output;
		}

		/**
		 * Return an array with products
		 *
		 * @return array
		 */
		public function get_preview_products() {
			if ( ! empty( $this->preview_products ) ) {
				return $this->preview_products;
			}

			$products = get_posts(
				array(
					'posts_per_page' => 2,
					'orderby'        => 'rand',
					'post_type'      => 'product',
					'status'         => 'published',
					'fields'         => 'ids',
				)
			);

			$preview_products = array();

			if ( $products ) {
				foreach ( $products as $product_id ) {
					$product       = wc_get_product( $product_id );
					$product_image = $product->get_image_id() ? $product->get_image_id() : get_option(
						'woocommerce_placeholder_image',
						0
					);

					$price_with_tax    = (float) wc_get_price_including_tax( $product, array( 'qty' => 1 ) );
					$price_without_tax = (float) wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );

					$tax_rate = 0;
					$tax      = new WC_Tax();
					$taxes    = $tax->get_rates( $product->get_tax_class() );

					if ( $taxes ) {
						$rates    = array_shift( $taxes );
						$tax_rate = round( array_shift( $rates ) );
					}

					$preview_products[] = array(
						'id'                  => $product->get_id(),
						'quantity'            => 1,
						'name'                => $product->get_name(),
						'sku'                 => $product->get_sku(),
						'description'         => wp_strip_all_tags( $product->get_short_description() ),
						'weight'              => $product->get_weight(),
						'weight_unit'         => get_option( 'woocommerce_weight_unit', 'kg' ),
						'dimensions'          => wc_format_dimensions( $product->get_dimensions( false ) ),
						'permalink'           => $product->get_permalink(),
						'thumbnail'           => wp_get_attachment_image_url( $product_image ),
						'thumbnail_path'      => wp_get_original_image_path( $product_image ),
						'regular_price'       => $product->get_regular_price(),
						'sale_price'          => $product->get_sale_price(),
						'price'               => $product->get_price(),
						'percentage_tax'      => $tax_rate,
						'discount_percentage' => 0,
						'line_total'          => $price_without_tax,
						'line_total_tax'      => $price_with_tax - $price_without_tax,
					);
				}
			} else {
				$preview_products = array(
					array(
						'id'                  => 1,
						'quantity'            => 1,
						'name'                => 'Beanie',
						'sku'                 => 'woo-beanie',
						'description'         => 'Product description',
						'weight'              => 1,
						'weight_unit'         => get_option( 'woocommerce_weight_unit', 'kg' ),
						'dimensions'          => '6x10x2 cm',
						'permalink'           => '#',
						'thumbnail'           => YITH_YWPI_ASSETS_URL . 'assets/images/pdf-builder/beanie.jpg',
						'thumbnail_path'      => YITH_YWPI_ASSETS_DIR . 'assets/images/pdf-builder/beanie.jpg',
						'regular_price'       => 24,
						'sale_price'          => 20,
						'price'               => 20,
						'percentage_tax'      => 20,
						'discount_percentage' => 0,
						'line_total'          => 20,
						'line_total_tax'      => 4,
					),
					array(
						'id'                  => 2,
						'quantity'            => 1,
						'name'                => 'Cap',
						'sku'                 => 'woo-cap',
						'description'         => 'Product description',
						'weight'              => 1,
						'weight_unit'         => get_option( 'woocommerce_weight_unit', 'kg' ),
						'dimensions'          => '6x10x2 cm',
						'permalink'           => '#',
						'thumbnail'           => YITH_YWPI_ASSETS_URL . 'assets/images/pdf-builder/cap.jpg',
						'thumbnail_path'      => YITH_YWPI_ASSETS_DIR . 'assets/images/pdf-builder/cap.jpg',
						'regular_price'       => 30,
						'sale_price'          => 0,
						'price'               => 30,
						'percentage_tax'      => 20,
						'discount_percentage' => 0,
						'line_total'          => 30,
						'line_total_tax'      => 6,
					),
				);
			}

			return $preview_products;
		}

		/**
		 * Return the customer info
		 *
		 * @param   bool|WC_Order $order  Order.
		 *
		 * @return array
		 */
		protected function get_customer( $order ) {
			if ( ! $order ) {
				$customer = array(
					'billing_first_name' => 'John',
					'billing_last_name'  => 'Doe',
					'billing_address_1'  => '705 West Trout Ave',
					'billing_address_2'  => '',
					'billing_city'       => 'New York',
					'billing_state'      => 'NY',
					'billing_country'    => 'USA',
					'billing_postcode'   => '10002',
					'billing_email'      => 'email@email.com',
					'billing_phone'      => '555-5555',
					'billing_company'    => 'John Doe Company',
				);
			} else {
				$country_code         = $order->get_billing_country();
				$wc_countries         = WC()->countries;
				$billing_country_name = $wc_countries->countries[ $country_code ];

				$state_code = $order->get_billing_state();
				$states     = $wc_countries->get_states();
				$state_name = $states[ $country_code ][ $state_code ];

				$customer = array(
					'billing_first_name' => $order->get_billing_first_name(),
					'billing_last_name'  => $order->get_billing_last_name(),
					'billing_address_1'  => $order->get_billing_address_1(),
					'billing_address_2'  => $order->get_billing_address_2(),
					'billing_city'       => $order->get_billing_city(),
					'billing_state'      => $state_name,
					'billing_country'    => $billing_country_name,
					'billing_postcode'   => $order->get_billing_postcode(),
					'billing_email'      => $order->get_billing_email(),
					'billing_phone'      => $order->get_billing_phone(),
					'billing_company'    => $order->get_billing_company(),
				);
			}

			return apply_filters( 'yith_ywpi_template_editor_customer_info', $customer, $order, $this );
		}

		/**
		 * Return the customer shipping information
		 *
		 * @param   bool|WC_Order $order  Order.
		 *
		 * @return array
		 */
		protected function get_shipping( $order ) {
			if ( ! $order ) {
				$shipping = array(
					'shipping_first_name' => 'John',
					'shipping_last_name'  => 'Doe',
					'shipping_company'    => 'John Doe Company',
					'shipping_country'    => 'USA',
					'shipping_address_1'  => '705 West Trout Ave',
					'shipping_address_2'  => '',
					'shipping_city'       => 'New York',
					'shipping_state'      => 'NY',
					'shipping_postcode'   => '10002',
				);
			} else {
				$country_code          = $order->get_shipping_country();
				$wc_countries          = WC()->countries;
				$shipping_country_name = $wc_countries->countries[ $country_code ];

				$state_code = $order->get_shipping_state();
				$states     = $wc_countries->get_states();
				$state_name = $states[ $country_code ][ $state_code ];

				$shipping = array(
					'shipping_first_name' => $order->get_shipping_first_name(),
					'shipping_last_name'  => $order->get_shipping_last_name(),
					'shipping_address_1'  => $order->get_shipping_address_1(),
					'shipping_address_2'  => $order->get_shipping_address_2(),
					'shipping_city'       => $order->get_shipping_city(),
					'shipping_state'      => $state_name,
					'shipping_country'    => $shipping_country_name,
					'shipping_postcode'   => $order->get_shipping_postcode(),
					'shipping_company'    => $order->get_shipping_company(),
				);
			}

			return apply_filters( 'yith_ywpi_template_editor_shipping_info', $shipping, $order, $this );
		}

		/**
		 * Return the order based on the document type
		 *
		 * @param   YITH_Document|int $document The document to show or the path of the file to be shown.
		 * @param   int               $order_id Order id.
		 * @param   array             $block    Block data.
		 *
		 * @return bool|WC_Order|WC_Order_Refund
		 */
		private function get_order( $document, $order_id, $block ) {
			if ( $document instanceof YITH_Credit_Note && 'yith/ywpi-date' !== $block['blockName'] ) {
				$order_id = get_post_field( 'post_parent', $document->order->get_id() );
			}

			$order = wc_get_order( $order_id );

			return $order;
		}

		/**
		 * Return the product thumbnail render
		 *
		 * @param array|WC_Product $product Current product.
		 * @param array            $attr    Table attribute.
		 * @param string           $context Context.
		 *
		 * @return array
		 */
		public function get_product_content( $product, $attr, $context = 'product' ) {
			if ( is_array( $product ) ) {
				$product_content = array(
					'thumbnail'           => 'product' === $context ? $this->get_product_thumbnail( $product ) : '',
					'name'                => $this->get_product_name( $product, $attr ),
					'refund-description'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing',
					'quantity'            => $product['quantity'],
					'regular-price'       => wc_price( $product['regular_price'] ),
					'sale-price'          => $product['sale_price'] > 0 ? wc_price( $product['sale_price'] ) : '-',
					'price'               => $product['sale_price'] > 0 ? sprintf( '<span class="old-price">%s</span><br>%s', wc_price( $product['regular_price'] ), wc_price( $product['price'] ) ) : wc_price( $product['price'] ),
					'discount-percentage' => '0%',
					'tax'                 => wc_price( $product['line_total_tax'] ),
					'percentage-tax'      => $product['percentage_tax'],
					'line-total'          => wc_price( $product['line_total'] ),
					'product-total'       => wc_price( $product['line_total'] + $product['line_total_tax'] ),
				);
			}

			return $product_content;
		}

		/**
		 * Return the product thumbnail render
		 *
		 * @param array|WC_Product $product Current product.
		 *
		 * @return string
		 */
		public function get_product_thumbnail( $product ) {
			if ( is_array( $product ) ) {
				if ( empty( $product['thumbnail_path'] ) ) {
					$placeholder_image = get_option( 'woocommerce_placeholder_image', 0 );
					$thumbnail         = wp_get_original_image_path( $placeholder_image );
				} else {
					$thumbnail = $product['thumbnail_path'];
				}
			}

			return sprintf( '<img src="%s" class="thumbnail-img"/>', $thumbnail );
		}

		/**
		 * Return the product thumbnail render
		 *
		 * @param array|WC_Product $product Current product.
		 * @param array            $attr Table attribute.
		 *
		 * @return string
		 */
		public function get_product_name( $product, $attr ) {
			$product_name = '';

			if ( is_array( $product ) ) {
				$product_name = $product['name'];

				if ( ! empty( $product['sku'] ) && ( ! isset( $attr['productSku'] ) || isset( $attr['productSku'] ) && $attr['productSku'] ) ) {
					$product_name = $product['name'] . ' <br/><small>' . apply_filters(
						'yith_ywpi_sku_label',
						__( ' SKU:', 'yith-woocommerce-pdf-invoice' )
					) . $product['sku'] . '</small>';
				}

				if ( ! empty( $product['description'] ) && isset( $attr['productDescription'] ) && $attr['productDescription'] ) {
					$product_name .= ' <br/><small>' . $product['description'] . '</small>';
				}

				if ( $attr['invoiceType'] ) {
					if ( ! empty( $product['weight'] ) && isset( $attr['productWeight'] ) && $attr['productWeight'] ) {
						$product_name .= ' <br/><small>' . __( ' Weight:', 'yith-woocommerce-pdf-invoice' ) . ' ' . $product['weight'] . ' ' . $product['weight_unit'] . '</small>';
					}

					if ( ! empty( $product['dimensions'] ) && 'N/A' !== $product['dimensions'] && isset( $attr['productDimensions'] ) && $attr['productDimensions'] ) {
						$product_name .= ' <br/><small>' . __( ' Dimensions:', 'yith-woocommerce-pdf-invoice' ) . ' ' . $product['dimensions'] . '</small>';
					}
				}
			}

			return $product_name;
		}

		/**
		 * Return the row content of the table product
		 *
		 * @param array $product_content Product content.
		 * @param array $attr Attribute content.
		 *
		 * @return string
		 */
		public function get_product_content_row( $product_content, $attr ) {
			$output = '';

			if ( isset( $attr['thumbnails'] ) && $attr['thumbnails'] ) {
				$output .= '<td class="thumbnail">' . $product_content['thumbnail'] . '</td>';
			}

			if ( isset( $attr['productName'] ) && $attr['productName'] ) {
				$output .= sprintf( '<td class="product-name">%s</td>', $product_content['name'] );
			}

			if ( ! $attr['invoiceType'] && $attr['refundDescription'] ) {
				$output .= sprintf( '<td class="refund-description">%s</td>', $product_content['refund-description'] );
			}

			if ( $attr['invoiceType'] && $attr['quantity'] ) {
				$output .= sprintf( '<td class="quantity number">%s</td>', $product_content['quantity'] );
			}

			if ( $attr['invoiceType'] && $attr['regularPrice'] ) {
				$output .= sprintf( '<td class="product-price number">%s</td>', $product_content['regular-price'] );
			}

			if ( $attr['invoiceType'] && $attr['salePrice'] ) {
				$output .= sprintf( '<td class="product-price number">%s</td>', $product_content['sale-price'] );
			}

			if ( $attr['invoiceType'] && $attr['price'] ) {
				$output .= sprintf( '<td class="product-price number">%s</td>', $product_content['price'] );
			}

			if ( $attr['invoiceType'] && $attr['discountPercentage'] ) {
				$output .= sprintf( '<td class="subtotal number">%s</td>', $product_content['discount-percentage'] );
			}

			if ( $attr['invoiceType'] && $attr['tax'] ) {
				$output .= sprintf( '<td class="product-price number">%s</td>', $product_content['tax'] );
			}

			if ( $attr['invoiceType'] && $attr['percentageTax'] ) {
				$output .= sprintf( '<td class="product-price number">%s%%</td>', $product_content['percentage-tax'] );
			}

			if ( $attr['productSubtotal'] ) {
				$output .= sprintf( '<td class="subtotal number">%s</td>', $product_content['line-total'] );
			}

			if ( $attr['invoiceType'] && $attr['productTotal'] ) {
				$output .= sprintf( '<td class="subtotal number">%s</td>', $product_content['product-total'] );
			}

			return $output;
		}

		/**
		 * Return the fee preview detail
		 *
		 * @return array
		 */
		public function get_fee_preview_content() {
			return array(
				array(
					'id'                  => 1000,
					'quantity'            => '',
					'name'                => 'Additional Fee',
					'sku'                 => '',
					'description'         => '',
					'weight'              => '',
					'weight_unit'         => '',
					'dimensions'          => '',
					'permalink'           => '#',
					'thumbnail'           => '',
					'thumbnail_path'      => '',
					'regular_price'       => 20,
					'sale_price'          => 0,
					'price'               => '',
					'percentage_tax'      => 20,
					'discount_percentage' => 0,
					'line_total'          => 20,
					'line_total_tax'      => 4,
				),
			);
		}

		/**
		 * Return the fee preview detail
		 *
		 * @return array
		 */
		public function get_shipping_preview_content() {
			return array(
				array(
					'id'                  => 2000,
					'quantity'            => '',
					'name'                => 'Shipping',
					'sku'                 => '',
					'description'         => '',
					'weight'              => '',
					'weight_unit'         => '',
					'dimensions'          => '',
					'permalink'           => '#',
					'thumbnail'           => '',
					'thumbnail_path'      => '',
					'regular_price'       => 5,
					'sale_price'          => 0,
					'price'               => '',
					'percentage_tax'      => 20,
					'discount_percentage' => 0,
					'line_total'          => 5,
					'line_total_tax'      => 1,
				),
			);
		}
	}
}

/**
 * Unique access to instance of YITH_YWPI_PDF_Template_Editor class
 *
 * @return YITH_YWPI_PDF_Template_Editor
 */
function yith_ywpi_template_editor() { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO
	return YITH_YWPI_PDF_Template_Editor::get_instance();
}
