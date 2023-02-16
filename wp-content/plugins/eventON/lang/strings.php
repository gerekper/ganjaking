<?php
/**
 * Strings
 *
 * This file holds localisation strings that are scanned by localisation plugins. It is not included anywhere.
 *
 * @author 		EventON
 * @category 	lang
 * @package 	EventON/lang
 * @version     2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$OPT = get_option('evcal_options_evcal_1');
$sin_name = (!empty($OPT['evo_textstr_sin']))? $OPT['evo_textstr_sin']: __('Event','eventon');
$plu_name = (!empty($OPT['evo_textstr_plu']))? $OPT['evo_textstr_plu']: __('Events','eventon');

// month names
__( 'january', 'eventon' );
__( 'february', 'eventon' );
__( 'march', 'eventon' );
__( 'april', 'eventon' );
__( 'may', 'eventon' );
__( 'june', 'eventon' );
__( 'july', 'eventon' );
__( 'august', 'eventon' );
__( 'september', 'eventon' );
__( 'october', 'eventon' );
__( 'november', 'eventon' );
__( 'december', 'eventon' );

// day names
__( 'monday', 'eventon' );
__( 'tuesday', 'eventon' );
__( 'wednesday', 'eventon' );
__( 'thursday', 'eventon' );
__( 'friday', 'eventon' );
__( 'saturday', 'eventon' );
__( 'sunday', 'eventon' );

// taxonomies
__( 'event type', 'eventon' ); 
__( 'event type', 'eventon' ); 
__( 'event type 2', 'eventon' ); 
__( 'event type 3', 'eventon' ); 
__( 'event type 4', 'eventon' ); 
__( 'event type 5', 'eventon' ); 