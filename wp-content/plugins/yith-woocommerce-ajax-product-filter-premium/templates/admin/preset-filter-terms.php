<?php
/**
 * Preset filter - Term options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $id int
 * @var $taxonomy string
 * @var $terms array
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>


<div class="terms-wrapper">
	<?php
	if ( ! empty( $terms ) ) :
		foreach ( $terms as $term_id => $term_options ) :
			$filter_term = get_term( $term_id, $taxonomy );

			if ( ! $filter_term || is_wp_error( $filter_term ) ) {
				continue;
			}

			$term_name = $filter_term->name;

			YITH_WCAN()->admin->filter_term_field( $id, $term_id, $filter_term->name, $term_options );
		endforeach;
	endif;
	?>
</div>
