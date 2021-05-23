<?php
/**
 * Job office location block schema template.
 *
 * @package Yoast\WP\SEO\Schema_Templates
 */

use Yoast\WP\SEO\Schema_Templates\Assets\Icons;
// phpcs:disable WordPress.Security.EscapeOutput -- Reason: The Icons contains safe svg.
?>
{{block name="yoast/office-location" title="<?php esc_attr_e( 'Office location', 'wordpress-seo-premium' ); ?>" category="common" description="<?php esc_attr_e( 'The address where the office is located.', 'wordpress-seo-premium' ); ?>" icon="<?php echo Icons::heroicons_office_building(); ?>" parent=[ "yoast/job-location" ] supports={"multiple": false} }}
<div class={{class-name}}>
	{{heading name="<?php esc_attr_e( 'Location', 'wordpress-seo-premium' ); ?>" value="<?php esc_attr_e( 'Location', 'wordpress-seo-premium' ); ?>" defaultHeadingLevel=3 }}
	{{heading name="address-title" defaultHeadingLevel=4 }}
	{{rich-text required=true name="address" tag="p" keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter street address', 'wordpress-seo-premium' ); ?>"}}
	{{heading name="postal-code-title" defaultHeadingLevel=4 }}
	{{rich-text required=true name="postal-code" tag="p" keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter postal code', 'wordpress-seo-premium' ); ?>"}}
	{{heading name="city-title" defaultHeadingLevel=4 }}
	{{rich-text required=true name="city" tag="p" keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter city', 'wordpress-seo-premium' ); ?>"}}
	{{heading name="region-title" defaultHeadingLevel=4 }}
	{{rich-text required=true name="region" tag="p" keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter region', 'wordpress-seo-premium' ); ?>"}}
	{{heading name="country-title" defaultHeadingLevel=4 }}
	{{rich-text required=true name="country" tag="p" keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter country', 'wordpress-seo-premium' ); ?>"}}
</div>
{{inherit-sidebar parents=[ "yoast/job-posting" ] }}
