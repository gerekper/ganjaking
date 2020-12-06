<?php
// Make sure this script can oly be run on the command line
if(!defined('STDIN')) { die("You're unauthorized to view this page."); }

require_once('../../../wp-load.php');

if($argc<3) { die( __('Usage: php memberpress-importer-cli.php <importer> <filepath> <args>', 'memberpress-importer') ); }

$importer = $argv[1];
$filepath = $argv[2];
$args = wp_parse_args($argv[3]);

$_SERVER['SERVER_NAME'] = 'memberpress';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$mpimp->import_from_csv($importer,$filepath,$args,0,0,array(),true);

