<?php
/**
 * Job expiration block schema template.
 *
 * @package Yoast\WP\SEO\Schema_Templates
 */

use Yoast\WP\SEO\Schema_Templates\Assets\Icons;
// phpcs:disable WordPress.Security.EscapeOutput -- Reason: The Icons contains safe svg.
?>
{{block name="yoast/job-expiration" title="<?php esc_attr_e( 'Job expiration date', 'wordpress-seo-premium' ); ?>" category="yoast-recommended-job-blocks" description="<?php esc_attr_e( 'The date after which the job posting is not valid anymore.', 'wordpress-seo-premium' ); ?>" icon="<?php echo Icons::heroicons_ban(); ?>" parent=[ "yoast/job-posting" ] supports={"multiple": false} }}
<div class={{class-name}}>
	{{rich-text name="title" tag="strong" default="<?php esc_attr_e( 'Closes on', 'wordpress-seo-premium' ); ?>"}}
	{{date name="expirationDate" }}
</div>
{{inherit-sidebar parents=[ "yoast/job-posting" ] }}
