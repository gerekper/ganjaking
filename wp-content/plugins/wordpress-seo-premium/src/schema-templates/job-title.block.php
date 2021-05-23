<?php
/**
 * Job title block schema template.
 *
 * @package Yoast\WP\SEO\Schema_Templates
 */

use Yoast\WP\SEO\Schema_Templates\Assets\Icons;
// phpcs:disable WordPress.Security.EscapeOutput -- Reason: The Icons contains safe svg.
?>
{{block name="yoast/job-title" title="<?php esc_attr_e( 'Job title', 'wordpress-seo-premium' ); ?>" category="yoast-required-job-blocks" description="<?php esc_attr_e( 'The title of the job.', 'wordpress-seo-premium' ); ?>" icon="<?php echo Icons::heroicons_annotation(); ?>" parent=[ "yoast/job-posting" ] supports={"multiple": false}}}
<div class={{class-name}}>
	{{title name="title" blockName="<?php esc_attr_e( 'job title', 'wordpress-seo-premium' ); ?>" required=true tags=[ "h2", "h3", "h4", "h5", "h6", "strong" ] keepPlaceholderOnFocus=true placeholder="<?php esc_attr_e( 'Enter job title', 'wordpress-seo-premium' ); ?>"}}
</div>
{{inherit-sidebar parents=[ "yoast/job-posting" ] }}
