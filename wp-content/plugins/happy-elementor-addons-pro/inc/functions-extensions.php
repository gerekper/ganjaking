<?php
/**
 * Extension functions and defination
 *
 */
defined( 'ABSPATH' ) || exit;

/**
 * Check if Image Masking is enabled
 *
 * @return bool
 */
function hapro_is_image_masking_enabled() {
	return apply_filters( 'happyaddons/extensions/image_masking', true );
}

/**
 * Check if Display Condition is enabled
 *
 * @return bool
 */
function hapro_is_display_condition_enabled() {
	return apply_filters( 'happyaddons/extensions/display_condition', true );
}

/**
 * Check if Happy Particle Effects is enabled
 *
 * @return bool
 */
function hapro_is_happy_particle_effects_enabled() {
	return apply_filters( 'happyaddons/extensions/happy_particle_effects', true );
}
