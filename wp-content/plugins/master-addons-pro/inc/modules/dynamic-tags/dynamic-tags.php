<?php

namespace MasterAddons\Modules\DynamicTags;

defined('ABSPATH') || exit;

define('JLTMA_DYNAMIC_TAGS_PATH_INC', plugin_dir_path(__FILE__) . 'inc/');
define('JLTMA_DYNAMIC_TAGS_URL', plugins_url('/', __FILE__));
define('JLTMA_DYNAMIC_TAGS_DIR', plugin_basename(__FILE__));

require plugin_dir_path(__FILE__) . 'class-dynamic-tags.php';
