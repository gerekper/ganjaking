<?php
/**
 * Associations badge rule Field
 *
 * @var string $id            Field ID.
 * @var string $text          Field Text.
 * @var array  $schedule_from Schedule from field options.
 * @var array  $schedule_to   Schedule to field options.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 */

$schedule_from_html = '<div class="yith-wcbm-schedule-dates-badge-rules--from">' . yith_plugin_fw_get_field( $schedule_from ) . '</div>';
$schedule_to_html   = '<div class="yith-wcbm-schedule-dates-badge-rules--to">' . yith_plugin_fw_get_field( $schedule_to ) . '</div>';

?>

<div id="<?php echo esc_attr( $id ); ?>" class="yith-wcbm-schedule-dates-badge-rules-container">
	<?php echo sprintf( $text, $schedule_from_html, $schedule_to_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
