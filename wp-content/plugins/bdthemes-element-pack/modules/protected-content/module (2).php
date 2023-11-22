<?php
namespace ElementPack\Modules\ProtectedContent;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'protected-content';
	}

	public function get_widgets() {
		$widgets = [
			'Protected_Content',
		];

		return $widgets;
	}

	/**
	 * Fetch user roles as array
	 * @return return all users as array
	 */
	public static function pc_user_roles(){
	    
	    global $wp_roles;
		
		$all_roles  = $wp_roles->roles;
		$user_roles = [];

	    if(!empty($all_roles)){
	        foreach($all_roles as $key => $value){
	            $user_roles[$key] = $all_roles[$key]['name'];
	        }
	    }

	    return $user_roles;
	}

}
