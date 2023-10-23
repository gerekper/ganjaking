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
	 * @param string $view           View name.
	 * @param array  $args           Parameters to include in the view.
	 * @param bool   $return_content Return or not the content.
	 */
	function yith_ywpi_get_view( $view, $args = array(), $return_content = false ) {
		$view_path = trailingslashit( YITH_YWPI_VIEWS_PATH ) . $view;

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		if ( file_exists( $view_path ) ) {
			if ( $return_content ) {
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
		$order_id = $document->order->get_id();

		/**
		 * APPLY_FILTERS: yith_ywpi_allowed_tag
		 *
		 * Filter the customer details allowed HTML tags.
		 *
		 * @param string the allowed HTML tags.
		 *
		 * @return string
		 */
		$html_allowed_tag = apply_filters( 'yith_ywpi_allowed_tag', array( 'br' => array() ) );

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			if ( $document instanceof YITH_Credit_Note ) {
				// Use the parent order to extract customer information.
				$current_order_id = $document->order->get_id();
				$order_id         = get_post_field( 'post_parent', $current_order_id );
			}

			$content = wp_kses( YITH_PDF_Invoice()->get_customer_billing_details( $order_id ), $html_allowed_tag );
		} else {
			$content = wp_kses( YITH_PDF_Invoice()->get_customer_shipping_details( $order_id ), $html_allowed_tag );
		}

		/**
		 * APPLY_FILTERS: yith_ywpi_customer_details_content
		 *
		 * Filter the customer details content.
		 *
		 * @param string $content the customer details content.
		 * @param object $document the document object.
		 *
		 * @return string
		 */
		return apply_filters( 'yith_ywpi_customer_details_content', $content, $document );
	}
}

if ( ! function_exists( 'yith_ywpi_get_license_activation_url' ) ) {
	/**
	 * Get license activation url
	 *
	 * @return string
	 */
	function yith_ywpi_get_license_activation_url() {
		if ( ! function_exists( 'YITH_Plugin_Licence' ) && function_exists( 'yith_plugin_fw_load_update_and_licence_files' ) ) {
			// Try to load YITH_Plugin_Licence class.
			yith_plugin_fw_load_update_and_licence_files();
		}

		if ( function_exists( 'YIT_Plugin_Licence' ) ) {
			$license = YIT_Plugin_Licence();

			if ( is_callable( array( $license, 'get_license_activation_url' ) ) ) {
				return $license->get_license_activation_url( YITH_YWPI_SLUG );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'yith_ywpi_get_license' ) ) {
	/**
	 * Check if there is an active license
	 *
	 * @return bool|string
	 */
	function yith_ywpi_get_license() {
		if ( ! function_exists( 'YITH_Plugin_Licence' ) ) {
			// Try to load YITH_Plugin_Licence class.
			yith_plugin_fw_load_update_and_licence_files();
		}

		if ( function_exists( 'YITH_Plugin_Licence' ) ) {
			$license = YITH_Plugin_Licence();

			if ( is_callable( array( $license, 'get_licence' ) ) ) {
				$licenses = $license->get_licence();

				return isset( $licenses[ YITH_YWPI_SLUG ] ) ?? $licenses[ YITH_YWPI_SLUG ];
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_ywpi_get_unique_post_title' ) ) {
	/**
	 * Get unique post title
	 *
	 * @param   string $title      The post title.
	 * @param   int    $post_id    The post ID.
	 * @param   string $post_type  The post type.
	 *
	 * @return string
	 */
	function yith_ywpi_get_unique_post_title( $title, $post_id, $post_type = null ) {
		$count       = 1;
		$start_title = $title;
		$post_type   = is_null( $post_type ) ? get_post_type( $post_id ) : $post_type;

		while ( get_page_by_title( $title, OBJECT, $post_type ) ) {
			$title = sprintf( '%s (%d)', $start_title, $count++ );
		}

		return $title;
	}
}

if ( ! function_exists( 'yith_ywpi_is_gutenberg_active' ) ) {
	/**
	 * Check if Gutenberg is active
	 * Must be used not earlier than plugins_loaded action fired.
	 */
	function yith_ywpi_is_gutenberg_active() {
		$block_editor = false;

		if ( version_compare( $GLOBALS['wp_version'], '5.6', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		return $block_editor;
	}
}

if ( ! function_exists( 'yith_ywpi_get_pdf_template' ) ) {
	/**
	 * Return the pdf template object
	 *
	 * @param mixed  $pdf_template PDF Template.
	 * @param string $lang         Language.
	 *
	 * @return YITH_YWPI_PDF_Template
	 */
	function yith_ywpi_get_pdf_template( $pdf_template, $lang = '' ) {
		if ( function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;

			if ( ! is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
				$lang         = empty( $lang ) ? $sitepress->get_current_language() : $lang;
				$pdf_template = wpml_object_id_filter(
					$pdf_template,
					YITH_YWPI_PDF_Template_Builder::$pdf_template,
					true,
					$lang
				);
			}
		}

		return new YITH_YWPI_PDF_Template( $pdf_template );
	}
}
