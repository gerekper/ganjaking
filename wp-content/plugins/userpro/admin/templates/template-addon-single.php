<div class="userpro-addon-grid">
	<div class="userpro-addon-container">
		<div class="userpro-addon-grid-inner">
			 <div class="userpro-addon-img"><a class="flickr-fancybox" rel="group" href="<?php //echo $image['full_image'] ?>"><img src="<?php echo $result->plugin_img;?>"></a></div>
  	 </div>  
		<div class="userpro-addon-meta-info">
			<div class="userpro-addon-meta-title"><a href="<?php echo $result->purchase_url?>"><?php echo $result->plugin_name;?></a></div>
			<div class="userpro-addon-meta-description"><?php echo $result->description;?></div>
			<?php 
				if( isset($result->is_free_download) ){
			?>	
			<a class="userpro-addon-meta-buy" href="<?php echo $result->purchase_url?>" target="_blank"><?php _e('FREE DOWNLOAD','userpro'); ?></a>
			<?php	
			}else if( !userpro_check_addon_status($active_plugins, $result->plugin_slug) ){
			?>
			<a class="userpro-addon-meta-buy" href="<?php echo $result->purchase_url?>" target="_blank"><?php _e('Buy Now','userpro'); ?></a>
			<?php }else{
			?>
				<div class="userpro-addon-meta-buy"><?php _e('Installed','userpro');?></div>
			<?php
			}?>
      </div>  
 </div>                          
</div>
