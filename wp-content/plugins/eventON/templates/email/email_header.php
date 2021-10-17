<?php
/**
 * Email Header
 *
 * @author 		eventON
 * @package 	eventON/Templates/Emails
 * @version     0.1
 *
 * To customize copy this file to your theme folder in below folder structure
 * path: your-theme-dir/eventon/templates/email/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$evo_options = get_option('evcal_options_evcal_1');

$wrapper = "
	background-color: #e6e7e8;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 25px 25px 25px 25px;
";

$innner = "
	background-color: #ffffff;
	-webkit-text-size-adjust:none !important;
	margin:0;
	border-radius:5px;
";

?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<div style="<?php echo $wrapper; ?>">
		<div style="<?php echo $innner;?>">