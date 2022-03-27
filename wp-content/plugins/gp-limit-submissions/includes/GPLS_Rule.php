<?php

class GPLS_Rule {

	public $form_id;

	public function __construct() {

	}

	public static function load( $ruleData, $form_id = false ) {

		switch ( $ruleData['rule_type'] ) {
			case 'ip':
				return GPLS_Rule_Ip::load( $ruleData );
				break;
			case 'embed_url':
				return GPLS_Rule_Embed_Url::load( $ruleData );
				break;
			case 'field':
				return GPLS_Rule_Field::load( $ruleData, $form_id );
				break;
			case 'role':
				return GPLS_Rule_Role::load( $ruleData );
				break;
			case 'user':
				return GPLS_Rule_User::load( $ruleData );
				break;
		}
	}

	public function get_type() {
	}

	public function context() {
		return true;
	}

	public function query() {
		return '1 = 1';
	}

	public function set_query_data( $query_data = array() ) {
		$this->form_id = $query_data['form_id'];
	}
}
