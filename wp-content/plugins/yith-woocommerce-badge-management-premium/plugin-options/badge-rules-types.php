<?php
/**
 * Badge Rule Types
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\PluginOptions
 */

global $wp_roles;
global $pagenow;
global $post;

$roles  = array();
$button = 'post-new.php' === $pagenow ? 'publish' : 'save';

foreach ( $wp_roles->roles as $role_id => $role_info ) {
	$roles[ $role_id ] = $role_info['name'];
}
$roles['yith-wcbm-guest'] = __( 'Guest Users', 'yith-woocommerce-badges-management' );

$default_fields = array(
	'type'              => array(
		'name'            => 'yith_wcbm_badge_rule[_type]',
		'field_position'  => -1,
		'type'            => 'text',
		'extra_row_class' => 'yith-wcbm-type__row',
	),
	'post-title'        => array(
		'label'             => __( 'Rule name', 'yith-woocommerce-badges-management' ),
		'desc'              => __( 'Enter a name to identify this badge rule.', 'yith-woocommerce-badges-management' ),
		'custom_attributes' => array(
			'placeholder' => __( 'Rule name', 'yith-woocommerce-badges-management' ),
			'required'    => 'true',
		),
		'type'              => 'text',
		'name'              => 'post_title',
		'id'                => 'yith-wcbm-title',
	),
	'enabled'           => array(
		'label' => __( 'Active rule', 'yith-woocommerce-badges-management' ),
		'desc'  => __( 'Choose whether to enable or disable this rule.', 'yith-woocommerce-badges-management' ),
		'type'  => 'onoff',
		'name'  => 'yith_wcbm_badge_rule[_enabled]',
	),
	'exclude_products'  => array(
		'label' => __( 'Exclude products', 'yith-woocommerce-badges-management' ),
		'desc'  => __( 'Enable to exclude some products from this rule.', 'yith-woocommerce-badges-management' ),
		'type'  => 'onoff',
		'name'  => 'yith_wcbm_badge_rule[_exclude_products]',
	),
	'excluded_products' => array(
		'label'    => __( 'Excluded products', 'yith-woocommerce-badges-management' ),
		'desc'     => __( 'Select the products that you want to exclude.', 'yith-woocommerce-badges-management' ),
		'type'     => 'ajax-products',
		'multiple' => true,
		'name'     => 'yith_wcbm_badge_rule[_excluded_products]',
		'data'     => array(
			'placeholder'          => __( 'Search Products...', 'yith-woocommerce-badges-management' ),
			'minimum_input_length' => '1',
		),
		'deps'     => array(
			'id'    => '_exclude_products',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'badge'             => array(
		'label' => __( 'Badge to assign', 'yith-woocommerce-badges-management' ),
		'desc'  => __( 'Select the badge to show.', 'yith-woocommerce-badges-management' ),
		'type'  => 'ajax-posts',
		'name'  => 'yith_wcbm_badge_rule[_badge]',
		'data'  => array(
			'placeholder'          => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
			'post_type'            => YITH_WCBM_Post_Types::$badge,
			'minimum_input_length' => '1',
		),
	),
	'schedule'          => array(
		'label' => __( 'Schedule rule', 'yith-woocommerce-badges-management' ),
		'desc'  => __( 'Enable to show this badge only for a specific time interval.', 'yith-woocommerce-badges-management' ),
		'type'  => 'onoff',
		'name'  => 'yith_wcbm_badge_rule[_schedule]',
	),
	'schedule_dates'    => array(
		'label'  => __( 'Schedule rule from', 'yith-woocommerce-badges-management' ),
		'desc'   => __( 'Set a start and end date to show this badge.', 'yith-woocommerce-badges-management' ),
		'type'   => 'custom',
		'action' => 'yith_wcbm_print_schedule_dates_badge_rule_field',
		'name'   => 'yith_wcbm_badge_rule[_schedule_dates]',
		'data'   => array(
			'date-format' => 'dd-mm-yy',
		),
		'deps'   => array(
			'id'    => '_schedule',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'show_badge_to'     => array(
		'label'   => __( 'Show badge to', 'yith-woocommerce-badges-management' ),
		'desc'    => __( 'Choose if the badges will be shown to all users or only specific user roles.', 'yith-woocommerce-badges-management' ),
		'type'    => 'radio',
		'name'    => 'yith_wcbm_badge_rule[_show_badge_to]',
		'options' => array(
			'all-users'      => __( 'All users', 'yith-woocommerce-badges-management' ),
			'specific-users' => __( 'Only specific users or user roles', 'yith-woocommerce-badges-management' ),
		),
		'std'     => 'all-users',
	),
	'user_roles'        => array(
		'label'    => __( 'Show badge to these user roles', 'yith-woocommerce-badges-management' ),
		'desc'     => __( 'Choose which user roles to apply this rule to.', 'yith-woocommerce-badges-management' ),
		'type'     => 'select',
		'multiple' => true,
		'name'     => 'yith_wcbm_badge_rule[_user_roles]',
		'class'    => 'wc-enhanced-select',
		'data'     => array(
			'placeholder' => __( 'Search user roles...', 'yith-woocommerce-badges-management' ),
		),
		'options'  => $roles,
		'deps'     => array(
			'id'    => '_show_badge_to',
			'value' => 'specific-users',
			'type'  => 'hide',
		),
	),
	'users'             => array(
		'label'    => __( 'Show badges to these users', 'yith-woocommerce-badges-management' ),
		'desc'     => __( 'Choose which users to apply this rule to.', 'yith-woocommerce-badges-management' ),
		'type'     => 'ajax-customers',
		'multiple' => true,
		'name'     => 'yith_wcbm_badge_rule[_users]',
		'data'     => array(
			'placeholder'          => __( 'Search users...', 'yith-woocommerce-badges-management' ),
			'minimum_input_length' => '1',
		),
		'deps'     => array(
			'id'    => '_show_badge_to',
			'value' => 'specific-users',
			'type'  => 'hide',
		),
	),
	'submit'            => array(
		'field_position' => 50,
		'type'           => 'html',
		'html'           => '<input name="' . $button . '" id="' . $button . '" type="submit" class="yith-plugin-fw__button--primary yith-plugin-fw__button--xl" value="' . __( 'Save Rule', 'yith-woocommerce-badges-management' ) . '">',
	),
);

$default_fields = apply_filters( 'yith_wcbm_badge_rules_default_fields', $default_fields );

$priority = 1;
foreach ( $default_fields as &$field ) {
	$field['field_position'] = $field['field_position'] ?? $priority++;
}

$rules_type = array(
	'product'        => array(
		'title'    => _x( 'Products badge', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
		'desc'     => _x( 'A badge to show in all products or products with specific conditions (recently, on sale, featured, out of stock, low stock).', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ),
		'icon'     => 'product-badge-rule',
		'fields'   => array(
			'assign_to'            => array(
				'label'          => __( 'Assign a badge to', 'yith-woocommerce-badges-management' ),
				'desc'           => __( 'Choose in which products to show the badge of this rule.', 'yith-woocommerce-badges-management' ),
				'type'           => 'select',
				'name'           => 'yith_wcbm_badge_rule[_assign_to]',
				'field_position' => 1.5,
				'class'          => 'wc-enhanced-select',
				'options'        => array(
					'all'          => __( 'All products', 'yith-woocommerce-badges-management' ),
					'recent'       => __( 'Only recent Products', 'yith-woocommerce-badges-management' ),
					'on-sale'      => __( 'On sale products', 'yith-woocommerce-badges-management' ),
					'featured'     => __( 'Featured products', 'yith-woocommerce-badges-management' ),
					'in-stock'     => __( 'In stock products', 'yith-woocommerce-badges-management' ),
					'out-of-stock' => __( 'Out of stock products', 'yith-woocommerce-badges-management' ),
					'back-order'   => __( 'Back-order products', 'yith-woocommerce-badges-management' ),
					'low-stock'    => __( 'Low stock products', 'yith-woocommerce-badges-management' ),
					'bestsellers'  => __( 'Bestsellers', 'yith-woocommerce-badges-management' ),
				),
			),
			'newer_then'           => array(
				'label'          => __( 'Assign badge to product newer than', 'yith-woocommerce-badges-management' ),
				'desc'           => __( 'Show the badge for products that are newer than X days.', 'yith-woocommerce-badges-management' ),
				'type'           => 'custom',
				'action'         => 'yith_wcbm_print_badge_rules_newer_than_field',
				'name'           => 'yith_wcbm_badge_rule[_newer_then]',
				'field_position' => 1.6,
				'min'            => 1,
				'deps'           => array(
					'id'    => '_assign_to',
					'value' => 'recent',
					'type'  => 'hide',
				),
				'std'            => 5,
			),
			'low_stock_quantity'   => array(
				'label'          => __( 'Low stock quantity', 'yith-woocommerce-badges-management' ),
				'desc'           => __( 'Set the low stock quantity.', 'yith-woocommerce-badges-management' ),
				'type'           => 'number',
				'name'           => 'yith_wcbm_badge_rule[_low_stock_quantity]',
				'field_position' => 1.6,
				'min'            => 1,
				'deps'           => array(
					'id'    => '_assign_to',
					'value' => 'low-stock',
					'type'  => 'hide',
				),
				'std'            => 5,
			),
			'bestsellers_quantity' => array(
				'label'          => __( 'Bestsellers', 'yith-woocommerce-badges-management' ),
				'desc'           => __( 'Set in how many best seller products to show the badge', 'yith-woocommerce-badges-management' ),
				'type'           => 'number',
				'name'           => 'yith_wcbm_badge_rule[_bestsellers_quantity]',
				'field_position' => 1.6,
				'min'            => 1,
				'deps'           => array(
					'id'    => '_assign_to',
					'value' => 'bestsellers',
					'type'  => 'hide',
				),
				'std'            => 5,
			),
		),
		'callback' => array( YITH_WCBM_Badge_Rules::get_instance(), 'get_product_badges_from_product_rules' ),
	),
	'category'       => array(
		'title'    => _x( 'Category badge', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
		'desc'     => _x( 'A badge to show in products of specific category.', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ),
		'icon'     => 'category-badge-rule',
		'fields'   => array(
			'associations' => array(
				'label'              => __( 'Categories badges', 'yith-woocommerce-badges-management' ),
				'desc'               => __( 'Choose which badges to show in products of specific categories.', 'yith-woocommerce-badges-management' ),
				'type'               => 'custom',
				'action'             => 'yith_wcbm_print_badge_rule_associations_field',
				'name'               => 'yith_wcbm_badge_rule[_associations]',
				'field_position'     => 1.5,
				'extra_row_class'    => 'yith-wcbm-associations-badge-rule-field',
				'associations_field' => array(
					'type' => 'ajax-terms',
					'data' => array(
						'placeholder'          => __( 'Search category...', 'yith-woocommerce-badges-management' ),
						'taxonomy'             => 'product_cat',
						'minimum_input_length' => '1',
					),
				),
			),
		),
		'callback' => array( YITH_WCBM_Badge_Rules::get_instance(), 'get_product_badges_from_category_rules' ),
	),
	'tag'            => array(
		'title'    => _x( 'Tag badge', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
		'desc'     => _x( 'A badge to show in products with specific tags.', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ),
		'icon'     => 'tag-badge-rule',
		'fields'   => array(
			'associations' => array(
				'label'              => __( 'Tags badges', 'yith-woocommerce-badges-management' ),
				'desc'               => __( 'Choose which badges to show in products of specific tags.', 'yith-woocommerce-badges-management' ),
				'type'               => 'custom',
				'action'             => 'yith_wcbm_print_badge_rule_associations_field',
				'name'               => 'yith_wcbm_badge_rule[_associations]',
				'field_position'     => 1.5,
				'extra_row_class'    => 'yith-wcbm-associations-badge-rule-field',
				'associations_field' => array(
					'id'   => 'yith-wcbm-rule-tag-badge',
					'type' => 'ajax-terms',
					'data' => array(
						'placeholder'          => __( 'Search tag...', 'yith-woocommerce-badges-management' ),
						'taxonomy'             => 'product_tag',
						'minimum_input_length' => '1',
					),
				),
			),
		),
		'callback' => array( YITH_WCBM_Badge_Rules::get_instance(), 'get_product_badges_from_tag_rules' ),
	),
	'shipping-class' => array(
		'title'    => _x( 'Shipping class', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
		'desc'     => _x( 'A badge to show in products delivered with specific shipping class.', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ),
		'icon'     => 'shipping-class-badge-rule',
		'fields'   => array(
			'associations' => array(
				'label'              => __( 'Shipping Class badges', 'yith-woocommerce-badges-management' ),
				'desc'               => __( 'Choose which badges to show in products of specific shipping classes.', 'yith-woocommerce-badges-management' ),
				'type'               => 'custom',
				'action'             => 'yith_wcbm_print_badge_rule_associations_field',
				'name'               => 'yith_wcbm_badge_rule[_associations]',
				'field_position'     => 1.5,
				'extra_row_class'    => 'yith-wcbm-associations-badge-rule-field',
				'associations_field' => array(
					'id'   => 'yith-wcbm-rule-tag-badge',
					'type' => 'ajax-terms',
					'data' => array(
						'placeholder'          => __( 'Search shipping class...', 'yith-woocommerce-badges-management' ),
						'taxonomy'             => 'product_shipping_class',
						'minimum_input_length' => '1',
					),
				),
			),
		),
		'callback' => array( YITH_WCBM_Badge_Rules::get_instance(), 'get_product_badges_from_shipping_class_rules' ),
	),
);

$rules_type = apply_filters( 'yith_wcbm_badge_rules_types_before_adding_default_fields', $rules_type );
foreach ( $rules_type as $rule_type_id => &$rule_type ) {
	$rule_type['fields'] = wp_parse_args( $rule_type['fields'] ?? array(), $default_fields );
	if ( in_array( $rule_type_id, array( 'shipping-class', 'tag', 'category' ), true ) ) {
		unset( $rule_type['fields']['badge'] );
	}

	uasort( $rule_type['fields'], 'yith_wcbm_compare_field_position_value_in_array' );
}

return apply_filters( 'yith_wcbm_badge_rules_types', $rules_type );
