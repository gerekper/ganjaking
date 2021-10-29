<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
<?php
WafpAppHelper::plugin_title(__('Subscriptions referred by affiliates','affiliate-royale', 'easy-affiliate'));
$sub_table->display();
?>
</div>
