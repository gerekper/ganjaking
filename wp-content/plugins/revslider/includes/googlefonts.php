<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2022 ThemePunch
 * @since 	  5.1.0
 * @lastfetch 12.08.2022
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
'Lato' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Montserrat' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Poppins' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Roboto Condensed' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Source Sans Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
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
'Noto Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Inter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
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
'Mukta' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
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
'Noto Sans KR' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
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
'Work Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans TC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'sans-serif'
),
'Lora' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Nanum Gothic' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Fira Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Barlow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Quicksand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Hind Siliguri' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mulish' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Titillium Web' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Inconsolata' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Kanit' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Heebo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin'),
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
'Josefin Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
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
'Cairo' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oxygen' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Arimo' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans SC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'sans-serif'
),
'Dosis' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'DM Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Libre Baskerville' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'PT Sans Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'EB Garamond' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Bebas Neue' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Anton' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bitter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Dancing Script' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Source Code Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Source Serif Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cabin' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Prompt' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Lobster' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Hind' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Barlow Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans HK' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-hongkong', 'latin'),
'category' => 'sans-serif'
),
'Signika Negative' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'M PLUS Rounded 1c' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Abel' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Varela Round' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fjalla One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Comfortaa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Pacifico' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Crimson Text' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Exo 2' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Manrope' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Maven Pro' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Yanone Kaffeesatz' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Overpass' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Arvo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Merriweather Sans' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Archivo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Serif JP' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'serif'
),
'Cinzel' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Hind Madurai' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Noto Serif TC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'serif'
),
'Asap' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Abril Fatface' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Teko' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Secular One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rajdhani' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
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
'Indie Flower' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Caveat' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Questrial' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Slabo 27px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Fira Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
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
'Zilla Slab' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Jost' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Public Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Taviraj' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'IBM Plex Serif' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cormorant Garamond' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Red Hat Display' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif KR' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Domine' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Bree Serif' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Permanent Marker' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Tajawal' => array(
'variants' => array('200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Archivo Narrow' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Play' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Acme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Nanum Myeongjo' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Alfa Slab One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Signika' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Vollkorn' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'ABeeZee' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sarabun' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Cormorant SC' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Ibarra Real Nova' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Exo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'M PLUS 1p' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Padauk' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'myanmar'),
'category' => 'sans-serif'
),
'Amatic SC' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'IBM Plex Mono' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Satisfy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Righteous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yantramanav' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alegreya' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Chakra Petch' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Alegreya Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Space Grotesk' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fredoka One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Didact Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Martel' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Saira Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Russo One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Staatliches' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tinos' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'DM Serif Display' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ubuntu Condensed' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amiri' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Great Vibes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Cardo' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Courgette' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Archivo Black' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Space Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Frank Ruhl Libre' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Crete Round' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kalam' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Antic Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Asap Condensed' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Volkhov' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'PT Sans Caption' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Patua One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lobster Two' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Baloo 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Cookie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Prata' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Rokkitt' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Old Standard TT' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Changa' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Kufi Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Kaushan Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Noto Sans Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Orbitron' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Pathway Gothic One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oleo Script Swash Caps' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Encode Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sawarabi Mincho' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
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
'Spectral' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Patrick Hand' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Chivo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Francois One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sacramento' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Almarai' => array(
'variants' => array('300', '400', '700', '800'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Passion One' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Alata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Concert One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Advent Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cantarell' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Quattrocento Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Lexend Deca' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Press Start 2P' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext'),
'category' => 'display'
),
'Gelasio' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cuprum' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sora' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gloria Hallelujah' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Unna' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'PT Mono' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Montserrat Alternates' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Yeseva One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Gothic A1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Philosopher' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Playfair Display SC' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Josefin Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'News Cycle' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mali' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Quattrocento' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Paytone One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Itim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Marcellus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Faustina' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Special Elite' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mitr' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Ropa Sans' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Carter One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Saira' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sawarabi Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Poiret One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ultra' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fira Sans Extra Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Luckiest Guy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alice' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Ubuntu Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Saira Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Commissioner' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Monda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bangers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Vidaloka' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Yellowtail' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Khand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Neuton' => array(
'variants' => array('200', '300', '400', 'italic', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Rubik Mono One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'El Messiri' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Architects Daughter' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Neucha' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Sanchez' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Homemade Apple' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Playball' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Ruda' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Handlee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Tangerine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Titan One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
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
'Black Han Sans' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Lexend' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Crimson Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Merienda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Aleo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Oleo Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Outfit' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mukta Malar' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Quantico' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Urbanist' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Encode Sans Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bungee' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Actor' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Kosugi Maru' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif SC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Amaranth' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Sen' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Viga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Marck Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Jura' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'kayah-li', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Red Hat Text' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Istok Web' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Hammersmith One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Arsenal' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Khula' => array(
'variants' => array('300', '400', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bai Jamjuree' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Source Serif 4' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Gudea' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Lusitana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Sans Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thai'),
'category' => 'sans-serif'
),
'Hind Vadodara' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mr Dafoe' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Coda' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Economica' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Pangolin' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Cabin Condensed' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Berkshire Swash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'IBM Plex Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Palanquin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rock Salt' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mada' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Alegreya Sans SC' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'DM Serif Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Adamina' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bad Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Pontano Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Basic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nanum Gothic Coding' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'monospace'
),
'Alex Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Nanum Pen Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Sigmar One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Sriracha' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Cousine' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Gentium Book Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Monoton' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Baskervville' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Julius Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Unica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lilita One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Michroma' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Gruppo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sarala' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Damion' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Karma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Literata' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Black Ops One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Alef' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Fira Mono' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'BenchNine' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tenor Sans' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Electrolize' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Share Tech Mono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Be Vietnam Pro' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fugaz One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Blinker' => array(
'variants' => array('100', '200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Armata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shrikhand' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Martel Sans' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Castoro' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Arima Madurai' => array(
'variants' => array('100', '200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Caveat Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Abhaya Libre' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'serif'
),
'Nothing You Could Do' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Six Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Pragati Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Forum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Lalezar' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Varela' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Naskh Arabic' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic'),
'category' => 'serif'
),
'Niramit' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Ramabhadra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Jaldi' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Audiowide' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Syncopate' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cantata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Sansita' => array(
'variants' => array('400', 'italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bodoni Moda' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Yrsa' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Antic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Eczar' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Sorts Mill Goudy' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Athiti' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Courier Prime' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Rufina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Arapey' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Allerta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Laila' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kumbh Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Aclonica' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fredericka the Great' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Glegoo' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Sintony' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Italianno' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Rancho' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Overlock' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Palanquin Dark' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Krub' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Shadows Into Light Two' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Pridi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Alatsi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'IBM Plex Sans Arabic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Anonymous Pro' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'greek', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Libre Caslon Text' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Chewy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'PT Serif Caption' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Bowlby One SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'BioRhyme' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Pinyon Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Enriqueta' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kameron' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Overpass Mono' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Lemonada' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Reenie Beanie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Covered By Your Grace' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Racing Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Belleza' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'VT323' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Noto Sans Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Darker Grotesque' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Capriola' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Spinnaker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Syne' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kreon' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Cabin Sketch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Markazi Text' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Holtwood One SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gochi Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Candal' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cambay' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Hind Guntur' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Squada One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cutive Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Gilda Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Pattaya' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'K2D' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Macondo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Herr Von Muellerhoff' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Comic Neue' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Londrina Solid' => array(
'variants' => array('100', '300', '400', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'League Spartan' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Boogaloo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Allerta Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fira Code' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Mate' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Jua' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Bevan' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Mrs Saint Delafield' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Annie Use Your Telescope' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mukta Vaani' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Leckerli One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Voltaire' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Epilogue' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Reem Kufi' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Krona One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Miriam Libre' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Stint Ultra Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Saira Extra Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Trirong' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Oranienbaum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Biryani' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Niconne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Caudex' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Fresca' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Just Another Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Suez One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Rozha One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Aldrich' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Average Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Amita' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Rye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Changa One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Telex' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Roboto Flex' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Big Shoulders Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Charm' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Cinzel Decorative' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Graduate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Carrois Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Norican' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Arizonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Days One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Coming Soon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Koulen' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Kristi' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Nanum Brush Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Readex Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Zen Maru Gothic' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Short Stack' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Delius' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rambla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mandali' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Trocchi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Jockey One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Suranna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Rochester' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Shippori Mincho' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Antonio' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fraunces' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Manjari' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'sans-serif'
),
'Corben' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Alegreya SC' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('malayalam'),
'category' => 'sans-serif'
),
'Halant' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Yesteryear' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Arbutus Slab' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Contrail One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Magra' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'La Belle Aurore' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'IM Fell English SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cormorant Infant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Esteban' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Average' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'STIX Two Text' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Scada' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Quintessential' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Sniglet' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Slabo 13px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Maitree' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Henny Penny' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mate SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Judson' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Do Hyeon' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Noto Sans Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari'),
'category' => 'sans-serif'
),
'Nixie One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fauna One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
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
'Tillana' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'JetBrains Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Chonburi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Nobile' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'GFS Didot' => array(
'variants' => array('400'),
'subsets' => array('greek'),
'category' => 'serif'
),
'Lateef' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'handwriting'
),
'Marcellus SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Alike' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Limelight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Libre Barcode 39' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Plus Jakarta Sans' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Creepster' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lustria' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bungee Inline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Coustard' => array(
'variants' => array('400', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Skranji' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rammetto One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Grand Hotel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bowlby One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amiko' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Calistoga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Kite One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Grandstander' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Seaweed Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Averia Serif Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Thasadith' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Hi Melody' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Sunflower' => array(
'variants' => array('300', '500', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Roboto Serif' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Patrick Hand SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'NTR' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Hepta Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Rubik Moonrocks' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Petit Formal Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Knewave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kosugi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Merienda One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Calligraffitti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Noto Serif Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Brawler' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Alike Angular' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Wallpoet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Qwigley' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Share' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rosario' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Averia Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bubblegum Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Serif Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('malayalam'),
'category' => 'serif'
),
'Gugi' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Bellefair' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'David Libre' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Atkinson Hyperlegible' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Balsamiq Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Amethysta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Marmelad' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oxygen Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Noto Serif Bengali' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('bengali'),
'category' => 'serif'
),
'Encode Sans Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Copse' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Dawning of a New Day' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'DM Mono' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Goudy Bookletter 1911' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Monsieur La Doulaise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Spectral SC' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Dongle' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Molengo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Proza Libre' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
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
'Bungee Shade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Livvic' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Baloo Chettan 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'malayalam', 'vietnamese'),
'category' => 'display'
),
'Allan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Aladin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kiwi Maru' => array(
'variants' => array('300', '400', '500'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Kelly Slab' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Gabriela' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin'),
'category' => 'serif'
),
'Zen Kaku Gothic New' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baloo Tamma 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('kannada', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Metrophobic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Belgrano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mr De Haviland' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Schoolbell' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Radley' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Podkova' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Marvel' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Hanuman' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Cormorant Upright' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Mallanna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Baloo Da 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('bengali', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Stardos Stencil' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Convergence' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Grenze Gotisch' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Big Shoulders Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Waiting for the Sunrise' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rasa' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Yatra One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Kadwa' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Vesper Libre' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Allison' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sansita Swashed' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Baloo Thambi 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Grape Nuts' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Fanwood Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Turret Road' => array(
'variants' => array('200', '300', '400', '500', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Oxanium' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cutive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Bentham' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fjord One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'IM Fell DW Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Sans Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('telugu'),
'category' => 'sans-serif'
),
'Kurale' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Duru Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sue Ellen Francisco' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Vollkorn SC' => array(
'variants' => array('400', '600', '700', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Nova Mono' => array(
'variants' => array('400'),
'subsets' => array('greek', 'latin'),
'category' => 'monospace'
),
'Quando' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Georama' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Irish Grover' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Megrim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fondamento' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'ZCOOL QingKe HuangYou' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'UnifrakturMaguntia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Share Tech' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'B612' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noto Sans Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Farro' => array(
'variants' => array('300', '400', '500', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Happy Monkey' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Reggae One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'Euphoria Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gravitas One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Chelsea Market' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Elsie' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Codystar' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Poly' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Buenard' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mouse Memoirs' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'League Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Love Ya Like A Sister' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mirza' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Inknut Antiqua' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Gurajada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Fahkwang' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Timmana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Emilys Candy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Antic Didone' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Zeyada' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Federo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Newsreader' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'B612 Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Original Surfer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Sans Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('kannada'),
'category' => 'sans-serif'
),
'Carme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rouge Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Inder' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gayathri' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Vazirmatn' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Poller One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Caladea' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Expletus Sans' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Almendra' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Italiana' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Pompiere' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Homenaje' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Source Sans 3' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Arya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Harmattan' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Montez' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Meddon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Noto Nastaliq Urdu' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic'),
'category' => 'serif'
),
'Galada' => array(
'variants' => array('400'),
'subsets' => array('bengali', 'latin'),
'category' => 'display'
),
'Cambo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Oregano' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Coiny' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'IM Fell Double Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mansalva' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Aref Ruqaa' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Montserrat Subrayada' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Della Respira' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sedgwick Ave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Ceviche One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tenali Ramakrishna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Noto Sans Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gurmukhi'),
'category' => 'sans-serif'
),
'KoHo' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Give You Glory' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Odibee Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aguafina Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Raleway Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Bengali' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Pirata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Petrona' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Clicker Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Rakkas' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Major Mono Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Supermercado One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Andika' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mukta Mahee' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Battambang' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Saira Stencil One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Bellota Text' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Metamorphous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Paaji 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Freckle Face' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'RocknRoll One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'ZCOOL XiaoWei' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Anaheim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baumans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vast Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Goldman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Goblin One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Shojumaru' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mako' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Scope One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Trykker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Modak' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Lekton' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Port Lligat Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'McLaren' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Numans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'IBM Plex Sans KR' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('korean', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baloo Tammudu 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'telugu', 'vietnamese'),
'category' => 'display'
),
'Finger Paint' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ledger' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Lexend Zetta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Wendy One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Encode Sans Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Orienta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Notable' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Dela Gothic One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Atma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'display'
),
'Lily Script One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kodchasan' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Balthazar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Baloo Bhai 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Doppio One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Recursive' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Chau Philomene One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Katibeh' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Andada Pro' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'IBM Plex Sans Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'thai'),
'category' => 'sans-serif'
),
'Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Voces' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Denk One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Meera Inimai' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tamil'),
'category' => 'sans-serif'
),
'Shippori Mincho B1' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Averia Sans Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pavanam' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Walter Turncoat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Flamenco' => array(
'variants' => array('300', '400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Solway' => array(
'variants' => array('300', '400', '500', '700', '800'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Headland One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Over the Rainbow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bilbo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Delius Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Salsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Square Peg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Sinhala' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('sinhala'),
'category' => 'sans-serif'
),
'Tiro Gurmukhi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Yusei Magic' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Eater' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Artifika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Faster One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Coda Caption' => array(
'variants' => array('800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tomorrow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
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
'Asul' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noto Sans Oriya' => array(
'variants' => array('100', '400', '700', '900'),
'subsets' => array('oriya'),
'category' => 'sans-serif'
),
'Montaga' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Shalimar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sail' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ranchers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Glory' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gujarati'),
'category' => 'sans-serif'
),
'Ruslan Display' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Encode Sans Semi Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Gorditas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sulphur Point' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Prosto One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Alumni Sans Inline One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'League Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Tienne' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Loved by the King' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gaegu' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Germania One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'MuseoModerno' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Brygada 1918' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Imprima' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Vibur' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Cherry Swash' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'The Girl Next Door' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Dynalight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mountains of Christmas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Crafty Girls' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Amarante' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Inria Serif' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Libre Barcode 39 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Charmonman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'MedievalSharp' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fredoka' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Strait' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'BhuTuka Expanded One' => array(
'variants' => array('400'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'display'
),
'Jomhuria' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'M PLUS 1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'DotGothic16' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Peralta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Libre Caslon Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ma Shan Zheng' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Just Me Again Down Here' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Cherry Cream Soda' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Medula One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kufam' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Tauri' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bigshot One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rationale' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Zen Old Mincho' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Slackey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Port Lligat Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Figtree' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gafata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gamja Flower' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Albert Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nova Round' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Puritan' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Paprika' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ramaraja' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Mochiy Pop One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'Delius Unicase' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Frijole' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mina' => array(
'variants' => array('400', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Lexend Exa' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Geo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Song Myung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Besley' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ewert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Wire One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Bahianita' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Rowdies' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Carrois Gothic SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Sonsie One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Square' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Redressed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Chango' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sree Krushnadevaraya' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Cantora One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Spicy Rice' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 128' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Iceland' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Prociono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cormorant Unicase' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Baloo Bhaina 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'oriya', 'vietnamese'),
'category' => 'display'
),
'Akshar' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'UnifrakturCook' => array(
'variants' => array('700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Stoke' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Style Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Bilbo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Stylish' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Red Rose' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'The Nautigal' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Londrina Outline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Flat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alumni Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Habibi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Bellota' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Unkempt' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Miniver' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Uncial Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Shanti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Overlock SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ribeye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Asar' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Kranky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Trade Winds' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sumana' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Potta One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Akronim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cute Font' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Sarina' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rosarivo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Khmer' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Hurricane' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'IM Fell French Canon' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Libre Bodoni' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Gotu' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Farsan' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Yeon Sung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Libre Barcode 39 Extended Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Chathura' => array(
'variants' => array('100', '300', '400', '700', '800'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Londrina Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nokora' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Varta' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fontdiner Swanky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Julee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Stalemate' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Buda' => array(
'variants' => array('300'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sancreek' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lovers Quarrel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Ruthie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'ZCOOL KuaiLe' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Iceberg' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rum Raisin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Macondo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Klee One' => array(
'variants' => array('400', '600'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Zilla Slab Highlight' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ravi Prakash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'display'
),
'Engagement' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mystery Quest' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Dekko' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Englebert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Azeret Mono' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'BIZ UDPGothic' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Manuale' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Bubbler One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Orelega One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sahitya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Monofett' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Crushed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fenix' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kaisei Decol' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Kotta One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kulim Park' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bayon' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Cagliostro' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'East Sea Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Tiro Telugu' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'serif'
),
'New Rocker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Zen Kaku Gothic Antique' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Zen Kurenaido' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Zen Antique Soft' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Hachi Maru Pop' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Stick' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Simonetta' => array(
'variants' => array('400', 'italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Red Hat Mono' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Sura' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Trispace' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sirin Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Croissant One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Chicle' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Autour One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Hahmlet' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Averia Gruesa Libre' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Donegal One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Vampiro One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Viaoda Libre' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Train One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'Big Shoulders Stencil Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Felipa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Inika' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ruluko' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Milonga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Text Me One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Offside' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tulpen One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Serif Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('tamil'),
'category' => 'serif'
),
'Bona Nova' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Condiment' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kantumruy' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Anek Bangla' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oooh Baby' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Symbols' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('symbols'),
'category' => 'sans-serif'
),
'Marko One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Inria Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Stint Ultra Expanded' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Myanmar' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('myanmar'),
'category' => 'sans-serif'
),
'Swanky and Moo Moo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Baloo Bhaijaan 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Chilanka' => array(
'variants' => array('400'),
'subsets' => array('latin', 'malayalam'),
'category' => 'handwriting'
),
'Margarine' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Akaya Telivigala' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'display'
),
'Piazzolla' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Kaisei Opti' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Kumar One' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ranga' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Tamil Supplement' => array(
'variants' => array('400'),
'subsets' => array('tamil-supplement'),
'category' => 'sans-serif'
),
'Gowun Batang' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'IM Fell Great Primer' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Emoji' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('emoji'),
'category' => 'sans-serif'
),
'Ephesis' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Content' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Elsie Swash Caps' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kavivanar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'handwriting'
),
'Dorsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Wellfleet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Angkor' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Mochiy Pop P One' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'WindSong' => array(
'variants' => array('400', '500'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Scheherazade New' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Eagle Lake' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lakki Reddy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'handwriting'
),
'Tourney' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Molle' => array(
'variants' => array('italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Uchen' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Kaisei Tokumin' => array(
'variants' => array('400', '500', '700', '800'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Barrio' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Modern Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Joan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Inspiration' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Comforter' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Tiro Kannada' => array(
'variants' => array('400', 'italic'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Underdog' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Piedra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kirang Haerang' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Nosifer' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'MonteCarlo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'IBM Plex Sans Thai Looped' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'thai'),
'category' => 'sans-serif'
),
'Peddana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Junge' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Meie Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Linden Hill' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Akaya Kanadaka' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Georgian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('georgian'),
'category' => 'sans-serif'
),
'Grenze' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Zen Antique' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Metal Mania' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Giga' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Anek Malayalam' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'malayalam'),
'category' => 'sans-serif'
),
'Spirax' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kavoon' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Maiden Orange' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bahiana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jomolhari' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Joti One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ribeye Marrow' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Griffy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Srisakdi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Devonshire' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Nuosu SIL' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'yi'),
'category' => 'serif'
),
'Jolly Lodger' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Risque' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Smokum' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mogra' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Bigelow Rules' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Diplomata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'M PLUS 2' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Qwitcher Grypen' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Beth Ellen' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Libre Barcode 128 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Romanesco' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Yomogi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Arbutus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Karantina' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Jim Nightshade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lexend Mega' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mrs Sheppards' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lancelot' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Diplomata SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Unlock' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Noto Serif Thai' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thai'),
'category' => 'serif'
),
'IM Fell DW Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gemunu Libre' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Caesar Dressing' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Anek Latin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Birthstone' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Fascinate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Radio Canada' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Galdeano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Nerko One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Zhi Mang Xing' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Arima' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext', 'malayalam', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Poor Story' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Rhodium Libre' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'IM Fell French Canon SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Flavors' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Revalia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Plaster' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Almendra SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Galindo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Freehand' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Atomic Age' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Odor Mean Chey' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Murecho' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Moul' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Jacques Francois Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gantari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Smythe' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Shippori Antique' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mohave' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Hina Mincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Jacques Francois' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Road Rage' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Bakbak One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Spline Sans' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bungee Outline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Emblema One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Miss Fajardose' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Barriecito' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Licorice' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Hanalei Fill' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Seymour One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rubik Dirt' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Oldenburg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Keania One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'New Tegomin' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Spline Sans Mono' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Girassol' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Princess Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Beau Rivage' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gowun Dodum' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Vujahday Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Chenla' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Gluten' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Nova Slim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rampart One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'display'
),
'Syne Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Noto Serif Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari'),
'category' => 'serif'
),
'Zen Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Glass Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Great Primer SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Hanalei' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fascinate Inline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Siemreap' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Snippet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Yaldevi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'BIZ UDPMincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Londrina Sketch' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Almendra Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Oval' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bonbon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Dr Sugiyama' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Long Cang' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Moulpali' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Asset' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Liu Jian Mao Cao' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Stalinist One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Carattere' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Big Shoulders Stencil Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Armenian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('armenian'),
'category' => 'sans-serif'
),
'Encode Sans SC' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Metal' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Smooch' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Festive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Benne' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'serif'
),
'BIZ UDGothic' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Anek Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Trochut' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sedgwick Ave Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Dangrek' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Fruktur' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Suwannaphum' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'serif'
),
'Libre Barcode 39 Extended' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rubik Marker Hatch' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Texturina' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Bungee Hairline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Butterfly Kids' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gidugu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Mr Bedfort' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kdam Thmor Pro' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Chela One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Corinthia' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Gothic' => array(
'variants' => array('400'),
'subsets' => array('gothic'),
'category' => 'sans-serif'
),
'Montagu Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Tiro Devanagari Marathi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Erica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rubik Burned' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Combo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Purple Purse' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Imbue' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Bokor' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Birthstone Bounce' => array(
'variants' => array('400', '500'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Kumar One Outline' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sofadi One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'GFS Neohellenic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek'),
'category' => 'sans-serif'
),
'IM Fell Double Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Astloch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Qahiri' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Tiro Devanagari Sanskrit' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Anybody' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Math' => array(
'variants' => array('400'),
'subsets' => array('math'),
'category' => 'sans-serif'
),
'Syne Tactile' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rubik Distressed' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Passero One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'BIZ UDMincho' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'greek-ext', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Shippori Antique B1' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rubik Beastly' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Snowburst One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'M PLUS 1 Code' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Zen Loop' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Tera' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'BioRhyme Expanded' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Sunshiney' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fuggles' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Miltonian' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Federant' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Smooch Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bonheur Royale' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Taprom' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Anek Tamil' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Rubik Iso' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Black And White Picture' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Suravaram' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Lavishly Yours' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Praise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Ms Madi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Aubrey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fasthand' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Vibes' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'display'
),
'Yuji Syuku' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Nova Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rubik Maze' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Langar' => array(
'variants' => array('400'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'display'
),
'Butcherman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Peta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fuzzy Bubbles' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Lacquer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ruge Boogie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Petemoss' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Nova Cut' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Edu VIC WA NT Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Noto Sans Tai Viet' => array(
'variants' => array('400'),
'subsets' => array('tai-viet'),
'category' => 'sans-serif'
),
'Waterfall' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Tiro Devanagari Hindi' => array(
'variants' => array('400', 'italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Music' => array(
'variants' => array('400'),
'subsets' => array('music'),
'category' => 'sans-serif'
),
'Sevillana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kaisei HarunoUmi' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Comforter Brush' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Edu NSW ACT Foundation' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Zen Tokyo Zoo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Single Day' => array(
'variants' => array('400'),
'subsets' => array('korean'),
'category' => 'display'
),
'Silkscreen' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Miltonian Tattoo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tai Heritage Pro' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'tai-viet', 'vietnamese'),
'category' => 'serif'
),
'Gentium Plus' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Big Shoulders Inline Text' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Stick No Bills' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'sans-serif'
),
'Rubik Microbe' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Preahvihear' => array(
'variants' => array('400'),
'subsets' => array('khmer', 'latin'),
'category' => 'sans-serif'
),
'Water Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Edu QLD Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ballet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Familjen Grotesk' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Dhurjati' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Noto Serif HK' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('chinese-hongkong', 'cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Samaritan' => array(
'variants' => array('400'),
'subsets' => array('samaritan'),
'category' => 'sans-serif'
),
'Moon Dance' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Luxurious Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Charis SIL' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Whisper' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gupter' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gwendolyn' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Neonderthaw' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Meow Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Anek Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif Balinese' => array(
'variants' => array('400'),
'subsets' => array('balinese'),
'category' => 'serif'
),
'Imperial Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Geostar Fill' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Xanh Mono' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Edu SA Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Finlandica' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kenia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rubik Glitch' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Symbols 2' => array(
'variants' => array('400'),
'subsets' => array('symbols'),
'category' => 'sans-serif'
),
'Anek Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Aboreto' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IBM Plex Sans Devanagari' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('kannada'),
'category' => 'serif'
),
'Kantumruy Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('khmer', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alumni Sans Pinstripe' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Edu TAS Beginner' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Koh Santepheap' => array(
'variants' => array('100', '300', '400', '700', '900'),
'subsets' => array('khmer', 'latin'),
'category' => 'display'
),
'Rubik Wet Paint' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Truculenta' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Gentium Book Plus' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans Thai Looped' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thai'),
'category' => 'sans-serif'
),
'Mea Culpa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Hubballi' => array(
'variants' => array('400'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'display'
),
'IBM Plex Sans Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Serif Khmer' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Send Flowers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Genos' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cherokee', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Tapestry' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Geostar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Anek Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tiro Bangla' => array(
'variants' => array('400', 'italic'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Splash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Gulzar' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Warnes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Gideon Roman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Cherish' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Mingzat' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'lepcha'),
'category' => 'sans-serif'
),
'Big Shoulders Inline Display' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'DynaPuff' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Love Light' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Grechen Fuemen' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Updock' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Luxurious Roman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Yuji Mai' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Oi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Lao' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('lao'),
'category' => 'sans-serif'
),
'Yuji Boku' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Tiro Tamil' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'serif'
),
'Moo Lah Lah' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Rubik Bubbles' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Flow Circular' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Ole' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Sinhala' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('sinhala'),
'category' => 'serif'
),
'Alumni Sans Collegiate One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Blaka' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Khmer' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Noto Sans Sora Sompeng' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('sora-sompeng'),
'category' => 'sans-serif'
),
'Are You Serious' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Fleur De Leah' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Nyiakeng Puachue Hmong' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('nyiakeng-puachue-hmong'),
'category' => 'serif'
),
'Blaka Hollow' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Serif Armenian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('armenian'),
'category' => 'serif'
),
'Anek Odia' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'oriya'),
'category' => 'sans-serif'
),
'Anek Kannada' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('kannada', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Libre Barcode EAN13 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Island Moments' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'My Soul' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Telugu' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('telugu'),
'category' => 'serif'
),
'Babylonica' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Passions Conflict' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Rashi Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Rubik Puddles' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'hebrew', 'latin', 'latin-ext'),
'category' => 'display'
),
'Noto Serif Georgian' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('georgian'),
'category' => 'serif'
),
'Noto Sans Egyptian Hieroglyphs' => array(
'variants' => array('400'),
'subsets' => array('egyptian-hieroglyphs'),
'category' => 'sans-serif'
),
'Estonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Ingrid Darling' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Indic Siyaq Numbers' => array(
'variants' => array('400'),
'subsets' => array('indic-siyaq-numbers'),
'category' => 'sans-serif'
),
'Kolker Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Caramel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Serif Hebrew' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew'),
'category' => 'serif'
),
'Kings' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Sassy Frass' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Javanese' => array(
'variants' => array('400', '700'),
'subsets' => array('javanese'),
'category' => 'sans-serif'
),
'Noto Serif Gujarati' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gujarati'),
'category' => 'serif'
),
'Noto Serif Tibetan' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('tibetan'),
'category' => 'serif'
),
'Grey Qo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Old Turkic' => array(
'variants' => array('400'),
'subsets' => array('old-turkic'),
'category' => 'sans-serif'
),
'Explora' => array(
'variants' => array('400'),
'subsets' => array('cherokee', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Twinkle Star' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Puppies Play' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Noto Sans Deseret' => array(
'variants' => array('400'),
'subsets' => array('deseret'),
'category' => 'sans-serif'
),
'Noto Serif Ethiopic' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('ethiopic'),
'category' => 'serif'
),
'Redacted Script' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'M PLUS Code Latin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans Carian' => array(
'variants' => array('400'),
'subsets' => array('carian'),
'category' => 'sans-serif'
),
'Noto Sans Saurashtra' => array(
'variants' => array('400'),
'subsets' => array('saurashtra'),
'category' => 'sans-serif'
),
'Noto Sans Lepcha' => array(
'variants' => array('400'),
'subsets' => array('lepcha'),
'category' => 'sans-serif'
),
'Noto Sans Balinese' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('balinese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Thaana' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('thaana'),
'category' => 'sans-serif'
),
'Flow Block' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Bamum' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('bamum'),
'category' => 'sans-serif'
),
'Flow Rounded' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Noto Sans Cherokee' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cherokee'),
'category' => 'sans-serif'
),
'Noto Sans Tagalog' => array(
'variants' => array('400'),
'subsets' => array('tagalog'),
'category' => 'sans-serif'
),
'Noto Serif Lao' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('lao'),
'category' => 'serif'
),
'Noto Traditional Nushu' => array(
'variants' => array('400'),
'subsets' => array('nushu'),
'category' => 'sans-serif'
),
'Redacted' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Noto Sans Multani' => array(
'variants' => array('400'),
'subsets' => array('multani'),
'category' => 'sans-serif'
),
'Noto Serif Gurmukhi' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans Buginese' => array(
'variants' => array('400'),
'subsets' => array('buginese'),
'category' => 'sans-serif'
),
'Noto Sans Avestan' => array(
'variants' => array('400'),
'subsets' => array('avestan', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Noto Sans Meetei Mayek' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('meetei-mayek'),
'category' => 'sans-serif'
),
'Noto Sans Adlam' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('adlam'),
'category' => 'sans-serif'
),
'Noto Sans Cham' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cham'),
'category' => 'sans-serif'
),
'Noto Serif Myanmar' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('myanmar'),
'category' => 'serif'
),
'Noto Sans Brahmi' => array(
'variants' => array('400'),
'subsets' => array('brahmi'),
'category' => 'sans-serif'
),
'Noto Sans Imperial Aramaic' => array(
'variants' => array('400'),
'subsets' => array('imperial-aramaic'),
'category' => 'sans-serif'
),
'Noto Sans Mongolian' => array(
'variants' => array('400'),
'subsets' => array('mongolian'),
'category' => 'sans-serif'
),
'Noto Sans Anatolian Hieroglyphs' => array(
'variants' => array('400'),
'subsets' => array('anatolian-hieroglyphs'),
'category' => 'sans-serif'
),
'Noto Sans Coptic' => array(
'variants' => array('400'),
'subsets' => array('coptic'),
'category' => 'sans-serif'
),
'Noto Sans Hanunoo' => array(
'variants' => array('400'),
'subsets' => array('hanunoo'),
'category' => 'sans-serif'
),
'Noto Sans Sundanese' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('sundanese'),
'category' => 'sans-serif'
),
'Noto Sans Marchen' => array(
'variants' => array('400'),
'subsets' => array('marchen'),
'category' => 'sans-serif'
),
'Noto Sans Caucasian Albanian' => array(
'variants' => array('400'),
'subsets' => array('caucasian-albanian'),
'category' => 'sans-serif'
),
'Noto Sans Canadian Aboriginal' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('canadian-aboriginal'),
'category' => 'sans-serif'
),
'Noto Sans Adlam Unjoined' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('adlam'),
'category' => 'sans-serif'
),
'Noto Sans Syriac' => array(
'variants' => array('100', '400', '900'),
'subsets' => array('syriac'),
'category' => 'sans-serif'
),
'Noto Sans Old Italic' => array(
'variants' => array('400'),
'subsets' => array('old-italic'),
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
'Noto Sans Old Persian' => array(
'variants' => array('400'),
'subsets' => array('old-persian'),
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
'Noto Sans Runic' => array(
'variants' => array('400'),
'subsets' => array('runic'),
'category' => 'sans-serif'
),
'Noto Sans Ol Chiki' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('ol-chiki'),
'category' => 'sans-serif'
),
'Noto Sans Ogham' => array(
'variants' => array('400'),
'subsets' => array('ogham'),
'category' => 'sans-serif'
),
'Noto Sans N Ko' => array(
'variants' => array('400'),
'subsets' => array('nko'),
'category' => 'sans-serif'
),
'Noto Serif Grantha' => array(
'variants' => array('400'),
'subsets' => array('grantha'),
'category' => 'serif'
),
'Noto Sans Tifinagh' => array(
'variants' => array('400'),
'subsets' => array('tifinagh'),
'category' => 'sans-serif'
),
'Noto Sans Wancho' => array(
'variants' => array('400'),
'subsets' => array('wancho'),
'category' => 'sans-serif'
),
'Noto Sans Inscriptional Pahlavi' => array(
'variants' => array('400'),
'subsets' => array('inscriptional-pahlavi'),
'category' => 'sans-serif'
),
'Noto Sans Syloti Nagri' => array(
'variants' => array('400'),
'subsets' => array('syloti-nagri'),
'category' => 'sans-serif'
),
'Noto Serif Yezidi' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('yezidi'),
'category' => 'serif'
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
'Noto Sans Pau Cin Hau' => array(
'variants' => array('400'),
'subsets' => array('pau-cin-hau'),
'category' => 'sans-serif'
),
'Noto Sans Phags Pa' => array(
'variants' => array('400'),
'subsets' => array('phags-pa'),
'category' => 'sans-serif'
),
'Noto Sans Inscriptional Parthian' => array(
'variants' => array('400'),
'subsets' => array('inscriptional-parthian'),
'category' => 'sans-serif'
),
'Noto Sans Old South Arabian' => array(
'variants' => array('400'),
'subsets' => array('old-south-arabian'),
'category' => 'sans-serif'
),
'Noto Sans Linear A' => array(
'variants' => array('400'),
'subsets' => array('linear-a'),
'category' => 'sans-serif'
),
'Noto Serif Tangut' => array(
'variants' => array('400'),
'subsets' => array('tangut'),
'category' => 'serif'
),
'Noto Sans Palmyrene' => array(
'variants' => array('400'),
'subsets' => array('palmyrene'),
'category' => 'sans-serif'
),
'Noto Sans Psalter Pahlavi' => array(
'variants' => array('400'),
'subsets' => array('psalter-pahlavi'),
'category' => 'sans-serif'
),
'Noto Sans Bhaiksuki' => array(
'variants' => array('400'),
'subsets' => array('bhaiksuki'),
'category' => 'sans-serif'
),
'Noto Sans Grantha' => array(
'variants' => array('400'),
'subsets' => array('grantha'),
'category' => 'sans-serif'
),
'Noto Sans Osage' => array(
'variants' => array('400'),
'subsets' => array('osage'),
'category' => 'sans-serif'
),
'Noto Sans Mro' => array(
'variants' => array('400'),
'subsets' => array('mro'),
'category' => 'sans-serif'
),
'Noto Sans Pahawh Hmong' => array(
'variants' => array('400'),
'subsets' => array('pahawh-hmong'),
'category' => 'sans-serif'
),
'Noto Sans Bassa Vah' => array(
'variants' => array('400'),
'subsets' => array('bassa-vah'),
'category' => 'sans-serif'
),
'Noto Serif Ahom' => array(
'variants' => array('400'),
'subsets' => array('ahom'),
'category' => 'serif'
),
'Noto Sans Mayan Numerals' => array(
'variants' => array('400'),
'subsets' => array('mayan-numerals'),
'category' => 'sans-serif'
),
'Noto Sans Lisu' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('lisu'),
'category' => 'sans-serif'
),
'Noto Sans Old North Arabian' => array(
'variants' => array('400'),
'subsets' => array('old-north-arabian'),
'category' => 'sans-serif'
),
'Noto Sans Batak' => array(
'variants' => array('400'),
'subsets' => array('batak'),
'category' => 'sans-serif'
),
'Noto Sans Hanifi Rohingya' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('hanifi-rohingya'),
'category' => 'sans-serif'
),
'Noto Sans Kayah Li' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('kayah-li'),
'category' => 'sans-serif'
),
'Noto Sans Cuneiform' => array(
'variants' => array('400'),
'subsets' => array('cuneiform'),
'category' => 'sans-serif'
),
'Noto Sans Tai Tham' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('tai-tham'),
'category' => 'sans-serif'
),
'Noto Sans Glagolitic' => array(
'variants' => array('400'),
'subsets' => array('glagolitic'),
'category' => 'sans-serif'
),
'Noto Sans Zanabazar Square' => array(
'variants' => array('400'),
'subsets' => array('zanabazar-square'),
'category' => 'sans-serif'
),
'Noto Sans Kharoshthi' => array(
'variants' => array('400'),
'subsets' => array('kharoshthi'),
'category' => 'sans-serif'
),
'Noto Sans Phoenician' => array(
'variants' => array('400'),
'subsets' => array('phoenician'),
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
'Noto Sans Tirhuta' => array(
'variants' => array('400'),
'subsets' => array('tirhuta'),
'category' => 'sans-serif'
),
'Noto Sans Sharada' => array(
'variants' => array('400'),
'subsets' => array('sharada'),
'category' => 'sans-serif'
),
'Noto Sans Osmanya' => array(
'variants' => array('400'),
'subsets' => array('osmanya'),
'category' => 'sans-serif'
),
'Noto Sans Nabataean' => array(
'variants' => array('400'),
'subsets' => array('nabataean'),
'category' => 'sans-serif'
),
'Noto Sans New Tai Lue' => array(
'variants' => array('400'),
'subsets' => array('new-tai-lue'),
'category' => 'sans-serif'
),
'Noto Sans Hatran' => array(
'variants' => array('400'),
'subsets' => array('hatran'),
'category' => 'sans-serif'
),
'Noto Sans Chakma' => array(
'variants' => array('400'),
'subsets' => array('chakma'),
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
'Noto Sans Elbasan' => array(
'variants' => array('400'),
'subsets' => array('elbasan'),
'category' => 'sans-serif'
),
'Noto Sans Masaram Gondi' => array(
'variants' => array('400'),
'subsets' => array('masaram-gondi'),
'category' => 'sans-serif'
),
'Noto Sans Ugaritic' => array(
'variants' => array('400'),
'subsets' => array('ugaritic'),
'category' => 'sans-serif'
),
'Noto Sans Takri' => array(
'variants' => array('400'),
'subsets' => array('takri'),
'category' => 'sans-serif'
),
'Noto Sans Old Permic' => array(
'variants' => array('400'),
'subsets' => array('old-permic'),
'category' => 'sans-serif'
),
'Noto Sans Lycian' => array(
'variants' => array('400'),
'subsets' => array('lycian'),
'category' => 'sans-serif'
),
'Noto Sans Tagbanwa' => array(
'variants' => array('400'),
'subsets' => array('tagbanwa'),
'category' => 'sans-serif'
),
'Noto Sans Warang Citi' => array(
'variants' => array('400'),
'subsets' => array('warang-citi'),
'category' => 'sans-serif'
),
'Noto Sans Limbu' => array(
'variants' => array('400'),
'subsets' => array('limbu'),
'category' => 'sans-serif'
),
'Noto Sans Lydian' => array(
'variants' => array('400'),
'subsets' => array('lydian'),
'category' => 'sans-serif'
),
'Noto Sans Linear B' => array(
'variants' => array('400'),
'subsets' => array('linear-b'),
'category' => 'sans-serif'
),
'Noto Sans Newa' => array(
'variants' => array('400'),
'subsets' => array('newa'),
'category' => 'sans-serif'
),
'Noto Sans Khojki' => array(
'variants' => array('400'),
'subsets' => array('khojki'),
'category' => 'sans-serif'
),
'Noto Sans Mandaic' => array(
'variants' => array('400'),
'subsets' => array('mandaic'),
'category' => 'sans-serif'
),
'Noto Sans Buhid' => array(
'variants' => array('400'),
'subsets' => array('buhid'),
'category' => 'sans-serif'
),
'Noto Sans Sogdian' => array(
'variants' => array('400'),
'subsets' => array('sogdian'),
'category' => 'sans-serif'
),
'Noto Sans Vai' => array(
'variants' => array('400'),
'subsets' => array('vai'),
'category' => 'sans-serif'
),
'Noto Sans Rejang' => array(
'variants' => array('400'),
'subsets' => array('rejang'),
'category' => 'sans-serif'
),
'Noto Sans Meroitic' => array(
'variants' => array('400'),
'subsets' => array('meroitic'),
'category' => 'sans-serif'
),
'Noto Sans Elymaic' => array(
'variants' => array('400'),
'subsets' => array('elymaic'),
'category' => 'sans-serif'
),
'Noto Sans Khudawadi' => array(
'variants' => array('400'),
'subsets' => array('khudawadi'),
'category' => 'sans-serif'
),
'Noto Sans Tai Le' => array(
'variants' => array('400'),
'subsets' => array('tai-le'),
'category' => 'sans-serif'
),
'Noto Sans Siddham' => array(
'variants' => array('400'),
'subsets' => array('siddham'),
'category' => 'sans-serif'
),
'Noto Sans Soyombo' => array(
'variants' => array('400'),
'subsets' => array('soyombo'),
'category' => 'sans-serif'
),
'Noto Sans Shavian' => array(
'variants' => array('400'),
'subsets' => array('shavian'),
'category' => 'sans-serif'
),
'Noto Sans Miao' => array(
'variants' => array('400'),
'subsets' => array('miao'),
'category' => 'sans-serif'
),
'Noto Sans Gunjala Gondi' => array(
'variants' => array('400'),
'subsets' => array('gunjala-gondi'),
'category' => 'sans-serif'
),
'Noto Sans Old Sogdian' => array(
'variants' => array('400'),
'subsets' => array('old-sogdian'),
'category' => 'sans-serif'
),
'Noto Sans Nushu' => array(
'variants' => array('400'),
'subsets' => array('nushu'),
'category' => 'sans-serif'
)
);