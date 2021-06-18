<div class="updb-widget-style">
	<div class="updb-basic-info"><?php _e( 'Basic Information', 'userpro-dashboard' );?></div>
<div class="updb-view-profile-details">
<?php foreach( $view_fields as $key => $array ) { 				
	if ($array){
		if( $key == 'social' || $key == 'accountinfo')
			continue;
		echo userpro_show_field( $key, $array, $i, $args,0, $user_id );
	}
} 
?>
</div>
</div>
