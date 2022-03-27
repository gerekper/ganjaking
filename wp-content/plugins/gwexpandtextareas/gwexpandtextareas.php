<?php

/**
* Plugin Name: GP Expand Editor Textareas
* Description: Tiny textareas in the form editor can be a challenge! Load form editor textareas in a modal window for easy editing.
* Plugin URI: https://gravitywiz.com/documentation/gravity-forms-expand-textareas/
* Version: 1.1
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

require 'includes/class-gp-bootstrap.php';

$gp_expand_text_areas_bootstrap = new GP_Bootstrap( 'class-gp-expand-editor-textareas.php', __FILE__ );
