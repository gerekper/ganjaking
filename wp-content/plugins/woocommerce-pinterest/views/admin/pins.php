<?php if (!defined('ABSPATH')) {
	die;
}
?>

<?php

use \Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\Table\PinsTable;

?>

<?php

/**
 * Used vars list
 *
 * @var FileManager $fileManager
 * @var string $stateMessage
 * @var PinsTable $table
 */

?>

<div class="wrap">
	<h1><?php esc_html_e('Pins', 'woocommerce-pinterest'); ?>
		<?php $fileManager->includeTemplate('admin/state.php', array('stateMessage' => $stateMessage)); ?>
	</h1>
	<form method="GET" action="<?php echo esc_url(admin_url('admin.php')); ?>">
		<input type="hidden" value="pins" name="tab">
		<input type="hidden" value="woocommerce-pinterest-page" name="page">
		<?php
		$table->prepare_items();
		$table->search_box(esc_html__('Search pins', 'woocommerce-pinterest'), 'woocommerce-pinterest-pins-search');
		$table->display();
		?>
	</form>

</div>
