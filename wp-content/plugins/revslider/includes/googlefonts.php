<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2022 ThemePunch
 * @since 	  5.1.0
 * @lastfetch 09.11.2022
 */
 
if(!defined('ABSPATH')) exit();

/**
*** CREATED WITH SCRIPT SNIPPET AND DATA TAKEN FROM https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&fields=items(family%2Csubsets%2Cvariants%2Ccategory)&key={YOUR_API_KEY}

$list_raw = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&fields=items(family%2Csubsets%2Cvariants%2Ccategory)&key={YOUR_API_KEY}');

$list = json_decode($list_raw, true);
$list = $list['items'];

echo '<pre>';
foreach($list as $l){
	echo "'".$l['family'] ."' => array("."\n";
	echo "'variants' => array(";
	foreach($l['variants'] as $k => $v){
		if($k > 0) echo ", ";
		if($v == 'regular') $v = '400';
		echo "'".$v."'";
	}
	echo "),\n";
	echo "'subsets' => array(";
	foreach($l['subsets'] as $k => $v){
		if($k > 0) echo ", ";
		echo "'".$v."'";
	}
	echo "),\n";
	echo "'category' => '". $l['category'] ."'";
	echo "\n),\n";
}
echo '</pre>';
**/

$googlefonts = array(
'Roboto' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Open Sans' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans JP' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'Montserrat' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lato' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Poppins' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Source Sans Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Roboto Condensed' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Oswald' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Roboto Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Raleway' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Inter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mukta' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ubuntu' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Roboto Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Nunito' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Merriweather' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'PT Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Playfair Display' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Nunito Sans' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Rubik' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans KR' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Work Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lora' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Fira Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans TC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'sans-serif'
),
'Nanum Gothic' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Kanit' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Quicksand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Barlow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mulish' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Inconsolata' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Titillium Web' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Heebo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Hind Siliguri' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Josefin Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'IBM Plex Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Libre Franklin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Karla' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'DM Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Arimo' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Dosis' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Libre Baskerville' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Oxygen' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'PT Sans Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cairo' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bitter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Anton' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Source Code Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Cabin' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bebas Neue' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Source Serif Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Manrope' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Barlow Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Hind' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Dancing Script' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Prompt' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Signika Negative' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Abel' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Lobster' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Secular One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans SC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'sans-serif'
),
'EB Garamond' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Varela Round' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Exo 2' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Comfortaa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Fjalla One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Crimson Text' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Pacifico' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Maven Pro' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Merriweather Sans' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Serif JP' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'serif'
),
'Arvo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Teko' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Overpass' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Slabo 27px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Asap' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Hind Madurai' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Caveat' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Archivo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Public Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Abril Fatface' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jost' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans HK' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-hongkong', 'latin'),
'category' => 'sans-serif'
),
'Assistant' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shadows Into Light' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rajdhani' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yanone Kaffeesatz' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Tajawal' => array(
'variants' => array('200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Satisfy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Questrial' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Zilla Slab' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Space Grotesk' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Catamaran' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Barlow Semi Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Indie Flower' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'M PLUS Rounded 1c' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fira Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Red Hat Display' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cormorant Garamond' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'IBM Plex Serif' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'IBM Plex Mono' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Archivo Narrow' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Domine' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Nanum Myeongjo' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Poor Story' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Signika' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Play' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Alfa Slab One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Sarabun' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Acme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'ABeeZee' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Permanent Marker' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Vollkorn' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cinzel' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Fredoka One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Exo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Saira Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Chakra Petch' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'M PLUS 1p' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Alegreya Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Amatic SC' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Bree Serif' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Alegreya' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Space Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Righteous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Crete Round' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Frank Ruhl Libre' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Asap Condensed' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Ubuntu Condensed' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Didact Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Russo One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'DM Serif Display' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kalam' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'El Messiri' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yantramanav' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif TC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'serif'
),
'Tinos' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Lobster Two' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Patua One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Archivo Black' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amiri' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Martel' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Courgette' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Great Vibes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Almarai' => array(
'variants' => array('300', '400', '700', '800'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Spectral' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Rokkitt' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cardo' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Encode Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Paytone One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Sans Caption' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Changa' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Baloo 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Kufi Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Prata' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Cormorant' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noticia Text' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Francois One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Alata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Antic Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Titan One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fira Sans Extra Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Old Standard TT' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Saira' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sora' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kaushan Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Michroma' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'PT Mono' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'IBM Plex Sans Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Passion One' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Orbitron' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cookie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Yellowtail' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Montserrat Alternates' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Gothic A1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Chivo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'News Cycle' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Urbanist' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Philosopher' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Deca' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Quattrocento Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Concert One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sawarabi Mincho' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Lexend' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Patrick Hand' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Pathway Gothic One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Faustina' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Marcellus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Gloria Hallelujah' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Luckiest Guy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Commissioner' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Yeseva One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Mali' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Press Start 2P' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sacramento' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Playfair Display SC' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Unna' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Serif KR' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Josefin Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mitr' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Carter One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gelasio' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cantarell' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Advent Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Eczar' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Quattrocento' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Sawarabi Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Cuprum' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Staatliches' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Outfit' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Oleo Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Crimson Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Ropa Sans' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ultra' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Handlee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Poiret One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Lilita One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bangers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Alice' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Macondo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Monda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Khand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bungee' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Sigmar One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Creepster' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Marck Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Special Elite' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Neuton' => array(
'variants' => array('200', '300', '400', 'italic', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Neucha' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Rubik Mono One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bai Jamjuree' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Volkhov' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Nastaliq Urdu' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Aleo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mukta Malar' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Mr Dafoe' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Vidaloka' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Taviraj' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Architects Daughter' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sen' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif SC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'IBM Plex Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Ubuntu Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Tangerine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Homemade Apple' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Arsenal' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Saira Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Encode Sans Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Electrolize' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Parisienne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Allura' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Playball' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Lusitana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Hind Vadodara' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Viga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Padauk' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'myanmar'),
'category' => 'sans-serif'
),
'Bodoni Moda' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Quantico' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Istok Web' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nanum Gothic Coding' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'monospace'
),
'Hammersmith One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Plus Jakarta Sans' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fugaz One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sanchez' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Sriracha' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Kosugi Maru' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Itim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Actor' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Pragati Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ruda' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Alegreya Sans SC' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Be Vietnam Pro' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Nanum Pen Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Merienda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Cabin Condensed' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Antonio' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gruppo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Khula' => array(
'variants' => array('300', '400', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gudea' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Adamina' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'DM Serif Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Tenor Sans' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'thai'),
'category' => 'sans-serif'
),
'Palanquin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Berkshire Swash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Monoton' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mada' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Red Hat Text' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cousine' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Amaranth' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Jura' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'kayah-li', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Abhaya Libre' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'serif'
),
'Ramabhadra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Rock Salt' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Literata' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Sorts Mill Goudy' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Baskervville' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Unica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Economica' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'BenchNine' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Martel Sans' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Roboto Flex' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Black Han Sans' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Noto Naskh Arabic' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic'),
'category' => 'serif'
),
'Alex Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Shrikhand' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Blinker' => array(
'variants' => array('100', '200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bad Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Caveat Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Share Tech Mono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Pridi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Julius Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kumbh Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Niramit' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Audiowide' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sintony' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Black Ops One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Allerta Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Krub' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Pontano Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Anonymous Pro' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'greek', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Forum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Gentium Book Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Chewy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Castoro' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Varela' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Six Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Sarala' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cantata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Damion' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rufina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Covered By Your Grace' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Syncopate' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Carrois Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Alef' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Laila' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mandali' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Athiti' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Sansita' => array(
'variants' => array('400', 'italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Reenie Beanie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fira Mono' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Nothing You Could Do' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Epilogue' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Pangolin' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Lalezar' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Judson' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Courier Prime' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Hind Guntur' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Allerta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Readex Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Aclonica' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Yrsa' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Arapey' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Coda' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Comic Neue' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Libre Caslon Text' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Antic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Bowlby One SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Armata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Karma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Cutive Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Kreon' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lemonada' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Average Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Atkinson Hyperlegible' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Arima Madurai' => array(
'variants' => array('100', '200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Shadows Into Light Two' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Glegoo' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Italianno' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Palanquin Dark' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'VT323' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'League Spartan' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Saira Extra Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fraunces' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Pinyon Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Boogaloo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alatsi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'sans-serif'
),
'Markazi Text' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cabin Sketch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fira Code' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Rancho' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Norican' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gochi Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Nanum Brush Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Londrina Solid' => array(
'variants' => array('100', '300', '400', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Leckerli One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Aldrich' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'PT Serif Caption' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Gilda Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kameron' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fredericka the Great' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Annie Use Your Telescope' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Capriola' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Short Stack' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Zen Maru Gothic' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Coming Soon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Basic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amita' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Noto Serif Bengali' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'serif'
),
'JetBrains Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Jua' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Biryani' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Holtwood One SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Racing Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mrs Saint Delafield' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Voltaire' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Candal' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rambla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Average' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Squada One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Herr Von Muellerhoff' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bevan' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Mate' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cambay' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Just Another Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Libre Barcode 39' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Graduate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cormorant Infant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Shippori Mincho' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Overlock' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Changa One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Skranji' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Podkova' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Belleza' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jaldi' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rozha One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rammetto One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Telex' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Scada' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cinzel Decorative' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Charm' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Henny Penny' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Corben' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'K2D' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'STIX Two Text' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Arizonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Quintessential' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Reem Kufi' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Pattaya' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Rochester' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Days One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Suranna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Balsamiq Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Oranienbaum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Enriqueta' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Zen Kaku Gothic New' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Trirong' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Darker Grotesque' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kristi' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Esteban' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Trocchi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Alike' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Calistoga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Manjari' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'sans-serif'
),
'Marcellus SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Krona One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Delius' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'La Belle Aurore' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Source Serif 4' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Knewave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Niconne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lustria' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Chonburi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Arbutus Slab' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Caudex' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Wallpoet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Contrail One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nobile' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Syne' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Overpass Mono' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Nixie One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bubblegum Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Miriam Libre' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Do Hyeon' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Suez One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Sniglet' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell English SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Koulen' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'GFS Didot' => array(
'variants' => array('400'),
'subsets' => array('greek'),
'category' => 'serif'
),
'Caladea' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Maitree' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Cedarville Cursive' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ovo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Halant' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Gugi' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Fauna One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Averia Serif Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kosugi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fresca' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Monsieur La Doulaise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Yesteryear' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lateef' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Mallanna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Albert Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amiko' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mukta Vaani' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alegreya SC' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Spinnaker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'BioRhyme' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Grandstander' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Voces' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fjord One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Petit Formal Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bellefair' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Magra' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amethysta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Seaweed Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Inline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Anek Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'DM Mono' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Grand Hotel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Baloo Tamma 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('kannada', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Molengo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Merienda One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Livvic' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Proza Libre' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Coustard' => array(
'variants' => array('400', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mate SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Qwigley' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Calligraffitti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Roboto Serif' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Jockey One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kite One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Big Shoulders Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Thasadith' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Tillana' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Averia Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Sans Bengali' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yatra One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Dongle' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Rosario' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Vazirmatn' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Slabo 13px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'NTR' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Grape Nuts' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'League Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Marvel' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Della Respira' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Rubik Moonrocks' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Kiwi Maru' => array(
'variants' => array('300', '400', '500'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Newsreader' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'UnifrakturMaguntia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Marmelad' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Allan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sunflower' => array(
'variants' => array('300', '500', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Bowlby One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bungee Shade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Source Sans 3' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Rasa' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Oxygen Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Dawning of a New Day' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Inknut Antiqua' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Metrophobic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kurale' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Hepta Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Share' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Viaoda Libre' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Lemon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell English' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Aladin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Stardos Stencil' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Patrick Hand SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Copse' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Waiting for the Sunrise' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kelly Slab' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Nova Mono' => array(
'variants' => array('400'),
'subsets' => array('greek', 'latin'),
'category' => 'monospace'
),
'Homenaje' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Schoolbell' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Hanuman' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Radley' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Baloo Thambi 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Pompiere' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'David Libre' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Pirata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Poller One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Grenze Gotisch' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Fondamento' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Convergence' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Brawler' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Vollkorn SC' => array(
'variants' => array('400', '600', '700', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Limelight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mansalva' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Farro' => array(
'variants' => array('300', '400', '500', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'IBM Plex Sans Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'thai'),
'category' => 'sans-serif'
),
'Noto Serif Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'serif'
),
'Cormorant SC' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Georama' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Love Ya Like A Sister' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cormorant Upright' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Oleo Script Swash Caps' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mirza' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Big Shoulders Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Bentham' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Zeyada' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Turret Road' => array(
'variants' => array('200', '300', '400', '500', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Spectral SC' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cutive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Baloo Da 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('bengali', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Irish Grover' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mouse Memoirs' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oxanium' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Quando' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Encode Sans Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sansita Swashed' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'B612' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Arya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fanwood Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Goudy Bookletter 1911' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'KoHo' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Sedgwick Ave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Noto Sans Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gabriela' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin'),
'category' => 'serif'
),
'Inder' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Montez' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gravitas One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vesper Libre' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Gurajada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'McLaren' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Stint Ultra Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Buenard' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Meddon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Antic Didone' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Euphoria Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kadwa' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Rouge Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ma Shan Zheng' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Andika' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Carme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Emilys Candy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Aref Ruqaa' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'RocknRoll One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sue Ellen Francisco' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Federo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Megrim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Reggae One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell DW Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Numans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Share Tech' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Duru Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oregano' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Notable' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noto Sans Oriya' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'oriya'),
'category' => 'sans-serif'
),
'Poly' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Original Surfer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lexend Zetta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Baloo Paaji 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Odibee Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell Double Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Harmattan' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Petrona' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Allison' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shippori Mincho B1' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Italiana' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Raleway Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Happy Monkey' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tenali Ramakrishna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Chelsea Market' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rakkas' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Supermercado One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Montserrat Subrayada' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fahkwang' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Dela Gothic One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Alike Angular' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Orienta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Geo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Oooh Baby' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'ZCOOL XiaoWei' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Shanti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mr De Haviland' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Averia Gruesa Libre' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Major Mono Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Modak' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Expletus Sans' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Atma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'display'
),
'Battambang' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Ceviche One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Elsie' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Recursive' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Doppio One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bellota Text' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'DotGothic16' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Coiny' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Kodchasan' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Cambo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Uncial Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metamorphous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Freckle Face' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Shojumaru' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Goldman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Libre Bodoni' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'ZCOOL QingKe HuangYou' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Baumans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ranchers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Azeret Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Amarante' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Timmana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Montaga' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Sans Sinhala' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Anaheim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Walter Turncoat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Meera Inimai' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tamil'),
'category' => 'sans-serif'
),
'Mountains of Christmas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Anek Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'sans-serif'
),
'Aguafina Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Wendy One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Akshar' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Galada' => array(
'variants' => array('400'),
'subsets' => array('bengali', 'latin'),
'category' => 'display'
),
'Saira Stencil One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Shalimar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Besley' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ledger' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Hi Melody' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Give You Glory' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'BIZ UDPGothic' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Trykker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'IBM Plex Sans KR' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('korean', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Codystar' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lekton' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Goblin One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pavanam' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Zen Antique' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Germania One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Figtree' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Brygada 1918' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Encode Sans Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Clicker Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Tomorrow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Silkscreen' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruslan Display' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Finger Paint' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bubbler One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Glory' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Averia Sans Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Caslon Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Fredoka' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baloo Chettan 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'malayalam', 'vietnamese'),
'category' => 'display'
),
'Vast Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lily Script One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bilbo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'B612 Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Salsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Eater' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Zen Old Mincho' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Peralta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yusei Magic' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sail' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Bhai 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Jomhuria' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Belgrano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Asul' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Faster One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lexend Exa' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Serif Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Inria Serif' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Scope One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Prosto One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Libre Barcode 39 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gamja Flower' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Katibeh' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Over the Rainbow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Zen Antique Soft' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Ibarra Real Nova' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Artifika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Delius Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'MuseoModerno' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Balthazar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Crafty Girls' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sarpanch' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Life Savers' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Bhaijaan 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Loved by the King' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Solway' => array(
'variants' => array('300', '400', '500', '700', '800'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Englebert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mukta Mahee' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Akaya Telivigala' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'display'
),
'Klee One' => array(
'variants' => array('400', '600'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Delius Unicase' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Vibur' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Flamenco' => array(
'variants' => array('300', '400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Chau Philomene One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'M PLUS 1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Port Lligat Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'The Girl Next Door' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Just Me Again Down Here' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Almendra' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mina' => array(
'variants' => array('400', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cherry Swash' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Encode Sans Semi Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Port Lligat Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ruthie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gaegu' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Tiro Devanagari Hindi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Charmonman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Slackey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mako' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Imprima' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Headland One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Comforter Brush' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sumana' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'ZCOOL KuaiLe' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Overlock SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tienne' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Sans Vai' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vai'),
'category' => 'sans-serif'
),
'Piedra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kotta One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kufam' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Andada Pro' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Rubik Dirt' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'UnifrakturCook' => array(
'variants' => array('700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Dynalight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Wancho' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'wancho'),
'category' => 'sans-serif'
),
'Zen Kurenaido' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tauri' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baloo Bhaina 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'oriya', 'vietnamese'),
'category' => 'display'
),
'Bigshot One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Spicy Rice' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Carrois Gothic SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rowdies' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'League Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gayathri' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Island Moments' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Style Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Prociono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ms Madi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Bellota' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Chango' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ephesis' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gotu' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Cormorant Unicase' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cherry Cream Soda' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 128' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Zen Kaku Gothic Antique' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Frijole' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Londrina Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Red Hat Mono' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Nova Round' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Strait' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Wire One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Macondo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kaisei Decol' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Nova Square' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Iceland' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'MedievalSharp' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kolker Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Stylish' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Alumni Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Murecho' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gafata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Color Emoji' => array(
'variants' => array('400'),
'subsets' => array('emoji'),
'category' => 'sans-serif'
),
'Denk One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Medula One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sree Krushnadevaraya' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Gorditas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Yeon Sung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Hurricane' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Rationale' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Trispace' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Londrina Outline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mystery Quest' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Tammudu 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'telugu', 'vietnamese'),
'category' => 'display'
),
'Ramaraja' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Puritan' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Coda Caption' => array(
'variants' => array('800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Farsan' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Anek Bangla' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kranky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gemunu Libre' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Kulim Park' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Asar' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Rum Raisin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Habibi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Chicle' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kaisei Tokumin' => array(
'variants' => array('400', '500', '700', '800'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Stoke' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Julee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Akronim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'WindSong' => array(
'variants' => array('400', '500'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Trade Winds' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cute Font' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Cantora One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Libre Barcode 39 Extended Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ribeye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rosarivo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Train One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'Square Peg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sonsie One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Varta' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Nova Flat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Unkempt' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jolly Lodger' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Inter Tight' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Griffy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'BIZ UDPMincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Inria Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Khmer' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Lovers Quarrel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Song Myung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Buda' => array(
'variants' => array('300'),
'subsets' => array('latin'),
'category' => 'display'
),
'Big Shoulders Stencil Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Potta One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Engagement' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Miniver' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Paprika' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fuzzy Bubbles' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Bayon' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Scheherazade New' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Margarine' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Redressed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'New Rocker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell French Canon' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bahianita' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Nokora' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Sirin Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Stint Ultra Expanded' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ewert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Stick' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Moul' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Iceberg' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tiro Kannada' => array(
'variants' => array('400', 'italic'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Piazzolla' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Sulphur Point' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sancreek' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Dekko' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Manuale' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cagliostro' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noto Sans Myanmar' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('myanmar'),
'category' => 'sans-serif'
),
'Anek Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nosifer' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Serif Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'thai'),
'category' => 'serif'
),
'Milonga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Zilla Slab Highlight' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fontdiner Swanky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'M PLUS 2' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bilbo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Hachi Maru Pop' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Elsie Swash Caps' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kantumruy' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Bigelow Rules' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Marko One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Stalemate' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Red Rose' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Monofett' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Angkor' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Noto Sans Georgian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('georgian', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'East Sea Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Croissant One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruluko' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Barrio' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fenix' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Qwitcher Grypen' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Fasthand' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Orelega One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Simonetta' => array(
'variants' => array('400', 'italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tulpen One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tourney' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Joti One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sarina' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Wellfleet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Offside' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sura' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'IM Fell Great Primer' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Kavoon' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Vampiro One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Felipa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Underdog' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Crushed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Donegal One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Condiment' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Text Me One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Tamil Supplement' => array(
'variants' => array('400'),
'subsets' => array('tamil-supplement'),
'category' => 'sans-serif'
),
'Mochiy Pop One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'Chilanka' => array(
'variants' => array('400'),
'subsets' => array('latin', 'malayalam'),
'category' => 'handwriting'
),
'Kumar One' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Autour One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rampart One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ranga' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Hahmlet' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Aboreto' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Chathura' => array(
'variants' => array('100', '300', '400', '700', '800'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Montagu Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Gowun Batang' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Kaisei Opti' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Content' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Road Rage' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Kavivanar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'handwriting'
),
'Hina Mincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Lexend Giga' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Mega' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Freehand' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Meie Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lakki Reddy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'handwriting'
),
'Junge' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Eagle Lake' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Beth Ellen' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Stick No Bills' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Unlock' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell Great Primer SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Akaya Kanadaka' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'display'
),
'Xanh Mono' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Swanky and Moo Moo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Molle' => array(
'variants' => array('italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Maiden Orange' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sahitya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'IBM Plex Sans Thai Looped' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'thai'),
'category' => 'sans-serif'
),
'Radio Canada' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Spline Sans' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Modern Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Dorsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Linden Hill' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Emoji' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('emoji'),
'category' => 'sans-serif'
),
'Fascinate Inline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Serif Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'serif'
),
'Metal Mania' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Arima' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext', 'malayalam', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Ravi Prakash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'display'
),
'Inika' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Jomolhari' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Princess Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Srisakdi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Zhi Mang Xing' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Bona Nova' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Risque' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bakbak One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Familjen Grotesk' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mochiy Pop P One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'Mogra' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Symbols' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'symbols'),
'category' => 'sans-serif'
),
'Bahiana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Keania One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Karantina' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'MonteCarlo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Spirax' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Arbutus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rhodium Libre' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Mrs Sheppards' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Chenla' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Galdeano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Ribeye Marrow' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Diplomata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Shippori Antique' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'IM Fell DW Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'BIZ UDGothic' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yomogi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Kirang Haerang' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Emblema One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Black And White Picture' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Comforter' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Jim Nightshade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Nova Slim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Grenze' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Girassol' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Plaster' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Libre Barcode 128 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Revalia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Birthstone' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Londrina Sketch' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jacques Francois Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Romanesco' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Galindo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Zen Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Peddana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Anek Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Odor Mean Chey' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Encode Sans SC' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lancelot' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yaldevi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Sedgwick Ave Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'GFS Neohellenic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek'),
'category' => 'sans-serif'
),
'Fascinate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Caesar Dressing' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gantari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Jacques Francois' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Siemreap' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Nerko One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'New Tegomin' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Big Shoulders Stencil Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Nuosu SIL' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'yi'),
'category' => 'serif'
),
'Devonshire' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Moulpali' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Tiro Gurmukhi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Vujahday Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Diplomata SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yuji Syuku' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Almendra SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mohave' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Stalinist One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Chela One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Smokum' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gowun Dodum' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Oldenburg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Snippet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Hanalei Fill' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Spline Sans Mono' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Glass Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell French Canon SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Smythe' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alkalami' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Libre Barcode 39 Extended' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bungee Outline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Licorice' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Passero One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Asset' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amiri Quran' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'serif'
),
'Birthstone Bounce' => array(
'variants' => array('400', '500'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Flavors' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Imbue' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Barriecito' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Benne' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Warang Citi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'warang-citi'),
'category' => 'sans-serif'
),
'Long Cang' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Tiro Telugu' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'serif'
),
'Festive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Almendra Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Atomic Age' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tiro Bangla' => array(
'variants' => array('400', 'italic'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Kdam Thmor Pro' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Beau Rivage' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Dr Sugiyama' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Shippori Antique B1' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Gothic' => array(
'variants' => array('400'),
'subsets' => array('gothic'),
'category' => 'sans-serif'
),
'Miss Fajardose' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Joan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Armenian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('armenian', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ruge Boogie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Corinthia' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Liu Jian Mao Cao' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Noto Music' => array(
'variants' => array('400'),
'subsets' => array('music'),
'category' => 'sans-serif'
),
'Fuggles' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'DynaPuff' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Hairline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'BIZ UDMincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Bokor' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Carattere' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Georgian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('georgian', 'latin', 'latin-ext'),
'category' => 'serif'
),
'BioRhyme Expanded' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Gidugu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Combo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Erica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mr Bedfort' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Anybody' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Dangrek' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Seymour One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Geostar Fill' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Texturina' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Bonbon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Water Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Metal' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Noto Serif Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Butterfly Kids' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kumar One Outline' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Butcherman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Gupter' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'The Nautigal' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Smooch' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Whisper' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Tai Viet' => array(
'variants' => array('400'),
'subsets' => array('tai-viet'),
'category' => 'sans-serif'
),
'Fruktur' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Purple Purse' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kaisei HarunoUmi' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Syne Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Hanalei' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Tera' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bonheur Royale' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Qahiri' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Suwannaphum' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Praise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Zen Tokyo Zoo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Double Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Snowburst One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mea Culpa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Symbols 2' => array(
'variants' => array('400'),
'subsets' => array('symbols'),
'category' => 'sans-serif'
),
'Noto Sans Multani' => array(
'variants' => array('400'),
'subsets' => array('multani'),
'category' => 'sans-serif'
),
'Noto Sans Math' => array(
'variants' => array('400'),
'subsets' => array('math'),
'category' => 'sans-serif'
),
'Suravaram' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Noto Sans Ethiopic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('ethiopic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Trochut' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Astloch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Serif Balinese' => array(
'variants' => array('400'),
'subsets' => array('balinese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Samaritan' => array(
'variants' => array('400'),
'subsets' => array('samaritan'),
'category' => 'sans-serif'
),
'Charis SIL' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Vibes' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'display'
),
'Nova Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Langar' => array(
'variants' => array('400'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'display'
),
'Neonderthaw' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Aubrey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'M PLUS 1 Code' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Peta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Nova Oval' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Taprom' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Gluten' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'IBM Plex Sans Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Lacquer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Abyssinica SIL' => array(
'variants' => array('400'),
'subsets' => array('ethiopic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Ballet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gideon Roman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Tiro Devanagari Marathi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Smooch Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Zen Loop' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Miltonian Tattoo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sunshiney' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Single Day' => array(
'variants' => array('400'),
'subsets' => array('korean'),
'category' => 'display'
),
'Anek Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Edu QLD Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Hubballi' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'display'
),
'Anek Latin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Miltonian' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Waterfall' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Khmer' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Lavishly Yours' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Big Shoulders Inline Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Gwendolyn' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Nova Cut' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sofadi One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Syne Tactile' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Petemoss' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Preahvihear' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Noto Serif Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Dhurjati' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Truculenta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Rubik Marker Hatch' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Tai Heritage Pro' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'tai-viet', 'vietnamese'),
'category' => 'serif'
),
'Rubik Beastly' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Moon Dance' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Meow Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sevillana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Gentium Plus' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Edu TAS Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Anek Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Federant' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Koh Santepheap' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Noto Sans Thai Looped' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thai'),
'category' => 'sans-serif'
),
'Imperial Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Flow Circular' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Rubik Wet Paint' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Finlandica' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'My Soul' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Big Shoulders Inline Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Kenia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Luxurious Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Inspiration' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Rubik Glitch' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Geostar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alumni Sans Pinstripe' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kantumruy Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Lao' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('lao', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Uchen' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Yuji Boku' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Fleur De Leah' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'BhuTuka Expanded One' => array(
'variants' => array('400'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'display'
),
'Grechen Fuemen' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Alumni Sans Inline One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'IBM Plex Sans Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Love Light' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gentium Book Plus' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Rubik Distressed' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Nabla' => array(
'variants' => array('400'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'math', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Khmer' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tiro Devanagari Sanskrit' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Anek Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tapestry' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Old Turkic' => array(
'variants' => array('400'),
'subsets' => array('old-turkic'),
'category' => 'sans-serif'
),
'Noto Sans Thaana' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thaana'),
'category' => 'sans-serif'
),
'Rubik Bubbles' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Gulzar' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Serif HK' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('chinese-hongkong', 'cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Carian' => array(
'variants' => array('400'),
'subsets' => array('carian'),
'category' => 'sans-serif'
),
'Edu VIC WA NT Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Warnes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yuji Mai' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Old South Arabian' => array(
'variants' => array('400'),
'subsets' => array('old-south-arabian'),
'category' => 'sans-serif'
),
'Noto Sans Javanese' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('javanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Rashi Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Are You Serious' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Bungee Spice' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Edu NSW ACT Foundation' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Reem Kufi Fun' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Send Flowers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Updock' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Genos' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cherokee', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Passions Conflict' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Tai Le' => array(
'variants' => array('400'),
'subsets' => array('tai-le'),
'category' => 'sans-serif'
),
'Mingzat' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'lepcha'),
'category' => 'sans-serif'
),
'Tiro Tamil' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'serif'
),
'Noto Serif Ethiopic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('ethiopic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Oi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Edu SA Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Luxurious Roman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Serif Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Egyptian Hieroglyphs' => array(
'variants' => array('400'),
'subsets' => array('egyptian-hieroglyphs'),
'category' => 'sans-serif'
),
'Cairo Play' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Redacted Script' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Anek Odia' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'oriya'),
'category' => 'sans-serif'
),
'Caramel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Nyiakeng Puachue Hmong' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('nyiakeng-puachue-hmong'),
'category' => 'serif'
),
'Libre Barcode EAN13 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kings' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Babylonica' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Splash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Adlam' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('adlam', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif Armenian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('armenian', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Blaka Hollow' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Serif Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'serif'
),
'Sassy Frass' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Alumni Sans Collegiate One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans Mongolian' => array(
'variants' => array('400'),
'subsets' => array('mongolian'),
'category' => 'sans-serif'
),
'M PLUS Code Latin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Moo Lah Lah' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Blaka' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Deseret' => array(
'variants' => array('400'),
'subsets' => array('deseret'),
'category' => 'sans-serif'
),
'Noto Serif Sinhala' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'serif'
),
'Noto Serif Tibetan' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('tibetan'),
'category' => 'serif'
),
'Estonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Rubik Maze' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Cherokee' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cherokee', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rubik Burned' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Aref Ruqaa Ink' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Rubik Iso' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ole' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Explora' => array(
'variants' => array('400'),
'subsets' => array('cherokee', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Grey Qo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Tifinagh' => array(
'variants' => array('400'),
'subsets' => array('tifinagh'),
'category' => 'sans-serif'
),
'Reem Kufi Ink' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Cherish' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Twinkle Star' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Rubik Microbe' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ingrid Darling' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Balinese' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('balinese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Canadian Aboriginal' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('canadian-aboriginal', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Takri' => array(
'variants' => array('400'),
'subsets' => array('takri'),
'category' => 'sans-serif'
),
'Rubik Puddles' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Puppies Play' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Yezidi' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('yezidi'),
'category' => 'serif'
),
'Noto Sans Anatolian Hieroglyphs' => array(
'variants' => array('400'),
'subsets' => array('anatolian-hieroglyphs'),
'category' => 'sans-serif'
),
'Noto Serif Lao' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('lao', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Flow Block' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Redacted' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Coptic' => array(
'variants' => array('400'),
'subsets' => array('coptic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Cuneiform' => array(
'variants' => array('400'),
'subsets' => array('cuneiform'),
'category' => 'sans-serif'
),
'Noto Traditional Nushu' => array(
'variants' => array('400'),
'subsets' => array('nushu'),
'category' => 'sans-serif'
),
'Blaka Ink' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Flow Rounded' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Meetei Mayek' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'meetei-mayek'),
'category' => 'sans-serif'
),
'Noto Sans Brahmi' => array(
'variants' => array('400'),
'subsets' => array('brahmi'),
'category' => 'sans-serif'
),
'Noto Sans Tagalog' => array(
'variants' => array('400'),
'subsets' => array('tagalog'),
'category' => 'sans-serif'
),
'Noto Serif Myanmar' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('myanmar'),
'category' => 'serif'
),
'Noto Sans Imperial Aramaic' => array(
'variants' => array('400'),
'subsets' => array('imperial-aramaic'),
'category' => 'sans-serif'
),
'Noto Sans Sora Sompeng' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('sora-sompeng'),
'category' => 'sans-serif'
),
'Noto Sans Cham' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cham', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Old North Arabian' => array(
'variants' => array('400'),
'subsets' => array('old-north-arabian'),
'category' => 'sans-serif'
),
'Noto Sans Bamum' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('bamum'),
'category' => 'sans-serif'
),
'Noto Sans Avestan' => array(
'variants' => array('400'),
'subsets' => array('avestan', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Buhid' => array(
'variants' => array('400'),
'subsets' => array('buhid', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Mro' => array(
'variants' => array('400'),
'subsets' => array('mro'),
'category' => 'sans-serif'
),
'Noto Sans Osage' => array(
'variants' => array('400'),
'subsets' => array('osage'),
'category' => 'sans-serif'
),
'Noto Sans Old Italic' => array(
'variants' => array('400'),
'subsets' => array('old-italic'),
'category' => 'sans-serif'
),
'Noto Sans Adlam Unjoined' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('adlam', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Yi' => array(
'variants' => array('400'),
'subsets' => array('yi'),
'category' => 'sans-serif'
),
'Noto Sans Cypriot' => array(
'variants' => array('400'),
'subsets' => array('cypriot'),
'category' => 'sans-serif'
),
'Noto Sans Buginese' => array(
'variants' => array('400'),
'subsets' => array('buginese'),
'category' => 'sans-serif'
),
'Noto Sans Ol Chiki' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('ol-chiki'),
'category' => 'sans-serif'
),
'Noto Sans Sundanese' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('sundanese'),
'category' => 'sans-serif'
),
'Noto Serif Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Bhaiksuki' => array(
'variants' => array('400'),
'subsets' => array('bhaiksuki'),
'category' => 'sans-serif'
),
'Noto Sans Ogham' => array(
'variants' => array('400'),
'subsets' => array('ogham'),
'category' => 'sans-serif'
),
'Noto Sans Batak' => array(
'variants' => array('400'),
'subsets' => array('batak', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Marchen' => array(
'variants' => array('400'),
'subsets' => array('marchen'),
'category' => 'sans-serif'
),
'Noto Sans Grantha' => array(
'variants' => array('400'),
'subsets' => array('grantha', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Lisu' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'lisu'),
'category' => 'sans-serif'
),
'Noto Serif Ahom' => array(
'variants' => array('400'),
'subsets' => array('ahom', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Serif Tangut' => array(
'variants' => array('400'),
'subsets' => array('tangut'),
'category' => 'serif'
),
'Noto Sans Hanunoo' => array(
'variants' => array('400'),
'subsets' => array('hanunoo'),
'category' => 'sans-serif'
),
'Noto Sans Medefaidrin' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('medefaidrin'),
'category' => 'sans-serif'
),
'Noto Sans Old Hungarian' => array(
'variants' => array('400'),
'subsets' => array('old-hungarian'),
'category' => 'sans-serif'
),
'Noto Sans Limbu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'limbu'),
'category' => 'sans-serif'
),
'Noto Sans Tai Tham' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('tai-tham'),
'category' => 'sans-serif'
),
'Noto Sans Inscriptional Parthian' => array(
'variants' => array('400'),
'subsets' => array('inscriptional-parthian'),
'category' => 'sans-serif'
),
'Noto Serif Grantha' => array(
'variants' => array('400'),
'subsets' => array('grantha', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Linear A' => array(
'variants' => array('400'),
'subsets' => array('linear-a'),
'category' => 'sans-serif'
),
'Noto Sans Old Persian' => array(
'variants' => array('400'),
'subsets' => array('old-persian'),
'category' => 'sans-serif'
),
'Noto Sans Caucasian Albanian' => array(
'variants' => array('400'),
'subsets' => array('caucasian-albanian'),
'category' => 'sans-serif'
),
'Noto Sans N Ko' => array(
'variants' => array('400'),
'subsets' => array('nko'),
'category' => 'sans-serif'
),
'Noto Sans Miao' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'miao'),
'category' => 'sans-serif'
),
'Noto Serif Dogra' => array(
'variants' => array('400'),
'subsets' => array('dogra'),
'category' => 'serif'
),
'Noto Sans Kaithi' => array(
'variants' => array('400'),
'subsets' => array('kaithi'),
'category' => 'sans-serif'
),
'Noto Sans Inscriptional Pahlavi' => array(
'variants' => array('400'),
'subsets' => array('inscriptional-pahlavi'),
'category' => 'sans-serif'
),
'Noto Sans Palmyrene' => array(
'variants' => array('400'),
'subsets' => array('palmyrene'),
'category' => 'sans-serif'
),
'Noto Sans Pau Cin Hau' => array(
'variants' => array('400'),
'subsets' => array('pau-cin-hau'),
'category' => 'sans-serif'
),
'Noto Sans Bassa Vah' => array(
'variants' => array('400'),
'subsets' => array('bassa-vah'),
'category' => 'sans-serif'
),
'Noto Sans Chakma' => array(
'variants' => array('400'),
'subsets' => array('chakma'),
'category' => 'sans-serif'
),
'Noto Sans Psalter Pahlavi' => array(
'variants' => array('400'),
'subsets' => array('psalter-pahlavi'),
'category' => 'sans-serif'
),
'Noto Sans Syloti Nagri' => array(
'variants' => array('400'),
'subsets' => array('syloti-nagri'),
'category' => 'sans-serif'
),
'Noto Sans Phags Pa' => array(
'variants' => array('400'),
'subsets' => array('phags-pa'),
'category' => 'sans-serif'
),
'Noto Sans Kayah Li' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('kayah-li'),
'category' => 'sans-serif'
),
'Noto Sans Syriac' => array(
'variants' => array('100', '400', '900'),
'subsets' => array('syriac'),
'category' => 'sans-serif'
),
'Noto Sans Kharoshthi' => array(
'variants' => array('400'),
'subsets' => array('kharoshthi'),
'category' => 'sans-serif'
),
'Noto Sans Khudawadi' => array(
'variants' => array('400'),
'subsets' => array('khudawadi'),
'category' => 'sans-serif'
),
'Noto Sans Mayan Numerals' => array(
'variants' => array('400'),
'subsets' => array('mayan-numerals'),
'category' => 'sans-serif'
),
'Noto Sans Indic Siyaq Numbers' => array(
'variants' => array('400'),
'subsets' => array('indic-siyaq-numbers'),
'category' => 'sans-serif'
),
'Noto Sans New Tai Lue' => array(
'variants' => array('400'),
'subsets' => array('new-tai-lue'),
'category' => 'sans-serif'
),
'Noto Sans Osmanya' => array(
'variants' => array('400'),
'subsets' => array('osmanya'),
'category' => 'sans-serif'
),
'Noto Sans Zanabazar Square' => array(
'variants' => array('400'),
'subsets' => array('zanabazar-square'),
'category' => 'sans-serif'
),
'Noto Sans Hanifi Rohingya' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('hanifi-rohingya'),
'category' => 'sans-serif'
),
'Noto Sans Saurashtra' => array(
'variants' => array('400'),
'subsets' => array('saurashtra'),
'category' => 'sans-serif'
),
'Noto Sans Newa' => array(
'variants' => array('400'),
'subsets' => array('newa'),
'category' => 'sans-serif'
),
'Noto Sans Sharada' => array(
'variants' => array('400'),
'subsets' => array('sharada'),
'category' => 'sans-serif'
),
'Noto Sans Masaram Gondi' => array(
'variants' => array('400'),
'subsets' => array('masaram-gondi'),
'category' => 'sans-serif'
),
'Noto Sans Meroitic' => array(
'variants' => array('400'),
'subsets' => array('meroitic'),
'category' => 'sans-serif'
),
'Noto Sans Nushu' => array(
'variants' => array('400'),
'subsets' => array('nushu'),
'category' => 'sans-serif'
),
'Noto Sans Linear B' => array(
'variants' => array('400'),
'subsets' => array('linear-b'),
'category' => 'sans-serif'
),
'Noto Sans Shavian' => array(
'variants' => array('400'),
'subsets' => array('shavian'),
'category' => 'sans-serif'
),
'Noto Sans Phoenician' => array(
'variants' => array('400'),
'subsets' => array('phoenician'),
'category' => 'sans-serif'
),
'Noto Sans Rejang' => array(
'variants' => array('400'),
'subsets' => array('rejang'),
'category' => 'sans-serif'
),
'Noto Sans Runic' => array(
'variants' => array('400'),
'subsets' => array('runic'),
'category' => 'sans-serif'
),
'Noto Sans Duployan' => array(
'variants' => array('400'),
'subsets' => array('duployan'),
'category' => 'sans-serif'
),
'Noto Sans Manichaean' => array(
'variants' => array('400'),
'subsets' => array('manichaean'),
'category' => 'sans-serif'
),
'Noto Sans Ugaritic' => array(
'variants' => array('400'),
'subsets' => array('ugaritic'),
'category' => 'sans-serif'
),
'Noto Sans Lydian' => array(
'variants' => array('400'),
'subsets' => array('lydian'),
'category' => 'sans-serif'
),
'Noto Sans Hatran' => array(
'variants' => array('400'),
'subsets' => array('hatran'),
'category' => 'sans-serif'
),
'Noto Sans Mandaic' => array(
'variants' => array('400'),
'subsets' => array('mandaic'),
'category' => 'sans-serif'
),
'Noto Sans Glagolitic' => array(
'variants' => array('400'),
'subsets' => array('glagolitic'),
'category' => 'sans-serif'
),
'Noto Sans Lepcha' => array(
'variants' => array('400'),
'subsets' => array('lepcha'),
'category' => 'sans-serif'
),
'Noto Sans Old Permic' => array(
'variants' => array('400'),
'subsets' => array('old-permic'),
'category' => 'sans-serif'
),
'Noto Sans Elbasan' => array(
'variants' => array('400'),
'subsets' => array('elbasan'),
'category' => 'sans-serif'
),
'Noto Sans Pahawh Hmong' => array(
'variants' => array('400'),
'subsets' => array('pahawh-hmong'),
'category' => 'sans-serif'
),
'Noto Sans Elymaic' => array(
'variants' => array('400'),
'subsets' => array('elymaic'),
'category' => 'sans-serif'
),
'Noto Sans Tagbanwa' => array(
'variants' => array('400'),
'subsets' => array('tagbanwa'),
'category' => 'sans-serif'
),
'Noto Sans Gunjala Gondi' => array(
'variants' => array('400'),
'subsets' => array('gunjala-gondi'),
'category' => 'sans-serif'
),
'Noto Sans Khojki' => array(
'variants' => array('400'),
'subsets' => array('khojki'),
'category' => 'sans-serif'
),
'Noto Sans Nabataean' => array(
'variants' => array('400'),
'subsets' => array('nabataean'),
'category' => 'sans-serif'
),
'Noto Sans Soyombo' => array(
'variants' => array('400'),
'subsets' => array('soyombo'),
'category' => 'sans-serif'
),
'Noto Sans Modi' => array(
'variants' => array('400'),
'subsets' => array('modi'),
'category' => 'sans-serif'
),
'Noto Sans Mahajani' => array(
'variants' => array('400'),
'subsets' => array('mahajani'),
'category' => 'sans-serif'
),
'Noto Sans Sogdian' => array(
'variants' => array('400'),
'subsets' => array('sogdian'),
'category' => 'sans-serif'
),
'Noto Sans Old Sogdian' => array(
'variants' => array('400'),
'subsets' => array('old-sogdian'),
'category' => 'sans-serif'
),
'Noto Sans Siddham' => array(
'variants' => array('400'),
'subsets' => array('siddham'),
'category' => 'sans-serif'
),
'Noto Sans Tirhuta' => array(
'variants' => array('400'),
'subsets' => array('tirhuta'),
'category' => 'sans-serif'
),
'Noto Sans Lycian' => array(
'variants' => array('400'),
'subsets' => array('lycian'),
'category' => 'sans-serif'
)
);