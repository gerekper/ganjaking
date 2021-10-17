<?php
/**
 * event post color meta box
 */

// Use nonce for verification
	//wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename_2' );
	
	$event = $this->EVENT;
	$ev_vals = $this->event_data;
	
	$evOpt = get_option('evcal_options_evcal_1');

?>		

<div class=''>	
	<?php
		// Hex value cleaning
		$hexcolor = eventon_get_hex_color($ev_vals,'', $evOpt );	
	?>			
	<div id='color_selector' >
		<em id='evColor' style='background-color:<?php echo (!empty($hexcolor) )? $hexcolor: 'na'; ?>'></em>
		<p class='evselectedColor'>
			<span class='evcal_color_hex evcal_chex'  ><?php echo (!empty($hexcolor) )? $hexcolor: 'Hex code'; ?></span>
			<span class='evcal_color_selector_text evcal_chex'><?php _e('Click here to pick a color','eventon');?></span>
		</p>
	</div>
	<p style='margin-bottom:0; padding:5px 0'><?php _e('OR Select from other colors','eventon');?></p>
	
	<div id='evcal_colors'>
		<?php 

			global $wpdb;
			$tableprefix = $wpdb->prefix;

			$results = $wpdb->get_results(
				"SELECT {$tableprefix}posts.ID, mt0.meta_value AS color, mt1.meta_value AS color_num
				FROM {$tableprefix}posts 
				INNER JOIN {$tableprefix}postmeta AS mt0 ON ( {$tableprefix}posts.ID = mt0.post_id )
				INNER JOIN {$tableprefix}postmeta AS mt1 ON ( {$tableprefix}posts.ID = mt1.post_id )
				WHERE 1=1 
				AND ( mt0.meta_key = 'evcal_event_color' )
				AND {$tableprefix}posts.post_type = 'ajde_events'
				AND (({$tableprefix}posts.post_status = 'publish'))
				GROUP BY {$tableprefix}posts.ID
				ORDER BY {$tableprefix}posts.post_date DESC LIMIT 50"
			, ARRAY_A);

			if($results){
				$other_colors = array();
				
				foreach($results as $color){
					// hex color cleaning
					$hexval = substr( str_replace('#', '', $color['color']) , 0,7);
					$hexval_num = !empty($color['color_num'])? $color['color_num']: 0;
					
					
					if(!empty( $hexval) && (empty($other_colors) || (is_array($other_colors) && !in_array($hexval, $other_colors)	)	)	){
						echo "<div class='evcal_color_box' style='background-color:#".$hexval."'color_n='".$hexval_num."' color='".$hexval."'></div>";
						
						$other_colors[]=$hexval;
					}
				}
			}							
		?>				
	</div>
	<div class='clear'></div>
	<input id='evcal_event_color' type='hidden' name='evcal_event_color' 
		value='<?php echo str_replace('#','',$hexcolor); ?>'/>
	<input id='evcal_event_color_n' type='hidden' name='evcal_event_color_n' 
		value='<?php echo (!empty($ev_vals["evcal_event_color_n"]) )? $ev_vals["evcal_event_color_n"][0]: null ?>'/>
</div>	