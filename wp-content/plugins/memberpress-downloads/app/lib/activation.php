<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

use memberpress\downloads\controllers as ctrl;
/**
* Must call register_all_cpts before flush_rewrite_rules!
* Called on activation from hook: register_activation_hook
*/
$app_ctrl = ctrl\App::fetch();
$app_ctrl->register_all_cpts();
$app_ctrl->init_uploads_dir();
flush_rewrite_rules();
