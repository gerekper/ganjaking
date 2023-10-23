<?php
/**
 * Custom Template styles
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.

?>
	body {
	background-color: #e0e7f0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
	margin: 0;
	padding: 0;
	min-width: 100%!important;
	font-family: 'Raleway', sans-serif;
	}

	#content_table{
	width: 100%;
	max-width: 600px;
	}

	#overheader{
	padding: 30px 40px;
	font-size: 30px;
	text-transform: uppercase;
	color: #495361;
	font-weight: 800;
	line-height: 35px;
	}

	#header{
	padding: 10px;
	height: 108px;
	line-height:30px;
	font-size:25px;
	text-align: center;
	color: #ffffff;
	background: #769ed2;
	}

	#mailbody{
	padding: 50px 40px;
	border-top: 5px solid #e0e7f0;
	border-bottom: 5px solid #e0e7f0;
	font-size: 14px;
	color: #656565;
	line-height: 25px;
	background: #ffffff;
	}

	#footer{
	padding: 25px 10px;
	text-align: center;
	line-height: 20px;
	font-size: 13px;
	background: #ffffff;
	}

	#footer a{
	text-decoration: underline;
	color: #769ed2;
	font-weight: bold;
	}

	#subfooter{
	padding: 10px;
	text-align: center;
	line-height: 20px;
	font-size: 12px;
	color:#6b7785;
	}

	.ywrr-table td.title-column a{
	color:#769ed2;
	}

<?php
