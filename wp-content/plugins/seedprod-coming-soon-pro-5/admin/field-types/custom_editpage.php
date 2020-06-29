<?php
$coming_soon_page_id = get_option('seed_cspv5_coming_soon_page_id'); 
if(empty($coming_soon_page_id)){
?>
<a href="<?php echo admin_url() ?>options-general.php?page=seed_cspv5_themes&page_id=-1&type=cs&name=<?php echo urlencode('Coming Soon Page') ?>" class="button-primary"><i class="fa fa-edit"></i> Edit Coming Soon/Maintenance Page</a>
<?php }else{ ?>
<a href="<?php echo admin_url() ?>options-general.php?page=seed_cspv5_customizer&seed_cspv5_customize=<?php echo $coming_soon_page_id ?>" class="button-primary"><i class="fa fa-edit"></i> Edit Coming Soon/Maintenance Page</a>
<?php } ?>