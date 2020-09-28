<?php
if ($user_id){
			$value = userpro_profile_data( $key, $user_id );
                        
			if (isset($array['type']) && $key != 'role' && in_array($array['type'], array('select','multiselect','checkbox','checkbox-full','radio','radio-full') ) ) {
				$value = userpro_profile_data_nicename( $key, userpro_profile_data( $key, $user_id ) );
			}
			if ( ( isset($array['html']) && $array['html'] == 0 ) ) {
				$value =  userpro_profile_nohtml( $value );
			}
			if (isset($array['type']) && $array['type'] == 'picture'){
				if ($key == 'profilepicture') {
					$value = get_avatar($user_id, 64);
				} else {
					$crop = userpro_profile_data( $key, $user_id );
					if ($crop){
					if (isset($array['width'])){
						$width = $array['width'];
						$height = $array['height'];
					} else {
						$width = '';
						$height = '';
					}
					$value = '<img src="'.$crop.'" width="'.$width.'" height="'.$height.'" alt="" class="modified" />';
					}
				}
			}
			if (isset($array['type']) && $array['type'] == 'file'){
				$file = userpro_profile_data( $key, $user_id );
				if ($file){
				$value = '<div class="userpro-file-input"><a href="'.$file.'" '.userpro_file_type_icon($file).'>'.basename( $file ).'</a></div>';
				}
			}
		}
		
		
		
		/* display a field */
		if (!$user_id) $user_id = 0;
		if (isset($array['type']) && userpro_field_by_role( $key, $user_id ) && userpro_private_field_class($array)=='' && !empty($value) && userpro_field_is_viewable( $key, $user_id, $args )  && !in_array($key, $userpro->fields_to_hide_from_view() ) && $array['type'] != 'mailchimp' && $array['type'] != 'followers'  ) {
		$res .= "<div class='form-group userpro-field userpro-field-".$key." ".userpro_private_field_class($array)." userpro-field-$template' data-key='$key'>";
		
		if ( $array['label'] && $array['type'] != 'passwordstrength' ) {
		
		if ($args['field_icons'] == 1) {
		$res .= "<div class='col-sm-6 userpro-label-upl view iconed'>";
		} else {
		$res .= "<div class='col-sm-6 userpro-label-upl view'>";
		}
		$res .= "<label for='$key-$i' class='control-label'>".__($array['label'],'userpro')."";
		
			if ($args['field_icons'] == 1 && $userpro->field_icon($key)) {
				$res .= '<span class="userpro-field-icon"><i class="userpro-icon-'. $userpro->field_icon($key) .'"></i></span>';
			}
					
		$res .= ":</label></div>";
		
		}
		
		$res .= "<div class='col-sm-6'> <div class='userpro-input' id='firstname '.$key.' >";
		
		//***
			/* Before custom field is displayed!
			*/
		/**/
		
		$value = apply_filters('userpro_before_value_is_displayed', $value, $key, $array, $user_id);
		
		/* SHOW VALUE */
		$countrylist=get_option('userpro_fields');
		if(isset($countrylist['billing_country'])){
		$country=$countrylist['billing_country']['options'];
		}
		if ($key == 'role'){
			$res .= userpro_user_role($value);
		} 
		elseif($key=='billing_country')
		{
			
			foreach($country as $country_code => $country_name)
			{
					
				if($country_name==$value)
				{
					$res .= $value;
				}
				if($country_code==$value)
				{
					$value = $country_name;
					$res .= $value;
				}
				
				
			}
			
		}
		elseif($key=='shipping_country')
		{
				
			foreach($country as $country_code => $country_name)
			{

				if($country_name==$value)
				{
					$res .= $value;
				}
				if($country_code==$value)
				{
					$value = $country_name;
					$res .= $value;
				}
				
			}
				
		}
		else if($array['type'] == 'datepicker'){
		$format = '';
        $date_format = explode('-',userpro_get_option('date_format'));
        foreach($date_format as $f){
            if($f == 'yy'){
                $format .= substr(strtoupper($f), 1) . '-';
            }
            else{
                $format .= substr($f, 1) . '-';
            }
        }
        $format = rtrim($format, '-');
				try{
					$start = DateTime::createFromFormat($format, date($format));
        			$end   = DateTime::createFromFormat($format, $value);
					if(!isset($start->diff( $end )->y) ){
						$res .= $value;
					}
					else{
						$res  .= $start->diff( $end )->y;
					}
				}
				catch(Exception $ex){
					$res.= $value;
				}
		}
		else {
			$res .= $value;
		}
		/* hidden field notice */
		if (userpro_field_is_viewable($key, $user_id, $args) && ( userpro_profile_data( 'hide_'.$key, $user_id ) || userpro_field_default_hidden( $key, $template, $args[ $template . '_group' ] ) ) ) {
			$res .= '<div class="userpro-help">'.sprintf(__('(Your %s will not be visible to public)','userpro'), strtolower($array['label'])).'</div>';
		}
		 
		$res .= "<div class='userpro-clear'></div>";
		$res .= "</div>";
                
		$res .= "</div></div><div class='userpro-clear'></div>";
                
		
		}
                
?>		