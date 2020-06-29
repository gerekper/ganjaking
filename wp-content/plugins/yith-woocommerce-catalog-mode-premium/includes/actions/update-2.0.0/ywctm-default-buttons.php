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
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ywctm_create_sample_buttons' ) ) {

	/**
	 * Run plugin upgrade to version 2.0.0
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_create_sample_buttons() {

		$sample_buttons = array(
			array(
				'name'    => esc_html__( 'Sample Button 1', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 14px;">' . esc_html__( 'ASK INFO', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'text_color'              => array(
						'default' => '#ffffff',
						'hover'   => '#ffffff',
					),
					'button_url'              => '',
					'background_color'        => array(
						'default' => '#e09004',
						'hover'   => '#b97600',
					),
					'border_color'            => array(
						'default' => '#e09004',
						'hover'   => '#b97600',
					),
					'border_style'            => array(
						'thickness' => 1,
						'radius'    => 50,
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 200,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
					),
					'padding_settings'        => array(
						'top'    => '5',
						'bottom' => '5',
						'left'   => '10',
						'right'  => '10',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Button 2', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 14px;">' . esc_html__( 'SEND INQUIRY', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'text_color'              => array(
						'default' => '#ffffff',
						'hover'   => '#ffffff',
					),
					'button_url'              => '',
					'background_color'        => array(
						'default' => '#36809a',
						'hover'   => '#215d72',
					),
					'border_color'            => array(
						'default' => '#36809a',
						'hover'   => '#215d72',
					),
					'border_style'            => array(
						'thickness' => 1,
						'radius'    => 50,
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 200,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
					),
					'padding_settings'        => array(
						'top'    => '5',
						'bottom' => '5',
						'left'   => '10',
						'right'  => '10',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Button 3', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 12px;">' . esc_html__( 'LOGIN TO SEE PRICE', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'text_color'              => array(
						'default' => '#247390',
						'hover'   => '#ffffff',
					),
					'button_url'              => '',
					'background_color'        => array(
						'default' => '#ffffff',
						'hover'   => '#247390',
					),
					'border_color'            => array(
						'default' => '#247390',
						'hover'   => '#247390',
					),
					'border_style'            => array(
						'thickness' => 1,
						'radius'    => 50,
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 150,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
					),
					'padding_settings'        => array(
						'top'    => '5',
						'bottom' => '5',
						'left'   => '10',
						'right'  => '10',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Label', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					/* translators: %s sample phone number */
					'label_text'              => '<div><span style="color: #9f4300; font-size: 16px;"><strong><span style="font-family: inherit;">' . esc_html__( 'Contact us to inquire about this product', 'yith-woocommerce-catalog-mode' ) . '</span></strong></span><br /><br /><span style="font-size: 14px;">' . sprintf( esc_html__( 'If you love this product and wish for a customized quote contact us at number %s and we will be happy to provide you with more info!', 'yith-woocommerce-catalog-mode' ), '<strong>+01234567890</strong>' ) . '</span></div>',
					'text_color'              => array(
						'default' => '#4b4b4b',
						'hover'   => '#4b4b4b',
					),
					'button_url'              => '',
					'background_color'        => array(
						'default' => '#f9f5f2',
						'hover'   => '#f9f5f2',
					),
					'border_color'            => array(
						'default' => '#e3bdaf',
						'hover'   => '#e3bdaf',
					),
					'border_style'            => array(
						'thickness' => 1,
						'radius'    => 5,
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => '',
						'unit'  => '',
					),
					'margin_settings'         => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
					),
					'padding_settings'        => array(
						'top'    => '20',
						'bottom' => '30',
						'left'   => '20',
						'right'  => '20',
					),
				),
			),
		);

		foreach ( $sample_buttons as $sample_button ) {

			$button_data = array(
				'post_title'   => $sample_button['name'],
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'publish',
				'post_author'  => 0,
				'post_type'    => 'ywctm-button-label',
			);
			$button_id   = wp_insert_post( $button_data );
			foreach ( $sample_button['options'] as $key => $value ) {
				update_post_meta( $button_id, 'ywctm_' . $key, $value );
			}
		}

		update_option( 'ywctm_update_version', YWCTM_VERSION );

	}

	add_action( 'admin_init', 'ywctm_create_sample_buttons' );
}
