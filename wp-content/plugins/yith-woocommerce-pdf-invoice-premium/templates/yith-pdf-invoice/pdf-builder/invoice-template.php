<?php
/**
 * Main PDF document
 *
 * @package YITH\PDFInvoice\Templates
 * @since   4.0.0
 * @version 4.0.0
 * @author  YITH <plugins@yithemes.com>
 *
 * @var $content
 * @var $footer
 */

defined( 'ABSPATH' ) || exit;

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style>
		body {
			color: #000;
		}
	</style>
</head>
<body>
<htmlpagefooter name="footer">
	<div id="document-footer" style="background-color:transparent;font-size:8px;text-align: center">
		<?php echo esc_html( $footer ); ?>
	</div>
</htmlpagefooter>
<sethtmlpagefooter name="footer" value="on" page="ALL" />
<div class="content">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php do_action( 'yith_ywpi_template_notes_builder', $document ); ?>
</div>
<sethtmlpagefooter name="footer" value="on" page="ALL" />
</body>
</html>
