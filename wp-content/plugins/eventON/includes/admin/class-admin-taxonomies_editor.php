<?php
/*
 * EventON Taxonomy Editor
 * @version 4.2
 */

class EVO_Taxonomies_editor{

function editor_ajax_calls(){
	$ajax_events = array(
		'get_event_tax_term_section'=>'get_event_tax_term_section',
		'event_tax_list'		=>'tax_select_term',
		'event_tax_save_changes'=>'event_tax_save_changes',
		'event_tax_remove'		=>'event_tax_remove',
	);
	foreach ( $ajax_events as $ajax_event => $class ) {
		$prepend = 'eventon_';
		add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
		add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
	}

	$this->helper = new evo_helper();

}

// AJAX
	function get_event_tax_term_section(){		

		$post_data = $this->helper->sanitize_array( $_POST);

		echo json_encode(array(
			'status'=>'good',
			'content'=> $this->get_tax_form($post_data)
		)); exit;
	}
	// tax term list to select from
	function tax_select_term(){
		$post_data = $this->helper->sanitize_array( $_POST);
		$terms = get_terms(
			$post_data['tax'],
			array(
				'orderby'           => 'name', 
			    'order'             => 'ASC',
			    'hide_empty'=>false
			) 
		);

		ob_start();
		echo "<div class='evo_tax_entry' data-eventid='{$post_data['eventid']}' data-tax='{$post_data['tax']}' data-type='list'>";

		if(count($terms)>0){	

			echo "<p>". __('Select a term from the below list.','eventon') . "</p>";

			// multiple tax select option
			if( in_array( $post_data['tax'], $this->get_single_select_tax_array() ) ):
				?><select class='field' name='event_tax_termid'><?php	
			else:			
				?><select class='field' name='event_tax_termid[]' multiple='multiple'><?php	
			endif;

			// saved term ids
			$saved_term_ids = array();
			if( !empty($post_data['termid'])){
				$saved_term_ids = explode(',', $post_data['termid']);
			}

			// for each term
			foreach ( $terms as $term ) {

				if( empty($term->name)) continue;

				$selected = in_array($term->term_id, $saved_term_ids)? 'selected="selected"':'';

				?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
			}
			?></select>
			<p style='text-align:center; padding-top:10px;'><span class='evo_btn evo_term_submit'><?php _e('Save Changes','eventon');?></span></p>
			<?php
		}else{
			?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
		}

		echo "</div>";

		echo json_encode(array(
			'status'=>'good',
			'content'=>ob_get_clean()
		)); exit;
	}

