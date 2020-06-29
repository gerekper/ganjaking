<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 * @since 	  5.1.0
 * @lastfetch 17.12.2019
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
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Open Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lato' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Montserrat' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Roboto Condensed' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Source Sans Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Oswald' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Raleway' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Roboto Mono' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Poppins' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Noto Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'devanagari', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Roboto Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Merriweather' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'PT Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Ubuntu' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Playfair Display' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Open Sans Condensed' => array(
'variants' => array('300', '300italic', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Muli' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Lora' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Nunito' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Slabo 27px' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Titillium Web' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Rubik' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic', 'hebrew', 'latin'),
'category' => 'sans-serif'
),
'Fira Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Work Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Nanum Gothic' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Noto Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Nunito Sans' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Quicksand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Sans Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Arimo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Inconsolata' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Noto Sans KR' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Dosis' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans JP' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'sans-serif'
),
'Heebo' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Oxygen' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Anton' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Libre Baskerville' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Karla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Varela Round' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'hebrew', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Cabin' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Josefin Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Crimson Text' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bitter' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Barlow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Libre Franklin' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Hind' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Noto Sans TC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'sans-serif'
),
'Yanone Kaffeesatz' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lobster' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Fjalla One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Kanit' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Indie Flower' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Abel' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Source Code Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Dancing Script' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Arvo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Pacifico' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Mukta' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Exo 2' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Source Serif Pro' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Merriweather Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Shadows Into Light' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Barlow Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'EB Garamond' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Overpass' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bree Serif' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Questrial' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Zilla Slab' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Abril Fatface' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Asap' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Teko' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'IBM Plex Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Archivo Narrow' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Comfortaa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Acme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Exo' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Maven Pro' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Signika' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Amatic SC' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Catamaran' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'tamil', 'latin'),
'category' => 'sans-serif'
),
'Cairo' => array(
'variants' => array('200', '300', '400', '600', '700', '900'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'sans-serif'
),
'Fira Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Hind Siliguri' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'bengali'),
'category' => 'sans-serif'
),
'Crete Round' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Prompt' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Play' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Ubuntu Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Righteous' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Domine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'PT Sans Caption' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Assistant' => array(
'variants' => array('200', '300', '400', '600', '700', '800'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Patua One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vollkorn' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Baloo Bhai' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Ropa Sans' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Cinzel' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Rajdhani' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Bebas Neue' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Permanent Marker' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Amiri' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'serif'
),
'Cormorant Garamond' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'ABeeZee' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Archivo Black' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Rokkitt' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Cuprum' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Caveat' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'handwriting'
),
'Courgette' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Francois One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Monda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Hind Madurai' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'tamil', 'latin'),
'category' => 'sans-serif'
),
'Pathway Gothic One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Fredoka One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alegreya Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Satisfy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Alegreya' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Martel' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Alfa Slab One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'News Cycle' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Kalam' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'handwriting'
),
'Cardo' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'greek', 'greek-ext', 'latin'),
'category' => 'serif'
),
'Great Vibes' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Barlow Semi Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Didact Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Kaushan Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Nanum Myeongjo' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Old Standard TT' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Noticia Text' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Noto Sans SC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-simplified', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Tinos' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Tajawal' => array(
'variants' => array('200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Lobster Two' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cantarell' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fira Sans Extra Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Istok Web' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Sacramento' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Quattrocento Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Vidaloka' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gothic A1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Frank Ruhl Libre' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('latin-ext', 'hebrew', 'latin'),
'category' => 'serif'
),
'Bowlby One SC' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
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
'IBM Plex Serif' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Noto Serif JP' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'serif'
),
'Passion One' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Cookie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Concert One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Patrick Hand' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Prata' => array(
'variants' => array('400'),
'subsets' => array('cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'M PLUS 1p' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('japanese', 'latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Special Elite' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Poiret One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Quattrocento' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Orbitron' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Economica' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Saira Extra Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Playfair Display SC' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Volkhov' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Archivo' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'BenchNine' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Hind Vadodara' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Neuton' => array(
'variants' => array('200', '300', '400', 'italic', '700', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Russo One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Bangers' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Chivo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Josefin Slab' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Philosopher' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Saira Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Gochi Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Advent Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'greek', 'latin'),
'category' => 'sans-serif'
),
'Handlee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Neucha' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Gudea' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Hind Guntur' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('telugu', 'latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Parisienne' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Montserrat Alternates' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Sanchez' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Changa' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'sans-serif'
),
'Viga' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Sawarabi Mincho' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Ultra' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Taviraj' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'serif'
),
'Ruda' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Khand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Audiowide' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Paytone One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Armata' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Yantramanav' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Suranna' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Pontano Sans' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Cabin Condensed' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Hammersmith One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Yrsa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Nanum Gothic Coding' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'monospace'
),
'Arapey' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Spectral' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Unica One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Jaldi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Pridi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'serif'
),
'Alice' => array(
'variants' => array('400'),
'subsets' => array('cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Marck Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'handwriting'
),
'Tangerine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'PT Mono' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'monospace'
),
'M PLUS Rounded 1c' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('japanese', 'latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Amaranth' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Quantico' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Enriqueta' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Yellowtail' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mitr' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Architects Daughter' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sorts Mill Goudy' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Monoton' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Varela' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Reenie Beanie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Playball' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Scada' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Actor' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Saira' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Fugaz One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pragati Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Gentium Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Press Start 2P' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Bad Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Oleo Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Cormorant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Julius Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Kreon' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Adamina' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fauna One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Allura' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Karma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Gentium Book Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Bungee' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Homemade Apple' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Baloo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin', 'vietnamese'),
'category' => 'display'
),
'Damion' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sarala' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Signika Negative' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Squada One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lalezar' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'arabic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Unna' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Asap Condensed' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Glegoo' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Rock Salt' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Shadows Into Light Two' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Sintony' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Yeseva One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Lusitana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Khula' => array(
'variants' => array('300', '400', '600', '700', '800'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Ubuntu Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'monospace'
),
'El Messiri' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('cyrillic', 'arabic', 'latin'),
'category' => 'sans-serif'
),
'Merienda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Sarabun' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Covered By Your Grace' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lilita One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Palanquin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Pinyon Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'PT Serif Caption' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Rubik Mono One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Alex Brush' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Chewy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rasa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'serif'
),
'Cantata One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Sigmar One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Nanum Pen Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Molengo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Carter One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Staatliches' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Arbutus Slab' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Nothing You Could Do' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Forum' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Michroma' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Candal' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Alegreya Sans SC' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Mukta Malar' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'tamil', 'latin'),
'category' => 'sans-serif'
),
'Jura' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Abhaya Libre' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'sinhala', 'latin'),
'category' => 'serif'
),
'Spinnaker' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Tenor Sans' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Marcellus' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Marmelad' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'VT323' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Sawarabi Gothic' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Antic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Alef' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Boogaloo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mountains of Christmas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Antic Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mr Dafoe' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Coda' => array(
'variants' => array('400', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Rambla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Average' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'DM Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Aclonica' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'IBM Plex Mono' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Rancho' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Basic' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bevan' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Black Ops One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Cousine' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'hebrew', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Nobile' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Aldrich' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Caveat Brush' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Share Tech Mono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Rufina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Electrolize' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fredericka the Great' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Markazi Text' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'arabic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Reem Kufi' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'ZCOOL XiaoWei' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Lustria' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Noto Serif SC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-simplified', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Scheherazade' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'serif'
),
'Shrikhand' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'display'
),
'Days One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cabin Sketch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Itim' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'handwriting'
),
'Red Hat Display' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Pangolin' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Fira Mono' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'monospace'
),
'Italianno' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Space Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Biryani' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Arima Madurai' => array(
'variants' => array('100', '200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('latin-ext', 'tamil', 'latin', 'vietnamese'),
'category' => 'display'
),
'Niconne' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Syncopate' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rochester' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Halant' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Encode Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Coming Soon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Leckerli One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sunflower' => array(
'variants' => array('300', '500', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Berkshire Swash' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Just Another Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Overlock' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Lateef' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'handwriting'
),
'Norican' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Allerta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Radley' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Arsenal' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Hanuman' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Baloo Bhaina' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'oriya', 'latin', 'vietnamese'),
'category' => 'display'
),
'Oranienbaum' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Allerta Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Saira Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Gruppo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Racing Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'DM Serif Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Mali' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'handwriting'
),
'Lemonada' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'arabic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Marcellus SC' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Share' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Encode Sans Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Grand Hotel' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Mukta Vaani' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Telex' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Magra' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Aleo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Cinzel Decorative' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Copse' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Trirong' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'serif'
),
'Allan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Aladin' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Pattaya' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Ramabhadra' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Palanquin Dark' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Martel Sans' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Merienda One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lekton' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Kameron' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Overpass Mono' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'monospace'
),
'Raleway Dots' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Changa One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Annie Use Your Telescope' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mallanna' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Londrina Solid' => array(
'variants' => array('100', '300', '400', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alegreya SC' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Anonymous Pro' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'cyrillic', 'latin'),
'category' => 'monospace'
),
'Rye' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Yesteryear' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Nanum Brush Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Caudex' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'greek', 'greek-ext', 'latin'),
'category' => 'serif'
),
'Petit Formal Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Bentham' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Krub' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Rosario' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Nixie One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cedarville Cursive' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sriracha' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'handwriting'
),
'Carrois Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Shojumaru' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Marvel' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noto Serif TC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-traditional', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Big Shoulders Text' => array(
'variants' => array('100', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Contrail One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cambay' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Kosugi Maru' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Baloo Bhaijaan' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'arabic', 'latin', 'vietnamese'),
'category' => 'display'
),
'Bungee Inline' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Judson' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Kadwa' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Bai Jamjuree' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Carme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'IBM Plex Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Buenard' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Jockey One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Arizonia' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Coustard' => array(
'variants' => array('400', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mada' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Belleza' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Graduate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Niramit' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Gilda Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Voltaire' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Eczar' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Herr Von Muellerhoff' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Red Hat Text' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'GFS Didot' => array(
'variants' => array('400'),
'subsets' => array('greek'),
'category' => 'serif'
),
'Delius' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Slabo 13px' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Titan One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Fondamento' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Do Hyeon' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Encode Sans Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Deca' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Mukta Mahee' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'gurmukhi', 'latin'),
'category' => 'sans-serif'
),
'Kelly Slab' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Capriola' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bubblegum Sans' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Encode Sans Semi Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Sue Ellen Francisco' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'DM Serif Display' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Noto Serif KR' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Goudy Bookletter 1911' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Average Sans' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Poly' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Oxygen Mono' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'monospace'
),
'Maitree' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'serif'
),
'Schoolbell' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kristi' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Duru Sans' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Chakra Petch' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Amethysta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Calligraffitti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Love Ya Like A Sister' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Faustina' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Metrophobic' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Galada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'bengali'),
'category' => 'display'
),
'Freckle Face' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Ceviche One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Vesper Libre' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Ovo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cutive' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Montserrat Subrayada' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rozha One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Mr De Haviland' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Cutive Mono' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'monospace'
),
'Miriam Libre' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'hebrew', 'latin'),
'category' => 'sans-serif'
),
'Suez One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'hebrew', 'latin'),
'category' => 'serif'
),
'Lakki Reddy' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'handwriting'
),
'Laila' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Chonburi' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'display'
),
'Cormorant Infant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Baumans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Coda Caption' => array(
'variants' => array('800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'IM Fell Double Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Emilys Candy' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Federo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Modak' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'display'
),
'Inder' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Homenaje' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Secular One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'hebrew', 'latin'),
'category' => 'sans-serif'
),
'McLaren' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Andada' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Six Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Black Han Sans' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Seaweed Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Wallpoet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sansita' => array(
'variants' => array('400', 'italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Cambo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Unkempt' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Trocchi' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Averia Serif Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Knewave' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Pompiere' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Athiti' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'IM Fell English' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fanwood Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gurajada' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Balthazar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mate' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Prociono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gabriela' => array(
'variants' => array('400'),
'subsets' => array('cyrillic-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Doppio One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Montez' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Wendy One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Anaheim' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Amiko' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Convergence' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Gravitas One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alike' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Oregano' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Chelsea Market' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'La Belle Aurore' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Faster One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Strait' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Denk One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Brawler' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Finger Paint' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kosugi' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Vast Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sedgwick Ave' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Carrois Gothic SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'UnifrakturMaguntia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Crafty Girls' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lemon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Qwigley' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Corben' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Rouge Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Limelight' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Spicy Rice' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'BioRhyme' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Nova Square' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Share Tech' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Belgrano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cormorant SC' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Patrick Hand SC' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Oleo Script Swash Caps' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Megrim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Spectral SC' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Aguafina Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Averia Sans Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Skranji' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Mirza' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'display'
),
'Zeyada' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Amita' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'handwriting'
),
'Waiting for the Sunrise' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bowlby One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Battambang' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Proza Libre' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Quando' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Kurale' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'devanagari', 'latin'),
'category' => 'serif'
),
'Loved by the King' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bungee Shade' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Numans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Crushed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Stardos Stencil' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fresca' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Harmattan' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Clicker Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'K2D' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Podkova' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Andika' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Short Stack' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Cantora One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Meddon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Jua' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Charm' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'handwriting'
),
'Sniglet' => array(
'variants' => array('400', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Overlock SC' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Esteban' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Expletus Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alike Angular' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Give You Glory' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Voces' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Dawning of a New Day' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'IM Fell DW Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Baloo Chettan' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'malayalam', 'vietnamese'),
'category' => 'display'
),
'Krona One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Happy Monkey' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Mrs Saint Delafield' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Iceland' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Katibeh' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'display'
),
'Puritan' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Euphoria Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'The Girl Next Door' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Literata' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin-ext', 'greek', 'greek-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Princess Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Spirax' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mouse Memoirs' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bilbo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Gafata' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Pavanam' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'tamil', 'latin'),
'category' => 'sans-serif'
),
'Scope One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Cherry Swash' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Encode Sans Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Rationale' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Delius Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Codystar' => array(
'variants' => array('300', '400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Fjord One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Walter Turncoat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Baloo Thambi' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'tamil', 'latin', 'vietnamese'),
'category' => 'display'
),
'Lily Script One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Tauri' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Rammetto One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Mako' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Just Me Again Down Here' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Imprima' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bellefair' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'hebrew', 'latin'),
'category' => 'serif'
),
'Orienta' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'NTR' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Averia Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 39' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Wire One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Della Respira' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ledger' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'serif'
),
'Salsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Poller One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cormorant Upright' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Fontdiner Swanky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tienne' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Vampiro One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Baloo Paaji' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'gurmukhi', 'latin', 'vietnamese'),
'category' => 'display'
),
'Over the Rainbow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Artifika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Port Lligat Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rakkas' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'display'
),
'Frijole' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'David Libre' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin-ext', 'hebrew', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Life Savers' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Sarpanch' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Geo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'IM Fell English SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mandali' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Kumar One' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'display'
),
'Creepster' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mogra' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'display'
),
'Padauk' => array(
'variants' => array('400', '700'),
'subsets' => array('myanmar', 'latin'),
'category' => 'sans-serif'
),
'Bubbler One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Sirin Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Atma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'latin', 'bengali'),
'category' => 'display'
),
'Mansalva' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Holtwood One SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Habibi' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Shanti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Headland One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Manjari' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Chathura' => array(
'variants' => array('100', '300', '400', '700', '800'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Kranky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Elsie' => array(
'variants' => array('400', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Vibur' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Prosto One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Song Myung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Nova Mono' => array(
'variants' => array('400'),
'subsets' => array('greek', 'latin'),
'category' => 'monospace'
),
'Baloo Da' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'bengali', 'vietnamese'),
'category' => 'display'
),
'Antic Didone' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Englebert' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Cherry Cream Soda' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Port Lligat Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ranchers' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Aref Ruqaa' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'serif'
),
'Asul' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Coiny' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'tamil', 'latin', 'vietnamese'),
'category' => 'display'
),
'Livvic' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Dynalight' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Kotta One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Zilla Slab Highlight' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Peralta' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Noto Sans HK' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-hongkong', 'latin'),
'category' => 'sans-serif'
),
'Sail' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Timmana' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Medula One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Farsan' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Slackey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Saira Stencil One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Mate SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Ruluko' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Barriecito' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Engagement' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gugi' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Alatsi' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Inknut Antiqua' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Baloo Tamma' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'kannada', 'latin', 'vietnamese'),
'category' => 'display'
),
'Darker Grotesque' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Metamorphous' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Cormorant Unicase' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'B612 Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Mystery Quest' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Eater' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Dekko' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'handwriting'
),
'Sonsie One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Chicle' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Amarante' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Donegal One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Ramaraja' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Ruslan Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Macondo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Arya' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'sans-serif'
),
'Nova Round' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sree Krushnadevaraya' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Germania One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Yatra One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'display'
),
'Koulen' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Stalemate' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Sumana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Tulpen One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Condiment' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Almarai' => array(
'variants' => array('300', '400', '700', '800'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Kite One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Sarina' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Junge' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Delius Unicase' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Chau Philomene One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Simonetta' => array(
'variants' => array('400', 'italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Trade Winds' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rum Raisin' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Ribeye' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Khmer' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Italiana' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Vollkorn SC' => array(
'variants' => array('400', '600', '700', '900'),
'subsets' => array('latin-ext', 'cyrillic-ext', 'cyrillic', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Baskervville' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Cagliostro' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Flamenco' => array(
'variants' => array('300', '400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ma Shan Zheng' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Farro' => array(
'variants' => array('300', '400', '500', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Bilbo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Fenix' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Pirata One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Stint Ultra Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Vibes' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'display'
),
'Quintessential' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Rosarivo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Almendra' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Angkor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Chilanka' => array(
'variants' => array('400'),
'subsets' => array('latin', 'malayalam'),
'category' => 'handwriting'
),
'Lovers Quarrel' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'IM Fell French Canon' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Milonga' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Hepta Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Dorsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Akronim' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Nova Slim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Text Me One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Thasadith' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'New Rocker' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Manuale' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Gaegu' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Paprika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Charmonman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'handwriting'
),
'Ribeye Marrow' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Sancreek' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Hanalei Fill' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Londrina Outline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin', 'bengali'),
'category' => 'sans-serif'
),
'Libre Caslon Text' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'KoHo' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Ewert' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Notable' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Stoke' => array(
'variants' => array('300', '400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'IM Fell French Canon SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Moul' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Petrona' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Buda' => array(
'variants' => array('300'),
'subsets' => array('latin'),
'category' => 'display'
),
'Big Shoulders Display' => array(
'variants' => array('100', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Yeon Sung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Srisakdi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'display'
),
'Nova Flat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fascinate Inline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nokora' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Linden Hill' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Wellfleet' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Monsieur La Doulaise' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'League Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Swanky and Moo Moo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Marko One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Jacques Francois Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bokor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Sahitya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Sura' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Croissant One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Kavoon' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Uncial Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Meera Inimai' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin'),
'category' => 'sans-serif'
),
'Henny Penny' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Blinker' => array(
'variants' => array('100', '200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Nosifer' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Eagle Lake' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Joti One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Trochut' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'UnifrakturCook' => array(
'variants' => array('700'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell Great Primer' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Maiden Orange' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tillana' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'handwriting'
),
'Glass Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Libre Barcode 128' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Redressed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ranga' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'display'
),
'ZCOOL QingKe HuangYou' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Galdeano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Julee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Barrio' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Chela One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Be Vietnam' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Elsie Swash Caps' => array(
'variants' => array('400', '900'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Averia Gruesa Libre' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Diplomata' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Bahiana' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Offside' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Revalia' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Bayon' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Underdog' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Inika' => array(
'variants' => array('400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'IM Fell DW Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Stint Ultra Expanded' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Ruthie' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Fahkwang' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Autour One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'B612' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Griffy' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Risque' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Baloo Tammudu' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Chango' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Oldenburg' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Plaster' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Gamja Flower' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Kodchasan' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese', 'thai'),
'category' => 'sans-serif'
),
'Grenze' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Miniver' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Smokum' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Unlock' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Trykker' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Stylish' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Mrs Sheppards' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Margarine' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Smythe' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Montaga' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gupter' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Alata' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Iceberg' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ruge Boogie' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Monofett' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metal Mania' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Purple Purse' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Felipa' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Content' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Taprom' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Odor Mean Chey' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Modern Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Caesar Dressing' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Original Surfer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Asar' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Irish Grover' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Keania One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Molle' => array(
'variants' => array('italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Crimson Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Lancelot' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Dr Sugiyama' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Ravi Prakash' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'display'
),
'Libre Barcode 39 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Poor Story' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Rhodium Libre' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'devanagari', 'latin'),
'category' => 'serif'
),
'Liu Jian Mao Cao' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Atomic Age' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Siemreap' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Arbutus' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Fira Code' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin-ext', 'greek', 'cyrillic-ext', 'greek-ext', 'cyrillic', 'latin'),
'category' => 'monospace'
),
'Suwannaphum' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Bigshot One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 39 Extended' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Meie Script' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Devonshire' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Libre Barcode 39 Extended Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sunshiney' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kumar One Outline' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin-ext', 'latin'),
'category' => 'display'
),
'Snippet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'MedievalSharp' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Asset' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kavivanar' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'tamil', 'latin'),
'category' => 'handwriting'
),
'Jomhuria' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'arabic', 'latin'),
'category' => 'display'
),
'Diplomata SC' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Kantumruy' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Snowburst One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'IM Fell Great Primer SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Hi Melody' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Black And White Picture' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Major Mono Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'monospace'
),
'Flavors' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Jomolhari' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Gorditas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Freehand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Jim Nightshade' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Londrina Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Combo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Almendra SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'GFS Neohellenic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek'),
'category' => 'sans-serif'
),
'Dangrek' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Bungee Hairline' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Kenia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jacques Francois' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Romanesco' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Astloch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jolly Lodger' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Nova Oval' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kirang Haerang' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'IM Fell Double Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Goblin One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bigelow Rules' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Sevillana' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Miss Fajardose' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Almendra Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Calistoga' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'East Sea Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Metal' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Piedra' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Butterfly Kids' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Tenali Ramakrishna' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Galindo' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Bungee Outline' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
),
'Macondo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Solway' => array(
'variants' => array('300', '400', '500', '700', '800'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Nova Cut' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Miltonian Tattoo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fruktur' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Cute Font' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Public Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Supermercado One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'ZCOOL KuaiLe' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Seymour One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Nova Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bonbon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Miltonian' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mr Bedfort' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'handwriting'
),
'Geostar Fill' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Emblema One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Londrina Sketch' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Erica One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Lexend Exa' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Sulphur Point' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Sedgwick Ave Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'handwriting'
),
'Federant' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gidugu' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Fascinate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Butcherman' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Hanalei' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Sofadi One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Geostar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aubrey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Passero One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Suravaram' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Libre Caslon Display' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Preahvihear' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Girassol' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Libre Barcode 128 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gelasio' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Beth Ellen' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Peddana' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'serif'
),
'Tomorrow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Moulpali' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Ibarra Real Nova' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Stalinist One' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'cyrillic', 'latin'),
'category' => 'display'
),
'Single Day' => array(
'variants' => array('400'),
'subsets' => array('korean'),
'category' => 'display'
),
'Kdam Thmor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Chenla' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Courier Prime' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'monospace'
),
'Dhurjati' => array(
'variants' => array('400'),
'subsets' => array('telugu', 'latin'),
'category' => 'sans-serif'
),
'Lexend Tera' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Gayathri' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Fasthand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Odibee Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'BioRhyme Expanded' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Lacquer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Warnes' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Kulim Park' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'sans-serif'
),
'Long Cang' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Turret Road' => array(
'variants' => array('200', '300', '400', '500', '700', '800'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'display'
),
'Lexend Zetta' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Mega' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Lexend Peta' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Inria Serif' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin-ext', 'latin'),
'category' => 'serif'
),
'Lexend Giga' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Zhi Mang Xing' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Bahianita' => array(
'variants' => array('400'),
'subsets' => array('latin-ext', 'latin', 'vietnamese'),
'category' => 'display'
)
);

?>