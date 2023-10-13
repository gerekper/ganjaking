<?php
/**
 * Template options in WC Product Panel
 *
 * @var array $people_types The product people types
 *
 * @package YITH\Booking\Views
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBK' ) || exit;

$all_people_type_ids = yith_wcbk()->person_type_helper()->get_person_type_ids();
$people_type_ids     = array_keys( $people_types );
$all_people_type_ids = array_unique( array_merge( $people_type_ids, $all_people_type_ids ) );

?>
<div class="yith-wcbk-people-types yith-wcbk-settings-section bk_show_if_people_and_people_types">
	<div class="yith-wcbk-settings-section__title">
		<h3><?php esc_html_e( 'People Types', 'yith-booking-for-woocommerce' ); ?></h3>
		<div class="yith-wcbk-people-types__expand-collapse">
			<span class="yith-wcbk-people-types__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
			<span class="yith-wcbk-people-types__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
		</div>
	</div>
	<div class="yith-wcbk-settings-section__content">
		<?php if ( current_user_can( 'edit_' . YITH_WCBK_Post_Types::PERSON_TYPE . 's' ) && current_user_can( 'create_' . YITH_WCBK_Post_Types::PERSON_TYPE . 's' ) ) : ?>
			<div class="yith-wcbk-settings-section__description">
				<?php
				$settings_path = sprintf(
					'YITH > Booking > %s > %s',
					_x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					_x( 'People', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
				);
				echo sprintf(
				// translators: %s is the settings path (YITH > Booking > Configuration > People).
					esc_html__( 'You can create people types in %s', 'yith-booking-for-woocommerce' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=ywcbk-person-type' ) ) . '">' . esc_html( $settings_path ) . '</a>'
				);
				?>
			</div>
		<?php endif; ?>
		<div id="yith-wcbk-people-types__list" class="yith-wcbk-settings-section-box__sortable-container">
			<?php

			foreach ( $all_people_type_ids as $people_type_id ) {
				$people_type = $people_types[ $people_type_id ] ?? array( 'id' => $people_type_id );

				yith_wcbk_get_module_view( 'people', 'product-tabs/people/people-type.php', compact( 'people_type', 'people_type_id' ) );
			}

			?>
		</div>
	</div>
</div>
