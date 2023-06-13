<?php
/*
 * Taxonomy Settings
 * @version 4.2.2
 */

$settings = new EVO_Settings();

$fields = EVO()->taxonomies->get_event_tax_fields_array($tax, $event_tax_term);

// process the fields array from taxonomy class
	foreach( $fields as $key=>$value){

		// field id
			$field_id = isset($value['var']) ? $value['var'] : $key;

		// get field value
			$field_value = '';
			if(empty($value['value'])){
				if(!empty($value['var']) && !empty( $termMeta[$value['var']] )){
					if( !is_array($termMeta[$value['var']]) && !is_object($termMeta[$value['var']])){
						$field_value = stripslashes(str_replace('"', "'", (esc_attr( $termMeta[$value['var']] )) ));
					}						
				}

			}else{	$field_value = $value['value'];	}


		$fields_processed[ $key ] = $value;
		if( !empty($value['placeholder']) ) $fields_processed[ $key ]['default'] = $value['placeholder'];
		if( !empty($value['legend']) ) $fields_processed[ $key ]['tooltip'] = $value['legend'];
		$fields_processed[ $key ]['id'] = $field_id;
		$fields_processed[ $key ]['value'] = $field_value;

	}

	//print_r($fields_processed);

$footer_btns = array(
	'save_changes'=> array(
		'label'=> __('Save Changes','eventon'),
		'data'=> array(
			'uid'=>'evo_save_tax_edit_settings',
			'lightbox_key'=>'evo_config_term',
			'hide_lightbox'=> 2000,
			'end'=>'admin'
		),
		'class'=> 'evo_btn evolb_trigger_save'
	)
);

// generate coordinates button for location
	if( $tax == 'event_location' && EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1')){
		$footer_btns['generate_coords']= array(
			'label'=> __('Generate Location Coordinates','eventon'),
			'data'=> array(	),
			'class'=> 'evo_btn evo_auto_gen_latlng'
		);
	}

// if term id exists add further edit button
	if( $term_id){
		$footer_btns['further_edit']= array(
			'label'=> __('Edit from page','eventon'),
			'data'=> array(	),
			'class'=> 'evo_admin_btn btn_secondary',
			'href'=> get_admin_url(). 'term.php?taxonomy='. $tax .'&tag_ID=' . $term_id .'&post_type=ajde_events',
		);
	}

$data_array =  array(
	'form_class'=>'evo_tax_event_settings',
	'container_class'=>'evo_tax pad20',
	'hidden_fields'=>array(
		'tax'=>$tax,
		'event_id'=>$event_id,
		'term_id'=>$term_id,
		'type'=> ( $term_id ? 'edit':'new'),
		'action'=>'eventon_event_tax_save_changes'
	),
	'footer_btns'=> $footer_btns,
	'fields'=> $fields_processed
);


echo $settings->get_event_edit_settings( apply_filters('evo_eventedit_taxonomy_fields_array', $data_array, $post_data, $settings ) );