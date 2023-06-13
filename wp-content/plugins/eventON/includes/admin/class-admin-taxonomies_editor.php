<?php
/*
 * EventON Taxonomy Editor
 * @version 4.4
 */

class EVO_Taxonomies_editor{
private $helper;

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
		echo "<div class='evo_tax_entry'><form>";

		if(count($terms)>0){	

			// \hidden fields for the form
			echo EVO()->elements->process_multiple_elements(array(
				array(
					'type'=>'input', 'field_type'=>'hidden',
					'id'=>'event_id','value'=>$post_data['event_id']
				),array(
					'type'=>'input', 'field_type'=>'hidden',
					'id'=>'tax','value'=>$post_data['tax']
				),array(
					'type'=>'input', 'field_type'=>'hidden',
					'id'=>'type','value'=>'list'
				),array(
					'type'=>'input', 'field_type'=>'hidden',
					'id'=>'action','value'=>'eventon_event_tax_save_changes'
				)
			));

			echo "<p>". __('Select a term from the below list.','eventon') . "</p>";

			// multiple tax select option
				if( in_array( $post_data['tax'], $this->get_single_select_tax_array() ) ):
					?><select class='field' name='event_tax_termid'><?php	
				else:			
					?><select class='field' name='event_tax_termid[]' multiple='multiple'><?php	
				endif;

			// saved term ids
				$saved_term_ids = array();
				if( !empty($post_data['term_id'])){
					$saved_term_ids = explode(',', $post_data['term_id']);
				}

			// for each term
				foreach ( $terms as $term ) {

					if( empty($term->name)) continue;

					$selected = in_array($term->term_id, $saved_term_ids)? 'selected="selected"':'';

					?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
				}
			?></select>

			<?php 
				$btn_data = array(
					'd'=> array(						
						'uid'=> 'evo_save_term_list_item',
						'lightbox_key'=>'evo_config_term',
						'hide_lightbox'=>2000
					)
				);
			?>

			<p style='text-align:center; padding-top:10px;'>
				<span class='evo_btn evo_submit_form' <?php echo $this->helper->array_to_html_data( $btn_data );?>><?php _e('Save Changes','eventon');?></span>
			</p>

			<?php
		}else{
			?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
		}

		echo "</form></div>";

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
					$event_id = (int)$post_data['event_id'];

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

				$fields = EVO()->taxonomies->get_event_tax_fields_array($post_data['tax'],'');

				
				// if a term ID is present
				if($taxtermID){

					$term_meta = array();

					// save description
					$term_description = isset($_POST['description'])? 
						$this->helper->sanitize_html($_POST['description']):'';


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


							$field_value = $post_data[ $value['var'] ];
							$field_value = str_replace('"', "'", $field_value );

							// for secondary description
							if( $key == 'description2' && isset( $_POST['description2'] )){
								$field_value = $this->helper->sanitize_html( $_POST['description2'] );
							}

							$term_meta[ $value['var'] ] = $field_value; 

						}else{
							$term_meta[ $value['var'] ] = ''; 
						}
					}

					// save meta values
						evo_save_term_metas($tax, $taxtermID, $term_meta);


					// assign term to event & replace
						$append = in_array( $post_data['tax'] , $this->get_single_select_tax_array() ) ? 
							false: true;
						wp_set_object_terms( $post_data['event_id'], $taxtermID, $tax , $append);	

					$status = 'good';
					$content = __('Changes successfully saved!','eventon');	
				}

			break;
			}

			echo json_encode(array(
				'tax'=> $tax,
				'status'=>$status,
				'msg'=>$content,
				'htmldata'=> $this->get_meta_box_content($tax , $post_data['event_id'] )
			)); exit;
		}
	// remove a taxonomy term
		function event_tax_remove(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$status = 'bad';
			$content = '';
			
			if(!empty($post_data['term_id'])){
				$event_id = (int)$post_data['event_id'];
				wp_remove_object_terms( $event_id, (int)$post_data['term_id'], $post_data['tax'] , false);
				$status = 'good';
				$content = __('Changes successfully saved!','eventon');	
			}else{
				$content = __('Term ID was not passed!','eventon');	
			}

			echo json_encode(array(
				'tax'=> $post_data['tax'],
				'status'=>$status,
				'msg'=>$content,
				'htmldata'=> $this->get_meta_box_content($post_data['tax'] , $post_data['event_id'] )
			)); exit;
		}

// META BOX CONTENT
	function get_meta_box_content($tax, $event_id){
		$event_tax_term = wp_get_post_terms($event_id, $tax);

		$string_term_ids = '';

		$tax_human_name = $this->get_translated_tax_name( $tax );

		$text_select_different = sprintf(__('Select a %s from list','eventon'),  $tax_human_name);
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
							'term_id'=> $term->term_id,
							'event_id'=> $event_id,
							'tax'=> $tax,
							'load_new_content'=> true,
							'action'=> 'eventon_get_event_tax_term_section'
						)
					)
				);

				$term_data_del = array(
					'd'=> array(
						'ajaxdata'=> array(
							'tax'=> $tax,
							'term_id'=> $term->term_id,
							'event_id'=> $event_id,
							'action'=> 'eventon_event_tax_remove',
						),
						'uid'=> 'evo_remove_tax_term',
					)
				);

				?><span>
					<em><?php echo $term->name;?></em>
					<i class='fa fa-pencil evolb_trigger' <?php echo $this->helper->array_to_html_data( $term_data );?> title='<?php echo $text_edit;?>' ></i> 
					<i class='fa fa-times evo_trigger_ajax_run' <?php echo $this->helper->array_to_html_data( $term_data_del );?> title='<?php _e('Delete','eventon');?>'></i>
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
					'event_id'=> $event_id,
					'term_id'=> $string_term_ids,
					'tax'=> $tax,
					'action'=> 'eventon_event_tax_list',
					'load_new_content'=> true
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
					'event_id'=> $event_id,
					'tax'=> $tax,
					'action'=> 'eventon_get_event_tax_term_section',
					'load_new_content'=> true
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

		$event_id = isset($post_data['event_id']) ? (int)$post_data['event_id']: false;
		$term_id = isset($post_data['term_id']) ? (int)$post_data['term_id']: false;
		$tax = isset($post_data['tax']) ? $post_data['tax']: false;

		// definitions
			$termMeta = $event_tax_term = false;

		// if edit
		if(!$is_new && $tax){
			$event_tax_term = get_term_by('term_id', $term_id,  $tax);
			$termMeta = evo_get_term_meta( $tax, $term_id, '', true);			
		}

		ob_start();

		include_once('views/taxonomy_settings.php');

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