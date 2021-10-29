<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
<?php
WafpAppHelper::plugin_title(__('Payments made to affiliates','affiliate-royale', 'easy-affiliate'));
$list_table->display();
?>
</div>
