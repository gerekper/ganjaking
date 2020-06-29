<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

	'endpoints' => apply_filters( 'yith_wcfm_endpoints_options', array(

			'endpoints_options_start' => array(
				'type' => 'sectionstart',
			),

			'endpoints_options_title' => array(
				'type'  => 'title',
				'title' => __( 'Endpoints', 'yith-frontend-manager-for-woocommerce' ),
				'desc'  => __( 'Endpoints are appended to your page URLs to handle specific actions on the frontend manager pages. They should be unique and leaving them blank will disable the endpoint.', 'yith-frontend-manager-for-woocommerce' )
			),

			'endpoints_options_end' => array(
				'type' => 'sectionend',
			),
		)
	)
);