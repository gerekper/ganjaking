<?php
/**
 * Admin taxonomy functions
 *
 *
 * @author 		Ashan Jay
 * @category 	Admin
 * @package 	eventon/Admin/Taxonomies
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class eventon_taxonomies{

	function __construct(){
		add_action( 'admin_init', array($this,'eventon_taxonomy_admin' ));
		add_action( 'event_type_pre_add_form', array($this, 'event_type_description' ));
		add_action( 'admin_init', array($this, 'eventon_add_tax') );

		// event type 1
		add_filter( 'manage_edit-event_type_columns', array($this,'event_type_edit_columns'),5 );
		add_filter( 'manage_event_type_custom_column', array($this,'event_type_custom_columns'),5,3 );

		// event type 2
		add_filter( 'manage_edit-event_type_2_columns', array($this,'event_type_edit_columns'),5 );
		add_filter( 'manage_event_type_2_custom_column', array($this,'event_type_custom_columns'),5,3 );

		add_action( 'event_type_add_form_fields', array($this,'evo_tax_add_new_meta_field_et1'), 10, 2 );

		add_action( 'event_type_edit_form_fields', array($this,'evo_tax_edit_new_meta_field_et1'), 10, 2 );
		add_action( 'edited_event_type', array($this,'evo_tax_save_new_meta_field_et1'), 10, 2 ); 
		add_action( 'create_event_type', array($this,'evo_tax_save_new_meta_field_et1'), 10, 2 );

		// event location
			add_filter("manage_edit-event_location_columns", array($this,'eventon_evLocation_theme_columns')); 
			add_filter("manage_event_location_custom_column", array($this,'eventon_manage_evLocation_columns'), 10, 3);
			add_action( 'event_location_add_form_fields', array($this,'eventon_taxonomy_add_new_meta_field'), 10, 2 );
	 		add_action( 'event_location_edit_form_fields', array($this,'eventon_taxonomy_edit_meta_field'), 10, 2 );
	 		add_action( 'edited_event_location', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );  
			add_action( 'create_event_location', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );
			add_action( 'event_location_edit_form', array($this,'loc_tax_footer'), 10, 2 );

		// event organizer
			add_filter("manage_edit-event_organizer_columns", array($this,'eventon_evorganizer_theme_columns'));  
			add_filter("manage_event_organizer_custom_column", array($this,'eventon_manage_evorganizer_columns'), 10, 3); 
			add_action( 'event_organizer_add_form_fields', array($this,'eventon_taxonomy_add_new_meta_field_org'), 10, 2 );
			add_action( 'event_organizer_edit_form_fields', array($this,'eventon_taxonomy_edit_meta_field_org'), 10, 2 );
			add_action( 'edited_event_organizer', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );  
			add_action( 'create_event_organizer', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );

	}

	// other settings
		function eventon_taxonomy_admin(){
			global $pagenow;
			if($pagenow =='edit-tags.php' && !empty($_GET['taxonomy']) 
				&& ($_GET['taxonomy']=='event_location' || 
				$_GET['taxonomy']=='event_organizer' )
				&& !empty($_GET['post_type']) 
				&& $_GET['post_type']=='ajde_events'){
				wp_enqueue_media();
			}


		}
		function event_type_description() {
			echo wpautop( __( 'Event Type Categories can be edited, deleted and updated in this page. <br/>More Information: <a href="http://www.myeventon.com/documentation/how-to-use-event-types-to-do-more/" target="_blank">Learn how to use event types to do more with eventON</a>', 'eventon' ) );
		}

	// event types
		function eventon_add_tax(){
			$options = get_option('evcal_options_evcal_1');
			for($x=3; $x <= evo_max_ett_count(); $x++){
				if(!empty($options['evcal_ett_'.$x]) && $options['evcal_ett_'.$x]=='yes'){
					add_filter( "manage_edit-event_type_{$x}_columns", array($this,'event_type_edit_columns'),5 );
					add_filter( "manage_event_type_{$x}_custom_column", array($this,'event_type_custom_columns'),5,3 );
				}
			}
		}

		// Columms
		function event_type_edit_columns($defaults){

			unset( $defaults['description']);

		    $defaults['cb'] = "<input type=\"checkbox\" />";
		    $defaults["name"] = __( 'Name', 'eventon' );
		    $defaults['event_type_id'] = __('ID');
		    return $defaults;
		} 
		function event_type_custom_columns($value, $column_name, $id){
			if($column_name == 'event_type_id'){
				$t_id = (int)$id;
				$term_meta = get_option( "evo_et_taxonomy_$t_id" );
				$term_color = (!empty($term_meta['et_color']))? 
					'<span class="evoterm_color" style="background-color:#'.$term_meta['et_color'].'"></span>':false;

				$term_icon = (!empty($term_meta['et_icon']))? 
					'<span class="evoterm_icon"><i class="fa '.$term_meta['et_icon'].'"></i></span>':false;
				?>
				<span class="term_id"><?php echo $t_id?></span><?php echo $term_color . $term_icon;?><span class='clear'></span>
				<?php
			}
		}

		// add term page
			function evo_tax_add_new_meta_field_et1() {
				// this will add the custom meta field to the add new term page
				?>
				<div class="form-field" id='evo_et1_color'>				
					<p class='evo_et1_color_circle' hex='bbbbbb'></p>
					<label for="term_meta[et_color]"><?php _e( 'Color', 'eventon' ); ?></label>
					<input type="hidden" name="term_meta[et_color]" id="term_meta[et_color]" value="">
					<p class="description"><?php _e( 'Pick a color','eventon' ); ?></p>
				</div>
				<?php 

				global $ajde;
				echo $ajde->wp_admin->icons();
				?>
				<div class="form-field " id='evo_evnet_type_icon'>				
					<p class='icon faicon'>
						<i class="ajde_icons default fa fa-circle-thin"></i> 
						<input type="hidden" name="term_meta[et_icon]" id="term_meta[et_icon]" value=""></p>				
					<p class="description"><?php _e( 'Select an Icon','eventon' ); ?></p>
				</div>
				<?php
			}
		// Edit term page
			function evo_tax_edit_new_meta_field_et1($term) {		 
				// put the term ID into a variable
					$t_id = $term->term_id;
				 
					// retrieve the existing value(s) for this meta field. This returns an array
					$term_meta = get_option( "evo_et_taxonomy_$t_id" ); 
					global $ajde;
					echo $ajde->wp_admin->icons();
				?>
				<tr class="form-field">
				<th scope="row" valign="top"><label for="term_meta[et_color]"><?php _e( 'Color', 'eventon' ); ?></label></th>
					<td id='evo_et1_color'>
						<?php $__this_value = !empty( $term_meta['et_color'] ) ? esc_attr( $term_meta['et_color'] ) : ''; ?>
						<p class='evo_et1_color_circle' hex='<?php echo $__this_value;?>' style='background-color:#<?php echo $__this_value;?>'></p>
						<input type="hidden" name="term_meta[et_color]" id="term_meta[et_color]" value="<?php echo $__this_value;?>">
						<p class="description"><?php _e( 'Pick a color','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
				<th scope="row" valign="top"><label for="term_meta[et_icon]"><?php _e( 'Icon', 'eventon' ); ?></label></th>
					<td id='evo_et1_color'>
						<?php $__this_value = ( !empty($term_meta['et_icon']) ) ? esc_attr( $term_meta['et_icon'] ) : ''; ?>
						<p class='icon faicon' >
							<i class="ajde_icons default fa <?php echo $__this_value;?>"></i> 
							<input type="hidden" name="term_meta[et_icon]" id="term_meta[et_icon]" value="<?php echo $__this_value;?>">
						</p>
						<p class="description"><?php _e( 'Select an Icon','eventon' ); ?></p>
					</td>
				</tr>
			<?php
			}
		// Save extra taxonomy fields callback function.
			function evo_tax_save_new_meta_field_et1( $term_id ) {
				if ( isset( $_POST['term_meta'] ) ) {
					$t_id = $term_id;
					$term_meta = get_option( "evo_et_taxonomy_$t_id" );
					$cat_keys = array_keys( $_POST['term_meta'] );
					foreach ( $cat_keys as $key ) {
						if ( isset ( $_POST['term_meta'][$key] ) ) {
							$term_meta[$key] = $_POST['term_meta'][$key];
						}
					}
					// Save the option array.
					update_option( "evo_et_taxonomy_$t_id", $term_meta );
				}
			}  

	// Get taxonomy terms list as array
		function get_event_tax_fields_array($tax, $event_tax_term=''){
			$is_new = (isset($_POST['type']) && $_POST['type']=='new')? true: false;

			if($tax == 'event_location'){
				return array(
					'term_name'=>array(
						'type'=>'text',
						'name'=> __('Location Name','eventon'),
						'placeholder'=>'eg. Irving City Park',
						'value'=> ($event_tax_term? $event_tax_term->name:''),
						'var'=>	'term_name',
						'legend'=> ($is_new?'':'NOTE: If you change the location name, it will create a new location.')
					),
					'description'=>array(
						'type'=>'textarea',
						'name'=>__('Location Description','eventon'),
						'var'=>'description',
						'value'=> ($event_tax_term? $event_tax_term->description:''),				
					),
					'location'=>array(
						'type'=>'text',
						'name'=>__('Location Address','eventon'),
						'placeholder'=>'eg. 12 Rue de Rivoli, Paris',
						'var'=>'location_address'				
					),
					'location_city'=>array(
						'type'=>'text',
						'name'=>__('Location City (Optional)','eventon'),
						'var'=>'location_city'				
					),
					'location_state'=>array(
						'type'=>'text',
						'name'=>__('Location State (Optional)','eventon'),
						'var'=>'location_state'				
					),
					'location_country'=>array(
						'type'=>'text',
						'name'=>__('Location Country (Optional)','eventon'),
						'var'=>'location_country'				
					),
					'evcal_lat'=>array(
						'type'=>'text',
						'name'=>__('Latitude','eventon'),	
						'var'=> 'location_lat'					
					),'evcal_lon'=>array(
						'type'=>'text',
						'name'=>__('Longitude','eventon'),
						'var'=> 'location_lon'					
					),
					'location_getdir_latlng'=>array(
						'type'=>'yesno',
						'name'=>__('Use Lat/Lng for get directions location','eventon'),
						'var'=> 'location_getdir_latlng'					
					),
					'evcal_location_link'=>array(
						'type'=>'text',
						'name'=>'Link for Location',	
						'var'=>'evcal_location_link'					
					),
					'evcal_location_link_target'=>array(
						'type'=>'yesno',
						'name'=>__('Open location link in new window','eventon'),
						'var'=> 'evcal_location_link_target'					
					),
					'evo_loc_img'=>array(
						'type'=>'image',
						'name'=>__('Location Image','eventon'),
						'var'=>	'evo_loc_img'	
					),
					'location_type'=>array(
						'type'=>'select',
						'name'=>'Location Type',	
						'var'=>'location_type',
						'options'=> array(
							'place'=> __('Physical Location','eventon'),
							'virtual'=> __('Virtual Location','eventon'),
						)					
					),
					'submit'=>array('type'=>'button',)
				);
			}

			if($tax == 'event_organizer'){
				return array(
					'term_name'=>array(
						'type'=>'text',
						'name'=>__('Organizer Name','eventon'),
						'placeholder'=>'eg. Electronic Entertainments',
						'value'=> ($event_tax_term? $event_tax_term->name:''),
						'var'=>	'term_name',
						'legend'=> ($is_new?'':'NOTE: If you change the organizer name, it will create a new organizer.')
					),
					'description'=>array(
						'type'=>'textarea',
						'name'=>__('Organizer Description','eventon'),
						'var'=>'description',
						'value'=> ($event_tax_term? $event_tax_term->description:''),				
					),
					'evcal_org_contact'=>array(
						'type'=>'text',
						'name'=>__('Organizer General Contact Information','eventon'),
						'var'=>'evcal_org_contact'				
					),
					'evcal_org_contact_e'=> array(
						'var'=> 'evcal_org_contact_e','type'=>'text',
						'name'=>__( 'Email Address', 'eventon' ),
						'desc'=>__( 'Enter Organizer Email Address','eventon' )
					),
					'evcal_org_address'=>array(
						'type'=>'text',
						'name'=>__('Organizer Address','eventon'),	
						'var'=> 'evcal_org_address'					
					),
					'evcal_org_fb'=> array(
						'var'=> 'evcal_org_fb','type'=>'text',
						'name'=>__( 'Facebook Link', 'eventon' ),
						'desc'=>__( 'Link to organizer facebook page','eventon' )
					),
					'evcal_org_tw'=> array(
						'var'=> 'evcal_org_tw','type'=>'text',
						'name'=>__( 'Twitter Link', 'eventon' ),
						'desc'=>__( 'Link to organizer Twitter page','eventon' )
					),
					'evcal_org_ln'=> array(
						'var'=> 'evcal_org_ln','type'=>'text',
						'name'=>__( 'Linkedin Link', 'eventon' ),
						'desc'=>__( 'Link to organizer Linkedin page','eventon' )
					),
					'evcal_org_yt'=> array(
						'var'=> 'evcal_org_yt','type'=>'text',
						'name'=>__( 'Youtube Link', 'eventon' ),
						'desc'=>__( 'Link to organizer Youtube page','eventon' )
					),
					'evcal_org_exlink'=>array(
						'var'=> 'evcal_org_exlink','type'=>'text',
						'name'=>__('Organizer Link','eventon'),
						'var'=> 'evcal_org_exlink'					
					),
					'_evocal_org_exlink_target'=>array(
						'type'=>'yesno',
						'name'=>__('Open link in new window','eventon'),	
						'var'=>'_evocal_org_exlink_target'					
					),
					'evo_org_img'=>array(
						'type'=>'image',
						'name'=>__('Organizer Image','eventon'),
						'var'=>	'evo_org_img'	
					),
					'submit'=>array('type'=>'button',)
				);
			}

		}

	// TAXONOMY - event location
		// remove some columns		
			function eventon_evLocation_theme_columns($theme_columns) {
			    $new_columns = array(
			        'cb' => '<input type="checkbox" />',
			        //'id' => __('ID','eventon'),
			        'name' => __('Location','eventon'),
			        'event_location_details' => __('Info','eventon'),
			        //'event_location' => __('Address','eventon'),
			        //'ev_lonlat' => __('Lon/Lat','eventon'),
			        'posts' => __('Count','eventon'),
					//      'description' => __('Description'),
			        'slug' => __('Slug'),
			    );			    
			    //return array_merge($theme_columns, $new_columns);
			    return $new_columns;
			}

		// Add event location address field 
			function eventon_manage_evLocation_columns($out, $column_name, $term_id) {
			    $term_meta = evo_get_term_meta( 'event_location',$term_id );
			    switch ($column_name) {
			        case 'event_location_details': 
			        	$term = get_term_by('id', $term_id, 'event_location'); 

			        	//get data
			        	$type = !empty($term_meta['location_type'])? $term_meta['location_type']:false;
			        	$imgID = !empty($term_meta['evo_loc_img'])? $term_meta['evo_loc_img']:false;
			        	$ADDRESS = !empty($term_meta['location_address']) ? 
			        		stripslashes(esc_attr( $term_meta['location_address'] )) : '-';

			        	$lon = (!empty($term_meta['location_lon']))? esc_attr( $term_meta['location_lon'] ) : false;
			        	$lat = (!empty($term_meta['location_lat']))? esc_attr( $term_meta['location_lat'] ) : false;	
			        	$locLink = (!empty($term_meta['evcal_location_link']))? esc_attr( $term_meta['evcal_location_link'] ) :false;

			        	// image
			        		$img_url = ($imgID)? wp_get_attachment_image_src($imgID,'thumbnail'):false;
			        		$imgHTML = ($img_url)? "<p class='evotax_location_image'><img src='{$img_url[0]}'/></p>":'';

			        	$out = $imgHTML;


			        	// location type
			        	if( $type == 'virtual'){
			        		$out .= "<span class='location_type'>". __('Virtual','eventon') ."</span>";
			        	}

			        	$out .= "<p class='evotax_location_info'>";

			        	if( $type != 'virtual') 
			        		$out .= "<b>".__('ADDRESS','eventon').':</b> '.$ADDRESS."<br/>";
			        	if($lon && $lat) 
			        		$out .= "<b>LAT/LON:</b> {$lat}/{$lon}<br/>";
			        	$out .= "<b>ID: </b>{$term_id} <br/>";

			        	if($locLink)
			        		$out .= "<b>LINK: </b>{$locLink} <br/>";

			        	$out .= "</p>";
			        break;
			        case 'event_location': 
			        	$out = "<p>".esc_attr( $term_meta['location_address'] ) ? esc_attr( $term_meta['location_address'] ) : ''."</p>";
			        break;
			        case 'ev_lonlat': 
			        	$lon = (!empty($term_meta['location_lon']))? esc_attr( $term_meta['location_lon'] ) : '-';
			        	$lat = (!empty($term_meta['location_lat']))? esc_attr( $term_meta['location_lat'] ) : '-';			        	
			        	$out = "<p>{$lon} / {$lat}</p>";
			        break;
			        case 'id': $out = $term_id; break;	

			       	default:
			            break;
			    }
			    return $out;    
			}
		// add term page
			function eventon_taxonomy_add_new_meta_field() {
				global $ajde;	 
				// this will add the custom meta field to the add new term page
				?>
				<div class="form-field">
					<label for="term_meta[location_address]"><?php _e( 'Location Address', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_address]" id="term_meta[location_address]" value="">
					<p class="description"><?php _e( 'Enter a location address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[location_lat]"><?php _e( 'Latitude', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_lat]" id="term_meta[location_lat]" value="">
					<p class="description"><?php _e( '(Optional) latitude for address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[location_lon]"><?php _e( 'Longitude', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_lon]" id="term_meta[location_lon]" value="">
					<p class="description"><?php _e( '(Optional) longitude for address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<p><span class='yesno_row evo'>
						<?php 	
						echo $ajde->wp_admin->html_yesnobtn(array(
							'id'=>'term_meta[location_getdir_latlng]', 
							'var'=> '',
							'input'=>true,
							'label'=>__('Use Lat/Lng for get directions location','eventon')
						));?>											
					</span></p>
				</div>
				<div>
					<p><?php _e('NOTE: LatLong will be auto generated for address provided for faster google map drawing. If location marker is not correct feel free to edit the LatLong values to correct location marker coordinates above. Location address field is REQUIRED for this to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat for address</a>','eventon');?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[evcal_location_link]"><?php _e( 'Location Link', 'eventon' ); ?></label>
					<input type="text" name="term_meta[evcal_location_link]" id="term_meta[evcal_location_link]" value="" placeholder='http://'>
					<p class="description"><?php _e( 'Enter a location link','eventon' ); ?></p>
					<p><span class='yesno_row evo'>
						<?php 	
						echo $ajde->wp_admin->html_yesnobtn(array(
							'id'=>'term_meta[evcal_location_link_target]', 
							'var'=> '',
							'input'=>true,
							'label'=>__('Open location link in new window','eventon')
						));?>											
					</span></p>
				</div>
				
				<div class="form-field evo_metafield_image">
					<label for="term_meta[evo_loc_img]"><?php _e( 'Image', 'eventon' ); ?></label>
					
					<input style='width:auto' class="custom_upload_image_button button <?php echo 'chooseimg';?>" data-txt='<?php echo __('Remove Image','eventon');?>' type="button" value="<?php _e('Choose Image','eventon');?>" /><br/>
					<span class='evo_loc_image_src image_src'><img src='' style='display:none'/></span>
					
					<input class='evo_loc_img evo_meta_img' type="hidden" name="term_meta[evo_loc_img]" id="term_meta[evo_loc_img]" value="">
					<p class="description"><?php _e( '(Optional) Location Image','eventon' ); ?></p>
				</div>

				<?php 
				// additional fields
					foreach($this->get_event_tax_fields_array('event_location') as $field=>$value){
						if(in_array($field, array('term_name','description','location', 'evcal_lat','evcal_lon','evcal_location_link','evo_loc_img','submit','location_getdir_latlng','evcal_location_link_target' ))) continue;

						?>
						<div class="form-field">
							<label for="term_meta[<?php echo $field;?>]"><?php echo $value['name']; ?></label>
							<input type="text" name="term_meta[<?php echo $field;?>]" id="term_meta[<?php echo $field;?>]" value="">
						</div>
						<?php
					}

				?>
				<div class="form-field ">
					<label for="term_meta[location_type]"><?php _e( 'Location Type', 'eventon' ); ?></label>
					<select type="text" name="term_meta[location_type]" id="term_meta[location_type]">
						<option value='place'><?php _e('Physical Location','eventon');?></option>
						<option value='virtual'><?php _e('Virtual Location','eventon');?></option>
					</select>
				</div>

				<?php

			}

		// edit tag page footer
			function loc_tax_footer($tag, $tax){
				echo "<p><a class='evo_admin_btn' href='".get_site_url().'/event-location/'.$tag->slug."'>".__('VIEW','eventon')."</a></p>";
			}
		
		// Edit term page
			function eventon_taxonomy_edit_meta_field($term) {
			 	
			 	global $ajde;	 

				// put the term ID into a variable
				$t_id = $term->term_id;
			 
				// retrieve the existing value(s) for this meta field. This returns an array
				
				$term_meta = evo_get_term_meta('event_location',$t_id);
				//$term_meta = get_option( "taxonomy_$t_id" ); 

				?>
				
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_address]"><?php _e( 'Location Address', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_address]" id="evo_admin_location_address" value="<?php echo !empty($term_meta['location_address'] ) ? esc_attr( stripslashes($term_meta['location_address']) ) : ''; ?>">
						<p class="description"><?php _e( 'Enter a location address','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_lat]"><?php _e( 'Latitude', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_lat]" id="term_meta[location_lat]" value="<?php echo  !empty($term_meta['location_lat']) ? esc_attr( $term_meta['location_lat'] ) : ''; ?>">
						<p class="description"><?php _e( '(Optional) latitude for address','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_lon]"><?php _e( 'Longitude', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_lon]" id="term_meta[location_lon]" value="<?php echo !empty($term_meta['location_lon']) ? esc_attr( $term_meta['location_lon'] ) : ''; ?>">
						<p class="description"><?php _e( '(Optional) longitude for address','eventon' ); ?></p>

						<div style='padding-top:20px'>
							<?php if( EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1')):?>
								<p><?php _e('<b>NOTE:</b> LatLong will be auto generated for address provided for faster google map drawing. If location marker is not correct feel free to edit the LatLong values to correct location marker coordinates above. Location address field is REQUIRED for this to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat Coordinates for address in here</a>','eventon');?></p>
								<p style='padding-top:10px'><a class='evo_auto_gen_latlng evo_admin_btn'><?php _e('Generate Location Coordinates','eventon');?></a></p>

							<?php else:?>
								<p><?php _e('<b>NOTE:</b> You must set Google Maps API key via EventON Settings > Google Maps API for auto generation of location coordinates to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat Coordinates for address in here</a>','eventon');?></p>
							<?php endif;?>
						</div>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_getdir_latlng]"><?php _e('Use Lat/Lang for get location directions','eventon'); ?></label></th>
					<td>
						<p><span class='yesno_row evo'>
							<?php 	
							$location_getdir_latlng = $this->termmeta($term_meta,'location_getdir_latlng');
							echo $ajde->wp_admin->html_yesnobtn(array(
								'id'=>'term_meta[location_getdir_latlng]', 
								'var'=>$location_getdir_latlng,
								'input'=>true,
								'label'=>	__('Use Lat/Lang for get location directions' ,'eventon')
							));?>											
						</span></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[evcal_location_link]"><?php _e( 'Location Link', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[evcal_location_link]" id="term_meta[evcal_location_link]" value="<?php echo !empty($term_meta['evcal_location_link']) ? esc_attr( $term_meta['evcal_location_link'] ) : ''; ?>" placeholder='http://'>
						<p><span class='yesno_row evo'>
							<?php 	
							$evcal_location_link_target = $this->termmeta($term_meta,'evcal_location_link_target');
							echo $ajde->wp_admin->html_yesnobtn(array(
								'id'=>		'term_meta[evcal_location_link_target]', 
								'var'=>		$evcal_location_link_target,
								'input'=>	true,
								'label'=>	__('Open location link in new window','eventon')
							));?>											
						</span></p>

						
						<p class="description"><?php _e( '(Optional) Location Link','eventon' ); ?></p>
					</td>
				</tr>
				
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[evo_loc_img]"><?php _e( 'Image', 'eventon' ); ?></label></th>
					<td class='evo_metafield_image'>
						<?php 
							if(!empty($term_meta['evo_loc_img'])){
								$img_url = wp_get_attachment_image_src($term_meta['evo_loc_img'],'medium');
							}else{ $img_url=false;}

							$__button_text = (!empty($term_meta['evo_loc_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
							$__button_text_not = (empty($term_meta['evo_loc_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
							$__button_class = (!empty($term_meta['evo_loc_img']))? 'removeimg':'chooseimg';
						?>
						
						<input style='width:auto' class="custom_upload_image_button button <?php echo $__button_class;?>" data-txt='<?php echo $__button_text_not;?>' type="button" value="<?php echo $__button_text;?>" /><br/>
						<span class='evo_loc_image_src image_src'><img src='<?php echo $img_url[0];?>' style='<?php echo !empty($term_meta['evo_loc_img'])?'':'display:none';?>'/></span>
						
						<input class='evo_loc_img evo_meta_img' type="hidden" name="term_meta[evo_loc_img]" id="term_meta[evo_loc_img]" value="<?php echo !empty( $term_meta['evo_loc_img'] ) ? esc_attr( $term_meta['evo_loc_img'] ) : ''; ?>">
						<p class="description"><?php _e( '(Optional) Location Image','eventon' ); ?></p>
					</td>
				</tr>
				
			<?php 
				// additional fields
					foreach($this->get_event_tax_fields_array('event_location') as $field=>$value){
						if(in_array($field, array('term_name','description','location', 'evcal_lat','evcal_lon','evcal_location_link','evo_loc_img','submit','location_getdir_latlng','evcal_location_link_target' ))) continue;

						?>
						<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[<?php echo $field;?>]"><?php echo $value['name']; ?></label></th>
							<td>
								<input type="text" name="term_meta[<?php echo $field;?>]" id="term_meta[<?php echo $field;?>]" value="<?php echo !empty($term_meta[$field]) ? esc_attr( $term_meta[$field] ) : ''; ?>">
							</td>
						</tr>
						<?php
					}

				?>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_type]"><?php _e( 'Location Type', 'eventon' ); ?></label></th>
					<td>
						<select type="text" name="term_meta[location_type]" id="term_meta[location_type]">
							<option value='place' <?php echo (!empty($term_meta['location_type']) && $term_meta['location_type'] == 'place')? 'selected':'';?>><?php _e('Physical Location','eventon');?></option>
							<option value='virtual' <?php echo (!empty($term_meta['location_type']) && $term_meta['location_type'] == 'virtual')? 'selected':'';?>><?php _e('Virtual Location','eventon');?></option>
						</select>						
					</td>
				</tr>

				<?php
			}
				
	// TAXONOMY Event Organizer
		// remove some columns
			function eventon_evorganizer_theme_columns($theme_columns) {
			    $new_columns = array(
			        'cb' => '<input type="checkbox" />',
			        'id' => __('ID','eventon'),
			        'name' => __('Organizer','eventon'),
			        //'names' => __('Organizer','eventon'),
			        'contact' => __('Contact Info','eventon'),
			        'posts' => __('Count','eventon'),
					//      'description' => __('Description'),
			        'slug' => __('Slug')
			        );
			    return $new_columns;
			}
		// Add event organizer columns
			function eventon_manage_evorganizer_columns($out, $column_name, $term_id) {
			  	$term_meta = evo_get_term_meta( 'event_organizer', $term_id );
			    switch ($column_name) {
			        case 'contact': 
			        	$address = !empty($term_meta['evcal_org_address'])? 
			        		stripslashes(esc_attr( $term_meta['evcal_org_address'] )): false;
			        	$contact = !empty($term_meta['evcal_org_contact'])? 
			        		stripslashes(esc_attr( $term_meta['evcal_org_contact'] )): false;
			        	$out = "<p>".$contact.$address."</p>";
			        break;
			        case 'id': 
			        	$out = $term_id;
			        break;
			        case 'names':
			        	ob_start();

			        	$out = ob_get_clean();
			        break;			       
			        default:
			            break;
			    }
			    return $out;    
			}
		// add term page
			function eventon_taxonomy_add_new_meta_field_org() {
				// this will add the custom meta field to the add new term page


				// additional fields
					foreach($this->get_event_tax_fields_array('event_organizer') as $field=>$value){

						if( in_array( $field , array('_evocal_org_exlink_target','submit') ) ) continue;
						
						if($value['type'] == 'image'):
						?>
							<div class="form-field evo_metafield_image">
								<label for="term_meta[evo_org_img]"><?php _e( 'Image', 'eventon' ); ?></label>
								
								<input style='width:auto' class="custom_upload_image_button button <?php echo 'chooseimg';?>" data-txt='<?php echo __('Remove Image','eventon');?>' type="button" value="<?php _e('Choose Image','eventon');?>" /><br/>
								<span class='evo_org_image_src image_src'><img src='' style='display:none'/></span>
								
								<input class='evo_org_img evo_meta_img' type="hidden" name="term_meta[evo_org_img]" id="term_meta[evo_org_img]" value="">
								<p class="description"><?php _e( '(Optional) Organizer Image','eventon' ); ?></p>
							</div>
						<?php else:?>
							<div class="form-field">
								<label for="term_meta[<?php echo $field;?>]"><?php echo $value['name']; ?></label>
								<input type="text" name="term_meta[<?php echo $field;?>]" id="term_meta[<?php echo $field;?>]" value="">
							</div>
						<?php
						endif;
					}

				do_action('evo_organizer_add_term_fields');?>
				
			<?php
			}
		// Edit term
			function eventon_taxonomy_edit_meta_field_org($term) {	

				global $ajde;	 
				// put the term ID into a variable
				$t_id = $term->term_id;
			 
				// retrieve the existing value(s) for this meta field. This returns an array
				$term_meta = evo_get_term_meta( 'event_organizer', $t_id );
				
				
				//  fields
					foreach($this->get_event_tax_fields_array('event_organizer') as $field=>$value){

						if($field == '_evocal_org_exlink_target') continue;
						if($field == 'submit') continue;
						
						if($field == 'evcal_org_exlink'):
						?>
							<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[evcal_org_exlink]"><?php _e( 'Link to the organizers page', 'eventon' ); ?></label></th>
							<td>
								<input type="text" name="term_meta[evcal_org_exlink]" id="term_meta[evcal_org_exlink]" value="<?php echo !empty($term_meta['evcal_org_exlink'])  ? esc_attr( $term_meta['evcal_org_exlink'] ) : ''; ?>">
								<p><span class='yesno_row evo'>
									<?php 	
									$_evocal_org_exlink_target = $this->termmeta($term_meta,'_evocal_org_exlink_target');
									echo $ajde->wp_admin->html_yesnobtn(array(
										'id'=>'term_meta[_evocal_org_exlink_target]', 
										'var'=>$_evocal_org_exlink_target,
										'input'=>true,
										'label'=>__('Open organizer link in new window','eventon')
									));?>											
								</span></p>

								<p class="description"><?php _e( 'Use this field to link organizer to other user profile pages','eventon' ); ?></p>
							</td>
							</tr>

						<?php continue; endif;?>

						<?php if($value['type'] == 'image'):?>
							<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[evo_org_img]"><?php _e( 'Image', 'eventon' ); ?></label></th>
							<td class='evo_metafield_image'>
								<?php 
									if(!empty($term_meta['evo_org_img'])){
										$img_url = wp_get_attachment_image_src($term_meta['evo_org_img'],'medium');
									}else{ $img_url=false;}

									$__button_text = (!empty($term_meta['evo_org_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
									$__button_text_not = (empty($term_meta['evo_org_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
									$__button_class = (!empty($term_meta['evo_org_img']))? 'removeimg':'chooseimg';
								?>						
								<input style='width:auto' class="custom_upload_image_button button <?php echo $__button_class;?>" data-txt='<?php echo $__button_text_not;?>' type="button" value="<?php echo $__button_text;?>" /><br/>
								<span class='evo_org_image_src image_src'><img src='<?php echo $img_url[0];?>' style='<?php echo !empty($term_meta['evo_org_img'])?'':'display:none';?>'/></span>
								
								<input class='evo_org_img evo_meta_img' type="hidden" name="term_meta[evo_org_img]" id="term_meta[evo_org_img]" value="<?php echo !empty( $term_meta['evo_org_img'] ) ? esc_attr( $term_meta['evo_org_img'] ) : ''; ?>">
								<p class="description"><?php _e( '(Optional) Organizer Image','eventon' ); ?></p>
							</td>
							</tr>

						<?php continue; endif;?>

						<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[<?php echo $field;?>]"><?php echo $value['name']; ?></label></th>
							<td>
								<input type="text" name="term_meta[<?php echo $field;?>]" id="term_meta[<?php echo $field;?>]" value="<?php echo !empty($term_meta[$field]) ? esc_attr( $term_meta[$field] ) : ''; ?>">
							</td>
						</tr>
						<?php
					}

				do_action('evo_organizer_edit_term_fields', $t_id, $term_meta);?>				
			<?php
			}
		
	// Save extra taxonomy fields callback function.
		function evo_save_taxonomy_custom_meta( $term_id , $oo) {
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id = $term_id;

				$taxonomy = $_REQUEST['taxonomy'];
				
				//$term_meta = get_option( "taxonomy_$t_id" );
				$term_meta = array();
				
				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ) {

					if( in_array($key, array('location_lon','location_lat')) ) continue;

					if($key=='location_address'){
						// location lat long override
						if($key == 'location_address' && empty($_POST['term_meta']['location_lon'] )){
							$latlon = eventon_get_latlon_from_address($_POST['term_meta']['location_address']);
						}
						// longitude
						$term_meta['location_lon'] = (!empty($_POST['term_meta']['location_lon']))?
							$_POST['term_meta']['location_lon']:
							(!empty($latlon['lng'])? floatval($latlon['lng']): null);

						// latitude
						$term_meta['location_lat'] = (!empty($_POST['term_meta']['location_lat']))?
							$_POST['term_meta']['location_lat']:
							(!empty($latlon['lat'])? floatval($latlon['lat']): null);						
					}

					$term_meta[$key] = (isset($_POST['term_meta'][$key]))?
						$_POST['term_meta'][$key]:null;

				}

				

				// Save the option array.
				// /update_option( "taxonomy_$t_id", $term_meta );
				evo_save_term_metas($taxonomy, $t_id, $term_meta);
			}
		}  

	// Supporting functions
		function termmeta($term_meta, $var){
			return !empty( $term_meta[$var] ) ? 
				stripslashes(str_replace('"', "'", (esc_attr( $term_meta[$var] )) )) : 
				null;
		}
}
	
?>