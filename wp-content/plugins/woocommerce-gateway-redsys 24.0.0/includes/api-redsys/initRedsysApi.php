<?php

$GLOBALS['REDSYS_API_PATH']    = realpath( dirname( __FILE__ ) );
$GLOBALS['REDSYS_LOG_ENABLED'] = true;

require_once $GLOBALS['REDSYS_API_PATH'] . '/Model/Impl/ISOperationMessage.php';
require_once $GLOBALS['REDSYS_API_PATH'] . '/Model/Impl/ISAuthenticationMessage.php';
require_once $GLOBALS['REDSYS_API_PATH'] . '/Service/Impl/ISService.php';
require_once $GLOBALS['REDSYS_API_PATH'] . '/Service/Impl/ISAuthenticationService.php';
require_once $GLOBALS['REDSYS_API_PATH'] . '/Utils/ISLogger.php';
ISLogger::initialize( $GLOBALS['REDSYS_API_PATH'] . '/Log/', ISLogger::$DEBUG );
