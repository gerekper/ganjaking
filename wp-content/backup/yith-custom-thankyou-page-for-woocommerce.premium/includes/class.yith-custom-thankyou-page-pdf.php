<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */
if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}


use Dompdf\Dompdf;


if ( ! class_exists( 'YITH_Custom_Thankyou_Page_PDF' ) ) {
	/**
	 * Print Order Details as PDF
	 *
	 * @class       YITH_Custom_Thankyou_Page_Premium
	 * @package     YITH Custom ThankYou Page for Woocommerce
	 * @author      YITH
	 * @since       1.0.5
	 */
	class YITH_Custom_Thankyou_Page_PDF {

		protected static $_instance = null;

		/**
		 * Construct function
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function __construct() {

			/* load dompdf lib */
			require_once YITH_CTPW_LIB_DIR . 'dompdf/autoload.inc.php';

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			/* register the frontend script to call it in yith_ctpw_pdf_button */
			wp_register_script( 'yith-ctpw-front', YITH_CTPW_ASSETS_URL . 'js/yith_ctpw_front' . $suffix . '.js', array( 'jquery' ), YITH_CTPW_VERSION, true );

			// if the user set to use the button as shortcode we not add it automatically
			// but we will register it as shortcode.
			$use_as_shortcode = get_option( 'yith_ctpw_enable_pdf_as_shortcode', 'no' );
			if ( 'no' === $use_as_shortcode ) {
				/* print the PDF button */
				add_action( 'yith_ctpw_successful_ac', array( $this, 'yith_ctpw_pdf_button' ), 35 );
			} else {
				add_shortcode( 'yith_ctpw_pdf_button', array( $this, 'yith_ctpw_pdf_button_shortcode' ) );
			}

			/* add ajax function callback to get pdf */
			add_action( 'wp_ajax_yith_ctpw_get_pdf', array( $this, 'yith_ctpw_get_pdf' ) );
			add_action( 'wp_ajax_nopriv_yith_ctpw_get_pdf', array( $this, 'yith_ctpw_get_pdf' ) );

			// PDF document actions
			// pdf style.
			add_action( 'yith_ctpw_pdf_template_head', array( $this, 'yith_ctpw_add_pdf_styles' ), 10 );

			// header logo.
			if ( get_option( 'yith_ctpw_pdf_show_logo', 'no' ) !== 'no' && get_option( 'yith_ctpw_pdf_custom_logo', '' ) !== '' ) {
				add_action( 'yith_ctpw_template_document_header', array( $this, 'yith_ctpw_add_pdf_logo' ), 10 );
			}
			// order header.
			if ( get_option( 'yith_ctpw_pdf_show_order_header' ) !== 'no' && get_option( 'yith_ctpw_pdf_show_order_header' ) !== '' ) {
				add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_header' ), 10 );
			}

			// order details table.
			if ( get_option( 'yith_ctpw_pdf_show_order_details_table' ) !== 'no' && get_option( 'yith_ctpw_pdf_show_order_details_table' ) !== '' ) {
				add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_table' ), 15 );
			}

			// customer details.
			if ( get_option( 'yith_ctpw_pdf_show_customer_details', 'no' ) === 'yes' ) {
				add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_customer_details' ), 20 );
			}

			// footer.
			if ( trim( get_option( 'yith_ctpw_pdf_footer_text' ) ) !== '' ) {
				add_action( 'yith_ctpw_template_footer', array( $this, 'yith_ctpw_add_pdf_footer_text' ), 10 );
			}


		}

		/**
		 * Get Class Main Instance
		 *
		 * @return YITH_Custom_Thankyou_Page_PDF
		 * @since 1.0.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Set a maximum execution time
		 *
		 * @param int $seconds time in seconds.
		 *
		 * @since 1.0.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		private function set_time_limit( $seconds ) {
			$check_safe_mode = ini_get( 'safe_mode' );
			if ( ( ! $check_safe_mode ) || ( 'OFF' === strtoupper( $check_safe_mode ) ) ) {
			    @set_time_limit( $seconds );
			}
		}

		/**
		 * Load frontend script for PDF button
		 *
		 * @return void
		 * @since 1.0.9
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function yith_ctpw_pdf_script() {
			/*load the frotend script*/
			wp_enqueue_script( 'yith-ctpw-front' );

			/* provide some values for the script*/
			$localize_array_datas = array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'pdf_creator' => YITH_CTPW_URL . 'includes/pdf_creator.php',
				'order_id'    => intval( $_GET['order-received'] ), //phpcs:ignore
				'file_name'   => apply_filters( 'yith_ctpw_pdf_file_name', 'yctpw.pdf' ),
				'loading_gif' => YITH_CTPW_ASSETS_URL . 'images/preloader.gif',
			);
			wp_localize_script( 'yith-ctpw-front', 'yith_ctpw_ajax', $localize_array_datas );
		}

		/**
		 * Print the PDF button on checkout success
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_pdf_button() {

			$this->yith_ctpw_pdf_script();

			$style = $this->yith_ctpw_get_button_styles();

			/* printing the button */
			$button_label = get_option( 'yith_ctpw_pdf_button_label', esc_html__( 'Save as PDF', 'yith-custom-thankyou-page-for-woocommerce' ) );
			echo $style . '<button id="yith_ctwp_pdf_button">' . apply_filters( 'yith_ctpw_pdf_button_label', $button_label ) . '</button>'; //phpcs:ignore

		}

		/**
		 * Get pdf ajax call
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_get_pdf() {
			$this->set_time_limit( 120 );

			$result = array(
				'status' => false,
				'file'   => '',
			);

			/* manage the Preview Settings if we are in Backend PDF preview */
			if ( isset( $_POST['order_id'] ) && 0 !== $_POST['order_id'] ) { //phpcs:ignore
				if ( isset ( $_POST['backend_preview'] ) && $_POST['backend_preview'] ) { //phpcs:ignore

					if ( isset( $_POST['preview_settings']['show_order_header_table'] ) && 'no' === $_POST['preview_settings']['show_order_header_table'] ) {//phpcs:ignore
						remove_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_header' ), 10 );
					} else {
						add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_header' ), 10 );
					}

					if ( isset( $_POST['preview_settings']['show_order_details_table'] ) && 'no' === $_POST['preview_settings']['show_order_details_table'] ) {//phpcs:ignore
						remove_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_table' ), 15 );
					} else {
						add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_order_infos_table' ), 15 );
					}

					if ( isset( $_POST['preview_settings']['show_customer_details'] ) && 'no' === $_POST['preview_settings']['show_customer_details'] ) {//phpcs:ignore
						remove_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_customer_details' ), 20 );
					} else {
						add_action( 'yith_ctpw_template_order_content', array( $this, 'yith_ctpw_add_pdf_customer_details' ), 20 );
					}

					if ( isset( $_POST['preview_settings']['show_logo'] ) && 'no' === $_POST['preview_settings']['show_logo'] ) {//phpcs:ignore
						remove_action( 'yith_ctpw_template_document_header', array( $this, 'yith_ctpw_add_pdf_logo' ), 10 );
					} else {
						add_action( 'yith_ctpw_template_document_header', array( $this, 'yith_ctpw_add_pdf_logo' ), 10 );
					}

					if ( isset( $_POST['preview_settings']['footer_text'] ) && '' ===  trim( $_POST['preview_settings']['footer_text'] ) ) {//phpcs:ignore
						remove_action( 'yith_ctpw_template_footer', array( $this, 'yith_ctpw_add_pdf_footer_text' ), 10 );
					} else {
						add_action( 'yith_ctpw_template_footer', array( $this, 'yith_ctpw_add_pdf_footer_text' ), 10 );
					}
				}

				$order = wc_get_order( sanitize_key( wp_unslash( $_POST['order_id'] ) ) );//phpcs:ignore

				ob_start();
				wc_get_template(
					'order_infos_pdf_template.php',
					array(
						'order'      => $order,
						'main_class' => apply_filters( 'yith_ctpw_ftp_template_add_body_class', '' ),
					),
					'',
					YITH_CTPW_PDF_TEMPLATE_PATH
				);


				$html = ob_get_clean();
				$html = apply_filters( 'yith_ctpw_before_pdf_rendering_html', $html, $order );

				/* if we have the backedn_preview value we provide the html as result and stop here - this is for preview in PDF settings Panel in backend */
				if ( isset( $_POST['backend_preview'] ) && $_POST['backend_preview'] ) {//phpcs:ignore
					$result['status'] = true;
					$result['file']   = $html;
					echo wp_json_encode( $result );
					wp_die();

				}

				// instantiate and use the dompdf class.
				$dompdf = new Dompdf();
				$dompdf->loadHtml( $html );

				$dompdf->setPaper( 'A4', 'portrait' );

				$dompdf->render();
				$pdf_content = $dompdf->output();

				// construct the pdf filename and path.
				$up_dir      = wp_upload_dir();
				$folder_path = trailingslashit( $up_dir['basedir'] ) . 'ctpw_tmp/';
				$filename    = 'ctpw_order_' . $order->get_id() . '.pdf';


				// check if the pdf temp folder path exists, if not create it.
				if ( ! is_dir( $folder_path ) ) {
					mkdir( $folder_path );
				}

				// write the pdf.
				if ( file_put_contents( $folder_path . $filename, $pdf_content ) ) {
					$result['status'] = true;
					$result['file']   = $folder_path . $filename;
				}
			}

			echo wp_json_encode( $result );

			wp_die();
		}

		/**
		 * Get Pdf Button Css from plugin settings
		 *
		 * @return string css codes
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.9
		 */
		public function yith_ctpw_get_button_styles() {
			$style                  = '<style>';
			$pdf_button_colors      = get_option( 'yith_ctpw_pdf_button_colors' );
			$back_color             = $pdf_button_colors['normal'];
			$back_color_hover       = $pdf_button_colors['hover'];
			$pdf_button_text_colors = get_option( 'yith_ctpw_pdf_button_text_colors' );
			$text_color             = $pdf_button_text_colors['normal'];
			$text_color_hover       = $pdf_button_text_colors['hover'];

			$style .= '#yith_ctwp_pdf_button {';
			if ( 'none' !== $back_color && '' !== $back_color ) {
				$style .= 'background-color: ' . $back_color . '; ';
			}

			if ( 'none' !== $text_color && '' !== $text_color ) {
				$style .= 'color: ' . $text_color . ';';
			}

			$style .= '}';
			$style .= ' #yith_ctwp_pdf_button:hover {';

			if ( 'none' !== $back_color_hover && '' !== $back_color_hover ) {
				$style .= 'background-color: ' . $back_color_hover . ';';
			}

			if ( 'none' !== $text_color_hover && '' !== $text_color_hover ) {
				$style .= 'color: ' . $text_color_hover . ';';
			}

			$style .= '}';
			$style .= '</style>';

			return $style;
		}

		/**
		 * Shortcode to print PDF button
		 *
		 * @since 1.0.8
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @return string
		 */
		public function yith_ctpw_pdf_button_shortcode() {
			/*load the frotend script*/
			$this->yith_ctpw_pdf_script();

			$style = $this->yith_ctpw_get_button_styles();

			/* printing the button */
			$button_label = get_option( 'yith_ctpw_pdf_button_label', esc_html__( 'Save as PDF', 'yith-custom-thankyou-page-for-woocommerce' ) );

			return $style . '<button id="yith_ctwp_pdf_button">' . apply_filters( 'yith_ctpw_pdf_button_label', $button_label ) . '</button>';
		}

		/**
		 * Add styles to pdf template
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_styles() {
			ob_start();
			echo '<style>';
			// APPLY_FILTER yctpw_pdf_font_family: change PDF file font-family.
			echo '* { font-family: "' . esc_attr( apply_filters( 'yctpw_pdf_font_family', 'Times New Roman' ) ) . '";}';
			wc_get_template( 'ctpw_pdf_styles.css', '', '', YITH_CTPW_PDF_TEMPLATE_PATH );
			echo '</style>';
			echo ob_get_clean(); //phpcs:ignore
		}

		/**
		 * Add logo to pdf header
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_logo() {

			if ( isset ( $_POST['backend_preview'] ) && ! empty( $_POST['backend_preview'] ) ) { //phpcs:ignore
				$logo_url = $_POST['preview_settings']['logo_image_url'];//phpcs:ignore
			} else {
				$logo_url           = get_option( 'yith_ctpw_pdf_custom_logo', '' );
				$logo_attachment_id = attachment_url_to_postid( $logo_url );
				$logo_url           = $logo_attachment_id ? get_attached_file( $logo_attachment_id ) : $logo_url;
			}


			if ( empty( $logo_url ) ) {
				return;
			}

			$logo_max_width = apply_filters( 'yith_ctpw_pdf_logo_max_width', get_option( 'yith_ctpw_pdf_custom_logo_max_size', '90' ) );

			ob_start();
			?>

			<header>
			<div id="main_logo"><img src="<?php echo esc_attr( $logo_url ); ?>" style="max-width: <?php echo esc_attr( $logo_max_width ); ?>px;"/></div>
			</header>

			<?php
			echo ob_get_clean(); //phpcs:ignore
		}

		/**
		 * Add header order part to pdf template
		 *
		 * @param WC_Order $order order object.
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_order_infos_header( $order ) {

			ob_start();
			wc_get_template(
				'order_infos_pdf_template_order_header.php',
				array(
					'order' => $order,
				),
				'',
				YITH_CTPW_PDF_TEMPLATE_PATH
			);

			echo ob_get_clean();//phpcs:ignore
		}

		/**
		 * Add order table to pdf template
		 *
		 * @param WC_Order $order order object.
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_order_infos_table( $order ) {
			ob_start();
			wc_get_template(
				'order_infos_pdf_template_order_table.php',
				array(
					'order' => $order,
				),
				'',
				YITH_CTPW_PDF_TEMPLATE_PATH
			);

			echo ob_get_clean();//phpcs:ignore
		}

		/**
		 * Add Customer Details to pdf template
		 *
		 * @param WC_Order $order order object.
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_customer_details( $order ) {
			ob_start();
			wc_get_template(
				'pdf_template_customer_details.php',
				array(
					'order' => $order,
				),
				'',
				YITH_CTPW_PDF_TEMPLATE_PATH
			);

			echo ob_get_clean();//phpcs:ignore
		}

		/**
		 * Add footer text from plugin options to pdf template
		 *
		 * @param WC_Order $order order object.
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.5
		 */
		public function yith_ctpw_add_pdf_footer_text( $order ) {
			echo '<div id="footer_text">';
			echo trim( get_option( 'yith_ctpw_pdf_footer_text' ) );//phpcs:ignore
			echo '</div>';
		}

	} // end class.

}
