<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Used vars list
 *
 * @var string $key
 * @var array $data
 * @var bool $catalogGenerated
 * @var string $catalogFilePath
 * @var int $generatedRowsNumber
 * @var bool $catalogFileExists
 */
?>

<?php

$ingestDataSourceUrl     = 'https://www.pinterest.com/product-catalogs';
$pinterestCatalogsDocUrl = 'https://help.pinterest.com/en/business/article/shopping-on-pinterest';

?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<?php echo esc_html( $data['title'] ); ?>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $data['title'] ); ?></span></legend>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php' ) . '?action=woocommerce_pinterest_generate_catalog' ); ?>"
			   class="button"><?php esc_html_e( 'Generate catalog', 'woocommerce-pinterest' ); ?></a>
			<?php if ( $catalogGenerated ) : ?>
				<input
						type="text"
						class="woocommerce-pinterest-catalog-url-field"
						value="<?php echo esc_attr( $catalogFilePath ); ?>"
						readonly
				>

				<button type="button" class="woocommerce-pinterest-copy-catalog-url-button"><span
							class="dashicons dashicons-admin-page"></span></button>

				<p class="description">
					<?php
					/* translators: '%s' something here, '%d', '%s' */
					echo sprintf( esc_attr__( esc_html( 'Your catalog is ready. It contains %d rows (products). Ingest your data source %s or %s about Catalogs.' ),
						'woocommerce-pinterest' ), intval( $generatedRowsNumber ), sprintf( '<a href=\'%s\' target="_blank">here</a>', esc_url( $ingestDataSourceUrl ) ), sprintf( '<a href="%s" target="_blank">read Pinterest documentation</a>', esc_url( $pinterestCatalogsDocUrl ) ) );
					?>
				</p>
			<?php elseif ( $catalogFileExists ) : ?>
				<p class="description">
					<?php
					/* translators: '%d is replaced with generated products number' */
					echo sprintf( esc_attr__(
						esc_html( 'Catalog generation in progress. Currently generated %d rows (products)' )
						, 'woocommerce-pinterest' ),
						intval( $generatedRowsNumber ) );
					?>
				</p>

			<?php else : ?>
				<p class="description">
					<?php esc_html_e( 'Catalog file not found. Please, generate it if you want to use Pinterest Catalogs feature.', 'woocommerce-pinterest' ); ?>
				</p>
			<?php endif; ?>
		</fieldset>
	</td>
</tr>
