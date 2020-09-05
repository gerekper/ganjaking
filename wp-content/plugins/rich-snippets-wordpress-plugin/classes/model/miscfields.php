<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class MiscFields_Model.
 *
 * Adds some more interesting fields.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.2.0
 */
final class MiscFields_Model {

	/**
	 * Prints the fields on post-edit screen.
	 *
	 * @param array $args
	 *
	 * @since 2.2.0
	 *
	 */
	public static function fields( $args ) {

		echo self::get_5_star_rating_html( $args );
		echo self::get_100_points_rating_html( $args );
		echo self::get_duration_minutes_html( $args );

	}


	/**
	 * Prints HTML code for a 5 star rating.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public static function get_5_star_rating_html( $args ) {

		if ( 'overwrite' === $args['screen'] && 'misc_rating_5_star' !== $args['selected'] ) {
			return '';
		}

		ob_start();

		$value = $args['value'];
		$value = is_scalar( $value ) ? $value : '';
		$value = absint( $value );

		$input_name = $args['input_name'];
		$input_name = 'edit' === $args['screen'] ? $input_name . '[rating5]' : $input_name;

		?>
        <div data-name="misc_rating_5_star"
             class="misc-field <?php echo 'misc_rating_5_star' === $args['selected'] ?: 'wpb-rs-hidden'; ?> misc-fields-rating5">
			<?php
			for ( $i = 1; $i <= 5; $i ++ ) {
				printf(
					'<span class="star dashicons dashicons-star-%s"></span>',
					$i <= $value ? 'filled' : 'empty'
				);
			}
			?>
            <span class="star-cancel dashicons dashicons-no-alt"></span>
            <input type="hidden" name="<?php echo esc_attr( $input_name ); ?>"
                   value="<?php echo $value; ?>">
        </div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the HTML code for a 100 points rating.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public static function get_100_points_rating_html( $args ) {

		if ( 'overwrite' === $args['screen'] && 'misc_rating_100_points' !== $args['selected'] ) {
			return '';
		}

		$value = $args['value'];
		$value = is_scalar( $value ) ? $value : '';
		$value = absint( $value );

		$input_name = $args['input_name'];
		$input_name = 'edit' === $args['screen'] ? $input_name . '[rating100]' : $input_name;

		ob_start();
		?>
        <div data-name="misc_rating_100_points"
             class="misc-field <?php echo 'misc_rating_100_points' === $args['selected'] ?: 'wpb-rs-hidden'; ?> misc-fields-rating100">
            <input class="misc-field-range" type="range"
                   name="<?php echo esc_attr( $input_name ); ?>" min="0" max="100"
                   step="1" value="<?php echo $value; ?>"/>
            <span class="misc-field-range-view"><?php echo $value; ?></span>
        </div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the HTML code for a duration in minutes field.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public static function get_duration_minutes_html( $args ) {

		if ( 'overwrite' === $args['screen'] && 'misc_duration_minutes' !== $args['selected'] ) {
			return '';
		}

		$value = $args['value'];
		$value = is_scalar( $value ) ? $value : '';
		$value = absint( $value );

		$input_name = $args['input_name'];
		$input_name = 'edit' === $args['screen'] ? $input_name . '[duration_minutes]' : $input_name;

		ob_start();
		?>
        <div data-name="misc_duration_minutes"
             class="misc-field <?php echo 'misc_duration_minutes' === $args['selected'] ?: 'wpb-rs-hidden'; ?> misc-duration-minutes">
            <input type="number"
                   name="<?php echo esc_attr( $input_name ); ?>" min="0"
                   step="1" value="<?php echo $value; ?>"/>
            <span><?php _e( 'minutes', 'rich-snippets-schema' ); ?></span>
        </div>
		<?php

		return ob_get_clean();
	}


	/**
	 * @param $values
	 *
	 * @return mixed
	 */
	public static function internal_subselect( $values ) {

		$values['http://schema.org/Rating'][] =
		$values['http://schema.org/AggregateRating'][] = array(
			'id'     => 'misc_rating_5_star',
			'label'  => esc_html_x( '5 Star Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\MiscFields_Model', 'misc_rating_5_star' ),
			'field'  => 'get_5_star_rating_html',
		);

		$values['http://schema.org/Rating'][] =
		$values['http://schema.org/AggregateRating'][] = array(
			'id'     => 'misc_rating_100_points',
			'label'  => esc_html_x( '100 Points Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\MiscFields_Model', 'misc_rating_100_points' ),
			'field'  => '',
		);

		$values['http://schema.org/Duration'][] = array(
			'id'     => 'misc_duration_minutes',
			'label'  => esc_html_x( 'Duration (in minutes)', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\MiscFields_Model', 'misc_duration_minutes' ),
			'field'  => '',
		);

		return $values;
	}


	/**
	 * Returns the value for a 5 star rating.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.2.0
	 * @since 2.14.25 Added $overwritten property.
	 */
	public static function misc_rating_5_star( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		$rating_snippet       = new Rich_Snippet();
		$rating_snippet->type = 'AggregateRating';
		$rating_snippet->set_props( array(
			array(
				'name'  => 'ratingCount',
				'value' => 1,
			),
			array(
				'name'  => 'bestRating',
				'value' => 5,
			),
			array(
				'name'  => 'ratingValue',
				'value' => $val,
			),
			array(
				'name'  => 'worstRating',
				'value' => 0,
			),
		) );

		$rating_snippet->prepare_for_output();

		return $rating_snippet;
	}


	/**
	 * Returns the value for a 100 points rating.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.2.0
	 * @since 2.14.25 Added $overwritten property.
	 */
	public static function misc_rating_100_points( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		$rating_snippet       = new Rich_Snippet();
		$rating_snippet->type = 'AggregateRating';
		$rating_snippet->set_props( array(
			array(
				'name'  => 'ratingCount',
				'value' => 1,
			),
			array(
				'name'  => 'bestRating',
				'value' => 100,
			),
			array(
				'name'  => 'ratingValue',
				'value' => $val,
			),
			array(
				'name'  => 'worstRating',
				'value' => 0,
			),
		) );

		$rating_snippet->prepare_for_output();

		return $rating_snippet;
	}


	/**
	 * Returns the value for a duration in minutes.
	 *
	 * @see   https://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @since 2.2.0
	 * @since 2.14.25 Added $overwritten property.
	 *
	 * @return string
	 */
	public static function misc_duration_minutes( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		return sprintf( 'PT%sM', (string) $val );
	}
}
