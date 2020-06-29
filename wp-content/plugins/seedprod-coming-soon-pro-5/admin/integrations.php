<?php

// var

$page_id = '';
if(!empty($_REQUEST['page_id'])){
    $page_id = $_REQUEST['page_id'];
}

$emaillist = '';
if(!empty($_REQUEST['seed_cspv5_emaillist'])){
    $emaillist = $_REQUEST['seed_cspv5_emaillist'];
}

?>
<div class="wrap">
<?php include(SEED_CSPV5_PLUGIN_PATH.'admin/header.php') ?>
    <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content">
                <?php 
	                // Get settings
                    $mod = '';
                	if($emaillist == 'mailchimp'){
					    $settings_name = 'seed_cspv5_'.$page_id.'_'.$emaillist;
					    $settings = get_option($settings_name);
					    if(!empty($settings)){
					        $settings = maybe_unserialize($settings);
					    }
					    if(empty($settings['mailchimp_api_key']) || (!empty($settings['api_version']) && $settings['api_version'] == '3')){
					    	// Use V3
					    	$mod = '_v3';
					    }
	                }
                ?>
                 <?php echo call_user_func('seed_cspv5_section_'.$emaillist.$mod,$emaillist,$page_id); ?>
</div></div></div></div>