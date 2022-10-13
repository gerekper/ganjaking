<?php
/**
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$show_footer = get_option( 'wc_store_catalog_pdf_download_show_footer' );
$footer_text = get_option( 'wc_store_catalog_pdf_download_footer_text' );

?>

</div><!--#main-->

<?php if ( $show_footer === 'yes' ) { ?>
	<div id="footer">
		<?php if ( isset( $footer_text ) && ! empty( $footer_text ) ) { ?>
			<p><?php echo $footer_text; ?></p>
		<?php } ?>
	</div><!--#footer-->
<?php } ?>
</body>
</html>