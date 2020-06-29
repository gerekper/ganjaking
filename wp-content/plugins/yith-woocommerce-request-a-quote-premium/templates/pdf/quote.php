<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$pdf_font = apply_filters( 'pdf_font_family', '"dejavu sans"' );
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style type="text/css">
		body {
			color: #000;
			font-family: <?php echo $pdf_font; ?>;
		}
		.logo{
			width: 100%;
			float: left;
			max-width: 300px;
		}
		.right{
			float: right;
			width: 40%;
			text-align: right;
		}
		.clear{
			clear: both;
		}
		.admin_info{
			font-size: 12px;
		}

		table{
			border: 0;
		}
		table.quote-table{
			border: 0;
			font-size: 14px;
		}

		.small-title{
			text-align: right;
			font-weight: 600;
			color: #4e4e4e;
			padding-top: 5px;
			padding-right: 5px;
		}
		.small-info p{
			border-left: 2px solid #a8c6e4;
			padding: 0 0 5px 5px;
			margin-bottom: 20px;
		}
		.quote-table td{
			border: 0;
			border-bottom: 1px solid #eee;
		}
		.quote-table .with-border td{
			border-bottom: 2px solid #eee;
		}
		.quote-table .with-border td{
			border-top: 2px solid #eee;
		}
		.quote-table .quote-total td{
			height: 100px;
			vertical-align: middle;
			font-size: 18px;
			border-bottom: 0;
		}
		.quote-table small{
			font-size: 13px;
		}
		.quote-table .last-col{
			padding-right: 45px;
		}
		.quote-table .last-col.tot{
			font-weight: 600;
		}
		.quote-table .tr-wb{
			border-left: 1px solid #ccc ;
			border-right: 1px solid #ccc ;
		}
		.pdf-button{
			color: #a8c6e4;
			text-decoration: none;
		}
		div.content{ padding-bottom: 100px; border-bottom: 1px }

		.footer {
			position: fixed;
			bottom: 0;
			text-align: center;
			font-size: 70%
		}

		.footer {
			width: 100%;
			text-align: center;
			position: fixed;
			bottom: 0;
		}

		.pagenum:before {
			content: counter(page);
		}

		<?php
			$template = get_option( 'ywraq_pdf_template', 'table' );

		if ( 'div' === $template ) :
			?>

		/* div template style */
		.table-wrapper ul {
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.table-wrapper * {
			box-sizing: border-box;
		}

		.quote-table.raq-header {
			border-bottom: 1px solid #ebebeb;
			margin: 5px 0;
		}

		.quote-table.raq-header li, .quote-table.raq-items .raq_item li, .quote-table.raq-totals li  {
			float: left;
			padding: 5px 0;
		}
		.fields-1 li {
			width: 100%;
		}

		.fields-2 li {
			width: 50%;
		}

		.fields-3 li {
			width: 33%;
		}

		.fields-4 li {
			width: 25%;
		}

		.fields-5 li {
			width: 20%;
		}
		.quote-table.raq-items > li {
			border-bottom: 1px solid #ebebeb;
			padding: 5px 0;
		}
		.quote-table.raq-items > li.with-metas {
			border: none;
		}

		.raq-totals .colspan0 {
			width: 75%
		}

		.raq-totals .colspan1 {
			width: 50%
		}

		.raq-totals .colspan2 {
			width: 66%
		}

		.raq-totals .colspan3 {
			width: 75%
		}

		.raq-totals .colspan4 {
			width: 80%
		}

		.raq-totals .totals_label {
			text-align: right;
			padding-right: 10px !important;
		}

		.raq-totals {
			border-bottom: 1px solid #ebebeb;
		}

		.raq-items .wc-item-meta li {
			float: none !important;
			width: 100%;
		}

		.wc-item-meta li p {
			display: inline-block;
			padding: 0 !important;
			margin: 0;
		}


			<?php
			endif;
		?>


	</style>
	<?php

	do_action( 'yith_ywraq_quote_template_head' );
	?>
</head>

<body>
<?php
do_action( 'yith_ywraq_quote_template_footer', $order_id );
?>

<?php
do_action( 'yith_ywraq_quote_template_header', $order_id );
?>
<div class="content">
	<?php
	do_action( 'yith_ywraq_quote_template_content', $order_id );
	?>
</div>
<?php
do_action( 'yith_ywraq_quote_template_after_content', $order_id );
?>
</body>
</html>
