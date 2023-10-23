<?php
	add_action('userpro_after_fields','get_map');	

	function get_map($args) {
		global $userpro;
		if( $args['template'] == 'view' ){
			$address_line1 = get_user_meta($args['user_id'], 'address_line_1', true);
			$address_line2 = get_user_meta($args['user_id'], 'address_line_2', true);
			$address_line3 = get_user_meta($args['user_id'], 'address_line_3', true);
		    $country = get_user_meta($args['user_id'], 'country', true);
			if(!empty($address_line1) || !empty($address_line2) || !empty($address_line3) || !empty($country)){
				include_once(UPGMAP_PATH.'templates/gmap.php');
			}
		}
		
		if ( ($args['template'] == 'edit') || ($args['template']=='register') ){
			//$user_id = userpro_get_view_user( get_query_var('up_username') );
			include_once(UPGMAP_PATH.'templates/gmap.php');
		}
	}
?>