	// save changes
	function event_tax_save_changes(){
		$post_data = $this->helper->sanitize_array( $_POST);
		$status = 'bad';
		$content = '';
		$tax = $post_data['tax'];

		switch($post_data['type']){
		case 'list':
			if(!empty($post_data['event_tax_termid'])){
				$event_id = (int)$post_data['eventid'];

				// selected terms filtering
				if( is_array($post_data['event_tax_termid'])){
					$selected_terms = array_map('intval', $post_data['event_tax_termid'] );
				}else{
					$selected_terms = (int)$post_data['event_tax_termid'];
				}

				wp_set_object_terms( $event_id, $selected_terms, $tax , false);
				$status = 'good';
				$content = __('Changes successfully saved!','eventon');	
			}else{
				$content = __('Term ID was not passed!','eventon');	
			}
		break;
		case 'new':
		case 'edit':
			
			if(!isset($post_data[ 'term_name' ])) break;

			$term_name = esc_attr(stripslashes($post_data[ 'term_name' ]));
			$term = term_exists( $term_name, $tax );
			
			// term already exists
			if($term !== 0 && $term !== null){
				$taxtermID = (int)$term['term_id'];
			}else{
				// create slug from term name
					$trans = array(" "=>'-', ","=>'');
					$term_slug= strtr($term_name, $trans);

				// create wp term
				$new_term_ = wp_insert_term( $term_name, $tax , array('slug'=>$term_slug) );

				if(!is_wp_error($new_term_)){
					$taxtermID = intval( $new_term_['term_id'] );
				}	
			}

			$fields = $this->get_event_tax_fields_array($post_data['tax'],'');

			
			// if a term ID is present
			if($taxtermID){

				$term_meta = array();

				// save description
				$term_description = isset($post_data['description'])? sanitize_text_field($post_data['description']):'';
				$tt = wp_update_term($taxtermID, $tax, array( 'description'=>$term_description ));
				
				// lat and lon values saved in the form
					if(isset($post_data['location_lon'])) $term_meta['location_lon'] = str_replace('"', "'", $post_data['location_lon']); 
					if(isset($post_data['location_lat'])) $term_meta['location_lat'] = str_replace('"', "'", $post_data['location_lat']); 

				foreach($fields as $key=>$value){
					if(in_array($key, array('description', 'submit','term_name','evcal_lat','evcal_lon'))) continue;

					if(isset($post_data[$value['var']])){

						do_action('evo_tax_save_each_field',$value['var'], $post_data[$value['var']]);

						// specific to location tax
						if($value['var']=='location_address'){
							if(isset($post_data['location_address']))
								$latlon = eventon_get_latlon_from_address($post_data['location_address']);

							// longitude
							$term_meta['location_lon'] = isset($term_meta['location_lon']) ? $term_meta['location_lon']:
								(!empty($latlon['lng'])? floatval($latlon['lng']): null);

							// latitude
							$term_meta['location_lat'] = isset($term_meta['location_lat']) ? $term_meta['location_lat']:
								(!empty($latlon['lat'])? floatval($latlon['lat']): null);

							$term_meta['location_address' ] = (isset($post_data[ 'location_address' ]))? $post_data[ 'location_address' ]:null;

							continue;
						}


						$term_meta[ $value['var'] ] = str_replace('"', "'", $post_data[$value['var']]); 

					}else{
						$term_meta[ $value['var'] ] = ''; 
					}
				}

				// save meta values
					evo_save_term_metas($tax, $taxtermID, $term_meta);


				// assign term to event & replace
					$append = in_array( $post_data['tax'] , $this->get_single_select_tax_array() ) ? 
						false: true;
					wp_set_object_terms( $post_data['eventid'], $taxtermID, $tax , $append);	

				$status = 'good';
				$content = __('Changes successfully saved!','eventon');	
			}

		break;
		}

		echo json_encode(array(
			'status'=>$status,
			'content'=>$content,
			'htmldata'=> $this->get_meta_box_content($tax , $post_data['eventid'] )
		)); exit;
	}
	// remove a taxonomy term
	function event_tax_remove(){
		$post_data = $this->helper->sanitize_array( $_POST);
		$status = 'bad';
		$content = '';
		
		if(!empty($post_data['termid'])){
			$event_id = (int)$post_data['eventid'];
			wp_remove_object_terms( $event_id, (int)$post_data['termid'], $post_data['tax'] , false);
			$status = 'good';
			$content = __('Changes successfully saved!','eventon');	
		}else{
			$content = __('Term ID was not passed!','eventon');	
		}

		echo json_encode(array(
			'status'=>$status,
			'content'=>$content,
			'htmldata'=> $this->get_meta_box_content($post_data['tax'] , $post_data['eventid'] )
		)); exit;
	}

// META BOX CONTENT
	function get_meta_box_content($tax, $event_id){
		$event_tax_term = wp_get_post_terms($event_id, $tax);

		$string_term_ids = '';

		$tax_human_name = $this->get_translated_tax_name( $tax );

		$text_select_different = sprintf(__('Select different %s from list','eventon'),  $tax_human_name);
		$text_create_new = sprintf(__('Create a new %s','eventon'),$tax_human_name);

		//print_r($event_tax_term);

		ob_start();
		// If a tax term is already set
		if ( $event_tax_term && ! is_wp_error( $event_tax_term ) ){	
			
			$text_edit = sprintf(__('Edit %s','eventon'),$tax_human_name);

			$set_term_ids = array();
						
			?><p class='evo_selected_tax_term'><?php

			// each already selected terms
			foreach($event_tax_term as $term){
				$set_term_ids[] = $term->term_id;

				$term_data = array(
					'lbvals'=> array(
						'lbc'=>'evo_config_term',
						't'=>$text_edit,
						'ajax'=>'yes',
						'd'=> array(
							'uid'=>'evo_get_tax_term_form',
							'type'=>'edit',
							'termid'=> $term->term_id,
							'eventid'=> $event_id,
							'tax'=> $tax,
							'action'=> 'eventon_get_event_tax_term_section'
						)
					)
				);

				$term_data_del = array(
					'type'=>'delete',
					'termid'=> $term->term_id,
					'tax'=> $tax,
					'eventid'=> $event_id,
				);

				?><span>
					<em><?php echo $term->name;?></em>
					<i class='fa fa-pencil evo_tax_term_form evolb_trigger' <?php echo $this->helper->array_to_html_data( $term_data );?> title='<?php echo $text_edit;?>' ></i> 
					<i class='fa fa-times evo_tax_remove' <?php echo $this->helper->array_to_html_data( $term_data_del );?> title='<?php _e('Delete','eventon');?>'></i>
				</span>
				<?php
			}

			?>
			</p>

			<?php $string_term_ids = implode(',', $set_term_ids);			
		}

		// action buttons
		$data_vals_sel = array(
			'lbvals'=> array(
				'lbc'=>'evo_config_term',
				't'=>$text_select_different,
				'ajax'=>'yes',
				'd'=> array(
					'uid'=>'evo_get_tax_list',
					'type'=>'list',
					'eventid'=> $event_id,
					'termid'=> $string_term_ids,
					'tax'=> $tax,
					'action'=> 'eventon_event_tax_list'
				)
			)
		);
		$data_vals_new = array(
			'lbvals'=> array(
				'lbc'=>'evo_config_term',
				't'=>$text_create_new,
				'ajax'=>'yes',
				'd'=> array(
					'uid'=>'evo_get_tax_term_form',
					'type'=>'new',
					'eventid'=> $event_id,
					'tax'=> $tax,
					'action'=> 'eventon_get_event_tax_term_section'
				)
			)
		);

		?>
		<p class='evo_selected_tax_actions'>
			<a class='evo_tax_term_list evo_btn evolb_trigger' <?php echo $this->helper->array_to_html_data( $data_vals_sel );?> ><?php echo $text_select_different;?></a>
			<a class='evo_tax_term_form evo_btn evolb_trigger' <?php echo $this->helper->array_to_html_data( $data_vals_new );?>><?php echo $text_create_new;?></a>
		</p>
		
		<?php

		return ob_get_clean();
	}

// FORM - new/ edit
	function get_tax_form( $post_data=''){
		global $ajde;

		$post_data = $this->helper->sanitize_array( $_POST);

		$is_new = (isset($post_data['type']) && $post_data['type']=='new')? true: false;

		$event_id = isset($post_data['eventid']) ? (int)$post_data['eventid']: false;
		$term_id = isset($post_data['termid']) ? (int)$post_data['termid']: false;
		$tax = isset($post_data['tax']) ? $post_data['tax']: false;

		// definitions
			$termMeta = $event_tax_term = false;

		// if edit
		if(!$is_new && $tax){

			$event_tax_term = get_term_by('term_id', $term_id,  $tax);
			$termMeta = evo_get_term_meta( $tax, $term_id, '', true);
			
		}

		ob_start();
		
		echo "<div class='evo_tax_entry' data-eventid='{$event_id}' data-tax='{$_POST['tax']}' data-type='{$_POST['type']}'>";
		
		// pass term id if editing
			if($event_tax_term && !$is_new):?>
				<p><input class='field' type='hidden' name='termid' value="<?php echo $term_id;?>" /></p>
			<?php endif;

		// for each fields
		$fields = EVO()->taxonomies->get_event_tax_fields_array($tax, $event_tax_term);

		
		foreach( $fields as $key=>$value){
			
			// get field value
				$field_value = '';
				if(empty($value['value'])){
					if(!empty($value['var']) && !empty( $termMeta[$value['var']] )){
						if( !is_array($termMeta[$value['var']]) && !is_object($termMeta[$value['var']])){
							$field_value = stripslashes(str_replace('"', "'", (esc_attr( $termMeta[$value['var']] )) ));
						}						
					}

				}else{	$field_value = $value['value'];	}

			switch ($value['type']) {
				case 'text':
					echo EVO()->elements->get_element(array(
						'type'=>'text',
						'id'=>$key,
						'name'=>$value['name'],
						'value'=>$field_value,
						'default'=> !empty($value['placeholder'])? $value['placeholder']:'',
						'tooltip'=> !empty($value['legend'])? $value['legend']:''
					));
				break;
				case 'select':
					?>
					<p>	
						<label for='<?php echo $key;?>'><?php echo $value['name']?></label>
						<select id='<?php echo $key;?>' class='field' name='<?php echo $value['var'];?>'>
						<?php foreach( $value['options'] as $f=>$v){
							echo "<option value='{$f}' ". ($field_value == $f?'selected':'') .">{$v}</option>";
						}?>
						</select>							
						<?php if(!empty($value['legend'])):?>
							<em class='evo_legend'><?php echo $value['legend']?></em>
						<?php endif;?>
					</p>
					<?php
				break;
				case 'textarea':
					echo EVO()->elements->get_element(array(
						'type'=>'textarea',
						'id'=>$key,
						'name'=>$value['name'],
						'value'=>$field_value,
						'tooltip'=> !empty($value['legend'])? $value['legend']:''
					));
				break;
				case 'image':
					$image_id = $termMeta? $field_value: false;

					// image soruce array
					$img_src = ($image_id)? 	wp_get_attachment_image_src($image_id,'medium'): null;
						$img_src = (!empty($img_src))? $img_src[0]: null;

					$__button_text = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
					$__button_text_not = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
					$__button_class = ($image_id)? 'removeimg':'chooseimg';
					?>
					<p class='evo_metafield_image'>
						<label><?php echo $value['name']?></label>
						
						<input class='field <?php echo $key;?> custom_upload_image evo_meta_img' name="<?php echo $key;?>" type="hidden" value="<?php echo ($image_id)? $image_id: null;?>" /> 
	            		
	            		<input class="custom_upload_image_button button <?php echo $__button_class;?>" data-txt='<?php echo $__button_text_not;?>' type="button" value="<?php echo $__button_text;?>" /><br/>
	            		<span class='evo_loc_image_src image_src'>
	            			<img src='<?php echo $img_src;?>' style='<?php echo !empty($image_id)?'':'display:none';?>'/>
	            		</span>
	            		
	            	</p>
					<?php
				break;
				case 'yesno':

					echo EVO()->elements->get_element(array(
						'type'=>'yesno_btn',
						'id'=>$key, 
						'value'=>$field_value,
						'inputAttr'=>array('class'=>'field'),
						'label'=>$value['name']
					));

				break;
				case 'button':
					?>
					<p style='text-align:center; padding-top:10px'><span class='evo_btn evo_term_submit'><?php echo $is_new? __('Add New','eventon'): __('Save Changes','eventon');?></span>

						<?php if(!$is_new):?>
							<a class='evo_admin_btn btn_secondary' href='<?php echo get_admin_url()?>term.php?taxonomy=<?php echo $tax;?>&tag_ID=<?php echo $term_id;?>&post_type=ajde_events'><?php _e('Edit From Page','eventon');?></a>
						<?php endif;?>

					</p>
					<?php
				break;
			}

			// after location longitude field
			if($key == 'evcal_lon' && EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1')){
				echo "<p><a class='evo_auto_gen_latlng evo_admin_btn'>". __('Generate Location Coordinates','eventon') ."</a></p>";
			}
		}

		echo "</div>";

		return ob_get_clean();
	}

// DATA feed
	// return the taxonomies that only support one term value
	function get_single_select_tax_array(){
		return array('event_location');
	}

	// get tax translater name
	function get_translated_tax_name($tax){
		$data = apply_filters('evo_tax_translated_names', array(
			'event_location'=>__('location','eventon'),
			'event_organizer'=>__('organizer','eventon')
		), $tax);

		return isset($data[ $tax ]) ? $data[ $tax ] : $tax;
	}
}