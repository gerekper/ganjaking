<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$section1 = array(
	'import_export_title'     => array(
		'name' => __( 'Import / Export points from csv file', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_import_export_title',
	),

	'options_import_form'     => array(
		'name'      => '',
		'desc'      => '',
		'yith-type' => 'options-import-form',
		'type'      => 'yith-field',
		'default'   => '',
		'id'        => 'ywpar_options_import_form',
	),

	'import_export_title_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_import_export_title_end',
	),

);

return array( 'import' => $section1 );
