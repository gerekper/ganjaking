<?php if ( ! defined('ABSPATH')) {
	die;
}

use Premmerce\WooCommercePinterest\Admin\Table\CatalogTable;

/**
 * Used vars list
 *
 * @var $table CatalogTable
 */
?>

<tr valign="top">
	<th scope="row">

	</th>
	<td style="padding: 0">
		<div id="woocommerce-pinterest-google-catalog-table" class="woocommerce-pinterest-settings-list-table">
			<?php $table->display(); ?>

			<div class="woocommerce-pinterest-table-saved-message">
				<p><?php esc_html_e('Saved', 'woocommerce-pinterest'); ?></p>
			</div>
		</div>
	</td>

</tr>
