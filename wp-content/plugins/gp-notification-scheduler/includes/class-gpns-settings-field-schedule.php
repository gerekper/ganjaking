<?php

use Gravity_Forms\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

class GPNS_Settings_Field_Notification_Schedule extends Gravity_Forms\Gravity_Forms\Settings\Fields\Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'notification_schedule';

	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function markup() {
		$form         = $this->settings->get_current_form();
		$notification = $this->settings->get_current_values();
		$type         = rgars( $notification, 'scheduleType', 'immediate' );

		ob_start();
		?>

		<tr id="gpns-settings" valign="top">
			<td>
				<div id="gpns-schedule-types">
					<?php
					foreach ( gp_notification_schedule()->get_schedule_types( $form ) as $value => $label ) :
						printf(
							'<input type="radio" value="%1$s" name="_gform_setting_scheduleType" id="gpns-schedule-type-%1$s" %3$s />' .
							'<label for="gpns-schedule-type-%1$s">%2$s</label>',
							$value, $label, checked( $type, $value, false )
						);
					endforeach;
					?>
				</div>

				<div id="gpns-delay-settings" class="gpns-type-settings" style="<?php echo $type !== 'delay' ? 'display:none;' : ''; ?>"
				>
					<span><?php esc_html_e( 'Send this notification', 'gp-notification-scheduler' ); ?></span>
					<input type="number" placeholder="" name="_gform_setting_scheduleDelayOffset" value="<?php echo rgar( $notification, 'scheduleDelayOffset' ); ?>"
					/>
					<select name="_gform_setting_scheduleDelayOffsetUnit">
						<?php
						foreach ( gp_notification_schedule()->get_units() as $value => $label ) :
							printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleDelayOffsetUnit', 'hour' ), $value, false ), $label );
						endforeach
						?>
					</select>
				</div>
				<div id="gpns-date-settings" class="gpns-type-settings" style="<?php echo $type !== 'date' ? 'display:none;' : ''; ?>">
					<span>Send this notification on</span>
					<input type="text" placeholder="yyyy-mm-dd" name="_gform_setting_scheduleDate" value="<?php echo rgar( $notification, 'scheduleDate' ); ?>" id="gpns-schedule-date"
					/>
					<i id="gpns-schedule-date-icon" class="fa fa-calendar-check-o" aria-hidden="true"></i>
					<span>at</span>
					<select name="_gform_setting_scheduleHour">
						<?php
						foreach ( gp_notification_schedule()->get_numeric_choices( 1, 12 ) as $choice ) :
							printf( '<option value="%s" %s>%s</option>', $choice['value'], selected( rgar( $notification, 'scheduleHour', 12 ), $choice['value'], false ), $choice['label'] );
						endforeach;
						?>
					</select>
					<select name="_gform_setting_scheduleMinute">
						<?php
						foreach ( range( 0, 55, 5 ) as $value ) :
							printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleMinute', 0 ), $value, false ), str_pad( $value, 2, '0', STR_PAD_LEFT ) );
						endforeach;
						?>
					</select>
					<select name="_gform_setting_scheduleAmpm">
						<?php
						foreach ( array( 'am', 'pm' ) as $value ) :
							printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleAmpm', 'pm' ), $value, false ), $value );
						endforeach;
						?>
					</select>
				</div>
				<?php if ( gp_notification_schedule()->has_date_fields( $form ) ) : ?>
					<div id="gpns-field-settings" class="gpns-type-settings" style="<?php echo $type !== 'field' ? 'display:none;' : ''; ?>">
						<span>Send this notification</span>
						<input type="number" placeholder="" name="_gform_setting_scheduleFieldOffset" value="<?php echo rgar( $notification, 'scheduleFieldOffset' ); ?>" />
						<select name="_gform_setting_scheduleFieldOffsetUnit">
							<?php
							foreach ( gp_notification_schedule()->get_units() as $value => $label ) :
								printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleFieldOffsetUnit', 'hour' ), $value, false ), $label );
							endforeach;
							?>
						</select>
						<select name="_gform_setting_scheduleFieldTiming">
							<?php
							foreach (
								array(
									'before' => __( 'before', 'gp-notification-scheduler' ),
									'after'  => __( 'after', 'gp-notification-scheduler' ),
								) as $value => $label
							) :
								printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleFieldTiming', 'before' ), $value, false ), $label );
							endforeach;
							?>
						</select>
						<select name="_gform_setting_scheduleField">
							<?php if ( ! empty( $date_groups ) ) : ?>
							<optgroup label="<?php esc_html_e( 'Fields', 'gp-notification-scheduler' ); ?>">
								<?php endif; ?>
								<?php
								foreach ( gp_notification_schedule()->get_date_fields( $form ) as $field ) :
									printf( '<option value="%s" %s>%s</option>', $field->id, selected( rgar( $notification, 'scheduleField' ), $field->id, false ), $field->get_field_label( false, null ) );
								endforeach;
								?>
								<?php if ( ! empty( $date_groups ) ) : ?>
							</optgroup>
						<?php endif; ?>
							<?php if ( ! empty( $date_groups ) ) : ?>
								<optgroup
									label="<?php esc_html_e( 'Groups', 'gp-notification-scheduler' ); ?>"
								>
									<?php
									foreach ( $date_groups as $group ) :
										printf( '<option value="%s" %s>%s</option>', $group['slug'], selected( rgar( $notification, 'scheduleField' ), $group['slug'], false ), $group['label'] );
									endforeach;
									?>
								</optgroup>
							<?php endif; ?>
						</select>
					</div>
				<?php endif; ?>

				<div id="gpns-recurring">

					<input type="checkbox" id="gpns-enable-recurring" name="_gform_setting_scheduleEnableRecurring" value="1" <?php checked( rgar( $notification, 'scheduleEnableRecurring' ), true ); ?> />
					<label for="gpns-enable-recurring"
					>Repeat</label> <?php gform_tooltip( 'notification_schedule_repeat' ); ?>

					<div id="gpns-recurring-schedule-section" class="gpns-recurring-setting" style="display:none;">

						<select id="gpns-recurring-interval" name="_gform_setting_scheduleRecurringInterval">
							<?php
							foreach (
								array(
									'yearly'  => __( 'yearly', 'gp-notification-scheduler' ),
									'monthly' => __( 'monthly', 'gp-notification-scheduler' ),
									'weekly'  => __( 'weekly', 'gp-notification-scheduler' ),
									'daily'   => __( 'daily', 'gp-notification-scheduler' ),
								) as $value => $label
							) :
								printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleRecurringInterval', 'yearly' ), $value, false ), $label );
							endforeach;
							?>
						</select>

						<label for="gpns-recurring-ending">ending</label>
						<select id="gpns-recurring-ending" name="_gform_setting_scheduleRecurringEnding">
							<?php
							foreach (
								array(
									'never' => __( 'never', 'gp-notification-scheduler' ),
									'after' => __( 'after', 'gp-notification-scheduler' ),
								) as $value => $label
							) :
								printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleRecurringEnding', 'never' ), $value, false ), $label );
							endforeach;
							?>
						</select>

					</div>

					<div id="gpns-recurring-ending-section" class="gpns-recurring-setting" style="display:none;">
						<input id="gpns-recurring-ending-value" name="_gform_setting_scheduleRecurringEndingValue" type="number" value="<?php echo rgar( $notification, 'scheduleRecurringEndingValue', 1 ); ?>"
						/>
						<select id="gpns-recurring-ending-unit" name="_gform_setting_scheduleRecurringEndingUnit">
							<?php
							foreach ( gp_notification_schedule()->get_units() as $value => $label ) :
								printf( '<option value="%s" %s>%s</option>', $value, selected( rgar( $notification, 'scheduleRecurringEndingUnit', 'years' ), $value, false ), $label );
							endforeach;
							?>
						</select>
					</div>

				</div>

			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
}

Fields::register( 'notification_schedule', 'GPNS_Settings_Field_Notification_Schedule' );
