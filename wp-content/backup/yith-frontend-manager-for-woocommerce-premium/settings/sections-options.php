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

	'sections' => apply_filters( 'yith_wcfm_sections_options', array(

			'sections_options_start' => array(
				'type' => 'sectionstart',
			),

			'sections_options_title' => array(
				'type'  => 'title',
				'title' => __( 'Sections', 'yith-frontend-manager-for-woocommerce' ),
				'desc'  => __( 'Please, select which modules you want to enable on the frontend.', 'yith-frontend-manager-for-woocommerce' )
			),

			'sections_options_end' => array(
				'type' => 'sectionend',
			),
		)
	)
);