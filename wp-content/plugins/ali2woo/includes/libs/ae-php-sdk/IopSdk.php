<?php
/**
 * IOP SDK entry
 * please do not modified this file unless you know how to modify and how to recover
 * @author xuteng.xt
 */

    /**
     * log dir
     */
    if (!defined("IOP_SDK_WORK_DIR")) {
        define("IOP_SDK_WORK_DIR", dirname(__FILE__));
    }

    if (!defined("IOP_AUTOLOADER_PATH")) {
        define("IOP_AUTOLOADER_PATH", dirname(__FILE__));
    }

    require("iop/Constants.php");
    require("iop/IopClient.php");
    require("iop/IopLogger.php");
    require("iop/IopRequest.php");
    require("iop/UrlConstants.php");
