<?php
/**
 *
 */



// if location tax saved before
	$location_terms = !empty($event_id)? wp_get_post_terms($event_id, 'event_location'):'';
	$termMeta = $evo_location_tax_id = '';
	if ( $location_terms && ! is_wp_error( $location_terms ) ){
		$evo_location_tax_id =  $location_terms[0]->term_id;
		
		//$termMeta = get_option( "taxonomy_$evo_location_tax_id");
		$termMeta = evo_get_term_meta('event_location',$evo_location_tax_id, '', true);
	}

echo "<div class='row locationSelect'>
	<p class='label'><label for='".$__field_id."'>".$__field_name.$__req."</label></p>";

echo '<p class="selection" data-role="none">';

// edit form
if($_EDITFORM && !empty($evo_location_tax_id) && $hide_list){
	echo "<span class='evoau_selected_val'>".$location_terms[0]->name."</span>";
}

// default values for saved field
	echo "<input type='hidden' name='evoau_location_select' value='{$evo_location_tax_id}'/>
	<input type='hidden' name='evo_loc_img_id' value=''/>";

// terms list for selection
if($terms_exists && !$hide_list):
	$base_txt = eventon_get_custom_language($opt_2, 'evoAUL_ssl', 'Select Saved Locations', $lang);

	echo '<select class="evoau_location_select" name="evoau_location_select" data-role="none">';
		echo "<option value=''>".$base_txt."</option>";
	// each select field optinos
		foreach ( $locations as $loc ) {
			$taxmeta = evo_get_term_meta('event_location',$loc->term_id, '', true);
	    	// /$taxmeta = get_option("taxonomy_".$loc->term_id);

	    	$__selected = ($evo_location_tax_id== $loc->term_id)? "selected='selected'":null;
	       	
	    	// select option attributes
	    	$data = array(
	    		'add'=>'location_address',
	    		'lon'=>'location_lon',
	    		'lat'=>'location_lat',
	    		'link'=>'evcal_location_link',
	    		'img'=>'evo_loc_img',
	    	);
	    	$datastr = '';
	    	foreach($data as $f=>$v){	$datastr.= ' data-'.$f.'="'.( !empty($taxmeta[$v])?$taxmeta[$v]:'').'"';	}

	       	echo "<option value='{$loc->term_id}' {$datastr} {$__selected}>" . $loc->name . '</option>';
	    }								    
    
    echo "</select>";
    
endif;

// edit location button
	if($_EDITFORM && !empty($evo_location_tax_id) && EVO()->cal->check_yn('evoau_allow_edit_location')){
		echo "<span class='editMeta formBtnS'>". eventon_get_custom_language($opt_2,'evoAUL_edit','Edit', $lang)."</span>";
	}

// Create new buttons

	if($allow_add_new){ 
		$_alt_txt = $hide_list? evo_lang('Hide Create New Form'): eventon_get_custom_language($opt_2, 'evoAUL_sfl', 'Select from List', $lang); 
		echo "<span class='enterNew formBtnS ' data-txt='".$_alt_txt."' data-st='".($terms_exists?'ow':'nea')."'>". eventon_get_custom_language($opt_2,'evoAUL_cn','Create New', $lang)."</span>";
	}

echo "</p>";							    

$data = array(
	'event_location_name',
	'event_location',
	'event_location_cord',
	'event_location_link',
);

// Create new FORM
	if($allow_add_new){ 
	    echo "<div class='enterownrow' style='display:". ( $allow_add_new? 'none':'block'). "'>";
	    
	    $fields = EVOAU()->frontend->au_form_fields();
	    foreach($data as $v){
	    	$dataField = $fields[$v];
	    	$savedValue = (!empty($termMeta) && !empty($termMeta[$dataField[1]]) )?$termMeta[$dataField[1]]: ''; 

	    	// lat and lon values
	    	if($v=='event_location_cord'){
	    		$savedValue = (!empty($termMeta) && !empty($termMeta['location_lat']) && !empty($termMeta['location_lon']) )? $termMeta['location_lat'].','.$termMeta['location_lon']:'';
	    	}

	    	// location name
	    	if($v == 'event_location_name' && !empty($location_terms)){
	    		$savedValue = $location_terms[0]->name;
	    	}
	    	echo "<p class='subrows {$v}'><label class='$dataField[4]'>".eventon_get_custom_language($opt_2, $dataField[4], $dataField[0], $lang)."</label><input class='fullwidth' type='text' name='{$dataField[1]}' value='{$savedValue}' data-role='none'/></p>";
	    }
	    echo "</div>";
	}

echo "</div>";