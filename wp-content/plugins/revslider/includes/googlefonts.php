<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2020 ThemePunch
 * @since 	  5.1.0
 * @lastfetch 01.09.2020
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
'variants' => array('300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
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
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
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
'Poppins' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
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
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Merriweather' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Roboto Slab' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Ubuntu' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Playfair Display' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Open Sans Condensed' => array(
'variants' => array('300', '300italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Nunito' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lora' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Mukta' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'PT Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Work Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
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
'Noto Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Nanum Gothic' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Hind Siliguri' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fira Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Titillium Web' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nunito Sans' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Quicksand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Sans TC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'sans-serif'
),
'Heebo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Barlow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'PT Sans Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Anton' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Oxygen' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yanone Kaffeesatz' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Arimo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Karla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Dosis' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Crimson Text' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Josefin Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Hind' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Inconsolata' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Libre Baskerville' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Slabo 27px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Bebas Neue' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cabin' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bitter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Libre Franklin' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fjalla One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Teko' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
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
'Dancing Script' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Overpass' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Source Serif Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Source Code Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Varela Round' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Cairo' => array(
'variants' => array('200', '300', '400', '600', '700', '900'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'IBM Plex Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Exo 2' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Indie Flower' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Pacifico' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Merriweather Sans' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Arvo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Barlow Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Comfortaa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Russo One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Asap' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Inter' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Questrial' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Hind Madurai' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Zilla Slab' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Noto Sans SC' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'sans-serif'
),
'Archivo Narrow' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kanit' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Prompt' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Shadows Into Light' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'EB Garamond' => array(
'variants' => array('400', '500', '600', '700', '800', 'italic', '500italic', '600italic', '700italic', '800italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Abril Fatface' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Monda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Catamaran' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Alata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Architects Daughter' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Antic Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Acme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
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
'IBM Plex Serif' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Fira Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Righteous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Assistant' => array(
'variants' => array('200', '300', '400', '600', '700', '800'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Noto Sans HK' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('chinese-hongkong', 'latin'),
'category' => 'sans-serif'
),
'Cormorant Garamond' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Martel' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Exo' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Balsamiq Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Maven Pro' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Domine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Play' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Fredoka One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Caveat' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Amiri' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Tajawal' => array(
'variants' => array('200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'DM Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Permanent Marker' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Noto Serif JP' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('japanese', 'latin'),
'category' => 'serif'
),
'Patua One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'PT Sans Caption' => array(
'variants' => array('400', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Signika' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Crete Round' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Rajdhani' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Satisfy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ubuntu Condensed' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Vollkorn' => array(
'variants' => array('400', '500', '600', '700', '800', '900', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Archivo' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Cinzel' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Alfa Slab One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Cantarell' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Nanum Myeongjo' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Archivo Black' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Francois One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'ABeeZee' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Noticia Text' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Alegreya Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Courgette' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Barlow Semi Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Frank Ruhl Libre' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Lobster Two' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'M PLUS 1p' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kaushan Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Cardo' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Alegreya' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Kalam' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Hind Vadodara' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Concert One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Patrick Hand' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Great Vibes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Tinos' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cuprum' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Pathway Gothic One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sacramento' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Volkhov' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Rokkitt' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Playfair Display SC' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Old Standard TT' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Yantramanav' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Changa' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Advent Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fira Sans Extra Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Passion One' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Quattrocento Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Orbitron' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Didact Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Prata' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'serif'
),
'Gloria Hallelujah' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Montserrat Alternates' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Ropa Sans' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Taviraj' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'M PLUS Rounded 1c' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Istok Web' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'News Cycle' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ramabhadra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Sawarabi Mincho' => array(
'variants' => array('400'),
'subsets' => array('japanese', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Almarai' => array(
'variants' => array('300', '400', '700', '800'),
'subsets' => array('arabic'),
'category' => 'sans-serif'
),
'Neuton' => array(
'variants' => array('200', '300', '400', 'italic', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Chivo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Special Elite' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Poiret One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Khand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Philosopher' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'vietnamese'),
'category' => 'sans-serif'
),
'Josefin Slab' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sarabun' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'BenchNine' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cookie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Hind Guntur' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Pragati Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Baloo 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Yellowtail' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Parisienne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Saira' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Ultra' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Squada One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cormorant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Asap Condensed' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Jaldi' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Quattrocento' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Gudea' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Saira Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Neucha' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Bangers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Playball' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Press Start 2P' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'latin', 'latin-ext'),
'category' => 'display'
),
'Ruda' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Encode Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Faustina' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Gothic A1' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Markazi Text' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Oleo Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
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
'Sanchez' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Staatliches' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Monoton' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vidaloka' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Arapey' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Krona One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'IBM Plex Mono' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Electrolize' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Handlee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Hammersmith One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Karma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Adamina' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gentium Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Spectral' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Mulish' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'El Messiri' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'cyrillic', 'latin'),
'category' => 'sans-serif'
),
'Alice' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin'),
'category' => 'serif'
),
'Nanum Gothic Coding' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'monospace'
),
'Cabin Condensed' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Viga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Spartan' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Luckiest Guy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gentium Book Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Armata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Jockey One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sigmar One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Pontano Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Marck Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bad Script' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin'),
'category' => 'handwriting'
),
'Rock Salt' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Actor' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Carter One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fugaz One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Alef' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin'),
'category' => 'sans-serif'
),
'Tangerine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gochi Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Judson' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Amaranth' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mitr' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Julius Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Pridi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Sawarabi Gothic' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Aclonica' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Allura' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'PT Mono' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Tenor Sans' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Paytone One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Chakra Petch' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Audiowide' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Unna' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Itim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'DM Serif Display' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lusitana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Londrina Solid' => array(
'variants' => array('100', '300', '400', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Homemade Apple' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Damion' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sarala' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Quantico' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Palanquin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sorts Mill Goudy' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Signika Negative' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yeseva One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Merienda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Baloo Chettan 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'malayalam', 'vietnamese'),
'category' => 'display'
),
'Bungee' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Marcellus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Saira Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Noto Serif SC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Abhaya Libre' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'sinhala'),
'category' => 'serif'
),
'Arima Madurai' => array(
'variants' => array('100', '200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Alex Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Noto Serif TC' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('chinese-traditional', 'latin'),
'category' => 'serif'
),
'Chewy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Enriqueta' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Space Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Covered By Your Grace' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Nothing You Could Do' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Cousine' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Lalezar' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Pinyon Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Varela' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kreon' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kosugi Maru' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin'),
'category' => 'sans-serif'
),
'Shrikhand' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sriracha' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Saira Extra Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Rambla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shadows Into Light Two' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Ubuntu Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Cantata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Khula' => array(
'variants' => array('300', '400', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Basic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nanum Pen Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Candal' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Jura' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Yrsa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lilita One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Glegoo' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Mr Dafoe' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Spinnaker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Berkshire Swash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Leckerli One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Reem Kufi' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Days One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rufina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Scheherazade' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'serif'
),
'Red Hat Display' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Average' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mada' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('arabic', 'latin'),
'category' => 'sans-serif'
),
'Six Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mali' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'PT Serif Caption' => array(
'variants' => array('400', 'italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Arsenal' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Raleway Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bowlby One SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Black Han Sans' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Black Ops One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Forum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'display'
),
'Rancho' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rubik Mono One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alegreya Sans SC' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sintony' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Michroma' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Aleo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Niconne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'IBM Plex Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Encode Sans Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Mukta Malar' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Overpass Mono' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Allerta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Trirong' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Suez One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Coda' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Allan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fredericka the Great' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Arbutus Slab' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'VT323' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Bentham' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Alegreya SC' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Antic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Bevan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Boogaloo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Caveat Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Holtwood One SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Red Hat Text' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Italianno' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gruppo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kadwa' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin'),
'category' => 'serif'
),
'Share Tech Mono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Capriola' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Eczar' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Aldrich' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Palanquin Dark' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Share' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Overlock' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Reenie Beanie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Anonymous Pro' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'greek', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'DM Serif Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Cabin Sketch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pangolin' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Schoolbell' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Martel Sans' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Hanuman' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Rozha One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Annie Use Your Telescope' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Marmelad' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bai Jamjuree' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Coda Caption' => array(
'variants' => array('800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Manrope' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'greek', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Syncopate' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cinzel Decorative' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Krub' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Gelasio' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Sunflower' => array(
'variants' => array('300', '500', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Kameron' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Lateef' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'handwriting'
),
'Contrail One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Allerta Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Just Another Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Suranna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Do Hyeon' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Nixie One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Molengo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Lemonada' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Racing Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Scada' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nobile' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oranienbaum' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Cutive Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Telex' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fira Mono' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Seaweed Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Deca' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Public Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Marcellus SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Bungee Inline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Halant' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Arizonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Graduate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Biryani' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Miriam Libre' => array(
'variants' => array('400', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Magra' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Caudex' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Aladin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Average Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yesteryear' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Double Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Coustard' => array(
'variants' => array('400', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mallanna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Coming Soon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lustria' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Galada' => array(
'variants' => array('400'),
'subsets' => array('bengali', 'latin'),
'category' => 'display'
),
'Noto Serif KR' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Pattaya' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Belleza' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alike Angular' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Jost' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sansita' => array(
'variants' => array('400', 'italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Duru Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Knewave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Delius' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fauna One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Tenali Ramakrishna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Chau Philomene One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rochester' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Norican' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Sedgwick Ave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Kristi' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Belgrano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sen' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Petit Formal Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Voltaire' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Changa One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Montserrat Subrayada' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Literata' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('cyrillic', 'greek', 'greek-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Grand Hotel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gilda Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Herr Von Muellerhoff' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Carrois Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Bubblegum Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Amiko' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'B612' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'GFS Didot' => array(
'variants' => array('400'),
'subsets' => array('greek'),
'category' => 'serif'
),
'Averia Serif Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ovo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Corben' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mukta Vaani' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Love Ya Like A Sister' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Merienda One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Copse' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Chelsea Market' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Slabo 13px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Cambay' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Buenard' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Laila' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Charm' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Oxygen Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Calligraffitti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Nanum Brush Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Montaga' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Chonburi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Niramit' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Red Rose' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Metrophobic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Maitree' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'serif'
),
'Emilys Candy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Radley' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Harmattan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'ZCOOL XiaoWei' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'serif'
),
'Mr De Haviland' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Rosario' => array(
'variants' => array('300', '400', '500', '600', '700', '300italic', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Titan One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Carme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Battambang' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Gabriela' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin'),
'category' => 'serif'
),
'Lekton' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Poly' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Kelly Slab' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sue Ellen Francisco' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Oregano' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'David Libre' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('hebrew', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'IM Fell English' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Athiti' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'UnifrakturMaguntia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gugi' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Marvel' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Amethysta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Jua' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Cutive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ceviche One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cormorant Infant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Cedarville Cursive' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Amita' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Trocchi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Goudy Bookletter 1911' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Wallpoet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vast Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mirza' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Thasadith' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Federo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Kosugi' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'japanese', 'latin'),
'category' => 'sans-serif'
),
'Shanti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Esteban' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Montez' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rouge Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Freckle Face' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Piedra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Secular One' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Manjari' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Podkova' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Alike' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Spectral SC' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Kurale' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Mrs Saint Delafield' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Convergence' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fjord One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sniglet' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Anaheim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Gravitas One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Inder' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mansalva' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'La Belle Aurore' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lemon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
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
'Pompiere' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Limelight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Notable' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Gurajada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Fanwood Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Oleo Script Swash Caps' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mandali' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Atma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'display'
),
'Padauk' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'myanmar'),
'category' => 'sans-serif'
),
'Wendy One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cormorant SC' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Homenaje' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Clicker Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Pavanam' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'sans-serif'
),
'Qwigley' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Stardos Stencil' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Meera Inimai' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tamil'),
'category' => 'sans-serif'
),
'Share Tech' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Andada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Baskervville' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Rammetto One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Finger Paint' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Baumans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 39' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Encode Sans Semi Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Expletus Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Quando' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Frijole' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'McLaren' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rasa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'serif'
),
'K2D' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Trade Winds' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vesper Libre' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Faster One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bellefair' => array(
'variants' => array('400'),
'subsets' => array('hebrew', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Unkempt' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell DW Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Doppio One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cormorant Upright' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'B612 Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Waiting for the Sunrise' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Crafty Girls' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Katibeh' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Rakkas' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Kite One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Patrick Hand SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Srisakdi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'display'
),
'Happy Monkey' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Shade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Dawning of a New Day' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fondamento' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Tauri' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Proza Libre' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nova Square' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gafata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mountains of Christmas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Brawler' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Poller One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Denk One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'BioRhyme' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Farro' => array(
'variants' => array('300', '400', '500', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
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
'IM Fell English SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sarpanch' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bowlby One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Salsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Andika' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Meddon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Averia Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Imprima' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Ledger' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Delius Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Cherry Cream Soda' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Megrim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Skranji' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Antic Didone' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Walter Turncoat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Livvic' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Sail' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Libre Caslon Text' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Baloo Thambi 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Baloo Da 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('bengali', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Euphoria Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Crimson Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800', '900', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Life Savers' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mouse Memoirs' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Strait' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Short Stack' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Aguafina Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lily Script One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Zeyada' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mako' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Encode Sans Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Carrois Gothic SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cambo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Loved by the King' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Timmana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Comic Neue' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Arya' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Give You Glory' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Asul' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Numans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Monsieur La Doulaise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Iceland' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metamorphous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Almendra' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Saira Stencil One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Bilbo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Cherry Swash' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tienne' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Codystar' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Headland One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Orienta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Be Vietnam' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Creepster' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Peralta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Over the Rainbow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Elsie' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Darker Grotesque' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Shojumaru' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Encode Sans Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Voces' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sonsie One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Prosto One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Amarante' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Artifika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ruluko' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Londrina Outline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ma Shan Zheng' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Ibarra Real Nova' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Chathura' => array(
'variants' => array('100', '300', '400', '700', '800'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Caladea' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'The Girl Next Door' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Averia Sans Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Mono' => array(
'variants' => array('400'),
'subsets' => array('greek', 'latin'),
'category' => 'monospace'
),
'Big Shoulders Display' => array(
'variants' => array('100', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Dekko' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Geo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cantora One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Hi Melody' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Vollkorn SC' => array(
'variants' => array('400', '600', '700', '900'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Farsan' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Puritan' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Baloo Bhaina 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'oriya', 'vietnamese'),
'category' => 'display'
),
'Kumar One' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Inknut Antiqua' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Koulen' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Eater' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Medula One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Port Lligat Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Blinker' => array(
'variants' => array('100', '200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
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
'Ribeye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Slackey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Round' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mukta Mahee' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Germania One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vibur' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Coiny' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil', 'vietnamese'),
'category' => 'display'
),
'Aref Ruqaa' => array(
'variants' => array('400', '700'),
'subsets' => array('arabic', 'latin'),
'category' => 'serif'
),
'Kranky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Engagement' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Dynalight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Flamenco' => array(
'variants' => array('300', '400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vampiro One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Song Myung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Alatsi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Poor Story' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Fresca' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Scope One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Italiana' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sumana' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'IM Fell French Canon SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Spicy Rice' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 39 Extended Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Just Me Again Down Here' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Sirin Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ranchers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Spirax' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Port Lligat Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Ruslan Display' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Yeon Sung' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Ewert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'League Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lexend Zetta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Yatra One' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Sarina' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Delius Unicase' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Wire One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Calistoga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Jacques Francois Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Paprika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Macondo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mate SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ribeye Marrow' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nosifer' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Gaegu' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Ramaraja' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Fontdiner Swanky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tillana' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Fira Code' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'latin', 'latin-ext'),
'category' => 'monospace'
),
'Manuale' => array(
'variants' => array('400', '500', '600', '700', 'italic', '500italic', '600italic', '700italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Pirata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Oxanium' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kodchasan' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Modak' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Bilbo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Crushed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Rosarivo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Henny Penny' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Grenze' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Chela One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sancreek' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mogra' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Fenix' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Fascinate Inline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gupter' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Vibes' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin'),
'category' => 'display'
),
'Girassol' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mystery Quest' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Stint Ultra Expanded' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Text Me One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Swanky and Moo Moo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rationale' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Habibi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Big Shoulders Text' => array(
'variants' => array('100', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Trochut' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Gamja Flower' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Chango' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Donegal One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lovers Quarrel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Quintessential' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gayathri' => array(
'variants' => array('100', '400', '700'),
'subsets' => array('latin', 'malayalam'),
'category' => 'sans-serif'
),
'Stint Ultra Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Averia Gruesa Libre' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bubbler One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sedgwick Ave Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'handwriting'
),
'Stoke' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Unlock' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Charmonman' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'handwriting'
),
'Barrio' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell French Canon' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Buda' => array(
'variants' => array('300'),
'subsets' => array('latin'),
'category' => 'display'
),
'Angkor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Bellota Text' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Lexend Peta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kotta One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Simonetta' => array(
'variants' => array('400', 'italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Barriecito' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Cormorant Unicase' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Major Mono Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'monospace'
),
'Englebert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Moul' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Mina' => array(
'variants' => array('400', '700'),
'subsets' => array('bengali', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Stylish' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Akronim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Tulpen One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Chicle' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Condiment' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Baloo Paaji 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gurmukhi', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Black And White Picture' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Courier Prime' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Khmer' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Nokora' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Overlock SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Great Primer' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Cagliostro' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Ranga' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'display'
),
'Jomhuria' => array(
'variants' => array('400'),
'subsets' => array('arabic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Kavoon' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lakki Reddy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'handwriting'
),
'Rowdies' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Marko One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Content' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Bayon' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Uncial Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Prociono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Eagle Lake' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Maiden Orange' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Julee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'UnifrakturCook' => array(
'variants' => array('700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Inika' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Junge' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Margarine' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Della Respira' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Petrona' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'serif'
),
'Dorsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Stalemate' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
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
'Sura' => array(
'variants' => array('400', '700'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Recursive' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese'),
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
'Fahkwang' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'KoHo' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'thai', 'vietnamese'),
'category' => 'sans-serif'
),
'Metal Mania' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Trykker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Nova Flat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Linden Hill' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Elsie Swash Caps' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bigshot One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Miniver' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tomorrow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Arbutus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Autour One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruthie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Iceberg' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Joti One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Atomic Age' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kantumruy' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Diplomata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Smokum' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cute Font' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Hanalei Fill' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Bhai 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('gujarati', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Galdeano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rhodium Libre' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext'),
'category' => 'serif'
),
'Croissant One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Cut' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Smythe' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jomolhari' => array(
'variants' => array('400'),
'subsets' => array('latin', 'tibetan'),
'category' => 'serif'
),
'Modern Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Glass Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bahiana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Rum Raisin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Offside' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Wellfleet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sree Krushnadevaraya' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'MedievalSharp' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Molle' => array(
'variants' => array('italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Snippet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'MuseoModerno' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Bellota' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('cyrillic', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Griffy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell DW Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Varta' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Monofett' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'ZCOOL QingKe HuangYou' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Original Surfer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Caslon Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Libre Barcode 128 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Oldenburg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
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
'Lexend Exa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Baloo Tamma 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('kannada', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Princess Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Diplomata SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Outline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Gorditas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Slim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 128' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kavivanar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'tamil'),
'category' => 'handwriting'
),
'Revalia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Meie Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Keania One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Dangrek' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'East Sea Dokdo' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Odor Mean Chey' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Romanesco' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lancelot' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Felipa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Purple Purse' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Goblin One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Emblema One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Flavors' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Devonshire' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Inria Serif' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Jim Nightshade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Taprom' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Mrs Sheppards' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Asset' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Dr Sugiyama' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Underdog' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Siemreap' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Galindo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Caesar Dressing' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Risque' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ravi Prakash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'display'
),
'Gotu' => array(
'variants' => array('400'),
'subsets' => array('devanagari', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Irish Grover' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kumar One Outline' => array(
'variants' => array('400'),
'subsets' => array('gujarati', 'latin', 'latin-ext'),
'category' => 'display'
),
'Long Cang' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'IM Fell Great Primer SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Londrina Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Freehand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Almendra SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Libre Barcode 39 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Suwannaphum' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Lexend Tera' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Lacquer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Snowburst One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Plaster' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Double Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sevillana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Federant' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sulphur Point' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Almendra Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jolly Lodger' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jacques Francois' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sora' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Chilanka' => array(
'variants' => array('400'),
'subsets' => array('latin', 'malayalam'),
'category' => 'handwriting'
),
'Macondo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Butcherman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Metal' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Miss Fajardose' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Mr Bedfort' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Solway' => array(
'variants' => array('300', '400', '500', '700', '800'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sunshiney' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Preahvihear' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'GFS Neohellenic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek'),
'category' => 'sans-serif'
),
'Astloch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bigelow Rules' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruge Boogie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Seymour One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Erica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Beth Ellen' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Supermercado One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bonbon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kirang Haerang' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'display'
),
'Liu Jian Mao Cao' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'Chenla' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Sofadi One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Oval' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Butterfly Kids' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Odibee Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fruktur' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'DM Mono' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Combo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Hairline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Peddana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Zhi Mang Xing' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'handwriting'
),
'ZCOOL KuaiLe' => array(
'variants' => array('400'),
'subsets' => array('chinese-simplified', 'latin'),
'category' => 'display'
),
'Libre Barcode 39 Extended' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fascinate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aubrey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Suravaram' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Miltonian Tattoo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Passero One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Gidugu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Miltonian' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Geostar Fill' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Londrina Sketch' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Geostar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Fasthand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Inria Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Epilogue' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900', '100italic', '200italic', '300italic', 'italic', '500italic', '600italic', '700italic', '800italic', '900italic'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Kenia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kdam Thmor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Moulpali' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Grenze Gotisch' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Stalinist One' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Giga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Hanalei' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'BioRhyme Expanded' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Dhurjati' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Single Day' => array(
'variants' => array('400'),
'subsets' => array('korean'),
'category' => 'display'
),
'Turret Road' => array(
'variants' => array('200', '300', '400', '500', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lexend Mega' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'sans-serif'
),
'Bahianita' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Baloo Tammudu 2' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'telugu', 'vietnamese'),
'category' => 'display'
),
'Viaoda Libre' => array(
'variants' => array('400'),
'subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'),
'category' => 'display'
),
'Warnes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
)
);

?>