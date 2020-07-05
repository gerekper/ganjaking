<?php if ( ! defined('ABSPATH')) {
	die;
}

use \Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable\WcCategoryPinterestBoardRelationsTable;

/**
 * Used vars list
 *
 * @var $tableCategoryArray
 * @var WcCategoryPinterestBoardRelationsTable $table
 */

?>

<tr valign="top" class="top">
	<th scope="row" class="titledesc">

	</th>
	<td style="padding: 0">
		<div id="woocommerce-pinterest-category-board-table" class="woocommerce-pinterest-settings-list-table">
				<?php $table->display(); ?>
			<div class="woocommerce-pinterest-table-saved-message">
				<p><?php esc_html_e('Saved', 'woocommerce-pinterest'); ?></p>
			</div>
		</div>
	</td>
</tr>
