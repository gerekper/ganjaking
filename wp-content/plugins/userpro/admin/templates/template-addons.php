<?php
	$active_plugins = get_plugins();
?>

<div class="userpro-addons-container">
<div class="up-upsell-strip">Available addons</div>
	<?php 
                $results = array();
		$results = get_transient('userpro_addons');
		if( empty( $results ) ){
			$response = wp_remote_get('https://s3-us-west-2.amazonaws.com/userpro-upsell/addons/userpro-addons.json');
			$results = $response['body'];
			set_transient('userpro_addons', $results,360 * HOUR_IN_SECONDS);
		}
		$results = json_decode($results);
                if( !empty( $results ) ){
		foreach( $results as $result ){
			include userpro_path .'admin/templates/template-addon-single.php';
		}
                }
	?>
</div>
<?php 
	$results = get_transient('userpro_recommended_plugins');
		if( empty( $results ) ){
			$response = wp_remote_get('https://s3-us-west-2.amazonaws.com/userpro-upsell/recommended/recommended-addons.json');
			if ( ! is_wp_error( $response ) && $response['response']['code'] == 200) {
				$results = $response['body'];			
				set_transient('userpro_recommended_plugins', $results,360 * HOUR_IN_SECONDS);
			}
		}
		if( !empty( $results ) ){
			$results = json_decode($results);
			?>
			<div class="userpro-addons-container">
			<div class="up-upsell-strip">Recommended Plugins</div>
			<?php 
			foreach( $results as $result ){
				include userpro_path .'admin/templates/template-addon-single.php';
			}
			?>
			</div>
<?php			
		}	
?>
