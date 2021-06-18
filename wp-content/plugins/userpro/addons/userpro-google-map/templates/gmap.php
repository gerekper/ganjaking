<?php
$enable_gmap = userpro_gmap_get_option('enable_gmap');
if($enable_gmap){
?>
	<div class="section-header userpro-section userpro-column userpro-collapsible-1 userpro-collapsed-0 up-google-map" ><div class="section-title"><?php _e('Map','userpro');?></div></div>
	<div id="map" class="row userpro-field col-md-12 col-md-offset-0 col-xs-12 col-xs-offset-0 col-sm-12 col-sm-offset-0"></div>
<?php
}