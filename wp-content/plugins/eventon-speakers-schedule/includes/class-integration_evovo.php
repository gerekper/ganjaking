<?php
/*
 * Integrate with evovo
 * @version 1.0.3
 */


class EVOSS_Int_VO{
	public function __construct(){
		add_filter('evovo_variationtype_form_fields', array($this,'variation_type_form'),10,2);
	}

	public function variation_type_form($arr, $post){
		if( $post['json']['type'] == 'new'){


			$terms = get_terms(array(
				'taxonomy'=>'event_speaker',
				'hide_empty'=>false,
			));

			if(empty($terms)) return $arr;

			//print_r($terms);

			$data_st = array();
			foreach($terms as $T){
				$data_st[] = $T->name;
			}

			$arr['populate_speakers'] = array(
				'label'=> __('Populate with speakers','evovo'),
				'type'=>'populate_button',
				'vn'=> 'Speakers',
				'data'=> implode(',', $data_st)	
			);
		}

		return $arr;
	}
}

new EVOSS_Int_VO();