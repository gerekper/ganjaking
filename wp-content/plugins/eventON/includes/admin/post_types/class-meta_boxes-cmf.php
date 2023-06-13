<?php
/**
 *	Event edit custom meta field data
 *	@version 4.4
 */

$metabox_array = array();
$p_id = get_the_ID();

$EVENT = new EVO_Event( $p_id );

// Custom Meta fields for events
	$num = evo_calculate_cmd_count( EVO()->cal->get_op('evcal_1') );
	for($x =1; $x<=$num; $x++){	
		if(!eventon_is_custom_meta_field_good($x)) continue;

		$fa_icon_class = EVO()->cal->get_prop('evcal__fai_00c'.$x);		

		$visibility_type = (!empty($evcal_opt1['evcal_ec_f'.$x.'a4']) )? $evcal_opt1['evcal_ec_f'.$x.'a4']:'all' ;

		$metabox_array[] = array(
			'id'=>'evcal_ec_f'.$x.'a1',
			'variation'=>'customfield',
			'name'=>	EVO()->cal->get_prop('evcal_ec_f'.$x.'a1'),		
			'iconURL'=> $fa_icon_class,
			'iconPOS'=>'',
			'x'=>$x,
			'visibility_type'=>$visibility_type,
			'type'=>'code',
			'content'=>'',
			'slug'=>'evcal_ec_f'.$x.'a1'
		);
	}

$closedmeta = eventon_get_collapse_metaboxes($EVENT->ID);

if( count($metabox_array)>0):
	foreach($metabox_array as $index=>$mBOX){
		ob_start();

		
		$x = $mBOX['x'];
		$__field_id = '_evcal_ec_f'.$x.'a1_cus';
		$__field_type = EVO()->cal->get_prop('evcal_ec_f'.$x.'a2');

		echo "<div class='evcal_data_block_style1'>
				<div class='evcal_db_data ' data-id='{$__field_id}'>";

				
			// FIELD
			$__saved_field_value = ($EVENT->get_prop("_evcal_ec_f".$x."a1_cus") )? $EVENT->get_prop("_evcal_ec_f".$x."a1_cus"):null ;
			
			// wysiwyg editor
			if( $__field_type == 'textarea'){
			
				wp_editor($__saved_field_value, $__field_id);					
				
			// textarea editor
			}elseif( $__field_type == 'textarea_basic'){			
				
				echo "<textarea class='textarea_basic' type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus'> ";										
				echo $__saved_field_value.'</textarea>';	
				
			// button
			}elseif( $__field_type =='button'){
				
				$__saved_field_link = ($EVENT->get_prop("_evcal_ec_f".$x."a1_cusL")  )? $EVENT->get_prop("_evcal_ec_f".$x."a1_cusL"):null ;

				echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";
				echo 'value="'. ( !empty($__saved_field_value) ? addslashes($__saved_field_value ) :'' ) .'"';						
				echo "style='width:100%' placeholder='".__('Button Text','eventon')."' title='Button Text'/>";

				echo "<input type='text' id='_evcal_ec_f".$x."a1_cusL' name='_evcal_ec_f".$x."a1_cusL' ";
				echo 'value="'. $__saved_field_link.'"';						
				echo "style='width:100%' placeholder='".__('Button Link','eventon')."' title='Button Link'/>";

					$onw = ($EVENT->get_prop("_evcal_ec_f".$x."_onw") )? $EVENT->get_prop("_evcal_ec_f".$x."_onw"):null ;
				?>

				<span class='yesno_row evo'>
					<?php 	
					echo EVO()->elements->yesno_btn(array(
						'id'=>'_evcal_ec_f'.$x . '_onw',
						'var'=> $EVENT->get_prop('_evcal_ec_f'.$x . '_onw'),
						'input'=>true,
						'label'=>__('Open in New window','eventon')
					));?>											
				</span>
				<?php
			
			// text	
			}else{
				echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";										
				echo 'value="'. $__saved_field_value.'"';						
				echo "style='width:100%'/>";								
			}

		echo "</div></div>";

		$metabox_array[$index]['content'] = ob_get_clean();
		$metabox_array[$index]['close'] = ( $closedmeta && in_array($mBOX['id'], $closedmeta) ? true:false);
	}

	// process for visibility
	echo EVO()->evo_admin->metaboxes->process_content( $metabox_array );

else:
	echo '<p class="pad20"><span class="evomarb10" style="display:block">' . __('You do not have any custom meta fields activated.') . '</span><a class="evo_btn" href="'. get_admin_url(null, 'admin.php?page=eventon#evcal_009','admin') .'">'. __('Activate Custom Meta Fields','eventon') . '</a></p>';
endif;

//print_r($metabox_array);





