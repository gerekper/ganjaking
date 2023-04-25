<?php
/**
 * Admin calendars settings
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Outputs the content for a calendar setting field.
 *
 * @since 1.0.0
 * @param array $field The field data.
 */
function wc_od_calendar_field( $field ) {
	?>
	<div id="<?php echo esc_attr( $field['id'] ); ?>" class="wc-od-calendar-field"></div>
	<?php
}

/**
 * Gets the modal content for the calendar event form.
 *
 * @since 1.0.0
 * @param string $extra_content Optional. Additional content for display after
 * the default paramenters.
 * @return string The modal content.
 */
function wc_od_event_modal_content( $extra_content = '' ) {
	ob_start();
	?>
	<div class="event-modal">
		<form id="event-form" method="post">
			<div class="row">
				<div class="full-column columns">
					<div class="column-wrap">
						<label><?php _e( 'Title', 'woocommerce-order-delivery' ); ?></label>
						<input class="widefat" type="text" name="title" value="" placeholder="<?php _e( 'Enter the event title', 'woocommerce-order-delivery' ); ?>" required />
					</div>
				</div>
			</div>

			<div class="row">
				<div class="half-column columns">
					<div class="column-wrap">
						<label><?php _e( 'Start', 'woocommerce-order-delivery' ); ?></label>
						<input class="date-field" type="text" name="start" value="" placeholder="yyyy/mm/dd" required />
					</div>
				</div>
				<div class="half-column columns">
					<div class="column-wrap">
						<label><?php _e( 'End', 'woocommerce-order-delivery' ); ?></label>
						<input class="date-field" type="text" name="end" value="" placeholder="yyyy/mm/dd" required />
					</div>
				</div>
			</div>

			<?php echo $extra_content; ?>

			<div class="row actions">
				<div class="full-column columns">
					<div class="column-wrap">
						<p>
							<input class="button" type="submit" value="<?php _e( 'Save', 'woocommerce-order-delivery' ); ?>" />
							<a class="cancel" href="#"><?php _e( 'Cancel', 'woocommerce-order-delivery' ); ?></a>
							<a class="delete" href="#"><?php _e( 'Delete', 'woocommerce-order-delivery' ); ?></a>
						</p>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Gets the tooltip content for a calendar event.
 *
 * @since 1.0.0
 * @param string $extra_content Optional. Additional content for display after
 * the default content.
 * @return string The tooltip content for a calendar event.
 */
function wc_od_event_tooltip_content( $extra_content = '' ) {
	ob_start();
	?>
	<h3 class="title">{{title}}</h3>
	<div class="row">
		<div class="half-column columns">
			<div class="column-wrap">
				<label><?php _e( 'Start', 'woocommerce-order-delivery' ); ?>:</label> <span>{{start}}</span>
			</div>
		</div>
		<div class="half-column columns">
			<div class="column-wrap">
				<label><?php _e( 'End', 'woocommerce-order-delivery' ); ?>:</label> <span>{{end}}</span>
			</div>
		</div>
	</div>
	<?php
	echo $extra_content;

	return ob_get_clean();
}


/** Delivery events functions *************************************************/


/**
 * Gets the modal content for the delivery event form.
 *
 * @since 1.0.0
 * @return string The modal content for the delivery event.
 */
function wc_od_delivery_modal_content() {
	$countries = wc_od_get_countries();

	ob_start();
	?>
	<div class="row">
		<div class="half-column columns">
			<div class="column-wrap">
				<label for="country"><?php _e( 'Country', 'woocommerce-order-delivery' ); ?></label>
				<select id="country" name="country" data-placeholder="<?php _e( 'Select a country&hellip;', 'woocommerce-order-delivery' ); ?>">
					<option></option>
				<?php foreach ( $countries as $key => $country ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo $country; ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="half-column columns">
			<div class="column-wrap">
				<label for="states"><?php _e( 'States', 'woocommerce-order-delivery' ); ?></label>
				<select id="states" name="states" data-placeholder="<?php _e( 'Select the states&hellip;', 'woocommerce-order-delivery' ); ?>"></select>
			</div>
		</div>
	</div>

	<?php
	$extra_content = ob_get_clean();

	return wc_od_event_modal_content( $extra_content );
}

/**
 * Gets the tooltip content for a delivery event.
 *
 * @since 1.0.0
 * @return string The tooltip content for a delivery event.
 */
function wc_od_delivery_tooltip_content() {
	ob_start();
	?>
	<div class="row">
		<div class="half-column columns">
			<div class="column-wrap">
				<label><?php _e( 'Country', 'woocommerce-order-delivery' ); ?>:</label> {{country}}
			</div>
		</div>
		<div class="half-column columns">
			<div class="column-wrap">
				<label><?php _e( 'States', 'woocommerce-order-delivery' ); ?>:</label> {{states}}
			</div>
		</div>
	</div>
	<?php
	$extra_content = ob_get_clean();

	return wc_od_event_tooltip_content( $extra_content );
}
