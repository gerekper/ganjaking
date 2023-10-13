<?php
/**
 * Select-alt field.
 *
 * @var string       $id
 * @var string       $name
 * @var string       $class
 * @var string|array $value
 * @var array        $data
 * @var array        $custom_attributes
 * @var bool         $multiple
 * @var array        $options
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

// Late enqueue script and styles.
wp_enqueue_style( 'yith-wcbk-fields' );
wp_enqueue_script( 'yith-wcbk-fields' );

?>
<div class="yith-wcbk-select-alt__container">
	<?php
	yith_wcbk_print_field(
		array(
			'type'              => 'select',
			'name'              => $name,
			'class'             => $class,
			'value'             => $value,
			'data'              => $data,
			'custom_attributes' => $custom_attributes,
			'multiple'          => ! empty( $multiple ),
			'options'           => $options ?? array(),
		),
		true
	);
	?>
	<span class="yith-wcbk-select-alt__arrow yith-icon yith-icon-arrow-down-alt"></span>
</div>
