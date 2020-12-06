<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
<?php
WafpAppHelper::plugin_title( __('Transactions referred by Affiliates','affiliate-royale', 'easy-affiliate'),
                             '<a href="'. admin_url('admin.php?page=affiliate-royale-transactions&action=new') . '" class="add-new-h2">Add New</a>');
$list_table->display();
?>
</div>
