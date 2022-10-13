<?php
/**
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$show_header = get_option( 'wc_store_catalog_pdf_download_show_header' );
$logo        = get_option( 'wc_store_catalog_pdf_download_logo' );
$header_text = get_option( 'wc_store_catalog_pdf_download_header_text' );

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style>
	body {
		width:100%;
		font-family: "DejaVu Sans";
	}
	
	p.description {
		line-height:1.5;
	}
	
	table {
		width:100%;
		margin:20px 0;
	}

	.logo {
		display:block;
		margin-bottom:20px;
	}
	
	span.price {
		margin:0;
	}

	table.grid td {
		text-align:center;
		padding:10px 5px;
	}
	
	table.grid img {
		margin-bottom:5px;
	}

	table.grid h2 {
		font-size:14px;
		margin:0 0 5px 0;
	}
	
	table.grid span.price {
		font-size:12px;
	}
	
	.list .row {
		page-break-after: always;
	}

	.list .row.last-row {
		page-break-after: avoid;
	}

	.list .image {
		width:auto;
		max-width:100%;
		margin-bottom:20px;
		text-align:center;
	}

	.list .content {
		margin-bottom:20px;
		width:auto;
	}

	.list .content h2 {
		margin:0 0 10px 0;
		font-size:18px;
	}
	
	.list .product-description {
		margin:20px 0;
	}

	div.single h2 {
		font-size:18px;
	}
	
	div.single span.price {
		clear:both;
		margin-top:20px;
	}

	div.single img {
		max-width:100%;
		padding-bottom:30px;
	}

	a {
		text-decoration:none;
		color:#000;
	}
	
	div.single .clear {
		content:"";
		clear:both;
		margin-top:30px;
	}
	
	table.shop_attributes {
		border: 0;
		border-top: 1px dotted rgba(0,0,0,0.1);
		margin-bottom: 1.618em;
		width: 100%;
	}

	table.shop_attributes th {
			width: 150px;
			font-weight: 700;
			padding: 8px;
			border-top: 0;
			border-bottom: 1px dotted rgba(0,0,0,0.1);
			margin: 0;
			line-height: 1.5;
	}

	table.shop_attributes td {
			font-style: italic;
			padding: 0;
			border-top: 0;
			border-bottom: 1px dotted rgba(0,0,0,0.1);
			margin: 0;
			line-height: 1.5;
	}

	table.shop_attributes p {
		margin: 0;
		padding: 8px 0;
	}

	table.shop_attributes tr.alt td, table.shop_attributes tr.alt th {
			background: #f5f5f5;
	}

	u, ins {
  		text-decoration: none;
	}
</style>
</head>

<body marginwidth="0" marginheight="0">

<script type="text/php">

	if ( isset( $pdf ) ) {

		$footer = $pdf->open_object();

		$font = Font_Metrics::get_font( "DejaVu Sans" );

		$w = $pdf->get_width();
		$h = $pdf->get_height();

		$y = (int) $pdf->get_height() - 40;

		$pdf->line( 20, $y, $w - 20, $y, array( .780, .780, .780 ), 1 );

		$width = Font_Metrics::get_text_width( $text, $font, $size );

		$pdf->page_text( 20, ( (int) $pdf->get_height() - 30 ), sprintf( __( '%s {PAGE_NUM} %s {PAGE_COUNT}', 'woocommerce-store-catalog-pdf-download' ), 'Page', 'of' ), $font, 10, array( .780, .780, .780 ) );

		$pdf->close_object();

		$pdf->add_object( $footer, 'all' );

	}
</script>

<div id="header">
	<?php if ( isset( $logo ) && ! empty( $logo ) ) { 
		$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );
	?>
		<a href="<?php echo site_url(); ?>"><img src="<?php echo esc_url( $logo_image_url[0] ); ?>" class="logo" /></a>
	<?php } ?>
	
	<?php if ( 'yes' === $show_header ) { ?>

			<?php if ( isset( $header_text ) && ! empty( $header_text ) ) { ?>
				<p><?php echo $header_text; ?></p>
			<?php } ?>
		
	<?php } ?>
</div><!--#header-->

<div id="main">