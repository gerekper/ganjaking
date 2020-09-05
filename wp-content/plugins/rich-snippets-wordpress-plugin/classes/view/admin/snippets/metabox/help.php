<?php
namespace wpbuddy\rich_snippets;
$course_url = _x( 'https://rich-snippets.io/structured-data-training-course/', 'URL to structured data training course', 'rich-snippets-schema' );
$course_url = add_query_arg( [
	'pk_campaign' => 'plugin-help-metabox',
	'pk_source'   => Helper_Model::instance()->get_site_url_host()
], $course_url );

$course_image_url = _x( 'https://wp-buddy.com/wp-content/uploads/2018/11/structured-data-training-1024x576.jpg', 'Url to how-to video image', 'rich-snippets-schema' );
?>
<p><?php _e( 'Don\'t know what structured data is? Take the structured data training course for free.', 'rich-snippets-schema' ); ?></p>

<a href="<?php echo esc_url( $course_url ); ?>" target="_blank"><img
            src="<?php echo esc_url( $course_image_url ); ?>"
            alt="<?php esc_attr_e( 'Structured Data Training Course', 'rich-snippets-schema' ); ?>"/></a>

<br/>

<a class="button button-primary" target="_blank"
   href="<?php echo esc_url( $course_url ); ?>"><?php _e( 'Structured Data Training Course', 'rich-snippets-schema' ); ?></a>