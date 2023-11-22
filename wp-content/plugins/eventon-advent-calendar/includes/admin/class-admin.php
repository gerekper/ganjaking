<?php
/**
 * Admin
 * @version 0.1
 */
class EVOAD_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));	
		add_action( 'admin_menu', array( $this, 'menu' ),9);	
		add_action('evo_admin_all_wp_admin_scripts', array($this, 'admin_styles'));	
	}

	function admin_init(){
		include_once('class-post_meta.php');
		
		// settings
		add_filter('eventon_settings_tabs',array($this, 'tab_array' ),10, 1);
		add_action('eventon_settings_tabs_evcal_ad',array($this, 'tab_content' ));	
		add_filter( 'evo_addons_details_list', array( $this, 'eventon_addons_list' ), 10, 1 );

		// language
		add_filter('eventon_settings_lang_tab_content', array($this,'langs'), 10, 1);

		// eventCard inclusion
		add_filter( 'eventon_eventcard_boxes',array($this,'eventCard_inclusion') , 10, 1);
		add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);

		// appearance
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
	
	}

	// menu
		function menu(){
			add_submenu_page( 'eventon', 'Advent', __('Advent','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_ad', '' );
		}

	// addon details
		function eventon_addons_list($default){
			$default['eventon-advent-calendar'] = array(
				'id'=> EVOAD()->addon_id,
				'name'=> EVOAD()->name,
				'link'=>'https://www.myeventon.com/addons/advent-calendar/',
				'download'=>'https://www.myeventon.com/addons/advent-calendar/',
				'desc'=>'Convert eventON into an advent calendar',
			);
			return $default;
		}
		
	// styles
		public function admin_styles(){
			wp_enqueue_style( 'evoad_wp_admin', EVOAD()->assets_path.'evoad_admin_styles.css',array(), EVO()->version);
		}

	// language
		function langs($_existen){

			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Advent Calendar'),
						array('label'=>'Active Now','var'=>1),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;

		}

	// Settings
		public function eventCard_inclusion($array){
			$array['evoad']= array('evoad',__('Advent Event Box','eventon'));
			return $array;
		}
		public function custom_icons($array){
			$array[] = array('id'=>'evcal__evoad_001','type'=>'icon','name'=>'Advent Event Icon','default'=>'fa-snowflake');
			return $array;
		}
		function tab_array($evcal_tabs){
			$evcal_tabs['evcal_ad']='Advent';		
			return $evcal_tabs;
		}

		function tab_content(){
			
			EVO()->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evoau_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_csv" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php

					$cutomization_pg_array = apply_filters('evoad_settings_fields',array(
						
						array(
							'id'=>'evoad1',
							'name'=>'EventCard Hidden Advent Event Fields',
							'display'=>'show',
							'tab_name'=>'Form Fields','icon'=>'briefcase',
							'fields'=>array(

								array(
									'id'=>'evoad_hidden_fields', 
									'type'=>'customcode',
									'code'=> $this->code()
								),
								
								array('id'=>'evoad_hidden_ev_msg','type'=>'text',
									'name'=>'Message to show for un-revealed advent events',
									'legend'=>'This message will appear on eventcard for the future advent events that are not revealed yet.',
								),
								array('id'=>'evoad_notif','type'=>'note',
									'name'=>'NOTE: Above selected hidden fields will be visible to viewer once the advent event date arrive.'
								),
						)),
					));		

					EVO()->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_ad'); 
					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
			</div>			
			</form>	
		<?php
		}

		public function code(){
			ob_start();
			
			$settings = new evo_cal_help();
			$fields = $settings->get_eventcard_fields();

			// make sure to load new version of the settings
			EVO()->cal->reload_option_data('evcal_ad');
			$fields_status = EVO()->cal->get_prop('evoad_field_status', 'evcal_ad');
			
			if(!$fields_status) $fields_status = array();

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.evoad_multiselect_fields').on('click','em',function(){
						const O = $(this);
						const value = O.data('v');
						

						$(this).toggleClass('select');
						$(this).siblings().removeClass('select');				

						// update input
						$(this).siblings('input').val( value );
					});
				});
			</script>
			<p><b><?php _e('Select when to HIDE the below eventcard fields.');?></b> <br/><i><?php _e('NOTE: Please refer to this as a guide to selecting below fields. NEVER = Never hide the field. BEFORE = Hide before the reveal date. AFTER = Hide after the event was revealed. BEFORE/AFTER = Hide the fields before and after the event is revealed on current date. ALWAYS = Hide the field at all times. OTHER: Event & Event fields are revealed when event date is current date.');?></i></p>
			
			<div class='evoad_multiselect_fields' style="padding-top: 5px;" data-d=''>
				<?php

				
				foreach($fields as $key=>$data){

					if( !isset($fields_status[ $key ]) ) $fields_status[ $key ] = 'never';

					echo "<p data-f='{$key}'><span>";

					$val = 'never';
					foreach( array(
						'never','before','after', 'before/after','always'
					) as $vv){

						// select
						$sel = ( $fields_status[ $key ] == $vv ) ? 'select':'';
						if( $fields_status[ $key ] == $vv ) $val = $vv;

						echo "<em data-v='{$vv}' class='{$sel}'>{$vv}</em>";
					}

					echo "<input type='hidden' name='evoad_field_status[{$key}]' value='{$val}'/>";
					
					echo "</span> {$data} </p>";
				}

				?>			
			</div>
			<?php
			return ob_get_clean();
		}
		function fields_array(){

			$settings = new evo_cal_help();
			$fields = $settings->get_eventcard_fields();

			$output = array();
			foreach($fields as $key=>$data){
				$output[ $key] = $data[1];
			}


			return $output;
		}
	
	// Appearance
		function appearance_settings($array){			
			$new[] = array('id'=>'evoad','type'=>'hiddensection_open',
				'name'=>__('Advent Calendar Styles','evoad'), 'display'=>'none');
			$new[] = array('id'=>'evoad','type'=>'fontation','name'=>__('Row Background Color','evoad'),
				'variations'=>array(
					array('id'=>'evoad1', 'name'=>'Unrevealed','type'=>'color', 'default'=>'ff7070'),
					array('id'=>'evoad2', 'name'=>'Revealed','type'=>'color', 'default'=>'75e082'),
					array('id'=>'evoad3', 'name'=>'Revealed Past','type'=>'color', 'default'=>'a9a9a9'),
				)
			);
			$new[] = array('id'=>'evoad','type'=>'fontation','name'=>__('Row Text Color','evoad'),
				'variations'=>array(
					array('id'=>'evoad1t', 'name'=>'Unrevealed','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoad2t', 'name'=>'Revealed','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoad3t', 'name'=>'Revealed Past','type'=>'color', 'default'=>'ffffff'),
				)
			);
			
			$new[] = array('id'=>'evoad','type'=>'hiddensection_close');
			return array_merge($array, $new);
		}

		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.evocard_row .evo_metarow_evoad.unrevealed',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoad1t',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evoad1',	'default'=>'ff7070'),
					)
				),
				array(
					'item'=>'.evocard_row .evo_metarow_evoad.revealnow',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoad2t',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evoad2',	'default'=>'75e082'),
					)
				),array(
					'item'=>'.evocard_row .evo_metarow_evoad.pastreveal',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoad3t',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evoad3',	'default'=>'a9a9a9'),
					)
				),
				//array('item'=>'.evoTX_wc .evoGC .evoGC_week .evoGC_date.today i','css'=>'border-color:#$', 'var'=>'evoad3','default'=>'ffafaf'),				
				
			);			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
}