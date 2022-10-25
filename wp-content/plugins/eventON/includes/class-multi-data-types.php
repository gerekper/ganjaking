<?php
/**
 * Multi Data Types Class
 * @version 4.2
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class evo_mdt{
	public $opt;
	public function __construct(){
		$this->opt = get_option('evcal_options_evcal_1');

		add_action('admin_init', array($this, 'admin_init'));
		// /add_action('init', array($this, 'init'));

		// register MDT
		add_action('eventon_register_taxonomy', array($this, 'register'), 10);

		// frontend boxes
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
		
		for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
			add_filter('eventon_eventCard_evomdt_'.$x, array($this, 'frontend_box'), 10, 2);
		}
	}

	function admin_init(){
		add_filter('eventon_event_metaboxs',array($this, 'event_metabox'), 10, 2);
		add_action('eventon_save_meta', array($this, 'save_event_post'), 10, 2);
		
		add_action( 'eventon_eventcard_boxes', array( $this, 'eventCard_inclusion' ), 10,1 );
		add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);

		// taxonomy connect
		add_filter( 'evo_taxonomy_form_fields_array',array($this, 'form_field_array') , 10, 3);
		add_filter( 'evo_tax_translated_names',array($this, 'human_tax_name') , 10, 2);

	}

	// Register
		function register(){
			// Each activated multi data types
			$evo_get_mdt_names = $this->evo_get_mdt_names($this->opt);

			$__capabilities = array(
				'manage_terms' 		=> 'manage_eventon_terms',
				'edit_terms' 		=> 'edit_eventon_terms',
				'delete_terms' 		=> 'delete_eventon_terms',
				'assign_terms' 		=> 'assign_eventon_terms',
			);

			for($x=1; $x<= $this->evo_get_mdt_count($this->opt); $x++){
				$mdt_name = $evo_get_mdt_names[$x];

				register_taxonomy( 'multi_data_type_'.$x, 
					apply_filters( 'eventon_taxonomy_objects_mdt'.$x, array('ajde_events') ),
					apply_filters( 'eventon_taxonomy_args_mdt'.$x, array(
						'hierarchical' 			=> false, 
						'label'	 				=> $mdt_name,
						'show_ui' => true,
						'show_in_menu'=>true,
						'show_in_nav_menu'=>true,
						'show_tagcloud'=>false,
						'show_admin_column'=>false,
						'show_in_quick_edit'         => false,
	    				'meta_box_cb'                => false,
						'query_var' => true,
						'capabilities'			=> $__capabilities,
						'rewrite' => array( 'slug' => 'multi-data-type-'.$x ) 
					)) 
				);
			}
		}

	// Frontend
		function eventCard_inclusion($array){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array['evomdt_'.$x]= array( 'evomdt_'.$x, $mdt_name[$x]);
			}
			return $array;
		}
		function custom_icons($array){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array[] = array('id'=>'evcal__evomdt_'.$x,'type'=>'icon','name'=> $mdt_name[$x].' Icon','default'=>'fa-list');
			}
			return $array;
		}
		function frontend_box($object, $helpers){

			$x = $object->x;
			$mdt_name = $this->evo_get_mdt_names();
			$terms = wp_get_post_terms($object->event_id, $object->tax);

			if ( $terms && ! is_wp_error( $terms ) ):
			ob_start();
			echo  "<div class='evo_metarow_mdt_{$x} evo_metarow_mdt evorow evcal_evdata_row evcal_evrow_sm".$helpers['end_row_class']."' data-event_id='".$object->event_id."'>
					<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evomdt_'.$x, 'fa-list',$helpers['evOPT'] )."'></i></span>
					<div class='evcal_evdata_cell'>";
				echo "<h3 class='evo_h3'>".evo_lang($mdt_name[$x])."</h3>";

				if(!empty($object->pmv['_evomdt_subheader_'.$x]))
					echo "<p class='evomdt_subtitle'>".$object->pmv['_evomdt_subheader_'.$x][0]."</p>";

				echo "<div class='evomdt_data grid'>";
				// each term
				$tax_data = $this->get_mdt_term_data( $object->tax );
				foreach($terms as $term){
					echo "<div class='evomdt_item'>";
					$img_attr = '';
					if(!empty($tax_data[$term->term_id]['image']))
						$img_attr = wp_get_attachment_image_src( $tax_data[$term->term_id]['image'], 'full' );

					$img = !empty($img_attr)? "<img src='".$img_attr[0]."'/>":'';

					echo $img;
					echo "<h4 class='evo_h4'>".$tax_data[$term->term_id]['name'].'</h4>';
					echo apply_filters('the_content',$tax_data[$term->term_id]['description']);

					// additional data fields
					$this->additional_field_values($object->tax, $tax_data , $term->term_id);

					echo "</div>";
				}
				echo "</div>";

			echo "</div>";
			echo "</div>";

			return ob_get_clean();
			endif;
		}
		function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array['evomdt_'.$x]= array(
					'event_id' => $eventid,
					'pmv'=>$pmv,
					'x'=>$x,
					'tax'=>'multi_data_type_'.$x,
					'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
				);
			}
			return $array;
		}
		function eventcard_adds($array){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array[] = 'evomdt_'.$x;
			}
			return $array;
		}

		function additional_field_values($tax, $tax_data, $termid){

			$mdt_index = $this->tax_index($tax);

			for( $z=1; $z <= $this->evo_max_mdt_addfield_count(); $z++){
				$postfix = $mdt_index. '_' .$z;
				if( evo_settings_check_yn($this->opt , 'evcal_mdta_'.$postfix) &&
					!empty( $this->opt[ 'evcal_mdta_name_'.$postfix ]) &&
					!empty( $tax_data[$termid]['evcal_mdta_'.$postfix])
				){	
					echo "<p><span>" . $this->opt[ 'evcal_mdta_name_'.$postfix] . "</span>";

					// link filtering
					if( strtolower(substr($tax_data[$termid]['evcal_mdta_'.$postfix],0,4)) == 'http' ){
                        echo '<a href="' . stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]) . '" target=_blank>' . stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]) . "</a>";
					}else{
						echo stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]);
					}
                   

					echo "</p>";
				}
			}
		}	

	// Event Post meta box		
		function event_metabox($array, $EVENT){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){	
				$icon = get_eventON_icon('evcal__evomdt_'.$x, 'fa-list',$this->opt );			
				$array[] = array(
					'id'=>'ev_mdt_'.$x,
					'name'=> __('Multi data type','eventon')  .' / '. $mdt_name[$x] ,
					'variation'=>'customfield',	
					'hiddenVal'=>'',	
					'iconURL'=>$icon,
					'iconPOS'=>'',
					'type'=>'code',
					'content'=>$this->content($mdt_name[$x], 'multi_data_type_'.$x, $x, $EVENT),
					'slug'=>'ev_mdt_1'
				);
			}

			return $array;			
		}
		function content($name, $tax, $x, $EVENT){
			
			ob_start();
			?>
			<div class='evcal_data_block_style1'>
				<div class='evcal_db_data'>
					<p>
						<input type="text" id="evcal_subheader_<?php echo $x;?>" name="_evomdt_subheader_<?php echo $x;?>" value="<?php echo $EVENT->get_prop('_evomdt_subheader_'.$x);?>" style="width:100%"/>
						<label for="evcal_lmlink_target"><?php _e('Section subtitle text','eventon');?></label>	
					</p>

					<div class='evo_singular_tax_for_event <?php echo $tax;?>' >
					<?php 

					echo EVO()->taxonomies->get_meta_box_content( $tax, $EVENT->ID);
					?>
					</div>
				</div>
			</div>
			<?php 

			return ob_get_clean();
		}
		function save_event_post($fields, $post_id){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){	
				if(isset($_POST['_evomdt_subheader_'.$x])){
					update_post_meta($post_id, '_evomdt_subheader_'.$x, $_POST['_evomdt_subheader_'.$x]);	
				}else{
					delete_post_meta($post_id, '_evomdt_subheader_'.$x);
				}
			}
		}

		// add meta data fields to tax array
		function form_field_array($array, $tax, $event_tax_term){

			if( strpos($tax, 'multi_data_type_') === false ) return $array;
						
			$mdt_index = $this->tax_index($tax);

			$array[$tax ] = array(
				'term_name'=>array(
					'type'=>'text',
					'name'=> __('Name','eventon'),
					'value'=> ($event_tax_term? $event_tax_term->name:''),
					'var'=>	'term_name'
				),
				'description'=> array(
					'type'=>'textarea',
					'name'=>__('Description','eventon'),
					'var'=>'description',
					'value'=> ($event_tax_term? $event_tax_term->description:''),				
				),
			);

			// image field
				if( evo_settings_check_yn($this->opt , 'evcal_mdt_img'.$mdt_index) ){
					$array[$tax ]['evcal_mdt_img'.$mdt_index] = array(
						'type'=>'image',
						'name'=>__('Image','eventon'),
						'var'=>	'evcal_mdt_img'.$mdt_index
					);
				}

			// foreach additional fields
				for( $z=1; $z <= $this->evo_max_mdt_addfield_count(); $z++){
					$postfix = $mdt_index. '_' .$z;
					if( evo_settings_check_yn($this->opt , 'evcal_mdta_'.$postfix) &&
						!empty( $this->opt[ 'evcal_mdta_name_'.$postfix ])
					){
						$array[$tax ]['evcal_mdta_'.$postfix] = array(
							'type'=>'text',
							'name'=>$this->opt[ 'evcal_mdta_name_'.$postfix],	
							'var'=>'evcal_mdta_'.$postfix					
						);
					}
				}

				$array[$tax ]['submit']=array('type'=>'button');


			return $array;

		}

		function human_tax_name($array, $tax){
			if( strpos($tax, 'multi_data_type_') === false ) return $array;

			$mdt_index = $this->tax_index($tax);

			$array[ $tax ] = $this->get_mdt_name($mdt_index );

			return $array;
		}


	// Supportive
		function tax_index($tax){
			$mdt_index = explode('_', $tax);
			return $mdt_index[3];
		}
		function evo_max_mdt_count(){
			return apply_filters('evo_multi_data_type_count',3);
		}
		// this return the count for each multi data type that are activated in accordance
		function evo_get_mdt_count($evopt=''){
			$evopt = (!empty($evopt))? $evopt: $this->opt;

			$maxnum = $this->evo_max_mdt_count();
			$count=0;
			for($x=1; $x<= $maxnum; $x++ ){
				if(!empty($evopt['evcal_mdt_'.$x]) && $evopt['evcal_mdt_'.$x]=='yes'){
					$count = $x;
				}else{	break;	}
			}
			return $count;
		}
		function evo_get_mdt_names($options=''){
			$output = array();

			$options = (!empty($options))? $options: $this->opt;
			for( $x=1; $x <= $this->evo_max_mdt_count($options); $x++){

				$pretext = (!empty($options['evcal_mdt_name_'.$x ]))? 
					$options['evcal_mdt_name_'.$x ]:'Multi Data Type '.$x;

				$output[$x] = evo_lang_get('multi-data-type-'.$x, $pretext);
			}
			return $output;
		}
		function get_mdt_name( $mdt_index){
			$options = $this->opt;
			$pretext = (!empty($options['evcal_mdt_name_'.$mdt_index ]))? 
					$options['evcal_mdt_name_'.$mdt_index ]:
					'Multi Data Type '.$mdt_index;

			return evo_lang_get('multi-data-type-'. $mdt_index , $pretext);
		}
		function evo_max_mdt_addfield_count(){
			return apply_filters('evo_multi_data_type_fields_count',2);
		}
		function get_mdt_term_data($tax){
			$output = array();
			$terms = get_terms($tax, array('hide_empty'=>false));
			if ( $terms && ! is_wp_error( $terms ) ){
				
				$fields = $this->fields_of_mdt($tax);
				
				foreach($terms as $term){

					$termmeta = evo_get_term_meta($tax,$term->term_id);

					$output[$term->term_id]['name'] = $term->name;
					$output[$term->term_id]['description'] = $term->description;

					// each additional data field
					foreach($fields as $field=>$val){
						if(in_array($field, array('name','description'))) continue;
						if(empty($termmeta[$field])) continue;
						$output[$term->term_id][$field] = $termmeta[$field];
					}
				}
			}
			return $output;
		}
		function fields_of_mdt($tax='multi_data_type_1'){

			$mdt_index = $this->tax_index($tax);

			$base = array(
				'name'=>array('Name','text'),
				'description'=> array('Description','textarea'),
			);

			// image field
				if( evo_settings_check_yn($this->opt , 'evcal_mdt_img'.$mdt_index) ){
					$base['image'] = array('Image','image');
				}

			// foreach additional fields
				for( $z=1; $z <= $this->evo_max_mdt_addfield_count(); $z++){
					$postfix = $mdt_index. '_' .$z;
					if( evo_settings_check_yn($this->opt , 'evcal_mdta_'.$postfix) &&
						!empty( $this->opt[ 'evcal_mdta_name_'.$postfix ])
					){
						$base['evcal_mdta_'.$postfix] = array(
							$this->opt[ 'evcal_mdta_name_'.$postfix],
							'text',
							'norequired'
						);
					}
				}
			return $base;
		}
}