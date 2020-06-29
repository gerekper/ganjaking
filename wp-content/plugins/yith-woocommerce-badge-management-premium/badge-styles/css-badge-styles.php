<?php
require_once('color-functions.php');
$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : NULL;
$css_text_color = (isset($css_text_color)) ? $css_text_color : NULL;
$id_css_badge = (isset($id_css_badge)) ? '-' . $id_css_badge : '';
$id_badge_style = (isset($id_badge_style)) ? $id_badge_style : NULL;

if(isset ($_POST['color']) ){
    $col = $_POST['color'];
    if ( strlen($col) == 6){
		$css_bg_color = '#' . $col;
    }
}

if(isset ($_POST['text_color']) ){
    $col = $_POST['text_color'];
    if ( strlen($col) == 6){
        $css_text_color = '#' . $col;
    }
}

if(isset ($_POST['id_badge_style']) ){
    $id_badge_style = $_POST['id_badge_style'];
}

switch ($id_badge_style) {
	case '1':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#3986C6';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position: absolute;
				background-color: transparent;
				overflow: auto;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				padding: 6px 8px;
				background: <?php echo $css_bg_color ?>;
				font-size: 13px;
				font-weight: bold;
				line-height:13px;
			}
			<?php

		break;
	case '2':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#4AC393';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				background-color: transparent;
				width: 65px;
				height: 65px;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{
				width:0;
				height:0;
				border-right: 65px solid <?php echo $css_bg_color ?>;
				border-bottom: 65px solid transparent;
				z-index:12;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				font-size: 13px;
				font-weight: bold;
				line-height: 13px;
				position: absolute;
				z-index: 14;
				-webkit-transform: rotate(45deg);
			   	-ms-transform: rotate(45deg);
			   	transform: rotate(45deg);
				top: 15px;
				left: -5px;
				width: 91px;
				text-align: center;
			}
			<?php
		break;
	case '3':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#FFFFFF';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#61C300';
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				background-color: transparent;
				overflow: auto;
				width: auto;
				height: auto;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				box-sizing: border-box;
				padding: 10px 6px;
				background: <?php echo $css_bg_color ?>;
				border-radius: 7px;
				border: 2px solid <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				font-size: 10px;
				font-weight: bold;
				line-height:10px;
			}
			<?php

		break;
    case '4':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#F78E35';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				background-color: transparent;
				height: 66px;
				width: 66px;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{
				height: 50px;
				width: 50px;
				-webkit-transform: rotate(45deg);
			   	-ms-transform: rotate(45deg);
			   	transform: rotate(45deg);
				background: <?php echo $css_bg_color ?>;
				border-radius: 6px;
				position: absolute;
				top: 8px;
				left: 8px;
				z-index: 12;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{

			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				font-family: "Open Sans",sans-serif;
				font-size: 13px;
				font-weight: 200;
				line-height: 13px;
				position: absolute;
				z-index: 14;
				width: 66px;
				top: 26px;
				text-align: center;
			}
			<?php
		break;
	case '5':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#45D0EB';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			$css_bg_color2 = '#' . yith_wcbm_color_with_factor(substr($css_bg_color,1), 0.6);
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;

				background-color: transparent;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{

			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1:before{

			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{
				width:22px;
				height:26px;
				display: inline-block;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				background: <?php echo $css_bg_color ?>;
				font-family: "Open Sans",sans-serif;
				font-size: 14px;
				font-weight: bold;
				line-height: 30px;
				height: 30px;
				white-space:nowrap;
				padding-right: 10px;
				padding-left: 6px;
				box-sizing: border-box;
				display: inline-block;
				position:relative;
				top:-6px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text:before{
				content: '';
				width:0;
				height:0;
				border-right: 22px solid <?php echo $css_bg_color ?>;
				border-bottom: 30px solid transparent;
				position: absolute;
				top: 0;
				left:-22px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text:after{
				content: '';
				width:0;
				height:0;
				border-top: 6px solid <?php echo $css_bg_color2 ?>;
				border-right: 5px solid transparent;
				border-left: 2px solid transparent;
				position: absolute;
				right: 1px;
				bottom: -6px;
			}
			<?php
		break;
	case '6':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#F66600';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#000000';
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				background-color: transparent;
				overflow: auto;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				font-size: 13px;
				font-weight: 400;
				line-height:13px;
				padding: 3px 0px;
				background: transparent;
				border-bottom:3px solid <?php echo $css_bg_color ?>;
			}
			<?php
		break;
	case '7':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#F66600';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			$css_bg_color2 = '#' . yith_wcbm_color_with_factor(substr($css_bg_color,1), 0.6);
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				width:auto;
				height:auto;
				background-color: transparent;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{
				width:0;
				height:0;
				border-top: 6px solid <?php echo $css_bg_color2 ?>;
				border-right: 5px solid transparent;
				border-left: 2px solid transparent;
				position: absolute;
				right: 1px;
				top: 30px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{
				width:13px;
				height:26px;
				display: inline-block;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				background: <?php echo $css_bg_color ?>;
				font-size: 14px;
				font-weight: bold;
				line-height: 30px;
				height: 30px;
				white-space:nowrap;
				padding-right: 10px;
				padding-left: 8px;
				position: relative;
				top: -6px;
				right: 0;
				display: inline-block;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text:before{
				content: '';
				width:0;
				height:0;
				border-right: 12px solid <?php echo $css_bg_color ?>;
				border-bottom: 15px solid transparent;
				position: absolute;
				top: 0;
				left:-12px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text:after{
				content: '';
				width:0;
				height:0;
				border-right: 12px solid <?php echo $css_bg_color ?>;
				border-top: 15px solid transparent;
				position: absolute;
				top: 15px;
				left:-12px;
			}
			<?php
		break;

	case '8':
			$css_bg_color = (isset($css_bg_color)) ? $css_bg_color : '#3E93FF';
			$css_text_color = (isset($css_text_color)) ? $css_text_color : '#ffffff';
			$css_bg_color2 = '#' . yith_wcbm_color_with_factor(substr($css_bg_color,1), 0.6);
			?>
			.yith-wcbm-css-badge<?php echo $id_css_badge ?>{
				color: <?php echo $css_text_color ?>;
				font-family: "Open Sans",sans-serif;
				position:relative;
				box-sizing: border-box;
				position: absolute;
				background-color: transparent;
				width: 65px;
				height: 65px;
				overflow:hidden;
			}
			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s1{
				position: absolute;
				z-index: 12;
				top:0;
				left:2px;
				border-bottom: 4px solid <?php echo $css_bg_color2 ?>;
				border-left: 3px solid transparent;
				width:10px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-s2{
				position: absolute;
				z-index: 12;
				bottom:2px;
				right:0px;
				border-left: 4px solid <?php echo $css_bg_color2 ?>;
				border-bottom: 3px solid transparent;
				height:10px;
			}

			.yith-wcbm-css-badge<?php echo $id_css_badge ?> div.yith-wcbm-css-text{
				background: <?php echo $css_bg_color ?>;
				font-size: 10px;
				font-weight: bold;
				line-height: 22px;
				position: absolute;
				text-align: center;
				z-index: 14;
				-webkit-transform: rotate(45deg);
			   	-ms-transform: rotate(45deg);
			   	transform: rotate(45deg);
				top: 11px;
				left: -7px;
				width: 100px;
				text-align: center;
			}
			<?php
		break;
	default:
		break;
}