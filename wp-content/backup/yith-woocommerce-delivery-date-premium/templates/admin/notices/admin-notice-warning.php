<?php
if( !defined('ABSPATH')){
	exit;
}
?>
<div class="notice notice-warning" style="padding-right: 38px;position: relative;">
<p><?php echo $message;?></p>
<?php if( $url!='' ):?>
<a class="notice-dismiss" href="<?php echo $url;?>"  style="text-decoration: none;"></a>
<?php endif;?>
</div>