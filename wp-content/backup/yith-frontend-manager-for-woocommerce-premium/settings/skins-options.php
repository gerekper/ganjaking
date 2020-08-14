<?php
return array(
	'skins' => apply_filters( 'yith_wcms_skins_options', array(
			'skins_options_start' => array(
				'type' => 'sectionstart',
			),

			'skins_options_title' => array(
				'type'  => 'title',
				'title' => __( 'Dashboard skin', 'yith-frontend-manager-for-woocommerce' ),
			),

			'skins_options_choose' => array(
				'title'   => __( 'Choose the skin', 'yith-frontend-manager-for-woocommerce' ),
				'id'      => 'yith_wcfm_skin',
				'type'    => 'select',
				'options' => array(
					'none'    => __( 'None', 'yith-frontend-manager-for-woocommerce' ),
					'default' => __( 'Default', 'yith-frontend-manager-for-woocommerce' ),
					'skin-1'  => __( 'Skin-1', 'yith-frontend-manager-for-woocommerce' )
				),
				'css'     => 'min-width:150px;',
				'default' => 'skin-1',
				'label'   => 'choose the skin to use for your dashboard. If "none" is selected, the dashboard will be rendered inside your "my-account" page'
			),

			'skins_options_end' => array(
				'type' => 'sectionend',
			),
		)
	)
);