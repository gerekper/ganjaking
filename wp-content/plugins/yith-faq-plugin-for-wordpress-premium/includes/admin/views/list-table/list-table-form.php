<?php
/**
 * List table template
 *
 * @package YITH\FAQPluginForWordPress\Admin\Views\ListTable
 * @var $table        YITH_Custom_Table The table to display.
 * @var $list_url     string The page URL.
 * @var $getted       array Page $_GET params.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form id="custom-table" method="GET" action="">
	<?php $table->display(); ?>
</form>

