<?php
if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

global $updraftcentral_host_plugin;

if ($updraftcentral_host_plugin && 'updraftplus' !== $updraftcentral_host_plugin->get_plugin_name()) {
	$updraftcentral_host_plugin->debugtools_dashboard();
} else {
	do_action('updraftplus_debugtools_dashboard');
}
