<?php
/**
 * Core Functions
 *
 * @package YITH\PDFInvoice
 */

if ( ! function_exists( 'yith_ywpi_get_panel_url' ) ) {
	/**
	 * Return the YITH YWPI Panel url
	 *
	 * @param string $tab Tab.
	 * @param string $subtab Sub tab.
	 *
	 * @return string
	 */
	function yith_ywpi_get_panel_url( $tab = '', $subtab = '' ) {
		$query_args = array( 'page' => YITH_YWPI_Plugin_FW_Loader::get_instance()->get_panel_page() );

		if ( $tab ) {
			$query_args['tab'] = $tab;
		}
		if ( $subtab ) {
			$query_args['sub_tab'] = $subtab;
		}

		return add_query_arg( $query_args, '' );
	}
}

if ( ! function_exists( 'yith_ywpi_get_view' ) ) {
	/**
	 * Load a view.
	 *
	 * @param string $view View name.
	 * @param array  $args Parameters to include in the view.
	 * @param bool   $return Return or not the content.
	 */
	function yith_ywpi_get_view( $view, $args = array(), $return = false ) {
		$view_path = trailingslashit( YITH_YWPI_VIEWS_PATH ) . $view;

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		if ( file_exists( $view_path ) ) {
			if ( $return ) {
				$content = include $view_path;

				return $content;
			} else {
				include $view_path;
			}
		}
	}
}

if ( ! function_exists( 'yith_ywpi_get_selected_template' ) ) {
	/**
	 * Get selected template in the plugin option 'ywpi_document_template_selected'
	 */
	function yith_ywpi_get_selected_template() {

		$template_option = ywpi_get_option( 'ywpi_document_template_selected' );
		$template_option = ! empty( $template_option ) ? $template_option : 'default';

		return $template_option;
	}
}

if ( ! function_exists( 'yith_ywpi_get_template_path' ) ) {
	/**
	 * Get selected template path
	 */
	function yith_ywpi_get_template_path() {

		$template_path = '';

		$type = yith_ywpi_get_selected_template();

		switch ( $type ) {
			case 'default':
				$template_path = 'yith-pdf-invoice/';
				break;
			case 'black_white':
			case 'modern':
				$template_path = 'yith-pdf-invoice/' . $type . '_template/';
				break;
		}

		return $template_path;
	}
}

if ( ! function_exists( 'ywpi_get_customer_details_template' ) ) {
	/**
	 * Get customer details for the templates
	 *
	 * @param YITH_Document $document The document.
	 */
	function ywpi_get_customer_details_template( $document ) {

		$order_id         = yit_get_prop( $document->order, 'id' );
		$html_allowed_tag = apply_filters( 'yith_ywpi_allowed_tag', array( 'br' => array() ) );

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			if ( $document instanceof YITH_Credit_Note ) {
				// Use the parent order to extract customer information.
				$current_order_id = yit_get_prop( $document->order, 'id' );
				$order_id         = get_post_field( 'post_parent', $current_order_id );
			}

			$content = wp_kses( YITH_PDF_Invoice()->get_customer_billing_details( $order_id ), $html_allowed_tag );
		} else {
			$content = wp_kses( YITH_PDF_Invoice()->get_customer_shipping_details( $order_id ), $html_allowed_tag );
		}

		return apply_filters( 'yith_ywpi_customer_details_content', $content, $document );
	}
}
