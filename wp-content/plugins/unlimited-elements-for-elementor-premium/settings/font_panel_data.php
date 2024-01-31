<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

$arrData = array();

// font family
$arrFonts = UniteFunctionsUC::arrayToAssoc(array(
	"Arial, Helvetica, sans-serif",
	"Georgia, serif",
	"Palatino Linotype, Book Antiqua, Palatino, serif",
	"Times New Roman, Times, serif",
));

$arrGoogleFonts = array(
	"Abril Fatface" => "Abril+Fatface",
	"Alfa Slab" => "Alfa+Slab+One",
	"Arvo" => "Arvo:400,400i,700,700i",
	"Dancing Script" => "Dancing+Script:400,700",
	"Droid Sans" => "Droid+Sans:400,700",
	"Exo" => "Exo:400,700",
	"Fanwood Text" => "Fanwood+Text:400,400i",
	"Fauna One" => "Fauna+One",
	"Gentium Book Basic" => "Gentium+Book+Basic:400,400i,700,700i",
	"Indie Flower" => "Indie+Flower",
	"Josefin Slab" => "Josefin+Slab:100,100i,300,300i,400,400i,600,600i,700,700i",
	"Julius Sans One" => "Julius+Sans+One",
	"Kurale" => "Kurale",
	"Lato" => "Lato:100,100i,300,300i,400,400i,700,700i,900,900i",
	"Lobster" => "Lobster",
	"Montserrat" => "Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i",
	"Mr De Haviland" => "Mr+De+Haviland",
	"Nixie One" => "Nixie+One",
	"Old Standard TT" => "Old+Standard+TT:400,400i,700",
	"Open Sans" => "Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i",
	"Oswald" => "Oswald:200,300,400,500,600,700",
	"Pacifico" => "Pacifico",
	"Permanent Marker" => "Permanent Marker",
	"Playfair Display" => "Playfair+Display:400,400i,700,700i,900,900i",
	"Poller One" => "Poller+One",
	"Poppins" => "Poppins:300,400,700,900",
	"Prata" => "Prata",
	"PT Sans Narrow" => "PT+Sans+Narrow:400,700",
	"Quattrocento" => "Quattrocento:400,700",
	"Raleway" => "Raleway:400,700",
	"Roboto" => "Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i",
	"Roboto Slab" => "Roboto+Slab:100,300,400,700",
	"Shadows Into Light" => "Shadows+Into+Light",
	"Spicy Rice" => "Spicy+Rice",
	"Ubuntu" => "Ubuntu:300,300i,400,400i,500,500i,700,700i",
	"Vollkorn" => "Vollkorn:400,400i,700,700i",
);

$arrSettingsGoogleFonts = array();

if(!empty($arrSettingsGoogleFonts))
	$arrGoogleFonts = array_merge($arrSettingsGoogleFonts, $arrGoogleFonts);

$arrFonts["__google_fonts_html_select_sap__"] = "--- Google Fonts ---";

foreach($arrGoogleFonts as $text => $value){
	$arrFonts[$text] = $text;
}

$arrData["arrFontFamily"] = $arrFonts;
$arrData["arrGoogleFonts"] = $arrGoogleFonts;

// font size
$arrData["arrFontSize"] = array(
	"10px",
	"11px",
	"12px",
	"13px",
	"14px",
	"15px",
	"16px",
	"17px",
	"18px",
	"19px",
	"20px",
	"21px",
	"22px",
	"23px",
	"24px",
	"25px",
	"26px",
	"27px",
	"28px",
	"29px",
	"30px",
	"32px",
	"34px",
	"36px",
	"38px",
	"40px",
	"42px",
	"44px",
	"46px",
	"48px",
	"50px",
	"52px",
	"54px",
	"56px",
	"58px",
	"60px",
	"62px",
	"64px",
	"66px",
	"68px",
	"70px",
	"72px",
	"74px",
	"76px",
	"78px",
	"80px",
	"84px",
	"86px",
	"90px",
	"92px",
	"96px",
	"120px",
);

$arrData["arrMobileSize"] = array(
	"10px",
	"12px",
	"13px",
	"14px",
	"15px",
	"16px",
	"18px",
	"20px",
	"22px",
	"24px",
	"26px",
	"28px",
	"32px",
	"36px",
	"42px",
	"48px",
	"60px",
);

// font style
$arrData["arrFontStyle"] = array(
	"normal" => "Normal",
	"italic" => "Italic",
);

// font weight
$arrData["arrFontWeight"] = array(
	"100" => "100 (Thin)",
	"200" => "200 (Extra Light)",
	"300" => "300 (Light)",
	"400" => "400 (Regular)",
	"500" => "500 (Medium)",
	"600" => "600 (Semi Bold)",
	"700" => "700 (Bold)",
	"800" => "800 (Extra Bold)",
	"900" => "900 (Black)",
);

// line height
$arrData["arrLineHeight"] = array(
	"1em",
	"1.2em",
	"1.5em",
	"1.7em",
	"1.8em",
	"1.9em",
	"2.0em",
	"2.1em",
	"2.2em",
	"2.3em",
	"2.4em",
	"2.5em",
	"2.6em",
	"2.7em",
	"2.8em",
	"2.9em",
	"3em",
);

// text decoration
$arrData["arrTextDecoration"] = array(
	"none" => "None",
	"underline" => "Underline",
	"overline" => "Overline",
	"line-through" => "Line Through",
);

// text transform
$arrData["arrTextTransform"] = array(
	"none" => "None",
	"capitalize" => "Capitalize",
	"lowercase" => "Lowercase",
	"uppercase" => "Uppercase",
);

return $arrData;
