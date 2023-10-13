<?php
/**
 * SVG field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$svg = ! empty( $svg ) ? $svg : '';

switch ( $svg ) {
	case 'minus':
		?>
		<svg viewBox="0 0 24 24" role="img" focusable="false" style="height: 1em; width: 1em; display: block; fill: currentcolor;">
			<rect height="2" rx="1" width="12" x="6" y="11"></rect>
		</svg>
		<?php
		break;

	case 'plus':
		?>
		<svg viewBox="0 0 24 24" role="img" focusable="false" style="height: 1em; width: 1em; display: block; fill: currentcolor;">
			<rect height="2" rx="1" width="12" x="6" y="11"></rect>
			<rect height="12" rx="1" width="2" x="11" y="6"></rect>
		</svg>
		<?php
		break;

	case 'arrow-right':
		?>
		<svg viewBox="0 0 24 24" role="presentation" aria-hidden="true" focusable="false" style="height:24px;width:24px;display:block;fill:currentColor">
			<path d="m0 12.5a.5.5 0 0 0 .5.5h21.79l-6.15 6.15a.5.5 0 1 0 .71.71l7-7v-.01a.5.5 0 0 0 .14-.35.5.5 0 0 0 -.14-.35v-.01l-7-7a .5.5 0 0 0 -.71.71l6.15 6.15h-21.79a.5.5 0 0 0 -.5.5z" fill-rule="evenodd"></path>
		</svg>
		<?php
		break;

	case 'arrow-right-alt':
		?>
		<svg viewBox="0 0 18 18" role="presentation" aria-hidden="true" focusable="false" style="height:18px;width:18px;display:block;stroke:currentColor">
			<path d="M5 1 L 13 9.3" fill-rule="evenodd" stroke-width="2"></path>
			<path d="M13 8.7 L 5 17 " fill-rule="evenodd" stroke-width="2"></path>
		</svg>
		<?php
		break;
	case 'arrow-right-alt-thin':
		?>
		<svg viewBox="0 0 18 18" role="presentation" aria-hidden="true" focusable="false" style="height:18px;width:18px;display:block;stroke:currentColor">
			<path d="M5 1 L 13 9.3" fill-rule="evenodd" stroke-width="1.3"></path>
			<path d="M13 8.7 L 5 17 " fill-rule="evenodd" stroke-width="1.3"></path>
		</svg>
		<?php
		break;
	case 'arrow-down-alt':
		?>
		<svg viewBox="0 0 18 18" role="presentation" aria-hidden="true" focusable="false" style="height: 16px; width: 16px; display: block; fill: currentcolor;">
			<path d="m16.29 4.3a1 1 0 1 1 1.41 1.42l-8 8a1 1 0 0 1 -1.41 0l-8-8a1 1 0 1 1 1.41-1.42l7.29 7.29z" fill-rule="evenodd"></path>
		</svg>
		<?php
		break;
	case 'info':
		?>
		<svg viewBox="0 0 24 24" role="img" focusable="false" style="height: 1em; width: 1em; fill:currentColor;">
			<path d="m12 0c-6.63 0-12 5.37-12 12s5.37 12 12 12 12-5.37 12-12-5.37-12-12-12zm0 23c-6.07 0-11-4.92-11-11s4.93-11 11-11 11 4.93 11 11-4.93 11-11 11zm4.75-14c0 1.8-.82 2.93-2.35 3.89-.23.14-1 .59-1.14.67-.4.25-.51.38-.51.44v2a .75.75 0 0 1 -1.5 0v-2c0-.74.42-1.22 1.22-1.72.17-.11.94-.55 1.14-.67 1.13-.71 1.64-1.41 1.64-2.61a3.25 3.25 0 0 0 -6.5 0 .75.75 0 0 1 -1.5 0 4.75 4.75 0 0 1 9.5 0zm-3.75 10a1 1 0 1 1 -2 0 1 1 0 0 1 2 0z" fill-rule="evenodd"></path>
		</svg>
		<?php
		break;

	case 'loader':
		?>
		<svg version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 40 30" enable-background="new 0 0 0 0" xml:space="preserve" style="width: 30px; opacity:.8">
			<circle fill="" stroke="none" cx="5" cy="15" r="0" style="fill:currentcolor">
				<animate attributeName="r" begin="0s" dur=".9s" repeatCount="indefinite" values="2;5;2"></animate>
			</circle>
			<circle fill="" stroke="none" cx="17" cy="15" r="0" style="fill:currentcolor">
				<animate attributeName="r" begin=".3s" dur=".9s" repeatCount="indefinite" values="2;5;2"></animate>
			</circle>
			<circle fill="" stroke="none" cx="29" cy="15" r="0" style="fill:currentcolor">
				<animate attributeName="r" begin=".6s" dur=".9s" repeatCount="indefinite" values="2;5;2"></animate>
			</circle>
		</svg>
		<?php
		break;
	case 'loader-2':
		?>
		<svg version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 40 30" enable-background="new 0 0 0 0" xml:space="preserve" style="width: 30px;">
			<circle fill="" stroke="none" cx="5" cy="15" r="5" style="fill:currentcolor; opacity: 0.3"></circle>
			<circle fill="" stroke="none" cx="20" cy="15" r="5" style="fill:currentcolor; opacity: 0.3"></circle>
			<circle fill="" stroke="none" cx="35" cy="15" r="5" style="fill:currentcolor; opacity: 0.3"></circle>
			<circle fill="" stroke="none" cx="5" cy="15" r="5" style="fill:currentcolor">
				<animate attributeName="opacity" dur=".9s" values="0;5;0;5;0;5;0" repeatCount="indefinite" begin="0s"></animate>
				<animate attributeName="r" begin="0s" dur=".9s" repeatCount="indefinite" values="2;6;2;6;2;6;2"></animate>
				<animate attributeName="cx" begin="0s" dur=".9s" repeatCount="indefinite" values="5;5;20;20;35;35;5"></animate>
			</circle>
		</svg>
		<?php
		break;
	case 'loader-alt':
		?>
		<svg version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 40 15" enable-background="new 0 0 0 0" xml:space="preserve" style="width: 25px;">
			<circle fill="" stroke="none" cx="5" cy="5" r="5" style="fill:currentcolor">
				<animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 0; 0 5" repeatCount="indefinite" begin="0.1"></animateTransform>
			</circle>
			<circle fill="" stroke="none" cx="20" cy="5" r="5" style="fill:currentcolor">
				<animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 0; 0 5" repeatCount="indefinite" begin="0.3"></animateTransform>
			</circle>
			<circle fill="" stroke="none" cx="35" cy="5" r="5" style="fill:currentcolor">
				<animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 0; 0 5" repeatCount="indefinite" begin="0.5"></animateTransform>
			</circle>
		</svg>
		<?php
		break;
	case 'no':
		?>
		<svg viewBox="0 0 18 18" role="presentation" aria-hidden="true" focusable="false" style="height:18px;width:18px;display:block;stroke:currentColor">
			<path d="M1 1 L 17 17" fill-rule="evenodd" stroke-width="2"></path>
			<path d="M17 1 L 1 17 " fill-rule="evenodd" stroke-width="2"></path>
		</svg>
		<?php
		break;
}
