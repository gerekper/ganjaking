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
	'bulk_title'     => array(
		'name' => __( 'Bulk Actions', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_bulk_title',
	),

	'bulk_form'      => array(
		'name'      => '',
		'desc'      => '',
		'yith-type' => 'options-bulk-form',
		'type'      => 'yith-field',
		'default'   => '',
		'id'        => 'ywpar_bulk_form',
	),

	'bulk_title_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_bulk_title_end',
	),

);

return array( 'bulk' => $section1 );
