<?php
/**
 * Add Badge Rule Modal Content
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 */

$types = yith_wcbm_get_badge_rules_types();

$add_badge_rule_link = add_query_arg( array( 'post_type' => YITH_WCBM_Post_Types_Premium::$badge_rule ), admin_url( 'post-new.php' ) );
?>

<div class="yith-wcbm-badge-rules-types">
	<?php foreach ( $types as $badge_rule_type => $type_args ) : ?>
		<a href="<?php echo esc_url_raw( add_query_arg( array( '_type' => $badge_rule_type ), $add_badge_rule_link ) ); ?>" class="yith-wcbm-badge-rule-type yith-wcbm-badge-rule-type__<?php echo esc_attr( $badge_rule_type ); ?>">
			<div class="yith-wcbm-badge-rule-type__icon <?php echo ! empty( $type_args['icon'] ) ? 'yith-wcbm-icon-' . esc_attr( $type_args['icon'] ) : ''; ?>"></div>
			<div class="yith-wcbm-badge-rule-type__info">
				<div class="yith-wcbm-badge-rule-type__title">
					<?php echo esc_html( $type_args['title'] ); ?>
				</div>
				<div class="yith-wcbm-badge-rule-type__description">
					<?php echo esc_html( $type_args['desc'] ); ?>
				</div>
			</div>
		</a>
	<?php endforeach; ?>
</div>
