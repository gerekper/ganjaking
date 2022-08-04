<?php
/**
 * Template options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
/*
$template_tab = array(
	'template' => array(
		'ywpi-template_list_table' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_YWPI_Post_Types::TEMPLATE,
			'wp-list-style' => 'boxed',
		),
	),
);

return $template_tab;

*/

/** Options v.2.1.0 */

$template_options = array(
	'template' => array(
		'template-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'template-content' => array(
					'title' => __( 'Content', 'yith-woocommerce-pdf-invoice' ),
				),
				'template-style'   => array(
					'title' => __( 'Style', 'yith-woocommerce-pdf-invoice' ),
				),
			),
		),
	),
);

return apply_filters( 'ywpi_template_options', $template_options );
