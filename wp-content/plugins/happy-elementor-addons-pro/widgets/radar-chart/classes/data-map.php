<?php
/**
 * Chart widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget\Radar_Chart;

defined( 'ABSPATH' ) || die();


class Data_Map {

	public static function initial($settings) {
		$data_settings = json_encode(
			[
				'type'    => 'radar',
				'data'    => [
					'labels'   => explode(',', esc_html( $settings['labels'] ) ),
					'datasets' => self::chart_dataset($settings),
				],
				'options' => self::chart_options($settings)
			]
		);
		return $data_settings;
	}

	public static function chart_dataset($settings) {

		$datasets = [];
		$items = $settings['chart_data'];

		if ( !empty( $items ) ) {
			foreach ( $items as $item ) {
				$item['label']                	= !empty( $item['label'] ) ? esc_html( $item['label'] ) : '';
				$item['data']                	 = !empty( $item['data'] ) ? array_map( 'trim', explode(',', $item['data'] ) ) : '';
				$item['backgroundColor']      	= !empty( $item['background_color'] ) ? $item['background_color'] : 'rgba(86, 45, 212, 0.7)';
				$item['borderColor']          	= !empty( $item['border_color'] ) ? $item['border_color'] : '#602edc';
				$item['borderWidth']          	= ( $settings['border_width']['size'] !== '' ) ? $settings['border_width']['size'] : 1;
				$item['pointBorderColor']     	= !empty( $item['pointer_color'] ) ? $item['pointer_color'] : '#3449CD';
				$item['pointBackgroundColor']   = !empty( $item['pointer_color'] ) ? $item['pointer_color'] : '#3449CD';
				$item['pointBorderWidth']     	= ( $settings['pointer_border_width']['size'] !== '' ) ? $settings['pointer_border_width']['size'] : 1;

				$datasets[] = $item;
			}
		}

		return $datasets;
	}


	public static function chart_options($settings) {

		$tooltips_display = $settings['tooltip_display'] == 'yes' ? true : false;
		$legend_display   = $settings['legend_display'] == 'yes' ? true : false;
		$title_display    = $settings['title_display'] == 'yes' ? true : false;

		$legend_style = [
			'boxWidth'   => !empty( $settings['legend_box_width']['size'] ) ? $settings['legend_box_width']['size'] : 45,
			'fontFamily' => !empty( $settings['legend_font_family'] ) ? $settings['legend_font_family'] : 'auto',
			'fontSize'   => !empty( $settings['legend_font_size']['size'] ) ? $settings['legend_font_size']['size'] : 12,
			'fontStyle'  => (!empty( $settings['legend_font_style'] ) ? $settings['legend_font_style'] : '') . ' ' . (!empty( $settings['legend_font_weight'] ) ? $settings['legend_font_weight'] : ''),
			'fontColor'  => !empty( $settings['legend_font_color'] ) ? $settings['legend_font_color'] : '#222',
		];

		$tooltip = [
			'enabled' 			=> $tooltips_display,
			'backgroundColor' 	=> !empty( $settings['tooltip_background_color'] ) ? $settings['tooltip_background_color'] : 'rgba(0, 0, 0, .7)',
			'borderWidth' 		=> !empty( $settings['tooltip_border_width']['size'] ) ? $settings['tooltip_border_width']['size'] : 0,
			'borderColor' 		=> !empty( $settings['tooltip_border_color'] ) ? $settings['tooltip_border_width'] : '',
			'titleFontFamily' 	=> !empty( $settings['tooltip_title_font_family'] ) ? $settings['tooltip_title_font_family'] : 'auto',
			'titleFontSize'   	=> !empty( $settings['tooltip_title_font_size']['size'] ) ? $settings['tooltip_title_font_size']['size'] : 12,
			'titleFontStyle'	=> (!empty( $settings['tooltip_title_font_style'] ) ? $settings['tooltip_title_font_style'] : '') . ' ' . (!empty( $settings['tooltip_title_font_weight'] ) ? $settings['tooltip_title_font_weight'] : ''),
			'titleFontColor'  	=> !empty( $settings['tooltip_title_font_color'] ) ? $settings['tooltip_title_font_color'] : '#fff',
			'bodyFontFamily' 	=> !empty( $settings['tooltip_body_font_family'] ) ? $settings['tooltip_body_font_family'] : 'auto',
			'bodyFontSize'   	=> !empty( $settings['tooltip_body_font_size']['size'] ) ? $settings['tooltip_body_font_size']['size'] : 11,
			'bodyFontStyle'  	=> (!empty( $settings['tooltip_body_font_style'] ) ? $settings['tooltip_body_font_style'] : '') . ' ' . (!empty( $settings['tooltip_body_font_weight'] ) ? $settings['tooltip_body_font_weight'] : ''),
			'bodyFontColor'  	=> !empty( $settings['tooltip_body_font_color'] ) ? $settings['tooltip_body_font_color'] : '#f7f7f7',
			'cornerRadius'  	=> !empty( $settings['tooltip_border_radius']['size'] ) ? $settings['tooltip_border_radius']['size'] : 6,
			'xPadding'  		=> !empty( $settings['tooltip_padding']['size'] ) ? $settings['tooltip_padding']['size'] : 6,
			'yPadding'  		=> !empty( $settings['tooltip_padding']['size'] ) ? $settings['tooltip_padding']['size'] : 6,
			'caretSize'  		=> !empty( $settings['tooltip_caret_size']['size'] ) ? $settings['tooltip_caret_size']['size'] : 5,
			'mode' 				=> !empty( $settings['tooltip_mode'] ) ? $settings['tooltip_mode'] : 'nearest',
		];

		$options = [
			'title' => [
				'display' => $title_display,
				'text' => $settings['chart_title'],
				'fontFamily' => !empty( $settings['title_font_family'] ) ? $settings['title_font_family'] : 'auto',
				'fontSize'   => !empty( $settings['title_font_size']['size'] ) ? $settings['title_font_size']['size'] : 18,
				'fontStyle'  => (!empty( $settings['title_font_style'] ) ? $settings['title_font_style'] : '') . ' ' . (!empty( $settings['title_font_weight'] ) ? $settings['title_font_weight'] : ''),
				'fontColor'  => !empty( $settings['title_font_color'] ) ? $settings['title_font_color'] : '#222',
			],
			'tooltips' => $tooltip,
			'legend' => [
				'display'  => $legend_display,
				'position' => !empty( $settings['legend_position'] ) ? $settings['legend_position'] : 'top',
				'reverse'  => $settings['legend_reverse'] == 'yes' ? true : false,
				'labels' => $legend_style,
			],
			'animation' => [
				'easing' => $settings['animation_options'],
				'duration' => $settings['chart_animation_duration'],
			],
			'layout' => [
				'padding' => [
					'top' => $settings['layout_padding']['top'],
					'right' => $settings['layout_padding']['right'],
					'bottom' => $settings['layout_padding']['bottom'],
					'left' => $settings['layout_padding']['left']
				]
			],
			'maintainAspectRatio' => false,
			'elements' => [
				'line' => [ 
					'tension' => !empty( $settings['line_tension']['size'] ) ? $settings['line_tension']['size'] / 100 : 0,
				]
			]
		];

		return $options;
	}
}
