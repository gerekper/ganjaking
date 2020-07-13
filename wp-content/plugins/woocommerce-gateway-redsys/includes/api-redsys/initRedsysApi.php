<?php 

$GLOBALS["REDSYS_API_PATH"]=realpath(dirname(__FILE__));
$GLOBALS["REDSYS_LOG_ENABLED"]=true;

include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISOperationMessage.php";
include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISAuthenticationMessage.php";
include_once $GLOBALS["REDSYS_API_PATH"]."/Service/Impl/ISService.php";
include_once $GLOBALS["REDSYS_API_PATH"]."/Service/Impl/ISAuthenticationService.php";
include_once $GLOBALS["REDSYS_API_PATH"].'/Utils/ISLogger.php';
ISLogger::initialize($GLOBALS["REDSYS_API_PATH"]."/Log/", ISLogger::$DEBUG);
