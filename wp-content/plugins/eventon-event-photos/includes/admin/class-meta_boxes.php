<?php
/**
 * Meta boxes for event photos
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	evoep/Admin/
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoep_metaboxes{
	public function __construct(){
		add_action( 'eventon_save_meta', array($this,'save_meta_data'), 10 , 2 );
		add_action('evo_more_images_end', array($this, 'meta_box_adds'), 10, 1);

		EVO()->cal->load_more('evcal_ep');
	}

	// event image meta box additions
		function meta_box_adds($EVENT){
			global $ajde;

			EVO()->cal->set_cur('evcal_ep');
			$global_gal = EVO()->cal->check_yn('evoEP_global_gal');

			if($global_gal){
				echo "<p>".__('Additional images are gloablly set to show as separate photo gallery.').'</p>';
				return false;
				
			}
			$img_gal = $EVENT->check_yn('_evoep_gal');
			?>
			<div class='evoep_additions' style='padding-top: 10px'>
				<p class='yesno_leg_line ' style='margin:0px'>
					<?php echo eventon_html_yesnobtn(array(
						'var'=>$img_gal, 
						'id'=>'_evoep_gal',
						'label'=>__('Show as a separate gallery','eventon'),
						'guide'=>__('Show additional event images as a separate gallery from main event image'),
						'guide_position'=>'L',
						'input'=> true,
					)); ?>
				</p>				
			</div>
			<?php
		}

	// Save the data from meta box
	function save_meta_data($arr, $post_id){
		$fields = array( 'event_photos','evoep_images','_evoep_gal'	);
		foreach($fields as $field){
			if(!empty($_POST[$field])){
				update_post_meta( $post_id, $field, $_POST[$field] );
			}else{
				delete_post_meta($post_id, $field);
			}
		}			
	}
}